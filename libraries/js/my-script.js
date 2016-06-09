//utilizado para hooks em javascript
var Hook = {
    hooks: [],
    result: '',
    register: function ( name, callback ) {
      if( 'undefined' == typeof( Hook.hooks[name] ) )
        Hook.hooks[name] = []
      Hook.hooks[name].push( callback )
    },
    is_register: function (name) {
      if( 'undefined' == typeof( Hook.hooks[name] ) )
         return false;
      else{
          return true;
      }
    },
    call: function ( name, arguments ) {
      if( 'undefined' != typeof( Hook.hooks[name] ) )
        for( i = 0; i < Hook.hooks[name].length; ++i )
          if( true != Hook.hooks[name][i]( arguments ) ) { break; }
    }
 };



$(window).load(function () {
    var src = $('#src').val();    
    
    try {
        FB.init({
            appId: $('#socialdb_fb_api_id').val(),
            status: true, // check login status
            cookie: true, // enable cookies to allow the server to access the session
            xfbml: true  // parse XFBML
        });
    } catch (e) {}

    if (typeof $('#instagramInsertedIds').val() !== 'undefined' && $('#instagramInsertedIds').val() !== 'false') {
        if ($('#instagramInsertedIds').val() !== 'instagram_error') {
            var imported_ids = JSON.parse($('#instagramInsertedIds').val());
            if (imported_ids.length > 0) {
                showViewMultipleItemsSocialNetwork(imported_ids);
            }
        } else {
            showAlertGeneral('Error', 'Invalid Instagram identifier or no items to be imported', 'error');
        }
    }


    if (typeof $('#facebookInsertedIds').val() !== 'undefined' && $('#facebookInsertedIds').val() !== 'false') {
        if ($('#facebookInsertedIds').val() !== 'facebook_error') {
            var imported_ids = JSON.parse($('#facebookInsertedIds').val());
            if (imported_ids.length > 0) {
                showViewMultipleItemsSocialNetwork(imported_ids);
            }
        } else {
            showAlertGeneral('Error', 'Invalid Facebook identifier or no items to be imported', 'error');
        }
    }

    $("area[rel^='prettyPhoto']").prettyPhoto();

    /************************* VERIFICACAO DE PAGINAS **************************/
    //verifico se esta querendo visualizar um objeto especifico
    if ($('#object_page').val() !== '') {
        showSingleObjectByName($('#object_page').val(), src);
    }
    if ($('#category_page').val() !== '') {
        showPageCategories($('#category_page').val(), src);
    }
    if ($('#property_page').val() !== '') {
        showPageProperties($('#property_page').val(), src);
    }
    if ($('#tag_page').val() !== '') {
        showPageTags($('#tag_page').val(), src);
    }
    if ($('#tax_page').val() !== '') {
        showPageTaxonomies($('#tax_page').val(), src);
    }
    /************************* FIM VERIFICACAO DE PAGINAS **********************/
    //verifico se esta mandando alguma mensagem
    if ($('#info_messages').val() !== '' && $('#info_title').val() !== '') {
        showAlertGeneral($('#info_title').val(), $('#info_messages').val(), 'info');
    }
    //verifico se esta mandando alguma mensagem
    if ($('#repository_main_page').val() === 'true') {
        display_view_main_page();
    }
    //verifico se esta recuperando senha
    if ($('#recovery_password').val() !== '') {
        $('#password_user_id').val($('#recovery_password').val());
        $('#myModalPasswordReset').modal('show');
    }
    //verifico se acabou de criar uma colecao 
    if ($('#open_wizard').val() === 'true') {
        
        if(!Hook.is_register('tainacan_oncreate_collection')){
            showCollectionConfiguration(src);
        }else{
            Hook.call( 'tainacan_oncreate_collection', [src] );
        }
    }
    //verifico se o usuario se registrou
    if ($('#open_login').val() === 'true') {
        showLoginScreen(src);
    }

    $('#openmyModalRegister').click(function (e) {
        $('#myModalRegister').modal('show');
    });
    // end
    get_collections_template(src);
    check_privacity(src);
    list_main_ordenation();
    showDynatreeSingleEdit(src);
    showHeaderCollection(src);
    show_most_participatory_authors(src);
    //get_categories_properties_ordenation();
    notification_events_repository();
    $('#home_button').click(function (e) {
        $('#remove').hide();
        $('#form').hide();
        $('#list').show();
        $("#menu_object").show();
        $('#create_button').show();
    });

    $('#click_new_collection').click(function (e) {
        $('#myModal').modal('show');
    });

    function showModalCreateCollection() {
        $('#myModal').modal('show');
    }

    if (window != window.top)
    {
        /* I'm in a frame! */
        $(".navbar").css("display", "none");
        $("#wpadminbar").css("height", "0px");
        $("#wpadminbar").addClass("oculta");
        $(".ab-item").css("display", "none");
        $('#footer').hide();
    }

    $("#search_collections").autocomplete({
        source: $('#src').val() + '/controllers/collection/collection_controller.php?operation=get_collections_json',
        messages: {
            noResults: '',
            results: function() { 
               $('.ui-helper-hidden-accessible').remove();
            }
        },
        minLength: 3,
        focus: function(event, ui) {
            event.preventDefault();
            $("#search_collections").val(ui.item.label);
        },
        select: function (event, ui) {
            event.preventDefault();
            //$("#search_collections").val(ui.item.label);
            window.location = ui.item.permalink;
            
        }
    });


    $('#create_button').click(function (e) {
        var src = $('#src').val();
        $("#menu_object").hide();
        $("#container_socialdb").hide('slow');
        $("#form").hide('slow');
        $("#list").hide('slow');
        show_modal_main();
        $.ajax({
            url: src + '/controllers/object/object_controller.php',
            type: 'POST',
            data: {operation: 'create', collection_id: $("#collection_id").val()}
        }).done(function (result) {
            hide_modal_main();
            $("#form").html(result);
            $('#form').show('slow');
            $('#create_button').hide();
        });

        e.preventDefault();
    });

    $('#url_create_button').click(function (e) {
        var src = $('#src').val();
        $("#menu_object").hide();
        $.ajax({
            url: src + '/controllers/object/object_controller.php',
            type: 'POST',
            data: {operation: 'create', collection_id: $("#collection_id").val(), is_url: 'true'}
        }).done(function (result) {
            $("#container_socialdb").hide('slow');
            $("#form").hide('slow');
            $("#list").hide('slow');
            $("#form").html(result);
            $('#form').show('slow');
            $('#create_button').hide();
        });

        e.preventDefault();
    });


    $('#formSearchCollections').submit(function (e) {
//        e.preventDefault();
//        $('#list').hide();
//        $('#loader_objects').show();
       var search_for = $("#search_collections").val();
//        $.ajax({
//            url: $("#src").val() + '/controllers/object/object_controller.php',
//            type: 'POST',
//            data: {operation: 'list_search', keyword: search_for, collection_id: $("#collection_id").val(), ordenation_id: $('#collection_single_ordenation').val()}
//        }).done(function (result) {
//            elem = jQuery.parseJSON(result);
//            console.log(elem);
//            if (elem.is_json) {
//                window.location = elem.link;
//            } else {
//                $('#loader_objects').hide();
//                $('#list').html(elem.html);
//                $('#list').show();
//            }
//        });

        e.preventDefault();
        showAdvancedSearch( $("#src").val(), search_for);
    });

    // When user types enter at main search box, it opens the advanced search form with the searched term
    $("#search_collections").keyup(function(e) {
        if (e.which == 13) {
            e.preventDefault();
            showAdvancedSearch( $("#src").val(), $(this).val());
        }
    });

    //submit do importar colecao
    $('#importCollection').submit(function (e) {
        $('.nav-tabs').tab();
        $('.dropdown-toggle').dropdown();
        $('#modalImportCollection').modal('hide');
        $('#modalImportMain').modal('show');
        $.ajax({
            url: $('#src').val() + '/controllers/collection/collection_controller.php',
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false
        }).success(function (result) {
            $('#collection_file').val('');
            $('.nav-tabs').tab();
            $('.dropdown-toggle').dropdown();
            elem = jQuery.parseJSON(result);
            if (elem.result) {
                window.location = elem.url;
            } else {
                $('#modalImportMain').modal('hide');
                showAlertGeneral('Erro', 'Houve um erro na importação desde arquivo', 'error');
            }
        }).error(function (error) {
            showAlertGeneral('Erro', 'Houve um erro na importação desde arquivo', 'error');
        });
        e.preventDefault();

    });
    // submits dos eventos das categorias e tags
    //add
    $('#submit_adicionar_category_single').submit(function (e) {
        $('.nav-tabs').tab();
        $('.dropdown-toggle').dropdown();
        $('#modalAddCategoria').modal('hide');
        $('#modalImportMain').modal('show');//mostro o modal de carregamento
        $.ajax({
            url: $('#src').val() + '/controllers/event/event_controller.php',
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false
        }).done(function (result) {
            $('#modalImportMain').modal('hide');//escondo o modal de carregamento
            $('#category_single_name').val('');
            $('.nav-tabs').tab();
            $('.dropdown-toggle').dropdown();
            elem = jQuery.parseJSON(result);
            load_menu_left($('#collection_id').val());
            showAlertGeneral(elem.title, elem.msg, elem.type);
            //se estiver em um dynatree especifico
            if($('#category_single_add_dynatree_id').val()!==''){
                 $("#"+$('#category_single_add_dynatree_id').val()).dynatree("getTree").reload();
            }
            //cabecalho da colecao
            showHeaderCollection($('#src').val());
             $("#dynatree_modal_edit").dynatree("getTree").reload();
            wpquery_clean();
            $('.nav-tabs').tab();
        });
        e.preventDefault();

    });
    $('#submit_adicionar_tag_single').submit(function (e) {
        e.preventDefault();
        $('.nav-tabs').tab();
        $('#modalAdicionarTag').modal('hide');
        $('#modalImportMain').modal('show');//mostro o modal de carregamento
        $.ajax({
            url: $('#src').val() + '/controllers/event/event_controller.php',
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false
        }).done(function (result) {
            $('#modalImportMain').modal('hide');//esconde o modal de carregamento
            $('#tag_single_name').val('');
            elem = jQuery.parseJSON(result);
            $("#dynatree").dynatree("getTree").reload();
            showAlertGeneral(elem.title, elem.msg, elem.type);
            showHeaderCollection($('#src').val());
            wpquery_clean();
            $('.nav-tabs').tab();
        });
        e.preventDefault();

    });
    //edit
    $('#submit_edit_category_single').submit(function (e) {
        e.preventDefault();
        $('#modalEditCategoria').modal('hide');
        $('#modalImportMain').modal('show');//mostro o modal de carregamento
        $.ajax({
            url: $('#src').val() + '/controllers/event/event_controller.php',
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false
        }).done(function (result) {
            $('#modalImportMain').modal('hide');//esconde o modal de carregamento
            console.log(' value of category page is '+ $('#category_page').val());
            if ($('#category_page').val() !== '') {
                showPageCategories($('#category_page').val(), src);
            }
            elem = jQuery.parseJSON(result);
            $("#dynatree").dynatree("getTree").reload();
            //se estiver em um dynatree especifico
            if($('#category_single_edit_dynatree_id').val()!==''){
                 $("#"+$('#category_single_edit_dynatree_id').val()).dynatree("getTree").reload();
            }
            //cabecalho da colecao
            showHeaderCollection($('#src').val());
            wpquery_clean();
            showAlertGeneral(elem.title, elem.msg, elem.type);
        });
    });

    //edit tag
    $('#submit_edit_tag_single').submit(function (e) {
        $('#modalEditTag').modal('hide');
        $('#modalImportMain').modal('show');//mostro o modal de carregamento
        $.ajax({
            url: $('#src').val() + '/controllers/event/event_controller.php',
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false
        }).done(function (result) {
            $('#modalImportMain').modal('hide');//esconde o modal de carregamento
            if ($('#tag_page').val() !== '') {
                showPageTags($('#tag_page').val(), src);
            }
            elem = jQuery.parseJSON(result);
            $("#dynatree").dynatree("getTree").reload();            
            showHeaderCollection($('#src').val());
            wpquery_clean();
            showAlertGeneral(elem.title, elem.msg, elem.type);
            $('.nav-tabs').tab();
        });
        e.preventDefault();
    });
    //delete
    $('#submit_delete_category_single').submit(function (e) {
        $('#modalExcluirCategoria').modal('hide');
        $('#modalImportMain').modal('show');//mostro o modal de carregamento
        $.ajax({
            url: $('#src').val() + '/controllers/event/event_controller.php',
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false
        }).done(function (result) {
            $('#modalImportMain').modal('hide');//esconde o modal de carregamento
            elem = jQuery.parseJSON(result);
            $("#dynatree").dynatree("getTree").reload();
            //se estiver em um dynatree especifico
            if($('#category_single_delete_dynatree_id').val()!==''){
                 $("#"+$('#category_single_delete_dynatree_id').val()).dynatree("getTree").reload();
            }
            //cabecalho da colecao
            showHeaderCollection($('#src').val());
            wpquery_clean();
            showAlertGeneral(elem.title, elem.msg, elem.type);
            $('.nav-tabs').tab();
        });
        e.preventDefault();
    });

    //delete tag
    $('#submit_delete_tag_single').submit(function (e) {
        $('#modalExcluirTag').modal('hide');
        $('#modalImportMain').modal('show');//mostro o modal de carregamento
        $.ajax({
            url: $('#src').val() + '/controllers/event/event_controller.php',
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false
        }).done(function (result) {
            $('#modalImportMain').modal('hide');//esconde o modal de carregamento
            elem = jQuery.parseJSON(result);
            $("#dynatree").dynatree("getTree").reload();
            showHeaderCollection($('#src').val());
            wpquery_clean();
            showAlertGeneral(elem.title, elem.msg, elem.type);
            $('.nav-tabs').tab();
        });
        e.preventDefault();
    });

    $('#formUserRegister').submit(function (e) {
        e.preventDefault();
        $('#myModalRegister').modal('hide');
        $('#modalImportMain').modal('show');//mostro o modal de carregamento
        $.ajax({
            url: $("#src").val() + '/controllers/user/user_controller.php',
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false
        }).done(function (result) {
            $('#modalImportMain').modal('hide');//esconde o modal de carregamento
            elem = jQuery.parseJSON(result);
            console.log(elem);
            if (elem.result === '1') {
                confirm_success_register_user(elem.title, elem.msg, elem.url);
            } else {
                showAlertGeneral(elem.title, elem.msg, elem.type);
            }
        });
    });

    $('#formUserPasswordReset').submit(function (e) {
        e.preventDefault();
        $.ajax({
            url: $("#src").val() + '/controllers/user/user_controller.php',
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            showAlertGeneral(elem.title, elem.msg, elem.type);

            if (elem.type == 'success') {
                $('#myModalPasswordReset').modal('hide');
                setTimeout(function () {
                    showLoginScreen($("#src").val());
                }, 2000);
            }

        });

    });

});

