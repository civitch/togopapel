import $ from "jquery";


$(document).ready(function () {

    var paths = $('.map__image__godo a');
    var links = $('.map__list_godo a'); // fonction de traitement globale


    var activeMapArea = function activeMapArea(id) {
        var info = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
        $('#map-godo .is-active, #map_list .is-active').each(function (indexm, elm) {
            $(elm).removeClass('is-active');
        });

        if (id !== undefined) {
            if (info !== null) {
                $('[data-id="' + info + '"]').addClass('is-active');
            } else {
                $('[data-target="list-' + id + '"]').addClass('is-active');
            }

            $('#region-' + id).addClass('is-active');
        }
    }; // function qui fait un traitement sur le SVG


    paths.each(function (index, el) {
        $(el).on('mouseenter', function () {
            var id = this.id.replace('region-', '');
            activeMapArea(id);
        });
    }); //function qui fait un traitement sur les listes

    links.each(function (index, el) {
        $(el).on('mouseenter', function () {
            var id = $(this).data('target').replace('list-', '');
            var info = $(this).data('id');
            activeMapArea(id, info);
        });
    });
    $('.mapp_v').on('mouseout', function () {
        activeMapArea();
    });

    $('[for="registration_particular_condition"]').html('« J’accepte les <a href="/cgu">Conditions Générales d‘Utilisation</a> »');
    $('[for="registration_pro_condition"]').html('« J’accepte les <a href="/cgu">Conditions Générales d‘Utilisation</a> »');
});



//suppression des images dans une annonce
document.querySelectorAll('[data-delete]').forEach(a => {
    a.addEventListener('click', e => {
        e.preventDefault();
        fetch(a.getAttribute('href'), {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({'_token': a.dataset.token, '_id': a.dataset.id})
        }).then(response => response.json())
            .then(data => {
                if (data.success) {
                    a.parentNode.parentNode.removeChild(a.parentNode)
                } else {
                    alert(data.error)
                }
            })
            .catch(e => alert(e))
    })
});

const $annonce_box_liste = $('.fav_blk_ann');
$annonce_box_liste.on('click', '[data-role="add-favoris"]', function (e) {
    e.preventDefault();
    const $id = $(this).data('id');
    const $big = $(this);
    $.ajax({
        url: Routing.generate('favoris_new'),
        dataType: 'json',
        method: 'POST',
        data: {id: $id}
    }).done((data) => {
        $big.parent('.fav_blk_ann')
            .empty()
            .html(`<a href="#" data-role="delete-favoris"  data-id="${data.annonce}" class="favoris-premium-like"><i class="fa fa-heart"></i></a>`);
    }).fail((jqXHR) => {
        switch (jqXHR.status) {
            case 404:
                console.log(jqXHR.responseJSON.error);
                break;
            default:
                console.log('erreur serveur');
        }
    })
});
$annonce_box_liste.on('click', '[data-role="delete-favoris"]', function (e) {
    e.preventDefault();
    const $id = $(this).data('id');
    const $big = $(this);
    $.ajax({
        url: Routing.generate('favoris_delete'),
        dataType: 'json',
        method: 'POST',
        data: {id: $id}
    }).done((data) => {
        $big.parent('.fav_blk_ann')
            .empty()
            .html(`<a href="#" data-role="add-favoris"  data-id="${data.annonce}" class="favoris-premium-like"><i class="fa fa-heart-o"></i></a>`);
    }).fail((jqXHR) => {
        switch (jqXHR.status) {
            case 404:
                console.log(jqXHR.responseJSON.error);
                break;
            default:
                console.log('erreur serveur');
        }
    })
});


