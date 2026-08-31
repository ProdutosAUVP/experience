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
     está à frente e ninguém está com o cursor em cima. Passar o cursor
     segura; tirar, volta a andar. Qualquer ação manual reinicia a
     contagem — é isso que faz o passeio continuar depois do clique. */
  function relogio(raiz, passo, intervalo) {
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
    raiz.addEventListener('pointerenter', segurar);
    raiz.addEventListener('pointerleave', soltar);
    raiz.addEventListener('focusin', segurar);
    raiz.addEventListener('focusout', soltar);
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

    const conta = relogio(raiz, avancar, 4000);
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

  document.querySelectorAll('.auvp-roleta').forEach(roleta);
  document.querySelectorAll('.auvp-exp__panel').forEach(abas);
})();
