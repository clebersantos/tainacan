<?php
/*
 * View responsavel em mostrar os arquivos de um objeto
 */
include_once ('js/list_file_js.php');
?>
<!-- TAINACAN: mostra os os arquivos de um objeto, o icone do arquivo 
   é gerado automaticamente pelo wordpress, apenas o título que colocamos manualmente
-->
<div> 
    <h4 class="title-pipe" style="margin-bottom: 20px;margin-top: -0.5%;">
        <?php _e('Attachments', 'tainacan'); ?>
    </h4>
    <?php if(isset($attachments['image'])): ?>
    <center><a onclick="showSlideShow()"  class=" btn btn-default btn-sm"><?php _e('View slideshow','tainacan') ?></a></center><br>
        <?php endif; ?>
    <?php if (!$attachments['posts']): ?>
        <div id="no_file_<?php echo $object_id; ?>" class="text-center">
            <?php _e('No Attachments','tainacan'); ?>
        </div>
    <?php else: ?>
    <div id="files_<?php echo $object_id; ?>" style="text-align: center;" class="item-attachments">
            <?php
            foreach ($attachments['posts'] as $attachment) {
                echo wp_get_attachment_link($attachment->ID, 'thumbnail', false, true);
                echo "<h4 class='text-center'><small>". wp_trim_words($attachment->post_content,15) ."</small></h4>";
            }
            ?>
        </div>   
<?php endif; ?>

</div>
<!-- TAINACAN: modal padrao bootstrap aberto via javascript pelo seu id, slideshow anexos -->
        <div class="modal fade" id="modalSlideShow" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                 <div class="modal-content">
                     <div class="modal-header">
                         <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                         <h4 class="modal-title" id="myModalLabel"><?php _e('SlideShow','tainacan'); ?></h4>
                    </div>
                     <div class="modal-body">
                         <div class="row">
                             <div id="carousel-attachment">
                                 <?php foreach ($attachments['image'] as $image): ?>
                                     <div class='slideshow-item' style="display:block;">
                                         <img src="<?= $image->guid ?>" />
                                         <div class="image-caption"> <?= $image->post_content ?> </div>
                                     </div>
                                 <?php endforeach; ?>
                             </div>
                         </div>
                    </div>
                </div>
            </div>
        </div>     

 