$(document).ready(function () {
    $('.input_date').mask('00/00/0000');
});
/******************* funcoes para templates de colecoes ***********************/
function listTemplates(){
    $('#list_templates').show();
    $('#form_new_collection').hide();
    get_collections_template(src);
}

function backTemplates(){
    $('#list_templates').show();
    $('#form_new_collection').hide();
}

function onClickTemplate(template){
    $('#list_templates').hide();
    $('#template_collection').val(template);
    $('#form_new_collection').show();
}

function get_collections_template(src)
{
    $.ajax({
        url: src + '/controllers/collection/collection_controller.php',
        type: 'POST',
        data: {operation: 'list-collection-templates'}
    }).done(function (result) {
         $('#list_templates').html(result);
    });
}
/*************  FIM : funcoes para templates de colecoes **********************/
/***************** funcao para gerar o modal para edicao de categoria *******/
function show_modal_edit_category(title,key){
    
    //$("#category_single_parent_name_edit").val(node.data.title);
    //$("#category_single_parent_id_edit").val(node.data.key);
    $("#category_single_edit_name").val(title);
    $("#socialdb_event_previous_name").val(title);
    $("#category_single_edit_id").val(key);
    $('#modalEditCategoria').modal('show');
    //                $("#operation").val('update');
    $('.dropdown-toggle').dropdown();
    $.ajax({
        type: "POST",
        url: $('#src').val() + "/controllers/category/category_controller.php",
        data: {category_id: key, operation: 'get_parent'}
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
    });
     // metas
    $.ajax({
        type: "POST",
        url: $('#src').val() + "/controllers/category/category_controller.php",
        data: {category_id: key, operation: 'get_metas'}
    }).done(function (result) {
        elem = jQuery.parseJSON(result);
       // console.log(elem);
        if(elem.term.description){
            $("#category_edit_description").val(elem.term.description);
        }
        $('.dropdown-toggle').dropdown();
    });
}
/***************** END: funcao para gerar o modal para edicao de categoria *******/
/***************** funcao para gerar o modal para edicao de tag *******/
function show_modal_edit_tag(title,key){
   $("#tag_single_edit_name").val(title);
    $("#tag_single_edit_id").val(key);
    $('#modalEditTag').modal('show');
    $("#operation").val('update');
    $('.dropdown-toggle').dropdown();
}
/***************** END: funcao para gerar o modal para edicao de tag *******/
//buscando um termo
function get_category_promise(category_id, property_id) {
    var promise = $.ajax({
        type: "POST",
        url: $('#src').val() + "/controllers/category/category_controller.php",
        data: {category_id: category_id, property_id: property_id, operation: 'get_category'}
    });
    return promise;
}
// verificando a privacidade de uma colecao
function check_privacity(src)
{
    $.ajax({
        url: src + '/controllers/collection/collection_controller.php',
        type: 'POST',
        data: {operation: 'check_privacity', collection_id: $("#collection_id").val()}
    }).done(function (result) {
        elem = jQuery.parseJSON(result);
        if (elem.privacity == false)
        {
            $('#container_socialdb').hide();
            $('#dynatree').hide();
            redirect_privacity(elem.title, elem.msg, elem.url);
        }
    });
}
//modal_block modal que bloqueia acoes do usuario
function showModalImportCollection() {
    $("#myModal").modal('hide');
    $("#modalImportCollection").modal('show');
}

