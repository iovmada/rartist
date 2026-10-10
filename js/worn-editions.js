/* Worn Editions: native scrolling works before enhancement and without JS. */
(function () {
  "use strict";
  document.querySelectorAll("[data-worn-gallery]").forEach(function (gallery) {
    var viewport = gallery.querySelector(".worn-gallery__viewport");
    var slides = Array.from(viewport.querySelectorAll(".worn-gallery__slide"));
    var footer = gallery.querySelector(".worn-gallery__footer");
    var title = gallery.querySelector("[data-worn-title]");
    var details = gallery.querySelector("[data-worn-details]");
    var counter = gallery.querySelector("[data-worn-counter]");
    var progress = gallery.querySelector(".worn-gallery__progress");
    var buttons = Array.from(progress.querySelectorAll("button"));
    if (!slides.length) return;

    var active = 0;
    var requested = 0;
    var navigating = false;
    var frame = 0;
    var motion = window.matchMedia("(prefers-reduced-motion: reduce)");
    function number(value) { return String(value).padStart(2, "0"); }
    function update(index) {
      active = index;
      title.textContent = number(index + 1) + " \u2014 " + slides[index].dataset.title;
      details.textContent = slides[index].dataset.details;
      counter.textContent = number(index + 1) + " / " + number(slides.length);
      slides.forEach(function (slide, i) { slide.setAttribute("aria-hidden", i === index ? "false" : "true"); });
      buttons.forEach(function (button, i) {
        if (i === index) button.setAttribute("aria-current", "true");
        else button.removeAttribute("aria-current");
      });
    }
    function position(index) {
      return slides[index].getBoundingClientRect().left - viewport.getBoundingClientRect().left + viewport.scrollLeft;
    }
    function go(index, instant) {
      requested = (index + slides.length) % slides.length;
      navigating = true;
      viewport.scrollTo({ left: position(requested), behavior: instant || motion.matches ? "instant" : "smooth" });
    }
    function nearest() {
      var index = 0;
      var distance = Infinity;
      slides.forEach(function (slide, i) {
        var next = Math.abs(position(i) - viewport.scrollLeft);
        if (next < distance) { distance = next; index = i; }
      });
      update(index);
      if (!navigating) requested = index;
    }
    gallery.querySelector("[data-worn-prev]").addEventListener("click", function () { go(requested - 1); });
    gallery.querySelector("[data-worn-next]").addEventListener("click", function () { go(requested + 1); });
    buttons.forEach(function (button, i) { button.addEventListener("click", function () { go(i); }); });
    viewport.addEventListener("keydown", function (event) {
      var index;
      if (event.key === "ArrowRight") index = active + 1;
      else if (event.key === "ArrowLeft") index = active - 1;
      else if (event.key === "Home") index = 0;
      else if (event.key === "End") index = slides.length - 1;
      else return;
      event.preventDefault();
      go(index);
    });
    viewport.addEventListener("scroll", function () {
      if (frame) return;
      frame = window.requestAnimationFrame(function () { nearest(); frame = 0; });
    }, { passive: true });
    viewport.addEventListener("scrollend", function () { navigating = false; requested = active; });
    // Manual touch, wheel and pointer navigation supersede a button destination.
    ["pointerdown", "touchstart", "wheel"].forEach(function (type) {
      viewport.addEventListener(type, function () { navigating = false; requested = active; }, { passive: true });
    });
    window.addEventListener("resize", function () { go(active, true); });
    update(0);
    gallery.classList.add("is-enhanced");
    footer.hidden = false;
    progress.hidden = slides.length < 2;
    gallery.querySelector(".worn-gallery__controls").hidden = slides.length < 2;
  });
})();
