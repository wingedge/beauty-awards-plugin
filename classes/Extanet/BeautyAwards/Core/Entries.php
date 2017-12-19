<?php

namespace Extanet\BeautyAwards\Core;

use Alekhin\WebsiteHelpers\ReturnObject;
use Extanet\BeautyAwards\Database\Tables;
use Extanet\BeautyAwards\Database\Field;

class Entries {

    const table_name_entries = 'baw_entries';
    const table_name_entry_images = 'baw_entry_images';
    const table_name_entry_categories = 'baw_entry_categories';

    static function add_entry($name, $email, $award_name, $entry, $shipping_details, $time) {
        $r = new ReturnObject();
        $r->data->entry_id = 0;
        $r->data->entry_fee = Contest::entry_fee();
        $r->data->created = intval(trim($time));

        global $wpdb;
        $wpdb->insert($wpdb->prefix . self::table_name_entries, [
            'name' => $name,
            'email' => $email,
            'award_name' => $award_name,
            'title' => $entry->title,
            'description' => $entry->description,
            'shipping_name' => $shipping_details->name,
            'shipping_country' => $shipping_details->country,
            'shipping_address1' => $shipping_details->address1,
            'shipping_address2' => $shipping_details->address2,
            'shipping_state' => $shipping_details->state,
            'shipping_city' => $shipping_details->city,
            'shipping_postal_code' => $shipping_details->postal_code,
            'entry_total' => count($entry->categories) * $r->data->entry_fee,
            'payment_transaction_id' => '',
            'created' => $r->data->created,
            'disqualified' => 0,
        ]);
        $r->data->entry_id = $wpdb->insert_id;

        foreach ($entry->images as $category_id) {
            $wpdb->insert($wpdb->prefix . self::table_name_entry_images, [
                'image_id' => $category_id,
                'entry_id' => $r->data->entry_id,
            ]);
        }

        foreach ($entry->categories as $category_id) {
            $wpdb->insert($wpdb->prefix . self::table_name_entry_categories, [
                'category_id' => $category_id,
                'entry_fee' => $r->data->entry_fee,
                'entry_id' => $r->data->entry_id,
            ]);
        }

        $r->success = TRUE;
        $r->message = 'Entry saved!';
        return $r;
    }

    static function mark_as_paid($entry_id, $transaction_id) {
        $r = new ReturnObject();

        $entry_id = intval(trim($entry_id));
        if ($entry_id == 0) {
            $r->message = 'Invalid entry!';
            return $r;
        }

        if (empty($transaction_id)) {
            $r->message = 'Specify transaction id!';
            return $r;
        }

        global $wpdb;
        $wpdb->update($wpdb->prefix . self::table_name_entries, ['payment_transaction_id' => $transaction_id,], ['id' => $entry_id,]);

        $r->success = TRUE;
        $r->message = 'Entry marked as paid!';
        return $r;
    }

    static function update_info($entry_id, $title, $description) {
        $r = new ReturnObject();

        $entry_id = intval(trim($entry_id));
        if ($entry_id == 0) {
            $r->message = 'Invalid entry!';
            return $r;
        }

        global $wpdb;
        $wpdb->update($wpdb->prefix . self::table_name_entries, ['title' => $title, 'description' => $description,], ['id' => $entry_id,]);

        $r->success = TRUE;
        $r->message = 'Entry changes saved!';
        return $r;
    }

    static function disqualify($entry_id) {
        $r = new ReturnObject();

        $entry_id = intval(trim($entry_id));
        if ($entry_id == 0) {
            $r->message = 'Invalid entry!';
            return $r;
        }

        global $wpdb;
        $wpdb->update($wpdb->prefix . self::table_name_entries, ['disqualified' => 1,], ['id' => $entry_id,]);

        $r->success = TRUE;
        $r->message = 'Entry disqualified!';
        return $r;
    }

