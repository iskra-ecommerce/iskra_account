// Iskra Account — Phone Mask (standalone)
(function($) {
    'use strict';

    $(document).on('focus', '.iskra-input--phone', function() {
        var input = $(this);
        if (!input.val()) {
            input.val('+7 ');
        }
    });

    $(document).on('blur', '.iskra-input--phone', function() {
        var input = $(this);
        if (input.val() === '+7 ' || input.val() === '+7') {
            input.val('');
        }
    });
})(jQuery);
