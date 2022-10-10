
function alterDataTableContent(){
    $('.table').addClass('align-middle');
    $('.table').addClass('table-row-dashed gy-5');
    $('.table').removeClass('table-bordered');
    $('tbody').addClass('fw-bold text-gray-600');
    $('thead tr').addClass('text-start text-muted fw-bolder fs-7 text-uppercase gs-0');
    $('thead th').addClass('min-w-125px');
    $('thead .text-end').removeClass('min-w-125px');
    $('thead .text-end').addClass('min-w-70px');
    $('.table').removeClass('table-striped');
}