<script> 
$(function(){  
    
    var myDropzone = new Dropzone("div#dropzone_multiple_items", {
                maxFilesize:  parseInt('<?php echo file_upload_max_size(); ?>')/ 1024 / 1024,
                init: function () {
                    thisDropzone = this;
                    this.on("removedfile", function (file) {
                        //    if (!file.serverId) { return; } // The file hasn't been uploaded
                        $.get($('#src').val()+'/controllers/object/object_controller.php?operation=delete_file&object_id='+$("#object_id_add").val()+'&file_name='+ file.name, function (data) {
                            if (data.trim() === 'false') {
                                 showAlertGeneral('<?php _e("Atention!",'tainacan') ?>', '<?php _e("An error ocurred, File already removed or corrupted!",'tainacan') ?>', 'error');
                            } else {
                                showAlertGeneral('<?php _e("Success",'tainacan') ?>', '<?php _e("File removed!",'tainacan') ?>', 'success');
                            }
                        }); // Send the file id along
                    });
                    //ao terminar o uplaod dos itens
                    this.on("queuecomplete", function (file) {
                          $('#click_editor_items_button').show();
                          $('#click_editor_items_button').focus();
//                        $.get($('#src').val()+'/controllers/object/object_controller.php?collection_id='+$('#collection_id').val()+'&operation=editor_items&object_id='+<?php echo $object_id ?>, function (data) {
//                            try {
//                                //var jsonObject = JSON.parse(data);
//                                if(data!=0){
//                                    $("#uploading").slideUp();
//                                    $('#editor_items').html(data);
//                                }else{
//                                    showAlertGeneral('<?php _e("Atention!",'tainacan') ?>', '<?php _e("File is too big or Uploaded, however, not supported by wordpress, please select valid files!",'tainacan') ?>', 'error');
//                                }
//                            }
//                            catch (e)
//                            {
//                                // handle error 
//                            }
//                        });
                    });
                    $.get($('#src').val()+'/controllers/object/object_controller.php?operation=list_files&object_id='+$("#object_id_add").val(), function (data) {
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
                    this.on("success", function(file, message) { 
//                        elem = JSON.parse(message);
//                        
//                        $('#click_editor_items_button').show();
//                        $('#click_editor_items_button').focus();
//                        if(elem.errors.upload_error[0]){
//                            showAlertGeneral('<?php _e("Atention!",'tainacan') ?>', '<?php _e("Sorry, this file type is not permitted for security reasons or too big for this server",'tainacan') ?>', 'error');
//                        }
                    });
                },
                url: $('#src').val()+'/controllers/object/object_controller.php?operation=save_file&object_id='+<?php echo $object_id ?>,
                addRemoveLinks: true

            });
	
});
function back_main_list() {
        $('#form').hide();
        $('#create_button').show();
        $('#menu_object').show();
        $("#list").show();
        $("#container_socialdb").show('fast');
        $.ajax( {
        url: $('#src').val()+'/controllers/object/object_controller.php',
        type: 'POST',
        data: {operation: 'delete_temporary_object',ID:'<?php echo $object_id ?>'}
        } ).done(function( result ) {
            $('#main_part').show();
            $('#collection_post').show();
            $('#configuration').slideDown();
            $('#configuration').hide();
        });
    }
    
    function edit_items_uploaded() {
        $.ajax( {
        url: $('#src').val()+'/controllers/object/object_controller.php',
        type: 'POST',
        data: {
            operation: 'editor_items',
            collection_id: $('#collection_id').val(),
            object_id:'<?php echo $object_id ?>'}
        } ).done(function( data ) {
            if(data!=0){
                console.log('mostra_upload');
                $("#uploading").slideUp();
                $('#editor_items').html(data);
                
                $("#editor_items").css('display','block');
            }else{
                showAlertGeneral('<?php _e("Attention!",'tainacan') ?>', '<?php _e("File is too big or Uploaded, however, not supported by wordpress, please select valid files!",'tainacan') ?>', 'error');
            }
        });
    }
    
    function upload_more_files(){
          swal({
                title: '<?php _e("Attention!",'tainacan') ?>',
                text: '<?php _e("You will lose all data unpublished!",'tainacan') ?>',
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: 'btn-primary',
                closeOnConfirm: true,
                closeOnCancel: true
            },
            function (isConfirm) {
                if (isConfirm) {
                    $("#editor_items").slideDown();
                    $("#editor_items").hide();
                    $('#uploading').show();
                }
            });
    }
</script>
            