<?php ?>
<script>
    $(function () {
        set_containers_class($('#collection_id').val());
        // *************** Iframe Popover Collection ****************
        //$('#iframebutton').attr('data-content', 'Teste').data('bs.popover').setContent();
        $('[data-toggle="popover"]').popover();
        // var myPopover = $('#iframebutton').data('popover');
        // $('#iframebutton').popover('hide');
        // myPopover.options.html = true;
        //<iframe width="560" height="315" src="https://www.youtube.com/embed/CGyEd0aKWZE" frameborder="0" allowfullscreen></iframe>
        // myPopover.options.content = '<form><input type="text" style="width:200px;" value="<iframe width=\'800\' height=\'600\' src=\'' + $("#socialdb_permalink_collection").val() + '\' frameborder=\'0\'></iframe>" /></form>';
        if ($('#is_filter').val() == '1') {
            $('#form').hide();
            $('#list').hide();
            $('#loader_objects').show();
            $.ajax({
                type: "POST",
                url: $('#src').val() + "/controllers/wp_query/wp_query_controller.php",
                data: {operation: 'filter', wp_query_args: '<?php echo serialize($_GET) ?>', collection_id: $('#collection_id').val()}
            }).done(function (result) {
                elem = jQuery.parseJSON(result);
                $('#loader_objects').hide();
                $('#list').html(elem.page);
                $('#wp_query_args').val(elem.args);
                $('#list').show();
                set_popover_content($("#socialdb_permalink_collection").val() + '?' + elem.url + '&is_filter=1');
                show_filters($('#collection_id').val(), elem.args);
                if (elem.empty_collection) {
                    $('#collection_empty').show();
                    $('#items_not_found').hide();
                }
            });
        }
    });
/**************************** Comentarios **************************************************/
function list_comments_general(){
    if($('#socialdb_event_comment_term_id').val()=='collection'){
       list_comments_term('comments_term','collection'); 
    }else if($('#socialdb_event_comment_term_id').val()==''){
         list_comments($('#single_object_id').val());
    }else{
       list_comments_term('comments_term',$('#socialdb_event_comment_term_id').val()); 
    }
}


function submit_comment(object_id) {
        if ($('#comment').val().trim() === '') {
            showAlertGeneral('<?php _e('Attention!', 'tainacan') ?>', '<?php _e('Fill your comment', 'tainacan') ?>', 'info');
        } else {
            show_modal_main();
            $.ajax({
                type: "POST",
                url: $('#src').val() + "/controllers/event/event_controller.php",
                data: {
                    operation: 'add_event_comment_create',
                    socialdb_event_create_date: '<?php echo mktime() ?>',
                    socialdb_event_user_id: $('#current_user_id').val(),
                    socialdb_event_comment_create_object_id: object_id,
                    socialdb_event_comment_create_content: $('#comment').val(),
                    socialdb_event_comment_author_name: $('#author').val(),
                    socialdb_event_comment_author_email: $('#email').val(),
                    socialdb_event_comment_author_website: $('#url').val(),
                    socialdb_event_comment_term_id: $('#socialdb_event_comment_term_id').val(),
                    socialdb_event_comment_parent: 0,
                    socialdb_event_collection_id: $('#collection_id').val()}
            }).done(function (result) {
                hide_modal_main();
                elem_first = jQuery.parseJSON(result);
                showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
                list_comments_general();
                $('.dropdown-toggle').dropdown();
                $('.nav-tabs').tab();
            });
        }
    }
    // submissao da resposta a um comentario
    function submit_comment_reply(object_id) {
        if ($('#comment_msg_reply').val().trim() === '') {
            showAlertGeneral('<?php _e('Attention!', 'tainacan') ?>', '<?php _e('Fill your comment', 'tainacan') ?>', 'info');
        } else {
            $.ajax({
                type: "POST",
                url: $('#src').val() + "/controllers/event/event_controller.php",
                data: {
                    operation: 'add_event_comment_create',
                    socialdb_event_create_date: '<?php echo mktime() ?>',
                    socialdb_event_user_id: $('#current_user_id').val(),
                    socialdb_event_comment_create_object_id: object_id,
                    socialdb_event_comment_create_content: $('#comment_msg_reply').val(),
                    socialdb_event_comment_author_name: $('#author_reply').val(),
                    socialdb_event_comment_author_email: $('#email_reply').val(),
                    socialdb_event_comment_author_website: $('#url_reply').val(),
                    socialdb_event_comment_term_id: $('#edit_socialdb_event_comment_term_id').val(),
                    socialdb_event_comment_parent: $('#comment_id').val(),
                    socialdb_event_collection_id: $('#collection_id').val()
                }
            }).done(function (result) {

                list_comments_general();
                $('.dropdown-toggle').dropdown();
                $('.nav-tabs').tab();
                $('#modalReplyComment').modal("hide");
                elem_first = jQuery.parseJSON(result);
                showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
                $('html, body').animate({
                    scrollTop: $("#comments").offset().top
                }, 2000);
            });
        }
    }
    // mostra modal de resposta
    function showModalReply(comment_parent_id) {
        console.log($('#modalReplyComment'));
        $('#comment_id').val(comment_parent_id);
        $('#modalReplyComment').modal("show");
    }
    // mostrar modal de reportar abuso
    function showModalReportAbuseComment(comment_parent_id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/comment/comment_controller.php",
            data: {
                operation: 'get_comment_json',
                comment_id: comment_parent_id
            }
        }).done(function (result) {
            var comment = jQuery.parseJSON(result);
            $('#comment_id_report').val(comment_parent_id);
            $('#description_comment_abusive').html(comment.comment.comment_content);
            $('#showModalReportAbuseComment').modal("show");
        });
    }
    // mostrar edicao
    function showEditComment(comment_id) {
        show_modal_main();
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/comment/comment_controller.php",
            data: {
                operation: 'get_comment_json',
                comment_id: comment_id
            }
        }).done(function (result) {
            hide_modal_main();
            var comment = jQuery.parseJSON(result);
            $('#comment_text_' + comment_id).hide("slow");
            $('#edit_field_value_' + comment_id).val(comment.comment.comment_content);
            $('#comment_edit_field_' + comment_id).show("slow");
        });
    }
    // cancelar edicao
    function cancelEditComment(comment_id) {
        $('#comment_edit_field_' + comment_id).hide("slow");
        $('#comment_text_' + comment_id).show("slow");
    }
    // disparado quando eh dono ou admin   
    function showAlertDeleteComment(comment_id, title, text, time) {
        swal({
            title: title,
            text: text,
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: 'btn-danger',
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {
                  show_modal_main();
                $.ajax({
                    type: "POST",
                    url: $('#src').val() + "/controllers/event/event_controller.php",
                    data: {
                        operation: 'add_event_comment_delete',
                        socialdb_event_create_date: time,
                        socialdb_event_user_id: $('#current_user_id').val(),
                        socialdb_event_comment_delete_id: comment_id,
                        socialdb_event_collection_id: $('#collection_id').val()}
                }).done(function (result) {
                    hide_modal_main();
                    list_comments_general();
                    elem_first = jQuery.parseJSON(result);
                    showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);

                });
            }
        });
    }
    // formulario de reportar abuso para demais usuarios
    function submit_report_abuse() {
        show_modal_main();
        if ($('#comment_msg_report').val().trim() === '') {
            showAlertGeneral('<?php _e('Attention!', 'tainacan') ?>', '<?php _e('Fill all fields', 'tainacan') ?>', 'info');
        } else {
            $('#showModalReportAbuseComment').modal("hide");
            $.ajax({
                type: "POST",
                url: $('#src').val() + "/controllers/event/event_controller.php",
                data: {
                    operation: 'add_event_comment_delete',
                    socialdb_event_create_date: '<?php echo mktime() ?>',
                    socialdb_event_observation: $('#comment_msg_report').val(),
                    socialdb_event_user_id: $('#current_user_id').val(),
                    socialdb_event_comment_delete_id: $('#comment_id_report').val(),
                    socialdb_event_collection_id: $('#collection_id').val()}
            }).done(function (result) {
            hide_modal_main();
                 list_comments_general();
                elem_first = jQuery.parseJSON(result);
                showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
            });
        }
    }
    // submissao do formulario de edicao
    function submitEditComment(comment_id) {
        if ($('#edit_field_value_' + comment_id).val().trim() === '') {
            showAlertGeneral('<?php _e('Attention!', 'tainacan') ?>', '<?php _e('Fill your comment', 'tainacan') ?>', 'info');
        } else {
            show_modal_main();
            $.ajax({
                type: "POST",
                url: $('#src').val() + "/controllers/event/event_controller.php",
                data: {
                    operation: 'add_event_comment_edit',
                    socialdb_event_create_date: '<?php echo mktime() ?>',
                    socialdb_event_user_id: $('#current_user_id').val(),
                    socialdb_event_comment_edit_id: comment_id,
                    socialdb_event_comment_edit_content: $('#edit_field_value_' + comment_id).val(),
                    socialdb_event_collection_id: $('#collection_id').val()
                }
            }).done(function (result) {
                 list_comments_general();
                hide_modal_main();
                $('.dropdown-toggle').dropdown();
                $('.nav-tabs').tab();
                elem_first = jQuery.parseJSON(result);
                showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
                $('html, body').animate({
                    scrollTop: $("#comments").offset().top
                }, 2000);
            });
        }
    }

