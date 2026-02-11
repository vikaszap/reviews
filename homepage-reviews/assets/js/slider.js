jQuery(document).ready(function ($) {
    $('.homepage-reviews-slider').each(function () {
        var $slider = $(this);
        var $track = $slider.find('.reviews-track');
        var $slides = $slider.find('.review-slide');
        var $dots = $slider.find('.slider-dot');
        var slideCount = $slides.length;
        var currentIndex = 0;
        var isAnimating = false;
        var autoplayTimer = null;

        // Read autoplay config passed from PHP
        var config = (typeof homepageReviewsConfig !== 'undefined') ? homepageReviewsConfig : {};
        var autoplayEnabled = config.autoplay || false;
        var autoplaySpeed = parseInt(config.autoplay_speed, 10) || 5000;

        function updateSlider() {
            isAnimating = true;
            var translateX = -(currentIndex * 100);
            $track.css('transform', 'translateX(' + translateX + '%)');

            // Update dots
            $dots.removeClass('active');
            $dots.eq(currentIndex).addClass('active');

            // Reset animating flag after transition (matching 0.5s CSS)
            setTimeout(function() {
                isAnimating = false;
            }, 500);
        }

        function nextSlide() {
            if (isAnimating) return;
            currentIndex = (currentIndex < slideCount - 1) ? currentIndex + 1 : 0;
            updateSlider();
        }

        function prevSlide() {
            if (isAnimating) return;
            currentIndex = (currentIndex > 0) ? currentIndex - 1 : slideCount - 1;
            updateSlider();
        }

        // ---------- Autoplay ----------
        function startAutoplay() {
            if (!autoplayEnabled || slideCount <= 1) return;
            stopAutoplay();
            autoplayTimer = setInterval(nextSlide, autoplaySpeed);
        }

        function stopAutoplay() {
            if (autoplayTimer) {
                clearInterval(autoplayTimer);
                autoplayTimer = null;
            }
        }

        function resetAutoplay() {
            if (!autoplayEnabled) return;
            stopAutoplay();
            startAutoplay();
        }

        // Pause on hover / touch, resume on leave
        $slider.on('mouseenter touchstart', function () {
            stopAutoplay();
        });
        $slider.on('mouseleave touchend', function () {
            startAutoplay();
        });

        // ---------- Controls ----------
        $slider.find('.slider-next').off('click').on('click', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            nextSlide();
            resetAutoplay();
        });

        $slider.find('.slider-prev').off('click').on('click', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            prevSlide();
            resetAutoplay();
        });

        // Click on dots
        $dots.off('click').on('click', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            if (isAnimating) return;

            var index = $(this).attr('data-index');
            currentIndex = parseInt(index, 10);
            updateSlider();
            resetAutoplay();
        });

        // Initialize
        updateSlider();
        startAutoplay();
    });
});