//modal_block modal que bloqueia acoes do usuario
function show_modal_main() {
    $("#modalImportMain").modal('show');
}

function hide_modal_main() {
    $("#modalImportMain").modal('hide');
}
//mostra como o username vai ficar para o usuario
function showUserName(selector) {
    $.ajax({
        url: $('#src').val() + '/controllers/user/user_controller.php',
        type: 'POST',
        data: {operation: 'show_username', username: $(selector).val(), collection_id: $("#collection_id").val()}
    }).done(function (result) {
        $('#result_username').html(result);
    });
}
// lista os autores mais participativos
function show_most_participatory_authors(src) {
    $.ajax({
        url: src + '/controllers/collection/collection_controller.php',
        type: 'POST',
        data: {operation: 'get_most_participatory_authors', collection_id: $("#collection_id").val()}
    }).done(function (result) {
        $('#most_participatory_author').html(result);
    });
}
function populateList(src) {
    $.ajax({
        url: src + '/controllers/license/index.php',
        type: 'POST',
        data: {operation: 'list'}
    }).done(function (result) {
        console.log(result);
        obj = jQuery.parseJSON(result);
        $.each(obj, function (idx, elem) {
            elem = jQuery.parseJSON(elem);
            $('table tbody').append('<tr><td>' + elem.license_name + '</td><td>' + elem.license_content
                    + '</td><td><input type="hidden" class="post_id" name="post_id" value="' + elem.license_id
                    + '"><a href="#" class="edit"><span class="glyphicon glyphicon-edit"></span></a>'
                    + '</td></tr>');
        });
    });
    ;

}
// mostra a listagem inicial
function showList(src) {
    $.ajax({
        url: src + '/controllers/object/object_controller.php',
        type: 'POST',
        data: {operation: 'list', mycollections: $("#mycollections").val(), keyword: $("#search_collection_field").val(), collection_id: $("#collection_id").val(), ordenation_id: $('#collection_single_ordenation').val()}
    }).done(function (result) {
        elem = jQuery.parseJSON(result);
        //console.log(elem,result);
        $('#loader_objects').hide();
        $('#list').html(elem.page);
        $('#wp_query_args').val(elem.args);
        $('#list').show();
        if (elem.empty_collection) {
            $('#collection_empty').show();
            $('#items_not_found').hide();
        }
    });

}
// funcao antiga que realiza a filtragem dos items
function list_all_objects(classifications, collection_id, ordered_id, order_by, keyword) {
    wpquery_filter();
}
// mostar os filtros do dynatree
function show_dynatree_filters(classifications, collection_id, keyword) {
    $.ajax({
        url: $('#src').val() + '/controllers/collection/collection_controller.php',
        type: 'POST',
        data: {
            operation: 'show_filters_dynatree',
            collection_id: collection_id,
            classifications: classifications,
            keyword: keyword
        }
    }).done(function (result) {
        $('#dynatree_filters').html(result);
    });
}



function showDynatreeSingleEdit(src) {
    $("#dynatree_modal_edit").dynatree({
        selectionVisible: true, // Make sure, selected nodes are visible (expanded).  
        checkbox: true,
        initAjax: {
            url: src + '/controllers/collection/collection_controller.php',
            data: {
                collection_id: $("#collection_id").val(),
                operation: 'initDynatreeSingleEdit',
                hide_tag: 'true',
                hide_checkbox: 'true'
            }
            , addActiveKey: true
        },
        onLazyRead: function (node) {
            node.appendAjax({
                url: src + '/controllers/category/category_controller.php',
                data: {
                    category_id: node.data.key,
                    collection_id: $("#collection_id").val(),
                    classCss: node.data.addClass,
                    hide_checkbox: 'true',
                    operation: 'findDynatreeChild'
                }
            });
        },
        onClick: function (node, event) {
            if (node.data.key != $("#category_single_edit_id").val()) {
                $("#category_single_parent_id_edit").val(node.data.key);
                $("#category_single_parent_name_edit").val(node.data.title);
            }
        },
        onKeydown: function (node, event) {
            // Eat keyboard events, when a menu is open
            if ($(".contextMenu:visible").length > 0)
                return false;

            switch (event.which) {

                // Open context menu on [Space] key (simulate right click)
                case 32: // [Space]
                    $(node.span).trigger("mousedown", {
                        preventDefault: true,
                        button: 2
                    })
                            .trigger("mouseup", {
                                preventDefault: true,
                                pageX: node.span.offsetLeft,
                                pageY: node.span.offsetTop,
                                button: 2
                            });
                    return false;

                    // Handle Ctrl-C, -X and -V
                case 67:
                    if (event.ctrlKey) { // Ctrl-C
                        copyPaste("copy", node);
                        return false;
                    }
                    break;
                case 86:
                    if (event.ctrlKey) { // Ctrl-V
                        copyPaste("paste", node);
                        return false;
                    }
                    break;
                case 88:
                    if (event.ctrlKey) { // Ctrl-X
                        copyPaste("cut", node);
                        return false;
                    }
                    break;
            }
        },
        onCreate: function (node, span) {
            //bindContextMenuSingle(span);
            //$('.dropdown-toggle').dropdown();
        },
        onPostInit: function (isReloading, isError) {
            //$('#parentCat').val("Nenhum");
            $('#parentId').val("");
            $("ul.dynatree-container").css('border', "none");
            //$( "#btnExpandAll" ).trigger( "click" );
        },
        onActivate: function (node, event) {
            // Close menu on click
            if ($(".contextMenu:visible").length > 0) {
                $(".contextMenu").hide();
                //          return false;
            }
        },
        onSelect: function (flag, node) {
        },
        dnd: {
            zIndex: 99999,
            preventVoidMoves: true, // Prevent dropping nodes 'before self', etc.     
            revert: false, // true: slide helper back to source if drop is rejected
            onDragStart: function (node) {
                /** This function MUST be defined to enable dragging for the tree.*/
               
                logMsg("tree.onDragStart(%o)", node);
                if (node.data.isFolder) {
                    return false;
                }
                return true;
            },
            onDragStop: function (node) {
                logMsg("tree.onDragStop(%o)", node);
            },
            onDragEnter: function (node, sourceNode) {
                if (node.parent !== sourceNode.parent)
                    return false;
                return ["before", "after"];
            },
            onDrop: function (node, sourceNode, hitMode, ui, draggable) {
                sourceNode.move(node, hitMode);
            }
        }
    });
}



