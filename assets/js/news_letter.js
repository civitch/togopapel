import $ from "jquery";
var idTemplate=0;
var userId=0;
$("#btn-add-template").click(function(){
   $("#exampleModalLabel").text("Nouveau template");
   $('#modal_new_template').modal({keyboard: false})
   $("#content_template").val('');
   $("#libelle_template").val('');
   console.log("good");
});
$("#btn_add_template_save").click(function(){
   console.log("good");
   let libelle=$("#libelle_template").val();
   let content=$("#content_template").val();
   $('#modal_new_template').modal('toggle');
   let dataReq={libelle:libelle,content:content,id:idTemplate}
   var urlForRequete="/corporate/news-letter/new-template";
   if($("#exampleModalLabel").text()=="Editer template")
   {
      urlForRequete="/corporate/news-letter/edit-template";
      $.ajax({
         type:"post",
         url:urlForRequete,
         data: dataReq,
         success:function(json)
         {
            $("#tempate-"+idTemplate+" td:eq(0)").text(libelle);
            $("#tempate-"+idTemplate+" td:eq(0)").html('<iframe style="height:200px;width:400px;" srcdoc="'+content+'"></iframe>');
            console.log(json);
         },
         error:function(xhr,errMess,err)
         {
            console.log(errMess);
         }
      });
   }
   else
   {
      $.ajax({
         type:"post",
         url:urlForRequete,
         data: dataReq,
         success:function(json)
         {
            console.log(json);
         },
         error:function(xhr,errMess,err)
         {
            console.log(errMess);
         }
      });
   }

});
$(".btn-modifier").click(function(e){
   e.preventDefault();
   idTemplate=$(this).prop('id').split('-')[2];
   $("#exampleModalLabel").text("Editer template");
   $.ajax({
      type:"get",
      url:"/corporate/news-letter/get-template/"+idTemplate,
      success:function(json)
      {
         $("#libelle_template").val(json.libelle);
         $("#content_template").val(json.content);
         $('#modal_new_template').modal('toggle');

      },
      error:function(xhr,errMess,err)
      {
         console.log(errMess);
      }
   });
   //let libelle=$("#libelle_template").val();
   //let content=$("#content_template").val();
});
$(".btn-supprimer").click(function(e){
   e.preventDefault();
   idTemplate=$(this).prop('id').split('-')[2];
   console.log(idTemplate);
   $('#modal_supprimer_template').modal('toggle');
   //let libelle=$("#libelle_template").val();
   //let content=$("#content_template").val();
});
$(".btn-envoyer").click(function(e){
   e.preventDefault();
   idTemplate=$(this).prop('id').split('-')[2];
   $("#blocinputUser").removeClass('d-block');
   $("#blocinputUser").addClass('d-none');
   $("#typeOfReceiver").val(1);
   $("#inputUser").val('')
   $('#modal_envoyer').modal('toggle');
   //let libelle=$("#libelle_template").val();
   //let content=$("#content_template").val();
});
$("#btn_supprimer_template_confirm").click(function(e){
   $.ajax({
      type:"get",
      url:"/corporate/news-letter/delete-template/"+idTemplate,
      success:function(json)
      {
         $('#modal_supprimer_template').modal('toggle');
      },
      error:function(xhr,errMess,err)
      {
         console.log(errMess);
      }
   });
   //let libelle=$("#libelle_template").val();
   //let content=$("#content_template").val();
});
$("#btn_envoyer_confirm").click(function(e){

   e.preventDefault();
   $('#modal_envoyer').modal('toggle');
   $('#modal_loading').modal('toggle');
   var data={};
   data.type=$("#typeOfReceiver").val();
   if($("#typeOfReceiver").val()==4)
   {
      data.userId=userId;
   }
   $.ajax({
      type:"post",
      url:"/corporate/news-letter/envoyer-new/"+idTemplate,
      data:data,
      success:function(json)
      {
         $('#modal_loading').modal('toggle');
      },
      error:function(xhr,errMess,err)
      {
         console.log(errMess);
      }
   });
   //let libelle=$("#libelle_template").val();
   //let content=$("#content_template").val();
});
$("#typeOfReceiver").change(function(e){
   let selectedOption=$(this).val();
   let inputUser=$("#inputUser").val();
   if(selectedOption==4)
   {
      $("#blocinputUser").removeClass('d-none');
      $("#blocinputUser").addClass('d-block');
      $.ajax({
         type:"post",
         url:"/corporate/news-letter/get-user",
         data:{key:inputUser},
         success:function(json)
         {
            console.log(json);
            var _html="";
            var cpt=0;
            $("#listOfUser").html("");
            while(cpt<json.length)
            {
               _html+='<li class="list-group-item" id="'+json[cpt].id+'">'+json[cpt].user+'</li>';
               cpt++;
            }
            $("#listOfUser").html(_html);
            $("#listOfUser .list-group-item").bind( "click", selectUser);
         },
         error:function(xhr,errMess,err)
         {
            console.log(errMess);
         }
      });
   }
   else
   {
      $("#blocinputUser").removeClass('d-block');
      $("#blocinputUser").addClass('d-none');
   }

});
$("#inputUser").keyup(function(){
   let inputUser=$(this).val();
   $("#listOfUser").removeClass('d-none');
   console.log(inputUser);
   $.ajax({
      type:"post",
      url:"/corporate/news-letter/get-user",
      data:{key:inputUser},
      success:function(json)
      {
         console.log(json);
         var _html="";
         var cpt=0;
         $("#listOfUser").html("");
         while(cpt<json.length)
         {
            _html+='<li class="list-group-item" id="'+json[cpt].id+'">'+json[cpt].user+'</li>';
            cpt++;
         }
         $("#listOfUser").html(_html);
         $("#listOfUser .list-group-item").bind( "click", selectUser);
      },
      error:function(xhr,errMess,err)
      {
         console.log(errMess);
      }
   });
});
function selectUser(event){
   let nom=$(this).text();
   let id=$(this).prop('id');
   userId=id;
   $("#inputUser").val(nom);
   $("#listOfUser").addClass('d-none');
}