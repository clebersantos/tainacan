<?php
/*
 * 
 * View responsavel em listar todas propriedades do objeto em questao, utilizada para pegar os valores para edicao dos eventos
 */

include_once ('js/show_list_event_properties_js.php');
$ids = [];
?>

<?php if (!isset($property_object) && !isset($property_data)): ?>
    <?php _e('No Properties available', 'tainacan'); ?>
<?php endif; ?>
<?php if (isset($property_object)):
    ?>
                                                                    <!--h4><?php _e('Object Properties', 'tainacan'); ?></h4-->
    <?php
    foreach ($property_object as $property) {
        $object_id = $property['metas']['object_id'];
        $ids[] = $property['id'];
        //if ($property['metas']['socialdb_property_object_is_facet'] == 'false'):
        ?>
        <div class="bs-callout bs-callout-info">
            <h4><?php echo $property['name']; ?></h4>
            <div id="labels_<?php echo $property['id']; ?>_<?php echo $object_id; ?>">
                <?php if (!empty($property['metas']['objects']) && !empty($property['metas']['value'])) { ?>
                    <?php foreach ($property['metas']['objects'] as $object) { // percoro todos os objetos  ?>
                        <?php
                        if (isset($property['metas']['value']) && !empty($property['metas']['value']) && in_array($object->ID, $property['metas']['value'])): // verifico se ele esta na lista de objetos da colecao 
                            echo '<b><a  href="' . get_the_permalink($property['metas']['collection_data'][0]->ID) . '?item=' . $object->post_name . '" >' . $object->post_title . '</a></b><br>';
                        endif;
                        ?>
                    <?php } ?> 
                    <?php
                }else {
                    echo '<p>' . __('empty field', 'tainacan') . '</p>';
                }
                ?>       
            </div> 
            <div style="display: none;" id="widget_<?php echo $property['id']; ?>_<?php echo $object_id; ?>">
                <?php
                //acao para modificaco da propriedade de objeto na insercao do item
                if(has_action('modificate_single_item_properties_object')): 
                         do_action('modificate_single_item_properties_object',$property); 
                endif;
                ?>
                <a target="_blank" class="btn btn-primary btn-xs" href="<?php echo get_permalink($property['metas']['collection_data'][0]->ID); ?>"><?php _e('Add new', 'tainacan'); ?><?php echo ' ' . $property['metas']['collection_data'][0]->post_title; ?></a><br><br>
                <input type="text" onkeyup="autocomplete_object_property('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>');" id="single_autocomplete_value_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" placeholder="<?php _e('Type the three first letters of the object ', 'tainacan'); ?>"  class="chosen-selected form-control" />
                <select onclick="clear_select_object_property(this,'<?php echo $property['id']; ?>', '<?php echo $object_id; ?>');"  id="single_property_value_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" multiple class="chosen-selected2 form-control" style="height: auto;" multiple name="category_moderators[]" id="chosen-selected2-user"  >
                    <?php if (!empty($property['metas']['objects'])) { ?>
                        <?php foreach ($property['metas']['objects'] as $object) { // percoro todos os objetos  ?>
                            <?php if (isset($property['metas']['value']) && !empty($property['metas']['value']) && in_array($object->ID, $property['metas']['value'])): // verifico se ele esta na lista de objetos da colecao  ?>    
                                <option selected='selected' value="<?php echo $object->ID ?>"><?php echo $object->post_title ?></span>
                                <?php endif; ?>
                            <?php } ?> 
                        <?php }else { ?>   
                        <option value=""><?php _e('No objects added in this collection', 'tainacan'); ?></option>
                    <?php } ?>             
                </select>
                <input type="hidden" id="single_property_<?php echo $property['id']; ?>_<?php echo $object_id; ?>_value_before" name="property_<?php echo $property['id']; ?>_<?php echo $object_id; ?>_value_before" value="<?php if (is_array($property['metas']['value'])) echo implode(',', is_array($property['metas']['value'])); ?>">
            </div>
            <button type="button" onclick="cancel_object_property('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>')" id="single_cancel_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" class="btn btn-default btn-xs" style="display: none;" ><span class="glyphicon glyphicon-arrow-left" ></span></button>
            <?php
            // verifico se o metadado pode ser alterado
            if (verify_allowed_action($collection_id, 'socialdb_collection_permission_edit_property_object_value',$object_id)):
                ?>    
                <button type="button" onclick="edit_object_property('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>')" id="single_edit_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" class="btn btn-default btn-xs" ><span class="glyphicon glyphicon-edit"></span></button>
            <?php endif; ?>    
            <button type="button" onclick="save_object_property('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>')" id="single_save_<?php echo $property['id']; ?>_<?php echo $object_id; ?>"class="btn btn-default btn-xs" style="display: none;"><span class="glyphicon glyphicon-floppy-disk"></span></button> 
        </div>  
        <?php //endif; ?>
    <?php } ?>
     <input type="hidden" name="properties_object_ids" id='properties_object_ids' value="<?php echo implode(',', $ids); ?>">
