<?php
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/create_js.php');
?>
<div  id="uploading">
    <!--h3><?php _e('Add Multiple Items', 'tainacan'); ?></h3 -->
    <button style="display: none;" id="click_editor_items_button" onclick="edit_items_uploaded()" class="btn btn-primary  pull-left"><span class="glyphicon glyphicon-pencil"></span>&nbsp;<?php _e('Edit Items','tainacan') ?></button>
    <button onclick="back_main_list();" class="btn btn-default pull-right"><?php _e('Cancel','tainacan') ?></button><br><br>
    <!-- TAINACAN: UPLOAD DE ITEMS -->

    <div style="padding-bottom: 40%;"  id="dropzone_multiple_items" class="dropzone">
    </div>
</div>
<!-- TAINACAN: MAPEAMENTO DOS ITEMS -->
<div style="margin-bottom: 50px;" id='editor_items'>
    <!-- MOSTRA O EDITOR DOS ITENS AO FINAL DO UPLOAD -->
</div>
