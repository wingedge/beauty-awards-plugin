<?php

namespace Extanet\BeautyAwards\Core;

use Alekhin\WebsiteHelpers\ReturnObject;
use Extanet\BeautyAwards\Database\Tables;
use Extanet\BeautyAwards\Database\Field;

class Categories {

    const type_all = 0;
    const type_profession = 1;
    const type_industry = 2;
    const table_name = 'baw_categories';

    static function exists($id) {
        global $wpdb;
        return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `" . $wpdb->prefix . self::table_name . "` WHERE `id` = %d;", $id)) > 0;
    }

    static function add_category($name, $description, $winnings, $type, $parent_id) {
        $r = new ReturnObject();

        $name = trim($name);
        $description = trim($description);
        $winnings = floatval(trim($winnings));
        $type = intval(trim($type));
        $parent_id = intval(trim($parent_id));

        if (empty($name)) {
            $r->message = 'Please enter the name of this category!';
            return $r;
        }
        if (strlen($name) > 100) {
            $r->message = 'Category names can only have 100 characters!';
            return $r;
        }

        if (!in_array($type, [self::type_profession, self::type_industry,]) && $parent_id == 0) {
            $r->message = 'Select the type or a parent for this category!';
            return $r;
        }
        if (in_array($type, [self::type_profession, self::type_industry,])) {
            $parent_id = 0;
        }
        if ($parent_id > 0) {
            $type = self::type_all;
            if (!self::exists($parent_id)) {
                $r->message = 'The parent category you specified does not exist!';
                return $r;
            }
        }

        global $wpdb;
        $max_sort = intval(trim($wpdb->get_var($wpdb->prepare("SELECT `sort` FROM `" . $wpdb->prefix . self::table_name . "` WHERE `type` = %d AND `parent_id` = %d ORDER BY `sort` DESC LIMIT 1;", $type, $parent_id))));

        $wpdb->insert($wpdb->prefix . self::table_name, [
            'name' => $name,
            'description' => $description,
            'winnings' => $winnings,
            'type' => $type,
            'sort' => $max_sort + 1,
            'parent_id' => $parent_id,
        ]);

        $r->success = TRUE;
        $r->message = 'Category added!';
        return $r;
    }

    static function save_category($id, $name, $description, $winnings, $type, $parent_id) {
        $r = new ReturnObject();

        $id = intval(trim($id));
        $name = trim($name);
        $description = trim($description);
        $winnings = floatval(trim($winnings));
        $type = intval(trim($type));
        $parent_id = intval(trim($parent_id));

        if (empty($name)) {
            $r->message = 'Please enter the name of this category!';
            return $r;
        }
        if (strlen($name) > 100) {
            $r->message = 'Category names can only have 100 characters!';
            return $r;
        }

        if (!in_array($type, [self::type_profession, self::type_industry,]) && $parent_id == 0) {
            $r->message = 'Select the type or a parent for this category!';
            return $r;
        }
        if (in_array($type, [self::type_profession, self::type_industry,])) {
            $parent_id = 0;
        }
        if ($parent_id > 0) {
            $type = self::type_all;
            if (!self::exists($parent_id)) {
                $r->message = 'The parent category you specified does not exist!';
                return $r;
            }
        }
        if ($parent_id == $id) {
            $r->message = 'You can\'t set this category to be its own parent!';
            return $r;
        }

        global $wpdb;
        $wpdb->update($wpdb->prefix . self::table_name, [
            'name' => $name,
            'description' => $description,
            'winnings' => $winnings,
            'type' => $type,
            'parent_id' => $parent_id,
                ], ['id' => $id,]);

        $r->success = TRUE;
        $r->message = 'Category saved!';
        return $r;
    }

    static function delete_category($id) {
        $r = new ReturnObject();

        global $wpdb;
        //$wpdb->query($wpdb->prepare("DELETE FROM `entries_whatever` WHERE `category_id` = %d;", $id));
        $wpdb->query($wpdb->prepare("DELETE FROM `" . $wpdb->prefix . self::table_name . "` WHERE `parent_id` = %d;", $id));
        $wpdb->query($wpdb->prepare("DELETE FROM `" . $wpdb->prefix . self::table_name . "` WHERE `id` = %d;", $id));

        $r->success = TRUE;
        $r->message = 'Category deleted!';
        return $r;
    }

