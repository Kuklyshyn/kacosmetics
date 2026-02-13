/**
 * Bestseller Products Slider
 */
(function() {
    'use strict';

    function initBestsellerSlider() {
        const slider = document.querySelector('.bestseller-slider');
        const prevBtn = document.querySelector('.bestseller-nav-prev');
        const nextBtn = document.querySelector('.bestseller-nav-next');

        if (!slider || !prevBtn || !nextBtn) return;

        const slides = slider.querySelectorAll('.bestseller-slide');
        if (slides.length === 0) return;

        let currentIndex = 0;
        let slidesPerView = getSlidesPerView();
        let maxIndex = Math.max(0, slides.length - slidesPerView);

        // Autoplay settings
        const autoplayInterval = 2000; // 2.5 seconds
        let autoplayTimer = null;
        let isHovered = false;

        // Touch/drag support
        let isDragging = false;
        let startX = 0;
        let currentTranslate = 0;
        let prevTranslate = 0;

        function getSlidesPerView() {
            if (window.innerWidth <= 480) return 2;
            if (window.innerWidth <= 768) return 2;
            return 3;
        }

        function getSlideWidth() {
            const slide = slides[0];
            const gap = window.innerWidth <= 480 ? 10 : window.innerWidth <= 768 ? 15 : 20;
            return slide.offsetWidth + gap;
        }

        function updateSlider(animate = true) {
            const slideWidth = getSlideWidth();
            const translateX = -currentIndex * slideWidth;

            slider.style.transition = animate ? 'transform 0.4s ease' : 'none';
            slider.style.transform = `translateX(${translateX}px)`;

            // Update buttons
            prevBtn.disabled = currentIndex === 0;
            nextBtn.disabled = currentIndex >= maxIndex;
        }

        function goToSlide(index) {
            currentIndex = Math.max(0, Math.min(index, maxIndex));
            updateSlider();
        }

        function nextSlide() {
            if (currentIndex < maxIndex) {
                goToSlide(currentIndex + 1);
            }
        }

        function prevSlide() {
            if (currentIndex > 0) {
                goToSlide(currentIndex - 1);
            }
        }

        // Button events
        nextBtn.addEventListener('click', function() {
            nextSlide();
            resetAutoplay();
        });
        prevBtn.addEventListener('click', function() {
            prevSlide();
            resetAutoplay();
        });

        // Autoplay functions
        function startAutoplay() {
            if (autoplayTimer) clearInterval(autoplayTimer);
            autoplayTimer = setInterval(function() {
                if (!isHovered && !isDragging) {
                    if (currentIndex >= maxIndex) {
                        currentIndex = 0;
                    } else {
                        currentIndex++;
                    }
                    updateSlider();
                }
            }, autoplayInterval);
        }

        function stopAutoplay() {
            if (autoplayTimer) {
                clearInterval(autoplayTimer);
                autoplayTimer = null;
            }
        }

        function resetAutoplay() {
            stopAutoplay();
            startAutoplay();
        }

        // Pause on hover
        slider.addEventListener('mouseenter', function() {
            isHovered = true;
        });

        slider.addEventListener('mouseleave', function() {
            isHovered = false;
        });

        // Touch events
        slider.addEventListener('touchstart', touchStart, { passive: true });
        slider.addEventListener('touchmove', touchMove, { passive: true });
        slider.addEventListener('touchend', touchEnd);

        // Mouse events for drag
        slider.addEventListener('mousedown', mouseStart);
        slider.addEventListener('mousemove', mouseMove);
        slider.addEventListener('mouseup', mouseEnd);
        slider.addEventListener('mouseleave', mouseEnd);

        function touchStart(e) {
            isDragging = true;
            startX = e.touches[0].clientX;
            prevTranslate = -currentIndex * getSlideWidth();
        }

        function touchMove(e) {
            if (!isDragging) return;
            const currentX = e.touches[0].clientX;
            const diff = currentX - startX;
            currentTranslate = prevTranslate + diff;
            slider.style.transition = 'none';
            slider.style.transform = `translateX(${currentTranslate}px)`;
        }

        function touchEnd() {
            if (!isDragging) return;
            isDragging = false;

            const movedBy = currentTranslate - prevTranslate;
            const threshold = getSlideWidth() / 4;

            if (movedBy < -threshold && currentIndex < maxIndex) {
                currentIndex++;
            } else if (movedBy > threshold && currentIndex > 0) {
                currentIndex--;
            }

            updateSlider();
        }

        function mouseStart(e) {
            isDragging = true;
            startX = e.clientX;
            prevTranslate = -currentIndex * getSlideWidth();
            slider.style.cursor = 'grabbing';
        }

        function mouseMove(e) {
            if (!isDragging) return;
            e.preventDefault();
            const currentX = e.clientX;
            const diff = currentX - startX;
            currentTranslate = prevTranslate + diff;
            slider.style.transition = 'none';
            slider.style.transform = `translateX(${currentTranslate}px)`;
        }

        function mouseEnd() {
            if (!isDragging) return;
            isDragging = false;
            slider.style.cursor = 'grab';

            const movedBy = currentTranslate - prevTranslate;
            const threshold = getSlideWidth() / 4;

            if (movedBy < -threshold && currentIndex < maxIndex) {
                currentIndex++;
            } else if (movedBy > threshold && currentIndex > 0) {
                currentIndex--;
            }

            updateSlider();
        }

        // Handle resize
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                slidesPerView = getSlidesPerView();
                maxIndex = Math.max(0, slides.length - slidesPerView);
                currentIndex = Math.min(currentIndex, maxIndex);
                updateSlider(false);
            }, 250);
        });

        // Initial setup
        updateSlider(false);

        // Start autoplay
        startAutoplay();
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initBestsellerSlider);
    } else {
        initBestsellerSlider();
    }

})();
