jQuery(document).ready(function ($) {
    $('.homepage-reviews-slider').each(function () {
        var $slider = $(this);
        var $track = $slider.find('.reviews-track');
        var $slides = $slider.find('.review-slide');
        var $dots = $slider.find('.slider-dot');
        var slideCount = $slides.length;
        var currentIndex = 0;

        function updateSlider() {
            var translateX = -(currentIndex * 100);
            $track.css('transform', 'translateX(' + translateX + '%)');

            // Update dots
            $dots.removeClass('active');
            $dots.eq(currentIndex).addClass('active');
        }

        $slider.find('.slider-next').on('click', function () {
            if (currentIndex < slideCount - 1) {
                currentIndex++;
            } else {
                currentIndex = 0;
            }
            updateSlider();
        });

        $slider.find('.slider-prev').on('click', function () {
            if (currentIndex > 0) {
                currentIndex--;
            } else {
                currentIndex = slideCount - 1;
            }
            updateSlider();
        });

        // Click on dots
        $dots.on('click', function () {
            currentIndex = $(this).data('index');
            updateSlider();
        });

        // Initialize
        updateSlider();
    });
});
