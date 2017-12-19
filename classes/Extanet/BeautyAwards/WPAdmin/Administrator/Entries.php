<?php

namespace Extanet\BeautyAwards\WPAdmin\Administrator;

use Alekhin\WebsiteHelpers\Address;
use Alekhin\WebsiteHelpers\ReturnObject;
use Extanet\BeautyAwards\Core\BeautyAwards;
use Extanet\BeautyAwards\Core\Categories;
use Extanet\BeautyAwards\Core\Entries as EntriesSource;
use Extanet\BeautyAwards\WPAdmin\WPAdmin;

class Entries {

    const session_key_posted = __CLASS__ . '\posted';
    const items_per_page = 20;

    static $p = NULL;

    static function link_list($page = 1, $filter_email = NULL, $filter_categories = NULL) {
        $a = new Address(admin_url('admin.php?page=baw_contest_entries'));

        $page = min(self::get_max_pages(), max(1, intval(trim($page))));
        if ($page > 1) {
            $a->query['current_page'] = $page;
        }

        if (!is_null($filter_email) && is_email($filter_email)) {
            $a->query['filter_email'] = $filter_email;
        }

        if (!is_null($filter_categories) && is_array($filter_categories)) {
            $cs = [];
            foreach ($filter_categories as $cat_id) {
                $cat_id = intval(trim($cat_id));
                if (Categories::exists($cat_id)) {
                    $cs[] = $cat_id;
                }
            }
            if (!empty($cs)) {
                $a->query['filter_categories'] = implode(',', $cs);
            }
        }

        //echo '<pre>' . print_r($a, TRUE) . '</pre>';
        //echo '<pre>' . print_r($a->url(), TRUE) . '</pre>';
        return $a->url();
    }

    static function link_edit($entry_id) {
        $a = new Address(admin_url('admin.php?page=baw_contest_entries'));
        $a->query['action'] = 'edit';
        $a->query['entry_id'] = intval(trim($entry_id));
        return $a->url();
    }

    static function link_disqualify($entry_id) {
        $a = new Address(admin_url('admin.php?page=baw_contest_entries'));
        $a->query['action'] = 'disqualify';
        $a->query['entry_id'] = intval(trim($entry_id));
        return $a->url();
    }

    static function get_max_pages($filter_email = NULL, $filter_categories = NULL) {
        $count = EntriesSource::count($filter_email, $filter_categories);
        return max(1, ceil($count / self::items_per_page));
    }

    static function get_page_from_get() {
        return max(1, intval(trim(filter_input(INPUT_GET, 'current_page'))));
    }

    static function get_entry_id_from_get() {
        return intval(trim(filter_input(INPUT_GET, 'entry_id')));
    }

    static function get_filter_value_email() {
        $email = filter_input(INPUT_GET, 'filter_email');
        if (is_null($email)) {
            return NULL;
        }
        return trim($email);
    }

    static function get_filter_value_categories() {
        $cs = [];
        $cats = explode(',', trim(filter_input(INPUT_GET, 'filter_categories')));
        foreach ($cats as $cat) {
            $cat = intval(trim($cat));
            if ($cat > 0) {
                $cs[] = $cat;
            }
        }
        if (empty($cs)) {
            return NULL;
        }
        return $cs;
    }

    static function get_category_names($entry_id, $add_link = FALSE) {
        $cn = [];
        $categories = EntriesSource::get_categories($entry_id);
        foreach ($categories as $category_id) {
            $string = '';
            if ($add_link) {
                $string .= '<a href="' . self::link_list(self::get_page_from_get(), self::get_filter_value_email(), [$category_id,]) . '">';
            }
            $string .= Categories::get_name($category_id);
            if ($add_link) {
                $string .= '</a>';
            }
            $cn[$category_id] = $string;
        }
        return $cn;
    }

    static function save_entry_changes() {
        $r = new ReturnObject();
        $r->data->nonce = filter_input(INPUT_POST, 'session_marker');
        $r->data->id = intval(trim(filter_input(INPUT_POST, 'entry_id')));
        $r->data->title = trim(filter_input(INPUT_POST, 'title'));
        $r->data->description = trim(filter_input(INPUT_POST, 'description'));

        if (!wp_verify_nonce($r->data->nonce, 'edit_entry')) {
            $r->message = 'Invalid session! Please refresh this page.';
            return $r;
        }

        if (!current_user_can('manage_options')) {
            $r->message = 'You are not an administrator! You can\'t edit entries.';
            return $r;
        }

        if (empty($r->data->title)) {
            $r->message = 'Enter the title of this entry!';
            return $r;
        }

        if (empty($r->data->description)) {
            $r->message = 'Enter the description of this entry!';
            return $r;
        }

        $re = EntriesSource::update_info($r->data->id, $r->data->title, $r->data->description);
        if (!$re->success) {
            $r->message = $re->message;
            return $r;
        }

        $r->success = TRUE;
        $r->message = 'The entry has been edited successfully!';
        return $r;
    }

