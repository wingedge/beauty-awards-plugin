<?php

// dynamic fields based from
// https://ux.stackexchange.com/questions/6556/best-pattern-for-international-address-forms

namespace Extanet\BeautyAwards\FrontEnd;

use Alekhin\WebsiteHelpers\ReturnObject;
use Alekhin\Geo\Countries;
use Alekhin\WebsiteHelpers\Address as AddressHelper;
use Alekhin\Geo\USAStates;
use Alekhin\Geo\CanadaProvinces;

class Address {

    const session_key_name = __CLASS__ . '\name';
    const session_key_country = __CLASS__ . '\country';
    const session_key_address1 = __CLASS__ . '\address1';
    const session_key_address2 = __CLASS__ . '\address2';
    const session_key_state = __CLASS__ . '\state';
    const session_key_city = __CLASS__ . '\city';
    const session_key_postal_code = __CLASS__ . '\postal_code';

    static function session_reset() {
        $sks = [];
        $sks[] = self::session_key_name;
        $sks[] = self::session_key_country;
        $sks[] = self::session_key_address1;
        $sks[] = self::session_key_address2;
        $sks[] = self::session_key_state;
        $sks[] = self::session_key_city;
        $sks[] = self::session_key_postal_code;
        foreach ($sks as $sk) {
            if (isset($_SESSION[$sk])) {
                unset($_SESSION[$sk]);
            }
        }
    }

    static function link_back_to_entries() {
        $a = new AddressHelper($_SERVER['REQUEST_URI']);
        $a->query['baw_action'] = 'back_to_entries';
        return wp_nonce_url($a->url(), 'back_to_entries', 'session_marker');
    }

    static function name($name = NULL) {
        if (!is_null($name)) {
            $_SESSION[self::session_key_name] = trim($name);
        }

        if (!isset($_SESSION[self::session_key_name])) {
            return UserInfo::name();
            //return '';
        }

        return $_SESSION[self::session_key_name];
    }

    static function country($country = NULL) {
        if (!is_null($country)) {
            $_SESSION[self::session_key_country] = trim($country);
        }

        if (!isset($_SESSION[self::session_key_country])) {
            return '';
        }

        return $_SESSION[self::session_key_country];
    }

    static function address1($address1 = NULL) {
        if (!is_null($address1)) {
            $_SESSION[self::session_key_address1] = trim($address1);
        }

        if (!isset($_SESSION[self::session_key_address1])) {
            return '';
        }

        return $_SESSION[self::session_key_address1];
    }

    static function address2($address2 = NULL) {
        if (!is_null($address2)) {
            $_SESSION[self::session_key_address2] = trim($address2);
        }

        if (!isset($_SESSION[self::session_key_address2])) {
            return '';
        }

        return $_SESSION[self::session_key_address2];
    }

    static function state($state = NULL) {
        if (!is_null($state)) {
            $_SESSION[self::session_key_state] = trim($state);
        }

        if (!isset($_SESSION[self::session_key_state])) {
            return '';
        }

        return $_SESSION[self::session_key_state];
    }

    static function city($city = NULL) {
        if (!is_null($city)) {
            $_SESSION[self::session_key_city] = trim($city);
        }

        if (!isset($_SESSION[self::session_key_city])) {
            return '';
        }

        return $_SESSION[self::session_key_city];
    }

    static function postal_code($postal_code = NULL) {
        if (!is_null($postal_code)) {
            $_SESSION[self::session_key_postal_code] = trim($postal_code);
        }

        if (!isset($_SESSION[self::session_key_postal_code])) {
            return '';
        }

        return $_SESSION[self::session_key_postal_code];
    }

