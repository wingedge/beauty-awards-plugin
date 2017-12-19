<?php

namespace Extanet\BeautyAwards\WPAdmin\Administrator;

use Alekhin\WebsiteHelpers\Address;
use Alekhin\WebsiteHelpers\ReturnObject;
use Extanet\BeautyAwards\Core\BeautyAwards;
use Extanet\BeautyAwards\Core\Categories as CategoriesSource;
use Extanet\BeautyAwards\WPAdmin\WPAdmin;

class Categories {

    const session_key_posted = __CLASS__ . '\posted';

    static $p = NULL;

    static function link_list() {
        $a = new Address(admin_url('admin.php?page=baw_categories'));
        return $a->url();
    }

    static function link_add_new($parent_id = NULL) {
        $a = new Address(self::link_list());
        $a->query['action'] = 'add';
        if (!is_null($parent_id)) {
            $parent_id = intval(trim($parent_id));
            if ($parent_id > 0) {
                $a->query['parent_id'] = $parent_id;
            }
        }
        return $a->url();
    }

    static function link_add_back() {
        $parent_id = intval(trim(filter_input(INPUT_GET, 'parent_id')));
        if ($parent_id > 0) {
            return self::link_edit($parent_id);
        }
        return self::link_list();
    }

    static function link_edit($id) {
        $a = new Address(self::link_list());
        $a->query['action'] = 'edit';
        $a->query['id'] = intval(trim($id));
        return $a->url();
    }

    static function link_edit_back($id) {
        if (($parent_id = CategoriesSource::get_parent_id($id)) == 0) {
            return self::link_list();
        }
        return self::link_edit($parent_id);
    }

    static function link_delete($id) {
        $a = new Address(self::link_list());
        $a->query['action'] = 'delete';
        $a->query['id'] = intval(trim($id));
        return $a->url();
    }

    static function add_new() {
        $r = new ReturnObject();
        $r->data->nonce = trim(filter_input(INPUT_POST, 'session_marker'));
        $r->data->name = trim(filter_input(INPUT_POST, 'name'));
        $r->data->description = trim(filter_input(INPUT_POST, 'description'));
        $r->data->winnings = floatval(trim(filter_input(INPUT_POST, 'winnings')));
        $r->data->type = intval(trim(filter_input(INPUT_POST, 'type')));

        if (!wp_verify_nonce($r->data->nonce, 'add_category')) {
            $r->message = 'Invalid session! Please refresh the page.';
            return $r;
        }

        if (!current_user_can('manage_options')) {
            $r->message = 'You do not have permission to add categories!';
            return $r;
        }

        $category_type = 0;
        $parent_id = 0;
        if ($r->data->type < 0) {
            $category_type = abs($r->data->type);
        }
        if ($r->data->type > 0) {
            $parent_id = $r->data->type;
        }
        $result = CategoriesSource::add_category($r->data->name, $r->data->description, $r->data->winnings, $category_type, $parent_id);
        if (!$result->success) {
            $r->message = $result->message;
            return $r;
        }

        $r->success = TRUE;
        $r->message = 'New category has been added!';
        if ($parent_id == 0) {
            $r->redirect = self::link_list();
        } else {
            $r->redirect = self::link_edit($parent_id);
        }
        return $r;
    }

    static function save_category() {
        $r = new ReturnObject();
        $r->data->nonce = trim(filter_input(INPUT_POST, 'session_marker'));
        $r->data->id = intval(trim(filter_input(INPUT_POST, 'category_id')));
        $r->data->name = trim(filter_input(INPUT_POST, 'name'));
        $r->data->description = trim(filter_input(INPUT_POST, 'description'));
        $r->data->winnings = floatval(trim(filter_input(INPUT_POST, 'winnings')));
        $r->data->type = intval(trim(filter_input(INPUT_POST, 'type')));

        if (!wp_verify_nonce($r->data->nonce, 'edit_category')) {
            $r->message = 'Invalid session! Please refresh the page.';
            return $r;
        }

        if (!current_user_can('manage_options')) {
            $r->message = 'You do not have permission to edit categories!';
            return $r;
        }

        $category_type = 0;
        $parent_id = 0;
        if ($r->data->type < 0) {
            $category_type = abs($r->data->type);
        }
        if ($r->data->type > 0) {
            $parent_id = $r->data->type;
        }
        $result = CategoriesSource::save_category($r->data->id, $r->data->name, $r->data->description, $r->data->winnings, $category_type, $parent_id);
        if (!$result->success) {
            $r->message = $result->message;
            return $r;
        }

        $r->success = TRUE;
        $r->message = 'Category changes have been saved!';
        if ($parent_id == 0) {
            $r->redirect = self::link_list();
        } else {
            $r->redirect = self::link_edit($parent_id);
        }
        return $r;
    }

    static function delete_category() {
        $r = new ReturnObject();
        $r->data->nonce = trim(filter_input(INPUT_POST, 'session_marker'));
        $r->data->id = intval(trim(filter_input(INPUT_POST, 'category_id')));

        if (!wp_verify_nonce($r->data->nonce, 'delete_category')) {
            $r->message = 'Invalid session! Please refresh the page.';
            return $r;
        }

        if (!current_user_can('manage_options')) {
            $r->message = 'You do not have permission to delete categories!';
            return $r;
        }

        $parent_id = CategoriesSource::get_parent_id($r->data->id);

        $result = CategoriesSource::delete_category($r->data->id);
        if (!$result->success) {
            $r->message = $result->message;
            return $r;
        }

        $r->success = TRUE;
        $r->message = 'Category has been deleted!';
        if ($parent_id == 0) {
            $r->redirect = self::link_list();
        } else {
            $r->redirect = self::link_edit($parent_id);
        }
        return $r;
    }

