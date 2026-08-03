=== AUVP Experience ===
Contributors: auvpexperience
Tags: elementor, landing page, formulário, imersões
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Seções, widgets Elementor e formulários do site AUVP Experience.

== Description ==

Entrega o site da AUVP Experience como blocos reutilizáveis dentro do
WordPress, sem depender de tema específico.

* 11 widgets para o Elementor, na categoria "AUVP Experience"
* Os mesmos blocos disponíveis como shortcodes
* Formulário de candidatura e painel de sugestão de destinos, com os
  envios gravados no painel e notificação por e-mail
* CSS e JS próprios, isolados do tema: todas as classes são prefixadas
  com `auvp-` e todos os seletores são escopados em `.auvp-x`
* Fontes auto-hospedadas — nenhuma requisição a serviços de terceiros

= Widgets =

Navegação e overlays, Hero, Posicionamento, Imersões, Diferencial,
Faixa deslizante, Networking, Experiência, FAQ, Candidatura e Rodapé.

= Shortcodes =

* `[auvp_experience]` — a página inteira, na ordem do briefing
* `[auvp_secao slug="hero"]` — uma seção; qualquer campo de texto pode ser
  sobrescrito por atributo, ex.: `[auvp_secao slug="hero" titulo="..."]`
* `[auvp_navegacao]` — menu fixo, menu em tela cheia e overlays
* `[auvp_rodape]` — rodapé

== Installation ==

1. Plugins > Adicionar novo > Enviar plugin e selecione o .zip.
2. Ative o plugin.
3. Crie uma página, escolha o template "Elementor Canvas" e arraste os
   widgets da categoria "AUVP Experience" — comece por "Navegação e
   overlays".

== Frequently Asked Questions ==

= Preciso do Elementor? =

Não. Sem o Elementor os shortcodes continuam funcionando. Com ele, cada
seção vira um widget com os textos editáveis.

= Onde ficam as candidaturas? =

No menu "AUVP Experience" do painel. Há exportação em CSV na listagem.

= Como integrar com um CRM? =

Use o hook `auvp_lead_stored( $post_id, $tipo, $dados )`.

== Changelog ==

= 1.0.0 =
* Versão inicial.
