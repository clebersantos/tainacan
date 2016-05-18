<?php
$_GET['is_module_active'] = TRUE;
include_once(dirname(__FILE__) . '/../../../../models/category/category_model.php');
include_once(dirname(__FILE__).'/../../../../controllers/general/general_controller.php');  
 class ContestQuestionController extends Controller{
	 public function operation($operation,$data){
                $model = new CategoryModel;   
		switch ($operation) {
                    //dynatree 
                    case 'add':
                        $dynatree = [];
                        return json_encode($model->generate_user_categories_dynatree($data, $dynatree,false,false));
                   
                        
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

$controller = new ContestQuestionController();
echo $controller->operation($operation,$data);
