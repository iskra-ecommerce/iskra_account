// Iskra Account — Main JavaScript
(function($) {
    'use strict';

    $(document).ready(function() {
        // Password visibility toggle
        $(document).on('click', '[data-oc-toggle="password-visibility"]', function() {
            var target = $($(this).data('oc-target'));
            var icon = $(this).find('i');
            if (target.attr('type') === 'password') {
                target.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                target.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // Email check via AJAX
        var emailTimer;
        $(document).on('input', '[data-oc-toggle="check-email"]', function() {
            var input = $(this);
            var url = input.data('oc-url');
            var status = input.closest('.iskra-input-wrap').find('.iskra-input-status');
            var error = input.closest('.iskra-field').find('.iskra-error');

            clearTimeout(emailTimer);
            status.removeClass('iskra-status--valid iskra-status--invalid').html('');

            var email = input.val();
            if (!email || email.length < 5) return;

            emailTimer = setTimeout(function() {
                $.ajax({
                    url: url + '&email=' + encodeURIComponent(email),
                    dataType: 'json',
                    success: function(json) {
                        if (json.exists) {
                            input.addClass('is-invalid');
                            status.addClass('iskra-status--invalid').html('<i class="fa-solid fa-circle-exclamation"></i>');
                            if (error.length) error.html('Email already registered').addClass('d-block');
                        } else {
                            input.removeClass('is-invalid');
                            status.addClass('iskra-status--valid').html('<i class="fa-solid fa-circle-check"></i>');
                            if (error.length) error.html('').removeClass('d-block');
                        }
                    }
                });
            }, 500);
        });

        // Phone mask
        $(document).on('input', '.iskra-input--phone', function() {
            var input = $(this);
            var value = input.val().replace(/\D/g, '');
            if (value.length === 0) { input.val(''); return; }

            var mask = '+7 ';
            if (value.length > 1) mask += '(' + value.substring(1, 4);
            if (value.length >= 5) mask += ') ' + value.substring(4, 7);
            if (value.length >= 8) mask += '-' + value.substring(7, 9);
            if (value.length >= 10) mask += '-' + value.substring(9, 11);
            input.val(mask);
        });

        // Language preference via AJAX
        $(document).on('change', '#input-language', function() {
            var code = $(this).val();
            $.ajax({
                url: 'index.php?route=extension/iskra/account.setLanguage&language=' + $('html').attr('lang'),
                type: 'post',
                data: { code: code },
                dataType: 'json'
            });
        });

        // Reset form highlights on success
        $(document).on('submit', '#form-register', function() {
            $(this).find('.is-invalid').removeClass('is-invalid');
            $(this).find('.iskra-error').removeClass('d-block').html('');
        });
    });
})(jQuery);
