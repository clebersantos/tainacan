<?php
/*
 * 
 * View responsavel em mostrar o menu mais opcoes com as votacoes, propriedades e arquivos anexos
 * 
 * 
 */

include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/list_js.php');

?> 
<!-- TAINACAN: hidden utilizados para execucao de processos desta view (list.php)  -->
<input type="hidden" id="keyword_pagination" name="keyword_pagination" value="<?php if(isset($keyword)) echo $keyword; ?>" >
<input type="hidden" id="sorted_form" name="sorted_form" value="<?php  echo $sorted_by; ?>" >
<!-- TAINACAN: panel situado abaixo do painel da colecao e acima da listagem de itens   -->
<div class="panel panel-default clear" style="margin-top: 5px;">
    <div class="panel-heading" style="border-bottom: 0px;display:block;"> 
        <!-- TAINACAN: mostra o tipo e a forma de ordenacao realizada para a listagem  -->
        <strong><span style="font-size: 15px;"><?php  echo $listed_by; ?></strong>
        <!-- TAINACAN: mostra o numero de objetos obtidos na pesquisa atual  -->
        <span class="pull-right"><?php  _e('Number of objects: '); ?><span id="object_count"><b><?php echo  $loop->found_posts; ?></b></span></span>
    </div>    
</div>
 <?php if ($loop->have_posts()) : ?>
