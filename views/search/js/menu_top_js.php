<?php ?>
<script>
   function autocomplete_menu_top(property_id) {
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
                console.log(temp);
                if (typeof temp == "undefined") {
                    $("#multipleselect_value_" + property_id ).append("<option class='selected' value='" + ui.item.value + "' selected='selected' >" + ui.item.label + "</option>");
                     wpquery_multipleselect(property_id, "multipleselect_value_" + property_id);
                }
                setTimeout(function () {
                    $("#autocomplete_multipleselect_" + property_id).val('');
                }, 100);
            }
        });
    }

    function clear_autocomplete_menu_top(e,facet_id) {
        $('option:selected', e).remove();
        wpquery_multipleselect(facet_id, "multipleselect_value_" + facet_id);
        //$('.chosen-selected2 option').prop('selected', 'selected');
    }
    
</script>
