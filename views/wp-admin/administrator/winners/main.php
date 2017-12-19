<?php

use Extanet\BeautyAwards\Core\Categories;
use Extanet\BeautyAwards\WPAdmin\Administrator\Winners;
?>
<div id="box_contest_winners" class="wrap">
    <h1 class="wp-heading-inline">Winners</h1>
    <hr class="wp-header-end" />

    <p>
        <a href="<?php echo Winners::link_confirm_final_winners(); ?>" class="button-primary">
            Confirm Final Winners
        </a>
        <a href="<?php echo Winners::link_export_to_excel(); ?>" target="_blank" id="btn_export_to_excel2" data-category-id="0" class="button-secondary">
            Export to Excel
        </a>
    </p>

    <h2>Current Winners</h2>
    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="ddl_select_category">Select Category</label>
                </th>
                <td>
                    <select id="ddl_select_category" class="widefat">
                        <option value="0">(select category)</option>
                        <?php foreach (array_merge(Categories::get_categories_by_type(Categories::type_profession), Categories::get_categories_by_type(Categories::type_industry)) as $parent_category): ?>
                            <optgroup label="<?php echo $parent_category->name; ?>">
                                <?php foreach (Categories::get_sub_categories($parent_category->id) as $sub_category): ?>
                                    <option value="<?php echo $sub_category->id; ?>"><?php echo $sub_category->name; ?></option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </tbody>
    </table>

    <p id="par_winners_no_category">Select a category to view the current winners.</p>
    <p id="par_winners_loading" style="display: none;">Getting list of winners...</p>
    <p id="par_winners_error" style="display: none;">An error occurred while getting the list of winners.</p>
    <p id="par_winners_error_custom" style="display: none;"></p>
    <div id="box_winners_container" style="display: none;"></div>

</div>
