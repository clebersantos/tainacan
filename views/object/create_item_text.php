<?php
    include_once ('../../../../../wp-config.php');
    include_once ('../../../../../wp-load.php');
    include_once ('../../../../../wp-includes/wp-db.php');
    include_once ('js/create_item_text_js.php');
    /**
     * 
     * View utilizado para criar um item do tipo texto, utiliza os containers 
     * list_properties-accordion, list_ranking_create e show_insert_object_licenses
     * 
     * 
     */    
?>
<form  id="submit_form">
    <input type="hidden" id="object_id_add" name="object_id" value="<?php echo $object_id ?>">
    <input type="hidden" id="object_from" name="object_from" value="internal">
    <input type="hidden" id="object_type" name="object_type" value="text">
    <div class="row" style="background-color: #f1f2f2">
        <div class="col-md-3 menu_left_loader">
             <center>
                    <img src="<?php echo get_template_directory_uri() . '/libraries/images/catalogo_loader_725.gif' ?>">
                    <h4><?php _e('Loading metadata...', 'tainacan') ?></h4>
             </center>
        </div>
        <div style="display: none; background: white;border: 3px solid #E8E8E8;font: 11px Arial;" class="col-md-3 menu_left">
            <div class="expand-all-item btn white tainacan-default-tags">
                <div class="action-text" 
                     style="display: inline-block">
                         <?php _e('Expand all', 'tainacan') ?></div>
                &nbsp;&nbsp;<span class="glyphicon-triangle-bottom white glyphicon"></span>
            </div>
            <div id="text_accordion" class="multiple-items-accordion">
            <!-- TAINACAN: thumbnail do item -->
            <div id="thumbnail_id" <?php do_action('item_thumbnail_attributes') ?>>
                <h2> 
                    <?php _e('Item Thumbnail','tainacan'); ?><?php do_action('optional_message') ?>
                    <a class="pull-right" 
                       style="margin-right: 20px;" 
                       >
                        <span  title="<?php _e('Insert a thumbnail in your item!','tainacan'); ?>" 
                       data-toggle="tooltip" data-placement="bottom" class="glyphicon glyphicon-question-sign"></span>
                    </a>
                </h2>
                <div class="form-group" >
                        <input type="hidden" name="thumbnail_url" id="thumbnail_url" value="">
                        <div id="image_side_create_object">
                        </div>
                        <input type="file"
                               id="object_thumbnail"
                               name="object_thumbnail"
                               class="form-control">  
                </div>
            </div>    
            <!-- TAINACAN: a fonte do item -->
            <div id="socialdb_object_dc_source"  <?php do_action('item_source_attributes') ?>>
                <h2> <?php _e('Item Source','tainacan'); ?>
                     <a class="pull-right" 
                       style="margin-right: 20px;" 
                        >
                        <span title="<?php _e('What\'s the item source','tainacan'); ?>" 
                       data-toggle="tooltip" data-placement="bottom" class="glyphicon glyphicon-question-sign"></span>
                    </a>
                </h2>
                 <div class="form-group" >
                    <input
                           type="text"
                           id="object_source"
                           class="form-control"
                           name="object_source"
                           placeholder="<?php _e('What\'s the item source','tainacan'); ?>"
                           value="" >
                  </div>   
            </div>
            <!-- TAINACAN: a descricao do item -->
            <div id="post_content" >
                <h2>
                    <?php _e('Item Description','tainacan'); ?>
                    <a class="pull-right" 
                       style="margin-right: 20px;" 
                        >
                        <span title="<?php _e('Describe your item','tainacan'); ?>" 
                       data-toggle="tooltip" data-placement="bottom" class="glyphicon glyphicon-question-sign"></span>
                    </a>
                </h2>
                <div class="form-group" >
                    <textarea class="form-control" 
                              rows="8"
                              id="object_description_example" 
                              placeholder="<?php _e('Describe your item','tainacan'); ?>"
                              name="object_description" ></textarea>
                </div>
            </div>
            <!-- TAINACAN: tags do item -->
            <div id="tag" <?php do_action('item_tags_attributes') ?>>
                <h2><?php _e('Object tags','tainacan'); ?>
                    <a class="pull-right" 
                       style="margin-right: 20px;" 
                       >
                        <span  title="<?php _e('The set of tags may be inserted by comma','tainacan') ?>" 
                       data-toggle="tooltip" data-placement="bottom" class="glyphicon glyphicon-question-sign"></span>
                    </a>
                </h2>
                <div class="form-group" >
                    <input type="text" 
                           class="form-control" 
                           id="object_tags" 
                           name="object_tags"  
                           placeholder="<?php _e('The set of tags may be inserted by comma','tainacan') ?>">
                 </div>
            </div>
            <!-- TAINACAN: a propriedades do item -->
            <div id="show_form_properties">
                <center>
                    <img src="<?php echo get_template_directory_uri() . '/libraries/images/catalogo_loader_725.gif' ?>">
                    <h4><?php _e('Loading Properties...', 'tainacan') ?></h4>
                </center>
            </div>
            <!-- TAINACAN: a licencas do item -->
            <div id="list_licenses_items">
                <h2>
                    <?php _e('Licenses','tainacan'); ?>
                    <a class="pull-right" 
                       style="margin-right: 20px;" 
                       >
                        <span  title="<?php _e('Licenses available for this item','tainacan') ?>" 
                       data-toggle="tooltip" data-placement="bottom" class="glyphicon glyphicon-question-sign"></span>
                    </a>
                    <a id='required_field_license' style="padding: 3px;margin-left: -30px;" >
                                <span class="glyphicon glyphicon glyphicon-star" title="<?php echo __('This metadata is required!','tainacan')?>" 
                               data-toggle="tooltip" data-placement="top" ></span>
                    </a>
                    <a id='ok_field_license'  style="display: none;padding: 3px;margin-left: -30px;" >
                            <span class="glyphicon  glyphicon-ok-circle" title="<?php echo __('Field filled successfully!','tainacan')?>" 
                           data-toggle="tooltip" data-placement="top" ></span>
                    </a>
                    <input type="hidden" 
                                 id='core_validation_license' 
                                 class='core_validation' 
                                 value='false'>
                    <input type="hidden" 
                                 id='core_validation_license_message'  
                                 value='<?php echo sprintf(__('The field license is required','tainacan'),$property['name']); ?>'>
                </h2>
                <div id="show_form_licenses"></div>
             </div>   
             <!-- TAINACAN: votacoes do item -->
             <div id="create_list_ranking_<?php echo $object_id ?>"></div>
            </div>
        </div>
        <div style=" background: white;border: 3px solid #E8E8E8;margin-left: 15px;width: 74%;" class="col-md-9">
            <h3>
                <?php if(has_action('label_add_item')): ?>
                       <?php do_action('label_add_item',$object_name) ?>
                <?php else: ?>
                      <?php _e('Create new item - Write text','tainacan') ?>
                <?php endif; ?>
                <button type="button" onclick="back_main_list();"class="btn btn-default pull-right">
                    <b><?php _e('Back','tainacan') ?></b>
                </button>
            </h3>
            <hr>
                <div class="form-group">
                    <label for="object_name"><?php _e('Item name','tainacan'); ?></label>
                    <input class="form-control" required="required" type="text"  id="object_name" name="object_name"  placeholder="<?php _e('Item name','tainacan'); ?>">
                </div>
               <!-- TAINACAN: Campo com o ckeditor para items do tipo texto -->
               <div id="object_content_text" class="form-group" <?php do_action('item_content_attributes') ?>>
                    <label for="object_editor"><?php _e('Item Content','tainacan'); ?></label>
                    <textarea class="form-control" id="object_editor" name="object_editor" placeholder="<?php _e('Object Content','tainacan'); ?>">
                    </textarea>
                </div>
                <!-- TAINACAN: UPLOAD DE ANEXOS DOS ITEMS -->
                <div class="form-group">
                    <label for="attachments"><?php _e('Item Attachments','tainacan'); ?></label>
                    <div <?php do_action('item_attachments_attributes') ?> 
                        id="dropzone_new" <?php ($socialdb_collection_attachment=='no') ? print_r('style="display:none"') : '' ?> 
                        class="dropzone"
                        style="margin-bottom: 15px;min-height: 150px;padding-top: 0px;">
                            <div class="dz-message" data-dz-message>
                             <span style="text-align: center;vertical-align: middle;">
                                 <h2>
                                     <span class="glyphicon glyphicon-upload"></span>
                                     <b><?php _e('Drop Files','tainacan')  ?></b> 
                                         <?php _e('to upload','tainacan')  ?>
                                 </h2>
                                 <h4>(<?php _e('or click','tainacan')  ?>)</h4>
                             </span>
                         </div>
                    </div>
                </div>    
                <input type="hidden" id="object_classifications" name="object_classifications" value="">
                <input type="hidden" id="object_content" name="object_content" value="">
                <input type="hidden" id="create_object_collection_id" name="collection_id" value="">
                <input type="hidden" id="operation" name="operation" value="add">
                <!--button onclick="back_main_list();" style="margin-bottom: 20px;"  class="btn btn-default btn-lg pull-left"><b><?php _e('Back','tainacan') ?></b></button-->
                <button type="button" onclick="back_main_list();" style="margin-bottom: 20px;color" class="btn btn-default btn-lg pull-left"><?php _e('Cancel','tainacan'); ?></button>
                <div id="submit_container">
                    <button type="submit" id="submit" style="margin-bottom: 20px;" class="btn btn-success btn-lg pull-right send-button"><?php _e('Submit','tainacan'); ?></button>
                </div>  
                <div id="submit_container_message" style="display: none;">
                     <button type="button" onclick="show_message()" style="margin-bottom: 20px;" class="btn btn-success btn-lg pull-right send-button"><?php _e('Submit','tainacan'); ?></button>
                </div>  
        </div>
    </div> 
</form>
    