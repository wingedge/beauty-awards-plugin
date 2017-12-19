<?php

namespace Extanet\BeautyAwards\FrontEnd;

use Alekhin\WebsiteHelpers\Address;
use Alekhin\WebsiteHelpers\ReturnObject;

class Industry {

    const session_key_industries = __CLASS__ . '\industries';

    static function session_reset() {
        $sks = [];
        $sks[] = self::session_key_industries;
        foreach ($sks as $sk) {
            if (isset($_SESSION[$sk])) {
                unset($_SESSION[$sk]);
            }
        }
    }

    static function link_skip_industries() {
        $a = new Address($_SERVER['REQUEST_URI']);
        $a->query['baw_action'] = 'skip_industries';
        return wp_nonce_url($a->url(), 'skip_industries', 'session_marker');
    }

    static function link_back_to_user_info() {
        $a = new Address($_SERVER['REQUEST_URI']);
        $a->query['baw_action'] = 'back_to_user_info';
        return wp_nonce_url($a->url(), 'back_to_user_info', 'session_marker');
    }

    static function industries($industries = NULL) {
        if (!is_null($industries) && is_array($industries)) {
            foreach ($industries as &$profession) {
                $profession = intval(trim($profession));
            }
            unset($profession);
            $_SESSION[self::session_key_industries] = $industries;
        }
        if (!isset($_SESSION[self::session_key_industries])) {
            return [];
        }
        if (!is_array($_SESSION[self::session_key_industries])) {
            return [];
        }
        return $_SESSION[self::session_key_industries];
    }

    static function choose_industry() {
        $r = new ReturnObject();
        $r->data->nonce = filter_input(INPUT_POST, 'session_marker');
        if (!is_array($r->data->industries = isset($_POST['industries']) ? $_POST['industries'] : [])) {
            $r->data->industries = [];
        }

        if (!wp_verify_nonce($r->data->nonce, 'choose_industry')) {
            $r->message = 'Invalid session! Please refresh this page.';
            return $r;
        }

        if (empty($r->data->industries)) {
            $r->message = 'Please select your industries!';
            return $r;
        }

        self::industries($r->data->industries);

        $r->success = TRUE;
        $r->message = 'Industries set!';
        return $r;
    }

    static function skip_industries() {
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

        if (!wp_verify_nonce($r->data->nonce, 'skip_industries')) {
            $r->message = 'Invalid session! Please refresh this page.';
            return $r;
        }

        $r->success = TRUE;
        $r->message = 'Industries skipped!';
        return $r;
    }

    static function back_to_user_info() {
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

        if (!wp_verify_nonce($r->data->nonce, 'back_to_user_info')) {
            $r->message = 'Invalid session! Please refresh this page.';
            return $r;
        }

        $r->success = TRUE;
        $r->message = 'Back!';
        return $r;
    }

    static function view_nonce() {
        wp_nonce_field('choose_industry', 'session_marker');
    }

}
