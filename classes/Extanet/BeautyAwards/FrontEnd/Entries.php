<?php

namespace Extanet\BeautyAwards\FrontEnd;

use Alekhin\WebsiteHelpers\Address as AddressHelper;
use Alekhin\WebsiteHelpers\ReturnObject;
use Extanet\BeautyAwards\Core\EntryItem;
use Extanet\BeautyAwards\Core\EntryImage;

class Entries {

    const session_key_entries = __CLASS__ . '\entries';
    const session_key_editing_index = __CLASS__ . '\editing_index';
    const session_key_next_screen = __CLASS__ . '\next_screen';

    static function session_reset() {
        $sks = [];
        $sks[] = self::session_key_entries;
        $sks[] = self::session_key_editing_index;
        $sks[] = self::session_key_next_screen;
        foreach ($sks as $sk) {
            if (isset($_SESSION[$sk])) {
                unset($_SESSION[$sk]);
            }
        }
    }

    static function link_edit_entry($index) {
        $a = new AddressHelper($_SERVER['REQUEST_URI']);
        $a->query['baw_action'] = 'edit_entry';
        $a->query['baw_entry_index'] = intval(trim($index));
        return wp_nonce_url($a->url(), 'edit_entry', 'session_marker');
    }

    static function link_delete_entry($index) {
        $a = new AddressHelper($_SERVER['REQUEST_URI']);
        $a->query['baw_action'] = 'delete_entry';
        $a->query['baw_entry_index'] = intval(trim($index));
        return wp_nonce_url($a->url(), 'delete_entry', 'session_marker');
    }

    static function prepare_entries() {
        if (!isset($_SESSION[self::session_key_entries])) {
            $_SESSION[self::session_key_entries] = [];
        }

        if (!is_array($_SESSION[self::session_key_entries])) {
            $_SESSION[self::session_key_entries] = [];
        }
    }

    static function editing_index($index = NULL) {
        if (!is_null($index)) {
            $_SESSION[self::session_key_editing_index] = max([-1, intval(trim($index)),]);
        }

        if (!isset($_SESSION[self::session_key_editing_index])) {
            return -1;
        }

        return $_SESSION[self::session_key_editing_index];
    }

    static function get_entries() {
        self::prepare_entries();
        return $_SESSION[self::session_key_entries];
    }

    static function count_entries() {
        self::prepare_entries();
        return count($_SESSION[self::session_key_entries]);
    }

    static function add_entry() {
        $r = new ReturnObject();

        self::prepare_entries();
        self::editing_index(count($_SESSION[self::session_key_entries]));
        $_SESSION[self::session_key_entries][] = new EntryItem();

        $r->success = TRUE;
        $r->message = 'Entry added!';
        return $r;
    }

    static function next_screen($next_screen = NULL) {
        if (!is_null($next_screen)) {
            $_SESSION[self::session_key_next_screen] = $next_screen;
        }
        if (!isset($_SESSION[self::session_key_next_screen])) {
            return EntryForm::step_address;
        }
        return $_SESSION[self::session_key_next_screen];
    }

    static function title($entry_index, $title = NULL) {
        if (!is_null($title)) {
            $_SESSION[self::session_key_entries][$entry_index]->title = $title;
        }

        return $_SESSION[self::session_key_entries][$entry_index]->title;
    }

    static function image_id($entry_index, $image_id = NULL) {
        if (!is_null($image_id)) {
            EntryImage::delete_image(self::image_id($entry_index));
            $_SESSION[self::session_key_entries][$entry_index]->images[0] = $image_id;
        }

        if (!isset($_SESSION[self::session_key_entries][$entry_index]->images[0])) {
            return 0;
        }

        return $_SESSION[self::session_key_entries][$entry_index]->images[0];
    }

    static function description($entry_index, $description = NULL) {
        if (!is_null($description)) {
            $_SESSION[self::session_key_entries][$entry_index]->description = $description;
        }

        return $_SESSION[self::session_key_entries][$entry_index]->description;
    }

    static function categories($entry_index, $categories = NULL) {
        if (!is_null($categories)) {
            $_SESSION[self::session_key_entries][$entry_index]->categories = is_array($categories) ? $categories : [];
        }

        return $_SESSION[self::session_key_entries][$entry_index]->categories;
    }

    static function edit_entry() {
        $r = new ReturnObject();
        $r->data->nonce = filter_input(INPUT_GET, 'session_marker');
        $r->data->index = intval(trim(filter_input(INPUT_GET, 'baw_entry_index')));

        $a = new AddressHelper($_SERVER['REQUEST_URI']);
        if (isset($a->query['baw_action'])) {
            unset($a->query['baw_action']);
        }
        if (isset($a->query['baw_entry_index'])) {
            unset($a->query['baw_entry_index']);
        }
        if (isset($a->query['session_marker'])) {
            unset($a->query['session_marker']);
        }
        $r->redirect = $a->url();

        if (!wp_verify_nonce($r->data->nonce, 'edit_entry')) {
            $r->message = 'Invalid session! Please refresh this page.';
            return $r;
        }

        self::editing_index(min(self::count_entries() - 1, max(0, $r->data->index)));

        $r->success = TRUE;
        $r->message = 'Edit!';
        return $r;
    }

    static function remove_entry($index, $bypass = FALSE) {
        $r = new ReturnObject();
        $r->data->nonce = filter_input(INPUT_GET, 'session_marker');
        $r->data->index = intval(trim(filter_input(INPUT_GET, 'baw_entry_index')));

        if (!$bypass && !wp_verify_nonce($r->data->nonce, 'delete_entry')) {
            $r->message = 'Invalid session! Please refresh this page.';
            return $r;
        }

        $a = new AddressHelper($_SERVER['REQUEST_URI']);
        if (isset($a->query['baw_action'])) {
            unset($a->query['baw_action']);
        }
        if (isset($a->query['session_marker'])) {
            unset($a->query['session_marker']);
        }
        $r->redirect = $a->url();

        if (isset($_SESSION[self::session_key_entries][$index])) {
            unset($_SESSION[self::session_key_entries][$index]);
        }
        $_SESSION[self::session_key_entries] = array_values($_SESSION[self::session_key_entries]);

        $r->success = TRUE;
        $r->message = 'Delete!';
        return $r;
    }

    static function clear_entries() {
        unset($_SESSION[self::session_key_entries]);
        self::prepare_entries();
    }

    static function confirm_entries() {
        $r = new ReturnObject();
        $r->data->nonce = filter_input(INPUT_POST, 'session_marker');

        if (!wp_verify_nonce($r->data->nonce, 'any_action_entries')) {
            $r->message = 'Invalid session! Please refresh the page.';
            return $r;
        }

        if (empty(self::get_entries())) {
            $r->message = 'Please add your entries before continuing!';
            return $r;
        }
        self::next_screen(EntryForm::step_address);

        $r->success = TRUE;
        $r->message = 'Entries confirmed!';
        return $r;
    }

    static function view_nonce() {
        wp_nonce_field('any_action_entries', 'session_marker');
    }

}
