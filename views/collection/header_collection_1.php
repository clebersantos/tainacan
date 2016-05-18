<?php
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/header_js.php');
//$post = get_post($collection_id);
$options = get_option('socialdb_theme_options');
?>
<!-- TAINACAN: panel da colecao, background-color definido pelo o usuario -->
<!--div class="panel-heading" style="max-width: 100%;border-color: <?= $collection_metas['socialdb_collection_board_border_color'] ?>;color:<?= $collection_metas['socialdb_collection_board_font_color'] ?>;background-color: <?= $collection_metas['socialdb_collection_board_background_color'] ?>;"-->
<div class="panel-heading collection_header container-fluid" style="max-width: 100%;background-color: #6a6a6a;padding:0 20px;">
    <div class="row">
        <!-- TAINACAN: container com o menu da colecao, link para eventos e a busca de items -->
        <div class="col-md-12">
            <!-- TAINACAN: div com o menu da colecao e link eventos -->
            <div class="col-md-2">

                <input type="hidden" id="socialdb_permalink_collection" name="socialdb_permalink_collection" value="<?php echo get_the_permalink($collection_post->ID); ?>" />

                <?php if ((verify_collection_moderators($collection_post->ID, get_current_user_id())||current_user_can( 'manage_options' ))&& get_post_type($collection_post->ID) == 'socialdb_collection'): ?>
                    <!-- TAINACAN: menu da colecao que abre as acoes via ajax dentro do configuration do single.php -->
                    <a href="#" style="color:<?php echo $collection_metas['socialdb_collection_board_link_color']; ?>" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><span class="glyphicon glyphicon-cog"></span>&nbsp;<?php _e('Collection Settings'); ?><span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a onclick="showCollectionConfiguration('<?php echo get_template_directory_uri() ?>');" href="#"><span class="glyphicon glyphicon-wrench"></span>&nbsp;<?php _e('Configuration'); ?></a></li>
                        <li><a onclick="showDesignConfiguration('<?php echo get_template_directory_uri() ?>');" href="#"><span class="glyphicon glyphicon-picture"></span>&nbsp;<?php _e('Design'); ?></a></li>
                        
                        <?php
                        if (get_option('collection_root_id') == $collection_post->ID) 
                        {
                            ?>
                            <!--li class="divider"></li>
                            <li><a onclick="showAPIConfiguration('< ?php echo get_template_directory_uri() ?>');" href="#"><span class="glyphicon glyphicon-lock"></span>&nbsp;< ?php _e('API Keys Configuration'); ?></a></li-->

                            <?php
                        } 
                        else 
                        {
                            ?>
                            <li><a onclick="showPropertiesConfiguration('<?php echo get_template_directory_uri() ?>');" href="#"><span class="glyphicon glyphicon-list-alt"></span>&nbsp;<?php _e('Metadata'); ?></a></li>
                            <li><a onclick="showRankingConfiguration('<?php echo get_template_directory_uri() ?>');" href="#"><span class="glyphicon glyphicon-star"></span>&nbsp;<?php _e('Rankings'); ?></a></li>
                            <li><a onclick="showSearchConfiguration('<?php echo get_template_directory_uri() ?>');" href="#"><span class="glyphicon glyphicon-list-alt"></span>&nbsp;<?php _e('Search'); ?></a></li>
                            <li class="divider"></li>
                            <li><a onclick="showUsersConfiguration('<?php echo get_template_directory_uri() ?>');" href="#"><span class="glyphicon glyphicon-user"></span>&nbsp;<?php _e('Users'); ?></a></li>
                            <li><a onclick="showCategoriesConfiguration('<?php echo get_template_directory_uri() ?>');" href="#"><span class="glyphicon glyphicon-filter"></span>&nbsp;<?php _e('Categories'); ?></a></li>
                            <li><a onclick="showSocialConfiguration('<?php echo get_template_directory_uri() ?>');" href="#"><span class="glyphicon glyphicon-user"></span>&nbsp;<?php _e('Social'); ?></a></li>
                            <li><a onclick="showLicensesConfiguration('<?php echo get_template_directory_uri() ?>');" href="#"><span class="glyphicon glyphicon-user"></span>&nbsp;<?php _e('Licenses'); ?></a></li>
                            <li><a onclick="showImport('<?php echo get_template_directory_uri() ?>');" href="#"><span class="glyphicon glyphicon-open"></span>&nbsp;<?php _e('Import'); ?></a></li>
                            <li><a onclick="showExport('<?php echo get_template_directory_uri() ?>');" href="#"><span class="glyphicon glyphicon-save"></span>&nbsp;<?php _e('Export'); ?></a></li>
                            <li class="divider"></li>
                            <li style="background-color: #e4b9b9;"><a onclick="delete_collection_redirect('<?php _e('Delete Collection') ?>','<?php echo __('Are you sure to remove the collection: ').$collection_post->post_title ?>','<?php echo $collection_post->ID ?>','<?= mktime() ?>','<?php echo get_option('collection_root_id') ?>')" href="#"><span class="glyphicon glyphicon-trash"></span>&nbsp;<?php _e('Delete'); ?></a></li>
                            <?php
                        }
                        ?>


                    </ul>
                <?php endif; ?>
                    <?php
                      if (get_option('collection_root_id') != $collection_post->ID) {
                        ?>
                             <br><a onclick="showEvents('<?php echo get_template_directory_uri() ?>');" style="color:<?php echo $collection_metas['socialdb_collection_board_link_color']; ?>" href="#"><span class="glyphicon glyphicon-flash"></span> <?php _e('Events'); ?>&nbsp;<span id="notification_events" style="background-color:red;color:white;font-size:13px;"></span></a>
                             <?php if(!verify_collection_moderators($collection_post->ID, get_current_user_id())&&!current_user_can( 'manage_options' )): ?>
                             <br><a onclick="show_report_abuse_collection('<?php echo $collection_post->ID; ?>');" style="color:<?php echo $collection_metas['socialdb_collection_board_link_color']; ?>" href="#"><span class="glyphicon glyphicon-warning-sign"></span> <?php _e('Report Abuse'); ?>&nbsp;</a>
                                 <!-- modal exluir -->
                            <div class="modal fade" id="modal_delete_collection<?php echo $collection_post->ID; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                              <div class="modal-dialog">
                                <div class="modal-content">  
                                        <div class="modal-header">
                                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                          <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-trash"></span>&nbsp;<?php _e('Report Abuse'); ?></h4>
                                        </div>
                                        <div class="modal-body">
                                          <?php echo __('Describe why the collection: '). $collection_post->post_title.__(' is abusive: '); ?>
                                            <textarea id="observation_delete_collection<?php echo $collection_post->ID ?>" class="form-control"></textarea>
                                        </div>
                                        <div class="modal-footer">
                                          <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close'); ?></button>
                                          <button onclick="report_abuse_collection('<?php _e('Delete Collection') ?>','<?php _e('Are you sure to remove the collection: '). $collection_post->post_title ?>','<?php echo $collection_post->ID ?>','<?php echo mktime() ?>','<?php echo get_option('collection_root_id') ?>')" type="button" class="btn btn-primary"><?php echo __('Delete'); ?></button>
                                        </div>
                                    </form>  
                                </div>
                              </div>
                            </div>
                            <?php endif; ?>
                 <?php } ?>
            </div>
              <!-- TAINACAN: div com o input para pesquisa de items na colecao -->
            <div class="col-md-10">
                <div class="input-group">
                    <div class="input-group-btn">
                        <button onclick="clear_list()"id="clear" class="btn-xs btn-primary btn" style="margin-right:10px;margin-bottom:5px"><?php _e('Clear') ?></button>
                    </div>
                    <input onkeyup="set_value(this)" onkeydown="if (event.keyCode === 13)
                                document.getElementById('search_main').click();
                           " type="text" style="font-size: 13px; " class="form-control input-medium placeholder" id="search_objects" placeholder="<?php _e('Search Objects') ?>">
                    <span class="input-group-btn">
                        <button id="search_main" type="button" onclick="search_objects('#search_objects')"  class="btn btn-default"><span class="glyphicon glyphicon-search"></span></button>
                    </span>
                </div>
            </div>
        </div>

        <?php
        if ($collection_metas['socialdb_collection_board_skin_mode'] != "skin_cover") {
            $classThumb = 'col-md-2';
            $classTitle = 'col-md-10';
            $thumbSize = 'thumbnail';
        } else {
            $classThumb = 'col-md-6';
            $classTitle = 'col-md-6';
            $thumbSize = 'large';
        }
        ?>
        <!-- TAINACAN: div com o titulo, thumbnail ou tamanho large da imagem para modo capa, a descricao, links para compartilhamento -->
        <div class="col-md-12" style="margin-top:10px;">
            <div class="<?php echo $classThumb; ?>" >
                <a href="<?php echo get_the_permalink($collection_post->ID); ?>"> 
                    <span class="pull-right">
                            <?php
                            $url_image = wp_get_attachment_url( get_post_thumbnail_id( $collection_post->ID ) );
                            if (get_the_post_thumbnail($collection_post->ID, $thumbSize)) {
                                echo get_the_post_thumbnail($collection_post->ID, $thumbSize);
                                ?><img style="display:none;" itemprop="image" src="<?php echo $url_image; ?>" /><?php
                            } else {
                                ?>
                            <img src="<?php echo get_template_directory_uri() ?>/libraries/images/default_thumbnail.png">
                        <?php } ?>
                    </span>
                </a>
            </div>
            <div class="<?php echo $classTitle; ?>" style="float: left;" >
                <div style="float: right;">

                    <!-- ******************** TAINACAN: compartilhar colecao (titutlo,imagem e descricao) no FACEBOOK ******************** -->
                    <a target="_blank" href="http://www.facebook.com/sharer/sharer.php?s=100&amp;p[url]=<?php echo get_the_permalink($collection_post->ID); ?>&amp;p[images][0]=<?php echo wp_get_attachment_url(get_post_thumbnail_id($collection_post->ID)); ?>&amp;p[title]=<?php echo htmlentities($collection_post->post_title); ?>&amp;p[summary]=<?php echo strip_tags($collection_post->post_content); ?>">
                        <img src="<?php echo get_template_directory_uri() ?>/libraries/images/icon_facebook.png" style="max-width: 32px;" />
                    </a>

                    <!-- ******************** TAINACAN: compartilhar colecao (titulo,imagem) no GOOGLE PLUS ******************** -->
                    <a target="_blank" href="https://plus.google.com/share?url=<?php echo get_the_permalink($collection_post->ID); ?>"><img src="<?php echo get_template_directory_uri() ?>/libraries/images/icon_googleplus.png" style="max-width: 32px;" /></a>

                    <!-- ************************ TAINACAN: compartilhar colecao  no TWITTER ******************** -->
                    <a target="_blank" href="https://twitter.com/intent/tweet?url=<?php echo get_the_permalink($collection_post->ID); ?>&amp;text=<?php echo htmlentities($collection_post->post_title); ?>&amp;via=socialdb"><img src="<?php echo get_template_directory_uri() ?>/libraries/images/icon_twitter.png" style="max-width: 32px;" /></a>
                    
                    <?php if (get_option('collection_root_id') != $collection_post->ID): ?>
                    <!-- ******************** TAINACAN: RSS da colecao com seus metadados ******************** -->
                    <a target="_blank" href="<?php echo site_url().'/feed_collection/'.$collection_post->post_name ?>"><img src="<?php echo get_template_directory_uri() ?>/libraries/images/icon_rss.png" style="max-width: 32px;" /></a>
                    <?php endif; ?>
                    <!-- ******************** TAINACAN: exportar CSV os items da colecao que estao filtrados ******************** -->
<?php
if (get_option('collection_root_id') != $collection_post->ID) {
    ?>
                        <a href="#" onclick="export_selected_objects()"><img src="<?php echo get_template_directory_uri() ?>/libraries/images/export.png" style="max-width: 32px;" /></a>
                        <?php
                    }
                    ?>

                    <!-- ******************** TAINACAN: IFRAME URL ******************** -->
                    <button style="float:right;margin-left:5px;" id="iframebutton" type="button" class="btn btn-default btn-sm" data-container="body" data-toggle="popover" data-placement="left" data-title="URL Iframe" data-content="">
                        <span class="glyphicon glyphicon-link"></span>
                    </button>
                </div>
                <!-- TAINACAN: div com o titulo e a descricao -->
                <div style="margin-top: 0px;font-size: 24px;">
                        <?php if (isset($mycollections) && $mycollections == 'true') { ?>
                        <b><?php _e('My Collections'); ?></b>
                    </div>
                    <?php } else {
                        ?>
                    <b><?php echo $collection_post->post_title; ?></b>

                    <?php echo $collection_post->post_content; ?>
                    <?php } ?>
            </div>
        </div>
    </div>   
</div>   
</div>    