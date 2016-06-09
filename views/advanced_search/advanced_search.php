<?php
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/advanced_search_js.php');
?>  
<style>
    .quadrante{
        background-color: white;
        padding-left: 50px;
        padding-right: 50px;
        padding-top: 10px;
        padding-bottom: 20px;
        margin-bottom: 5px;
        border: 3px solid #E8E8E8;
       overflow:auto;
    }
    .padding-left-space{
        padding-left: 15px;
    }
</style>    
<form id="advanced_search_submit" style="min-height: 500px;">
    <input type="hidden" id="advanced_search_operation" name="operation" value="do_advanced_search">   
    <input type="hidden" id="advanced_search_wp_query_args" name="wp_query_args" value="do_advanced_search">
    <input type="hidden" id="advanced_search_collection_id" name="collection_id" value="<?php echo $collection_id; ?>">
    <div style="margin-left: 5px;margin-right: 5px;" class="row">
        <ol class="breadcrumb" style="margin-top: -15px; padding-left: 0; background: #f2f2f2;">
            <li><a href="<?php echo get_permalink(get_option('collection_root_id')); ?>"> <?php _e('Repository', 'tainacan') ?> </a></li>
            <li><a href="#" onclick="backToMainPageSingleItem()"><?php echo get_post($collection_id)->post_title; ?></a></li>
            <li class="active"><?php echo __('Advanced Search','tainacan'); ?></li>
        </ol>
        <div class="quadrante">
                <h3><?php _e('Advanced Search', 'tainacan'); ?>
                    <a class="btn btn-default pull-right" onclick="redirect_collection($('#advanced_search_collection_id').val())" ><?php _e('Back to the collection page', 'tainacan'); ?></a> 
                </h3>
                <hr>
                <?php if ($collection_id != get_option('collection_root_id')): ?>
                <div >
                    <h5>
                       <b><?php _e('Select the Collection', 'tainacan'); ?></b>
                    </h5>
                    <select onchange="show_collection_properties($(this).val())" 
                            class="form-control" 
                            id="advanced_search_collection" 
                            name="advanced_search_collection">

                    </select>
                </div>   
                <?php endif; ?>
        </div>
        <div class="quadrante">
            <h5>
                <b>
                <?php _e('General Search', 'tainacan'); ?>
                </b>
            </h5>
            <div class="row">
                <div class="col-md-10">
                    <input type="text" 
                            class="form-control" 
                            name="advanced_search_title" 
                            id="advanced_search_title" 
                            placeholder="<?php if ($collection_id != get_option('collection_root_id')) _e('Type the item title or its description', 'tainacan'); ?>">
                </div>  
                <button type="submit" class="col-md-2 btn btn-success pull-right">
                    <?php _e('Find', 'tainacan') ?>
                </button>
            </div>
            <br>
            <a id="slide_up_button" onclick="slide_down_metadata_form()" style="cursor: pointer" ><?php _e('Search for more metadada','tainacan')?> <span class="glyphicon glyphicon-triangle-bottom"></span></a>
            <a id="slide_down_button" onclick="slide_up_metadata_form()" style="cursor: pointer;display: none;" ><?php _e('Hide metadada','tainacan')?> <span class="glyphicon glyphicon-triangle-top"></span></a>
            <div style="margin-top: 10px;" class="hide" id="propertiesAdvancedSearch"></div>
        </div>          
                    <!--div class="form-group">
                        <label for="advanced_search_title"><?php _e('Title or description', 'tainacan'); ?></label>
                        <input type="text" 
                               value="<?php print_r(empty($home_search_term) ? "" : $home_search_term ); ?>" 
                               class="form-control" 
                               name="advanced_search_title" 
                               id="advanced_search_title" 
                               placeholder="<?php if ($collection_id != get_option('collection_root_id')) _e('Type the item title or its description', 'tainacan'); ?>">
                    </div>
                    <!--div class="form-group">
                       <label for="advanced_search_description"><?php _e('Description', 'tainacan'); ?></label>
                       <input type="text" class="form-control" id="advanced_search_description" placeholder="<?php _e('Type the item description', 'tainacan'); ?>">
                   </div-->
                    <!--div class="form-group">
                        <label for="advanced_search_tags"><?php _e('Tags', 'tainacan'); ?></label>
                        <input type="text" class="form-control" name="advanced_search_tags" id="advanced_search_tags" placeholder="<?php _e('A set of tags may be searched by comma ', 'tainacan'); ?>">
                    </div>
                    <!--input type="text" class=" form-control" name="advanced_search" id="advanced_search" placeholder="<?php _e('Search on the Repository or for Collection', 'tainacan'); ?>" -->

<!--label for="search_for"><?php _e('for', 'tainacan'); ?></label>
<input type="text" class="form-control" id="search_for" name="search_for" required="required" value="" -->
                    
            
        
       
        
            
    </div> 
</form>
<div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-10" id="show-results-advanced-search">
    </div>
    <div class="col-md-1"></div>
</div>     