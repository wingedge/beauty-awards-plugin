<?php

use Extanet\BeautyAwards\WPAdmin\Administrator\Categories;
use Extanet\BeautyAwards\Core\Categories as CategoriesSource;

$parent_id = CategoriesSource::get_parent_id($category_id);
?>
<div id="box_contest_categories" class="wrap category-delete">
    <h1 class="wp-heading-inline">Delete Category</h1>
    <hr class="wp-header-end" />

    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">
        <?php wp_nonce_field('delete_category', 'session_marker'); ?>
        <input type="hidden" name="category_id" value="<?php echo $category_id; ?>" />

        <p><strong>Are you sure you want to delete this category?</strong></p>

        <p>
            <span class="dashicons dashicons-warning"></span>
            <strong>Note:</strong>
            <?php if ($parent_id == 0): ?>
                All sub-categories under this category and entries under those sub-categories will be deleted permanently!
            <?php else: ?>
                All entries under this sub-category will be deleted permanently!
            <?php endif; ?>
        </p>

        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="txt_name">Name</label></th>
                    <td><?php echo CategoriesSource::get_name($category_id); ?></td>
                </tr>
                <tr>
                    <th scope="row"><label for="txt_description">Description</label></th>
                    <td><?php echo CategoriesSource::get_description($category_id); ?></td>
                </tr>
            </tbody>
        </table>

        <p class="submit">
            <button id="btn_delete_category" name="delete_category" class="button-primary">
                <span class="dashicons dashicons-trash"></span>
                Delete Category
            </button>
            <a href="<?php echo Categories::link_edit_back($category_id); ?>" class="button-secondary">
                <span class="dashicons dashicons-arrow-left-alt2"></span>
                Cancel
            </a>
        </p>
    </form>
</div>
