<?php

use Extanet\BeautyAwards\FrontEnd\Address;
use Extanet\BeautyAwards\FrontEnd\EntryForm;
?>
<form id="form_entry_address" action="<?php echo filter_input(INPUT_POST, ''); ?>" method="POST">
    <?php Address::view_nonce(); ?>
    <h2>If your entry is selected, where do we mail your award to?</h2>
    <?php EntryForm::view_post_message(); ?>
    <p>
        <input type="text" id="txt_name" name="name" placeholder="Name" value="<?php echo (!is_null(EntryForm::$p)) ? EntryForm::$p->get_data('name') : Address::name(); ?>" />
    </p>
    <p>
        <select id="ddl_country" name="country">
            <option value="">( select country )</option>
            <?php Address::view_countries_options(!is_null(EntryForm::$p) ? EntryForm::$p->get_data('country') : Address::country()); ?>
        </select>
    </p>
    <div class="address-form"<?php echo (!is_null(EntryForm::$p) ? EntryForm::$p->get_data('country') : Address::country()) != '' ? '' : ' style="display: none;"'; ?>>
        <p>
            <input type="address" id="txt_address1" name="address1" placeholder="Address" value="<?php echo (!is_null(EntryForm::$p)) ? EntryForm::$p->get_data('address1') : Address::address1(); ?>" />
        </p>
        <p>
            <input type="address" id="txt_address2" name="address2" placeholder="Address (line 2)" value="<?php echo (!is_null(EntryForm::$p)) ? EntryForm::$p->get_data('address2') : Address::address2(); ?>" />
        </p>
        <p id="par_us_state">
            <select id="ddl_state" name="state">
                <option value="">( select state )</option>
                <?php Address::view_usa_states_options((!is_null(EntryForm::$p) ? EntryForm::$p->get_data('country') : Address::country()) == 'US' ? (!is_null(EntryForm::$p) ? EntryForm::$p->get_data('state') : Address::state()) : NULL); ?>
            </select>
        </p>
        <p id="par_canada_province">
            <select id="ddl_province" name="province">
                <option value="">( select province )</option>
                <?php Address::view_canada_provinces_options((!is_null(EntryForm::$p) ? EntryForm::$p->get_data('country') : Address::country()) == 'CA' ? (!is_null(EntryForm::$p) ? EntryForm::$p->get_data('state') : Address::state()) : NULL); ?>
            </select>
        </p>
        <p id="par_other_state_province">
            <input type="text" id="txt_state_province" name="state_province" placeholder="State / Province / Region" value="<?php echo (!in_array((!is_null(EntryForm::$p) ? EntryForm::$p->get_data('country') : Address::country()), ['US', 'CA',])) ? (!is_null(EntryForm::$p) ? EntryForm::$p->get_data('state') : Address::state()) : ''; ?>" />
        </p>
        <p>
            <input type="text" id="txt_city" name="city" placeholder="City / Town" value="<?php echo (!is_null(EntryForm::$p)) ? EntryForm::$p->get_data('city') : Address::city(); ?>" />
        </p>
        <p>
            <input type="text" id="txt_postal_code" name="postal_code" placeholder="ZIP / Postal code" value="<?php echo (!is_null(EntryForm::$p)) ? EntryForm::$p->get_data('postal_code') : Address::postal_code(); ?>" />
        </p>
    </div>
    <p class="submit">
        <button id="btn_submit_address" name="submit_address" value="1" class="button-primary">
            Continue to payment
        </button>
    </p>
    <p>
        <a href="<?php echo Address::link_back_to_entries(); ?>">Back to entries</a>
    </p>
</form>
