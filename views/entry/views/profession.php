<?php

use Extanet\BeautyAwards\Core\Categories;
use Extanet\BeautyAwards\FrontEnd\EntryForm;
use Extanet\BeautyAwards\FrontEnd\Profession;
?>
<form action="<?php echo filter_input(INPUT_POST, ''); ?>" method="POST">
    <?php Profession::view_nonce(); ?>
    <h2>Take your first step to become award-winning!</h2>
    <?php EntryForm::view_post_message(); ?>
    <?php $categories = Categories::get_categories_by_type(Categories::type_profession); ?>
    <?php $selected = Profession::professions(); ?>
    <?php foreach ($categories as $category): ?>
        <input type="checkbox" id="chk_profession_<?php echo $category->id; ?>" name="professions[]" value="<?php echo $category->id; ?>"<?php echo (in_array($category->id, !is_null(EntryForm::$p) ? EntryForm::$p->get_data('professions') : $selected)) ? ' checked="checked"' : ''; ?> />
        <label for="chk_profession_<?php echo $category->id; ?>"><?php echo $category->name; ?></label>
    <?php endforeach; ?>
    <p class="submit">
        <button id="btn_choose_profession" name="choose_profession" value="1" class="button-primary">
            Continue
        </button>
    </p>
</form>
