document.addEventListener('DOMContentLoaded', function() {
    const swiper = new Swiper('.cts-swiper', {
        loop: true,
        slidesPerView: 1,
        spaceBetween: 30,
        navigation: {
            nextEl: '.cts-next',
            prevEl: '.cts-prev',
        },
        autoHeight: true,
        breakpoints: {
            768: {
                slidesPerView: 1,
                spaceBetween: 30,
            }
        }
    });
});