    static function disqualify_entry() {
        $r = new ReturnObject();
        $r->data->nonce = filter_input(INPUT_POST, 'session_marker');
        $r->data->entry_id = intval(trim(filter_input(INPUT_POST, 'entry_id')));

        if (!wp_verify_nonce($r->data->nonce, 'disqualify_entry')) {
            $r->message = 'Invalid session! Please refresh this page.';
            return $r;
        }

        if (!current_user_can('manage_options')) {
            $r->message = 'You are not an administrator! You can\'t edit entries.';
            return $r;
        }

        $re = EntriesSource::disqualify($r->data->entry_id);
        if (!$re->success) {
            $r->message = $re->message;
            return $r;
        }

        $r->redirect = self::link_list();
        $r->success = TRUE;
        $r->message = 'The entry has been disqualified!';
        return $r;
    }

    static function filter_by_action() {
        $r = new ReturnObject();
        $r->success = TRUE;

        if (!wp_verify_nonce(filter_input(INPUT_POST, 'session_marker'), 'filter_by_action')) {
            return $r;
        }

        $filter_email = trim(filter_input(INPUT_POST, 'filter_by_email'));
        $filter_category = intval(trim(filter_input(INPUT_POST, 'filter_by_categories')));

        $r->redirect = self::link_list(self::get_page_from_get(), !empty($filter_email) && is_email($filter_email) ? $filter_email : NULL, $filter_category > 0 && Categories::exists($filter_category) ? [$filter_category,] : NULL);
        return $r;
    }

    static function on_init() {
        if (isset($_SESSION[self::session_key_posted])) {
            self::$p = $_SESSION[self::session_key_posted];
            unset($_SESSION[self::session_key_posted]);
        }
    }

    static function on_admin_menu() {
        add_submenu_page('baw_contest', 'Manage Contest Entries', 'Entries', 'manage_options', 'baw_contest_entries', [__CLASS__, 'view_main',]);
    }

    static function on_current_screen() {
        if (get_current_screen()->id != 'contest_page_baw_contest_entries') {
            return;
        }

        if (empty($action = trim(filter_input(INPUT_GET, 'action')))) {
            $action = 'list';
        }
        if (!in_array($action, ['edit', 'disqualify', 'list',])) {
            wp_redirect(self::link_list());
            exit;
        }

        if ($action == 'edit') {
            if (!EntriesSource::exists($entry_id = self::get_entry_id_from_get())) {
                wp_redirect(self::link_list());
                exit;
            }

            if (!is_null(filter_input(INPUT_POST, 'save_entry'))) {
                self::$p = $_SESSION[self::session_key_posted] = self::save_entry_changes();
                wp_redirect(self::$p->redirect);
                exit;
            }
        }
        if ($action == 'disqualify') {
            if (!EntriesSource::exists($entry_id = self::get_entry_id_from_get())) {
                wp_redirect(self::link_list());
                exit;
            }

            if (!is_null(filter_input(INPUT_POST, 'disqualify_entry'))) {
                self::$p = $_SESSION[self::session_key_posted] = self::disqualify_entry();
                wp_redirect(self::$p->redirect);
                exit;
            }
        }
        if ($action == 'list') {
            if (!is_null(filter_input(INPUT_POST, 'filter_by_action'))) {
                self::$p = self::filter_by_action();
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
        if (!in_array($action = trim(filter_input(INPUT_GET, 'action')), ['edit', 'disqualify',])) {
            $action = 'list';
        }
        switch ($action) {
            case 'edit':
                include BeautyAwards::get_dir('/views/wp-admin/administrator/entries/edit.php');
                break;
            case 'disqualify':
                include BeautyAwards::get_dir('/views/wp-admin/administrator/entries/disqualify.php');
                break;
            default:
                include BeautyAwards::get_dir('/views/wp-admin/administrator/entries/list.php');
        }
    }

    static function view_pagination() {
        $page_links = paginate_links([
            //'base' => self::link_list(1, self::get_filter_value_email(), self::get_filter_value_categories()),
            'base' => '%_%',
            'format' => '?current_page=%#%',
            'prev_text' => '&laquo;',
            'next_text' => '&raquo;',
            'total' => self::get_max_pages(self::get_filter_value_email(), self::get_filter_value_categories()),
            'current' => self::get_page_from_get(),
        ]);

        if ($page_links) {
            echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0">' . $page_links . '</div></div>';
        }
    }

    static function initialize($pf) {
        add_action('init', [__CLASS__, 'on_init',]);
        add_action('admin_menu', [__CLASS__, 'on_admin_menu',]);
        add_action('current_screen', [__CLASS__, 'on_current_screen',]);
        add_action('admin_notices', [__CLASS__, 'on_admin_notices',]);
    }

}
