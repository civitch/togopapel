import $ from "jquery";
import 'lightslider/dist/css/lightslider.min.css';
import 'lightslider/dist/js/lightslider.min';

function scrollSinglePage(){
    $(window).scroll(function(){
        if(($(document).scrollTop() > 100) && ($(document).width() > 992)) {
            $('.flotting-header').fadeIn('600', 'swing');
        }else{
            $('.flotting-header').hide();
        }
    });

}

scrollSinglePage();


$(".imgs_lightslider").lightSlider({
    gallery: true,
    item: 1,
    loop: true,
    slideMargin: 0,
    thumbItem: 9
});
// Afficher le téléphone de l'utilisateur lors du clic
/**/
$('.btn_jjso').one("click", function(){
    const element = $(this);
    const number = $('#btn_jjso_number').text();
    let _html='<a style="color:white" href="tel:'+number+'">'+number+'</a>'
    element.html(_html);
});

/**/

$('.floatting-send-message:last-child').one("click", function(){
    const element = $(this);
    const number = $('#btn_jjso_number').text();
    let _html='<a style="color:#ef233c" href="tel:'+number+'">'+number+'</a>'
    element.html(_html);
});

