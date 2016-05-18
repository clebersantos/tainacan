<?php
/**
 * Author: Marco Túlio Bueno Veira
 */
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/import_configuration_js.php');
?>

<div id="ranking_title " class="row">
    <div class="col-md-1">
        <br>

    </div>        
    <div class="col-md-10">  
        <h3>
            <?php _e('Import','tainacan'); ?>
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
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="oaipmhtab" >
                <div id="validate_url_container" >
                    <div id="list_oaipmh_dc">
                        <table  class="table table-bordered" style="background-color: #d9edf7;">
                            <th><?php _e('Identifier','tainacan'); ?></th>
                            <th><?php _e('Edit','tainacan'); ?></th>
                            <th><?php _e('Delete','tainacan'); ?></th>
                            <th><?php _e('Import','tainacan'); ?></th>
                            <th><?php _e('Harvesting','tainacan'); ?></th>
                            <tbody id="table_oaipmh_dc" >
                            </tbody>
                        </table>
                    </div>
                    <br>
                    <div class="form-group"> 
                        <label><?php _e('Base URL','tainacan'); ?></label>
                        <input type="text" id="url_base_oai" class="form-control" placeholder="<?php _e('Insert the OAI-PMH respository URL','tainacan'); ?>">                  
                    </div>
                    <div class="form-group"> 
                        <label><?php _e('Set (Optional)','tainacan'); ?></label>
                        <input type="text" id="sets_import_oaipmh" name="sets_import_oaipmh" class="form-control" placeholder="<?php _e('Type a valid set','tainacan'); ?>">                  
                    </div>
                    <input type="hidden" id="collection_import_id" name="collection_id" value="">
                    <input type="hidden" id="operation" name="operation" value="validate">
                    <button type="button" onclick="validate_url()" id="submit_oaipmh" class="btn btn-default"><?php _e('Validate','tainacan'); ?></button>
                </div>
                <div id="loader_validacao" style="display:none">
                    <center>
                        <img src="<?php echo get_template_directory_uri() . '/libraries/images/catalogo_loader_725.gif' ?>">
                        <h3><?php _e('Validating Repository...','tainacan') ?></h3>
                    </center>
                </div>    
                <div id="maping_container">
                </div>
                <div id="progress" style="display: none;">
                    <center>
                        <h3 id="title_import"><?php echo __("Please wait, this process may take several minutes",'tainacan'); ?></h3>
                        <progress id="progressbar" value="0" max="100"></progress><br>
                        <center>
                            <h3><span id="progressstatus"></span></h3>
                            <br>

                        </center>
                    </center>
                </div>
                <div id="cronometer"  style="display: none;" >
                    <center>
                        <h3><span id="hora">00h</span><span id="minuto">00m</span><span id="segundo">00s</span></h3>
                    </center>    
                </div>    
            </div>  
            <!-- Tab panes -->
            <div role="tabpanel" class="tab-pane" id="csvtab">
                <div id="validate_url_csv_container" >
                    <div id="list_csv_dc">
                        <table  class="table table-bordered" style="background-color: #d9edf7;">
                            <th><?php _e('Identifier','tainacan'); ?></th>
                            <th><?php _e('Edit','tainacan'); ?></th>
                            <th><?php _e('Delete','tainacan'); ?></th>
                            <th><?php _e('Import','tainacan'); ?></th>
                            <tbody id="table_csv" >
                            </tbody>
                        </table>
                    </div>
                    <br> 
                    <form id="formCsv" name="formCsv" enctype="multipart/form-data" method="post">
                        <div class="form-group">           
                            <input type="file" accept=".csv" id="csv_file" name="csv_file" class="form-control" placeholder="<?php _e('Insert the CSV file','tainacan'); ?>">                  
                        </div>
                        <input type="hidden" id="collection_import_csv_id" name="collection_id" value="">
                        <input type="hidden" id="operation_csv" name="operation" value="validate_csv">
                        <button type="submit" id="submit_csv" class="btn btn-default"><?php _e('Save','tainacan'); ?></button>
                    </form>
                </div>
                <div id="maping_container_csv">
                </div>
            </div>
        </div>
    </div>
</div>
