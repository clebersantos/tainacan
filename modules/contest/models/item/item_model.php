<?php

include_once (dirname(__FILE__) . '/../../../../../../../wp-config.php');
include_once (dirname(__FILE__) . '/../../../../../../../wp-load.php');
include_once (dirname(__FILE__) . '/../../../../../../../wp-includes/wp-db.php');
include_once (dirname(__FILE__) . '/../../../../models/collection/collection_model.php');
include_once (dirname(__FILE__) . '/../../../../models/license/license_model.php');
include_once (dirname(__FILE__) . '/../../../../models/property/property_model.php');
include_once (dirname(__FILE__) . '/../../../../models/category/category_model.php');
include_once (dirname(__FILE__) . '/../../../../models/event/event_object/event_object_create_model.php');
require_once(dirname(__FILE__) . '/../../../../models/general/general_model.php');
require_once(dirname(__FILE__) . '/../../../../models/user/user_model.php');
require_once(dirname(__FILE__) . '/../../../../models/tag/tag_model.php');

/**
 * The class ObjectModel
 *
 */
class ItemModel extends Model {

    public function create() {
        $user_id = get_current_user_id();
        if ($user_id == 0 || is_wp_error($user_id)) {
            $user_id = get_option('anonimous_user');
        }
        $post = array(
            'post_title' => 'Temporary_post',
            'post_status' => 'inherit',
            'post_author' => $user_id,
            'post_type' => 'socialdb_object'
        );
        $object_id = wp_insert_post($post);
        return $object_id;
    }

    public function add_argument($title,$collection_id,$classification) {
        $category_root_id = $this->collection_model->get_category_root_of($data['collection_id']);
        $user_id = get_current_user_id();
        $post = array(
            'post_title' => $data['object_name'],
            'post_status' => 'inherit',
            'post_author' => $user_id,
            'post_parent' => $user_id,
            'post_type' => 'socialdb_object'
        );
        $post_id = wp_insert_post($post);
        //categoria raiz da colecao
        wp_set_object_terms($post_id, array((int) $category_root_id), 'socialdb_category_type');
        //inserindo as classificacoes
        $this->insert_classifications($data['object_classifications'], $data['ID']);
        //inserindo tags
        $this->insert_tags($data['object_tags'], $data['collection_id'], $data['ID']);
       
        // inserindo o evento
        $data = $this->insert_item_event($data['ID'], $data);

        return $data;
    }
    
    /**
     * @signature - function insert_event($object_id, $data )
     * @param int $object_id O id do Objeto
     * @param int $collection_id
     * @return array os dados para o evento
     * @description - 
     * @author: Eduardo 
     */
    public function insert_item_event($object_id, $collection_id) {
        $data = [];
        $eventAddObject = new EventObjectCreateModel();
        $data['socialdb_event_object_item_id'] = $object_id;
        $data['socialdb_event_collection_id'] = $collection_id;
        $data['socialdb_event_user_id'] = get_current_user_id();
        $data['socialdb_event_create_date'] = time();
        return $eventAddObject->create_event($data);
    }
    
    /**
     * @signature - function insert_classifications($classification_string, $object_id)
     * @param string $classification_string A string que esta concatenada com os valores do dynatree selecionadas
     * @param int $object_id O id do Objeto
     * @return void
     * @description - Insere os valores selecionados no dynatree no objeto criado
     * @author: Eduardo 
     */
    public function insert_classifications($classification_string, $object_id) {
        $classification_array = explode(',', $classification_string);
        foreach ($classification_array as $classification) {
            if (strpos($classification, '_') !== false) {
                $value_array = explode('_', $classification);
                if ($value_array[1] == 'tag') {
                    wp_set_object_terms($object_id, array((int) $value_array[0]), 'socialdb_tag_type', true);
                } else {
                    $metas = get_post_meta($object_id, 'socialdb_property_' . $value_array[1]);
                    if (!$metas || (count($metas) == 1 && $metas[0] == '')) {
                        update_post_meta($object_id, 'socialdb_property_' . $value_array[1], $value_array[0]);
                    } else {
                        add_post_meta($object_id, 'socialdb_property_' . $value_array[1], $value_array[0]);
                    }
                }
            } else {
                wp_set_object_terms($object_id, array((int) $classification), 'socialdb_category_type', true);
            }
        }
    }

}
