<?php if (empty($winners)): ?>
    <p>No winning entries found.</p>
<?php else: ?>
    <?php $rank = 1; ?>
    <table class="widefat striped">
        <thead>
            <tr>
                <th>Rank</th>
                <th>Name</th>
                <th>Title</th>
                <th>Description</th>
                <th>Country</th>
                <th>Score</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($winners as $winner): ?>
                <tr>
                    <th><?php echo '#' . $rank; ?></th>
                    <th><?php echo $winner->name; ?></th>
                    <th><?php echo $winner->title; ?></th>
                    <th><?php echo $winner->description; ?></th>
                    <th><?php echo $winner->shipping_country; ?></th>
                    <th><?php echo $winner->score; ?></th>
                </tr>
                <?php $rank++; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
<?php
