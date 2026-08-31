# AUVP Experience

Site da AUVP Experience Co. — imersões estratégicas globais.

Site estático: HTML, CSS, imagens e **um** arquivo de JavaScript. Sem build,
sem dependência, sem framework. Abrir o `index.html` no navegador já mostra a
página final — não existe passo de compilação entre o que está no repositório
e o que vai para o ar.

---

## Estrutura

```
index.html                  home
missao-china.html           página da Missão China
assets/
  js/
    auvp.js                 os passeios automáticos e o caça-níquel
  css/
    01-tokens.css           paleta, escala tipográfica, medidas
    02-base.css             reset e padrões do documento
    03-layout.css           .auvp-shell, utilitários de texto
    04-textura.css          grão de papel e barra de progresso
    05-botoes.css
    06-navegacao.css        barra do topo
    07-hero.css             1ª dobra — foto com overlay preto
    08-posicionamento.css   2ª dobra — roleta de pilares
    09-imersoes.css         3ª dobra — cards de destino
    10-diferencial.css      4ª dobra — índice de acessos
    11-faixa.css            5ª dobra — faixa deslizante
    12-networking.css       6ª dobra — caça-níquel
    13-experiencia.css      7ª dobra — abas
    14-faq.css              8ª dobra
    15-formularios.css      campos, chips e o formulário de sugestão
    16-rodape.css
    17-animacao.css         animações de rolagem e @keyframes
    18-missao-china.css     só a página da Missão China
  img/
    auvp-experience-horizontal.svg
    hero-imersao.jpg        fundo da 1ª dobra
    DSC*.jpg                onze fotos da Missão China — mosaico da página
                            da missão e cartões da roleta da home
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

## Quase tudo é CSS

O site nasceu sem JavaScript nenhum e quase tudo continua assim:

| Interação | Como funciona |
|---|---|
| Menu em tela cheia | âncora `#menu` + `:target` — fecha sozinho ao navegar |
| Menu suspenso das Imersões | `:hover` e `:focus-within` |
| Card da China | link para a página da missão — o card inteiro é um `<a>` |
| Card de Próximos destinos | `<input type="checkbox">` + `<label>` — o card gira e mostra o formulário |
| FAQ | `<details name="…">` — abre um e fecha o outro |
| Abas da Experiência | `<input type="radio">` + `<label>` |
| Chips de destino | `<input type="checkbox">` — vão junto no formulário |
| Faixa deslizante | `@keyframes` andando -50% sobre dois grupos iguais |
| Mosaico de fotos da Missão China | `@keyframes` alternando a opacidade |
| Logo girando sobre o mapa | `@keyframes` |
| Perfis de networking | caça-níquel movido pela rolagem — ver abaixo |
| Revelação ao rolar | `animation-timeline: view()` |
| Barra de progresso | `animation-timeline: scroll()` |

As animações de rolagem estão dentro de um `@supports`. Em navegador que não
as suporta, o bloco é ignorado e o conteúdo aparece normalmente — **em nenhuma
hipótese a página fica em branco.**

### O que o JavaScript faz — e por que ele existe

O `assets/js/auvp.js` cuida de **três coisas**: o passeio automático da roleta
do posicionamento, o das abas da Experiência e o caça-níquel da dobra de
networking. Nada mais.

Ele entrou porque apareceu um pedido que CSS não faz: **o passeio precisa
continuar depois de um clique**. CSS não sabe trocar o estado de um radio,
então o passeio era uma animação por fora — e enquanto ela rodava, o que
estava na tela não era o estado marcado. Daí vinham as duas queixas que
motivaram a mudança: a ordem dos cartões "quebrando" no fim do ciclo e o
clique nas abas parecendo não pegar.

A regra agora é: **o JavaScript comanda o estado, o CSS continua desenhando.**
Nas abas, ele só marca o próximo radio; todo o desenho vem das mesmas regras
de `:checked` de antes. Na roleta, ele desloca o trilho — a fila está
duplicada no HTML, e o laço fecha em cima da cópia do primeiro cartão, que é
idêntica ao ponto de partida, então voltar ao começo não aparece.

