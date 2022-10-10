import 'select2'
import 'jquery-validation/dist/jquery.validate'
import 'jquery-validation/dist/localization/messages_fr';
import 'ladda/dist/ladda-themeless.min.css'
import * as Ladda from 'ladda'
import 'cleave.js/src/Cleave'
import 'bootstrap-datepicker/dist/js/bootstrap-datepicker'
import 'bootstrap-datepicker/dist/css/bootstrap-datepicker.css'

let $ = require('jquery')
let button = $('.ladda-button').get(0);
let ladda = Ladda.create(button)
$('.ladda-spinner').hide().remove();

// $('select').select2();

// $('select').hide()


let buttom = $("form").on('click', (e) => {
    $("form").validate({
        lang: 'fr',
        invalidHandler: function(event, validator) {
            // 'this' refers to the form
            var errors = validator.numberOfInvalids();

            if (errors > 0 || errors !== null) {
                $(":submit").removeClass("data-loading");
            } else {

                ladda.start()
                $('.ladda-progess').hide();

            }
        },
        submitHandler: function(event) {
            ladda.start();
            event.submit();
        }
    })

});

// Input Multiple Design
$(document).ready(function() {
    //     $("span.select2-container--default").attr('class', 'select2 select2-container select2-container--bootstrap5 ');
    //     $("span.select2-selection--single").attr('class', 'select2-selection select2-selection--single form-select form-select-solid');
    //     $("span.select2-selection--multiple").attr('class', 'select2-selection select2-selection--multiple form-select form-control form-control-solid');
    // info -> add proprities to slect action in form
    // $("span.select2-selection--single").attr('class', 'select2-selection select2-selection--single form-select form-select-solid');

    $("span.select2-selection--single").on('click', function(){
        $("span.select2-selection--single").attr('class', 'select2-selection select2-selection--single form-select form-select-solid');
        $(this).trigger("select2:open")
        $(this).trigger("select2:opening")
        console.log('dep')

    });

    $('.js-datepicker').datepicker({
        format: 'dd-mm-yyyy',
        orientation: 'bottom',
        icons: {
            time: 'fa fa-clock-o',
            date: 'fa fa-calendar',
            up: 'fa fa-chevron-up',
            down: 'fa fa-chevron-down',
            previous: 'fa fa-chevron-left',
            next: 'fa fa-chevron-right',
            today: 'fa fa-crosshairs',
            clear: 'fa fa-trash'
        }
    });
});


// Format sur le champs Input Number 
if (($('input[custom_number="format_number"]').length) > 0) {

    new Cleave('input[custom_number="format_number"]', {
        numeral: true,
        numeralDecimalMark: ',',
        delimiter: ' ',
        numeralDecimalScale: 4,
        numeralThousandsGroupStyle: 'thousand'
    });
}

//Validation Date
var dateRegex = /^(?=\d)(?:(?:31(?!.(?:0?[2469]|11))|(?:30|29)(?!.0?2)|29(?=.0?2.(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00)))(?:\x20|$))|(?:2[0-8]|1\d|0?[1-9]))([-.\/])(?:1[012]|0?[1-9])\1(?:1[6-9]|[2-9]\d)?\d\d(?:(?=\x20\d)\x20|$))?(((0?[1-9]|1[012])(:[0-5]\d){0,2}(\x20[AP]M))|([01]\d|2[0-3])(:[0-5]\d){1,2})?$/;
//console.log(dateRegex.test('21/12/2020'));

// Add JQUERY VALIDATOR
jQuery.validator.addMethod("validDate", function(value, element) {
    return this.optional(element) || moment(value, "DD/MM/YYYY").isValid();
}, "Please enter a valid date in the format DD/MM/YYYY");