<?php endif; ?>

<?php if (isset($property_data)): ?>
                  <!--h4><?php _e('Property data', 'tainacan'); ?></h4-->
    <?php
    foreach ($property_data as $property) {
        $object_id = $property['metas']['object_id'];
        ?>
        <div class="bs-callout bs-callout-info">
            <h4><?php echo $property['name']; ?></h4> 
            <p class="help-block"><?php
                echo "<b>";
                _e('Help: ', 'tainacan');
                echo "</b>";
                if ($property['metas']['socialdb_property_help']) {
                    echo $property['metas']['socialdb_property_help'];
                }
                ?></p>
            <!--- Mostra o valor do metadado----->
            <div id="value_<?php echo $property['id']; ?>_<?php echo $object_id; ?>">
            <?php if($property['metas']['value']&&!empty($property['metas']['value'])&&  is_array($property['metas']['value'])): ?>
                <?php foreach ($property['metas']['value'] as $value): ?>
                <p>
                    <?php
                    if (filter_var($value, FILTER_VALIDATE_URL)):
                        echo '<b><a target="_blank" href="' . $value . '" >' . $value . '</a></b>';
                    elseif (filter_var(trim($value), FILTER_VALIDATE_EMAIL)):
                        echo '<b><a target="_blank" href="mailto:' . $value . '">' . $value . '</a></b>';
                    elseif ($value):
                        echo '<b><a style="cursor:pointer;" onclick="wpquery_link_filter(' . "'" . $value . "'" . ',' . $property['id'] . ')">' . $value . '</a></b>';
                    endif;
                    ?> 
                </p> 
                <?php endforeach; ?>
            <?php else: ?>
                   <p><?php  _e('empty field', 'tainacan') ?></p> 
            <?php endif; ?>
            </div>
            <p>
            <!--- Fim: Mostra o valor do metadado----->    
            <!-- Widgets para edicao -->    
                <?php   
                  if(has_action('modificate_single_item_properties_data')){
                       do_action('modificate_single_item_properties_data',$property,$object_id);
                  }else if ($property['type'] === 'text') { ?>     
                    <input style="display: none;" disabled="disabled" value="<?php if ($property['metas']['value']) echo $property['metas']['value'][0]; ?>" type="text" id="single_property_value_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" class="form-control" name="socialdb_property_<?php echo $property['id']; ?>" <?php
                    if (!$property['metas']['socialdb_property_required']): echo 'required="required"';
                    endif;
                    ?>>
                <?php } elseif ($property['type'] === 'textarea') { ?>   
                    <textarea style="display: none;" disabled="disabled" id="single_property_value_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" class="form-control" name="socialdb_property_<?php echo $property['id']; ?>" <?php
                    if (!$property['metas']['socialdb_property_required']): echo 'required="required"';
                    endif;
                    ?>><?php if ($property['metas']['value']) echo $property['metas']['value'][0]; ?>
                    </textarea>
              <?php }elseif ($property['type'] === 'date'&&!has_action('modificate_single_item_properties_data')) { 
                  ?> 
                    <input style="display: none;" 
                           disabled="disabled" 
                           value="<?php if ($property['metas']['value']) echo $property['metas']['value'][0]; ?>" 
                           id="single_property_value_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" 
                           type="text" class="form-control input_date" 
                           name="socialdb_property_<?php echo $property['id']; ?>" 
                           >
              <?php 
              }else{ 
                ?> 
                    <input style="display: none;" disabled="disabled" value="<?php if ($property['metas']['value']) echo $property['metas']['value'][0]; ?>" id="single_property_value_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" type="text" class="form-control" name="socialdb_property_<?php echo $property['id']; ?>" <?php
                    if (!$property['metas']['socialdb_property_required']): echo 'required="required"';
                    endif;
                    ?>>
                       <?php } ?> 
                <input style="display: none;" type="hidden" id="single_property_<?php echo $property['id']; ?>_<?php echo $object_id; ?>_value_before" name="property_<?php echo $property['id']; ?>_<?php echo $object_id; ?>_value_before" value="<?php if (is_array($property['metas']['value'])) echo implode(',', $property['metas']['value']); ?>">
            </p> 
            <!--- Fim: Widgets para edicao----->
            <!-- Fim de mostrar botoes -->
            <button type="button" onclick="cancel_data_property('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>')" id="single_cancel_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" class="btn btn-default btn-xs" style="display: none;" ><span class="glyphicon glyphicon-arrow-left" ></span></button>
            <?php
            // verifico se o metadado pode ser alterado
            if (verify_allowed_action($collection_id, 'socialdb_collection_permission_edit_property_data_value',$object_id)):
                ?>    
                <button type="button" onclick="edit_data_property('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>')" id="single_edit_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" class="btn btn-default btn-xs" ><span class="glyphicon glyphicon-edit"></span></button>
            <?php endif; ?>    
            <button type="button" onclick="save_data_property('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>')" id="single_save_<?php echo $property['id']; ?>_<?php echo $object_id; ?>"class="btn btn-default btn-xs" style="display: none;"><span class="glyphicon glyphicon-floppy-disk"></span></button>
        </div>              
    <?php } ?>
    <?php
