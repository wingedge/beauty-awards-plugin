<?php

use Extanet\BeautyAwards\Core\Categories;
use Extanet\BeautyAwards\Core\Contest;
use Extanet\BeautyAwards\Core\EntryImage;
use Extanet\BeautyAwards\FrontEnd\Address;
use Extanet\BeautyAwards\FrontEnd\Entries;
use Extanet\BeautyAwards\FrontEnd\EntryForm;
use Extanet\BeautyAwards\FrontEnd\Payment;
use Extanet\BeautyAwards\FrontEnd\UserInfo;
use Alekhin\Geo\Countries;
use Alekhin\Geo\CanadaProvinces;
use Alekhin\Geo\USAStates;
?>
<form id="form_entry_payment" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">
    <?php Payment::view_nonce(); ?>
    <h2>Your award submission fee</h2>
    <?php EntryForm::view_post_message(); ?>

    <div class="payment-user-info">
        <table class="info-details">
            <tbody>
                <tr>
                    <td>Name</td>
                    <td><?php echo UserInfo::name(); ?></td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td><?php echo UserInfo::email(); ?></td>
                </tr>
                <tr>
                    <td>Name to display on award</td>
                    <td><?php echo UserInfo::award_name(); ?></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td>
                        <a href="<?php echo Payment::link_change_user_info(); ?>">Change</a>
                    </td>
                    <td>&nbsp;</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="payment-address-info">
        <table class="info-details">
            <tbody>
                <tr>
                    <td><strong>Shipping address</strong></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;&nbsp;Name</td>
                    <td><?php echo Address::name(); ?></td>
                </tr>
                <tr>
                    <td>&nbsp;&nbsp;Country</td>
                    <td><?php echo Countries::get_name(Address::country()); ?></td>
                </tr>
                <tr>
                    <td>&nbsp;&nbsp;Address</td>
                    <td>
                        <?php echo Address::address1(); ?>
                        <?php if (!empty(Address::address2())): ?>
                            <br /><?php echo Address::address2(); ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php if (Address::country() == 'US'): ?>
                            &nbsp;&nbsp;State
                        <?php elseif (Address::country() == 'CA'): ?>
                            &nbsp;&nbsp;Province
                        <?php else: ?>
                            &nbsp;&nbsp;State / Province
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (Address::country() == 'US'): ?>
                            <?php echo USAStates::get_name(Address::state()); ?>
                        <?php elseif (Address::country() == 'CA'): ?>
                            <?php echo CanadaProvinces::get_name(Address::state()); ?>
                        <?php else: ?>
                            <?php echo Address::state(); ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php if (Address::country() == 'US' || Address::country() == 'CA'): ?>
                            &nbsp;&nbsp;City
                        <?php else: ?>
                            &nbsp;&nbsp;City / Town
                        <?php endif; ?>
                    </td>
                    <td><?php echo Address::city(); ?></td>
                </tr>
                <tr>
                    <td>
                        <?php if (Address::country() == 'US'): ?>
                            &nbsp;&nbsp;ZIP code
                        <?php elseif (Address::country() == 'CA'): ?>
                            &nbsp;&nbsp;Postal code
                        <?php else: ?>
                            &nbsp;&nbsp;ZIP / Postal code
                        <?php endif; ?>
                    </td>
                    <td><?php echo Address::postal_code(); ?></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td>
                        <a href="<?php echo Payment::link_back_to_address(); ?>">Change</a>
                    </td>
                    <td>&nbsp;</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="payment-cart">
        <table class="payment-cart-details">
            <tbody>
                <?php $total = 0; ?>
                <?php $tax = 0; ?>
                <?php foreach (Entries::get_entries() as $entry): ?>
                    <?php if (count($entry->categories) == 1): ?>
                        <tr>
                            <td class="col-entry-thumbnail">
                                <div class="entry-thumgnail-container" style="background-image: url('<?php echo EntryImage::get_source($entry->images[0]); ?>');"></div>
                            </td>
                            <td class="col-cart-item">
                                <strong><?php echo $entry->title; ?></strong>
                                &mdash;
                                <?php echo Categories::get_name($entry->categories[0]); ?>
                            </td>
                            <td class="col-amount">$<?php echo Contest::entry_fee(); ?></td>
                        </tr>
                        <?php $total += Contest::entry_fee(); ?>
                    <?php else: ?>
                        <tr>
                            <td rowspan="<?php echo (count($entry->categories) + 1); ?>" class="col-entry-thumbnail">
                                <div class="entry-thumgnail-container" style="background-image: url('<?php echo EntryImage::get_source($entry->images[0]); ?>');"></div>
                            </td>
                            <td class="col-cart-item"><strong><?php echo $entry->title; ?></strong></td>
                            <td class="col-amount"></td>
                        </tr>
                        <?php foreach ($entry->categories as $category_id): ?>
                            <tr>
                                <td class="col-cart-item">&nbsp;&ndash;&nbsp;<?php echo Categories::get_name($category_id); ?></td>
                                <td class="col-amount">$<?php echo Contest::entry_fee(); ?></td>
                            </tr>
                            <?php $total += Contest::entry_fee(); ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php if (Address::country() == 'US' && Address::state() == 'CT'): ?>
                    <?php $tax = round($total * .0635, 2); ?>
                    <?php $total = $total + $tax; ?>
                    <tr>
                        <td class="col-cart-item">
                            6.35% Tax
                        </td>
                        <td class="col-amount">$<?php echo $tax; ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <tbody>
                <tr>
                    <td class="col-cart-item" colspan="2"><strong>Total</strong></td>
                    <td class="col-amount">$<?php echo number_format($total, (intval($total) == $total) ? 0 : 2); ?></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">
                        <a href="<?php echo Payment::link_change_entries(); ?>">Change</a>
                    </td>
                    <td>&nbsp;</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="payment-form">
        <div id="box_card_element"></div>
        <div id="box_card_errors" role="alert"></div>
    </div>

    <p>
        <label for="chk_confirm_details">
            <input type="checkbox" id="chk_confirm_details" name="confirm_details" value="1" />
            I have checked all my submission details and they are correct
        </label>
    </p>

    <p class="submit">
        <button id="btn_submit_payment" name="submit_payment" value="1" class="button-primary">
            Submit
        </button>
    </p>
    <p>
        <a href="<?php echo Payment::link_back_to_address(); ?>">Back to address</a>
    </p>
</form>
