<?php
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/edit_maping_attributes_js.php');
?>
<div class='panel panel-default'>
    <div id="importForm" class='panel-body'>
        <form id="form_import_social"> 
        <div class="form-group">
            <h2><?php echo __('Edit Social Mapping','tainacan').' - '.ucfirst($social_network); ?></h2><br>
            <input type='hidden' id='mapping_id' name="mapping_id" value='<?php echo $mapping_id; ?>'>
            <input type='hidden' id='social_network_term' name="social_network_term" value='<?php echo $term; ?>'>
            <input type='hidden' id='social_network' name="social_network" value='<?php echo $social_network; ?>'>
        </div>
        <?php  foreach ($fields as $field) { ?>
            <div class='row form-group'>
                <label class='col-md-5'>
                    <?php echo $field['name_socialdb_entity'] ?>
                </label>    
                <div class='col-md-5'>
                   <select name='<?php echo $field['socialdb_entity'] ?>' class='data form-control' id='<?php echo $field['socialdb_entity'] ?>'>

                   </select>
                </div>	
                 <!--div class='col-md-4'>
                     <input type="text" class='form-control' placeholder="<?php _e('Set the qualifier (Optional)','tainacan') ?>" name="qualifier_socialdb_<?php echo $field['socialdb_entity'] ?>" value="">
                </div-->
            </div>		
        <?php } ?>
        </form> 
    </div>
    <button id="cancel_button_import" class="btn btn-default" onclick="cancel_export()"><?php echo __('Cancel','tainacan'); ?></button>
    <button id="submit_button_import" class="btn btn-primary" onclick="update_mapping()"><?php echo __('Update','tainacan'); ?></button>
  