<script>
    $(function () {
        $("#tainacan-breadcrumbs .current-config").text('<?php _e('Configurations', 'tainacan'); ?>');

        showModerationDays();
        // Adiciona borda nas entidades criadas na configuração
        $('#configuration .col-md-6:not([id])').css('border-bottom', '1px solid #e3e3e3');

        $('[data-toggle="tooltip"]').tooltip();

        if ($('#open_wizard').val() == 'true') {
            $('#btn_back_collection').hide();
            $('#submit_configuration').hide();
            $('#save_and_next').val('true');
        } else {
            // $('#MyWizard').hide();
            $('#config_categories_title').hide();
            $('#button_save_and_next').hide();
            $('#save_and_next').val('false');
        }

        if ($('#change_collection_images').val() == '1') {
            $('#collection_thumbnail').focus();
            $('#collection_thumbnail').trigger('click');
            $('#change_collection_images').val('');
        } else if ($('#change_collection_images').val() == '2') {
            $('#socialdb_collection_cover').focus();
            $('#socialdb_collection_cover').trigger('click');
            $('#change_collection_images').val('');
        }

        $('#add_watermark').click(function () {
            $("#uploadWatermark").toggle(this.checked);
        });

        //$('.combobox').combobox({bsVersion: '3'});
        var src = $('#src').val();
        // $('#my-wizard').wizard(); //wizard para navegacao
        $('#submit_form_edit_collection').submit(function (e) {
            var verify = $(this).serializeArray();
            if (verify[0].value.trim() === '') {
                showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('Please set a valid name', 'tainacan') ?>', 'info');
                return false;
            }
            else if ($("#verify_collection_name").val() !== 'block') {
                // $("#collection_content").val(CKEDITOR.instances.editor.getData());
                e.preventDefault();
                $.ajax({
                    url: src + '/controllers/collection/collection_controller.php',
                    type: 'POST',
                    data: new FormData(this),
                    processData: false,
                    contentType: false
                }).done(function (result) {
                    elem = jQuery.parseJSON(result);
                    showHeaderCollection(src);
                    show_most_participatory_authors(src);
                    $('#redirect_to_caegories').show();
                    showAlertGeneral('<?php _e('Success', 'tainacan') ?>', '<?php _e('Configuration saved successfully!', 'tainacan') ?>', 'success');
                    if (elem.save_and_next && elem.save_and_next == 'true') {
                        showPropertiesAndFilters('<?php echo get_template_directory_uri() ?>');
                        // setTimeout(function () { showPropertiesConfiguration(''); }, 2000);
                    } else {
                        console.log(elem.is_moderator);
                        if (elem.is_moderator) {
                            showCollectionConfiguration(src);
                        } else {
                            backToMainPage();
                        }
                    }
                });
                e.preventDefault();
                $('#configuration').focus();
            } else {
                $("#show_adv_config_link").hide('slow');
                $("#advanced_config").show('slow');
                $("#hide_adv_config_link").show('slow');
                $('#suggested_collection_name').focus();
                showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('Please set a valid address', 'tainacan') ?>', 'info');
                return false;
            }
        });
        //
        list_ordenation();
        list_collections_parent();
        //showCKEditor();
    });

    function showModerationDays() {
        if ($('#socialdb_collection_moderation_type').val() == 'democratico') {
            $('#div_moderation_days').fadeIn();
        } else {
            $('#div_moderation_days').fadeOut();
        }
    }

    function list_ordenation() {
        $.ajax({
            url: $('#src').val() + '/controllers/collection/collection_controller.php',
            type: 'POST',
            data: {operation: 'list_ordenation', collection_id: $("#collection_id").val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            if (elem.general_ordenation) {
                $("#collection_order").append("<optgroup label='<?php _e('General ordenation', 'tainacan') ?>'>");
                $.each(elem.general_ordenation, function (idx, general) {
                    if (general && general !== false) {
                        $("#collection_order").append("<option value='" + general.id + "' selected='selected' >" + general.name + "</option>");
                    }
                });
            }
            if (elem.property_data) {
                $("#collection_order").append("<optgroup label='<?php _e('Data properties', 'tainacan') ?>'>");
                $.each(elem.property_data, function (idx, data) {
                    if (data && data !== false) {
                        $("#collection_order").append("<option value='" + data.id + "' selected='selected' >" + data.name + "</option>");
                    }
                });
            }
            console.log(elem.rankings);
            if (elem.rankings) {
                $("#collection_order").append("<optgroup label='<?php _e('Rankings', 'tainacan') ?>'>");
                $.each(elem.rankings, function (idx, ranking) {
                    if (ranking && ranking !== false) {
                        $("#collection_order").append("<option value='" + ranking.id + "' selected='selected' >" + ranking.name + "</option>");
                    }
                });
            }
            if (elem.selected) {
                $("#collection_order").val(elem.selected);
            }
            $('.dropdown-toggle').dropdown();
        });
    }

    function autocomplete_moderators(collection_id) {
        $("#autocomplete_moderator").autocomplete({
            source: $('#src').val() + '/controllers/user/user_controller.php?operation=list_user&collection_id=' + collection_id,
            messages: {
                noResults: '',
                results: function () {
                }
            },
            minLength: 2,
            select: function (event, ui) {
                console.log(event);
                //$("#moderators_" + collection_id).html('');
                //$("#moderators_" + collection_id).val('');
                //var temp = $("#chosen-selected2 [value='" + ui.item.value + "']").val();
                var temp = $("#moderators_" + collection_id + " [value='" + ui.item.value + "']").val();
                if (typeof temp == "undefined") {
                    $("#moderators_" + collection_id).append("<option class='selected' value='" + ui.item.value + "' selected='selected' >" + ui.item.label + "</option>");

                }
                setTimeout(function () {
                    $("#autocomplete_moderator").val('');
                }, 100);
            }
        });
    }
    function clear_select_moderators(e) {
        $('option:selected', e).remove();
        //$('.chosen-selected2 option').prop('selected', 'selected');
    }
    function verify_name_collection() {
        $.ajax({
            url: $("#src").val() + '/controllers/collection/collection_controller.php',
            type: 'POST',
            data: {operation: 'verify_name_collection',
                suggested_collection_name: $('#suggested_collection_name').val(),
                collection_id: $("#collection_id").val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            if ($('#suggested_collection_name').val().trim() === '' || (elem.exists !== false && $('#initial_address').val() !== $('#suggested_collection_name').val())) {
                $("#verify_collection_name").val('block');
                $("#collection_name_success").hide('slow');
                $("#collection_name_error").show('slow');
                //$("#collection_name_error").delay(5000);
                //$("#collection_name_error").hide('slow');
            } else {
                $("#verify_collection_name").val('allow');
                $("#collection_name_error").hide('slow')
                $("#collection_name_success").show('slow');
                //$("#collection_name_success").delay(5000);
                //$("#collection_name_success").hide('slow');
            }
        });
    }
    function list_collections_parent() {
        $.ajax({
            url: $('#src').val() + '/controllers/collection/collection_controller.php',
            type: 'POST',
            data: {operation: 'list_collections_parent', collection_id: $("#collection_id").val()}
        }).done(function (result) {
            //$("#socialdb_collection_parent").append("<option value='' selected='selected' >Selecione..</option>");
            $("#socialdb_collection_parent").append("<option value='collection_root' ><?php _e('Collection root', 'tainacan') ?></option>");
            elem = jQuery.parseJSON(result);
            generate_select_list_collections_parent(elem.children, '&nbsp;&nbsp;');
            // $('#socialdb_collection_parent').combobox({bsVersion: '3'});
            //console.log($("#selected_parent_collection").val());
            if ($("#selected_parent_collection").val() !== '') {
                // $("[name=socialdb_collection_parent").val($("#selected_parent_collection").val());
                $("#socialdb_collection_parent").val($("#selected_parent_collection").val());
                // $('.combobox').val();
            }
        });
    }

    function generate_select_list_collections_parent(json, deep) {
        var deep_level;
        if (json.length > 0) {
            $.each(json, function (idx, collection) {
                if (collection && collection !== false) {
                    $("#socialdb_collection_parent").append("<option value='" + collection.id + "' >" + deep + "" + collection.name + "</option>");
                    if (collection.children.length > 0) {
                        deep_level = deep + '&nbsp;&nbsp;&nbsp;';
                        generate_select_list_collections_parent(collection.children, deep_level);
                    }
                }
            });
        }
    }

    function showAdvancedConfig() {
        $("#show_adv_config_link").hide('slow');
        $("#advanced_config").show('slow');
        $("#hide_adv_config_link").show('slow');
    }

    function hideAdvancedConfig() {
        $("#advanced_config").hide('slow');
        $("#hide_adv_config_link").hide('slow');
        $("#show_adv_config_link").show('slow');
    }

    function add_img_to_post(img_url, collection_id) {
        $.ajax({
            url: $('#src').val() + '/controllers/collection/collection_controller.php',
            type: 'POST',
            data: {operation: 'add_img_to_post', igm_url: img_url, collection_id: collection_id}
        });
    }

    function get_tainacan_base_url() {
        return '<?php echo get_template_directory_uri(); ?>';
    }

    var cover_img_options = {
        uploadUrl: '<?php echo get_stylesheet_directory_uri() ?>' + '/views/collection/upload_file.php',
        cropUrl: '<?php echo get_stylesheet_directory_uri() ?>' + '/views/collection/crop_file.php',
        imgEyecandy: true,
        imgEyecandyOpacity: 0.1,
        modal: true,
        loaderHtml: '<div class="loader bubblingG"><span id="bubblingG_1"></span><span id="bubblingG_2"></span><span id="bubblingG_3"></span></div>',
        onAfterImgCrop: function () {
            var cropped = $('img.croppedImg').attr('src');
            var collection_id = $("#collection_id").val();
            $.ajax({
                url: $('#src').val() + '/controllers/collection/collection_controller.php',
                data: {operation: 'set_collection_cover_img', img_url: cropped, collection_id: collection_id}
            });
        },
        onError: function () {
            alert('Tente novamente mais tarde!');
        }
    }
    var croppicContainer = new Croppic('collection_cover_image', cover_img_options);

    function show_edit_cover() {
        $("#edit_cover_container").show();
    }

</script>