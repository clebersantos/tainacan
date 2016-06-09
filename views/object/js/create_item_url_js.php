<script> 
$(function(){
    // #1 - breadcrumbs para localizacao da pagina
    $("#tainacan-breadcrumbs").show();
    $("#tainacan-breadcrumbs .current-config").text('<?php _e('Create new item','tainacan') ?>');
    //#3  -  ativo os tootips
     $('[data-toggle="tooltip"]').tooltip();
    //#4 - ckeditor para o conteudo do item
    showCKEditor('object_editor');
    //#5 - funcao que busca os rankings de um item
    list_ranking_create($("#object_id_add").val());
    //#6 - seto o id da colecao  no form do item     
    $('#create_object_collection_id').val($('#collection_id').val());
   
    show_object_properties();
    show_collection_licenses();
    var src = $('#src').val();
    $( '#submit_form' ).submit( function( e ) {
       var verify =  $( this ).serializeArray();
        //hook para validacao do formulario
        if(Hook.is_register( 'tainacan_validate_create_item_form')){
            Hook.call( 'tainacan_validate_create_item_form', [ $( this ).serializeArray() ] );
            console.log(Hook.result.is_validated);
            if(!Hook.result.is_validated){
                $('#modalImportMain').modal('hide');//mostro o modal de carregamento
                showAlertGeneral('<?php _e('Attention','tainacan') ?>', Hook.result.message, 'info');
                return false;
            }
        }
       $("#object_content").val(CKEDITOR.instances.object_editor.getData()); 
       var selKeys = $.map($("#dynatree").dynatree("getSelectedNodes"), function(node) {
                    return node.data.key;
       });
       $('#object_classifications').val(selKeys.join(", ")); 
       $('#modalImportMain').modal('show');//mostro o modal de carregamento
       $.ajax( {
              url: src+'/controllers/object/object_controller.php',
              type: 'POST',
              data: new FormData( this ),
              processData: false,
              contentType: false
            } ).done(function( result ) {
                    $('#modalImportMain').modal('hide');//mostro o modal de carregamento
                    elem_first =jQuery.parseJSON(result); 
                    if(!elem_first){
                         showAlertGeneral('<?php _e('Attention!','tainacan') ?>', '<?php _e('Invalid submission, file is too big!','tainacan') ?>', 'error');
                    }
                    if(elem_first.validation_error){
                        showAlertGeneral(elem_first.title, elem_first.msg, 'error');
                    }else{
                         $("#tainacan-breadcrumbs").hide();
                        $('#form').hide();
                         $('#main_part').show();
                        $('#collection_post').show();
                        $('#configuration').hide();
                        $('#configuration').html('');
                        //$("#dynatree").dynatree("getTree").reload();
                        //showList(src);
                        //wpquery_filter();
                        wpquery_clean();
                        set_containers_class($('#collection_id').val());
                        $("#container_socialdb").show('slow');
                        $('#create_button').show();
                        $('#menu_object').show();
                        showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
                    }
                    
            }); 
            e.preventDefault();
    });
    
    
    var myDropzone = new Dropzone("div#dropzone_new", {
                init: function () {
                    thisDropzone = this;
                    this.on("removedfile", function (file) {
                        //    if (!file.serverId) { return; } // The file hasn't been uploaded
                        $.get($('#src').val() + '/controllers/object/object_controller.php?operation=delete_file&object_id=' + $("#object_id_add").val() + '&file_name=' + file.name, function (data) {
                            if (data.trim() === 'false') {
                                showAlertGeneral('<?php _e("Atention!", 'tainacan') ?>', '<?php _e("An error ocurred, File already removed or corrupted!", 'tainacan') ?>', 'error');
                            } else {
                                showAlertGeneral('<?php _e("Success", 'tainacan') ?>', '<?php _e("File removed!", 'tainacan') ?>', 'success');
                            }
                        }); // Send the file id along
                    });
                    $.get($('#src').val() + '/controllers/object/object_controller.php?operation=list_files&object_id=' + $("#object_id_add").val(), function (data) {
                        try {
                            //var jsonObject = JSON.parse(data);
                            $.each(data, function (key, value) {
                                if (value.name !== undefined && value.name !== 0) {
                                    var mockFile = {name: value.name, size: value.size};
                                    thisDropzone.options.addedfile.call(thisDropzone, mockFile);
                                }
                            });
                        }
                        catch (e)
                        {
                            // handle error 
                        }
                    });
                },
                url: $('#src').val() + '/controllers/object/object_controller.php?operation=save_file&object_id=' + $("#object_id_add").val(),
                addRemoveLinks: true

            });
            //upload file limit
            $("#object_file").on("change", function (e) {
                //check whether browser fully supports all File API
                if (window.File && window.FileReader && window.FileList && window.Blob)
                {
                    //get the file size and file type from file input field
                    var fsize = $('#object_file')[0].files[0].size;
                    var server_size = '<?php echo file_upload_max_size(); ?>';
                    if (fsize > parseFloat(server_size)) //do something if file size more than 1 mb (1048576)
                    {
                        showAlertGeneral('<?php _e('Attention!', 'tainacan') ?>', '<?php _e('This file is too big, the file limit size of this server is ', 'tainacan') ?>' + bytesToSize(server_size), 'error');
                        // alert(fsize +" bites\nToo big!");
                        $('#object_file').val('');
                    }
                }
            });
        
        
    <?php if(isset($has_url)): ?>
         import_object();
         $('#external_option').attr('checked','checked');
         $('#external_option').trigger('change');
    <?php endif; ?>
        
    <?php if(isset($has_file)): ?>
         $('input:radio[name="object_type"]').filter('[value="<?php echo $file_type ?>"]').attr('checked', true);
         $('#external_option').attr('checked','checked');
         $('#external_option').trigger('change');
         $('#object_url_others_input').val('<?php echo $has_file ?>');
         $('#object_content_text').hide();
    <?php endif; ?>
        
	
});
//BEGIN: funcao para mostrar votacoes
    function list_ranking_create(id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/ranking/ranking_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'create_list_ranking_object', object_id: id}
        }).done(function (result) {
            $('#create_list_ranking_' + id).html(result);
            $('.dropdown-toggle').dropdown();
            $('.nav-tabs').tab(); 
        });
    }
