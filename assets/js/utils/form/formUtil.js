/*
    js form util, to display, inputs errors or clear errors on js validation 
*/
import 'jquery-serializejson'
//import { notify } from './utils'

export default function FormUtil(opts = {}) {
    let defaultOptions = {
        inputErrorClass: 'is-invalid',
        helperErrorClass: 'invalid-feedback',
        inputContainerSelector: 'div.form-group',
        helperElement: 'span'
    }
    let options = {...defaultOptions, ...opts }
    this.inputErrorClass = options.inputErrorClass
    this.helperErrorClass = options.helperErrorClass
    this.helperElement = options.helperElement
    this.inputContainerSelector = options.inputContainerSelector
}

FormUtil.prototype.showError = function(input, error, hasInputContainer = true) {
    if (typeof input === 'string') {
        input = $(input);
    }
    this.clearError(input)
    input.addClass(this.inputErrorClass);
    let helper = $(`<${this.helperElement}></ ${this.helperElement}>`)
        .addClass(this.helperErrorClass)
        .text(error);
    let inputContainer = input.closest(this.inputContainerSelector);
    if (helper.css('display') == 'none') {
        helper.css('display', 'block !important');
    }
    inputContainer.append(helper)
}

FormUtil.prototype.showErrors = function(inputSelector, errors, hasInputContainer = true, inputAttrSelector = 'name') {
    let selecteInput = {}
    if (typeof inputSelector === 'string') {
        selecteInput = $(inputSelector);
    } else if (typeof inputSelector === 'object') {
        selecteInput = inputSelector;
    }
    this.clearErrors(inputSelector)
    let that = this;
    errors.map(function(error) {
        let input = selecteInput.find(`[${inputAttrSelector}^="${error.property_path}"]`);
        that.showError(input, error.message, hasInputContainer)
    })
}

FormUtil.prototype.clearError = function(input, hasInputContainer = true) {
    if (typeof input === 'string') {
        input = $(input);
    }
    if (input.hasClass(this.inputErrorClass)) {
        input.removeClass(this.inputErrorClass)
    }
    let helperSlector = `${this.helperElement}.${this.helperErrorClass}`;
    let helper = null
    if (hasInputContainer) {
        let inputContainer = input.closest(this.inputContainerSelector);
        helper = inputContainer.find(helperSlector);
    } else {
        helper = input.next(helperSlector);
    }

    if (helper.length) {
        helper.remove();
    }
}

FormUtil.prototype.clearErrors = function(inputSelector) {
    let selecteInput = {}
    if (typeof inputSelector === 'string') {
        selecteInput = $(inputSelector);
    } else if (typeof inputSelector === 'object') {
        selecteInput = inputSelector;
    }

    let inputs = selecteInput.find(':input');
    let that = this;
    inputs.each(function(i) {
        that.clearError($(this))
    });
};

FormUtil.prototype.validate = function(inputsContainer, options, queryParams = {}) {
    // clear previuos errors
    this.clearErrors(inputsContainer)

    // get data
    let data = $(inputsContainer).find(':input').serializeJSON({
        parseWithFunction: function(parseVal, name) {
            let input = $(`[name="${name}"]`)
            if (input.data('type') == 'phoneNumber') {
                if (parseVal !== undefined && parseVal !== '' && parseVal !== null) {
                    let iti = intlTelInputGlobals.getInstance(input.get(0))
                    let phoneNumberE164 = iti.getNumber(window.intlTelInputUtils.numberFormat.E164);
                    return phoneNumberE164
                }
            }
            return parseVal ||  null
        }
    });

    // add queryString if
    queryParams = {... { validate: 1 }, ...queryParams }
    if (!$.isEmptyObject(queryParams)) {
        options.url += '?' + $.param(queryParams);
    }

    let result = null
    options.contentType = 'application/json'
    options.data = JSON.stringify(data)
    let that = this

    // make request
    $.ajax(options).done(function(resData) {
        result = true
    }).fail(function(response, error, errorText) {
        let responseData = {};
        let message = errorText;
        if (response.responseJSON !== undefined) {
            responseData = response.responseJSON;
            if (responseData.errors !== undefined) {
                let errors = responseData.errors;
                that.showErrors(inputsContainer, errors);
            } else if (responseData.message !== undefined) {
                message = responseData.message;
            }
            result = false;
        } else {
            throw new Error(message)
        }

    });
    return result
}

FormUtil.prototype.submit = function(form, options, queryParams = {}) {
    let data = form.serializeJSON({
        parseWithFunction: function(parseVal, name) {
            let input = $(`[name="${name}"]`)
            if (input.data('type') == 'phoneNumber') {
                if (parseVal !== undefined && parseVal !== '' && parseVal !== null) {
                    let iti = intlTelInputGlobals.getInstance(input.get(0))
                    let phoneNumberE164 = iti.getNumber(window.intlTelInputUtils.numberFormat.E164);
                    return phoneNumberE164
                }
            }
            return parseVal ||  null
        }
    });
    let result = false;

    let queryString = $.param(queryParams);
    options.data = JSON.stringify(data)
    options.contentType = 'application/json';
    $.ajax(options).done(function(resData) {
        return resData;
    }).fail(function(res, error, errorText) {
        let message = errorText;
        result = false;
        if (res.responseJSON !== undefined) {
            responseData = res.responseJSON;
            if (responseData.message !== undefined) {
                message = responseData.message;
            }
        }
        throw new Error(message)
    })
    return result
}