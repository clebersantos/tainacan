<?php
$_GET['is_module_active'] = TRUE;
include_once(dirname(__FILE__) . '/../../../../models/category/category_model.php');
include_once(dirname(__FILE__) . '/../../../../models/property/property_model.php');
include_once(dirname(__FILE__).'/../../../../controllers/general/general_controller.php');  
 class OntologyPropertyController extends Controller{
	 public function operation($operation,$data){
                $model = new PropertyModel;   
		switch ($operation) {
                    case 'edit':
                        $array = $model->get_all_property($data['property_id'], true);
                        $data['data'] = $array;
                        $data['property'] = get_term_by('id', $data['property_id'],'socialdb_property_type');
                        $data['category'] = get_term_by('id', $array['metas']['socialdb_property_created_category'],'socialdb_category_type');   
                        $data['type'] = $model->get_property_type_hierachy($data['property_id']);
                        return $this->render(dirname(__FILE__).'/../../views/property/list.php', $data);
                        
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

$controller = new OntologyPropertyController();
echo $controller->operation($operation,$data);
