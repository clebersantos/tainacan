<?php include_once ('js/menu_right_js.php'); $not_showed = false;
?>
 <!-- TAINACAN: widgets do menu esquerdo -->   
 
 
   <!--div>
                                <div class="panel panel-default clear">
                                    <div class="panel-heading" style="border-bottom: 0px;display:block;">
                                        <span class="glyphicon glyphicon-tags color_icon"></span>&nbsp;&nbsp;<?php _e('Filters','tainacan'); ?>&nbsp;&nbsp;
                                    </div>
                                </div>
                            </div>
 <?php  if($has_tree__): ?> 
        <?php if(isset($tree['socialdb_collection_facet_widget_tree'])&&$tree['socialdb_collection_facet_widget_tree']=='dynatree'): ?> 
                            <div id="dynatree_filters">
                            </div>
                            <div id="dynatree">
                            </div>
         <?php elseif(isset($tree['socialdb_collection_facet_widget_tree'])&&$tree['socialdb_collection_facet_widget_tree']=='hypertree'): ?> 
                            <div id="hypertree" style="display: none;"></div>
        <?php elseif(isset($tree['socialdb_collection_facet_widget_tree'])&&$tree['socialdb_collection_facet_widget_tree']=='spacetree'): ?>                    
                            <div id="spacetree" style="display: none;"></div>
                            <div id="spacetree_opt" style="display:none;">
                                <h4>Orienta&ccedil;&atilde;o</h4>
                                <table>
                                    <tr>
                                        <td>
                                            <label for="r-left">Left </label>
                                        </td>
                                        <td>
                                            <input type="radio" id="r-left" name="orientation" checked="checked" value="left" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="r-top">Top </label>
                                        </td>
                                        <td>
                                            <input type="radio" id="r-top" name="orientation" value="top" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="r-bottom">Bottom </label>
                                        </td>
                                        <td>
                                            <input type="radio" id="r-bottom" name="orientation" value="bottom" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="r-right">Right </label>
                                        </td>
                                        <td>
                                            <input type="radio" id="r-right" name="orientation" value="right" />
                                        </td>
                                    </tr>
                                </table>
                                <div style="display:none;">
                                    <h4>Selection Mode</h4>
                                    <table>
                                        <tr>
                                            <td>
                                                <label for="s-normal">Normal </label>
                                            </td>
                                            <td>
                                                <input type="radio" id="s-normal" name="selection" checked="checked" value="normal" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="s-root">Set as Root </label>
                                            </td>
                                            <td>
                                                <input type="radio" id="s-root" name="selection" value="root" />
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div <?php if ($_GET['nav'] != "treemap") {
                        echo "style='display:none;'";
                    } ?>> <strong>* <?php _e('To return to the previous map, just click with the right mouse button.','tainacan'); ?></strong> </div><br>
                    
        <?php elseif(isset($tree['socialdb_collection_facet_widget_tree'])&&$tree['socialdb_collection_facet_widget_tree']=='treemap'): ?>
                            <div id="treemap" style="display:none;"></div>
                            <div id="treemap_opt" style="display:none">

                                <table>
                                    <tr>
                                        <td>
                                            <label for="r-sq">Squarified </label>
                                        </td>
                                        <td>
                                            <input type="radio" id="r-sq" name="layout" checked="checked" value="left" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="r-st">Strip </label>
                                        </td>
                                        <td>
                                            <input type="radio" id="r-st" name="layout" value="top" />
                                        </td>
                                    <tr>
                                        <td>
                                            <label for="r-sd">SliceAndDice </label>
                                        </td>
                                        <td>
                                            <input type="radio" id="r-sd" name="layout" value="bottom" />
                                        </td>
                                    </tr>
                                </table>

                            </div>
                            <a id="back" <?php if ($_GET['nav'] != "treemap2") {
                        echo "style='display:none;'";
                    } ?> href="#" class="theme button white">Go to Parent</a>
                            <div id="rg_infovis" style="display:none;"></div>
                            <div id="inner-details" style="display:none;"></div>
                            <div id="log" style="display:none;"></div>
                            
          <?php endif; // END if dos tipos de tree ?> 
<?php endif; // END if dos tipos de tree ?> 
        -->                    
