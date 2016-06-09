<?php

/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * e.g., it puts together the home page when no home.php file exists.
 *
 * Learn more: {@link https://codex.wordpress.org/Template_Hierarchy}
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */
require_once(dirname(__FILE__) . '../../../models/theme_options/theme_options_model.php');
require_once(dirname(__FILE__) . '../../../models/theme_options/populate_model.php');
require_once(dirname(__FILE__) . '../../../models/collection/collection_templates_model.php');
require_once(dirname(__FILE__) . '../../general/general_controller.php');

class ThemeOptionsController extends Controller {

    public function operation($operation, $data) {
        $theme_options_model = new ThemeOptionsModel();
        switch ($operation) {
            case "edit_configuration":
                $data = $theme_options_model->get_theme_options_data();
                if (is_array($data)) {
                    return $this->render(dirname(__FILE__) . '../../../views/theme_options/edit.php', $data);
                } else {
                    return $this->render(dirname(__FILE__) . '../../../views/theme_options/edit.php');
                }
                break;
            case "edit_general_configuration":
                $collectioModelTemplates = new CollectionTemplatesModel;
                $data = $theme_options_model->get_theme_general_options_data();
                $data['templates'] = $collectioModelTemplates->get_collections_templates();
                return $this->render(dirname(__FILE__) . '../../../views/theme_options/edit_configuration.php', $data);
                break;
            case "edit_welcome_email":
                $data['socialdb_welcome_email'] = get_option('socialdb_welcome_email');
                return $this->render(dirname(__FILE__) . '../../../views/theme_options/edit_email.php', $data);
                break;
            case "update_options":
                return $theme_options_model->update($data);
                break;
            case "update_configuration":
                return $theme_options_model->update_configuration($data);
                break;
            case "update_welcome_email":
                return $theme_options_model->update_welcome_email($data);
                break;
            case "edit_licenses":
                $data = $theme_options_model->get_theme_general_options_data();
                $data = $theme_options_model->get_theme_general_options_data();
                return $this->render(dirname(__FILE__) . '../../../views/theme_options/licenses/edit.php', $data);
                break;
            case "listStandartLicenses":
                $arrLicenses = $theme_options_model->get_licenses('standart');
                return json_encode($arrLicenses);
                break;
            case "listCustomLicenses":
                $arrLicenses = $theme_options_model->get_licenses('custom');
                return json_encode($arrLicenses);
                break;
            case "add_repository_license":
                if ($data['add_license_url'] == '' && $data['add_license_description'] == ''):
                    $result['title'] = __('Error', 'tainacan');
                    $result['msg'] = __('Please, fill the form correctly!', 'tainacan');
                    $result['type'] = 'error';
                else:
                    if (!$theme_options_model->verify_equal_license_title($data['add_license_name'])):
                        $result['title'] = __('Info', 'tainacan');
                        $result['msg'] = __('This license is already registered!', 'tainacan');
                        $result['type'] = 'info';
                    else:
                        $result = $theme_options_model->insert_custom_license($data);
                    endif;
                endif;

                return json_encode($result);
                break;
            case "get_license_to_edit":
                $license = $theme_options_model->get_license_to_edit($data['license_id']);
                return json_encode($license);
                break;
            case "edit_repository_license":
                $result = $theme_options_model->edit_repository_license($data);
                return json_encode($result);
                break;
            case "delete_custom_license":
                $result = $theme_options_model->delete_repository_license($data['license_id']);
                return json_encode($result);
                break;
            case "change_pattern_license":
                $result = $theme_options_model->change_pattern_license($data['license_id']);
                return json_encode($result);
                break;
            /*             * ************************* POPULAR COLECOES********************** */
            case "edit_tools":
                return $this->render(dirname(__FILE__) . '../../../views/theme_options/edit_tools.php', $data);
                break;
            case 'populate_collection':
                $populateModel = new PopulateModel($data['items_category']);
                return $populateModel->populate_collection($data);
            case 'getProgress':
                $populateModel = new PopulateModel(0);
                return $populateModel->getProgress($data);
            case 'integrity_test':
                $result = array();
//                $collections = $theme_options_model->get_all_collections();
//                foreach ($collections as $collection) {
//                    $posts = $theme_options_model->get_collection_posts($collection->ID);
//                    foreach ($posts as $post) {
//                        $files = $theme_options_model->list_files_attachment($post->ID);
//                        foreach ($files as $file) {
//                            $md5_atual = ($theme_options_model->is_url_exist($file["guid"]) ? md5_file($file["guid"]) : 'Not Found!');
//                            $result_test = ($file["md5_inicial"] == $md5_atual ? 'OK' : 'NOK');
//                            add_post_meta($file['ID'], 'check_md5_' . time(), $md5_atual);
//                            $info_file['id'] = $file["ID"];
//                            $info_file['title'] = $file["name"];
//                            $info_file['md5_inicial'] = $file["md5_inicial"];
//                            $info_file['md5_atual'] = $md5_atual;
//                            $info_file['result'] = $result_test;
//
//                            $result[] = $info_file;
//                        }
//                    }
//                }


                $files = $theme_options_model->get_all_attachments();
                if (!empty($files)) {
                    foreach ($files as $file) {
                        $file["md5_inicial"] = get_post_meta($file["ID"], 'md5_inicial', true);
                        
                        $md5_atual = ($theme_options_model->is_url_exist($file["guid"]) ? md5_file($file["guid"]) : 'Not Found!');
                        $result_test = ($file["md5_inicial"] == $md5_atual ? 'OK' : 'NOK');
                        add_post_meta($file['ID'], 'check_md5_' . time(), $md5_atual);
                        $info_file['id'] = $file["ID"];
                        $info_file['title'] = $file["post_title"];
                        $info_file['md5_inicial'] = $file["md5_inicial"];
                        $info_file['md5_atual'] = $md5_atual;
                        $info_file['result'] = $result_test;

                        $result[] = $info_file;
                    }
                }
                return json_encode($result);
        }
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

$theme_options_controller = new ThemeOptionsController();
echo $theme_options_controller->operation($operation, $data);
?>