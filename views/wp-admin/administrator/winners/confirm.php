<?php

use Extanet\BeautyAwards\WPAdmin\Administrator\Winners;
?>
<div id="box_contest_winners" class="wrap">
    <h1 class="wp-heading-inline">Confirm Winners</h1>
    <hr class="wp-header-end" />

    <p>
        <a href="<?php echo Winners::link_main(); ?>" class="button-secondary">
            <span class="dashicons dashicons-arrow-left-alt2"></span>
            Back to Winners list
        </a>
    </p>

    <p><strong>Are you sure you want to confirm the final list of winners?</strong></p>
    <p>This action will do the following:</p>
    <ul>
        <li>Close the judging of entries</li>
        <li>Send an email to all participants stating if they won or not</li>
    </ul>

    <form action="<?php echo filter_input(INPUT_POST, 'REQUEST_URI'); ?>" method="POST">
        <?php wp_nonce_field('confirm_final_winners', 'session_marker'); ?>
        <p class="submit">
            <button name="confirm_final_winners" class="button-primary">
                Confirm Final Winners
            </button>
        </p>
    </form>
</div>