<?php
include_once ('js/layout_js.php');
$selected_view_mode = $ordenation['collection_metas']['socialdb_collection_list_mode'];
?>

<div class="col-md-12 no-padding" id="layout-config">
    <ul class="col-md-10 no-padding">
        <li class="col-md-2" style="padding-right: 0; padding-top: 14px; padding-left: 5px;">
            <a onclick="showCollectionConfiguration('<?php echo get_template_directory_uri() ?>');">
                <h4 style="display: inline-block; color: black"> 1. <?php _e('Configurations', 'tainacan')?> </h4>
            </a>
        </li>
        <li class="col-md-2 no-padding" style="padding-right: 0; padding-top: 14px; padding-left: 5px;">
            <a onclick="showPropertiesAndFilters('<?php echo get_template_directory_uri() ?>');" class="config-section-header">
                <h4 style="display: inline-block;"> 2. <?php _e('Metadata and Filters', 'tainacan')?> </h4>
            </a>
        </li>
        <li class="col-md-2" style="border-top: 4px solid #d2a96D; padding-right: 0; padding-top: 10px; padding-left: 5px;">
            <a onclick="showLayout('<?php echo get_template_directory_uri() ?>');">
                <h4 style="display: inline-block; color: black; font-weight: bolder"> 3. <?php _e('Layout', 'tainacan')?> </h4>
            </a>
        </li>
    </ul>
</div>

<div class="categories_menu row col-md-12 no-padding" id="properties_tabs">
    <div class="col-md-12 preset-filters no-padding" style="background: white; padding-bottom: 20px;">
        <div class="categories_menu" class="row" id="personalize_search">

            <div class="row">
                <div class="col-md-10" style="float: none; margin: 0 auto; padding-top: 20px;">
                    <form method="POST" name="form_ordenation_search" id="form_ordenation_search">
                        <input type="hidden" name="property_category_id"  value="<?php echo $category_root_id; ?>">

                        <input type="hidden" name="selected_view_mode" class="selected_view_mode" value="<?php echo $selected_view_mode ?>"/>

                        <!------------------- Modo de exibição dos itens -------------------------->
                        <div class="form-group">
                            <label for="collection_list_mode"><?php _e('Default list mode','tainacan'); ?></label>
                            <select name="collection_list_mode" id="collection_list_mode" class="form-control">
                                <option value="cards"><?php _e('Cards', 'tainacan'); ?></option>
                                <option value="gallery"><?php _e('Gallery', 'tainacan'); ?></option>
                                <option value="list"><?php _e('List', 'tainacan'); ?></option>
                                <option value="slideshow"><?php _e('Slideshow', 'tainacan'); ?></option>
                            </select>
                        </div>

                        <!------------------- Ordenacao-------------------------->
                        <div class="form-group">
                            <label for="collection_order"><?php _e('Select the default ordination','tainacan'); ?></label>
                            <select id="collection_order" name="collection_order" class="form-control">
                            </select>
                        </div>

                        <!------------------- Forma de ordenacao -------------------------->
                        <div class="form-group">
                            <label for="collection_ordenation_form"><?php _e('Select the ordination form','tainacan'); ?></label>
                            <select name="socialdb_collection_ordenation_form" class="form-control">
                                <option value="desc" <?php
                                if ($ordenation['collection_metas']['socialdb_collection_ordenation_form'] == 'desc' || empty($ordenation['collection_metas']['socialdb_collection_ordenation_form'])) {
                                    echo 'selected = "selected"';
                                }
                                ?>>
                                    <?php _e('DESC','tainacan'); ?>
                                </option>
                                <option value="asc" <?php
                                if ($ordenation['collection_metas']['socialdb_collection_ordenation_form'] == 'asc') {
                                    echo 'selected = "selected"';
                                }
                                ?>>
                                    <?php _e('ASC','tainacan'); ?>
                                </option>
                            </select>
                        </div>
                        <input type="hidden" id="collection_id_order_form" name="collection_id" value="<?php echo $collection_id; ?>">
                        <input type="hidden" id="operation" name="operation" value="update_ordenation">
                        <button type="submit" id="submit_ordenation_form" class="btn btn-primary pull-right"><?php _e('Go to collection','tainacan') ?></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> <!-- #properties-tabs -->