/******************************************************************************/

    function set_popover_content(content) {
        $('[data-toggle="popover"]').popover();
        var myPopover = $('#iframebutton').data('popover');
        $('#iframebutton').popover('hide');
        if (myPopover) {
            myPopover.options.html = true;
            //<iframe width="560" height="315" src="https://www.youtube.com/embed/CGyEd0aKWZE" frameborder="0" allowfullscreen></iframe>
            myPopover.options.content = '<form><input type="text" style="width:200px;" value="<iframe width=\'800\' height=\'600\' src=\'' + content + '\' frameborder=\'0\'></iframe>" /></form>';
        }
    }


    function set_containers_class(collection_id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/collection/collection_controller.php",
            data: {operation: 'set_container_classes', collection_id: collection_id}
        }).done(function (result) {
            $('.dropdown-toggle').dropdown();
            elem = jQuery.parseJSON(result);
            if (elem.has_left && elem.has_left == 'true' && (!elem.has_right || elem.has_right !== 'true')) {
                $('#div_central').show();
                $('#div_central').removeClass('col-md-12');
                $('#div_central').addClass('col-md-9');
                $('#div_left').show();
                load_menu_left(collection_id);
            } else {
                <?php if(!has_filter('category_root_as_facet')||apply_filters('category_root_as_facet', true)): ?>
                $('#div_left').hide();
                $('#div_central').removeClass('col-md-9');
                $('#div_central').removeClass('col-md-10');
                $('#div_central').removeClass('col-md-12');
                $('#div_central').addClass('col-md-12');
                $('#div_central').show();
                $('#div_left').html('');
                <?php else: ?>
                 load_menu_left(collection_id);   
                <?php endif; ?>
                // load_menu_top(collection_id);
            }
        });
    }


    function load_menu_left(collection_id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/collection/collection_controller.php",
            data: {operation: 'load_menu_left', collection_id: collection_id}
        }).done(function (result) {
            $('.dropdown-toggle').dropdown();
            $('#div_left').html(result);
        });
    }

    function list_category_property_single(category_id) {
        if (!category_id) {
            category_id = $("#category_single_edit_id").val();
        }
        $('#modalEditCategoria').modal('hide');
        $.ajax({
            url: $('#src').val() + '/controllers/property/property_controller.php',
            type: 'POST',
            data: {operation: 'list', hide_wizard: 'true', category_id: category_id, collection_id: $("#collection_id").val()}
        }).done(function (result) {
            $("#menu_object").hide();
            $("#container_socialdb").hide('slow');
            $("#list").hide('slow');
            $("#loader_objects").hide();            
            $("#form").html(result);
            $('#form').css('background','white');
            $('#form').css('border','3px solid #E8E8E8');
            $('#form').css('margin-left','-3px');
            $('#form').css('height','2000px');
            $('#form').css('border-top','none');
            $('#form').show('slow');
            //$('#single_category_property').html(result);
            //$('#single_modal_category_property').modal('show');
        });
    }

    /**
     * verificando se o item ainda esta publicado
     * @param {type} item_id
     * @returns {undefined}
     */
    function verifyPublishedItem(item_id) {
        $.ajax({
            url: $('#src').val() + '/controllers/object/objectsingle_controller.php',
            type: 'POST',
            data: {
                operation: 'verifyPublishedItem',
                collection_id: $("#collection_id").val(),
                item_id: item_id}
        }).done(function (result) {
            json = JSON.parse(result);
            console.log(json);
            if (json.is_removed) {
                showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('This item has been removed, redirecting to collection home page! ', 'tainacan') ?>', 'error');
                window.location = json.url;
            }
        });
    }

    /**
     *  Funcao que verifica se uma acao pode ser executada 
     * @param {type} value
     * @param {type} facet_id
     * @returns ajax promisse
     */
    function verifyAction(collection_id, action, object_id) {
        return $.ajax({
            url: $('#src').val() + '/controllers/home/home_controller.php',
            type: 'POST',
            data: {
                operation: 'verifyAction',
                action: action,
                collection_id: collection_id,
                object_id: object_id}
        });
    }


    function bindContextMenuSingle(span) {
        // Add context menu to this node:
        $(span).contextMenu({menu: "myMenuSingle"}, function (action, el, pos) {
            // The event was bound to the <span> tag, but the node object
            // is stored in the parent <li> tag
            var node = $.ui.dynatree.getNode(el);
            console.log(node.data.key);
            switch (action) {
                case "add":
                    var promisse = verifyAction($('#collection_id').val(), 'socialdb_collection_permission_create_category', 0);
                    promisse.done(function (result) {
                        json = JSON.parse(result);
                        if (!json.isAllowed) {
                            showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('This action was configured as "NOT ALLOWED" by moderators!', 'tainacan') ?>', 'info');
                        } else {
                            $("#category_single_parent_name").val(node.data.title);
                            $("#category_single_parent_id").val(node.data.key);
                            $('#modalAddCategoria').modal('show');
                            $('.dropdown-toggle').dropdown();
                        }
                    });
                    break;
                case "edit":
                    var promisse = verifyAction($('#collection_id').val(), 'socialdb_collection_permission_edit_category', 0);
                    promisse.done(function (result) {
                        json = JSON.parse(result);
                        if (!json.isAllowed) {
                            showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('This action was configured as "NOT ALLOWED" by moderators!', 'tainacan') ?>', 'info');
                        } else {
                             
                            //$("#category_single_parent_name_edit").val(node.data.title);
                            //$("#category_single_parent_id_edit").val(node.data.key);
                            $("#category_single_edit_name").val(node.data.title);
                            $("#socialdb_event_previous_name").val(node.data.title);
                            $("#category_edit_description").val('');
                            $("#category_single_edit_id").val(node.data.key);
                            $('#modalEditCategoria').modal('show');
                            //                $("#operation").val('update');
                            $('.dropdown-toggle').dropdown();
                            $.ajax({
                                type: "POST",
                                url: $('#src').val() + "/controllers/category/category_controller.php",
                                data: {category_id: node.data.key, operation: 'get_parent'}
                            }).done(function (result) {
                                elem = jQuery.parseJSON(result);
                                $("#category_single_edit_name").val(elem.child_name);
                                if (elem.name) {
                                    $("#category_single_parent_name_edit").val(elem.name);
                                    $("#category_single_parent_id_edit").val(elem.term_id);
                                    $("#socialdb_event_previous_parent").val(elem.term_id);
                                } else {
                                    $("#category_single_parent_name_edit").val('Categoria raiz');
                                }
                                //$("#show_category_property").show();
                                $('.dropdown-toggle').dropdown();
                            });
                            // metas
                            $.ajax({
                                type: "POST",
                                url: $('#src').val() + "/controllers/category/category_controller.php",
                                data: {category_id: node.data.key, operation: 'get_metas'}
                            }).done(function (result) {
                                elem = jQuery.parseJSON(result);
                                // console.log(elem);
                                if (elem.term.description) {
                                    $("#category_edit_description").val(elem.term.description);
                                }
                                <?php do_action('javascript_metas_category') ?>
                                //if (elem.socialdb_category_permission) {
                                //  $("#category_permission").val(elem.socialdb_category_permission);
                                //}
//                                if (elem.socialdb_category_moderators) {
//                                    $("#chosen-selected2-user").html('');
//                                    $.each(elem.socialdb_category_moderators, function (idx, user) {
//                                        if (user && user !== false) {
//                                            $("#chosen-selected2-user").append("<option class='selected' value='" + user.id + "' selected='selected' >" + user.name + "</option>");
//                                        }
//                                    });
//                                }
                                //set_fields_archive_mode(elem);
                                $('.dropdown-toggle').dropdown();
                            });
                        }
                    });
                    break;
                case "delete":
                    var promisse = verifyAction($('#collection_id').val(), 'socialdb_collection_permission_delete_category', 0);
                    promisse.done(function (result) {
                        json = JSON.parse(result);
                        if (!json.isAllowed) {
                            showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('This action was configured as "NOT ALLOWED" by moderators!', 'tainacan') ?>', 'info');
                        } else {
                            $("#category_single_delete_id").val(node.data.key);
                            $("#delete_category_single_name").text(node.data.title);
                            $('#modalExcluirCategoria').modal('show');
                            $('.dropdown-toggle').dropdown();
                        }
                    });
                    break;
                case 'metadata':
                    list_category_property_single(node.data.key);
                    break;
                default:
                    alert("Todo: appply action '" + action + "' to node " + node);
            }
        });
    }

    /**
     * 
     funcao que mostra o menu de acoes do dynatree para tags
     * @param {type} value
     * @param {type} facet_id
     * @returns {undefined}     */
    function bindContextMenuSingleTag(span) {
        // Add context menu to this node:
        $(span).contextMenu({menu: "myMenuSingleTag"}, function (action, el, pos) {
            // The event was bound to the <span> tag, but the node object
            // is stored in the parent <li> tag
            var node = $.ui.dynatree.getNode(el);
            console.log(node.data.key);
            switch (action) {
                case "add":
                    var promisse = verifyAction($('#collection_id').val(), 'socialdb_collection_permission_create_tags', 0);
                    promisse.done(function (result) {
                        json = JSON.parse(result);
                        if (!json.isAllowed) {
                            showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('This action was configured as "NOT ALLOWED" by moderators!', 'tainacan') ?>', 'info');
                        } else {
                            $('#modalAdicionarTag').modal('show');
                            $('.dropdown-toggle').dropdown();
                        }
                    });
                    break;
                case "edit":
                    var promisse = verifyAction($('#collection_id').val(), 'socialdb_collection_permission_edit_tags', 0);
                    promisse.done(function (result) {
                        json = JSON.parse(result);
                        if (!json.isAllowed) {
                            showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('This action was configured as "NOT ALLOWED" by moderators!', 'tainacan') ?>', 'info');
                        } else {
                            $("#tag_single_edit_name").val(node.data.title);
                            $("#tag_single_edit_id").val(node.data.key);
                            $("#tag_edit_description").val('');
                            $('#modalEditTag').modal('show');
                            $("#operation").val('update');
                            $.ajax({
                                type: "POST",
                                url: $('#src').val() + "/controllers/tag/tag_controller.php",
                                data: {tag_id: node.data.key, operation: 'get_tag'}
                            }).done(function (result) {
                                elem = jQuery.parseJSON(result);
                                // console.log(elem);
                                if (elem.term.description) {
                                    $("#tag_edit_description").val(elem.term.description);
                                }
                                $('.dropdown-toggle').dropdown();
                            });
                            $('.dropdown-toggle').dropdown();
                        }
                    });
                    break;
                case "delete":
                    var promisse = verifyAction($('#collection_id').val(), 'socialdb_collection_permission_delete_tags', 0);
                    promisse.done(function (result) {
                        json = JSON.parse(result);
                        if (!json.isAllowed) {
                            showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('This action was configured as "NOT ALLOWED" by moderators!', 'tainacan') ?>', 'info');
                        } else {
                            $("#delete_tag_single_name").text(node.data.title);
                            $("#tag_single_delete_id").val(node.data.key);
                            $('#modalExcluirTag').modal('show');
                            $('.dropdown-toggle').dropdown();
                        }
                    });
                    break;
                default:
                    alert("Todo: appply action '" + action + "' to node " + node);
            }
        });
    }

    /*
     *
     * TODO: refactor code
     * */
    //wp query functions #######################################################
    // faz as filtragens de links externos e retorna para a pagina de listagem
    function wpquery_link_filter(value, facet_id) {
        $('#display_view_main_page').show();
        $('#collection_post').show();
        $('#configuration').hide().html('');
        $('#main_part').show('slow');
        var stateObj = {foo: "bar"};
        history.replaceState(stateObj, "page 2", '?');
        $('#list').hide();
        $('#loader_objects').show();
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/wp_query/wp_query_controller.php",
            data: {operation: 'wpquery_link', facet_id: facet_id, wp_query_args: $('#wp_query_args').val(), value: value, collection_id: $('#collection_id').val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            $('#loader_objects').hide();
            $('#list').html(elem.page);
            $('#wp_query_args').val(elem.args);
            set_popover_content($("#socialdb_permalink_collection").val() + '?' + elem.url + '&is_filter=1');
            show_filters($('#collection_id').val(), elem.args);
            $('#list').show();
            if (elem.empty_collection) {
                $('#collection_empty').show();
                $('#items_not_found').hide();
            }
            setMenuContainerHeight();
        });
    }
    // faz as filtragens de links externos e retorna para a pagina de listagem PARA termos
    function wpquery_term_filter(value, facet_id) {
        $('#display_view_main_page').show();
        $('#collection_post').show();
        $('#configuration').hide();
        $('#configuration').html('');
        $('#main_part').show('slow');
        var stateObj = {foo: "bar"};
        history.replaceState(stateObj, "page 2", '?');
        $('#list').hide();
        $('#loader_objects').show();
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/wp_query/wp_query_controller.php",
            data: {operation: 'wpquery_radio', facet_id: facet_id, wp_query_args: $('#wp_query_args').val(), value: value, collection_id: $('#collection_id').val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            $('#loader_objects').hide();
            $('#list').html(elem.page);
            $('#wp_query_args').val(elem.args);
            set_popover_content($("#socialdb_permalink_collection").val() + '?' + elem.url + '&is_filter=1');
            show_filters($('#collection_id').val(), elem.args);
            $('#list').show();
            if (elem.empty_collection) {
                $('#collection_empty').show();
                $('#items_not_found').hide();
            }
            setMenuContainerHeight();
        });
    }

    function wpquery_filter_by_facet(value, facet_id, operation) {
        $("#list").hide();
        $('#loader_objects').show();
        var facet_id = facet_id || "";
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/wp_query/wp_query_controller.php",
            data: {operation: operation, value: value, facet_id: facet_id, wp_query_args: $('#wp_query_args').val(), collection_id: $('#collection_id').val()}
        }).done(function (result) {
            var elem = $.parseJSON(result);
            $('#loader_objects').hide();
            $('#wp_query_args').val(elem.args);
            set_popover_content($("#socialdb_permalink_collection").val() + '?' + elem.url + '&is_filter=1');
            show_filters($('#collection_id').val(), elem.args);
            // $('.clear-top-search').fadeIn();
            $('#list').html(elem.page).show();
            if (elem.empty_collection) {
                $('#collection_empty').show();
                $('#items_not_found').hide();
            }

            setMenuContainerHeight();
        });
    }

    function wpquery_select(seletor, facet_id) {
        $('#list').hide();
        $('#loader_objects').show();
        var value = $(seletor).val();
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/wp_query/wp_query_controller.php",
            data: {operation: 'wpquery_select', facet_id: facet_id, wp_query_args: $('#wp_query_args').val(), value: value, collection_id: $('#collection_id').val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            $('#loader_objects').hide();
            $('#list').html(elem.page);
            $('#wp_query_args').val(elem.args);
            set_popover_content($("#socialdb_permalink_collection").val() + '?' + elem.url + '&is_filter=1');
            show_filters($('#collection_id').val(), elem.args);
            $('#list').show();
            if (elem.empty_collection) {
                $('#collection_empty').show();
                $('#items_not_found').hide();
            }
            setMenuContainerHeight();
        });
    }

    function wpquery_checkbox(seletor, facet_id) {
        $('#list').hide();
        $('#loader_objects').show();
        var value = $('input:checkbox:checked#' + seletor).map(function () {
            return this.value;
        }).get().join(",");
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/wp_query/wp_query_controller.php",
            data: {operation: 'wpquery_checkbox', facet_id: facet_id, wp_query_args: $('#wp_query_args').val(), value: value, collection_id: $('#collection_id').val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            $('#loader_objects').hide();
            $('#list').html(elem.page);
            $('#wp_query_args').val(elem.args);
            set_popover_content($("#socialdb_permalink_collection").val() + '?' + elem.url + '&is_filter=1');
            show_filters($('#collection_id').val(), elem.args);
            $('#list').show();
            if (elem.empty_collection) {
                $('#collection_empty').show();
                $('#items_not_found').hide();
            }
            setMenuContainerHeight();
        });
    }

    function wpquery_multipleselect(facet_id, seletor) {
        $('#list').hide();
        var value = '';
        $('#loader_objects').show();
        if (!$('#' + seletor)) {
            value = '';
        } else {
            if ($('#' + seletor).val()) {
                value = $('#' + seletor).val().join(",");
            } else {
                value = '';
            }
        }
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/wp_query/wp_query_controller.php",
            data: {operation: 'wpquery_multipleselect', facet_id: facet_id, wp_query_args: $('#wp_query_args').val(), value: value, collection_id: $('#collection_id').val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            $('#loader_objects').hide();
            $('#list').html(elem.page);
            $('#wp_query_args').val(elem.args);
            set_popover_content($("#socialdb_permalink_collection").val() + '?' + elem.url + '&is_filter=1');
            show_filters($('#collection_id').val(), elem.args);
            $('#list').show();
            if (elem.empty_collection) {
                $('#collection_empty').show();
                $('#items_not_found').hide();
            }
            setMenuContainerHeight();
        });
    }

    function wpquery_range(facet_id, facet_type, value1, value2) {
        $('#list').hide();
        $('#loader_objects').show();
        var value = value1 + ',' + value2;
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/wp_query/wp_query_controller.php",
            data: {operation: 'wpquery_range', facet_id: facet_id, facet_type: facet_type, wp_query_args: $('#wp_query_args').val(), value: value, collection_id: $('#collection_id').val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            $('#loader_objects').hide();
            $('#list').html(elem.page);
            $('#wp_query_args').val(elem.args);
            set_popover_content($("#socialdb_permalink_collection").val() + '?' + elem.url + '&is_filter=1');
            show_filters($('#collection_id').val(), elem.args);
            $('#list').show();
            if (elem.empty_collection) {
                $('#collection_empty').show();
                $('#items_not_found').hide();
            }
            setMenuContainerHeight();
        });
    }

    function wpquery_fromto(facet_id, facet_type) {

        if ($('#facet_' + facet_id + '_1').val() !== '' && $('#facet_' + facet_id + '_2').val() !== '') {
            $('#list').hide();
            $('#loader_objects').show();
            var value = $('#facet_' + facet_id + '_1').val() + ',' + $('#facet_' + facet_id + '_2').val();
            $.ajax({
                type: "POST",
                url: $('#src').val() + "/controllers/wp_query/wp_query_controller.php",
                data: {operation: 'wpquery_fromto', facet_id: facet_id, facet_type: facet_type, wp_query_args: $('#wp_query_args').val(), value: value, collection_id: $('#collection_id').val()}
            }).done(function (result) {
                elem = jQuery.parseJSON(result);
                $('#loader_objects').hide();
                $('#list').html(elem.page);
                $('#wp_query_args').val(elem.args);
                set_popover_content($("#socialdb_permalink_collection").val() + '?' + elem.url + '&is_filter=1');
                show_filters($('#collection_id').val(), elem.args);
                $('#list').show();
                if (elem.empty_collection) {
                    $('#collection_empty').show();
                    $('#items_not_found').hide();
                }
                setMenuContainerHeight();
            });
        }
    }

    function wpquery_ordenation(value) {
        $('#list').hide();
        $('#loader_objects').show();
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/wp_query/wp_query_controller.php",
            data: {operation: 'wpquery_ordenation', wp_query_args: $('#wp_query_args').val(), value: value, collection_id: $('#collection_id').val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            $('#loader_objects').hide();
            $('#list').html(elem.page);
            $('#wp_query_args').val(elem.args);
            $('#list').show();
            if (elem.empty_collection) {
                $('#collection_empty').show();
                $('#items_not_found').hide();
            }
            setMenuContainerHeight();
        });
    }

    function wpquery_orderBy(value) {
        $('#list').hide();
        $('#loader_objects').show();
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/wp_query/wp_query_controller.php",
            data: {operation: 'wpquery_orderby', wp_query_args: $('#wp_query_args').val(), value: value, collection_id: $('#collection_id').val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            $('#loader_objects').hide();
            $('#list').html(elem.page);
            $('#wp_query_args').val(elem.args);
            $('#list').show();
            if (elem.empty_collection) {
                $('#collection_empty').show();
                $('#items_not_found').hide();
            }
            setMenuContainerHeight();
        });
    }

    function wpquery_keyword(value) {
        $('#list').hide();
        $('#loader_objects').show();
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/wp_query/wp_query_controller.php",
            data: {operation: 'wpquery_keyword', wp_query_args: $('#wp_query_args').val(), value: value, collection_id: $('#collection_id').val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            $('#loader_objects').hide();
            $('#list').html(elem.page);
            $('#wp_query_args').val(elem.args);
            set_popover_content($("#socialdb_permalink_collection").val() + '?' + elem.url + '&is_filter=1');
            show_filters($('#collection_id').val(), elem.args);
            $('#list').show();
            // $('.clear-top-search').fadeIn();
            if (elem.empty_collection) {
                $('#collection_empty').show();
                $('#items_not_found').hide();
            }
            setMenuContainerHeight();
        });
    }

    function wpquery_page(value) {
        $('#list').hide();
        $('#loader_objects').show();
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/wp_query/wp_query_controller.php",
            data: {operation: 'wpquery_page', wp_query_args: $('#wp_query_args').val(), value: value, collection_id: $('#collection_id').val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            $('#loader_objects').hide();
            $('#list').html(elem.page);
            $('#wp_query_args').val(elem.args);
            $('#list').show();
            if (elem.empty_collection) {
                $('#collection_empty').show();
                $('#items_not_found').hide();
            }
            setMenuContainerHeight();
        });
    }

    function wpquery_filter() {
        $('#list').hide();
        $('#loader_objects').show();
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/wp_query/wp_query_controller.php",
            data: {operation: 'filter', wp_query_args: $('#wp_query_args').val(), collection_id: $('#collection_id').val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            $('#loader_objects').hide();
            $('#list').html(elem.page);
            $('#wp_query_args').val(elem.args);
            set_popover_content($("#socialdb_permalink_collection").val() + '?' + elem.url + '&is_filter=1');
            show_filters($('#collection_id').val(), elem.args);
            $('#list').show();
            if (elem.empty_collection) {
                $('#collection_empty').show();
                $('#items_not_found').hide();
            }
            setMenuContainerHeight();
        });
    }

    function wpquery_clean() {
        $('#list').hide();
        $('#loader_objects').show();
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/wp_query/wp_query_controller.php",
            data: {operation: 'clean', wp_query_args: $('#wp_query_args').val(), collection_id: $('#collection_id').val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            $('#loader_objects').hide();
            $('#list').html(elem.page);
            $('#wp_query_args').val(elem.args);
            //console.log(elem.listed_by_value);
            $('#collection_single_ordenation').val(elem.listed_by_value);
            set_popover_content($("#socialdb_permalink_collection").val() + '?' + elem.url + '&is_filter=1');
            show_filters($('#collection_id').val(), elem.args);
            if (elem.empty_collection) {
                $('#collection_empty').show();
                $('#items_not_found').hide();
            }
            $('#list').show();
            setMenuContainerHeight();
        });
    }

    function wpquery_remove(index_array, type, value) {
        $('#list').hide();
        $('#loader_objects').show();
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/wp_query/wp_query_controller.php",
            data: {
                index_array: index_array,
                type: type,
                value: value,
                operation: 'remove',
                wp_query_args: $('#wp_query_args').val(),
                collection_id: $('#collection_id').val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            $('#loader_objects').hide();
            $('#list').html(elem.page);
            $('#wp_query_args').val(elem.args);
            set_popover_content($("#socialdb_permalink_collection").val() + '?' + elem.url + '&is_filter=1');
            show_filters($('#collection_id').val(), elem.args);
            if (elem.empty_collection) {
                $('#collection_empty').show();
                $('#items_not_found').hide();
            }
            var result_set = $('.search-resultset').find('a').length;
            if (result_set > 0) {
                $("button#clear").fadeIn();
            }
            $('#flag_dynatree_ajax').val('true');
            $('#list').show();
            setMenuContainerHeight();
        });
    }

    // funcao que captura a action on change no selectbox na pagina single.php
    function getOrder(value) {
        wpquery_ordenation($(value).val());
        //list_all_objects(selKeys.join(", "), $("#collection_id").val(), $(value).val());
    }

