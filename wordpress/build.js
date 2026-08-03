#!/usr/bin/env node
/**
 * Gera os arquivos prontos para colar no WordPress/Elementor.
 *
 *   node wordpress/build.js
 *
 * A fonte da verdade continua sendo o site estático na raiz do repositório.
 * Este script recorta os blocos de index.html, escopa o CSS e embute
 * imagens e fontes, de modo que o resultado funcione sozinho — sem plugin,
 * sem upload de arquivo e sem requisição a serviços de terceiros.
 *
 * Saída em wordpress/saida/.
 */

const fs = require('fs');
const path = require('path');

const ROOT = path.resolve(__dirname, '..');
const OUT = path.join(__dirname, 'saida');
const SCOPE = '.auvp-x';

/* Classes que vivem no nível do <body> e não dentro do wrapper: escopá-las
   as faria deixar de casar. Todas já são prefixadas, então não colidem. */
const GLOBAL_CLASSES = [
  'auvp-is-locked',
  'auvp-menu-open',
  'auvp-has-cursor',
  'auvp-cursor-hover',
  'auvp-cursor-label',
  // o wrapper dos elementos fixos carrega .auvp-x e .auvp-overlays juntas
  'auvp-overlays',
];

/* =========================================================
   1. Escopo do CSS
   ========================================================= */

function splitSelectors(sel) {
  const parts = [];
  let depth = 0;
  let current = '';

  for (const ch of sel) {
    if (ch === '(' || ch === '[') depth++;
    else if (ch === ')' || ch === ']') depth--;

    if (ch === ',' && depth === 0) {
      parts.push(current);
      current = '';
    } else {
      current += ch;
    }
  }
  parts.push(current);

  return parts;
}

