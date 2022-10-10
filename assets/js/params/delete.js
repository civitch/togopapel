import { comfirmPopPop } from '../utils/SweetAlert2/sweet_alert_custom'

$(function() {

    $('#paramDataTable').on('click', '.deleteAction', function(e) {
        e.preventDefault();
        deleteItem(paramDataTable, $(this));
    });

    let deleteItem = (dt, item) => {

        comfirmPopPop(dt, item, {
            swal_title: "Suppresion du paramettre",
            swal_text: "Etes vous sur de la suppression ?",
            swal_icon: "error",
            request_url: item.attr('href'),
            request_type: "DELETE",
        })
    }

});