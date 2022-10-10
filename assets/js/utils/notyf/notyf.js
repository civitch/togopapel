import { Notyf } from 'notyf';
import 'notyf/notyf.min.css';

// Create an instance of Notyf
const notyf = new Notyf({
    duration: 5000,
    position: {
        x: 'right',
        y: 'top',
    },
    types: [{
            type: 'info',
            background: '#00bfff',
            icon: false
        },
        {
            type: 'warning',
            background: '#ffd700',
            icon: false
        },
    ]
});


let messages = document.querySelectorAll('notyf');

messages.forEach(message => {
    if (message.className === 'success') {
        notyf.success(message.innerHTML);
    }

    if (message.className === 'error') {
        notyf.error(message.innerHTML);
    }

    if (message.className === 'danger') {
        notyf.error(message.innerHTML);
    }

    if (message.className === 'info') {
        notyf.open({
            type: 'info',
            message: '<b>Info</b> - ' + message.innerHTML,
        });
    }

    if (message.className === 'warning') {
        notyf.open({
            type: 'warning',
            message: '<b>Warning</b> - ' + message.innerHTML
        });
    }
});



export function printMSG(type, sms, time)
{
    if(time === undefined)
        time = 4000;
    toastr.options = {
        closeButton: true,
        progressBar: true,
        showMethod: 'slideDown',
        timeOut: time,
        positionClass: "toast-top-center" 
    };
    switch(type){
        case 'info':
            toastr.info(sms);
            break;
        case 'success':
            toastr.success(sms);
            break;
        case 'error':
            toastr.error(sms);
            break;
    }
}