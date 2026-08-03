# AUVP Experience

Site institucional da **AUVP Experience Co.** — imersões estratégicas globais.

Posicionamento: desejo (aspiracional) → filtro (não é para qualquer um) → ação (aplicar).

---

## Stack

HTML, CSS e JavaScript puros. **Sem build, sem dependências, sem requisições a terceiros.**

A decisão foi deliberada: é uma landing page de campanha que precisa carregar rápido,
ser fácil de editar por qualquer pessoa da equipe e publicar em qualquer lugar
(GitHub Pages, Vercel, Netlify, S3) sem pipeline.

```
index.html                  página principal
imersoes/china.html         página de destino
imersoes/chile.html         página de destino
assets/css/fonts.css        @font-face das fontes auto-hospedadas
assets/css/main.css         tokens, reset, tipografia e componentes
assets/css/sections.css     estilos por seção
assets/js/app.js            todas as interações (~600 linhas, comentado)
assets/fonts/               Cormorant Garamond + Inter (OFL 1.1)
assets/img/                 arte vetorial gerada para o projeto
```

## Rodar localmente

Precisa de um servidor HTTP — abrir com `file://` quebra o carregamento das fontes.

```bash
npx http-server -p 8000
# ou
python3 -m http.server 8000
```

---

## Antes de publicar

Estes pontos estão marcados no código e **precisam de decisão da equipe**:

| O quê | Onde | Situação |
|---|---|---|
| Endpoint do formulário | `assets/js/app.js` → `CONFIG.formEndpoint` | vazio; sem ele o formulário abre o cliente de e-mail |
| E-mail de contato | `assets/js/app.js` → `CONFIG.contactEmail` | `experience@auvp.com.br` — **confirmar** |
| Links de Instagram / LinkedIn | rodapé de todas as páginas (`<!-- TODO -->`) | apontando para `#` |
| Datas, investimento e roteiro | `imersoes/*.html` | marcados como "A confirmar" |
| Foto/vídeo real no hero | `index.html`, seção HERO | usando arte vetorial (ver abaixo) |

### Formulários

Há dois: **candidatura** (seção 8) e **sugestão de destino** (seção 3).
Ambos validam no cliente e depois:

- **com `CONFIG.formEndpoint` preenchido** → `POST` JSON para o endpoint (Formspree, HubSpot, RD Station, rota própria);
- **sem endpoint** → montam um `mailto:` com os campos preenchidos e abrem o cliente de e-mail do usuário.

O segundo caminho é um fallback honesto para não publicar um formulário que finge enviar.
Assim que houver CRM definido, basta preencher a constante.

### Imagens

Não havia banco de imagens disponível, então a arte foi **gerada para o projeto**
(`assets/img/*.svg`): skylines em camadas, cordilheira com mina a céu aberto e malha
de meridianos, todas com granulação e vinheta para leitura editorial.

Elas funcionam como está, mas o ideal é trocar por fotografia real quando houver banco.
Os arquivos têm as proporções certas — é só substituir mantendo o nome, ou apontar o `src`.

**Para usar vídeo no hero**, troque o `<img>` de `.hero__media` por:

```html
<video autoplay muted loop playsinline poster="assets/img/hero-city.svg">
  <source src="assets/video/hero.mp4" type="video/mp4">
</video>
```

O CSS já cobre `video` com as mesmas regras do `img`.

---

## Divergências no briefing

Duas coisas foram resolvidas por interpretação — vale revisar:

1. **Card 2 da seção "Nossas imersões"** vinha rotulado como *Portugal*, mas título e
   subtítulo descreviam o **Chile** ("o coração mineiro da América Latina"). Foi tratado
   como Chile, e a descrição sobre "mercado europeu" virou "mercado latino-americano".
   Se o destino correto for Portugal, `imersoes/chile.html` precisa ser reescrito.
2. A estrutura do briefing **pula o item 7** (vai de 6 para 8). Nada ficou de fora;
   as seções foram renumeradas de 01 a 07 na interface.

Além disso, a seção de candidatura (CTA final) não tinha copy no briefing e foi escrita
seguindo o tom das demais.

---

## Design

**Paleta** — preto quase absoluto (`#08080a`), marfim (`#efeae1`) e dourado (`#c9a15b`).
Todas as cores, tipos e espaçamentos são custom properties em `main.css` (`:root`),
então rebranding é troca de tokens, não caça a valores no CSS.

**Tipografia** — Cormorant Garamond (display, alta modulação, itálico para os destaques)
com Inter (interface, caixa alta e tracking aberto nos rótulos). As duas são fontes
variáveis auto-hospedadas: 6 arquivos, ~86 KB efetivos em pt-BR (só o subset `latin` é baixado).

## Interações

Tudo em um único loop de `requestAnimationFrame` — nada de bibliotecas de scroll.

- Preloader com contador e cortina
- Cursor customizado com `mix-blend-mode`, estado de hover e rótulo contextual
- Botões magnéticos (`data-magnetic`)
- Revelação por palavra nos títulos (`data-split`) e por elemento (`data-reveal`)
- Máscara de mídia em `clip-path` nas imagens
- Parallax (`data-parallax`) no hero e nas capas dos destinos
- Marquee infinito que acelera conforme a velocidade do scroll
- Faixa de perfis arrastável (ponteiro + trackpad horizontal)
- Painel de sugestão de destinos com chips selecionáveis
- Lista da seção Experiência com painel que troca no hover (acordeão no mobile)
- Acordeão de FAQ, barra de progresso e índice lateral de seções

### Acessibilidade

- Navegação por teclado em todos os controles, `aria-expanded`/`aria-pressed` nos
  componentes de estado, `skip link`, foco visível.
- `prefers-reduced-motion: reduce` desliga animações, parallax, marquee e cursor,
  e entrega o conteúdo em estado final.
- Sem dependência de hover: o painel da seção Experiência responde a clique/toque,
  e a faixa de perfis rola por toque e por trackpad.
- Textos alternativos descritivos; conteúdo decorativo com `aria-hidden`.

---

## Deploy

O workflow em `.github/workflows/pages.yml` publica no GitHub Pages a cada push na
branch padrão. Ele só roda depois de ativar **Settings → Pages → Source: GitHub Actions**.

Como não há build, qualquer host estático serve: basta subir a pasta inteira.

## Licenças

Código deste repositório: AUVP Experience Co.
Fontes: SIL Open Font License 1.1 — textos completos em `assets/fonts/LICENSE-*.txt`.
