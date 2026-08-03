/* =============================================================
   AUVP EXPERIENCE — Interações
   Sem dependências externas. Um único loop de rAF para efeitos
   ligados ao scroll (parallax, marquee, progresso, índice lateral).
   ============================================================= */
(function () {
  "use strict";

  /* -----------------------------------------------------------
     CONFIGURAÇÃO

     No WordPress o plugin injeta window.AUVP_WP com a rota REST e o
     nonce — nada precisa ser editado aqui. Fora dele (site estático),
     ajuste os valores abaixo antes de publicar.
     ----------------------------------------------------------- */
  var WP = window.AUVP_WP || {};

  var CONFIG = {
    // Endpoint que recebe o POST dos formulários.
    // Vazio = fallback para e-mail (abre o cliente de e-mail do usuário).
    // Ex.: 'https://formspree.io/f/xxxxxxx' ou uma rota própria.
    formEndpoint: WP.endpoint || "",
    // Confirme este endereço antes de publicar.
    contactEmail: WP.email || "experience@auvp.com.br",
    // Nonce do REST do WordPress, quando disponível.
    nonce: WP.nonce || "",
  };

  /* Marca um elemento como já inicializado. Evita ligar o mesmo listener
     duas vezes quando o Elementor re-renderiza um widget no editor. */
  function once(el, key) {
    if (!el || el.dataset["auvp" + key]) return false;
    el.dataset["auvp" + key] = "1";
    return true;
  }

  /* ---------- utilidades ---------- */
  var $ = function (sel, ctx) { return (ctx || document).querySelector(sel); };
  var $$ = function (sel, ctx) { return Array.prototype.slice.call((ctx || document).querySelectorAll(sel)); };
  var clamp = function (v, a, b) { return Math.min(b, Math.max(a, v)); };
  var lerp = function (a, b, t) { return a + (b - a) * t; };

  var reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  var finePointer = window.matchMedia("(hover: hover) and (pointer: fine)").matches;

  /* -----------------------------------------------------------
     1. Preloader
     ----------------------------------------------------------- */
  function preloader() {
    var el = $(".auvp-preloader");
    if (!el) return Promise.resolve();

    var fill = $(".auvp-preloader__fill", el);
    var count = $(".auvp-preloader__count", el);
    var mark = $(".auvp-preloader__mark", el);

    // anima cada letra do wordmark
    if (mark && !mark.dataset.split) {
      mark.dataset.split = "1";
      mark.innerHTML = mark.textContent.replace(/\S/g, function (c) { return "<span>" + c + "</span>"; });
      $$("span", mark).forEach(function (s, i) { s.style.animationDelay = i * 28 + "ms"; });
    }

    return new Promise(function (resolve) {
      var done = function () {
        el.classList.add("auvp-is-done");
        document.body.classList.remove("auvp-is-locked");
        window.setTimeout(resolve, reduceMotion ? 0 : 420);
      };

      if (reduceMotion) { done(); return; }

      document.body.classList.add("auvp-is-locked");
      var p = 0;
      var tick = function () {
        p = Math.min(100, p + Math.random() * 9 + 2.5);
        if (fill) fill.style.transform = "scaleX(" + p / 100 + ")";
        if (count) count.textContent = String(Math.round(p)).padStart(3, "0");
        if (p < 100) {
          window.setTimeout(tick, 70 + Math.random() * 90);
        } else {
          window.setTimeout(done, 420);
        }
      };
      window.setTimeout(tick, 260);
    });
  }

  /* -----------------------------------------------------------
     2. Split de texto em palavras (para revelação em máscara)
     ----------------------------------------------------------- */
  function splitWords() {
    $$("[data-split]").forEach(function (el) {
      if (el.dataset.done) return;
      el.dataset.done = "1";
      el.classList.add("auvp-split");

      var i = 0;
      var walk = function (node) {
        var out = document.createDocumentFragment();
        Array.prototype.slice.call(node.childNodes).forEach(function (child) {
          if (child.nodeType === 3) {
            var parts = child.textContent.split(/(\s+)/);
            parts.forEach(function (part) {
              if (!part) return;
              if (/^\s+$/.test(part)) { out.appendChild(document.createTextNode(" ")); return; }
              var w = document.createElement("span");
              w.className = "auvp-word";
              var inner = document.createElement("span");
              inner.textContent = part;
              inner.style.setProperty("--i", i++);
              w.appendChild(inner);
              out.appendChild(w);
            });
          } else if (child.nodeType === 1) {
            // preserva tags inline (<em>, <br>) refazendo o conteúdo dentro
            var clone = child.cloneNode(false);
            if (child.tagName === "BR") { out.appendChild(clone); return; }
            clone.appendChild(walk(child));
            out.appendChild(clone);
          }
        });
        return out;
      };

      var frag = walk(el);
      el.innerHTML = "";
      el.appendChild(frag);
    });
  }

  /* -----------------------------------------------------------
     3. Revelação por IntersectionObserver
     ----------------------------------------------------------- */
  function reveals() {
    var targets = $$("[data-reveal], [data-split], .auvp-media-mask");
    if (!("IntersectionObserver" in window) || reduceMotion) {
      targets.forEach(function (t) { t.classList.add("auvp-is-in"); });
      return;
    }

    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (!e.isIntersecting) return;
        e.target.classList.add("auvp-is-in");
        io.unobserve(e.target);
      });
    }, { rootMargin: "0px 0px -12% 0px", threshold: 0.08 });

    targets.forEach(function (t) {
      var d = t.getAttribute("data-delay");
      if (d) t.style.setProperty("--delay", d + "ms");
      io.observe(t);
    });
  }

  /* -----------------------------------------------------------
     4. Navegação: fundo sólido, auto-hide e menu fullscreen
     ----------------------------------------------------------- */
  function navigation() {
    var nav = $(".auvp-nav");
    var menu = $(".auvp-menu");
    var burger = $(".auvp-burger");
    if (!nav) return;

    var last = window.scrollY;

    var onScroll = function () {
      var y = window.scrollY;
      nav.classList.toggle("auvp-is-solid", y > 40);
      if (!document.body.classList.contains("auvp-menu-open")) {
        nav.classList.toggle("auvp-is-hidden", y > 400 && y > last + 4);
      }
      last = y;
    };
    window.addEventListener("scroll", onScroll, { passive: true });
    onScroll();

    if (!menu || !burger) return;

    var setMenu = function (open) {
      document.body.classList.toggle("auvp-menu-open", open);
      document.body.classList.toggle("auvp-is-locked", open);
      menu.classList.toggle("auvp-is-open", open);
      burger.setAttribute("aria-expanded", String(open));
      $(".auvp-burger__text", burger).textContent = open ? "Fechar" : "Menu";
      if (open) nav.classList.remove("auvp-is-hidden");
    };

    burger.addEventListener("click", function () {
      setMenu(!document.body.classList.contains("auvp-menu-open"));
    });

    $$(".auvp-menu__link, .auvp-menu a", menu).forEach(function (a) {
      a.addEventListener("click", function () { setMenu(false); });
    });

    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && document.body.classList.contains("auvp-menu-open")) setMenu(false);
    });
  }

  /* -----------------------------------------------------------
     5. Cursor customizado + magnetismo
     ----------------------------------------------------------- */
  function cursor() {
    if (!finePointer || reduceMotion) return;

    var dot = $(".auvp-cursor");
    var ring = $(".auvp-cursor-ring");
    var label = $(".auvp-cursor-ring__label");
    if (!dot || !ring) return;

    var mx = window.innerWidth / 2, my = window.innerHeight / 2;
    var rx = mx, ry = my;

    document.addEventListener("mousemove", function (e) {
      mx = e.clientX; my = e.clientY;
      document.body.classList.add("auvp-has-cursor");
    }, { passive: true });

    document.addEventListener("mouseleave", function () {
      document.body.classList.remove("auvp-has-cursor");
    });

    var render = function () {
      rx = lerp(rx, mx, 0.16);
      ry = lerp(ry, my, 0.16);
      dot.style.transform = "translate3d(" + mx + "px," + my + "px,0)";
      ring.style.transform = "translate3d(" + rx + "px," + ry + "px,0)";
    };
    loopAdd(render);

    var hoverSel = "a, button, input, select, textarea, .auvp-chip, [data-cursor]";
    document.addEventListener("mouseover", function (e) {
      var t = e.target.closest(hoverSel);
      if (!t) return;
      document.body.classList.add("auvp-cursor-hover");
      var txt = t.getAttribute("data-cursor");
      if (txt && label) {
        label.textContent = txt;
        document.body.classList.add("auvp-cursor-label");
      }
    });

    document.addEventListener("mouseout", function (e) {
      if (!e.target.closest(hoverSel)) return;
      document.body.classList.remove("auvp-cursor-hover", "auvp-cursor-label");
    });

    // magnetismo
    $$("[data-magnetic]").forEach(function (el) {
      var strength = parseFloat(el.getAttribute("data-magnetic")) || 0.28;
      el.addEventListener("mousemove", function (e) {
        var r = el.getBoundingClientRect();
        var x = (e.clientX - (r.left + r.width / 2)) * strength;
        var y = (e.clientY - (r.top + r.height / 2)) * strength;
        el.style.transform = "translate3d(" + x + "px," + y + "px,0)";
      });
      el.addEventListener("mouseleave", function () {
        el.style.transform = "";
        el.style.transition = "transform .6s cubic-bezier(.22,1,.36,1)";
        window.setTimeout(function () { el.style.transition = ""; }, 620);
      });
    });
  }

  /* -----------------------------------------------------------
     6. Loop único de rAF
     ----------------------------------------------------------- */
  var loopFns = [];
  var loopRunning = false;

  function loopAdd(fn) {
    loopFns.push(fn);
    if (!loopRunning) {
      loopRunning = true;
      var frame = function () {
        for (var i = 0; i < loopFns.length; i++) loopFns[i]();
        window.requestAnimationFrame(frame);
      };
      window.requestAnimationFrame(frame);
    }
  }

  /* Velocidade de scroll compartilhada */
  var scroll = { y: window.scrollY, vel: 0 };
  function scrollTracker() {
    var prev = window.scrollY;
    loopAdd(function () {
      scroll.y = window.scrollY;
      scroll.vel = lerp(scroll.vel, scroll.y - prev, 0.25);
      prev = scroll.y;
    });
  }

  /* -----------------------------------------------------------
     7. Parallax
     ----------------------------------------------------------- */
  var parallaxItems = [];
  var parallaxRunning = false;

  function parallax() {
    if (reduceMotion) return;

    $$("[data-parallax]").forEach(function (el) {
      if (!once(el, "Parallax")) return;
      parallaxItems.push({ el: el, f: parseFloat(el.getAttribute("data-parallax")) || 0.12, y: 0 });
    });

    if (!parallaxItems.length || parallaxRunning) return;
    parallaxRunning = true;

    loopAdd(function () {
      var vh = window.innerHeight;
      parallaxItems.forEach(function (it) {
        var r = it.el.getBoundingClientRect();
        if (r.bottom < -vh * 0.4 || r.top > vh * 1.4) return;
        var center = r.top + r.height / 2;
        var target = (center - vh / 2) * -it.f;
        it.y = lerp(it.y, target, 0.12);
        it.el.style.transform = "translate3d(0," + it.y.toFixed(2) + "px,0)";
      });
    });
  }

  /* -----------------------------------------------------------
     8. Barra de progresso + índice lateral
     ----------------------------------------------------------- */
  function progress() {
    var bar = $(".auvp-progress__bar");
    var nav = $(".auvp-dotnav");
    var items = nav ? $$(".auvp-dotnav__item", nav) : [];
    var sections = items.map(function (a) { return $(a.getAttribute("href")); });

    loopAdd(function () {
      var max = document.documentElement.scrollHeight - window.innerHeight;
      var p = max > 0 ? clamp(scroll.y / max, 0, 1) : 0;
      if (bar) bar.style.transform = "scaleX(" + p + ")";

      if (!nav) return;
      nav.classList.toggle("auvp-is-visible", scroll.y > window.innerHeight * 0.7);

      var active = -1;
      sections.forEach(function (s, i) {
        if (!s) return;
        var r = s.getBoundingClientRect();
        if (r.top <= window.innerHeight * 0.45) active = i;
      });
      items.forEach(function (a, i) { a.classList.toggle("auvp-is-active", i === active); });
    });
  }

  /* -----------------------------------------------------------
     9. Marquee infinito, com aceleração pelo scroll
     ----------------------------------------------------------- */
  function marquee() {
    $$(".auvp-marquee").forEach(function (root) {
      if (!once(root, "Marquee")) return;

      var track = $(".auvp-marquee__track", root);
      if (!track) return;

      var base = track.innerHTML;
      var guard = 0;
      while (track.scrollWidth < root.offsetWidth * 2 && guard++ < 12) {
        track.insertAdjacentHTML("beforeend", base);
      }

      var half = track.scrollWidth / 2;
      var x = 0;
      var speed = parseFloat(root.getAttribute("data-speed")) || 0.55;

      if (reduceMotion) return;

      loopAdd(function () {
        x -= speed + Math.abs(scroll.vel) * 0.14;
        if (half > 0 && x <= -half) x += half;
        track.style.transform = "translate3d(" + x.toFixed(2) + "px,0,0)";
      });

      window.addEventListener("resize", function () { half = track.scrollWidth / 2; }, { passive: true });
    });
  }

  /* -----------------------------------------------------------
     10. Acordeão (FAQ e afins)
     ----------------------------------------------------------- */
  function accordion() {
    $$("[data-acc]").forEach(function (root) {
      if (!once(root, "Acc")) return;

      var single = root.getAttribute("data-acc") === "single";

      $$(".auvp-acc__trigger", root).forEach(function (btn) {
        btn.addEventListener("click", function () {
          var item = btn.closest(".auvp-acc__item");
          var open = item.classList.contains("auvp-is-open");

          if (single) {
            $$(".auvp-acc__item", root).forEach(function (o) {
              o.classList.remove("auvp-is-open");
              $(".auvp-acc__trigger", o).setAttribute("aria-expanded", "false");
            });
          }

          item.classList.toggle("auvp-is-open", !open);
          btn.setAttribute("aria-expanded", String(!open));
        });
      });
    });
  }

  /* -----------------------------------------------------------
     11. Seção "Experiência": lista + palco visual
     ----------------------------------------------------------- */
  function careTabs() {
    // Escopo por instância: no WordPress pode haver mais de uma seção
    // "Experiência" na mesma página.
    $$(".auvp-care__list").forEach(function (list) {
      if (!once(list, "Care")) return;

      var scope = list.closest(".auvp-care__grid") || document;
      var items = $$(".auvp-care__item", list);
      var plates = $$(".auvp-care__plate", scope);

      var indexOfActive = function () {
        for (var i = 0; i < items.length; i++) {
          if (items[i].classList.contains("auvp-is-active")) return i;
        }
        return -1;
      };

      var activate = function (i) {
        items.forEach(function (it, k) {
          var on = k === i;
          it.classList.toggle("auvp-is-active", on);
          it.setAttribute("aria-expanded", String(on));
        });
        plates.forEach(function (p, k) { p.classList.toggle("auvp-is-on", k === i); });
      };

      items.forEach(function (it, i) {
        it.addEventListener("click", function () {
          activate(i === indexOfActive() ? -1 : i);
        });
        if (finePointer) {
          it.addEventListener("mouseenter", function () { activate(i); });
        }
      });

      activate(0);
    });
  }

  /* -----------------------------------------------------------
     12. Faixa horizontal arrastável (networking)
     ----------------------------------------------------------- */
  function dragScroll() {
    $$(".auvp-hscroll").forEach(function (root) {
      if (!once(root, "Drag")) return;

      var track = $(".auvp-hscroll__track", root);
      var hint = $(".auvp-hscroll__hint span", root.parentNode) || $(".auvp-hscroll__hint span");
      if (!track) return;

      var x = 0, target = 0, maxX = 0, down = false, startX = 0, startTarget = 0;

      var measure = function () {
        maxX = Math.max(0, track.scrollWidth - root.offsetWidth);
        target = clamp(target, -maxX, 0);
      };
      measure();
      window.addEventListener("resize", measure, { passive: true });

      root.addEventListener("pointerdown", function (e) {
        down = true;
        startX = e.clientX;
        startTarget = target;
        root.classList.add("auvp-is-dragging");
        root.setPointerCapture(e.pointerId);
      });

      root.addEventListener("pointermove", function (e) {
        if (!down) return;
        target = clamp(startTarget + (e.clientX - startX), -maxX, 0);
      });

      var release = function (e) {
        if (!down) return;
        down = false;
        root.classList.remove("auvp-is-dragging");
        if (e && e.pointerId != null && root.hasPointerCapture && root.hasPointerCapture(e.pointerId)) {
          root.releasePointerCapture(e.pointerId);
        }
      };
      root.addEventListener("pointerup", release);
      root.addEventListener("pointercancel", release);
      root.addEventListener("pointerleave", release);

      // roda do mouse na horizontal
      root.addEventListener("wheel", function (e) {
        if (Math.abs(e.deltaX) <= Math.abs(e.deltaY)) return;
        e.preventDefault();
        target = clamp(target - e.deltaX, -maxX, 0);
      }, { passive: false });

      loopAdd(function () {
        x = reduceMotion ? target : lerp(x, target, 0.12);
        track.style.transform = "translate3d(" + x.toFixed(2) + "px,0,0)";
        if (hint && maxX > 0) hint.style.setProperty("--p", (-x / maxX).toFixed(3));
      });
    });
  }

  /* -----------------------------------------------------------
     13. Painel "próximos destinos" + chips
     ----------------------------------------------------------- */
  function destinationVote() {
    // O painel é localizado por aria-controls. No WordPress o id é gerado
    // por instância do widget, então não dá para fixá-lo no seletor.
    var resolve = function (toggle) {
      var id = toggle.getAttribute("aria-controls");
      return (id && document.getElementById(id)) || $(".auvp-vote");
    };

    $$("[data-vote-open]").forEach(function (toggle) {
      if (!once(toggle, "Vote")) return;

      var panel = resolve(toggle);
      if (!panel) return;

      toggle.addEventListener("click", function (e) {
        e.preventDefault();

        if (!panel.classList.contains("auvp-is-open")) {
          panel.classList.add("auvp-is-open");
          panel.setAttribute("aria-hidden", "false");
          panel.removeAttribute("inert");

          $$('[data-vote-open][aria-controls="' + panel.id + '"]').forEach(function (t) {
            if (t.hasAttribute("aria-expanded")) t.setAttribute("aria-expanded", "true");
          });

          window.setTimeout(function () {
            panel.scrollIntoView({ behavior: reduceMotion ? "auto" : "smooth", block: "center" });
            var first = $(".auvp-chip", panel);
            if (first) first.focus();
          }, 120);
          return;
        }

        panel.scrollIntoView({ behavior: reduceMotion ? "auto" : "smooth", block: "center" });
      });
    });

    $$(".auvp-vote").forEach(function (panel) {
      if (!once(panel, "Chips")) return;

      var sync = function () {
        var hidden = $("[data-vote-selected]", panel);
        if (!hidden) return;
        hidden.value = $$(".auvp-chip.auvp-is-on", panel)
          .map(function (c) { return c.textContent.trim(); })
          .join(", ");
      };

      $$(".auvp-chip", panel).forEach(function (chip) {
        chip.addEventListener("click", function () {
          var on = chip.classList.toggle("auvp-is-on");
          chip.setAttribute("aria-pressed", String(on));
          sync();
        });
      });
    });
  }

  /* -----------------------------------------------------------
     14. Formulários
     ----------------------------------------------------------- */
  function forms() {
    $$("form[data-form]").forEach(function (form) {
      if (!once(form, "Form")) return;

      var status = $(".auvp-form__status", form);
      var success = document.getElementById(form.getAttribute("data-success") || "");
      var submit = $("[type=submit]", form);

      form.setAttribute("novalidate", "novalidate");

      form.addEventListener("submit", function (e) {
        e.preventDefault();
        if (!validate(form)) {
          if (status) status.textContent = "Revise os campos destacados.";
          return;
        }

        var data = {};
        new FormData(form).forEach(function (v, k) { data[k] = typeof v === "string" ? v.trim() : v; });
        data._tipo = form.getAttribute("data-tipo") || "candidatura";
        data._origem = window.location.href;

        if (status) status.textContent = "Enviando…";
        if (submit) submit.disabled = true;

        var finish = function (msg, ok) {
          if (submit) submit.disabled = false;
          if (status) status.textContent = msg;
          if (ok && success) {
            form.classList.add("auvp-is-sent");
            success.classList.add("auvp-is-shown");
            success.setAttribute("tabindex", "-1");
            success.focus();
          }
        };

        if (!CONFIG.formEndpoint) {
          // Fallback sem back-end: abre o cliente de e-mail com os dados preenchidos.
          // Campos técnicos (prefixo _) ficam de fora do corpo da mensagem.
          var subject = form.getAttribute("data-subject") || "AUVP Experience";
          var body = Object.keys(data).filter(function (k) {
            return k.charAt(0) !== "_" && data[k];
          }).map(function (k) {
            return labelFor(form, k) + ": " + data[k];
          }).join("\n");
          window.location.href = "mailto:" + CONFIG.contactEmail +
            "?subject=" + encodeURIComponent(subject) +
            "&body=" + encodeURIComponent(body);
          finish("Abrimos seu cliente de e-mail para concluir o envio.", true);
          return;
        }

        var headers = { "Content-Type": "application/json", Accept: "application/json" };
        if (CONFIG.nonce) headers["X-WP-Nonce"] = CONFIG.nonce;

        fetch(CONFIG.formEndpoint, {
          method: "POST",
          headers: headers,
          body: JSON.stringify(data),
        })
          .then(function (res) {
            return res.json().catch(function () { return {}; }).then(function (json) {
              if (!res.ok || json.ok === false) {
                throw new Error(json.message || "HTTP " + res.status);
              }
              finish("", true);
            });
          })
          .catch(function (err) {
            var msg = err && err.message && err.message.indexOf("HTTP") !== 0
              ? err.message
              : "Não conseguimos enviar agora. Tente novamente ou escreva para " + CONFIG.contactEmail + ".";
            finish(msg, false);
          });
      });

      // limpa erro ao digitar
      $$("input, select, textarea", form).forEach(function (input) {
        input.addEventListener("input", function () {
          var field = input.closest(".auvp-field");
          if (!field) return;
          field.classList.remove("auvp-has-error");
          var err = $(".auvp-field__error", field);
          if (err) err.textContent = "";
        });
      });
    });

    function labelFor(form, name) {
      var input = form.elements[name];
      if (!input) return name;
      var field = input.closest ? input.closest(".auvp-field") : null;
      var label = field ? $("label", field) : null;
      return label ? label.textContent.trim() : name;
    }

    function validate(form) {
      var ok = true;
      $$("[required]", form).forEach(function (input) {
        var field = input.closest(".auvp-field");
        var err = field ? $(".auvp-field__error", field) : null;
        var value = (input.value || "").trim();
        var msg = "";

        if (!value) msg = "Campo obrigatório.";
        else if (input.type === "email" && !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(value)) msg = "E-mail inválido.";
        else if (input.type === "tel" && value.replace(/\D/g, "").length < 10) msg = "Telefone incompleto.";

        if (msg) {
          ok = false;
          if (field) field.classList.add("auvp-has-error");
          if (err) err.textContent = msg;
        }
      });
      return ok;
    }
  }

  /* -----------------------------------------------------------
     15. Ano no rodapé
     ----------------------------------------------------------- */
  function footerYear() {
    $$("[data-year]").forEach(function (el) { el.textContent = new Date().getFullYear(); });
  }

  /* -----------------------------------------------------------
     Boot
     ----------------------------------------------------------- */
  var booted = false;

  /**
   * Pode ser chamado quantas vezes for preciso: tudo que registra listener
   * é protegido por once(), então re-executar só alcança o que é novo.
   * O editor do Elementor re-renderiza widgets a cada alteração.
   */
  function boot() {
    splitWords();
    navigation();
    accordion();
    careTabs();
    destinationVote();
    forms();
    footerYear();

    parallax();
    marquee();
    dragScroll();

    if (!booted) {
      booted = true;
      scrollTracker();
      progress();
      cursor();

      preloader().then(function () {
        reveals();
        // dá o start na primeira dobra mesmo sem scroll
        $$(".auvp-hero [data-split], .auvp-hero [data-reveal]").forEach(function (el) { el.classList.add("auvp-is-in"); });
      });
      return;
    }

    reveals();
  }

  window.AUVP = window.AUVP || {};
  window.AUVP.init = boot;

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", boot);
  } else {
    boot();
  }

  // Elementor: reinicializa quando um widget é (re)renderizado no editor.
  window.addEventListener("elementor/frontend/init", function () {
    if (!window.elementorFrontend || !window.elementorFrontend.hooks) return;
    window.elementorFrontend.hooks.addAction("frontend/element_ready/global", function () {
      boot();
    });
  });
})();
