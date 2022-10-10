import 'intl-tel-input/build/css/intlTelInput.css';
import intlTelInput from 'intl-tel-input';
import FormUtil from '../form/formUtil';

$(function() {

    let formUtil = new FormUtil()
    let ipinfo = fetch("https://ipinfo.io/json?token=f27691c2ae09be")
        .then(response => response.json());

    // init phone number
    let submitButtom = $('.SubmitAction');
    let preferredCountries = ["cg", "fr", "cn"];
    let phoneNumbers = $('[data-type="phoneNumber"]');
    let phoneNumberOptions = {
        separateDialCode: true,
        utilsScritpt: 'intl-tel-input/build/js/utils.js',
        initialCountry: "auto",
        preferredCountries: preferredCountries,
        geoIpLookup: function(success, failure) {
            ipinfo.then((resData) => {
                success(resData.country);
            })
        }
    }

    if (phoneNumbers.length) {
        phoneNumbers.each((function() {
            intlTelInput($(this).get(0), phoneNumberOptions)
        }))
        import ('intl-tel-input/build/js/utils')
    }

    // validate phone number on blur
    phoneNumbers.on('keyup blur', function(e) {
        formUtil.clearError($(this))
        let iti = intlTelInputGlobals.getInstance($(this).get(0))
        if (e.type === 'blur') {
            formUtil.showError($(this), 'Numéro de téléphone invalide')
        }
    })


    // Submit Button in vue
    submitButtom.on("click", function(e) {
        if (e.type !== 'blur') {
            let iti2 = intlTelInputGlobals.getInstance(phoneNumbers.get(0))
                // change information for data
            let phoneNumberE164 = iti2.getNumber(window.intlTelInputUtils.numberFormat.E164);
            phoneNumbers.prop('value', phoneNumberE164);

            //$('form').submit()
        }

    });

})

