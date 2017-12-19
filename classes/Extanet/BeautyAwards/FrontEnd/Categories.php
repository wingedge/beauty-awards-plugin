<?php

namespace Extanet\BeautyAwards\FrontEnd;

use Alekhin\WebsiteHelpers\Address;
use Alekhin\WebsiteHelpers\ReturnObject;

class Categories {

    const session_key_show_all = __CLASS__ . '\show_all';

    static function session_reset() {
        $sks = [];
        $sks[] = self::session_key_show_all;
        foreach ($sks as $sk) {
            if (isset($_SESSION[$sk])) {
                unset($_SESSION[$sk]);
            }
        }
    }

    static function link_show_all_categories() {
        $a = new Address($_SERVER['REQUEST_URI']);
        $a->query['baw_action'] = 'show_all_categories';
        return wp_nonce_url($a->url(), 'show_all_categories', 'session_marker');
    }

    static function link_hide_other_categories() {
        $a = new Address($_SERVER['REQUEST_URI']);
        $a->query['baw_action'] = 'hide_other_categories';
        return wp_nonce_url($a->url(), 'hide_other_categories', 'session_marker');
    }

    static function link_back_to_entry_details() {
        $a = new Address($_SERVER['REQUEST_URI']);
        $a->query['baw_action'] = 'back_to_entry_details';
        return wp_nonce_url($a->url(), 'back_to_entry_details', 'session_marker');
    }

    static function show_all($show_all = NULL) {
        if (!is_null($show_all)) {
            $_SESSION[self::session_key_show_all] = ($show_all == TRUE);
        }

        if (!isset($_SESSION[self::session_key_show_all])) {
            return FALSE;
        }

        return $_SESSION[self::session_key_show_all] == TRUE;
    }

    static function show_all_categories() {
        $r = new ReturnObject();
        $r->data->nonce = filter_input(INPUT_GET, 'session_marker');

        $a = new Address($_SERVER['REQUEST_URI']);
        if (isset($a->query['baw_action'])) {
            unset($a->query['baw_action']);
        }
        if (isset($a->query['session_marker'])) {
            unset($a->query['session_marker']);
        }
        $r->redirect = $a->url();

        if (!wp_verify_nonce($r->data->nonce, 'show_all_categories')) {
            $r->message = 'Invalid session! Please refresh this page.';
            return $r;
        }

        self::show_all(TRUE);

        $r->success = TRUE;
        $r->message = 'All categories shown!';
        return $r;
    }

    static function hide_other_categories() {
        $r = new ReturnObject();
        $r->data->nonce = filter_input(INPUT_GET, 'session_marker');

        $a = new Address($_SERVER['REQUEST_URI']);
        if (isset($a->query['baw_action'])) {
            unset($a->query['baw_action']);
        }
        if (isset($a->query['session_marker'])) {
            unset($a->query['session_marker']);
        }
        $r->redirect = $a->url();

        if (!wp_verify_nonce($r->data->nonce, 'hide_other_categories')) {
            $r->message = 'Invalid session! Please refresh this page.';
            return $r;
        }

        self::show_all(FALSE);

        $r->success = TRUE;
        $r->message = 'Other categories hidden!';
        return $r;
    }

    static function choose_categories() {
        $r = new ReturnObject();
        $r->data->nonce = filter_input(INPUT_POST, 'session_marker');
        if (!is_array($r->data->categories = isset($_POST['subcategories']) ? $_POST['subcategories'] : [])) {
            $r->data->categories = [];
        }

        if (!wp_verify_nonce($r->data->nonce, 'choose_catogories')) {
            $r->message = 'Invalid session! Please refresh this page.';
            return $r;
        }

        if (empty($r->data->categories)) {
            $r->message = 'Please select the categories you want to submit this entry to!';
            return $r;
        }

        Entries::categories(Entries::editing_index(), $r->data->categories);

        $r->success = TRUE;
        $r->message = 'Categories chosen set!';
        return $r;
    }

    static function back_to_entry_details() {
        $r = new ReturnObject();
        $r->data->nonce = filter_input(INPUT_GET, 'session_marker');
        $r->data->index = intval(trim(filter_input(INPUT_GET, 'baw_entry_index')));

        $a = new Address($_SERVER['REQUEST_URI']);
        if (isset($a->query['baw_action'])) {
            unset($a->query['baw_action']);
        }
        if (isset($a->query['session_marker'])) {
            unset($a->query['session_marker']);
        }
        $r->redirect = $a->url();

        if (!wp_verify_nonce($r->data->nonce, 'back_to_entry_details')) {
            $r->message = 'Invalid session! Please refresh this page.';
            return $r;
        }

        $r->success = TRUE;
        $r->message = 'Back!';
        return $r;
    }

    static function view_nonce() {
        wp_nonce_field('choose_catogories', 'session_marker');
    }

}
