/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)

import $ from 'jquery';


import 'jssocials/dist/jssocials.css';
import 'jssocials/dist/jssocials-theme-flat.css';
import 'font-awesome/css/font-awesome.css';
import 'jssocials/dist/jssocials.min.js';
$("#share_social_sh_page").jsSocials({
    text: `${$('.title_js_social').text()}`,
    url: `${$('.url_js_social').text()}`,
    showCount: false,
    showLabel: false,
    shares: [
        "email",
        "twitter",
        "facebook",
        "whatsapp",
    ],
    shareIn: "popup",
});

import '../css/app.css';
//import '@fortawesome/fontawesome-free/css/all.min.css';
//import '@fortawesome/fontawesome-free/js/all.min.js';



import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap/dist/js/bootstrap.min.js";
import 'select2/dist/css/select2.min.css';
import 'select2/dist/js/select2.min.js';
import "select2-theme-bootstrap4/dist/select2-bootstrap.min.css";


$('.js-select-single').select2({
    theme: "bootstrap",
    language: {
        "noResults": function(){
            return "Aucun résultat";
        }
    },
});


import "./img_upload";
import 'datatables.net-bs4/css/dataTables.bootstrap4.min.css';
import 'datatables.net-bs4/js/dataTables.bootstrap4.min';

import './front';
import './bung';
import './dataTablesFunc';
import '@popperjs/core';

import './algolia-places-gdo';

import './annonce-show';
import './carousel';

import Map from "./map";
Map.init();