**Sem o arquivo o site continua de pé.** A classe `auvp-js`, posta no `<html>`
por uma linha no `<head>` antes da primeira pintura, é o que liga o modo com
script:

| | com script | sem script |
|---|---|---|
| Roleta | trilho deslocado pelas setas e pelo relógio | fila que rola de lado com o dedo, com encaixe |
| Setas da roleta | aparecem | somem — não teriam o que comandar |
| Cópias dos cartões | entram, para fechar o laço | somem — seriam conteúdo repetido |
| Abas da Experiência | trocam no clique e sozinhas | trocam no clique |
| Networking | caça-níquel, um perfil por vez em destaque | lista com os cinco perfis à mostra |

Os dois passeios só andam com a dobra na tela e a aba do navegador à frente, e
o cursor segura cada um na sua área: **na roleta, só quando está sobre a fila
de cartões** — parar porque o cursor passou pelo título, longe deles, era
parar sem motivo. Sob `prefers-reduced-motion` o relógio não liga e o
caça-níquel nem se monta.

### O caça-níquel do networking

**Título e texto de apoio à esquerda, o rolo à direita** — a mesma divisão do
FAQ, com as mesmas proporções e o mesmo ponto de quebra (860px). Abaixo disso
vira uma coluna só, com teto de 560px e centralizada: esparramada na tela
inteira do tablet, ela deixava metade da dobra vazia à direita.

**A janela mostra três lugares em tela grande e dois em tela pequena.** Com
três lugares no celular, a altura que sobrava para cada um ficava menor que o
texto do perfil e os cinco se sobrepunham, ilegíveis. Com dois, o perfil da
vez fica no lugar de cima e o seguinte espia embaixo — e o deslocamento do
rolo perde o `+1` que centralizava.

O invólucro da dobra é mais alto que a tela e o palco fica grudado dentro
dele. O quanto já se rolou desse excedente vira uma posição contínua de 0 a
n-1: o rolo acompanha o dedo, sem pulos, e o perfil mais perto do centro fica
em destaque. Do JavaScript saem só duas coisas — a variável `--pos` e a classe
`esta-ativo`; todo o desenho é do CSS.

A velocidade do giro é a altura do invólucro: `100svh + (n - 1) × 45svh`.
Aumentar o `45svh` faz girar mais devagar, porque estica o curso.

**Cuidado ao mexer:** a dobra tinha três animações de rolagem no
`17-animacao.css` — o item pulsando, o numeral preenchendo, o título
engrossando. Elas saíram junto com o desenho antigo. Duas coisas mandando na
mesma opacidade e na mesma escala é uma briga que ninguém ganha: enquanto
conviveram, o rolo ficou com os perfis em tamanhos e opacidades trocados.

---

## Antes de publicar

| O quê | Onde |
|---|---|
| Ligar os dois formulários | comentário **“COMO LIGAR ESTE FORMULÁRIO”**, um em cada página |
| Conferir as fotos da dobra Experiência | `13-experiencia.css` — as URLs foram montadas sem poder abrir o Pexels daqui; o enquadramento das malas é o que mais pede olho |
| Destino do card 2 | ver observação abaixo |

### Barra do topo

O topo percorre as dobras da home: Pilares, Imersões, Diferencial, Networking,
Experiência e FAQ. "Imersões" tem os dois papéis — **no clique**, leva à dobra
Nossas imersões; **parado sobre ele**, abre o menu com a Missão China, que é o
único item de lá. O menu aparece no cursor e também no foco do teclado, sem
script. Na página da Missão China o topo é o mesmo, com os destinos apontando
para as dobras da home.

O rótulo **Pilares** é o da 2ª dobra (`#posicionamento`), a dos quatro cartões
— trocar o nome é trocar o texto do link, o `id` da seção não precisa mudar.