// funcao que captura a action on change no selectbox na pagina single.php
    function desc_ordenation() {
        wpquery_orderBy('desc');
        //  var selKeys = $.map($("#dynatree").dynatree("getSelectedNodes"), function (node) {
        //     return node.data.key;
        // });
        //list_all_objects(selKeys.join(", "), $("#collection_id").val(), $('#collection_single_ordenation').val(), 'desc');
    }
// funcao que captura a action on change no selectbox na pagina single.php
    function asc_ordenation() {
        wpquery_orderBy('asc');
        // var selKeys = $.map($("#dynatree").dynatree("getSelectedNodes"), function (node) {
        //    return node.data.key;
        // });
        //  list_all_objects(selKeys.join(", "), $("#collection_id").val(), $('#collection_single_ordenation').val(), 'asc');
    }
    function search_objects(e) {
        // $("button#clear").show();
        var search_for = $(e).val();
        wpquery_keyword(search_for);
        // list_all_objects(selKeys.join(", "), $("#collection_id").val(), $('#collection_single_ordenation').val(), '', search_for);
    }

    function backToMainPage() {
        //wpquery_filter();

        var showing_breadcrumbs = $("#tainacan-breadcrumbs").attr('style');
        if ( $('#tainacan-breadcrumbs').is(':visible') ) {
            $("#tainacan-breadcrumbs").hide();
        }

        wpquery_clean();
        list_main_ordenation_filter();
        $("#category_page").val('');
        $("#property_page").val('');
        $('#display_view_main_page').show();
        $('#collection_post').show();
        $('#configuration').hide().html('');
        $('#form').hide().html('');
        $('#create_button').show();
        $('#menu_object').show();
        $("#list").show();
        $("#container_socialdb").show('fast');
        $('#main_part').show('slow');
        set_containers_class($('#collection_id').val());
    }

    //apenas para a pagina de demonstracao do item
    function backToMainPageSingleItem() {
        wpquery_filter();
        set_containers_class($('#collection_id').val());
        list_main_ordenation_filter();
        $('#display_view_main_page').show();
        $('#collection_post').show();
        $('#configuration').hide().html('');
        $('#main_part').show('slow');
        var stateObj = {foo: "bar"};
        history.replaceState(stateObj, "page 2", '?');
        //set_containers_class($('#collection_id').val());
    }

    // volta a listagem e limpa as url
    function back_and_clean_url() {
         $("#category_page").val('');
        $("#property_page").val('');
        $('#form').hide();
        $('#create_button').show();
        $('#menu_object').show();
        $("#list").show();
        $("#container_socialdb").show('fast');
        var stateObj = {foo: "bar"};
        history.replaceState(stateObj, "page 2", '?');
    }

    function show_filters(collection_id, filters) {
        $.ajax({
            url: $('#src').val() + '/controllers/collection/collection_controller.php',
            type: 'POST',
            data: {
                operation: 'show_filters',
                collection_id: collection_id,
                filters: filters
            }
        }).done(function (result) {
            $('#filters_collection').html(result);
            $('.remove-link-filters').show();
            var result_set = $('.search-resultset').find('a').length;
            if (result_set > 0) {
                $("button#clear").fadeIn();
            } else {
                $("button#clear").fadeOut('fast');
            }
        });
    }

    //***************************************** BEGIN SOCIAL NETWORK IMPORT *********************************************//

