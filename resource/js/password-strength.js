// Iskra Account — Password Strength Meter
(function($) {
    'use strict';

    $(document).on('input', '#input-password', function() {
        var password = $(this).val();
        var meter = $('#password-strength');
        if (!meter.length) return;

        var fill = meter.find('.iskra-password-strength__fill');
        var label = meter.find('.iskra-password-strength__label');

        var score = 0;

        // Length
        if (password.length >= 8) score += 20;
        if (password.length >= 12) score += 10;
        if (password.length >= 16) score += 5;

        // Lowercase
        if (/[a-zа-яё]/.test(password)) score += 15;

        // Uppercase
        if (/[A-ZА-ЯЁ]/.test(password)) score += 15;

        // Numbers
        if (/[0-9]/.test(password)) score += 15;

        // Symbols
        if (/[^a-zA-Zа-яА-ЯёЁ0-9]/.test(password)) score += 20;

        score = Math.min(100, score);

        fill.css('width', score + '%');

        var level, color;
        if (score === 0) {
            level = '';
            color = '';
        } else if (score < 25) {
            level = 'Weak';
            color = '#dc3545';
        } else if (score < 50) {
            level = 'Fair';
            color = '#ffc107';
        } else if (score < 75) {
            level = 'Good';
            color = '#0d6efd';
        } else {
            level = 'Strong';
            color = '#198754';
        }

        fill.css('background-color', color);
        meter.removeClass('iskra-password-strength--weak iskra-password-strength--fair iskra-password-strength--good iskra-password-strength--strong');

        if (score > 0) {
            meter.addClass('iskra-password-strength--' + level.toLowerCase());
            label.text(level);
        } else {
            fill.css('background-color', '');
            label.text('Enter password');
        }
    });

    // Password confirm match
    $(document).on('input', '#input-password-confirm', function() {
        var confirm = $(this).val();
        var password = $('#input-password').val();
        var error = $('#error-password-confirm');

        if (confirm.length === 0) {
            $(this).removeClass('is-valid is-invalid');
            error.html('').removeClass('d-block');
            return;
        }

        if (confirm === password) {
            $(this).removeClass('is-invalid').addClass('is-valid');
            error.html('').removeClass('d-block');
        } else {
            $(this).removeClass('is-valid').addClass('is-invalid');
            error.html('Passwords do not match').addClass('d-block');
        }
    });
})(jQuery);
