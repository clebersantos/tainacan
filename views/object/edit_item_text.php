<?php
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/edit_item_text_js.php');

 /**
* 
* View utilizado para EDITAR um item do tipo texto, utiliza os containers 
* list_properties-accordion, list_ranking_create e show_insert_object_licenses
* 
* 
*/   
$tags_name = [];
if(isset($tags)){
    foreach ($tags as $tag) {
        $tags_name[] = get_term_by('id',$tag,'socialdb_tag_type')->name; 
    }
}
$fields = ['text','video','image','pdf','audio'];
$item_attachments = get_posts( ['post_type' => 'attachment', 'exclude' => get_post_thumbnail_id( $object->ID ), 'post_parent' => $object->ID ] );
?>
<form  id="submit_form_edit_object">
    <input type="hidden" id="object_id_edit" name="object_id" value="<?= $object->ID ?>">
    <div class="row" style="background-color: #f1f2f2">
        <div class="col-md-3 menu_left_loader">
             <center>
                    <img src="<?php echo get_template_directory_uri() . '/libraries/images/catalogo_loader_725.gif' ?>">
                    <h4><?php _e('Loading metadata...', 'tainacan') ?></h4>
             </center>
        </div>
<!----------------- MENU ESQUERDO  ----------------->        
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
                    <div id="existent_thumbnail">
                        <?php
                        if (get_the_post_thumbnail($object->ID, 'thumbnail')) {
                            echo get_the_post_thumbnail($object->ID, 'thumbnail');
                            ?>
                            <br><br>
                            <label for="remove_thumbnail"><?php _e('Remove Thumbnail', 'tainacan'); ?></label>
                            <input type="hidden" name="object_has_thumbnail" value="true">
                            <input type="checkbox"  id="remove_thumbnail_object" name="remove_thumbnail_object" value="true">
                            <br><br>
                            <?php } else {
                            ?> 
                            <input type="hidden" name="object_has_thumbnail" value="false">
                            <img height="150" src="<?php echo get_item_thumbnail_default($object->ID); ?>"><br><br>
                        <?php } ?>
                    </div>     
                    <div id="image_side_edit_object">
                    </div>
                    <input type="file" size="50" id="object_thumbnail_edit" name="object_thumbnail" class="btn btn-default btn-sm">  
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
                           placeholder="<?php _e('Where your object come from','tainacan'); ?>"
                           value="<?php echo $socialdb_object_dc_source;  ?>" >  
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
                               id="object_description_example" 
                               name="object_description" ><?php echo $object->post_content; ?></textarea>
                </div>
            </div>
            <!-- TAINACAN: tags do item -->
            <div id="tag"  <?php do_action('item_tags_attributes') ?>>
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
                          value="<?= implode(',', $tags_name) ?>" 
                          placeholder="<?php _e('The set of tags may be inserted by comma','tainacan') ?>">
                 </div>
            </div>
            <!-- TAINACAN: a propriedades do item -->  
            <div id="show_form_properties_edit">
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
             <div id="update_list_ranking_<?php echo $object->ID ?>"></div>
            </div>
        </div>
