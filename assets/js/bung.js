import $ from "jquery";



let $input_files = $('#annonce_pictureFiles');
$input_files.on('change', function(e){
    const file = $(this)[0].files;
    $('.img_preview').children().remove();
    if(file.length > 0)
    {
        if(file.length === 1)
        {
            $('#annonce_pictureFiles + label').html(`${file.length} fichier uploadé`);
        }
        else{
            $('#annonce_pictureFiles + label').html(`  ${file.length} fichiers uploadés`);
        }
        $.each(file, function (index, value) {
            const reader = new FileReader();
            reader.onload = function(e){
                $('.img_preview').append('<img class="img_upload_annonce" src="'+ e.target.result+'"/>');

            };
            reader.readAsDataURL(value);
        });
    }
    else{
        $('label[for="annonce_pictureFiles"]').text('');
        $('.img_preview').children().remove();
    }
});



// Si "offre" est coché afficher,
// afficher le champ price
// et mettez le à required
if($('#annonce_type_0').prop('checked')){
    $('.block_form_price').show();
    $('#annonce_price').attr('required', 'required');
}


// Qd "offre" change
// et qu'il est coché
// afficher le champ price
// et mettre en required
$('#annonce_type_0').change(function () {
    if($('#annonce_type_0').prop('checked')){
        $('.block_form_price').show();
        $('#annonce_price').attr('required', 'required');
    }
});

// Qd "demande" est change
// si il est coché
// Vider ce champ
// et cacher le champ input
// et supprimer le required
$('#annonce_type_1').change(function () {
    if($('#annonce_type_1').prop('checked')){
        $('#annonce_price').val('');
        $('.block_form_price').hide();

        $('#annonce_price').removeAttr('required');
    }
});


$('#annonce_price').keyup(function(e){
    $('#annonce_price').val(e.target.value.replace(/\s+/g, ''));
});