    static function submit_address() {
        $r = new ReturnObject();
        $r->data->nonce = filter_input(INPUT_POST, 'session_marker');
        $r->data->name = trim(filter_input(INPUT_POST, 'name'));
        $r->data->country = trim(filter_input(INPUT_POST, 'country'));
        $r->data->address1 = trim(filter_input(INPUT_POST, 'address1'));
        $r->data->address2 = trim(filter_input(INPUT_POST, 'address2'));
        switch ($r->data->country) {
            case 'US':
                $r->data->state = trim(filter_input(INPUT_POST, 'state'));
                break;
            case 'CA':
                $r->data->state = trim(filter_input(INPUT_POST, 'province'));
                break;
            default:
                $r->data->state = trim(filter_input(INPUT_POST, 'state_province'));
        }
        $r->data->city = trim(filter_input(INPUT_POST, 'city'));
        $r->data->postal_code = trim(filter_input(INPUT_POST, 'postal_code'));

        if (!wp_verify_nonce($r->data->nonce, 'submit_address')) {
            $r->message = 'Invalid session! Please refresh this page.';
            return $r;
        }

        if (empty($r->data->name)) {
            $r->message = 'Please enter the receiver\'s name!';
            return $r;
        }

        if (empty($r->data->country)) {
            $r->message = 'Please select the receiver\'s country!';
            return $r;
        }

        if (empty($r->data->address1)) {
            $r->message = 'Please enter the receiver\'s address!';
            return $r;
        }

        switch ($r->data->country) {
            case 'US':
                if (empty($r->data->state)) {
                    $r->message = 'Please select the receiver\'s state!';
                    return $r;
                }
                break;
            case 'CA':
                if (empty($r->data->state)) {
                    $r->message = 'Please select the receiver\'s province!';
                    return $r;
                }
                break;
            default:
                if (empty($r->data->state)) {
                    $r->message = 'Please enter the receiver\'s state/province!';
                    return $r;
                }
        }

        if (empty($r->data->city)) {
            $r->message = 'Please enter the receiver\'s city' . (!in_array($r->data->country, ['US', 'CA',]) ? ' / town' : '') . '!';
            return $r;
        }

        if (empty($r->data->postal_code)) {
            switch ($r->data->country) {
                case 'US':
                    $r->message = 'Please enter the receiver\'s ZIP code!';
                    break;
                case 'CA':
                    $r->message = 'Please enter the receiver\'s postal code!';
                    break;
                default:
                    $r->message = 'Please enter the receiver\'s ZIP / postal code!';
            }
            return $r;
        }

        self::name($r->data->name);
        self::country($r->data->country);
        self::address1($r->data->address1);
        self::address2($r->data->address2);
        self::state($r->data->state);
        self::city($r->data->city);
        self::postal_code($r->data->postal_code);

        $r->success = TRUE;
        $r->message = 'Shipping address saved!';
        return $r;
    }

    static function back_to_entries() {
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

        if (!wp_verify_nonce($r->data->nonce, 'back_to_entries')) {
            $r->message = 'Invalid session! Please refresh this page.';
            return $r;
        }

        $r->success = TRUE;
        $r->message = 'Back!';
        return $r;
    }

    static function view_nonce() {
        wp_nonce_field('submit_address', 'session_marker');
    }

    static function view_countries_options($selected = NULL) {
        $countries = Countries::get_countries();
        foreach ($countries as $code => $name) {
            echo '<option value="' . $code . '"' . (!is_null($selected) && $selected == $code ? ' selected="selected"' : '') . '>' . $name . '</option>' . "\n";
        }
    }

    static function view_usa_states_options($selected = NULL) {
        $states = USAStates::get_states();
        foreach ($states as $code => $name) {
            echo '<option value="' . $code . '"' . (!is_null($selected) && $selected == $code ? ' selected="selected"' : '') . '>' . $name . '</option>' . "\n";
        }
    }

    static function view_canada_provinces_options($selected = NULL) {
        $provinces = CanadaProvinces::get_provinces();
        foreach ($provinces as $code => $name) {
            echo '<option value="' . $code . '"' . (!is_null($selected) && $selected == $code ? ' selected="selected"' : '') . '>' . $name . '</option>' . "\n";
        }
    }

}