//    function import_youtube_video_url() {
//        var youtube_video_url = $('#youtube_video_url').val().trim();
//        var collectionId = $('#collection_id').val();
//
//        if (youtube_video_url) {
//            $('#modalImportMain').modal('show');
//            var src = $('#src').val();
//
//            $.ajax({
//                url: src + '/controllers/social_network/youtube_controller.php',
//                type: 'POST',
//                data: {operation: 'import_video_url',
//                    video_url: youtube_video_url,
//                    collectionId: collectionId},
//                success: function (response) {
//                    $('#modalImportMain').modal('hide');
//                    if (response) {
//                        showAlertGeneral('<?php _e('Success', 'tainacan'); ?>', '<?php _e('Video imported successfully', 'tainacan'); ?>', 'success');
//                        set_containers_class(collectionId);
//                        wpquery_clean();
//                    } else {
//                        showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Invalid URL or Video already inserted.', 'tainacan'); ?>', 'error');
//                    }
//                }
//            });
//            $('#youtube_video_url').val('');
//            $('#modalshowModalImportSocialNetwork').modal('hide');
//        } else {
//            showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Necessary to inform Youtube video url', 'tainacan'); ?>', 'error');
//        }
//    }

//    function import_youtube_channel() {
//        var inputIdentifierYoutube = $('#youtube_identifier_input').val().trim();
//        var inputPlaylistYoutube = $('#youtube_playlist_identifier_input').val().trim();
//        var collectionId = $('#collection_id').val();
//
//        if (inputIdentifierYoutube) {
//            $('#modalImportMain').modal('show');
//            var src = $('#src').val();
//
//            //ajax
//            $.ajax({
//                url: src + '/controllers/social_network/youtube_controller.php',
//                type: 'POST',
//                data: {operation: 'import_video_channel',
//                    identifier: inputIdentifierYoutube,
//                    playlist: inputPlaylistYoutube,
//                    collectionId: collectionId},
//                success: function (response) {
//                    $('#modalImportMain').modal('hide');
//                    var json = JSON.parse(response);
//                    if (json.length > 0) {
//                        showViewMultipleItemsSocialNetwork(json);
//                        //showAlertGeneral('<?php _e('Success', 'tainacan'); ?>', '<?php _e('OK', 'tainacan'); ?>', 'success');
//                        //wpquery_clean();
//                    }
//                    else {
//                        showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Invalid Channel/Playlist or no videos to be imported', 'tainacan'); ?>', 'error');
//                    }
//                }
//            });
//            //end ajax
//
//            $('#youtube_identifier_input').val('');
//            $('#youtube_playlist_identifier_input').val('');
//            $('#modalshowModalImportSocialNetwork').modal('hide');
//        } else {
//            showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Necessary to inform Youtube channel identifier', 'tainacan'); ?>', 'error');
//        }
//    }

