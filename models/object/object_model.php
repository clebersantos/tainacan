<?php

include_once (dirname(__FILE__) .'/../../../../../wp-config.php');
include_once (dirname(__FILE__) .'/../../../../../wp-load.php');
include_once (dirname(__FILE__) .'/../../../../../wp-includes/wp-db.php');
include_once (dirname(__FILE__) . '../../../models/collection/collection_model.php');
include_once (dirname(__FILE__) . '../../../models/license/license_model.php');
include_once (dirname(__FILE__) . '../../../models/property/property_model.php');
include_once (dirname(__FILE__) . '../../../models/category/category_model.php');
include_once (dirname(__FILE__) . '../../../models/event/event_object/event_object_create_model.php');
require_once(dirname(__FILE__) . '../../general/general_model.php');
require_once(dirname(__FILE__) . '../../user/user_model.php');
require_once(dirname(__FILE__) . '../../tag/tag_model.php');

/**
 * The class ObjectModel
 *
 */
class ObjectModel extends Model {

    public $collection_model;
    public $user_model;

    public function ObjectModel() {
        $this->collection_model = new CollectionModel();
        $this->user_model = new UserModel();
    }

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

    public function add($data) {
        $data = $this->validate_form($data);
        if (isset($data['validation_error'])) {
            return json_encode($data);
        }
        $category_root_id = $this->collection_model->get_category_root_of($data['collection_id']);
        $user_id = get_current_user_id();
        if ($user_id == 0) {
            $user_id = get_option('anonimous_user');
        }
        $post = array(
            'ID' => $data['object_id'],
            'post_title' => $data['object_name'],
            'post_content' => $data['object_description'],
            'post_status' => 'inherit',
            'post_author' => $user_id,
            'post_type' => 'socialdb_object'
        );
        $data['ID'] = wp_update_post($post);
        $slug = wp_unique_post_slug(sanitize_title_with_dashes($data['object_name']), $data['ID'], 'inherit', 'socialdb_object', 0);
        $post = array(
            'ID' => $data['object_id'],
            'post_name' => $slug
        );
        $data['ID'] = wp_update_post($post);
        //inserindo o objecto do item e o seu tipo
        $this->insert_item_resource($data);
        //categoria raiz da colecao
        wp_set_object_terms($data['ID'], array((int) $category_root_id), 'socialdb_category_type');
        //inserindo as classificacoes
        $this->insert_classifications($data['object_classifications'], $data['ID']);
        //inserindo tags
        $this->insert_tags($data['object_tags'], $data['collection_id'], $data['ID']);
        //inserindo os valores das propriedades
        $this->insert_properties_values($data, $data['ID']);
        //verificando se existe aquivos para ser incluidos
        if ($_FILES) {
            $attachment_id = $this->add_thumbnail($data['ID']);
            if (isset($_FILES['object_thumbnail']) && !empty($_FILES['object_thumbnail'])) {
                set_post_thumbnail($data['ID'], $attachment_id);
            }
        }
        //inserido via img via url
        if (isset($data['thumbnail_url']) && $data['thumbnail_url']) {
            $this->add_thumbnail_url($data['thumbnail_url'], $data['ID']);
        }
        //inserindo a url fonte dos dados
        if (isset($data['object_url']) && $data['object_url']) {
            update_post_meta($data['ID'], 'socialdb_uri_imported', $data['object_url']);
        }
        //verificando se existe mapeamento ativo
        if (get_post_meta($data['collection_id'], 'socialdb_collection_mapping_exportation_active')) {
            add_post_meta($data['ID'], 'socialdb_channel_id', get_post_meta($data['collection_id'], 'socialdb_collection_mapping_exportation_active', true));
        }
        // propriedade de termos
        $this->insert_properties_terms($data, $data['ID']);

        //object_license
        if ($data['object_license']) {
            update_post_meta($data['ID'], 'socialdb_license_id', $data['object_license']);
        }

        // inserindo o evento
        $data = $this->insert_object_event($data['ID'], $data);

        return $data;
    }

    /**
     * @signature - insert_item_resource($data)
     * @param array $data Os dados vindos do formulario
     * @return void
     * @description - Insere o objeto do item
     * @author: Eduardo 
     */
    public function insert_item_resource($data) {
        update_post_meta($data['ID'], 'socialdb_object_from', $data['object_from']);
        update_post_meta($data['ID'], 'socialdb_object_dc_source', $data['object_source']);
        if ($data['object_type'] == 'text') {
            update_post_meta($data['ID'], 'socialdb_object_content', $data['object_content']);
            update_post_meta($data['ID'], 'socialdb_object_dc_type', $data['object_type']);
        } else {
            if ($data['object_from'] == 'internal' && !empty($_FILES['object_file']['name'])) {
                $attachment_id = $this->add_object_item($data['ID']);
                update_post_meta($data['ID'], 'socialdb_object_content', $attachment_id);
            } else {
                update_post_meta($data['ID'], 'socialdb_object_content', $data['object_url']);
                if (strpos($data['object_url'], 'youtube.com') !== false) {
                    parse_str(parse_url($data['object_url'], PHP_URL_QUERY), $vars);
                    $this->add_thumbnail_url('https://i.ytimg.com/vi/' . $vars['v'] . '/0.jpg', $data['ID']);
                }
            }
            //type
            if ($data['object_type'] == 'other') {
                update_post_meta($data['ID'], 'socialdb_object_dc_type', $data['object_type_other']);
            } else {
                update_post_meta($data['ID'], 'socialdb_object_dc_type', $data['object_type']);
            }
            //image type para inserir o thumbnail se necessario
            if ($data['object_type'] == 'image') {
                if (empty($_FILES['object_thumbnail']['name'])) {
                    if ($data['object_from'] == 'internal') {
                        set_post_thumbnail($data['ID'], get_post_meta($data['ID'], 'socialdb_object_content', true));
                    } else {
                        if (!isset($data['object_has_thumbnail']) || $data['object_has_thumbnail'] == 'false') {
                            $this->add_thumbnail_url($data['object_url'], $data['ID']);
                        }
                    }
                }
            }
        }
         //insiro os valores no metadado comum
        if(isset($data['object_name'])){
             $data['title'] = $data['object_name'];
             $data['description'] = $data['object_description'];
        }
        $this->set_common_field_values($data['ID'], 'title', $data['title']);  
        $this->set_common_field_values($data['ID'], 'description', $data['description']);
        $this->set_common_field_values($data['ID'], 'object_from', $data['object_from']);
        $this->set_common_field_values($data['ID'], 'object_source', $data['object_source']);
        $this->set_common_field_values($data['ID'], 'object_type', $data['object_type']);
        $this->set_common_field_values($data['ID'], 'object_content', $data['object_content']);
    }

