<?php

namespace Extanet\BeautyAwards\WPAdmin\Administrator;

use Alekhin\WebsiteHelpers\ReturnObject;
use Extanet\BeautyAwards\Core\BeautyAwards;
use Extanet\BeautyAwards\Core\Settings as SettingsCore;
use Extanet\BeautyAwards\WPAdmin\WPAdmin;

class Settings {

    const session_key_posted = __CLASS__ . '\posted';

    static $p = NULL;

    static function get_wp_pages() {
        $pages = [];

        $args = new \stdClass();
        $args->posts_per_page = -1;
        $args->post_type = 'page';
        $args->orderby = 'title';
        $args->order = 'ASC';
        foreach (get_posts((array) $args) as $po) {
            $pages[$po->ID] = $po->post_title;
        }

        return $pages;
    }

    static function save_changes() {
        $r = new ReturnObject();
        $r->data->nonce = filter_input(INPUT_POST, 'session_marker');
        $r->data->stripe_api_private_key = trim(filter_input(INPUT_POST, 'stripe_api_private_key'));
        $r->data->stripe_api_public_key = trim(filter_input(INPUT_POST, 'stripe_api_public_key'));
        $r->data->photo_tips_page_id = intval(trim(filter_input(INPUT_POST, 'photo_tips_page_id')));

        if (!wp_verify_nonce($r->data->nonce, 'manage_settings')) {
            $r->message = 'Invalid session! Please refresh this page.';
            return $r;
        }

        SettingsCore::stripe_private_key($r->data->stripe_api_private_key);
        SettingsCore::stripe_public_key($r->data->stripe_api_public_key);
        SettingsCore::photo_tips_page_id($r->data->photo_tips_page_id);

        $r->success = TRUE;
        $r->message = 'Your changes have been saved!';
        return $r;
    }

    static function on_init() {
        if (isset($_SESSION[self::session_key_posted])) {
            self::$p = $_SESSION[self::session_key_posted];
            unset($_SESSION[self::session_key_posted]);
        }
    }

    static function on_admin_menu() {
        add_submenu_page('baw_contest', 'Manage Settings', 'Settings', 'manage_options', 'baw_contest_settings', [__CLASS__, 'view_main',]);
    }

    static function on_current_screen() {
        if (get_current_screen()->id != 'contest_page_baw_contest_settings') {
            return;
        }

        if (!is_null(filter_input(INPUT_POST, 'save_changes'))) {
            self::$p = $_SESSION[self::session_key_posted] = self::save_changes();
            wp_redirect(self::$p->redirect);
            exit;
        }
    }

    static function on_admin_notices() {
        if (is_null(self::$p)) {
            return;
        }

        WPAdmin::view_notice(self::$p->message, self::$p->success);
    }

    static function view_main() {
        include BeautyAwards::get_dir('/views/wp-admin/administrator/settings.php');
    }

    static function initialize($pf) {
        add_action('init', [__CLASS__, 'on_init',]);
        add_action('admin_menu', [__CLASS__, 'on_admin_menu',]);
        add_action('current_screen', [__CLASS__, 'on_current_screen',]);
        add_action('admin_notices', [__CLASS__, 'on_admin_notices',]);
    }

}
