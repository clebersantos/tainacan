<?php

class ViewHelper {

    public $metadata_types;
    public $default_metadata;
    public $special_metadata;

    public function get_metadata_types() {
        return $this->metadata_types = [
            'text' => __('Text', 'tainacan'),
            'textarea' => __('Long text', 'tainacan'),
            'date' => __('Date', 'tainacan'),
            'numeric' => __('Numeric', 'tainacan'),
            'autoincrement' => __('Auto-Increment', 'tainacan'),
            'relationship' => __('Relationship', 'tainacan'),
            'category' => __('Category', 'tainacan'),
            'voting' => __('Rankings', 'tainacan')
        ];
    }

    public function get_default_metadata() {
        return $this->default_metadata = [
            'socialdb_object_dc_type' => 'Type',
            'socialdb_object_from' => 'Format',
            'socialdb_object_dc_source' => 'Source',
            'socialdb_license_id' => 'License Type'
        ];
    }

    public function get_special_metadata() {
        return $this->special_metadata = ['relationship', 'category', 'voting'];
    }

    public function get_metadata_icon($metadata_type) {
        echo get_template_directory_uri() . "/libraries/images/icons/icon-$metadata_type.png";
    }
}