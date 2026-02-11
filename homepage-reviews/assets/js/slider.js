jQuery(document).ready(function ($) {
    $('.homepage-reviews-slider').each(function () {
        var $slider = $(this);
        var $track = $slider.find('.reviews-track');
        var $slides = $slider.find('.review-slide');
        var $dots = $slider.find('.slider-dot');
        var slideCount = $slides.length;
        var currentIndex = 0;
        var isAnimating = false;

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

            if (currentIndex < slideCount - 1) {
                currentIndex++;
            } else {
                currentIndex = 0;
            }
            updateSlider();
        });

        $slider.find('.slider-prev').off('click').on('click', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            if (isAnimating) return;

            if (currentIndex > 0) {
                currentIndex--;
            } else {
                currentIndex = slideCount - 1;
            }
            updateSlider();
        });

        // Click on dots
        $dots.off('click').on('click', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            if (isAnimating) return;

            var index = $(this).attr('data-index');
            currentIndex = parseInt(index, 10);
            updateSlider();
        });

        // Initialize
        updateSlider();
    });
});
