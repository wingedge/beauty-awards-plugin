<?php

namespace Extanet\BeautyAwards\FrontEnd;

use Alekhin\WebsiteHelpers\Address as AddressHelper;
use Alekhin\WebsiteHelpers\ReturnObject;
use Extanet\BeautyAwards\Core\Leads;

class UserInfo {

    const session_key_name = __CLASS__ . '\name';
    const session_key_email = __CLASS__ . '\email';
    const session_key_award_name = __CLASS__ . '\award_name';
    const session_key_next_screen = __CLASS__ . '\next_screen';

    static function session_reset() {
        $sks = [];
        $sks[] = self::session_key_name;
        $sks[] = self::session_key_email;
        $sks[] = self::session_key_award_name;
        $sks[] = self::session_key_next_screen;
        foreach ($sks as $sk) {
            if (isset($_SESSION[$sk])) {
                unset($_SESSION[$sk]);
            }
        }
    }

    static function link_back_to_profession() {
        $a = new AddressHelper($_SERVER['REQUEST_URI']);
        $a->query['baw_action'] = 'back_to_profession';
        return wp_nonce_url($a->url(), 'back_to_profession', 'session_marker');
    }

    static function next_screen($next_screen = NULL) {
        if (!is_null($next_screen)) {
            $_SESSION[self::session_key_next_screen] = $next_screen;
        }
        if (!isset($_SESSION[self::session_key_next_screen])) {
            return EntryForm::step_industry;
        }
        return $_SESSION[self::session_key_next_screen];
    }

    static function name($name = NULL) {
        if (!is_null($name)) {
            $_SESSION[self::session_key_name] = $name;
        }
        if (!isset($_SESSION[self::session_key_name])) {
            return '';
        }
        return trim($_SESSION[self::session_key_name]);
    }

    static function email($email = NULL) {
        if (!is_null($email)) {
            $_SESSION[self::session_key_email] = $email;
        }
        if (!isset($_SESSION[self::session_key_email])) {
            return '';
        }
        return trim($_SESSION[self::session_key_email]);
    }

    static function award_name($name = NULL) {
        if (!is_null($name)) {
            $_SESSION[self::session_key_award_name] = $name;
        }
        if (!isset($_SESSION[self::session_key_award_name])) {
            return '';
        }
        return trim($_SESSION[self::session_key_award_name]);
    }

    static function submit_user_info() {
        $r = new ReturnObject();
        $r->data->nonce = filter_input(INPUT_POST, 'session_marker');
        $r->data->name = trim(filter_input(INPUT_POST, 'name'));
        $r->data->email = trim(filter_input(INPUT_POST, 'email'));
        $r->data->award_name = trim(filter_input(INPUT_POST, 'award_name'));

        if (!wp_verify_nonce($r->data->nonce, 'submit_user_info')) {
            $r->message = 'Invalid session! Please refresh this page.';
            return $r;
        }

        if (empty($r->data->name)) {
            $r->message = 'Please enter your name!';
            return $r;
        }
        if (empty($r->data->email)) {
            $r->message = 'Please enter your email address!';
            return $r;
        }
        if (!is_email($r->data->email)) {
            $r->message = 'Please enter a valid email address!';
            return $r;
        }
        if (empty($r->data->award_name)) {
            $r->message = 'Please enter the name to display on award!';
            return $r;
        }

        self::name($r->data->name);
        self::email($r->data->email);
        self::award_name($r->data->award_name);

        Leads::create_lead($r->data->name, $r->data->email, session_id());
        self::next_screen(EntryForm::step_industry);

        $r->success = TRUE;
        $r->message = 'User info set!';
        return $r;
    }

    static function back_to_profession() {
        $r = new ReturnObject();
        $r->data->nonce = filter_input(INPUT_GET, 'session_marker');
        $r->data->index = intval(trim(filter_input(INPUT_GET, 'baw_entry_index')));

        $a = new AddressHelper($_SERVER['REQUEST_URI']);
        if (isset($a->query['baw_action'])) {
            unset($a->query['baw_action']);
        }
        if (isset($a->query['session_marker'])) {
            unset($a->query['session_marker']);
        }
        $r->redirect = $a->url();

        if (!wp_verify_nonce($r->data->nonce, 'back_to_profession')) {
            $r->message = 'Invalid session! Please refresh this page.';
            return $r;
        }

        $r->success = TRUE;
        $r->message = 'Back!';
        return $r;
    }

    static function view_nonce() {
        wp_nonce_field('submit_user_info', 'session_marker');
    }

}