function showHeaderCollection(src) {
    $.ajax({
        url: src + '/controllers/collection/collection_controller.php',
        type: 'POST',
        data: {operation: 'show_header', collection_id: $("#collection_id").val(), mycollections: $("#mycollections").val()}
    }).done(function (result) {
        $("#collection_post").html(result);
        $('.nav-tabs').tab();
        $('.dropdown-toggle').dropdown();
    });
}


function showCollectionConfiguration(src) {
    $.ajax({
        url: src + '/controllers/collection/collection_controller.php',
        type: 'POST',
        data: {operation: 'edit_configuration', collection_id: $("#collection_id").val()}
    }).done(function (result) {
        $('#main_part').hide();
        $('#configuration').html(result);
        $('#configuration').show();
    });
}

function showCollectionConfiguration_editImages(src, field) {
    $('#change_collection_images').val(field);
    $.ajax({
        url: src + '/controllers/collection/collection_controller.php',
        type: 'POST',
        data: {operation: 'edit_configuration', collection_id: $("#collection_id").val()}
    }).done(function (result) {
        $('#main_part').hide();
        $('#configuration').html(result);
        $('#configuration').show();
    });
}



//CODIGO SAYMON
function showSocialConfiguration(src) {
    $.ajax({
        url: src + '/controllers/social_network/youtube_controller.php',
        type: 'POST',
        data: {operation: 'list', collection_id: $("#collection_id").val()}
    }).done(function (result) {
        $('#main_part').hide();
        $('#configuration').html(result);
        $('#configuration').show();
    });
}
// FIM CODIGO SAYMON
//CODIGO EDUARDO
// mostra a view inicial das categorias
function showCategoriesConfiguration(src, is_front) {
    $.ajax({
        url: src + '/controllers/category/category_controller.php',
        type: 'POST',
        data: {operation: 'list', collection_id: $("#collection_id").val()}
    }).done(function (result) {
        $('#main_part').hide();
        $("#form").html('');

        if (is_front) {
            $('#configuration').addClass('col-md-12').css({background: 'white', padding: "20px" }).html(result).show();
            $("#display_view_main_page").remove();
            $("body.home .tainacan-topo-categoria button").remove();
        } else {
            $('#configuration').html(result).show();
        }

    });
}

// funcao que mostras o menu das propriedades
function showPropertiesConfiguration(src) {
    $("#form").html('');
    $.ajax({
        url: src + '/controllers/property/property_controller.php',
        type: 'POST',
        data: {operation: 'list', collection_id: $("#collection_id").val()}
    }).done(function (result) {
        $('#main_part').hide();
        $('#configuration').html(result).show();
    });
}

function showPropertiesAndFilters(src) {
    $.ajax({
        url: src + '/controllers/property/property_controller.php',
        type: 'POST',
        data: {operation: 'list_metadata', collection_id: $("#collection_id").val()}
    }).done(function (result) {
        $('#main_part').hide();
        $('#configuration').html(result).show();
    });
}

function showLayout(src) {
    $.ajax({
        url: src + '/controllers/search/search_controller.php',
        type: 'POST',
        data: {operation: 'edit_layout', collection_id: $("#collection_id").val()}
    }).done(function (result) {
        $('#main_part').hide();
        $('#configuration').html(result).show();
    });
}

function showPropertiesConfigurationWizard(src) {
    $("#configuration").animate({width: 'toggle'}, 900);
    $.ajax({
        url: src + '/controllers/property/property_controller.php',
        type: 'POST',
        data: {operation: 'list', collection_id: $("#collection_id").val()}
    }).done(function (result) {
        $('#main_part').hide();
        $("#form").html('');
        $("#configuration").animate({width: 'toggle'}, 400).html(result).show();
    });
}
// funcao que mostra os itens disponiveis na ordenacao na pagina single.php
function list_main_ordenation(has_category_properties) {
    var default_ordenation = '';
    $('#loader_objects').show();
    $("#collection_single_ordenation").html('');
    $.ajax({
        url: $('#src').val() + '/controllers/collection/collection_controller.php',
        type: 'POST',
        data: {operation: 'list_ordenation', collection_id: $("#collection_id").val()}
    }).done(function (result) {
        $("#collection_single_ordenation").html('');
        elem = jQuery.parseJSON(result);
        if (elem.general_ordenation) {
            $("#collection_single_ordenation").append("<optgroup label='" + elem.names.general_ordenation + "'>");
            $.each(elem.general_ordenation, function (idx, general) {
                if (general && general !== false) {
                    default_ordenation = general.id;
                    $("#collection_single_ordenation").append("<option value='" + general.id + "'  >" + general.name + "</option>");
                }
            });
        }
        if (elem.property_data && has_category_properties !== true) {
            $("#collection_single_ordenation").append("<optgroup label='" + elem.names.data_property + "'>");
            $.each(elem.property_data, function (idx, data) {
                if (data && data !== false) {
                    $("#collection_single_ordenation").append("<option value='" + data.id + "'  >" + data.name + "</option>");
                }
            });
            //get_categories_properties_ordenation();
        }
        if (elem.rankings) {
            $("#collection_single_ordenation").append("<optgroup label='" + elem.names.ranking + "'>");
            $.each(elem.rankings, function (idx, ranking) {
                if (ranking && ranking !== false) {
                    if (ranking.type) {
                        $("#collection_single_ordenation").append("<option value='" + ranking.id + "' selected='selected' >" + ranking.name + "  - ( Tipo :" + ranking.type + " ) </option>");
                    } else {
                        $("#collection_single_ordenation").append("<option value='" + ranking.id + "'  >" + ranking.name + "</option>");
                    }
                }
            });
        }

        if (elem.selected !== '') {
            $("#collection_single_ordenation").val(elem.selected);
        } else {
            $("#collection_single_ordenation").val(default_ordenation);
        }
        if ($('#is_filter').val() != '1') {
            showList($('#src').val());
        }
        $('.dropdown-toggle').dropdown();
    });
}
// funcao que mostra os itens disponiveis na ordenacao na pagina single.php
function list_main_ordenation_filter(has_category_properties) {
    var default_ordenation = '';
    $("#collection_single_ordenation").html('');
    $.ajax({
        url: $('#src').val() + '/controllers/collection/collection_controller.php',
        type: 'POST',
        data: {operation: 'list_ordenation', collection_id: $("#collection_id").val()}
    }).done(function (result) {
        $("#collection_single_ordenation").html('');
        elem = jQuery.parseJSON(result);
        if (elem.general_ordenation) {
            $("#collection_single_ordenation").append("<optgroup label='" + elem.names.general_ordenation + "'>");
            $.each(elem.general_ordenation, function (idx, general) {
                if (general && general !== false) {
                    default_ordenation = general.id;
                    $("#collection_single_ordenation").append("<option value='" + general.id + "' selected='selected' >" + general.name + "</option>");
                }
            });
        }
        if (elem.property_data && has_category_properties !== true) {
            $("#collection_single_ordenation").append("<optgroup label='" + elem.names.data_property + "'>");
            $.each(elem.property_data, function (idx, data) {
                if (data && data !== false) {
                    $("#collection_single_ordenation").append("<option value='" + data.id + "' selected='selected' >" + data.name + "</option>");
                }
            });
            //get_categories_properties_ordenation();
        }
        if (elem.rankings) {
            $("#collection_single_ordenation").append("<optgroup label='" + elem.names.ranking + "'>");
            $.each(elem.rankings, function (idx, ranking) {
                if (ranking && ranking !== false) {
                    if (ranking.type) {
                        $("#collection_single_ordenation").append("<option value='" + ranking.id + "' selected='selected' >" + ranking.name + "  - ( Tipo :" + ranking.type + " ) </option>");
                    } else {
                        $("#collection_single_ordenation").append("<option value='" + ranking.id + "'  >" + ranking.name + "</option>");
                    }
                }
            });
        }

        if (elem.selected !== '') {
            $("#collection_single_ordenation").val(elem.selected);
        } else {
            $("#collection_single_ordenation").val(default_ordenation);
        }
        $('.dropdown-toggle').dropdown();
    });
}

