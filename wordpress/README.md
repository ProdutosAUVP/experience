# AUVP Experience no WordPress + Elementor

O site é entregue como um **plugin**, não como tema. Você instala, ativa e as
seções aparecem no Elementor como widgets arrastáveis, com os textos editáveis.

---

## Instalar

```bash
bash wordpress/package.sh          # gera dist/auvp-experience-1.0.0.zip
```

No WordPress: **Plugins → Adicionar novo → Enviar plugin** → selecione o `.zip` → **Ativar**.

## Montar a página

1. **Páginas → Adicionar nova**.
2. No painel de atributos, escolha o template **Elementor Canvas** (página em
   branco, sem cabeçalho e rodapé do tema). É o que preserva o design como foi
   desenhado — o site tem menu fixo e rodapé próprios.
3. **Editar com Elementor** → busque por "AUVP" no painel de widgets.
4. Arraste, nesta ordem:

   | # | Widget | Observação |
   |---|--------|------------|
   | 1 | AUVP — Navegação e overlays | menu fixo, menu em tela cheia, cursor, grão, preloader e barra de progresso |
   | 2 | AUVP — Hero | |
   | 3 | AUVP — Posicionamento | |
   | 4 | AUVP — Imersões | inclui o painel de sugestão de destinos |
   | 5 | AUVP — Diferencial | |
   | 6 | AUVP — Faixa deslizante | |
   | 7 | AUVP — Networking | |
   | 8 | AUVP — Experiência | |
   | 9 | AUVP — FAQ | |
   | 10 | AUVP — Candidatura | formulário |
   | 11 | AUVP — Rodapé | |

   Cada widget deve ficar sozinho em sua seção/container, com **largura total**
   e **padding zero** — o espaçamento interno já vem do próprio componente.

### Usar o cabeçalho e o rodapé do tema

Se preferir manter o header/footer do tema, é só **não** inserir os widgets
"Navegação e overlays" e "Rodapé". Nesse caso o preloader, o cursor
customizado, o grão e a barra de progresso também não aparecem — eles moram
no widget de navegação.

### Sem Elementor

Os mesmos blocos existem como shortcode:

```
[auvp_experience]                       a página inteira
[auvp_navegacao]                        menu + overlays
[auvp_secao slug="hero"]                uma seção
[auvp_secao slug="hero" titulo="..."]   com campo sobrescrito
[auvp_rodape]
```

Slugs: `hero`, `posicionamento`, `destinos`, `diferencial`, `marquee`,
`networking`, `experiencia`, `faq`, `candidatura`.

---

## Formulários

Os dois formulários (candidatura e sugestão de destino) postam para a rota
REST `/wp-json/auvp/v1/lead`. Não é preciso configurar nada.

- **Onde ver os envios:** menu **AUVP Experience** no painel. Cada registro
  guarda todos os campos, além da URL de origem.
- **Exportar:** botão *Exportar tudo em CSV* na listagem.
- **E-mail:** vai para o endereço em Configurações → Geral. Para mudar:

  ```php
  add_filter( 'auvp_lead_email_to', fn() => 'experience@auvp.com.br' );
  ```

- **Integrar com CRM** (RD Station, HubSpot, etc.):

  ```php
  add_action( 'auvp_lead_stored', function ( $post_id, $tipo, $dados ) {
      wp_remote_post( 'https://seu-crm/endpoint', array(
          'body' => wp_json_encode( $dados ),
      ) );
  }, 10, 3 );
  ```

### Proteção contra spam

Honeypot, tempo mínimo de preenchimento (3s) e limite de 5 envios por IP a
cada 10 minutos. **Não usamos nonce como obrigatório** de propósito: páginas
de campanha costumam ficar em cache de página inteira e um nonce expirado
derrubaria envios legítimos. O nonce do REST é enviado quando está disponível.

---

## Por que o site não briga com o tema

Dois mecanismos, aplicados em camadas:

1. **Todas as classes são prefixadas com `auvp-`.** Nomes genéricos como
   `.btn`, `.card`, `.nav` e `.title` colidiriam com praticamente qualquer
   tema; `.auvp-btn` não colide com nada.
2. **Todo seletor é escopado em `.auvp-x`**, o wrapper que o PHP imprime em
   volta de cada seção. Isso impede que o nosso reset (`*`, `h1`, `p`,
   `button`) vaze para o tema e para o painel, e faz nossas regras vencerem
   as do tema por especificidade.

Continuam globais, de propósito: `:root` (variáveis), `html` e as classes de
estado que o JS aplica no `<body>` (`auvp-menu-open`, `auvp-is-locked`,
`auvp-has-cursor`…), todas prefixadas.

Há um teste visual para isso em `wordpress/tools/preview.php`: ele renderiza
as seções contra um "tema hostil" que estiliza `.btn`, `.card`, `.title`,
`h1`, `h2` e `h3` com cores berrantes. O site tem de sair intacto e o bloco do
tema tem de continuar feio.

```bash
php wordpress/tools/preview.php > wordpress/tools/preview.html
```

---

## Desenvolvimento

A fonte da verdade continua sendo o site estático na raiz do repositório.
O plugin **não tem cópia manual** de CSS/JS: `wordpress/build.js` gera
`wordpress/auvp-experience/assets/` a partir de `assets/`.

```bash
node wordpress/build.js     # regenera os assets do plugin
bash  wordpress/package.sh  # build + php -l + zip
```

Depois de mexer em qualquer CSS ou JS da raiz, rode o build antes de empacotar.

### Onde fica a copy

Em `includes/class-auvp-content.php`, num único registro. Cada seção declara
seus campos (tipo, rótulo, valor padrão) e é a partir dele que são gerados:

- os controles do Elementor (inclusive os repetidores),
- os atributos aceitos pelos shortcodes,
- os valores padrão dos templates.

Adicionar um campo ali já o faz aparecer no editor — não é preciso mexer no
widget.

### Estrutura

```
auvp-experience.php              cabeçalho e bootstrap
includes/
  helpers.php                    escape e leitura de campos
  class-auvp-content.php         registro de seções + copy (fonte da verdade)
  class-auvp-render.php          renderiza seção -> HTML
  class-auvp-assets.php          enfileira CSS/JS e injeta a config do front
  class-auvp-shortcodes.php      shortcodes
  class-auvp-forms.php           rota REST, antispam, e-mail
  class-auvp-leads.php           CPT, colunas do admin, exportação CSV
  class-auvp-elementor.php       categoria e registro dos widgets
  widgets/                       base + uma classe por seção
templates/                       markup de cada seção
assets/                          GERADO — não edite à mão
```

---

## Limitações conhecidas

- **Não foi testado numa instalação real de WordPress.** O ambiente onde o
  plugin foi escrito não tinha acesso a wordpress.org. A verificação feita
  foi: `php -l` em todos os arquivos, renderização dos templates fora do
  WordPress (`tools/preview.php`) e teste das interações no HTML resultante,
  incluindo isolamento contra um tema hostil. Antes de ir para produção,
  suba num ambiente de homologação e confira o editor do Elementor e um
  envio real de formulário.
- As páginas de destino (China e Chile) continuam como HTML estático. Se
  precisarem virar páginas do WordPress, dá para montá-las com os widgets
  existentes ou criar um widget específico — hoje elas não estão no plugin.
