<?php

use Extanet\BeautyAwards\WPAdmin\Administrator\Settings;
use Extanet\BeautyAwards\Core\Settings as SettingsCore;
?>
<div id="box_contest_manage" class="wrap">
    <h1 class="wp-heading-inline">Contest Management</h1>
    <hr class="wp-header-end" />

    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">
        <?php wp_nonce_field('manage_settings', 'session_marker'); ?>
        <h2>Payment Settings</h2>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="txt_entry_fee">Stripe API Secret Key</label>
                    </th>
                    <td>
                        <input type="text" id="txt_stripe_api_private_key" name="stripe_api_private_key" value="<?php echo (is_null(Settings::$p) ? SettingsCore::stripe_private_key() : Settings::$p->get_data('stripe_api_private_key')); ?>" placeholder="Stripe API Private Key" class="widefat" />
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="txt_entry_fee">Stripe API Public Key</label>
                    </th>
                    <td>
                        <input type="text" id="txt_stripe_api_public_key" name="stripe_api_public_key" value="<?php echo (is_null(Settings::$p) ? SettingsCore::stripe_public_key() : Settings::$p->get_data('stripe_api_public_key')); ?>" placeholder="Stripe API Public Key" class="widefat" />
                    </td>
                </tr>
            </tbody>
        </table>
        <h2>Form Settings</h2>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="ddl_photo_tips_page_id">Photo Tips Page</label>
                    </th>
                    <td>
                        <select id="ddl_photo_tips_page_id" name="photo_tips_page_id" class="widefat">
                            <option value="0">-- select page--</option>
                            <?php foreach (Settings::get_wp_pages() as $id => $title): ?>
                                <option value="<?php echo $id; ?>"<?php echo (is_null(Settings::$p) ? SettingsCore::photo_tips_page_id() : Settings::$p->get_data('photo_tips_page_id')) == $id ? ' selected="selected"' : ''; ?>><?php echo $title; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="submit">
            <button id="btn_save_changes" name="save_changes" class="button-primary">
                Save Changes
            </button>
        </p>
    </form>
</div>