Abaixo de 900px os links saem do topo e quem faz o papel é o menu em tela
cheia, atrás do botão — lá a lista é plana e a Missão China aparece como item
próprio.

**Duas armadilhas de cascata moram aqui**, e as duas já morderam: a regra que
esconde os links em tela estreita e a que esconde as cópias dos cartões da
roleta têm a mesma especificidade das regras que as mostram. Quem vem por
último ganha — se alguma delas subir de lugar no arquivo, para de valer, em
silêncio.

### Responsividade

Dois pontos do site já quebraram feio em tela pequena e agora têm regra
própria. Vale saber quais são antes de mexer neles:

**Dobra Experiência, até 900px.** As abas empilham — a 768 as quatro lado a
lado já cortavam os títulos no meio ("LOGÍSTICA ORGANIZAD…"). E, empilhadas,
elas não cabem mais penduradas no rodapé do painel: em tela pequena a fila
passava por cima do título e furava a moldura por baixo. Nessa faixa o painel
deixa de posicionar por cima e volta ao fluxo; a foto e o véu seguem no fundo,
absolutos, e o painel cresce com o que tem dentro. As abas também passam a
`flex: 0 0 auto`: enquanto a base do flex era 0, o cartão aberto encolhia para
a altura do título e o `overflow: hidden` comia o fim do texto, em silêncio.

**Dobra de networking, até 860px.** Ver a seção do caça-níquel acima.

O resto do site já era fluido e continua: conferido de 320px a 1920px nas duas
páginas, sem estouro horizontal e sem elemento fora da tela.

### Formulário

O JavaScript do site não trata de formulário, então o `<form>` precisa de um
destino próprio:

1. **Serviço de formulário** — crie um formulário no Formspree, Getform ou
   similar e cole a URL no `action=""`. Funciona sem mais nada.
2. **Link direto** — troque o botão por um link de WhatsApp ou e-mail.

Enquanto nenhum for feito, o botão não envia nada. O comentário no arquivo
explica as opções.

### Missão China

A abertura é só o título e a frase, no papel. A faixa de foto que havia ali
saiu: era a foto da home repetida e empurrava o mosaico — que já é imagem, e
imagem de verdade desta viagem — para baixo da dobra.

**O mosaico** são três quadros parados que trocam a foto por dentro, com
fade — não é uma esteira rolando. As fotos de um quadro ficam empilhadas no
mesmo lugar e só a opacidade se alterna, com atrasos negativos dividindo o
ciclo entre elas.

São onze fotos da viagem, repartidas em 4 + 4 + 3. A quantidade tem de estar
dita na classe do quadro: `--quarteto` para quatro, `--duo` para duas, e nada
para três, que é o padrão. É ela que divide o ciclo em partes iguais e
encolhe a janela de exibição na mesma proporção — com quatro fotos, cada uma
fica um quarto do ciclo no ar, não um terço. **Ao acrescentar ou tirar foto
de um quadro, acerte essa classe junto** — é o que mantém a conta fechada.

Os ciclos dos quadros de quatro fotos cresceram junto (16s e 20s, contra os
18s do quadro de três): no ciclo antigo cada foto passaria depressa demais
para ser vista.

Dois cuidados que sustentam o efeito. As janelas de fade se sobrepõem: se
apenas se encostassem, sobraria um piscar de fundo entre uma foto e a
seguinte. E o passo de cada quadro é o `--ciclo` dele, nunca um
`animation-duration` por fora — os atrasos são frações de `--ciclo`, e
alterar a duração por outro caminho faz atraso e duração deixarem de bater,
o que reabre o piscar.

As fotos são reduzidas para 1600px no lado maior antes de entrar. Os
originais têm 4240px, para aparecerem num quadro de ~500px — sem reduzir, a
dobra baixa oito vezes mais pixel do que mostra. As quatro últimas somavam
3,0 MB e ficaram em 861 KB. **Foto nova passa por essa régua antes de subir.**

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

### Roleta do posicionamento

