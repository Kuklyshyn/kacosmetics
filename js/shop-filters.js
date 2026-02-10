/**
 * Shop Filters Toggle functionality
 */
(function() {
    'use strict';

    /**
     * Get URL parameter value
     */
    function getUrlParam(name) {
        var urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(name);
    }

    /**
     * Format price with currency symbol
     */
    function formatPrice(price) {
        var settings = window.kacShopFilters || {
            currencySymbol: '€',
            currencyPosition: 'right_space',
            priceDecimals: 2,
            decimalSeparator: ',',
            thousandSeparator: ' '
        };

        var formattedPrice = price.toFixed(settings.priceDecimals);

        // Replace decimal separator
        if (settings.decimalSeparator !== '.') {
            formattedPrice = formattedPrice.replace('.', settings.decimalSeparator);
        }

        // Add currency symbol based on position
        switch (settings.currencyPosition) {
            case 'left':
                return settings.currencySymbol + formattedPrice;
            case 'left_space':
                return settings.currencySymbol + ' ' + formattedPrice;
            case 'right':
                return formattedPrice + settings.currencySymbol;
            case 'right_space':
            default:
                return formattedPrice + ' ' + settings.currencySymbol;
        }
    }

    /**
     * Customize WooCommerce Price Filter Slider
     * Makes the slider step smaller for more precise filtering
     * And syncs current filter values from URL
     */
    function initPriceSlider() {
        // Wait for jQuery and jQuery UI slider to be available
        if (typeof jQuery === 'undefined' || typeof jQuery.ui === 'undefined') {
            return;
        }

        var $ = jQuery;
        var $slider = $('.price_slider');
        var $minInput = $('.price_slider_amount input[name="min_price"]');
        var $maxInput = $('.price_slider_amount input[name="max_price"]');
        var $minLabel = $('.price_slider_amount .from');
        var $maxLabel = $('.price_slider_amount .to');

        if ($slider.length === 0) {
            return;
        }

        // Get current filter values from URL
        var urlMinPrice = getUrlParam('min_price');
        var urlMaxPrice = getUrlParam('max_price');

        // Wait for WooCommerce to initialize the slider
        var checkSlider = setInterval(function() {
            if ($slider.hasClass('ui-slider')) {
                clearInterval(checkSlider);

                // Get slider min/max bounds
                var sliderMin = $slider.slider('option', 'min');
                var sliderMax = $slider.slider('option', 'max');
                var range = sliderMax - sliderMin;

                // Calculate step based on price range
                var step = 1;
                if (range > 1000) {
                    step = 5;
                } else if (range > 500) {
                    step = 2;
                } else if (range > 100) {
                    step = 1;
                } else {
                    step = 0.5;
                }

                // Update slider step
                $slider.slider('option', 'step', step);

                // If URL has filter values, update the slider to show them
                if (urlMinPrice !== null || urlMaxPrice !== null) {
                    var currentMin = urlMinPrice !== null ? parseFloat(urlMinPrice) : sliderMin;
                    var currentMax = urlMaxPrice !== null ? parseFloat(urlMaxPrice) : sliderMax;

                    // Ensure values are within bounds
                    currentMin = Math.max(currentMin, sliderMin);
                    currentMax = Math.min(currentMax, sliderMax);

                    // Update slider values
                    $slider.slider('values', 0, currentMin);
                    $slider.slider('values', 1, currentMax);

                    // Update hidden inputs
                    if ($minInput.length) {
                        $minInput.val(currentMin);
                    }
                    if ($maxInput.length) {
                        $maxInput.val(currentMax);
                    }

                    // Update displayed price labels
                    if ($minLabel.length) {
                        $minLabel.html(formatPrice(currentMin));
                    }
                    if ($maxLabel.length) {
                        $maxLabel.html(formatPrice(currentMax));
                    }
                }
            }
        }, 100);

        // Clear interval after 5 seconds to prevent infinite loop
        setTimeout(function() {
            clearInterval(checkSlider);
        }, 5000);
    }

    function initShopFilters() {
        const filtersToggle = document.querySelector('.filters-toggle');
        const filtersContent = document.querySelector('.filters-content');

        // Initialize price slider customization
        initPriceSlider();

        if (!filtersToggle || !filtersContent) {
            return;
        }

        // Toggle filters on mobile
        filtersToggle.addEventListener('click', function() {
            filtersContent.classList.toggle('active');

            // Update button text
            if (filtersContent.classList.contains('active')) {
                this.innerHTML = this.innerHTML.replace('Filters', 'Hide Filters');
            } else {
                this.innerHTML = this.innerHTML.replace('Hide Filters', 'Filters');
            }
        });

        // Close filters when clicking outside on mobile
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 768) {
                const isClickInside = event.target.closest('.shop-sidebar');

                if (!isClickInside && filtersContent.classList.contains('active')) {
                    filtersContent.classList.remove('active');
                    filtersToggle.innerHTML = filtersToggle.innerHTML.replace('Hide Filters', 'Filters');
                }
            }
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initShopFilters);
    } else {
        initShopFilters();
    }

})();