function get_categories_properties_ordenation() {
    $("#category_property_label").html('');
    var selKeys = $.map($("#dynatree").dynatree("getSelectedNodes"), function (node) {
        return node.data.key;
    });
    $.ajax({
        url: $('#src').val() + '/controllers/collection/collection_controller.php',
        type: 'POST',
        data: {operation: 'get_category_property', collection_id: $("#collection_id").val(), categories: selKeys.join(", ")}
    }).done(function (result) {
        //$("#collection_single_ordenation").html('');
        elem = jQuery.parseJSON(result);

        // console.log(result);
        if (elem.property_data) {
            // list_main_ordenation(true);
            $("#collection_single_ordenation").append("<optgroup id='category_property_label' label='" + elem.names.data_property + "'>");
            $.each(elem.property_data, function (idx, data) {
                $(this).find("option[value=" + data.id + "]").remove();
                if (data && data !== false) {
                    $("#collection_single_ordenation").append("<option value='" + data.id + "'>" + data.name + "</option>");
                }
            });
        } else {
            //list_main_ordenation();
        }
    });
}

function showExport(src) {
    $.ajax({
        url: src + '/controllers/export/export_controller.php',
        type: 'POST',
        data: {operation: 'index_export', collection_id: $("#collection_id").val()}
    }).done(function (result) {
        $('#main_part').hide();
        $('#configuration').html(result);
         $('#configuration').show();
    });
}

function showFormCreateURL(url){
     var src = $('#src').val();
    //$("#menu_object").hide();
    $("#form").html('');
    $.ajax({
        url: src + '/controllers/object/object_controller.php',
        type: 'POST',
        data: {operation: 'create_item_url', collection_id: $("#collection_id").val(),has_url:url}
    }).done(function (result) {
//        $("#container_socialdb").hide('slow');
//        $("#form").hide('slow');
//        $("#list").hide('slow');
//        $("#form").html(result);
//        $('#form').show('slow');
//        $('#create_button').hide();
        $('#main_part').hide();
        $('#display_view_main_page').hide();
        $('#loader_collections').hide();
        $('#configuration').html(result).show(); 
    });
}

function showFormCreateURLFile(url, type){
     var src = $('#src').val();
    $("#form").html('');
    $.ajax({
        url: src + '/controllers/object/object_controller.php',
        type: 'POST',
        data: {operation: 'create_item_url', collection_id: $("#collection_id").val(),has_file:url, file_type:type}
    }).done(function (result) {
        hide_modal_main();
        //        $("#container_socialdb").hide('slow');
//        $("#form").hide('slow');
//        $("#list").hide('slow');
//        $("#form").html(result);
//        $('#form').show('slow');
//        $('#create_button').hide();
        $('#main_part').hide();
        $('#display_view_main_page').hide();
        $('#loader_collections').hide();
        $('#configuration').html(result).show(); 
    });
}
/*************** LISTA COMENTARIOS DA COLECAO ************/
/**
 * @function showPageCollectionPage 
 * @returns {void} Insere o html com os comentarios da colecao
 */
function showPageCollectionPage() {
    console.log('clickeded');
    $("#menu_object").hide();
    $("#container_socialdb").hide('slow');
    $("#form").hide('slow');
    $("#list").hide('slow');
    $.ajax({
        url: $('#src').val() + '/controllers/collection/collection_controller.php',
        type: 'POST',
        data: {operation: 'comments', collection_id: $("#collection_id").val()}
    }).done(function (result) {
        json = JSON.parse(result);
        if(json.html){
           //$("#loader_objects").hide();            
            $("#form").html(json.html);
            $('#form').show('slow');
           // $('#create_button').hide();
        }
    });
}
/*************** LISTA COMENTARIOS DE TERMOS ************/
/**
 * @function list_comments_term 
 * @param {string} seletor O id da tag do html que sera jogado o html
 * @param {int} term_id O id do termo que esta inserido os comentarios
 * @returns {void} Insere o html com a listagem dos comentarios
 */
 function list_comments_term(seletor,term_id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/object/object_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'list_comments_term', term_id: term_id}
        }).done(function (result) {
            $("#"+seletor).html(result);
            $('.dropdown-toggle').dropdown();
            $('.nav-tabs').tab();
        });
    }
/***********************************************************/    
    
// FIM CODIGO EDUARDO


// CODIGO MARCUS
function showDesignConfiguration(src) {
    $.ajax({
        url: src + '/controllers/design/design_controller.php',
        type: 'POST',
        data: {operation: 'edit_configuration', collection_id: $("#collection_id").val()}
    }).done(function (result) {
        $('#main_part').hide();
        $('#configuration').html(result);
    });
}

function showSearchConfiguration(src) {
    $.ajax({
        url: src + '/controllers/search/search_controller.php',
        type: 'POST',
        data: {operation: 'edit', collection_id: $("#collection_id").val()}
    }).done(function (result) {
        $('#main_part').hide();
        $('#configuration').html(result).show();
        // document.getElementById("configuration").innerHTML = result;
    });
}

function showLicensesConfiguration(src) {
    $.ajax({
        url: src + '/controllers/license/license_controller.php',
        type: 'POST',
        data: {operation: 'list_licenses', collection_id: $("#collection_id").val()}
    }).done(function (result) {
        $('#main_part').hide();
        $('#configuration').html(result);
    });
}

function showEvents(src) {
    $.ajax({
        url: src + '/controllers/event/event_controller.php',
        type: 'POST',
        data: {operation: 'list', collection_id: $("#collection_id").val()}
    }).done(function (result) {
        $('#main_part').hide('slow');
        $('#configuration').html(result);
    });
}

function showEventsRepository(src, collection_root_id) {
    $.ajax({
        url: src + '/controllers/event/event_controller.php',
        type: 'POST',
        data: {operation: 'list_events_repository', collection_id: collection_root_id}
    }).done(function (result) {
        resetHomeStyleSettings();
        $('#main_part').hide();
        $('#configuration').html(result);
    });
}
/**
 * 
 * @param {type} action
 * @param {type} category_root_name
 * @param {type} category_root_id
 * @param {type} dynatree_id O id do dynatree que sera recarregado
 * @returns {undefined}
 */
function showModalFilters(action,category_root_name,category_root_id,dynatree_id) {
    if(!category_root_name){
        category_root_name = 'Category';
    }
    if(!category_root_id){
        category_root_id = 'socialdb_category';
    }
    if(dynatree_id){
        $("#category_single_add_dynatree_id").val(dynatree_id);
    }
    //
    switch (action) {
        case "add_category":
            $("#category_single_parent_name").val(category_root_name);
            $("#category_single_parent_id").val(category_root_id);
            $('#modalAddCategoria').modal('show');
            // $('.dropdown-toggle').dropdown();
            break;
        case "add_property":
            $('#modalAddProperty').modal('show');
            // $('.dropdown-toggle').dropdown();
            break;
        case "add_tag":
            $("#tag_single_name").val('');
            $('#modalAdicionarTag').modal('show');
            // $('.dropdown-toggle').dropdown();
            break;
    }
    $('.dropdown-toggle').dropdown();
}

$(document).ready(function () {
    //Handles menu drop down
    $('.dropdown-menu').find('form').click(function (e) {
        e.stopPropagation();
    });
});

