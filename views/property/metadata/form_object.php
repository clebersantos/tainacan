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
                         <div class="form-group category-fit-column" style="display: inline-block; width: 59%">
                            <label for="property_term_required" style="display: block"><?php _e('Elements Quantity:','tainacan'); ?></label>
                            <input type="radio" name="socialdb_property_object_cardinality" id="socialdb_property_object_cardinality_1"  value="1">&nbsp;<?php _e('Unic value','tainacan') ?>
                            <input type="radio" name="socialdb_property_object_cardinality" id="socialdb_property_object_cardinality_n" checked="checked" value="n">&nbsp;<?php _e('Multiple values','tainacan') ?>
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

                        <hr class="hr-style">
                    </div>
                    <div class="form-group">
                        <label for="use-filter"><?php _e('Use as a filter','tainacan'); ?></label>
                        <input type="checkbox" value="use_filter" name="property_data_use_filter" class="property_data_use_filter" />
                    </div>

                    <div class="form-group data-widget" style="display: none;">
                        <label for="search_data_widget"><?php _e('Filter type','tainacan'); ?></label>
                        <select name="search_data_widget" id="search_data_widget" class="form-control"  data-type="socialdb_property_object"
                                onchange="select_tree_color('#meta-relationship')" >
                            <option value="tree"><?php _e('Tree','tainacan') ?></option>
                        </select>

                        <?php echo $view_helper->render_tree_colors(); ?>

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