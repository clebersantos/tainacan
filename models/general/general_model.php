<?php

class Model {

    /**
     * function add_thumbnail()
     * @param int o id do objeto
     * @return void 
     * Metodo responsavel em adcionar as imagens
     * Autor: Eduardo Humberto 
     */
    public function add_thumbnail($post_id) {
        foreach ($_FILES as $file => $array) {
            if (!empty($_FILES[$file]["name"]) && ($file == 'repository_logo' || $file == 'collection_thumbnail' || $file == 'object_thumbnail')) {
                $_FILES[$file]["name"] = remove_accents($_FILES[$file]["name"]);
                $newupload = $this->insert_attachment($file, $post_id);
                return $newupload;
            }
        }
    }

    /**
     * function add_thumbnail()
     * @param int o id do objeto
     * @return void 
     * Metodo responsavel em adcionar a capa da colecao
     * Autor: Eduardo Humberto 
     */
    public function add_cover($post_id) {
        foreach ($_FILES as $file => $array) {
            if (!empty($_FILES[$file]["name"]) && $file == 'socialdb_collection_cover') {
                $_FILES[$file]["name"] = remove_accents($_FILES[$file]["name"]);
                $newupload = $this->insert_attachment($file, $post_id);
                return $newupload;
            }
        }
    }

    public function add_thumbnail_item($post_id) {
        foreach ($_FILES as $file => $array) {
            if (!empty($_FILES[$file]["name"]) && $file == 'object_thumbnail') {
                $_FILES[$file]["name"] = remove_accents($_FILES[$file]["name"]);
                $newupload = $this->insert_attachment($file, $post_id);
            }
        }
    }

    public function add_object_item($post_id) {
        foreach ($_FILES as $file => $array) {
            if (!empty($_FILES[$file]["name"]) && ($file == 'object_file' || $file == 'attachment')) {
                $_FILES[$file]["name"] = remove_accents($_FILES[$file]["name"]);
                $newupload = $this->insert_attachment($file, $post_id);
                return $newupload;
            }
        }
    }

