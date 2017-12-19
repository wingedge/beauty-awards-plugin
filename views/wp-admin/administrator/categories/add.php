<?php

use Extanet\BeautyAwards\WPAdmin\Administrator\Categories;
use Extanet\BeautyAwards\Core\Categories as CategoriesSource;

$parent_id = intval(trim(filter_input(INPUT_GET, 'parent_id')));
?>
<div class="wrap">
    <h1 class="wp-heading-inline">Add Category</h1>
    <hr class="wp-header-end" />

    <p>
        <a href="<?php echo Categories::link_add_back(); ?>" class="button-secondary">
            <span class="dashicons dashicons-arrow-left-alt2"></span>
            <?php if ($parent_id > 0): ?>
                Back to parent category
            <?php else: ?>
                Back to list of categories
            <?php endif; ?>
        </a>
    </p>

    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">
        <?php wp_nonce_field('add_category', 'session_marker'); ?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="txt_name">Name</label></th>
                    <td><input type="text" id="txt_name" name="name" class="widefat" maxlength="100" value="<?php echo (!is_null(Categories::$p)) ? Categories::$p->get_data('name') : ''; ?>" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="txt_description">Description</label></th>
                    <td>
                        <textarea type="text" id="txt_description" name="description" class="widefat" rows="7"><?php echo (!is_null(Categories::$p)) ? Categories::$p->get_data('description') : ''; ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="txt_winnings">Winnings (%)</label></th>
                    <td>
                        <input type="number" id="txt_winnings" name="winnings" min="0" max="100" step="0.01" value="<?php echo (!is_null(Categories::$p)) ? floatval(trim(Categories::$p->get_data('winnings'))) : '20'; ?>" />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="ddl_type">Type/Parent</label></th>
                    <td>
                        <select id="ddl_type" name="type" class="widefat">
                            <option value="0">(select type or parent category)</option>
                            <optgroup label="This is a Top-Level Category">
                                <option value="-1"<?php echo (!is_null(Categories::$p) && Categories::$p->get_data('type') == -1) ? ' selected="selected"' : ''; ?>>under "By Profession"</option>
                                <option value="-2"<?php echo (!is_null(Categories::$p) && Categories::$p->get_data('type') == -2) ? ' selected="selected"' : ''; ?>>under "By Industry/Occasion"</option>
                            </optgroup>
                            <?php foreach ([CategoriesSource::type_profession => 'By Profession', CategoriesSource::type_industry => 'By Industry/Occasion',] as $type_id => $type_name): ?>
                                <?php if (!empty($categories = CategoriesSource::get_categories_by_type($type_id))): ?>
                                    <optgroup label="<?php echo $type_name; ?>">
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category->id; ?>"<?php echo ((!is_null(Categories::$p) ? Categories::$p->get_data('type') : intval(trim(filter_input(INPUT_GET, 'parent_id')))) == $category->id) ? ' selected="selected"' : ''; ?>><?php echo $category->name; ?></option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </td>
            </tbody>
        </table>
        <p class="submit">
            <button id="btn_add_category" name="add_category" class="button-primary">
                Add Category
            </button>
        </p>
    </form>
</div>
