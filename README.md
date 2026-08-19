# AUVP Experience

Site da AUVP Experience Co. — imersões estratégicas globais.

Site estático: HTML, CSS e imagens. **Sem JavaScript, sem build, sem
dependência.** Abrir o `index.html` no navegador já mostra a página final —
não existe passo de compilação entre o que está no repositório e o que vai
para o ar.

---

## Estrutura

```
index.html                  home
missao-china.html           página da Missão China
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
    18-missao-china.css     só a página da Missão China
  img/
    auvp-experience-horizontal.svg
    hero-imersao.jpg        fundo da 1ª dobra
    china.svg               mapa da dobra Canton Fair
    canton-fair.svg         logo que marca Guangzhou no mapa
```

Os arquivos **01 a 06, 15, 16 e 17** são compartilhados pelas duas páginas.
Os de **07 a 14** desenham as dobras da home e só entram no `index.html`; o
**18** é da Missão China e só entra no `missao-china.html`. Cada página
carrega o que usa, e nada mais.

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
| Roleta de fotos da Missão China | `@keyframes` vertical, com a fita duplicada |
| Logo girando sobre o mapa | `@keyframes` |
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
| Ligar os dois formulários | comentário **“COMO LIGAR ESTE FORMULÁRIO”**, um em cada página |
| Foto da faixa de abertura da Missão China | `missao-china.html`, `.auvp-capa__faixa` — hoje usa a mesma foto da home |
| Fotos da roleta da Missão China | `.auvp-roleta` — hoje são as de banco da home |
| Datas, investimento e roteiro | dentro dos cards China e Chile, marcados como “A confirmar” |
| Destino do card 2 | ver observação abaixo |

### Formulário

Como não há JavaScript, o `<form>` precisa de um destino:

1. **Serviço de formulário** — crie um formulário no Formspree, Getform ou
   similar e cole a URL no `action=""`. Funciona sem mais nada.
2. **Link direto** — troque o botão por um link de WhatsApp ou e-mail.

Enquanto nenhum for feito, o botão não envia nada. O comentário no arquivo
explica as opções.

### Missão China

A abertura é uma faixa de foto com o chapéu por cima; o título e a frase de
abertura vêm embaixo, no papel. A faixa é baixa de propósito: a roleta logo
abaixo já é imagem, e duas telas cheias de foto seguidas empurravam o texto
para longe demais.

**A roleta** são três colunas de fotos correndo para cima em velocidades
diferentes, com o bloco verde e os três textos logo abaixo, sem respiro entre
os dois. Cada coluna repete a mesma lista de fotos **duas vezes** e desliza
exatamente 50%: ao terminar, o quadro é idêntico ao do começo e o laço não tem
emenda. Ao trocar as fotos, **troque as duas cópias juntas** — se as listas
divergirem, a emenda aparece.

Dois detalhes que o laço exige, e que quebram em silêncio se alguém mexer: o
respiro entre as fotos é `margin-bottom` de cada imagem, não `gap` da fita
(com `gap` a fita fica com N imagens e N−1 vãos, e metade da altura não cai
num começo de ciclo); e a fita é `display: flow-root`, para a margem da última
foto contar na altura — sem isso ela colapsa e sobra um vão de descompasso a
cada volta.

O vídeo da dobra da culinária é o do YouTube, embutido pelo domínio
`youtube-nocookie.com`, que não deixa cookie de rastreio em quem só passa
pela página sem dar play. Ele começa sozinho e **mudo** — navegador nenhum
permite autoplay com som, então o `mute=1` é o preço de não ficar só a
miniatura. O `loading="lazy"` faz o vídeo carregar quando a dobra se
aproxima, não no carregamento da página. Para trocar o vídeo, troque o ID na
URL do `<iframe>`.

**O mapa da Canton Fair** é o `assets/img/china.svg`, entrando como `<img>`.
O cinza mora dentro do próprio arquivo (`#a8a6a1`) em vez de no CSS, para não
existirem duas cópias dos contornos — para escurecer ou clarear o mapa, mude
a cor lá.

A logo (`assets/img/canton-fair.svg`) fica por cima, posicionada em
porcentagem sobre o desenho — `left: 68%; top: 86%`, que é onde cai
Guangzhou. Ela gira devagar para a esquerda, uma volta a cada 18 segundos.
Quem posiciona é o `<span>` de fora e quem gira é a `<img>` de dentro: se as
duas transformações ficassem no mesmo elemento, a rotação apagaria a
centralização e a logo sairia do lugar. Sob `prefers-reduced-motion`, o giro
para junto com o resto das animações do site.

### Fontes

Duas famílias, carregadas por `<link>` no `<head>`:

- **General Sans** (Fontshare) — títulos, botões e rótulos de interface
- **Red Hat Mono** (Google Fonts) — texto corrido

Se algum dos dois serviços estiver indisponível, a página cai para a fonte de
sistema — continua legível e com a mesma diagramação (testado).

Para auto-hospedar e não depender de serviço externo, baixe os `.woff2` das
duas, coloque em `assets/fonts/` e troque os dois `<link>` por `@font-face`
no `01-tokens.css`.

### Cores e legibilidade

Tudo em neutros de papel e tinta. O verde **#023620** é pontual — aparece só
em chapéus, números de seção, a palavra destacada de cada título, estados
ativos, botões e marcas pequenas. O rodapé é a única área escura.

Todas as cores são variáveis no `01-tokens.css`. Para trocar o acento, mude
uma linha:

```css
--verde: #023620;
```

Os tons de tinta foram calibrados por contraste medido, não por aparência.
Todo texto do site fica acima de 4.5:1 sobre o papel — o mínimo da WCAG para
texto normal. Se for criar um tom novo, meça antes: `--ink-45` já foi 2.82:1
e `--ink-30` já foi 1.91:1, e era exatamente o que deixava rótulos e legendas
ilegíveis. O único tom abaixo da linha é o `--ink-30` (3.44:1), reservado a
placeholder de campo, que não carrega informação — quem carrega é o `<label>`
ao lado.

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