const $annonce_premium = $('.fav_blk_ann_premium');
$annonce_premium.on('click', '[data-role="add-favoris"]', function (e) {
    e.preventDefault();
    const $id = $(this).data('id');
    const $big = $(this);
    $.ajax({
        url: Routing.generate('favoris_new'),
        dataType: 'json',
        method: 'POST',
        data: {id: $id}
    }).done((data) => {
        $big.parent('.fav_blk_ann_premium')
            .empty()
            .html(`<a href="#" data-role="delete-favoris"  data-id="${data.annonce}" class="favoris-premium-like"><i class="fa fa-heart"></i></a>`);
    }).fail((jqXHR) => {
        switch (jqXHR.status) {
            case 404:
                console.log(jqXHR.responseJSON.error);
                break;
            default:
                console.log('erreur serveur');
        }
    })
});
$annonce_premium.on('click', '[data-role="delete-favoris"]', function (e) {
    e.preventDefault();
    const $id = $(this).data('id');
    const $big = $(this);
    $.ajax({
        url: Routing.generate('favoris_delete'),
        dataType: 'json',
        method: 'POST',
        data: {id: $id}
    }).done((data) => {
        $big.parent('.fav_blk_ann_premium')
            .empty()
            .html(`<a href="#" data-role="add-favoris" data-id="${data.annonce}" class="favoris-premium-like"><i class="fa fa-heart-o"></i></a>`);
    }).fail((jqXHR) => {
        switch (jqXHR.status) {
            case 404:
                console.log(jqXHR.responseJSON.error);
                break;
            default:
                console.log('erreur serveur');
        }
    })
});



const $annonce_show_premium = $('.fav_shown_ann');
$annonce_show_premium.on('click', '[data-role="add-favoris"]', function (e) {
    e.preventDefault();
    const $id = $(this).data('id');
    const $big = $(this);
    $.ajax({
        url: Routing.generate('favoris_new'),
        dataType: 'json',
        method: 'POST',
        data: {id: $id}
    }).done((data) => {
        $big.parent('.fav_shown_ann')
            .empty()
            .html(`<a href="#" data-role="delete-favoris"  data-id="${data.annonce}" class="favoris-shown-like"><i class="fa fa-heart"></i></a>`);
    }).fail((jqXHR) => {
        switch (jqXHR.status) {
            case 404:
                console.log(jqXHR.responseJSON.error);
                break;
            default:
                console.log('erreur serveur');
        }
    })
});
$annonce_show_premium.on('click', '[data-role="delete-favoris"]', function (e) {
    e.preventDefault();
    const $id = $(this).data('id');
    const $big = $(this);
    $.ajax({
        url: Routing.generate('favoris_delete'),
        dataType: 'json',
        method: 'POST',
        data: {id: $id}
    }).done((data) => {
        $big.parent('.fav_shown_ann')
            .empty()
            .html(`<a href="#" data-role="add-favoris" data-id="${data.annonce}" class="favoris-shown-like"><i class="fa fa-heart-o"></i></a>`);
    }).fail((jqXHR) => {
        switch (jqXHR.status) {
            case 404:
                console.log(jqXHR.responseJSON.error);
                break;
            default:
                console.log('erreur serveur');
        }
    })
});


const $actions_favoris = $('.button_actions_fv');
$actions_favoris.on('click', '[data-role="delete-favoris"]', function (e) {
    e.preventDefault();
    const $id = $(this).data('id');
    const $big = $(this);
    $.ajax({
        url: Routing.generate('favoris_delete'),
        dataType: 'json',
        method: 'POST',
        data: {id: $id}
    }).done(() => {
        $big.closest('.block_annonce').remove();
    }).fail((jqXHR) => {
        switch (jqXHR.status) {
            case 404:
                console.log(jqXHR.responseJSON.error);
                break;
            default:
                console.log('erreur serveur');
        }
    })
});

function replaceCheckBoxToSwitch() {
    $('.form-check').toggleClass('custom-switch').removeClass('form-check');
    $('.form-check-input').toggleClass('custom-control-input').removeClass('form-check-input');
    $('.form-check-label').toggleClass('custom-control-label').removeClass('form-check-label');
}
function paiement_credit(){
    $('.block-forfait').on('click', '[data-role="credit-ask"]', function (e) {
        e.preventDefault();
        const $id = $(this).data('id');
        $('#demande_credit_card').val($id);
        $('#creditModal').modal('toggle');
    });
}

