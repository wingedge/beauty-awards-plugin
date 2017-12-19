<?php

use Extanet\BeautyAwards\Core\Categories;
use Extanet\BeautyAwards\WPAdmin\Administrator\Judges;
use Extanet\BeautyAwards\Core\Judges as JudgesSource;

$judge_id = Judges::get_judge_id_from_get();
$judge = get_user_by('ID', $judge_id);
$assigned = JudgesSource::get_categories_flat($judge_id);
?>
<div id="box_judges_manage" class="wrap judges-assign-categories">
    <h1 class="wp-heading-inline">Assign Categories to Judge</h1>
    <hr class="wp-header-end" />

    <p>
        <a href="<?php ?>" class="button-secondary">
            <span class="dashicons dashicons-arrow-left-alt2"></span>
            Back to Judges list
        </a>
    </p>

    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">
        <?php wp_nonce_field('assign_judge_categories', 'session_marker'); ?>
        <input type="hidden" name="judge_id" value="<?php echo $judge_id; ?>" />
        <table class="form-table judge-categories">
            <tbody>
                <tr>
                    <th scope="row">Judge</th>
                    <td>
                        <strong><?php echo $judge->display_name; ?></strong>
                        <small>(<?php echo $judge->user_login; ?>)</small>
                    </td>
                </tr>
                <?php foreach (array_merge(Categories::get_categories_by_type(Categories::type_profession), Categories::get_categories_by_type(Categories::type_industry)) as $category): ?>
                    <tr class="category-row">
                        <th scope="row">
                            <label for="chk_parent_<?php echo $category->id; ?>">
                                <input type="checkbox" id="chk_parent_<?php echo $category->id; ?>" class="parent-category" data-category-id="<?php echo $category->id; ?>" />
                                <?php echo $category->name; ?>
                            </label>
                        </th>
                        <td>
                            <?php foreach (Categories::get_sub_categories($category->id) as $subcategory): ?>
                                <label for="chk_category_<?php echo $subcategory->id; ?>" class="subcategories">
                                    <input type="checkbox" id="chk_category_<?php echo $subcategory->id; ?>" name="categories[]" value="<?php echo $subcategory->id; ?>" data-parent-id="<?php echo $category->id; ?>" class="sub-category"<?php echo in_array($subcategory->id, $assigned) ? ' checked="checked"' : ''; ?> />
                                    <?php echo $subcategory->name; ?>
                                </label>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p class="submit">
            <button id="btn_assign_categories" name="assign_judge_categories" class="button-primary">
                Assign Categories to Judge
            </button>
        </p>
    </form>
</div>