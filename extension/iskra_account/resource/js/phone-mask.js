// Iskra Account — Phone Mask
(function() {
	'use strict';

	document.querySelectorAll('.iskra-input--phone').forEach(function(input) {
		input.addEventListener('input', function(e) {
			var x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,2})(\d{0,2})/);
			if (x) {
				e.target.value = !x[2] ? x[1] : (x[1] + ' (' + x[2] + ') ' + x[3] + (x[4] ? '-' + x[4] : ''));
			}
		});
	});
})();