function scopeOne(raw) {
  const sel = raw.trim();
  if (!sel) return sel;

  if (sel === ':root' || sel === 'html' || sel.startsWith('html ') || sel.startsWith('html.')) return sel;
  if (/^body[.:#]/.test(sel)) return sel;
  if (sel === 'body') return SCOPE;
  if (/^body[\s>+~]/.test(sel)) return SCOPE + sel.slice(4);
  if (GLOBAL_CLASSES.some((c) => sel.startsWith('.' + c))) return sel;
  if (sel === '*') return `${SCOPE}, ${SCOPE} *`;

  return `${SCOPE} ${sel}`;
}

function scopeBlock(css) {
  let out = '';
  let i = 0;

  while (i < css.length) {
    if (css.startsWith('/*', i)) {
      const end = css.indexOf('*/', i);
      const stop = end === -1 ? css.length : end + 2;
      out += css.slice(i, stop);
      i = stop;
      continue;
    }

    let j = i;
    while (j < css.length && css[j] !== '{' && css[j] !== ';' && !css.startsWith('/*', j)) j++;

    if (j >= css.length) {
      out += css.slice(i);
      break;
    }
    if (css.startsWith('/*', j)) {
      out += css.slice(i, j);
      i = j;
      continue;
    }
    if (css[j] === ';') {
      out += css.slice(i, j + 1);
      i = j + 1;
      continue;
    }

    let k = j;
    let depth = 0;
    for (; k < css.length; k++) {
      if (css[k] === '{') depth++;
      else if (css[k] === '}') {
        depth--;
        if (depth === 0) break;
      }
    }

    const prelude = css.slice(i, j);
    const body = css.slice(j + 1, k);
    const lead = prelude.match(/^\s*/)[0];
    const head = prelude.trim();

    if (head.startsWith('@')) {
      const name = head.slice(1).split(/[\s({]/)[0].toLowerCase();
      const recurse = ['media', 'supports', 'container', 'layer', 'scope'].includes(name);
      out += lead + head + ' {' + (recurse ? scopeBlock(body) : body) + '}';
    } else {
      out += lead + scopeSelector(head) + ' {' + body + '}';
    }

    i = k + 1;
  }

  return out;
}

function scopeSelector(sel) {
  return splitSelectors(sel).map(scopeOne).join(', ');
}

/* =========================================================
   2. Recorte dos blocos do index.html
   ========================================================= */

const VOID_TAGS = new Set(['img', 'br', 'hr', 'input', 'meta', 'link', 'source']);

/**
 * Devolve o HTML do elemento que começa em `start`, casando a tag de
 * fechamento correspondente (conta aninhamento da mesma tag).
 */
function elementAt(html, start) {
  const tag = /^<([a-zA-Z][\w-]*)/.exec(html.slice(start))[1];
  if (VOID_TAGS.has(tag.toLowerCase())) {
    return html.slice(start, html.indexOf('>', start) + 1);
  }

  const open = new RegExp('<' + tag + '(?=[\\s>/])', 'gi');
  const close = new RegExp('</' + tag + '\\s*>', 'gi');
  let depth = 0;
  let i = start;

  while (i < html.length) {
    open.lastIndex = i;
    close.lastIndex = i;
    const o = open.exec(html);
    const c = close.exec(html);

    if (!c) break;

    if (o && o.index < c.index) {
      depth++;
      i = o.index + 1;
    } else {
      depth--;
      i = c.index + 1;
      if (depth === 0) return html.slice(start, c.index + c[0].length);
    }
  }

  throw new Error('Tag <' + tag + '> sem fechamento correspondente');
}

function blockAt(html, anchor) {
  const at = html.indexOf(anchor);
  if (at === -1) throw new Error('Âncora não encontrada: ' + anchor);
  return elementAt(html, at);
}

/* =========================================================
   3. Embutir imagens e fontes
   ========================================================= */

function dataUri(file, mime) {
  return 'data:' + mime + ';base64,' + fs.readFileSync(file).toString('base64');
}

function inlineImages(html) {
  return html.replace(/(src|href)="((?:\.\.\/)*assets\/img\/[^"]+)"/g, (m, attr, rel) => {
    const file = path.join(ROOT, rel.replace(/^(\.\.\/)+/, ''));
    if (!fs.existsSync(file)) return m;
    return `${attr}="${dataUri(file, 'image/svg+xml')}"`;
  });
}

function fontFaces() {
  /* Só o subset latin: cobre o português e evita dobrar o peso. */
  const files = [
    ['Cormorant Garamond', 'normal', 'cormorant-garamond-latin.woff2'],
    ['Cormorant Garamond', 'italic', 'cormorant-garamond-italic-latin.woff2'],
    ['Inter', 'normal', 'inter-latin.woff2'],
  ];

  return files
    .map(([family, style, file]) => {
      const uri = dataUri(path.join(ROOT, 'assets/fonts', file), 'font/woff2');
      return `@font-face{font-family:"${family}";font-style:${style};font-weight:300 500;font-display:swap;src:url(${uri}) format("woff2")}`;
    })
    .join('\n');
}

/* =========================================================
   4. Build
   ========================================================= */

const indexHtml = fs.readFileSync(path.join(ROOT, 'index.html'), 'utf8');

/* Blocos, na ordem da página. A navegação reúne overlays, menu fixo,
   menu em tela cheia e índice lateral num pedaço só. */
const grainStart = indexHtml.indexOf('<div class="auvp-grain"');
const dotnav = blockAt(indexHtml, '<nav class="auvp-dotnav"');
const dotnavEnd = indexHtml.indexOf(dotnav) + dotnav.length;

const BLOCKS = [
  ['navegacao', indexHtml.slice(grainStart, dotnavEnd)],
  ['hero', blockAt(indexHtml, '<section class="auvp-hero"')],
  ['posicionamento', blockAt(indexHtml, '<section class="auvp-section" id="posicionamento"')],
  ['imersoes', blockAt(indexHtml, '<section class="auvp-section" id="imersoes"')],
  ['diferencial', blockAt(indexHtml, '<section class="auvp-section auvp-edge" id="diferencial"')],
  [
    'faixa',
    blockAt(indexHtml, '<div class="auvp-marquee"') + '\n' + blockAt(indexHtml, '<p class="auvp-sr-only">'),
  ],
  ['networking', blockAt(indexHtml, '<section class="auvp-section auvp-net" id="networking"')],
  ['experiencia', blockAt(indexHtml, '<section class="auvp-section" id="experiencia"')],
  ['faq', blockAt(indexHtml, '<section class="auvp-section" id="faq"')],
  ['candidatura', blockAt(indexHtml, '<section class="auvp-section auvp-apply" id="aplicar"')],
  ['rodape', blockAt(indexHtml, '<footer class="auvp-footer">')],
].map(([slug, html]) => [slug, inlineImages(html)]);

/* --- CSS --- */
const mainCss = fs.readFileSync(path.join(ROOT, 'assets/css/main.css'), 'utf8');
const sectionsCss = fs.readFileSync(path.join(ROOT, 'assets/css/sections.css'), 'utf8');

let css = scopeBlock(mainCss) + '\n' + scopeBlock(sectionsCss);

/* O wrapper não pode virar contêiner de rolagem: overflow-x hidden
   quebraria position:sticky nas seções Diferencial, Experiência e FAQ.
   `clip` corta igual sem criar o contexto de rolagem. */
css = css.replace(/overflow-x:\s*hidden/g, 'overflow-x: clip');

const cssHeader = `/* =============================================================
   AUVP EXPERIENCE — folha de estilo para colar no WordPress
   GERADO POR wordpress/build.js — NÃO EDITE À MÃO.
   Fonte: assets/css/{main,sections}.css

   Todos os seletores são escopados em ${SCOPE}, o wrapper que envolve
   cada bloco colado. Nada daqui alcança o tema.
   As fontes vão embutidas em base64: nenhum upload, nenhuma
   requisição a serviços de terceiros.
   ============================================================= */

`;

const cssFinal = cssHeader + fontFaces() + '\n\n' + css;

/* --- JS --- */
const js = fs.readFileSync(path.join(ROOT, 'assets/js/app.js'), 'utf8');

/* --- escrita --- */
fs.rmSync(OUT, { recursive: true, force: true });
fs.mkdirSync(path.join(OUT, 'secoes'), { recursive: true });

fs.writeFileSync(path.join(OUT, 'estilos.css'), cssFinal);
fs.writeFileSync(path.join(OUT, 'script.js'), js);

BLOCKS.forEach(([slug, html], i) => {
  const header = `<!-- AUVP Experience — bloco ${String(i + 1).padStart(2, '0')}: ${slug}
     Cole num widget HTML do Elementor.
     Requer estilos.css e script.js aplicados uma vez na página. -->\n`;
  fs.writeFileSync(
    path.join(OUT, 'secoes', `${String(i + 1).padStart(2, '0')}-${slug}.html`),
    header + '<div class="auvp-x">\n' + html + '\n</div>\n'
  );
});

/* Página completa: um único bloco, com CSS e JS embutidos. */
const paginaCompleta = `<!-- =============================================================
     AUVP EXPERIENCE — página completa
     GERADO POR wordpress/build.js — NÃO EDITE À MÃO.

     Cole este arquivo inteiro num widget HTML do Elementor (ou no
     bloco "HTML personalizado" do editor padrão). Não precisa de mais
     nada: estilo, script, fontes e imagens já estão aqui dentro.
     ============================================================= -->
<style>
${cssFinal}
</style>

<div class="auvp-x">
${BLOCKS.map(([, html]) => html).join('\n\n')}
</div>

<script>
${js}
</script>
`;

fs.writeFileSync(path.join(OUT, 'pagina-completa.html'), paginaCompleta);

/* Snippet opcional de PHP, copiado como está. */
fs.copyFileSync(
  path.join(__dirname, 'formulario-opcional.php'),
  path.join(OUT, 'formulario-opcional.php')
);

/* Prévia local: simula um tema hostil e um container do Elementor com
   transform — o cenário que quebra position:fixed. Não vai para o Git. */
const previa = `<!DOCTYPE html><html lang="pt-BR"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Prévia local — AUVP Experience</title>
<style>
  body { margin: 0; background: #fff; color: #111; font-family: Georgia, serif; }
  .btn, .card, .title, .nav, .menu, .section, .field, .form, .chip {
    background: #ff0 !important; color: #f0f !important; border: 3px dashed red !important;
    font-family: "Comic Sans MS", cursive !important;
  }
  h1, h2, h3 { color: #f0f; font-family: "Comic Sans MS", cursive; }
  a { color: #00f; text-decoration: underline; }
  .tema { padding: 24px; font-family: system-ui, sans-serif; }
  .elementor-container { transform: translateZ(0); will-change: transform; }
</style></head><body>
<div class="tema"><h2 class="title">Cabeçalho do tema</h2><a class="btn" href="#">Botão do tema</a></div>
<div class="elementor-container">
${paginaCompleta}
</div>
<div class="tema"><h2 class="title">Rodapé do tema</h2><p>Deve continuar feio.</p></div>
</body></html>
`;

fs.writeFileSync(path.join(OUT, '_previa-local.html'), previa);

/* --- relatório --- */
const kb = (n) => (n / 1024).toFixed(1) + ' KB';
console.log('blocos:', BLOCKS.map(([s]) => s).join(', '));
console.log('estilos.css        ', kb(cssFinal.length));
console.log('script.js          ', kb(js.length));
console.log('pagina-completa.html', kb(paginaCompleta.length));
