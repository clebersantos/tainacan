<script>
    $(function () {

        showDynatreeLeft($('#src').val());
        //se existir filtro para eventos
        if($('#filters_has_event_notification').val()=='true'){
            list_events_filters();
        }
    });

    function showDynatreeLeft(src) {
        $("#dynatree").dynatree({
            selectionVisible: true, // Make sure, selected nodes are visible (expanded).  
            checkbox: true,
            initAjax: {
                url: src + '/controllers/collection/collection_controller.php',
                data: {
                    collection_id: $("#collection_id").val(),
                    operation: 'initDynatree'
                },
                addActiveKey: true
            },
            onLazyRead: function (node) {
                node.appendAjax({
                    url: src + '/controllers/collection/collection_controller.php',
                    data: {
                        key: node.data.key,
                        collection: $("#collection_id").val(),
                        classCss: node.data.addClass,
                        operation: 'expand_dynatree'
                    }
                });
            },
            onClick: function (node, event) {
                // Close menu on click
                if ($(".contextMenu:visible").length > 0) {
                    $(".contextMenu").hide();
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
                var key = node.data.key;
                var n = key.toString().indexOf("_");
                if (n > 0) {// se for propriedade de objeto
                    values = key.split("_");
                    if (values[1] === 'tag' || (values[1] === 'facet' && values[2] === 'tag')) {
                        bindContextMenuSingleTag(span);
                    } else if (values[1] === 'facet' && values[2] === 'category') {
                        bindContextMenuSingle(span);
                    }
                } else {
                    bindContextMenuSingle(span);
                }
                Hook.call('tainacan_oncreate_main_dynatree',[node]);
                $('.dropdown-toggle').dropdown();
            },
            onPostInit: function (isReloading, isError) {
                //$('#parentCat').val("Nenhum");
                $('#parentId').val("");
                $("ul.dynatree-container").css('border', "none");
                //$( "#btnExpandAll" ).trigger( "click" );
            },
            onActivate: function (node, event) {
                // Close menu on click
                $('#modalImportMain').modal('show');
                // Close menu on click
                var promisse = get_url_category(node.data.key);
                promisse.done(function (result) {
                    elem = jQuery.parseJSON(result);                    
                    $('#modalImportMain').modal('hide');
                    var n = node.data.key.toString().indexOf("_");
                    if(node.data.key.indexOf('_tag')>=0){
                        showPageTags(elem.slug, src);
                        node.deactivate();
                    }else if(n<0||node.data.key.indexOf('_facet_category')>=0){
                        showPageCategories(elem.slug, src);
                        node.deactivate();
                    }
                });
            },
            onSelect: function (flag, node) {
                var selKeys = $.map(node.tree.getSelectedNodes(), function (node) {
                    return node.data.key;
                });
                //get_categories_properties_ordenation();
                if($('#flag_dynatree_ajax').val()==='true') {
                    var node_values = selKeys.join(", ");
                    wpquery_filter_by_facet( node_values, "", "wpquery_dynatree");
                }
                
                //list_all_objects(selKeys.join(", "), $("#collection_id").val(), $('#collection_single_ordenation').val(), '', $("#value_search").val())
            },
            dnd: {
                preventVoidMoves: true, // Prevent dropping nodes 'before self', etc.     
                revert: false, // true: slide helper back to source if drop is rejected
                onDragStart: function (node) {
                    /** This function MUST be defined to enable dragging for the tree.*/

                    // logMsg("tree.onDragStart(%o)", node);
                    if (node.data.isFolder) {
                        return false;
                    }
                    return true;
                },
                onDragStop: function (node) {
                    //
                    // logMsg("tree.onDragStop(%o)", node);
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
 
    function autocomplete_menu_left(property_id) {
        $("#autocomplete_multipleselect_" + property_id).autocomplete({
            source: $('#src').val() + '/controllers/collection/collection_controller.php?operation=list_items_search_autocomplete&property_id=' + property_id,
            messages: {
                noResults: '',
                results: function () { }
            },
            minLength: 2,
            select: function (event, ui) {
                // console.log(event);
                $("#autocomplete_multipleselect_" + property_id).html('');
                $("#autocomplete_multipleselect_" + property_id).val('');
                //var temp = $("#chosen-selected2 [value='" + ui.item.value + "']").val();
                var temp = $("#multipleselect_value_" + property_id + " [value='" + ui.item.value + "']").val();
                if (typeof temp == "undefined") {
                    $("#multipleselect_value_" + property_id ).append("<option onclick='clear_autocomplete_menu_left(this,"+property_id+")' value='" + ui.item.value + "' id='option_"+property_id+"_"+ui.item.value.replace(/\s+/, "") +"' selected='selected' >" + ui.item.label + "</option>");
                     wpquery_multipleselect(property_id, "multipleselect_value_" + property_id);  
                }
                setTimeout(function () {
                    $("#autocomplete_multipleselect_" + property_id).val('');
                }, 100);
            }
        });
    }
    
    function clear_autocomplete_menu_left(e,facet_id) {
         $(e).remove();
         wpquery_multipleselect(facet_id, "multipleselect_value_" + facet_id);
    }
    function list_events_filters(){
        $.ajax({
            url: $('#src').val() + '/controllers/search/search_controller.php',
            type: 'POST',
            data: {operation: 'get_events_data',collection_id: $("#collection_id").val()}
        }).done(function (result) {
            $('#notifications_filter').html(result);
        });
    }
    
    /*********************************************************************/

    function findCSSTags( css_source ) {
        var tagPattern = /\[\[.+?\]\]/gi;
        var tagsFound = {};

        while((n = tagPattern.exec( css_source ) ) != null) {
            var tag = n[0].match(/\[\[\s*(\w+)/i);
            var value = n[0].match(/:\s*([\w#]+)\s*\]\]/i);
            tag = tag[1];
            if(value) {
                value = value[1];
            }
            tagsFound[tag] = value;
        }
        if(css_source.match(/\.align-center/i)) {
            tagsFound['menu_align'] = "left";
            tagsFound['menu_align_center'] = "";
        }
        if(css_source.match(/\.align-right/i)) {
            tagsFound['menu_align'] = "left";
            tagsFound['menu_align_right'] = "";
        }
        return tagsFound;
    }

    function activeFacetAccordion() {
        return ( $("#accordion .form-group").length == 1 ) ? 0 : false;
    }

    $("#accordion").accordion({
        collapsible: true,
        header: "label",
        animate: 200,
        heightStyle: "content",
        icons: false
    });

    $('#accordion .ui-accordion-content').show();

    $('.expand-all').toggle(function() {
        setMenuContainerHeight();

        $('#accordion .ui-accordion-content').fadeOut();
        $('.prepend-filter-label').switchClass('glyphicon-triangle-bottom','glyphicon-triangle-right');
        $(this).find('span').switchClass('glyphicon-triangle-bottom','glyphicon-triangle-right');
        $('.cloud_label').click();
    }, function() {
        $('#accordion .ui-accordion-content').fadeIn();
        $('.prepend-filter-label').switchClass('glyphicon-triangle-right', 'glyphicon-triangle-bottom');
        $(this).find('span').switchClass('glyphicon-triangle-right', 'glyphicon-triangle-bottom');
        $('.cloud_label').click();
    });


    var icon_html = "<span class='prepend-filter-label glyphicon-triangle-bottom blue glyphicon'></span>";
    $('label.title-pipe').each(function(idx, el) {
       $(el).prepend(icon_html);
    });

</script>