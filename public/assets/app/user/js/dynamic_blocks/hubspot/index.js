$(document).ready(function () {
    $(".hubspot-contact-frame").off("click").on("click", function () {
        let block_id_name = $(this).parent().attr('id');
        getFormDataAndRenewBlock(hubspot_contact_deals_url, block_id_name, null, 'POST', {
            'hs_contact_id': $(this).data("id"),
            "meta": {
                "current_page": this.id ? JSON.parse(this.id).last_page_number : 1,
            }
        })
    });
    $(".deels-pagination").on("click", function (e) {
        e.preventDefault()
        let block_id_name = $(this).parent().attr('id')
        if(e.target.id){
            getFormDataAndRenewBlock(hubspot_contact_deals_url, block_id_name, null, 'POST', {
                'hs_contact_id': $(this).data("id"),
                "meta": {
                    "current_page": e.target.id.substring(7),
                }
            })
        }
    });
    $(".comments-pagination").on("click", function (e) {
        e.preventDefault()
        let block_id_name = $(this).parent().attr('id')
        let data = $.parseJSON(this.id);
        if(e.target.id){
            getFormDataAndRenewBlock(hubspot_contact_deal_show_url, block_id_name, null, 'POST', {
                ...data,
                "meta": {
                    "current_page": e.target.id.substring(7),
                },
                'sort': $(".sort-select").val()
            })
        }
    });
    $(".sort-select").on("change", function (e) {
        let block_id_name = $(this).parent().parent().attr('id');
        let data = $.parseJSON(this.id);
        getFormDataAndRenewBlock(hubspot_contact_deal_show_url, block_id_name, null, 'POST', {
            ...data,
            'sort': e.target.value,
        })
    });
    $(".hubspot-index").on("click", function () {
        let block_id_name = $(this).parent().attr('id');
        getFormDataAndRenewBlock(hubspot_index_url, block_id_name)
    });
    $(".hubspot-create-new-contact").off("click").on("click", function () {
        let block_id_name = $(this).parent().attr('id');
        getFormDataAndRenewBlock(hubspot_contact_create_url, block_id_name)
    });
    $(".hubspot-contact-create-button").off("click").on("click", function () {
        let block_id_name = $(this).parent().attr('id');
        getFormDataAndRenewBlock(hubspot_contact_store_url, block_id_name, 'hubspot-contact-create-form')
    });
    $(".hubspot-contact-deal-create-button").off("click").on("click", function () {
        let block_id_name = $(this).parent().attr('id');
        getFormDataAndRenewBlock(hubspot_contact_deal_create_url, block_id_name, null, 'POST', {'hs_contact_id': $(this).data("id")})
    });
    $(".hubspot-deal-short").off("click").on("click", function () {
        let block_id_name = $(this).parent().attr('id');
        let data = $.parseJSON(this.id);
        getFormDataAndRenewBlock(hubspot_contact_deal_show_url, block_id_name, null, 'POST',{ ...data, 'sort': "DESC" })
    });
    $(".hubspot-deal-settings-edit").off("click").on("click", function () {
        let block_id_name = $(this).parent().parent().attr('id');
        getFormDataAndRenewBlock(hubspot_deal_settings_edit_url, block_id_name, null, 'POST', {'hs_contact_id': $(this).data("id")})
    });
    $(".hubspot-deal-settings-update-button").off("click").on("click", function () {
        let block_id_name = $(this).parent().attr('id');
        getFormDataAndRenewBlock(hubspot_deal_settings_update_url, block_id_name, 'hubspot-deal-settings', 'POST', {'hs_contact_id': $(this).data("id")})
    });
    $(".hubspot-contact-link").off("click").on("click", function () {
        let block_id_name = $(this).parent().attr('id');
        getFormDataAndRenewBlock(hubspot_contact_link_url, block_id_name, 'hubspot-deal-settings', 'POST', {'hs_contact_id': $(this).data("id")})
    });
    $(".hubspot-contact-deal-store-button").off("click").on("click", function () {
        let block_id_name = $(this).parent().attr('id');
        let form_data = getDefaultFormData(block_id_name);
        $(".hubspot-contact-deal-create-form").find(':input').each(function (i, input) {
            form_data[$(input).attr('name')] = $(input).val();
        });
        renewDynamicBlockData(hubspot_contact_deal_store_url, block_id_name, "POST", form_data);
    });
    $(".hubspot-contact-deal-create-pipeline").off("change").on("change", function () {
        let hs_select = $('.hubspot-contact-deal-create-dealstage');
        hs_select.empty()
        let pipelines = $.parseJSON($('.hubspot-pipelines').val());
        let pipeline_id = $(this).find(":selected").val();
        let pipeline = pipelines[pipeline_id];
        let stages = pipeline['stages'];
        let options = '';
        stages.forEach(function (stage) {
            options += '<option value="' + stage['stageId'] + '">' + stage['label'] + '</option>'
        })
        hs_select.append(options).prop('disabled', false);
    });

    function getFormDataAndRenewBlock(url, block_id_name, form_class_name = null, request_type = 'POST', data = {}) {
        let form_data = getDefaultFormData(block_id_name);
        $.each(data, function (key, value) {
            form_data[key] = value;
        })

        if (form_class_name) {
            form_class_name = '.' + form_class_name
            $(form_class_name).find(':input').each(function (i, input) {
                if ($(input).is(':checkbox')) {
                    form_data[$(input).attr('name')] = $(input).is(":checked");
                } else {
                    form_data[$(input).attr('name')] = $(input).val();
                }
            });
        }
        renewDynamicBlockData(url, block_id_name, request_type, form_data);
    }
    function renewDynamicBlockData(url, block_id_name, request_type = "GET", form_data = {}) {
        clearErrorMsg();
        let spinner = $(".hubspot-spinner-container");
        spinner.css('display', 'block');
        let hubspot_body_id = '#' + block_id_name
        let prev_html = $(hubspot_body_id).html()
        $(hubspot_body_id).html("");
        $.ajax({
            url : url,
            type: request_type,
            data: form_data,
            success: function(data)
            {
                spinner.css('display', 'none');
                $(hubspot_body_id).html(data.html);
            },
            error: function (jqXHR)
            {
                spinner.css('display', 'none');
                let data = JSON.parse(jqXHR.responseText)
                printErrorMsg(data.errors);
                $(hubspot_body_id).html(prev_html);
            }
        })
    }
    function printErrorMsg(msg) {

        let error = $(".hubspot-print-error-msg")

        error.css('display', 'block');
        error.empty()
        let li_errors = ''
        $.each(msg, function (key, value) {
            li_errors += '<li>' + key + ': ' + value + '</li>';
        });
        error.empty().append(li_errors)
    }
    function clearErrorMsg() {

        let error = $(".hubspot-print-error-msg")

        error.css('display', 'none');
        error.empty()
    }

    function getDefaultFormData(block_id_name) {
        let ticket_id = $('.hubspot-ticket-id').val();
        let block_id = block_id_name.substring(13); // hubspot-body-
        return {
            "ticket_id": ticket_id,
            "block_id": block_id,
        };
    }
});