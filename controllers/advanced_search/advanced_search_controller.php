<?php

/**
 * Author: Marco Túlio Bueno Vieira
 */
require_once(dirname(__FILE__) . '../../general/general_controller.php');
require_once(dirname(__FILE__) . '../../../models/collection/collection_model.php');
require_once(dirname(__FILE__) . '../../../models/wp_query/wp_query_model.php');
require_once(dirname(__FILE__) . '../../../models/wp_query/advanced_search_model.php');
require_once(dirname(__FILE__) . '../../../models/object/object_model.php');

class AdvancedSearchController extends Controller {

    public function operation($operation, $data) {
        $object_model = new ObjectModel();
        $collection_model = new CollectionModel();
        $advanced_search_model = new AdvancedSearchModel();
        switch ($operation) {
            case "open_page"://
                return $this->render(dirname(__FILE__) . '../../../views/advanced_search/advanced_search.php',$data);
                break;

            case "get_collections_json":
                return $collection_model->get_collections_json($data);
                break;

            case 'show_object_properties':
                $data = $object_model->show_object_properties($data);
                return $this->render(dirname(__FILE__) . '../../../views/advanced_search/show_object_properties.php', $data);
                break;
            case "get_objects_by_property_json":             
                return $object_model->get_objects_by_property_json($data);

            case 'show_object_properties_auto_load':
                $data = $object_model->show_object_properties($data);
                return $this->render(dirname(__FILE__) . '../../../views/advanced_search/show_object_properties.php', $data);
                break;
            case 'select_collection':
                return $this->get_collections();
            case 'do_advanced_search':
                $wpquery_model = new WPQueryModel();
                $return = array();
                if(empty($wpquery_model->get_collection_posts($data['collection_id']))){
                    $return['empty_collection'] = true;
                }else{
                    $return['empty_collection'] = false;
                }
                $collection_model = new CollectionModel;
                $args = $wpquery_model->advanced_searched_filter($data);
                $paramters = $wpquery_model->do_filter($args);
                $data['loop'] =  new WP_Query($paramters);
                $data['data'] = $advanced_search_model->get_data_wpquery($data['loop']);
                if($args['collection_id']):
                    $data['collection_data'] = $collection_model->get_collection_data($args['collection_id']);
                    $data['listed_by'] = $wpquery_model->get_ordered_name($args['collection_id'], $args['ordenation_id'], $args['order_by']);
                    $data['is_moderator'] = CollectionModel::is_moderator($args['collection_id'], get_current_user_id());
                endif;
                $return['page'] = $this->render(dirname(__FILE__) . '../../../views/object/list_advanced_search.php', $data);
                $return['args'] = serialize($args);
                $return['data'] =  $data['data'];
                return json_encode($return);
            
             case "wpquery_page_advanced":
                $wpquery_model = new WPQueryModel();
                $return = array();
                $recover_data = unserialize(stripslashes($data['wp_query_args']));
                if(empty($wpquery_model->get_collection_posts($recover_data['collection_id']))){
                    $return['empty_collection'] = true;
                }else{
                    $return['empty_collection'] = false;
                    $data['collection_id'] = $recover_data['collection_id'];
                }
                $collection_model = new CollectionModel;
                $args = $wpquery_model->advanced_searched_filter($data);
                $paramters = $wpquery_model->do_filter($args);
                $data['pagid'] = $data['value'];
                $data['loop'] =  new WP_Query($paramters);
                $data['data'] = $advanced_search_model->get_data_wpquery($data['loop']);
                if($args['collection_id']):
                    $data['collection_data'] = $collection_model->get_collection_data($args['collection_id']);
                    $data['listed_by'] = $wpquery_model->get_ordered_name($args['collection_id'], $args['ordenation_id'], $args['order_by']);
                    $data['is_moderator'] = CollectionModel::is_moderator($args['collection_id'], get_current_user_id());
                endif;
                $return['page'] = $this->render(dirname(__FILE__) . '../../../views/object/list_advanced_search.php', $data);
                $return['args'] = serialize($args);
                $return['data'] =  $data['data'];
                return json_encode($return);
            case 'redirect_collection':
                $data['url'] = get_permalink($data['collection_id']);
                return json_encode($data);
                
        }
    }

    
    /**
     * function get_collections_json()
     * @param array Os dados vindo do formulario
     * @return json com o id e o nome de cada colecao
     * @author Eduardo Humberto
     */
    public function get_collections() {
        global $wpdb;
        $wp_posts = $wpdb->prefix . "posts";
        $query = "
                        SELECT p.* FROM $wp_posts p
                        WHERE p.post_type like 'socialdb_collection' and p.post_status LIKE 'publish'
                        ORDER BY p.post_title
                ";
        $result = $wpdb->get_results($query);
        if ($result) {
            foreach ($result as $collection) {
                $json[] = array('value' => $collection->ID, 'name' => $collection->post_title);
            }
        }
        return json_encode($json);
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

$advanced_search_controller = new AdvancedSearchController();
echo $advanced_search_controller->operation($operation, $data);
?>