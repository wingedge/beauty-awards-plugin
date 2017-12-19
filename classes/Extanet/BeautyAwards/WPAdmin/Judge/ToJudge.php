<?php

namespace Extanet\BeautyAwards\WPAdmin\Judge;

use Alekhin\WebsiteHelpers\Address;
use Alekhin\WebsiteHelpers\ReturnObject;
use Extanet\BeautyAwards\Core\Judges;
use Extanet\BeautyAwards\Core\BeautyAwards;
use Extanet\BeautyAwards\WPAdmin\WPAdmin;

class ToJudge {

    const session_key_posted = __CLASS__ . '\posted';

    static $p;

    static function link_list() {
        $a = new Address(admin_url('admin.php?page=baw_judge'));
        return $a->url();
    }

    static function link_judge($category_entry_id, $judged_return = FALSE) {
        $a = new Address(self::link_list());
        $a->query['action'] = 'judge';
        $a->query['id'] = intval(trim($category_entry_id));
        if ($judged_return) {
            $a->query['jr'] = 1;
        }
        return $a->url();
    }

    static function link_previous($category_entry_id) {
        if (($previous_id = self::get_previous_id($category_entry_id)) > 0) {
            return self::link_judge($previous_id);
        }
        return NULL;
    }

    static function link_next($category_entry_id) {
        if (($previous_id = self::get_next_id($category_entry_id)) > 0) {
            return self::link_judge($previous_id);
        }
        return NULL;
    }

    static function get_previous_id($category_entry_id) {
        $previous = 0;
        foreach (Judges::get_to_judge(get_current_user_id()) as $tji) {
            if ($tji->id == $category_entry_id && $previous > 0) {
                return $previous;
            }
            $previous = $tji->id;
        }
        return 0;
    }

    static function get_next_id($category_entry_id) {
        $match = FALSE;
        foreach (Judges::get_to_judge(get_current_user_id()) as $tji) {
            if ($match) {
                return $tji->id;
            }
            if ($tji->id == $category_entry_id) {
                $match = TRUE;
            }
        }
        return 0;
    }

    static function get_category_entry_id_from_get() {
        return intval(trim(filter_input(INPUT_GET, 'id')));
    }

    static function judge_entry() {
        $r = new ReturnObject();
        $r->data->nonce = filter_input(INPUT_POST, 'session_marker');
        $r->data->id = intval(trim(filter_input(INPUT_POST, 'category_entry_id')));
        $r->data->judge_id = get_current_user_id();
        $r->data->rating = max(0, min(10, intval(trim(filter_input(INPUT_POST, 'rating')))));
        $r->data->comment = trim(filter_input(INPUT_POST, 'comment'));

        if (!wp_verify_nonce($r->data->nonce, 'judge_entry')) {
            $r->message = 'Invalid session! Please refresh this page.';
            return $r;
        }
        if (!current_user_can(Judges::role_key_judge)) {
            $r->message = 'You must be a judge to rate entries!';
            return $r;
        }

        if (!Judges::can_juudge_entry($r->data->judge_id, $r->data->id)) {
            $r->message = 'You are not allowed to judge in this category!';
            return $r;
        }

        if ($r->data->rating < 1) {
            $r->message = 'Select a rating for this entry!';
            return $r;
        }

        if (intval(trim(filter_input(INPUT_GET, 'jr'))) == 1) {
            $r->redirect = Judged::link_list();
        } else {
            if (($next_id = self::get_previous_id($r->data->id)) == 0) {
                $next_id = self::get_next_id($r->data->id);
            }
            $r->redirect = ($next_id > 0) ? self::link_judge($next_id) : self::link_list();
        }

        Judges::score($r->data->id, $r->data->judge_id, $r->data->rating, $r->data->comment);

        $r->data = new \stdClass();
        $r->success = TRUE;
        $r->message = 'Entry has been scored!';
        return $r;
    }

    static function on_init() {
        if (isset($_SESSION[self::session_key_posted])) {
            self::$p = $_SESSION[self::session_key_posted];
            unset($_SESSION[self::session_key_posted]);
        }
    }

    static function on_admin_menu() {
        $to_judge = Judges::count_to_judge(get_current_user_id());
        add_submenu_page('baw_judge', 'To Judge (' . $to_judge . ')', 'To Judge (' . $to_judge . ')', Judges::role_key_judge, 'baw_judge', [__CLASS__, 'view_main',]);
    }

    static function on_current_screen() {
        if (get_current_screen()->id != 'toplevel_page_baw_judge') {
            return;
        }

        if (empty($action = trim(filter_input(INPUT_GET, 'action')))) {
            $action = 'list';
        }
        if (!in_array($action, ['judge', 'list',])) {
            wp_redirect(self::link_list());
            exit;
        }

        if ($action == 'judge') {
            //if (!is_null(filter_input(INPUT_GET, 'id'))) {
            //    if (!CategoriesSource::exists(intval(trim(filter_input(INPUT_GET, 'parent_id'))))) {
            //        wp_redirect(self::link_list());
            //        exit;
            //    }
            //}

            if (!is_null(filter_input(INPUT_POST, 'judge_entry'))) {
                self::$p = $_SESSION[self::session_key_posted] = self::judge_entry();
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
        if (!in_array($action = trim(filter_input(INPUT_GET, 'action')), ['judge',])) {
            $action = 'list';
        }
        switch ($action) {
            case 'judge':
                include BeautyAwards::get_dir('/views/wp-admin/judge/judge.php');
                break;
            default:
                include BeautyAwards::get_dir('/views/wp-admin/judge/to-judge.php');
        }
    }

    static function initialize($pf) {
        add_action('init', [__CLASS__, 'on_init',]);
        add_action('admin_menu', [__CLASS__, 'on_admin_menu',]);
        add_action('current_screen', [__CLASS__, 'on_current_screen',]);
        add_action('admin_notices', [__CLASS__, 'on_admin_notices',]);
    }

}
