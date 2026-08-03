#!/usr/bin/env node
/**
 * Gera os assets do plugin WordPress a partir do site estático.
 *
 *   node wordpress/build.js
 *
 * O que ele faz:
 *   1. copia fontes, imagens e o app.js para dentro do plugin;
 *   2. concatena o CSS e escopa cada seletor em `.auvp-x`, que é o
 *      wrapper que o PHP imprime em volta de toda seção.
 *
 * O escopo existe para que nenhuma regra do site vaze para o tema, o
 * painel do WordPress ou o editor do Elementor — e para que as nossas
 * regras vençam as do tema por especificidade.
 */

const fs = require('fs');
const path = require('path');

const ROOT = path.resolve(__dirname, '..');
const SRC = path.join(ROOT, 'assets');
const DEST = path.join(__dirname, 'auvp-experience', 'assets');
const SCOPE = '.auvp-x';

/* Classes que o JS aplica em <body> — regras que começam com elas
   precisam continuar globais, senão deixam de casar. */
const BODY_CLASSES = [
  'auvp-is-locked',
  'auvp-menu-open',
  'auvp-has-cursor',
  'auvp-cursor-hover',
  'auvp-cursor-label',
];

/* ---------- escopo de seletores ---------- */

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

  // :root e html continuam globais (variáveis e comportamento de rolagem).
  if (sel === ':root' || sel === 'html' || sel.startsWith('html ') || sel.startsWith('html.')) {
    return sel;
  }

  // body.classe → global (a classe fica no <body>, fora do wrapper).
  if (/^body[.:#]/.test(sel)) return sel;

  // body puro → vira o próprio wrapper.
  if (sel === 'body') return SCOPE;
  if (/^body[\s>+~]/.test(sel)) return SCOPE + sel.slice(4);

  // estados aplicados no <body>.
  if (BODY_CLASSES.some((c) => sel.startsWith('.' + c))) return sel;

  // seletor universal do reset.
  if (sel === '*') return `${SCOPE}, ${SCOPE} *`;

  return `${SCOPE} ${sel}`;
}

function scopeSelector(sel) {
  return splitSelectors(sel).map(scopeOne).join(', ');
}

/* ---------- percurso do CSS ---------- */

function processBlock(css) {
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
      // @import, @charset e afins.
      out += css.slice(i, j + 1);
      i = j + 1;
      continue;
    }

    // acha a chave de fechamento correspondente
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
      out += lead + head + ' {' + (recurse ? processBlock(body) : body) + '}';
    } else {
      out += lead + scopeSelector(head) + ' {' + body + '}';
    }

    i = k + 1;
  }

  return out;
}

/* ---------- utilidades de arquivo ---------- */

function copyDir(from, to) {
  fs.mkdirSync(to, { recursive: true });
  let count = 0;

  for (const entry of fs.readdirSync(from, { withFileTypes: true })) {
    const src = path.join(from, entry.name);
    const dst = path.join(to, entry.name);

    if (entry.isDirectory()) {
      count += copyDir(src, dst);
    } else {
      fs.copyFileSync(src, dst);
      count++;
    }
  }

  return count;
}

/* ---------- build ---------- */

fs.rmSync(DEST, { recursive: true, force: true });

const imgs = copyDir(path.join(SRC, 'img'), path.join(DEST, 'img'));
const fonts = copyDir(path.join(SRC, 'fonts'), path.join(DEST, 'fonts'));

fs.mkdirSync(path.join(DEST, 'js'), { recursive: true });
fs.copyFileSync(path.join(SRC, 'js', 'app.js'), path.join(DEST, 'js', 'app.js'));

const fontsCss = fs.readFileSync(path.join(SRC, 'css', 'fonts.css'), 'utf8');
const mainCss = fs.readFileSync(path.join(SRC, 'css', 'main.css'), 'utf8');
const sectionsCss = fs.readFileSync(path.join(SRC, 'css', 'sections.css'), 'utf8');

let scoped = processBlock(mainCss) + '\n' + processBlock(sectionsCss);

/* O wrapper não pode virar um contêiner de rolagem: overflow-x hidden
   quebraria position:sticky nas seções Diferencial, Experiência e FAQ.
   `clip` corta igual sem criar o contexto de rolagem. */
scoped = scoped.replace(/overflow-x:\s*hidden/g, 'overflow-x: clip');

const header = `/* =============================================================
   AUVP Experience — folha única do plugin WordPress
   GERADO POR wordpress/build.js — NÃO EDITE À MÃO.
   Fonte: assets/css/{fonts,main,sections}.css
   Todos os seletores são escopados em ${SCOPE}.
   ============================================================= */\n\n`;

fs.mkdirSync(path.join(DEST, 'css'), { recursive: true });
fs.writeFileSync(path.join(DEST, 'css', 'auvp-experience.css'), header + fontsCss + '\n' + scoped);

const size = fs.statSync(path.join(DEST, 'css', 'auvp-experience.css')).size;

console.log(`imagens: ${imgs} | fontes: ${fonts} | js: 1`);
console.log(`css: assets/css/auvp-experience.css (${(size / 1024).toFixed(1)} KB)`);
