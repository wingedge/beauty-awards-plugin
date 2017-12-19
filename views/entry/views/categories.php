<?php

use Extanet\BeautyAwards\Core\Categories as CategoriesSource;
use Extanet\BeautyAwards\FrontEnd\Categories;
use Extanet\BeautyAwards\FrontEnd\Entries;
use Extanet\BeautyAwards\FrontEnd\EntryForm;
use Extanet\BeautyAwards\FrontEnd\Industry;
use Extanet\BeautyAwards\FrontEnd\Profession;
?>
<form id="form_entry_categories" action="<?php echo filter_input(INPUT_POST, ''); ?>" method="POST">
    <?php Categories::view_nonce(); ?>
    <h2>Choose the categories you'd like this entry to be judged under</h2>
    <?php EntryForm::view_post_message(); ?>
    <?php $first_parent = TRUE; ?>
    <?php foreach ([CategoriesSource::type_profession, CategoriesSource::type_industry] as $type): ?>
        <?php foreach (CategoriesSource::get_categories_by_type($type) as $parent): ?>
            <?php if (Categories::show_all() || in_array($parent->id, array_merge(Profession::professions(), Industry::industries()))): ?>
                <div class="parent-category<?php echo $first_parent ? ' parent-category-open' : ''; ?>">
                    <h2>
                        <span class="dashicons dashicons-arrow-<?php echo $first_parent ? 'up' : 'down'; ?>-alt2"></span>
                        <?php echo $parent->name; ?>
                    </h2>
                    <?php foreach (CategoriesSource::get_sub_categories($parent->id) as $subcategory): ?>
                        <input type="checkbox" id="chk_subcategory_<?php echo $subcategory->id; ?>" name="subcategories[]" value="<?php echo $subcategory->id; ?>"<?php echo in_array($subcategory->id, (!is_null(EntryForm::$p) ? EntryForm::$p->get_data('categories') : Entries::categories(Entries::editing_index()))) ? ' checked="checked"' : ''; ?> />
                        <label for="chk_subcategory_<?php echo $subcategory->id; ?>"><?php echo $subcategory->name; ?></label>
                    <?php endforeach; ?>
                </div>
                <?php $first_parent = FALSE; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endforeach; ?>
    <?php if (Categories::show_all()): ?>
        <p><a href="<?php echo Categories::link_hide_other_categories(); ?>">Hide other categories.</a></p>
    <?php else: ?>
        <p>These are the categories we think make sense for you, you can also <a href="<?php echo Categories::link_show_all_categories(); ?>">reveal all <?php echo CategoriesSource::count_subcategories(); ?> categories</a>.</p>
    <?php endif; ?>
    <p class="submit">
        <button id="btn_choose_categories" name="choose_categories" value="1" class="button-primary">
            Continue
        </button>
    </p>
    <p>
        <a href="<?php echo Categories::link_back_to_entry_details(); ?>">Back to entry details</a>
    </p>
</form>
