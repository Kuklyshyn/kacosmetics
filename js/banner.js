/**
 * Top Banner functionality
 */
(function() {
    'use strict';

    function initBanner() {
        const banner = document.querySelector('.top-banner');
        const closeButton = document.querySelector('.banner-close');

        if (!banner || !closeButton) {
            return;
        }

        // Check if banner was previously closed
        if (localStorage.getItem('bannerClosed') === 'true') {
            banner.style.display = 'none';
            return;
        }

        // Handle close button click
        closeButton.addEventListener('click', function() {
            banner.style.display = 'none';
            localStorage.setItem('bannerClosed', 'true');

            // Adjust sticky header position if needed
            const header = document.querySelector('.dior-style-header');
            if (header) {
                header.style.top = '0';
            }
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initBanner);
    } else {
        initBanner();
    }

})();
