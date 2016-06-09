<script>
    $(function () {
        var src = $('#src').val();

        $('.pagination_items').jqPagination({
            link_string: '/?page={page_number}',
            max_page: $('#number_pages').val(),
            paged: function (page) {
                $('html,body').animate({scrollTop: 0}, 'slow');
                wpquery_page(page);
            }
        });

        $("#container_three_columns").removeClass('white-background');
        setMenuContainerHeight();

        $(".droppableClassifications").droppable({
                hoverClass: "drophover",
                addClasses: true,
                //    tolerance: "pointer",
                over: function (event, ui) {
                    logMsg("droppable.over, %o, %o", event, ui);
                },
                drop: function (event, ui) {
                    var object_id = $(this).closest('div').find('.object_id').val();
                    if($('#add_classification_allowed_'+object_id).val()=='1'){
                        var source = ui.helper.data("dtSourceNode") || ui.draggable;
                        var key = source.data.key;
                        var n = key.toString().indexOf("_");
                        var value_id = '';
                        var type = ' ';
                        if (n > 0) {// se for propriedade de objeto
                            values = key.split("_");
                            if (values[1] === 'facet') {
                                showAlertGeneral('<?php _e('Atention', 'tainacan') ?>', '<?php _e('You may not classificate objects with root categories, object properties and tags', 'tainacan') ?>', 'error');
                                return;
                            }
                            else if (values[1] === 'tag') {
                                type = 'tag';
                                value_id = values[0];
                            } else {
                                type = values[1];
                                value_id = values[0];
                            }
                        } else {
                            type = 'category';
                            value_id = key.toString();
                        }

                        $.ajax({
                            type: "POST",
                            url: $('#src').val() + "/controllers/event/event_controller.php",
                            data: {
                                operation: 'add_event_classification_create',
                                socialdb_event_create_date: '<?php echo mktime(); ?>',
                                socialdb_event_user_id: $('#current_user_id').val(),
                                socialdb_event_classification_object_id: object_id,
                                socialdb_event_classification_term_id: value_id,
                                socialdb_event_classification_type: type,
                                socialdb_event_collection_id: $('#collection_id').val()}
                        }).done(function (result) {
                            elem_first = jQuery.parseJSON(result);
                            set_containers_class($('#collection_id').val());
                            show_classifications(object_id);
                            showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);

                        });
                    }else{
                        showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('Action not allowed by admin!', 'tainacan') ?>', 'info');
                    }
                },
                activate: function (event, ui) {
                    // $(this).addClass("ui-state-highlight").find("p").hover();
                    $(this).css('border-style', 'dashed');
                    //$(".cat").removeClass("categorias");
                    //$(".row cat").show(); 
                },
                deactivate: function (event, ui) {
                    // $(this).addClass("ui-state-highlight").find("p").hover();
                    $(this).css('border-style', 'none');

                    //    $(".categorias").hide();
                    //  $(".categorias").hover();
                }
            }); 
    });

    function show_info(id) {
        check_privacity_info(id);
        list_ranking(id);
        list_files(id);
        list_properties(id);
        list_properties_edit_remove(id);
        $("#more_info_show_" + id).toggle();
        $("#less_info_show_" + id).toggle();
        $("#all_info_" + id).toggle('slow');
    }

    function showPopover(id) {
        // pop up #example1, #example2, #example3 with same content
        $('#popover_network' + id).popover({
            html: true,
            content: function () {
                return $('#popover_content_wrapper' + id).html();
            }
        });
    }

//BEGIN: funcao para mostrar os arquivos
    function list_files(id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/object/object_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'show_files', object_id: id}
        }).done(function (result) {
            $('#list_files_' + id).html(result);
            $('.dropdown-toggle').dropdown();
            $('.nav-tabs').tab();
        });
    }
//END
//BEGIN: funcao para mostrar votacoes
    function list_ranking(id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/ranking/ranking_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'list_ranking_object', object_id: id}
        }).done(function (result) {
            $('#list_ranking_' + id).html(result);
            $('.dropdown-toggle').dropdown();
            $('.nav-tabs').tab();
        });
    }

    function list_ranking_auto_load(id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/ranking/ranking_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'list_ranking_object', object_id: id}
        }).done(function (result) {
//        $('#list_ranking_auto_load_'+id).html(result);
//        $('#list_ranking_auto_load_'+id).show();
            $('#ranking_auto_load').html(result);
            $('#ranking_auto_load').shshow_classificiations_ow();
        });
    }
//END
//BEGIN:as proximas funcoes sao para mostrar os eventos
// list_properties(id): funcao que mostra a primiera listagem de propriedades
    function list_properties(id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/object/object_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'list_properties', object_id: id}
        }).done(function (result) {
            $('#list_all_properties_' + id).html(result);
            $('.dropdown-toggle').dropdown();
            $('.nav-tabs').tab();
        });
    }
// mostra a listagem apos clique no botao para edicao e exclusao
    function list_properties_edit_remove(id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/object/object_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'list_properties_edit_remove', object_id: id}
        }).done(function (result) {
            $('#list_properties_edit_remove').html(result);
            $('.dropdown-toggle').dropdown();
            $('.nav-tabs').tab();
        });
    }
// mostra o formulario para criacao de propriedade de dados
    function show_form_data_property(object_id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/object/object_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'show_form_data_property', object_id: object_id}
        }).done(function (result) {
            $('#data_property_form_' + object_id).html(result);
            $('#list_all_properties_' + object_id).hide();
            $('#object_property_form_' + object_id).hide();
            $('#edit_data_property_form_' + object_id).hide();
            $('#edit_object_property_form_' + object_id).hide();
            $('#data_property_form_' + object_id).show();
            $('.dropdown-toggle').dropdown();
            $('.nav-tabs').tab();
        });
    }
