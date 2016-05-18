<?php
/*
 * View responsavel em mostrar o menu mais opcoes com as votacoes, propriedades e arquivos anexos
 *
 */
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/list_js.php');
?>
<!-- TAINACAN: hidden utilizados para execucao de processos desta view (list.php)  -->
<input type="hidden" id="keyword_pagination" name="keyword_pagination" value="<?php if (isset($keyword)) echo $keyword; ?>" />
<input type="hidden" id="sorted_form" name="sorted_form" value="<?php echo $sorted_by; ?>" />

<?php
$countLine = 0;
$classColumn = 12;
$show_string = is_root_category($collection_id) ?  __('Showing collections:','tainacan') : __('Showing Items:', 'tainacan');

if ( $loop->have_posts() ):
    // Determina # de colunas;
    if ($collection_data['collection_metas']['socialdb_collection_columns'] != '') {
        $classColumn = 12 / $collection_data['collection_metas']['socialdb_collection_columns'];
    }

    //  Id e tamanho da class construidos dinamicamente
    while ( $loop->have_posts() ) : $loop->the_post(); $countLine++; ?>
        <li class="col-md-6" id="object_<?php echo get_the_ID() ?>">
            <input type="hidden" id="add_classification_allowed_<?php echo get_the_ID() ?>" name="add_classification_allowed" value="<?php echo (string)verify_allowed_action($collection_id,'socialdb_collection_permission_add_classification',get_the_ID()); ?>" />
            <!-- TAINACAN: coloca a class row DO ITEM, sao cinco colunas possiveis todas elas podendo ser escondidas pelo o usuario, mas seu tamanho eh fixo col-md-2  -->
            <div class="item-colecao">
                <div class="row droppableClassifications item-info">
                    <div class="col-md-4 colFoto">
                        <a href="<?php echo get_collection_item_href($collection_id); ?>"
                           onclick="<?php get_item_click_event($collection_id, get_the_ID() )?>">
                            <?php echo get_item_thumb_image(get_the_ID()); ?>
                        </a>
                    </div>  

                    <div class="col-md-8 flex-box item-meta-box" style="flex-direction:column;">
                        <div class="item-meta col-md-12 no-padding">
                            <h4 class="item-display-title">
                                <a href="<?php echo get_collection_item_href($collection_id); ?>"
                                   onclick="<?php get_item_click_event($collection_id, get_the_ID() )?>">
                                    <?php the_title(); ?>
                                </a>
                            </h4>
                            <div class="item-description"><?php echo wp_trim_words(get_the_content(), 8); ?></div>
                            <div class="item-author"><?php echo "<strong>" . __('Created by: ', 'tainacan') . "</strong>" . get_the_author(); ?></div>
                            <div class="item-creation"><?php echo "<strong>" . __('Created at: ', 'tainacan') . "</strong>" . get_the_date('d/m/Y'); ?></div>

                            <?php if (get_option('collection_root_id') != $collection_id): ?>
                                <button id="show_rankings_<?php echo get_the_ID() ?>" onclick="show_value_ordenation('<?php echo get_the_ID() ?>')"
                                        class="btn btn-default btn-lg"><?php _e('Show rankings', 'tainacan'); ?></button>

                                <div class="editing-item">

                                    <!-- TAINACAN: container(AJAX) que mostra o html com os rankings do objeto-->
                                    <div id="rankings_<?php echo get_the_ID() ?>" class="rankings-container"></div>

                                    <div id="popover_content_wrapper<?php echo get_the_ID(); ?>" class="hide flex-box">
                                        <a target="_blank" href="https://twitter.com/intent/tweet?url=<?php echo get_the_permalink($collection_id) . '?item=' . get_post(get_the_ID())->post_name; ?>&amp;text=<?php echo htmlentities(get_the_title()); ?>&amp;via=socialdb"><div data-icon="&#xe005;"></div></a>
                                        <a onclick="redirect_facebook('<?php echo get_the_ID() ?>');" href="#"><div data-icon="&#xe021;"></div></a>
                                        <a target="_blank" href="https://plus.google.com/share?url=<?php echo get_the_permalink($collection_id) . '?item=' . get_post(get_the_ID())->post_name; ?>"><div data-icon="&#xe01b;"></div></a>
                                    </div>

                                    <ul class="item-funcs col-md-5 right">
                                        <!-- TAINACAN: hidden com id do item -->
                                        <input type="hidden" class="post_id" name="post_id" value="<?= get_the_ID() ?>">

                                        <li>
                                            <div class="item-redesocial">
                                                <a id="popover_network<?php echo get_the_ID(); ?>" rel="popover" data-placement="left"
                                                   onclick="showPopover(<?php echo get_the_ID(); ?>)">
                                                    <div style="cursor:pointer;" data-icon="&#xe00b;"></div>
                                                </a>
                                            </div>
                                        </li>

                                        <?php if (get_option('collection_root_id') != $collection_id): ?>
                                            <!--------------------------- DELETE AND EDIT OBJECT------------------------------------------------>
                                            <?php if ($is_moderator || get_post(get_the_ID())->post_author == get_current_user_id()): ?>
                                                <li>
                                                    <a onclick="delete_object('<?= __('Delete Object', 'tainacan') ?>', '<?= __('Are you sure to remove the object: ', 'tainacan') . get_the_title() ?>', '<?php echo get_the_ID() ?>', '<?= mktime() ?>')" href="#" class="remove">
                                                        <span class="glyphicon glyphicon-trash"></span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#" onclick="edit_object('<?php echo get_the_ID() ?>')">
                                                        <span class="glyphicon glyphicon-pencil"></span>
                                                    </a>
                                                </li>
                                            <?php else: ?>
                                                <?php
                                                // verifico se eh oferecido a possibilidade de remocao do objeto vindulado
                                                if(verify_allowed_action($collection_id,'socialdb_collection_permission_delete_object')): ?>
                                                    <li>
                                                        <a onclick="show_report_abuse('<?php echo get_the_ID() ?>')" href="#" class="report_abuse">
                                                            <span class="glyphicon glyphicon-warning-sign"></span>
                                                        </a>
                                                    </li>
                                                <?php endif; ?>
                                                <!-- TAINACAN:  modal padrao bootstrap para reportar abuso -->
                                                <div class="modal fade" id="modal_delete_object<?php echo get_the_ID() ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                                <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-trash"></span>&nbsp;<?php _e('Report Abuse', 'tainacan'); ?></h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <?php echo __('Describe why the object: ', 'tainacan') . get_the_title() . __(' is abusive: ', 'tainacan'); ?>
                                                                <textarea id="observation_delete_object<?php echo get_the_ID() ?>" class="form-control"></textarea>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close', 'tainacan'); ?></button>
                                                                <button onclick="report_abuse_object('<?= __('Delete Object') ?>', '<?= __('Are you sure to remove the object: ', 'tainacan') . get_the_title() ?>', '<?php echo get_the_ID() ?>', '<?= mktime() ?>')" type="button" class="btn btn-primary"><?php echo __('Delete', 'tainacan'); ?></button>
                                                            </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            <!--li><a href=""><span class="glyphicon glyphicon-comment"></span></a></li-->
                                        <?php else: ?>
                                            <!-- TAINACAN: mostra o modal da biblioteca sweet alert para exclusao de uma colecao -->
                                            <?php if ($is_moderator || get_post(get_the_ID())->post_author == get_current_user_id()): ?>
                                                <li>
                                                    <a onclick="delete_collection('<?= __('Delete Object', 'tainacan') ?>', '<?= __('Are you sure to remove the collection: ', 'tainacan') . get_the_title() ?>', '<?php echo get_the_ID() ?>', '<?= mktime() ?>', '<?php echo get_option('collection_root_id') ?>')" href="#" class="remove">
                                                        <span class="glyphicon glyphicon-trash"></span>
                                                    </a>
                                                </li>
                                            <?php else: ?>
                                                <!-- TAINACAN: mostra o modal para reportar abusao em um item, gerando assim um evento -->
                                                <li>
                                                    <a onclick="show_report_abuse('<?php echo get_the_ID() ?>')" href="#" class="report_abuse">
                                                        <span class="glyphicon glyphicon-warning-sign"></span>
                                                    </a>
                                                </li>
                                                <!-- TAINACAN:  modal padrao bootstrap para reportar abuso -->
                                                <div class="modal fade" id="modal_delete_object<?php echo get_the_ID() ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                                <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-trash"></span>&nbsp;<?php _e('Report Abuse', 'tainacan'); ?></h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <?php echo __('Describe why the collection: ', 'tainacan') . get_the_title() . __(' is abusive: ', 'tainacan'); ?>
                                                                <textarea id="observation_delete_collection<?php echo get_the_ID() ?>" class="form-control"></textarea>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close', 'tainacan'); ?></button>
                                                                <button onclick="report_abuse_collection('<?php _e('Delete Collection', 'tainacan') ?>', '<?php _e('Are you sure to remove the collection: ', 'tainacan') . get_the_title() ?>', '<?php echo get_the_ID() ?>', '<?= mktime() ?>', '<?php echo get_option('collection_root_id') ?>')" type="button" class="btn btn-primary"><?php echo __('Delete', 'tainacan'); ?></button>
                                                            </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </ul>


                                </div> <!-- .editing-item -->

                                <!-- TAINACAN: script para disparar o evento que mostra os rankings -->
                                <script>
                                    $('#show_rankings_<?php echo get_the_ID() ?>').hide();
                                    $('#show_rankings_<?php echo get_the_ID() ?>').trigger('click');
                                </script>
                            <?php endif; ?>

                            <!-- TAINACAN: container(AJAX) que mostra o html com as classificacoes do objeto -->
                            <div id="classifications_<?php echo get_the_ID() ?>" class="class-meta-box"></div>

                        </div>

                        <div class="">
                            <!-- CATEGORIES AND TAGS -->
                            <input type="hidden" value="<?php echo get_the_ID() ?>" class="object_id">
                            <button id="show_classificiations_<?php echo get_the_ID() ?>" style="width:100%" class="btn btn-default"
                                    onclick="show_classifications('<?php echo get_the_ID() ?>')">
                                <?php _e('Metadata', 'tainacan'); ?>
                            </button>
                        </div>


                </div>

                </div>
            </div>
        </li>
        <?php
