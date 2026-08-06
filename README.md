# AUVP Experience

Site da AUVP Experience Co. — imersões estratégicas globais.

**O repositório tem um arquivo só: `index.html`.** A página inteira está nele —
estilo, imagens e interações. Sem JavaScript, sem plugin, sem arquivo externo.

---

## Colar no WordPress / Elementor

1. **Páginas → Adicionar nova**.
2. Nos atributos da página, escolha o template **Elementor Canvas** (página em
   branco). O site tem menu e rodapé próprios; o cabeçalho do tema brigaria com
   eles.
3. **Editar com Elementor** → arraste o widget **HTML**.
4. Abra o `index.html`, **selecione tudo, copie e cole** no widget.
5. Publique.

O `<head>` do arquivo é descartado pelo navegador na hora de colar — pode
copiar tudo sem medo. Se preferir copiar só o essencial, o arquivo tem duas
marcações: comece no comentário **“PARA USAR NO WORDPRESS / ELEMENTOR”** e vá
até o `</div>` com o comentário **“fim do bloco para colar”**.

> No editor do Elementor a prévia pode aparecer estranha, porque ele injeta o
> próprio CSS na área de edição. Confira sempre na página publicada.

### Por que não quebra

A versão anterior dependia de JavaScript, e no WordPress isso falha com
facilidade: a prévia do editor não executa scripts, e o WordPress remove
`<script>` de quem não tem a permissão `unfiltered_html` (comum em host
gerenciado, multisite ou com plugin de segurança). Sem o script, a página
ficava preta.

Agora **não há JavaScript nenhum**. Menu, acordeões, abas, cards que abrem e
animações de rolagem são feitos em CSS puro:

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

### Por que não briga com o tema

Todas as classes são prefixadas com `auvp-` e todos os seletores são escopados
em `.auvp-x`, a `<div>` que envolve o bloco. O estilo do site não alcança o
tema, e o do tema não alcança o site.

O menu usa `position: sticky`, não `fixed`: containers do Elementor costumam ter
`transform`, o que anula `position: fixed` nos elementos de dentro.

---

## Editar

Abra o `index.html` num editor de texto. O `<style>` fica no começo do bloco e
o conteúdo vem logo depois — é só editar o texto no HTML e colar de novo.

Não mexa nas classes `auvp-*`: são elas que ligam o conteúdo ao estilo.

---

## Antes de publicar

| O quê | Onde |
|---|---|
| Ligar os formulários | comentário **“COMO LIGAR O FORMULÁRIO”**, na seção de candidatura |
| Datas, investimento e roteiro | dentro dos cards China e Chile, marcados como “A confirmar” |
| Destino do card 2 | ver observação abaixo |

### Formulários

Como não há JavaScript nem plugin, o `<form>` precisa de um destino. Três
caminhos, do mais simples ao mais integrado:

1. **Serviço de formulário** — crie um formulário no Formspree, Getform ou
   similar e cole a URL no `action=""`. Funciona sem mais nada.
2. **Elementor Pro** — apague o `<form>` e ponha o widget “Formulário” do
   Elementor no lugar, com os mesmos campos.
3. **Link direto** — troque o botão por um link de WhatsApp ou e-mail.

Enquanto nenhum for feito, o botão não envia nada. O comentário no arquivo
explica cada opção.

### Fontes

Duas famílias, carregadas por `@import` no topo do `<style>`:

- **General Sans** (Fontshare) — títulos, botões e rótulos de interface
- **Rokkitt** (Google Fonts) — texto corrido

Se algum dos dois serviços estiver indisponível, a página cai para a fonte de
sistema e Georgia — continua legível e com a mesma diagramação (testado).

> A **General Sans não pôde ser carregada no ambiente onde o site foi feito**
> (o Fontshare estava bloqueado pela política de rede). A URL usada é a oficial
> da API deles; a diagramação foi conferida com uma grotesca equivalente no
> lugar. Confira a renderização final ao publicar.

Para auto-hospedar as fontes e não depender de serviço externo, baixe os
`.woff2` das duas, suba para o servidor e troque os dois `@import` por blocos
`@font-face` apontando para os arquivos.

### Cores

Tudo em neutros de papel e tinta. O verde **#023620** é pontual — aparece só
em chapéus, números de seção, a palavra destacada de cada título, estados
ativos, botões e marcas pequenas.

Todas as cores são variáveis no início do `<style>`. Para trocar o acento,
mude uma linha:

```css
--verde: #023620;
```

### Imagens

Não havia banco de imagens, então a arte (skylines, cordilheira com mina,
malha de meridianos) foi **desenhada em SVG para o projeto**, em tons neutros,
e está embutida no arquivo. Para trocar por fotografia, substitua cada `<svg class="auvp-art">`
por um `<img class="auvp-art" src="URL-DA-IMAGEM" alt="…">` — as imagens
precisam estar hospedadas (por exemplo, na biblioteca de mídia do WordPress).

O fundo da primeira dobra é a foto `DSC00333.jpg`, deste repositório, servida
pelo `raw.githubusercontent.com`. Para trocar a foto, troque essa URL no
`<img class="auvp-art">` dentro de `.auvp-hero__media`.

---

## Divergências no briefing

1. **O card 2** vinha rotulado como *Portugal*, mas título e subtítulo
   descreviam o **Chile** (“o coração mineiro da América Latina”). Foi tratado
   como Chile, e “mercado europeu” virou “mercado latino-americano”. Se o
   destino correto for Portugal, esse card precisa ser reescrito.
2. A estrutura do briefing **pula o item 7** (vai de 6 para 8). Nada ficou de
   fora; as seções foram renumeradas de 01 a 07 na interface.
3. A seção de candidatura não tinha copy no briefing e foi escrita seguindo o
   tom das demais.

## O que foi verificado

Renderização e interações com **JavaScript desativado**, dentro de um container
com `transform`, contra um tema hostil que estiliza `.btn`, `.card`, `.title`,
`h1`, `h2`, `h3`, `p`, `summary` e `details` com cores berrantes — em desktop e
mobile. Também com os dois serviços de fonte bloqueados.

O teste do tema hostil encontrou um vazamento real (`summary { background }` do
tema atravessando os acordeões) e o site ganhou uma barreira de reset por
elemento para isso.

**Não foi testado numa instalação real de WordPress** (o ambiente onde o site
foi feito não tinha acesso a wordpress.org). Vale colar numa página de
homologação antes de publicar.
