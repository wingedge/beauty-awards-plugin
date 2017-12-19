<?php

namespace Extanet\BeautyAwards\WPAdmin\Administrator;

use Alekhin\WebsiteHelpers\Address;
use Alekhin\WebsiteHelpers\ReturnObject;
use Extanet\BeautyAwards\Core\BeautyAwards;
use Extanet\BeautyAwards\Core\Judges as JudgesSource;
use Extanet\BeautyAwards\WPAdmin\WPAdmin;

class Judges {

    const session_key_posted = __CLASS__ . '\posted';

    static $p = NULL;

    static function link_judges_list() {
        $a = new Address(admin_url('admin.php?page=baw_contest_judges'));
        return $a->url();
    }

    static function link_assign_categories($judge_id) {
        $a = new Address(self::link_judges_list());
        $a->query['action'] = 'assign';
        $a->query['judge_id'] = intval(trim($judge_id));
        return $a->url();
    }

    static function get_judge_id_from_get() {
        return intval(trim(filter_input(INPUT_GET, 'judge_id')));
    }

    static function assign_categories() {
        $r = new ReturnObject();
        $r->data->judge_id = intval(trim(filter_input(INPUT_POST, 'judge_id')));
        if (!is_array($r->data->categories = isset($_POST['categories']) ? $_POST['categories'] : [])) {
            $r->data->categories = [];
        }

        JudgesSource::set_categories($r->data->judge_id, $r->data->categories);

        $r->redirect = self::link_judges_list();
        $r->success = TRUE;
        $r->message = 'Categories assigned to judge!';
        return $r;
    }

    static function on_init() {
        if (isset($_SESSION[self::session_key_posted])) {
            self::$p = $_SESSION[self::session_key_posted];
            unset($_SESSION[self::session_key_posted]);
        }
    }

    static function on_admin_menu() {
        add_submenu_page('baw_contest', 'Manage Contest Judges', 'Judges', 'manage_options', 'baw_contest_judges', [__CLASS__, 'view_main',]);
    }

    static function on_current_screen() {
        if (get_current_screen()->id != 'contest_page_baw_contest_judges') {
            return;
        }

        if (empty($action = trim(filter_input(INPUT_GET, 'action')))) {
            $action = 'list';
        }
        if (!in_array($action, ['assign', 'list',])) {
            wp_redirect(self::link_list());
            exit;
        }

        if ($action == 'assign') {
            if (is_null(filter_input(INPUT_GET, 'judge_id')) || (!is_null(filter_input(INPUT_GET, 'judge_id')) && !JudgesSource::exists(intval(trim(filter_input(INPUT_GET, 'judge_id')))))) {
                wp_redirect(self::link_judges_list());
                exit;
            }

            if (!is_null(filter_input(INPUT_POST, 'assign_judge_categories'))) {
                self::$p = $_SESSION[self::session_key_posted] = self::assign_categories();
                wp_redirect(self::$p->redirect);
                exit;
            }
        }
    }

    static function on_admin_notices() {
        if (is_null(self::$p)) {
            return;
        }

        WPAdmin::view_notice(self::$p->message, self::$p->success);
    }

    static function view_main() {
        if (!in_array($action = trim(filter_input(INPUT_GET, 'action')), ['assign',])) {
            $action = 'list';
        }
        if ($action == 'assign') {
            include BeautyAwards::get_dir('/views/wp-admin/administrator/judges/assign.php');
        } else {
            include BeautyAwards::get_dir('/views/wp-admin/administrator/judges/list.php');
        }
    }

    static function initialize($pf) {
        add_action('init', [__CLASS__, 'on_init',]);
        add_action('admin_menu', [__CLASS__, 'on_admin_menu',]);
        add_action('current_screen', [__CLASS__, 'on_current_screen',]);
        add_action('admin_notices', [__CLASS__, 'on_admin_notices',]);
    }

}