//        if ($countLine == $collection_data['collection_metas']['socialdb_collection_columns']) {
//            echo "</div> <!-- TAINACAN: apeanas um separador entre os objetos> <hr--><div class=\"row\">";
//            $countLine = 0;
//        }
        ?>
    <?php endwhile; ?>
    <!--/div-->
    <!--/div-->
    <!--/div -->
<?php else: ?>
    <!-- TAINACAN: se a pesquisa nao encontrou nenhum item -->
    <div id="items_not_found" class="alert alert-danger">
        <span class="glyphicon glyphicon-warning-sign"></span>&nbsp;<?php _e('No objects found!', 'tainacan'); ?>
    </div>
    <!-- TAINACAN: se a colecao estiver vazia eh mostrado -->
    <div id="collection_empty" style="display:none" >
        <?php if (get_option('collection_root_id') != $collection_id): ?>
            <div class="jumbotron">
                <h2 style="text-align: center;"><?php _e('This collection is empty, create the first item!', 'tainacan') ?></h2>
                <p style="text-align: center;"><a onclick="show_form_item()" class="btn btn-primary btn-lg" href="#" role="button"><span class="glyphicon glyphicon-plus"></span>&nbsp;<?php _e('Click here to add a new item', 'tainacan') ?></a>
                </p>
            </div>
        <?php else: ?>
            <div class="jumbotron">
                <h2 style="text-align: center;"><?php _e('This repository is empty, create the first collection!', 'tainacan') ?></h2>
                <p style="text-align: center;"><a onclick="showModalCreateCollection()" class="btn btn-primary btn-lg" href="#" role="button"><span class="glyphicon glyphicon-plus"></span>&nbsp;<?php _e('Click here to add a new collection', 'tainacan') ?></a>
                </p>
            </div>
        <?php endif; ?>
    </div>
