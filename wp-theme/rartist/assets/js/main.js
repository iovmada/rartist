/*
 * Shell behaviour: the navigation panel.
 *
 * The panel is a real element in the document, hidden with [hidden]. Everything the
 * markup already conveys (aria-expanded, aria-controls) is kept in sync here; the CSS
 * decides whether it reads as a full-width panel or a compact sheet.
 */

(function () {
	'use strict';

	var panel = document.querySelector('[data-nav-panel]');
	var toggles = document.querySelectorAll('[data-nav-toggle]');

	if (!panel || !toggles.length) {
		return;
	}

	var scrim = null;

	function isCompact() {
		return window.matchMedia('(max-width: 900px)').matches;
	}

	function setExpanded(expanded) {
		toggles.forEach(function (toggle) {
			toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
		});
	}

	function open() {
		panel.hidden = false;
		setExpanded(true);

		if (isCompact() && !scrim) {
			scrim = document.createElement('div');
			scrim.className = 'nav-scrim';
			scrim.addEventListener('click', close);
			document.body.appendChild(scrim);
			document.body.style.overflow = 'hidden';
		}

		var first = panel.querySelector('a, button');

		if (first) {
			first.focus({ preventScroll: true });
		}
	}

	function close() {
		panel.hidden = true;
		setExpanded(false);

		if (scrim) {
			scrim.remove();
			scrim = null;
			document.body.style.overflow = '';
		}
	}

	function toggle() {
		if (panel.hidden) {
			open();
		} else {
			close();
		}
	}

	toggles.forEach(function (button) {
		button.addEventListener('click', toggle);
	});

	panel.querySelectorAll('[data-nav-close]').forEach(function (button) {
		button.addEventListener('click', close);
	});

	document.addEventListener('keydown', function (event) {
		if ('Escape' === event.key && !panel.hidden) {
			close();
			toggles[0].focus({ preventScroll: true });
		}
	});

	// A click outside the panel closes it on desktop, where there is no scrim.
	document.addEventListener('click', function (event) {
		if (panel.hidden || isCompact()) {
			return;
		}

		if (panel.contains(event.target)) {
			return;
		}

		for (var i = 0; i < toggles.length; i++) {
			if (toggles[i].contains(event.target)) {
				return;
			}
		}

		close();
	});
})();
