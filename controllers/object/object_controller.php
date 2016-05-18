<?php

require_once(dirname(__FILE__) . '../../../models/object/object_model.php');
require_once(dirname(__FILE__) . '../../../models/collection/collection_model.php');
require_once(dirname(__FILE__) . '../../../controllers/general/general_controller.php');
require_once(dirname(__FILE__) . '../../../models/user/user_model.php');

require_once(dirname(__FILE__) . '../../../models/object/objectfile_model.php');

class ObjectController extends Controller {

    public function operation($operation, $data) {
        $object_model = new ObjectModel();
        $objectfile_model = new ObjectFileModel;
        switch ($operation) {
            case "create":
                $data['object_name'] =  get_post_meta($data['collection_id'], 'socialdb_collection_object_name', true);
                $data['socialdb_collection_attachment'] = get_post_meta($data['collection_id'], 'socialdb_collection_attachment', true);
                $data['object_id'] = $object_model->create();
                return $this->render(dirname(__FILE__) . '../../../views/object/create.php', $data);
                break;
            case "add":
                return $object_model->add($data);
                break;
            //BEGIN: EDITOR DE ITEMS MULTIPLOS
             case "showViewMultipleItems":
                $data['object_id'] = $object_model->create();
                return $this->render(dirname(__FILE__) . '../../../views/object/multiple_items/create.php', $data);
                break;
            case "editor_items":
                $data['properties'] = $object_model->show_object_properties($data);
                $data['items'] = $objectfile_model->get_files($data);
                if($data['items']&&empty(!$data['items'])){
                    return $this->render(dirname(__FILE__) . '../../../views/object/multiple_items/editor_items.php', $data);
                }else{
                    return 0;
                }
                break;
           //END: EDITOR DE ITEMS MULTIPLOS
           //BEGIN: EDITOR DE ITENS PARA REDES SOCIAIS
            case "showViewMultipleItemsSocialNetwork":
                $data['properties'] = $object_model->show_object_properties($data);
                $data['items'] = $objectfile_model->get_inserted_items_social_network($data);
                if($data['items']&&empty(!$data['items'])){
                    return $this->render(dirname(__FILE__) . '../../../views/object/multiple_social_network/editor_items.php', $data);
                }else{
                    return 0;
                }
                break; 
           //END: EDITOR DE ITENS PARA REDES SOCIAIS
            case 'insert_fast':// apenas com o titulo
                return $object_model->fast_insert($data);
                break;
            case 'insert_fast_url':// atraves da url
                return $object_model->fast_insert_url($data);
            case "edit":
                $object_name =  get_post_meta($data['collection_id'], 'socialdb_collection_object_name', true);
                $socialdb_collection_attachment = get_post_meta($data['collection_id'], 'socialdb_collection_attachment', true);
                $data = $object_model->edit($data['object_id'], $data['collection_id']);
               $data['object_name'] = $object_name;
                $data['socialdb_collection_attachment'] = $socialdb_collection_attachment;
                $data['socialdb_object_from'] = get_post_meta($data['object']->ID, 'socialdb_object_from', true);
                $data['socialdb_object_dc_source'] = get_post_meta($data['object']->ID, 'socialdb_object_dc_source', true);
                $data['socialdb_object_content'] = get_post_meta($data['object']->ID, 'socialdb_object_content', true);
                $data['socialdb_object_dc_type'] = get_post_meta($data['object']->ID, 'socialdb_object_dc_type', true);
                return $this->render(dirname(__FILE__) . '../../../views/object/edit.php', $data);
                break;
            case "update":
                return $object_model->update($data);
                break;
            case "delete":
                return $object_model->delete($data);
                break;
            case "show_classifications":
                $data = $object_model->show_classifications($data);
                return $this->render(dirname(__FILE__) . '../../../views/object/object_classifications.php', $data);
                break;
            case "list": // A listagem inicial dos objetos
                $return = array();
                $collection_model = new CollectionModel;
                $collection_id = $data['collection_id'];
                $recover_wpquery = $object_model->get_args($data);
                $args = $object_model->list_all($data);
                $data['loop'] =  new WP_Query($args);
                $data['collection_data'] = $collection_model->get_collection_data($collection_id);
                   
                $view_count = get_post_meta($collection_id, 'collection_view_count', true);
                if ( empty( $view_count ) ):
                    add_post_meta( $collection_id, 'collection_view_count', 1, true );
                else:
                    $updated = $view_count + 1;
                    update_post_meta( $collection_id, 'collection_view_count', $updated, $view_count );
                endif;

                if (!$data['sorted_by']) {
                    $data['sorted_by'] = 'desc';
                }
                $data['listed_by'] = $object_model->get_ordered_name($data['collection_id'], $data['ordenation_id'], $data['order_by']);
                $data['is_moderator'] = CollectionModel::is_moderator($data['collection_id'], get_current_user_id());
                $return['page'] = $this->render(dirname(__FILE__) . '../../../views/object/list.php', $data);
                $return['args'] = serialize($recover_wpquery);
                if(empty($object_model->get_collection_posts($data['collection_id']))){
                    $return['empty_collection'] = true;
                }else{
                    $return['empty_collection'] = false;
                }
                if(mb_detect_encoding($return['page'], 'auto')=='UTF-8'){
                     $return['page'] = iconv('ISO-8859-1', 'UTF-8',  utf8_decode($return['page']));
                }
                return json_encode($return);
                break;
            case 'filter': // a listagem com filtros
                $collection_model = new CollectionModel;
                $data['loop'] = $object_model->filter($data);
                $data['collection_data'] = $collection_model->get_collection_data($data['collection_id']);
                if ($data['order_by']) {
                    $data['listed_by'] = $object_model->get_ordered_name($data['collection_id'], $data['ordenation_id'], $data['order_by']);
                } else {
                    $data['listed_by'] = $object_model->get_ordered_name($data['collection_id'], $data['ordenation_id']);
                }
                //
                if (!$data['sorted_by']) {
                    $data['sorted_by'] = 'desc';
                }
                $data['is_moderator'] = CollectionModel::is_moderator($data['collection_id'], get_current_user_id());
                return $this->render(dirname(__FILE__) . '../../../views/object/list.php', $data);

            // propriedades na insercao do objeto
            case 'show_object_properties'://
                $data = $object_model->show_object_properties($data);
                return $this->render(dirname(__FILE__) . '../../../views/object/show_insert_object_properties.php', $data);
            // propriedades na EDICAO do objeto
            case 'show_object_properties_edit'://
                $data = $object_model->show_object_properties($data);
                $data['categories_id'] = wp_get_object_terms($data['object_id'], 'socialdb_category_type',array('fields'=>'ids'));
                return $this->render(dirname(__FILE__) . '../../../views/object/edit_object_properties/list_properties.php', $data);
                break;
            // mostra propriedades preparando para um evento
            case 'list_properties':// mostra todas as propriedades com seus respectivos valores (aparece por default)
                $data = $object_model->list_properties($data);
                 $data['categories_id'] = wp_get_object_terms($data['object_id'], 'socialdb_category_type',array('fields'=>'ids'));
                return $this->render(dirname(__FILE__) . '../../../views/object/show_list_event_properties.php', $data);
                break;
            case 'list_properties_edit_remove':// pega todas as propriedade para serem mostradas no formulario de edicao e remocao
                $data = $object_model->list_properties($data);
                return $this->render(dirname(__FILE__) . '../../../views/object/show_list_event_properties_edit_remove.php', $data);
                break;
            case "get_objects_by_property_json":// pega todos os objetos relacionado de uma propriedade e coloca em um array json
                return $object_model->get_objects_by_property_json($data);
            case "get_property_object_value":// retorna os valores para uma propriedade de objeto especificao
                return $object_model->get_property_object_value($data);
            case 'show_form_data_property':// mostra o formulario para insercao de propriedade de dados
                $property_model = new PropertyModel();
                $data = $property_model->list_data($data);
                return $this->render(dirname(__FILE__) . '../../../views/object/data_property_form.php', $data);
            case 'show_form_object_property':// mostra o formulario para insercao de propriedade de objecto
                $property_model = new PropertyModel();
                $data = $property_model->list_data($data);
                return $this->render(dirname(__FILE__) . '../../../views/object/object_property_form.php', $data);
            case 'show_edit_data_property_form':// mostra o formulario para EDICAO de propriedade de dados
                $property_model = new PropertyModel();
                $data['value'] = json_decode($property_model->edit_property($data));
                $data = $property_model->list_data($data);
                return $this->render(dirname(__FILE__) . '../../../views/object/edit_data_property_form.php', $data);
            case 'show_edit_object_property_form':// mostra o formulario para EDICAO de propriedade de OBJETOS
                $property_model = new PropertyModel();
                $data['value'] = json_decode($property_model->edit_property($data));
                $data = $property_model->list_data($data);
                return $this->render(dirname(__FILE__) . '../../../views/object/edit_object_property_form.php', $data);
            case "list_single_object":
                $user_model = new UserModel();
                $object_id = $data['object_id'];
                $data['object'] = get_post($object_id);
                $data["username"] = $user_model->get_user($data['object']->post_author)['name'];
                $data['metas'] = get_post_meta($object_id);
                return $this->render(dirname(__FILE__) . '../../../views/object/list_single_object.php', $data);
                break;
            case "list_single_object_by_name":
                $user_model = new UserModel();
                $object_name = $data['object_name'];
                $args = array(
                    'name' => $object_name,
                    'post_type' => 'socialdb_object',
                    'post_status' => 'publish',
                    'numberposts' => 1
                );
                $result = get_posts($args);
                if(empty($result)||!isset($result)){
                    $args = array(
                        'name' => $object_name,
                        'post_type' => 'socialdb_object',
                        'post_status' => 'inherit',
                        'numberposts' => 1
                    );
                    $result = get_posts($args);
                }
                if (count($result) > 0&&isset($result[0])&&in_array($result[0]->post_status, array('publish','inherit'))) {
                    $data['object'] = $result[0];
                    $data["username"] = $user_model->get_user($data['object']->post_author)['name'];
                    $data['metas'] = get_post_meta($data['object']->ID);
                    $array_json['html'] = $this->render(dirname(__FILE__) . '../../../views/object/list_single_object.php', $data);
                    return json_encode($array_json);
                }else{
                    $array_json['redirect'] = get_the_permalink($data['collection_id']);
                    return json_encode($array_json);
                }
                break;
            case 'list_search' :
                if ($data['collection_id'] == get_option('collection_root_id')) {
                    $array['is_json'] = false;
                    //
                    if (!$data['sorted_by']) {
                        $data['sorted_by'] = 'desc';
                    }
                    $data['loop'] = $object_model->list_collection($data);
                    $data['listed_by'] = $object_model->get_ordered_name($data['collection_id'], $data['ordenation_id']);
                    $array['html'] = $this->render(dirname(__FILE__) . '../../../views/object/list.php', $data);
                    return json_encode($array);
                } else {
                    $array['is_json'] = TRUE;
                    $array['link'] = get_the_permalink(get_option('collection_root_id')) . "?search=" . $data['keyword'];
                    return json_encode($array);
                }
                break;
            //temp file
            case 'delete_temporary_object':
                return $object_model->delete($data);
            //ACTION FILES
            case 'list_files':
                return $objectfile_model->list_files($data);
            case 'save_file':
                return $objectfile_model->save_file($data);
            case 'delete_file':
                return $objectfile_model->delete_file($data);
            case 'show_files':
                $data['attachments'] = $objectfile_model->show_files($data);
                return $this->render(dirname(__FILE__) . '../../../views/object/file/list_file.php', $data);
            case 'redirect_facebook':
                return json_encode($object_model->redirect_facebook($data));
             // acoes para listagem de comentarios
            case 'list_comments':
                $data['permissions'] = $object_model->verify_comment_permissions($data['collection_id']);
                return $this->render(dirname(__FILE__) . '../../../views/object/comments/view_comments.php', $data);
            case 'list_comments_term':
                $data['permissions'] = $object_model->verify_comment_permissions($data['collection_id']);
                return $this->render(dirname(__FILE__) . '../../../views/comment/view_comments.php', $data);
            // licenças na insercao do objeto
            case 'show_collection_licenses':
                $data = $object_model->show_collection_licenses($data);
                return $this->render(dirname(__FILE__) . '../../../views/object/show_insert_object_licenses.php', $data);
                break;
            case 'help_choosing_license':
                $data = $object_model->help_choosing_license($data);
                return json_encode($data);
                break;
            // limpando uma colecao
            case 'clean_collection_itens':
                $data = $object_model->clean_collection($data);
                return json_encode($data);
            // limpando alguns itens da colecao 
            case 'delete_items_socialnetwork':
                $data = $object_model->delete_items_socialnetwork($data);
                return json_encode($data);
            case 'remove_ids_socialnetwork':
                $data['items_id'] = explode(',',$data['items_id']);
                $data = $object_model->delete_items_socialnetwork($data);
                return json_encode($data);
            case 'get_last_attachment':
                $object_model->set_attachment_description($data['post_parent'], $data['post_content']);
                break;
            case 'update_attachment_legend':
                $object_model->update_attachment_legend($data['item_id'], $data['item_legend']);
                break;
        }
    }

    /**
     * function get_author_name($author_id)
     * @param int $author_id o id do author
     * @return string Retorna o nome do usuario
     * 
     * @author: Eduardo Humberto 
     */
    public function get_author_name($author_id) {
        $object_model = new ObjectModel();
        return $object_model->get_object_author($author_id, 'name');
    }

}

/*
 * Controller execution
 */
if ($_POST['operation']) {
    $operation = $_POST['operation'];
    $data = $_POST;
} else {
    $operation = $_GET['operation'];
    $data = $_GET;
}

$object_controller = new ObjectController();
echo $object_controller->operation($operation, $data);
?>
