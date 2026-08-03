# AUVP Experience no WordPress / Elementor

Sem plugin. O código é colado direto numa página.

O **`index.html` da raiz do repositório é o arquivo que se cola** — ele é ao
mesmo tempo o site estático e o bloco para o Elementor. Estilo, script, fontes
e imagens estão todos embutidos nele.

---

## Colar no Elementor

1. **Páginas → Adicionar nova**.
2. Nos atributos da página, escolha o template **Elementor Canvas**
   (página em branco). É o que preserva o design: o site tem menu fixo e
   rodapé próprios, e eles brigariam com o cabeçalho do tema.
3. **Editar com Elementor** → arraste o widget **HTML**.
4. Abra `index.html`, **selecione tudo, copie e cole** no widget.
5. Publique.

Pronto. Não precisa de plugin, upload de arquivo nem CDN externo.

O `<head>` do arquivo é ignorado ao colar — o navegador descarta `<title>` e
`<meta>` fora do documento. Se preferir copiar só o essencial, o arquivo tem
marcações: copie do comentário **"TUDO O QUE PRECISA SER COLADO"** até o
**`</div>`** com o comentário "fim do bloco para colar".

> No editor do Elementor a prévia pode aparecer estranha (ele injeta o próprio
> CSS na área de edição). Confira sempre na página publicada.

### Editar os textos

Abra o `index.html` e edite direto no markup, entre os dois blocos marcados
com `data-auvp` (o `<style>` no começo e o `<script>` no fim). Esses dois são
gerados — não mexa dentro deles. Todo o resto é a fonte da verdade.

Depois de editar, é só copiar e colar de novo no widget.

**Não mexa nas classes `auvp-*` nem nos atributos `data-*`**: é por eles que o
script encontra os elementos.

---

## Alternativa: uma seção por widget

Se a equipe for reordenar seções com frequência, `saida/` traz o site
fatiado — cada bloco num arquivo, para virar um widget HTML separado.

**Uma vez, na página:**

1. Cole `saida/estilos.css` em **Elementor → Configurações do site → CSS
   personalizado** (requer Elementor Pro) ou em **Aparência → Personalizar →
   CSS adicional** (funciona em qualquer instalação).
2. Cole `saida/script.js` em **Elementor → Configurações avançadas → Custom
   Code** (Pro), ou dentro de uma tag `<script>` num widget HTML no fim da
   página.

**Depois, um widget HTML por bloco, nesta ordem:**

| # | Arquivo | O que é |
|---|---------|---------|
| 01 | `secoes/01-navegacao.html` | menu fixo, menu em tela cheia, índice lateral, cursor, grão, preloader e barra de progresso |
| 02 | `secoes/02-hero.html` | primeira dobra |
| 03 | `secoes/03-posicionamento.html` | os 4 pilares |
| 04 | `secoes/04-imersoes.html` | cards de destino + painel de sugestão |
| 05 | `secoes/05-diferencial.html` | |
| 06 | `secoes/06-faixa.html` | faixa deslizante |
| 07 | `secoes/07-networking.html` | |
| 08 | `secoes/08-experiencia.html` | |
| 09 | `secoes/09-faq.html` | |
| 10 | `secoes/10-candidatura.html` | formulário |
| 11 | `secoes/11-rodape.html` | |

Cada widget deve ficar **sozinho** em sua seção/container do Elementor, com
**largura total** e **padding zero** — o espaçamento já vem de dentro do
componente.

### Usar o cabeçalho e o rodapé do tema

Basta **não** colar os blocos `01-navegacao` e `11-rodape`. Nesse caso o
preloader, o cursor customizado, o grão e a barra de progresso também somem —
eles moram no bloco de navegação.

---

## Formulários

Por padrão os dois formulários (candidatura e sugestão de destino) funcionam
em **modo e-mail**: ao enviar, abrem o cliente de e-mail do visitante com os
campos já preenchidos. Funciona sem configurar nada, mas depende de o
visitante concluir o envio.

### Para gravar as candidaturas no WordPress

Cole `saida/formulario-opcional.php` no fim do **`functions.php` do seu tema
filho** (Aparência → Editor de arquivos de tema).

> Use um **tema filho**. Editar o `functions.php` do tema principal faz o
> código ser apagado na próxima atualização dele.

A partir daí:

