/* Interações da AUVP Experience
   ==========================================================
   O site foi feito inteiro em CSS e só ganhou este arquivo quando
   apareceu um pedido que CSS não faz: que o passeio automático das duas
   roletas continue depois de um clique. CSS não sabe trocar o estado de
   um radio, então o passeio era uma animação por fora — e enquanto ela
   rodava, o que estava na tela não era o estado marcado. Era daí que
   vinham as duas queixas: a ordem dos cartões "quebrando" e o clique nas
   abas parecendo não pegar.

   A regra aqui é: o JavaScript comanda o estado, o CSS continua
   desenhando. Sem o arquivo, as duas dobras seguem utilizáveis — a
   roleta vira uma fila que rola de lado com o dedo, as abas continuam
   trocando no clique. Nada depende de script para ser lido.

   Quem liga o modo com script é a classe `auvp-js`, posta no <html> por
   uma linha no <head>, antes da primeira pintura, para não haver salto.
*/
(() => {
  'use strict';

  const semMovimento = window.matchMedia('(prefers-reduced-motion: reduce)');

  /* Relógio que só anda quando a dobra está na tela, a aba do navegador
     está à frente e ninguém está com o cursor na `zona` — a área que
     segura o passeio, que nem sempre é a dobra inteira. Na roleta é só a
     fila de cartões: parar por causa do cursor em cima do título, longe
     dos cartões, era parar sem motivo. Qualquer ação manual reinicia a
     contagem — é isso que faz o passeio continuar depois do clique. */
  function relogio(raiz, passo, intervalo, zona) {
    let id = null;
    let naTela = false;
    let parado = false;

    const parar = () => { clearInterval(id); id = null; };
    const tocar = () => {
      parar();
      if (naTela && !parado && !semMovimento.matches && !document.hidden) {
        id = setInterval(passo, intervalo);
      }
    };

    if ('IntersectionObserver' in window) {
      new IntersectionObserver((entradas) => {
        naTela = entradas[0].isIntersecting;
        tocar();
      }, { threshold: 0.2 }).observe(raiz);
    } else {
      naTela = true;
      tocar();
    }

    const segurar = () => { parado = true; parar(); };
    const soltar = () => { parado = false; tocar(); };
    const area = zona || raiz;
    area.addEventListener('pointerenter', segurar);
    area.addEventListener('pointerleave', soltar);
    area.addEventListener('focusin', segurar);
    area.addEventListener('focusout', soltar);
    document.addEventListener('visibilitychange', tocar);
    semMovimento.addEventListener('change', tocar);

    return { reiniciar: tocar };
  }

  /* ---- Roleta do posicionamento ----
     A fila está duplicada no HTML. Avançar anda um cartão; quando o
     trilho para sobre a cópia do primeiro, ele volta ao começo sem
     transição — como o que está na tela é idêntico, o pulo não aparece.
     É esse ida-e-volta silencioso que fecha o laço sem rebobinar. */
  function roleta(raiz) {
    const trilho = raiz.querySelector('.auvp-roleta__trilho');
    const cartoes = Array.from(trilho.children);
    const total = cartoes.length / 2;
    const anterior = raiz.querySelector('.auvp-roleta__bt--anterior');
    const proximo = raiz.querySelector('.auvp-roleta__bt--proximo');
    if (!trilho || total < 2 || !anterior || !proximo) return;

    let i = 0;

    const passo = () =>
      cartoes[1].getBoundingClientRect().left - cartoes[0].getBoundingClientRect().left;

    const mover = (comTransicao) => {
      if (!comTransicao) trilho.style.transition = 'none';
      trilho.style.transform = `translateX(${-i * passo()}px)`;
      if (!comTransicao) {
        void trilho.offsetWidth;   // força o reflow: sem isso o 'none' não vale
        trilho.style.transition = '';
      }
    };

    const avancar = () => {
      i += 1;
      mover(true);
      if (i < total) return;
      // parou sobre a cópia do primeiro cartão: volta ao começo calado
      const fim = (e) => {
        if (e.target !== trilho || e.propertyName !== 'transform') return;
        trilho.removeEventListener('transitionend', fim);
        i = 0;
        mover(false);
      };
      trilho.addEventListener('transitionend', fim);
    };

    const voltar = () => {
      if (i === 0) {               // salta para a cópia e desce dali
        i = total;
        mover(false);
      }
      i -= 1;
      mover(true);
    };

    const janela = raiz.querySelector('.auvp-roleta__janela');
    const conta = relogio(raiz, avancar, 2500, janela);
    proximo.addEventListener('click', () => { avancar(); conta.reiniciar(); });
    anterior.addEventListener('click', () => { voltar(); conta.reiniciar(); });

    let redesenho;
    window.addEventListener('resize', () => {
      clearTimeout(redesenho);
      redesenho = setTimeout(() => { if (i >= total) i = 0; mover(false); }, 150);
    });

    mover(false);
  }

  /* ---- Abas da Experiência ----
     Aqui o estado continua nos radios, do jeito que já era: o relógio só
     marca o próximo. Clicar numa aba dispara o `change`, que reinicia a
     contagem — o passeio segue de onde a pessoa parou, em vez de morrer
     no primeiro clique. */
  function abas(painel) {
    const radios = Array.from(painel.querySelectorAll('input[type="radio"]'));
    if (radios.length < 2) return;

    const avancar = () => {
      const atual = radios.findIndex((r) => r.checked);
      radios[(atual + 1) % radios.length].checked = true;
    };

    const conta = relogio(painel, avancar, 4000);
    radios.forEach((r) => r.addEventListener('change', conta.reiniciar));
  }

  /* ---- Caça-níquel do networking ----
     O rolo gira enquanto a dobra atravessa a tela: o centro dela indo de
     85% da altura da janela até 15% é o curso inteiro do giro. Não há
     palco grudado — a dobra tem a altura do que tem dentro, como as
     outras, e é isso que evita a tela quase vazia que o pin cobrava.

     A posição é contínua (o rolo acompanha o dedo, sem pulos) e o perfil
     mais perto do centro é o que fica em destaque. Quem desenha é o
     CSS: daqui saem só o `--pos` e a classe `esta-ativo`. */
  function cacaNiquel(pin) {
    const rolo = pin.querySelector('.auvp-net__rolo');
    if (!rolo) return;
    const itens = Array.from(rolo.children);
    if (itens.length < 2) return;
    pin.style.setProperty('--n', itens.length);

    let ativo = -1;
    let pedido = null;

    const medir = () => {
      const caixa = pin.getBoundingClientRect();
      const tela = window.innerHeight;
      const centro = caixa.top + caixa.height / 2;
      const inicio = tela * 0.85;                   // centro aqui: primeiro perfil
      const fim = tela * 0.15;                      // centro aqui: último
      const bruto = (inicio - centro) / (inicio - fim);
      const pos = Math.min(Math.max(bruto, 0), 1) * (itens.length - 1);
      rolo.style.setProperty('--pos', pos.toFixed(4));
      const perto = Math.round(pos);
      if (perto === ativo) return;
      if (itens[ativo]) itens[ativo].classList.remove('esta-ativo');
      itens[perto].classList.add('esta-ativo');
      ativo = perto;
    };

    const agendar = () => {
      if (pedido) return;
      pedido = requestAnimationFrame(() => { pedido = null; medir(); });
    };

    window.addEventListener('scroll', agendar, { passive: true });
    window.addEventListener('resize', agendar);
    medir();
  }

  document.querySelectorAll('.auvp-roleta').forEach(roleta);
  document.querySelectorAll('.auvp-exp__panel').forEach(abas);
  if (!semMovimento.matches) document.querySelectorAll('.auvp-net__pin').forEach(cacaNiquel);
})();
