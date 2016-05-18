<script>
    $(function () {
        $('#validate_url_container').show('slow');
        listTableOAIPMHDC();
        var src = $('#src').val();
        $('#collection_import_id').val($('#collection_id').val());
        $('#collection_import_csv_id').val($('#collection_id').val());

        $('#click_oaipmhtab').click(function (e) {
            e.preventDefault()
            $(this).tab('show')
        });
        $('#click_csvtab').click(function (e) {
            e.preventDefault()
            $(this).tab('show');
            listTableCSV();
        });

    });


    function validate_url() {
        var url_base = $('#url_base_oai').val();
        if (url_base !== '') {
            $('#validate_url_container').hide();
            $('#loader_validacao').show();
            $.ajax({
                type: "POST",
                url: $('#src').val() + "/controllers/import/import_controller.php",
                data: {
                    url: $('#url_base_oai').val(),
                    sets: $('#sets_import_oaipmh').val(),
                    collection_id: $('#collection_id').val(),
                    operation: 'validate_url'
                }
            }).done(function (result) {
                console.log('success');
                $('#loader_validacao').hide('slow');
                $('#maping_container').html(result);

            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log('erro');
                $('#loader_validacao').hide();
                $('#validate_url_container').show('slow');
                showAlertGeneral('<?php _e('Atention', 'tainacan') ?>', '<?php _e('Server not found or not available', 'tainacan') ?>', '<?php _e('error') ?>');
            });
        } else {
            showAlertGeneral('<?php _e('Atention', 'tainacan') ?>', '<?php _e('URL base is empty', 'tainacan') ?>', '<?php _e('error') ?>');
        }
    }

    /* @name: listTableOAIPMHDC()
     * @description: cria dinamicamente uma tabela contendo
     * os identificadores de canais salvos no banco
     * 
     * @author: EDUARDO
     **/

    function listTableOAIPMHDC() {

        var src = $('#src').val();
        var collectionId = $('#collection_id').val();

        // $("#btn_identifiers_youtube_update").hide();
        //$("#btn_identifiers_youtube_cancel").hide();
        //$("#loader_videos").hide();
        $.ajax({
            url: src + "/controllers/mapping/mapping_controller.php",
            type: 'POST',
            data: {operation: 'list_mapping_oaipmh_dc',
                collection_id: collectionId
            },
            success: function (data) {
                $("#table_oaipmh_dc").html('');
                if (data !== '[]') {
                    var jsonObject = jQuery.parseJSON(data);
                    if (jsonObject && jsonObject != null && jsonObject.identifier) {
                        $("#table_oaipmh_dc").html('');
                        $.each(jsonObject.identifier, function (id, object) {
                            if (object.size) {
                                if (object.lastUpdate === false || object.lastUpdate === '') {
                                    $("#table_oaipmh_dc").append("<tr><td>" + object.name + "</td>" +
                                            "<td><a href='#' onclick=\"edit_mapping_oaipmh('" + object.name + "'," + object.id + "," + collectionId + ")\"><span class='glyphicon glyphicon-pencil'></span></a></td>" +
                                            "<td><a href='#' onclick='delete_mapping(" + object.id + "," + collectionId + " )'><span class='glyphicon glyphicon-trash'></span></a></td>" +
                                            "<td><a href='#' onclick=\"do_import(" + object.id + ",'" + object.name + "','" + object.token + "','begin','" + object.size + "','" + object.sets + "')\"><span class='glyphicon glyphicon-arrow-down'></span></a></td>" +
                                            "<td>--</td></tr>");
                                }
                                else {
                                    $("#table_oaipmh_dc").append("<tr><td>" + object.name + "</td>" +
                                            "<td><a href='#' onclick=\"edit_mapping_oaipmh('" + object.name + "'," + object.id + "," + collectionId + ")\"><span class='glyphicon glyphicon-pencil'></span></a></td>" +
                                            "<td><a href='#' onclick='delete_mapping(" + object.id + "," + collectionId + ")'><span class='glyphicon glyphicon-trash'></span></a></td>" +
                                            "<td><a href='#' ><span style='opacity:0.4' class='glyphicon glyphicon-arrow-down'>&nbsp;<?php _e('Imported in', 'tainacan') ?> " + object.lastUpdate + "</span></a></td>" +
                                            "<td><a href='#' onclick=\"is_harvesting(" + object.id + ",'" + object.is_harvesting + "')\">" + object.is_harvesting + "</a></td></tr>");
                                }
                            }
                        });
                        $("#table_oaipmh_dc").show();
                    }
                } // caso o controller retorne false
            }
        });// fim da inclusão de identificador youtube 
    }
    /* @name: listTableOAIPMHDC()
     * @description: cria dinamicamente uma tabela contendo
     * os identificadores de canais salvos no banco
     * 
     * @author: EDUARDO
     **/
    function delete_mapping(mapping_id, collection_id) {

        swal({
            title: '<?php _e('Attention', 'tainacan') ?>',
            text: '<?php _e('Are you sure to delete this mapping?', 'tainacan') ?>',
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
                    url: $('#src').val() + "/controllers/mapping/mapping_controller.php",
                    data: {
                        operation: 'delete_mapping',
                        mapping_id: mapping_id,
                        collection_id: collection_id
                    }
                }).done(function (result) {
                    elem_first = jQuery.parseJSON(result);
                    listTableOAIPMHDC();
                    listTableCSV();
                    showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);

                });
            }
        });
    }

    /* @name: listTableOAIPMHDC()
     * @description: cria dinamicamente uma tabela contendo
     * os identificadores de canais salvos no banco
     * 
     * @author: EDUARDO
     **/
    function is_harvesting(mapping_id, is_harvesting) {
        if (is_harvesting === 'disabled') {
            is_harvesting = 'enabled';
        } else {
            is_harvesting = 'disabled';
        }
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/mapping/mapping_controller.php",
            data: {
                collection_id: $('#collection_id').val(),
                operation: 'is_harvesting',
                mapping_id: mapping_id,
                is_harvesting: is_harvesting
            }
        }).done(function (result) {
            listTableOAIPMHDC();
            showAlertGeneral('<?php _e('Success', 'tainacan') ?>', '<?php _e('Mapping', 'tainacan') ?> ' + is_harvesting + ' successfuly', '<?php _e('success') ?>');

        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log('erro');
            $('#loader_validacao').hide();
            showAlertGeneral('<?php _e('Atention', 'tainacan') ?>', '<?php _e('Server not found or not available', 'tainacan') ?>', '<?php _e('error') ?>');
        });

    }

    /* @name: listTableOAIPMHDC()
     * @description: cria dinamicamente uma tabela contendo
     * os identificadores de canais salvos no banco
     * 
     * @author: EDUARDO
     **/
    function edit_mapping_oaipmh(url, mapping_id, collection_id) {
        var url_base = url;
        if (url_base !== '') {
            $('#validate_url_container').hide('slow');
            $('#loader_validacao').show();
            $.ajax({
                type: "POST",
                url: $('#src').val() + "/controllers/mapping/mapping_controller.php",
                data: {url: url_base,
                    collection_id: collection_id,
                    operation: 'edit_mapping_oaipmh',
                    mapping_id: mapping_id
                }
            }).done(function (result) {
                console.log('success');
                $('#loader_validacao').hide('slow');
                $('#maping_container').html(result);
                $('#maping_container').show();
                $('#url_base_oai').attr("disable");

            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log('erro');
                $('#loader_validacao').hide();
                showAlertGeneral('<?php _e('Atention', 'tainacan') ?>', '<?php _e('Server not found or not available', 'tainacan') ?>', '<?php _e('error') ?>');
            });
        } else {
            showAlertGeneral('<?php _e('Atention', 'tainacan') ?>', '<?php _e('URL base is empty', 'tainacan') ?>', '<?php _e('error') ?>');
        }
    }

    /* @name: listTableOAIPMHDC()
     * @description: cria dinamicamente uma tabela contendo
     * os identificadores de canais salvos no banco
     * 
     * @author: EDUARDO
     **/
    function update_date(id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/mapping/mapping_controller.php",
            data: {
                mapping_id: id,
                collection_id: $('#collection_id').val(),
                operation: 'update_date'
            }
        }).done(function (result) {
            listTableOAIPMHDC();
        }).fail(function (jqXHR, textStatus, errorThrown) {

        });

    }

    /* @name: listTableOAIPMHDC()
     * @description: cria dinamicamente uma tabela contendo
     * os identificadores de canais salvos no banco
     * 
     * @author: EDUARDO
     **/
    function import_list_set(url_base) {
        if (url_base !== '') {
            $.ajax({
                type: "POST",
                url: $('#src').val() + "/controllers/import/import_controller.php",
                data: {
                    url: url_base,
                    collection_id: $('#collection_id').val(),
                    operation: 'import_list_set'
                }
            }).done(function (result) {

            }).fail(function (jqXHR, textStatus, errorThrown) {
                showAlertGeneral('<?php _e('Atention', 'tainacan') ?>', '<?php _e('Server not found or not available', 'tainacan') ?>', '<?php _e('error') ?>');
                return;
            });
        } else {
            showAlertGeneral('<?php _e('Atention', 'tainacan') ?>', '<?php _e('URL base is empty', 'tainacan') ?>', '<?php _e('error') ?>');
        }
    }
    /* @name: do_import()
     * @description: Funcao que chama via ajax a acao que salva os dados mapeados no vanco
     * os identificadores de canais salvos no banco
     * 
     * @author: EDUARDO
     **/

    function do_import(mapping_id, url_base, token, imported, size, sets) {
        var first;
        if (isNaN(imported)) {
            import_list_set(url_base);
            tempo();
            $("#validate_url_container").hide('slow');
            $("#cronometer").show('slow');
            $("#progress").show('slow');
            imported = 0;
            first = true;
        } else {
            first = false;
        }
        $.ajax({
            dataType: "json",
            type: "GET",
            url: $('#src').val() + "/controllers/import/import_controller.php",
            data: {
                objects_found: size,
                collection_id: $('#collection_id').val(),
                url: url_base,
                mapping_id: mapping_id,
                token: token,
                first: first,
                sets: sets,
                operation: 'do_import'}
        }).done(function (result) {
            imported += result.imported;
            update_progressbar(imported, size);
            if (((result.token === "NULL" || result.token === ""))) {
                window.clearInterval(intervalo);
                update_date(mapping_id);
                $("#progress").hide('slow');
                $('#maping_container').hide();
                $('#validate_url_container').show('slow');
                $("#cronometer").hide('slow');
                showHeaderCollection($('#src').val());
                showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('All objects imported succesfully!', 'tainacan') ?>', 'success');
                return;
            } else {
                //window.clearInterval(intervalo);
                //  saving_data(collection_id,all_data);
                do_import(mapping_id, url_base, result.token, imported, size);
            }
        });
    }

    /* @name: do_import()
     * @description: Funcao que chama via ajax a acao que salva os dados mapeados no vanco
     * os identificadores de canais salvos no banco
     * 
     * @author: EDUARDO
     **/

    function update_repository(mapping_id, url_base, token, imported, size) {
        var first;
        if (isNaN(imported)) {
            tempo();
            $("#validate_url_container").hide('slow');
            $("#cronometer").show('slow');
            $("#progress").show('slow');
            imported = 0;
            first = true;
        } else {
            first = false;
        }
        $.ajax({
            dataType: "json",
            type: "GET",
            url: $('#src').val() + "/controllers/import/import_controller.php",
            data: {
                objects_found: size,
                collection_id: $('#collection_id').val(),
                url: url_base,
                mapping_id: mapping_id,
                token: token,
                first: first,
                operation: 'do_import'}
        }).done(function (result) {
            imported += result.imported;
            update_progressbar(imported, size);
            if (((result.token === "NULL" || result.token === ""))) {
                window.clearInterval(intervalo);
                update_date(mapping_id);
                $("#progress").hide('slow');
                $('#maping_container').hide();
                $('#validate_url_container').show('slow');
                $("#cronometer").hide('slow');
                showHeaderCollection($('#src').val());
                showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('All objects imported succesfully!', 'tainacan') ?>', 'success');
                return;
            } else {
                //window.clearInterval(intervalo);
                //  saving_data(collection_id,all_data);
                update_repository(mapping_id, url_base, result.token, imported, size);
            }
        });
    }

    /* @name: listTableOAIPMHDC()
     * @description: cria dinamicamente uma tabela contendo
     * os identificadores de canais salvos no banco
     * 
     * @author: EDUARDO
     **/
    function show_message_size() {
        showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('Importing the object becomes the importation very slowly!', 'tainacan') ?>', 'info');
    }
    /* @name: listTableOAIPMHDC()
     * @description: cria dinamicamente uma tabela contendo
     * os identificadores de canais salvos no banco
     * 
     * @author: EDUARDO
     **/
    function tempo() {
        var s = 1;
        var m = 0;
        var h = 0;
        intervalo = window.setInterval(function () {
            if (s == 60) {
                m++;
                s = 0;
            }
            if (m == 60) {
                h++;
                s = 0;
                m = 0;
            }
            if (h < 10)
                document.getElementById("hora").innerHTML = "0" + h + "h";
            else
                document.getElementById("hora").innerHTML = h + "h";
            if (s < 10)
                document.getElementById("segundo").innerHTML = "0" + s + "s";
            else
                document.getElementById("segundo").innerHTML = s + "s";
            if (m < 10)
                document.getElementById("minuto").innerHTML = "0" + m + "m";
            else
                document.getElementById("minuto").innerHTML = m + "m";
            s++;
        }, 1000);
    }




    function update_progressbar(imported, total) {
        var percent = (imported / total) * 100;
        $("#progressbar").val(percent);
    }

    $('#formCsv').submit(function (e) {
        $.ajax({
            url: $('#src').val() + '/controllers/import/csv_controller.php',
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false
        }).done(function (result) {
            try {
                elem = jQuery.parseJSON(result);
                if (elem.error) {
                    showAlertGeneral('<?php _e('Error!', 'tainacan'); ?>', elem.msg, 'error');
                }
            }
            catch (e)
            {
                $('#csv_file').val('');
                $('#validate_url_csv_container').hide();
                $('#maping_container_csv').html(result);
                $('#maping_container_csv').show();
                $('#importForm_csv').show();
            }
        });
        e.preventDefault();

    });

    function edit_mapping_csv(mapping_id, collection_id) {
        $('#validate_url_csv_container').hide('slow');
        $('#loader_validacao').show();
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/mapping/mapping_controller.php",
            data: {
                collection_id: collection_id,
                operation: 'edit_mapping_csv',
                mapping_id: mapping_id
            }
        }).done(function (result) {
            $('#loader_validacao').hide('slow');
            $('#maping_container_csv').html(result);
            $('#maping_container_csv').show();
        });
    }

    function listTableCSV() {

        var src = $('#src').val();
        var collectionId = $('#collection_id').val();

        // $("#btn_identifiers_youtube_update").hide();
        //$("#btn_identifiers_youtube_cancel").hide();
        //$("#loader_videos").hide();
        $.ajax({
            url: src + "/controllers/mapping/mapping_controller.php",
            type: 'POST',
            data: {operation: 'list_mapping_csv',
                collection_id: collectionId
            },
            success: function (data) {

                $("#table_csv").html('');
                if (data !== '[]') {
                    var jsonObject = jQuery.parseJSON(data);
                    if (jsonObject && jsonObject != null) {
                        $.each(jsonObject.identifier, function (id, object) {
                            if (object.lastUpdate === false || object.lastUpdate === '') {
                                $("#table_csv").append("<tr><td>" + object.name + "</td>" +
                                        "<td><a href='#' onclick=\"edit_mapping_csv(" + object.id + "," + collectionId + ")\"><span class='glyphicon glyphicon-pencil'></span></a></td>" +
                                        "<td><a href='#' onclick='delete_mapping(" + object.id + "," + collectionId + " )'><span class='glyphicon glyphicon-trash'></span></a></td>" +
                                        "<td><a href='#' onclick=\"do_import_csv('" + object.id + "')\"><span class='glyphicon glyphicon-arrow-down'></span></a></td>");
                            }
                            else {
                                $("#table_csv").append("<tr><td>" + object.name + "</td>" +
                                        "<td><a href='#'><span style='opacity:0.4'  class='glyphicon glyphicon-pencil'></span></a></td>" +
                                        "<td><a href='#' onclick='delete_mapping(" + object.id + "," + collectionId + ")'><span class='glyphicon glyphicon-trash'></span></a></td>" +
                                        "<td><a href='#' ><span style='opacity:0.4' class='glyphicon glyphicon-arrow-down'>&nbsp;<?php _e('Imported in', 'tainacan') ?> " + object.lastUpdate + "</span></a></td>");
                            }
                        });
                        $("#table_csv").show();
                    }
                } // caso o controller retorne false
            }
        });// fim da inclusão de identificador youtube 
    }

    function do_import_csv(mapping_id) {
        show_modal_main();
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/import/csv_controller.php",
            data: {
                collection_id: $('#collection_id').val(),
                mapping_id: mapping_id,
                async: false,
                operation: 'do_import_csv'}
        }).done(function (result) {
            hide_modal_main();
            listTableCSV();
            var jsonObject = jQuery.parseJSON(result);
            if (jsonObject) {
                showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('All objects imported succesfully!', 'tainacan') ?>', 'success');
            }
            else {
                showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('Please, do the mapping!', 'tainacan') ?>', 'info');
            }
        });
    }
</script>