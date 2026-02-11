/**
 * Hero Banner Slider
 */
(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        initHeroBanner();
    });

    function initHeroBanner() {
        var banner = document.querySelector('.hero-banner');
        if (!banner) return;

        var slides = banner.querySelectorAll('.hero-slide');
        var dots = banner.querySelectorAll('.hero-dot');
        var prevBtn = banner.querySelector('.hero-nav-prev');
        var nextBtn = banner.querySelector('.hero-nav-next');

        // If single slide, no slider needed
        if (slides.length <= 1) {
            return;
        }

        var currentSlide = 0;
        var slideCount = slides.length;
        var autoplayInterval = null;
        var autoplay = banner.dataset.autoplay === 'true';
        var interval = parseInt(banner.dataset.interval, 10) || 5000;

        // Show specific slide
        function showSlide(index) {
            // Handle wrap-around
            if (index >= slideCount) {
                index = 0;
            } else if (index < 0) {
                index = slideCount - 1;
            }

            // Update slides
            slides.forEach(function(slide, i) {
                slide.classList.toggle('active', i === index);
            });

            // Update dots
            dots.forEach(function(dot, i) {
                dot.classList.toggle('active', i === index);
            });

            currentSlide = index;
        }

        // Next slide
        function nextSlide() {
            showSlide(currentSlide + 1);
        }

        // Previous slide
        function prevSlide() {
            showSlide(currentSlide - 1);
        }

        // Start autoplay
        function startAutoplay() {
            if (autoplay && !autoplayInterval) {
                autoplayInterval = setInterval(nextSlide, interval);
            }
        }

        // Stop autoplay
        function stopAutoplay() {
            if (autoplayInterval) {
                clearInterval(autoplayInterval);
                autoplayInterval = null;
            }
        }

        // Event listeners
        if (nextBtn) {
            nextBtn.addEventListener('click', function() {
                stopAutoplay();
                nextSlide();
                startAutoplay();
            });
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', function() {
                stopAutoplay();
                prevSlide();
                startAutoplay();
            });
        }

        // Dot navigation
        dots.forEach(function(dot, index) {
            dot.addEventListener('click', function() {
                stopAutoplay();
                showSlide(index);
                startAutoplay();
            });
        });

        // Pause on hover
        banner.addEventListener('mouseenter', stopAutoplay);
        banner.addEventListener('mouseleave', startAutoplay);

        // Touch/swipe support
        var touchStartX = 0;
        var touchEndX = 0;

        banner.addEventListener('touchstart', function(e) {
            touchStartX = e.changedTouches[0].screenX;
            stopAutoplay();
        }, { passive: true });

        banner.addEventListener('touchend', function(e) {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
            startAutoplay();
        }, { passive: true });

        function handleSwipe() {
            var diff = touchStartX - touchEndX;
            var threshold = 50;

            if (diff > threshold) {
                // Swipe left - next slide
                nextSlide();
            } else if (diff < -threshold) {
                // Swipe right - previous slide
                prevSlide();
            }
        }

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            // Only if banner is in viewport
            var rect = banner.getBoundingClientRect();
            var inViewport = rect.top < window.innerHeight && rect.bottom > 0;

            if (!inViewport) return;

            if (e.key === 'ArrowLeft') {
                stopAutoplay();
                prevSlide();
                startAutoplay();
            } else if (e.key === 'ArrowRight') {
                stopAutoplay();
                nextSlide();
                startAutoplay();
            }
        });

        // Initialize first slide and start autoplay
        showSlide(0);
        startAutoplay();
    }
})();
