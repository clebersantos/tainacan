<?php
$_GET['is_module_active'] = TRUE;
include_once(dirname(__FILE__).'../../../models/filters/filters_model.php');
include_once(dirname(__FILE__).'/../../../../controllers/general/general_controller.php');  
 class FiltersController extends Controller{
	 public function operation($operation,$data){
                $model = new FiltersModel;   
		switch ($operation) {
                    case 'initDynatreePropertiesFilter':
                        return $model->initDynatreePropertiesFilter($data['collection_id']);
                    case 'childrenDynatreePropertiesFilter':
                        $posts = $archival_management_model->get_collection_posts($data['collection_id']);
                        return $archival_management_model->get_items_to_eliminate($data, $posts);
                    case 'restrictionsDynatreeProperties':
                        return $model->initDynatreePropertiesFilter($data['collection_id'],false); 
                        
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

$controller = new FiltersController();
echo $controller->operation($operation,$data);
