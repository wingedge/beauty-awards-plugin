<?php

use Extanet\BeautyAwards\FrontEnd\EntryForm;
?>
<div class="message<?php echo EntryForm::$p->success ? ' message-success' : ' message-error'; ?>">
    <p><?php echo EntryForm::$p->message; ?></p>
</div>
