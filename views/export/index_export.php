<?php
/**
 * Author: Eduardo
 */
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/index_export_js.php');
?>

<div id="ranking_title " class="row">
    <div class="col-md-1">
        <br>

    </div>        
    <div class="col-md-10">  
        <h3>
            <?php _e('Export','tainacan'); ?>
            <button onclick="backToMainPage();" class="btn btn-default pull-right "><?php _e('Back to collection','tainacan') ?></button>
        </h3> 
        <hr>
    </div>
</div>


<div class="col-md-1">

</div>	
<div class="col-md-10">
    <div role="tabpanel">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a id="click_oaipmhtab" href="#oaipmhtab" aria-controls="oaipmhtab" role="tab" data-toggle="tab"><?php _e('OAI-PMH','tainacan') ?></a></li>
            <li role="presentation"><a id="click_csvtab" href="#csvtab" aria-controls="csvtab" role="tab" data-toggle="tab"><?php _e('CSV','tainacan') ?></a></li>
            <li role="presentation"><a id="click_zip" href="#zip" aria-controls="zip" role="tab" data-toggle="tab"><?php _e('Package','tainacan') ?></a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="oaipmhtab" >
                <div id="export_oaipmh_dc_container" >
                    <form id="form_default">
                        <div id="list_export_oaipmh_dc">
                            <table  class="table table-bordered" style="background-color: #d9edf7;">
                                <th><?php _e('Identifier','tainacan'); ?></th>
                                <th><?php _e('Edit','tainacan'); ?></th>
                                <th><?php _e('Delete','tainacan'); ?></th>
                                <th><?php _e('Active Mapping','tainacan'); ?></th>
                                <tbody id="table_export_oaipmh_dc" >
                                </tbody>
                            </table>
                        </div>
                        <br>
                        <div id="oai_repository"></div>
                        <button type="submit" id="show_mapping_export_oaipmhdc" class="btn btn-primary pull-right"><?php _e('Save active mapping','tainacan'); ?></button>
                        <button type="button" onclick="show_mapping_export()" id="show_mapping_export_oaipmhdc" class="btn btn-default"><?php _e('Create new OAIPMH DC','tainacan'); ?></button>
                    </form>    
                </div>   
                <div id="maping_container_export">
                </div>   
            </div>  
            <!-- Tab panes -->
            <div role="tabpanel" class="tab-pane" id="csvtab">
                <br>
                <form id="form_export_csv" method="post" action="<?php echo get_template_directory_uri() ?>/controllers/export/export_controller.php" onsubmit="return verify_delimiter();">
                    <input type="hidden" id="collection_id_export_csv" name="collection_id" value="<?php echo $collection_id; ?>" />
                    <input type="hidden" id="operation_export_csv" name="operation" value="export_csv_file" />
                    <label for="socialdb_delimiter_csv"><?php _e('Set Delimiter','tainacan'); ?></label><br>
                    <input type="text" id="socialdb_delimiter_csv" name="socialdb_delimiter_csv" maxlength="1" required><br><br>
                    <button type="submit" id="export_csv" class="btn btn-primary"><?php _e('Export CSV File','tainacan'); ?></button>
                    <!--button type="button" onclick="export_csv_file()" id="export_csv" class="btn btn-primary"><?php _e('Export CSV File','tainacan'); ?></button-->
                </form>
                
            </div>
            <!-- Tab panes -->
            <div role="tabpanel" class="tab-pane" id="zip">
                <br>
                <form id="form_export_zip" method="post" action="<?php echo get_template_directory_uri() ?>/controllers/export/zip_controller.php">
                    <input type="hidden" id="collection_id_zip" name="collection_id" value="<?php echo $collection_id; ?>" />
                    <input type="hidden" id="operation_export_zip" name="operation" value="export_collection" />
                    <select disabled="disabled" class="form-control">
                        <option selected="selected"><?php _e('Tainacan Format','tainacan') ?></option>
                    </select>
                    <br>
                    <button type="submit" id="export_zip" class="btn btn-primary"><?php _e('Export package','tainacan'); ?></button>
                    <!--button type="button" onclick="export_csv_file()" id="export_csv" class="btn btn-primary"><?php _e('Export CSV File','tainacan'); ?></button-->
                </form>
            </div>
        </div>
    </div>
</div>
