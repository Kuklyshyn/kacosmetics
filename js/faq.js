/**
 * FAQ Accordion functionality
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        // FAQ accordion toggle
        $('.faq-question').on('click', function(e) {
            e.preventDefault();

            var $item = $(this).closest('.faq-item');
            var $answer = $item.find('.faq-answer');
            var isOpen = $item.hasClass('active');

            // Toggle current item
            if (isOpen) {
                $item.removeClass('active');
                $(this).attr('aria-expanded', 'false');
                $answer.slideUp(300);
            } else {
                $item.addClass('active');
                $(this).attr('aria-expanded', 'true');
                $answer.slideDown(300);
            }
        });
    });

})(jQuery);
