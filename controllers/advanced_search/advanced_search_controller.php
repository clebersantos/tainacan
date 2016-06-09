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
                if($data['advanced_search_collection']==get_option('collection_root_id')){//se estiver buscando em todas as colecoes
                    if($data['advanced_search_general']!==''){//se utilizar a busca generalizada
                        $args_object = $wpquery_model->keyword_filter(['value'=>$data['advanced_search_general']]);
                        $args_object['collection_id'] = 'all_items';
                        $args_object['category_root_id'] = 'all_items';
                        $paramters_object = $wpquery_model->do_filter($args_object); 
                        $args_collection = $wpquery_model->keyword_filter(['value'=>$data['advanced_search_general']]);
                        $args_collection['collection_id'] = $data['advanced_search_collection'];
                        $paramters_collection = $wpquery_model->do_filter($args_collection);
                        $loop_objects = new WP_Query($paramters_object);
                        $loop_collections = new WP_Query($paramters_collection);
                       // var_dump($loop_collections);
                    }else{
                        $args_object = $wpquery_model->advanced_searched_filter($data);
                        $paramters_object = $wpquery_model->do_filter($args_object);
                        $args_collection = $wpquery_model->advanced_searched_filter($data);
                        $args_collection['collection_id'] = $data['advanced_search_collection'];
                        $paramters_collection = $wpquery_model->do_filter($args_collection);
                        $loop_objects = new WP_Query($paramters_object);
                        $loop_collections = new WP_Query($paramters_collection);
                    }
                }else{
                    if($data['advanced_search_general']!==''){//se utilizar a busca generalizada
                        $args_object = $wpquery_model->keyword_filter(['value'=>$data['advanced_search_general']]);
                        $args_object['collection_id'] = $data['advanced_search_collection'];
                        $args_object['category_root_id'] = $wpquery_model->get_category_root_of($data['advanced_search_collection']);
                        $paramters_object = $wpquery_model->do_filter($args_object); 
                        $loop_objects = new WP_Query($paramters_object);
                    }else{
                        $args_object = $wpquery_model->advanced_searched_filter($data);
                        $paramters_object = $wpquery_model->do_filter($args_object);
                        $loop_objects = new WP_Query($paramters_object);
                    }
                }
                
                if ($loop_objects&&$loop_objects->have_posts()) : 
                    $data['loop_objects'] =  $loop_objects;
                    $return['args_item'] = serialize($args_object);
                endif;
                if ($loop_collections&&$loop_collections->have_posts()) : 
                    $data['loop_collections'] = $loop_collections;
                    $return['args_collection'] =  serialize($args_collection);
                endif;
                if(!isset($data['loop_objects'])&&!isset($data['loop_collections'])){
                    $return['not_found'] = true;
                }
                
                $return['page'] = $this->render(dirname(__FILE__) . '../../../views/advanced_search/list_advanced_search.php', $data);
                $return['data'] =  $data['data'];
                return json_encode($return);
             case "wpquery_page_advanced_collection":
                $wpquery_model = new WPQueryModel();
                $return = array();
                $args = unserialize(stripslashes($data['wp_query_args']));
                $args['pagid'] = $data['value'];
                $args['posts_per_page'] = $data['posts_per_page'];
                $paramters = $wpquery_model->do_filter($args);
                $data['pagid'] = $data['value'];
                $data['loop_collections'] =  new WP_Query($paramters);
                $return['page'] = $this->render(dirname(__FILE__) . '../../../views/advanced_search/loops_page/list_collection_search.php', $data);
                $return['args'] = serialize($args);
                $return['data'] =  $data['data'];
                return json_encode($return);
             case "wpquery_page_advanced_item":
                $wpquery_model = new WPQueryModel();
                $return = array();
                $args = unserialize(stripslashes($data['wp_query_args']));
                $args['pagid'] = $data['value'];
                $args['posts_per_page'] = $data['posts_per_page'];
                $paramters = $wpquery_model->do_filter($args);
                $data['pagid'] = $data['value'];
                $data['loop_objects'] =  new WP_Query($paramters);
                $return['page'] = $this->render(dirname(__FILE__) . '../../../views/advanced_search/loops_page/list_items_search.php', $data);
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