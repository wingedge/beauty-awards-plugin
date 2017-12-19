<?php

use Extanet\BeautyAwards\WPAdmin\Administrator\Categories;
use Extanet\BeautyAwards\Core\Categories as CategoriesSource;
?>
<div id="box_contest_categories" class="wrap">
    <h1 class="wp-heading-inline">Categories</h1>
    <a href="<?php echo Categories::link_add_new(); ?>" class="page-title-action">Add New</a>
    <hr class="wp-header-end" />

    <?php foreach ([CategoriesSource::type_profession => 'By Profession', CategoriesSource::type_industry => 'By Industry/Occasion',] as $type_id => $type_name): ?>
        <h2><?php echo $type_name; ?></h2>
        <?php if (empty($categories = CategoriesSource::get_categories_by_type($type_id))): ?>
            <p>You have no categories defined for this type.</p>
        <?php else: ?>
            <table class="widefat striped categories">
                <thead>
                    <tr>
                        <th class="col-category-sort">&nbsp;</th>
                        <th class="col-category-name">Name</th>
                        <th class="col-category-description">Description</th>
                        <th class="col-category-subcategories">Sub-categories</th>
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
                            <td class="col-category-subcategories"><?php echo number_format(intval($category->subcategories), 0); ?></td>
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
    <?php endforeach; ?>
</div>
