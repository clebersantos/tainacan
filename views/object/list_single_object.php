<?php
/*
 *
 * View responsavel em mostrar um objeto especifico
 *
 *
 */

include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/list_single_js.php');

$create_perm_object = verify_allowed_action($collection_id,'socialdb_collection_permission_create_property_object');
$edit_perm_object = verify_allowed_action($collection_id,'socialdb_collection_permission_edit_property_object');
$delete_perm_object = verify_allowed_action($collection_id,'socialdb_collection_permission_delete_property_object');
$create_perm_data = verify_allowed_action($collection_id,'socialdb_collection_permission_create_property_data');
$edit_perm_data = verify_allowed_action($collection_id,'socialdb_collection_permission_edit_property_data');
$delete_perm_data = verify_allowed_action($collection_id,'socialdb_collection_permission_delete_property_data');

$meta_type = ucwords( $metas['socialdb_object_dc_type'][0] );
$meta_source = $metas['socialdb_object_dc_source'][0];
?>
<input type="hidden" name="single_object_id" id="single_object_id" value="<?php echo $object->ID; ?>" >
<div class="container-fluid">

    <ol class="breadcrumb">
        <button class="btn bt-defaul content-back" onclick="backToMainPageSingleItem()"><span class="glyphicon glyphicon-arrow-left"></span></button>
        <li><a href="<?php echo get_permalink(get_option('collection_root_id')); ?>"> <?php _e('Repository', 'tainacan') ?> </a></li>
        <li><a href="#" onclick="backToMainPageSingleItem()"><?php echo get_post($collection_id)->post_title; ?></a></li>
        <li class="active"><?php echo $object->post_title; ?></li>
        <input type="hidden" id="single_name" name="item_single_name" value="<?php echo  $object->post_name; ?>" />
        <input type="hidden" id="socialdb_permalink_object" name="socialdb_permalink_object" value="<?php echo get_the_permalink($collection_id) . '?item=' . $object->post_name; ?>" />
        <button class="btn bt-defaul content-back pull-right" id="iframebuttonObject" data-container="body" data-toggle="popoverObject" data-placement="left" data-title="Item URL" data-content="">
            <span class="glyphicon glyphicon-link"></span>
        </button>
    </ol>
    <hr class="no-margin">
    <div class="col-md-12 content-title single-item-title">
        <h2>
             <span id="text_title"><?php echo $object->post_title; ?></span> 
             <span id="event_title" style="display:none;">
                 <input type="text" value="<?php echo $object->post_title; ?>" id="title_field">
             </span> 
             <small>
                 <?php
                // verifico se o metadado pode ser alterado
                if (verify_allowed_action($collection_id, 'socialdb_collection_permission_edit_property_data_value',$object_id)):
                    ?> 
                <button type="button" alt="<?php _e('Cancel modification','tainacan') ?>" onclick="cancel_title()" id="cancel_title" class="btn btn-default btn-xs" style="display: none;" ><span class="glyphicon glyphicon-arrow-left" ></span></button>
                <button type="button" onclick="edit_title()" id="edit_title" class="btn btn-default btn-xs" ><span class="glyphicon glyphicon-edit"></span></button>
                <button type="button" onclick="save_title('<?php echo $object->ID ?>')" id="save_title"class="btn btn-default btn-xs" style="display: none;"><span class="glyphicon glyphicon-floppy-disk"></span></button>
                <?php endif; ?>
             </small>
            <small><?php echo $username; ?></small>
        </h2>
    </div>