paiement_credit();

// changement du checkbox en switch
replaceCheckBoxToSwitch();

// .img_preview_fil
// .picture_gdo_upload svg



const faqAccordion = $('.collapse');

faqAccordion.collapse('show');

faqAccordion.on('shown.bs.collapse', function () {

});

faqAccordion.on('hidden.bs.collapse', function () {
    $('.accordion .card-header button').each(() => {
        if(!$(this).hasClass('collapsed')){
            $(this).children('.arrow_up').hide();
            $(this).children('.arrow_dwn').show();
        }
    });
});

const $faqCollapse = $('.accordion .card-header button');

$faqCollapse.on('click', function(){
    $(this).each(() => {
        if($(this).hasClass('collapsed')){
            $(this).children('.arrow_up').show();
            $(this).children('.arrow_dwn').hide();
        }else{
            $(this).children('.arrow_up').hide();
            $(this).children('.arrow_dwn').show();
        }
    });
});


$faqCollapse.each(() => {
    if($faqCollapse.hasClass('collapsed')){
        $faqCollapse.children('.arrow_up').show();
        $faqCollapse.children('.arrow_dwn').hide();
    }
});





// menu responsive
$('.comble_negocybat').css('display', 'none');

$('.icon_responsive').on('click', function(){
    $('.menu_responsive_nego').toggleClass('active_gdo_mb');
    $('.comble_negocybat').css('display', 'block');
});

$('.comble_negocybat').on('click', function(){
    $('.menu_responsive_nego').toggleClass('active_gdo_mb');
    $('.comble_negocybat').css('display', 'none');
});


// dropdown mobile notifications
$('.dcdwn_dec').on('click', () =>{
    const drp_notificaiton = $('.drpdwmob_notifications');
    drp_notificaiton.toggle(400, ()=> {
        drp_notificaiton.css('display', 'block !important').css('transform', '.3s all ease-in');
    });
});


//dropdown ville home
$('select#dropdwn_city_home').on('select2:select', function (e) {
    const $data = e.params.data;
    if($data.id !== ""){
        window.location.href = $data.id;
    }
});

// Traitement hide/display password
$(".i_passwd_auth").click(function() {
    $('.i_passwd_auth').toggleClass('view');
    if($('.i_passwd_auth').hasClass('view'))
    {
        $('.i_passwd_auth').empty().html('<i class="fa fa-eye-slash"></i>');
    }else{
        $('.i_passwd_auth').empty().html('<i class="fa fa-eye"></i>');
    }

    //fas fa-eye-slash
    const input = $('#inputPassword');
    if (input.attr("type") === "password") {
        input.attr("type", "text");
    } else {
        input.attr("type", "password");
    }
});

$(function () {
    $('[data-toggle="tooltip"]').tooltip()
})



// affichage erreur password
$("#registration_particular_password_second").after('<div class="error_message_password"></div>');

$('#registration_particular_password_second').keyup(function(){
    var pwd= $('#registration_particular_password_first').val();
    var re_pwd= $('#registration_particular_password_second').val();
    if(re_pwd !== pwd){
        $('.error_message_password').html('*** Les mots de passe ne correspondent pas! ***');
        $('.error_message_password').css('display', 'block');
        return false;
    }else{
        $('.error_message_password').html('');
        $('.error_message_password').css('display', 'none');
        return true;
    }
});

$('#registration_pro_password_second').after('<div class="error_message_password"></div>');

$('#registration_pro_password_second').keyup(function(){
    var pwd= $('#registration_pro_password_first').val();
    var re_pwd= $('#registration_pro_password_second').val();
    if(re_pwd !== pwd){
        $('.error_message_password').html('*** Les mots de passe ne correspondent pas! ***');
        $('.error_message_password').css('display', 'block');
        return false;
    }else{
        $('.error_message_password').html('');
        $('.error_message_password').css('display', 'none');
        return true;
    }
});

$('#annonce_hideTel').attr('checked', 'checked');