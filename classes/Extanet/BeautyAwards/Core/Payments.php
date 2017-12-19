<?php

namespace Extanet\BeautyAwards\Core;

use Alekhin\WebsiteHelpers\ReturnObject;
use Extanet\BeautyAwards\Core\Settings;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\Stripe;
use Stripe\Error\InvalidRequest;

class Payments {

    //const api_key_secret = 'sk_test_o4Zi2HJTnHzsQBNyN6rTO7cl';
    //const api_key_public = 'pk_test_gIOhZPPzMpTW3Kqcj7bXDSE3';

    static function process_payment($token, $amount, $email) {
        $r = new ReturnObject();
        $r->data->token = $token;
        $r->data->amount = $amount;
        $r->data->email = $email;
        $r->data->transaction_id = '';

        try {
            $cui = new \stdClass();
            $cui->email = $r->data->email;
            //$cui->source = $r->data->token;
            $r->data->customer = Customer::create((array) $cui);

            $chi = new \stdClass();
            $chi->amount = $r->data->amount * 100;
            $chi->currency = 'usd';
            $chi->description = 'Entry fee';
            $chi->customer = $r->data->customer;
            $chi->source = $r->data->token;
            $r->data->charge = Charge::create((array) $chi);
        } catch (InvalidRequest $ex) {
            $r->message = $ex->getMessage();
            return $r;
        } catch (Exception $ex) {
            $r->message = 'There was an error charging the card!';
            return $r;
        }

        $r->data->transaction_id = $r->data->charge->balance_transaction;
        $r->success = TRUE;
        $r->message = 'Charge for ' . $r->data->amount . ' successful!';
        return $r;
    }

    static function on_wp_enqueue_scripts() {
        wp_enqueue_script('stripe-js', 'https://js.stripe.com/v3/', [], FALSE, FALSE);
    }

    static function on_wp_head() {
        echo '<script type="text/javascript">var stripe_api_key = \'' . Settings::stripe_public_key() . '\';</script>';
    }

    static function initialize() {
        Stripe::setApiKey(Settings::stripe_private_key());
        add_action('wp_enqueue_scripts', [__CLASS__, 'on_wp_enqueue_scripts',], 9);
        add_action('wp_head', [__CLASS__, 'on_wp_head',], 100);
    }

}