function showSingleObject(object_id, src) {
    $.ajax({
        url: src + '/controllers/object/object_controller.php',
        type: 'POST',
        data: {operation: 'list_single_object', object_id: object_id, collection_id: $("#collection_id").val()}
    }).done(function (result) {
        $('#main_part').hide();
        $('#display_view_main_page').hide();
        $('#loader_collections').hide();
        $('#collection_post').hide();
        $('#configuration').html(result).show();
    });
}
/***************************** funcoes para mostrar paginas especificas  *******/
//PARA ITEMS
function showSingleObjectByName(object_name, src) {
    $.ajax({
        url: src + '/controllers/object/object_controller.php',
        type: 'POST',
        data: {operation: 'list_single_object_by_name', object_name: object_name, collection_id: $("#collection_id").val()}
    }).done(function (result) {
        var json = JSON.parse(result);
        if(json.html){
            $('#main_part').hide();
            $('#collection_post').hide();
            $('#configuration').html(json.html);
        } else {
             showAlertGeneral('Atenção!', 'Este item foi removido','info');
             var stateObj = {foo: "bar"};
             history.replaceState(stateObj, "page 2", '?');
        }
    });
}
//PARA CATEGORIAS
function showPageCategories(slug_category, src) {
   // console.log('I am here');
    $("#menu_object").hide();
    $("#category_page").val(slug_category);
    $("#container_socialdb").hide('slow');
    $("#form").hide('slow');
    $("#list").hide('slow');
    $.ajax({
        url: src + '/controllers/category/category_controller.php',
        type: 'POST',
        data: {operation: 'page', slug_category: slug_category, collection_id: $("#collection_id").val()}
    }).done(function (result) {
        json = JSON.parse(result);
        if(json.html){
           // console.log('show the page of '+slug_category);
            $("#loader_objects").hide();            
            $("#form").html(json.html);
            $('#form').show('slow');
           // $('#create_button').hide();
        }else{
            $("#menu_object").show();
            $("#container_socialdb").show('slow');
            $("#list").show('slow');
             showAlertGeneral(json.title, json.error,'info');
             var stateObj = {foo: "bar"};
             history.replaceState(stateObj, "page 2", '?');
        }
    });
}
//funcao essencial para retornar o link da pagina das categorias
function get_url_category(term_id){
    return $.ajax({
            url: $('#src').val() + '/controllers/category/category_controller.php',
            type: 'POST',
            data: { 
                operation: 'get_url_category', 
                collection_id: $("#collection_id").val(),
                term_id:term_id
            }
    });
}
//PARA PROPRIEDADES
function showPageProperties(slug_property, src) {
    $("#menu_object").hide();
    $("#container_socialdb").hide('slow');
    $("#form").hide('slow');
    $("#list").hide('slow');
    $.ajax({
        url: src + '/controllers/property/property_controller.php',
        type: 'POST',
        data: {
            operation: 'page',
            slug_property: slug_property,
            collection_id: $("#collection_id").val()}
    }).done(function (result) {
        json = JSON.parse(result);
        if(json.html){
             $("#loader_objects").hide();            
            $("#form").html(json.html);
            $('#form').show('slow');
           // $('#create_button').hide();
        }else{
             $("#menu_object").show();
            $("#container_socialdb").show('slow');
            $("#list").show('slow');
             showAlertGeneral(json.title, json.error,'info');
             var stateObj = {foo: "bar"};
             history.replaceState(stateObj, "page 2", '?');
        }
        
    });
}
//funcao essencial para retornar o slug da pagina das propriedades
function get_slug_property(term_id){
    return $.ajax({
            url: $('#src').val() + '/controllers/property/property_controller.php',
            type: 'POST',
            data: { 
                operation: 'get_slug_property', 
                collection_id: $("#collection_id").val(),
                term_id:term_id
            }
    });
}
//PARA TAGS
function showPageTags(slug_tag, src) {
    $("#menu_object").hide();
    $("#container_socialdb").hide('slow');
    $("#form").hide('slow');
    $("#list").hide('slow');
    $.ajax({
        url: src + '/controllers/tag/tag_controller.php',
        type: 'POST',
        data: {operation: 'page', slug_tag: slug_tag, collection_id: $("#collection_id").val()}
    }).done(function (result) {
        json = JSON.parse(result);
        if(json.html){
            $("#loader_objects").hide();            
            $("#form").html(json.html);
            $('#form').show('slow');
           // $('#create_button').hide();
        }else{
             $("#menu_object").show();
            $("#container_socialdb").show('slow');
            $("#list").show('slow');
             showAlertGeneral(json.title, json.error,'info');
             var stateObj = {foo: "bar"};
             history.replaceState(stateObj, "page 2", '?');
        }
        
    });
}
function showGraph(url){
    $("#category_page").val('');
    $("#property_page").val('');
    $('#main_part').show();
    $('#collection_post').show();
    $('#configuration').hide();
    var width = $('#div_central').width()-200;
    $("#menu_object").hide();
    $("#container_socialdb").hide('slow');
    $("#form").hide('slow');
    $("#list").hide('slow');
    $("#form").html('<iframe class="col-md-12" scrolling-x="no" height="700"  style="border:3px solid #E8E8E8;background:white;overflow-x:hidden;overflow-y:scroll;" src="'+$('#src').val()+'/extras/visualRDF/index_tainacan.php?url='+url+'&width='+width+'"></iframe>');
    $("#form").show('slow');
    $("#form").css('border','none');
    $("html, body").delay(1000).animate({
        scrollTop: $('#form').offset().top 
    }, 2000);
}
/***************************** Fim: funcoes para mostrar paginas especificas  *******/
$(function () {
    var nav = $('#hypertree');
    var tamanhoTelaW = $(window).width();

    var w = (tamanhoTelaW / 2) - 100, h = (tamanhoTelaW / 2) - 100;
    $(window).scroll(function () {
        if ($(this).scrollTop() > 300) {

            nav.css("width", w);
            nav.css("height", h);
            nav.addClass("menuFixo");
        } else {
            nav.removeClass("menuFixo");
        }
    });
});

function showLoginScreen(src) {
    $.ajax({
        url: src + '/controllers/user/user_controller.php',
        type: 'POST',
        data: {operation: 'show_login_screen', collection_id: $("#collection_id").val()}
    }).done(function (result) {
        resetHomeStyleSettings();
        $('#main_part').hide();
        $('#loader_collections').hide();
        $('#collection_post').hide();
        $('#configuration').html(result);
    });

}

function showProfileScreen(src) {
    $.ajax({
        url: src + '/controllers/user/user_controller.php',
        type: 'POST',
        data: {operation: 'show_profile_screen', collection_id: $("#collection_id").val()}
    }).done(function (result) {
        $('#main_part').hide();
        resetHomeStyleSettings();
        $('#loader_collections').hide();
        $('#collection_post').hide();
        $('#configuration').html(result);
    });

}

function check_passwords()
{
    if ($('#new_password_reset').val().trim() == '' || $('#new_check_password_reset').val().trim() == '' || $('#old_password_reset').val().trim() == '') {
        showAlertGeneral("Erro", "Preencha os campos corretamente.", "error");
        return false;
    } else
    {
        if ($('#new_password_reset').val() === $('#new_check_password_reset').val()) {
            $('#formUserPasswordReset').submit();
            return true;
        }
        else {
            showAlertGeneral("Error", "Passwords do not match!", "error");
            return false;
        }
    }
}

function check_register_fields()
{
    if ($('#user_login').val().trim() == '' || $('#first_name').val().trim() == '' || $('#user_conf_pass').val().trim() == '' || $('#user_pass').val().trim() == '') {
        showAlertGeneral("Erro", "Preencha os campos corretamente.", "error");
        return false;
    } else
    {
        if ($('#user_pass').val().trim() !== $('#user_conf_pass').val().trim()) {
            showAlertGeneral("Erro", "Senhas nao conferem. Favor verificar!", "error");
            return false;
        } else {
            $('#formUserRegister').submit();
            return true;
        }
    }
}

function changeBoxWidth(formInput) {
    //formInput.style.background = "yellow";
    //$('#searchBoxIndex').addClass("col-md-5").removeClass("col-md-3", 1000);
    $('#searchBoxIndex').animate({
        width: '42%'
    }, 1000, function () {
        // Animation complete.
    });
}

function showFullDescription() {
    $('#modalShowFullDescription').modal('show');
}

function showModalImportSocialNetwork() {
    $('#modalshowModalImportSocialNetwork').modal('show');
}

function showModalImportAll() {
    $('#modalshowModalImportAll').modal('show');
}

function showAddItemURL() {
    show_modal_main();
    $.ajax({
        url: $('#src').val() + '/controllers/object/object_controller.php',
        type: 'POST',
        data: {operation: 'showAddItemURL', collection_id: $("#collection_id").val()}
    }).done(function (result) {
        $('#main_part').hide();
        $('#display_view_main_page').hide();
        $('#loader_collections').hide();
        //$('#collection_post').hide();
        hide_modal_main();
        $('#configuration').html(result).show();
    });
}
// FIM CODIGO MARCUS