    /**
     * @signature - validate_form($data)
     * @param array $data Os dados vindos do formulario
     * @return json com os dados do resultado do evento criado
     * @description - Verificacao de campios do formulario
     * @author: Eduardo 
     */
    public function validate_form($data) {
        if ($data['properties_id'] !== '') {
            $properties_id = explode(',', $data['properties_id']);
            foreach ($properties_id as $property_id) {
                if (isset($data['checkbox_required_' . $property_id]) && $data['checkbox_required_' . $property_id] == 'required' && !isset($data['socialdb_propertyterm_' . $property_id])) {
                    $data['validation_error'] = true;
                    $data['title'] = __('Error', 'tainacan');
                    $data['msg'] = __('Please check a option in the field:', 'tainacan') . get_term_by('id', $property_id, 'socialdb_property_type')->name;
                    return $data;
                }
            }
        }
        // validation empty fil
        if ($data['object_type'] != 'text' && $data['object_from'] == 'internal') {
            if (isset($_FILES['object_file']['name']) && empty($_FILES['object_file']['name'])) {
                $data['validation_error'] = true;
                $data['title'] = __('Error', 'tainacan');
                $data['msg'] = __('Please select a file to upload to this item', 'tainacan');
                return $data;
            }
        } elseif ($data['object_type'] != 'text' && $data['object_from'] == 'external') {
            if (isset($data['object_url']) && empty($data['object_url'])) {
                $data['validation_error'] = true;
                $data['title'] = __('Error', 'tainacan');
                $data['msg'] = __('Please select a URL to this item', 'tainacan');
                return $data;
            }
        }
        //validation format
        if ($data['object_type'] != 'text' && $data['object_from'] == 'internal') {
            $path = $_FILES['object_file']['name'];
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            if ($data['object_type'] == 'video' && !in_array($ext, ['mp4', 'm4v', 'wmv', 'avi', 'mpg', 'ogv', '3gp', '3g2'])) {
                $data['validation_error'] = true;
                $data['title'] = __('Error', 'tainacan');
                $data['msg'] = __('The format not allowed for video', 'tainacan');
                return $data;
            } elseif ($data['object_type'] == 'image' && !in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                $data['validation_error'] = true;
                $data['title'] = __('Error', 'tainacan');
                $data['msg'] = __('The format not allowed for image', 'tainacan');
                return $data;
            } elseif ($data['object_type'] == 'audio' && !in_array($ext, ['mp3', 'm4a', 'ogg', 'wav', 'wma'])) {
                $data['validation_error'] = true;
                $data['title'] = __('Error', 'tainacan');
                $data['msg'] = __('The format not allowed for audio', 'tainacan');
                return $data;
            } elseif ($data['object_type'] == 'pdf' && !in_array($ext, ['pdf'])) {
                $data['validation_error'] = true;
                $data['title'] = __('Error', 'tainacan');
                $data['msg'] = __('This is not a PDF file!', 'tainacan');
                return $data;
            }
        } elseif ($data['object_type'] != 'text' && $data['object_from'] == 'external') {
            $path = $data['object_url'];
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            if ($data['object_type'] == 'image' && !in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'svg'])) {
                $data['validation_error'] = true;
                $data['title'] = __('Error', 'tainacan');
                $data['msg'] = __('The format not allowed for image', 'tainacan');
                return $data;
            } elseif ($data['object_type'] == 'audio' && !in_array($ext, ['mp3', 'm4a', 'ogg', 'wav', 'wma'])) {
                $data['validation_error'] = true;
                $data['title'] = __('Error', 'tainacan');
                $data['msg'] = __('The format not allowed for audio', 'tainacan');
                return $data;
            } elseif ($data['object_type'] == 'pdf' && !in_array($ext, ['pdf'])) {
                $data['validation_error'] = true;
                $data['title'] = __('Error', 'tainacan');
                $data['msg'] = __('This is not a PDF file!', 'tainacan');
                return $data;
            }
        }

        return $data;
    }

    /**
     * @signature - fast_insert_url($data)
     * @param array $data Os dados vindos do formulario
     * @return json com os dados do resultado do evento criado
     * @description - Insere um objeto vindo da importacao url
     * @author: Eduardo 
     */
    public function fast_insert_url($data) {
        $user_id = get_current_user_id();
        if ($user_id == 0) {
            $user_id = get_option('anonimous_user');
        }
        $category_root_id = $this->collection_model->get_category_root_of($data['collection_id']);
        $post = array(
            'post_title' => $data['title'],
            'post_content' => $data['description'],
            'post_status' => 'draft',
            'post_author' => $user_id,
            'post_type' => 'socialdb_object'
        );
        $data['ID'] = wp_insert_post($post);
        //categoria raiz da colecao
        wp_set_object_terms($data['ID'], array((int) $category_root_id), 'socialdb_category_type');
       
        //inserindo as classificacoes
        if (isset($data['classifications']) && $data['classifications']) {
            $this->insert_classifications($data['classifications'], $data['ID']);
        }
        if (isset($data['thumbnail_url']) && $data['thumbnail_url']) {
            $this->add_thumbnail_url($data['thumbnail_url'], $data['ID']);
        }
        //inserindo a url fonte dos dados
        if (isset($data['url']) && $data['url']) {
            update_post_meta($data['ID'], 'socialdb_uri_imported', $data['url']);
        }
        //verificando se existe mapeamento ativo
        if (get_post_meta($data['collection_id'], 'socialdb_collection_mapping_exportation_active')) {
            add_post_meta($data['ID'], 'socialdb_channel_id', get_post_meta($data['collection_id'], 'socialdb_collection_mapping_exportation_active', true));
        }
        // inserindo o evento
        $data = $this->insert_object_event($data['ID'], $data);

        return $data;
    }

    /**
     * @signature - fast_insert($data)
     * @param array $data Os dados vindos do formulario
     * @return json com os dados do resultado do evento criado
     * @description - Insere um objeto apenas com o titulo
     * @author: Eduardo 
     */
    public function fast_insert($data) {
        $category_root_id = $this->collection_model->get_category_root_of($data['collection_id']);
        $user_id = get_current_user_id();
        if ($user_id == 0) {
            $user_id = get_option('anonimous_user');
        }
        $post = array(
            'post_title' => $data['title'],
            'post_content' => '',
            'post_status' => 'draft',
            'post_author' => $user_id,
            'post_type' => 'socialdb_object'
        );
        $data['ID'] = wp_insert_post($post);
        $post['ID'] =  $data['ID'];
        //categoria raiz da colecao
        wp_set_object_terms($data['ID'], array((int) $category_root_id), 'socialdb_category_type');
        
        update_post_meta( $data['ID'], 'socialdb_object_dc_type', 'text');
        update_post_meta( $data['ID'], 'socialdb_object_from', 'internal');
        //inserindo as classificacoes
        if (isset($data['classifications']) && $data['classifications']) {
            $this->insert_classifications($data['classifications'], $data['ID']);
        }
        //verificando se existe mapeamento ativo
        if (get_post_meta($data['collection_id'], 'socialdb_collection_mapping_exportation_active')) {
            add_post_meta($data['ID'], 'socialdb_channel_id', get_post_meta($data['collection_id'], 'socialdb_collection_mapping_exportation_active', true));
        }
        // inserindo o evento
        $data['item'] = $post;
        $data = $this->insert_object_event($data['ID'], $data);
        
        return $data;
    }
    
    public function add_item_not_published($data) {
        $object_id = socialdb_insert_object_socialnetwork([$data['title']], 'draft',$data['description']);
        update_post_meta($object_id, 'socialdb_object_dc_type', $data['type']);
        update_post_meta($object_id, 'socialdb_uri_imported', $data['url']);
        update_post_meta($object_id, 'socialdb_object_from', 'external');
        if($data['type']=='text'){
            update_post_meta($object_id, 'socialdb_object_dc_source', $data['thumbnail_url']);
            if (isset($data['thumbnail_url']) && $data['thumbnail_url'] && $data['thumbnail_url'] !== '') {
                $this->add_thumbnail_url($data['thumbnail_url'], $object_id);
            }
        }else{
            if($data['content']){
                update_post_meta($object_id, 'socialdb_object_dc_source', $data['content']);
                 if($data['type']=='image'){
                    $this->add_thumbnail_url($data['content'],$object_id);
                 }
                 update_post_meta($object_id, 'socialdb_object_content',$data['content']);
            }
        }
        return json_encode([$object_id]);
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

    /**
     * @signature - function update_classifications($classification_string, $object_id)
     * @param string $classification_string A string que esta concatenada com os valores do dynatree selecionadas
     * @param int $object_id O id do Objeto
     * @return void
     * @description - Atualiza os valores selecionados no dynatree no objeto criado
     * @author: Eduardo 
     */
    public function update_classifications($classification_string, $object_id, $collection_id = null) {
        $properties = array();
        $tags = array();
        $categories = array();
        $classification_array = explode(',', $classification_string);
        foreach ($classification_array as $classification) {
            if (strpos($classification, '_') !== false) {
                $value_array = explode('_', $classification);
                if ($value_array[1] == 'tag') {
                    $tags[] = (int) $value_array[0];
                } else {
                    $properties[$value_array[1]][] = $value_array[0];
                }
            } else {
                if ((int) $classification != 0) {
                    $categories[] = (int) $classification;
                }
            }
        }
        if ($properties) {
            foreach ($properties as $property => $values) {
                delete_post_meta($object_id, 'socialdb_property_' . $property);
                foreach ($values as $value) {
                    add_post_meta($object_id, 'socialdb_property_' . $property, $value);
                }
            }
        }


        if (!empty($categories)) {

            $categories[] = (int) $this->collection_model->get_category_root_of($collection_id);
            wp_set_object_terms($object_id, $categories, 'socialdb_category_type');
        } else {
            wp_delete_object_term_relationships($object_id, 'socialdb_category_type');
            $category_root_id = $this->collection_model->get_category_root_of($collection_id);
            //categoria raiz da colecao
            wp_set_object_terms($object_id, array((int) $category_root_id), 'socialdb_category_type');
        }
        if (!empty($tags)) {
            wp_set_object_terms($object_id, $tags, 'socialdb_tag_type');
        } else {
            wp_delete_object_term_relationships($object_id, 'socialdb_tag_type');
        }
    }

    /**
     * @signature - function insert_tags($string_tags, $collection_id, $object_id)
     * @param string $string_tags A string que veio do formulario com todas as tags
     * @param int $collection_id O id da colecao
     * @param int $object_id O id do Objeto
     * @return void
     * @description - Insere os valores das tags colocadas pelo usuario
     * @author: Eduardo 
     */
    public function insert_tags($string_tags, $collection_id, $object_id) {
        $tagModel = new TagModel();
        $this->concatenate_commom_field_value( $object_id, "socialdb_propertyterm_tag", '');
        $tags = explode(',', $string_tags);
        if (is_array($tags) && !empty($tags)) {
            foreach ($tags as $tag) {
                if ($tag !== ''):
                    $tag_array = $tagModel->add($tag, $collection_id);
                    $tagModel->add_tag_object($tag_array['term_id'], $object_id);
                    $this->concatenate_commom_field_value( $object_id, "socialdb_propertyterm_tag", $tag_array['term_id']);
                endif;
            }
        }
    }

    /**
     * @signature - function insert_properties_values($data)
     * @param array $data Com os dados vindo do formulario
     * @return void
     * @description - Insere os valores das propriedades 
     * @author: Eduardo 
     */
    public function insert_properties_values($data, $object_id) {
        $property_model = new PropertyModel;
        if ($data['properties_id'] !== '') {
            $properties_id = explode(',', $data['properties_id']);

            foreach ($properties_id as $property_id) {
                $dados = json_decode($property_model->edit_property(array('property_id' => $property_id)));
                if ($dados->type && in_array($dados->type, ['stars', 'like', 'binary'])) {
                    add_post_meta($object_id, 'socialdb_property_' . $dados->id, 0);
                    continue;
                }
                //se for para inserir os valores das propriedades de dados
                if (!is_array($data["socialdb_property_$property_id"]) && $data["socialdb_property_$property_id"] !== '') {
                    update_post_meta($object_id, "socialdb_property_$property_id", $data["socialdb_property_$property_id"]);
                    //inserir o valor no metadado de valor comu
                    $this->set_common_field_values($object_id,  "socialdb_property_$property_id", $data["socialdb_property_$property_id"]);
                }// se estiver inserindo propriedade de objeto e tiver valores relacionado 
                elseif (is_array($data["socialdb_property_$property_id"]) && !empty(is_array($data["socialdb_property_$property_id"]))) {
                    delete_post_meta($object_id, "socialdb_property_$property_id");
                    $this->set_common_field_values($object_id,  "socialdb_property_$property_id", $data["socialdb_property_$property_id"],'item');
                    foreach ($data["socialdb_property_$property_id"] as $value) {
                        if(empty(trim($value)))
                            continue;
                        
                        add_post_meta($object_id, "socialdb_property_$property_id", $value);
                        if (isset($dados->metas->socialdb_property_object_is_reverse) && ($dados->metas->socialdb_property_object_is_reverse === 'true')) {
                            add_post_meta($value, "socialdb_property_" . $dados->metas->socialdb_property_object_reverse, $object_id);
                        }//adicionando a propriedade simetrica
                        else if(get_post($value)&&isset($dados->metas->socialdb_property_object_is_reverse)) {
                            delete_post_meta($value, "socialdb_property_$property_id", $object_id);
                            add_post_meta($value, "socialdb_property_$property_id", $object_id);
                        }
                    }
                }// se estiver apenas apagando a propriedade de objeto ou de dados
                else {
                    $property_reverse = get_post_meta($object_id, "socialdb_property_$property_id", true);
                    if (isset($dados->metas) && ($dados->metas->socialdb_property_object_is_reverse == 'true' && $property_reverse)) {
                        add_post_meta($property_reverse, "socialdb_property_" . $dados->metas->socialdb_property_object_reverse, '');
                    } else {
                        $this->clean_simetric_property($object_id, $property_id);
                    }
                    update_post_meta($object_id, "socialdb_property_$property_id", '');
                     $this->set_common_field_values($object_id,  "socialdb_property_$property_id", '');
                }
            }
        }
    }

    /**
     * @signature - function clean_simetric_property($data)
     * @param array $data Com os dados vindo do formulario
     * @return void
     * @description - deleta os valores simetricos
     * @author: Eduardo 
     */
    public function clean_simetric_property($object_id, $property_id) {
        $itens = get_post_meta($object_id, "socialdb_property_$property_id");
        if ($itens) {
            foreach ($itens as $item) {
                delete_post_meta($item, "socialdb_property_$property_id", $object_id);
            }
        }
    }

    /**
     * @signature - function insert_properties_terms($data)
     * @param array $data Com os dados vindo do formulario
     * @return void
     * @description - Insere os valores das propriedades 
     * @author: Eduardo 
     */
    public function insert_properties_terms($data, $object_id) {
        if ($data['properties_id'] !== '') {
            $properties_id = explode(',', $data['properties_id']);
            foreach ($properties_id as $property_id) {
                if (isset($data["socialdb_propertyterm_$property_id"])&&!is_array($data["socialdb_propertyterm_$property_id"]) && $data["socialdb_propertyterm_$property_id"] !== '') {
                    wp_set_object_terms($object_id, array((int) $data["socialdb_propertyterm_$property_id"]), 'socialdb_category_type', true);
                     $this->set_common_field_values($object_id, "socialdb_propertyterm_$property_id", [(int) $data["socialdb_propertyterm_$property_id"]],'term');
                } elseif (is_array($data["socialdb_propertyterm_$property_id"]) && !empty(is_array($data["socialdb_propertyterm_$property_id"]))) {
                    foreach ($data["socialdb_propertyterm_$property_id"] as $value) {
                        wp_set_object_terms($object_id, array((int) $value), 'socialdb_category_type', true);
                        $this->set_common_field_values($object_id, "socialdb_propertyterm_$property_id", $data["socialdb_propertyterm_$property_id"],'term');
                    }
                }
            }
        }
    }

    /**
     * @signature - function insert_event($object_id, $data )
     * @param int $object_id O id do Objeto
     * @param array $data Os dados vindos do formulario
     * @return array os dados para o evento
     * @description - 
     * @author: Eduardo 
     */
    public function insert_object_event($object_id, $data) {
        $eventAddObject = new EventObjectCreateModel();
        $data['socialdb_event_object_item_id'] = $object_id;
        $data['socialdb_event_collection_id'] = $data['collection_id'];
        $data['socialdb_event_user_id'] = get_current_user_id();
        $data['socialdb_event_create_date'] = time();
        return $eventAddObject->create_event($data);
    }

    /**
     * @signature - function add_video($collection_id, $name, $content )
     * @paramters - ($collection_id, $name, $content)
     * @return - boolean confirmação da inserção do post
     * @description - métdo responsável pela inserção de vídeos em uma coleção
     * @author: Eduardo 
     */
    public function add_video($collection_id, $name, $content) {
        $category_root_id = $this->collection_model->get_category_root_of($collection_id);
        $user_id = get_current_user_id();
        if ($user_id == 0) {
            $user_id = get_option('anonimous_user');
        }
        $post = array(
            'post_title' => $name,
            'post_content' => $content,
            'post_status' => 'publish',
            'post_author' => $user_id,
            'post_type' => 'socialdb_object'
        );
        $data['ID'] = wp_insert_post($post);
        //categoria raiz da colecao
        wp_set_object_terms($data['ID'], array((int) $category_root_id), 'socialdb_category_type');
        //verificando se existe mapeamento ativo
        if (get_post_meta($collection_id, 'socialdb_collection_mapping_exportation_active')) {
            add_post_meta($data['ID'], 'socialdb_channel_id', get_post_meta($data['collection_id'], 'socialdb_collection_mapping_exportation_active', true));
        }
        return $data['ID'];
    }

    /**
     * @signature - function add_photo($collection_id, $name, $content )
     * @paramters - ($collection_id, $name, $content)
     * @return - boolean confirmação da inserção do post
     * @description - métdo responsável pela inserção de vídeos em uma coleção
     * @author: saymon 
     */
    public function add_photo($collection_id, $name, $content, $type = 'image', $source = '') {
        $category_root_id = $this->collection_model->get_category_root_of($collection_id);
        $user_id = get_current_user_id();
        if ($user_id == 0) {
            $user_id = get_option('anonimous_user');
        }
        $post = array(
            'post_title' => $name,
            'post_content' => '',
            'post_status' => 'publish',
            'post_author' => $user_id,
            'post_type' => 'socialdb_object'
        );
        $data['ID'] = wp_insert_post($post);
        update_post_meta($data['ID'], 'socialdb_object_from', 'external');
        update_post_meta($data['ID'], 'socialdb_object_dc_source', $source);
        update_post_meta($data['ID'], 'socialdb_object_content', $content);
        update_post_meta($data['ID'], 'socialdb_object_dc_type', $type);
        //categoria raiz da colecao
        wp_set_object_terms($data['ID'], array((int) $category_root_id), 'socialdb_category_type');
        //verificando se existe mapeamento ativo
        if (get_post_meta($collection_id, 'socialdb_collection_mapping_exportation_active')) {
            add_post_meta($data['ID'], 'socialdb_channel_id', get_post_meta($data['collection_id'], 'socialdb_collection_mapping_exportation_active', true));
        }
        return $data['ID'];
    }

    /**
     * @signature - edit($object_id,$collection_id)
     * @param int $object_id O id do objeto a ser editado
     * @param int $collection_id O id da colecao do objeto
     * @return array  Os dados a serem usados na view
     * @description - metodo responsavel em buscar os dados de um objeto
     * @author: Eduardo 
     */
    public function edit($object_id, $collection_id) {
        $data = [];
        $properties = [];
        $data['object'] = get_post($object_id);
        $categories = $this->get_object_categories_id($object_id);
        $tags = $this->get_object_tags_id($object_id, true);
        $data['tags'] = $tags;
        $all_data = $this->list_properties(array('object_id' => $object_id, 'collection_id' => $collection_id)); // busco todas as propriedades do objeto
        if ($all_data['property_object']) {// verifico se tem o que interessa, propriedade de objeto
            foreach ($all_data['property_object'] as $property_object) {// varro o array
                if ($property_object['metas']['value']) {// verifico se o valor nao esta vazio
                    foreach ($property_object['metas']['value'] as $value) { // pecorro os valores
                        $properties[] = $value . '_' . $property_object['id']; // monta a string como no key do dynatree
                    }
                }
            }
        }
        $data['classifications'] = implode(',', array_merge($properties, array_merge($categories, $tags)));

        return $data;
    }

    /**
     * @signature - function add_video($collection_id, $name, $content )
     * @paramters - ($collection_id, $name, $content)
     * @return - boolean confirmação da inserção do post
     * @description - métdo responsável pela inserção de vídeos em uma coleção
     * @author: Eduardo 
     */
    public function update($data) {
        $post = array(
            'ID' => $data['object_id'],
            'post_title' => $data['object_name'],
            'post_content' => $data['object_description'],
            'post_type' => 'socialdb_object'
        );
        $data['ID'] = wp_update_post($post);
        if ($data['remove_thumbnail_object']) {
            delete_post_thumbnail($data['ID']);
        }
        //inserindo o objecto do item e o seu tipo
        $this->insert_item_resource($data);
        //inserindo as classificacoes
        $this->update_classifications($data['object_classifications'], $data['ID'], $data['collection_id']);
        //inserindo tags
        $this->insert_tags($data['object_tags'], $data['collection_id'], $data['ID']);
        //inserindo os valores das propriedades
        $this->insert_properties_values($data, $data['ID']);
        //verificando se existe aquivos para ser incluidos
        if ($_FILES) {
            $this->add_thumbnail_item($data['ID']);
        }
        //inserido via img via url
        if (isset($data['thumbnail_url']) && $data['thumbnail_url']) {
            $this->add_thumbnail_url($data['thumbnail_url'], $data['ID']);
        }
        //inserindo a url fonte dos dados
        if (isset($data['object_url']) && $data['object_url']) {
            delete_post_meta($data['ID'], 'socialdb_uri_imported');
            update_post_meta($data['ID'], 'socialdb_uri_imported', $data['object_url']);
        }

        //object_license
        if ($data['object_license']) {
            update_post_meta($data['ID'], 'socialdb_license_id', $data['object_license']);
        }
        // propriedade de termos
        $this->insert_properties_terms($data, $data['ID']);

        //msg
        $data['msg'] = __('The event was successful', 'tainacan');
        $data['type'] = 'success';
        $data['title'] = __('Success', 'tainacan');
        return json_encode($data);
    }

    /**
     * function delete($data)
     * @param array Array com os dados do post a ser excluido
     * @return void 
     * Metodo reponsavel em excluir o objeto
     * Autor: Eduardo Humberto 
     */
    public function delete($data) {
        wp_delete_post($data['ID']);
        return json_encode($data);
    }
    
    /**
     * 
     * @param array $data Os dados vindo do modal de upload de imagem
     */
    public function insert_attachment_event($data) {
        $post = array(
            'post_title' => 'Temporary_post',
            'post_status' => 'inherit',
            'post_author' => get_current_user_id(),
            'post_type' => 'socialdb_object'
        );
        $object_id = wp_insert_post($post);
        $attachment_id = $this->add_object_item($object_id);
        if($attachment_id&&$attachment_id>0){
            return json_encode(['attachment_id'=>$attachment_id]);
        } else{
            return json_encode(false);
        }
    }

    /**
     * function list_all()
     * @param array Array com os dados da colecao
     * @return void 
     * Metodo reponsavel em determinar se deve listar as colecoes ou objetos
     * Autor: Eduardo Humberto 
     */
    public function list_all($args = null) {
        if ($args['collection_id'] == get_option('collection_root_id')) {
            return $this->list_collection($args);
        } else {
            return $this->list_object($args);
        }
    }

    /**
     * function list_all()
     * @param array Array com os dados da colecao
     * @return void 
     * Metodo reponsavel em determinar se deve listar as colecoes ou objetos
     * Autor: Eduardo Humberto 
     */
    public function filter($args = null) {
        if ($args['collection_id'] == get_option('collection_root_id')) {
            return $this->list_collection($args);
        } else {
            return $this->filter_objects($args);
        }
    }

    /**
     * function get_args($data)
     * @param array Array com os dados da colecao
     * @return void 
     * Metodo reponsavel em determinar se deve listar as colecoes ou objetos
     * Autor: Eduardo Humberto 
     */
    public function get_args($data) {
        $args['category_root_id'] = $this->collection_model->get_category_root_of($data['collection_id']);
        // pego as classificacoes
        $categories = $this->get_classification('category', $data['classifications']);
        $tags = $this->get_classification('tag', $data['classifications']);
        $properties = $this->get_classification('property', $data['classifications']);
        if ($categories) {
            $args['facets'] = $this->categories_by_facet($categories, $data['collection_id']);
        }
        if ($tags) {
            $args['tags'] = $tags;
        }
        if ($properties) {
            $args['properties_tree'] = $properties;
        }
        //tipo de ordenacao
        $args['orderby'] = $this->set_order_by($data);
        if ($args['orderby'] == 'meta_value_num') {
            $args['metakey'] = 'socialdb_property_' . $data['ordenation_id'];
            $args['ordenation_id'] = $data['ordenation_id'];
        }
        //a forma de ordenacao
        $args['order'] = $this->set_type_order($data);
        $args['collection_id'] = $data['collection_id'];
        return $args;
    }

    /**
     * function list_object()
     * @param array Array com os dados da colecao
     * @return void 
     * Metodo reponsavel em  listar apenas objetos
     * Autor: Eduardo Humberto 
     */
    public function list_object($args = null) {
        $tax_query = array('relation' => 'IN');
        $tax_query[] = array(
            'taxonomy' => 'socialdb_category_type',
            'field' => 'id',
            'terms' => array($this->collection_model->get_category_root_of($args['collection_id']))
        );
        //tipo de ordenacao
        $orderby = $this->set_order_by($args);
        $array_defaults = ['socialdb_object_from', 'socialdb_object_dc_type', 'socialdb_object_dc_source', 'title', 'socialdb_license_id'];
//        if ($orderby == 'meta_value_num') {
//            $meta_key = 'socialdb_property_' . $args['ordenation_id'];
//        }
        if ($orderby == 'meta_value_num') {
            $meta_key = 'socialdb_property_' . trim($args['ordenation_id']);
        } elseif (in_array($orderby, $array_defaults)) {
            $meta_key = $orderby;
        } else {
            $meta_key = '';
        }
        //a forma de ordenacao
        $order = $this->set_type_order($args);
        $args = array(
            'posts_per_page' => 10,
            'post_type' => 'socialdb_object',
            'paged' => 1,
            'tax_query' => $tax_query,
            'orderby' => $orderby,
            'order' => $order,
            //  'no_found_rows' => true, // counts posts, remove if pagination required
            'update_post_term_cache' => false, // grabs terms, remove if terms required (category, tag...)
            'update_post_meta_cache' => false, // grabs post meta, remove if post meta required
        );
        if (isset($meta_key) && !in_array($meta_key, ['title', 'comment_count'])) {
            $args['meta_key'] = $meta_key;
        }
        return $args;
    }

    /**
     * function filter()
     * @param array Array com os dados a serem utilizados para realizar os filtros
     * @return void 
     * Metodo reponsavel em  realizar a filtragem dos items que estao listados
     * @author Eduardo Humberto 
     */
    public function filter_objects($data) {
        // pego as classificacoes
        $categories = $this->get_classification('category', $data['classifications']);
        $tags = $this->get_classification('tag', $data['classifications']);
        $properties = $this->get_classification('property', $data['classifications']);
        // inserindo as categorias e as tags na query
        $tax_query = $this->get_tax_query($categories, $data['collection_id'], $tags);
        //inserindo as propriedades
        $meta_query = $this->get_meta_query($properties);
        //tipo de ordenacao
        $orderby = $this->set_order_by($data);
        $array_defaults = ['socialdb_object_from', 'socialdb_object_dc_type', 'socialdb_object_dc_source', 'title', 'socialdb_license_id'];
        if ($orderby == 'meta_value_num') {
            $meta_key = 'socialdb_property_' . trim($data['ordenation_id']);
        } elseif (in_array($orderby, $array_defaults)) {
            $meta_key = $orderby;
        } else {
            $meta_key = '';
        }
        //a forma de ordenacao
        $order = $this->set_type_order($data);
        //a pagina 
        $page = $this->set_page($data);
        //all_data_inside
        $args = array(
            'post_type' => 'socialdb_object',
            'paged' => $page,
            'posts_per_page' => 10,
            'tax_query' => $tax_query,
            'orderby' => $meta_key,
            'order' => $order,
            //'no_found_rows' => true, // counts posts, remove if pagination required
            'update_post_term_cache' => false, // grabs terms, remove if terms required (category, tag...)
            'update_post_meta_cache' => false, // grabs post meta, remove if post meta required
        );
        if ($meta_query) {
            $args['meta_query'] = $meta_query;
        }
        if (isset($meta_key) && !in_array($meta_key, ['title', 'comment_count'])) {
            $args['meta_key'] = $meta_key;
        }
        if (isset($data['keyword']) && $data['keyword'] != '') {
            $args['s'] = $data['keyword'];
        }
        return $args;
    }

    /**
     * @signature set_page($data)
     * @param array $data O array de dados vindo do formulario
     * @return int com a pagina a ser visualizada
     * Metodo reponsavel em  retornar o fromato que sera ordenado (crescente ou decrescente)
     * @author Eduardo Humberto 
     */
    public function set_page($data) {
        if ($data['pagid'] && $data['pagid'] != '' && is_numeric($data['pagid'])) {
            return $data['pagid'];
        } else {
            return 1;
        }
    }

    /**
     * @signature set_type_order($data)
     * @param array $data O array de dados vindo do formulario
     * @return string com o tipo de pesquisa que sera realizada
     * Metodo reponsavel em  retornar o fromato que sera ordenado (crescente ou decrescente)
     * @author Eduardo Humberto 
     */
    public function set_type_order($data) {
        if (!isset($data['order_by']) || $data['order_by'] == '') {
            $order = get_post_meta($data['collection_id'], 'socialdb_collection_ordenation_form', true);
            if ($order !== '' && $order) {
                return strtoupper($order);
            } else {
                return 'DESC';
            }
        } else if ($data['order_by'] && $data['order_by'] == 'asc') {
            return 'ASC';
        } else {
            return 'DESC';
        }
    }

    /**
     * @signature set_order_by($data)
     * @param array $data O array de dados vindo do formulario
     * @return string com o tipo de pesquisa que sera realizada
     * Metodo reponsavel em  retornar o tipo de ordem que sera utilizado no wp_query
     * @author Eduardo Humberto 
     */
    public function set_order_by($data) {
        $defaults = false;
        $array_defaults = ['socialdb_object_from', 'socialdb_object_dc_type', 'socialdb_object_dc_source', 'title', 'socialdb_license_id'];
        if (isset($data['ordenation_id'])) {
            $property = get_term_by('id', $data['ordenation_id'], 'socialdb_property_type');
        } else {
            $property = false;
        }

        if (in_array($data['ordenation_id'], $array_defaults) || in_array($data['orderby'], $array_defaults)) {
            $defaults = true;
        }

        if ($property && $property->slug != 'socialdb_ordenation_recent') {
            return 'meta_value_num';
        } elseif ($defaults) {
            if (isset($data['ordenation_id'])) {
                return trim($data['ordenation_id']);
            } else {
                return trim($data['orderby']);
            }
        } else {
            return 'date';
        }
    }

    /**
     * @signature get_tax_query($categories,$collection_id)
     * @param array $categories as categorias selecionadas no dynatree a serem utilizadas na filtragem das colecoes
     * @param int $collection_id O id da colecao
     * @param array $tags as tags selecionadas no dynatree
     * @return array com os dados a serem utilizados no wp_query 
     * Metodo reponsavel em  montar o array que sera utilizado no Wp_query
     * @author Eduardo Humberto 
     */
    public function get_tax_query($categories, $collection_id, $tags) {
        // coloco categorias no array tax query
        $tax_query = array('relation' => 'AND'); // devem ter a relacao AND para filtrar dentro da colecao
        $tax_query[] = array(
            'taxonomy' => 'socialdb_category_type',
            'field' => 'id',
            'terms' => $this->collection_model->get_category_root_of($collection_id),
            'operator' => 'IN'
        );
        if ($categories) {
            $categories_by_facet = $this->categories_by_facet($categories, $collection_id);
            foreach ($categories_by_facet as $category_by_facet) {
                $tax_query[] = array(
                    'taxonomy' => 'socialdb_category_type',
                    'field' => 'id',
                    'terms' => $category_by_facet,
                    'operator' => 'IN'
                );
            }
        }
        if ($tags) {
            $tax_query[] = array(
                'taxonomy' => 'socialdb_tag_type',
                'field' => 'id',
                'terms' => $tags,
                'operator' => 'IN'
            );
        }
        return $tax_query;
    }

    /**
     * @signature categories_by_facet($categories, $collection_id)
     * @param array $categories as categorias selecionadas no dynatree a serem utilizadas na filtragem das colecoes
     * @param int $collection_id O id da colecao
     * @return array com os categorias separadas pela faceta (faceta como chave no array) 
     * @author Eduardo Humberto 
     */
    public function categories_by_facet($categories, $collection_id) {
        $categories_by_facet = array();
        $category_model = new CategoryModel();
        if (is_array($categories)) {
            foreach ($categories as $category) {
                $key = $category_model->get_category_facet_parent($category, $collection_id);
                $categories_by_facet[$key][] = $category;
            }
        }
        return $categories_by_facet;
    }

    /**
     * @signature get_tax_query($categories,$collection_id)
     * @param array $categories as categorias selecionadas no dynatree a serem utilizadas na filtragem das colecoes
     * @param int $collection_id O id da colecao
     * @param array $tags as tags selecionadas no dynatree
     * @return array com os dados a serem utilizados no wp_query 
     * Metodo reponsavel em  montar o array que sera utilizado no Wp_query
     * @author Eduardo Humberto 
     */
    public function get_meta_query($properties) {
        $meta_query = array();
        if ($properties) {
            $meta_query = array('relation' => 'AND');
            foreach ($properties as $property_id => $value_id) {
                $meta_query[] = array(
                    'key' => 'socialdb_property_' . $property_id,
                    'value' => $value_id,
                    'compare' => 'IN'
                );
            }
        }
        return $meta_query;
    }

    /**
     * function list_collection()
     * @param void
     * @return void 
     * Metodo reponsavel em listar as colecoes
     * Autor: Eduardo Humberto 
     */
    public function list_collection($data = null) {
        global $wp_query;
        $page = $this->set_page($data);
        $args = array(
            'post_type' => 'socialdb_collection',
            'post_status' => array('publish'),
            'post__not_in' => array(get_option('collection_root_id')),
            'paged' => $page,
            'orderby' => 'date',
            'order' => 'DESC',
        );
        if (isset($data['keyword']) && $data['keyword'] != '') {
            $args['s'] = $data['keyword'];
        }
        if ($data['mycollections'] && $data['mycollections'] == 'true') {
            $args['author'] = get_current_user_id();
        }
        return $args;
    }

    /**
     * function get_object_author()
     * @param int $user_id O id do usuario que deseja visualizar
     * @param string field O campo que deseja visualizar
     * @return mix o campo desejado 
     * Metodo reponsavel em listar as colecoes
     * Autor: Eduardo Humberto 
     */
    public function get_object_author($user_id, $field = 'id') {
        $array = $this->user_model->get_user($user_id);
        return $array[$field];
    }

    public function help_choosing_license($data) {
        //commercial_use_license 1,0
        //change_work_license 1,2,0

        $suggested_license = null;

        if ($data['commercial_use_license'] == '1') {

            if ($data['change_work_license'] == '1') {
                $suggested_license = "Creative Commons CC BY";
            } elseif ($data['change_work_license'] == '2') {
                $suggested_license = "Creative Commons CC BY-SA";
            } elseif ($data['change_work_license'] == '0') {
                $suggested_license = "Creative Commons CC BY-ND";
            }
        } elseif ($data['commercial_use_license'] == '0') {

            if ($data['change_work_license'] == '1') {
                $suggested_license = "Creative Commons CC BY-NC";
            } elseif ($data['change_work_license'] == '2') {
                $suggested_license = "Creative Commons CC BY-NC-SA";
            } elseif ($data['change_work_license'] == '0') {
                $suggested_license = "Creative Commons CC BY-NC-ND";
            }
        }

        return $this->get_cc_license_id($suggested_license);
    }

    public function get_cc_license_id($suggested_license) {
        $arrLicenses = get_option('socialdb_standart_licenses');
        $data_license = null;
        foreach ($arrLicenses as $license) {
            $object_post = get_post($license);
            if (strcmp($suggested_license, $object_post->post_title) == 0) {
                $data_license['id'] = $object_post->ID;
                $data_license['nome'] = $object_post->post_title;
                $data_license['msg'] = __("Your suggested license is: ", 'tainacan') . $suggested_license;
                $data_license['title'] = __("License", 'tainacan');
                $data_license['type'] = "success";
            }
        }

        if (!is_array($data_license)) {
            $data_license['msg'] = __("Something went wrong! Please try again!", 'tainacan');
            $data_license['title'] = __("Error", 'tainacan');
            $data_license['type'] = "error";
        }

        return $data_license;
    }

    /**
     * function show_collection_licenses()
     * @param array Array com os dados da colecao
     * @return As licenças habilitadas da colecao
     * @author Eduardo Humberto
     */
    public function show_collection_licenses($data) {
        $enabledLicenses = unserialize(get_post_meta($data['collection_id'], 'socialdb_collection_license_enabled')[0]);
        if (isset($data['object_id'])) {
            $data['pattern'] = get_post_meta($data['object_id'], 'socialdb_license_id');
        } else {
            $data['pattern'] = get_post_meta($data['collection_id'], 'socialdb_collection_license_pattern');
        }

        if ($enabledLicenses) {

            $arrLicenses = get_option('socialdb_standart_licenses');
            foreach ($arrLicenses as $license) {
                $object_post = get_post($license);
                if (in_array($object_post->ID, $enabledLicenses)) {
                    $data_license['id'] = $object_post->ID;
                    $data_license['nome'] = $object_post->post_title;

                    $data['licenses'][] = $data_license;
                }
            }

            $arrLicenses_custom = get_option('socialdb_custom_licenses');
            if ($arrLicenses_custom) {
                foreach ($arrLicenses_custom as $license) {
                    $object_post = get_post($license);
                    if (in_array($object_post->ID, $enabledLicenses)) {
                        $data_license['id'] = $object_post->ID;
                        $data_license['nome'] = $object_post->post_title;

                        $data['licenses'][] = $data_license;
                    }
                }
            }
            $collection_meta = get_post_meta($data['collection_id'], 'socialdb_collection_license');
            if ($collection_meta):
                foreach ($collection_meta as $meta):
                    if ($meta):
                        $object_post = get_post($meta);
                        if (in_array($object_post->ID, $enabledLicenses)):
                            $data_license['id'] = $object_post->ID;
                            $data_license['nome'] = $object_post->post_title;

                            $data['licenses'][] = $data_license;
                        endif;
                    endif;
                endforeach;
            endif;
        }

        return $data;
    }

    /**
     * Metodo responsavel em retornar as configuracoes de cada metadado para a montagem 
     * de sua view <i> show_object_properties</i>
     * 
     * function show_object_properties()
     * @param array Array com os dados da colecao
     * @return As propriedades da colecao mais das categorias selecionadas
     * @author Eduardo Humberto
     */
    public function show_object_properties($data) {
        $category_model = new CategoryModel();
        $categories = explode(',', $data['categories']);
        // se existir categorias no item, devera retornar um array de objetos com
        // as categorias do item
        if (!empty($categories)) {
            $all_categories = $category_model->get_terms_object_in_array_by_taxonomy($categories);
        } else {
            $all_categories = array();
        }
        // busco todos os ids de propriedades que este item possui, incluindo 
        // as propriedades das categorias, por isso eh necessario o id das categorias
        // o qual foi classificado
        $all_properties = $category_model->get_properties($data['collection_id'], $all_categories);
        $data['all_ids'] = implode(',', $all_properties);
        // este metodo no retorno tem como objetivo  pegar os valores das propriedades
        // do item para ser mostrado na view
        $result = $this->set_data_object_properties($all_properties, $data);
        return $result;
    }

    /**
     * function set_data_object_properties()
     * @param array Array com as popriedades
     * @return Os dados que serao utilizados para construcao da view
     * @author Eduardo Humberto
     */
    public function set_data_object_properties($properties, $data) {
        $property_model = new PropertyModel();
        if (is_array($properties)) {
            foreach ($properties as $property_id) {
                $type = $property_model->get_property_type_hierachy($property_id); // pego o tipo da propriedade
                $all_data = $property_model->get_all_property($property_id, true); // pego todos os dados possiveis da propriedade
                //diferenciando os tipos
                if ($type == 'socialdb_property_object') {// pego o tipo
                    $all_data['metas']['objects'] = $this->get_category_root_posts($all_data['metas']['socialdb_property_object_category_id']);
                    $all_data['metas']['collection_data'] = $this->get_collection_by_category_root($all_data['metas']['socialdb_property_object_category_id']);
                    // pegando os valores se necessario
                    if ($data['object_id']) {
                        $all_data['metas']['value'] = $property_model->get_object_property_value($data['object_id'], $property_id);
                        $all_data['metas']['object_id'] = $data['object_id'];
                    }
                    $data['property_object'][] = $all_data;
                } elseif ($type == 'socialdb_property_data') {
                    // pegando os valores se necessario
                    if ($data['object_id']) {
                        $all_data['metas']['value'] = $property_model->get_object_property_value($data['object_id'], $property_id);
                        $all_data['metas']['object_id'] = $data['object_id'];
                    }
                    //se caso for autoincrement
                    if ($all_data['metas']['socialdb_property_data_widget'] == 'autoincrement') {
                        $all_data['metas']['socialdb_property_data_value_increment'] = $this->get_last_counter($property_id);
                    }
                    $data['property_data'][] = $all_data;
                } else if ($type == 'socialdb_property_term') {
                    if ($data['object_id']) {
                        $all_data['metas']['value'] = $property_model->get_object_property_value($data['object_id'], $property_id);
                        $all_data['metas']['object_id'] = $data['object_id'];
                    }
                    $all_data['has_children'] = $this->getChildren($all_data['metas']['socialdb_property_term_root']);
                    $data['property_term'][] = $all_data;
                } else {
                    $data['rankings'][] = $all_data;
                }
            }
        }
        $data['category_root_id'] = $this->get_category_root_of($data['collection_id']);
        return $data;
    }

    /**
     * function list_properties()
     * 
     * Metdoo que retorna todas propriedades de um objeto ja buscando as propriedades de suas categorias
     * 
     * @param array Array com os dados do formulario
     * @return Os dados que serao utilizados para construcao da view
     * @author Eduardo Humberto
     */
    public function list_properties($data) {
        $data['categories'] = implode(',', $this->get_object_categories_id($data['object_id']));
        return $this->show_object_properties($data);
    }

    /**
     * function get_object_categories_id()
     * @param int O id do objeto
     * @return array(int) Os term_id das categories vinculadas ao objeto
     * @author Eduardo Humberto
     */
    public function get_object_categories_id($object_id) {
        $categories_id = array();
        $categories = wp_get_object_terms($object_id, 'socialdb_category_type');
        if (is_array($categories) && !empty($categories)) {
            foreach ($categories as $category) {
                $categories_id[] = $category->term_id;
            }
        }
        return $categories_id;
    }


    /**
     * function get_objects_by_property_json()
     * @param int Os dados vindo do formulario
     * @return json com o id e o nome de cada objeto
     * @author Eduardo Humberto
     */
    public function get_objects_by_property_json($data) {
        global $wpdb;
        $wp_posts = $wpdb->prefix . "posts";
        $term_relationships = $wpdb->prefix . "term_relationships";
        $property_model = new PropertyModel;
        $all_data = $property_model->get_all_property($data['property_id'], true); // pego todos os dados possiveis da propriedad
        //$category_root_id = get_term_by('id', $all_data['metas']['socialdb_property_object_category_id'], 'socialdb_category_type');
//        $query = "
//                        SELECT p.* FROM $wp_posts p
//                        INNER JOIN $term_relationships t ON p.ID = t.object_id    
//                        WHERE t.term_taxonomy_id = {$category_root_id->term_taxonomy_id}
//                        AND p.post_type like 'socialdb_object' and p.post_status like 'publish' and p.post_title LIKE '%{$data['term']}%'
//                ";
//        $result = $wpdb->get_results($query);
        $result = $this->get_category_root_posts($all_data['metas']['socialdb_property_object_category_id']);
        if ($result) {
            foreach ($result as $object) {
                if(strpos(strtolower($object->post_title), strtolower($data['term']))!==false){
                     $json[] = array('value' => $object->ID, 'label' => $object->post_title);
                }
            }
        }
        return json_encode($json);
    }

    /**
     * funcao que retorna os valores de um propriedade de objeto de um objeto em json
     * @param int Os dados vindo do formulario
     * @return json com o id e o nome de cada objeto da propriedade
     * @author Eduardo Humberto
     */
    public function get_property_object_value($data) {
        $properties = get_post_meta($data['object_id'], 'socialdb_property_' . $data['property_id']);
        if (isset($properties) && $properties[0] != '') {
            foreach ($properties as $property) {
                $data['values'][] = array('id' => $property, 'name' => get_post($property)->post_title);
            }
        }
        return json_encode($data);
    }

    /**
     * funcao show_classifications
     * @param array $data Os dados vindo do formulario
     * @return array com os dados a serem utilizados para mostrar tdos as classificacoes de um objeto
     * @author Eduardo Humberto
     */
    public function show_classifications($data) {
        $categoryModel = new CategoryModel;
        $categories = wp_get_object_terms($data['object_id'], 'socialdb_category_type');
        if (is_array($categories)) {
            foreach ($categories as $category) {
                if($this->get_category_root_of($data['collection_id'])==$category->term_id){
                    continue;
                }
                $category_data['term'] = $category;
                $facet_id = $categoryModel->get_category_facet_parent($category->term_id, $data['collection_id']);
                $category_data['class'] = $categoryModel->get_facet_class($facet_id, $data['collection_id']);
                if ($category_data['class'] == '' || !$category_data['class']) {
                    $category_data['class'] = 'color13';
                }
                $data['categories'][] = $category_data;
            }
        }
        $properties = $this->get_all_object_properties($data['object_id'], $data['collection_id']);
        if (is_array($properties) && !empty($properties)) {
            $data['properties'] = $properties;
        }

        $tags = wp_get_object_terms($data['object_id'], 'socialdb_tag_type');
        $data['tags'] = $tags;
        return $data;
    }

    /**
     * funcao que retorna todas as propriedades de um objeto em um array de objetos postmeta ($object->meta_value)
     * @param int $object_id Os dados vindo do formulario
     * @param int $collection_id O id da colecao
     * @return array com os dados a serem utilizados
     * @author Eduardo Humberto
     */
    public function get_all_object_properties($object_id, $collection_id) {
        global $wpdb;
        $wp_posts = $wpdb->prefix . "posts";
        $wp_postmeta = $wpdb->prefix . "postmeta";
        $query = "
                      SELECT pm.* FROM $wp_posts p
                      INNER JOIN $wp_postmeta pm ON p.ID = pm.post_id    
                      WHERE p.ID = $object_id AND pm.meta_key LIKE 'socialdb_property_%'
             ";
        $array_results = $wpdb->get_results($query);
        return $this->get_properties_facets($array_results, $collection_id);
    }

    /**
     * funcao que retorna todas as propriedades facetas com o seu valor, id e
     * @param array $array_results Os dados vindos da consulta do banco de dados
     * @return array com os dados a serem utilizados
     * @author Eduardo Humberto
     */
    public function get_properties_facets($array_results, $collection_id) {
        $categoryModel = new CategoryModel;
        $data = array();
        if ($array_results) {
            foreach ($array_results as $object) {
                $property_id = str_replace('socialdb_property_', '', $object->meta_key);
                $facets = CollectionModel::get_facets($collection_id);
                //$is_facet = get_term_meta($property_id, 'socialdb_property_object_is_facet', true);
                //if ($is_facet && $is_facet == 'true') {
                if (in_array($property_id, $facets)) {
                    $property['property_id'] = $property_id;
                    $property['relationship_id'] = $object->meta_value;
                    if (get_term_by('id', $property_id, 'socialdb_property_type')) {
                        $property['relationship_name'] = get_post($object->meta_value)->post_title;
                        $property['class'] = $categoryModel->get_facet_class($property_id, $collection_id);
                    }
                    if ($property['relationship_name']) {
                        $data[] = $property;
                    }
                } elseif (get_term_by('id', $property_id, 'socialdb_category_type')) {
                    $property['property_id'] = $property_id;
                    $property['relationship_id'] = $object->meta_value;
                    $category = get_term_by('id', $object->meta_value, 'socialdb_category_type');
                    $property['relationship_name'] = $category->name;
                    $property['class'] = 'category_property_img';
                    $data[] = $property;
                }
            }
        }
        return $data;
    }

    /**
     * funcao que retorna a string que sera colocada no titulo dos objetos sem ser realizado qualquer pesquisa
     * @param int $collection_id O id da colecao
     * @param int $property_id A propriedade que foi selecionada
     * @param string $order A forma de ordenacao
     * @return array com os dados a serem utilizados
     * @author Eduardo Humberto
     */
    public function get_ordered_name($collection_id, $property_id = '', $order = 'desc') {
        $result = '';
        if (is_numeric($property_id)) {
            $ordenation = $property_id;
        } else {
            $ordenation = get_post_meta($collection_id, 'socialdb_collection_default_ordering', true);
        }
        $term = get_term_by('id', $ordenation, 'socialdb_property_type');
        if ($term && $term->slug != 'socialdb_ordenation_recent') {
            $parent = get_term_by('id', $term->parent, 'socialdb_property_type');
            if ($parent->name == 'socialdb_property_data') {
                $result.= $term->name . ' (' . __('Data property', 'tainacan') . ')';
            } else {
                $result.= $term->name . ' (' . __('Ranking', 'tainacan') . ')';
            }
        } else {
            $result.= __('Recents', 'tainacan');
        }

        if ($order == 'desc') {
            $result = '<span class="glyphicon glyphicon-sort-by-attributes-alt"></span>&nbsp;' . $result;
        } else {
            $result = '<span class="glyphicon glyphicon-sort-by-attributes"></span>&nbsp;' . $result;
        }
        return $result;
    }

    public function verify_comment_permissions($collection_id) {
        $permissions['create'] = get_post_meta($collection_id, 'socialdb_collection_permission_create_comment', true);
        $permissions['edit'] = get_post_meta($collection_id, 'socialdb_collection_permission_edit_comment', true);
        $permissions['delete'] = get_post_meta($collection_id, 'socialdb_collection_permission_delete_comment', true);

        return $permissions;
    }

    /**
     * funcao que despublica todos os itens de uma colecao
     * @param array $data Os dados vindo da requisicao
     * @return array com os dados a serem utilizados para resposata
     * @author Eduardo Humberto
     */
    public function clean_collection($data) {
        $items = $this->get_collection_posts_trash($data['collection_id']);
        if ($items && is_array($items)) {
            foreach ($items as $item) {
                $object = array(
                    'ID' => $item->ID,
                    'post_status' => 'draft'
                );
                $object_id = $item->ID;
                $collection_id = $data['collection_id'];
                //***** BEGIN CHECK SOCIAL NETWORKS *****//
                // YOUTUBE
                $mapping_id_youtube = $this->get_post_by_title('socialdb_channel_youtube', $collection_id, 'youtube');
                $getCurrentIds_youtube = unserialize(get_post_meta($mapping_id_youtube, 'socialdb_channel_youtube_inserted_ids', true));
                if (isset($getCurrentIds_youtube[$object_id])) {
                    unset($getCurrentIds_youtube[$object_id]);
                    update_post_meta($mapping_id_youtube, 'socialdb_channel_youtube_inserted_ids', serialize($getCurrentIds_youtube));
                }

                //FACEBOOK
                $mapping_id_facebook = $this->get_post_by_title('socialdb_channel_facebook', $collection_id, 'facebook');
                $getCurrentIds_facebook = unserialize(get_post_meta($mapping_id_facebook, 'socialdb_channel_facebook_inserted_ids', true));
                if (isset($getCurrentIds_facebook[$object_id])) {
                    unset($getCurrentIds_facebook[$object_id]);
                    update_post_meta($mapping_id_facebook, 'socialdb_channel_facebook_inserted_ids', serialize($getCurrentIds_facebook));
                }

                //INSTAGRAM
                $mapping_id_instagram = $this->get_post_by_title('socialdb_channel_instagram', $collection_id, 'instagram');
                $getCurrentIds_instagram = unserialize(get_post_meta($mapping_id_instagram, 'socialdb_channel_instagram_inserted_ids', true));
                if (isset($getCurrentIds_instagram[$object_id])) {
                    unset($getCurrentIds_instagram[$object_id]);
                    update_post_meta($mapping_id_instagram, 'socialdb_channel_instagram_inserted_ids', serialize($getCurrentIds_instagram));
                }

                //FLICKR
                $mapping_id_flickr = $this->get_post_by_title('socialdb_channel_flickr', $collection_id, 'flickr');
                $getCurrentIds_flickr = unserialize(get_post_meta($mapping_id_flickr, 'socialdb_channel_flickr_inserted_ids', true));
                if (isset($getCurrentIds_flickr[$object_id])) {
                    unset($getCurrentIds_flickr[$object_id]);
                    update_post_meta($mapping_id_flickr, 'socialdb_channel_flickr_inserted_ids', serialize($getCurrentIds_flickr));
                }

                //VIMEO
                $mapping_id_vimeo = $this->get_post_by_title('socialdb_channel_vimeo', $collection_id, 'vimeo');
                $getCurrentIds_vimeo = unserialize(get_post_meta($mapping_id_vimeo, 'socialdb_channel_vimeo_inserted_ids', true));
                if (isset($getCurrentIds_vimeo[$object_id])) {
                    unset($getCurrentIds_vimeo[$object_id]);
                    update_post_meta($mapping_id_vimeo, 'socialdb_channel_vimeo_inserted_ids', serialize($getCurrentIds_vimeo));
                }
                //***** END CHECK SOCIAL NETWORKS *****//
                wp_update_post($object);
            }
            $data['title'] = __('Success', 'tainacan');
            $data['msg'] = __('All items removed successfully', 'tainacan');
            $data['type'] = 'success';
        } else {
            $data['title'] = __('Attention', 'tainacan');
            $data['msg'] = __('No items added in this collection', 'tainacan');
            $data['type'] = 'info';
        }
        return $data;
    }

    /**
     * funcao que despublica  itens de uma colecao
     * @param array $data Os dados vindo da requisicao
     * @return array com os dados a serem utilizados para resposata
     * @author Eduardo Humberto
     */
    public function delete_items_socialnetwork($data) {
        $items = $data['items_id'];
        if ($items && is_array($items)) {
            foreach ($items as $item) {
                if (!is_object($item)) {
                    $item = get_post($item);
                }
                $object = array(
                    'ID' => $item->ID,
                    'post_status' => 'draft'
                );
                $object_id = $item->ID;
                $collection_id = $data['collection_id'];
                //***** BEGIN CHECK SOCIAL NETWORKS *****//
                // YOUTUBE
                $mapping_id_youtube = $this->get_post_by_title('socialdb_channel_youtube', $collection_id, 'youtube');
                $getCurrentIds_youtube = unserialize(get_post_meta($mapping_id_youtube, 'socialdb_channel_youtube_inserted_ids', true));
                if (isset($getCurrentIds_youtube[$object_id])) {
                    unset($getCurrentIds_youtube[$object_id]);
                    update_post_meta($mapping_id_youtube, 'socialdb_channel_youtube_inserted_ids', serialize($getCurrentIds_youtube));
                }

                //FACEBOOK
                $mapping_id_facebook = $this->get_post_by_title('socialdb_channel_facebook', $collection_id, 'facebook');
                $getCurrentIds_facebook = unserialize(get_post_meta($mapping_id_facebook, 'socialdb_channel_facebook_inserted_ids', true));
                if (isset($getCurrentIds_facebook[$object_id])) {
                    unset($getCurrentIds_facebook[$object_id]);
                    update_post_meta($mapping_id_facebook, 'socialdb_channel_facebook_inserted_ids', serialize($getCurrentIds_facebook));
                }

                //INSTAGRAM
                $mapping_id_instagram = $this->get_post_by_title('socialdb_channel_instagram', $collection_id, 'instagram');
                $getCurrentIds_instagram = unserialize(get_post_meta($mapping_id_instagram, 'socialdb_channel_instagram_inserted_ids', true));
                if (isset($getCurrentIds_instagram[$object_id])) {
                    unset($getCurrentIds_instagram[$object_id]);
                    update_post_meta($mapping_id_instagram, 'socialdb_channel_instagram_inserted_ids', serialize($getCurrentIds_instagram));
                }

                //FLICKR
                $mapping_id_flickr = $this->get_post_by_title('socialdb_channel_flickr', $collection_id, 'flickr');
                $getCurrentIds_flickr = unserialize(get_post_meta($mapping_id_flickr, 'socialdb_channel_flickr_inserted_ids', true));
                if (isset($getCurrentIds_flickr[$object_id])) {
                    unset($getCurrentIds_flickr[$object_id]);
                    update_post_meta($mapping_id_flickr, 'socialdb_channel_flickr_inserted_ids', serialize($getCurrentIds_flickr));
                }

                //VIMEO
                $mapping_id_vimeo = $this->get_post_by_title('socialdb_channel_vimeo', $collection_id, 'vimeo');
                $getCurrentIds_vimeo = unserialize(get_post_meta($mapping_id_vimeo, 'socialdb_channel_vimeo_inserted_ids', true));
                if (isset($getCurrentIds_vimeo[$object_id])) {
                    unset($getCurrentIds_vimeo[$object_id]);
                    update_post_meta($mapping_id_vimeo, 'socialdb_channel_vimeo_inserted_ids', serialize($getCurrentIds_vimeo));
                }
                //***** END CHECK SOCIAL NETWORKS *****//
                wp_update_post($object);
            }
            $data['title'] = __('Success', 'tainacan');
            $data['msg'] = __('All items removed successfully', 'tainacan');
            $data['type'] = 'success';
        } else {
            $data['title'] = __('Attention', 'tainacan');
            $data['msg'] = __('No items added in this collection', 'tainacan');
            $data['type'] = 'info';
        }
        return $data;
    }

    public function set_attachment_description($post_parent, $post_content) {
        if ( isset($data['post_content']) && isset($data['post_parent']) ) {
            $post_content = $data['post_content'];
            $post_parent = $data['post_parent'];
        }
        $newest_attachment = get_posts( ['post_type' => 'attachment', 'post_parent' => $post_parent, 'exclude' => get_post_thumbnail_id($post_parent), 'numberposts' => 1] )[0];
        wp_update_post( ['ID' => $newest_attachment->ID, 'post_content' => $post_content] );
    }

    public function update_attachment_legend($item_id, $item_content) {
        wp_update_post( [ 'ID' => $item_id, 'post_content' => $item_content ] );
    }

}
