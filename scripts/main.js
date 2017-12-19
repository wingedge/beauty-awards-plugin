(function ($) {

    var countdown = {};
    countdown.timers = null;
    countdown.update_timers = function () {
        var danao = Math.round((new Date()).getTime() / 1000);
        countdown.timers.each(function () {
            var seconds = parseInt($.trim($(this).attr('data-target')), 10) - danao;
            var text = '';

            if (seconds === 0) {
                text = 'now';
            } else {
                var days = Math.floor(seconds / 86400);
                if (days > 0) {
                    text += (text === '' ? '' : ' ') + days + ' day' + (days === 1 ? '' : 's') + ' and ';
                }
                seconds -= days * 86400;

                var hours = Math.floor(seconds / 3600);
                seconds -= hours * 3600;
                var minutes = Math.floor(seconds / 60);
                seconds -= minutes * 60;

                if (hours > 0) {
                    text += hours + 'H:';
                }
                if (hours > 0 || minutes > 0) {
                    text += (minutes < 10 ? '0' : '') + minutes + 'M:';
                    text += (seconds < 10 ? '0' : '') + seconds + 'S';
                }
            }
            $(this).html(text);
        });
    };
    countdown.init = function () {
        countdown.timers = $('.baw-countdown');

        window.setInterval(countdown.update_timers, 1000);
        countdown.update_timers();
    };

    var dropload = {};
    dropload.is_supported = function () {
        var div = document.createElement('div');
        return (('draggable' in div) || ('ondragstart' in div && 'ondrop' in div)) && 'FormData' in window && 'FileReader' in window;
    };

    var entry_details = {};
    entry_details.drag_and_drop_supported = false;
    entry_details.dropped_files = false;
    entry_details.is_ajax_uploading = false;
    entry_details.set_message = function (message) {
        $('#par_upload_message').html(message).show();
    };
    entry_details.hide_message = function () {
        $('#par_upload_message').html('').hide();
    };
    entry_details.clear_preview = function () {
        $('#box_file_upload').removeClass('dropload-has-file');
        $('#box_file_upload .dropload-preview').css('background-image', '');
    };
    entry_details.set_preview = function (file) {
        $('#box_file_upload').removeClass('dropload-has-file');

        var reader = new FileReader();
        reader.onload = function (e) {
            $('#box_file_upload .dropload-preview').css('background-image', 'url(\'' + e.target.result + '\')');
            $('#box_file_upload').addClass('dropload-has-file');
        };
        reader.readAsDataURL(file);
    };
    entry_details.upload_image = function (file) {
        $('#hid_image_id').val(0);
        entry_details.hide_message();

        entry_details.is_ajax_uploading = true;
        $('#box_file_upload').addClass('dropload-is-uploading');

        var ad = new FormData();
        ad.append('action', 'baw_upload_image_entry');
        ad.append('image', file);

        var ao = {};
        ao.cache = false;
        ao.complete = entry_details.on_upload_image_complete;
        ao.contentType = false;
        ao.data = ad;
        ao.error = entry_details.on_upload_image_error;
        ao.global = false;
        ao.method = ao.type = 'POST';
        ao.processData = false;
        ao.success = entry_details.on_upload_image_success;
        ao.url = globaw.image_upload_url;
        ao.xhr = function () {
            var cxhr = $.ajaxSettings.xhr();
            if (cxhr.upload) {
                cxhr.upload.addEventListener('progress', entry_details.on_upload_image_progress, false);
            }
            return cxhr;
        };
        $.ajax(ao);
    };
    entry_details.set_image = function (file) {
        if (file.size > globaw.max_file_size) {
            entry_details.set_message('Max image file size is ' + globaw.max_file_size_string + ' MB!');
            return;
        }

        if (!globaw.allowed_mime_types.includes(file.type)) {
            entry_details.set_message('The file you uploaded does not seem to be an image!');
            return;
        }

        entry_details.set_preview(file);
        entry_details.upload_image(file);
    };
    entry_details.init = function () {
        entry_details.drag_and_drop_supported = dropload.is_supported();
        $('#box_file_upload #fup_image').on('change', entry_details.on_file_change);
        if (entry_details.drag_and_drop_supported) {
            $('#form_entry_details').on('submit', entry_details.on_form_submit);
            $('#box_file_upload')
                    .addClass('dropload-is-supported')
                    .on('dragover dragenter', entry_details.dropload_events.enter)
                    .on('dragleave dragend drop', entry_details.dropload_events.leave)
                    .on('drop', entry_details.dropload_events.drop)
                    .on('drag dragstart dragend dragover dragenter dragleave drop', entry_details.dropload_events.all);
        }
    };
    entry_details.on_file_change = function () {
        var fup = $('#fup_image');
        if (fup[0].files.length > 0) {
            entry_details.set_image(fup[0].files[0]);
        }
    };
    entry_details.on_form_submit = function () {
        if (entry_details.drag_and_drop_supported) {
            if (entry_details.is_ajax_uploading) {
                entry_details.set_message('Current image is still uploading!');
                return false;
            }
            $('#fup_image').remove();
        }
    };
    entry_details.on_upload_image_complete = function () {
        entry_details.is_ajax_uploading = false;
        $('#box_file_upload').removeClass('dropload-is-uploading');
        $('#box_file_upload .dropload-uploading span').html('');
    };
    entry_details.on_upload_image_error = function (err, ers, ert) {
        entry_details.clear_preview();
        entry_details.set_message('An error occurred while trying to upload the image!');
    };
    entry_details.on_upload_image_success = function (data) {
        if (!data.hasOwnProperty('success') || !data.hasOwnProperty('message') || !data.hasOwnProperty('data')) {
            entry_details.clear_preview();
            entry_details.set_message('An error occurred while uploading the image!');
        }

        if (!data.data.hasOwnProperty('image_id')) {
            entry_details.clear_preview();
            entry_details.set_message('An error occurred while uploading the image!');
        }

        if (!data.success) {
            entry_details.clear_preview();
            entry_details.set_message(data.message);
        }

        $('#hid_image_id').val(data.data.image_id);
        entry_details.set_message(data.message);
    };
    entry_details.on_upload_image_progress = function (e) {
        if (e.lengthComputable) {
            $('#box_file_upload .dropload-uploading span').html('Uploading: ' + parseInt((e.loaded / e.total) * 100, 10) + '%');
        }
    };
    entry_details.dropload_events = {};
    entry_details.dropload_events.enter = function () {
        $('#box_file_upload').addClass('dropload-is-dropping');
    };
    entry_details.dropload_events.leave = function () {
        $('#box_file_upload').removeClass('dropload-is-dropping');
    };
    entry_details.dropload_events.drop = function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        if (entry_details.is_ajax_uploading) {
            entry_details.set_message('Current image is still uploading!');
            return;
        }
        entry_details.dropped_files = e.originalEvent.dataTransfer.files;
        if (entry_details.dropped_files.length > 0) {
            entry_details.set_image(entry_details.dropped_files[0]);
        }
    };
    entry_details.dropload_events.all = function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
    };

    var modal = {}; // photo tips
    modal.open = function () {
        $('.modal-overlay').addClass('modal-is-open');
    };
    modal.close = function () {
        $('.modal-overlay').removeClass('modal-is-open');
    };
    modal.init = function () {
        $(document)
                .on('click', '.modal-open', modal.on_open_click)
                .on('click', '.modal-header-close', modal.on_close_click);
    };
    modal.on_open_click = function (e) {
        e.preventDefault();
        modal.open();
    };
    modal.on_close_click = function (e) {
        e.preventDefault();
        modal.close();
    };

    var categories = {};
    categories.init = function () {
        $('#form_entry_categories .parent-category h2').on('click', categories.on_parent_title_click);
    };
    categories.on_parent_title_click = function (e) {
        e.preventDefault();

        var pc = $(this).parents('.parent-category').first();
        if (pc.hasClass('parent-category-open')) {
            pc.removeClass('parent-category-open');
            $('.dashicons', this).removeClass('dashicons-arrow-up-alt2').addClass('dashicons-arrow-down-alt2');
        } else {
            pc.addClass('parent-category-open');
            $('.dashicons', this).removeClass('dashicons-arrow-down-alt2').addClass('dashicons-arrow-up-alt2');
        }

        //$('#form_entry_categories .parent-category').removeClass('parent-category-open');
        //$('#form_entry_categories .parent-category h2 .dashicons').removeClass('dashicons-arrow-up-alt2').addClass('dashicons-arrow-down-alt2');
        //$(this).parents('.parent-category').first().addClass('parent-category-open');
        //$('.dashicons', this).removeClass('dashicons-arrow-down-alt2').addClass('dashicons-arrow-up-alt2');
    };

    var address = {};
    address.init = function () {
        $('#ddl_country').on('change', address.on_country_change).trigger('change');
    };
    address.on_country_change = function () {
        var country = $('#ddl_country').val();

        if (country == '') {
            $('#form_entry_address .address-form').hide();
        } else {
            $('#form_entry_address .address-form').show();
        }

        if (country == 'US') {
            $('#par_us_state').show();
        } else {
            $('#par_us_state').hide();
        }

        if (country == 'CA') {
            $('#par_canada_province').show();
        } else {
            $('#par_canada_province').hide();
        }

        if (country == 'US' || country == 'CA') {
            $('#par_other_state_province').hide();
            $('#txt_city').attr('placeholder', 'City');
            if (country == 'US') {
                $('#txt_postal_code').attr('placeholder', 'ZIP code');
            }
            if (country == 'CA') {
                $('#txt_postal_code').attr('placeholder', 'Postal code');
            }
        } else {
            $('#par_other_state_province').show();
            $('#txt_city').attr('placeholder', 'City / Town');
            $('#txt_postal_code').attr('placeholder', 'ZIP / Postal code');
        }
    };

    var payment = {};
    payment.form = null;
    payment.stripe = Stripe(stripe_api_key);
    payment.element = payment.stripe.elements();
    payment.card = null;
    payment.init = function () {
        payment.form = $('#form_entry_payment');

        payment.card = payment.element.create('card');
        payment.card.mount('#box_card_element');

        payment.form.on('submit', payment.on_form_submit);
        payment.card.addEventListener('change', payment.on_card_change);
    };
    payment.on_form_submit = function (e) {
        e.preventDefault();
        payment.stripe.createToken(payment.card).then(payment.on_token_create);
    };
    payment.on_token_create = function (r) {
        if (r.error) {
            $('#box_card_errors').html(r.error.message);
        } else {
            var hid_token = document.createElement('input');
            hid_token.setAttribute('type', 'hidden');
            hid_token.setAttribute('name', 'stripe_token');
            hid_token.setAttribute('value', r.token.id);

            var hid_button = document.createElement('input');
            hid_button.setAttribute('type', 'hidden');
            hid_button.setAttribute('name', 'submit_payment');
            hid_button.setAttribute('value', 1);

            payment.form[0].appendChild(hid_token);
            payment.form[0].appendChild(hid_button);
            payment.form[0].submit();
        }
    };
    payment.on_card_change = function (e) {
        if (e.error) {
            $('#box_card_errors').html(e.error.message);
        } else {
            $('#box_card_errors').html('');
        }
    };

    $(function () {
        if ($('#form_entry_details').length > 0) {
            entry_details.init();
            modal.init();
        }
        if ($('#form_entry_categories').length > 0) {
            categories.init();
        }
        if ($('#form_entry_address').length > 0) {
            address.init();
        }
        if ($('#form_entry_payment').length > 0) {
            payment.init();
        }
        if ($('.baw-countdown').length > 0) {
            countdown.init();
        }
    });
})(jQuery);