    static function get_categories_by_type($type) {
        global $wpdb;
        if (!in_array($type, [self::type_all, self::type_profession, self::type_industry,])) {
            $type = self::type_all;
        }
        return $wpdb->get_results($wpdb->prepare("SELECT `tc`.*, (SELECT COUNT(*) FROM `" . $wpdb->prefix . self::table_name . "` WHERE `parent_id` = `tc`.`id`) AS `subcategories` FROM `" . $wpdb->prefix . self::table_name . "` AS `tc` WHERE `tc`.`type` = %d ORDER BY `sort` ASC, `name` ASC", $type));
    }

    static function get_sub_categories($category_id) {
        global $wpdb;
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM `" . $wpdb->prefix . self::table_name . "` WHERE `parent_id` = %d ORDER BY `sort` ASC, `name` ASC", intval(trim($category_id))));
    }

    static function count_subcategories($category_id = 0) {
        global $wpdb;
        if ($category_id > 0) {
            return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `" . $wpdb->prefix . self::table_name . "` WHERE `parent_id` = %d", $category_id));
        }
        return $wpdb->get_var("SELECT COUNT(*) FROM `" . $wpdb->prefix . self::table_name . "` WHERE `parent_id` > 0");
    }

    static function sort_categories($ids) {
        $r = new ReturnObject();

        if (!current_user_can('manage_options')) {
            $r->message = 'You do not have permission to sort categories!';
            return $r;
        }

        global $wpdb;
        $counter = 0;
        foreach ($ids as $id) {
            $id = intval(trim($id));
            $wpdb->update($wpdb->prefix . self::table_name, ['sort' => $counter,], ['id' => $id,]);
            $counter++;
        }

        $r->success = TRUE;
        $r->message = 'Categories sorted!';
        return $r;
    }

    static function get_name($category_id) {
        global $wpdb;
        return $wpdb->get_var($wpdb->prepare("SELECT `name` FROM `" . $wpdb->prefix . self::table_name . "` WHERE `id` = %d;", $category_id));
    }

    static function get_description($category_id) {
        global $wpdb;
        return $wpdb->get_var($wpdb->prepare("SELECT `description` FROM `" . $wpdb->prefix . self::table_name . "` WHERE `id` = %d;", $category_id));
    }

    static function get_winnings($category_id) {
        global $wpdb;
        return floatval($wpdb->get_var($wpdb->prepare("SELECT `winnings` FROM `" . $wpdb->prefix . self::table_name . "` WHERE `id` = %d;", $category_id)));
    }

    static function get_type($category_id) {
        global $wpdb;
        return intval($wpdb->get_var($wpdb->prepare("SELECT `type` FROM `" . $wpdb->prefix . self::table_name . "` WHERE `id` = %d;", $category_id)));
    }

    static function get_parent_id($category_id) {
        global $wpdb;
        return intval($wpdb->get_var($wpdb->prepare("SELECT `parent_id` FROM `" . $wpdb->prefix . self::table_name . "` WHERE `id` = %d;", $category_id)));
    }

    static function on_activate() {
        global $wpdb;
        if (!Tables::exists($wpdb->prefix . self::table_name)) {
            $fields = [];
            $fields[] = new Field('name', 'VARCHAR(100)');
            $fields[] = new Field('description', 'TEXT');
            $fields[] = new Field('winnings', 'DOUBLE(6,2)');
            $fields[] = new Field('type', 'TINYINT(1)');
            $fields[] = new Field('parent_id', 'INT(11)');
            $fields[] = new Field('sort', 'INT(11)');
            Tables::create($wpdb->prefix . self::table_name, $fields);
        }
    }

    static function initialize($pf) {
        register_activation_hook($pf, [__CLASS__, 'on_activate',]);
    }

}
