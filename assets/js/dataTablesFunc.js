import $ from "jquery";

const $language =  {
    processing:     "Traitement en cours...",
    search:         "Rechercher&nbsp;:",
    lengthMenu:    "Afficher _MENU_ &eacute;l&eacute;ments",
    infoEmpty:      "Affichage de l'&eacute;lement 0 &agrave; 0 sur 0 &eacute;l&eacute;ments",
    infoFiltered:   "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
    infoPostFix:    "",
    loadingRecords: "Chargement en cours...",
    zeroRecords:    "Aucun &eacute;l&eacute;ment &agrave; afficher",
    emptyTable:     "Aucune donnée disponible dans le tableau",
    paginate: {
        first:      "Premier",
        previous:   "Pr&eacute;c&eacute;dent",
        next:       "Suivant",
        last:       "Dernier"
    },
    aria: {
        sortAscending:  ": activer pour trier la colonne par ordre croissant",
        sortDescending: ": activer pour trier la colonne par ordre décroissant"
    }
};

const list_demandeCredit = $('#demande_credit_list');
list_demandeCredit.dataTable({
    info: false,
    language: $language
});

const list_annonces = $('#list_annonces');
list_annonces.dataTable({
    info: false,
    language: $language
});


// Evènement suppression annonce
list_annonces.on('click', '[data-role="delete"]', function (e) {
   const $delete = $(this);
   let id = $(this).data('id');
   let token = $(this).data('token');
   $.ajax({
       url: Routing.generate('annonce_delete'),
       method: 'DELETE',
       data: {id: id, token: token},
       dataType: 'json',
       beforeSend: (jqXHR, settings) =>{
            $delete.attr('disabled', 'disabled');
       }
   }).done((data, text, jqXr) =>{
       $(`tr#${id}`).remove();
   }).fail((jqXHR, textStatus, errorThrow) =>{
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
   }).always(() =>{
       $delete.removeAttr('disabled');
   });
});