<!-- TAINACAN: esta div apenas coloca um estilo para scroll -->
<div class="row" style="height:800px;overflow-y:scroll;">
    <!-- TAINACAN: esta div apenas engloba toda a listagem,  -->
    <div class="post">
         <!-- TAINACAN: esta div eh responsavel em mostrar os cabecalhos das div's que ficam na parte superior  das colunas, title,content, menu ...  -->
        <div class="row" <?php if($collection_data['collection_metas']['socialdb_collection_columns'] != '1') echo "style='display:none;'"; ?>>
                <div <?php if($collection_data['collection_metas']['socialdb_collection_hide_thumbnail'] == 'hide_thumb') echo "style='display:none;'"; ?> class="col-md-2"><strong><?php _e('Object Thumbnail'); ?></strong></b></div>
                <div <?php if($collection_data['collection_metas']['socialdb_collection_hide_title'] == 'hide_title') echo "style='display:none;'"; ?> class="col-md-2"><strong><?php _e('Object Name'); ?></strong></div>
                <div <?php if($collection_data['collection_metas']['socialdb_collection_hide_description'] == 'hide_description') echo "style='display:none;'"; ?> class="col-md-2"><strong><?php _e('Object Description'); ?></strong></div>
                <div <?php if($collection_data['collection_metas']['socialdb_collection_hide_categories'] == 'hide_category') echo "style='display:none;'"; ?> class="col-md-2"><strong><?php _e('Classifications'); ?></strong></div>
               <div <?php if($collection_data['collection_metas']['socialdb_collection_hide_rankings'] == 'hide_rankings') echo "style='display:none;'"; ?> class="col-md-2"><strong><?php _e('Rankings'); ?></strong></div>
                <div <?php if($collection_data['collection_metas']['socialdb_collection_hide_menu'] == 'hide_menu') echo "style='display:none;'"; ?> class="col-md-2"><strong><?php _e('Actions'); ?></strong></div>
        </div>
          <!-- TAINACAN: esta div apenas setada para que todas os items fiquem abaixo da class row  -->
        <div class="row">
        <?php 
        if($collection_data['collection_metas']['socialdb_collection_columns'] != ''){
            $classColumn = 12/$collection_data['collection_metas']['socialdb_collection_columns'];
        }else{
            $classColumn = 12;
        }
        $countLine = 0;
        while ($loop->have_posts()) : $loop->the_post(); 
            $countLine++;
        ?>  
         <!-- TAINACAN: esta div eh responsavel em determinar se a listagem sera de uma coluna, duas e etc | O id e o tamanho da class sao construidos dinamicamente  -->    
        <!-- Container geral do objeto-->
             <div class="col-md-<?php echo $classColumn; ?>" id="object_<?php echo get_the_ID() ?>">
                  <!-- TAINACAN: coloca a class row DO ITEM, sao cinco colunas possiveis todas elas podendo ser escondidas pelo o usuario, mas seu tamanho eh fixo col-md-2  -->
                 <div class="row">
                    <!-- TAINACAN: container que mostra o thumbnail do objeto ou a imagem default do fichario, utiliza-se a biblioteca pretty photo para expansao da imagem para seu tamanho default  -->
                   <div class="col-md-2" <?php if($collection_data['collection_metas']['socialdb_collection_hide_thumbnail'] == 'hide_thumb') echo "style='display:none;'"; ?>>
                    <?php 
                    // se nao colocou o tomanho da thumbanil defina uma padrao
                    if($collection_data['collection_metas']['socialdb_collection_size_thumbnail']==''){
                        $collection_data['collection_metas']['socialdb_collection_size_thumbnail'] = 'thumbnail';
                    }
                    //verifica se tem thumbnail
                    if(get_the_post_thumbnail(get_the_ID(),$collection_data['collection_metas']['socialdb_collection_size_thumbnail'])){
                                $url = get_post_meta(get_the_ID(),'socialdb_thumbnail_url',true);
                                $url_image = wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) );
                                if($url){ 
                                    ?>
                                <!-- onclick="showSingleObject('< ?php echo get_the_ID() ?>', '< ?php echo get_template_directory_uri() ?>');" -->
                                <a href="#" onclick="$.prettyPhoto.open(['<?php echo $url_image; ?>'],[''],['']); return false"><?php echo  get_the_post_thumbnail(get_the_ID(),$collection_data['collection_metas']['socialdb_collection_size_thumbnail']); ?></a>
                                <?php  }else{ 
                                    ?>
                                    <a href="#" onclick="$.prettyPhoto.open(['<?php echo $url_image; ?>'],[''],['']); return false">
                                    <?php
                                        echo  get_the_post_thumbnail(get_the_ID(),$collection_data['collection_metas']['socialdb_collection_size_thumbnail']);
                                    ?>
                                    </a>    
                                    <?php
                                } 
                    }else{// pega a foto padrao ?>
                                <a onclick="showSingleObject('<?php echo get_the_ID() ?>', '<?php echo get_template_directory_uri() ?>');" href="#"><img src="<?php echo get_template_directory_uri() ?>/libraries/images/default_thumbnail.png"></a>
                    <?php } ?>
                   </div>
                    <!-- TAINACAN: esta div mostra o titulo do item -->
                    <?php if(get_option( 'collection_root_id' )==$collection_id): ?>
                         <div <?php if($collection_data['collection_metas']['socialdb_collection_hide_title'] == 'hide_title') echo "style='display:none;'"; ?> class="col-md-2"><a href="<?php echo get_the_permalink(); ?>"><?php the_title(); ?></a></div>
                    <?php else: 
                        $uri = get_post_meta(get_the_ID(),'socialdb_uri_imported',true);
                        ?>
                        <div <?php if($collection_data['collection_metas']['socialdb_collection_hide_title'] == 'hide_title') echo "style='display:none;'"; ?> class="col-md-2">
                            <!--a onclick="showSingleObject('<?php echo get_the_ID() ?>', '<?php echo get_template_directory_uri() ?>');" href="#">
                                <?php the_title(); ?>
                            </a-->
                            <a target="_blank" href="<?php echo get_the_permalink($collection_id) ?>?item=<?php echo get_post(get_the_ID())->post_name ?>">
                                <?php the_title(); ?>
                            </a>

                        </div>
                    <?php endif; ?>
                     <!-- TAINACAN: esta div mostra a descricao do item, limitado por 450 caracteres -->
                     <div <?php if($collection_data['collection_metas']['socialdb_collection_hide_description'] == 'hide_description') echo "style='display:none;'"; ?> class="col-md-2"><?php ?>
                         <?php if($collection_data['collection_metas']['socialdb_collection_hide_description'] != 'hide_description') echo substr(get_the_content(), 0, 450) ; ?>
                     </div>
                    <!-- TAINACAN: esta div mostra as classificacoes-->
                    <div <?php if($collection_data['collection_metas']['socialdb_collection_hide_categories'] == 'hide_category') echo "style='display:none;'"; ?> class="col-md-2 droppableClassifications">
                        <!-- TAINACAN: hidden com id do objeto -->
                        <input type="hidden" value="<?php echo get_the_ID() ?>" class="object_id">
                        <!-- TAINACAN: botao que ativa o ajax que mostra as classificacoes -->
                        <center><button id="show_classificiations_<?php echo get_the_ID() ?>" onclick="show_classifications('<?php echo get_the_ID() ?>')" class="btn btn-default btn-lg"><?php _e('Show classifications'); ?></button></center>
                        <!-- TAINACAN: container(AJAX) que mostra o html com as classificacoes do objeto -->
                        <div id="classifications_<?php echo get_the_ID() ?>">
                        </div>
                    </div>
                     <!-- TAINACAN: esta div mostra os rankings --> 
                     <div <?php if($collection_data['collection_metas']['socialdb_collection_hide_rankings'] == 'hide_rankings') echo "style='display:none;'"; ?> class="col-md-2">
                         <?php if(get_option( 'collection_root_id' )!=$collection_id):  ?>
                        <!-- TAINACAN: este botao nao aparece na tela porem eh necessario pois eh disparado automaticamente --> 
                         <button id="show_rankings_<?php echo get_the_ID() ?>" onclick="show_rankings('<?php echo get_the_ID() ?>')" class="btn btn-default btn-lg"><?php _e('Show rankings'); ?></button>
                         <!-- TAINACAN: container(AJAX) que mostra o html com os rankings do objeto-->
                         <div id="rankings_<?php echo get_the_ID() ?>"></div>
                          <!-- TAINACAN: script para disparar o evento que mostra os rankings -->
                        <script>
                            $('#show_rankings_<?php echo get_the_ID() ?>').hide();
                            $('#show_rankings_<?php echo get_the_ID() ?>').trigger('click');
                        </script>
                           <?php endif; ?>
                     </div>
                      <!-- TAINACAN: esta div mostra as acoes possiveis para o usuario, dependendo de sua permissao-->  
                    <div <?php if($collection_data['collection_metas']['socialdb_collection_hide_menu'] == 'hide_menu') echo "style='display:none;'"; ?> class="col-md-2">
                        <!-- TAINACAN: hidden com id do item -->
                        <input type="hidden" class="post_id" name="post_id" value="<?= get_the_ID() ?>">
                        <!-- TAINACAN: mostra a pagina do item dentro da div configuration do single.php -->
                        <a onclick="showSingleObject('<?php echo get_the_ID() ?>', '<?php echo get_template_directory_uri() ?>');" href="#" class="more_info"> 
                            <span class="glyphicon glyphicon-eye-open"></span>&nbsp;<?php echo __('View Item'); ?>
                        </a><br> 
                        <!-- TAINACAN: mostra o dono do item -->
                        <a href="#" class="more_info"> 
                            <span class="glyphicon glyphicon-user"></span>&nbsp;<?php echo __('Author: ').get_the_author(); ?>
                        </a><br> 
                        <!------------ begin: if collection is not root --------------->
                        <?php if(get_option( 'collection_root_id' )!=$collection_id):  ?>
                            <!--------------------------- DELETE AND EDIT OBJECT------------------------------------------------>
                            <?php if($is_moderator||get_post(get_the_ID())->post_author==get_current_user_id()): ?>
                            <!-- TAINACAN: mostra o modal da biblioteca sweet alert para exclusao do objeto -->
                            <a onclick="delete_object('<?= __('Delete Object') ?>','<?= __('Are you sure to remove the object: '). get_the_title() ?>','<?php echo get_the_ID() ?>','<?= mktime() ?>')" href="#" class="remove"> 
                                <span class="glyphicon glyphicon-remove"></span>&nbsp;<?php _e('Delete'); ?>
                            </a><br>
                            <!-- TAINACAN: mostra dentro da div form da pagina single.php o formulario de edicao do item -->
                            <a href="#" onclick="edit_object('<?php echo get_the_ID() ?>')" >
                                <span class="glyphicon glyphicon-edit"></span>&nbsp;<?php _e('Edit'); ?>
                            </a><br>
                            <?php else: ?>
                             <!-- TAINACAN: mostra o modal para reportar abusao em um item, gerando assim um evento -->
                            <a onclick="show_report_abuse('<?php echo get_the_ID() ?>')" href="#" class="report_abuse">
                                <span class="glyphicon glyphicon-warning-sign"></span>&nbsp;<?php _e('Report Abuse'); ?>
                            </a><br>
                             <!-- TAINACAN:  modal padrao bootstrap para reportar abuso -->
                            <div class="modal fade" id="modal_delete_object<?php echo get_the_ID() ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                              <div class="modal-dialog">
                                <div class="modal-content">  
                                        <div class="modal-header">
                                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                          <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-trash"></span>&nbsp;<?php _e('Report Abuse'); ?></h4>
                                        </div>
                                        <div class="modal-body">
                                          <?php echo __('Describe why the object: '). get_the_title().__(' is abusive: '); ?>
                                            <textarea id="observation_delete_object<?php echo get_the_ID() ?>" class="form-control"></textarea>
                                        </div>
                                        <div class="modal-footer">
                                          <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close'); ?></button>
                                          <button onclick="report_abuse_object('<?= __('Delete Object') ?>','<?= __('Are you sure to remove the object: '). get_the_title() ?>','<?php echo get_the_ID() ?>','<?= mktime() ?>')" type="button" class="btn btn-primary"><?php echo __('Delete'); ?></button>
                                        </div>
                                    </form>  
                                </div>
                              </div>
                            </div>
                            <?php endif; ?>
                        <?php else:  ?>
                            <!-- TAINACAN: mostra o modal da biblioteca sweet alert para exclusao de uma colecao -->
                            <?php if($is_moderator||get_post(get_the_ID())->post_author==get_current_user_id()): ?>
                            <a onclick="delete_collection('<?= __('Delete Object') ?>','<?= __('Are you sure to remove the collection: '). get_the_title() ?>','<?php echo get_the_ID() ?>','<?= mktime() ?>','<?php echo get_option('collection_root_id') ?>')" href="#" class="remove"> 
                                <span class="glyphicon glyphicon-remove"></span>&nbsp;<?php _e('Delete'); ?>
                            </a><br>
                            <?php else: ?>
                             <!-- TAINACAN: mostra o modal para reportar abusao em um item, gerando assim um evento -->
                            <a onclick="show_report_abuse('<?php echo get_the_ID() ?>')" href="#" class="report_abuse">
                                <span class="glyphicon glyphicon-warning-sign"></span>&nbsp;<?php _e('Report Abuse'); ?>
                            </a><br>
                            <!-- TAINACAN:  modal padrao bootstrap para reportar abuso -->
                            <div class="modal fade" id="modal_delete_object<?php echo get_the_ID() ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                              <div class="modal-dialog">
                                <div class="modal-content">  
                                        <div class="modal-header">
                                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                          <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-trash"></span>&nbsp;<?php _e('Report Abuse'); ?></h4>
                                        </div>
                                        <div class="modal-body">
                                          <?php echo __('Describe why the collection: '). get_the_title().__(' is abusive: '); ?>
                                            <textarea id="observation_delete_collection<?php echo get_the_ID() ?>" class="form-control"></textarea>
                                        </div>
                                        <div class="modal-footer">
                                          <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close'); ?></button>
                                          <button onclick="report_abuse_collection('<?php _e('Delete Collection') ?>','<?php _e('Are you sure to remove the collection: '). get_the_title() ?>','<?php echo get_the_ID() ?>','<?= mktime() ?>','<?php echo get_option('collection_root_id') ?>')" type="button" class="btn btn-primary"><?php echo __('Delete'); ?></button>
                                        </div>
                                    </form>  
                                </div>
                              </div>
                            </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        <!------------ end: if collection is not root --------------->
                        <?php if($uri):  ?> 
                         <!-- TAINACAN: link para items que possuirem uma origem (items importados) -->
                        <a href="<?= $uri ?>" target="_blank">
                                      <span class="glyphicon glyphicon-globe"></span>
                                     <?= __('Source') ?></a>   
                        <br>
                        <?php endif; ?> 
                        <!-- TAINACAN: link para publicacao do item no facebook -->
                        <a onclick="redirect_facebook('<?php echo get_the_ID() ?>');" href="#">
                                    <!--a onclick="graphStreamPublish('twerwer','http://www.tainacan.gi.fic.ufg.br/collection/videos-do-ministerio-da-cultura/?item=fundo-nacional-pro-leitura-e-aprovado-no-senado','http://www.tainacan.gi.fic.ufg.br/wp-content/uploads/2015/08/default614.jpg','iterm1','descricao')" href="#"-->
                                    <img src="<?php echo get_template_directory_uri() ?>/libraries/images/icon_facebook.png" style="max-width: 32px;" />
                        </a>
                         <!-- TAINACAN: link para publicacao do item no g+ -->
                        <a target="_blank" href="https://plus.google.com/share?url=<?php echo get_the_permalink($collection_id).'?item='.get_post(get_the_ID())->post_name; ?>"><img src="<?php echo get_template_directory_uri() ?>/libraries/images/icon_googleplus.png" style="max-width: 32px;" /></a>
                         <!-- TAINACAN: link para publicacao do item no twitter -->
                        <a target="_blank" href="https://twitter.com/intent/tweet?url=<?php echo get_the_permalink($collection_id).'?item='.get_post(get_the_ID())->post_name; ?>&amp;text=<?php echo htmlentities(get_the_title()); ?>&amp;via=socialdb"><img src="<?php echo get_template_directory_uri() ?>/libraries/images/icon_twitter.png" style="max-width: 32px;" /></a>
                    </div>
                    <!-- TAINACAN: div situada embaixo de cada item (col-md-12) responsavel em mostrar informacoes do item --> 
                    <div class="col-md-12" >
                        <br>
                        <!-- TAINACAN: container com todos os as div's que recebem os conteudos via ajax, mostrada a partir da action do botao moreinfo  -->
                        <div class="row" id="all_info_<?php echo get_the_ID() ?>" style="display:none;" >
                            <div class="col-md-4">
                                <!-- TAINACAN: panel dos rankings -->
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title" id="panel-title"><?php _e('Rankings'); ?><a class="anchorjs-link" href="#panel-title"><span class="anchorjs-icon"></span></a></h3>
                                    </div>
                                    <div class="panel-body">
                                        <!-- TAINACAN: div (ajax) que recebe o html com todos os rankings do item -->
                                        <div id="list_ranking_<?php echo get_the_ID() ?>"></div>
                                    </div>
                                </div>  
                                 <!-- TAINACAN: panel dos rankings -->
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title" id="panel-title"><?php _e('Attachments'); ?><a class="anchorjs-link" href="#panel-title"><span class="anchorjs-icon"></span></a></h3>
                                    </div>
                                    <div class="panel-body">
                                        <!-- TAINACAN: div (ajax) que recebe o html com todos os arquivos do item -->
                                        <div id="list_files_<?php echo get_the_ID() ?>"></div>
                                    </div>
                                </div>  
                            </div>
                            <!-- TAINACAN: div com todas as propriedades do item -->
                            <div class="col-md-8">
                                <div class="panel panel-default">
                                    <!-- TAINACAN: cabecalho com os botoes para adicao e edicao de propriedade -->
                                    <div class="panel-heading">
                                        <h3 class="panel-title" id="panel-title"><?php _e('Properties'); ?><a class="anchorjs-link" href="#panel-title"><span class="anchorjs-icon"></span></a></h3>
                                        <div class="btn-group">
                                            <!-- TAINACAN:botao que mostra o dropdown com os link para o formulario de criacao das propriedades -->
                                            <button data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button" id="btnGroupVerticalDrop1" style="font-size:11px;">
                                                <span class="glyphicon glyphicon-plus grayleft" ></span>
                                                <span class="caret"></span>
                                            </button>
                                            <ul  aria-labelledby="btnGroupVerticalDrop1" role="menu" class="dropdown-menu" style="width: 200px;">
                                                <!-- TAINACAN: abre o formulario para adicao de propriedade de atributo na div data_property_form_{ID} -->
                                                <li>&nbsp;<span class="glyphicon glyphicon-th-list graydrop"></span>&nbsp;<span><a class="add_property_data" onclick="show_form_data_property('<?php echo get_the_ID() ?>')" href="#property_form_<?php echo get_the_ID() ?>"><?php _e('Add new data property'); ?></a></span></li>
                                                <!-- TAINACAN: abre o formulario para adicao de propriedade de objeto na div object_property_form_{ID} -->
                                                <li>&nbsp;<span class="glyphicon glyphicon-th-list graydrop"></span>&nbsp;<span><a class="add_property_object" onclick="show_form_object_property('<?php echo get_the_ID() ?>')" href="#property_form_<?php echo get_the_ID() ?>"><?php _e('Add new object property'); ?></a></span></li>
                                            </ul>   
                                        </div>
                                        <div class="btn-group">
                                              <!-- TAINACAN:botao que mostra o dropdown com os link para o formulario de edicao das propriedades e exclusao -->
                                            <button  data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button" id="btnGroupVerticalDrop2" style="font-size:11px;">
                                                <span class="glyphicon glyphicon-pencil grayleft"></span>
                                                <span class="caret"></span>
                                            </button>
                                              <!-- TAINACAN: ul (AJAX) que recebe o html com todas as propriedades da colecao -->
                                            <ul id="list_properties_edit_remove" style="width:225px;" aria-labelledby="btnGroupVerticalDrop1" role="menu" class="dropdown-menu">
                                            </ul>   
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                         <!-- TAINACAN: div (AJAX) que recebe todas as propriedades da colecao com seus widgets e valores, aberto por default -->
                                        <div id="list_all_properties_<?php echo get_the_ID() ?>">
                                        </div> 
                                        <!-- TAINACAN: div (AJAX) que recebe o formulario para adicao de propriedade de atributo -->
                                        <div id="data_property_form_<?php echo get_the_ID() ?>">
                                        </div>
                                        <!-- TAINACAN: div (AJAX) que recebe o formulario para adicao de propriedade de objeto -->
                                        <div id="object_property_form_<?php echo get_the_ID() ?>">
                                        </div> 
                                        <!-- TAINACAN: div (AJAX) que recebe o formulario para edicao de propriedade de atributo (data)-->
                                        <div id="edit_data_property_form_<?php echo get_the_ID() ?>">
                                        </div>
                                        <!-- TAINACAN: div (AJAX) que recebe o formulario para edicao de propriedade de objeto (data)-->
                                        <div id="edit_object_property_form_<?php echo get_the_ID() ?>">
                                        </div> 
                                    </div>
                                </div>      
                            </div>
                        </div> 
                    </div>
                    <!-- TAINACAN: div situada embaixo de cada item (col-md-12) responsavel em mostrar o botao que dispara o evento para abrir a div acima com as informacoes --> 
                    <div class="col-md-12" >
                       <!-- TAINACAN: abre (mostra) as informacoes do item, (acima) --> 
                        <center id="more_info_show_<?php echo get_the_ID() ?>">
                            <a onclick="show_info('<?php echo get_the_ID() ?>')" href="#object_<?php echo get_the_ID() ?>" class="more_info"> 
                                 <span class="glyphicon glyphicon-chevron-down"></span>&nbsp;<?php _e('More Info'); ?>
                            </a>
                        </center>
                         <!-- TAINACAN: esconde as informacoes do item, (acima) --> 
                        <center id="less_info_show_<?php echo get_the_ID() ?>" style="display:none;">
                            <a onclick="show_info('<?php echo get_the_ID() ?>')" href="#object_<?php echo get_the_ID() ?>" class="more_info"> 
                                 <span class="glyphicon glyphicon-chevron-up"></span>&nbsp;<?php _e('Less Info'); ?>
                            </a>
                        </center>
                    </div>
                    <!-- end more info -->  
                    <!-- comments   
                    <div class="col-md-12" id="more_info">
                    </div>-->
             </div>
         </div>
        <?php if($countLine == $collection_data['collection_metas']['socialdb_collection_columns']){ 
            echo "</div> <!-- TAINACAN: apeanas um separador entre os objetos --> <hr><div class=\"row\">";
            $countLine = 0;
        } ?>
        <?php endwhile; ?> 
        <hr>
        </div>
    </div> 