//END

function show_object_properties(){
    var selKeys = $.map($("#dynatree").dynatree("getSelectedNodes"), function(node) {
                            return node.data.key;
    });
    var selectedCategories = selKeys.join(",");
    $.ajax( {
        url: $('#src').val()+'/controllers/object/object_controller.php',
        type: 'POST',
        data: {operation: 'show_object_properties_accordion',object_id:$("#object_id_add").val(),collection_id:$("#create_object_collection_id").val(),categories:selectedCategories}
      } ).done(function( result ) {
            $('#show_form_properties').html(result);
        });
    }

function show_collection_licenses(){
    $.ajax( {
        url: $('#src').val()+'/controllers/object/object_controller.php',
        type: 'POST',
        data: {operation: 'show_collection_licenses',collection_id:$("#create_object_collection_id").val()}
      } ).done(function( result ) {
        //$('html, body').animate({
           ///  scrollTop: parseInt($("#wpadminbar").offset().top)
           // }, 900);       
        $('#show_form_licenses').html(result); 
    });
}

function back_main_list() {
        $('#form').hide();
        $("#tainacan-breadcrumbs").hide();
        $('#configuration').hide();
        $('#main_part').show();
        $('#display_view_main_page').show();
        $.ajax( {
            url: $('#src').val()+'/controllers/object/object_controller.php',
            type: 'POST',
            data: {operation: 'delete_temporary_object',ID:$("#object_id_add").val()}
        } ).done(function( result ) {
            // $('html, body').animate({
             //   scrollTop: parseInt($("#wpadminbar").offset().top)
           // }, 900);  
        });
    }
 
 function import_object(){
    show_modal_main(); 
    var url = String($('#url_object').val());
    var key = $('#socialdb_embed_api_id').val();
    if(url.search('youtube.com')>=0){
         $('#object_content_text').hide();// ckeditor apenas para texto
         $('#object_url_text').hide();// esconde o campo de url para textos
         $('#object_url_others').show('slow');// o campo para colocar a url do item sem ser texto
         $('#object_url_others_input').val(url);
         console.log($('#object_url_others').val());
         $('#video_type').attr('checked','checked');
         return false;
    }
    var ajaxurl = 'http://api.embed.ly/1/oembed?key=:' + key + '&url=' + url;
    //div loader
    $('#loading').css({
        width: $(document).width(),
        height: $(document).height(),
        background: $('#src').val() + '/libraries/images/catalogo_loader_725.gif'
    });
    $('#loading').fadeIn(1000);
    $('#loading').fadeTo("slow", 0.8);
    $.getJSON(ajaxurl, {}, function (json) {
        hide_modal_main();
        var description = '', title = '';
        if (json.title !== undefined && json.title != null && json.title != false) {
            title = json.title;
        }
        else {
            $('#loading').hide();
            showAlertGeneral('<?php _e('Attention','tainacan') ?>', '<?php _e('This URL not contains availables items for importation','tainacan') ?>', 'error');
            return;
        }
        // se nao tiver descricao ele coloca o titulo na descricao
        if (json.description !== undefined && json.description != null && json.description != false) {
            description += json.description;
        }
        else {
            description = title;
        }
        //concatena o html na descricao
        if (json.html !== undefined && json.html != null && json.html != false) {
            json.html = json.html.replace('width="854"', 'width="200"');
            json.html = json.html.replace('height="480"', 'height="200"');
            description = json.html + description;
        }
        // limpando o formulario do modal de insercao
        $('#thumbnail_url').html('');
        $('#title_insert_object_url').val('');
        $('#description_insert_object_url').val('');
        //pegando a imagem
        var img = json.thumbnail_url;
        var html = '';
        $('#thumbnail_url').val(img);
        // verifico se existe imagem para ser importada
        if (json.thumbnail_url !== undefined && json.thumbnail_url != null && json.thumbnail_url != false) {
            html += "<img id='thumbnail' src='" + img + "' style='cursor: pointer; max-width: 170px;' />&nbsp&nbsp";
            $('#image_side_create_object').html(html);
        }
        $('#object_name').val(title);
        CKEDITOR.instances.object_editor.setData(description);
        $('#loading').hide('slow');

    }).fail(function (result) {
        console.log('error', result, url);
        $('#loading').hide();
        hide_modal_main();
        showAlertGeneral('Atenção', 'URL inexistente ou indisponível', 'error');
    });
 }
 //funcoes que mostram a visualizacao do item
 function show_other_type_field(field){
    if($(field).val()==='other'){
        $('#object_type_other').attr('required','required');
        $('#object_type_other').show('slow');
        $('#badge_helper').hide();
    }else{
        $('#object_type_other').removeAttr("required");
        $('#object_type_other').hide('slow');
        $('#badge_helper').show();
    }
    
    console.log($(field).val());
    if($(field).val()!=='text'){
        $('#object_url').val('');
         $('#object_url_others').val('');
        $('#external_option').attr('checked','checked'); // se for externo o default e url
        $('#object_content_text').hide();// ckeditor apenas para texto
        $('#object_url_text').hide();// esconde o campo de url para textos
        $('#url_object').removeAttr("required");//retiro o campo de requirido deste input para urls que sejam do item do tipo texto
        $('#object_url_others').show('slow');// o campo para colocar a url do item sem ser texto
        $('#object_url_others').attr('required','required');
         $('#object_file').hide();// esconde a submissao de items tipo arquivo
         $('#object_file').removeAttr("required");// retiro o campo de requirido do arquivo
    }else{
        $('#badge_helper').hide();// esconde o helper
        $('#object_url_others').val('');
         $('#object_file').hide();// esconde a submissao de items tipo arquivo
         $('#object_file').removeAttr("required");// retiro o campo de requirido do arquivo
         $('#internal_option').attr('checked','checked');
         $('#object_url_text').hide();// escondo o campo de colocar url para textos
         $('#url_object').removeAttr("required");//retiro o campo de requirido deste input para urls que sejam do item do tipo texto
         $('#object_url_others').hide();// escondo o campo de colocar a url para tipos de arquivo que nao seja texto
         $('#object_url_others').removeAttr("required");//retiro o campo de requirido deste input para urls que nao seja do item do tipo texto
         $('#object_content_text').show();
    }
    //retirando o thumbnail
    if($(field).val()==='image'){
        $('#thumbnail-idea-form').hide();
    }else{
        $('#thumbnail-idea-form').show();
    }
 }
 // text functions
  function toggle_from(field){
    if($(field).val()==='external'){
        if($('input[name=object_type]:checked', '#submit_form').val()==='text'){
            $('#object_url_others').hide();// o campo url para outros tipos 
            $('#object_url_others').removeAttr("required");//retiro o campo de requirido deste input para urls que nao seja do item do tipo texto
            $('#object_url_text').show('slow');// o campo url para text
            $('#url_object').attr('required','required');// coloco o campo de url para arquivos que nao seja texto como obrigatorio
            $('#object_file').hide('slow'); // escondo o campo para pegar arquivos internos
            $('#object_file').removeAttr("required");// retiro o campo de requirido do arquivo
        }else{
            $('#object_file').hide(); 
            $('#object_file').removeAttr("required");
            $('#object_url_text').hide('slow');// escondo o campo  de url para textos ja que o conteudo sera escrito dentro do ckeditor
            $('#url_object').removeAttr("required");//retiro o campo de requirido deste input para urls que sejam do item do tipo texto
            $('#object_url_others').show('slow'); 
            $('#object_url_others').attr('required','required');// coloco o campo de url para arquivos que nao seja texto como obrigatorio
        }
    }else{
        if($('input[name=object_type]:checked', '#submit_form').val()==='text'){
            $('#object_file').hide(); // escondo o campo de upload de arquivos
            $('#object_file').removeAttr("required");// retiro o campo de requirido do arquivo
            $('#object_url_others').hide();//escondo o input para urls para tipos que nao seja texto
            $('#object_url_others').removeAttr("required");//retiro o campo de requirido deste input para urls que nao seja do item do tipo texto
            $('#object_url_text').hide('slow');// escondo o campo  de url para textos ja que o conteudo sera escrito dentro do ckeditor
            $('#url_object').removeAttr("required");//retiro o campo de requirido deste input para urls que sejam do item do tipo texto
        }else{
            $('#object_url_text').hide();// escondo o campo de colocar url para textos
            $('#object_url_text').removeAttr("required");//retiro o campo de requirido deste input para urls que sejam do item do tipo texto
            $('#object_url_others').hide();// escondo o campo de colocar a url para tipos de arquivo que nao seja texto
            $('#url_object').removeAttr("required");//retiro o campo de requirido deste input para urls que nao seja do item do tipo texto
            $('#object_file').show('slow'); // mostra o campo de submissao de arquivo
            $('#object_file').attr('required','required');// coloco o campo de upload de arquivo como obrigatorio
        }
    }
 }
 //se mudar a url
 function set_source(field){
      $('#object_source').val($(field).val()); 
 }
</script>
            