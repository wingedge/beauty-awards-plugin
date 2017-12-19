<?php
use Extanet\BeautyAwards\FrontEnd\EntryForm;
?>
<div class="modal-overlay">
    <div class="modal-box">
        <header class="modal-header">
            <?php echo EntryForm::photo_tips_title(); ?>
            <a href class="modal-header-close"></a>
        </header>
        <section class="modal-content">
            <?php echo EntryForm::photo_tips_content(); ?>
        </section>
    </div>
</div>
