<?php if (isset($property_data)||isset($property_term)||isset($property_object)): ?>
   <div class="panel panel-info">
    <div class="panel-heading">
        <?php _e('Properties','tainacan'); ?>
    </div>
    <div class="panel-body">

        <?php
        include_once ('../../../../../wp-config.php');
        include_once ('../../../../../wp-load.php');
        include_once ('../../../../../wp-includes/wp-db.php');
        include_once ('js/show_insert_object_properties_js.php');
        $properties_terms_radio = [];
        $properties_terms_tree = [];
        $properties_terms_selectbox = [];
        $properties_terms_checkbox = [];
        $properties_terms_multipleselect = [];
        $properties_terms_treecheckbox = [];
        $properties_autocomplete = [];
        ?>

        
        <?php if (isset($property_data)): ?>
            <h4><?php _e('Data properties','tainacan'); ?></h4>
            <?php foreach ($property_data as $property) { $properties_autocomplete[] = $property['id']; ?>
                <div class="form-group col-md-12">   
                        <?php if ($property['type'] == 'text') { ?>   
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="autocomplete_value_<?php echo $property['id']; ?>" name="socialdb_property_<?php echo $property['id']; ?>" placeholder="<?php echo $property['name']; ?>">
                            </div> 
                           <div class="col-md-4">
                                <select class="form-control input-sm" id="advanced_search_property_<?php echo $property['id']; ?>_operation" name="socialdb_property_<?php echo $property['id']; ?>_operation">
                                    <option value="1"><?php _e('Equals','tainacan'); ?></option>
                                    <option value="2"><?php _e('Not equals','tainacan'); ?></option>
                                    <option value="3"><?php _e('Contains','tainacan'); ?></option>
                                    <option value="4"><?php _e('Does not Contain','tainacan'); ?></option>
                                </select>
                           </div>
                        <?php }elseif ($property['type'] == 'textarea') { $properties_autocomplete[] = $property['id']; ?>   
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="autocomplete_value_<?php echo $property['id']; ?>" name="socialdb_property_<?php echo $property['id']; ?>"  placeholder="<?php echo $property['name']; ?>"></textarea>
                            </div> 
                           <div class="col-md-4">
                                <select class="form-control input-sm" id="socialdb_property_<?php echo $property['id']; ?>_operation" name="socialdb_property_<?php echo $property['id']; ?>_operation">
                                    <option value="1"><?php _e('Equals','tainacan'); ?></option>
                                    <option value="2"><?php _e('Not equals','tainacan'); ?></option>
                                    <option value="3"><?php _e('Contains','tainacan'); ?></option>
                                    <option value="4"><?php _e('Does not Contain','tainacan'); ?></option>
                                </select>
                           </div> 
                        <?php }elseif($property['type'] == 'numeric') { ?> 
                            <div class="col-md-8">
                                 <input class="form-control"  placeholder="<?php echo $property['name']; ?>" type="numeric"  name="socialdb_property_<?php echo $property['id']; ?>" >
                            </div>     
                            <div class="col-md-4">
                                <select class="form-control input-sm" id="advanced_search_property_<?php echo $property['id']; ?>_operation" name="socialdb_property_<?php echo $property['id']; ?>_operation">
                                    <option value="1"><?php _e('Equals','tainacan'); ?></option>
                                    <option value="2"><?php _e('Not equals','tainacan'); ?></option>
                                    <option value="3"><?php _e('Higher','tainacan'); ?></option>
                                    <option value="4"><?php _e('Lower','tainacan'); ?></option>
                                </select>
                           </div>
                        <?php }elseif($property['type'] == 'date') { $properties_autocomplete[] = $property['id']; ?> 
                            <div class="col-md-8">
                                 <input class="form-control input_date" id="autocomplete_value_<?php echo $property['id']; ?>"  placeholder="<?php echo $property['name']; ?>" type="text"  name="socialdb_property_<?php echo $property['id']; ?>" >
                            </div>     
                            <div class="col-md-4">
                                <select class="form-control input-sm" id="advanced_search_property_<?php echo $property['id']; ?>_operation" name="socialdb_property_<?php echo $property['id']; ?>_operation">
                                    <option value="1"><?php _e('Equals','tainacan'); ?></option>
                                    <option value="2"><?php _e('Not equals','tainacan'); ?></option>
                                    <option value="3"><?php _e('After','tainacan'); ?></option>
                                    <option value="4"><?php _e('Before','tainacan'); ?></option>
                                </select>
                           </div>
                        <?php } ?> 
                </div>
            <?php } ?>
            <?php
        endif;
        
        
         if((isset($property_term)&&count($property_term)>1)||(count($property_term)==1&&!empty($property_term[0]['has_children']))): 
            ?>
            <h4><?php _e('Term properties','tainacan'); ?></h4>
            <?php foreach ($property_term as $property) { ?>
                <div class="form-group col-md-12">
                        <label ><?php echo $property['name']; ?></label> 
                        <p><?php
                            if ($property['metas']['socialdb_property_help']) {
                                //echo $property['metas']['socialdb_property_help'];
                            }
                            ?></p> 
                        <?php
                        if ($property['type'] == 'radio') {
                            $properties_terms_radio[] = $property['id'];
                            ?>
                            <div id='search_field_property_term_<?php echo $property['id']; ?>'></div>
                            <?php
                        } elseif ($property['type'] == 'tree') {
                              $properties_terms_tree[] = $property['id']; 
                                ?>
                                <div class="row">
                                    <div  style='height: 150px;overflow: scroll;' class='col-lg-6'  id='search_field_property_term_<?php echo $property['id']; ?>'></div>
                                    <select onclick="clear_select_object_property(this)" name='socialdb_propertyterm_<?php echo $property['id']; ?>' size='2' class='col-lg-6' id='search_socialdb_propertyterm_<?php echo $property['id']; ?>' ></select>
                               </div>
                               <?php
                        } elseif ($property['type'] == 'selectbox') {
                            $properties_terms_selectbox[] = $property['id'];
                            ?>
                            <select class="form-control" name="socialdb_property_<?php echo $property['id']; ?>" id='search_field_property_term_<?php echo $property['id']; ?>' <?php
                            
                            ?>></select>
                                    <?php
                                }elseif ($property['type'] == 'checkbox') {
                                    $properties_terms_checkbox[] = $property['id'];
                                    ?>
                            <div id='search_field_property_term_<?php echo $property['id']; ?>'></div>
                            <?php
                        } elseif ($property['type'] == 'multipleselect') {
                            $properties_terms_multipleselect[] = $property['id'];
                            ?>
                             <select multiple class="form-control" name="socialdb_propertyterm_<?php echo $property['id']; ?>" id='search_field_property_term_<?php echo $property['id']; ?>' ></select>
                            <?php
                        } elseif ($property['type'] == 'tree_checkbox') {
                            $properties_terms_treecheckbox[] = $property['id']; 
                            ?>
                           <div class="row">
                                <div style='height: 150px;overflow: scroll;' class='col-lg-6'  id='search_field_property_term_<?php echo $property['id']; ?>'></div>
                                <select onclick="clear_select_object_property(this)" multiple size='6' class='col-lg-6' name='socialdb_propertyterm_<?php echo $property['id']; ?>[]' id='search_socialdb_propertyterm_<?php echo $property['id']; ?>' <?php if($property['metas']['socialdb_property_required']=='true'): echo 'required="required"'; endif; ?>></select>
                           </div>
                           <?php
                        }
                        ?> 
                  </div> 
            <?php } ?>
        <?php endif;
        ?>

        <?php if (isset($property_object)):
            ?>
            <h4><?php _e('Object Properties','tainacan'); ?></h4>
            <?php foreach ($property_object as $property) { ?>
                <?php //if($property['metas']['socialdb_property_object_is_facet']=='false'):  ?>
                <div class="form-group col-md-12">
                    <label for="object_tags"><?php echo $property['name']; ?></label>
                    <a target="_blank" class="btn btn-primary btn-xs" href="<?php echo get_permalink($property['metas']['collection_data'][0]->ID); ?>"><?php _e('See the collection','tainacan'); ?><?php echo ' ' . $property['metas']['collection_data'][0]->post_title; ?></a>
                    <input type="text" onkeyup="autocomplete_object_property_add('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>');" id="autocomplete_value_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" placeholder="<?php _e('Type the three first letters of the item of this collection ','tainacan'); ?>"  class="chosen-selected form-control"  />  
                    <select onclick="clear_select_object_property(this);" id="property_value_<?php echo $property['id']; ?>_<?php echo $object_id; ?>_add" multiple class="chosen-selected2 form-control" style="height: auto;" name="socialdb_property_<?php echo $property['id']; ?>[]"
                    >
                                <?php if (!empty($property['metas']['objects'])) { ?>     

                        <?php } else { ?>   
                            <option value=""><?php _e('No objects added in this collection','tainacan'); ?></option>
                        <?php } ?>       
                    </select>
                 </div>
                <?php // endif; ?>
            <?php } ?>
        <?php endif; ?>
        <input type="hidden" name="search_properties_autocomplete" id='search_properties_autocomplete' value="<?php echo implode(',', $properties_autocomplete); ?>">
        <input type="hidden" name="properties_terms_radio" id='search_properties_terms_radio' value="<?php echo implode(',', $properties_terms_radio); ?>">
        <input type="hidden" name="properties_terms_tree" id='search_properties_terms_tree' value="<?php echo implode(',', $properties_terms_tree); ?>">
        <input type="hidden" name="properties_terms_selectbox" id='search_properties_terms_selectbox' value="<?php echo implode(',', $properties_terms_selectbox); ?>">
        <input type="hidden" name="properties_terms_checkbox" id='search_properties_terms_checkbox' value="<?php echo implode(',', $properties_terms_checkbox); ?>">
        <input type="hidden" name="properties_terms_multipleselect" id='search_properties_terms_multipleselect' value="<?php echo implode(',', $properties_terms_multipleselect); ?>">
        <input type="hidden" name="properties_terms_treecheckbox" id='search_properties_terms_treecheckbox' value="<?php echo implode(',', $properties_terms_treecheckbox); ?>">
        <?php if (isset($all_ids)): ?>
            <input type="hidden" name="properties_id" value="<?php echo $all_ids; ?>">
        <?php endif; ?>
    


    </div>
</div>
<?php endif; ?>