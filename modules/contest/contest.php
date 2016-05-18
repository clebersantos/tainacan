<?php

/* 
 * Modo de debates do Tainacan
 * 
 * #1 - ADICIONANDO OS SCRIPTS DESTE MODULO 
 * #2 BOTAO DE ADICAO DE ARGUMENTO
 * 
 * @author: EDUARDO HUMBERTO
 */

####################### #1 - ADICIONANDO OS SCRIPTS DESTE MODULO#######################
define('MODULE_CONTEST', 'contest');
define('CONTEST_CONTROLLERS', get_template_directory_uri() . '/modules/' . MODULE_CONTEST );
load_theme_textdomain("tainacan", dirname(__FILE__) . "/languages");
add_action('wp_enqueue_scripts', 'tainacan_contest_js');
function tainacan_contest_js() {
    wp_register_script('contest', 
            get_template_directory_uri() . '/modules/' . MODULE_CONTEST . '/libraries/js/contest.js', array('jquery'), '1.11');
    $js_files = ['contest'];
    foreach ($js_files as $js_file):
        wp_enqueue_script($js_file);
    endforeach;
}
################################################################################

######################### #2 BOTAO DE ADICAO DE ARGUMENTO ###########################
/**
 * Filtro que mostra o botao personalizado de adicao de individuo
 */
function alter_button_add_item_contest($string) {
    $string .= '
        <div class="btn-group" role="group" aria-label="...">
            <div class="btn-group tainacan-add-wrapper">
                  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  '.__('Add','tainacan').'&nbsp;<span class="caret"></span> 
                  </button>
                    <ul class="dropdown-menu">
                        <li><a  onclick="contest_show_modal_create_argument()" style="cursor: pointer;">'. __('Item','tainacan').'</a></li>
                        <li><a onclick="contest_show_modal_create_question()" style="cursor: pointer;" >'. __('Question with multiple answers','tainacan').'</a></li>
                   </ul>
            </div>
        </div>';
    $string .= 
    '<div class="modal fade" id="modalCreateArgument" tabindex="-1" role="dialog" aria-labelledby="modalCreateArgument" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="'.CONTEST_CONTROLLERS.'/controllers/argument/argument_controller.php" method="POST">
                    <input type="hidden" name="operation" value="simple_add">
                    <div class="modal-header">
                        <button type="button" style="color:black;" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">'.__('New Argument', 'tainacan').'</h4>
                    </div>
                    <div class="modal-body"  >
                        <div class="form-group">
                           <label for="exampleInputEmail1">'.__('Describe a conclusion or an afirmation below').'</label>
                           <textarea name="conclusion" class="form-control" required="" placeholder="'.__('This field is obligate!','tainacan').'" ></textarea>
                        </div>
                        <div style="margin-left:25px;" class="form-group">
                          <label for="exampleInputPassword1"><span class="glyphicon glyphicon-thumbs-up"></span>&nbsp;'.__('Describe a positive argument (Optional) ','tainacan').'</label>
                           <textarea name="positive_argument" class="form-control"  placeholder="'.__('This field is optional!','tainacan').'" ></textarea>
                        </div>
                        <div style="margin-left:25px;" class="form-group">
                          <label for="exampleInputFile"><span class="glyphicon glyphicon-thumbs-down"></span>&nbsp;'.__('Describe a negative argument (Optional) ','tainacan').'</label>
                           <textarea name="negative_argument" class="form-control" placeholder="'.__('This field is optional!','tainacan').'" ></textarea>
                        </div>
                        <input type="hidden" name="collection_id" value="'.get_the_ID().'">
                    </div>
                    <div class="modal-footer">
                        <button style="color:grey;" type="button" class="btn btn-default" data-dismiss="modal">'. __('Close', 'tainacan').'</button>
                        <button type="button" class="btn btn-primary" >'. __('Save', 'tainacan').'</button>
                    </div>
                </form>
            </div>
        </div>
    </div>';
     $string .= 
    '<div class="modal fade" id="modalCreateQuestion" tabindex="-1" role="dialog" aria-labelledby="modalCreateArgument" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="'.CONTEST_CONTROLLERS.'/controllers/argument/argument_controller.php" method="POST">
                    <input type="hidden" name="operation" value="simple_add">
                    <div class="modal-header">
                        <button type="button" style="color:black;" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">'.__('New Question', 'tainacan').'</h4>
                    </div>
                    <div class="modal-body"  >
                        <div class="form-group">
                           <label for="exampleInputEmail1">'.__('Insert a question below').'</label>
                           <textarea name="conclusion" class="form-control" required="" placeholder="'.__('This field is obligate!','tainacan').'" ></textarea>
                        </div>
                        <div style="margin-left:25px;" class="form-group">
                          <label for="exampleInputPassword1"></span>&nbsp;'.__('Describe an answer (or conclusion) for the question (Optional) ','tainacan').'</label>
                           <textarea name="positive_argument" class="form-control"  placeholder="'.__('This field is optional!','tainacan').'" ></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button style="color:grey;" type="button" class="btn btn-default" data-dismiss="modal">'. __('Close', 'tainacan').'</button>
                        <button type="button" class="btn btn-primary" >'. __('Save', 'tainacan').'</button>
                    </div>
                </form>
            </div>
        </div>
    </div>';
    return $string;
}
add_filter( 'show_custom_add_item_button', 'alter_button_add_item_contest', 10, 3 );
