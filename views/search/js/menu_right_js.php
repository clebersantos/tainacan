<?php ?>
<script>
    $(function () {
        showDynatreeRight($('#src').val());
    });


    function showDynatreeRight(src) {
        $("#dynatree").dynatree({
            selectionVisible: true, // Make sure, selected nodes are visible (expanded).  
            checkbox: true,
            initAjax: {
                url: src + '/controllers/collection/collection_controller.php',
                data: {
                    collection_id: $("#collection_id").val(),
                    operation: 'initDynatree'
                }
                , addActiveKey: true
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
                    //          return false;
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
                if ($(".contextMenu:visible").length > 0) {
                    $(".contextMenu").hide();
                    //          return false;
                }
            },
            onSelect: function (flag, node) {
                var selKeys = $.map(node.tree.getSelectedNodes(), function (node) {
                    return node.data.key;
                });
                get_categories_properties_ordenation();
                wpquery_dynatree(selKeys.join(", "));
                //list_all_objects(selKeys.join(", "), $("#collection_id").val(), $('#collection_single_ordenation').val(), '', $("#value_search").val())
            },
            dnd: {
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

    function autocomplete_menu_right(property_id) {
        $("#autocomplete_multipleselect_" + property_id).autocomplete({
            source: $('#src').val() + '/controllers/collection/collection_controller.php?operation=list_items_search_autocomplete&property_id=' + property_id,
            messages: {
                noResults: '',
                results: function () {
                }
            },
            minLength: 2,
            select: function (event, ui) {

                $("#autocomplete_multipleselect_" + property_id).html('');
                $("#autocomplete_multipleselect_" + property_id).val('');
                //var temp = $("#chosen-selected2 [value='" + ui.item.value + "']").val();
                var temp = $("#multipleselect_value_" + property_id + " [value='" + ui.item.value + "']").val();
                if (typeof temp == "undefined") {
                    $("#multipleselect_value_" + property_id).append("<option class='selected' value='" + ui.item.value + "' selected='selected' >" + ui.item.label + "</option>");
                    wpquery_multipleselect(property_id, "multipleselect_value_" + property_id);
                    console.log(event);
                }
                setTimeout(function () {
                    $("#autocomplete_multipleselect_" + property_id).val('');
                }, 100);
            }
        });
    }

    function clear_autocomplete_menu_right(e,facet_id) {
        $('option:selected', e).remove();
        console.log("multipleselect_value_" + facet_id);
        wpquery_multipleselect(facet_id, "multipleselect_value_" + facet_id);
        //$('.chosen-selected2 option').prop('selected', 'selected');
    }


</script>