<?php
endif;

$numberItems = ceil($loop->found_posts / 10);

if ($loop->found_posts > 10):
    ?>
    <!-- TAINACAN: div com a paginacao da listagem -->
    <div class="">
        <div id="center_pagination" class="col-md-12">

            <input type="hidden" id="number_pages" name="number_pages" value="<?= $numberItems; ?>">
            <div class="pagination_items col-md-4 pull-left">
                <a href="#" class="btn btn-default btn-sm first" data-action="first"><span class="glyphicon glyphicon-backward"></span><!--&laquo;--></a>
                <a href="#" class="btn btn-default btn-sm previous" data-action="previous"><span class="glyphicon glyphicon-step-backward"></span><!--&lsaquo;--></a>
                <input type="text"  style="width: 90px;" readonly="readonly"  data-current-page="<?php if (isset($pagid)) echo $pagid; ?>" data-max-page="0" />
                <a href="#" class="btn btn-default btn-sm next" data-action="next"><span class="glyphicon glyphicon-step-forward"></span><!--&rsaquo;--></a>
                <a href="#" class="btn btn-default btn-sm last" data-action="last"><span class="glyphicon glyphicon-forward"></span><!--   &raquo; --></a>
            </div>

            <div class="col-md-3 center">
                <?php echo $show_string ?>
                <?php echo "1 - " . $loop->query['posts_per_page'] . __(' of ', 'tainacan') . $loop->found_posts ?>
            </div>

            <div class="col-md-3 pull-right">
                <?php _e('Items per page:', 'tainacan') ?>
                <select name="items-per-page" id="items-per-page">
                    <option disabled value="<?php echo $loop->query['posts_per_page'] ?>"><?php echo $loop->query['posts_per_page'] ?></option>
                </select>
            </div>

        </div>
    </div>
<?php endif; ?>


