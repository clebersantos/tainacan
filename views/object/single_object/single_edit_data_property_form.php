<?php
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/edit_data_property_form_js.php'); ?>
<h4><?php echo __('Editing the property: ','tainacan').$value->name; ?></h4>
<form  id="single_submit_form_edit_property_data">
    <input type="hidden" name="object_id"  value="<?php echo $category->term_id; ?>">
    <input type="hidden" name="property_category_id"  value="<?php echo $category->term_id; ?>">
    <div class="create_form-group">
        <label for="event_edit_property_data_name"><?php _e('Property data name','tainacan'); ?></label>
        <input type="text" class="form-control" id="single_event_edit_property_data_name" name="socialdb_event_property_data_edit_name" required="required" value="<?php echo $value->name; ?>" placeholder="<?php _e('Property Data name','tainacan'); ?>">
    </div>
    <div class="form-group">
        <label for="event_edit_property_data_widget"><?php _e('Property data widget','tainacan'); ?></label>
        <select class="form-control" id="single_event_edit_property_data_widget" name="socialdb_event_property_data_edit_widget">
            <option <?php if($value->type=='text') echo 'selected="selected";' ?> value="text"><?php _e('Text','tainacan'); ?></option>
            <option <?php if($value->type=='textarea') echo 'selected="selected";' ?> value="textarea"><?php _e('Textarea','tainacan'); ?></option>
            <option <?php if($value->type=='date') echo 'selected="selected";' ?> value="date"><?php _e('Date','tainacan'); ?></option>
            <option <?php if($value->type=='numeric') echo 'selected="selected";' ?> value="numeric"><?php _e('Numeric','tainacan'); ?></option>
            <option <?php if($value->type=='autoincrement') echo 'selected="selected";' ?> value="autoincrement"><?php _e('Auto-Increment','tainacan'); ?></option>
        </select>
    </div>
    <!--div class="form-group">
        <label for="event_edit_property_data_column_ordenation"><?php _e('Property data column ordenation','tainacan'); ?></label>
        <input type="radio" <?php if($value->metas->socialdb_property_data_column_ordenation=='true') echo 'checked="checked";' ?> name="socialdb_event_property_data_edit_ordenation_column" id="single_event_edit_property_data_column_ordenation_true" value="true">&nbsp;<?php _e('Yes','tainacan'); ?>
        <input type="radio" <?php if($value->metas->socialdb_property_data_column_ordenation=='false') echo 'checked="checked";' ?> name="socialdb_event_property_data_edit_ordenation_column" id="single_event_edit_property_data_column_ordenation_false" value="false">&nbsp;<?php _e('No','tainacan'); ?>
    </div-->
    <div class="form-group">
        <label for="event_edit_property_data_required"><?php _e('Property data required','tainacan'); ?></label>
        <input type="radio" <?php if($value->metas->socialdb_property_required=='true') echo 'checked="checked";' ?> name="socialdb_event_property_data_edit_required" id="single_event_edit_property_data_required_true" value="true">&nbsp;<?php _e('Yes','tainacan'); ?>
        <input type="radio" <?php if($value->metas->socialdb_property_required=='false') echo 'checked="checked";' ?> name="socialdb_event_property_data_edit_required" id="single_event_edit_property_data_required_false"  value="false">&nbsp;<?php _e('No','tainacan'); ?>
    </div>
    <input type="hidden" id="single_event_edit_property_data_collection_id" name="socialdb_event_collection_id" value="<?php echo $collection_id; ?>">
    <input type="hidden" id="single_event_edit_property_data_object_id" name="property_data_object_id" value="<?php echo $object_id; ?>">
    <input type="hidden" id="single_event_edit_property_data_create_time" name="socialdb_event_create_date" value="<?php echo mktime(); ?>">
    <input type="hidden" id="single_event_edit_property_data_user_id" name="socialdb_event_user_id" value="<?php echo get_current_user_id(); ?>">
    
    <input type="hidden" id="single_event_edit_property_data_id" name="socialdb_event_property_data_edit_id" value="<?php echo $value->id ?>">
    <input type="hidden" id="operation_property_data" name="operation" value="add_event_property_data_edit">
    <button type="submit" id="submit_property_data" class="btn btn-default"><?php _e('Submit','tainacan'); ?></button>
    <button type="button" onclick="back_button_single('<?php echo $object_id; ?>')" class="btn btn-default" id="clear_categories"><?php _e('Clear','tainacan'); ?></button>
</form>