var $language =  {
    processing:     "Traitement en cours...",
        search:         "Rechercher&nbsp;:",
        lengthMenu:    "Afficher _MENU_ &eacute;l&eacute;ments",
        infoEmpty:      "Affichage de l'&eacute;lement 0 &agrave; 0 sur 0 &eacute;l&eacute;ments",
        infoFiltered:   "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
        infoPostFix:    "",
        loadingRecords: "Chargement en cours...",
        zeroRecords:    "Aucun &eacute;l&eacute;ment &agrave; afficher",
        emptyTable:     "Aucune donnée disponible dans le tableau",
    aria: {
        sortAscending:  ": activer pour trier la colonne par ordre croissant",
            sortDescending: ": activer pour trier la colonne par ordre décroissant"
    }
};

$('#rubrique_liste').DataTable({
    info: false,
    language: $language
});

$('#options_main_list').DataTable({
    info: false,
    language: $language
});

$('#categorie_liste').DataTable({
    info: false,
    language: $language
});

$('#ville_liste').DataTable({
    info: false,
    language: $language
});

$('#annonces_liste').DataTable({
    info: false,
    language: $language
});

$('#categories_liste_admin').DataTable({
    info: false,
    language: $language
});

$('#owner-pack-liste').DataTable({
    info: false,
    language: $language
});

$('#users_wallet').DataTable({
    info: false,
    language: $language
});

$('#users_admins').DataTable({
    info: false,
    language: $language
});

$('#users_users').DataTable({
    info: false,
    language: $language
});

/*
// Edition des catégories
function manage_categorie() {

    $('table#categorie_liste').on('click', 'a[data-role="edit"]', function () {
        let id = $(this).data('id');
        let titre = $('#' + id).children('td[data-target="title"]').text();
        $('#edit-title-categorie').val(titre);
        $('#categorie-id-edit').val(id);
        $('#edit-title-categorie').next('.message-error').text('');
        $('#editCategorie').modal('toggle');
    });
    $('#categorie-edit-form').submit(function (e) {
        e.preventDefault();
        let $form = $(this);
        let $info = $form.serializeArray();
        let $data = {};
        $data['title'] = $info[0].value;
        $data['rubrique'] = $info[1].value;
        $data['token'] = $info[2].value;
        $data['id'] = $info[3].value;
        if($data['title'] === ''){
            $('#edit-title-categorie').next('.message-error').text('Saisissez un titre');
            return false;
        }else{
            $('#edit-title-categorie').next('.message-error').text('');
        }
        if($data['rubrique'] === ''){
            $('#edit-list-rubrique').next('.message-error').text('Saisissez une rubrique');
            return false;
        }else{
            $('#edit-list-rubrique').next('.message-error').text('');
        }
        $.ajax({
            url : Routing.generate('categorie_edit'),
            method: 'PUT',
            data : $data,
            datatype: 'json',
            beforeSend: function(){
                $('#save-edit-categorie').attr('disabled', 'disabled');
            }
        }).done(function(data){
            $(':input', $form).not(':button, :submit, :reset, :hidden').val('');
            $('#edit-title-categorie').next('.message-error').text('');

            $('#' + data.id).children('td[data-target="title"]').text(data.title);
            $('#' + data.id).children('td[data-target="rubrique"]').text(data.rubrique);
            $('#editCategorie').modal('toggle');
        }).fail(function(jqXHR, textStatus, errorThrown){
            if(jqXHR.responseJSON.error !== null && errorThrown === "Internal Server Error"){
                $('#edit-title-categorie').next('.message-error').text(jqXHR.responseJSON.error);
            }
        }).always(function () {
            $('#save-edit-categorie').removeAttr('disabled');
        });
    });

}

//Edition d'une rubrique
function manage_rubrique() {
    $('table#rubrique_liste').on('click', 'a[data-role="edit"]', function () {
        let id = $(this).data('id');
        let titre = $('#' + id).children('td[data-target="title"]').text();
        $('#edit-title-rubrique').val(titre);
        $('#rubrique-id-edit').val(id);
        $('#edit-title-rubrique').next('.message-error').text('');
        $('#editRubrique').modal('toggle');
    });
    $('#rubrique-edit-form').submit(function (e) {
        e.preventDefault();
        let $form = $(this);
        let $id = $('#rubrique-id-edit').val();
        let $titre =  $('#edit-title-rubrique').val();
        let $token =  $('#token-edit-rubrique').val();

        if($titre === ''){
            $('#edit-title-rubrique').next('.message-error').text('Saisir un titre');
            return false;
        }else{
            $('#edit-title-rubrique').next('.message-error').text('');
        }

        $.ajax({
            url : Routing.generate('rubrique_edit'),
            method: 'PUT',
            data : {id: $id, title: $titre, token: $token},
            datatype: 'json',
            beforeSend: function(){
                $('#save-edit-rubrique').attr('disabled', 'disabled');
            }
        }).done(function(data){
            $(':input', $form).not(':button, :submit, :reset, :hidden').val('');
            $('#edit-title-rubrique').next('.message-error').text('');
            $('#' + $id).children('td[data-target="title"]').text($titre);
            $('#editRubrique').modal('toggle');
        }).fail(function(jqXHR, textStatus, errorThrown){
            if(jqXHR.responseJSON.error !== null && errorThrown === "Internal Server Error"){
                $('#edit-title-rubrique').next('.message-error').text(jqXHR.responseJSON.error);
            }
        }).always(function () {
            $('#save-edit-rubrique').removeAttr('disabled');
        });
    });
}

manage_categorie();
manage_rubrique();

*/

var $liste_annonce_enabled = $('#annonces_enabled');
$liste_annonce_enabled.DataTable({
    info: false,
    language: $language,
});

var $liste_annonce_disabled = $('#annonces_notEnaled');
$liste_annonce_disabled.DataTable({
    info: false,
    language: $language,
});

var $liste_annonce_waiting = $('#annonces_waiting');
$liste_annonce_waiting.DataTable({
    info: false,
    language: $language,
});

var activateAnnonce = function ($id, $tok_csrf, $route) {
    $.ajax({
        url: Routing.generate($route),
        method: 'POST',
        data: {id: $id, token: $tok_csrf},
        dataType: 'json',

    }).done(function(){
        $('tr#' + $id).remove();
    }).fail(function(jqXHR) {
        switch (jqXHR.status) {
            case 400:
                console.log(jqXHR.responseJSON.error);
                break;
            case 404:
                console.log(jqXHR.responseJSON.error);
                break;
            default:
                console.log('erreur serveur');
        }
    })
};

$liste_annonce_enabled.on('click','[data-role="disabled"]', function (e) {
    e.preventDefault();
    let id = $(this).data('id');
    let token = $(this).data('token');
    activateAnnonce(id, token, 'annonce_corporate_disabled');
});


$liste_annonce_disabled.on('click','[data-role="enabled"]', function (e) {
    e.preventDefault();
    let id = $(this).data('id');
    let token = $(this).data('token');
    activateAnnonce(id, token, 'annonce_corporate_enabled');
});

$liste_annonce_waiting.on('click','[data-role="disabled"]', function (e) {
    e.preventDefault();
    let id = $(this).data('id');
    let token = $(this).data('token');
    activateAnnonce(id, token, 'annonce_corporate_disabled');
});


$liste_annonce_waiting.on('click','[data-role="enabled"]', function (e) {
    e.preventDefault();
    let id = $(this).data('id');
    let token = $(this).data('token');
    activateAnnonce(id, token, 'annonce_corporate_enabled');
});


$('#annonce_pictureFiles + label').html('Téléverser vos images');

