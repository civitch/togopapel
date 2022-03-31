import $ from "jquery";

function gdo_upload_one(){
    const inputFileOne = $('.picture_gdo_upload_one .custom-file input');
    inputFileOne.on('change', function(){
        const file = this.files[0];
        const img = $('.picture_gdo_upload_one #img_preview_fil_one');
        const close = $('.rmv_file_one');
        const blk_img = $('.sdh_iblk_img_one');
        const FileUploadPath = $(this).val();
        const Extension = FileUploadPath.substring(FileUploadPath.lastIndexOf('.') + 1).toLowerCase();
        if(FileUploadPath !== ""){
            if (Extension == "png" || Extension == "jpeg" || Extension == "jpg") {
                if(file){
                    const reader = new FileReader();
                    img.css('display', 'block');
                    close.css('display', 'block');
                    blk_img.css('display', 'block').css('background-color','#222');
                    reader.addEventListener('load', function(){
                        img.attr('src', this.result);
                    });
                    reader.readAsDataURL(file);
                    $('.picture_gdo_upload_one .custom-file').css('display', 'none');
                }
                else{
                    img.css('display', 'none');
                    img.attr('src', '');
                    close.css('display', 'none');
                    blk_img.css('display', 'none').css('background-color','transparent');
                    $('.picture_gdo_upload_one .custom-file').css('display', 'block');
                }
            }
        }
    });
}

function gdo_upload_two(){
    const inputFileTwo = $('.picture_gdo_upload_two .custom-file input');
    inputFileTwo.on('change', function(){
        const file = this.files[0];
        const img = $('.picture_gdo_upload_two #img_preview_fil_two');
        const close = $('.rmv_file_two');
        const blk_img = $('.sdh_iblk_img_two');
        const FileUploadPath = $(this).val();
        const Extension = FileUploadPath.substring(FileUploadPath.lastIndexOf('.') + 1).toLowerCase();
        if(FileUploadPath !== ""){
            if (Extension == "png" || Extension == "jpeg" || Extension == "jpg") {
                if(file){
                    const reader = new FileReader();
                    img.css('display', 'block');
                    close.css('display', 'block');
                    blk_img.css('display', 'block').css('background-color','#222');
                    reader.addEventListener('load', function(){
                        img.attr('src', this.result);
                    });
                    reader.readAsDataURL(file);
                    $('.picture_gdo_upload_two .custom-file').css('display', 'none');
                }
                else{
                    img.css('display', 'none');
                    img.attr('src', '');
                    close.css('display', 'none');
                    blk_img.css('display', 'none').css('background-color','transparent');
                    $('.picture_gdo_upload_two .custom-file').css('display', 'block');
                }
            }
        }
    });
}

function gdo_upload_three(){
    const inputFileThree = $('.picture_gdo_upload_three .custom-file input');
    inputFileThree.on('change', function(){
        const file = this.files[0];
        const img = $('.picture_gdo_upload_three #img_preview_fil_three');
        const close = $('.rmv_file_three');
        const blk_img = $('.sdh_iblk_img_three');
        const FileUploadPath = $(this).val();
        const Extension = FileUploadPath.substring(FileUploadPath.lastIndexOf('.') + 1).toLowerCase();
        if(FileUploadPath !== ""){
            if (Extension == "png" || Extension == "jpeg" || Extension == "jpg") {
                if(file){
                    const reader = new FileReader();
                    img.css('display', 'block');
                    close.css('display', 'block');
                    blk_img.css('display', 'block').css('background-color','#222');
                    reader.addEventListener('load', function(){
                        img.attr('src', this.result);
                    });
                    reader.readAsDataURL(file);
                    $('.picture_gdo_upload_three .custom-file').css('display', 'none');
                }
                else{
                    img.css('display', 'none');
                    img.attr('src', '');
                    close.css('display', 'none');
                    blk_img.css('display', 'none').css('background-color','transparent');
                    $('.picture_gdo_upload_three .custom-file').css('display', 'block');
                }
            }
        }
    });
}

