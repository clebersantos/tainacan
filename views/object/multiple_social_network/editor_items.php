<?php
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/editor_items_js.php');

$properties_terms_radio = [];
$properties_terms_tree = [];
$properties_terms_selectbox = [];
$properties_terms_checkbox = [];
$properties_terms_multipleselect = [];
$properties_terms_treecheckbox = [];
$data_properties_id= [];
$object_properties= [];
$term_properties_id= [];
$all_properties= [];
$files= [];
$filesImage= [];
$filesVideo= [];
$filesAudio= [];
$filesPdf= [];
$filesOther= [];
?>
<div class="container-fluid row">
        
        <!----------------------------- BUTTONS -------------------------------------->
        <div class="col-md-3">
            <!--button onclick="upload_more_files()" class="btn btn-danger"><span class="glyphicon glyphicon-alert"></span>&nbsp;<?php _e('Upload more files','tainacan') ?></button-->
        </div>
        <div style="padding-bottom: 20px;" class="col-md-9 row">
            <div class="btn-group">
                <button id="selectOptions" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <?php _e('Select','tainacan') ?> <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li><a onclick='selectAll()'  style="cursor: pointer;"> <?php _e('All','tainacan') ?></a></li>
                  <li><a onclick='unselectAll()'  style="cursor: pointer;"><?php _e('None','tainacan') ?></a></li>
                </ul>
            </div>    
            <button id="removeSelectedButton"  onclick='removeSelected()' type="button" class="btn btn-default" >
                <span  class="glyphicon glyphicon-trash"></span>
            </button>
            <button id="buttonSelectedAttachments" style="display: none;" onclick='selectedAttachments()' type="button" class="btn btn-default" >
                <?php _e('Select Attachments','tainacan') ?>
            </button>
            <button id="buttonBackItems" style="display: none;" onclick='backItemsEditting()' type="button" class="btn btn-default" >
                <?php _e('Edit Items','tainacan') ?>
            </button>
             <button onclick="back_main_list_socialnetwork();"class="btn btn-default pull-right"><?php _e('Cancel','tainacan') ?></button>
        </div>
        <!----------------------------- BUTTONS -------------------------------------->
        <!-------------- METADADOS - BLOCO ESQUERDO (COL-MD-3) --------------------->
        <div style="display:none;border-top-style: solid;border-top-color: #e8e8e8;" id='form_properties_items' class="col-md-3">
            <h3 style="display:none;" id='labels_items_selected' ><?php _e('Editting ','tainacan') ?>
                <span id='number_of_items_selected'></span>
                <?php _e(' item/items ','tainacan') ?>
            </h3>
            <!---------------- FORMULARIO COM OS METADADOS DOS ITEMS -------------------------------------------------->
             <!--div class="list-group" id="accordion" aria-multiselectable="true">  
                <div class="list-group-item list-head" id="headingOne">  
                    <a style="cursor: pointer;" class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne" for="collapseOne">
                        <abel for="object_name">
                            <?php _e('Item name','tainacan'); ?>
                        </label>
                    </a>
                </div>  
                <div id="collapseOne" class="collapse in" aria-labelledby="headingOne">
                    <div class="list-group list-group-item form-group">   
                        <input class="form-control" 
                               type="text" 
                               class="form-control" 
                               id="multiple_object_name" 
                               name="object_name" 
                               required="required" 
                               onkeyup="setTitle(this)"
                               placeholder="<?php _e('Item name','tainacan'); ?>">
                    </div> 
                </div>
            </div-->  
       <div id="accordion_socialnetwork" class="multiple-items-accordion"> 
            <h2> <?php _e('Item name','tainacan'); ?> </h2>
            <div class="form-group">                
                <input class="form-control" 
                       type="text" 
                       class="form-control" 
                       id="multiple_object_name" 
                       name="object_name" 
                       required="required" 
                       onkeyup="setTitle(this)"
                       placeholder="<?php _e('Item name','tainacan'); ?>">
            </div> 
            <!-- TAINACAN: a descricao do item -->
            <h2> <?php _e('Item Description','tainacan'); ?> </h2>
            <div id="object_description" class="form-group">          
                <textarea class="form-control" 
                          id="multiple_object_description" 
                          onkeyup="setDescription(this)"
                           name="multiple_object_description" ></textarea>     
            </div>

            <h2> <?php _e('Object tags','tainacan'); ?> </h2>
            <div class="form-group">                
                <input onkeyup="setTags(this)" type="text" class="form-control" id="multiple_object_tags" name="object_tags" >
                <span style="font-size: 8px;" class="label label-default">*<?php _e('The set of tags may be inserted by commas','tainacan') ?></span>
           </div> 

           <h2> <?php _e('Object Source','tainacan'); ?> </h2>
            <div class="form-group">                
                <input onkeyup="setSource(this)" type="text" class="form-control" id="multiple_object_source" name="object_source"  placeholder="<?php _e('Source of the item','tainacan') ?>">
           </div> 

        <?php
        // lista as propriedades de objeto da colecao atual
        if(isset($properties['property_object'])): ?>
            <!--h4><?php _e('Object Properties','tainacan'); ?></h4-->
            <?php foreach ($properties['property_object'] as $property) { 
                 $object_properties[] = $property['id']; 
                 $all_properties[] = $property['id'];
                 ?>
                <?php //if($property['metas']['socialdb_property_object_is_facet']=='false'): ?>
                    <h2> <?php echo $property['name']; ?> </h2>
                    <div class="form-group">                        
                        <a class="btn btn-primary btn-xs" href="<?php echo get_permalink($property['metas']['collection_data'][0]->ID); ?>"><?php _e('Add new','tainacan'); ?><?php echo ' '.$property['metas']['collection_data'][0]->post_title; ?></a>
                            <input type="text" 
                                   onkeyup="multiple_autocomplete_object_property_add('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>');" 
                                   id="multiple_autocomplete_value_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" 
                                   placeholder="<?php _e('Type the three first letters of the object of this collection ','tainacan'); ?>"  
                                   class="chosen-selected form-control"  />  
                            <select onclick="clear_select_object_property(this,'<?php echo $property['id']; ?>');" 
                                    id="multiple_property_value_<?php echo $property['id']; ?>_<?php echo $object_id; ?>_add" 
                                    multiple class="chosen-selected2 form-control" 
                                    style="height: auto;" 
                                    name="socialdb_property_<?php echo $property['id']; ?>[]" 
                                        <?php if($property['metas']['socialdb_property_required']=='true'): echo 'required="required"'; endif; ?> >
                                <?php if(!empty($property['metas']['objects'])){ ?>     

                                <?php }else { ?>   
                                     <option value=""><?php _e('No objects added in this collection','tainacan'); ?></option>
                                <?php } ?>       
                           </select>
                     </div>  
                <?php// endif; ?>
            <?php  } ?>
        <?php endif; 
        //lista as propriedades de dados da colecao atual
        if(isset($properties['property_data'])): ?>
            <!--h4><?php _e('Data properties','tainacan'); ?></h4-->
            <?php foreach ($properties['property_data'] as $property) { 
                $data_properties_id[] = $property['id'];  
                $data_properties[] = ['id'=>$property['id'],'default_value'=>$property['metas']['socialdb_property_default_value']];  
                $all_properties[] = $property['id']; ?>
                
                <h2> <?php echo $property['name']; ?> </h2> 
                <div class="form-group">                    
                        <?php if($property['type']=='text'){ ?>     
                                <input onblur="setPropertyData(this,'<?php echo $property['id']  ?>')" 
                                       type="text" 
                                       id='multiple_socialdb_property_<?php echo $property['id']; ?>'
                                       class="form-control" 
                                       value="<?php if($property['metas']['socialdb_property_default_value']): echo $property['metas']['socialdb_property_default_value']; endif; ?>" 
                                       name="socialdb_property_<?php echo $property['id']; ?>"
                                       <?php if($property['metas']['socialdb_property_required']=='true'): echo 'required="required"'; endif; ?>>
                        <?php }elseif($property['type']=='textarea') { ?>   
                              <textarea onblur="setPropertyData(this,'<?php echo $property['id']  ?>')"
                                        class="form-control" 
                                         id='multiple_socialdb_property_<?php echo $property['id']; ?>'
                                        name="socialdb_property_<?php echo $property['id']; ?>"
                                        <?php if($property['metas']['socialdb_property_required']=='true'): echo 'required="required"'; endif; ?>><?php if($property['metas']['socialdb_property_default_value']): echo $property['metas']['socialdb_property_default_value']; endif; ?>
                              
                              </textarea>
                         <?php }elseif($property['type']=='numeric') { ?>   
                              <input onblur="setPropertyData(this,'<?php echo $property['id']  ?>')"
                                     type="number" 
                                     onkeypress='return onlyNumbers(event)'
                                     id='multiple_socialdb_property_<?php echo $property['id']; ?>'
                                     value="<?php if($property['metas']['socialdb_property_default_value']): echo $property['metas']['socialdb_property_default_value']; endif; ?>" 
                                     class="form-control"
                                     name="socialdb_property_<?php echo $property['id']; ?>" 
                                     <?php if($property['metas']['socialdb_property_required']=='true'): echo 'required="required"'; endif; ?>>
                         <?php }elseif($property['type']=='autoincrement') {  ?>   
                              <input onblur="setPropertyData(this,'<?php echo $property['id']  ?>')"
                                     disabled="disabled"  
                                      id='multiple_socialdb_property_<?php echo $property['id']; ?>'
                                     type="number" 
                                     class="form-control" 
                                     name="only_showed_<?php echo $property['id']; ?>" value="<?php if(is_numeric($property['metas']['socialdb_property_data_value_increment'])): echo $property['metas']['socialdb_property_data_value_increment']+1; endif; ?>">
                              <!--input type="hidden"  name="socialdb_property_<?php echo $property['id']; ?>" value="<?php if($property['metas']['socialdb_property_data_value_increment']): echo $property['metas']['socialdb_property_data_value_increment']+1; endif; ?>" -->
                        <?php }else{ ?>
                              <input onblur="setPropertyData(this,'<?php echo $property['id']  ?>')"
                                     type="date" 
                                      id='multiple_socialdb_property_<?php echo $property['id']; ?>'
                                     value="<?php if($property['metas']['socialdb_property_default_value']): echo $property['metas']['socialdb_property_default_value']; endif; ?>" 
                                     class="form-control" 
                                     name="socialdb_property_<?php echo $property['id']; ?>" <?php if($property['metas']['socialdb_property_required']=='true'): echo 'required="required"'; endif; ?>>
                        <?php } ?> 
                </div>              
             <?php  } ?>
        <?php endif; 
        //lista as propriedades de dados
         if((isset($properties['property_term'])&&count($properties['property_term'])>1)||(count($properties['property_term'])==1&&!empty($properties['property_term'][0]['has_children']))): ?>
            <!--h4><?php _e('Term properties','tainacan'); ?></h4-->
            <?php foreach ( $properties['property_term'] as $property ) { 
                $all_properties[] = $property['id'];
                $term_properties_id[] = $property['id'];  
            ?>

                <h2> <?php echo $property['name']; ?></h2>
                <div class="form-group">                     
                        <p><?php if($property['metas']['socialdb_property_help']){ echo $property['metas']['socialdb_property_help']; } ?></p> 
                        <?php if($property['type']=='radio'){ 
                            $properties_terms_radio[] = $property['id']; 
                            ?>
                            <div id='multiple_field_property_term_<?php echo $property['id']; ?>'></div>
                            <?php
                         }elseif($property['type']=='tree') { 
                            $properties_terms_tree[] = $property['id']; 
                             ?>
                             <div style='height: 150px;overflow: scroll;'  id='multiple_field_property_term_<?php echo $property['id']; ?>'></div>
                                 <!--select name='socialdb_propertyterm_<?php echo $property['id']; ?>' size='2' class='col-lg-6' id='socialdb_propertyterm_<?php echo $property['id']; ?>' <?php if($property['metas']['socialdb_property_required']=='true'): echo 'required="required"'; endif; ?>></select-->
                            <?php
                         }elseif($property['type']=='selectbox') { 
                            $properties_terms_selectbox[] = $property['id']; 
                             ?>
                             <select onchange="setCategoriesSelect('<?php echo $property['id']; ?>',this)" class="form-control" name="multiple_socialdb_propertyterm_<?php echo $property['id']; ?>" id='multiple_field_property_term_<?php echo $property['id']; ?>' <?php if($property['metas']['socialdb_property_required']=='true'): echo 'required="required"'; endif; ?>>
                               
                             </select>
                            <?php
                          }elseif($property['type']=='checkbox') { 
                            $properties_terms_checkbox[] = $property['id']; 
                             ?>
                            <div id='multiple_field_property_term_<?php echo $property['id']; ?>'></div>
                            <?php
                          }elseif($property['type']=='multipleselect') { 
                            $properties_terms_multipleselect[] = $property['id']; 
                             ?>
                             <select onchange="setCategoriesSelectMultiple('<?php echo $property['id']; ?>',this)" multiple class="form-control" name="multiple_socialdb_propertyterm_<?php echo $property['id']; ?>" id='multiple_field_property_term_<?php echo $property['id']; ?>' <?php if($property['metas']['socialdb_property_required']=='true'): echo 'required="required"'; endif; ?>></select>
                            <?php
                          }elseif($property['type']=='tree_checkbox') { 
                            $properties_terms_treecheckbox[] = $property['id']; 
                             ?>
                            <div style='height: 150px;overflow: scroll;'   id='multiple_field_property_term_<?php echo $property['id']; ?>'></div>
                                 <!--select multiple size='6' class='col-lg-6' name='socialdb_propertyterm_<?php echo $property['id']; ?>[]' id='socialdb_propertyterm_<?php echo $property['id']; ?>' <?php if($property['metas']['socialdb_property_required']=='true'): echo 'required="required"'; endif; ?>></select -->
                           
                            <?php
                          }
                         ?> 
                </div>              
             <?php  } ?>
        <?php endif; ?>

        </div> <!-- Closes #accordion --> 
        <?php if(isset($all_ids)): ?>
        <input type="hidden" name="properties_id" value="<?php echo $all_ids; ?>">
        <?php endif; ?>

    </div> 
    <div id='no_properties_items'  class="col-md-3">
         <h3 ><?php _e('Select items to edit...','tainacan') ?>
         </h3>
    </div>
        <div id='selectingAttachment'style="display:none"  class="col-md-3">
         <h3 ><?php _e('Select attachments to ','tainacan') ?>
             <span id="nameItemAttachment"></span>
         </h3>
    </div>