//    function import_flickr() {
//        var inputIdentifierFlickr = $('#flickr_identifier_input').val().trim();
//        var collectionId = $('#collection_id').val();
//
//        if (inputIdentifierFlickr) {
//            $('#modalImportMain').modal('show');
//            var src = $('#src').val();
//
//            $.ajax({
//                url: src + '/controllers/social_network/flickr_controller.php',
//                type: 'POST',
//                data: {operation: 'import_flickr_items',
//                    identifier: inputIdentifierFlickr,
//                    collectionId: collectionId},
//                success: function (response) {
//                    //se a gravação no banco foi realizado, a tabela é incrementada
//                    $('#modalImportMain').modal('hide');
//                    var json = JSON.parse(response);
//                    if (json.length > 0) {
//                        showViewMultipleItemsSocialNetwork(json);
//                        //showAlertGeneral('<?php _e('Success', 'tainacan'); ?>', '<?php _e('OK', 'tainacan'); ?>', 'success');
//                        //wpquery_clean();
//                    }
//                    else {
//                        showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Invalid Flickr identifier or no items to be imported', 'tainacan'); ?>', 'error');
//                    }
//                }
//            });
//            $('#flickr_identifier_input').val('');
//            $('#modalshowModalImportSocialNetwork').modal('hide');
//        }
//        else {
//            showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Necessary to inform Flickr identifier', 'tainacan'); ?>', 'error');
//            $('#flickr_identifier_input').val('');
//        }
//    }

//    function import_instagram() {
//        var inputIdentifierInstagram = $('#instagram_identifier_input').val().trim();
//        var collection_id = $('#collection_id').val();
//
//        if (inputIdentifierInstagram) {
//            $('#modalImportMain').modal('show');
//            var src = $('#src').val();
//
//            window.location = src + "/controllers/social_network/instagram_controller.php?collection_id=" + collection_id + "&operation=getPhotosInstagram&identifier=" + inputIdentifierInstagram;
//
//            $('#instagram_identifier_input').val('');
//            $('#modalshowModalImportSocialNetwork').modal('hide');
//        }
//        else {
//            showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Necessary to inform Instagram identifier', 'tainacan'); ?>', 'error');
//        }
//    }

