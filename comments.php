<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
// Do not delete these lines
global $global_collection_id;
global $global_data_permissions;
global $global_term_id;
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
    die('Please do not load this page directly. Thanks!');

if (post_password_required()) {
    ?>
    <p class="nocomments">Este artigo está protegido por password. Insira-a para ver os comentários.</p>
    <?php
    return;
}
?>

<div id="comments">
    <?php if(!isset($global_term_id)||empty($global_term_id)): ?>
        <h4 style="margin-left: 15px;"><?php comments_number('0 Comentários', '1 Comentário', '% Comentários'); ?></h4>
    <?php endif; ?>
    <?php if (have_comments()) : ?>

        <!--        <ol class="commentlist">
                < ?php wp_list_comments('avatar_size=64&type=comment'); ?>
            </ol>-->

        <ol class="commentlist">
            <?php wp_list_comments('avatar_size=64&type=comment&page=&callback=mytheme_comment'); ?>
        </ol>

        <?php if ($wp_query->max_num_pages > 1) : ?>
            <div class="pagination">
                <ul>
                    <li class="older"><?php previous_comments_link('Anteriores'); ?></li>
                    <li class="newer"><?php next_comments_link('Novos'); ?></li>
                </ul>
            </div>
        <?php endif; ?>

    <?php endif; ?>

    <?php
    $verify_permission = 0;
    if ($global_data_permissions['create'] == 'anonymous') {
        $verify_permission = 1;
    } elseif ($global_data_permissions['create'] == 'members') {
        if ($user_ID) :
            $verify_permission = 1;
        endif;
    }
    else {
        $verify_permission = 1;
    }
    ?>

    <?php if (comments_open() && $verify_permission&&verify_allowed_action($global_collection_id,'socialdb_collection_permission_create_comment')) : ?>

        <div id="respond">
            <h3>Deixe o seu comentário!</h3>
            <!--form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform" -->
            <!--form id="comment_submiteewr" method="POST"  action="#"-->
            <fieldset>
                <?php if ($user_ID) : ?>

                <p style="margin-left: 15px;"> Autenticado como <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. </p>

                <?php else : ?>
                    
                <div class="col-12 tainacan-comment-fields"> 
                    <div class="col-md-3">          
                        <label for="author">Nome:</label>
                        <input type="text" class="form-control" name="author" id="author" value="<?php echo $comment_author; ?>" />                            
                    </div>
                    <div class="col-md-3">
                        <label for="email">Email:</label>
                        <input type="text" class="form-control" name="email" id="email" value="<?php echo $comment_author_email; ?>" />
                    </div>

                    <div class="col-md-3">
                        <label for="url">Site:</label>
                        <input type="text" class="form-control"  name="url" id="url" value="<?php echo $comment_author_url; ?>" />
                    </div>
                </div>

                <?php endif; ?>
                    
                <div class="col-md-9 tainacan-comment-msg">
                    <label for="comment">Mensagem:</label>
                    <textarea name="comment" id="comment" class="form-control" rows="" cols=""></textarea>
                    <input type="hidden" name="redirect_to" value="#" />
                    <input type="hidden" id="socialdb_event_comment_term_id" name="term_id" value="<?php echo $global_term_id ?>" />
                    <br />
                    <input type="button" onclick="submit_comment(<?php echo $post->ID ?>)" class="commentsubmit btn btn-primary" value="Enviar Comentário" />
                </div>
                <?php comment_id_fields(); ?>
                <?php do_action('comment_form', $post->ID); ?>
            </fieldset>
            <!--/form -->
            <p class="cancel"><?php cancel_comment_reply_link('Cancelar Resposta'); ?></p>
        </div>
    <?php else : ?>
        <h3>Os comentários estão fechados.</h3>
    <?php endif; ?>
</div>

<!-- modal REPLY COMMENTS -->
<div class="modal fade" id="modalReplyComment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form  id="submit_comment_reply">   
                <input type="hidden" id="comment_id" name="comment_id" value="">
                <input type="hidden" id="edit_socialdb_event_comment_term_id" name="term_id" value="<?php echo $global_term_id ?>" />
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-trash"></span>&nbsp;<?php echo __('Reply Comment','tainacan'); ?></h4>
                </div>
                <div class="modal-body">
                    <?php if ($user_ID) : ?>

                        <p>Autentificado como <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. </p>

                    <?php else : ?>

                        <label for="author">Nome:</label>
                        <input type="text" name="author" id="author_reply" value="<?php echo $comment_author; ?>" /><br>

                        <label for="email">Email:</label>
                        <input type="text" name="email" id="email_reply" value="<?php echo $comment_author_email; ?>" /><br>

                        <label for="url">Website:</label>
                        <input type="text" name="url" id="url_reply" value="<?php echo $comment_author_url; ?>" /><br>

                    <?php endif; ?>

                    <label for="comment">Mensagem:</label>
                    <textarea name="comment_msg_reply" id="comment_msg_reply" class="form-control" rows="" cols=""></textarea>
                </div> 

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close','tainacan'); ?></button>
                    <button type="button" onclick="submit_comment_reply(<?php echo $post->ID ?>)" class="btn btn-primary"><?php echo __('Send','tainacan'); ?></button>
                </div>
            </form>  
        </div>
    </div>
</div>

<!-- modal REPLY COMMENTS -->
<div class="modal fade" id="showModalReportAbuseComment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form  id="submit_comment_report_abuse">   
                <input type="hidden" id="comment_id_report" name="comment_id_report" value="">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-trash"></span>&nbsp;<?php echo __('Report Abuse','tainacan'); ?></h4>
                </div>
                <div class="modal-body">
                    <?php if ($user_ID) : ?>
                    <input type="hidden" name="user_id_comment_report" value="<?php echo $user_ID ?>">
                    <?php else : ?>
                    <input type="hidden" name="user_id_comment_report" value="0">  
                    <?php endif; ?>

                    <label for="comment"><?php _e('Tell us why this content is abusive','tainacan') ?></label>
                    <i><p id="description_comment_abusive"></p></i>
                    <textarea name="comment_msg_report" id="comment_msg_report" class="form-control" rows="" cols=""></textarea>
                </div> 

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close','tainacan'); ?></button>
                    <button type="button" onclick="submit_report_abuse()" class="btn btn-primary"><?php echo __('Send','tainacan'); ?></button>
                </div>
            </form>  
        </div>
    </div>
</div>