    static function count($filter_email = NULL, $filter_categories = NULL) {
        if (!is_null($filter_categories)) {
            if (!is_array($filter_categories)) {
                $filter_categories = [];
            }
            foreach ($filter_categories as &$cid) {
                $cid = intval(trim($cid));
            }
            unset($cid);
        }

        global $wpdb;
        if (is_null($filter_email) && is_null($filter_categories)) {
            return $wpdb->get_var("SELECT COUNT(*) FROM `" . $wpdb->prefix . self::table_name_entries . "`");
        }
        if (!is_null($filter_email) && is_null($filter_categories)) {
            return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `" . $wpdb->prefix . self::table_name_entries . "` WHERE `email` = %s", $filter_email));
        }
        if (is_null($filter_email) && !is_null($filter_categories)) {
            return $wpdb->get_var("SELECT COUNT(*) FROM `" . $wpdb->prefix . self::table_name_entries . "` AS `te`, `" . $wpdb->prefix . self::table_name_entry_categories . "` AS `tc` WHERE `tc`.`entry_id` = `te`.`id` AND `tc`.`category_id` IN (" . implode(',', $filter_categories) . ")");
        }
        return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `" . $wpdb->prefix . self::table_name_entries . "` AS `te`, `" . $wpdb->prefix . self::table_name_entry_categories . "` AS `tc` WHERE `te`.`email` = %s AND `tc`.`entry_id` = `te`.`id` AND `tc`.`category_id` IN (" . implode(',', $filter_categories) . ")", $filter_email));
    }

    static function count_by_country() {
        global $wpdb;
        return $wpdb->get_results("SELECT `shipping_country`, COUNT(*) AS `count` FROM `" . $wpdb->prefix . self::table_name_entries . "` GROUP BY `shipping_country` ORDER BY `count` DESC");
    }