function gdo_upload_four(){
    const inputFileOne = $('.picture_gdo_upload_four .custom-file input');
    inputFileOne.on('change', function(){
        const file = this.files[0];
        const img = $('.picture_gdo_upload_four #img_preview_fil_four');
        const close = $('.rmv_file_four');
        const blk_img = $('.sdh_iblk_img_four');
        const FileUploadPath = $(this).val();
        const Extension = FileUploadPath.substring(FileUploadPath.lastIndexOf('.') + 1).toLowerCase();
        if(FileUploadPath !== ""){
            if (Extension == "png" || Extension == "jpeg" || Extension == "jpg") {
                if(file){
                    const reader = new FileReader();
                    img.css('display', 'block');
                    close.css('display', 'block');
                    blk_img.css('display', 'block').css('background-color','#222');
                    reader.addEventListener('load', function(){
                        img.attr('src', this.result);
                    });
                    reader.readAsDataURL(file);
                    $('.picture_gdo_upload_four .custom-file').css('display', 'none');
                }
                else{
                    img.css('display', 'none');
                    img.attr('src', '');
                    close.css('display', 'none');
                    blk_img.css('display', 'none').css('background-color','transparent');
                    $('.picture_gdo_upload_four .custom-file').css('display', 'block');
                }
            }
        }
    });
}

function gdo_upload_pack(){
    const inputFileOne = $('.picture_gdo_upload_pack .custom-file input');
    inputFileOne.on('change', function(){
        const file = this.files[0];
        const img = $('.picture_gdo_upload_pack #img_preview_fil_pack');
        const close = $('.rmv_file_pack');
        const blk_img = $('.sdh_iblk_img_pack');
        const FileUploadPath = $(this).val();
        const Extension = FileUploadPath.substring(FileUploadPath.lastIndexOf('.') + 1).toLowerCase();
        if(FileUploadPath !== ""){
            if (Extension == "png" || Extension == "jpeg" || Extension == "jpg") {
                if(file){
                    const reader = new FileReader();
                    img.css('display', 'block');
                    close.css('display', 'block');
                    blk_img.css('display', 'block').css('background-color','#222');
                    reader.addEventListener('load', function(){
                        img.attr('src', this.result);
                    });
                    reader.readAsDataURL(file);
                    $('.picture_gdo_upload_pack .custom-file').css('display', 'none');
                }
                else{
                    img.css('display', 'none');
                    img.attr('src', '');
                    close.css('display', 'none');
                    blk_img.css('display', 'none').css('background-color','transparent');
                    $('.picture_gdo_upload_pack .custom-file').css('display', 'block');
                }
            }
        }
    });
}

$(".rmv_file_pack").click(() => {
    const img = $('.picture_gdo_upload_pack #img_preview_fil_pack');
    const blk_img = $('.sdh_iblk_img_pack');
    img.attr('src', '');
    img.css('display', 'none');
    $(".rmv_file_pack").css('display', 'none');
    blk_img.css('display', 'none').css('background-color','transparent');
    $('.picture_gdo_upload_pack .custom-file input').val("");
    $('.picture_gdo_upload_pack .custom-file').css('display', 'block');
});


$(".rmv_file_one").click(() => {
    const img = $('.picture_gdo_upload_one #img_preview_fil_one');
    const blk_img = $('.sdh_iblk_img_one');
    img.attr('src', '');
    img.css('display', 'none');
    $(".rmv_file_one").css('display', 'none');
    blk_img.css('display', 'none').css('background-color','transparent');
    $('.picture_gdo_upload_one .custom-file input').val("");
    $('.picture_gdo_upload_one .custom-file').css('display', 'block');
});

$(".rmv_file_two").click(() => {
    const img = $('.picture_gdo_upload_two #img_preview_fil_two');
    const blk_img = $('.sdh_iblk_img_two');
    const $this = $(this);
    img.attr('src', '');
    img.css('display', 'none');
    $(".rmv_file_two").css('display', 'none');
    blk_img.css('display', 'none').css('background-color','transparent');
    $('.picture_gdo_upload_two .custom-file input').val("");
    $('.picture_gdo_upload_two .custom-file').css("display", "block");
});


$(".rmv_file_three").click(() => {
    const img = $('.picture_gdo_upload_three #img_preview_fil_three');
    const blk_img = $('.sdh_iblk_img_three');
    img.attr('src', '');
    img.css('display', 'none');
    $(".rmv_file_three").css('display', 'none');
    blk_img.css('display', 'none').css('background-color','transparent');
    $('.picture_gdo_upload_three .custom-file input').val("");
    $('.picture_gdo_upload_three .custom-file').css("display", "block");
});

$(".rmv_file_four").click(() => {
    const img = $('.picture_gdo_upload_four #img_preview_fil_four');
    const blk_img = $('.sdh_iblk_img_four');
    img.attr('src', '');
    img.css('display', 'none');
    $(".rmv_file_four").css('display', 'none');
    blk_img.css('display', 'none').css('background-color','transparent');
    $('.picture_gdo_upload_four .custom-file input').val("");
    $('.picture_gdo_upload_four .custom-file').css("display", "block");
});


gdo_upload_one();
gdo_upload_two();
gdo_upload_three();
gdo_upload_four();
gdo_upload_pack();