// mostra o formulario para criacao de propriedade de objeto
    function show_form_object_property(object_id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/object/object_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'show_form_object_property', object_id: object_id}
        }).done(function (result) {
            $('#object_property_form_' + object_id).html(result);
            $('#list_all_properties_' + object_id).hide();
            $('#data_property_form_' + object_id).hide();
            $('#edit_data_property_form_' + object_id).hide();
            $('#edit_object_property_form_' + object_id).hide();
            $('#object_property_form_' + object_id).show();
            $('.dropdown-toggle').dropdown();
            $('.nav-tabs').tab();
        });
    }
// funcao acionando no bolta voltar que mostra a listagem principal
    function back_button(object_id) {
        $('#data_property_form_' + object_id).hide();
        $('#object_property_form_' + object_id).hide();
        $('#edit_data_property_form_' + object_id).hide();
        $('#edit_object_property_form_' + object_id).hide();
        $('#list_all_properties_' + object_id).show();
    }
// END:fim das funcoes que mostram as propriedades
//funcao que mostra as classificacoes apos clique no botao show_classification
    function show_classifications(object_id) {
        var close_box = "<a href='javascript:void(0)' class='close-metadata-box' onclick='toggle_item_box_elements("+object_id+")'>" +
            "<span class='glyphicon glyphicon-remove-circle'></span></a>";

        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/object/object_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'show_classifications', object_id: object_id}
        }).done(function (result) {
            toggle_item_box_elements(object_id);
            $('#classifications_' + object_id).html( close_box + result).fadeIn();
            $('#show_classificiations_' + object_id).fadeOut();
            $('.dropdown-toggle').dropdown();
            $('.nav-tabs').tab();
        });
    }

    function toggle_item_box_elements(object_id) {
        var elements = [ '.item-display-title', '.item-description', '.item-author', '.item-creation'];
        $.each(elements, function(index, element) {
            $('#classifications_' + object_id).parent('.item-meta').find(element).toggle();
        });

        $('#classifications_' + object_id + ' .close-metadata-box').toggle();
        $('#classifications_' + object_id).toggleClass('shown-classifications').toggle();
        $('#show_classificiations_' + object_id).fadeIn();
    }

//mostrar modal de denuncia
    function show_report_abuse(object_id) {
        $('#modal_delete_object' + object_id).modal('show');
    }
// editando objeto
    function edit_object(object_id) {
        $('#modalImportMain').modal('show');//mostro o modal de carregamento
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/object/object_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'edit_default', object_id: object_id}
        }).done(function (result) {
            $('#modalImportMain').modal('hide');//escondo o modal de carregamento
            $("#container_socialdb").hide('slow');
            $("#form").hide().html(result).show('slow');
            $('#create_button').hide();
            $('.dropdown-toggle').dropdown();
            $('.nav-tabs').tab();
        });
    }
    
    function edit_object_item(object_id) {
        $('#modalImportMain').modal('show');//mostro o modal de carregamento
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/object/object_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'edit', object_id: object_id}
        }).done(function (result) {
            hide_modal_main();
             $("#form").html('');
            $('#main_part').hide();
            $('#display_view_main_page').hide();
            $('#loader_collections').hide();
            $('#configuration').html(result).show();
            $('.dropdown-toggle').dropdown();
            $('.nav-tabs').tab();
        });
    }

    function redirect_facebook(object_id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/ranking/ranking_controller.php",
            data: {fb_id: $('#socialdb_fb_api_id').val(), collection_id: $('#collection_id').val(), operation: 'redirect_facebook', object_id: object_id}
        }).done(function (result) {
            json = jQuery.parseJSON(result);
            window.open(json.redirect, '_blank');
            // window.location = json.redirect;
        });
    }

    function show_rankings(object_id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/ranking/ranking_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'list_ranking_object', object_id: object_id}
        }).done(function (result) {
            $('#rankings_' + object_id).html(result);
            $('#show_rankings_' + object_id).hide();
            $('#rankings_' + object_id).show();
        });
    }

    function show_value_ordenation(object_id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/ranking/ranking_controller.php",
            data: {collection_id: $('#collection_id').val(), ordenation_id: $('#collection_single_ordenation').val(), operation: 'list_value_ordenation', object_id: object_id}
        }).done(function (result) {
            $('#rankings_' + object_id).html(result);
            $('#show_rankings_' + object_id).hide();
            $('#rankings_' + object_id).show();
        });
    }

    function check_privacity_info(id) {
        $.ajax({
            url: $('#src').val() + '/controllers/collection/collection_controller.php',
            type: 'POST',
            data: {operation: 'check_privacity', collection_id: id}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            if (elem.privacity == false)
            {
                redirect_privacity(elem.title, elem.msg, elem.url);
            }
        });
    }

    function showModalCreateCollection() {
        $('#myModal').modal('show');
    }


    /*
    * Slideshow view Mode slider
    * */

    $('.main-slide').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: true,
        fade: true,
        asNavFor: '.collection-slides'
    });

    $('.collection-slides').slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        asNavFor: '.main-slide',
        dots: true,
        centerMode: true,
        arrows: true,
        adaptiveHeight: true,
        autoplay: true,
        focusOnSelect: true
    });
</script>
