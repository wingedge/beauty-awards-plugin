<?php

use Extanet\BeautyAwards\Core\Categories;
use Extanet\BeautyAwards\FrontEnd\EntryForm;
use Extanet\BeautyAwards\FrontEnd\Industry;
?>
<form action="<?php echo filter_input(INPUT_POST, ''); ?>" method="POST">
    <?php Industry::view_nonce(); ?>
    <h2>Would you like to submit work under any of these industry categories?</h2>
    <?php EntryForm::view_post_message(); ?>
    <p>You can change this later</p>
    <?php $categories = Categories::get_categories_by_type(Categories::type_industry); ?>
    <?php $selected = Industry::industries(); ?>
    <?php foreach ($categories as $category): ?>
        <input type="checkbox" id="chk_industry_<?php echo $category->id; ?>" name="industries[]" value="<?php echo $category->id; ?>"<?php echo (in_array($category->id, !is_null(EntryForm::$p) ? EntryForm::$p->get_data('industries') : $selected)) ? ' checked="checked"' : ''; ?> />
        <label for="chk_industry_<?php echo $category->id; ?>">
            <?php echo $category->name; ?>
        </label>
    <?php endforeach; ?>
    <p>
        <a href="<?php echo Industry::link_skip_industries(); ?>">Skip, no special categories</a>
    </p>
    <p class="submit">
        <button id="btn_choose_industry" name="choose_industry" value="1" class="button-primary">
            Continue
        </button>
    </p>
    <p>
        <a href="<?php echo Industry::link_back_to_user_info(); ?>">Back</a>
    </p>
</form>
