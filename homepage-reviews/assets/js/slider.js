jQuery(document).ready(function ($) {
    $('.homepage-reviews-slider').each(function () {
        var $slider = $(this);
        var $track = $slider.find('.reviews-track');
        var $slides = $slider.find('.review-slide');
        var $dots = $slider.find('.slider-dot');
        var slideCount = $slides.length;
        var currentIndex = 0;
        var isAnimating = false;

        // Autoplay settings
        var autoplay = typeof homepage_reviews_slider_options !== 'undefined' && homepage_reviews_slider_options.autoplay === '1';
        var autoplaySpeed = typeof homepage_reviews_slider_options !== 'undefined' ? parseInt(homepage_reviews_slider_options.autoplay_speed, 10) : 5000;
        var autoplayTimer;

        function startAutoplay() {
            if (autoplay && slideCount > 1) {
                stopAutoplay();
                autoplayTimer = setInterval(function () {
                    if (currentIndex < slideCount - 1) {
                        currentIndex++;
                    } else {
                        currentIndex = 0;
                    }
                    updateSlider();
                }, autoplaySpeed);
            }
        }

        function stopAutoplay() {
            if (autoplayTimer) {
                clearInterval(autoplayTimer);
            }
        }

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

        $slider.find('.slider-next').off('click').on('click', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            if (isAnimating) return;

            stopAutoplay();
            if (currentIndex < slideCount - 1) {
                currentIndex++;
            } else {
                currentIndex = 0;
            }
            updateSlider();
            startAutoplay();
        });

        $slider.find('.slider-prev').off('click').on('click', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            if (isAnimating) return;

            stopAutoplay();
            if (currentIndex > 0) {
                currentIndex--;
            } else {
                currentIndex = slideCount - 1;
            }
            updateSlider();
            startAutoplay();
        });

        // Click on dots
        $dots.off('click').on('click', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            if (isAnimating) return;

            stopAutoplay();
            var index = $(this).attr('data-index');
            currentIndex = parseInt(index, 10);
            updateSlider();
            startAutoplay();
        });

        // Pause on hover
        $slider.on('mouseenter', function() {
            stopAutoplay();
        }).on('mouseleave', function() {
            startAutoplay();
        });

        // Initialize
        updateSlider();
        startAutoplay();
    });
});
