<?php
/**
 * Event cONTROLLER
 *
 */

require_once(dirname(__FILE__).'../../../models/event/event_classification/event_classification_create_model.php');
require_once(dirname(__FILE__).'../../../models/event/event_classification/event_classification_delete_model.php');
require_once(dirname(__FILE__).'../../../models/event/event_object/event_object_create_model.php');
require_once(dirname(__FILE__).'../../../models/event/event_object/event_object_delete_model.php');
require_once(dirname(__FILE__).'../../../models/event/event_property_object/event_property_object_edit_value_model.php');
require_once(dirname(__FILE__).'../../../models/event/event_property_object/event_property_object_create_model.php');
require_once(dirname(__FILE__).'../../../models/event/event_property_object/event_property_object_edit_model.php');
require_once(dirname(__FILE__).'../../../models/event/event_property_object/event_property_object_delete_model.php');
require_once(dirname(__FILE__).'../../../models/event/event_property_data/event_property_data_create_model.php');
require_once(dirname(__FILE__).'../../../models/event/event_property_data/event_property_data_edit_model.php');
require_once(dirname(__FILE__).'../../../models/event/event_property_data/event_property_data_delete_model.php');
require_once(dirname(__FILE__).'../../../models/event/event_property_data/event_property_data_edit_value_model.php');
require_once(dirname(__FILE__).'../../../models/event/event_term/event_term_create_model.php');
require_once(dirname(__FILE__).'../../../models/event/event_term/event_term_edit_model.php');
require_once(dirname(__FILE__).'../../../models/event/event_term/event_term_delete_model.php');
require_once(dirname(__FILE__).'../../../models/event/event_comment/event_comment_create_model.php');
require_once(dirname(__FILE__).'../../../models/event/event_comment/event_comment_edit_model.php');
require_once(dirname(__FILE__).'../../../models/event/event_comment/event_comment_delete_model.php');
require_once(dirname(__FILE__).'../../../models/event/event_collection/event_collection_delete_model.php');
require_once(dirname(__FILE__).'../../../models/event/event_collection/event_collection_create_model.php');
require_once(dirname(__FILE__).'../../../models/event/event_model.php');
require_once(dirname(__FILE__).'../../general/general_controller.php');  
require_once(dirname(__FILE__).'../../../models/event/event_tag/event_tag_create_model.php');
require_once(dirname(__FILE__).'../../../models/event/event_tag/event_tag_edit_model.php');
require_once(dirname(__FILE__).'../../../models/event/event_tag/event_tag_delete_model.php');
require_once(dirname(__FILE__).'../../../models/ranking/ranking_model.php');

 class EventController extends Controller{
	 public function operation($operation,$data){
             
		switch ($operation) {
                    case "list":
                        $data = EventModel::list_events($data);
                        return $this->render(dirname(__FILE__).'../../../views/event/list.php', $data);
                        break; 
                    case 'notification_events':
                        $data = EventModel::list_events($data);
                        if(isset($data['events_not_observed'])){
                           $not_observed_events = '&nbsp;'.count($data['events_not_observed']).'&nbsp;';
                        }
                        return $not_observed_events;
                    case 'notification_events_repository':
                        $data['collection_id'] = get_option('collection_root_id');
                        $data = EventModel::list_events($data);
                        if(isset($data['events_not_observed'])){
                           $not_observed_events = '&nbsp;'.count($data['events_not_observed']).'&nbsp;';
                        }
                        return $not_observed_events;
                    case 'get_event_info':
                        return  EventModel::get_event($data);
                    case 'list_events_repository':
                         $data = EventModel::list_events($data);
                        return $this->render(dirname(__FILE__).'../../../views/event/list.php', $data);
                    // APROVACAO DE EVENTOS DEMOCRATICOS    
                    case 'process_events_selected':
                        $this->process_events_selected($data);
                        break;  
                    // APROVACAO DE EVENTOS    
                        
                    //classification_create
                    case 'add_event_classification_create';
                        $event_classification_create_model = new EventClassificationCreateModel();
                        return $event_classification_create_model->create_event($data);
                    case 'socialdb_event_classification_create';
                        $event_classification_create_model = new EventClassificationCreateModel();
                        return $event_classification_create_model->verify_event($data); 
                    //classification_delete
                    case 'add_event_classification_delete':  
                        $event_classification_delete_model = new EventClassificationDeleteModel();
                        return $event_classification_delete_model->create_event($data);
                    case 'socialdb_event_classification_delete';
                        $event_classification_delete_model = new EventClassificationDeleteModel();
                        return $event_classification_delete_model->verify_event($data);
                    //object_delete
                    case 'add_event_object_create':  
                        $event_object_create_model = new EventObjectCreateModel();
                        return $event_object_create_model->create_event($data);
                    case 'socialdb_event_object_create';
                        $event_object_create_model = new EventObjectCreateModel();
                        return $event_object_create_model->verify_event($data);    
                    //object_delete
                    case 'add_event_object_delete':  
                        $event_object_delete_model = new EventObjectDeleteModel();
                        return $event_object_delete_model->create_event($data);
                    case 'socialdb_event_object_delete';
                        $event_object_delete_model = new EventObjectDeleteModel();
                        return $event_object_delete_model->verify_event($data);  
                        
                    //property_data_edit_value
                     case 'add_event_property_data_edit_value': 
                        $event_property_data_edit_value_model = new EventPropertyDataEditValue(); 
                        if(!isset($data['socialdb_event_property_data_edit_value_suggested_value'])||empty($data['socialdb_event_property_data_edit_value_suggested_value'])){
                            $data['socialdb_event_property_data_edit_value_suggested_value'] = '';
                        }
                        return $event_property_data_edit_value_model->create_event($data);
                    case 'socialdb_event_property_data_edit_value';
                        $event_property_data_edit_value_model = new EventPropertyDataEditValue();
                        return $event_property_data_edit_value_model->verify_event($data);
                     //create property data
                    case 'add_event_property_data_create':  
                        $event_property_data_create_model = new EventPropertyDataCreate();
                        return $event_property_data_create_model->create_event($data);
                    case 'socialdb_event_property_data_create';
                        $event_data_delete_model = new EventPropertyDataCreate();
                        return $event_data_delete_model->verify_event($data);  
                    //edit property data 
                    case 'add_event_property_data_edit':  
                        $event_property_data_edit_model = new EventPropertyDataEdit();
                        return $event_property_data_edit_model->create_event($data);
                    case 'socialdb_event_property_data_edit';
                        $event_property_data_edit_model = new EventPropertyDataEdit();
                        return $event_property_data_edit_model->verify_event($data);    
                    //delete property data
                    case 'add_event_property_data_delete':  
                        $event_property_data_delete_model = new EventPropertyDataDelete();
                        return $event_property_data_delete_model->create_event($data);
                    case 'socialdb_event_property_data_delete';
                        $event_property_data_delete_model = new EventPropertyDataDelete();
                        return $event_property_data_delete_model->verify_event($data);    
                        
                        
                    //property_object_edit_value
                     case 'add_event_property_object_edit_value':  
                        $event_property_object_edit_value_model = new EventPropertyObjectEditValue();
                        if(is_array($data['socialdb_event_property_object_edit_value_suggested_value'])){
                            //$array = $data['socialdb_event_property_object_edit_value_suggested_value'];
                            $return = $event_property_object_edit_value_model->create_event($data);
                           // foreach ($array as $value) {
                              //  $data['socialdb_event_property_object_edit_value_suggested_value'] = $value;
                              //  $return = $event_property_object_edit_value_model->create_event($data);
                           // }
                        }else{// se estiver excluindo todos os valores
                            $data['socialdb_event_property_object_edit_value_suggested_value'] = '';
                            $data['delete_all_values'] = true;
                            $return = $event_property_object_edit_value_model->create_event($data);
                        }
                        return $return;
                    case 'socialdb_event_property_object_edit_value';
                        $event_property_object_edit_value_model = new EventPropertyObjectEditValue();
                        return $event_property_object_edit_value_model->verify_event($data);    
                     //create property object
                    case 'add_event_property_object_create':  
                        $event_property_object_create_model = new EventPropertyObjectCreate();
                        return $event_property_object_create_model->create_event($data);
                    case 'socialdb_event_property_object_create';
                        $event_object_delete_model = new EventPropertyObjectCreate();
                        return $event_object_delete_model->verify_event($data);  
                    //edit property object 
                    case 'add_event_property_object_edit':  
                        $event_property_object_edit_model = new EventPropertyObjectEdit();
                        return $event_property_object_edit_model->create_event($data);
                    case 'socialdb_event_property_object_edit';
                        $event_property_object_edit_model = new EventPropertyObjectEdit();
                        return $event_property_object_edit_model->verify_event($data);  
                    //delete property object 
                    case 'add_event_property_object_delete':  
                        $event_property_object_delete_model = new EventPropertyObjectDelete();
                        return $event_property_object_delete_model->create_event($data);
                    case 'socialdb_event_property_object_delete';
                        $event_property_object_delete_model = new EventPropertyObjectDelete();
                        return $event_property_object_delete_model->verify_event($data);
                        
                    //create category
                    case 'add_event_term_create':  
                        $event_term_create_model = new EventTermCreate();
                        return $event_term_create_model->create_event($data);
                    case 'socialdb_event_term_create';
                        $event_term_create_model = new EventTermCreate();
                        return $event_term_create_model->verify_event($data);  
                    //edit category
                    case 'add_event_term_edit':  
                        $event_term_edit_model = new EventTermEdit();
                        return $event_term_edit_model->create_event($data);
                    case 'socialdb_event_term_edit';
                        $event_term_edit_model = new EventTermEdit();
                        return $event_term_edit_model->verify_event($data);  
                    //delete category
                    case 'add_event_term_delete':  
                        $event_term_delete_model = new EventTermDelete();
                        return $event_term_delete_model->create_event($data);
                    case 'socialdb_event_term_delete';
                        $event_term_delete_model = new EventTermDelete();
                        return $event_term_delete_model->verify_event($data);    
                        
                   //create tag
                    case 'add_event_tag_create':  
                        $event_tag_create_model = new EventTagCreate();
                        return $event_tag_create_model->create_event($data);
                    case 'socialdb_event_tag_create';
                        $event_tag_create_model = new EventTagCreate();
                        return $event_tag_create_model->verify_event($data);  
                    //edit tag
                    case 'add_event_tag_edit':  
                        $event_tag_edit_model = new EventTagEdit();
                        return $event_tag_edit_model->create_event($data);
                    case 'socialdb_event_tag_edit';
                        $event_tag_edit_model = new EventTagEdit();
                        return $event_tag_edit_model->verify_event($data);  
                    //delete tag
                    case 'add_event_tag_delete':  
                        $event_tag_delete_model = new EventTagDelete();
                        return $event_tag_delete_model->create_event($data);
                    case 'socialdb_event_tag_delete';
                        $event_tag_delete_model = new EventTagDelete();
                        return $event_tag_delete_model->verify_event($data);  
                        
                     //collection_delete
                    case 'add_event_collection_delete':  
                        $event_collection_delete_model = new EventCollectionDeleteModel();
                        return $event_collection_delete_model->create_event($data);
                    case 'socialdb_event_collection_delete';
                        $event_collection_delete_model = new EventCollectionDeleteModel();
                        return $event_collection_delete_model->verify_event($data); 
                     //collection_delete
                    case 'add_event_collection_create':  
                        $event_collection_create_model = new EventCollectionDeleteModel();
                        return $event_collection_create_model->create_event($data);
                    case 'socialdb_event_collection_create';
                        $event_collection_create_model = new EventCollectionCreateModel();
                        return $event_collection_create_model->verify_event($data); 
                        
                    //create comment
                    case 'add_event_comment_create':  
                        $event_comment_create_model = new EventCommentCreate();
                        return $event_comment_create_model->create_event($data);
                    case 'socialdb_event_comment_create';
                        $event_comment_create_model = new EventCommentCreate();
                        return $event_comment_create_model->verify_event($data);  
                    //edit comment
                    case 'add_event_comment_edit':  
                        $event_comment_edit_model = new EventCommentEdit();
                        return $event_comment_edit_model->create_event($data);
                    case 'socialdb_event_comment_edit';
                        $event_comment_edit_model = new EventCommentEdit();
                        return $event_comment_edit_model->verify_event($data); 
                    //delete comment
                    case 'add_event_comment_delete':  
                        $event_comment_delete_model = new EventCommentDelete();
                        return $event_comment_delete_model->create_event($data);
                    case 'socialdb_event_comment_delete';
                        $event_comment_delete_model = new EventCommentDelete();
                        return $event_comment_delete_model->verify_event($data); 
                    
                        
                        
		}
	}
        
        
        public function process_events_selected($data) {
            $values = explode(',',$data['events']);
            $ranking_model = new RankingModel;
            if($values  &&  is_array($values) && !empty($values)){
                foreach ($values as $value) {
                    $event = get_post($value);
                    $info['date'] = get_post_meta($event->ID, 'socialdb_event_create_date', true);
                    $info['id'] = $event->ID;
                    $info['democratic_vote_id'] = get_post_meta($event->ID, 'socialdb_event_democratic_vote_id', true);
                    $count = $ranking_model->count_votes_binary($info['democratic_vote_id'], $event->ID);
                    $info['count_up'] = $count['count_up'];
                    $info['count_down'] = $count['count_down'];
                    if ($info['count_up'] >= $info['count_down']) {
                        //confirmado
                        update_post_meta($event->ID, 'socialdb_event_confirmed', 'confirmed');
                        //executar o evento
                        $category = wp_get_object_terms($event->ID, 'socialdb_event_type')[0];
                        $this->operation($category->name, array('event_id' => $event->ID, 'socialdb_event_confirmed' => 'true'));
                    } else {
                        //nao confirmado
                        update_post_meta($event->ID, 'socialdb_event_confirmed', 'not_confirmed');
                    }
                    
                }
            }
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

$event_controller = new EventController();
echo $event_controller->operation($operation,$data);


?>