//    function import_vimeo() {
//        var inputIdentifierVimeo = $('#vimeo_identifier_input').val().trim();
//        var collectionId = $('#collection_id').val();
//
//        if (inputIdentifierVimeo) {
//            $('#modalImportMain').modal('show');
//            var src = $('#src').val();
//
//            $.ajax({
//                url: src + '/controllers/social_network/vimeo_controller.php',
//                type: 'POST',
//                data: {operation: 'import_vimeo_items',
//                    identifier: inputIdentifierVimeo,
//                    import_type: $('input[name="optradio_vimeo"]:checked').val(),
//                    collectionId: collectionId},
//                success: function (response) {
//                    //se a gravação no banco foi realizado, a tabela é incrementada
//                    $('#modalImportMain').modal('hide');
//                    var json = JSON.parse(response);
//                    if (json.length > 0) {
//                        showViewMultipleItemsSocialNetwork(json);
//                        //showAlertGeneral('<?php _e('Success', 'tainacan'); ?>', '<?php _e('OK', 'tainacan'); ?>', 'success');
//                        //wpquery_clean();
//                    }
//                    else {
//                        showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Invalid Vimeo identifier or no items to be imported', 'tainacan'); ?>', 'error');
//                    }
//                }
//            });
//            $('#vimeo_identifier_input').val('');
//            $('#modalshowModalImportSocialNetwork').modal('hide');
//        }
//        else {
//            showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Necessary to inform Vimeo identifier', 'tainacan'); ?>', 'error');
//            $('#vimeo_identifier_input').val('');
//        }
//    }

    //--------------------------- TELA DE IMPORTACAO DE MULTIPLO ARQUIVOS --------------------------
    function showViewMultipleItemsSocialNetwork(imported_ids) {
        $.ajax({
            url: $('#src').val() + '/controllers/object/object_controller.php',
            type: 'POST',
            data: {
                operation: 'showViewMultipleItemsSocialNetwork',
                items_id: imported_ids,
                collection_id: $("#collection_id").val()}
        }).done(function (result) {
            $('#main_part').hide();
            $('#display_view_main_page').hide();
            $('#loader_collections').hide();
            $('#collection_post').hide();
            $('#configuration').html(result);
            $('#configuration').slideDown();
        });
    }

    //***************************************** END SOCIAL NETWORK IMPORT *********************************************//


    //***************************************** BEGIN IMPORT ALL *********************************************//

    function importAll_verify() {
        var inputImportAll = $('#item_url_import_all').val().trim();

        if (inputImportAll) {
            var youtube_url = validateYouTubeUrl();
            if (youtube_url) {
                // É uma URL de um vídeo do youtube. Executar a importação do vídeo.
                console.log(youtube_url);
                import_youtube_video_url();
            } else {
                var youtube_channel_url = validateYouTubeChannelUrl();
                if (youtube_channel_url) {
                    // É uma URL de um canal do youtube. Executar a importação dos vídeos de canal.
                    var res = inputImportAll.split(youtube_channel_url[4]);
                    console.log(res[1]);
                    import_youtube_channel(res[1]);
                }
                else {
                    var youtube_playlist_url = validateYouTubePlaylistUrl();
                    if (youtube_playlist_url) {
                        // É uma URL de uma playlist do youtube. Executar a importação dos vídeos da playlist.
                        console.log(youtube_playlist_url);
                        import_youtube_playlist(youtube_playlist_url);
                    }
                    else {
                        var instagram_url = validateInstagramUrl();
                        if (instagram_url) {
                            // É uma URL do instagram. Executar a importação dos imagens e vídeos do usuario.
                            console.log(instagram_url);
                            import_instagram(instagram_url);
                        } else {
                            var vimeo_url = validateVimeoUrl();
                            if (vimeo_url) {
                                // É uma URL do vimeo. Executar a importação dos vídeos.
                                vimeo_url = vimeo_url.split("/");
                                if (vimeo_url[3].localeCompare('channels') === 0) {
                                    console.log('Canal: ' + vimeo_url[4]);
                                    import_vimeo('channels', vimeo_url[4]);
                                } else {
                                    console.log('Usuario: ' + vimeo_url[3]);
                                    import_vimeo('users', vimeo_url[3]);
                                }
                            }
                            else {
                                var flickr_url = validateFlickrUrl();
                                if (flickr_url) {
                                    // É uma URL do Flickr. Executar a importação dos itens do usuário.
                                    console.log(flickr_url);
                                    import_flickr(flickr_url);
                                }
                                else {
                                    var facebook_url = validateFacebookUrl();
                                    if (facebook_url) {
                                        // É uma URL do Facebook. Executar a importação dos itens do usuário.
                                        console.log(facebook_url);
                                    }
                                    else {
                                        var any_file_type = validateAnyFile();
                                        if (any_file_type) {
                                            // É uma URL de um arquivo. Executar a importação deste arquivo.
                                            console.log(any_file_type);
                                            showFormCreateURLFile($('#item_url_import_all').val(), any_file_type);
                                            $('#item_url_import_all').val('');
                                            $("#files_import_icon").addClass("grayscale");
                                            $('#modalshowModalImportAll').modal('hide');
                                        } else {
                                            var any_url = validateAnyUrl();
                                            if (any_url) {
                                                // É uma URL regular. Executar a importação através do Embed.ly.
                                                showFormCreateURL($('#item_url_import_all').val());
                                                $('#item_url_import_all').val('');
                                                $("#sites_import_icon").addClass("grayscale");
                                                $('#modalshowModalImportAll').modal('hide');
                                                console.log('URL Regular. Enviar pro Embed.ly.');
                                            } else {
                                                showAlertGeneral("<?php _e('Alert', 'tainacan'); ?>", "<?php _e('Please, insert a valid URL', 'tainacan'); ?>", "error");
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }


        } else {
            showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Necessary to inform something', 'tainacan'); ?>', 'error');
            $('#item_url_import_all').val('');
        }
    }

    function validateYouTubeUrl()
    {
        var url = $('#item_url_import_all').val();
        if (url != undefined || url != '') {
            var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=|\?v=)([^#\&\?]*).*/;
            var match = url.match(regExp);
            if (match && match[2].length == 11) {
                // Do anything for being valid
                // if need to change the url to embed url then use below line
                //$('#ytplayerSide').attr('src', 'https://www.youtube.com/embed/' + match[2] + '?autoplay=0');
                return match;
            }
            else {
                // Do anything for not being valid
                return false;
            }
        }
    }

    function validateYouTubeChannelUrl()
    {
        var url = $('#item_url_import_all').val();
        if (url != undefined || url != '') {
            var regExp = /((http|https):\/\/|)(www\.)?youtube\.com\/(channel\/|user\/)[a-zA-Z0-9]{1,}/;
            var match = url.match(regExp);
            if (match) {
                return match;
            }
            else {
                // Do anything for not being valid
                return false;
            }
        }
    }

    function validateYouTubePlaylistUrl()
    {
        var url = $('#item_url_import_all').val();
        if (url != undefined || url != '') {
            var regExp = /(?:(?:http|https):\/\/)?(?:www.)?(?:youtube.com|youtu.be)\/([A-Za-z0-9-_]+)/im;
            var match_youtube = url.match(regExp);
            if (match_youtube) {
                var reg = new RegExp("[&?]list=([a-z0-9_]+)", "i");
                var match = reg.exec(url);

                if (match) {
                    return match[1];
                }
                else {
                    // Do anything for not being valid
                    return false;
                }
            } else {
                return false;
            }
        }
    }

    function validateInstagramUrl()
    {
        var url = $('#item_url_import_all').val();
        if (url != undefined || url != '') {
            var regExp = /(?:(?:http|https):\/\/)?(?:www.)?(?:instagram.com|instagr.am)\/([A-Za-z0-9-_.]+)/im;
            var match = url.match(regExp);
            if (match) {
                return match[1];
            }
            else {
                // Do anything for not being valid
                return false;
            }
        }
    }

    function validateVimeoUrl()
    {
        var url = $('#item_url_import_all').val();
        if (url != undefined || url != '') {
            var regExp = /(?:(?:http|https):\/\/)?(?:www.)?(?:vimeo.com)\/([A-Za-z0-9-_]+)/im;
            var match = url.match(regExp);
            if (match) {
                return url;
            }
            else {
                // Do anything for not being valid
                return false;
            }
        }
    }

    function validateFlickrUrl()
    {
        var url = $('#item_url_import_all').val();
        if (url != undefined || url != '') {
            var regExp = /((http|https):\/\/|)(www\.)?flickr\.com\/(photos\/)[a-zA-Z0-9]{1,}/;
            var match = url.match(regExp);
            if (match) {
                var result = url.split('/');
                return result[4];
            }
            else {
                // Do anything for not being valid
                return false;
            }
        }
    }

    function validateFacebookUrl()
    {
        var url = $('#item_url_import_all').val();
        if (url != undefined || url != '') {
            var regExp = /(?:(?:http|https):\/\/)?(?:www.)?(?:facebook.com)/im;
            var match = url.match(regExp);
            if (match) {
                return match;
            }
            else {
                // Do anything for not being valid
                return false;
            }
        }
    }

    function validateAnyFile() {
        var url = $('#item_url_import_all').val();
        if (url != undefined || url != '') {
            var regExp = /(?:([^:/?#]+):)?(?:\/\/([^/?#]*))?([^?#]*\.(?:jpg|jpeg|bmp|tiff|gif|png|pdf|mp4|avi|mp3))(?:\?([^#]*))?(?:#(.*))?/i;
            var match = url.match(regExp);
            if (match && validateAnyUrl()) {
                var regExp_image = /(?:([^:/?#]+):)?(?:\/\/([^/?#]*))?([^?#]*\.(?:jpg|jpeg|bmp|tiff|gif|png))(?:\?([^#]*))?(?:#(.*))?/i;
                var match_image = url.match(regExp_image);
                if (match_image) {
                    return 'image';
                } else {
                    var regExp_video = /(?:([^:/?#]+):)?(?:\/\/([^/?#]*))?([^?#]*\.(?:mp4|avi))(?:\?([^#]*))?(?:#(.*))?/i;
                    var match_video = url.match(regExp_video);
                    if (match_video) {
                        return 'video';
                    } else {
                        var regExp_pdf = /(?:([^:/?#]+):)?(?:\/\/([^/?#]*))?([^?#]*\.(?:pdf))(?:\?([^#]*))?(?:#(.*))?/i;
                        var match_pdf = url.match(regExp_pdf);
                        if (match_pdf) {
                            return 'pdf';
                        } else {
                            var regExp_audio = /(?:([^:/?#]+):)?(?:\/\/([^/?#]*))?([^?#]*\.(?:mp3))(?:\?([^#]*))?(?:#(.*))?/i;
                            var match_audio = url.match(regExp_audio);
                            if (match_audio) {
                                return 'audio';
                            }else{
                                return 'other';
                            }
                        }
                    }
                }
            }
            else {
                // Do anything for not being valid
                return false;
            }
        }
    }

    function validateAnyUrl()
    {
        var url = $('#item_url_import_all').val();
        if (url != undefined || url != '') {
            var regExp = /(^|\s)((https?:\/\/)?[\w-]+(\.[\w-]+)+\.?(:\d+)?(\/\S*)?)/gi;
            var match = url.match(regExp);
            if (match) {
                return match;
            }
            else {
                // Do anything for not being valid
                return false;
            }
        }
    }

    function verify_import_type() {
        var url = $('#item_url_import_all').val().trim();
        if (url != undefined && url != '') {
            var youtube_url = validateYouTubeUrl();
            if (youtube_url) {
                // É uma URL de um vídeo do youtube.
                $("#btn_import_fb").css('display', 'none');
                $("#btn_import_allrest").css('display', 'block');
                $("#facebook_import_icon").addClass("grayscale");
                $("#flickr_import_icon").addClass("grayscale");
                $("#youtube_import_icon").removeClass("grayscale");
                $("#vimeo_import_icon").addClass("grayscale");
                $("#instagram_import_icon").addClass("grayscale");
                $("#files_import_icon").addClass("grayscale");
                $("#sites_import_icon").addClass("grayscale");
            } else {
                var youtube_channel_url = validateYouTubeChannelUrl();
                if (youtube_channel_url) {
                    // É uma URL de um canal do youtube.
                    $("#btn_import_fb").css('display', 'none');
                    $("#btn_import_allrest").css('display', 'block');
                    $("#facebook_import_icon").addClass("grayscale");
                    $("#flickr_import_icon").addClass("grayscale");
                    $("#youtube_import_icon").removeClass("grayscale");
                    $("#vimeo_import_icon").addClass("grayscale");
                    $("#instagram_import_icon").addClass("grayscale");
                    $("#files_import_icon").addClass("grayscale");
                    $("#sites_import_icon").addClass("grayscale");
                }
                else {
                    var youtube_playlist_url = validateYouTubePlaylistUrl();
                    if (youtube_playlist_url) {
                        // É uma URL de uma playlist do youtube.
                        $("#btn_import_fb").css('display', 'none');
                        $("#btn_import_allrest").css('display', 'block');
                        $("#facebook_import_icon").addClass("grayscale");
                        $("#flickr_import_icon").addClass("grayscale");
                        $("#youtube_import_icon").removeClass("grayscale");
                        $("#vimeo_import_icon").addClass("grayscale");
                        $("#instagram_import_icon").addClass("grayscale");
                        $("#files_import_icon").addClass("grayscale");
                        $("#sites_import_icon").addClass("grayscale");
                    }
                    else {
                        var instagram_url = validateInstagramUrl();
                        if (instagram_url) {
                            // É uma URL do instagram.
                            $("#btn_import_fb").css('display', 'none');
                            $("#btn_import_allrest").css('display', 'block');
                            $("#facebook_import_icon").addClass("grayscale");
                            $("#flickr_import_icon").addClass("grayscale");
                            $("#youtube_import_icon").addClass("grayscale");
                            $("#vimeo_import_icon").addClass("grayscale");
                            $("#instagram_import_icon").removeClass("grayscale");
                            $("#files_import_icon").addClass("grayscale");
                            $("#sites_import_icon").addClass("grayscale");
                        } else {
                            var vimeo_url = validateVimeoUrl();
                            if (vimeo_url) {
                                // É uma URL do vimeo.
                                $("#btn_import_fb").css('display', 'none');
                                $("#btn_import_allrest").css('display', 'block');
                                $("#facebook_import_icon").addClass("grayscale");
                                $("#flickr_import_icon").addClass("grayscale");
                                $("#youtube_import_icon").addClass("grayscale");
                                $("#vimeo_import_icon").removeClass("grayscale");
                                $("#instagram_import_icon").addClass("grayscale");
                                $("#files_import_icon").addClass("grayscale");
                                $("#sites_import_icon").addClass("grayscale");
                            }
                            else {
                                var flickr_url = validateFlickrUrl();
                                if (flickr_url) {
                                    // É uma URL do Flickr.
                                    $("#btn_import_fb").css('display', 'none');
                                    $("#btn_import_allrest").css('display', 'block');
                                    $("#facebook_import_icon").addClass("grayscale");
                                    $("#flickr_import_icon").removeClass("grayscale");
                                    $("#youtube_import_icon").addClass("grayscale");
                                    $("#vimeo_import_icon").addClass("grayscale");
                                    $("#instagram_import_icon").addClass("grayscale");
                                    $("#files_import_icon").addClass("grayscale");
                                    $("#sites_import_icon").addClass("grayscale");
                                }
                                else {
                                    var facebook_url = validateFacebookUrl();
                                    if (facebook_url) {
                                        $("#btn_import_fb").css('display', 'block');
                                        $("#btn_import_allrest").css('display', 'none');
                                        $("#facebook_import_icon").removeClass("grayscale");
                                        $("#flickr_import_icon").addClass("grayscale");
                                        $("#youtube_import_icon").addClass("grayscale");
                                        $("#vimeo_import_icon").addClass("grayscale");
                                        $("#instagram_import_icon").addClass("grayscale");
                                        $("#files_import_icon").addClass("grayscale");
                                        $("#sites_import_icon").addClass("grayscale");
                                    } else {
                                        var any_file_url = validateAnyFile();
                                        if (any_file_url) {
                                            $("#btn_import_fb").css('display', 'none');
                                            $("#btn_import_allrest").css('display', 'block');
                                            $("#facebook_import_icon").addClass("grayscale");
                                            $("#flickr_import_icon").addClass("grayscale");
                                            $("#youtube_import_icon").addClass("grayscale");
                                            $("#vimeo_import_icon").addClass("grayscale");
                                            $("#instagram_import_icon").addClass("grayscale");
                                            $("#files_import_icon").removeClass("grayscale");
                                            $("#sites_import_icon").addClass("grayscale");
                                        } else {
                                            var any_url = validateAnyUrl();
                                            if (any_url) {
                                                $("#btn_import_fb").css('display', 'none');
                                                $("#btn_import_allrest").css('display', 'block');
                                                $("#facebook_import_icon").addClass("grayscale");
                                                $("#flickr_import_icon").addClass("grayscale");
                                                $("#youtube_import_icon").addClass("grayscale");
                                                $("#vimeo_import_icon").addClass("grayscale");
                                                $("#instagram_import_icon").addClass("grayscale");
                                                $("#files_import_icon").addClass("grayscale");
                                                $("#sites_import_icon").removeClass("grayscale");
                                            } else {
                                                $("#btn_import_fb").css('display', 'none');
                                                $("#btn_import_allrest").css('display', 'block');
                                                $("#facebook_import_icon").addClass("grayscale");
                                                $("#flickr_import_icon").addClass("grayscale");
                                                $("#youtube_import_icon").addClass("grayscale");
                                                $("#vimeo_import_icon").addClass("grayscale");
                                                $("#instagram_import_icon").addClass("grayscale");
                                                $("#files_import_icon").addClass("grayscale");
                                                $("#sites_import_icon").addClass("grayscale");
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $("#btn_import_fb").css('display', 'none');
            $("#btn_import_allrest").css('display', 'block');
            $("#facebook_import_icon").addClass("grayscale");
            $("#flickr_import_icon").addClass("grayscale");
            $("#youtube_import_icon").addClass("grayscale");
            $("#vimeo_import_icon").addClass("grayscale");
            $("#instagram_import_icon").addClass("grayscale");
            $("#files_import_icon").addClass("grayscale");
            $("#sites_import_icon").addClass("grayscale");
        }
    }

    function import_youtube_video_url() {
        var youtube_video_url = $('#item_url_import_all').val().trim();
        var collectionId = $('#collection_id').val();

        if (youtube_video_url) {
            $('#modalImportMain').modal('show');
            var src = $('#src').val();

            $.ajax({
                url: src + '/controllers/social_network/youtube_controller.php',
                type: 'POST',
                data: {operation: 'import_video_url',
                    video_url: youtube_video_url,
                    collectionId: collectionId},
                success: function (response) {
                    $('#modalImportMain').modal('hide');
                    if (response) {
                        showAlertGeneral('<?php _e('Success', 'tainacan'); ?>', '<?php _e('Video imported successfully', 'tainacan'); ?>', 'success');
                        set_containers_class(collectionId);
                        wpquery_clean();
                    } else {
                        showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Invalid URL or Video already inserted.', 'tainacan'); ?>', 'error');
                    }
                }
            });
            $('#item_url_import_all').val('');
            $("#youtube_import_icon").addClass("grayscale");
            $('#modalshowModalImportAll').modal('hide');
        } else {
            showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Necessary to inform Youtube video url', 'tainacan'); ?>', 'error');
        }
    }

    function import_youtube_channel(inputIdentifierYoutube) {
        var collectionId = $('#collection_id').val();

        if (inputIdentifierYoutube) {
            $('#modalImportMain').modal('show');
            var src = $('#src').val();

            //ajax
            $.ajax({
                url: src + '/controllers/social_network/youtube_controller.php',
                type: 'POST',
                data: {operation: 'import_video_channel',
                    identifier: inputIdentifierYoutube,
                    //playlist: inputPlaylistYoutube,
                    playlist: '',
                    collectionId: collectionId},
                success: function (response) {
                    $('#modalImportMain').modal('hide');
                    var json = JSON.parse(response);
                    if (json.length > 0) {
                        showViewMultipleItemsSocialNetwork(json);
                        //showAlertGeneral('<?php _e('Success', 'tainacan'); ?>', '<?php _e('OK', 'tainacan'); ?>', 'success');
                        //wpquery_clean();
                    }
                    else {
                        showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Invalid Channel/Playlist or no videos to be imported', 'tainacan'); ?>', 'error');
                    }
                }
            });
            //end ajax

            $('#item_url_import_all').val('');
            $("#youtube_import_icon").addClass("grayscale");
            $('#modalshowModalImportAll').modal('hide');
        } else {
            showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Necessary to inform Youtube channel identifier', 'tainacan'); ?>', 'error');
        }
    }

    function import_youtube_playlist(inputIdentifierYoutube) {
        var collectionId = $('#collection_id').val();

        if (inputIdentifierYoutube) {
            $('#modalImportMain').modal('show');
            var src = $('#src').val();

            //ajax
            $.ajax({
                url: src + '/controllers/social_network/youtube_controller.php',
                type: 'POST',
                data: {operation: 'import_video_channel',
                    //identifier: inputIdentifierYoutube,
                    //playlist: inputPlaylistYoutube,
                    playlist: inputIdentifierYoutube,
                    collectionId: collectionId},
                success: function (response) {
                    $('#modalImportMain').modal('hide');
                    var json = JSON.parse(response);
                    if (json.length > 0) {
                        showViewMultipleItemsSocialNetwork(json);
                        //showAlertGeneral('<?php _e('Success', 'tainacan'); ?>', '<?php _e('OK', 'tainacan'); ?>', 'success');
                        //wpquery_clean();
                    }
                    else {
                        showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Invalid Channel/Playlist or no videos to be imported', 'tainacan'); ?>', 'error');
                    }
                }
            });
            //end ajax

            $('#item_url_import_all').val('');
            $("#youtube_import_icon").addClass("grayscale");
            $('#modalshowModalImportAll').modal('hide');
        } else {
            showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Necessary to inform Youtube channel identifier', 'tainacan'); ?>', 'error');
        }
    }

    function import_instagram(instagram_url) {
        var inputIdentifierInstagram = instagram_url.trim();
        var collection_id = $('#collection_id').val();

        if (inputIdentifierInstagram) {
            $('#modalImportMain').modal('show');
            var src = $('#src').val();

            window.location = src + "/controllers/social_network/instagram_controller.php?collection_id=" + collection_id + "&operation=getPhotosInstagram&identifier=" + inputIdentifierInstagram;

            $('#item_url_import_all').val('');
            $("#instagram_import_icon").addClass("grayscale");
            $('#modalshowModalImportAll').modal('hide');
        }
        else {
            showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Necessary to inform Instagram URL with user identifier', 'tainacan'); ?>', 'error');
        }
    }

    function import_flickr(flickr_url) {
        var inputIdentifierFlickr = flickr_url.trim();
        var collectionId = $('#collection_id').val();

        if (inputIdentifierFlickr) {
            $('#modalImportMain').modal('show');
            var src = $('#src').val();

            $.ajax({
                url: src + '/controllers/social_network/flickr_controller.php',
                type: 'POST',
                data: {operation: 'import_flickr_items',
                    identifier: inputIdentifierFlickr,
                    collectionId: collectionId},
                success: function (response) {
                    //se a gravação no banco foi realizado, a tabela é incrementada
                    $('#modalImportMain').modal('hide');
                    var json = JSON.parse(response);
                    if (json.length > 0) {
                        showViewMultipleItemsSocialNetwork(json);
                        //showAlertGeneral('<?php _e('Success', 'tainacan'); ?>', '<?php _e('OK', 'tainacan'); ?>', 'success');
                        //wpquery_clean();
                    }
                    else {
                        showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Invalid Flickr identifier or no items to be imported', 'tainacan'); ?>', 'error');
                    }
                }
            });
            $('#item_url_import_all').val('');
            $("#flickr_import_icon").addClass("grayscale");
            $('#modalshowModalImportAll').modal('hide');
        }
        else {
            showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Necessary to inform Flickr identifier', 'tainacan'); ?>', 'error');
            $('#item_url_import_all').val('');
        }
    }

    function import_vimeo(type, identifier) {
        var inputIdentifierVimeo = identifier.trim();
        var collectionId = $('#collection_id').val();

        if (inputIdentifierVimeo) {
            $('#modalImportMain').modal('show');
            var src = $('#src').val();

            $.ajax({
                url: src + '/controllers/social_network/vimeo_controller.php',
                type: 'POST',
                data: {operation: 'import_vimeo_items',
                    identifier: inputIdentifierVimeo,
                    import_type: type,
                    collectionId: collectionId},
                success: function (response) {
                    //se a gravação no banco foi realizado, a tabela é incrementada
                    $('#modalImportMain').modal('hide');
                    var json = JSON.parse(response);
                    if (json.length > 0) {
                        showViewMultipleItemsSocialNetwork(json);
                        //showAlertGeneral('<?php _e('Success', 'tainacan'); ?>', '<?php _e('OK', 'tainacan'); ?>', 'success');
                        //wpquery_clean();
                    }
                    else {
                        showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Invalid Vimeo identifier or no items to be imported', 'tainacan'); ?>', 'error');
                    }
                }
            });
            $('#item_url_import_all').val('');
            $("#vimeo_import_icon").addClass("grayscale");
            $('#modalshowModalImportAll').modal('hide');
        }
        else {
            showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Necessary to inform Vimeo identifier', 'tainacan'); ?>', 'error');
            $('#item_url_import_all').val('');
        }
    }
    //*****************************************  END IMPORT ALL  *********************************************//
    /**
    * funcao que concatena um array em um input, separado por virgulas
    * @param {int} o ID do item que sera inserido no array
    * @param {string} O id do input que esta sendo concatenado
    * @returns {void}     */
    function concatenate_in_array(key,seletor){
    var ids = [];
    if($(seletor).val()!==''){
        ids = $(seletor).val().split(',');
        index = ids.indexOf(key);
        if(index>=0){
            ids.splice(index, 1);
        }else{
            ids.push(key);
        }
        $(seletor).val(ids.join(','));
    }else{
        ids.push(key);
        $(seletor).val(ids.join(','));
    }
}
    /************************************************ HELPERS **********************************************************/
</script>
