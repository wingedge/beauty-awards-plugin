<?php

use Extanet\BeautyAwards\Core\Entries as EntriesSource;
use Extanet\BeautyAwards\WPAdmin\Administrator\Entries;
use Extanet\BeautyAwards\Core\EntryImage;
use Extanet\BeautyAwards\Core\Categories;

$entries = EntriesSource::get_entries(Entries::items_per_page, Entries::get_page_from_get(), Entries::get_filter_value_email(), Entries::get_filter_value_categories());
?>
<div id="box_entries_manage" class="wrap">
    <h1 class="wp-heading-inline">Entries</h1>
    <hr class="wp-header-end" />

    <div class="tablenav top">
        <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">
            <?php wp_nonce_field('filter_by_action', 'session_marker'); ?>
            <div class="alignleft actions">
                <input type="text" name="filter_by_email" value="<?php echo trim(Entries::get_filter_value_email()); ?>" placeholder="Filter by email..." />
                <?php if (!is_array($current_filter = Entries::get_filter_value_categories())): ?>
                    <?php $current_filter = []; ?>
                <?php endif; ?>
                <select name="filter_by_categories">
                    <option value="0">(select category)</option>
                    <?php foreach ([Categories::type_profession, Categories::type_industry,] as $type): ?>
                        <?php foreach (Categories::get_categories_by_type($type) as $parent): ?>
                            <optgroup label="<?php echo $parent->name; ?>">
                                <?php foreach (Categories::get_sub_categories($parent->id) as $category): ?>
                                    <option value="<?php echo $category->id; ?>"<?php echo in_array($category->id, $current_filter) ? ' selected="selected"' : ''; ?>><?php echo $category->name; ?></option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </select>
                <input type="submit" name="filter_by_action" id="post-query-submit" class="button" value="Filter">
            </div>
        </form>
    </div>
    <?php if (empty($entries)): ?>
        <p>No entries have been submitted yet.</p>
    <?php else: ?>
        <table class="widefat striped entries">
            <thead>
                <tr>
                    <th class="col-picture"></th>
                    <th class="col-entry">Entry</th>
                    <th class="col-description">Description</th>
                    <th class="col-user">User</th>
                    <th class="col-categories">Categories</th>
                    <th class="col-submitted">Submitted</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($entries as $entry): ?>
                    <tr id="entry-<?php echo $entry->id; ?>" class="iedit hentry">
                        <td class="col-picture"><div style="background-image: url('<?php echo EntryImage::get_source(EntriesSource::get_images($entry->id)[0]); ?>');"></div></td>
                        <td class="col-title column-title has-row-actions column-primary page-title">
                            <strong>
                                <a class="row-title" href="<?php echo Entries::link_edit($entry->id); ?>"><?php echo $entry->title; ?></a>
                            </strong>
                            <?php if ($entry->disqualified == 1): ?>
                                <strong>Disqualified</strong>
                            <?php endif; ?>
                            <div class="row-actions">
                                <span class="edit"><a href="<?php echo Entries::link_edit($entry->id); ?>">Edit</a> | </span>
                                <span class="trash"><a href="<?php echo Entries::link_disqualify($entry->id); ?>" class="submitdelete">Disqualify</a></span>
                            </div>
                        </td>
                        <td class="col-description"><?php echo $entry->description; ?></td>
                        <td class="col-user" author column-author>
                            <?php echo $entry->name; ?><br />
                            <a href="<?php echo Entries::link_list(Entries::get_page_from_get(), $entry->email, Entries::get_filter_value_categories()); ?>"><?php echo $entry->email; ?></a>
                        </td>
                        <td class="col-categories">
                            <?php echo implode(', ', Entries::get_category_names($entry->id, TRUE)); ?>
                        </td>
                        <td class="col-submitted date column-date">
                            <?php echo date('M j, Y', $entry->created); ?><br />
                            <?php echo date('G:i A', $entry->created); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <?php Entries::view_pagination(); ?>
</div>
