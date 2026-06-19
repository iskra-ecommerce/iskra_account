// Iskra Account — main JS
(function() {
	'use strict';

	// Phone mask
	document.querySelectorAll('[data-oc-mask="phone"]').forEach(function(input) {
		input.addEventListener('input', function(e) {
			var x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,2})(\d{0,2})/);
			e.target.value = !x[2] ? x[1] : x[1] + ' (' + x[2] + ') ' + x[3] + (x[4] ? '-' + x[4] : '');
		});
	});

	// Country selector
	document.getElementById('input-country')?.addEventListener('change', function() {
		var selected = this.options[this.selectedIndex];
		var phone = selected.getAttribute('data-oc-phone');
		var currency = selected.getAttribute('data-oc-currency');
		var lang = selected.getAttribute('data-oc-language');
		var phoneInput = document.getElementById('input-telephone');

		if (phone && phoneInput) {
			var prefix = phone + ' (___) ___-__-__';
			phoneInput.placeholder = prefix;
		}
		if (currency) {
			var url = document.getElementById('input-country').getAttribute('data-oc-currency-url');
			if (url) {
				fetch(url, {
					method: 'POST',
					headers: {'Content-Type': 'application/x-www-form-urlencoded'},
					body: 'code=' + encodeURIComponent(currency)
				});
			}
		}
	});
})();