    static function get_entries($count, $page = 1, $filter_email = NULL, $filter_categories = NULL) {
        if (!is_null($filter_categories)) {
            if (!is_array($filter_categories)) {
                $filter_categories = [];
            }
            foreach ($filter_categories as &$cid) {
                $cid = intval(trim($cid));
            }
            unset($cid);
        }

        global $wpdb;
        if (!is_null($filter_email) && !is_null($filter_categories) && !empty($filter_categories)) {
            return $wpdb->get_results($wpdb->prepare("SELECT `te`.* FROM `" . $wpdb->prefix . self::table_name_entries . "` AS `te`, `" . $wpdb->prefix . self::table_name_entry_categories . "` AS `tc` WHERE `te`.`email` = %s AND `tc`.`entry_id` = `te`.`id` AND `tc`.`category_id` IN (" . implode(',', $filter_categories) . ") ORDER BY `created` DESC LIMIT " . (($page - 1) * $count) . ", " . $count, $filter_email));
        }
        if (is_null($filter_email) && !is_null($filter_categories) && !empty($filter_categories)) {
            return $wpdb->get_results("SELECT `te`.* FROM `" . $wpdb->prefix . self::table_name_entries . "` AS `te`, `" . $wpdb->prefix . self::table_name_entry_categories . "` AS `tc` WHERE `tc`.`entry_id` = `te`.`id` AND `tc`.`category_id` IN (" . implode(',', $filter_categories) . ") ORDER BY `created` DESC LIMIT " . (($page - 1) * $count) . ", " . $count);
        }
        if (!is_null($filter_email) && (is_null($filter_categories) || empty($filter_categories))) {
            return $wpdb->get_results($wpdb->prepare("SELECT * FROM `" . $wpdb->prefix . self::table_name_entries . "` WHERE `email` = %s ORDER BY `created` DESC LIMIT " . (($page - 1) * $count) . ", " . $count, $filter_email));
        }
        return $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . self::table_name_entries . "` ORDER BY `created` DESC LIMIT " . (($page - 1) * $count) . ", " . $count);
    }

    static function get_winners_by_category($category_id) {
        $category_id = intval(trim($category_id));

        $w = [];

        if (!Categories::exists($category_id)) {
            return $w;
        }

        global $wpdb;
        return $wpdb->get_results($wpdb->prepare("SELECT `te`.*, AVG(`ts`.`score`) AS `score` FROM `" . $wpdb->prefix . self::table_name_entries . "` AS `te`, `" . $wpdb->prefix . self::table_name_entry_categories . "` AS `tc`, `" . $wpdb->prefix . Judges::table_name_score . "` AS `ts` WHERE `tc`.`entry_id` = `te`.`id` AND IFNULL(`te`.`disqualified`, 0) = 0 AND `ts`.`entry_category_id` = `tc`.`id` AND `tc`.`category_id` = %d GROUP BY `ts`.`score` ORDER BY `ts`.`score` DESC", $category_id));
    }

    static function exists($entry_id) {
        global $wpdb;
        return intval(trim($wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `" . $wpdb->prefix . self::table_name_entries . "` WHERE `id` = %d", $entry_id)))) > 0;
    }

    static function get_entry($entry_id) {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM `" . $wpdb->prefix . self::table_name_entries . "` WHERE `id` = %d", $entry_id));
    }

    static function get_category_entry($category_entry_id) {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM `" . $wpdb->prefix . self::table_name_entry_categories . "` WHERE `id` = %d", $category_entry_id));
    }

    static function get_images($entry_id) {
        global $wpdb;
        return $wpdb->get_col($wpdb->prepare("SELECT `image_id` FROM `" . $wpdb->prefix . self::table_name_entry_images . "` WHERE `entry_id` = %d", $entry_id));
    }

    static function get_categories($entry_id) {
        global $wpdb;
        return $wpdb->get_col($wpdb->prepare("SELECT `category_id` FROM `" . $wpdb->prefix . self::table_name_entry_categories . "` WHERE `entry_id` = %d", intval(trim($entry_id))));
    }

    static function on_activate() {
        global $wpdb;
        if (!Tables::exists($wpdb->prefix . self::table_name_entries)) {
            $wpdb->query("``  NULL, ``  NULL, PRIMARY KEY (`id`)) " . $wpdb->get_charset_collate() . ";");
            $fields = [];
            $fields[] = new Field('name', 'VARCHAR(150)');
            $fields[] = new Field('email', 'VARCHAR(254)');
            $fields[] = new Field('award_name', 'VARCHAR(150)');
            $fields[] = new Field('title', 'VARCHAR(300)');
            $fields[] = new Field('description', 'TEXT');
            $fields[] = new Field('shipping_name', 'VARCHAR(150)');
            $fields[] = new Field('shipping_country', 'VARCHAR(150)');
            $fields[] = new Field('shipping_address1', 'VARCHAR(300)');
            $fields[] = new Field('shipping_address2', 'VARCHAR(300)');
            $fields[] = new Field('shipping_state', 'VARCHAR(100)');
            $fields[] = new Field('shipping_city', 'VARCHAR(100)');
            $fields[] = new Field('shipping_postal_code', 'VARCHAR(20)');
            $fields[] = new Field('entry_total', 'DECIMAL(13,4)');
            $fields[] = new Field('payment_transaction_id', 'VARCHAR(100)');
            $fields[] = new Field('created', 'INT(11)');
            $fields[] = new Field('disqualified', 'TINYINT(1)');
            Tables::create($wpdb->prefix . self::table_name_entries, $fields);
        }
        if (!Tables::exists($wpdb->prefix . self::table_name_entry_images)) {
            $fields = [];
            $fields[] = new Field('image_id', 'INT(11) NULL');
            $fields[] = new Field('entry_id', 'INT(11)');
            Tables::create($wpdb->prefix . self::table_name_entry_images, $fields);
        }
        if (!Tables::exists($wpdb->prefix . self::table_name_entry_categories)) {
            $fields = [];
            $fields[] = new Field('category_id', 'INT(11)');
            $fields[] = new Field('entry_fee', 'DECIMAL(13,4)');
            $fields[] = new Field('entry_id', 'INT(11)');
            Tables::create($wpdb->prefix . self::table_name_entry_categories, $fields);
        }
    }

    static function initialize($pf) {
        register_activation_hook($pf, [__CLASS__, 'on_activate',]);
    }

}
