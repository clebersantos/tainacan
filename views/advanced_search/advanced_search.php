<?php
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/advanced_search_js.php');
?>  
<!--center><div id="main_part" class="jumbotron">
        <h1>Tainacan</h1>
        <p><?php _e('Welcome','tainacan') ?></p>
        <p><a onclick="redirect_collection($('#advanced_search_collection_id').val())" class="btn btn-primary btn-lg" href="#" role="button"><?php _e('Open Collection','tainacan') ?></a></p>
    </div></center-->
<form id="advanced_search_submit">
<input type="hidden" id="advanced_search_operation" name="operation" value="do_advanced_search">   
<input type="hidden" id="advanced_search_wp_query_args" name="wp_query_args" value="do_advanced_search">
<input type="hidden" id="advanced_search_collection_id" name="collection_id" value="<?php echo $collection_id; ?>">
<div style="margin: 10px;" class="row">
    <div class="col-md-1"></div>
    <div class="col-md-10">
        <h2><?php _e('Advanced Search','tainacan'); ?>
            <a class="btn btn-default pull-right" onclick="redirect_collection($('#advanced_search_collection_id').val())" ><?php _e('Back to the collection page','tainacan'); ?></a> 
        </h2>
        <hr>
         <?php if($collection_id!=get_option('collection_root_id')): ?>
         <div class="panel panel-info">
            <div class="panel-heading">
                <?php _e('Select the Collection','tainacan'); ?>
            </div>
            <div class="panel-body">
                <div class="form-group">   
                    <div class="form-group">
                        <label for="advanced_search_collection"></label>
                        <select onchange="show_collection_properties($(this).val())" class="form-control" id="advanced_search_collection" name="advanced_search_collection"></select>
                    </div>
                </div>      
            </div>
        </div>   
        <?php endif; ?>
        <div class="panel panel-info">
            <div class="panel-heading">
                <?php _e('Main fields','tainacan'); ?>
            </div>
            <div class="panel-body">
                <div class="form-group">   
                    <div class="form-group">
                        <label for="advanced_search_title"><?php _e('Title or description','tainacan'); ?></label>
                        <input type="text" 
                               value="<?php print_r( empty($home_search_term) ? "" : $home_search_term ); ?>" 
                               class="form-control" 
                               name="advanced_search_title" 
                               id="advanced_search_title" 
                               placeholder="<?php if($collection_id!=get_option('collection_root_id')) _e('Type the item title or its description','tainacan'); ?>">
                    </div>
                     <!--div class="form-group">
                        <label for="advanced_search_description"><?php _e('Description','tainacan'); ?></label>
                        <input type="text" class="form-control" id="advanced_search_description" placeholder="<?php _e('Type the item description','tainacan'); ?>">
                    </div-->
                    <div class="form-group">
                        <label for="advanced_search_tags"><?php _e('Tags','tainacan'); ?></label>
                        <input type="text" class="form-control" name="advanced_search_tags" id="advanced_search_tags" placeholder="<?php _e('A set of tags may be searched by comma ','tainacan'); ?>">
                    </div>
                    <!--input type="text" class=" form-control" name="advanced_search" id="advanced_search" placeholder="<?php _e('Search on the Repository or for Collection','tainacan'); ?>" -->
                  
                    <!--label for="search_for"><?php _e('for','tainacan'); ?></label>
                    <input type="text" class="form-control" id="search_for" name="search_for" required="required" value="" -->
                </div>      
            </div>
        </div>
        <div id="propertiesAdvancedSearch">            
        </div>
        <div class="col-md-12">
            <button type="button" onclick="reboot_form()" class="btn btn-lg btn-default pull-left"><?php _e('Clear search','tainacan') ?></button>
            <button type="submit" class="btn btn-lg btn-primary pull-right"><?php _e('Find','tainacan') ?></button>
        </div>
    </div>
    <div class="col-md-1"></div>
</div> 
</form>
<div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-10" id="show-results-advanced-search">
    </div>
     <div class="col-md-1"></div>
</div>     