    static function sort_categories() {
        $r = new ReturnObject();
        if (!is_array($r->data->ids = isset($_POST['ids']) ? $_POST['ids'] : [])) {
            $r->data->ids = [];
        }

        if (!current_user_can('manage_options')) {
            $r->message = 'You do not have permission to sort categories!';
            return $r;
        }

        CategoriesSource::sort_categories($r->data->ids);

        $r->success = TRUE;
        $r->message = 'Categories have been sorted succesfully!';
        return $r;
    }

    static function on_init() {
        if (isset($_SESSION[self::session_key_posted])) {
            self::$p = $_SESSION[self::session_key_posted];
            unset($_SESSION[self::session_key_posted]);
        }
    }

    static function on_admin_menu() {
        add_submenu_page('baw_contest', 'Manage Categories', 'Categories', 'manage_options', 'baw_categories', [__CLASS__, 'view_main',]);
    }

    static function on_current_screen() {
        if (get_current_screen()->id != 'contest_page_baw_categories') {
            return;
        }

        if (empty($action = trim(filter_input(INPUT_GET, 'action')))) {
            $action = 'list';
        }
        if (!in_array($action, ['add', 'edit', 'delete', 'list',])) {
            wp_redirect(self::link_list());
            exit;
        }

        if ($action == 'add') {
            if (!is_null(filter_input(INPUT_GET, 'parent_id'))) {
                if (!CategoriesSource::exists(intval(trim(filter_input(INPUT_GET, 'parent_id'))))) {
                    wp_redirect(self::link_list());
                    exit;
                }
            }

            if (!is_null(filter_input(INPUT_POST, 'add_category'))) {
                self::$p = $_SESSION[self::session_key_posted] = self::add_new();
                wp_redirect(self::$p->redirect);
                exit;
            }
        }
        if ($action == 'edit') {
            if (!CategoriesSource::exists($id = intval(trim(filter_input(INPUT_GET, 'id'))))) {
                wp_redirect(self::link_list());
                exit;
            }

            if (!is_null(filter_input(INPUT_POST, 'save_category'))) {
                self::$p = $_SESSION[self::session_key_posted] = self::save_category();
                wp_redirect(self::$p->redirect);
                exit;
            }
        }
        if ($action == 'delete') {
            if (!CategoriesSource::exists($id = intval(trim(filter_input(INPUT_GET, 'id'))))) {
                wp_redirect(self::link_list());
                exit;
            }

            if (!is_null(filter_input(INPUT_POST, 'delete_category'))) {
                self::$p = $_SESSION[self::session_key_posted] = self::delete_category();
                wp_redirect(self::$p->redirect);
                exit;
            }
        }
        if ($action == 'list') {
            
        }
    }

    static function on_admin_notices() {
        if (is_null(self::$p)) {
            return;
        }

        WPAdmin::view_notice(self::$p->message, self::$p->success);
    }

    static function ajax_categories_sort() {
        self::sort_categories();
        exit;
    }

    static function filter_baw_remove_category($categories, $category_id) {
        for ($counter = 0; $counter < count($categories); $counter++) {
            if ($categories[$counter]->id == $category_id) {
                unset($categories[$counter]);
            }
        }
        return array_values($categories);
    }

    static function view_list() {
        include BeautyAwards::get_dir('/views/wp-admin/administrator/categories/list.php');
    }

    static function view_add() {
        include BeautyAwards::get_dir('/views/wp-admin/administrator/categories/add.php');
    }

    static function view_edit() {
        $category_id = intval(trim(filter_input(INPUT_GET, 'id')));
        include BeautyAwards::get_dir('/views/wp-admin/administrator/categories/edit.php');
    }

    static function view_delete() {
        $category_id = intval(trim(filter_input(INPUT_GET, 'id')));
        include BeautyAwards::get_dir('/views/wp-admin/administrator/categories/delete.php');
    }

    static function view_main() {
        if (!in_array($action = trim(filter_input(INPUT_GET, 'action')), ['add', 'edit', 'delete',])) {
            $action = 'list';
        }
        switch ($action) {
            case 'add':
                self::view_add();
                break;
            case 'edit':
                self::view_edit();
                break;
            case 'delete':
                self::view_delete();
                break;
            default:
                self::view_list();
        }
    }

    static function initialize($pf) {
        add_action('init', [__CLASS__, 'on_init',]);
        add_action('admin_menu', [__CLASS__, 'on_admin_menu',]);
        add_action('current_screen', [__CLASS__, 'on_current_screen',]);
        add_action('admin_notices', [__CLASS__, 'on_admin_notices',]);

        add_action('wp_ajax_baw_categories_sort', [__CLASS__, 'ajax_categories_sort',]);

        add_filter('baw_remove_category', [__CLASS__, 'filter_baw_remove_category',], 10, 2);
    }

}
