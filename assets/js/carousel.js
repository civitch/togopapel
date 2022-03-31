import 'owl.carousel/dist/assets/owl.carousel.css';
import 'owl.carousel';

import $ from 'jquery';

$(document).ready(function(){
    $(".front_carousel.owl-carousel").owlCarousel({
        loop:true,
        margin: 25,
        responsiveClass:true,
        dots: false,
        responsive:{
            0:{
                items:1,

            },
            768:{
                items:1,
            },
            992:{
                items:2,
            },
            1200:{
                items:2,
            }
        },
        autoplay: true,
        nav: false
    });
});