// CODIGO MARCO
function showAdvancedSearch(src, search_term) {
    var search_term = search_term || "";
    show_modal_main();
    $.ajax({
        url: src + '/controllers/advanced_search/advanced_search_controller.php',
        type: 'POST',
        data: {operation: 'open_page', collection_id: $("#collection_id").val(), home_search_term: search_term }
    }).done(function (result) {
        resetHomeStyleSettings();
        hide_modal_main();
        $('#main_part').hide();
        $('#display_view_main_page').hide();
        $('#loader_collections').hide();
        $('#collection_post').hide();
        $('#configuration').html(result).show();
    });
}

function showRankingConfiguration(src) {
    $.ajax({
        url: src + '/controllers/ranking/ranking_controller.php',
        type: 'POST',
        data: {operation: 'list_data', collection_id: $("#collection_id").val()}
    }).done(function (result) {
        $('#main_part').hide();
        $('#configuration').html(result).show();
    });
}

function showCKEditor(id) {
    if (!id) {
        id = 'editor';
    }
    var editor, html = '';
    CKEDITOR.disableAutoInline = true;
    if (editor)
        return;
    // Create a new editor inside the <div id="editor">, setting its value to html
    var config = {};
    editor = CKEDITOR.replace(id, config, html);
}

function showImport(src) {
    $.ajax({
        url: src + '/controllers/import/import_controller.php',
        type: 'POST',
        data: {operation: 'show_import_configuration', collection_id: $("#collection_id").val()}
    }).done(function (result) {
        $('#main_part').hide();
        $('#configuration').html(result).show();
    });

}
// FIM CODIGO MARCO

function cl(string) {
    console.log(string);
}

// Funcao que esconde a mensagem de alerta (sucesso ou erro)
function hide_alert() {
    $(".alert").hide();
}

//FAZ A INSERCAO RAPIDA
function fast_insert() {
    if ($('#fast_insert_object').val().trim() === '') {
        showAlertGeneral('Atenção', 'URL ou nome inválido', 'info');
        return false;
    }
    var selKeys = $.map($("#dynatree").dynatree("getSelectedNodes"), function (node) {
        return node.data.key;
    });
    var result = $('#fast_insert_object').val().match(/[\w\d\.]+\.[\w\d]{1,4}/g);
    if (result == null) {
        $.ajax({
            url: $('#src').val() + '/controllers/object/object_controller.php',
            type: 'POST',
            data: {classifications: selKeys.join(", "), operation: 'insert_fast', collection_id: $("#collection_id").val(), title: $('#fast_insert_object').val()}
        }).done(function (result) {
            wpquery_filter();
            //list_all_objects(selKeys.join(", "), $("#collection_id").val());
            elem_first = jQuery.parseJSON(result);
            showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
        });
    } else {
        var format = $('#fast_insert_object').val().split('.');
        var supported_images = ['jpg', 'jpeg', 'png', 'gif'];
        if (format.length > 0 && supported_images.indexOf(format[format.length - 1]) > -1) {
            insert_image_url($('#fast_insert_object').val(), selKeys);
        } else {
            //showAlertGeneral('error', 'URL invalid or image unreacheable', 'error'); 
            insert_object_url($('#fast_insert_object').val(), selKeys);
        }
        //insert_object_url($('#fast_insert_object').val(), selKeys);
    }
    $('#fast_insert_object').val('');
}
//INSERE A IMAGEM PELA URL
function insert_image_url(image_url, classifications) {
    $.ajax({
        url: $('#src').val() + '/controllers/object/object_controller.php',
        type: 'POST',
        data: {
            classifications: classifications.join(", "),
            operation: 'insert_fast_url',
            collection_id: $("#collection_id").val(),
            description: '<a href="' + image_url + '">Link</a>',
            thumbnail_url: image_url,
            url: image_url,
            title: 'Item'}
    }).done(function (result) {
        $('#loader_import_object').hide('slow');
        $('#form_url_import').show();
        try {
            wpquery_filter();
            //list_all_objects(classifications.join(", "), $("#collection_id").val());
            elem_first = jQuery.parseJSON(result);
            $('#save_object_url').removeAttr('disabled');
            $('#modal_import_objet_url').modal('hide');
            showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
        } catch (e) {
            $('#save_object_url').removeAttr('disabled');
            $('#modal_import_objet_url').modal('hide');
            $('#save_object_url').attr('disabled', '');
        }
    });
}
// INSERINDO OBJETO PELA URL
function insert_object_url(url, classifications) {
    var key = $('#socialdb_embed_api_id').val();
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
        console.log(json);
        var description = '', title = '';
        if (json.title !== undefined && json.title != null && json.title != false) {
            title = json.title;
        }
        else {
            $('#loading').hide();
            showAlertGeneral('Atenção', 'Esta URL não possui items disponíveis para importação', 'error');
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
            $('#image_side').html(html);
            $('#save_object_url').click(function (e) {
                e.stopImmediatePropagation();
                e.preventDefault();
                $('#save_object_url').attr('disabled', 'disabled');
                $('#form_url_import').hide('slow');
                $('#loader_import_object').show('slow');
                title = $('#title_insert_object_url').val();
                description = $('#description_insert_object_url').val();
                $.ajax({
                    url: $('#src').val() + '/controllers/object/object_controller.php',
                    type: 'POST',
                    data: {
                        classifications: classifications.join(", "),
                        operation: 'insert_fast_url',
                        collection_id: $("#collection_id").val(),
                        description: description,
                        thumbnail_url: $('#thumbnail_url').val(),
                        url: url,
                        title: title}
                }).done(function (result) {
                    $('#loader_import_object').hide('slow');
                    $('#form_url_import').show();
                    try {
                        wpquery_filter();
                        //list_all_objects(classifications.join(", "), $("#collection_id").val());
                        elem_first = jQuery.parseJSON(result);
                        $('#save_object_url').removeAttr('disabled');
                        $('#modal_import_objet_url').modal('hide');
                        showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
                    } catch (e) {
                        $('#save_object_url').removeAttr('disabled');
                        $('#modal_import_objet_url').modal('hide');
                        $('#save_object_url').attr('disabled', '');
                    }
                });

            });
        } else {
            $('#image_side').html('');
            $('#save_object_url').click(function (e) {
                e.stopImmediatePropagation();
                e.preventDefault();
                $('#save_object_url').attr('disabled', 'disabled');
                $('#form_url_import').hide('slow');
                $('#loader_import_object').show('slow');
                title = $('#title_insert_object_url').val();
                description = $('#description_insert_object_url').val();
                $.ajax({
                    url: $('#src').val() + '/controllers/object/object_controller.php',
                    type: 'POST',
                    data: {
                        classifications: classifications.join(", "),
                        operation: 'insert_fast_url',
                        collection_id: $("#collection_id").val(),
                        description: description,
                        url: url,
                        title: title}
                }).done(function (result) {
                    $('#loader_import_object').hide('slow');
                    $('#form_url_import').show();
                    try {
                        $('#save_object_url').removeAttr('disabled');
                        wpquery_filter();
                        //list_all_objects(classifications.join(", "), $("#collection_id").val());
                        elem_first = jQuery.parseJSON(result);
                        $('#modal_import_objet_url').modal('hide');
                        //showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
                    } catch (e) {
                        $('#save_object_url').removeAttr('disabled');
                        $('#modal_import_objet_url').modal('hide');
                        $('#save_object_url').attr('disabled', '');
                    }
                });

            });
        }
        $('#title_insert_object_url').val(title);
        $('#description_insert_object_url').val(description);
        $('#loading').hide('slow');
        $('#modal_import_objet_url').modal('show');

    }).fail(function (result) {
        // console.log('error', result, url);
        $('#loading').hide();
        showAlertGeneral('Atenção', 'URL inexistente ou indisponível', 'error');
    });
}


function show_form_item() {
    var src = $('#src').val();
    $("#menu_object").hide();
    $.ajax({
        url: src + '/controllers/object/object_controller.php',
        type: 'POST',
        data: {operation: 'create', collection_id: $("#collection_id").val()}
    }).done(function (result) {
        $("#form").hide('slow');
        $("#list").hide('slow');
        $("#form").html(result);
        $('#form').show('slow');
        $('#create_button').hide();
    });
}
//##################################### REPOSITORY ########################################//
//notification events repository
function notification_events_repository() {
    $.ajax({
        type: "POST",
        url: $('#src').val() + "/controllers/event/event_controller.php",
        data: {operation: 'notification_events_repository'}
    }).done(function (result) {
        $('#notification_events_repository').html(result);
        $('.dropdown-toggle').dropdown();
        $('.nav-tabs').tab();
    });
}
//properties
// funcao que mostras o menu das propriedades
function showPropertiesRepository(src) {
    $.ajax({
        url: src + '/controllers/property/property_controller.php',
        type: 'POST',
        data: {operation: 'list_repository'}
    }).done(function (result) {
        resetHomeStyleSettings();
        $('#main_part').hide();
        $('#configuration').html(result);
        $('#configuration').show();
    });
}

