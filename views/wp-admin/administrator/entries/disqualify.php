<?php

use Extanet\BeautyAwards\Core\Entries as EntriesSource;
use Extanet\BeautyAwards\WPAdmin\Administrator\Entries;
use Extanet\BeautyAwards\Core\EntryImage;

$entry_id = Entries::get_entry_id_from_get();
$entry = EntriesSource::get_entry($entry_id);
?>
<div id="box_entries_manage" class="wrap entry-edit">
    <h1 class="wp-heading-inline">Edit Entry</h1>
    <hr class="wp-header-end" />

    <p>
        <a href="<?php echo Entries::link_list(); ?>" class="button-secondary">
            <span class="dashicons dashicons-arrow-left-alt2"></span>
            Back to entries
        </a>
    </p>

    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">
        <?php wp_nonce_field('disqualify_entry', 'session_marker'); ?>
        <input type="hidden" name="entry_id" value="<?php echo $entry_id; ?>" />
        <p><strong>Are you sure you want to disqualify this entry?</strong></p>
        <p class="submit">
            <button id="btn_disqualify_entry" name="disqualify_entry" class="button-primary">
                Disqualify Entry
            </button>
        </p>
    </form>

    <table class="form-table entry-edit">
        <tbody>
            <tr>
                <th scope="row">Entry Image</th>
                <td>
                    <div class="entry-image" style="background-image: url('<?php echo EntryImage::get_source(EntriesSource::get_images($entry->id)[0]); ?>');"></div>
                </td>
            </tr>
            <tr>
                <th scope="row">Title</th>
                <td>
                    <?php echo $entry->title; ?>
                </td>
            </tr>
            <tr>
                <th scope="row">Description</th>
                <td>
                    <?php echo $entry->description; ?>
                </td>
            </tr>
        </tbody>
    </table>

    <h2>Entry Details</h2>
    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row">Name</th>
                <td><?php echo $entry->name; ?></td>
            </tr>
            <tr>
                <th scope="row">Email</th>
                <td><?php echo $entry->email; ?></td>
            </tr>
            <tr>
                <th scope="row">Name on award</th>
                <td><?php echo $entry->award_name; ?></td>
            </tr>
        </tbody>
    </table>

    <h2>Payment</h2>
    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row">Status</th>
                <td>
                    <?php if (empty($entry->payment_transaction_id)): ?>
                        <strong>UNPAID</strong>
                    <?php else: ?>
                        Paid
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th scope="row">Transaction ID</th>
                <td><?php echo $entry->payment_transaction_id; ?></td>
            </tr>
        </tbody>
    </table>

    <h2>Shipping Details</h2>
    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row">Name</th>
                <td><?php echo $entry->shipping_name; ?></td>
            </tr>
            <tr>
                <th scope="row">Country</th>
                <td><?php echo $entry->shipping_country; ?></td>
            </tr>
            <tr>
                <th scope="row">Address</th>
                <td><?php echo $entry->shipping_address1; ?></td>
            </tr>
            <?php if (!empty($entry->shipping_address2)): ?>
                <tr>
                    <th scope="row">Address (line 2)</th>
                    <td><?php echo $entry->shipping_address2; ?></td>
                </tr>
            <?php endif; ?>
            <tr>
                <th scope="row">State / Province</th>
                <td><?php echo $entry->shipping_state; ?></td>
            </tr>
            <tr>
                <th scope="row">City / Town</th>
                <td><?php echo $entry->shipping_city; ?></td>
            </tr>
            <tr>
                <th scope="row">ZIP / Postal code</th>
                <td><?php echo $entry->shipping_postal_code; ?></td>
            </tr>
        </tbody>
    </table>
</div>
