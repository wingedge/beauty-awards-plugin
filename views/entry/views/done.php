<?php

use Extanet\BeautyAwards\FrontEnd\Done;
use Extanet\BeautyAwards\FrontEnd\EntryForm;
?>
<form action="<?php echo filter_input(INPUT_POST, ''); ?>" method="POST" enctype="multipart/form-data">
    <?php Done::view_nonce(); ?>
    <h2>We've received your submission.</h2>
    <?php EntryForm::view_post_message(); ?>
    <p>So what happens next? The judging process will begin soon and we'll send you an email to let you know how you [... ?]</p>
    <p class="submit">
        <a href="<?php echo Done::link_start_over(); ?>">Start over and submit more entries</a>
        &nbsp;&nbsp;&nbsp;
        <a href="<?php echo home_url(); ?>" class="button-primary button">
            Back to homepage
        </a>
    </p>
</form>