function showAPIConfiguration(src) {
    $.ajax({
        url: src + '/controllers/theme_options/theme_options_controller.php',
        type: 'POST',
        data: {operation: 'edit_configuration'}
    }).done(function (result) {
        resetHomeStyleSettings();
        $('#main_part').hide();
        $('#configuration').html(result);
        $('#configuration').show();
    });
}

function showRepositoryConfiguration(src) {
    $.ajax({
        url: src + '/controllers/theme_options/theme_options_controller.php',
        type: 'POST',
        data: {operation: 'edit_general_configuration'}
    }).done(function (result) {
        resetHomeStyleSettings();
        $('#main_part').hide();
        $('#configuration').html(result);
        $('#configuration').show();
    });
}

function showLicensesRepository(src) {
    $.ajax({
        url: src + '/controllers/theme_options/theme_options_controller.php',
        type: 'POST',
        data: {operation: 'edit_licenses'}
    }).done(function (result) {
        resetHomeStyleSettings();
        $('#main_part').hide();
        $('#configuration').html(result);
        $('#configuration').show();
    });
}

function showWelcomeEmail(src) {
    $.ajax({
        url: src + '/controllers/theme_options/theme_options_controller.php',
        type: 'POST',
        data: {operation: 'edit_welcome_email'}
    }).done(function (result) {
        resetHomeStyleSettings();
        $('#main_part').hide();
        $('#configuration').html(result);
        $('#configuration').show();
    });
}

function showTools(src) {
    $.ajax({
        url: src + '/controllers/theme_options/theme_options_controller.php',
        type: 'POST',
        data: {operation: 'edit_tools'}
    }).done(function (result) {
        resetHomeStyleSettings();
        $('#main_part').hide();
        $('#configuration').html(result);
        $('#configuration').show();
    });
}

function resetHomeStyleSettings() {
    $('#display_view_main_page').hide();
    $('body.home').css('background', 'white');
}

//
function bytesToSize(bytes) {
    var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    if (bytes == 0)
        return '0 Byte';
    var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
    return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
}
// apenas numeros no input
function onlyNumbers(e){
    var tecla=(window.event)?event.keyCode:e.which;
    if((tecla>47 && tecla<58)) 
        return true;
    else{
       if (tecla==8 || tecla==0) 
           return true;
       else  
           return false;
    }
}
// validacao para float
function isFloat(evt) {
    var charCode = (event.which) ? event.which : event.keyCode;
    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    else {
        //if dot sign entered more than once then don't allow to enter dot sign again. 46 is the code for dot sign
        var parts = evt.srcElement.value.split('.');
        if (parts.length > 1 && charCode == 46)
            return false;
        return true;
    }
}

//HELPERS
function toggleSlide(target,reverse){
    if(!reverse){
        reverse = false;
    }
    if($("#"+target).is(":visible") == true){
        $("#"+target).slideUp();
        if(reverse!==false){
            $("#"+reverse).slideDown();
        }
    }else{
        $("#"+target).slideDown();
        if(reverse!==false){
            $("#"+reverse).slideUp();
        }
    }
}



function show_field_properties(property_id,show_id){
   $('#field_property_'+property_id+'_'+show_id).show().css('margin-bottom','15px');
   $('#button_property_'+property_id+'_'+show_id).show();
   $('#button_property_'+property_id+'_'+(show_id-1)).hide();
}

//--------------------------------- MAIN PAGE DO REPOSITORIO -------------------------------//
function display_view_main_page() {
    $.ajax({
        url: $("#src").val() + '/controllers/home/home_controller.php',
        type: 'POST',
        data: {operation: 'display_view_main_page', max_collection_showed: $("#max_collection_showed").val()}
    }).done(function (result) {
        $("#loader_collections").hide('slow');
        $("#display_view_main_page").html(result);
    });
}
// FACEBOOK METHODS
function graphStreamPublish(message, link, picture, name, description) {
    //showLoader(true);
    console.log(FB);
    FB.api('/me/feed', 'post',
            {
                message: message,
                link: link,
                picture: picture,
                name: name,
                description: description

            },
    function (response) {
        //showLoader(false);
        console.log(response);
        if (!response || response.error) {
            alert('Error occured');
        } else {
            alert('Post ID: ' + response.id);
        }
    });
}
//************************ TELA DE ADICAO DE ITEM *******************************//
function showAddItemText() {
    var src = $('#src').val();
    $("#form").html('');
    show_modal_main();
    $.ajax({
        url: src + '/controllers/object/object_controller.php',
        type: 'POST',
        data: {operation: 'create_item_text', collection_id: $("#collection_id").val()}
    }).done(function (result) {
        hide_modal_main();
        $('#main_part').hide();
        $('#display_view_main_page').hide();
        $('#loader_collections').hide();
        $('#configuration').html(result).show();
    });
}
//--------------------------- TELA DE IMPORTACAO DE MULTIPLO ARQUIVOS --------------------------//
// lista os autores mais participativos
function showViewMultipleItems() {
    show_modal_main();
    $.ajax({
        url: $('#src').val() + '/controllers/object/object_controller.php',
        type: 'POST',
        data: {operation: 'showViewMultipleItems', collection_id: $("#collection_id").val()}
    }).done(function (result) {
        $('#main_part').hide();
        $('#display_view_main_page').hide();
        $('#loader_collections').hide();
        //$('#collection_post').hide();
        hide_modal_main();
        $('#configuration').html(result).show();
    });
}

function changeContainerBgColor() {
    $("#container_three_columns").addClass('white-background');
}

function getDynamicHeight() {
    var static_top_height = 127; // fixed top box height (in pixels)
    var static_bottom_height = 20; // fixed padding bottom size (pixels)
    var dynamic_height = $('#container_socialdb').height(); // dynamic collection size, based on # of items

    return dynamic_height + static_top_height + static_bottom_height;
}

function setMenuContainerHeight() {
    var $menu_container = $("#div_left");
    var dynamic_height = getDynamicHeight();

    if ( dynamic_height > 380 ) {
        $( $menu_container ).height(dynamic_height);
    } else {
        $( $menu_container ).css("height", "auto");
    }
}

var t = "";
$(window).on('resize', function(ev) {
    var window_width = $(window).width();
    // cl("A largura atual é " + window_width );

    if(window_width < 1010 && t == "done") {

    }
});


function changeViewMode(viewMode) {
    var concurrents = $("#collection-view-mode > div");
    $('.viewMode-control li.'+viewMode).addClass('selected-viewMode');

    $(concurrents).each(function(idx, el) {
        var cur_id = "#" + $(el).attr("id");
        if( cur_id == "#" + viewMode + "-viewMode" ) {
            $(this).fadeIn();
        } else {
            $(this).fadeOut();
        }
    });
}

//********************************** FUNCIONALIDADE ACORDEON *********************/
jQuery.fn.darken = function (darkenPercent) {
    $(this).each(function () {
        var rgb = $(this).css('background-color');
        rgb = rgb.replace('rgb(', '').replace(')', '').split(',');
        var red = $.trim(rgb[0]);
        var green = $.trim(rgb[1]);
        var blue = $.trim(rgb[2]);

        // darken
        red = parseInt(red * (100 - darkenPercent) / 100);
        green = parseInt(green * (100 - darkenPercent) / 100);
        blue = parseInt(blue * (100 - darkenPercent) / 100);
        // lighten
        /* red = parseInt(red * (100 + darkenPercent) / 100);
         green = parseInt(green * (100 + darkenPercent) / 100);
         blue = parseInt(blue * (100 + darkenPercent) / 100); */

        rgb = 'rgb(' + red + ', ' + green + ', ' + blue + ')';

        $(this).css('background-color', rgb);
    });
    return this;
};
$(".list-group .list-group").each(function () {
    $(this).children('.list-group-item').darken(10);
});