</div>
<div id="container_three_columns" class="container-fluid white-background">
    <div class="row">
        <div class="col-md-2">
            <div class="row">
                <div class="col-md-9 content-thumb">
                    <?php
                    if (get_the_post_thumbnail($object->ID, 'thumbnail')) {
                        $url_image = wp_get_attachment_url(get_post_thumbnail_id($object->ID));
                        ?>
                        <a href="#" onclick="$.prettyPhoto.open(['<?php echo $url_image; ?>'], [''], ['']);
                                return false">
                            <img src="<?php echo $url_image; ?>" class="img-responsive" />
                        </a>
                        <?php
                    } else {
                        ?>
                        <img class="img-responsive" src="<?php echo get_item_thumbnail_default($object->ID); ?>">
                    <?php } ?>
                    <?php
                    // Evento par aalteracao do thumbnail de um item
                    // verifico se o metadado pode ser alterado
                    if (verify_allowed_action($collection_id, 'socialdb_collection_permission_edit_property_data_value',$object_id)):
                        ?>
                    <div style="margin-top: 5px;">
                      <button type="button" onclick="edit_thumbnail()" id="edit_thumbnail" class="btn btn-default btn-xs" ><span class="glyphicon glyphicon-edit"></span></button>
                   </div>
                    <?php endif; ?>     
                </div>
                <div class="col-md-3 item-redesocial content-redesocial">
                    <a target="_blank" href="http://www.facebook.com/sharer/sharer.php?s=100&amp;p[url]=<?php echo get_the_permalink($collection_id) . '?item=' . $object->post_name; ?>&amp;p[images][0]=<?php echo wp_get_attachment_url(get_post_thumbnail_id($object->ID)); ?>&amp;p[title]=<?php echo htmlentities($object->post_title); ?>&amp;p[summary]=<?php echo strip_tags($object->post_content); ?>">
                        <span data-icon="&#xe021;"></span>
                    </a>
                    <a target="_blank" href="https://twitter.com/intent/tweet?url=<?php echo get_the_permalink($collection_id) . '?item=' . $object->post_name; ?>&amp;text=<?php echo htmlentities($object->post_title); ?>&amp;via=socialdb">
                        <span data-icon="&#xe005;"></span>
                    </a>
                    <a target="_blank" href="https://plus.google.com/share?url=<?php echo get_the_permalink($collection_id) . '?item=' . $object->post_name; ?>">
                        <span data-icon="&#xe01b;"></span>
                    </a>
                     <?php if(is_restful_active()): ?>
                        <!--a target="_blank" href="<?php echo site_url() . '/wp-json/posts/' . $object->ID.'/?type=socialdb_object' ?>">
                           <div class="fab"><small><h6><b>json</b></h6></small></div>
                        </a>
                        <!--a style="cursor: pointer;" onclick="export_selected_objects_json()">
                            <div class="fab"><small><h6><b>items</b></h6></small></div>
                        </a-->
                    <?php endif; ?>
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" >    
                          <div style="font-size:1em; cursor:pointer;" data-icon="&#xe00b;"></div>
                    </a>  
                    <ul style="
                                z-index: 9999;
                            " class="dropdown-menu" role="menu">
                         <li>
                             <a target="_blank" href="<?php echo get_the_permalink($collection_id) . '?item=' . $object->post_name; ?>.rdf"  ><span class="glyphicon glyphicon-upload"></span> <?php _e('RDF', 'tainacan'); ?>&nbsp;
                             </a>
                         </li>
                         <?php if(is_restful_active()): ?>
                         <li>
                             <a href="<?php echo site_url() . '/wp-json/posts/' . $object->ID.'/?type=socialdb_object' ?>"  ><span class="glyphicon glyphicon-upload"></span> <?php _e('JSON', 'tainacan'); ?>&nbsp;
                             </a>
                         </li>
                         <?php endif; ?>
                         <li>
                            <a onclick="showGraph('<?php echo get_the_permalink($collection_id). '?item=' . $object->post_name; ?>.rdf')"  style="cursor: pointer;"   >
                                <span class="glyphicon glyphicon-upload"></span> <?php _e('Graph', 'tainacan'); ?>&nbsp;
                            </a>
                        </li>
                     </ul>          
                </div>
                <ul class="col-md-3 item-funcs">
                    <?php if ($is_moderator || $object->post_author == get_current_user_id()): ?>
                              <!--li><a href=""><span class="glyphicon glyphicon-trash"></span></a></li>
                              <li class="hide"><a href=""><span class="glyphicon glyphicon-warning-sign"></span></a></li>
                              <li><a href=""><span class="glyphicon glyphicon-pencil"></span></a></li>
                              <li class="hide"><a href=""><span class="glyphicon glyphicon-comment"></span></a></li-->
                        <li>
                            <a onclick="single_delete_object('<?= __('Delete Object', 'tainacan') ?>', '<?= __('Are you sure to remove the object: ', 'tainacan') . $object->post_title ?>', '<?php echo $object->ID ?>', '<?= mktime() ?>')" href="#" class="remove">
                                <span class="glyphicon glyphicon-trash"></span>
                            </a>
                        </li>
                        <li>
                            <a href="#"  onclick="show_edit_object('<?php echo $object->ID ?>')" class="edit">
                                <span class="glyphicon glyphicon-pencil"></span>
                            </a>
                        </li>
                    <?php else: 
                        // verifico se eh oferecido a possibilidade de remocao do objeto vindulado
                    if(verify_allowed_action($collection_id,'socialdb_collection_permission_delete_object')): ?>
                        <li>
                            <a onclick="single_show_report_abuse('<?php echo $object->ID ?>')" href="#" class="report_abuse">
                                <span class="glyphicon glyphicon-warning-sign"></span>
                            </a>
                        </li>
                    <?php endif; ?>
                        <!-- modal exluir -->
                        <div class="modal fade" id="single_modal_delete_object<?php echo $object->ID ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-trash"></span>&nbsp;<?php _e('Report Abuse', 'tainacan'); ?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <?php echo __('Describe why the object: ', 'tainacan') . get_the_title() . __(' is abusive: ', 'tainacan'); ?>
                                        <textarea id="observation_delete_object<?php echo $object->ID ?>" class="form-control"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close', 'tainacan'); ?></button>
                                        <button onclick="single_report_abuse_object('<?= __('Delete Object') ?>', '<?= __('Are you sure to remove the object: ', 'tainacan') . get_the_title() ?>', '<?php echo $object->ID ?>', '<?= mktime() ?>')" type="button" class="btn btn-primary"><?php echo __('Delete', 'tainacan'); ?></button>
                                    </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="row same-height">
                <div class="col-md-9">
                    <span><small>Data: <?php echo get_the_date('d/m/y', $object->ID); ?></small></span><br>
                 <?php /* <a href=""><span><small>Ver anteriores</small></span></a> */ ?>
                </div>
                <input type="hidden" class="post_id" name="post_id" value="<?= $object->ID ?>">
            </div>
            <div id="single_list_ranking_<?php echo $object->ID; ?>" class="row">
               
            </div>

            <div class="row" <?php if(has_action('home_item_source_div')) do_action('home_item_source_div') ?> >
                <div class="col-md-12">
                    <h4 class="title-pipe"> <?php _e( 'Source','tainacan'); ?> 
                        <?php
                        // verifico se o metadado pode ser alterado
                        if (verify_allowed_action($collection_id, 'socialdb_collection_permission_edit_property_data_value',$object_id)):
                            ?>
                         <small>
                            <button type="button" onclick="cancel_source()" id="cancel_source" class="btn btn-default btn-xs" style="display: none;" ><span class="glyphicon glyphicon-arrow-left" ></span></button>
                            <button type="button" onclick="edit_source()" id="edit_source" class="btn btn-default btn-xs" ><span class="glyphicon glyphicon-edit"></span></button>
                            <button type="button" onclick="save_source('<?php echo $object->ID ?>')" id="save_source"class="btn btn-default btn-xs" style="display: none;"><span class="glyphicon glyphicon-floppy-disk"></span></button>
                        </small>
                        <?php endif; ?>
                    </h4>
                    <div id="text_source">
                       <?php echo format_item_text_source($meta_source); ?> 
                    </div>
                    <div id="event_source" style="display:none;" >
                        <input type="text" 
                               class="form-control" 
                               id="source_field" 
                               value="<?php echo $meta_source; ?>" 
                               name="source_field" 
                               placeholder="<?php _e('Type the source and click save!') ?>" >
                    </div>
                </div>
            </div>

            <div class="row" <?php if(has_action('home_item_type_div')) do_action('home_item_type_div') ?>>
                <div class="col-md-12">
                    <h4 class="title-pipe"> <?php _e( 'Type','tainacan'); ?> 
                        <?php
                        // verifico se o metadado pode ser alterado
                        if (verify_allowed_action($collection_id, 'socialdb_collection_permission_edit_property_data_value',$object_id)):
                            ?> 
                        <small>
                            <button type="button" onclick="cancel_type()" id="cancel_type" class="btn btn-default btn-xs" style="display: none;" ><span class="glyphicon glyphicon-arrow-left" ></span></button>
                            <button type="button" onclick="edit_type()" id="edit_type" class="btn btn-default btn-xs" ><span class="glyphicon glyphicon-edit"></span></button>
                            <button type="button" onclick="save_type('<?php echo $object->ID ?>')" id="save_type"class="btn btn-default btn-xs" style="display: none;"><span class="glyphicon glyphicon-floppy-disk"></span></button>
                        </small>
                        <?php endif; ?>
                    </h4>
                    <div id="text_type">
                       <?php   _e($meta_type, 'tainacan') ?>
                    </div>
                    <div id="event_type" style="display:none;" >
                        <input type="radio" 
                               value="text" 
                               <?php echo ($meta_type=='Text')? 'checked="checked"' : '' ?>
                               name="type_field" ><?php _e('Text', 'tainacan') ?><br>
                        <input type="radio" 
                               value="image" 
                                <?php echo ($meta_type=='Image')? 'checked="checked"' : '' ?>
                               name="type_field" ><?php _e('Image', 'tainacan') ?><br>
                        <input type="radio" 
                               value="audio" 
                                <?php echo ($meta_type=='Audio')? 'checked="checked"' : '' ?>
                               name="type_field" ><?php _e('Audio', 'tainacan') ?><br>
                        <input type="radio" 
                               value="video" 
                                <?php echo ($meta_type=='Video')? 'checked="checked"' : '' ?>
                               name="type_field" ><?php _e('Video', 'tainacan') ?><br>
                        <input type="radio" 
                               value="pdf" 
                                <?php echo ($meta_type=='Pdf')? 'checked="checked"' : '' ?>
                               name="type_field" ><?php _e('PDF', 'tainacan') ?><br>
                        <input type="radio" 
                               value="other" 
                                <?php echo ($meta_type=='Other')? 'checked="checked"' : '' ?>
                               name="type_field" ><?php _e('Other', 'tainacan') ?><br>
                    </div>
                </div>
            </div>

        </div>

        <!-- TAINACAN: esta div agrupa a listagem de itens ,submissao de novos itens e ordencao -->
        <div id="div_central"  <?php if(has_action('home_item_attachments_div')) echo 'class="col-md-10"'; else echo 'class="col-md-8"' ?> >
            <div class="row content-wrapper" <?php if(has_action('home_item_content_div')) do_action('home_item_content_div') ?>>
                <div>
                    <?php
                    if ($metas['socialdb_object_dc_type'][0] == 'text') {
                        echo $metas['socialdb_object_content'][0] ;
                    } else {
                        if ($metas['socialdb_object_from'][0] == 'internal') {
                            $url = wp_get_attachment_url($metas['socialdb_object_content'][0]);
                            switch ($metas['socialdb_object_dc_type'][0]) {
                                case 'audio':
                                    $content = '<audio controls><source src="' . $url . '">' . __('Your browser does not support the audio element.', 'tainacan') . '</audio>';
                                    break;
                                case 'image':
                                    if (get_the_post_thumbnail($object->ID, 'thumbnail')) {
                                        $url_image = wp_get_attachment_url(get_post_thumbnail_id($object->ID, 'large'));
                                        $content = '<center><a href="#" onclick="$.prettyPhoto.open([\''.$url_image.'\'], [\'\'], [\'\']);return false">
                                                        <img style="max-width:880px;" src="'.$url_image.'" class="img-responsive" />
                                                    </a></center>';
                                    }
                                    break;
                                case 'video':
                                    $content = '<video width="400" controls><source src="' . $url . '">' . __('Your browser does not support HTML5 video.', 'tainacan') . '</video>';
                                    break;
                                case 'pdf':
                                    $content = '<embed src="' . $url . '" width="600" height="500" alt="pdf" pluginspage="http://www.adobe.com/products/acrobat/readstep2.html">';
                                    break;
                                default:
                                    $content = '<p style="text-align:center;">'.__('File link:') . ' <a target="_blank" href="' . $url . '">' . __('Click here!', 'tainacan') . '</a></p>';
                                    break;
                            }
                        } else {
                            switch ($metas['socialdb_object_dc_type'][0]) {
                                case 'audio':
                                    $content = '<audio controls><source src="' . $metas['socialdb_object_content'][0] . '">' . __('Your browser does not support the audio element.', 'tainacan') . '</audio>';
                                    break;
                                case 'image':
                                    if (get_the_post_thumbnail($object->ID, 'thumbnail')) {
                                        $url_image = wp_get_attachment_url(get_post_thumbnail_id($object->ID, 'large'));
                                        $content = '<center><a href="#" onclick="$.prettyPhoto.open([\''.$url_image.'\'], [\'\'], [\'\']);return false">
                                                        <img style="max-width:880px;"  src="'.$url_image.'" class="img-responsive" />
                                                    </a></center>';
                                    }else{
                                        $content = "<img src='" . $metas['socialdb_object_content'][0] . "' class='img-responsive' />";
                                    }
                                    break;
                                case 'video':
                                    if (strpos($metas['socialdb_object_content'][0], 'youtube') !== false) {
                                        $step1 = explode('v=', $metas['socialdb_object_content'][0]);
                                        $step2 = explode('&', $step1[1]);
                                        $video_id = $step2[0];
                                        $content = "<div style='height:600px; display: flex !important;'  ><iframe  class='embed-responsive-item' src='http://www.youtube.com/embed/" . $video_id . "?html5=1' allowfullscreen frameborder='0'></iframe></div>";
                                    } elseif (strpos($metas['socialdb_object_content'][0], 'vimeo') !== false) {
                                        $step1 = explode('/', rtrim($metas['socialdb_object_content'][0],'/'));
                                        $video_id = end($step1);
                                        //"https://player.vimeo.com/video/132886713"
                                        $content = "<div class=\"embed-responsive embed-responsive-16by9\"><iframe class='embed-responsive-item' src='https://player.vimeo.com/video/" . $video_id . "' frameborder='0'></iframe></div>";
                                    } else {
                                        $content = "<div class=\"embed-responsive embed-responsive-16by9\"><iframe class='embed-responsive-item' src='" . $metas['socialdb_object_content'][0] . "' frameborder='0'></iframe></div>";
                                    }
                                    break;
                                case 'pdf':
                                    $content = '<embed src="' . $metas['socialdb_object_content'][0] . '" width="600" height="500" alt="pdf" pluginspage="http://www.adobe.com/products/acrobat/readstep2.html">';
                                    break;
                                default:
                                    $content = '<p style="text-align:center;">'.__('File link:', 'tainacan') . ' <a target="_blank" href="' . $metas['socialdb_object_content'][0] . '">' . __('Click here!', 'tainacan') . '</a></p>';
                                    break;
                            }
                        }

                        echo $content;
                    }
                    ?>
                </div>
            </div>
            <!-- Descricao do item -->
            <div class="row">
                <h4 class="title-pipe">Descrição&nbsp;&nbsp;
                    <?php
                    // verifico se o metadado pode ser alterado
                    if (verify_allowed_action($collection_id, 'socialdb_collection_permission_edit_property_data_value',$object_id)):
                        ?> 
                    <small>
                        <button type="button" onclick="cancel_description()" id="cancel_description" class="btn btn-default btn-xs" style="display: none;" ><span class="glyphicon glyphicon-arrow-left" ></span></button>
                        <button type="button" onclick="edit_description()" id="edit_description" class="btn btn-default btn-xs" ><span class="glyphicon glyphicon-edit"></span></button>
                        <button type="button" onclick="save_description('<?php echo $object->ID ?>')" id="save_description"class="btn btn-default btn-xs" style="display: none;"><span class="glyphicon glyphicon-floppy-disk"></span></button>
                    </small>
                    <?php endif; ?>
                </h4>
                <div class="col-md-12">
                    <div id="text_description"><p><?php echo $object->post_content; ?></p></div>
                    <div id="event_description" style="display:none"><textarea class="col-md-12" id="description_field"><?php echo $object->post_content; ?></textarea></div>
                </div>
            </div>
            <hr>
            <!-- Licencas do item -->
            <div class="row" <?php if(has_action('home_item_license_div')) do_action('home_item_license_div') ?>>
                <h4 class="title-pipe">
                    <?php _e('License','tainacan') ?>
                    <?php
                    // verifico se o metadado pode ser alterado
                    if (verify_allowed_action($collection_id, 'socialdb_collection_permission_edit_property_data_value',$object_id)):
                        ?> 
                    <small>
                        <button type="button" onclick="cancel_license()" id="cancel_license" class="btn btn-default btn-xs" style="display: none;" ><span class="glyphicon glyphicon-arrow-left" ></span></button>
                        <button type="button" onclick="edit_license()" id="edit_license" class="btn btn-default btn-xs" ><span class="glyphicon glyphicon-edit"></span></button>
                        <button type="button" onclick="save_license('<?php echo $object->ID ?>')" id="save_license"class="btn btn-default btn-xs" style="display: none;"><span class="glyphicon glyphicon-floppy-disk"></span></button>
                    </small>
                    <?php endif; ?>
                </h4>
                <div class="col-md-12" id="text_license">
                    <p><?php
                    if(isset(get_post($metas['socialdb_license_id'][0])->post_title))
                        echo get_post($metas['socialdb_license_id'][0])->post_title;
                    else
                        echo __('No license registered for this item','tainacan');
                    ?></p>
                </div>
                <div id="event_license" style="display: none;">
                </div>
            </div>
            <?php 
            //Acao que permite que os modulos insiram novas divs para o item
            do_action('home_item_insert_container',$object->ID) ?>
            <!-- Metadados do item -->
            <div class="row">
                <h4 class="title-pipe"> <?php _e('Properties', 'tainacan') ?>
                  <!--a href=""><small><span class="glyphicon glyphicon-pencil"></span></small></a-->
                    <div class="btn-group">

                        <?php  if($create_perm_object||$create_perm_data): ?>
                        <button data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button" id="btnGroupVerticalDrop1" style="font-size:11px;">
                            <span class="glyphicon glyphicon-plus grayleft" ></span>
                            <span class="caret"></span>
                        </button>
                        <ul  aria-labelledby="btnGroupVerticalDrop1" role="menu" class="dropdown-menu" style="width: 200px;">
                            <?php if($create_perm_data): ?>
                            <li>&nbsp;<span class="glyphicon glyphicon-th-list graydrop"></span>&nbsp;<span><a class="add_property_data" onclick="show_form_data_property_single('<?php echo $object->ID ?>')" href="#property_form_<?php echo $object->ID ?>"><?php _e('Add new data property', 'tainacan'); ?></a></span></li>
                            <?php endif; ?>
                            <?php if($create_perm_object): ?>
                            <li>&nbsp;<span class="glyphicon glyphicon-th-list graydrop"></span>&nbsp;<span><a class="add_property_object" onclick="show_form_object_property_single('<?php echo $object->ID ?>')" href="#property_form_<?php echo $object->ID ?>"><?php _e('Add new object property', 'tainacan'); ?></a></span></li>
                            <?php endif; ?>
                        </ul>
                        <?php endif; ?>
                    </div>
                    <div class="btn-group">
                        <?php  if($edit_perm_object||$delete_perm_object||$edit_perm_data||$delete_perm_data): ?>
                        <button onclick="list_properties_edit_remove_single($('#single_object_id').val())"  data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button" id="btnGroupVerticalDrop2" style="font-size:11px;">
                            <span class="glyphicon glyphicon-pencil grayleft"></span>
                            <span class="caret"></span>
                        </button>
                        <ul id="single_list_properties_edit_remove" style="width:225px;" aria-labelledby="btnGroupVerticalDrop1" role="menu" class="dropdown-menu">
                        </ul>
                        <?php endif; ?>
                    </div>
                    </h4>
                <div class="col-md-12">
                    <div id="single_list_all_properties_<?php echo $object->ID ?>">
                    </div>
                    <div id="single_data_property_form_<?php echo $object->ID ?>">
                    </div>
                    <div id="single_object_property_form_<?php echo $object->ID ?>">
                    </div>
                    <div id="single_edit_data_property_form_<?php echo $object->ID ?>">
                    </div>
                    <div id="single_edit_object_property_form_<?php echo $object->ID ?>">
                    </div>
                </div>
            </div>
            <div class="bs-callout bs-callout-info"  <?php if(has_action('home_item_tag_div')) do_action('home_item_tag_div') ?>>
                <h4 ><?php _e('Tags', 'tainacan'); ?>
                   <small>
                        <?php
                        // verifico se o metadado pode ser alterado
                        if (verify_allowed_action($collection_id, 'socialdb_collection_permission_edit_tag',$object->ID)):
                            ?>    
                                <button type="button" onclick="cancel_tag()" id="cancel_tag" class="btn btn-default btn-xs" style="display: none;" ><span class="glyphicon glyphicon-arrow-left" ></span></button>
                                <button type="button" onclick="edit_tag()" id="edit_tag" class="btn btn-default btn-xs" ><span class="glyphicon glyphicon-edit"></span></button>
                                <button type="button" onclick="save_tag('<?php echo $object->ID ?>')" id="save_tag" class="btn btn-default btn-xs" style="display: none;"><span class="glyphicon glyphicon-floppy-disk"></span></button>
                            <?php endif; ?>    
                   </small>
                </h4>
                <div class="col-md-12">
                    <input type="hidden" value="<?php echo $object->ID ?>" class="object_id">
                    <center><button id="single_show_classificiations_<?php echo $object->ID; ?>" onclick="show_classifications_single('<?php echo $object->ID; ?>')" class="btn btn-default btn-lg"><?php _e('Show classifications', 'tainacan'); ?></button></center>
                    <div id="single_classifications_<?php echo $object->ID ?>">
                    </div>
                    <div id="event_tag" style="display:none;">
                        <input type="text" style="width:50%;" class="form-control col-md-6" id="event_tag_field"  placeholder="<?php _e('Type the tag name','tainacan') ?>">
                    </div>
                    <script>
                        $('#single_show_classificiations_<?php echo $object->ID ?>').hide();
                        $('#single_show_classificiations_<?php echo $object->ID ?>').trigger('click');
                    </script>
                </div>
            </div>
            <br>
            <hr>
            <div class="row">
                <div id="comments_object"></div>
                <!--h4 class="title-pipe">Comentários (10)</h4>
                <br>
                <div class="row">
                  <div class="col-md-2">
                    <div class="col-md-10 pull-right content-thumb">
                      <a href=""><img src="images/imagem.png" alt="" class="img-responsive"></a>
                    </div>
                  </div>
                  <div class="col-md-10">
                    <p><b class="azul">Autor: </b>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
                  </div>
                </div>
                <hr>
                <div class="row">
                  <div class="col-md-2 col-md-offset-1">
                    <div class="col-md-10 pull-right content-thumb">
                      <a href=""><img src="images/imagem.png" alt="" class="img-responsive"></a>
                    </div>
                  </div>
                  <div class="col-md-9">
                    <p><b class="azul">Autor: </b>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
                  </div>
                </div-->
            </div>
            <br>
        </div>
        <div class="col-md-2"  <?php if(has_action('home_item_attachments_div')) do_action('home_item_attachments_div') ?> >
            <div id="single_list_files_<?php echo $object->ID ?>"></div>
            <!--div class="row">
              <div class="col-md-8 col-md-offset-2 content-thumb">
                <a href=""><img src="images/imagem.png" alt="" class="img-responsive"></a>
              </div>
              <h4 class="text-center"><small>Imagem titulo</small></h4>
            </div>
            <div class="row">
              <div class="col-md-8 col-md-offset-2 content-thumb">
                <a href=""><img src="images/imagem.png" alt="" class="img-responsive"></a>
              </div>
              <h4 class="text-center"><small>Imagem titulo</small></h4>
            </div-->
        </div>
    </div>
</div>
<!-- Modal para upload de thumbnail -->
 <div class="modal fade" id="single_modal_thumbnail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formThumbnail">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-trash"></span>&nbsp;<?php _e('Select a image', 'tainacan'); ?></h4>
            </div>
            <div class="modal-body">
                  <input type="file" 
                         class="form-control" 
                         id="thumbnail_field"  
                         name="attachment" >
                  <input type="hidden" name="operation" value="insert_attachment">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close', 'tainacan'); ?></button>
                <button type="submit" class="btn btn-primary"><?php echo __('Alter Image', 'tainacan'); ?></button>
            </div>
            </form>
        </div>
    </div>
</div>