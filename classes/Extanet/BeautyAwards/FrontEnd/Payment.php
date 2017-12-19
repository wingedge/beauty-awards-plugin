<?php

namespace Extanet\BeautyAwards\FrontEnd;

use Alekhin\WebsiteHelpers\Address as AddressHelper;
use Alekhin\WebsiteHelpers\ReturnObject;
use Extanet\BeautyAwards\Core\Entries as EntriesCore;
use Extanet\BeautyAwards\Core\Leads;
use Extanet\BeautyAwards\Core\Payments;
use Extanet\BeautyAwards\Core\Contest;

class Payment {

    static function link_back_to_address() {
        $a = new AddressHelper($_SERVER['REQUEST_URI']);
        $a->query['baw_action'] = 'back_address';
        return wp_nonce_url($a->url(), 'back_address', 'session_marker');
    }

    static function link_change_user_info() {
        $a = new AddressHelper($_SERVER['REQUEST_URI']);
        $a->query['baw_action'] = 'change_user_info';
        return wp_nonce_url($a->url(), 'change_user_info', 'session_marker');
    }

    static function link_change_entries() {
        $a = new AddressHelper($_SERVER['REQUEST_URI']);
        $a->query['baw_action'] = 'change_entries';
        return wp_nonce_url($a->url(), 'change_entries', 'session_marker');
    }

    static function submit_payment() {
        $r = new ReturnObject();
        $r->data->nonce = filter_input(INPUT_POST, 'session_marker');
        $r->data->token = filter_input(INPUT_POST, 'stripe_token');
        $r->data->confirm = intval(trim(filter_input(INPUT_POST, 'confirm_details')));
        $r->data->total = 0;
        $r->data->email = UserInfo::email();
        $r->data->ids = [];

        // check session
        // prepare shipping info
        $sd = new \stdClass();
        $sd->name = Address::name();
        $sd->country = Address::country();
        $sd->address1 = Address::address1();
        $sd->address2 = Address::address2();
        $sd->state = Address::state();
        $sd->city = Address::city();
        $sd->postal_code = Address::postal_code();

        if ($r->data->confirm != 1) {
            $r->message = 'Please check and confirm that the details you provided are correct!';
            return $r;
        }

        $time_created = time();

        // save all entries
        foreach (Entries::get_entries() as $entry) {
            $dr = EntriesCore::add_entry(UserInfo::name(), UserInfo::email(), UserInfo::award_name(), $entry, $sd, $time_created);
            $r->data->total += (count($entry->categories) * Contest::entry_fee());
            if ($dr->success) {
                $r->data->ids[] = $dr->data->entry_id;
            }
        }

        // apply tax if CT/US
        if ($sd->country == 'US' && $sd->state == 'CT') {
            $r->data->total += round($r->data->total * .0635, 2);
        }

        // mark lead as completed
        Leads::mark_lead_as_done(UserInfo::email(), session_id());

        // process payments
        $pr = Payments::process_payment($r->data->token, $r->data->total, $r->data->email);
        if ($pr->success) {
            // mark all ids as paid
            foreach ($r->data->ids as $entry_id) {
                EntriesCore::mark_as_paid($entry_id, $pr->data->transaction_id);
            }
        }

        $r->success = TRUE;
        $r->message = 'Entries submitted!';
        return $r;
    }

    static function change_user_info() {
        $r = new ReturnObject();
        $r->data->nonce = filter_input(INPUT_GET, 'session_marker');
        $r->data->index = intval(trim(filter_input(INPUT_GET, 'baw_entry_index')));

        if (!wp_verify_nonce($r->data->nonce, 'change_user_info')) {
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

        $r->success = TRUE;
        $r->message = 'Change!';
        return $r;
    }

    static function change_entries() {
        $r = new ReturnObject();
        $r->data->nonce = filter_input(INPUT_GET, 'session_marker');
        $r->data->index = intval(trim(filter_input(INPUT_GET, 'baw_entry_index')));

        if (!wp_verify_nonce($r->data->nonce, 'change_entries')) {
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

        $r->success = TRUE;
        $r->message = 'Change!';
        return $r;
    }

    static function back_to_address() {
        $r = new ReturnObject();
        $r->data->nonce = filter_input(INPUT_GET, 'session_marker');

        $a = new AddressHelper($_SERVER['REQUEST_URI']);
        if (isset($a->query['baw_action'])) {
            unset($a->query['baw_action']);
        }
        if (isset($a->query['session_marker'])) {
            unset($a->query['session_marker']);
        }
        $r->redirect = $a->url();

        if (!wp_verify_nonce($r->data->nonce, 'back_address')) {
            $r->message = 'Invalid session! Please refresh this page.';
            return $r;
        }

        $r->success = TRUE;
        $r->message = 'Back!';
        return $r;
    }

    static function view_nonce() {
        wp_nonce_field('submit_payment', 'session_marker');
    }

}
