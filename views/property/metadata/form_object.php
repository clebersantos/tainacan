<div id="meta-relationship" class="modal fade" role="dialog" aria-labelledby="Relationship">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> <?php _e('Add Property', 'tainacan') ?> - <?php _e('Relationship', 'tainacan') ?> </h4>
            </div>
            <div class="modal-body">
                <form id="submit_form_property_object">

                    <div class="metadata-common-fields">

                        <div class="create_form-group">
                            <label for="property_object_name"><?php _e('Property name','tainacan'); ?></label>
                            <input type="text" class="form-control" id="property_object_name" name="property_object_name" required="required" placeholder="<?php _e('Property Object name','tainacan'); ?>">
                        </div>
                        <div class="form-group">
                            <?php if (isset($is_root) && $is_root): ?>
                                <label for="property_object_category_id"><?php _e('Property relationship','tainacan'); ?></label>
                                <select class="form-control" id="property_object_category_id" name="property_object_category_id">
                                    <?php foreach ($property_object as $object) { ?>
                                        <option value="<?php echo $object['category_id'] ?>"><?php echo $object['collection_name'] ?></option>
                                    <?php } ?>
                                </select>
                            <?php else: ?>
                                <label for="property_object_category_id"><?php _e('Property object relationship','tainacan'); ?></label>
                                <input disabled="disabled" type="text" class="form-control" id="property_object_category_name" value="" placeholder="<?php _e('Click on the category in the tree','tainacan'); ?>" name="property_object_category_name" >
                                <input type="hidden"  id="property_object_category_id"  name="property_object_category_id" value="<?php echo $category->term_id; ?>" >
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="property_object_required"><?php _e('Property object required','tainacan'); ?></label>
                            <input type="radio" name="property_object_required" id="property_object_required_true" value="true">&nbsp;<?php _e('Yes','tainacan'); ?>
                            <input type="radio" name="property_object_required" id="property_object_required_false" checked="checked" value="false">&nbsp;<?php _e('No','tainacan'); ?>
                        </div>
                        <div class="form-group">
                            <label for="property_object_is_reverse"><?php _e('Property object reverse','tainacan'); ?></label>
                            <input type="radio" name="property_object_is_reverse" id="property_object_is_reverse_true" value="true">&nbsp;<?php _e('Yes','tainacan'); ?>
                            <input type="radio" name="property_object_is_reverse" id="property_object_is_reverse_false" checked="checked" value="false">&nbsp;<?php _e('No','tainacan'); ?>
                        </div>
                        <div id="show_reverse_properties" class="form-group" style="display: none;">
                            <label for="property_object_reverse"><?php _e('Select the reverse property','tainacan'); ?></label>
                            <select class="form-control" id="property_object_reverse" name="property_object_reverse">
                            </select>
                        </div>

                        <?php /*
                        <div class="form-group">
                            <label for="socialdb_property_term_widget"><?php _e('Property Term Widget','tainacan'); ?></label>
                            <select class="form-control" id="socialdb_property_term_widget" name="socialdb_property_term_widget">
                            </select>
                        </div>
                        */ ?>

                        <hr style="border: 0; height: 1px; background: #333; background-image: linear-gradient(to right, #ccc, #333, #ccc);">
                    </div>
                    <div class="form-group">
                        <label for="use-filter"><?php _e('Use as a filter','tainacan'); ?></label>
                        <input type="checkbox" value="use_filter" name="property_data_use_filter" class="property_data_use_filter" />
                    </div>

                    <div class="form-group data-widget" style="display: none;">
                        <label for="search_data_widget"><?php _e('Filter type','tainacan'); ?></label>
                        <select name="search_data_widget" id="search_data_widget" onchange="select_tree_color('#meta-relationship')" class="form-control" onfocus="get_metadata_widgets('socialdb_property_object');">
                            <option value="select"><?php _e('Select','tainacan') ?></option>
                        </select>
                        <div id="color_field_property_search" style="display: none;">
                            <h5><strong><?php _e('Set the facet color','tainacan'); ?></strong></h5>
                            <div class="form-group">
                                <?php for ($i = 1; $i < 14; $i++) {
                                    echo '<label class="radio-inline"> <input type="radio" class="color_property" name="color_facet" id="color_property' . $i . '" value="color_property' . $i . '" ';
                                    echo '><img src="' . get_template_directory_uri() . '/libraries/images/cor' . $i . '.png">  </label>';
                                }; ?>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="property_object_collection_id" name="collection_id" value="">
                    <input type="hidden" id="property_object_id" name="property_object_id" value="">
                    <input type="hidden" id="operation_property_object" name="operation" value="add_property_object">
                    <input type="hidden" name="property_category_id"  value="<?php echo $category->term_id; ?>">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left close-modal" data-dismiss="modal"><?php _e('Cancel','tainacan') ?></button>
                <button type="submit" class="btn btn-primary action-continue" form="submit_form_property_object">
                    <?php _e('Continue','tainacan') ?>
                </button>
<!--                <button type="button" onclick="clear_buttons()" class="btn btn-default" id="clear_categories">--><?php //_e('New','tainacan'); ?><!--</button>-->
            </div>
        </div>
    </div>
</div>