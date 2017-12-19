(function ($) {

    var ajax_options = function (data, success, error, complete) {
        this.cache = false;
        this.data = data;
        this.global = false;
        this.method = this.type = 'POST';
        this.url = ajaxurl;
        if (complete !== null) {
            this.complete = complete;
        }
        if (error !== null) {
            this.error = error;
        }
        if (success !== null) {
            this.success = success;
        }
    };

    var cat_sorter = {};
    cat_sorter.init = function () {
        $('table.categories tbody').sortable({
            axis: 'y',
            containment: 'parent',
            cursor: 'ns-resize',
            handle: '.categories-sort-handle',
            tolerance: 'pointer',
            stop: cat_sorter.on_sort_stop
        });
    };
    cat_sorter.on_sort_stop = function () {
        var ad = {};
        ad.action = 'baw_categories_sort';
        ad.ids = [];
        $('table.categories tbody tr').each(function () {
            ad.ids.push($(this).attr('data-id'));
        });
        $.ajax(new ajax_options(ad, function (data) {
            console.log(data);
        }, function (err, ers, ert) {
            console.log(err, ers, ert);
        }));
    };

    var cat_editor = {};
    cat_editor.init = function () {
        $('#btn_toggle_category_editor').on('click', cat_editor.on_toggle_category_editor_click);
        console.log('yeah');
    };
    cat_editor.on_toggle_category_editor_click = function (e) {
        e.preventDefault();
        var form = $('#form_category_editor');
        if (form.is(':visible')) {
            form.hide();
            $('#btn_toggle_category_editor .label').html('Show category editor');
        } else {
            form.show();
            $('#btn_toggle_category_editor .label').html('Hide category editor');
        }
    };

    var judge_cats = {};
    judge_cats.init = function () {
        $('#box_judges_manage.judges-assign-categories .judge-categories')
                .on('click', 'input[type=checkbox].parent-category', judge_cats.on_parent_category_change)
                .on('click', 'input[type=checkbox].sub-category', judge_cats.on_subcategory_change);

        $('#box_judges_manage.judges-assign-categories .judge-categories input[type=checkbox].parent-category').each(function () {
            var pid = $(this).attr('data-category-id');
            if ($('#box_judges_manage.judges-assign-categories .judge-categories input[type=checkbox].sub-category[data-parent-id=' + pid + ']').length === $('#box_judges_manage.judges-assign-categories .judge-categories input[type=checkbox].sub-category[data-parent-id=' + pid + ']:checked').length) {
                $('#box_judges_manage.judges-assign-categories .judge-categories input[type=checkbox].parent-category[data-category-id=' + pid + ']').prop('checked', true);
            }
        });

    };
    judge_cats.on_parent_category_change = function () {
        var pid = $(this).attr('data-category-id');
        $('#box_judges_manage.judges-assign-categories .judge-categories input[type=checkbox].sub-category[data-parent-id=' + pid + ']').prop('checked', this.checked);
    };
    judge_cats.on_subcategory_change = function () {
        var pid = $(this).attr('data-parent-id');
        if (this.checked) {
            if ($('#box_judges_manage.judges-assign-categories .judge-categories input[type=checkbox].sub-category[data-parent-id=' + pid + ']').length === $('#box_judges_manage.judges-assign-categories .judge-categories input[type=checkbox].sub-category[data-parent-id=' + pid + ']:checked').length) {
                $('#box_judges_manage.judges-assign-categories .judge-categories input[type=checkbox].parent-category[data-category-id=' + pid + ']').prop('checked', true);
            }
        } else {
            $('#box_judges_manage.judges-assign-categories .judge-categories input[type=checkbox].parent-category[data-category-id=' + pid + ']').prop('checked', false);
        }
    };

    var winners = {};
    winners.init = function () {
        $('#ddl_select_category').on('change', winners.on_category_change);
        $('#btn_export_to_excel').on('click', winners.on_export_to_excel_click);
    };
    winners.on_category_change = function () {
        var category_id = parseInt($.trim($('#ddl_select_category').val()), 10);

        $('#box_winners_container').empty();
        $('#par_winners_no_category, #par_winners_error, #par_winners_error_custom, #box_winners_container').hide();
        $('#par_winners_loading').show();

        $('#btn_export_to_excel').attr('data-category-id', category_id);

        var data = {};
        data.action = 'baw_get_winners_by_category';
        data.category_id = category_id;
        $.ajax(new ajax_options(data, winners.on_load_success, winners.on_load_error));
    };
    winners.on_load_success = function (response) {
        if (!response.hasOwnProperty('success') || !response.hasOwnProperty('message') || !response.hasOwnProperty('data')) {
            winners.on_load_error();
            return;
        }

        if (!response.data.hasOwnProperty('html')) {
            winners.on_load_error();
            return;
        }

        console.log(response.data);

        if (!response.success) {
            $('#par_winners_no_category, #par_winners_loading, #par_winners_error, #box_winners_container').hide();
            $('#par_winners_error_custom').show().html(response.message);
            return;
        }

        $('#par_winners_no_category, #par_winners_loading, #par_winners_error, #par_winners_error_custom').hide();
        $('#box_winners_container').show().html(response.data.html);
    };
    winners.on_load_error = function () {
        $('#par_winners_no_category, #par_winners_loading, #par_winners_error_custom, #box_winners_container').hide();
        $('#par_winners_error').show();
    };
    winners.on_export_to_excel_click = function (e) {
        var category_id = 
        e.preventDefault();
    };

    $(function () {
        if ($('#box_contest_categories.category-edit').length > 0) {
            cat_editor.init();
        }
        if ($('#box_contest_categories .categories').length > 0) {
            cat_sorter.init();
        }
        if ($('#box_judges_manage.judges-assign-categories').length > 0) {
            judge_cats.init();
        }
        if ($('#box_contest_winners').length > 0) {
            winners.init();
        }
    });
})(jQuery);
