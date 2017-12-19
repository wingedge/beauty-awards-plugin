<?php

use Extanet\BeautyAwards\WPAdmin\Administrator\Categories;
use Extanet\BeautyAwards\Core\Categories as CategoriesSource;

$parent_id = CategoriesSource::get_parent_id($category_id);
?>
<div id="box_contest_categories" class="wrap category-edit">
    <h1 class="wp-heading-inline">Edit Category</h1>
    <hr class="wp-header-end" />

    <p>
        <a href="<?php echo Categories::link_edit_back($category_id); ?>" class="button-secondary">
            <span class="dashicons dashicons-arrow-left-alt2"></span>
            <?php if ($parent_id > 0): ?>
                Back to parent category
            <?php else: ?>
                Back to list of categories
            <?php endif; ?>
        </a>
        <?php if ($parent_id == 0): ?>
            <a href id="btn_toggle_category_editor" class="button-secondary">
                <span class="dashicons dashicons-welcome-write-blog"></span>
                <span class="label">Show category editor</span>
            </a>
        <?php endif; ?>
    </p>

    <form id="form_category_editor" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST"<?php echo $parent_id == 0 ? ' style="display: none;"' : ''; ?>>
        <?php wp_nonce_field('edit_category', 'session_marker'); ?>
        <input type="hidden" name="category_id" value="<?php echo $category_id; ?>" />
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="txt_name">Name</label></th>
                    <td><input type="text" id="txt_name" name="name" class="widefat" maxlength="100" value="<?php echo (!is_null(Categories::$p)) ? Categories::$p->get_data('name') : CategoriesSource::get_name($category_id); ?>" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="txt_description">Description</label></th>
                    <td>
                        <textarea type="text" id="txt_description" name="description" class="widefat" rows="7"><?php echo (!is_null(Categories::$p)) ? Categories::$p->get_data('description') : CategoriesSource::get_description($category_id); ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="txt_winnings">Winnings (%)</label></th>
                    <td>
                        <input type="number" id="txt_winnings" name="winnings" min="0" max="100" step="0.01" value="<?php echo (!is_null(Categories::$p)) ? floatval(trim(Categories::$p->get_data('winnings'))) : CategoriesSource::get_winnings($category_id) ?>" />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="ddl_type">Type/Parent</label></th>
                    <td>
                        <select id="ddl_type" name="type" class="widefat">
                            <option value="0">(select type or parent category)</option>
                            <optgroup label="This is a Top-Level Category">
                                <option value="-1"<?php echo (!is_null(Categories::$p) ? Categories::$p->get_data('type') : CategoriesSource::get_type($category_id) * -1) == -1 ? ' selected="selected"' : ''; ?>>under "By Profession"</option>
                                <option value="-2"<?php echo (!is_null(Categories::$p) ? Categories::$p->get_data('type') : CategoriesSource::get_type($category_id) * -1) == -2 ? ' selected="selected"' : ''; ?>>under "By Industry/Occasion"</option>
                            </optgroup>
                            <?php foreach ([CategoriesSource::type_profession => 'By Profession', CategoriesSource::type_industry => 'By Industry/Occasion',] as $type_id => $type_name): ?>
                                <?php if (!empty($categories = apply_filters('baw_remove_category', CategoriesSource::get_categories_by_type($type_id), $category_id))): ?>
                                    <optgroup label="<?php echo $type_name; ?>">
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category->id; ?>"<?php echo (!is_null(Categories::$p) ? Categories::$p->get_data('type') : $parent_id) == $category->id ? ' selected="selected"' : ''; ?>><?php echo $category->name; ?></option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="submit">
            <button id="btn_save_category" name="save_category" class="button-primary">
                Save Changes to Category
            </button>
        </p>
    </form>

    <?php if ($parent_id == 0): ?>
        <h2 class="wp-heading-inline">
            Sub-categories
            <a href="<?php echo Categories::link_add_new($category_id); ?>" class="page-title-action">Add New</a>
        </h2>
        <?php if (empty($categories = CategoriesSource::get_sub_categories($category_id))): ?>
            <p>You have no categories defined for this type.</p>
        <?php else: ?>
            <table class="widefat striped categories">
                <thead>
                    <tr>
                        <th class="col-category-sort">&nbsp;</th>
                        <th class="col-category-name">Name</th>
                        <th class="col-category-description">Description</th>
                        <th class="col-category-winnings">Winnings</th>
                        <th class="col-category-actions">&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr data-id="<?php echo $category->id; ?>">
                            <td class="col-category-sort"><span class="dashicons dashicons-menu categories-sort-handle"></span></td>
                            <td class="col-category-name"><?php echo $category->name; ?></td>
                            <td class="col-category-description"><?php echo $category->description; ?></td>
                            <td class="col-category-winnings"><?php echo number_format(floatval($category->winnings), 2); ?>%</td>
                            <td class="col-category-actions">
                                <a href="<?php echo Categories::link_edit($category->id); ?>" class="button-secondary"><span class="dashicons dashicons-edit"></span> Edit</a>
                                <a href="<?php echo Categories::link_delete($category->id); ?>" class="button-secondary"><span class="dashicons dashicons-trash"></span></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>

</div>
