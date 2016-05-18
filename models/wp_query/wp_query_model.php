<?php
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once (dirname(__FILE__) . '../../../models/collection/collection_model.php');
include_once (dirname(__FILE__) . '../../../models/license/license_model.php');
include_once (dirname(__FILE__) . '../../../models/property/property_model.php');
include_once (dirname(__FILE__) . '../../../models/category/category_model.php');
include_once (dirname(__FILE__) . '../../../models/event/event_object/event_object_create_model.php');
require_once(dirname(__FILE__) . '../../general/general_model.php');
require_once(dirname(__FILE__) . '../../user/user_model.php');
require_once(dirname(__FILE__) . '../../tag/tag_model.php');

/**
 * The class WPQueryModel
 *
 */
class WPQueryModel extends Model {

    /**
     * function set_post_type()
     * @param int $collection_id
     * @return void 
     * Metodo reponsavel em determinar se deve listar as colecoes ou objetos
     * Autor: Eduardo Humberto 
     */
    public function set_post_type($collection_id,$data) {
        if ($collection_id == get_option('collection_root_id')) {
            return 'socialdb_collection';
        } else {
            return 'socialdb_object';
        }
    }

    /**
     * function dynatree_filter()
     * @param array Array com os dados da colecao
     * @return void 
     * Metodo reponsavel em separar os tipos selecionados no dynatree e colo
     * Autor: Eduardo Humberto 
     */
    public function dynatree_filter($data) {
        $category_model = new CategoryModel();
        $used_parents = array();
        $result = array();
        $recover_data = unserialize(stripslashes($data['wp_query_args']));
        $terms = explode(',', $data['value']);
         unset($recover_data['pagid']);
        unset($recover_data['properties_object_tree']);
        unset($recover_data['properties_data_tree']);
        unset($recover_data['license_tree']);
        unset($recover_data['type_tree']);
        unset($recover_data['format_tree']);
        unset($recover_data['source_tree']);
        if (!empty($terms)) {
            if (is_array($terms)) {
                foreach ($terms as $classification) {
                    if (strpos($classification, '_') === false && $classification) {
                        $key = $category_model->get_category_facet_parent(trim($classification), $data['collection_id']);
                        $used_parents[] = $key;
                        $result[$key][] = $classification;
                    } elseif (strpos($classification, '_') !== false && strpos($classification, 'tag') !== false) {
                        $result['tags'][] = explode('_', $classification)[0];
                    } elseif (strpos($classification, '_') !== false 
                            && strpos($classification, 'datatext') === false&&strpos($classification, 'license')=== false
                            &&strpos($classification, 'type')=== false&&strpos($classification, 'format')=== false&&strpos($classification, 'source')=== false) {
                        $property = explode('_', $classification)[1];
                        $value = explode('_', $classification)[0];
                        if (!isset($recover_data['properties_object_tree'][$property]) || !in_array($value, $recover_data['properties_object_tree'][$property])) {
                            $recover_data['properties_object_tree'][$property][] = $value;
                        }
                    } elseif (strpos($classification, 'datatext') !== false) {
                        $property =  trim(explode('_', $classification)[1]);
                        $value = trim(explode('_', $classification)[0]);
                        if (!isset($recover_data['properties_data_tree'][$property]) || !in_array($value, $recover_data['properties_data_tree'][$property])) {
                            $recover_data['properties_data_tree'][$property][] = $value;
                        }
                    } elseif (strpos($classification, 'license') !== false) {
                        $value = trim(explode('_', $classification)[0]);
                        if (!isset($recover_data['license_tree']) || !in_array($value, $recover_data['license_tree'])) {
                            $recover_data['license_tree'][] = $value;
                        }
                    }elseif (strpos($classification, 'type') !== false) {
                        $value = trim(explode('_', $classification)[0]);
                        if (!isset($recover_data['type_tree']) || !in_array($value, $recover_data['type_tree'])) {
                            $recover_data['type_tree'][] = $value;
                        }
                    }elseif (strpos($classification, 'format') !== false) {
                        $value = trim(explode('_', $classification)[0]);
                        if (!isset($recover_data['format_tree']) || !in_array($value, $recover_data['format_tree'])) {
                            $recover_data['format_tree'][] = $value;
                        }
                    }elseif (strpos($classification, 'source') !== false) {
                        $value = trim(explode('_', $classification)[0]);
                        if (!isset($recover_data['source_tree']) || !in_array($value, $recover_data['source_tree'])) {
                            $recover_data['source_tree'][$value] = get_post_meta($value, 'socialdb_object_dc_source', true);
                        }
                    }
                }
            }
        }
        //retiro as tags se estiver vazio
        if(!isset($result['tags'])&&isset($recover_data['tags'])){
            unset($recover_data['tags']);
        }
        
        //adciono no array de informacoes
        if($result&&!empty($result)){
            foreach ($result as $facet => $terms) {
                 if($facet=='tags'){
                     $recover_data['tags'] = $terms;
                 }else{
                     $recover_data['facets'][$facet] = $terms;
                 }
            }
        }
        //retiro a faceta caso ela estava marcada e depois nao possuir mas nenhum termo marcado
        $facets_id = array_filter(array_unique(get_post_meta($data['collection_id'], 'socialdb_collection_facets')));
        foreach ($facets_id as $facet_id) {
            $widget = get_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $facet_id . '_widget', true);
            if($widget=='tree'){
                if(isset($recover_data['facets'][$facet_id])&&!in_array($facet_id, $used_parents)){
                    unset($recover_data['facets'][$facet_id]);
                }
            }
        }
        return $recover_data;
    }
    /**
     * function cloud_filter()
     * @param array Array com os dados da colecao
     * @return void 
     * Metodo reponsavel em determinar se deve listar as colecoes ou objetos
     * Autor: Eduardo Humberto 
     */
    public function cloud_filter($data) {
        $recover_data = unserialize(stripslashes($data['wp_query_args']));
         unset($recover_data['pagid']);
        $new_value = $data['value'];
        if ($data['facet_id']=='tag') {
            if(!is_array($recover_data['tags'])||(is_array($recover_data['tags'])&&!in_array($new_value, $recover_data['tags']))){
                 $recover_data['tags'][] = $new_value;
            }
        } else {
            if(!is_array($recover_data['properties_data_tree'][$data['facet_id']])||!in_array($new_value, $recover_data['properties_data_tree'][$data['facet_id']])){
                 $recover_data['properties_data_tree'][$data['facet_id']][] = $new_value;
            }
        }
        return $recover_data;
    }
    /**
     * function link_metadata_filter()
     * @param array Array com os dados da colecao
     * @return void 
     * Metodo reponsavel em filtrar itens por uma propriedade de dados
     * Autor: Eduardo Humberto 
     */
    public function link_metadata_filter($data) {
        $recover_data = unserialize(stripslashes($data['wp_query_args']));
        unset($recover_data['pagid']);
        $new_value = $data['value'];
        if(!is_array($recover_data['properties_data_link'][$data['facet_id']])||!in_array($new_value, $recover_data['properties_data_link'][$data['facet_id']])){
             $recover_data['properties_data_link'][$data['facet_id']][] = $new_value;
        }
        return $recover_data;
    }
    
    /**
     * function checkbox_filter()
     * @param array Array com os dados da colecao
     * @return void 
     * Metodo reponsavel em determinar se deve listar as colecoes ou objetos
     * Autor: Eduardo Humberto 
     */
    public function radio_filter($data) {
        $recover_data = unserialize(stripslashes($data['wp_query_args']));
         unset($recover_data['pagid']);
        $new_radio = $data['value'];
        if (!empty($new_radio)) {
            $recover_data['facets'][$data['facet_id']] = array($new_radio);
        } else {
            unset($recover_data['facets'][$data['facet_id']]);
        }
        return $recover_data;
    }
    
     /**
     * function checkbox_filter()
     * @param array Array com os dados da colecao
     * @return void 
     * Metodo reponsavel em determinar se deve listar as colecoes ou objetos
     * Autor: Eduardo Humberto 
     */
    public function menu_filter($data) {
        $recover_data = unserialize(stripslashes($data['wp_query_args']));
         unset($recover_data['pagid']);
        $new_radio = $data['value'];
        if (!empty($new_radio)) {
            $recover_data['facets'][$data['facet_id']] = array($new_radio);
        } else {
            unset($recover_data['facets'][$data['facet_id']]);
        }
        return $recover_data;
    }

    /**
     * function select_filter($data)
     * @param array Array com os dados da colecao
     * @return void 
     * Metodo reponsavel em determinar se deve listar as colecoes ou objetos
     * Autor: Eduardo Humberto 
     */
    public function select_filter($data) {
        $recover_data = unserialize(stripslashes($data['wp_query_args']));
         unset($recover_data['pagid']);
        $new_radio = $data['value'];
        if (!empty($new_radio)) {
            $recover_data['facets'][$data['facet_id']] = array($new_radio);
        } else {
            unset($recover_data['facets'][$data['facet_id']]);
        }
        return $recover_data;
    }
    /**
     * function checkbox_filter()
     * @param array Array com os dados da colecao
     * @return void 
     * Metodo reponsavel em determinar se deve listar as colecoes ou objetos
     * Autor: Eduardo Humberto 
     */
    public function ordenation_filter($data) {
        $recover_data = unserialize(stripslashes($data['wp_query_args']));
         unset($recover_data['pagid']);
        $ordenation = $data['value'];
        if (!empty($ordenation)) {
            $recover_data['ordenation_id'] = $ordenation;
        } 
        return $recover_data;
    }
    
     /**
     * function checkbox_filter()
     * @param array Array com os dados da colecao
     * @return void 
     * Metodo reponsavel em determinar se deve listar as colecoes ou objetos
     * Autor: Eduardo Humberto 
     */
    public function orderby_filter($data) {
        $recover_data = unserialize(stripslashes($data['wp_query_args']));
         unset($recover_data['pagid']);
        $ordenation = $data['value'];
        if (!empty($ordenation)) {
            $recover_data['order_by'] = $ordenation;
        } 
        return $recover_data;
    }
    
     /**
     * function checkbox_filter()
     * @param array Array com os dados da colecao
     * @return void 
     * Metodo reponsavel em determinar se deve listar as colecoes ou objetos
     * Autor: Eduardo Humberto 
     */
    public function keyword_filter($data) {
        $recover_data = unserialize(stripslashes($data['wp_query_args']));
         unset($recover_data['pagid']);
        $ordenation = $data['value'];
        if (!empty($ordenation)) {
            $recover_data['keyword'] = $ordenation;
        } 
        return $recover_data;
    }
    /**
     * function checkbox_filter()
     * @param array Array com os dados da colecao
     * @return void 
     * Metodo reponsavel em determinar se deve listar as colecoes ou objetos
     * Autor: Eduardo Humberto 
     */
    public function page_filter($data) {
        $recover_data = unserialize(stripslashes($data['wp_query_args']));
        $ordenation = $data['value'];
        if (!empty($ordenation)) {
            $recover_data['pagid'] = $ordenation;
        } 
        return $recover_data;
    }
    /**
     * function checkbox_filter()
     * @param array Array com os dados da colecao
     * @return void 
     * Metodo reponsavel em determinar se deve listar as colecoes ou objetos
     * Autor: Eduardo Humberto 
     */
    public function checkbox_filter($data) {
        $recover_data = unserialize(stripslashes($data['wp_query_args']));
         unset($recover_data['pagid']);
        if (isset($recover_data['facets'])&&$data['value'] && !empty($data['value'])) {
            if (isset($recover_data['facets'][$data['facet_id']])) {
                unset($recover_data['facets'][$data['facet_id']]);
                $new_checked_categories = explode(',', $data['value']);
                if (is_array($new_checked_categories) && !empty($new_checked_categories)) {
                    $recover_data['facets'][$data['facet_id']] = $new_checked_categories;
                }
            } else {
                $new_checked_categories = explode(',', $data['value']);
                if (is_array($new_checked_categories) && !empty($new_checked_categories)) {
                    $recover_data['facets'][$data['facet_id']] = $new_checked_categories;
                }
            }
        } elseif ($data['value'] && !empty($data['value'])) {
            $new_checked_categories = explode(',', $data['value']);
            if (is_array($new_checked_categories) && !empty($new_checked_categories)) {
                $recover_data['facets'][$data['facet_id']] = $new_checked_categories;
            }
        }elseif(!$data['value'] || empty($data['value'])){
            unset($recover_data['facets'][$data['facet_id']]);
        }
        return $recover_data;
    }

    /**
     * function checkbox_filter()
     * @param array Array com os dados da colecao
     * @return void 
     * Metodo reponsavel em determinar se deve listar as colecoes ou objetos
     * Autor: Eduardo Humberto 
     */
    public function multipleselect_filter($data) {
        $recover_data = unserialize(stripslashes($data['wp_query_args']));
         unset($recover_data['pagid']);
        $property = get_term_by('id', $data['facet_id'], 'socialdb_property_type');
        if (trim($data['value']) == '') {
            if ($property->term_id)
                unset($recover_data['properties_multipleselect'][$property->term_id]);
            else
                unset( $recover_data['facets'][$data['facet_id']]);
        }else {
            if ($property) {
                $new_selected_properties = explode(',', $data['value']);
                if (is_array($new_selected_properties) && !empty($new_selected_properties)) {
                    $recover_data['properties_multipleselect'][$property->term_id] = $new_selected_properties;
                }
                $recover_data['properties_multipleselect'][$property->term_id] = explode(',', $data['value']);
            } elseif ($data['value'] && !empty($data['value'])) {
                $new_selected_categories = explode(',', $data['value']);
                if (is_array($new_selected_categories) && !empty($new_selected_categories)) {
                    $recover_data['facets'][$data['facet_id']] = $new_selected_categories;
                }
            }
        }
        return $recover_data;
    }

    /**
     * function checkbox_filter()
     * @param array Array com os dados da colecao
     * @return void 
     * Metodo reponsavel em determinar se deve listar as colecoes ou objetos
     * Autor: Eduardo Humberto 
     */
    public function range_filter($data) {
        $recover_data = unserialize(stripslashes($data['wp_query_args']));
         unset($recover_data['pagid']);
        $property = get_term_by('id', $data['facet_id'], 'socialdb_property_type');
        if ($property) {
            if ($data['facet_type'] == 'date') {
               $new_range_filters = explode(',',$data['value']);
                if (!empty($new_range_filters)) {
                    $date_sql[] = explode('/', $new_range_filters[0])[2].'-' .explode('/', $new_range_filters[0])[1].'-' .explode('/', $new_range_filters[0])[0];
                    $date_sql[] = explode('/', $new_range_filters[1])[2].'-' .explode('/', $new_range_filters[1])[1].'-' .explode('/', $new_range_filters[1])[0];
                   $recover_data['properties_data_fromto_date'][$property->term_id] = $date_sql;
                }
            } else {
                $new_range_filters = $data['value'];
                if (!empty($new_range_filters)) {
                    $recover_data['properties_data_range_numeric'][$property->term_id] = $new_range_filters;
                }
            }
        }
        return $recover_data;
    }

    /**
     * function checkbox_filter()
     * @param array Array com os dados da colecao
     * @return void 
     * Metodo reponsavel em determinar se deve listar as colecoes ou objetos
     * Autor: Eduardo Humberto 
     */
    public function fromto_filter($data) {
        $recover_data = unserialize(stripslashes($data['wp_query_args']));
         unset($recover_data['pagid']);
        $property = get_term_by('id', $data['facet_id'], 'socialdb_property_type');
        if ($property) {
            if ($data['facet_type'] == 'date') {
                $new_range_filters = explode(',',$data['value']);
                if (!empty($new_range_filters)) {
                    $date_sql[] = explode('/', $new_range_filters[0])[2].'-' .explode('/', $new_range_filters[0])[1].'-' .explode('/', $new_range_filters[0])[0];
                    $date_sql[] = explode('/', $new_range_filters[1])[2].'-' .explode('/', $new_range_filters[1])[1].'-' .explode('/', $new_range_filters[1])[0];
                   $recover_data['properties_data_fromto_date'][$property->term_id] = $date_sql;
                }
            } else {
                $new_range_filters = $data['value'];
                if (!empty($new_range_filters)) {
                    $recover_data['properties_data_fromto_numeric'][$property->term_id] = $new_range_filters;
                }
            }
        }
        return $recover_data;
    }
    
     /**
     * function checkbox_filter()
     * @param array Array com os dados da colecao
     * @return void 
     * Metodo reponsavel em determinar se deve listar as colecoes ou objetos
     * Autor: Eduardo Humberto 
     */
    public function filter($data) {
        $recover_data = unserialize(stripslashes($data['wp_query_args']));
        return $recover_data;
    }
    
      /**
     * function checkbox_filter()
     * @param array Array com os dados da colecao
     * @return void 
     * Metodo reponsavel em determinar se deve listar as colecoes ou objetos
     * Autor: Eduardo Humberto 
     */
    public function clean($data) {
        $recover_data = unserialize(stripslashes($data['wp_query_args']));
        unset($recover_data['properties_object_tree']);
        unset($recover_data['properties_data_tree']);
        unset($recover_data['license_tree']);
        unset($recover_data['type_tree']);
        unset($recover_data['format_tree']);
        unset($recover_data['source_tree']);
        unset($recover_data['advanced_search']);
        unset($recover_data['facets']);
        unset($recover_data['pagid']);
        unset($recover_data['properties_data_fromto_date']);
        unset($recover_data['properties_data_fromto_numeric']);
        unset($recover_data['properties_data_range_numeric']);
        unset($recover_data['properties_data_range_date']);
        unset($recover_data['properties_data_range_numeric']);
        unset($recover_data['properties_multipleselect']);
        unset($recover_data['keyword']);
        unset($recover_data['order_by']);
        unset($recover_data['order']);
        unset($recover_data['orderby']);
        unset($recover_data['ordenation_id']);
        unset($recover_data['tags']);
        return $recover_data;
    }

    /**
     * function do_filter()
     * @param array Array com os dados do ultimo filtro
     * @return void 
     * Metodo reponsavel em determinar se deve listar as colecoes ou objetos
     * Autor: Eduardo Humberto 
     */
    public function do_filter($recover_data) {
        //se estiver buscando colecoes
        if($recover_data['collection_id']==  get_option('collection_root_id')){
                $page = $this->set_page($recover_data);
                $orderby = $this->set_order_by($recover_data);
                 $order = $this->set_type_order($recover_data);
                $args = array(
                    'post_type' => 'socialdb_collection',
                    'paged' => (int)$page,
                 // 'posts_per_page' => 10,
                    'orderby' => $orderby,
                    'order' => $order,
                    //'no_found_rows' => true, // counts posts, remove if pagination required
                    'update_post_term_cache' => false, // grabs terms, remove if terms required (category, tag...)
                    'update_post_meta_cache' => false, // grabs post meta, remove if post meta required
                ); 
                if (isset($recover_data['keyword']) && $recover_data['keyword'] != '') {
                   $args['s'] = $recover_data['keyword'];
                   $args['orderby'] = 'title';
                   $args['order'] = 'ASC';
                }
             return $args;
        }else{
            $page = $this->set_page($recover_data);
            $orderby = $this->set_order_by($recover_data);
            $array_defaults = ['socialdb_object_from','socialdb_object_dc_type','socialdb_object_dc_source','title','socialdb_license_id','comment_count'];
            if ($orderby == 'meta_value_num') {
                $meta_key = 'socialdb_property_' . trim($recover_data['ordenation_id']);
            }elseif(in_array($orderby, $array_defaults)){
                 $meta_key = $orderby;
            }else{
                $meta_key = '';
            }
            // inserindo as categorias e as tags na query
            $tax_query = $this->get_tax_query($recover_data);
            //a forma de ordenacao
            $order = $this->set_type_order($recover_data);
            // se vai listar as colecoes ou objetos
            if(isset($recover_data['advanced_search'])){
                $post_type = 'socialdb_object';
            }else{
                $post_type = $this->set_post_type($recover_data['collection_id'],$recover_data);
            }
            //all_data_inside
            $args = array(
                'post_type' => $post_type,
                'paged' => (int)$page,
               // 'posts_per_page' => 10,
                'tax_query' => $tax_query,
                'orderby' => $orderby,
                'order' => $order,
                //'no_found_rows' => true, // counts posts, remove if pagination required
                'update_post_term_cache' => false, // grabs terms, remove if terms required (category, tag...)
                'update_post_meta_cache' => false, // grabs post meta, remove if post meta required
            );
            $meta_query = $this->get_meta_query($recover_data);
            if ($meta_query) {
                $args['meta_query'] = $meta_query;
            }
            if (isset($meta_key)&&!in_array($meta_key, ['title','comment_count','date'])) {
                $args['meta_key'] = $meta_key;
            }
            if (isset($recover_data['keyword']) && $recover_data['keyword'] != '') {
                //$args['s'] = $recover_data['keyword'];
            }
            return $args;
        }
    }

    /**
     * @signature set_page($data)
     * @param array $data O array de dados vindo do formulario
     * @return int com a pagina a ser visualizada
     * Metodo reponsavel em  retornar o fromato que sera ordenado (crescente ou decrescente)
     * @author Eduardo Humberto 
     */
    public function set_page($data) {
        if (isset($data['pagid']) && $data['pagid'] != '' && is_numeric($data['pagid'])) {
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
        $array_defaults = ['socialdb_object_from','socialdb_object_dc_type','socialdb_object_dc_source','title','socialdb_license_id'];
        if (!isset($data['order_by'])||$data['order_by'] == '') {
            $order = get_post_meta($data['collection_id'], 'socialdb_collection_ordenation_form', true);
            if ($order !== '' && $order) {
                return strtoupper($order);
            }elseif(in_array($data['ordenation_id'], $array_defaults)){
                 return 'ASC';
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
        $array_defaults = ['socialdb_object_from','socialdb_object_dc_type','socialdb_object_dc_source','title','socialdb_license_id'];
        if (isset($data['ordenation_id'])) {
            $property = get_term_by('id', $data['ordenation_id'], 'socialdb_property_type');
        } else {
            $property = false;
        }
        if((isset($data['ordenation_id'])&&in_array($data['ordenation_id'], $array_defaults))||
               (isset($data['orderby'])&& in_array($data['orderby'], $array_defaults))){
            $defaults = true;
        }
        if ($property && $property->slug != 'socialdb_ordenation_recent') {
            return 'meta_value_num';
        }elseif($defaults){
            if(isset($data['ordenation_id'])){
                return trim($data['ordenation_id']);
            }else{
                return trim($data['orderby']);
            }
        } 
        else {
             $default = get_post_meta($data['collection_id'], 'socialdb_collection_default_ordering',true);
            if((empty($default)||$default=='')||($property && $property->slug == 'socialdb_ordenation_recent')){
                return 'date'; 
            }else{
                return trim($default);
            }
        }
    }
     /**
     * function remove_filter()
     * @param array Array com os dados da colecao
     * @return void 
     * Metodo reponsavel em determinar se deve listar as colecoes ou objetos
     * Autor: Eduardo Humberto 
     */
    public function remove_filter($data) {
        $recover_data = unserialize(stripslashes($data['wp_query_args']));
        $value_to_remove = $data['value'];
        $type = $data['type'];
        $index = $data['index_array'];
        if($type=='_'&&$value_to_remove=='_'){
            unset($recover_data[$index]);
        }elseif ($index=='tags'&&isset($recover_data['tags'])) {
            foreach ($recover_data['tags'] as $key => $value) {
                if($value==$value_to_remove){
                    unset($recover_data['tags'][$key]);
                }
            }
            if(empty($recover_data['tags'])){
                unset($recover_data['tags']);
            }
        }
        elseif ($type=='_') {
            unset($recover_data[$index][$value_to_remove]);
        }elseif ($type=='||') {
            $index_value = array_search ( $value_to_remove ,$recover_data[$index]);
            unset($recover_data[$index][$index_value]);
            if(empty($recover_data[$index])){
                unset($recover_data[$index]);
            }
        }elseif(is_array($recover_data[$index][$type])){
            foreach ($recover_data[$index][$type] as $key => $value) {
                $array =  explode(',', $value_to_remove);
                if($value==$value_to_remove){
                    unset($recover_data[$index][$type][$key]);
                }elseif(is_array($array)&&in_array($value, $array)){
                     unset($recover_data[$index][$type][$key]);
                }
            }
            if(empty($recover_data[$index][$type])){
                unset($recover_data[$index][$type]);
            }
        }else{
             unset($recover_data[$index][$type]);
        }
        return $recover_data;
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
    public function get_tax_query($recover_data) {
        // coloco categorias no array tax query
        $tax_query = array('relation' => 'AND'); // devem ter a relacao AND para filtrar dentro da colecao
        if($recover_data['category_root_id']!='all_items'&&isset($recover_data['collection_id'])&&$recover_data['collection_id']!=get_option('collection_root_id')){
            $tax_query[] = array(
                'taxonomy' => 'socialdb_category_type',
                'field' => 'id',
                'terms' => $recover_data['category_root_id'],
                'operator' => 'IN'
            );
        }
        if (isset($recover_data['facets'])) {
            foreach ($recover_data['facets'] as $category_by_facet) {
                $tax_query[] = array(
                    'taxonomy' => 'socialdb_category_type',
                    'field' => 'id',
                    'terms' => $category_by_facet,
                    'operator' => 'IN'
                );
            }
        }
        if (isset($recover_data['tags'])) {
            $tax_query[] = array(
                'taxonomy' => 'socialdb_tag_type',
                'field' => 'id',
                'terms' => $recover_data['tags'],
                'operator' => 'IN'
            );
        }
        if(isset($recover_data['advanced_search']['tags'])){
            $tax_query[] = array(
                'taxonomy' => 'socialdb_tag_type',
                'field' => 'name',
                'terms' => $recover_data['advanced_search']['tags'],
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
    public function get_meta_query($recover_data) {
        $meta_query = array();
        $meta_query = array('relation' => 'AND');
        //se estiver buscando uma string em uma colecao de itens
        if (isset($recover_data['keyword']) && $recover_data['keyword'] != '') {
            $length = strlen($recover_data['keyword']);
            $recover_data['keyword'] = stripslashes ($recover_data['keyword'] );
            if ((strpos($recover_data['keyword'], '"') === 0)) {
                $array = explode('"', $recover_data['keyword']);
                foreach ($array as $str) {
                    if (trim($str) !== '') {
                        $meta_query[] = array(
                            'key' => 'socialdb_object_commom_values',
                            'value' => trim($str),
                            'compare' => 'LIKE'
                        );
                    }
                }
            } elseif ((strpos($recover_data['keyword'], "'") === 0)) {
                $array = explode("'", $recover_data['keyword']);
                foreach ($array as $str) {
                    if (trim($str) !== '') {
                        $meta_query[] = array(
                            'key' => 'socialdb_object_commom_values',
                            'value' => trim($str),
                            'compare' => 'LIKE'
                        );
                    }
                }
            } else {
                $array = explode(' ', $recover_data['keyword']);
                foreach ($array as $str) {
                    if (trim($str) !== '') {
                        $meta_query[] = array(
                            'key' => 'socialdb_object_commom_values',
                            'value' => trim($str),
                            'compare' => 'LIKE'
                        );
                    }
                }
            }
        }

        if (isset($recover_data['properties_tree'])) {
            foreach ($recover_data['properties_tree'] as $property_id => $value_id) {
                $meta_query[] = array(
                    'key' => 'socialdb_property_' . $property_id,
                    'value' => $value_id,
                    'compare' => 'IN'
                );
            }
        }
         // busca propriedade de dados do dynatree
        if (isset($recover_data['properties_data_tree'])) {
            //$meta_query = array('relation' => 'AND');
            foreach ($recover_data['properties_data_tree'] as $property_id => $value_id) {
                $values_array = [];
                if(is_array($values_array)){
                    foreach ($value_id as $value) {
                        $values_array[] = $this->get_meta_by_id($value);
                    }
                }
                $meta_query[] = array(
                    'key' => 'socialdb_property_' . $property_id,
                    'value' => $values_array,
                    'compare' => 'IN'
                );
            }
        }
        // busca propriedade de dados diretamente pelo seu valor
        if (isset($recover_data['properties_data_link'])) {
            //$meta_query = array('relation' => 'AND');
            foreach ($recover_data['properties_data_link'] as $property_id => $value_id) {
                $meta_query[] = array(
                    'key' => 'socialdb_property_' . $property_id,
                    'value' => $value_id,
                    'compare' => 'IN'
                );
            }
        }
        // busca licencas
        if (isset($recover_data['license_tree'])) {
           // $meta_query = array('relation' => 'AND');
            $meta_query[] = array(
                'key' => 'socialdb_license_id',
                'value' => $recover_data['license_tree'],
                'compare' => 'IN'
            );
            
        }
        // busca pelo o tipo do item
        if (isset($recover_data['type_tree'])) {
            $has_other = false;
            $to_include = [];
            $to_exclude = ['image','video','pdf','audio','text'];
           // $meta_query = array('relation' => 'AND');
            foreach ($recover_data['type_tree'] as $value_id) {
                if(trim($value_id)!=='other'){
                    unset($to_exclude[array_search(trim($value_id), $to_exclude)]);
                    $to_include[] = trim($value_id);
                }else{
                    $has_other = true; 
                }
            }
            if($has_other){
                $meta_query[] = array(
                    'key' => 'socialdb_object_dc_type',
                    'value' =>$to_exclude,
                    'compare' => 'NOT IN');
            }else{
                $meta_query[] = array(
                    'key' => 'socialdb_object_dc_type',
                    'value' =>$to_include,
                    'compare' => 'IN');
            }
            
        }
        // busca pelo o formato do item
        if (isset($recover_data['format_tree'])) {
            //$meta_query = array('relation' => 'AND');
            foreach ($recover_data['format_tree'] as $value_id) {
                $meta_query[] = array(
                    'key' => 'socialdb_object_from',
                    'value' => trim($value_id)
                    //'compare' => 'IN'
                );
            }
        }
        // busca pelo a fonte do item
        if (isset($recover_data['source_tree'])) {
            //$meta_query = array('relation' => 'AND');
            //foreach ($recover_data['source_tree'] as $value_id) {
                $meta_query[] = array(
                    'key' => 'socialdb_object_dc_source',
                    'value' => $recover_data['source_tree'],
                    'compare' => 'IN'
                );
            //}
        }
        // busca propriedade de objeto do dynatree
        if (isset($recover_data['properties_object_tree'])) {
            //$meta_query = array('relation' => 'AND');
            foreach ($recover_data['properties_object_tree'] as $property_id => $value_id) {
                $meta_query[] = array(
                    'key' => 'socialdb_property_' . $property_id,
                    'value' => (is_array($value_id)? array_map("trim", $value_id):trim($value_id)),
                    'compare' => 'IN'
                );
            }
        }
        if (isset($recover_data['properties_multipleselect'])) {
            //$meta_query = array('relation' => 'AND');
            foreach ($recover_data['properties_multipleselect'] as $property_id => $value_id) {
                $meta_query[] = array(
                    'key' => 'socialdb_property_' . $property_id,
                    'value' => (is_array($value_id)? array_map("trim", $value_id):trim($value_id)),
                    'compare' => 'IN'
                );
            }
        }
        if (isset($recover_data['properties_data_fromto_numeric'])) {
            //$meta_query = array('relation' => 'AND');
            foreach ($recover_data['properties_data_fromto_numeric'] as $property_id => $value) {
                $array =  explode(',', $value);
                $meta_query[] = array(
                    'key' => 'socialdb_property_' . $property_id,
                     'value' =>  $array[0],
                    'type' => 'NUMERIC',
                    'compare' => '>='
                );
                 $meta_query[] = array(
                    'key' => 'socialdb_property_' . $property_id,
                     'value' =>  $array[1],
                    'type' => 'NUMERIC',
                    'compare' => '<='
                );
            }
        }
        if (isset($recover_data['properties_data_fromto_date'])) {
            //$meta_query = array('relation' => 'AND');
            foreach ($recover_data['properties_data_fromto_date'] as $property_id => $value) {
                $meta_query[] = array(
                    'key' => 'socialdb_property_' . $property_id,
                    'value' => $value,
                    'type' => 'date',
                    'compare' => 'BETWEEN'
                );
            }
        }
        if (isset($recover_data['properties_data_range_numeric'])) {
            //$meta_query = array('relation' => 'AND');
            foreach ($recover_data['properties_data_range_numeric'] as $property_id => $value) {
                $meta_query[] = array(
                    'key' => 'socialdb_property_' . $property_id,
                    'value' =>  array_map('floatval', explode(',', $value)),
                    'type' => 'DECIMAL',
                    'compare' => 'BETWEEN'
                );
//                $array =  explode(',', $value);
//                $meta_query[] = array(
//                    'key' => 'socialdb_property_' . $property_id,
//                     'value' =>  floatval (trim($array[0])),
//                    'type' => 'NUMERIC',
//                    'compare' => '>='
//                );
//                 $meta_query[] = array(
//                    'key' => 'socialdb_property_' . $property_id,
//                     'value' =>  floatval (trim($array[1])),
//                    'type' => 'NUMERIC',
//                    'compare' => '<='
//                );
            }
           // var_dump( $meta_query);exit();
        }
        if (isset($recover_data['properties_data_range_date'])) {
            //$meta_query = array('relation' => 'AND');
            foreach ($recover_data['properties_data_range_date'] as $property_id => $value) {
                $meta_query[] = array(
                    'key' => 'socialdb_property_' . $property_id,
                    'value' => $value,
                    'type' => 'date',
                    'compare' => 'BETWEEN'
                );
            }
        }
        //advanced search
        $meta_query = $this->set_arguments_advanced_search($meta_query,$recover_data);
        return $meta_query;
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
                $result.= $term->name . ' (' . __('Data property','tainacan') . ')';
            } else {
                $result.= $term->name . ' (' . __('Ranking','tainacan') . ')';
            }
        } else {
            $result.= __('Recents','tainacan');
        }

        if ($order == 'desc') {
            $result = '<span class="glyphicon glyphicon-sort-by-attributes-alt"></span>&nbsp;' . $result;
        } else {
            $result = '<span class="glyphicon glyphicon-sort-by-attributes"></span>&nbsp;' . $result;
        }
        return $result;
    }
    ##################### ADVANCED SEARCH METHODS ##############################
    /**
     * function select_filter($data)
     * @param array Array com os dados da colecao
     * @return void 
     * Metodo reponsavel em determinar se deve listar as colecoes ou objetos
     * Autor: Eduardo Humberto 
     */
    public function advanced_searched_filter($data) {
        $recover_data = $this->clean($data);
        if(isset($data['advanced_search_collection'])
                &&get_option('collection_root_id')==$data['advanced_search_collection']){
            $recover_data['collection_id'] = 'all_items';
            $recover_data['category_root_id']='all_items';
        }else{
            $recover_data['collection_id'] = $data['collection_id']; 
            $recover_data['category_root_id'] = $this->get_category_root_of($recover_data['collection_id']);
        }
        //$recover_data['collection_id'] = $data['collection_id'];
       $recover_data['advanced_search'] = []; // apenas mostro que deve ser filtrado objetos
        $ordenation = $data['value'];
        if (!empty($ordenation)) {
            $recover_data['pagid'] = $ordenation;
        } 
        //verificando a pesquisa no titulo e descricao
        if($data['advanced_search_title']){
            $recover_data['keyword'] = '"'.$data['advanced_search_title'].'"';
        }
        //tags
        if($data['advanced_search_tags']){
            $recover_data['advanced_search']['tags'] = explode(',', $data['advanced_search_tags']);
        }
        //propriedades
        $recover_data = $this->set_properties_filter_advanced_search($recover_data, $data);
        $recover_data = $this->set_terms_filter_advanced_search($recover_data, $data);
        return $recover_data;
    }
    /**
     * function select_filter($data)
     * @param array Array com os dados da colecao
     * @return void 
     * Metodo reponsavel em determinar se deve listar as colecoes ou objetos
     * Autor: Eduardo Humberto 
     */
    public function set_terms_filter_advanced_search($recover_data,$data){
        if ($data['properties_id'] !== '') {
            $properties_id = explode(',', $data['properties_id']);
            foreach ($properties_id as $property_id) {
                if ($data["socialdb_propertyterm_$property_id"]&&!is_array($data["socialdb_propertyterm_$property_id"]) && $data["socialdb_propertyterm_$property_id"] !== '') {
                    $recover_data['facets'][$property_id] = $data["socialdb_propertyterm_$property_id"];
                } elseif ($data["socialdb_propertyterm_$property_id"]&&is_array($data["socialdb_propertyterm_$property_id"]) && !empty(is_array($data["socialdb_propertyterm_$property_id"]))) {
                    foreach ($data["socialdb_propertyterm_$property_id"] as $value) {
                      $recover_data['facets'][$property_id][] = $value;
                    }
                } 
            }
        }
        return $recover_data;
    }
     /**
     * function select_filter($data)
     * @param array Array com os dados da colecao
     * @return void 
     * Metodo reponsavel em determinar se deve listar as colecoes ou objetos
     * Autor: Eduardo Humberto 
     */
    public function set_properties_filter_advanced_search($recover_data,$data){
        $property_model = new PropertyModel;
        if ($data['properties_id'] !== '') {
            $properties_id = explode(',', $data['properties_id']);
            foreach ($properties_id as $property_id) {
                $dados = json_decode($property_model->edit_property(array('property_id'=>$property_id)));
                if($dados->type=='text'){//se for propriedade dedadso do tipo texto
                    if(isset($data["socialdb_property_$property_id"])&&!empty(trim($data["socialdb_property_$property_id"]))){ // se nao estivervazio
                        if($data["socialdb_property_{$property_id}_operation"]=='1'){ // se a opercao for totalmente igual
                             $array_data = ['value'=>$data["socialdb_property_$property_id"],'operation'=>'='];
                        }elseif($data["socialdb_property_{$property_id}_operation"]=='2'){ // totalmente diferente
                             $array_data = ['value'=>$data["socialdb_property_$property_id"],'operation'=>'!='];
                        }elseif($data["socialdb_property_{$property_id}_operation"]=='3'){ // contem alguma das palavras 
                             $array_data = ['value'=>$data["socialdb_property_$property_id"],'operation'=>'LIKE'];
                        }
                        elseif($data["socialdb_property_{$property_id}_operation"]=='4'){ // NAO contenha qualquer das palavras 
                             $array_data = ['value'=> $data["socialdb_property_$property_id"],'operation'=>'NOT LIKE'];
                        }
                        $recover_data['advanced_search']['text'][$property_id] = $array_data;
                    }
                }
                elseif($dados->type=='textarea'){//se for propriedade dedadso do tipo texto
                    if(isset($data["socialdb_property_$property_id"])&&!empty(trim($data["socialdb_property_$property_id"]))){ // se nao estivervazio
                        if($data["socialdb_property_{$property_id}_operation"]=='1'){ // se a opercao for totalmente igual
                             $array_data = ['value'=>$data["socialdb_property_$property_id"],'operation'=>'='];
                        }elseif($data["socialdb_property_{$property_id}_operation"]=='2'){ // totalmente diferente
                             $array_data = ['value'=>$data["socialdb_property_$property_id"],'operation'=>'!='];
                        }elseif($data["socialdb_property_{$property_id}_operation"]=='3'){ // contem alguma das palavras 
                             $array_data = ['value'=> $data["socialdb_property_$property_id"],'operation'=>'LIKE'];
                        }
                        elseif($data["socialdb_property_{$property_id}_operation"]=='4'){ // NAO contenha qualquer das palavras 
                             $array_data = ['value'=>  $data["socialdb_property_$property_id"],'operation'=>'NOT LIKE'];
                        }
                        $recover_data['advanced_search']['textarea'][$property_id] = $array_data;
                    }
                }elseif($dados->type=='numeric'){//se for propriedade dedadso do tipo texto
                    if(isset($data["socialdb_property_$property_id"])&&!empty(trim($data["socialdb_property_$property_id"]))){ // se nao estivervazio
                        if($data["socialdb_property_{$property_id}_operation"]=='1'){ // se a opercao for totalmente igual
                             $array_data = ['value'=>$data["socialdb_property_$property_id"],'operation'=>'='];
                        }elseif($data["socialdb_property_{$property_id}_operation"]=='2'){ // totalmente diferente
                             $array_data = ['value'=>$data["socialdb_property_$property_id"],'operation'=>'!='];
                        }elseif($data["socialdb_property_{$property_id}_operation"]=='3'){ // contem alguma das palavras 
                             $array_data = ['value'=> $data["socialdb_property_$property_id"],'operation'=>'>='];
                        }
                        elseif($data["socialdb_property_{$property_id}_operation"]=='4'){ // NAO contenha qualquer das palavras 
                             $array_data = ['value'=>$data["socialdb_property_$property_id"],'operation'=>'<='];
                        }
                        $recover_data['advanced_search']['numeric'][$property_id] = $array_data;
                    }
                }elseif($dados->type=='date'){//se for propriedade dedadso do tipo texto
                    if(isset($data["socialdb_property_$property_id"])&&!empty(trim($data["socialdb_property_$property_id"]))){ // se nao estivervazio
                        if($data["socialdb_property_{$property_id}_operation"]=='1'){ // se a opercao for totalmente igual
                             $array_data = ['value'=>$data["socialdb_property_$property_id"],'operation'=>'='];
                        }elseif($data["socialdb_property_{$property_id}_operation"]=='2'){ // totalmente diferente
                             $array_data = ['value'=>$data["socialdb_property_$property_id"],'operation'=>'!='];
                        }elseif($data["socialdb_property_{$property_id}_operation"]=='3'){ // contem alguma das palavras 
                             $array_data = ['value'=> $data["socialdb_property_$property_id"],'operation'=>'>='];
                        }
                        elseif($data["socialdb_property_{$property_id}_operation"]=='4'){ // NAO contenha qualquer das palavras 
                             $array_data = ['value'=>$data["socialdb_property_$property_id"],'operation'=>'<='];
                        }
                        $recover_data['advanced_search']['date'][$property_id] = $array_data;
                    }
                }elseif(isset($dados->metas->socialdb_property_object_category_id)&&$dados->metas->socialdb_property_object_category_id!=''){//se for propriedade dedadso do tipo texto
                    if(isset($data["socialdb_property_$property_id"])&&!empty($data["socialdb_property_$property_id"])){ // se nao estivervazio
                        $array_data = ['value'=>$data["socialdb_property_$property_id"],'operation'=>'='];
                        $recover_data['advanced_search']['object'][$property_id] = $array_data;
                    }
                }
                
            }
        }
        return $recover_data;
    }

    /**
     * function select_filter($data)
     * @param array Array com os dados da colecao
     * @return void 
     * Metodo reponsavel em determinar se deve listar as colecoes ou objetos
     * Autor: Eduardo Humberto 
     */
    public function set_arguments_advanced_search($meta_query,$recover_data) {
        //date
        $meta_query['relation'] = 'AND';
        if (isset($recover_data['advanced_search']['date'])) {
            foreach ($recover_data['advanced_search']['date'] as $property_id => $array) {
                $meta_query[] = array(
                    'key' => 'socialdb_property_' . $property_id,
                    'value' => $array['value'],
                    'type' => 'date',
                    'compare' => $array['operation']
                );
            }
        }
        //numeric
        if (isset($recover_data['advanced_search']['numeric'])) {
            
            foreach ($recover_data['advanced_search']['numeric'] as $property_id => $array) {
                $meta_query[] = array(
                    'key' => 'socialdb_property_' . $property_id,
                    'value' => $array['value'],
                    'type' => 'numeric',
                    'compare' => $array['operation']
                );
            }
        }
        // text
         if (isset($recover_data['advanced_search']['text'])) {
            
            foreach ($recover_data['advanced_search']['text'] as $property_id => $array) {
                $meta_query[] = array(
                    'key' => 'socialdb_property_' . $property_id,
                    'value' => $array['value'],
                    'compare' => $array['operation']
                );
            }
        }
        // textarea
        if (isset($recover_data['advanced_search']['textarea'])) {
           
            foreach ($recover_data['advanced_search']['textarea'] as $property_id => $array) {
                $meta_query[] = array(
                    'key' => 'socialdb_property_' . $property_id,
                    'value' => $array['value'],
                    'compare' => $array['operation']
                );
            }
        }
        // textarea
        if (isset($recover_data['advanced_search']['object'])) {
            
            foreach ($recover_data['advanced_search']['object'] as $property_id => $array) {
                $meta_query[] = array(
                    'key' => 'socialdb_property_' . $property_id,
                    'value' => $array['value'],
                    'compare' => 'IN'
                );
            }
        }
        return $meta_query;
    }
}
