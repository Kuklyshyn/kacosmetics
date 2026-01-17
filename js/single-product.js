/**
 * Single Product Page functionality
 */
(function() {
    'use strict';

    function initProductGallery() {
        const mainImage = document.querySelector('.product-main-image img');
        const thumbnails = document.querySelectorAll('.thumbnail-item');

        if (!mainImage || !thumbnails.length) {
            return;
        }

        // Set first thumbnail as active
        if (thumbnails[0]) {
            thumbnails[0].classList.add('active');
        }

        // Handle thumbnail clicks
        thumbnails.forEach(function(thumbnail, index) {
            thumbnail.addEventListener('click', function() {
                // Remove active class from all thumbnails
                thumbnails.forEach(function(thumb) {
                    thumb.classList.remove('active');
                });

                // Add active class to clicked thumbnail
                this.classList.add('active');

                // Get image from thumbnail
                const thumbnailImg = this.querySelector('img');
                if (thumbnailImg) {
                    // Get full size image URL
                    const fullImageSrc = thumbnailImg.src.replace('-300x300', '').replace('-150x150', '');

                    // Update main image with fade effect
                    mainImage.style.opacity = '0';

                    setTimeout(function() {
                        mainImage.src = fullImageSrc;
                        mainImage.srcset = fullImageSrc;
                        mainImage.style.opacity = '1';
                    }, 300);
                }
            });

            // Add keyboard navigation
            thumbnail.setAttribute('tabindex', '0');
            thumbnail.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.click();
                }
            });
        });

        // Add smooth transition to main image
        mainImage.style.transition = 'opacity 0.3s ease';
    }

    function initQuantityButtons() {
        const quantityInputs = document.querySelectorAll('.quantity input[type="number"]');

        quantityInputs.forEach(function(input) {
            // Create plus and minus buttons
            const wrapper = input.parentElement;

            if (!wrapper.querySelector('.qty-btn')) {
                const minusBtn = document.createElement('button');
                minusBtn.type = 'button';
                minusBtn.className = 'qty-btn qty-minus';
                minusBtn.innerHTML = '−';
                minusBtn.setAttribute('aria-label', 'Decrease quantity');

                const plusBtn = document.createElement('button');
                plusBtn.type = 'button';
                plusBtn.className = 'qty-btn qty-plus';
                plusBtn.innerHTML = '+';
                plusBtn.setAttribute('aria-label', 'Increase quantity');

                wrapper.insertBefore(minusBtn, input);
                wrapper.appendChild(plusBtn);

                // Handle minus button
                minusBtn.addEventListener('click', function() {
                    const currentValue = parseInt(input.value) || 1;
                    const minValue = parseInt(input.getAttribute('min')) || 1;

                    if (currentValue > minValue) {
                        input.value = currentValue - 1;
                        input.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                });

                // Handle plus button
                plusBtn.addEventListener('click', function() {
                    const currentValue = parseInt(input.value) || 1;
                    const maxValue = parseInt(input.getAttribute('max')) || 999;

                    if (currentValue < maxValue) {
                        input.value = currentValue + 1;
                        input.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                });
            }
        });
    }

    function initSmoothScroll() {
        const tabLinks = document.querySelectorAll('.woocommerce-tabs .tabs li a');

        tabLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                // Let WooCommerce handle tab switching
                // Just add smooth scroll to tabs
                setTimeout(function() {
                    const tabsContainer = document.querySelector('.woocommerce-tabs');
                    if (tabsContainer) {
                        const headerHeight = document.querySelector('.site-header')?.offsetHeight || 0;
                        const offset = tabsContainer.offsetTop - headerHeight - 20;

                        window.scrollTo({
                            top: offset,
                            behavior: 'smooth'
                        });
                    }
                }, 100);
            });
        });
    }

    // Initialize all functions
    function init() {
        initProductGallery();
        initQuantityButtons();
        initSmoothScroll();
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Re-initialize on AJAX complete (for variable products)
    document.body.addEventListener('updated_cart_totals', init);
    document.body.addEventListener('updated_checkout', init);

})();