Os quatro pilares da 2ª dobra são cartões com foto num trilho horizontal. A
frase da dobra ocupa a coluna da esquerda e os cartões correm à direita dela —
é o que deixa o canto esquerdo com peso em vez de vazio. Abaixo de 1200px as
duas partes viram uma coluna só, com a frase em cima, e as setas passam para
depois dos cartões: lá em cima, antes até da frase, elas apareciam sem nada
para comandar.

Quantos cartões cabem na janela é o `--vis` (1 em tela estreita, 2 no
restante). O passo — a distância de um cartão ao seguinte — quem mede é o
próprio JavaScript, no elemento, então ele vale em qualquer largura sem o CSS
repetir a conta.

A fila está duplicada no HTML e é a cópia que fecha o laço sem costura: o
trilho anda até parar sobre a cópia do primeiro cartão e volta ao começo sem
transição, o que não aparece porque a tela é idêntica. **Mexeu num cartão,
mexa no gêmeo.**

### Diferencial

Era um globo girando com uma parede de palavras por cima. Virou um índice:
cinco linhas, cada uma com a palavra e um traço que atravessa. Cada linha
entra com a rolagem, no timeline dela mesma — o escalonamento vem da posição
na tela, não de `animation-delay` — e o cursor completa o traço e empurra a
palavra. Nada de imagem externa: a dobra é só tipografia e um filete.

A revelação termina em `entry 100%`, o instante em que a linha acabou de
entrar inteira na tela. Enquanto ia até `cover 30%`, a última linha ficava
parada no meio da animação, mais clara que as outras — parecia cor diferente,
e era só a revelação sem terminar.
### Fotos da dobra Experiência

As quatro entram por URL do CDN do Pexels. O endereço se monta a partir do id
que aparece no fim do link da página da foto:

```
página  pexels.com/pt-br/foto/…-14036272/
CDN     images.pexels.com/photos/14036272/pexels-photo-14036272.jpeg?auto=compress&cs=tinysrgb&w=1600
```

Se for trocar alguma, **troque a linha, não acrescente outra**: duas regras
para o mesmo `--bg` e a última cala a primeira, em silêncio. Foi assim que a
aba 01 já mostrou a foto errada, depois de um merge deixar dois conjuntos de
URLs no arquivo.

O quadro da dobra é largo e baixo, e o `cover` corta em cima e embaixo. Na
foto das malas isso comia justo o que interessa, porque mala fica apoiada no
chão: o recorte dela é puxado para baixo com `background-position: center
72%`. É o único botão — 50% é o centro da foto, e quanto maior, mais o
recorte desce.

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
ativos, botões e marcas pequenas. O rodapé e a primeira dobra são as áreas
escuras.

Existe uma segunda versão do acento, o `--verde-claro` (**#a9c9b6**), só para
fundo escuro: sobre o overlay preto do hero o verde original desaparece. Ele
é o contrário do outro — sobre o papel, reprova no contraste. Cada um no seu
fundo.

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

O texto da primeira dobra já chega escrito sobre a foto: entra no
carregamento, em menos de um segundo, e não depende mais de rolagem. Quem
abre a página e não rola lê a dobra inteira.

O fundo da primeira dobra é `assets/img/hero-imersao.jpg`. Para trocar, troque
o arquivo ou o `src` do `<img class="auvp-art">` dentro de `.auvp-hero__media`.
Sobre ela vai o overlay preto (`.auvp-hero__scrim`): uma camada chapada em
toda a foto mais um degradê que fecha no rodapé, onde o texto mora. A soma
das duas deixa ~75% de preto embaixo, o suficiente para o texto branco, e
~45% no meio, onde a foto ainda precisa aparecer. Ao trocar por uma foto mais
clara, é esse par de valores que se ajusta.

Os cartões da roleta usam as fotos da Missão China que já estão no
repositório. As fotos dos cards de destino e da dobra Experiência ainda vêm
do Pexels, por URL. Para deixar o site inteiro auto-contido, baixe cada uma
para `assets/img/` e troque os `src`.

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
