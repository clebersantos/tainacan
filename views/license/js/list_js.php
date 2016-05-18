<script>
    var src = $('#src').val();
    var collection_id = $('#collection_id_license').val();
    $(function () {
        var src = $('#src').val();
        listStandartLicenses();

        $('#formAddLicense').submit(function (e) {
            e.preventDefault();
            $.ajax({
                url: $("#src").val() + '/controllers/license/license_controller.php',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false
            }).done(function (result) {
                elem = jQuery.parseJSON(result);
                showAlertGeneral(elem.title, elem.msg, elem.type);
                showLicensesConfiguration($("#src").val());

                $("#addLicenseOperation").val('add_repository_license');
                $("#editLicenseId").val('');
                $("#add_license_name").val('');
                $("#add_license_url").val('');
                $("#add_license_description").val('');
            });
        });
    });

    function listStandartLicenses() {
        var src = $('#src').val();

        $.ajax({
            url: src + '/controllers/license/license_controller.php',
            type: 'POST',
            data: {operation: 'listStandartLicenses', collection_id: collection_id},
            success: function (data) {
                if (data) {
                    var jsonObject = jQuery.parseJSON(data);
                    if (jsonObject && jsonObject != null) {
                        $("#list_licenses_content").html('');
                        $.each(jsonObject.licenses, function (id, object) {
                            $("#list_licenses_content").append("<tr><td>" + object.nome + "</td>" +
                                    "<td><input type='radio' name='standartLicense' id='radio" + object.id + "' value=" + object.id + " onclick='changeStandartLicense(this," + object.id + ");'/></td>" +
                                    "<td><input type='checkbox' name='enabledLicense[]' id='checkbox" + object.id + "' value=" + object.id + " onclick='changeEnabledLicense(this," + object.id + ");'/></td>" +
                                    "<td><a href='#' style='opacity:0.4'><span class='glyphicon glyphicon-pencil'></span></a></td>" +
                                    "<td><a href='#' style='opacity:0.4'><span class='glyphicon glyphicon-trash'></span></a></td>" +
                                    "</tr>");

                        });
                        listCustomLicenses();
                        $("#list_licenses_content").show();
                    }
                } // caso o controller retorne false
            }
        });// fim da inclusão
    }

    function listCustomLicenses() {
        var src = $('#src').val();

        $.ajax({
            url: src + '/controllers/license/license_controller.php',
            type: 'POST',
            data: {operation: 'listCustomLicenses', collection_id: collection_id},
            success: function (data) {
                if (data) {
                    var jsonObject = jQuery.parseJSON(data);
                    if (jsonObject && jsonObject != null) {
                        if (jsonObject.licenses) {
                            $.each(jsonObject.licenses, function (id, object) {
                                $("#list_licenses_content").append("<tr><td>" + object.nome + "</td>" +
                                        "<td><input type='radio' name='standartLicense' id='radio" + object.id + "' value=" + object.id + " onclick='changeStandartLicense(this," + object.id + ");'/></td>" +
                                        "<td><input type='checkbox' name='enabledLicense[]' id='checkbox" + object.id + "' value=" + object.id + " onclick='changeEnabledLicense(this," + object.id + ");'/></td>" +
                                        "<td><a onclick='editCustomLicense(" + object.id + ")' href='#formAddLicense'><span class='glyphicon glyphicon-pencil'></span></a></td>" +
                                        "<td><a onclick='deleteCustomLicense(" + object.id + ")' href='#formAddLicense'><span class='glyphicon glyphicon-trash'></span></a></td>" +
                                        "</tr>");
                            });
                        }
                        $('#radio' + jsonObject.pattern[0]).attr("checked", "checked");
                        $.each(jsonObject.enabled, function (id, object) {
                            $('#checkbox' + object).attr("checked", "checked");
                        });
                    }
                } // caso o controller retorne false
            }
        });// fim da inclusão 
    }

    function changeStandartLicense(form, id) {
        $.ajax({
            url: src + '/controllers/license/license_controller.php',
            type: 'POST',
            data: {operation: 'change_pattern_license', license_id: id, collection_id: collection_id},
            success: function (data) {
                if (data) {
                    elem = jQuery.parseJSON(data);
                    showAlertGeneral(elem.title, elem.msg, elem.type);
                } // caso o controller retorne false
            }
        });// fim da inclusão
    }

    function changeEnabledLicense(form, id) {
        $.ajax({
            url: src + '/controllers/license/license_controller.php',
            type: 'POST',
            data: {operation: 'change_enabled_license', license_id: id, collection_id: collection_id, form_data: $('#formEnabledLicenses').serialize()},
            success: function (data) {
                if (data) {
                    elem = jQuery.parseJSON(data);
                    showAlertGeneral(elem.title, elem.msg, elem.type);
                } // caso o controller retorne false
            }
        });// fim da inclusão
    }

    function editCustomLicense(id) {
        $.ajax({
            url: src + '/controllers/license/license_controller.php',
            type: 'POST',
            data: {operation: 'get_license_to_edit', license_id: id},
            success: function (data) {
                if (data) {
                    var jsonObject = jQuery.parseJSON(data);

                    $("#addLicenseOperation").val('edit_repository_license');
                    $("#editLicenseId").val(jsonObject.id);
                    $("#add_license_name").val(jsonObject.nome);
                    $("#add_license_url").val(jsonObject.url);
                    $("#add_license_description").val(jsonObject.description);
                } // caso o controller retorne false
            }
        });// fim da inclusão
    }

    function deleteCustomLicense(id) {
        swal({
            title: '<?php _e('Attention!', 'tainacan'); ?>',
            text: '<?php _e('Are you sure?', 'tainacan'); ?>',
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: 'btn-danger',
            closeOnConfirm: false,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    type: "POST",
                    url: $('#src').val() + '/controllers/license/license_controller.php',
                    data: {
                        operation: 'delete_custom_license',
                        license_id: id,
                        collection_id: collection_id
                    }
                }).done(function (result) {
                    elem = jQuery.parseJSON(result);
                    showAlertGeneral(elem.title, elem.msg, elem.type);
                    listStandartLicenses();
                });
            }
        });
    }
</script>
