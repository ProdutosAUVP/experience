#!/usr/bin/env node
/**
 * Deixa o index.html autocontido e gera os blocos por seção.
 *
 *   node wordpress/build.js
 *
 * O index.html é ao mesmo tempo o site estático e o que se cola no
 * Elementor. Este script preenche, dentro dele:
 *
 *   <style  data-auvp="estilos">  CSS escopado + fontes em base64
 *   <script data-auvp="script">   o app.js
 *   <img data-auvp-src="...">     a imagem convertida em data URI
 *
 * O markup entre esses blocos não é tocado — é lá que se edita a copy.
 * A operação é idempotente: rodar de novo apenas atualiza os blocos.
 *
 * Também escreve wordpress/saida/ com um arquivo por seção, para quem
 * preferir montar a página bloco a bloco no Elementor.
 */

const fs = require('fs');
const path = require('path');

const ROOT = path.resolve(__dirname, '..');
const INDEX = path.join(ROOT, 'index.html');
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

function scopeSelector(sel) {
  return splitSelectors(sel).map(scopeOne).join(', ');
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

/* =========================================================
   2. Recursos embutidos
   ========================================================= */

function dataUri(file, mime) {
  return 'data:' + mime + ';base64,' + fs.readFileSync(file).toString('base64');
}

/** @font-face com o woff2 em base64. Só o subset latin: cobre o português. */
function fontFaces() {
  const arquivos = [
    ['Cormorant Garamond', 'normal', 'cormorant-garamond-latin.woff2'],
    ['Cormorant Garamond', 'italic', 'cormorant-garamond-italic-latin.woff2'],
    ['Inter', 'normal', 'inter-latin.woff2'],
  ];

  return arquivos
    .map(([family, style, arquivo]) => {
      const uri = dataUri(path.join(ROOT, 'assets/fonts', arquivo), 'font/woff2');
      return `@font-face{font-family:"${family}";font-style:${style};font-weight:300 500;font-display:swap;src:url(${uri}) format("woff2")}`;
    })
    .join('\n');
}

/** Troca o src de todo elemento com data-auvp-src pelo data URI do arquivo. */
function embedImages(html) {
  return html.replace(
    /data-auvp-src="([^"]+)"([^>]*?)\s(src|href)="[^"]*"/g,
    (match, rel, meio, attr) => {
      const arquivo = path.join(ROOT, rel);
      if (!fs.existsSync(arquivo)) {
        console.warn('  ! imagem não encontrada:', rel);
        return match;
      }
      return `data-auvp-src="${rel}"${meio} ${attr}="${dataUri(arquivo, 'image/svg+xml')}"`;
    }
  );
}

/**
 * Substitui o conteúdo de <tag data-auvp="nome">…</tag>.
 *
 * A substituição é feita por função, e não por string: numa string de
 * replace, `$$` vira `$` e `$1` vira um grupo capturado. O app.js usa
 * `$$(seletor)` o tempo todo — com string, todos virariam `$(seletor)`.
 */
function fillBlock(html, tag, nome, conteudo) {
  const re = new RegExp(`(<${tag} data-auvp="${nome}"[^>]*>)[\\s\\S]*?(</${tag}>)`);
  if (!re.test(html)) throw new Error(`Bloco <${tag} data-auvp="${nome}"> não encontrado no index.html`);
  return html.replace(re, (match, abre, fecha) => abre + '\n' + conteudo + '\n' + fecha);
}

/* =========================================================
   3. Recorte dos blocos
   ========================================================= */

const VOID_TAGS = new Set(['img', 'br', 'hr', 'input', 'meta', 'link', 'source']);

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
   4. Build
   ========================================================= */

/* --- 4.1 CSS e JS --- */
const mainCss = fs.readFileSync(path.join(ROOT, 'assets/css/main.css'), 'utf8');
const sectionsCss = fs.readFileSync(path.join(ROOT, 'assets/css/sections.css'), 'utf8');
const js = fs.readFileSync(path.join(ROOT, 'assets/js/app.js'), 'utf8');

let css = scopeBlock(mainCss) + '\n' + scopeBlock(sectionsCss);

/* O wrapper não pode virar contêiner de rolagem: overflow-x hidden
   quebraria position:sticky nas seções Diferencial, Experiência e FAQ.
   `clip` corta igual sem criar o contexto de rolagem. */
css = css.replace(/overflow-x:\s*hidden/g, 'overflow-x: clip');

const cabecalhoCss = `/* GERADO POR wordpress/build.js — NÃO EDITE AQUI DENTRO.
   Fonte: assets/css/{main,sections}.css e assets/fonts/
   Todos os seletores são escopados em ${SCOPE}: nada daqui alcança o
   tema do WordPress, e nada do tema alcança o site. */`;

