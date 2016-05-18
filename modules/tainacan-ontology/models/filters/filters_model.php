<?php

//use CollectionModel;

include_once (dirname(__FILE__) . '/../../../../../../../wp-config.php');
include_once (dirname(__FILE__) . '/../../../../../../../wp-load.php');
include_once (dirname(__FILE__) . '/../../../../../../../wp-includes/wp-db.php');
include_once(dirname(__FILE__) . '/../../../../models/general/general_model.php');
include_once(dirname(__FILE__) . '/../../../../models/object/object_model.php');

class FiltersModel extends Model {
    
    /**
     * @signature initDynatreePropertiesFilter($collection_id)
     * @param int $collection_id O id da colecao que sera gerado o json
     * @return json O conteudo do dynatree
     */
   public function initDynatreePropertiesFilter($collection_id,$hide_checkbox = true) {
        $dynatree = [];
        $roots_parents = [
        get_term_by('name','socialdb_property_data','socialdb_property_type')->term_id,
        get_term_by('name','socialdb_property_object','socialdb_property_type')->term_id,
        get_term_by('name','socialdb_property_term','socialdb_property_type')->term_id ];
        $facets_id = array_filter(array_unique((get_post_meta($collection_id, 'socialdb_collection_facets'))?get_post_meta($collection_id, 'socialdb_collection_facets'):[]));
        $properties = [];
        $this->get_collection_properties($properties,0,$facets_id);
         $properties = array_unique($properties);
        if($properties&&  is_array($properties)){
            foreach ($properties as $property) {
                 // busco o objeto da propriedade
                 $propertyObject = get_term_by('id', $property, 'socialdb_property_type');
                 if(!$propertyObject||!in_array($propertyObject->parent, $roots_parents))
                     continue;
                 //insiro a propriedade da classe no dynatree
                 $children = $this->getChildren($propertyObject->term_id);
                 if (count($children) > 0) {
                    $dynatree[] = array(
                            'title' => ucfirst(Words($propertyObject->name, 30)), 
                            'key' => $propertyObject->term_id,  
                            'expand' => true, 
                            'hideCheckbox' => $hide_checkbox, 
                            'children' => $this->childrenDynatreePropertiesFilter($propertyObject->term_id, 'color4'),
                            'addClass' => 'color4');      
                 }else{
                     $dynatree[] = array(
                            'title' => ucfirst(Words($propertyObject->name, 30)), 
                            'key' => $propertyObject->term_id,  
                            'hideCheckbox' => $hide_checkbox, 
                            'addClass' => 'color4'); 
                 }
            }
        }
        
        return json_encode($dynatree);
    }
    
    /** function getChildrenDynatree() 
    * receive ((int,string) id,(array) dynatree) 
    * Return the children of the facets and insert in the array of the dynatree 
    * Author: Eduardo **/

    public function childrenDynatreePropertiesFilter($facet_id, $classCss = 'color4') {
        $dynatree = [];
        $children = $this->getChildren($facet_id);
        if (count($children) > 0) {
            foreach ($children as $child) {
                $children_of_child = $this->getChildren($child->term_id);
                if (count($children_of_child) > 0 || (!empty($children_of_child) && $children_of_child)) {
                    $dynatree[] =
                            array(
                                'title' => $child->name, 
                                'key' => $child->term_id,
                                'expand' => true, 
                                'children' => $this->childrenDynatreePropertiesFilter($child->term_id, 'color4'), 
                                'addClass' => $classCss);
                } else {
                    $dynatree[] = array(
                        'title' => $child->name, 
                        'key' => $child->term_id, 
                        'addClass' => $classCss);
                }
            }
        }
        return $dynatree;
    }
    /**
     * 
     * @global type $wpdb
     * @param type $property
     * @return boolean
     */
    public function used_by_classes($property) {
        global $wpdb;
        $wp_terms = $wpdb->prefix . "terms";
        $wp_termmeta = $wpdb->prefix . "termmeta";
        $query = "
			SELECT t.* FROM $wp_terms t
			INNER JOIN $wp_termmeta tm ON t.term_id = tm.term_id
			WHERE tm.meta_key LIKE 'socialdb_category_property_id' AND  
                        tm.meta_value LIKE '{$property}'
                        ORDER BY t.name
		";
        $result = $wpdb->get_results($query);
        if (isset($result) && !empty($result) && count($result) > 0) {
            return $result;
        } else {
            return false;
        }                
    }
}