endif;

if (isset($property_term)):
    ?>
    <!--h4><?php _e('Term properties', 'tainacan'); ?></h4-->
    <?php
    foreach ($property_term as $property) {
        if (count($property['has_children']) > 0):
            ?>
            <!--div class="form-group"-->
            <div class="bs-callout bs-callout-info">
                <!--label ><?php echo $property['name']; ?></label--> 
                <h4 ><?php echo $property['name']; ?></h4> 
                    <!--button type="button" onclick="cancel_term_property('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>')" id="cancel_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" class="btn btn-default btn-xs" style="display: none;" ><span class="glyphicon glyphicon-arrow-left" ></span></button>
                    <button type="button" onclick="edit_term_property('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>')" id="edit_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" class="btn btn-default btn-xs" ><span class="glyphicon glyphicon-edit"></span></button-->
                    <!--button type="button" onclick="save_term_property('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>')" id="save_<?php echo $property['id']; ?>_<?php echo $object_id; ?>"class="btn btn-default btn-xs" ><span class="glyphicon glyphicon-floppy-disk"></span></button-->
                <p><?php
                    if ($property['metas']['socialdb_property_help']) {
                        echo $property['metas']['socialdb_property_help'];
                    }
                    ?></p>
                <div id="labels_<?php echo $property['id']; ?>_<?php echo $object_id; ?>">
                    <?php echo '<p>' . __('empty field', 'tainacan') . '</p>'; ?>
                </div>
                <div style="display:none;" id="widget_<?php echo $property['id']; ?>_<?php echo $object_id; ?>">
                    <?php
                    if ($property['type'] == 'radio') {
                        $properties_terms_radio[] = $property['id'];
                        ?>
                        <div id='field_event_single_property_term_<?php echo $property['id']; ?>_<?php echo $object_id; ?>'></div>
                        <input type="hidden" value="" name="value_radio_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" id="value_single_radio_<?php echo $property['id']; ?>_<?php echo $object_id; ?>">
                        <?php
                    } elseif ($property['type'] == 'tree') {
                        $properties_terms_tree[] = $property['id'];
                        ?>
                        <div class="row">
                            <div class='col-lg-6'  id='field_event_single_property_term_<?php echo $property['id']; ?>_<?php echo $object_id; ?>' ></div>
                            <select name='socialdb_propertyterm_<?php echo $property['id']; ?>' size='2' class='col-lg-6' id='socialdb_propertyterm_<?php echo $property['id']; ?>_<?php echo $object_id; ?>' ></select>
                            <input type="hidden" value="" name="value_tree_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" id="value_single_tree_<?php echo $property['id']; ?>_<?php echo $object_id; ?>">
                        </div>
                        <?php
                    } elseif ($property['type'] == 'selectbox') {
                        $properties_terms_selectbox[] = $property['id'];
                        ?>
                        <select onchange="get_event_single_select(this,<?php echo $property['id']; ?>,<?php echo $object_id; ?>)" class="form-control" name="socialdb_propertyterm_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" id='field_event_single_property_term_<?php echo $property['id']; ?>_<?php echo $object_id; ?>' ></select>
                        <input type="hidden" value="" name="value_select_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" id="value_single_select_<?php echo $property['id']; ?>_<?php echo $object_id; ?>">
                        <?php
                    } elseif ($property['type'] == 'checkbox') {
                        $properties_terms_checkbox[] = $property['id'];
                        ?>
                        <div id='field_event_single_property_term_<?php echo $property['id']; ?>_<?php echo $object_id; ?>'></div>
                        <?php
                    } elseif ($property['type'] == 'multipleselect') {
                        $properties_terms_multipleselect[] = $property['id'];
                        ?>
                        <select  multiple class="form-control" name="socialdb_propertyterm_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" id='field_event_single_property_term_<?php echo $property['id']; ?>_<?php echo $object_id; ?>' ></select>
                        <?php
                    } elseif ($property['type'] == 'tree_checkbox') {
                        $properties_terms_treecheckbox[] = $property['id'];
                        ?>
                        <div class="row">
                            <div class='col-lg-6'  id='field_event_single_property_term_<?php echo $property['id']; ?>_<?php echo $object_id; ?>'></div>
                            <select onclick="remove_classication('<?php _e('Remove classification') ?>', '<?php _e('Are you sure to remove this classification', 'tainacan') ?>', $(this).val()[0],<?php echo $object_id; ?>, '<?php echo mktime(); ?>')" multiple size='6' class='col-lg-6' name='socialdb_propertyterm_<?php echo $property['id']; ?>[]' id='socialdb_propertyterm_<?php echo $property['id']; ?>_<?php echo $object_id; ?>' ></select>
                        </div>
                    <?php }
                    ?>
                </div>
                <button type="button" onclick="cancel_term_property('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>')" id="single_cancel_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" class="btn btn-default btn-xs" style="display: none;" ><span class="glyphicon glyphicon-arrow-left" ></span></button>
                <?php
                // verifico se o metadado pode ser alterado
                if (verify_allowed_action($collection_id, 'socialdb_collection_permission_add_classification',$object_id)):
                    ?>    
                    <button type="button" onclick="edit_term_property('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>')" id="single_edit_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" class="btn btn-default btn-xs" ><span class="glyphicon glyphicon-edit"></span></button>
                <?php endif; ?>    
                <?php
                echo '</div>';
            endif;
        }
    endif;
    ?>
    <input type="hidden" name="categories_id" id='event_single_object_categories_id_<?php echo $object_id; ?>' value="<?php echo implode(',', $categories_id); ?>">   
    <input type="hidden" name="properties_terms_radio" id='event_single_properties_terms_radio' value="<?php echo implode(',', $properties_terms_radio); ?>">
    <input type="hidden" name="properties_terms_tree" id='event_single_properties_terms_tree' value="<?php echo implode(',', $properties_terms_tree); ?>">
    <input type="hidden" name="properties_terms_selectbox" id='event_single_properties_terms_selectbox' value="<?php echo implode(',', $properties_terms_selectbox); ?>">
    <input type="hidden" name="properties_terms_checkbox" id='event_single_properties_terms_checkbox' value="<?php echo implode(',', $properties_terms_checkbox); ?>">
    <input type="hidden" name="properties_terms_multipleselect" id='event_single_properties_terms_multipleselect' value="<?php echo implode(',', $properties_terms_multipleselect); ?>">
    <input type="hidden" name="properties_terms_treecheckbox" id='event_single_properties_terms_treecheckbox' value="<?php echo implode(',', $properties_terms_treecheckbox); ?>">
    <input type="hidden" id="object_classifications_event_single_<?php echo $object_id; ?>" name="object_classifications" value="<?php echo implode(',', $categories_id); ?>">    



    <?php if (isset($all_ids)): ?>
        <input type="hidden" name="properties_id" value="<?php echo $all_ids; ?>">
        <?php
    
    
    
    
    
    
    
    
    
    
    
    
    
    


 endif; 