const cssFinal = `${cabecalhoCss}\n\n${fontFaces()}\n\n${css}`;

/* --- 4.2 index.html --- */
let index = fs.readFileSync(INDEX, 'utf8');

index = fillBlock(index, 'style', 'estilos', cssFinal);
index = fillBlock(index, 'script', 'script', js);
index = embedImages(index);

fs.writeFileSync(INDEX, index);

/* --- 4.3 blocos por seção --- */
const grainStart = index.indexOf('<div class="auvp-grain"');
const dotnav = blockAt(index, '<nav class="auvp-dotnav"');
const dotnavEnd = index.indexOf(dotnav) + dotnav.length;

const BLOCOS = [
  ['navegacao', index.slice(grainStart, dotnavEnd)],
  ['hero', blockAt(index, '<section class="auvp-hero"')],
  ['posicionamento', blockAt(index, '<section class="auvp-section" id="posicionamento"')],
  ['imersoes', blockAt(index, '<section class="auvp-section" id="imersoes"')],
  ['diferencial', blockAt(index, '<section class="auvp-section auvp-edge" id="diferencial"')],
  [
    'faixa',
    blockAt(index, '<div class="auvp-marquee"') + '\n' + blockAt(index, '<p class="auvp-sr-only">'),
  ],
  ['networking', blockAt(index, '<section class="auvp-section auvp-net" id="networking"')],
  ['experiencia', blockAt(index, '<section class="auvp-section" id="experiencia"')],
  ['faq', blockAt(index, '<section class="auvp-section" id="faq"')],
  ['candidatura', blockAt(index, '<section class="auvp-section auvp-apply" id="aplicar"')],
  ['rodape', blockAt(index, '<footer class="auvp-footer">')],
];

fs.rmSync(OUT, { recursive: true, force: true });
fs.mkdirSync(path.join(OUT, 'secoes'), { recursive: true });

fs.writeFileSync(path.join(OUT, 'estilos.css'), cssFinal + '\n');
fs.writeFileSync(path.join(OUT, 'script.js'), js);

BLOCOS.forEach(([slug, html], i) => {
  const n = String(i + 1).padStart(2, '0');
  const cabecalho = `<!-- AUVP Experience — bloco ${n}: ${slug}
     Cole num widget HTML do Elementor.
     Requer estilos.css e script.js aplicados uma vez na página. -->\n`;
  fs.writeFileSync(
    path.join(OUT, 'secoes', `${n}-${slug}.html`),
    cabecalho + '<div class="auvp-x">\n' + html + '\n</div>\n'
  );
});

fs.copyFileSync(
  path.join(__dirname, 'formulario-opcional.php'),
  path.join(OUT, 'formulario-opcional.php')
);

/* --- 4.4 prévia local (fora do Git) --- */
const corpo = /<div class="auvp-x">[\s\S]*<\/div>\s*<!-- fim do bloco para colar -->/.exec(index)[0];

const previa = `<!DOCTYPE html><html lang="pt-BR"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Prévia — o bloco colado dentro de um tema hostil</title>
<style>
  body { margin: 0; background: #fff; color: #111; font-family: Georgia, serif; }
  .btn, .card, .title, .nav, .menu, .section, .field, .form, .chip {
    background: #ff0 !important; color: #f0f !important; border: 3px dashed red !important;
    font-family: "Comic Sans MS", cursive !important;
  }
  h1, h2, h3 { color: #f0f; font-family: "Comic Sans MS", cursive; }
  a { color: #00f; text-decoration: underline; }
  .tema { padding: 24px; font-family: system-ui, sans-serif; }
  /* container do Elementor com transform: o cenário que quebra position:fixed */
  .elementor-container { transform: translateZ(0); will-change: transform; }
</style></head><body>
<div class="tema"><h2 class="title">Cabeçalho do tema</h2><a class="btn" href="#">Botão do tema</a></div>
<div class="elementor-container">
${corpo}
</div>
<div class="tema"><h2 class="title">Rodapé do tema</h2><p>Deve continuar feio.</p></div>
</body></html>
`;

fs.writeFileSync(path.join(OUT, '_previa-local.html'), previa);

/* --- relatório --- */
const kb = (n) => (n / 1024).toFixed(1) + ' KB';
console.log('index.html         ', kb(index.length), '(autocontido, pronto para colar)');
console.log('  css embutido     ', kb(cssFinal.length));
console.log('  js embutido      ', kb(js.length));
console.log('blocos por seção   ', BLOCOS.map(([s]) => s).join(', '));