- as candidaturas aparecem no menu **Candidaturas** do painel, com todos os
  campos e a URL de origem;
- você recebe um aviso por e-mail no endereço de Configurações → Geral;
- o JavaScript detecta a rota sozinho e para de usar o modo e-mail.

Para mudar o destinatário do aviso, no mesmo `functions.php`:

```php
add_filter( 'auvp_lead_email_to', fn() => 'experience@auvp.com.br' );
```

Para mandar para um CRM (RD Station, HubSpot…):

```php
add_action( 'auvp_lead_stored', function ( $post_id, $tipo, $dados ) {
    wp_remote_post( 'https://seu-crm/endpoint', array(
        'body' => wp_json_encode( $dados ),
    ) );
}, 10, 3 );
```

**Antispam:** campo-isca invisível, tempo mínimo de preenchimento (3s) e
limite de 5 envios por IP a cada 10 minutos. O nonce do REST é enviado quando
disponível, mas não é obrigatório — páginas de campanha costumam ficar em
cache de página inteira, e um nonce expirado derrubaria envios legítimos.

---

## Por que isso não briga com o tema

Duas camadas:

1. **Todas as classes CSS são prefixadas com `auvp-`.** Nomes genéricos como
   `.btn`, `.card`, `.nav` e `.title` colidiriam com praticamente qualquer
   tema; `.auvp-btn` não colide com nada.
2. **Todo seletor é escopado em `.auvp-x`**, o wrapper que envolve o bloco
   colado. Isso impede que o nosso reset (`*`, `h1`, `p`, `button`) vaze para
   o tema, e faz nossas regras vencerem as do tema por especificidade.

Continuam globais, de propósito: `:root` (só variáveis), `html` e as classes
de estado que o script aplica no `<body>` — todas prefixadas.

Há ainda um detalhe do Elementor tratado no script: containers do editor
costumam ter `transform`, o que **anula `position: fixed`** nos filhos. Por
isso o menu, o cursor, o grão e a barra de progresso são movidos para um
wrapper filho direto do `<body>` assim que a página carrega.

---

## Regerar

Depois de mexer em `assets/css/*`, `assets/js/app.js` ou `assets/img/*`:

```bash
node wordpress/build.js
```

O script reescreve, **dentro do próprio `index.html`**, os blocos
`<style data-auvp="estilos">` e `<script data-auvp="script">` e o `src` das
imagens marcadas com `data-auvp-src`. O markup entre eles não é tocado. Rodar
duas vezes seguidas produz o mesmo resultado.

Ele também reescreve `saida/` e gera `saida/_previa-local.html` (fora do Git):
o bloco colado dentro de um container com `transform`, contra um "tema
hostil" que estiliza `.btn`, `.card`, `.title`, `h1`, `h2` e `h3` com cores
berrantes. Abra num navegador — o site tem de sair intacto e o bloco do tema
tem de continuar feio. É esse teste que garante o isolamento nos dois
sentidos.

---

## Limitações conhecidas

- **Não foi testado numa instalação real de WordPress.** O ambiente onde isso
  foi escrito não tinha acesso a wordpress.org. A verificação feita foi:
  `php -l` no snippet, renderização do bloco dentro de um container com
  `transform` contra um tema hostil, e teste das interações no resultado
  (painel de destinos, validação dos formulários, acordeão, abas, arraste),
  em desktop e mobile. Antes de ir para produção, publique numa página de
  homologação e confira.
- **Não cole o mesmo bloco duas vezes na mesma página.** Os `id` são fixos
  (`#hero`, `#faq`, `#vote`…) e duplicá-los quebra as âncoras do menu.
- **O `index.html` tem ~320 KB** porque as fontes vão embutidas em base64. É o
  preço de não depender de upload nem de CDN externo. Se preferir mais leve,
  suba os `.woff2` de `assets/fonts/` para o servidor e troque os
  `@font-face` do topo do `<style>` por `url()` apontando para eles.
- A tag `og:image` do `<head>` ainda aponta para um caminho relativo. Ao
  publicar, troque por uma URL absoluta — é o que o compartilhamento em redes
  sociais usa.
- As páginas de destino (China e Chile) continuam usando CSS e JS externos,
  como site estático comum. Para colá-las no WordPress também, avise que eu
  aplico o mesmo tratamento nelas.
