<?php

namespace Extanet\BeautyAwards\Core;

use Extanet\BeautyAwards\Database\Tables;
use Extanet\BeautyAwards\Database\Field;

class Leads {

    const table_name = 'baw_leads';

    static function create_lead($name, $email, $session_id) {
        global $wpdb;
        $wpdb->insert($wpdb->prefix . self::table_name, [
            'name' => $name,
            'email' => $email,
            'session_id' => $session_id,
            'created' => time(),
            'finished' => 0,
        ]);
    }

    static function mark_lead_as_done($email, $session_id) {
        global $wpdb;
        $wpdb->update($wpdb->prefix . self::table_name, ['finished' => time(),], ['email' => $email, 'session_id' => $session_id,]);
    }

    static function get_lead_id($email, $session_id) {
        global $wpdb;
        return intval(trim($wpdb->get_var($wpdb->prepare("SELECT `id` FROM `" . $wpdb->prefix . self::table_name . "` WHERE `email` = %s AND `session_id` = %s", $email, $session_id))));
    }

    static function on_activate() {
        global $wpdb;
        if (!Tables::exists($wpdb->prefix . self::table_name)) {
            $fields = [];
            $fields[] = new Field('name', 'VARCHAR(100)');
            $fields[] = new Field('email', 'VARCHAR(320)');
            $fields[] = new Field('session_id', 'VARCHAR(128)');
            $fields[] = new Field('created', 'INT(11)');
            $fields[] = new Field('finished', 'INT(11)');
            Tables::create($wpdb->prefix . self::table_name, $fields);
        }
    }

    static function initialize($pf) {
        register_activation_hook($pf, [__CLASS__, 'on_activate',]);
    }

}