<?php  foreach ($facets as $facet): ?>
  
    <?php if($facet['widget']=='tree'&&!$not_showed): $not_showed = true  ?> 
                <div>
                     <!-- TAINACAN: panel para adicao de categorias e tags -->
                    <div class="panel panel-default clear">
                        <div class="panel-heading" style="border-bottom: 0px;display:block;">
                            <span class="glyphicon glyphicon-tags color_icon"></span>&nbsp;&nbsp;<?php _e('Filters','tainacan'); ?>&nbsp;&nbsp;
                            <div class="btn-group">
                                <button style="font-size:11px;" id="btnGroupVerticalDrop1" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    <span class="glyphicon glyphicon-plus color_icon"></span>
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu" aria-labelledby="btnGroupVerticalDrop1">
                                    <li><a onclick="showModalFilters('add_category');" href="#submit_filters_add_category"><span class="glyphicon glyphicon-tree-deciduous"></span>&nbsp;<?php _e('Add Facet'); ?></a></li>
                                    <!--li><a onclick="showModalFilters('add_property');" href="#submit_filters_add_property"><span class="glyphicon glyphicon-th-list"></span>&nbsp;<?php _e('Add Property'); ?></a></li-->
                                    <li><a onclick="showModalFilters('add_tag');" href="#submit_filters_add_tag"><span class="glyphicon glyphicon-tag"></span>&nbsp;<?php _e('Add Tag','tainacan'); ?></a></li>
                                </ul>
                            </div>
                                        <!--div class="btn-group" style="margin-left:5px;">
                                            <a href="#" id="btnCollapseAll"><span class="glyphicon glyphicon-collapse-up filtros"> </a> <a href="#" id="btnExpandAll"><span class="glyphicon glyphicon-collapse-down filtros"> </a>
                                        </div-->
                                        <div class="dropdown" style="float:right;">
                                            <button style="font-size:11px;" id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn btn-default dropdown-toggle">
                                                <!--Alterar Navega&ccedil;&atilde;o--> 
                    <?php  _e('Nav'); ?><span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                                                <!-- TAINACAN: tipos de navegacao avancada  --><li><a href="<?php echo get_the_permalink() . "?nav=regular"; ?>"><span class="glyphicon glyphicon-th-list"></span>&nbsp;<?php _e('Regular'); ?></a></li>
                                                <li><a href="<?php echo get_the_permalink() . "?nav=hypertree"; ?>"><span class="glyphicon glyphicon-tree-conifer"></span>&nbsp;<?php _e('Hypertree'); ?></a></li>
                                                <li><a href="<?php echo get_the_permalink() . "?nav=spacetree"; ?>"><span class="glyphicon glyphicon-tree-conifer"></span>&nbsp;<?php _e('Spacetree'); ?></a></li>
                                                <li><a href="<?php echo get_the_permalink() . "?nav=treemap"; ?>"><span class="glyphicon glyphicon-tree-conifer"></span>&nbsp;<?php _e('Treemap'); ?></a></li>
                                                <li><a href="<?php echo get_the_permalink() . "?nav=rgraph"; ?>"><span class="glyphicon glyphicon-tree-conifer"></span>&nbsp;<?php _e('RGraph'); ?></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <!-- TAINACAN: os filtros do dynatree eram mostrados neste local -- desativado -->
                            <div id="dynatree_filters">
                            </div>
                            <!-- TAINACAN: arvore montado nesta div pela biblioteca dynatree, html e css neste local totamente gerado pela biblioteca -->
                            <div id="dynatree">
                            </div>
                            <br>
    <?php elseif($facet['widget']=='range'):   ?> 
            <!-- TAINACAN: widget para realizacao de busca nos items  -->
        <div class="form-group">
            <label for="object_tags" class="title-pipe"><?php echo $facet['name']; ?></label><br>
            <?php foreach ($facet['options'] as $range): ?>
                <a href="#" onclick="wpquery_range('<?php echo $facet['id'] ?>','<?php echo $facet['type'] ?>','<?php echo $range['value_1'] ?>','<?php echo $range['value_2'] ?>')"><?php echo $range['value_1'] . ' ' . __('until','tainacan') . ' ' . $range['value_2']; ?></a><br>
            <?php endforeach; ?>
        </div>
    <?php elseif($facet['widget']=='from_to'): ?>
            <!-- TAINACAN: widget para realizacao de busca nos items  -->
             <div class="form-group">
                <label for="object_tags" class="title-pipe"><?php echo $facet['name']; ?></label><br>
                    <?php if($facet['type']=='date'){ ?>
                        <?php _e('From','tainacan') ?>
                        <input size="7" type="text" class="input_date form-control" value="" placeholder="dd/mm/aaaa"
                               id="facet_<?php echo $facet['id']; ?>_1" name="facet_<?php echo $facet['id']; ?>_1">
                        <?php _e('until','tainacan') ?>
                        <input type="text" class="input_date form-control" size="7" value="" placeholder="dd/mm/aaaa"
                               id="facet_<?php echo $facet['id']; ?>_2" name="facet_<?php echo $facet['id']; ?>_2"> <br />
                        <button class="tainacan-filter-range" onclick="wpquery_fromto('<?php echo $facet['id']; ?>','date');" >
                            <?php _e('Filter', 'tainacan') ?> <span class="glyphicon glyphicon-arrow-right"></span>
                        </button>
                    <?php }elseif($facet['type']=='numeric'){ ?>
                    <input type="numeric" value="" id="facet_<?php echo $facet['id']; ?>_1" name="facet_<?php echo $facet['id']; ?>_1"> <?php _e('until','tainacan') ?> <input type="numeric" value="" id="facet_<?php echo $facet['id']; ?>_2" name="facet_<?php echo $facet['id']; ?>_2"><button onclick="wpquery_fromto('<?php echo $facet['id']; ?>','numeric');" ><span class="glyphicon glyphicon-arrow-right"></span></button>
                    <?php }else{ ?>
                    <input type="text" value="" id="facet_<?php echo $facet['id']; ?>_1" name="facet_<?php echo $facet['id']; ?>_1"> <?php _e('until','tainacan') ?> <input type="text" value="" id="facet_<?php echo $facet['id']; ?>_2" name="facet_<?php echo $facet['id']; ?>_2"><button onclick="wpquery_fromto('<?php echo $facet['id']; ?>','text');" ><span class="glyphicon glyphicon-arrow-right"></span></button>
                    <?php }?>
            </div>
      <?php elseif($facet['widget']=='multipleselect'||$facet['widget']=='searchbox'): ?> 
            <!-- TAINACAN: widget para realizacao de busca nos items  -->
             <div class="form-group">
                <label for="object_tags" class="title-pipe"><?php echo $facet['name']; ?></label>
                    <input type="text" onkeyup="autocomplete_menu_right('<?php echo $facet['id']; ?>');" id="autocomplete_multipleselect_<?php echo $facet['id']; ?>" placeholder="<?php _e('Type the three first letters of the object of this collection ','tainacan'); ?>"  class="chosen-selected form-control"  />
                    <select style="display: none;" onclick="clear_autocomplete_menu_right(this,'<?php echo $facet['id']; ?>');" id="multipleselect_value_<?php echo $facet['id']; ?>" multiple class="chosen-selected2 form-control" style="height: auto;" name="multipleselect_value_<?php echo $facet['id']; ?>[]"  >
                   </select>
             </div>
      <?php elseif($facet['widget']=='radio'): ?> 
            <!-- TAINACAN: widget para realizacao de busca nos items  -->
             <div class="form-group">
                <label for="object_tags" class="title-pipe"><?php echo $facet['name']; ?></label>
                    <?php foreach($facet['categories'] as $category):  ?>
                        <input type="radio" onchange="wpquery_radio(this,'<?php echo $facet['id']; ?>');"  value="<?php echo $facet['id']. get_option('socialdb_divider').$category->term_id ; ?>" name="facet_<?php echo $facet['id']; ?>">&nbsp; <?php echo $category->name; ?>
                    <?php endforeach;  ?>
             </div>

        <?php elseif($facet['widget']=='checkbox'): ?> 
                <!-- TAINACAN: widget para realizacao de busca nos items  -->
                 <div class="form-group">
                     <label for="object_tags" class="title-pipe"><?php echo $facet['name']; ?></label><br>
                        <?php foreach($facet['categories'] as $category):  ?>
                            <input type="checkbox" value="<?php echo $category->term_id ; ?>" onchange="wpquery_checkbox(this,'<?php echo $facet['id']; ?>');" name="facet_<?php echo $facet['id']; ?>[]">&nbsp; <?php echo $category->name; ?><br>
                        <?php endforeach;  ?>
                 </div>
         <?php elseif($facet['widget']=='selectbox'): ?> 
        <!-- TAINACAN: widget para realizacao de busca nos items  -->
         <div class="form-group">
            <label for="object_tags" class="title-pipe"><?php echo $facet['name']; ?></label>
            <select class="form-control" onchange="wpquery_select(this,'<?php echo $facet['id']; ?>');" name="facet_<?php echo $facet['id']; ?>">
                   <option value="">  <?php echo __('Select...','tainacan'); ?></option>
                <?php foreach($facet['categories'] as $category):  ?>
                   <option value="<?php echo $category->term_id ; ?>" >  <?php echo $category->name; ?></option>
                <?php endforeach;  ?>
               </select>
         </div>

        <!-- TAINACAN: widget para realizacao de busca nos items  -->
        <?php
        elseif($facet['widget']=='menu'):

        $facet_menu_style =  get_post_meta( $collection_id, "socialdb_collection_facet_" .  $facet['id'] . "_menu_style", true );
        $f_menu = str_replace( "menu_style_", "", $facet_menu_style);
        $json_url = $this->get_menu_style_json( $f_menu );
        ?>
            <script type="text/javascript">
                var url = '<?php echo $json_url ?>';
                $.getJSON( url, function(data) {
                    data.id = '<?php echo $f_menu ?>';
                    var images_path = '<?php echo get_template_directory_uri() ?>' + '/extras/cssmenumaker/menus/' + data.id + '/images/';
                    var formatted_css = data.css.replace(/#menu_class#/g, "#appended-" + data.id );
                    formatted_css = formatted_css.replace(/#cssmenu/g, "#appended-" + data.id);
                    formatted_css = formatted_css.replace(/#include_path#/g, images_path);

                    var css_tags = formatted_css.match(/^@[a-z_]*/igm);
                    var tags_values = findCSSTags(formatted_css);

                    $(css_tags).each( function(idx, el) {
                        if( el != "@charset" ) {
                            var css_tag = el.replace('@', '');
                            var css_value = tags_values[css_tag];
                            var regex = new RegExp( el, "gim" );
                            formatted_css = formatted_css.replace( regex , css_value );
                        }
                    });

                    // Remove [[ e nome da var, deixa apenas HEX e ]]
                    formatted_css = formatted_css.replace(/(\[\[[a-z_: ]+)/gi, "");
                    // Remove ]]
                    formatted_css = formatted_css.replace(/]/gi, "");
                    var target_id = 'appended-' + data.id;
                    $('head').append("<style type=\"text/css\">" + formatted_css + "</style>");
                    var target_menu = "#menu_selected_result-" + data.id + " .cssmenumaker-menu";
                    $(target_menu).attr('id', target_id );
                });
            </script>

        <!-- TAINACAN: widget para realizacao de busca nos items  -->
        <div id="menu_selected_result-<?php echo $f_menu ?>" class="form-group">
            <label class="title-pipe"> <?php echo $facet['name']; ?> </label>
            <div id="tainacan-cssmenu-<?php echo $f_menu ?>" class="cssmenumaker-menu align-left">
                <ul> <?php echo $facet['html'];  ?> </ul>
            </div>
        </div>

           <?php elseif ($facet['widget'] == 'cloud'):  ?> 
                        <!-- Script inline para ser dinamico -->
                        <script>
                            $(function () {
                                var words = [];
                                var array = JSON.parse('<?php echo $facet['json']; ?>');
                                $.each(array,function(index,value){
                                    words.push({
                                        text:value.text,
                                        weight:value.weight,
                                        handlers: {
                                            click: function() {
                                              wpquery_cloud(value.value,value.facet_id);
                                            }
                                      }
                                  });
                                });
                                $('#cloud_<?php echo $facet['id']; ?>').jQCloud(words,{height: 200});
                            });
                        </script>  
                        <label for="object_tags" class="title-pipe"><?php echo $facet['name']; ?></label>
                        <div id="cloud_<?php echo $facet['id']; ?>">
                        </div>
                    <?php endif; ?>           
<?php endforeach; 
?>	