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
?>  
<input type="hidden" name="single_object_id" id="single_object_id" value="<?php echo $object->ID; ?>" >
<div class="container-fluid">

    <ol class="breadcrumb">
        <button class="btn bt-defaul content-back" onclick="backToMainPage()"><span class="glyphicon glyphicon-arrow-left"></span></button>
        <li><a href="#">Repositorio</a></li>
        <li><a href="#">coleção</a></li>
        <li class="active"><?php echo $object->post_title; ?></li>
    </ol>
    <hr class="no-margin">
    <div class="col-md-3 content-title">
        <h1><?php echo $object->post_title; ?> <small>Autor</small></h1>
    </div>
</div>
<div class="panel panel-default clear" style="margin-top: 5px;">
    <div class="panel-heading" style="border-bottom: 0px;display:block;"> 
        <div class="row">
            <div class="col-md-2" style="padding-left: 20px;" >
                <button class="btn btn-default" onclick="backToMainPage()" ><?php _e('Back'); ?></button>
                <input type="hidden" id="socialdb_permalink_object" name="socialdb_permalink_object" value="<?php echo get_the_permalink($collection_id) . '?item=' . $object->post_name; ?>" />
                <button style="float:right;margin-left:5px;" id="iframebuttonObject" type="button" class="btn btn-default btn-sm" data-container="body" data-toggle="popoverObject" data-placement="top" data-title="Item URL" data-content="">
                    <span class="glyphicon glyphicon-link"></span>
                </button>
            </div>
            <div class="col-md-10"><strong><span style="font-size: 15px;"><?php echo $object->post_title; ?></strong></div>
        </div>  
    </div>    
</div>
<div class="post" style="margin: 40px;">
    <div class="row">
        <div class="col-md-2"><strong><?php _e('Object Thumbnail'); ?></strong></b></div> 
        <div class="col-md-6"><strong><?php _e('Object Description'); ?></strong></div>
        <div class="col-md-2"><strong><?php _e('Classifications'); ?></strong></div>
        <div class="col-md-2"><strong><?php _e('Actions'); ?></strong></div>
    </div>

    <!-- Container geral do objeto-->
    <div class="row" id="object_<?php echo $object->ID; ?>" >
        <!-- Thumbnail -->
        <div class="col-md-2">
            <?php
            if (get_the_post_thumbnail($object->ID, 'thumbnail')) {
                $url_image = wp_get_attachment_url(get_post_thumbnail_id($object->ID));
                ?>
                <a href="#" onclick="$.prettyPhoto.open(['<?php echo $url_image; ?>'], [''], ['']);
                        return false">
                    <?php
                    echo get_the_post_thumbnail($object->ID, 'thumbnail');
                    ?>
                </a>
                <?php
            } else {
                ?>
                <img src="<?php echo get_template_directory_uri() ?>/libraries/images/default_thumbnail.png">
<?php } ?>
        </div>
        <!-- Title --> 
<?php //if(get_option( 'collection_root_id' )==$collection_id):   ?>
        <div class="col-md-2"><a href="<?php echo get_the_permalink($object->ID); ?>"><?php the_title(); ?></a></div>

        <!-- Description -->
        <div class="col-md-3"><?php echo $object->post_content; ?></div>
        <!-- Classifications -->  
        <div class="col-md-3 droppableClassifications">
            <input type="hidden" value="<?php echo $object->ID ?>" class="object_id">
            <center><button id="single_show_classificiations_<?php echo $object->ID; ?>" onclick="show_classifications_single('<?php echo $object->ID; ?>')" class="btn btn-default btn-lg"><?php _e('Show classifications'); ?></button></center>
            <div id="single_classifications_<?php echo $object->ID ?>">
            </div>
        </div>
        <!-- Actions -->  
        <div class="col-md-2">
            <input type="hidden" class="post_id" name="post_id" value="<?= $object->ID ?>">
            <a href="#" class="more_info"> 
                <span class="glyphicon glyphicon-user"></span>&nbsp;<?php echo __('Author: ') . $username; ?>
            </a><br> 
<?php if ($is_moderator || $object->post_author == get_current_user_id()): ?>
                <a onclick="single_delete_object('<?= __('Delete Object') ?>', '<?= __('Are you sure to remove the object: ') . $object->post_title ?>', '<?php echo $object->ID ?>', '<?= mktime() ?>')" href="#" class="remove"> 
                    <span class="glyphicon glyphicon-remove"></span>&nbsp;<?php _e('Delete'); ?>
                </a><br>
                <a href="#"  onclick="show_edit_object('<?php echo $object->ID ?>')" class="edit">
                    <span class="glyphicon glyphicon-edit"></span>&nbsp;<?php _e('Edit'); ?>
                </a><br>
<?php else: ?>
                <a onclick="single_show_report_abuse('<?php echo $object->ID ?>')" href="#" class="report_abuse">
                    <span class="glyphicon glyphicon-warning-sign"></span>&nbsp;<?php _e('Report Abuse'); ?>
                </a>
                <!-- modal exluir -->
                <div class="modal fade" id="single_modal_delete_object<?php echo $object->ID ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">  
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-trash"></span>&nbsp;<?php _e('Report Abuse'); ?></h4>
                            </div>
                            <div class="modal-body">
    <?php echo __('Describe why the object: ') . get_the_title() . __(' is abusive: '); ?>
                                <textarea id="observation_delete_object<?php echo $object->ID ?>" class="form-control"></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close'); ?></button>
                                <button onclick="single_report_abuse_object('<?= __('Delete Object') ?>', '<?= __('Are you sure to remove the object: ') . get_the_title() ?>', '<?php echo $object->ID ?>', '<?= mktime() ?>')" type="button" class="btn btn-primary"><?php echo __('Delete'); ?></button>
                            </div>
                            </form>  
                        </div>
                    </div>
                </div>
