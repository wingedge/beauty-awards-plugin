<?php

namespace Extanet\BeautyAwards\FrontEnd;

use Alekhin\WebsiteHelpers\Address;
use Alekhin\WebsiteHelpers\ReturnObject;

class Done {

    static function link_start_over() {
        $a = new Address($_SERVER['REQUEST_URI']);
        $a->query['baw_action'] = 'start_over';
        return wp_nonce_url($a->url(), 'any_action_done', 'session_marker');
    }

    static function start_over() {
        $r = new ReturnObject();
        $r->data->nonce = filter_input(INPUT_GET, 'session_marker');

        if (!wp_verify_nonce($r->data->nonce, 'any_action_done')) {
            $r->message = 'Invalid session! Please refresh this page.';
            return $r;
        }
        
        Entries::clear_entries();

        $a = new Address($_SERVER['REQUEST_URI']);
        if (isset($a->query['baw_action'])) {
            unset($a->query['baw_action']);
        }
        if (isset($a->query['session_marker'])) {
            unset($a->query['session_marker']);
        }
        $r->redirect = $a->url();

        $r->success = TRUE;
        $r->message = 'Start Over!';
        return $r;
    }

    static function view_nonce() {
        wp_nonce_field('any_action_done', 'session_marker');
    }

}
