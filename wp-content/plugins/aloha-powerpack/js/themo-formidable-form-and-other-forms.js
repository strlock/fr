jQuery(document).ready(function ($) {
    $('input.mphb-datepick').on('focus', function (e) {
        const parent_id = $(this).closest('.elementor-element').data('id');
        //check for the opened popup, if it found, add the unique id class
        if ($('.mphb-datepick-popup').length) {
            $('.mphb-datepick-popup').addClass(themo_mphb_booking_form.calendar_prefix + parent_id);
        }
    });
});


