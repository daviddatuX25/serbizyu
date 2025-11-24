import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay } from 'swiper/modules';

import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

document.addEventListener('DOMContentLoaded', function () {
    const swiperContainer = document.querySelector('.serviceSwiper');
    if (swiperContainer) {
        const loop = swiperContainer.parentElement.dataset.loop === 'true';
        new Swiper(swiperContainer, {
            modules: [Navigation, Pagination, Autoplay],
            loop: loop,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
        });
    }
});
