<?php
/**
 * Registro de seções: campos editáveis e conteúdo padrão.
 *
 * Este arquivo é a única fonte da copy. Widgets do Elementor, shortcodes
 * e templates leem tudo daqui.
 *
 * @package AUVP_Experience
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registro de seções.
 */
class AUVP_Content {

	/**
	 * Cache do registro.
	 *
	 * @var array|null
	 */
	protected static $sections = null;

	/**
	 * Devolve o registro completo de seções.
	 *
	 * @return array
	 */
	public static function sections() {
		if ( null !== self::$sections ) {
			return self::$sections;
		}

		$sections = array(

			/* ---------------------------------------------------------- */
			'hero'          => array(
				'title'  => __( 'AUVP — Hero', 'auvp-experience' ),
				'icon'   => 'eicon-banner',
				'fields' => array(
					'ancora'    => array(
						'type'    => 'text',
						'label'   => __( 'Âncora (id)', 'auvp-experience' ),
						'default' => 'hero',
					),
					'eyebrow'   => array(
						'type'    => 'text',
						'label'   => __( 'Chapéu', 'auvp-experience' ),
						'default' => 'AUVP Experience Co. — Imersões estratégicas globais',
					),
					'titulo'    => array(
						'type'    => 'textarea',
						'label'   => __( 'Título', 'auvp-experience' ),
						'desc'    => __( 'Aceita <em> para o destaque em itálico dourado.', 'auvp-experience' ),
						'default' => 'O mundo não é igual para quem vê <em>de perto</em>.',
					),
					'lede'      => array(
						'type'    => 'textarea',
						'label'   => __( 'Texto de apoio', 'auvp-experience' ),
						'default' => '<strong>A AUVP Experience te coloca dentro dos mercados que movem o mundo.</strong> Não como turista. Como alguém que quer entender, conectar e crescer.',
					),
					'cta_label' => array(
						'type'    => 'text',
						'label'   => __( 'Texto do botão', 'auvp-experience' ),
						'default' => 'Quero participar da próxima imersão',
					),
					'cta_link'  => array(
						'type'    => 'url',
						'label'   => __( 'Link do botão', 'auvp-experience' ),
						'default' => '#aplicar',
					),
					'imagem'    => array(
						'type'     => 'media',
						'label'    => __( 'Imagem de fundo', 'auvp-experience' ),
						'fallback' => 'img/hero-city.svg',
					),
					'video'     => array(
						'type'  => 'url',
						'label' => __( 'Vídeo de fundo (MP4)', 'auvp-experience' ),
						'desc'  => __( 'Se preenchido, substitui a imagem. A imagem vira poster.', 'auvp-experience' ),
					),
					'alt'       => array(
						'type'    => 'text',
						'label'   => __( 'Texto alternativo da imagem', 'auvp-experience' ),
						'default' => 'Skyline noturno de uma metrópole global',
					),
					'rodape'    => array(
						'type'    => 'text',
						'label'   => __( 'Chamada de rolagem', 'auvp-experience' ),
						'default' => 'Role para descobrir',
					),
					'selos'     => array(
						'type'    => 'repeater',
						'label'   => __( 'Selos do rodapé', 'auvp-experience' ),
						'titulo'  => 'texto',
						'fields'  => array(
							'texto' => array(
								'type'  => 'text',
								'label' => __( 'Texto', 'auvp-experience' ),
							),
						),
						'default' => array(
							array( 'texto' => 'Grupo reduzido' ),
							array( 'texto' => 'Processo seletivo' ),
							array( 'texto' => 'Agenda de negócios' ),
						),
					),
				),
			),

			/* ---------------------------------------------------------- */
			'posicionamento' => array(
				'title'  => __( 'AUVP — Posicionamento', 'auvp-experience' ),
				'icon'   => 'eicon-bullet-list',
				'fields' => array(
					'ancora'  => array(
						'type'    => 'text',
						'label'   => __( 'Âncora (id)', 'auvp-experience' ),
						'default' => 'posicionamento',
					),
					'eyebrow' => array(
						'type'    => 'text',
						'label'   => __( 'Chapéu', 'auvp-experience' ),
						'default' => '01 — Posicionamento',
					),
					'titulo'  => array(
						'type'    => 'textarea',
						'label'   => __( 'Título', 'auvp-experience' ),
						'default' => 'AUVP Experience promove uma imersão nos bastidores de economias que estão <em>moldando o futuro</em>.',
					),
					'aside'   => array(
						'type'    => 'textarea',
						'label'   => __( 'Texto lateral', 'auvp-experience' ),
						'default' => 'Quatro pilares sustentam cada imersão. Nenhum deles é acessório — é o que separa estar no país de estar dentro do mercado.',
					),
					'pilares' => array(
						'type'    => 'repeater',
						'label'   => __( 'Pilares', 'auvp-experience' ),
						'titulo'  => 'titulo',
						'fields'  => array(
							'num'    => array(
								'type'  => 'text',
								'label' => __( 'Número', 'auvp-experience' ),
							),
							'titulo' => array(
								'type'  => 'text',
								'label' => __( 'Título', 'auvp-experience' ),
							),
							'texto'  => array(
								'type'  => 'textarea',
								'label' => __( 'Descrição', 'auvp-experience' ),
							),
						),
						'default' => array(
							array(
								'num'    => '01',
								'titulo' => 'Visitas a empresas globais',
								'texto'  => 'Acesso direto a empresas que moldam a economia mundial. Mais do que conhecer, você entende como elas operam, crescem e tomam decisões estratégicas na prática.',
							),
							array(
								'num'    => '02',
								'titulo' => 'Conversas com quem opera o mercado',
								'texto'  => 'Você aprende com quem está no jogo. Executivos e empresários compartilham visões, bastidores e decisões que não chegam nos livros.',
							),
							array(
								'num'    => '03',
								'titulo' => 'Networking com empresários e investidores',
								'texto'  => 'Conexões que vão além do evento. Você se relaciona com pessoas que estão construindo, investindo e expandindo, abrindo portas para oportunidades reais.',
							),
							array(
								'num'    => '04',
								'titulo' => 'Curadoria estratégica da AUVP',
								'texto'  => 'Nada aqui é aleatório. Cada destino, empresa e encontro é pensado para gerar repertório, visão de mercado e decisões mais inteligentes sobre dinheiro.',
							),
						),
					),
				),
			),

			/* ---------------------------------------------------------- */
			'destinos'      => array(
				'title'  => __( 'AUVP — Imersões', 'auvp-experience' ),
				'icon'   => 'eicon-posts-grid',
				'fields' => array(
					'ancora'      => array(
						'type'    => 'text',
						'label'   => __( 'Âncora (id)', 'auvp-experience' ),
						'default' => 'imersoes',
					),
					'eyebrow'     => array(
						'type'    => 'text',
						'label'   => __( 'Chapéu', 'auvp-experience' ),
						'default' => '02 — Destinos',
					),
					'titulo'      => array(
						'type'    => 'text',
						'label'   => __( 'Título', 'auvp-experience' ),
						'default' => 'Nossas imersões',
					),
					'aside'       => array(
						'type'    => 'textarea',
						'label'   => __( 'Texto lateral', 'auvp-experience' ),
						'default' => 'Cada destino é escolhido por um motivo econômico, não turístico.',
					),
					'cards'       => array(
						'type'    => 'repeater',
						'label'   => __( 'Cards de destino', 'auvp-experience' ),
						'titulo'  => 'titulo',
						'fields'  => array(
							'indice'    => array(
								'type'  => 'text',
								'label' => __( 'Índice', 'auvp-experience' ),
							),
							'titulo'    => array(
								'type'  => 'text',
								'label' => __( 'Título', 'auvp-experience' ),
							),
							'sub'       => array(
								'type'  => 'text',
								'label' => __( 'Subtítulo', 'auvp-experience' ),
							),
							'texto'     => array(
								'type'  => 'textarea',
								'label' => __( 'Descrição', 'auvp-experience' ),
							),
							'cta_label' => array(
								'type'  => 'text',
								'label' => __( 'Texto do link', 'auvp-experience' ),
							),
							'link'      => array(
								'type'  => 'url',
								'label' => __( 'Link', 'auvp-experience' ),
							),
							'imagem'    => array(
								'type'  => 'media',
								'label' => __( 'Imagem', 'auvp-experience' ),
							),
							'alt'       => array(
								'type'  => 'text',
								'label' => __( 'Texto alternativo', 'auvp-experience' ),
							),
							'acao'      => array(
								'type'    => 'select',
								'label'   => __( 'Comportamento', 'auvp-experience' ),
								'options' => array(
									'link'    => __( 'Abrir link', 'auvp-experience' ),
									'sugerir' => __( 'Abrir painel de sugestão', 'auvp-experience' ),
								),
								'default' => 'link',
							),
						),
						'default' => array(
							array(
								'indice'    => '01 / Ásia',
								'titulo'    => 'China',
								'sub'       => 'O maior laboratório de negócios do mundo',
								'texto'     => 'Shanghai, Shenzhen e Hangzhou. Tecnologia, escala e execução em nível global.',
								'cta_label' => 'Ver detalhes',
								'link'      => '/imersoes/china/',
								'alt'       => 'Skyline vertical com torres, representando a China',
								'imagem'    => array( 'url' => auvp_asset( 'img/dest-china.svg' ) ),
								'acao'      => 'link',
							),
							array(
								'indice'    => '02 / América Latina',
								'titulo'    => 'Chile',
								'sub'       => 'O coração mineiro da América Latina',
								'texto'     => 'Negócios, internacionalização e oportunidades no mercado latino-americano.',
								'cta_label' => 'Ver detalhes',
								'link'      => '/imersoes/chile/',
								'alt'       => 'Cordilheira e mina a céu aberto, representando o Chile',
								'imagem'    => array( 'url' => auvp_asset( 'img/dest-chile.svg' ) ),
								'acao'      => 'link',
							),
							array(
								'indice'    => '03 / A definir',
								'titulo'    => 'Próximos destinos',
								'sub'       => 'Decida com a gente — para onde vamos depois?',
								'texto'     => 'A AUVP Experience evolui com quem está dentro. Participe da escolha dos próximos destinos.',
								'cta_label' => 'Sugerir destino',
								'link'      => '',
								'alt'       => 'Malha de meridianos com pontos marcando destinos',
								'imagem'    => array( 'url' => auvp_asset( 'img/dest-next.svg' ) ),
								'acao'      => 'sugerir',
							),
						),
					),
					'vote_eyebrow' => array(
						'type'    => 'text',
						'label'   => __( 'Painel: chapéu', 'auvp-experience' ),
						'default' => 'Decida com a gente',
					),
					'vote_titulo' => array(
						'type'    => 'text',
						'label'   => __( 'Painel: título', 'auvp-experience' ),
						'default' => 'Para onde vamos depois?',
					),
					'vote_texto'  => array(
						'type'    => 'textarea',
						'label'   => __( 'Painel: texto', 'auvp-experience' ),
						'default' => 'Marque os destinos que fariam você embarcar — e diga o que quer ver por lá. As sugestões entram na curadoria dos próximos roteiros.',
					),
					'chips'       => array(
						'type'    => 'repeater',
						'label'   => __( 'Painel: destinos sugeridos', 'auvp-experience' ),
						'titulo'  => 'texto',
						'fields'  => array(
							'texto' => array(
								'type'  => 'text',
								'label' => __( 'Destino', 'auvp-experience' ),
							),
						),
						'default' => array(
							array( 'texto' => 'Japão' ),
							array( 'texto' => 'Estados Unidos' ),
							array( 'texto' => 'Emirados Árabes' ),
							array( 'texto' => 'Índia' ),
							array( 'texto' => 'Coreia do Sul' ),
							array( 'texto' => 'Alemanha' ),
							array( 'texto' => 'Singapura' ),
							array( 'texto' => 'México' ),
						),
					),
					'vote_sucesso_titulo' => array(
						'type'    => 'text',
						'label'   => __( 'Painel: título do sucesso', 'auvp-experience' ),
						'default' => 'Sugestão registrada.',
					),
					'vote_sucesso_texto'  => array(
						'type'    => 'textarea',
						'label'   => __( 'Painel: texto do sucesso', 'auvp-experience' ),
						'default' => 'Obrigado. Sua indicação entra na curadoria dos próximos destinos da AUVP Experience.',
					),
				),
			),

			/* ---------------------------------------------------------- */
			'diferencial'   => array(
				'title'  => __( 'AUVP — Diferencial', 'auvp-experience' ),
				'icon'   => 'eicon-image-box',
				'fields' => array(
					'ancora'  => array(
						'type'    => 'text',
						'label'   => __( 'Âncora (id)', 'auvp-experience' ),
						'default' => 'diferencial',
					),
					'eyebrow' => array(
						'type'    => 'text',
						'label'   => __( 'Chapéu', 'auvp-experience' ),
						'default' => '03 — Diferencial',
					),
					'titulo'  => array(
						'type'    => 'textarea',
						'label'   => __( 'Título', 'auvp-experience' ),
						'default' => 'O valor da Experience não está só no destino. Está no <em>acesso</em>.',
					),
					'lede'    => array(
						'type'    => 'textarea',
						'label'   => __( 'Primeiro parágrafo', 'auvp-experience' ),
						'default' => 'Negócios não existem no vácuo. Eles nascem da cultura, dos hábitos e da forma como as pessoas vivem. Por isso, cada imersão também é um mergulho no contexto local.',
					),
					'texto'   => array(
						'type'    => 'textarea',
						'label'   => __( 'Segundo parágrafo', 'auvp-experience' ),
						'default' => 'Gastronomia, rotina, comportamento, arquitetura e dinâmica social: tudo aquilo que ajuda a entender como aquele mercado pensa, consome e cresce.',
					),
					'imagem'  => array(
						'type'     => 'media',
						'label'    => __( 'Imagem', 'auvp-experience' ),
						'fallback' => 'img/context-city.svg',
					),
					'alt'     => array(
						'type'    => 'text',
						'label'   => __( 'Texto alternativo', 'auvp-experience' ),
						'default' => 'Rua noturna iluminada por luz quente',
					),
					'tags'    => array(
						'type'    => 'repeater',
						'label'   => __( 'Etiquetas', 'auvp-experience' ),
						'titulo'  => 'texto',
						'fields'  => array(
							'texto' => array(
								'type'  => 'text',
								'label' => __( 'Texto', 'auvp-experience' ),
							),
						),
						'default' => array(
							array( 'texto' => 'Gastronomia' ),
							array( 'texto' => 'Rotina' ),
							array( 'texto' => 'Comportamento' ),
							array( 'texto' => 'Arquitetura' ),
							array( 'texto' => 'Dinâmica social' ),
						),
					),
				),
			),

			/* ---------------------------------------------------------- */
			'marquee'       => array(
				'title'  => __( 'AUVP — Faixa deslizante', 'auvp-experience' ),
				'icon'   => 'eicon-animation-text',
				'fields' => array(
					'velocidade' => array(
						'type'    => 'text',
						'label'   => __( 'Velocidade', 'auvp-experience' ),
						'desc'    => __( 'Pixels por quadro. Padrão: 0.5', 'auvp-experience' ),
						'default' => '0.5',
					),
					'itens'      => array(
						'type'    => 'repeater',
						'label'   => __( 'Frases', 'auvp-experience' ),
						'titulo'  => 'texto',
						'fields'  => array(
							'texto' => array(
								'type'  => 'text',
								'label' => __( 'Frase', 'auvp-experience' ),
							),
						),
						'default' => array(
							array( 'texto' => 'Imersões exclusivas' ),
							array( 'texto' => '<em>Explore os bastidores da economia global</em>' ),
						),
					),
				),
			),

			/* ---------------------------------------------------------- */
			'networking'    => array(
				'title'  => __( 'AUVP — Networking', 'auvp-experience' ),
				'icon'   => 'eicon-share',
				'fields' => array(
					'ancora'     => array(
						'type'    => 'text',
						'label'   => __( 'Âncora (id)', 'auvp-experience' ),
						'default' => 'networking',
					),
					'eyebrow'    => array(
						'type'    => 'text',
						'label'   => __( 'Chapéu', 'auvp-experience' ),
						'default' => '04 — Networking',
					),
					'titulo'     => array(
						'type'    => 'textarea',
						'label'   => __( 'Título', 'auvp-experience' ),
						'default' => 'Você não vai sozinho. E isso <em>muda tudo</em>.',
					),
					'complemento' => array(
						'type'    => 'textarea',
						'label'   => __( 'Complemento', 'auvp-experience' ),
						'default' => 'Empresários, investidores e executivos que estão jogando o mesmo jogo que você.',
					),
					'dica'       => array(
						'type'    => 'text',
						'label'   => __( 'Dica de arraste', 'auvp-experience' ),
						'default' => 'Arraste para o lado',
					),
					'perfis'     => array(
						'type'    => 'repeater',
						'label'   => __( 'Perfis', 'auvp-experience' ),
						'titulo'  => 'titulo',
						'fields'  => array(
							'num'    => array(
								'type'  => 'text',
								'label' => __( 'Número', 'auvp-experience' ),
							),
							'titulo' => array(
								'type'  => 'text',
								'label' => __( 'Título', 'auvp-experience' ),
							),
							'texto'  => array(
								'type'  => 'textarea',
								'label' => __( 'Descrição', 'auvp-experience' ),
							),
						),
						'default' => array(
							array(
								'num'    => '01',
								'titulo' => 'Empresários',
								'texto'  => 'Quem já opera um negócio e busca referência de escala, eficiência e expansão fora do Brasil.',
							),
							array(
								'num'    => '02',
								'titulo' => 'Investidores',
								'texto'  => 'Quem aloca capital e quer entender, na fonte, as economias que definem os próximos ciclos.',
							),
							array(
								'num'    => '03',
								'titulo' => 'Executivos',
								'texto'  => 'Quem toma decisão dentro de grandes estruturas e precisa de repertório internacional real.',
							),
							array(
								'num'    => '04',
								'titulo' => 'Fundadores',
								'texto'  => 'Quem está construindo e quer ver de perto como empresas globais resolveram os mesmos problemas.',
							),
							array(
								'num'    => '05',
								'titulo' => 'Conversas que continuam',
								'texto'  => 'O grupo não termina no embarque de volta. As conexões seguem — e é aí que costuma nascer o negócio.',
							),
						),
					),
				),
			),

			/* ---------------------------------------------------------- */
			'experiencia'   => array(
				'title'  => __( 'AUVP — Experiência', 'auvp-experience' ),
				'icon'   => 'eicon-tabs',
				'fields' => array(
					'ancora'  => array(
						'type'    => 'text',
						'label'   => __( 'Âncora (id)', 'auvp-experience' ),
						'default' => 'experiencia',
					),
					'eyebrow' => array(
						'type'    => 'text',
						'label'   => __( 'Chapéu', 'auvp-experience' ),
						'default' => '05 — Experiência',
					),
					'titulo'  => array(
						'type'    => 'textarea',
						'label'   => __( 'Título', 'auvp-experience' ),
						'default' => 'Conforto existe para permitir foco. Cuidaremos de <em>tudo</em> para você.',
					),
					'itens'   => array(
						'type'    => 'repeater',
						'label'   => __( 'Itens', 'auvp-experience' ),
						'titulo'  => 'titulo',
						'fields'  => array(
							'num'     => array(
								'type'  => 'text',
								'label' => __( 'Número', 'auvp-experience' ),
							),
							'titulo'  => array(
								'type'  => 'text',
								'label' => __( 'Título', 'auvp-experience' ),
							),
							'texto'   => array(
								'type'  => 'textarea',
								'label' => __( 'Descrição', 'auvp-experience' ),
							),
							'legenda' => array(
								'type'  => 'text',
								'label' => __( 'Legenda do painel', 'auvp-experience' ),
							),
						),
						'default' => array(
							array(
								'num'     => '01',
								'titulo'  => 'Hospedagem',
								'texto'   => 'Você fica em locais selecionados estrategicamente, com conforto, localização e estrutura alinhados ao padrão da experiência, para focar no que realmente importa.',
								'legenda' => 'Hospedagem selecionada, no ponto certo da cidade.',
							),
							array(
								'num'     => '02',
								'titulo'  => 'Logística organizada',
								'texto'   => 'Deslocamentos, acessos e detalhes operacionais já estão resolvidos. Você não perde tempo com fricção, só com o que gera valor.',
								'legenda' => 'Deslocamentos e acessos resolvidos antes de você chegar.',
							),
							array(
								'num'     => '03',
								'titulo'  => 'Concierge',
								'texto'   => 'Suporte dedicado durante toda a imersão. Desde ajustes pontuais até demandas específicas, você tem com quem contar em qualquer momento.',
								'legenda' => 'Alguém disponível do primeiro ao último dia.',
							),
							array(
								'num'     => '04',
								'titulo'  => 'Agenda estruturada',
								'texto'   => 'Cada dia é planejado para equilibrar conteúdo, conexões e experiência. Um fluxo pensado para extrair o máximo de cada destino, sem sobrecarga e sem desperdício de tempo.',
								'legenda' => 'Uma agenda densa, sem ser sufocante.',
							),
						),
					),
				),
			),

			/* ---------------------------------------------------------- */
			'faq'           => array(
				'title'  => __( 'AUVP — FAQ', 'auvp-experience' ),
				'icon'   => 'eicon-help-o',
				'fields' => array(
					'ancora'    => array(
						'type'    => 'text',
						'label'   => __( 'Âncora (id)', 'auvp-experience' ),
						'default' => 'faq',
					),
					'eyebrow'   => array(
						'type'    => 'text',
						'label'   => __( 'Chapéu', 'auvp-experience' ),
						'default' => '06 — Perguntas',
					),
					'titulo'    => array(
						'type'    => 'textarea',
						'label'   => __( 'Título', 'auvp-experience' ),
						'default' => 'Antes de aplicar,<br>vale alinhar.',
					),
					'aside'     => array(
						'type'    => 'textarea',
						'label'   => __( 'Texto lateral', 'auvp-experience' ),
						'default' => 'Ficou algo de fora? Escreva para a nossa equipe pelo formulário abaixo.',
					),
					'perguntas' => array(
						'type'    => 'repeater',
						'label'   => __( 'Perguntas', 'auvp-experience' ),
						'titulo'  => 'pergunta',
						'fields'  => array(
							'pergunta' => array(
								'type'  => 'text',
								'label' => __( 'Pergunta', 'auvp-experience' ),
							),
							'resposta' => array(
								'type'  => 'textarea',
								'label' => __( 'Resposta', 'auvp-experience' ),
							),
						),
						'default' => array(
							array(
								'pergunta' => 'É passeio turístico?',
								'resposta' => 'Não. Se quiser ver pontos turísticos, saiba que esse não é o programa para você. Aqui o foco é negócios, indústria e entender a economia real.',
							),
							array(
								'pergunta' => 'Preciso ser aluno AUVP?',
								'resposta' => 'Não precisa.',
							),
							array(
								'pergunta' => 'O valor da imersão inclui passagens e hospedagem?',
								'resposta' => 'Cada missão possui um pacote específico. Geralmente, focamos em oferecer a experiência completa (hospedagem premium, deslocamentos internos e acessos exclusivos), permitindo que você se preocupe apenas com o seu aprendizado e suas conexões. Os detalhes ficam disponíveis na página de cada destino.',
							),
							array(
								'pergunta' => 'Como é feita a curadoria das empresas visitadas?',
								'resposta' => 'Nossa equipe analisa criteriosamente quais setores e companhias estão liderando a economia global no momento. Escolhemos organizações que ofereçam lições valiosas de eficiência, escala e inovação, garantindo que cada visita seja uma aula exclusiva para o nosso grupo.',
							),
							array(
								'pergunta' => 'A imersão é para mim?',
								'resposta' => 'Buscamos participantes que já possuam uma mentalidade de investidor ou empreendedor. O foco da AUVP Experience é o aprendizado prático e o networking de alto nível, por isso, estar alinhado com os princípios de valor e longo prazo é fundamental para aproveitar a jornada ao máximo.',
							),
						),
					),
				),
			),

			/* ---------------------------------------------------------- */
			'candidatura'   => array(
				'title'  => __( 'AUVP — Candidatura', 'auvp-experience' ),
				'icon'   => 'eicon-form-horizontal',
				'fields' => array(
					'ancora'         => array(
						'type'    => 'text',
						'label'   => __( 'Âncora (id)', 'auvp-experience' ),
						'default' => 'aplicar',
					),
					'eyebrow'        => array(
						'type'    => 'text',
						'label'   => __( 'Chapéu', 'auvp-experience' ),
						'default' => '07 — Candidatura',
					),
					'titulo'         => array(
						'type'    => 'textarea',
						'label'   => __( 'Título', 'auvp-experience' ),
						'default' => 'A próxima imersão não é para todo mundo. Talvez seja <em>para você</em>.',
					),
					'pontos'         => array(
						'type'    => 'repeater',
						'label'   => __( 'Pontos', 'auvp-experience' ),
						'titulo'  => 'texto',
						'fields'  => array(
							'texto' => array(
								'type'  => 'textarea',
								'label' => __( 'Texto', 'auvp-experience' ),
							),
						),
						'default' => array(
							array( 'texto' => 'Grupo reduzido, para manter o nível das conversas e o acesso real às empresas.' ),
							array( 'texto' => 'Não é passeio: é agenda de negócios, com curadoria estratégica da AUVP.' ),
							array( 'texto' => 'Perfil dos participantes: empresários, investidores e executivos com visão de longo prazo.' ),
							array( 'texto' => 'Após o envio, nossa equipe entra em contato para entender seu momento.' ),
						),
					),
					'perfis'         => array(
						'type'    => 'repeater',
						'label'   => __( 'Opções de perfil', 'auvp-experience' ),
						'titulo'  => 'texto',
						'fields'  => array(
							'texto' => array(
								'type'  => 'text',
								'label' => __( 'Opção', 'auvp-experience' ),
							),
						),
						'default' => array(
							array( 'texto' => 'Empresário' ),
							array( 'texto' => 'Investidor' ),
							array( 'texto' => 'Executivo' ),
							array( 'texto' => 'Fundador' ),
							array( 'texto' => 'Outro' ),
						),
					),
					'destinos'       => array(
						'type'    => 'repeater',
						'label'   => __( 'Opções de destino', 'auvp-experience' ),
						'titulo'  => 'texto',
						'fields'  => array(
							'texto' => array(
								'type'  => 'text',
								'label' => __( 'Opção', 'auvp-experience' ),
							),
						),
						'default' => array(
							array( 'texto' => 'China' ),
							array( 'texto' => 'Chile' ),
							array( 'texto' => 'Próximos destinos' ),
							array( 'texto' => 'Ainda decidindo' ),
						),
					),
					'botao'          => array(
						'type'    => 'text',
						'label'   => __( 'Texto do botão', 'auvp-experience' ),
						'default' => 'Enviar candidatura',
					),
					'nota'           => array(
						'type'    => 'textarea',
						'label'   => __( 'Nota abaixo do botão', 'auvp-experience' ),
						'default' => 'Suas informações são usadas apenas para o contato sobre a imersão.',
					),
					'sucesso_titulo' => array(
						'type'    => 'text',
						'label'   => __( 'Título do sucesso', 'auvp-experience' ),
						'default' => 'Recebemos sua candidatura.',
					),
					'sucesso_texto'  => array(
						'type'    => 'textarea',
						'label'   => __( 'Texto do sucesso', 'auvp-experience' ),
						'default' => 'Nossa equipe vai analisar seu perfil e entrar em contato com os próximos passos e os detalhes da imersão.',
					),
				),
			),

			/* ---------------------------------------------------------- */
			'navegacao'     => array(
				'title'  => __( 'AUVP — Navegação e overlays', 'auvp-experience' ),
				'icon'   => 'eicon-nav-menu',
				'fields' => array(
					'marca_forte' => array(
						'type'    => 'text',
						'label'   => __( 'Marca (destaque)', 'auvp-experience' ),
						'default' => 'AUVP',
					),
					'marca_leve'  => array(
						'type'    => 'text',
						'label'   => __( 'Marca (complemento)', 'auvp-experience' ),
						'default' => 'Experience',
					),
					'marca_link'  => array(
						'type'    => 'url',
						'label'   => __( 'Link da marca', 'auvp-experience' ),
						'default' => '#hero',
					),
					'cta_label'   => array(
						'type'    => 'text',
						'label'   => __( 'Botão', 'auvp-experience' ),
						'default' => 'Aplicar',
					),
					'cta_link'    => array(
						'type'    => 'url',
						'label'   => __( 'Link do botão', 'auvp-experience' ),
						'default' => '#aplicar',
					),
					'preloader'   => array(
						'type'    => 'select',
						'label'   => __( 'Preloader', 'auvp-experience' ),
						'options' => array(
							'sim' => __( 'Exibir', 'auvp-experience' ),
							'nao' => __( 'Não exibir', 'auvp-experience' ),
						),
						'default' => 'sim',
					),
					'preloader_texto' => array(
						'type'    => 'text',
						'label'   => __( 'Texto do preloader', 'auvp-experience' ),
						'default' => 'Experience',
					),
					'links'       => array(
						'type'    => 'repeater',
						'label'   => __( 'Links', 'auvp-experience' ),
						'titulo'  => 'label',
						'fields'  => array(
							'label' => array(
								'type'  => 'text',
								'label' => __( 'Rótulo', 'auvp-experience' ),
							),
							'link'  => array(
								'type'  => 'url',
								'label' => __( 'Link', 'auvp-experience' ),
							),
						),
						'default' => array(
							array(
								'label' => 'Imersões',
								'link'  => '#imersoes',
							),
							array(
								'label' => 'Diferencial',
								'link'  => '#diferencial',
							),
							array(
								'label' => 'Networking',
								'link'  => '#networking',
							),
							array(
								'label' => 'Experiência',
								'link'  => '#experiencia',
							),
							array(
								'label' => 'FAQ',
								'link'  => '#faq',
							),
						),
					),
				),
			),

			/* ---------------------------------------------------------- */
			'rodape'        => array(
				'title'  => __( 'AUVP — Rodapé', 'auvp-experience' ),
				'icon'   => 'eicon-footer',
				'fields' => array(
					'palavra'  => array(
						'type'    => 'text',
						'label'   => __( 'Palavra de fundo', 'auvp-experience' ),
						'default' => 'Experience',
					),
					'assinatura' => array(
						'type'    => 'text',
						'label'   => __( 'Assinatura', 'auvp-experience' ),
						'default' => 'AUVP Experience Co.',
					),
					'tagline'  => array(
						'type'    => 'text',
						'label'   => __( 'Tagline', 'auvp-experience' ),
						'default' => 'Imersões estratégicas globais',
					),
					'col1_titulo' => array(
						'type'    => 'text',
						'label'   => __( 'Coluna 1: título', 'auvp-experience' ),
						'default' => 'Imersões',
					),
					'col1_links'  => array(
						'type'    => 'repeater',
						'label'   => __( 'Coluna 1: links', 'auvp-experience' ),
						'titulo'  => 'label',
						'fields'  => array(
							'label' => array(
								'type'  => 'text',
								'label' => __( 'Rótulo', 'auvp-experience' ),
							),
							'link'  => array(
								'type'  => 'url',
								'label' => __( 'Link', 'auvp-experience' ),
							),
						),
						'default' => array(
							array(
								'label' => 'China',
								'link'  => '/imersoes/china/',
							),
							array(
								'label' => 'Chile',
								'link'  => '/imersoes/chile/',
							),
							array(
								'label' => 'Próximos destinos',
								'link'  => '#imersoes',
							),
						),
					),
					'col2_titulo' => array(
						'type'    => 'text',
						'label'   => __( 'Coluna 2: título', 'auvp-experience' ),
						'default' => 'Navegar',
					),
					'col2_links'  => array(
						'type'    => 'repeater',
						'label'   => __( 'Coluna 2: links', 'auvp-experience' ),
						'titulo'  => 'label',
						'fields'  => array(
							'label' => array(
								'type'  => 'text',
								'label' => __( 'Rótulo', 'auvp-experience' ),
							),
							'link'  => array(
								'type'  => 'url',
								'label' => __( 'Link', 'auvp-experience' ),
							),
						),
						'default' => array(
							array(
								'label' => 'Diferencial',
								'link'  => '#diferencial',
							),
							array(
								'label' => 'Networking',
								'link'  => '#networking',
							),
							array(
								'label' => 'Experiência',
								'link'  => '#experiencia',
							),
							array(
								'label' => 'FAQ',
								'link'  => '#faq',
							),
						),
					),
					'col3_titulo' => array(
						'type'    => 'text',
						'label'   => __( 'Coluna 3: título', 'auvp-experience' ),
						'default' => 'Contato',
					),
					'col3_links'  => array(
						'type'    => 'repeater',
						'label'   => __( 'Coluna 3: links', 'auvp-experience' ),
						'titulo'  => 'label',
						'fields'  => array(
							'label' => array(
								'type'  => 'text',
								'label' => __( 'Rótulo', 'auvp-experience' ),
							),
							'link'  => array(
								'type'  => 'url',
								'label' => __( 'Link', 'auvp-experience' ),
							),
						),
						'default' => array(
							array(
								'label' => 'Aplicar para a próxima imersão',
								'link'  => '#aplicar',
							),
							array(
								'label' => 'Instagram',
								'link'  => '',
							),
							array(
								'label' => 'LinkedIn',
								'link'  => '',
							),
						),
					),
				),
			),
		);

		/**
		 * Permite adicionar, remover ou alterar seções e campos.
		 *
		 * @param array $sections Registro de seções.
		 */
		self::$sections = apply_filters( 'auvp_sections', $sections );

		return self::$sections;
	}

	/**
	 * Devolve a definição de uma seção.
	 *
	 * @param string $slug Slug da seção.
	 * @return array|null
	 */
	public static function section( $slug ) {
		$sections = self::sections();

		return isset( $sections[ $slug ] ) ? $sections[ $slug ] : null;
	}

	/**
	 * Monta o array de dados padrão de uma seção.
	 *
	 * @param string $slug Slug da seção.
	 * @return array
	 */
	public static function defaults( $slug ) {
		$section = self::section( $slug );
		$data    = array();

		if ( ! $section ) {
			return $data;
		}

		foreach ( $section['fields'] as $key => $field ) {
			if ( isset( $field['default'] ) ) {
				$data[ $key ] = $field['default'];
			} elseif ( 'repeater' === $field['type'] ) {
				$data[ $key ] = array();
			} else {
				$data[ $key ] = '';
			}
		}

		return $data;
	}

	/**
	 * Slugs das seções na ordem da página completa.
	 *
	 * @return array
	 */
	public static function page_order() {
		return apply_filters(
			'auvp_page_order',
			array( 'hero', 'posicionamento', 'destinos', 'diferencial', 'marquee', 'networking', 'experiencia', 'faq', 'candidatura' )
		);
	}
}
