<?php

use Extanet\BeautyAwards\FrontEnd\EntryForm;
?>
<div id="box_entry_form">
    <header class="entry-header">
        <div class="progress-bar"><div class="progress-value" style="width: <?php echo ((EntryForm::session_step() - 1) / 8) * 100; ?>%;"></div></div>
    </header>
    <div class="entry-view">
