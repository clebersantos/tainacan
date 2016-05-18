<?php
require_once(dirname(__FILE__).'../../../models/property/property_model.php');
require_once(dirname(__FILE__).'../../../models/event/event_property_data/event_property_data_create_model.php');
require_once(dirname(__FILE__).'../../../models/event/event_property_data/event_property_data_edit_model.php');
require_once(dirname(__FILE__).'../../../models/event/event_property_data/event_property_data_delete_model.php');
require_once(dirname(__FILE__).'../../../models/event/event_property_object/event_property_object_create_model.php');
require_once(dirname(__FILE__).'../../../models/event/event_property_object/event_property_object_edit_model.php');
require_once(dirname(__FILE__).'../../../models/event/event_property_object/event_property_object_delete_model.php');
require_once(dirname(__FILE__).'../../../models/event/event_property_term/event_property_term_create_model.php');
require_once(dirname(__FILE__).'../../../models/event/event_property_term/event_property_term_edit_model.php');
require_once(dirname(__FILE__).'../../../models/event/event_property_term/event_property_term_delete_model.php');
require_once(dirname(__FILE__).'../../general/general_controller.php');  

 class PropertyController extends Controller{
	 public function operation($operation,$data){
		$property_model = new PropertyModel();
		switch ($operation) {
            case 'page':
                $term = get_term_by('slug', $data['slug_property'], 'socialdb_property_type') ;
                if ($term) {
                    $data['term'] = $term;
                    $data['parent'] = get_term_by('id', $term->parent, 'socialdb_property_type') ;
                    $data['metadata']['type'] = $property_model->get_property_type($term->term_id); // pego o tipo da propriedade
                    $data['metadata']['data'] = $property_model->get_all_property($term->term_id,true); // pego todos os dados possiveis da propriedade
                    $array_json['html'] = $this->render(dirname(__FILE__) . '../../../views/property/page.php', $data);
                    return json_encode($array_json);
                }else{
                    $array_json['title'] = __('Attention!','tainacan');
                    $array_json['error'] = __('Property removed','tainacan');
                    return json_encode($array_json);
                }
                break;
            case "add_property_data":
                //return $property_model->add_property_data($data);
                return $this->insert_event_property_data_add($data);
                break;
            case "add_property_object":
                //return $property_model->add_property_object($data);
                return $this->insert_event_property_object_add($data);
                break;
            case "add_property_term":
                //return $property_model->add_property_term($data);
                return $this->insert_event_property_term_add($data);
                break;
            case "edit_property_data":
                return $property_model->edit_property($data);
                break;
            case "edit_property_object":
                return $property_model->edit_property($data);
                break;
            case 'edit_property_term':
                return $property_model->edit_property($data);
                break;
            case "update_property_data":
                //return $property_model->update_property_data($data);
                return $this->insert_event_property_data_update($data);
                break;
            case "update_property_object":
                //return $property_model->update_property_object($data);
                return $this->insert_event_property_object_update($data);
                break;
            case "update_property_term":
                //return $property_model->update_property_term($data);
                return $this->insert_event_property_term_update($data);
                break;
            case "delete":
                if($data['type']=='1'):
                    return $this->insert_event_property_data_delete($data);
                elseif($data['type']=='2'):
                    return $this->insert_event_property_object_delete($data);
                elseif($data['type']=='3'):
                    return $this->insert_event_property_delete($data);
                endif;
                break;
            case "list":
                $hide_wizard = (isset($data['hide_wizard']))? 'hide' : 'show';
                $data = $property_model->list_data($data);
                $data['hide_wizard'] = $hide_wizard;
                return $this->render(dirname(__FILE__).'../../../views/property/list.php', $data);
                break;
            case "list_metadata":
                $data = $property_model->list_data($data);
                $data['menu_style_ids'] = $this->get_menu_styles_ids();
                return $this->render(dirname(__FILE__).'../../../views/property/list_metadata.php', $data);
                break;
            case "list_property_terms":
                return $property_model->list_property_terms($data);
            case "list_property_data":
                return $property_model->list_property_data($data);
                break;
            case "list_property_object":
                return $property_model->list_property_object($data);
                break;
            case 'show_reverses':// utiliza a mesma funcao porem muda a categoria para procuar suas propriedades
                $array_final = [];
                if(strpos($data['category_id'], ',')!==false):
                    $return['property_object'] = [];
                    $categories = explode(',', $data['category_id']);
                    foreach ($categories as $category) {
                        $data['category_id'] = $category;
                        $object = json_decode($property_model->list_property_object($data,true));
                        if(!$object->no_properties||$object->no_properties==='false'){
                            $return['property_object'] = array_unique(array_merge($return['property_object'], $object->property_object), SORT_REGULAR) ;
                        }
                    }
                    if(empty($return['property_object'])){
                        $return['no_properties'] = true;
                    }else{
                        $return['no_properties'] = false;
                    }
                    $return = json_encode($return);
                else:
                    $return =  $property_model->list_property_object($data,true);
                endif;
                $return = json_decode($return);
                //retorno apenas as propriedades que se relacionam com a categoria atual 
                //(a qual pertence a propriedade que esta criando a reversa)
                if(!$return->no_properties){
                    foreach ($return->property_object as $property) {
                        if(isset($return->property_id)&&is_array($property->metas->socialdb_property_object_category_id)&&in_array($return->property_id, $property->metas->socialdb_property_object_category_id)){
                            $array_final[] = $property;
                        }else if(isset($property->metas->socialdb_property_object_category_id)&&isset($return->property_id)&&$return->property_id === $property->metas->socialdb_property_object_category_id){
                             $array_final[] = $property;
                        }
                    } 
                    $return->property_object = $array_final;
                }
                return json_encode($return);
            // properties repository
            case "list_repository":
                $data['category_id'] = get_term_by('slug', 'socialdb_category', 'socialdb_category_type')->term_id;
                $data['is_configuration_repository'] = true;
                $data = $property_model->list_data($data);
                return $this->render(dirname(__FILE__).'../../../views/theme_options/property/list.php', $data);
                break;
            // properties terms actions
            case 'get_children_property_terms':
                return json_encode($property_model->get_children_property_terms($data));
                break;
            //retorna a categoria raiz de uma propreidade de termo
            case 'get_property_term_category_root':
                $value  = get_term_meta($data['property_id'], 'socialdb_property_term_root',true);
                return json_encode($value);
            // retornando as propriedades das categorias irmas no autocomplete
            case 'list_properties_autocomplete':
                return trim($property_model->get_properties_autocomplete($data['collection_id'],$data['category'],$data['type'],$data['q']));  
            //retorna o slug da propriedade
            case 'get_slug_property':
                $category = get_term_by('id', $data['term_id'], 'socialdb_property_type');
                if($category){
                    return json_encode(['slug'=>$category->slug]);
                }    
                return json_encode(['slug'=>'']);
                
        }
	}
        /**
     * @signature - function insert_event_add($object_id, $data )
     * @param int $object_id O id do Objeto
     * @param array $data Os dados vindos do formulario
     * @return array os dados para o evento
     * @description - 
     * @author: Eduardo 
     */
    public function insert_event_property_data_add($data) {
        $eventAddProperty = new EventPropertyDataCreate();
        $data['socialdb_property_default_value'] = $data['socialdb_property_default_value'];
        $data['socialdb_event_property_data_create_help'] = $data['socialdb_property_data_help'];
        $data['socialdb_event_property_data_create_name'] = $data['property_data_name'];
        $data['socialdb_event_property_data_create_widget'] = $data['property_data_widget'];
        $data['socialdb_event_property_data_create_ordenation_column'] = $data['property_data_column_ordenation'];
        $data['socialdb_event_property_data_create_required'] = $data['property_data_required'];
        $data['socialdb_event_property_data_create_category_root_id'] = $data['property_category_id'];
        $data['socialdb_event_collection_id'] = $data['collection_id'];
        $data['socialdb_event_user_id'] = get_current_user_id();
        $data['socialdb_event_create_date'] = mktime();
        /* 
         * filtro que trabalha com os dados do formulario de adicao de propriedade de dados
         * para os eventos
         */
        if(has_filter('modificate_values_event_property_data_add')):
            $data = apply_filters( 'modificate_values_event_property_data_add', $data); 
        endif;    
        return $eventAddProperty->create_event($data);
    }
     /**
     * @signature - function insert_event_update($object_id, $data )
     * @param int $object_id O id do Objeto
     * @param array $data Os dados vindos do formulario
     * @return array os dados para o evento
     * @description - 
     * @author: Eduardo 
     */
    public function insert_event_property_data_update($data) {
        $eventEditProperty = new EventPropertyDataEdit();
        $data['socialdb_event_property_data_edit_id'] = $data['property_data_id'];
        $data['socialdb_property_default_value'] = $data['socialdb_property_default_value'];
        $data['socialdb_event_property_data_edit_help'] = $data['socialdb_property_data_help'];
        $data['socialdb_event_property_data_edit_name'] = $data['property_data_name'];
        $data['socialdb_event_property_data_edit_widget'] = $data['property_data_widget'];
        $data['socialdb_event_property_data_edit_ordenation_column'] = $data['property_data_column_ordenation'];
        $data['socialdb_event_property_data_edit_required'] = $data['property_data_required'];
        $data['socialdb_event_collection_id'] = $data['collection_id'];
        $data['socialdb_event_user_id'] = get_current_user_id();
        $data['socialdb_event_create_date'] = mktime();
        /* 
         * filtro que trabalha com os dados do formulario de alteracao de propriedade de dados
         * para os eventos
         */
        if(has_filter('modificate_values_event_property_data_update')):
            $data = apply_filters( 'modificate_values_event_property_data_update', $data); 
        endif;    
        return $eventEditProperty->create_event($data);
    }
    /**
     * @signature - function insert_event_add($object_id, $data )
     * @param int $object_id O id do Objeto
     * @param array $data Os dados vindos do formulario
     * @return array os dados para o evento
     * @description - 
     * @author: Eduardo 
     */
    public function insert_event_property_object_add($data) {
        $eventAddProperty = new EventPropertyObjectCreate();
        $data['socialdb_event_property_object_create_name'] = $data['property_object_name'];
        $data['socialdb_event_property_object_create_category_id'] = $data['property_object_category_id'];
        $data['socialdb_event_property_object_create_required'] = $data['property_object_required'];
        $data['socialdb_event_property_object_create_is_reverse'] = $data['property_object_is_reverse'];
        $data['socialdb_event_property_object_create_category_root_id'] = $data['property_category_id'];
        if(isset($data['property_object_reverse'])){
           $data['socialdb_event_property_object_create_reverse'] = $data['property_object_reverse'];   
        }
        $data['socialdb_event_collection_id'] = $data['collection_id'];
        $data['socialdb_event_user_id'] = get_current_user_id();
        $data['socialdb_event_create_date'] = mktime();
        /* 
         * filtro que trabalha com os dados do formulario de adicao de propriedade de objeto
         * para os eventos
         */
        if(has_filter('modificate_values_event_property_object_add')):
            $data = apply_filters( 'modificate_values_event_property_object_add', $data); 
        endif;    
        return $eventAddProperty->create_event($data);
    }
    /**
     * @signature - function insert_event_add($object_id, $data )
     * @param int $object_id O id do Objeto
     * @param array $data Os dados vindos do formulario
     * @return array os dados para o evento
     * @description - 
     * @author: Eduardo 
     */
    public function insert_event_property_object_update($data) {
        $eventAddProperty = new EventPropertyObjectEdit();
        $data['socialdb_event_property_object_edit_id'] = $data['property_object_id'];
        $data['socialdb_event_property_object_edit_name'] = $data['property_object_name'];
        $data['socialdb_event_property_object_category_id'] = $data['property_object_category_id'];
        $data['socialdb_event_property_object_edit_required'] = $data['property_object_required'];
        $data['socialdb_event_property_object_edit_is_reverse'] = $data['property_object_is_reverse'];
        if(isset($data['property_object_reverse'])){
           $data['socialdb_event_property_object_edit_reverse'] = $data['property_object_reverse'];   
        }
        $data['socialdb_event_collection_id'] = $data['collection_id'];
        $data['socialdb_event_user_id'] = get_current_user_id();
        $data['socialdb_event_create_date'] = mktime();
        /* 
         * filtro que trabalha com os dados do formulario de edicao de propriedade de objeto
         * para os eventos
         */
        if(has_filter('modificate_values_event_property_object_update')):
            $data = apply_filters( 'modificate_values_event_property_object_update', $data); 
        endif;    
        return $eventAddProperty->create_event($data);
    }
     /**
     * @signature - function insert_event_add($object_id, $data )
     * @param int $object_id O id do Objeto
     * @param array $data Os dados vindos do formulario
     * @return array os dados para o evento
     * @description - 
     * @author: Eduardo 
     */
    public function insert_event_property_term_add($data) {
        $eventAddProperty = new EventPropertyTermCreate();
        $data['socialdb_event_property_term_create_name'] = $data['property_term_name'];
        $data['socialdb_event_property_term_create_cardinality'] = $data['socialdb_property_term_cardinality'];
        $data['socialdb_event_property_term_create_root'] = $data['socialdb_property_term_root'];
        $data['socialdb_event_property_term_create_widget'] = $data['socialdb_property_term_widget'];
        $data['socialdb_event_property_term_create_required'] = $data['property_term_required'];
        $data['socialdb_event_property_term_create_help'] = $data['socialdb_property_help'];
        $data['socialdb_event_property_term_create_category_root_id'] = $data['property_category_id'];
        $data['socialdb_event_collection_id'] = $data['collection_id'];
        $data['socialdb_event_user_id'] = get_current_user_id();
        $data['socialdb_event_create_date'] = mktime();
        return $eventAddProperty->create_event($data);
    }
     /**
     * @signature - function insert_event_update($object_id, $data )
     * @param int $object_id O id do Objeto
     * @param array $data Os dados vindos do formulario
     * @return array os dados para o evento
     * @description - 
     * @author: Eduardo 
     */
    public function insert_event_property_term_update($data) {
        $eventAddProperty = new EventPropertyTermEdit();
        $data['socialdb_event_property_term_edit_id'] = $data['property_term_id'];
        $data['socialdb_event_property_term_edit_name'] = $data['property_term_name'];
        $data['socialdb_event_property_term_edit_cardinality'] = $data['socialdb_property_term_cardinality'];
        $data['socialdb_event_property_term_edit_root'] = $data['socialdb_property_term_root'];
        $data['socialdb_event_property_term_edit_widget'] = $data['socialdb_property_term_widget'];
        $data['socialdb_event_property_term_edit_required'] = $data['property_term_required'];
        $data['socialdb_event_property_term_edit_help'] = $data['socialdb_property_help'];
        $data['socialdb_event_collection_id'] = $data['collection_id'];
        $data['socialdb_event_user_id'] = get_current_user_id();
        $data['socialdb_event_create_date'] = mktime();
        return $eventAddProperty->create_event($data);
    }
    
    /**
     * @signature - function insert_event_update($object_id, $data )
     * @param int $object_id O id do Objeto
     * @param array $data Os dados vindos do formulario
     * @return array os dados para o evento
     * @description - 
     * @author: Eduardo 
     */
    public function insert_event_property_delete($data) {
        $eventAddProperty = new EventPropertyTermDelete();
        $data['socialdb_event_property_term_delete_id'] = $data['property_delete_id'];
        $data['socialdb_event_collection_id'] = $data['collection_id'];
        $data['socialdb_event_property_term_delete_category_root_id'] = $data['property_category_id'];
        $data['socialdb_event_user_id'] = get_current_user_id();
        $data['socialdb_event_create_date'] = mktime();
        return $eventAddProperty->create_event($data);
    }
    /**
     * @signature - function insert_event_update($object_id, $data )
     * @param int $object_id O id do Objeto
     * @param array $data Os dados vindos do formulario
     * @return array os dados para o evento
     * @description - 
     * @author: Eduardo 
     */
    public function insert_event_property_data_delete($data) {
        $eventAddProperty = new EventPropertyDataDelete();

        $data['socialdb_event_property_data_delete_id'] = $data['property_delete_id'];
        $data['socialdb_event_collection_id'] = $data['collection_id'];
        $data['socialdb_event_property_data_delete_category_root_id'] = $data['property_category_id'];
        $data['socialdb_event_user_id'] = get_current_user_id();
        $data['socialdb_event_create_date'] = mktime();

        return $eventAddProperty->create_event($data);
    }
     /**
     * @signature - function insert_event_update($object_id, $data )
     * @param int $object_id O id do Objeto
     * @param array $data Os dados vindos do formulario
     * @return array os dados para o evento
     * @description - 
     * @author: Eduardo 
     */
    public function insert_event_property_object_delete($data) {
        $eventAddProperty = new EventPropertyObjectDelete();
        $data['socialdb_event_property_object_delete_id'] = $data['property_delete_id'];
        $data['socialdb_event_property_object_delete_category_root_id'] = $data['property_category_id'];
        $data['socialdb_event_collection_id'] = $data['collection_id'];
        $data['socialdb_event_user_id'] = get_current_user_id();
        $data['socialdb_event_create_date'] = mktime();
        return $eventAddProperty->create_event($data);
    }
 }
 /*
 * Controller execution
 */
 if($_POST['operation']){
      $operation = $_POST['operation'];
      $data = $_POST;
 }else{
     $operation = $_GET['operation'];
     $data = $_GET;
 }

 $property_controller = new PropertyController();
 echo $property_controller->operation($operation,$data);