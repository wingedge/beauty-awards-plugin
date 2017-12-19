<?phpuse Extanet\BeautyAwards\Core\Contest;use Extanet\BeautyAwards\Core\Judges;use Extanet\BeautyAwards\WPAdmin\Judge\ToJudge;$to_judge = Judges::get_to_judge(get_current_user_id());?><div id="box_to_judge" class="wrap">    <h1 class="wp-heading-inline">To Judge</h1>    <hr class="wp-header-end" />    <?php if (!Contest::status() || time() > Contest::end_date()): ?>        <p>Thank you for Judging! Judging is currently closed. <?php echo date('M j, Y', Contest::start_date()); ?> to <?php echo date('M j, Y', Contest::end_date()); ?></p>    <?php else: ?>        <?php if (empty($to_judge)): ?>            <p>There is currently no entry to be judged.</p>        <?php else: ?>            <table class="widefat striped">                <thead>                    <tr>                        <th>Title</th>                        <th>Description</th>                        <th>Category</th>                        <th class="col-judge"></th>                    </tr>                </thead>                <tbody>                    <?php foreach ($to_judge as $tji): ?>                        <tr>                            <td><?php echo $tji->title; ?></td>                            <td><?php echo $tji->description; ?></td>                            <td><?php echo $tji->name; ?></td>                            <td class="col-judge">                                <a href="<?php echo ToJudge::link_judge($tji->id); ?>" class="button-secondary">Judge</a>                            </td>                        </tr>                    <?php endforeach; ?>                </tbody>            </table>        <?php endif; ?>    <?php endif; ?></div>