<!-----------------  FIM - MENU ESQUERDO -------------------------------------->
<!----------------- CONTAINER MAIOR - NOME,CONTEUDO E ANEXOS  ----------------->
        <div style=" background: white;border: 3px solid #E8E8E8;margin-left: 15px;width: 74%;" class="col-md-9">
            <h3>
                <?php if(has_action('label_edit_item')): ?>
                       <?php do_action('label_edit_item',$object_name) ?>
                <?php else: ?>
                       <?php _e('Edit item','tainacan'); ?>
                <?php endif; ?>
                <button type="button" onclick="back_main_list();"class="btn btn-default pull-right">
                    <b><?php _e('Back','tainacan') ?></b>
                </button>
            </h3>
            <hr>
                <div class="form-group">
                      <label for="object_name"><?php _e('Item name','tainacan'); ?></label>
                      <input type="text" class="form-control" name="object_name" id="object_name_edit" value="<?= $object->post_title ?>">
                </div>
                <!-- Tainacan: type do objeto -->
                <div class="form-group" <?php do_action('item_from_attributes') ?>>
                    <label for="object_name"><?php _e('Item type','tainacan'); ?></label><br>
                    <input type="radio" 
                           onchange="edit_show_other_type_field(this)" 
                           name="object_type" 
                           <?php if($socialdb_object_dc_type=='text'): echo 'checked="checked"'; endif;  ?>
                           value="text" 
                           required>&nbsp;<?php _e('Text','tainacan'); ?>
                    <input type="radio" 
                           name="object_type"
                           <?php if($socialdb_object_dc_type=='video'): echo 'checked="checked"'; endif;  ?>
                           id="video_type"
                           onchange="edit_show_other_type_field(this)" 
                           value="video" required>&nbsp;<?php _e('Video','tainacan'); ?>
                    <input type="radio" 
                           onchange="edit_show_other_type_field(this)" 
                           name="object_type" 
                           <?php if($socialdb_object_dc_type=='image'): echo 'checked="checked"'; endif;  ?>
                           value="image" required>&nbsp;<?php _e('Image','tainacan'); ?>
                    <input type="radio" 
                           onchange="edit_show_other_type_field(this)" 
                           name="object_type" 
                            <?php if($socialdb_object_dc_type=='pdf'): echo 'checked="checked"'; endif;  ?>
                           value="pdf" required>&nbsp;<?php _e('PDF','tainacan'); ?>
                    <input type="radio" 
                           name="object_type" 
                           <?php if($socialdb_object_dc_type=='audio'): echo 'checked="checked"'; endif;  ?>
                           onchange="edit_show_other_type_field(this)" 
                           value="audio" required>&nbsp;<?php _e('Audio','tainacan'); ?>
                    <input type="radio"
                           onchange="edit_show_other_type_field(this)" 
                           <?php if(!in_array($socialdb_object_dc_type, $fields)): echo 'checked="checked"'; endif;  ?>
                           name="object_type" 
                           value="other"  required>&nbsp;<?php _e('Other','tainacan'); ?>
                    <!--  TAINACAN:  Field extra para outro formato -->
                    <input <?php if(!in_array($socialdb_object_dc_type, $fields)): echo 'style="display:block"';else:echo 'style="display:none"'; endif;  ?>
                           type="text" 
                           id="object_type_other" 
                           name="object_type_other" 
                           value="<?php if(!in_array($socialdb_object_dc_type, $fields)): echo $socialdb_object_dc_type; else: echo ''; endif; ?>" >
                    <br>
                </div>
                <!-- Tainacan: se o item eh importado ou uploaded -->
                <div id="thumb-idea-form" <?php do_action('item_from_attributes') ?>>
                    <label for="object_thumbnail">
                        <?php _e('Item content','tainacan'); ?>
                    </label><br>
                    <input type="radio" 
                           name="object_from" 
                           id="external_option"
                           onchange="edit_toggle_from(this)" 
                           <?php if($socialdb_object_from=='external'): echo 'checked="checked"'; endif;  ?>
                           value="external" required>&nbsp;<?php _e('Web Address','tainacan'); ?>
                     <!-- TAINACAN: seleciona se o objeto eh interno -->
                    <input type="radio"
                           id="internal_option"
                           onchange="edit_toggle_from(this)" 
                           <?php if($socialdb_object_from=='internal'): echo 'checked="checked"'; endif;  ?>
                           name="object_from" 
                           value="internal"  required>&nbsp;<?php _e('Local','tainacan'); ?>
                    
                    
                        <!--  TAINACAN: Campo para importacao de noticias ou outros item VIA URL do tipo texto -->
                        <div style="display:<?php if($socialdb_object_from=='external'&&$socialdb_object_dc_type=='text'): echo 'block';else: echo 'none'; endif;  ?>;
                            padding-top: 10px;" 
                            id="object_url_text" 
                            class="input-group">
                            <!-- Tainacan: input para url do tipo texto para importacao de noicias e outros sites -->
                            <input onkeyup="edit_set_source(this)" 
                                   type="text" 
                                   id="url_object_edit" 
                                   value="<?php echo $socialdb_object_content;  ?>"
                                   class="form-control input-medium placeholder"  
                                   placeholder="<?php _e('Type/paste the URL and click in the button import','tainacan'); ?>" 
                                   name="object_url"  >
                            <!-- Tainacan: botao para realizar a importacao -->
                            <span class="input-group-btn">
                                <button onclick="import_object_edit()" class="btn btn-primary" type="button"><?php _e('Import','tainacan'); ?></button>
                            </span>
                        </div> 
                        <!-- TAINACAN: Campo para importacao de outros arquivos via url -->
                        <div id="object_url_others" style="display: <?php if($socialdb_object_from=='external'&&$socialdb_object_dc_type!='text'): echo 'block';else: echo 'none'; endif;  ?>;padding-top: 10px;" >
                            <input type="text" 
                                   onkeyup="edit_set_source(this)"
                                   id="object_url_others_input" 
                                   placeholder="<?php _e('Type/paste the URL','tainacan'); ?>"
                                   class="form-control"
                                   name="object_url" 
                                   value="<?php echo $socialdb_object_content;  ?>" >  
                        </div>
                   
                      <!-- TAINACAN: input file para fazer o upload de arquivo --> 
                     <input style="display: <?php if($socialdb_object_from=='internal'&&$socialdb_object_dc_type!='text'): echo 'block';else: echo 'none'; endif;  ?>;padding-top: 10px;" 
                            type="file" size="50" 
                            id="object_file" 
                            name="object_file" 
                            class="btn btn-default btn-sm">
                      <?php 
                      // mostra o link para o content atual do item
                      if($socialdb_object_dc_type!='text'&&$socialdb_object_from=='internal'):
                          echo '<h4>'.__('Actual Item Content','tainacan').'</h4>';
                         echo get_post($socialdb_object_content)->post_title."<br>";
                          echo wp_get_attachment_link($socialdb_object_content, 'thumbnail', false, true);
                      endif;   
                       ?>
                    <br>
                    <br>
                </div> 
                <div id='wrap_content' <?php do_action('item_content_attributes') ?>>
                    <div id="object_content_text_edit" style="display:<?php if($socialdb_object_dc_type=='text'): echo 'block';else: echo 'none'; endif;  ?>;" class="form-group">
                            <label for="object_editor"><?php _e('Item Content','tainacan'); ?></label>
                            <textarea class="form-control" id="objectedit_editor" name="objectedit_editor" placeholder="<?php _e('Object Content','tainacan'); ?>">
                            <?php echo get_post_meta($object->ID, 'socialdb_object_content', true); ?>
                            </textarea>     
                    </div>
                </div>    
                <!-- TAINACAN: DROPZONE --> 
                <div class="form-group">
                    <label for="attachments"><?php _e('Item Attachments','tainacan'); ?></label>
                    <div id="dropzone_edit"  
                        <?php do_action('item_attachments_attributes') ?> <?php if($socialdb_collection_attachment=='no') echo 'style="display:none"' ?> 
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

                <?php if( ! empty($item_attachments) ): ?>
                    <div class="col-md-12 edit-object-box-format">
                        <a href="javascript:void(0)" onclick="show_legends_box()" class="btn btn-primary">
                            <?php _e('Edit attachment\'s legends', 'tainacan'); ?>
                        </a>
                    </div>
                <?php endif; ?>

                <div class="form-group legends-box" style="display:none;">
                    <h4> <?php _e('Edit attachment\'s legends', 'tainacan'); ?> </h4>
                    <?php
                    foreach( $item_attachments as $attachment): ?>
                        <div class="col-lg-12" style="margin-bottom: 20px">
                            <img style="width: 10%; float: left;"
                                 src="<?php echo wp_get_attachment_thumb_url( $attachment->ID) ?>"
                                 alt="<?php echo $attachment->post_title?>" />
                            <input type="text"
                               style="width: 87%; float: right;" class="form-control image-legend"
                               value="<?php echo $attachment->post_content ?>"
                               placeholder="<?php _e('Insert attachment legend', 'tainacan')?>"
                               id="legend-<?php echo $attachment->ID ?>"
                            />
                        </div> <br /> <br />
                    <?php endforeach; ?>

                    <div class="col-md-12 edit-object-box-format">
                        <a href="javascript:void(0)" onclick="update_items_legends()" class="btn-primary btn">
                            <?php _e('Update Legends', 'tainacan'); ?>
                        </a>
                        <button type="button" onclick="$('.legends-box').hide()" class="btn btn-default"><?php _e('Cancel','tainacan') ?></button>
                    </div>

                    <div class="col-md-12">
                        <div class="alert alert-success ok-legend" role="alert" style="display: none;"> <?php _e('Successfully updated legends!', 'tainacan') ?> </div>
                        <div class="alert alert-danger error-legend" role="alert" style="display: none;"> <?php _e('Error updating legends. Please try again later.', 'tainacan') ?> </div>
                    </div>
                </div>

                <div class="modal fade tainacan-image-legend" tabindex="-1" role="dialog" aria-labelledby="ItemLegend">
                    <div class="modal-dialog modal-sm">
                        <div class="modal-content">
                            <div class="modal-body image_legend" style="padding: 20px 10px 0 10px;">
                                <label for="image_legend"> <?php _e('Image Legend', 'tainacan'); ?> </label>
                                <input type="text" class="form-control" id='image_legend' name="image_legend" placeholder="<?php _e('Set the image legend', 'tainacan');?>"/>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal"> <?php _e('Close', 'tainacan') ?></button>
                                <button type="button" class="btn btn-primary" onclick="setImageLegend();"> <?php _e('Save', 'tainacan') ?> </button>
                            </div>
                        </div>
                    </div>
                </div>
                <br />
                <input type="hidden" id="object_id_edit" name="object_id" value="<?= $object->ID ?>">
                <input type="hidden" id="selected_nodes_dynatree" name="selected_nodes_dynatree" value="">
                <input type="hidden" id="object_classifications_edit" name="object_classifications" value="<?= $classifications ?>">
                <input type="hidden" id="object_content_edit" name="object_content" value="<?= strip_tags(get_post_meta($object->ID, 'socialdb_object_content', true)) ?>">
                <input type="hidden" id="edit_object_collection_id" name="collection_id" value="<?= $collection_id ?>">
                <input type="hidden" id="operation_edit" name="operation" value="update">
                <button type="button" onclick="back_main_list();" style="margin-bottom: 20px;color" class="btn btn-default btn-lg pull-left"><?php _e('Cancel','tainacan'); ?></button>
                <div id="submit_container">
                    <button type="submit" id="submit_edit" class="btn btn-success btn-lg pull-right send-button"><?php _e('Submit','tainacan'); ?></button>
                </div>  
                <div id="submit_container_message" style="display: none;">
                     <button type="button" onclick="show_message()" style="margin-bottom: 20px;" class="btn btn-success btn-lg pull-right send-button"><?php _e('Submit','tainacan'); ?></button>
                </div>
        </div>
<!----------------- FIM: CONTAINER MAIOR - NOME,CONTEUDO E ANEXOS  ----------------->
    </div> 
</form>