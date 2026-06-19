// Iskra Account — Password Strength Indicator
(function() {
	'use strict';

	document.querySelectorAll('[data-oc-toggle="password-strength"]').forEach(function(input) {
		input.addEventListener('input', function() {
			var value = input.value;
			var strength = 0;
			var label = document.querySelector('#password-strength .iskra-password-strength__label');
			var fill = document.querySelector('#password-strength .iskra-password-strength__fill');
			var bar = document.querySelector('#password-strength');

			if (value.length >= 8) strength += 25;
			if (value.match(/[a-z]+/)) strength += 25;
			if (value.match(/[A-Z]+/)) strength += 25;
			if (value.match(/[0-9]+/)) strength += 25;

			if (!fill || !bar) return;

			fill.style.width = strength + '%';
			bar.className = 'iskra-password-strength';
			if (strength <= 25) {
				bar.classList.add('iskra-password-strength--weak');
				if (label) label.textContent = 'Weak';
			} else if (strength <= 50) {
				bar.classList.add('iskra-password-strength--fair');
				if (label) label.textContent = 'Fair';
			} else if (strength <= 75) {
				bar.classList.add('iskra-password-strength--good');
				if (label) label.textContent = 'Good';
			} else {
				bar.classList.add('iskra-password-strength--strong');
				if (label) label.textContent = 'Strong';
			}
		});
	});
})();