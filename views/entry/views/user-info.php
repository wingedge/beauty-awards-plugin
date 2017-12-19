<?php

use Extanet\BeautyAwards\FrontEnd\EntryForm;
use Extanet\BeautyAwards\FrontEnd\UserInfo;
?>
<form action="<?php echo filter_input(INPUT_POST, ''); ?>" method="POST">
    <?php UserInfo::view_nonce(); ?>
    <h2>Tell us about yourself</h2>
    <?php EntryForm::view_post_message(); ?>
    <p>You can submit as an individual, or as a company, salon, or barbershop, etc.</p>
    <p>
        <input type="text" id="txt_name" name="name" placeholder="Name" value="<?php echo (!is_null(EntryForm::$p)) ? EntryForm::$p->get_data('name') : UserInfo::name(); ?>" />
    </p>
    <p>
        <input type="email" id="txt_email" name="email" placeholder="Email" value="<?php echo (!is_null(EntryForm::$p)) ? EntryForm::$p->get_data('email') : UserInfo::email(); ?>" />
    </p>
    <p>If you're a winner, what name would you like displayed on the award?</p>
    <p>
        <input type="text" id="txt_award_name" name="award_name" placeholder="Name to display on award" value="<?php echo (!is_null(EntryForm::$p)) ? EntryForm::$p->get_data('award_name') : UserInfo::award_name(); ?>" />
    </p>
    <p class="submit">
        <button id="btn_submit_user_info" name="submit_user_info" value="1" class="button-primary">
            Continue
        </button>
    </p>
    <!--
    <p class="entries-item-controls">
        <a href="<?php echo UserInfo::link_back_to_profession(); ?>">Back</a>
    </p>
    -->
</form>
