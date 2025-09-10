import './bootstrap';
import Alpine from 'alpinejs';

import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay, Keyboard } from 'swiper/modules';

import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

document.addEventListener("DOMContentLoaded", () => {
    const swiper = new Swiper('.heroSwiper', {
        modules: [Navigation, Pagination, Autoplay, Keyboard],
        loop: true,
        autoplay: { 
            delay: 5000, 
            disableOnInteraction: false 
        },
        pagination: { 
            el: '.swiper-pagination', 
            clickable: true 
        },
        navigation: { 
            nextEl: '.swiper-button-next', 
            prevEl: '.swiper-button-prev' 
        },
        keyboard: { enabled: true },
        touchEventsTarget: 'container',
        simulateTouch: true,
        grabCursor: true,
        effect: 'slide',
        speed: 600,
    });
});



window.Alpine = Alpine;

Alpine.start();
