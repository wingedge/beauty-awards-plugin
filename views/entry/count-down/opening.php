<?php

use Extanet\BeautyAwards\Core\Contest;
?>
<div class="countdown-notice">
    <?php if (Contest::countdown_opening()): ?>
        <p>Entry submission will open in <span class="baw-countdown" data-target="<?php echo Contest::start_date(); ?>">...</span></p>
    <?php else: ?>
        <p>Entry submission is closed at the moment.</p>
    <?php endif; ?>
</div>
<?php
