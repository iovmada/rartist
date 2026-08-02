/*
 * Artwork page: mirror the selected size's price into the reserve bar.
 *
 * The selector itself is native radio inputs, so choosing a size works with this file
 * absent — only the price in the bar would stop following along.
 */

(function () {
	'use strict';

	var selector = document.querySelector('[data-size-selector]');
	var price = document.querySelector('[data-reserve-price]');

	if (!selector || !price) {
		return;
	}

	selector.addEventListener('change', function (event) {
		var input = event.target;

		if (!input.matches('input[type="radio"]')) {
			return;
		}

		var next = input.getAttribute('data-price');

		if (next) {
			price.textContent = next;
		}
	});
})();
