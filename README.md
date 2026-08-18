# AUVP Experience

Site da AUVP Experience Co. — imersões estratégicas globais.

Site estático: HTML, CSS e imagens. **Sem JavaScript, sem build, sem
dependência.** Abrir o `index.html` no navegador já mostra a página final —
não existe passo de compilação entre o que está no repositório e o que vai
para o ar.

---

## Estrutura

```
index.html                  markup da página inteira
assets/
  css/
    01-tokens.css           paleta, escala tipográfica, medidas
    02-base.css             reset e padrões do documento
    03-layout.css           .auvp-shell, utilitários de texto
    04-textura.css          grão de papel e barra de progresso
    05-botoes.css
    06-navegacao.css        barra do topo e menu em tela cheia
    07-hero.css             1ª dobra
    08-posicionamento.css   2ª dobra
    09-imersoes.css         3ª dobra — cards de destino
    10-diferencial.css      4ª dobra
    11-faixa.css            5ª dobra — faixa deslizante
    12-networking.css       6ª dobra
    13-experiencia.css      7ª dobra — abas
    14-faq.css              8ª dobra
    15-formularios.css      campos, chips e o formulário de sugestão
    16-rodape.css
    17-animacao.css         animações de rolagem e @keyframes
  img/
    auvp-experience-horizontal.svg
    hero-imersao.jpg        fundo da 1ª dobra
```

Os arquivos de CSS são carregados por `<link>` no `<head>`, **na ordem
numérica**. A ordem é parte do desenho: tokens e base primeiro, cada dobra
depois, animação por último. Uma dobra nova = um arquivo novo + um `<link>`
no lugar certo.

Não mexa nas classes `auvp-*`: são elas que ligam o conteúdo ao estilo.

## Publicar no GitHub Pages

**Settings → Pages → Source: Deploy from a branch → `main` / `/ (root)`.**

Não há mais nada a configurar. O `.nojekyll` diz ao GitHub para servir os
arquivos como estão, sem passar pelo Jekyll.

Para rodar localmente, abra o `index.html` direto no navegador ou suba um
servidor qualquer na pasta:

```sh
python3 -m http.server 8000
```

---

## Por que não tem JavaScript

Menu, acordeões, abas, cards que abrem e animações de rolagem são feitos em
CSS puro:

| Interação | Como funciona |
|---|---|
| Menu em tela cheia | âncora `#menu` + `:target` — fecha sozinho ao navegar |
| Cards de destino | `<details>` — abrem os detalhes na própria página |
| FAQ | `<details name="…">` — abre um e fecha o outro |
| Abas da Experiência | `<input type="radio">` + `<label>` |
| Chips de destino | `<input type="checkbox">` — vão junto no formulário |
| Faixa deslizante | `@keyframes` |
| Perfis de networking | rolagem horizontal nativa, com encaixe |
| Revelação ao rolar | `animation-timeline: view()` |
| Barra de progresso | `animation-timeline: scroll()` |

As animações de rolagem estão dentro de um `@supports`. Em navegador que não
as suporta, o bloco é ignorado e o conteúdo aparece normalmente — **em nenhuma
hipótese a página fica em branco.**

---

## Antes de publicar

| O quê | Onde |
|---|---|
| Ligar o formulário | comentário **“COMO LIGAR O FORMULÁRIO”**, na dobra de imersões |
| Datas, investimento e roteiro | dentro dos cards China e Chile, marcados como “A confirmar” |
| Destino do card 2 | ver observação abaixo |

### Formulário

Como não há JavaScript, o `<form>` precisa de um destino:

1. **Serviço de formulário** — crie um formulário no Formspree, Getform ou
   similar e cole a URL no `action=""`. Funciona sem mais nada.
2. **Link direto** — troque o botão por um link de WhatsApp ou e-mail.

Enquanto nenhum for feito, o botão não envia nada. O comentário no arquivo
explica as opções.

### Fontes

Duas famílias, carregadas por `<link>` no `<head>`:

- **General Sans** (Fontshare) — títulos, botões e rótulos de interface
- **Red Hat Mono** (Google Fonts) — texto corrido

Se algum dos dois serviços estiver indisponível, a página cai para a fonte de
sistema — continua legível e com a mesma diagramação (testado).

Para auto-hospedar e não depender de serviço externo, baixe os `.woff2` das
duas, coloque em `assets/fonts/` e troque os dois `<link>` por `@font-face`
no `01-tokens.css`.

### Cores

Tudo em neutros de papel e tinta. O verde **#023620** é pontual — aparece só
em chapéus, números de seção, a palavra destacada de cada título, estados
ativos, botões e marcas pequenas. O rodapé é a única área escura.

Todas as cores são variáveis no `01-tokens.css`. Para trocar o acento, mude
uma linha:

```css
--verde: #023620;
```

### Imagens

O fundo da primeira dobra é `assets/img/hero-imersao.jpg`. Para trocar, troque
o arquivo ou o `src` do `<img class="auvp-art">` dentro de `.auvp-hero__media`.

As fotos dos cards de destino e da dobra Experiência ainda vêm do Pexels, por
URL. Para deixar o site inteiro auto-contido, baixe cada uma para
`assets/img/` e troque os `src`.

A arte vetorial das dobras (skylines, cordilheira com mina, malha de
meridianos) foi desenhada em SVG para o projeto e está embutida no markup.

---

## Divergências no briefing

1. **O card 2** vinha rotulado como *Portugal*, mas título e subtítulo
   descreviam o **Chile** (“o coração mineiro da América Latina”). Foi tratado
   como Chile, e “mercado europeu” virou “mercado latino-americano”. Se o
   destino correto for Portugal, esse card precisa ser reescrito.
2. A estrutura do briefing **pula o item 7** (vai de 6 para 8). Nada ficou de
   fora; as seções foram renumeradas de 01 a 07 na interface.
3. A dobra de candidatura não tinha copy no briefing e foi escrita seguindo o
   tom das demais.

## Histórico

O site nasceu como **um `index.html` único**, para ser copiado e colado num
widget HTML do Elementor. Disso vinham duas coisas que não existem mais:

- todo o CSS ficava num `<style>` dentro do `<body>`;
- todo seletor era escopado em `.auvp-x` e havia um bloco de reset por
  elemento, para o tema do WordPress não vazar para dentro do site.

Fora do WordPress nada disso é necessário. A reestruturação removeu o escopo e
a barreira **sem mudar uma linha de copy nem um pixel do desenho** — a
verificação foi feita comparando as duas versões pixel a pixel.
