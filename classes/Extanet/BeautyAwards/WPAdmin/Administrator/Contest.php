<?php

namespace Extanet\BeautyAwards\WPAdmin\Administrator;

use Alekhin\WebsiteHelpers\ReturnObject;
use Extanet\BeautyAwards\Core\BeautyAwards;
use Extanet\BeautyAwards\Core\Contest as ContestSource;
use Extanet\BeautyAwards\WPAdmin\WPAdmin;

class Contest {

    const session_key_posted = __CLASS__ . '\posted';

    static $p = NULL;

    static function save_changes() {
        $r = new ReturnObject();
        $r->data->nonce = filter_input(INPUT_POST, 'session_marker');
        $r->data->date = new \stdClass();
        $r->data->date->start = new \stdClass();
        $r->data->date->start->month = intval(trim(filter_input(INPUT_POST, 'startdate_month')));
        $r->data->date->start->day = intval(trim(filter_input(INPUT_POST, 'startdate_day')));
        $r->data->date->start->year = intval(trim(filter_input(INPUT_POST, 'startdate_year')));
        $r->data->date->start->uts = 0;
        $r->data->date->end = new \stdClass();
        $r->data->date->end->month = intval(trim(filter_input(INPUT_POST, 'enddate_month')));
        $r->data->date->end->day = intval(trim(filter_input(INPUT_POST, 'enddate_day')));
        $r->data->date->end->year = intval(trim(filter_input(INPUT_POST, 'enddate_year')));
        $r->data->date->end->uts = 0;
        $r->data->status = intval(trim(filter_input(INPUT_POST, 'status'))) === 1;
        $r->data->countdown = new \stdClass();
        $r->data->countdown->opening = intval(trim(filter_input(INPUT_POST, 'countdown_opening'))) === 1;
        $r->data->countdown->closing = intval(trim(filter_input(INPUT_POST, 'countdown_closing'))) === 1;
        $r->data->entry_fee = floatval(trim(filter_input(INPUT_POST, 'entry_fee')));

        if (!wp_verify_nonce($r->data->nonce, 'manage_contest')) {
            $r->message = 'Invalid session! Please refresh this page.';
            return $r;
        }

        if ($r->data->date->start->month < 1 || $r->data->date->start->month > 12) {
            $r->message = 'Please select the start month!';
            return $r;
        }
        if ($r->data->date->start->day < 1 || $r->data->date->start->day > 31) {
            $r->message = 'Please enter a valid start day!';
            return $r;
        }
        if (!checkdate($r->data->date->start->month, $r->data->date->start->day, $r->data->date->start->year)) {
            $r->message = 'Please enter a valid start date!';
            return $r;
        }
        $r->data->date->start->uts = mktime(0, 0, 0, $r->data->date->start->month, $r->data->date->start->day, $r->data->date->start->year);

        if ($r->data->date->end->month < 1 || $r->data->date->end->month > 12) {
            $r->message = 'Please select the end month!';
            return $r;
        }
        if ($r->data->date->end->day < 1 || $r->data->date->end->day > 31) {
            $r->message = 'Please enter a valid end day!';
            return $r;
        }
        if (!checkdate($r->data->date->end->month, $r->data->date->end->day, $r->data->date->end->year)) {
            $r->message = 'Please enter a valid start date!';
            return $r;
        }
        $r->data->date->end->uts = mktime(23, 59, 59, $r->data->date->end->month, $r->data->date->end->day, $r->data->date->end->year);

        ContestSource::start_date($r->data->date->start->uts);
        ContestSource::end_date($r->data->date->end->uts);
        ContestSource::status($r->data->status);
        ContestSource::countdown_opening($r->data->countdown->opening);
        ContestSource::countdown_closing($r->data->countdown->closing);
        ContestSource::entry_fee($r->data->entry_fee);

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
        add_submenu_page('baw_contest', 'Contest Management', 'Manage', 'manage_options', 'baw_contest', [__CLASS__, 'view_main',]);
    }

    static function on_current_screen() {
        if (get_current_screen()->id != 'toplevel_page_baw_contest') {
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
        include BeautyAwards::get_dir('/views/wp-admin/administrator/contest.php');
    }

    static function initialize($pf) {
        add_action('init', [__CLASS__, 'on_init',]);
        add_action('admin_menu', [__CLASS__, 'on_admin_menu',]);
        add_action('current_screen', [__CLASS__, 'on_current_screen',]);
        add_action('admin_notices', [__CLASS__, 'on_admin_notices',]);
    }

}
