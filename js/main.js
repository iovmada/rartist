/* Rartist Studio — prelaunch page behaviour. Vanilla, no dependencies. */
(function () {
  "use strict";

  /* ── Scroll reveal ──────────────────────────────────────── */
  var revealables = document.querySelectorAll("[data-reveal]");

  if (!("IntersectionObserver" in window)) {
    revealables.forEach(function (el) {
      el.classList.add("is-visible");
    });
  } else {
    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          entry.target.classList.add("is-visible");
          observer.unobserve(entry.target);
        });
      },
      { rootMargin: "0px 0px -12% 0px", threshold: 0.05 }
    );

    revealables.forEach(function (el) {
      observer.observe(el);
    });
  }

  /* ── Invite form ────────────────────────────────────────── */
  var form = document.getElementById("signup-form");
  var field = form && form.querySelector(".signup__field");
  var input = document.getElementById("email");
  var submit = form && form.querySelector(".signup__submit");
  var message = document.getElementById("signup-message");
  var count = document.getElementById("collector-count");

  if (!form || !input || !message) return;

  var defaultMessage = message.innerHTML;
  var EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

  function setMessage(text, confirmed) {
    message.textContent = text;
    message.classList.toggle("is-confirmed", Boolean(confirmed));
  }

  input.addEventListener("input", function () {
    if (!field) return;
    if (field.classList.contains("has-error")) {
      field.classList.remove("has-error");
      message.innerHTML = defaultMessage;
      message.classList.remove("is-confirmed");
    }
  });

  form.addEventListener("submit", function (event) {
    event.preventDefault();

    var value = input.value.trim();

    if (!EMAIL.test(value)) {
      if (field) field.classList.add("has-error");
      setMessage("That email does not look complete. Try again.", false);
      input.focus();
      return;
    }

    if (field) field.classList.remove("has-error");

    /* No backend yet — the list is collected once an endpoint exists. */
    if (count) {
      var current = parseInt(count.textContent.replace(/\D/g, ""), 10);
      if (!isNaN(current)) count.textContent = (current + 1).toLocaleString("en-US");
    }

    input.value = "";
    input.disabled = true;
    if (submit) {
      submit.disabled = true;
      submit.textContent = "ON THE LIST";
    }

    setMessage(
      "You are on the list. We will write to you once before the first collection opens, and not otherwise.",
      true
    );
  });
})();