<!------------------------------- LISTA ITEMS UPADOS - BLOCO CENTRO DIREITO (COL-MD-9) -------------------------------------------------------------->
    <form id='sumbit_multiple_items'>
        <div class='col-md-9' id="no_item_uploaded" style='display:none;'>
            <h3 style="text-align: center;"><?php _e('No items uploaded','tainacan') ?></h3>
        </div>
        <div id="selectable" class='col-md-9 row' style='padding-bottom: 20px;height: 100%;background-color: #e8e8e8;'>
            <?php 
            // images
            if(is_array($items['image'])){ 
                ?>
                <div  id="container_images"class='col-md-12 row'>
                    <h3><input class="class_selected_items" type='checkbox' id='selectAllImages' onclick="selectImages()" value='#'> &nbsp;<?php _e('Image Files','tainacan') ?></h3><hr>
                <?php
                    foreach ($items['image'] as $file) { 
                        $files[] = $file['ID'];
                        $filesImage[] = $file['ID'];
                        ?>
                        <div  id="wrapper_<?php echo $file['ID'] ?>" class="col-md-3" style="padding-top: 20px;">
                            <center>
                                <div class="item" style="padding-top: 20px;padding-bottom: 20px;cursor: pointer;" id="panel_<?php echo $file['ID'] ?>"  onclick="focusItem('<?php echo $file['ID'] ?>')" ><!-- container do item -->   
                                    <input style="display:none" class="class_selected_items" id="item_option_<?php echo $file['ID'] ?>" onchange="selectedItems()" type="checkbox" name="selected_items"  value="<?php echo $file['ID'] ?>">
                                    <input id="attachment_option_<?php echo $file['ID'] ?>"  onchange="manipulateAttachaments('<?php echo $file['ID'] ?>')" class="class_checkboxAttachments" style="display:none" type="checkbox" name="checkboxAttachments"  value="<?php echo $file['ID'] ?>">
                                    <?php 
                                    if(get_the_post_thumbnail($file['ID'], 'thumbnail')){
                                       echo get_the_post_thumbnail($file['ID'], 'thumbnail');
                                    }else{ ?>
                                          <img src="<?php echo get_item_thumbnail_default($file['ID']); ?>" class="img-responsive">
                                    <?php }  ?> 
                                </div>     
                                <input required="required" style="margin-top: 10px;" placeholder="<?php _e('Add a title','tainacan') ?>" type="text" id='title_<?php echo $file['ID'] ?>' name='title_<?php echo $file['ID'] ?>' value='<?php echo $file['name'] ?>'>
                                <!-- Hidden para as categorias, tags e attachments  -->
                                <input type="hidden" id="source_<?php echo $file['ID'] ?>" name="source_<?php echo $file['ID'] ?>" value=''>
                                <input type="hidden" name="type_<?php echo $file['ID'] ?>" value='image'>
                                <input type="hidden" id='parent_<?php echo $file['ID'] ?>' name="parent_<?php echo $file['ID'] ?>" value=''>
                                <input type="hidden" id='attachments_<?php echo $file['ID'] ?>' name="attachments_<?php echo $file['ID'] ?>" value=''>
                                <input type="hidden" id='description_<?php echo $file['ID'] ?>' name="description_<?php echo $file['ID'] ?>" value='<?php  echo $file['content'] ?>'>
                                <input type="hidden" id='categorias_<?php echo $file['ID'] ?>' name="categorias_<?php echo $file['ID'] ?>" value=''>
                                <input type="hidden" id='tags_<?php echo $file['ID'] ?>' name="tags_<?php echo $file['ID'] ?>" value='<?php  echo $file['tags'] ?>'>
                                <!-- hiddens para valores das propriedades de dados dos items a serem criados -->
                                <?php 
                                if(is_array($data_properties)):
                                    foreach ($data_properties as $value) { ?>
                                         <input type="hidden" 
                                                name='socialdb_property_<?php echo $value['id'] ?>_<?php echo $file['ID'] ?>' 
                                                id='socialdb_property_<?php echo $value['id'] ?>_<?php echo $file['ID'] ?>'
                                                value='<?php if($value['default_value']&&!empty($value['default_value'])): echo $value['default_value']; endif; ?>'>
                                <?php  } 
                                endif;   
                                ?>
                                <!-- hiddens para valores das propriedades de OBJETO dos items a serem criados -->
                                <?php 
                                if(is_array($object_properties)):
                                    foreach ($object_properties as $value) { ?>
                                         <input type="hidden" 
                                                name='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>' 
                                                id='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>'
                                                value=''>
                                <?php  } 
                                endif;   
                                ?>    
                                <!-- hiddens para valores das propriedades de TERMO dos items a serem criados -->
                                <?php 
                                if(is_array($term_properties_id)):
                                    foreach ($term_properties_id as $value) { ?>
                                         <input type="hidden" 
                                                name='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>' 
                                                id='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>'
                                                value=''>
                                <?php  } 
                                endif;   
                                ?>    
                            </center>          
                        </div>    
                      <?php         
                    }
                ?>
                </div>
                <?php
            }
            // videos
            if(is_array($items['videos'])){ 
                ?>
                <div id="container_videos" class='col-md-12 row'>
                <h3><input class="class_selected_items" type='checkbox' id='selectAllVideo'  onclick="selectVideo()" value='#'> &nbsp;<?php _e('Videos Files','tainacan') ?></h3><hr>
                <?php
                    foreach ($items['videos'] as $file) { 
                        $files[] = $file['ID'];
                        $filesVideo[] = $file['ID'];
                        ?>
                        <div   id="wrapper_<?php echo $file['ID'] ?>" class="col-md-3" style="padding-top: 20px;">
                            <center><div class="item" style="padding-top: 20px;padding-bottom: 20px;cursor: pointer;" id="panel_<?php echo $file['ID'] ?>"  onclick="focusItem('<?php echo $file['ID'] ?>')" ><!-- container do item -->      
                               <input style="display:none" class="class_selected_items" id="item_option_<?php echo $file['ID'] ?>" onchange="selectedItems()" type="checkbox" name="selected_items" value="<?php echo $file['ID'] ?>" >
                               <input id="attachment_option_<?php echo $file['ID'] ?>" onchange="manipulateAttachaments('<?php echo $file['ID'] ?>')" class="class_checkboxAttachments" style="display:none" type="checkbox" name="checkboxAttachments"  value="<?php echo $file['ID'] ?>">
                               <?php 
                                 if(get_the_post_thumbnail($file['ID'], 'thumbnail')){
                                    echo get_the_post_thumbnail($file['ID'], 'thumbnail');
                                 }else{ ?>
                                       <img src="<?php echo get_item_thumbnail_default($file['ID']); ?>" class="img-responsive">
                                 <?php }  ?>  
                               </div>     
                               <input required="required" placeholder="<?php _e('Add a title','tainacan') ?>" type="text" id='title_<?php echo $file['ID'] ?>' name='title_<?php echo $file['ID'] ?>' value='<?php echo $file['name'] ?>'>    
                               <!-- Hidden para as categorias, tags e attachments  -->
                               <input type="hidden" id="source_<?php echo $file['ID'] ?>" name="source_<?php echo $file['ID'] ?>" value=''>
                               <input type="hidden" name="type_<?php echo $file['ID'] ?>" value='video'>
                               <input type="hidden" id='parent_<?php echo $file['ID'] ?>' name="parent_<?php echo $file['ID'] ?>" value=''>
                               <input type="hidden" id='attachments_<?php echo $file['ID'] ?>' name="attachments_<?php echo $file['ID'] ?>" value=''>
                               <input type="hidden" id='description_<?php echo $file['ID'] ?>' name="description_<?php echo $file['ID'] ?>" value='<?php  echo $file['content'] ?>'>
                               <input type="hidden" id='categorias_<?php echo $file['ID'] ?>' name="categorias_<?php echo $file['ID'] ?>" value=''>
                               <input type="hidden" id='tags_<?php echo $file['ID'] ?>' name="tags_<?php echo $file['ID'] ?>" value='<?php  echo $file['tags'] ?>'>
                               <!-- hiddens para valores das propriedades de dados dos items a serem criados -->
                               <?php 
                               if(is_array($data_properties)):
                                   foreach ($data_properties as $value) { ?>
                                        <input type="hidden" 
                                               name='socialdb_property_<?php echo $value['id'] ?>_<?php echo $file['ID'] ?>' 
                                               id='socialdb_property_<?php echo $value['id'] ?>_<?php echo $file['ID'] ?>'
                                               value='<?php if($value['default_value']&&!empty($value['default_value'])): echo $value['default_value']; endif; ?>'>
                               <?php  } 
                               endif;   
                               ?>
                               <!-- hiddens para valores das propriedades de OBJETO dos items a serem criados -->
                               <?php 
                               if(is_array($object_properties)):
                                   foreach ($object_properties as $value) { ?>
                                        <input type="hidden" 
                                               name='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>' 
                                               id='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>'
                                               value=''>
                               <?php  } 
                               endif;   
                               ?>      
                              <?php 
                               if(is_array($term_properties_id)):
                                   foreach ($term_properties_id as $value) { ?>
                                        <input type="hidden" 
                                               name='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>' 
                                               id='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>'
                                               value=''>
                               <?php  } 
                               endif;   
                               ?> 
                            </center>    
                        </div>    
                      <?php         
                    }
                ?>
                </div>
                <hr>
                <?php
            }
            // mostra os itens do tipo pdf
            if(is_array($items['pdf'])){ 
                ?>
                <div id="container_pdfs" class='col-md-12 row'>
                    <h3><input class="class_selected_items" type='checkbox' id='selectAllPdf' onclick="selectPdf()" value='#'> &nbsp;<?php _e('PDF Files','tainacan') ?></h3><hr>
                <?php
                    foreach ($items['pdf'] as $file) { 
                        $files[] = $file['ID'];
                        $filesPdf[] = $file['ID'];
                        ?>
                    <div  id="wrapper_<?php echo $file['ID'] ?>" class="col-md-3" style="padding-top: 20px;">
                            <center><div class="item"  style="padding-top: 20px;padding-bottom: 20px;cursor: pointer;" id="panel_<?php echo $file['ID'] ?>"  onclick="focusItem('<?php echo $file['ID'] ?>')" ><!-- container do item -->      
                                <input class="class_selected_items" 
                                       id="item_option_<?php echo $file['ID'] ?>" 
                                       onchange="selectedItems()" 
                                       type="checkbox" 
                                       style="display:none"
                                       name="selected_items" 
                                       value="<?php echo $file['ID'] ?>" >
                                <input id="attachment_option_<?php echo $file['ID'] ?>" onchange="manipulateAttachaments('<?php echo $file['ID'] ?>')" class="class_checkboxAttachments" style="display:none" type="checkbox" name="checkboxAttachments"  value="<?php echo $file['ID'] ?>">
                                <?php echo wp_get_attachment_image( $file['ID'],'thumbnail',1,['alt'   =>'' ] ); ?>  
                                 </div>     
                                <input required="required" placeholder="<?php _e('Add a title','tainacan') ?>" type="text" id='title_<?php echo $file['ID'] ?>' name='title_<?php echo $file['ID'] ?>' value='<?php echo $file['name'] ?>'>
                                <!-- Hidden para as categorias, tags e attachments  -->
                                <input type="hidden" id="source_<?php echo $file['ID'] ?>" name="source_<?php echo $file['ID'] ?>" value=''>
                                <input type="hidden" name="type_<?php echo $file['ID'] ?>" value='pdf'>
                                <input type="hidden" id='parent_<?php echo $file['ID'] ?>' name="parent_<?php echo $file['ID'] ?>" value=''>
                                <input type="hidden" id='attachments_<?php echo $file['ID'] ?>' name="attachments_<?php echo $file['ID'] ?>" value=''>
                                <input type="hidden" id='description_<?php echo $file['ID'] ?>' name="description_<?php echo $file['ID'] ?>" value=''>
                                <input type="hidden" id='categorias_<?php echo $file['ID'] ?>' name="categorias_<?php echo $file['ID'] ?>" value=''>
                                <input type="hidden" id='tags_<?php echo $file['ID'] ?>' name="tags_<?php echo $file['ID'] ?>" value='<?php  echo $file['tags'] ?>'>
                                <!-- hiddens para valores das propriedades de dados dos items a serem criados -->
                                <?php 
                                if(is_array($data_properties)):
                                    foreach ($data_properties as $value) { ?>
                                         <input type="hidden" 
                                                name='socialdb_property_<?php echo $value['id'] ?>_<?php echo $file['ID'] ?>' 
                                                id='socialdb_property_<?php echo $value['id'] ?>_<?php echo $file['ID'] ?>'
                                                value='<?php if($value['default_value']&&!empty($value['default_value'])): echo $value['default_value']; endif; ?>'>
                                <?php  } 
                                endif;   
                                ?>
                                <!-- hiddens para valores das propriedades de OBJETO dos items a serem criados -->
                                <?php 
                                if(is_array($object_properties)):
                                    foreach ($object_properties as $value) { ?>
                                         <input type="hidden" 
                                                name='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>' 
                                                id='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>'
                                                value=''>
                                <?php  } 
                                endif;   
                                ?>     
                                <?php 
                                if(is_array($term_properties_id)):
                                    foreach ($term_properties_id as $value) { ?>
                                         <input type="hidden" 
                                                name='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>' 
                                                id='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>'
                                                value=''>
                                <?php  } 
                                endif;   
                                ?> 
                            </center>               
                        </div>    
                      <?php         
                    }
                ?>
                </div>
                <?php
            }
              // AUDIO
            if(is_array($items['audio'])){ 
                ?>
                <div id="container_audios" class='col-md-12 row'>
                <h3><input class="class_selected_items" type='checkbox' id='selectAllAudio' onclick="selectAudio()" value='#'> &nbsp;<?php _e('Audio Files','tainacan') ?></h3><hr>
                <?php
                    foreach ($items['audio'] as $file) {
                        $files[] = $file['ID'];
                        $filesAudio[] = $file['ID'];
                        ?>
                        <div  id="wrapper_<?php echo $file['ID'] ?>" class="col-md-3" style="padding-top: 20px;">
                            <center><div class="item" style="padding-top: 20px;padding-bottom: 20px;cursor: pointer;" id="panel_<?php echo $file['ID'] ?>"  onclick="focusItem('<?php echo $file['ID'] ?>')" ><!-- container do item -->      
                                <input style="display:none" class="class_selected_items" id="item_option_<?php echo $file['ID'] ?>"  onchange="selectedItems()" type="checkbox" name="selected_items" value="<?php echo $file['ID'] ?>" >
                                <input id="attachment_option_<?php echo $file['ID'] ?>" onchange="manipulateAttachaments('<?php echo $file['ID'] ?>')" class="class_checkboxAttachments" style="display:none" type="checkbox" name="checkboxAttachments"  value="<?php echo $file['ID'] ?>">
                                <?php echo wp_get_attachment_image( $file['ID'],'thumbnail',1,['alt'   =>'' ] ); ?>  
                                 </div>     
                                <input required="required" placeholder="<?php _e('Add a title','tainacan') ?>" type="text" id='title_<?php echo $file['ID'] ?>' name='title_<?php echo $file['ID'] ?>' value='<?php echo $file['name'] ?>'>    
                                <!-- Hidden para as categorias, tags e attachments  -->
                                 <input type="hidden" id="source_<?php echo $file['ID'] ?>" name="source_<?php echo $file['ID'] ?>" value=''>
                                <input type="hidden" name="type_<?php echo $file['ID'] ?>" value='audio'>
                                <input type="hidden" id='parent_<?php echo $file['ID'] ?>' name="parent_<?php echo $file['ID'] ?>" value=''>
                                <input type="hidden" id='attachments_<?php echo $file['ID'] ?>' name="attachments_<?php echo $file['ID'] ?>" value=''>
                                <input type="hidden" id='description_<?php echo $file['ID'] ?>' name="description_<?php echo $file['ID'] ?>" value=''>
                                <input type="hidden" id='categorias_<?php echo $file['ID'] ?>' name="categorias_<?php echo $file['ID'] ?>" value=''>
                                <input type="hidden" id='tags_<?php echo $file['ID'] ?>' name="tags_<?php echo $file['ID'] ?>" value='<?php  echo $file['tags'] ?>'>
                                <!-- hiddens para valores das propriedades de dados dos items a serem criados -->
                                <?php 
                                if(is_array($data_properties)):
                                    foreach ($data_properties as $value) { ?>
                                         <input type="hidden" 
                                                name='socialdb_property_<?php echo $value['id'] ?>_<?php echo $file['ID'] ?>' 
                                                id='socialdb_property_<?php echo $value['id'] ?>_<?php echo $file['ID'] ?>'
                                                value='<?php if($value['default_value']&&!empty($value['default_value'])): echo $value['default_value']; endif; ?>'>
                                <?php  } 
                                endif;   
                                ?>
                                <!-- hiddens para valores das propriedades de OBJETO dos items a serem criados -->
                                <?php 
                                if(is_array($object_properties)):
                                    foreach ($object_properties as $value) { ?>
                                         <input type="hidden" 
                                                name='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>' 
                                                id='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>'
                                                value=''>
                                <?php  } 
                                endif;   
                                ?>
                                <?php 
                                if(is_array($term_properties_id)):
                                    foreach ($term_properties_id as $value) { ?>
                                         <input type="hidden" 
                                                name='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>' 
                                                id='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>'
                                                value=''>
                                <?php  } 
                                endif;   
                                ?>  
                             </center>  
                        </div>    
                      <?php         
                    }
                ?>
                </div>
                <hr>
                <?php
            }
             // OUTROS
            if(is_array($items['others'])){ 
                ?>
                <div id="container_others" class='col-md-12 row'>
                <h3><input class="class_selected_items" type='checkbox' id='selectAllOther' onclick="selectOther()" value='#'> &nbsp;<?php _e('Others Files','tainacan') ?></h3><hr>
                <?php
                    foreach ($items['others'] as $file) { 
                        $files[] = $file['ID'];
                        $filesOther[] = $file['ID'];
                        ?>
                        <div id="wrapper_<?php echo $file['ID'] ?>" class="col-md-3" style="padding-top: 20px;">
                            <center><div class="item" style="padding-top: 20px;padding-bottom: 20px;cursor: pointer;" id="panel_<?php echo $file['ID'] ?>"  onclick="focusItem('<?php echo $file['ID'] ?>')" ><!-- container do item -->      
                                <input style="display:none" class="class_selected_items" id="item_option_<?php echo $file['ID'] ?>"  onchange="selectedItems()" type="checkbox" name="selected_items" value="<?php echo $file['ID'] ?>" >
                                <input id="attachment_option_<?php echo $file['ID'] ?>" onchange="manipulateAttachaments('<?php echo $file['ID'] ?>')" class="class_checkboxAttachments" style="display:none" type="checkbox" name="checkboxAttachments"  value="<?php echo $file['ID'] ?>">
                                <?php echo wp_get_attachment_image( $file['ID'],'thumbnail',1,['alt'   =>'' ] ); ?>  
                                 </div>     
                                <input required="required" placeholder="<?php _e('Add a title','tainacan') ?>" type="text" id='title_<?php echo $file['ID'] ?>' name='title_<?php echo $file['ID'] ?>' value='<?php echo $file['name'] ?>'> 
                                <!-- Hidden para as categorias, tags e attachments  -->
                                <input type="hidden" id="source_<?php echo $file['ID'] ?>" name="source_<?php echo $file['ID'] ?>" value=''>
                                <input type="hidden" name="type_<?php echo $file['ID'] ?>" value='other'>
                                <input type="hidden" id='parent_<?php echo $file['ID'] ?>' name="parent_<?php echo $file['ID'] ?>" value=''>
                                <input type="hidden" id='attachments_<?php echo $file['ID'] ?>' name="attachments_<?php echo $file['ID'] ?>" value=''>
                                <input type="hidden" id='description_<?php echo $file['ID'] ?>' name="description_<?php echo $file['ID'] ?>" value=''>
                                <input type="hidden" id='categorias_<?php echo $file['ID'] ?>' name="categorias_<?php echo $file['ID'] ?>" value=''>
                                <input type="hidden" id='tags_<?php echo $file['ID'] ?>' name="tags_<?php echo $file['ID'] ?>" value='<?php  echo $file['tags'] ?>'>
                                <!-- hiddens para valores das propriedades de dados dos items a serem criados -->
                                <?php 
                                if(is_array($data_properties)):
                                    foreach ($data_properties as $value) { ?>
                                         <input type="hidden" 
                                                name='socialdb_property_<?php echo $value['id'] ?>_<?php echo $file['ID'] ?>' 
                                                id='socialdb_property_<?php echo $value['id'] ?>_<?php echo $file['ID'] ?>'
                                                value='<?php if($value['default_value']&&!empty($value['default_value'])): echo $value['default_value']; endif; ?>'>
                                <?php  } 
                                endif;   
                                ?>
                                <!-- hiddens para valores das propriedades de OBJETO dos items a serem criados -->
                                <?php 
                                if(is_array($object_properties)):
                                    foreach ($object_properties as $value) { ?>
                                         <input type="hidden" 
                                                name='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>' 
                                                id='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>'
                                                value=''>
                                <?php  } 
                                endif;   
                                ?>
                                <?php 
                                if(is_array($term_properties_id)):
                                    foreach ($term_properties_id as $value) { ?>
                                         <input type="hidden" 
                                                name='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>' 
                                                id='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>'
                                                value=''>
                                <?php  } 
                                endif;   
                                ?> 
                           </center>               
                        </div>    
                      <?php         
                    }
                ?>
                </div>
                <?php
            }
            ?>
            <button type="submit" style="margin-top: 20px;" id="submit_button" class="btn btn-lg btn-primary pull-right"><?php _e('Publish','tainacan'); ?></button>
        </div>
        <div class="col-md-12">
         <input type="hidden" name="collection_id" value="<?php echo $collection_id; ?>">
        <input type="hidden" name="operation" value="add_multiples_socialnetwork">
        <input type="hidden" name="multiple_properties_terms_radio" id='multiple_properties_terms_radio' value="<?php echo implode(',',$properties_terms_radio); ?>">
        <input type="hidden" name="multiple_properties_terms_tree" id='multiple_properties_terms_tree' value="<?php echo implode(',',$properties_terms_tree); ?>">
        <input type="hidden" name="multiple_properties_terms_selectbox" id='multiple_properties_terms_selectbox' value="<?php echo implode(',',$properties_terms_selectbox); ?>">
        <input type="hidden" name="multiple_properties_terms_checkbox" id='multiple_properties_terms_checkbox' value="<?php echo implode(',',$properties_terms_checkbox); ?>">
        <input type="hidden" name="multiple_properties_terms_multipleselect" id='multiple_properties_terms_multipleselect' value="<?php echo implode(',',$properties_terms_multipleselect); ?>">
        <input type="hidden" name="multiple_properties_terms_treecheckbox" id='multiple_properties_terms_treecheckbox' value="<?php echo implode(',',$properties_terms_treecheckbox); ?>">
        <input type="hidden" id='multiple_properties_data_id' name="multiple_properties_data_id" value="<?php echo implode(',', $data_properties_id); ?>">
        <input type="hidden" id='multiple_properties_object_id' name="multiple_properties_object_id" value="<?php echo implode(',', $object_properties); ?>">
        <input type="hidden" id='multiple_properties_term_id' name="multiple_properties_term_id" value="<?php echo implode(',', $term_properties_id); ?>">
        <input type="hidden" id='properties_id' name="properties_id" value="<?php echo implode(',', $all_properties); ?>">
        <input type="hidden" id='selected_items_id'  name="selected_items_id" value="">
        <input type="hidden" id='items_id'  name="items_id" value="<?php echo implode(',', $files); ?>">
        <input type="hidden" id='items_images'  name="items_image" value="<?php echo implode(',', $filesImage); ?>">
        <input type="hidden" id='items_video'  name="items_video" value="<?php echo implode(',', $filesVideo); ?>">
        <input type="hidden" id='items_audio'  name="items_audio" value="<?php echo implode(',', $filesAudio); ?>">
        <input type="hidden"  id='items_pdf' name="items_pdf" value="<?php echo implode(',', $filesPdf); ?>">
        <input type="hidden" id='items_other'  name="items_other" value="<?php echo implode(',', $filesOther); ?>">
       
        </div>
    </form>    
</div>