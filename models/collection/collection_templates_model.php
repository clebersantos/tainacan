<?php

ini_set('max_input_vars', '10000');
error_reporting(0);
require_once(dirname(__FILE__) . '/collection_model.php');
require_once(dirname(__FILE__) . '/collection_import_model.php');
require_once(dirname(__FILE__) . '/../export/zip_model.php');

class CollectionTemplatesModel extends CollectionModel {

    /**
     * @signature get_collections_templates()
     * @return array Com os dados de cada template localizado dentro da pasta
     * data/templates
     */
    public function get_collections_templates() {
        $data = [];
        $dir = dirname(__FILE__) . "/../../data/templates";
        foreach (new DirectoryIterator($dir) as $fileInfo) {
            if ($fileInfo->isDot())
                continue;
            
            if($fileInfo->getFilename()){
               $xml = simplexml_load_file($dir.'/'.$fileInfo->getFilename().'/package/metadata/administrative_settings.xml'); 
               if(is_file($dir.'/'.$fileInfo->getFilename().'/package/metadata/thumbnail.png')){
                    $thumbnail_id = get_template_directory_uri().'/data/templates'.'/'.$fileInfo->getFilename().'/package/metadata/thumbnail.png';
                }elseif(is_file($dir.'/'.$fileInfo->getFilename().'/package/metadata/thumbnail.jpg')){
                    $thumbnail_id =  get_template_directory_uri().'/data/templates'.'/'.$fileInfo->getFilename().'/package/metadata/thumbnail.jpg';
                }elseif(is_file($dir.'/'.$fileInfo->getFilename().'/package/metadata/thumbnail.gif')){
                    $thumbnail_id =  get_template_directory_uri().'data/templates'.'/'.$fileInfo->getFilename().'/package/metadata/thumbnail.gif';
                }elseif(is_file($dir.'/'.$fileInfo->getFilename().'/package/metadata/thumbnail.jpeg')){
                    $thumbnail_id =  get_template_directory_uri().'/data/templates'.'/'.$fileInfo->getFilename().'/package/metadata/thumbnail.jpeg';
                }else{
                    $thumbnail_id = '';
                }
               $data[] = array (
                   'directory'=>$fileInfo->getFilename(),
                   'title'=>(string)$xml->post_title,
                   'description'=>(string)$xml->post_content,
                   'thumbnail'=> $thumbnail_id   ); 
            }
            
            //$xml = simplexml_load_file($fileInfo->getPath() . '/' . $fileInfo->getFilename());
            //$data = $this->add_hierarchy_importing_collection($xml, 0, $this->get_category_root_id());
            //$categories_id[] = $data['ids'];
        }
        return $data;
    }
    
    /**
     * metodo responsavel em criar o template selecionado pelo usuario
     * @param type $data array vindo da requisicao ajaz
     * @return uma strin json caso o template seja criado corretamente
     */
    public function add_collection_template($data) {
        $collection = get_post($data['collection_id']);
        $dir = dirname(__FILE__) . "/../../data/templates";
        if(!is_dir($dir.'/'.$collection->post_name)){
             mkdir($dir.'/'.$collection->post_name);
             $zipModel = new ZipModel;
             if($zipModel->generate_collection_template($dir.'/'.$collection->post_name, $collection->ID)){
                return json_encode(['result'=>true]);
             }
        }
    }
    
    /**
     * metodo responsavel em remover o template selecionado pelo usuario
     * @param type $data array vindo da requisicao ajaz
     * @return uma strin json caso o template seja criado corretamente
     */
    public function delete_collection_template($data) {
        $dir = dirname(__FILE__) . "/../../data/templates";
        if(is_dir($dir.'/'.$data['collection_id'])){
             $zipModel = new ZipModel;
             if($zipModel->remove_template($dir.'/'.$data['collection_id'])){
                if(self::is_dir_empty($dir)){
                     update_option('disable_empty_collection', 'false');
                }
                return json_encode(['result'=>true]);
             }
        }
    }
    
    public static function is_dir_empty($dir) {
        if (!is_readable($dir)) return NULL; 
        $handle = opendir($dir);
        while (false !== ($entry = readdir($handle))) {
          if ($entry != "." && $entry != "..") {
            return FALSE;
          }
        }
        return TRUE;
    }

}
