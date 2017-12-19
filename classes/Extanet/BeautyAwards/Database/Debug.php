<?php

namespace Extanet\BeautyAwards\Database;

use Extanet\BeautyAwards\Core\Entries;
use Extanet\BeautyAwards\Core\Judges;

class Debug {

    static function execute() {
        global $wpdb;
        foreach (['baw_categories', 'baw_entries', 'baw_entry_categories', 'baw_entry_images', 'baw_judge_categories', 'baw_judge_scores', 'baw_leads',] as $table_name) {
            $dbr = $wpdb->get_results("DESC `" . $wpdb->prefix . $table_name . "`;");
            $r = [];
            foreach ($dbr as $dbri) {
                $r[] = $dbri->Field;
            }
            echo '<pre>' . print_r($r, TRUE) . '</pre>';
        }

        exit;
    }

    static function count_to_judge() {
        $judge_id = 4;
        global $wpdb;
        //echo '<pre>' . print_r($wpdb->get_results("SELECT `category_id` FROM `wp_baw_judge_categories` WHERE `judge_id` = 4"), TRUE) . '</pre>';
        //echo '<pre>' . print_r($wpdb->get_results("SELECT `entry_category_id` FROM `wp_baw_judge_scores` WHERE `judge_id` = 4"), TRUE) . '</pre>';
        echo '<pre>' . print_r($wpdb->prepare("SELECT * FROM `" . $wpdb->prefix . Entries::table_name_entries . "` AS `te`, `" . $wpdb->prefix . Entries::table_name_entry_categories . "` AS `tce` WHERE IFNULL(`te`.`disqualified`, 0) = 0 AND `tce`.`entry_id` = `te`.`id` AND `tce`.`category_id` IN (SELECT `category_id` FROM `" . $wpdb->prefix . Judges::table_name_categories . "` WHERE `judge_id` = %d) AND `tce`.`id` NOT IN (SELECT `entry_category_id` FROM `" . $wpdb->prefix . Judges::table_name_score . "` WHERE `judge_id` = %d);", $judge_id, $judge_id), TRUE) . '</pre>';
        echo '<pre>' . print_r($wpdb->get_results($wpdb->prepare("SELECT * FROM `" . $wpdb->prefix . Entries::table_name_entries . "` AS `te`, `" . $wpdb->prefix . Entries::table_name_entry_categories . "` AS `tce` WHERE IFNULL(`te`.`disqualified`, 0) = 0  AND `tce`.`entry_id` = `te`.`id` AND `tce`.`category_id` IN (SELECT `category_id` FROM `" . $wpdb->prefix . Judges::table_name_categories . "` WHERE `judge_id` = %d) AND `tce`.`id` NOT IN (SELECT `entry_category_id` FROM `" . $wpdb->prefix . Judges::table_name_score . "` WHERE `judge_id` = %d);", $judge_id, $judge_id)), TRUE) . '</pre>';
    }

}