<?php endif; ?>
            <a target="_blank" href="http://www.facebook.com/sharer/sharer.php?s=100&amp;p[url]=<?php echo get_the_permalink($collection_id) . '?item=' . $object->post_name; ?>&amp;p[images][0]=<?php echo wp_get_attachment_url(get_post_thumbnail_id($object->ID)); ?>&amp;p[title]=<?php echo htmlentities($object->post_title); ?>&amp;p[summary]=<?php echo strip_tags($object->post_content); ?>">
                <img src="<?php echo get_template_directory_uri() ?>/libraries/images/icon_facebook.png" style="max-width: 32px;" />
            </a>
            <!-- ******************** GOOGLE PLUS ******************** -->
            <a target="_blank" href="https://plus.google.com/share?url=<?php echo get_the_permalink($collection_id) . '?item=' . $object->post_name; ?>"><img src="<?php echo get_template_directory_uri() ?>/libraries/images/icon_googleplus.png" style="max-width: 32px;" /></a>
            <!-- ******************** TWITTER ******************** -->
            <a target="_blank" href="https://twitter.com/intent/tweet?url=<?php echo get_the_permalink($collection_id) . '?item=' . $object->post_name; ?>&amp;text=<?php echo htmlentities($object->post_title); ?>&amp;via=socialdb"><img src="<?php echo get_template_directory_uri() ?>/libraries/images/icon_twitter.png" style="max-width: 32px;" /></a>
        </div>
        <!-- more info -->  
        <div class="col-md-12" >
            <br>
            <div class="row" id="all_info_<?php echo $object->ID ?>" style="display:block;" >
                <div class="col-md-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title" id="panel-title"><?php _e('Rankings'); ?><a class="anchorjs-link" href="#panel-title"><span class="anchorjs-icon"></span></a></h3>
                        </div>
                        <div class="panel-body">
                            <div id="single_list_ranking_<?php echo $object->ID; ?>"></div>
                        </div>
                    </div>

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title" id="panel-title"><?php _e('Attachments'); ?><a class="anchorjs-link" href="#panel-title"><span class="anchorjs-icon"></span></a></h3>
                        </div>
                        <div class="panel-body">
                            <div id="single_list_files_<?php echo $object->ID ?>"></div>
                        </div>
                    </div>      

                </div>
                <div class="col-md-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title" id="panel-title"><?php _e('Properties'); ?><a class="anchorjs-link" href="#panel-title"><span class="anchorjs-icon"></span></a></h3>
                            <div class="btn-group">
                                <button data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button" id="btnGroupVerticalDrop1" style="font-size:11px;">
                                    <span class="glyphicon glyphicon-plus grayleft" ></span>
                                    <span class="caret"></span>
                                </button>
                                <ul  aria-labelledby="btnGroupVerticalDrop1" role="menu" class="dropdown-menu" style="width: 200px;">
                                    <li>&nbsp;<span class="glyphicon glyphicon-th-list graydrop"></span>&nbsp;<span><a class="add_property_data" onclick="show_form_data_property_single('<?php echo $object->ID ?>')" href="#property_form_<?php echo $object->ID ?>"><?php _e('Add new data property'); ?></a></span></li>
                                    <li>&nbsp;<span class="glyphicon glyphicon-th-list graydrop"></span>&nbsp;<span><a class="add_property_object" onclick="show_form_object_property_single('<?php echo $object->ID ?>')" href="#property_form_<?php echo $object->ID ?>"><?php _e('Add new object property'); ?></a></span></li>
                                </ul>   
                            </div>
                            <div class="btn-group">
                                <button  data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button" id="btnGroupVerticalDrop2" style="font-size:11px;">
                                    <span class="glyphicon glyphicon-pencil grayleft"></span>
                                    <span class="caret"></span>
                                </button>
                                <ul id="single_list_properties_edit_remove" style="width:225px;" aria-labelledby="btnGroupVerticalDrop1" role="menu" class="dropdown-menu">
                                </ul>   
                            </div>
                        </div>
                        <div class="panel-body">
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
                </div>
            </div> 
        </div>
        <!-- end more info -->  
        <!-- comments -->  
        <div class="col-md-12" id="more_info">
        </div>
    </div>
    <hr>
    <div id="comments_object"></div>
    <!--************************ COMENTARIOS PADRAO WORDPRESS ************************-->
</div> 

<!--	
<div class="col-md-2"><input type="hidden" class="post_id" name="post_id" value="<?= $object->ID ?>"><a href="#" class="edit"><span class="glyphicon glyphicon-edit"></span></a></div>
          
            <div class="col-md-2"><input type="hidden" class="post_id" name="post_id" value="<?= $object->ID ?>"><a href="#" class="remove"> <span class="glyphicon glyphicon-remove"></span></a></div>
     
<script>
       $(function(){
                                        console.log('here');
       });
</script> -->