    /**
     * function insert_attachment()
     * @param int o id do objeto
     * @return int O ID do post da imagem
     * Metodo responsavel em adcionar as imagens
     * Autor: Eduardo Humberto 
     */
    public function insert_attachment($file_handler, $post_id, $setthumb = 'true') {
        if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) {
            __return_false();
        }
        require_once(ABSPATH . "wp-admin" . '/includes/image.php');
        require_once(ABSPATH . "wp-admin" . '/includes/file.php');
        require_once(ABSPATH . "wp-admin" . '/includes/media.php');
        $attach_id = media_handle_upload($file_handler, $post_id);
        if ($setthumb) {
            if ($file_handler != "socialdb_collection_cover" && $file_handler != "object_file" && $file_handler != "arquivo" && $file_handler != "file") {
                $array = get_post_meta($post_id, '_thumbnail_id');
                if (empty($array) || !$array) {
                    add_post_meta($post_id, '_thumbnail_id', $attach_id);
                } else {
                    update_post_meta($post_id, '_thumbnail_id', $attach_id);
                }
            } else {
                $array = get_post_meta($post_id, '_file_id');
                if (empty($array) || !$array) {
                    add_post_meta($post_id, '_file_id', $attach_id);
                } else {
                    add_post_meta($post_id, '_file_id', $attach_id);
                }
            }
        }
        return $attach_id;
    }

    /**
     * function insert_attachment_file()
     * @param string $dir
     * @param int o id do objeto
     * @return int O ID do post da imagem
     * Metodo responsavel em adcionar as imagens
     * Autor: Eduardo Humberto 
     */
    public function insert_attachment_file($filename, $post_id) {
        require_once(ABSPATH . "wp-admin" . '/includes/image.php');
        require_once(ABSPATH . "wp-admin" . '/includes/file.php');
        require_once(ABSPATH . "wp-admin" . '/includes/media.php');
        $tmp = $filename;
        $file_array['name'] = basename($filename);
        $file_array['tmp_name'] = $tmp;
        $attach_id = media_handle_sideload($file_array, $post_id);
        return $attach_id;
    }

    /**
     * function add_thumbnail_url()
     * @param int o id do objeto
     * @return int O ID do post da imagem
     * Metodo responsavel em adcionar as imagens
     * Autor: Eduardo Humberto 
     */
    public function add_thumbnail_url($url, $object_id) {
        ini_set('max_execution_time', '0');
        if ($url && trim($url) != '') {
            $thumb_url = $url;
            $setthumb = 'true';
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
            if (strpos($thumb_url, "http:") === false && strpos($thumb_url, "https:") === false) {
                $thumb_url = "http:" . $thumb_url;
            }
            // Download file to temp location
            $ext = pathinfo($url, PATHINFO_EXTENSION);
            $tmp = download_url($thumb_url);
            // Set variables for storage
            // fix file filename for query strings
            preg_match('/[^\?]+\.(jpg|JPG|jpe|JPE|jpeg|JPEG|gif|GIF|png|PNG)/', $thumb_url, $matches);

            if (isset($matches[0])) {
                $removing_extension = str_replace('.' . $ext, '', basename($matches[0]));
                $file_array['name'] = str_replace(' ', '', remove_accent($removing_extension)) . '.' . $ext;
            } else {
                $file_array['name'] = 'image.gif';
            }
            $file_array['tmp_name'] = $tmp;
            // If error storing temporarily, unlink
            if (is_wp_error($tmp)) {
                // @unlink($file_array['tmp_name']);
                $file_array['tmp_name'] = '';
            }
            //var_dump($file_array);exit();
            // do the validation and storage stuff
            // $attach_id = wpgip_media_handle_sideload( $file_array,$post_id);
            $attach_id = media_handle_sideload($file_array, $object_id);
            // var_dump($attach_id,$file_array);
            if ($setthumb) {
                $array = get_post_meta($object_id, 'socialdb_thumbnail_id');
                if (!empty($array)) {
                    delete_post_meta($object_id, 'socialdb_thumbnail_url');
                    add_post_meta($object_id, 'socialdb_thumbnail_id', $attach_id);
                    add_post_meta($object_id, 'socialdb_thumbnail_url', $url);
                } else {
                    delete_post_meta($object_id, 'socialdb_thumbnail_url');
                    update_post_meta($object_id, 'socialdb_thumbnail_id', $attach_id);
                    add_post_meta($object_id, 'socialdb_thumbnail_url', $url);
                }
            }
            set_post_thumbnail($object_id, $attach_id);
        } else {
            return false;
        }
    }

    /**
     * function add_thumbnail_url()
     * @param int o id do objeto
     * @return int O ID do post da imagem
     * Metodo responsavel em adcionar as imagens
     * Autor: Eduardo Humberto 
     */
    public function add_thumbnail_url_facebook($url, $object_id) {
        ini_set('max_execution_time', '0');
        if ($url && trim($url) != '') {
            $thumb_url = $url;
            $setthumb = 'true';
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
            if (strpos($thumb_url, "http:") === false && strpos($thumb_url, "https:") === false) {
                $thumb_url = "http:" . $thumb_url;
            }
            // Download file to temp location
            $ext = pathinfo($url, PATHINFO_EXTENSION);
            $tmp = download_url($thumb_url);
            // Set variables for storage
            // fix file filename for query strings

            $file_array['name'] = 'thumb_url.jpg';
            $file_array['tmp_name'] = $tmp;
            // If error storing temporarily, unlink
            if (is_wp_error($tmp)) {
                @unlink($file_array['tmp_name']);
                $file_array['tmp_name'] = '';
            }
            //var_dump($file_array);exit();
            // do the validation and storage stuff
            // $attach_id = wpgip_media_handle_sideload( $file_array,$post_id);
            $attach_id = media_handle_sideload($file_array, $object_id);
            // var_dump($attach_id,$file_array);
            if ($setthumb) {
                $array = get_post_meta($object_id, 'socialdb_thumbnail_id');
                if (!empty($array)) {
                    delete_post_meta($object_id, 'socialdb_thumbnail_url');
                    add_post_meta($object_id, 'socialdb_thumbnail_id', $attach_id);
                    add_post_meta($object_id, 'socialdb_thumbnail_url', $url);
                } else {
                    delete_post_meta($object_id, 'socialdb_thumbnail_url');
                    update_post_meta($object_id, 'socialdb_thumbnail_id', $attach_id);
                    add_post_meta($object_id, 'socialdb_thumbnail_url', $url);
                }
            }
            set_post_thumbnail($object_id, $attach_id);
        } else {
            return false;
        }
    }

    /**
     * function add_file_url()
     * @param int o id do objeto
     * @return int O ID do post da imagem
     * Metodo responsavel em adcionar as imagens
     * Autor: Eduardo Humberto 
     */
    public function add_file_url($url, $object_id) {
        try {
            session_write_close();
            ini_set('max_execution_time', '0');
            ini_set('upload_max_filesize', '1024M');
            if ($url && trim($url) != '') {
                $name = remove_accent(substr($url, 0, strrpos($url, '.')));
                $extension = strrchr(explode('?sequence', $url)[0], '.');
                $thumb_url = $url;
                $setthumb = 'true';
                require_once(ABSPATH . 'wp-admin/includes/file.php');
                require_once(ABSPATH . 'wp-admin/includes/media.php');
                require_once( ABSPATH . 'wp-admin/includes/image.php' );
                if (strpos($thumb_url, "http:") === false && strpos($thumb_url, "https:") === false) {
                    $thumb_url = "http:" . $thumb_url;
                }
                // Download file to temp location
                $tmp = download_url($thumb_url);
                // Set variables for storage
                // fix file filename for query strings
                //preg_match('/[^\?]+\.(jpg|JPG|jpe|JPE|jpeg|JPEG|gif|GIF|png|PNG)/', $thumb_url, $matches);
                $file_array['name'] = $name . $extension;
                $file_array['tmp_name'] = $tmp;
                // If error storing temporarily, unlink
                if (is_wp_error($tmp)) {
                    @unlink($file_array['tmp_name']);
                    $file_array['tmp_name'] = '';
                } else {
                    // do the validation and storage stuff
                    // $attach_id = wpgip_media_handle_sideload( $file_array,$post_id);
                    $attach_id = media_handle_sideload($file_array, $object_id);
                    if ($setthumb) {
                        $array = get_post_meta($object_id, '_file_id');
                        if (empty($array) || !$array) {
                            add_post_meta($object_id, '_file_id', $attach_id);
                        } else {
                            add_post_meta($object_id, '_file_id', $attach_id);
                        }
                    }
                }
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    /* function getChildren() */
    /* receive ((int,string) parent) */
    /* Return the children of the especif parent */
    /* Author: Eduardo */

    public function getChildren($parent, $orderby = 'tt.count DESC,t.name ASC') {
        global $wpdb;
        $wp_term_taxonomy = $wpdb->prefix . "term_taxonomy";
        $wp_terms = $wpdb->prefix . "terms";
        $wp_taxonomymeta = $wpdb->prefix . "termmeta";
        $query = "
			SELECT * FROM $wp_terms t
			INNER JOIN $wp_term_taxonomy tt ON t.term_id = tt.term_id
				WHERE tt.parent = {$parent} ORDER BY $orderby  
		";
        return $wpdb->get_results($query);
    }

    /**
     * function get_collections_published()
     * @param array Os dados vindo do formulario
     * @return json com o id e o nome de cada colecao
     * @author Eduardo Humberto
     */
    public function get_collections_published() {
        global $wpdb;
        $wp_posts = $wpdb->prefix . "posts";
        $query = "
                        SELECT p.* FROM $wp_posts p
                        WHERE p.post_type like 'socialdb_collection' and p.post_status LIKE 'publish'
                ";
        $result = $wpdb->get_results($query);
        return $result;
    }

    /**
     * 
     * @param type $facet_id
     */
    public function remove_facet_from_collections($facet_id, $term_id) {
        $collections = $this->get_collections_published();
        if ($collections && is_array($collections)) {
            foreach ($collections as $collection) {
                delete_post_meta($collection->ID, 'socialdb_collection_facets', $facet_id);
                delete_post_meta($collection->ID, 'socialdb_collection_facets', $term_id);
            }
        }
    }

    /**
     * function get_category_root_of($collection_id)
     * @param int $collection_id
     * @return int With O term_id da categoria root da colecao.
     * 
     * metodo responsavel em retornar a categoria root da colecao
     * Autor: Eduardo Humberto 
     */
    public function get_category_root_of($collection_id) {
        return get_post_meta($collection_id, 'socialdb_collection_object_type', true);
    }

    /**
     * function get_category_root_id($collection_id)
     * @return int With O term_id da categoria root do repositorio.
     * 
     * metodo responsavel em retornar a categoria root do repositorio
     * Autor: Eduardo Humberto 
     */
    public function get_category_root_id() {
        $term = get_term_by('name', 'socialdb_category', 'socialdb_category_type');
        return $term->term_id;
    }

    /**
     * function get_collection_data($collection_id)
     * @param int $collection_id
     * @return int With O term_id da categoria root da colecao.
     * 
     * metodo responsavel em retornar a categoria root da colecao
     * Autor: Eduardo Humberto 
     */
    public function get_collection_data($collection_id) {
        global $wpdb;
        $wp_postmeta = $wpdb->prefix . "postmeta";
        $data['collection_post'] = get_post($collection_id);
        $query = "
			SELECT * FROM $wp_postmeta p
				WHERE p.post_id = {$collection_id}
		";
        $collections_data = $wpdb->get_results($query);
        foreach ($collections_data as $collection_data) {
            if ($collection_data->meta_key == 'socialdb_collection_facets') {
                if ($collection_data->meta_value != '') {
                    $config[$collection_data->meta_key][] = get_term_by('id', $collection_data->meta_value, 'socialdb_category_type');
                }
            } elseif (in_array($collection_data->meta_key, array('socialdb_collection_moderator', 'socialdb_collection_channel'))) {
                if ($collection_data->meta_value != '') {
                    $config[$collection_data->meta_key][] = $this->get_user($collection_data->meta_value);
                }
            } else {
                $config[$collection_data->meta_key] = $collection_data->meta_value;
            }
        }
        $config['sociadb_collection_privacity'] = wp_get_post_terms($collection_id, 'socialdb_collection_type');
        $config['socialdb_collection_property_object_facets'] = $this->get_property_object_facets($this->get_category_root_of($collection_id));
        $data['collection_metas'] = $config;
        return $data;
    }

    /* function get_property_object_facets() */
    /* @param int $category_root_id id da categoria raiz
      /* @return array com os dados e metadados das propriedades que são facetas./
      /* @author Eduardo */

    public function get_property_object_facets($category_root_id) {
        $data['property_object'] = [];
        $all_properties_id = get_term_meta($category_root_id, 'socialdb_category_property_id');
        if (is_array($all_properties_id)) {
            foreach ($all_properties_id as $property_id) {// varro todas propriedades
                $type = $this->get_property_type_hierachy($property_id); // pego o tipo da propriedade
                $all_data = $this->get_all_property($property_id, true); // pego todos os dados possiveis da propriedade
                if ($type == 'socialdb_property_object' && isset($all_data['metas']['socialdb_property_object_is_facet']) && $all_data['metas']['socialdb_property_object_is_facet'] == 'true') {// verifico o tipo e se e faceta
                    $data['property_object'][] = $all_data;
                    $data['no_properties'] = false;
                }
            }
        }
        return $data['property_object'];
    }

    /* function get_property_type($property_id) */
    /* @param int o id da propriedade 
      /* @return string retorna o tipo da propriedade./
      /* @author Eduardo */

    public static function get_property_type($property_id) {
//        if (isset(get_term_by('id', $property_id, 'socialdb_property_type')->parent)) {
//            $parent_id = get_term_by('id', $property_id, 'socialdb_property_type')->parent;
//            $parent = get_term_by('id', $parent_id, 'socialdb_property_type');
//            return $parent->name;
//        } else {
//            return 0;
//        }

        $roots_parents = [
            get_term_by('name', 'socialdb_property_data', 'socialdb_property_type')->term_id,
            get_term_by('name', 'socialdb_property_object', 'socialdb_property_type')->term_id,
            get_term_by('name', 'socialdb_property_term', 'socialdb_property_type')->term_id];

        $term = get_term_by('id', $property_id, 'socialdb_property_type');
        if (!$term) {
            return false;
        }
        $parent = $property_id;
        while (!in_array($parent, $roots_parents)) {
            $term = get_term_by('id', $parent, 'socialdb_property_type');
            $parent = $term->parent;
            if (in_array($parent, $roots_parents)) {
                break;
            }
        }
        foreach ($roots_parents as $root_parents) {
            if ($root_parents == $parent) {
                return get_term_by('id', $parent, 'socialdb_property_type')->name;
            }
        }
        return 0;
    }

    /* function get_property_type($property_id) */
    /* @param int o id da propriedade 
      /* @return string retorna o tipo da propriedade./
      /* @author Eduardo */

    public function get_property_type_hierachy($property_id) {
//        if (isset(get_term_by('id', $property_id, 'socialdb_property_type')->parent)) {
//            $parent_id = get_term_by('id', $property_id, 'socialdb_property_type')->parent;
//            $parent = get_term_by('id', $parent_id, 'socialdb_property_type');
//            return $parent->name;
//        } else {
//            return 0;
//        }

        $roots_parents = [
            get_term_by('name', 'socialdb_property_data', 'socialdb_property_type')->term_id,
            get_term_by('name', 'socialdb_property_object', 'socialdb_property_type')->term_id,
            get_term_by('name', 'socialdb_property_term', 'socialdb_property_type')->term_id];

        $term = get_term_by('id', $property_id, 'socialdb_property_type');
        if (!$term) {
            return 0;
        }
        $parent = $property_id;
        while (!in_array($parent, $roots_parents)) {
            if (in_array($parent, $roots_parents)) {
                break;
            } elseif ($parent == 0) {
                break;
            }
            $term = get_term_by('id', $parent, 'socialdb_property_type');
            $parent = $term->parent;
        }
        foreach ($roots_parents as $root_parents) {
            if ($root_parents == $parent) {
                return get_term_by('id', $parent, 'socialdb_property_type')->name;
            }
        }
        return 0;
    }

    /**
     * function generate_slug($title)
     * @param string $string
     * @return string
     * metodo responsavel em normatizar uma string para ser um slug
     * @author Eduardo Humberto 
     */
    public function generate_slug($string, $collection_id) {
        return sanitize_title(remove_accent($string)) . "_" . mktime() . rand(0, 100);
    }

    /**
     * function get_collection_data($collection_id)
     * @param string o nome do termo que sera inserido
     * @param int  O id da colecao que sera utilizado para verificar o termo
     * @param string  a taxonomia utilizada
     * @return boolean Falso se não existir o termo e verdadeiro se existir.
     * 
     * metodo responsavel em retornar a categoria root da colecao
     * Autor: Eduardo Humberto 
     */
    public function verify_term_by_slug($name, $collection_id, $taxonomy) {
        $slug = $this->generate_slug($name, $collection_id);
        $term = get_term_by('slug', $slug, $taxonomy);
        if ($term) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * function get_all_property($property_id)
     * @param int $property_id
     * @param boolean $get_metas Se buscar tb os metas da propriedade
     * @param integer $collection_id
     * @return array Com todos os dados da propriedade.
     * @autor: Eduardo Humberto 
     */
    public function get_all_property($property_id, $get_metas = false, $collection_id = "") {
        $property = get_term_by('id', $property_id, 'socialdb_property_type');
        $data = [];
        if ($property):
            $data['id'] = $property->term_id;
            $data['name'] = $property->name;
            $data['slug'] = $property->slug;
            if ($property_id) {
                $metas = $this->get_property_meta_data($property_id);
                if (isset($metas['socialdb_property_data_widget'])) {
                    $data['type'] = $metas['socialdb_property_data_widget'];
                } elseif (isset($metas['socialdb_property_term_widget']) && $metas['socialdb_property_term_widget'] != '') {
                    $data['type'] = $metas['socialdb_property_term_widget'];
                } else if (isset($metas['socialdb_property_object_category_id'])) {
                    if(is_array($metas['socialdb_property_object_category_id'])){
                        $data['type'] = __('Multiple','tainacan');
                    }else{
                        $data['type'] = $this->get_type_property_object($metas['socialdb_property_object_category_id']);
                    }
                   
                } else {
                    $type = $this->get_ranking_type($property->parent);
                    $data['type'] = $type;
                }
                if ($get_metas) {
                    if (isset($metas['socialdb_property_created_category']) && $metas['socialdb_property_created_category'] != '') {
                        if ($metas['socialdb_property_created_category'] == get_term_by('slug', 'socialdb_category', 'socialdb_category_type')->term_id) {
                            $metas['is_repository_property'] = true;
                        } else {
                            $metas['is_repository_property'] = false;
                        }
                    }
                    $data['metas'] = $metas;
                }
                if (!empty($collection_id)) {
                    $data['search_widget'] = get_post_meta($collection_id, 'socialdb_collection_facet_' . $property_id . '_widget')[0];
                }
            }
        endif;
        if(has_filter('modificate_values_get_all_property')){
            $data = apply_filters( 'modificate_values_get_all_property', $data);
        }
        return $data;
    }

    /**
     * Metodo que retorna o tipo do metadado de objeto
     */
    public function get_type_property_object($category_id) {
        $collection = $this->get_collection_by_category_root($category_id);
        if (isset($collection[0]->post_title)) {
            return $collection[0]->post_title;
        } else {
            return get_term_by('id', $category_id, 'socialdb_category_type')->name;
        }
    }

    /**
     * function get_type_by_name()
     * @param string property
     * @return string 
     * Metodo que retorna a property type 
     * Autor: Marco Túlio
     */
    public function get_type_by_name($type) {
        $type_work = get_term_by('name', $type, 'socialdb_property_type');

        return $type_work;
    }

    /**
     * function get_ranking_parent()
     * @param $data
     * @return string 
     * Metodo que retorna o type da propertie utilizado para adiquirir as propriedades parent do ranking
     * Autor: Marco Túlio
     */
    public function get_ranking_parent($data) {

        if ($data['ranking_type'] == 'like') {
            $type = $this->get_type_by_name('socialdb_property_ranking_like');
        } else
        if ($data['ranking_type'] == 'binary') {
            $type = $this->get_type_by_name('socialdb_property_ranking_binary');
        } else {
            $type = $this->get_type_by_name('socialdb_property_ranking_stars');
        }

        return $type;
    }

    /**
     * function get_ranking_type()
     * @param $parent_id
     * @return string 
     * Metodo que retorna um type de acordo com o id do parent, os metods são todos coligados
     * Autor: Marco Túlio
     */
    public function get_ranking_type($parent_id) {
        $type_work = get_term_by('id', $parent_id, 'socialdb_property_type');
        if ($type_work->name == 'socialdb_property_ranking_like') {
            $type = 'like';
        } else if ($type_work->name == 'socialdb_property_ranking_binary') {
            $type = 'binary';
        } else {
            $type = 'stars';
        }
        return $type;
    }

    /**
     * function get_collection_by_category_root($user_id)
     * @param int a categoria raiz de uma colecao
     * @return array(wp_post) a colecao de onde pertence a categoria root
     * @ metodo responsavel em retornar as colecoes de um determinando usuario
     * @author: Eduardo Humberto 
     */
    public function get_collection_by_category_root($category_root_id) {
        global $wpdb;
        $wp_posts = $wpdb->prefix . "posts";
        $wp_postmeta = $wpdb->prefix . "postmeta";
        $query = "
                    SELECT p.* FROM $wp_posts p
                    INNER JOIN $wp_postmeta pm ON p.ID = pm.post_id    
                    WHERE pm.meta_key LIKE 'socialdb_collection_object_type' and pm.meta_value like '$category_root_id'
            ";
        $result = $wpdb->get_results($query);


        if ($result && is_array($result) && count($result) > 0) {
            return $result;
        } else {
            return array();
        }
    }

    /**
     * function get_properties_object($object_id)
     * @param int O id do objeto que vai buscar suas propriedades
     * @return array(wp_post) a colecao de onde pertence a categoria root
     * @ metodo responsavel em retornar as colecoes de um determinando usuario
     * @author: Eduardo Humberto 
     */
    public function get_properties_object($object_id) {
        global $wpdb;
        $properties = [];
        $wp_posts = $wpdb->prefix . "posts";
        $wp_postmeta = $wpdb->prefix . "postmeta";
        $query = "
                    SELECT pm.* FROM $wp_posts p
                    INNER JOIN $wp_postmeta pm ON p.ID = pm.post_id    
                    WHERE pm.meta_key LIKE 'socialdb_property_%' and p.ID = " . $object_id . "
            ";
        $result = $wpdb->get_results($query);
        if ($result && is_array($result) && count($result) > 0) {
            foreach ($result as $property) {
                $id = str_replace('socialdb_property_', '', trim($property->meta_key));
                $properties[$id][] = $property->meta_value;
            }
        }
        return $properties;
    }

    /**
     * function get_property_meta_data($property_id)
     * @param int $property_id
     * @return array Com os metadados da propriedade.
     * 
     * metodo responsavel em retornar os metadados de uma propriedade ja criada
     * @autor: Eduardo Humberto 
     */
    public function get_property_meta_data($property_id) {
        global $wpdb;
        $config = [];
        $wp_taxonomymeta = $wpdb->prefix . "termmeta";
        $query = "
                        SELECT * FROM $wp_taxonomymeta t
                                WHERE t.term_id = {$property_id}
                ";
        $property_datas = $wpdb->get_results($query);
        if ($property_datas && is_array($property_datas)) {
            foreach ($property_datas as $property_data) {
                if ($property_data->meta_key == 'socialdb_property_used_by_categories'||$property_data->meta_key == 'socialdb_property_object_category_id') {
                    $config[$property_data->meta_key][] = $property_data->meta_value;
                } else {
                    $config[$property_data->meta_key] = $property_data->meta_value;
                }
            }
        }
        
        if($config['socialdb_property_object_category_id']
                &&is_array($config['socialdb_property_object_category_id'])
                    &&count($config['socialdb_property_object_category_id'])==1){
            $config['socialdb_property_object_category_id'] = $config['socialdb_property_object_category_id'][0];
        }
        return $config;
    }

    /**
     * function vinculate_property($category_id,$property_id)
     * @param string $category_id 
     * @param string $property_id
     * @return boolean true para sucesso e false para falha
     * 
     * @author: Eduardo Humberto 
     */
    public function vinculate_property($category_id, $property_id) {
        $metas = get_term_meta($category_id, 'socialdb_category_property_id');
        if ($metas) {
            if (in_array('', $metas)) {
                // delete_term_meta($category_id, 'socialdb_category_property_id', '');
            }
        }
        // var_dump($category_id, 'socialdb_category_property_id', $property_id,add_term_meta($category_id, 'socialdb_category_property_id', $property_id));
        return add_term_meta($category_id, 'socialdb_category_property_id', $property_id);
    }

    /**
     * function get_category_root_posts($category_id,$property_id)
     * @param string $category_root_id O id da categoria_raiz
     * @param string $field O campo que deseja retornar da tabela posts do wordpress
     * @return array(wp_post objects) Com todos os posts da colecao ou um aray vazio se nao existir posts
     * 
     * @author: Eduardo Humberto 
     */
    public function get_category_root_posts($category_root_id, $field = '*') {
        global $wpdb;
        $ancestors= [];
        if(is_array($category_root_id)){
            $array = array_filter( array_unique($category_root_id));
            foreach ($array as $category) {
                $ancestors[] = $category;
                $parents = get_term_children($category, 'socialdb_category_type');
                if(is_array($parents)){
                    $ancestors = array_merge($parents, $ancestors);
                }
            }
        }else{
            $ancestors[] = $category_root_id;
            $parents = get_term_children($category_root_id, 'socialdb_category_type');
            if(is_array($parents)){
                $ancestors = array_merge($parents, $ancestors);
            }
            $query = " = $category_root_id ";
        }
        $array = implode(',', array_filter( array_unique($ancestors)));
        $query = ' IN ('.$array.') ';
        $wp_posts = $wpdb->prefix . "posts";
        $wp_term_taxonomy = $wpdb->prefix . "term_taxonomy";
        $term_relationships = $wpdb->prefix . "term_relationships";
        $query = "
                    SELECT p.$field FROM $wp_posts p
                    INNER JOIN $term_relationships tr ON p.ID = tr.object_id    
                    INNER JOIN $wp_term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id    
                    WHERE tt.term_id {$query}
                    AND p.post_type like 'socialdb_object' AND p.post_status LIKE 'publish'
            ";
        $result = $wpdb->get_results($query);
        if ($result && is_array($result) && count($result) > 0) {
            return $result;
        } else {
            return array();
        }
    }

    /**
     * function get_collection_posts($category_id,$property_id)
     * @param string $collection_id O id da colecao
     * @param string $field O campo que deseja retornar da tabela posts do wordpress
     * @return array(wp_post objects) Com todos os posts da colecao ou um aray vazio se nao existir posts
     * 
     * @author: Eduardo Humberto 
     */
    public function get_collection_posts($collection_id, $field = '*') {
        global $wpdb;
        $wp_posts = $wpdb->prefix . "posts";
        $term_relationships = $wpdb->prefix . "term_relationships";
        $category_root_id = $this->get_category_root_of($collection_id);
        $term = get_term_by('id', $category_root_id, 'socialdb_category_type');
        if (isset($term->term_taxonomy_id)) {
            $query = "
                    SELECT p.$field FROM $wp_posts p
                    INNER JOIN $term_relationships t ON p.ID = t.object_id    
                    WHERE p.post_type LIKE 'socialdb_object' and t.term_taxonomy_id = {$term->term_taxonomy_id} AND p.post_status LIKE 'publish'
            ";

            $result = $wpdb->get_results($query);
            if ($result && is_array($result) && count($result) > 0) {
                return $result;
            } else {
                return array();
            }
        }
    }

    /**
     * function get_collection_posts_trash($category_id,$property_id)
     * @param string $collection_id O id da colecao
     * @param string $field O campo que deseja retornar da tabela posts do wordpress
     * @return array(wp_post objects) Com todos os posts da colecao ou um aray vazio se nao existir posts
     * 
     * @author: Eduardo Humberto 
     */
    public function get_collection_posts_trash($collection_id, $field = '*') {
        global $wpdb;
        $wp_posts = $wpdb->prefix . "posts";
        $term_relationships = $wpdb->prefix . "term_relationships";
        $category_root_id = $this->get_category_root_of($collection_id);
        $term = get_term_by('id', $category_root_id, 'socialdb_category_type');
        if (isset($term->term_taxonomy_id)) {
            $query = "
                    SELECT p.$field FROM $wp_posts p
                    INNER JOIN $term_relationships t ON p.ID = t.object_id    
                    WHERE p.post_type LIKE 'socialdb_object' and t.term_taxonomy_id = {$term->term_taxonomy_id} 
            ";

            $result = $wpdb->get_results($query);
            if ($result && is_array($result) && count($result) > 0) {
                return $result;
            } else {
                return array();
            }
        }
    }

    /**
     * function vinculate_objects_with_property($property_id,$collection_id)
     * @param string $property_id O id da propriedade
     * @param string $collection_id O id da colecao
     * @return void Apenas insere a propriedade
     * Funcao que vincula a propriedade criada com os objetos ja criados na 
     * @author: Eduardo Humberto 
     */
    public function vinculate_objects_with_property($property_id, $collection_id, $category_property_id) {
        ini_set('max_execution_time', '0');
        if ($category_property_id == $this->get_category_root_of($collection_id)) {
            $all_posts = $this->get_collection_posts($collection_id, 'ID');
            if (!empty($all_posts)) {
                foreach ($all_posts as $post_object) {
                    $has = get_post_meta($post_object->ID, 'socialdb_property_' . $property_id);
                    if (!$has) {
                        add_post_meta($post_object->ID, 'socialdb_property_' . $property_id, '');
                    }
                }
            }
        }
    }

    /**
     * function vinculate_objects_with_property($property_id,$collection_id)
     * @param string $property_id O id da propriedade
     * @param string $collection_id O id da colecao
     * @return void Apenas insere a propriedade
     * Funcao que vincula a propriedade criada com os objetos ja criados na 
     * @author: Eduardo Humberto 
     */
    public function vinculate_objects_with_property_autoincrement($property_id, $collection_id, $category_property_id) {
        ini_set('max_execution_time', '0');
        $counter = $this->get_last_counter($property_id);
        if (!$counter) {
            $counter = 1;
        } else {
            $counter++;
        }
        if ($category_property_id == $this->get_category_root_of($collection_id)) {
            $all_posts = $this->get_collection_posts($collection_id, 'ID');
            if (!empty($all_posts)) {
                foreach ($all_posts as $post_object) {
                    $has = get_post_meta($post_object->ID, 'socialdb_property_' . $property_id);
                    if (!$has) {
                        add_post_meta($post_object->ID, 'socialdb_property_' . $property_id, $counter);
                    }
                    $counter++;
                }
            }
        }
    }

    /**
     * function get_property_meta_data($property_id)
     * @param int $property_id
     * @return boolean .
     * 
     * metodo responsavel em deletar os metadados de uma propriedade
     * @autor: Eduardo Humberto 
     */
    public function delete_property_meta_data($property_id) {
        global $wpdb;
        $wp_taxonomymeta = $wpdb->prefix . "termmeta";
        $query = "
			DELETE FROM $wp_taxonomymeta 
				WHERE term_id = {$property_id}
		";
        $wpdb->query($query);
        if ($property_id) {
            return true;
        } else {
            return false;
        }
    }

    /* function get_categories_by_owner() */
    /* @param int $owner_id o dono das categorias
      /* @param $parent(optional) a categoria pai que sera utilizada como base na pesquisa
      /* @return array */
    /* Author: Eduardo */

    public function get_categories($parent = 0, $return_type = OBJECT) {
        global $wpdb;
        $wp_term_taxonomy = $wpdb->prefix . "term_taxonomy";
        $wp_terms = $wpdb->prefix . "terms";
        $query = "
			SELECT * FROM $wp_terms t
			INNER JOIN $wp_term_taxonomy tt ON t.term_id = tt.term_id
			WHERE tt.parent = {$parent}  
                        ORDER BY t.name
		";
        return $wpdb->get_results($query, $return_type);
    }

    /**
     * function get_user($data)
     * @param int $user_id os dados vindo do formulario
     * @return mix Retorna um array  com o nome e o ID do usuario ou false se não existir
     * 
     * @author: Eduardo Humberto 
     */
    public function get_user($user_id) {
        global $wpdb;
        $data = FALSE;
        //$wp_user = $wpdb->prefix . "users";
        $wp_user = "wp_users";
        $query = "
                SELECT u.ID AS ID ,u.user_nicename AS user_nicename FROM $wp_user u
                WHERE u.ID = {$user_id}";
        $item = $wpdb->get_results($query);
        if (isset($item[0]->ID)) {
            $data = array(
                'id' => $item[0]->ID,
                'name' => $item[0]->user_nicename
            );
        }
        return $data;
    }

    /**
     * function get_collection_by_object($object_id)
     * @param int $object_id O id do item
     * @return mix Retorna o post da colecao
     * 
     * @author: Eduardo Humberto 
     */
    public function get_collection_by_object($object_id) {
        $categories = wp_get_object_terms($object_id, 'socialdb_category_type');
        foreach ($categories as $category) {
            $result = $this->get_collection_by_category_root($category->term_id);
            if (!empty($result)) {
                return $result;
            }
        }
    }

    /**
     * function get_all_collections($user_id)
     * @return array(wp_post) as colecoes
     * @ metodo responsavel em retornar as colecoes deste repositorio
     * @author: Eduardo Humberto 
     */
//    public function get_all_collections() {
//        global $wpdb;
//        $wp_posts = $wpdb->prefix . "posts";
//        $query = "
//                    SELECT * FROM $wp_posts p
//                    WHERE p.post_type like 'socialdb_collection'
//                    order by p.post_title
//            ";
//        $result = $wpdb->get_results($query);
//        if ($result && is_array($result) && count($result) > 0) {
//            return $result;
//        } else {
//            return array();
//        }
//    }

    /**
     * function insert_properties_hierarchy
     * @param int $category_root_id O id da categoria raiz
     * @param int $property_id O id da propriedade a ser inserida
     * @return array com os metas ou false se estiver vazio.
     * @author: Eduardo Humberto 
     */
    public function insert_properties_hierarchy($category_root_id, $property_id, $is_facet = false) {
        // no default values. using these as examples
        $terms_id = get_term_children($category_root_id, 'socialdb_category_type');
        if (count($terms_id) > 0) {
            foreach ($terms_id as $term_id) {
                if ($this->verify_collection_category_root($term_id)) {
                    $collection = $this->get_collection_by_category_root($term_id);
                    $this->vinculate_property($term_id, $property_id); // vinculo com a colecao/categoria
                    if ($is_facet) {
                        add_post_meta($collection[0]->ID, 'socialdb_collection_facet_' . $property_id . '_color', 'color_property1');
                    }
                    //possivelmente um problema devido ao grande tempo de execucao
                    $this->vinculate_objects_with_property($property_id, $collection[0]->ID, $term_id);
                }
            }
        }
    }

    public function delete_properties_hierarchy($category_root_id, $property_id) {
        // no default values. using these as examples
        $terms_id = get_term_children($category_root_id, 'socialdb_category_type');
        if (count($terms_id) > 0) {
            foreach ($terms_id as $term_id) {
                if ($this->verify_collection_category_root($term_id)) {
                    delete_term_meta($term_id, 'socialdb_category_property_id', $property_id);
                }
            }
        }
    }

    /* function verify_collection_category_root() */
    /* @param int $owner_id o dono das categorias
      /* @param $parent(optional) a categoria pai que sera utilizada como base na pesquisa
      /* @return boolean */
    /* Funcao que verifica se a categoria e uma categoria raiz de uma colecao, verdadeiro se for e falso caso nao
      /* Author: Eduardo */

    public function verify_collection_category_root($category_id) {
        if (strpos($category_id, '_') !== false):
            $category_id = explode('_', $category_id)[0];
        endif;
        global $wpdb;
        $wp_posts = $wpdb->prefix . "posts";
        $wp_postmeta = $wpdb->prefix . "postmeta";
        $query = "
			SELECT * FROM $wp_posts p
			INNER JOIN $wp_postmeta pm ON p.ID = pm.post_id
			WHERE pm.meta_key LIKE 'socialdb_collection_object_type' AND
                        pm.meta_value LIKE '$category_id' 
		";
        $result = $wpdb->get_results($query);
        if (isset($result) && !empty($result) && count($result) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /* function verify_collection_category_root() */
    /* @param int $owner_id o dono das categorias
      /* @param $parent(optional) a categoria pai que sera utilizada como base na pesquisa
      /* @return boolean */
    /* Funcao que verifica se a categoria e uma categoria raiz de uma colecao, verdadeiro se for e falso caso nao
      /* Author: Eduardo */

    public function get_collection_category($category_id) {
        if (strpos($category_id, '_') !== false):
            $category_id = explode('_', $category_id)[0];
        endif;
        global $wpdb;
        $wp_posts = $wpdb->prefix . "posts";
        $wp_postmeta = $wpdb->prefix . "postmeta";
        $query = "
			SELECT * FROM $wp_posts p
			INNER JOIN $wp_postmeta pm ON p.ID = pm.post_id
			WHERE pm.meta_key LIKE 'socialdb_collection_object_type' AND
                        pm.meta_value LIKE '$category_id' 
		";
        $result = $wpdb->get_results($query);
        if (isset($result) && !empty($result) && count($result) > 0) {
            return $result[0]->post_id;
        } else {
            return false;
        }
    }

    /**
     * function get_classification()
     * @param string $type O tipo de classificacao que sera retornada
     * @param string $classifications A string com todas as classificacoes
     * @return array com as id das classficacoes 
     * Metodo reponsavel em  retiornar array com os ids dos itens selecionados a partir do tipo escolhido
     * @author Eduardo Humberto 
     */
    public function get_classification($type, $classifications) {
        $all_classification = explode(',', $classifications);
        $result = [];
        if (is_array($all_classification)) {
            foreach ($all_classification as $classification) {
                if ($type == 'category') {
                    if (strpos($classification, '_') === false && $classification) {
                        $result[] = $classification;
                    }
                } elseif ($type == 'tag') {
                    if (strpos($classification, '_') !== false && strpos($classification, 'tag') !== false) {
                        $result[] = explode('_', $classification)[0];
                    }
                } else {
                    if (strpos($classification, '_') !== false && strpos($classification, 'tag') === false) {
                        $value = explode('_', $classification);
                        $result[$value[1]][] = trim($value[0]);
                    }
                }
            }
        }
        return $result;
    }

    /** funcao que retorna a categoria faceta a partir de uma sub categoria 
     * @param int  $category_id O id da categoria
     * @param int  $collection_id O id da colecao
     * @return int  funcao que retorna a categoria faceta a partir de uma sub categoria ou false se nao encontrar 
     * @author: Eduardo */
    public function get_category_facet_parent($category_id, $collection_id) {
        $facets = CollectionModel::get_facets($collection_id);
        if ($facets && is_array($facets)&&!empty($facets)) {
            $parents = get_ancestors($category_id, 'socialdb_category_type');
            foreach ($facets as $facet) {
                if ((is_array($parents) && in_array($facet, $parents)) || $facet == $category_id) {
                    return $facet;
                }
            }
        }
        return false;
    }

    /** funcao que retorna o id da propriedade a partir de uma categoria
     * @param int  $category_id O id da categoria
     * @param int  $collection_id O id da colecao
     * @return int  funcao que retorna a categoria faceta a partir de uma sub categoria ou false se nao encontrar 
     * @author: Eduardo */
    public function get_category_property($category_id, $collection_id) {
        $ancestors = get_ancestors($category_id, 'socialdb_category_type');
        $category_root_id = $this->get_collection_category_root($collection_id);
        $properties = array_filter(get_term_meta($category_root_id, 'socialdb_category_property_id'));
        if ($properties && is_array($properties)) {
            foreach ($properties as $property) {
                $term_root = get_term_meta($property, 'socialdb_property_term_root', true);
                if ($term_root && $term_root != '' && is_array($ancestors)) {
                    foreach ($ancestors as $ancestor) {
                        if (in_array($term_root, $ancestors) || $term_root == $ancestor) {
                            return $ancestor;
                        }
                    }
                }
            }
        }
        return false;
    }

    /* funcao que retorna a categoria faceta a partir de uma sub categoria */
    /* @param int  $category_id O id da categoria
      /* @param int  $collection_id O id da colecao
      /* @return int  funcao que retorna a categoria faceta a partir de uma sub categoria ou false se nao encontrar */
    /* @author: Eduardo */

    public function get_facet_class($facet_id, $collection_id) {
        $facet_class_color = get_post_meta($collection_id, 'socialdb_collection_facet_' . $facet_id . '_color', true);
        return $facet_class_color;
    }

    /**
     * function get_mapping($object_id)
     * @param int $object_id
     * @return boolean 
     * @description metodo responsavel em retornar o mapeamento do objeto
     * @author: Eduardo Humberto 
     */
    public function get_mapping($object_id) {
        $channels = get_post_meta($object_id, 'socialdb_channel_id');
        if (is_array($channels)) {
            foreach ($channels as $ch) {
                $ch = get_post($ch);
                $oai_pmhdc = wp_get_object_terms($ch->ID, 'socialdb_channel_type');
                if (!empty($ch) && !empty($ch->ID) && isset($oai_pmhdc[0]->name) && $oai_pmhdc[0]->name == 'socialdb_channel_oaipmhdc') {
                    return $ch->ID;
                }
            }
            return false;
        } else {
            return false;
        }
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
        $category_root_id = get_term_by('id', $all_data['metas']['socialdb_property_object_category_id'], 'socialdb_category_type');
        $query = "
                        SELECT p.* FROM $wp_posts p
                        INNER JOIN $term_relationships t ON p.ID = t.object_id    
                        WHERE t.term_taxonomy_id = {$category_root_id->term_taxonomy_id}
                        AND p.post_type like 'socialdb_object' AND p.post_status like 'publish' and p.post_title LIKE '%{$data['term']}%'
                ";
        $result = $wpdb->get_results($query);
        if ($result) {
            foreach ($result as $object) {
                $json[] = array('value' => $object->ID, 'label' => $object->post_title);
            }
        }
        return json_encode($json);
    }

    /**
     * function get_objects_by_property_json_advanced_search()
     * @param int Os dados vindo do formulario
     * @return json com o id e o nome de cada objeto
     * @author Eduardo Humberto
     */
    public function get_objects_by_property_json_advanced_search($data) {
        global $wpdb;
        $wp_posts = $wpdb->prefix . "posts";
        $term_relationships = $wpdb->prefix . "term_relationships";
        if ($data['collection_id'] != get_option('collection_root_id')) {
            $category_root_id = $this->get_collection_category_root($data['collection_id']);
            $category_root_id = get_term_by('id', $category_root_id, 'socialdb_category_type');
            $where = "t.term_taxonomy_id = {$category_root_id->term_taxonomy_id} AND ";
        } else {
            $where = "";
        }
        $query = "
                        SELECT p.* FROM $wp_posts p
                        INNER JOIN $term_relationships t ON p.ID = t.object_id    
                        WHERE $where p.post_type like 'socialdb_object' AND p.post_status like 'publish' and ( p.post_title LIKE '%{$data['term']}%' OR p.post_content LIKE '%{$data['term']}%')
                ";
        $result = $wpdb->get_results($query);
        if ($result) {
            foreach ($result as $object) {
                $json[] = array('value' => $object->post_title, 'label' => $object->post_title);
            }
        }
        return json_encode($json);
    }

    /**
     * function get_objects_by_property_json()
     * @param int Os dados vindo do formulario
     * @return json com o id e o nome de cada objeto
     * @author Eduardo Humberto
     */
    public function get_terms_by_property_json($data) {
        global $wpdb;
        $wp_term_taxonomy = $wpdb->prefix . "term_taxonomy";
        $wp_terms = $wpdb->prefix . "terms";
        $wp_taxonomymeta = $wpdb->prefix . "termmeta";
        $query = "
			SELECT * FROM $wp_terms t
			INNER JOIN $wp_term_taxonomy tt ON t.term_id = tt.term_id
				WHERE tt.parent = {$data['property_id']} and t.name LIKE '%{$data['term']}%'  ORDER BY tt.count DESC,t.name ASC  
		";
        $result = $wpdb->get_results($query);
        if ($result) {
            foreach ($result as $term) {
                $json[] = array('value' => $term->term_id, 'label' => $term->name, 'is_term' => true);
            }
        }
        return json_encode($json);
    }

    /**
     * function get_objects_by_property_json()
     * @param int Os dados vindo do formulario
     * @return json com o id e o nome de cada objeto
     * @author Eduardo Humberto
     */
    public function get_data_by_property_json($data) {
        global $wpdb;
        $wp_posts = $wpdb->prefix . "posts";
        $wp_postmeta = $wpdb->prefix . "postmeta";
        $query = "
                        SELECT pm.* FROM $wp_posts p
                        INNER JOIN $wp_postmeta pm ON p.ID = pm.post_id    
                        WHERE pm.meta_key like 'socialdb_property_{$data['property_id']}' and pm.meta_value LIKE '%{$data['term']}%'
                ";
        $result = $wpdb->get_results($query);
        if ($result) {
            foreach ($result as $object) {
                $json[] = array('value' => $object->meta_value, 'label' => $object->meta_value);
            }
        }
        return json_encode($json);
    }

    /**
     * function get_object_tags_id()
     * @param int O id do objeto
     * @param boolean $is_dynatree se for para a montagem do o dynatree
     * @return array(int) Os term_id das tags do objeto
     * @author Eduardo Humberto
     */
    public function get_object_tags_id($object_id, $is_dynatree = false) {
        $tags_id = array();
        $tags = wp_get_object_terms($object_id, 'socialdb_tag_type');
        if (is_array($tags) && !empty($tags)) {
            foreach ($tags as $tag) {
                if ($is_dynatree) {
                    $tags_id[] = $tag->term_id . '_tag';
                } else {
                    $tags_id[] = $tag->term_id;
                }
            }
        }
        return $tags_id;
    }

    /**
     * function get_objects_by_property_json()
     * @param int Os dados vindo do formulario
     * @return json com o id e o nome de cada objeto
     * @author Eduardo Humberto
     */
    public function get_meta_by_id($id) {
        global $wpdb;
        $wp_postmeta = $wpdb->prefix . "postmeta";
        $query = "
                        SELECT pm.* FROM $wp_postmeta pm WHERE pm.meta_id = $id
                ";
        $result = $wpdb->get_results($query);
        if ($result && is_array($result)) {
            return $result[0]->meta_value;
        } else {
            return $result->meta_value;
        }
    }

    /**
     * function get_last_counter($property_id)
     * @param int O id da propriedade
     * @return int o valor em q esta o ponteiro da propr
     * @author Eduardo Humberto
     */
    public function get_last_counter($property_id) {
        global $wpdb;
        $order = array();
        $wp_posts = $wpdb->prefix . "posts";
        $wp_postmeta = $wpdb->prefix . "postmeta";
        $query = "
                        SELECT pm.meta_value FROM $wp_posts p
                        INNER JOIN $wp_postmeta pm ON p.ID = pm.post_id    
                        WHERE p.post_status LIKE 'publish' AND  pm.meta_key like 'socialdb_property_{$property_id}'
                ";
        $result = $wpdb->get_results($query);
        if ($result && !empty($result)) {
            foreach ($result as $object) {
                $order[] = $object->meta_value;
            }
            sort($order);
            return end($order);
        } else {
            return 0;
        }
    }

    /**
     * function is_repository_property($category_property_id)
     * @param int $category_property_id O id da categoria raiz a ser verificada 
     * @return bolean
     * @author: Eduardo Humberto 
     */
    public function is_repository_property($category_property_id) {
        if ($category_property_id == get_term_by('slug', 'socialdb_category', 'socialdb_category_type')->term_id) {
            return true;
        } else {
            return false;
        }
    }

    /** function get_parent_properties($collection_id,$all_properties_id) 
     * @param int  $term_id O id da categoria raiz da colecao atual 
     * @param array $all_properties_id O array com ids das propriedades que ja foram encontradas ate o meomento (argumento para a recursao)
     * @param int $category_root_id O id da categoria raiz ( Sim,  o mesmo argumento, porem este na recursao fica constante)
     * @return array  Com os ids das propriedades encontradas ou chama recursivamente a funcao ate encontrar a categoria root 
     * @author: Eduardo 

      public function get_parent_properties($term_id, $all_properties_id) {
      $term = get_term_by('id', $term_id, 'socialdb_category_type');
      if ($term_id != 0 && $term_id) {
      $properties = [];
      $properties_raw = get_term_meta($term->term_id, 'socialdb_category_property_id');
      if (is_array($properties_raw)) {
      foreach ($properties_raw as $property) {
      if ($property && $property != '') {
      $properties[] = $property;
      }
      }
      }
      if ($properties && isset($properties[0]) && $properties[0] != '') {
      $all_properties_id = array_merge($all_properties_id, $properties);
      }
      return $this->get_parent_properties($term->parent, $all_properties_id);
      } else {
      return $all_properties_id;
      }
      } */
    public function get_parent_properties($term_id, $all_properties_id, $category_root_id) {
        $term = get_term_by('id', $term_id, 'socialdb_category_type');
        // se nao for a categoria raiz
        if ($term_id != 0 && $term_id) {
            $properties = [];
            $properties_raw = get_term_meta($term->term_id, 'socialdb_category_property_id');
            if (is_array($properties_raw)) {
                foreach ($properties_raw as $property) {
                    if (!isset($category_root_id)) {
                        $category_root_id = $term->term_id;
                    }

                    if (is_null($category_root_id)) {
                        $category_root_id = $term->term_id;
                    }

                    if ($property && $property != '' && !$this->verify_default_property_term($property, $category_root_id)) {
                        $properties[] = $property;
                    }
                }
            }
            if ($properties && isset($properties[0]) && $properties[0] != '') {
                $all_properties_id = array_merge($all_properties_id, $properties);
            }
            return $this->get_parent_properties($term->parent, $all_properties_id, $category_root_id);
        } else {
            return $all_properties_id;
        }
    }

    /** function verify_default_property_term($collection_id,$all_properties_id) 
     * @param int  $property O id da propriedade a ser verificada
     * @param int $category_root_id O id da categoria raiz da colecao atual
     * @param int $collection_id O id da colecao atual
     * @return boolean  FALSE se a propriedade nao eh a default de outra colecao, TRUE se for default de outra colecao
     * @author: Eduardo */
    public function verify_default_property_term($property, $category_root_id) {
        $created_category = get_term_meta($property, 'socialdb_property_created_category', true); // adiciono a categoria de onde partiu esta propriedade
        if ($created_category != $category_root_id) {
            $collection = $this->get_collection_by_category_root($created_category);
            $collection_id = ( isset($collection[0]->ID) ? $collection[0]->ID : '');
            $slug = sanitize_title(remove_accent(__('Categories', 'tainacan'))) . "_collection" . $collection_id;
            $collection_default_term = get_term_by('slug', $slug, 'socialdb_property_type');
            if ($collection_default_term && $collection_default_term->term_id == $property) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * function get_collection_category_root($collection_id)
     * @param int $collection_id
     * @return int With O term_id da categoria root da colecao.
     * 
     * metodo responsavel em retornar a categoria root da colecao
     * Autor: Eduardo Humberto 
     */
    public function get_collection_category_root($collection_id) {
        return get_post_meta($collection_id, 'socialdb_collection_object_type', true);
    }

    /**
     * function get_category_children($parent_id,$field)
     * @param int Os dados vindo do formulario
     * @return json com o id e o nome de cada objeto
     * @author Eduardo Humberto
     */
    public function get_category_children($parent_id) {
        global $wpdb;
        $data = [];
        $wp_term_taxonomy = $wpdb->prefix . "term_taxonomy";
        $wp_terms = $wpdb->prefix . "terms";
        $query = "
			SELECT t.term_id FROM $wp_terms t
			INNER JOIN $wp_term_taxonomy tt ON t.term_id = tt.term_id
				WHERE tt.parent = {$parent_id}  ORDER BY tt.count DESC,t.name ASC  
		";
        $result = $wpdb->get_results($query);
        if ($result && !empty($result)) {
            foreach ($result as $term) {
                $data[] = $term->term_id;
            }
        }
        return $data;
    }

    function download_send_headers($filename) {
        // disable caching
        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2020 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");

        // force download  
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");

        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
    }

    /**
     * @signature - get_privacity($collection_id)
     * @param int $collection_id 
     * @return O tipo de privacidade da colecao
     * @description - 
     * @author: Eduardo 
     */
    public function get_privacity($collection_id) {
        $get_privacity = wp_get_object_terms($collection_id, 'socialdb_collection_type');
        if ($get_privacity) {
            foreach ($get_privacity as $privacity) {
                $privacity_name = $privacity->name;
            }
        }
        return $privacity_name;
    }

    /**
     * @signature - search_in_array($field,array $array,$search)
     * @param int $field 
     * @param array $array 
     * @param string $search 
     * @return O tipo de privacidade da colecao
     * @description - 
     * @author: Eduardo 
     */
    public function search_in_array($field, array $array, $search) {
        $matches = [];
        foreach ($array as $values) {
            if (strpos(strtolower($values[$field]), strtolower($search)) !== false) {
                $matches[] = $values;
            }
        }
        return $matches;
    }

    /**
     * @signature - get_privacity($collection_id)
     * @param string $dir  O caminho a ser criado o arquivo
     * @param string $xml O xml do arquivo a ser criado
     * @return void Apenas cria o arquivo
     * @description - 
     * @author: Eduardo 
     */
    public function create_xml_file($dir, $xml) {
        ob_clean();
        $df = fopen($dir, 'w');
        fwrite($df, $xml);
        fclose($df);
    }

    public function create_zip_by_folder($folder, $from = '/package/') {
        $rootPath = realpath($folder);
        // Initialize archive object
        $zip = new ZipArchive();
        $zip->open($rootPath . '/package.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
        // Create recursive directory iterator
        /** @var SplFileInfo[] $files */
        $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($folder . $from)
        );
        foreach ($files as $name => $file) {
            // Skip directories (they would be added automatically)
            if (!$file->isDir()) {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);

                // Add current file to archive
                $zip->addFile($filePath, $relativePath);
            }
        }
        // Zip archive will be created only after closing object
        $zip->close();
    }

    /**
     * 
     */
    public function recursiveRemoveDirectory($directory) {
        if (is_dir($directory)) {
            foreach (glob("{$directory}/*") as $file) {
                if (is_dir($file)) {
                    $this->recursiveRemoveDirectory($file);
                } else {
                    unlink($file);
                }
            }
            rmdir($directory);
        }
    }

    /** function add_hierarchy($xml,$collection_id,$parent = 0) 
     * @param int  $collection_id O id da colecao
     * @param array $categories O array com ids das propriedades que ja foram encontradas ate o meomento
     * @return array  Os ids pas propriedades encontradas apenas nas facetas 
     * @author: Eduardo */
    public function add_hierarchy($xml, $collection_id, $parent = 0) {
        ini_set('max_execution_time', '0');
        if ($xml) {
            $attributes = $xml->attributes();
            if (isset($attributes['label']) && !empty($attributes['label'])) {
                $array = socialdb_insert_term(trim($attributes['label']), 'socialdb_category_type', $parent, $this->generate_slug(trim($attributes['label']), $collection_id));
                add_term_meta($array['term_id'], 'socialdb_imported_id', (string) $attributes['id']);
                //if($parent == $this->get_category_root()){
                //$this->add_facet($array['term_id'], $collection_id);
                //}
                $parent = $array['term_id'];
            }
            $has_children = $xml->count();
            if ($has_children > 0) {
                foreach ($xml as $value) {
                    $this->add_hierarchy($value, $collection_id, $parent);
                }
            }
            $data['title'] = __('Success', 'tainacan');
            $data['msg'] = __('All categories imported successfully', 'tainacan');
            $data['type'] = 'success';
        } else {
            $data = array();
            $data['title'] = __('Error', 'tainacan');
            $data['msg'] = __('Xml incompatible', 'tainacan');
            $data['type'] = 'error';
        }
        return $data;
    }

    /** function add_hierarchy_importing_collection($xml,$collection_id,$parent = 0) 
     * @param 
     * @param array 
     * @return array 
     * @author: Eduardo */
    public function add_hierarchy_importing_collection($xml, $collection_id, $parent = 0, &$all_ids = []) {
        if ($xml) {
            $attributes = $xml->attributes();
            if (isset($attributes['label']) && !empty($attributes['label'])) {
                $array = wp_insert_term(trim($attributes['label']), 'socialdb_category_type', array('parent' => $parent,
                    'slug' => $this->generate_slug(trim($attributes['label']), 0)));
                add_term_meta($array['term_id'], 'socialdb_imported_id', (string) $attributes['id']);
                add_term_meta($array['term_id'], 'socialdb_category_owner', get_current_user_id());
                //if($parent == $this->get_category_root()){
                //$this->add_facet($array['term_id'], $collection_id);
                //}
                $parent = $array['term_id'];
                //if(!in_array($array['term_id'], $all_ids)){
                //  $all_ids[] = $array['term_id'];
                //}
            }
            $has_children = $xml->count();
            if ($has_children > 0) {
                foreach ($xml as $value) {
                    $this->add_hierarchy_importing_collection($value, $collection_id, $parent, $all_ids);
                }
            }
            $data['title'] = __('Success', 'tainacan');
            $data['msg'] = __('All categories imported successfully', 'tainacan');
            $data['type'] = 'success';
            $data['ids'] = $all_ids;
        } else {
            $data = array();
            $data['title'] = __('Error', 'tainacan');
            $data['msg'] = __('Xml incompatible', 'tainacan');
            $data['type'] = 'error';
            $data['ids'] = $all_ids;
        }
        return $data;
    }

    /**
     * function get_term_imported_id($imported_id)
     * @param int $imported_id
     * @return int com ID do termo que foi criado.
     * 
     * metodo responsavel em retornar o id do termo importado
     * @autor: Eduardo Humberto 
     */
    public function get_term_imported_id($imported_id) {
        global $wpdb;
        $wp_taxonomymeta = $wpdb->prefix . "termmeta";
        $query = "
            SELECT * FROM $wp_taxonomymeta t
                    WHERE t.meta_key like 'socialdb_imported_id' AND t.meta_value = {$imported_id}
                ";
        $term = $wpdb->get_results($query);
        if ($term && is_array($term)) {
            $last_index = count($term);
            return $term[$last_index - 1]->term_id;
        } elseif ($term && is_object($term)) {
            return $term->term_id;
        }
        return false;
    }

    /**
     * function get_post_imported_id($imported_id)
     * @param int $imported_id
     * @return int com ID do termo que foi criado.
     * 
     * metodo responsavel em retornar o id do termo importado
     * @autor: Eduardo Humberto 
     */
    public function get_post_imported_id($imported_id) {
        global $wpdb;
        if ($imported_id) {
            $wp_postmeta = $wpdb->prefix . "postmeta";
            $query = "
                SELECT * FROM $wp_postmeta p
                        WHERE p.meta_key like 'socialdb_imported_id' AND p.meta_value = '{$imported_id}'
                    ";
            $term = $wpdb->get_results($query);
            if ($term && is_array($term)) {
                $last_index = count($term);
                return $term[$last_index - 1]->post_id;
            } elseif ($term && !is_object($term)) {
                return $term->post_id;
            }
        }
        return false;
    }

    /**
     * function get_post_by_title($title)
     * @param string $title
     * @return int com ID do post ou false se nao existir.
     * 
     * metodo responsavel em retornar o id do post pelo seu titulo
     * @autor: Eduardo Humberto 
     */
    public function get_post_by_title($title, $collection_id, $type) {
        global $wpdb;
        $query = "SELECT * FROM $wpdb->posts WHERE post_title = '$title'";
        $result = $wpdb->get_results($query);
        if ($result && is_array($result)) {
            foreach ($result as $post) {
                $meta = get_post_meta($post->ID, 'socialdb_channel_' . $type . '_collection_id', true);

                if ($collection_id == $meta) {
                    return $post->ID;
                }
            }
            return false;
        } elseif ($result && isset($result->ID)) {
            return $post->ID;
        } else {
            return false;
        }
    }

    /**
     * funcao que insere o postmeta e retorna o id gerado mesmo argumentos
     * e opcionais do postmeta
     */
    public function sdb_add_post_meta($post_id, $key, $value, $is_single = false) {
        global $wpdb;
        add_post_meta($post_id, $key, $value, $is_single);
        return $wpdb->insert_id;
    }

    /**
     * funcao que busca o postmeta e retorna seu valor
     */
    public function sdb_get_post_meta($meta_id) {
        global $wpdb;
        $query = "SELECT * FROM $wpdb->postmeta WHERE meta_id = $meta_id";
        $result = $wpdb->get_results($query);
        if ($result && is_array($result)) {
            return $result[0];
        } elseif ($result && isset($result->ID)) {
            return $result;
        } else {
            return false;
        }
    }

    /**
     * funcao que atualizo o postmeta pelo seu id
     */
    public function sdb_update_post_meta($meta_id, $value) {
        global $wpdb;
        $query = "UPDATE $wpdb->postmeta SET meta_value = '$value' WHERE meta_id = $meta_id";
        $result = $wpdb->get_results($query);
        return $result;
    }

    /**
     * funcao que deleta o postmeta pelo seu id
     */
    public function sdb_delete_post_meta($meta_id) {
        global $wpdb;
        $query = "DELETE FROM $wpdb->postmeta WHERE meta_id = $meta_id";
        $result = $wpdb->get_results($query);
        return $result;
    }

    /**
     * function get_all_collections($user_id)
     * @return array(wp_post) as colecoes
     * @ metodo responsavel em retornar as colecoes deste repositorio
     * @author: Eduardo Humberto 
     */
    public function get_all_collections() {
        global $wpdb;
        $wp_posts = $wpdb->prefix . "posts";
        $query = "
                    SELECT * FROM $wp_posts p
                    WHERE p.post_type like 'socialdb_collection' AND p.post_status LIKE 'publish'
                    order by p.post_title
            ";
        $result = $wpdb->get_results($query);
        if ($result && is_array($result) && count($result) > 0) {
            return $result;
        } else {
            return array();
        }
    }

    /**
     * Metodo responsavel em gerar o link para buscar os individuos que possuem
     * esta categoria
     * 
     * @signature function get_link_individuals($id,$type)
     * @param int $id O id do termo ou tag
     * @param string $type (optional) O key do metadado do item
     * @return json Retorna um objeto json com a url a ser utilizada
     * 
     */
    public function get_link_individuals($id, $collection_id, $type = 'category') {
        $recover_data = [];
        $recover_data['collection_id'] = $collection_id;
        $recover_data['category_root_id'] = $this->get_category_root_of($collection_id);
        $recover_data['order'] = 'DESC';
        $recover_data['orderby'] = 'date';
        $recover_data['is_filter'] = '1';
        if ($type == 'category' && $id != $recover_data['category_root_id']) {
            $key = $this->get_category_facet_parent($id, $recover_data['collection_id']);
            $recover_data['facets'][$key][] = $id;
        } elseif ($type == 'tag') {
            $recover_data['tags'][] = $id;
        }
        $permalink = get_the_permalink($recover_data['collection_id']) . '?' . http_build_query($recover_data);
        return json_encode(['url' => $permalink]);
    }

    /**
     * Metodo responsavel em retornar todas as propriedades de uma colecao, nao importando
     * se sao propriedades ou sub-propriedades
     * 
     * 
     * @param array &$array_all_properties (referencia) Deve ser passado um array vazio onde serao inseridos os ids das propriedades
     * @param int $term_to_avoid O id da categorias de onde esta sendo criado a propriedade
     * @param array $children O array de filhos (recursao), deve se iniciar com as facetas da colecao
     * @return void
     */
    public function get_collection_properties(&$array_all_properties, $term_to_avoid, $children) {
        foreach ($children as $child_id) {
            if (is_object($child_id)) {
                $child_id = $child_id->term_id;
            }
            $term = get_term_by('id', $child_id, 'socialdb_category_type');
            if ($term && $term->term_id != $term_to_avoid) {
                $children_of_child = $this->getChildren($term->term_id);
                //se categorias existir categorias filhas filhas
                if (!empty($children_of_child) && is_array($children_of_child)) {
                    $this->get_collection_properties($array_all_properties, $term_to_avoid, $children_of_child);
                }
                $properties = get_term_meta($term->term_id, 'socialdb_category_property_id');
                if ($properties && !empty($properties)) {
                    foreach ($properties as $id) {
                        if (trim($id) != '') {
                            $array_all_properties[] = $id;
                        }
                    }
                }
            }
        }
    }

    /**
     * 
     * Metodo responsavel em criar ou atualizar o metadado do item 
     * <i>socialdb_object_commom_values</i> e <i>socialdb_object_commom_index</i> 
     * os quais sao responsaveis em guardar todos valores do item que estao gravados plain text
     * de forma a facilitar uma busca centralizada
     * 
     * @signature function set_common_field_values($index,$value)
     * @param int $item_id O id do item
     * @param string $index O key do metadado do item
     * @param string $value O valor ou valores do
     * @param string $type (optional) O tipo de dado que vai ser inserido (Default: Metadado de dado)
     * @return boolean True para caso a adicao ou atualizacao for feita com 
     * sucesso ou FALSE caso ocorra alguma falha
     */
    public function set_common_field_values($item_id, $index, $value, $type = '') {
        if (!$index || trim($index) == '') {
            return false;
        }
        $indexes = get_post_meta($item_id, 'socialdb_object_commom_index', true);
        $values = get_post_meta($item_id, 'socialdb_object_commom_values');
        if (is_array($value)):
            $this->set_common_field_values_array($item_id, $indexes, $index, $value, $type);
        else:
            if ($indexes && $values) {
                $indexes = unserialize($indexes);
                if (isset($indexes[$index])) {
                    $position = $indexes[$index];
                    $this->sdb_update_post_meta($position, $value);
                } else {
                    $position = $this->sdb_add_post_meta($item_id, 'socialdb_object_commom_values', $value);
                    $indexes[$index] = $position;
                    update_post_meta($item_id, 'socialdb_object_commom_index', serialize($indexes));
                }
            } else {
                $indexes = [];
                $position = $this->sdb_add_post_meta($item_id, 'socialdb_object_commom_values', $value);
                $indexes[$index] = $position;
                //update_post_meta($item_id, 'socialdb_object_commom_values', implode('@@;', $values)) ;
                update_post_meta($item_id, 'socialdb_object_commom_index', serialize($indexes));
            }
        endif;
    }

    /**
     * funcao que insere os valores de metadados array (term e object) no metadado comum com todos os valores
     * 
     * @param int $item_id O id do item que sendo salvo os dados
     * @param array $indexes O array de indices com os metadados
     * @param string $index O indice da propriedade
     * @param array $value_array Os ids do item que estao fazendo o relacionamento
     * @param string $type Os ids do item que estao fazendo o relacionamento
     * @return void
     */
    public function set_common_field_values_array($item_id, $indexes, $index, $value_array, $type) {
        if ($indexes) {//verifico se ja existe os postmeta com o array de indices
            $indexes = unserialize($indexes); // desserializo para um array comum
            if (isset($indexes[$index]) && is_array($indexes[$index])) {
                $positions = $indexes[$index];
                unset($indexes[$index]);
                foreach ($positions as $position) {
                    $this->sdb_delete_post_meta($position);
                }
                //adicionando os valores novamente
                $indexes = $this->add_array_commom_field($item_id, $indexes, $index, $value_array, $type);
                //atualizando o array com indices
                update_post_meta($item_id, 'socialdb_object_commom_index', serialize($indexes));
            } else {
                $indexes = $this->add_array_commom_field($item_id, $indexes, $index, $value_array, $type);
                //atualizando o array com indices
                update_post_meta($item_id, 'socialdb_object_commom_index', serialize($indexes));
            }
        } else {
            $indexes = $this->add_array_commom_field($item_id, [], $index, $value_array, $type);
            add_post_meta($item_id, 'socialdb_object_commom_index', serialize($indexes));
        }
    }

    /**
     *  funcao que adiciona o metadado de valores comuns no banco de dados
     * 
     * @param int $item_id O id do item que sendo salvo os dados
     * @param array $indexes O array de indices com os metadados
     * @param string $index O indice da propriedade
     * @param array $value_array Os ids do item que estao fazendo o relacionamento
     * @param string $type Os ids do item que estao fazendo o relacionamento
     * @return type
     */
    public function add_array_commom_field($item_id, $indexes, $index, $value_array, $type) {
        foreach ($value_array as $value) {
            if ($type == 'term') {
                $term = get_term_by('id', $value, 'socialdb_category_type');
                $item = ($term) ? $term : get_term_by('id', $value, 'socialdb_tag_type');
                $position = $this->sdb_add_post_meta($item_id, 'socialdb_object_commom_values', $item->name);
            } elseif (is_numeric($value)) {
                $item = get_post($value);
                $position = (is_object($item)) ? $this->sdb_add_post_meta($item_id, 'socialdb_object_commom_values', $item->post_title) : $this->sdb_add_post_meta($item_id, 'socialdb_object_commom_values', '');
            }
            if (isset($position) && isset($indexes[$index]) && is_array($indexes[$index])):
                $indexes[$index][] = $position;
            elseif (isset($indexes[$index])):
                $indexes[$index] = array($position, $indexes[$index]);

            endif;
        }
        return $indexes;
    }

    /**
     * funcao que apenas adiciona valores no array bidimensional de valores comuns
     * para <b> Metadados de Termo <b>
     * 
     * @param type $item_id
     * @param type $index
     * @param type $value
     */
    public function concatenate_commom_field_value($item_id, $index, $value) {
        $indexes = get_post_meta($item_id, 'socialdb_object_commom_index', true);
        $indexes = unserialize($indexes); // desserializo para um array comum
        $term = get_term_by('id', $value, 'socialdb_category_type');
        $item = ($term) ? $term : get_term_by('id', $value, 'socialdb_tag_type');
        $all_metas_values = get_post_meta($item_id, 'socialdb_object_commom_values');
        if (!$all_metas_values || (is_array($all_metas_values) && !in_array($item->name, $all_metas_values))) {
            $position = $this->sdb_add_post_meta($item_id, 'socialdb_object_commom_values', $item->name);
            $indexes[$index][] = $position;
            update_post_meta($item_id, 'socialdb_object_commom_index', serialize($indexes));
        }
    }

    /**
     * funcao que apenas remove uma propriedade especifica no array bidimensional de valores comuns
     * para <b> Metadados de Termo <b>
     * 
     * @param type $item_id
     * @param type $index
     * @param type $value
     */
    public function delete_commom_field_value($item_id, $index, $value) {
        $indexes = get_post_meta($item_id, 'socialdb_object_commom_index', true);
        $indexes = unserialize($indexes); // desserializo para um array comum
        $term = get_term_by('id', $value, 'socialdb_category_type');
        $item = ($term) ? $term : get_term_by('id', $value, 'socialdb_tag_type');
        if (isset($indexes[$index]))
            unset($indexes[$index]);
        delete_post_meta($item_id, 'socialdb_object_commom_values', $item->name);
        update_post_meta($item_id, 'socialdb_object_commom_index', serialize($indexes));
    }

    /**
     * funcao que apenas adiciona valores no array bidimensional de valores comuns
     * para <b> Metadados de Objeto<b>
     * 
     * @param type $item_id
     * @param type $index
     * @param type $value
     */
    public function concatenate_commom_field_value_object($item_id, $index, $value) {
        $indexes = get_post_meta($item_id, 'socialdb_object_commom_index', true);
        $indexes = unserialize($indexes); // desserializo para um array comum
        $item = get_post($value);
        $position = $this->sdb_add_post_meta($item_id, 'socialdb_object_commom_values', $item->post_title);
        if (!isset($indexes[$index])) {
            $indexes[$index] = [$position];
        } elseif (!is_array($indexes[$index])) {
            $indexes[$index] = [$indexes[$index]];
            $indexes[$index][] = $position;
        } else {
            $indexes[$index][] = $position;
        }
        update_post_meta($item_id, 'socialdb_object_commom_index', serialize($indexes));
    }

}
