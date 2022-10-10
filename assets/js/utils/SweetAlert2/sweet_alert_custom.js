import Swal from "sweetalert2";
import { preLoaderCollect, defaultLoad, removePreLoaderCollect } from './../defaultMessage/voucherMessage.js';
/**
 * Variable Default print Swal
 * @author Niff Rodolphe
 */
export let default_phrasing_global = {
    swal_title: "comfirmation",
    swal_text: "Etes-vous sûr de débloquer la société ?",
    swal_icon: "question",
    swal_confirmButtonText: 'Oui, Effectuer',
    swal_showCancelButton: true,
    swal_cancelButtonText: 'Annuler',
    swal_showLoaderOnConfirm: true,
    swal_confirmButtonColor: '#dc3545',
    swal_reverseButtons: true,

    swal_fail_title: "Echec",
    swal_fail_message: "Echec de requete",
    swal_fail_icon: "error",

    swal_success_title: "Etat de comfirmation",
    swal_success_message: "Effectuer avec success",
    swal_success_icon: "success",

    request_url: null,
    request_type: "GET",
    request_data: {},

    url_redirect_success : false,

}

/**
 * Function pour vérifié si l'information reçu dans 'value' est vide ou indéfinie
 * Dans le cas ou la valeur est null ou vide, on n'affiche la valeur dans le champs
 * 
 * @param {*} input 
 * @param {varchar} value 
 * @returns 
 */
export function init_input_number_phone_indacator(input, value) {
    let valeur_input = input.val();
    if (valeur_input == '' && valeur_input !== undefined) {
        return input.val(value);
    }
    return 0;
}

/**
 * 
 * @param {*} input 
 * @param {string} value
 * @returns 
 */
export function default_value_for_input(input, value) {
    let return_value = init_input_number_phone_indacator(input, value);
    if (return_value !== undefined && return_value == false) {
        return input.val(value);
    }
    return 0;
}

/**
 * comfirmPopPop
 *
 * Display a confirmation pop to the user
 *
 * @param dt
 * @param item
 * @param default_phrasing
 * @author Niff Rodolphe
 */
export function comfirmPopPop(
    dt, 
    item, 
    default_phrasing = {}, 
    other_action = defaultLoad,
    isPreLoaderActive = false
) {
    let default_phrasing_interne = {...default_phrasing_global, ...default_phrasing }
    
    if(isPreLoaderActive)
    {
        other_action();
    }
    
    Swal.fire({
            title: default_phrasing_interne.swal_title,
            text: default_phrasing_interne.swal_text,
            icon: default_phrasing_interne.swal_icon,
            confirmButtonText: default_phrasing_interne.swal_confirmButtonText,
            showCancelButton: default_phrasing_interne.swal_showCancelButton,
            cancelButtonText: default_phrasing_interne.swal_cancelButtonText,
            showLoaderOnConfirm: default_phrasing_interne.swal_showLoaderOnConfirm,
            confirmButtonColor: default_phrasing_interne.swal_confirmButtonColor,
            reverseButtons: default_phrasing_interne.swal_reverseButtons,
            preConfirm: () => {
                other_action();
                return $.ajax({
                    url: default_phrasing_interne.request_url,
                    type: default_phrasing_interne.request_type,
                    data: default_phrasing_interne.request_data,
                }).done((data) => {
                    return data;
                }).fail((response, errorThrown, errorMessage) => {
                    response.responseJSON = undefined;
                    let responseData = response?.responseJSON ?? response?.responseText;
                    let message = responseData?.message !== undefined ? responseData.message : JSON.parse(responseData) ?? errorMessage;
                    Swal.fire({
                        title: default_phrasing_interne.swal_fail_title,
                        text: message,
                        icon: default_phrasing_interne.swal_fail_icon,
                    });
                });
            }
        })
        .then((result) => {

            // if(isPreLoaderActive)
            // {
            //     removePreLoaderCollect();
            // }

            if (result.value) {
                dt?.draw();
                Swal.fire({
                    title: default_phrasing_interne.swal_success_title,
                    text: result.value?.message ?? result.value,
                    icon: default_phrasing_interne.swal_success_icon
                });

                if(default_phrasing_interne.url_redirect_success !== false)
                {
                    setTimeout(function(){
                        window.location.replace(default_phrasing_interne.url_redirect_success);
                    }, 2000)
                }

            }
        });
}