</div>
<?php else: ?> 
    <!-- TAINACAN: se a pesquisa nao encontrou nenhum item --> 
    <div id="items_not_found" class="alert alert-danger">
        <span class="glyphicon glyphicon-warning-sign"></span>&nbsp;<?php _e('No objects found!'); ?>
    </div>
    <!-- TAINACAN: se a colecao estiver vazia eh mostrado --> 
    <div id="collection_empty" style="display:none" >
        <div class="jumbotron">
            <h2 style="text-align: center;"><?php _e('This collection is empty, create the first item!') ?></h2>
            <p style="text-align: center;"><a onclick="show_form_item()" class="btn btn-primary btn-lg" href="#" role="button"><span class="glyphicon glyphicon-plus"></span>&nbsp;<?php _e('Click here to add a new item') ?></a>
            </p>
        </div>
    </div>
<?php endif; 

$numberItems = ceil($loop->found_posts / 10);
if ($loop->found_posts > 10):
  ?>
 <!-- TAINACAN: div com a paginacao da listagem --> 
 <div id="center_pagination" class="well well-sm" style="height: 40px;">  
            <input type="hidden" id="number_pages" name="number_pages" value="<?= $numberItems;  ?>">
            <div id="teste" class="pagination_items" style="position: relative;right: 50%;left: 50%;">
                <a href="#" class="first" data-action="first">&laquo;</a>
                <a href="#" class="previous" data-action="previous">&lsaquo;</a>
                <input type="text" style="width: 90px;" readonly="readonly"  data-current-page="<?php if(isset($pagid)) echo $pagid;  ?>" data-max-page="0" />
                <a href="#" class="next" data-action="next">&rsaquo;</a>
                <a href="#" class="last" data-action="last">&raquo;</a>                                       
            </div> 
  </div>  
<?php endif; ?>


            