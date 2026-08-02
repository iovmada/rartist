/*
 * Shell behaviour: the navigation dropdowns.
 *
 * There is more than one panel in the bar (destinations, collections) and each toggle
 * names its own through aria-controls, so nothing here hard-codes an id. Opening one
 * closes the others. The panels are real elements hidden with [hidden]; the CSS decides
 * whether they read as a full-width sheet or a compact drawer.
 */

(function () {
	'use strict';

	var toggles = Array.prototype.slice.call(document.querySelectorAll('[data-nav-toggle]'));

	if (!toggles.length) {
		return;
	}

	var scrim = null;

	function panelFor(toggle) {
		var id = toggle.getAttribute('aria-controls');

		return id ? document.getElementById(id) : null;
	}

	function panels() {
		return Array.prototype.slice.call(document.querySelectorAll('[data-nav-panel]'));
	}

	function isCompact() {
		return window.matchMedia('(max-width: 900px)').matches;
	}

	function removeScrim() {
		if (scrim) {
			scrim.remove();
			scrim = null;
			document.body.style.overflow = '';
		}
	}

	function closeAll() {
		panels().forEach(function (panel) {
			panel.hidden = true;
		});

		toggles.forEach(function (toggle) {
			toggle.setAttribute('aria-expanded', 'false');
		});

		removeScrim();
	}

	function open(toggle) {
		var panel = panelFor(toggle);

		if (!panel) {
			return;
		}

		closeAll();
		panel.hidden = false;

		// Every toggle pointing at this panel reflects the state, burger included.
		toggles.forEach(function (other) {
			if (panelFor(other) === panel) {
				other.setAttribute('aria-expanded', 'true');
			}
		});

		if (isCompact()) {
			scrim = document.createElement('div');
			scrim.className = 'nav-scrim';
			scrim.addEventListener('click', closeAll);
			document.body.appendChild(scrim);
			document.body.style.overflow = 'hidden';
		}

		var first = panel.querySelector('a, button');

		if (first) {
			first.focus({ preventScroll: true });
		}
	}

	toggles.forEach(function (toggle) {
		toggle.addEventListener('click', function () {
			var panel = panelFor(toggle);

			if (panel && !panel.hidden) {
				closeAll();
				return;
			}

			open(toggle);
		});
	});

	panels().forEach(function (panel) {
		panel.querySelectorAll('[data-nav-close]').forEach(function (button) {
			button.addEventListener('click', closeAll);
		});
	});

	document.addEventListener('keydown', function (event) {
		if ('Escape' !== event.key) {
			return;
		}

		var openPanel = panels().filter(function (panel) {
			return !panel.hidden;
		})[0];

		if (!openPanel) {
			return;
		}

		var owner = toggles.filter(function (toggle) {
			return panelFor(toggle) === openPanel;
		})[0];

		closeAll();

		if (owner) {
			owner.focus({ preventScroll: true });
		}
	});

	// A click outside closes it on desktop, where there is no scrim.
	document.addEventListener('click', function (event) {
		if (isCompact()) {
			return;
		}

		var openPanels = panels().filter(function (panel) {
			return !panel.hidden;
		});

		if (!openPanels.length) {
			return;
		}

		var inside = openPanels.some(function (panel) {
			return panel.contains(event.target);
		});

		var onToggle = toggles.some(function (toggle) {
			return toggle.contains(event.target);
		});

		if (!inside && !onToggle) {
			closeAll();
		}
	});
})();
