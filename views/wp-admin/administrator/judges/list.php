<?php

use Extanet\BeautyAwards\Core\Categories;
use Extanet\BeautyAwards\Core\Judges as JudgesSource;
use Extanet\BeautyAwards\WPAdmin\Administrator\Judges;
?>
<div id="box_judges_manage" class="wrap">
    <h1 class="wp-heading-inline">Judges</h1>
    <a href="<?php echo admin_url('users.php'); ?>" class="page-title-action">Manage Judges in WordPress' Users</a>
    <hr class="wp-header-end" />

    <?php if (empty($judges = JudgesSource::get_judges())): ?>
        <p>You have no judges. Create their accounts on <a href="<?php echo admin_url('users.php'); ?>">WordPress' Users section</a>.</p>
    <?php else: ?>
        <table class="widefat striped judges">
            <thead>
                <tr>
                    <th class="col-judge">Judge</th>
                    <th class="col-categories">Categories</th>
                    <th class="col-entries-to-judge">To Judge</th>
                    <th class="col-entries-judged">Judged</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($judges as $judge): ?>
                    <?php $categories = JudgesSource::get_categories($judge->ID); ?>
                    <tr data-id="<?php echo $judge - ID; ?>">
                        <td class="col-judge">
                            <div>
                                <strong><?php echo $judge->display_name; ?></strong>
                                <small>(<?php echo $judge->user_login; ?>)</small>
                            </div>
                            <div>
                                <a href="<?php echo Judges::link_assign_categories($judge->ID); ?>" class="button-secondary" data-categories="<?php echo json_encode($categories); ?>">Assign Categories</a>
                            </div>
                        </td>
                        <td class="col-categories">
                            <?php if (empty($categories)): ?>
                                <em>No categories assigned</em>
                            <?php else: ?>
                                <?php foreach ($categories as $parent_id => $subcategories): ?>
                                    <div>
                                        <strong><?php echo Categories::get_name($parent_id); ?></strong>
                                        <?php foreach ($subcategories as $category_id): ?>&nbsp;&ndash;&nbsp;<?php echo Categories::get_name($category_id); ?><?php endforeach; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </td>
                        <td class="col-entries-to-judge"><?php echo JudgesSource::count_to_judge($judge->ID); ?></td>
                        <td class="col-entries-judged"><?php echo JudgesSource::count_judged($judge->ID); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        <?php endif; ?>
    </table>
</div>
