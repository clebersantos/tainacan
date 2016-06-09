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

    public function get_property_data_types() {
        return $this->metadata_types = ['text' => __('Text', 'tainacan'),
            'textarea' => __('Long text', 'tainacan'),
            'date' => __('Date', 'tainacan'),
            'numeric' => __('Numeric', 'tainacan'),
            'autoincrement' => __('Auto-Increment', 'tainacan')];
    }

    public function get_default_metadata() {
        return $this->default_metadata = [
            //'socialdb_object_dc_type' => 'Type',
            //'socialdb_object_from' => 'Format',
            'thumbnail_id' => 'Item Thumbnail',
            'post_content' => 'Item Description',
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

    public function get_type_default_widget($type) {
        if ("text" == $type) {
            return "<option value='tree'>" . __('Tree', 'tainacan') . "</option>";
        } else if ("textarea" == $type) {
            return "<option value='searchbox'>" . __('Search box with autocomplete', 'tainacan') . "</option>";
        } else {
            return "<option value='from_to'>" . __('From/To', 'tainacan') . "</option>";
        }
    }

    public function render_tree_colors() {
        ?>
        <div id="color_field_property_search">
            <h5 style="color: black"><strong><?php _e('Set the facet color', 'tainacan'); ?></strong></h5>
            <div class="form-group" style="padding-left: 5px">
                <?php
                for ($i = 1; $i < 14; $i++) {
                    echo '<label class="radio-inline"> <input type="radio" class="color_property" name="color_facet" id="color_property' . $i . '" value="color_property' . $i . '" ';
                    echo '><img src="' . get_template_directory_uri() . '/libraries/images/cor' . $i . '.png">  </label>';
                };
                ?>
            </div>
        </div>
    <?php
    }

    public function render_button_cardinality($property,$i) {
        if ($property['metas']['socialdb_property_data_cardinality'] && $property['metas']['socialdb_property_data_cardinality'] == 'n'):
            ?>
               <button type="button" 
                       id="button_property_<?php echo $property['id']; ?>_<?php echo $i; ?>"
                       onclick="show_fields_metadata_cardinality(<?php echo $property['id'] ?>,<?php echo $i ?>)" 
                       style="margin-top: 5px;<?php echo (is_array($property['metas']['value'])&&($i+1)<count($property['metas']['value']))? 'display:none':'' ?>" 
                       class="btn btn-primary btn-lg btn-xs btn-block">
                    <span class="glyphicon glyphicon-plus"></span><?php _e('Add field', 'tainacan') ?>
                </button>
            <?php
        endif;
    }
    
     public function render_cardinality_property($property,$is_data = 'false') {
        if ($property['metas']['socialdb_property_data_cardinality'] && $property['metas']['socialdb_property_data_cardinality'] == 'n'):
            return 50;
        else:
            return 1;
        endif;
    }

}
