<?php

namespace Extanet\BeautyAwards\FrontEnd;

use Alekhin\WebsiteHelpers\ReturnObject;

class Profession {

    const session_key_professions = __CLASS__ . '\professions';

    static function session_reset() {
        $sks = [];
        $sks[] = self::session_key_professions;
        foreach ($sks as $sk) {
            if (isset($_SESSION[$sk])) {
                unset($_SESSION[$sk]);
            }
        }
    }

    static function professions($professions = NULL) {
        if (!is_null($professions) && is_array($professions)) {
            foreach ($professions as &$profession) {
                $profession = intval(trim($profession));
            }
            unset($profession);
            $_SESSION[self::session_key_professions] = $professions;
        }
        if (!isset($_SESSION[self::session_key_professions])) {
            return [];
        }
        if (!is_array($_SESSION[self::session_key_professions])) {
            return [];
        }
        return $_SESSION[self::session_key_professions];
    }

    static function choose_profession() {
        $r = new ReturnObject();
        $r->data->nonce = filter_input(INPUT_POST, 'session_marker');
        if (!is_array($r->data->professions = isset($_POST['professions']) ? $_POST['professions'] : [])) {
            $r->data->professions = [];
        }

        if (!wp_verify_nonce($r->data->nonce, 'choose_profession')) {
            $r->message = 'Invalid session! Please refresh this page.';
            return $r;
        }

        if (empty($r->data->professions)) {
            $r->message = 'Please select your professions!';
            return $r;
        }

        self::professions($r->data->professions);

        $r->success = TRUE;
        $r->message = 'Professions set!';
        return $r;
    }

    static function view_nonce() {
        wp_nonce_field('choose_profession', 'session_marker');
    }

}
