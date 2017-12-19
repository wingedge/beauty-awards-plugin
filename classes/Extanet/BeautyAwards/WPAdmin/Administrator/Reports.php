<?php

namespace Extanet\BeautyAwards\WPAdmin\Administrator;

use Extanet\BeautyAwards\Core\BeautyAwards;
use Extanet\BeautyAwards\Core\Entries as EntriesCore;
use Extanet\BeautyAwards\Core\Categories as CategoriesCore;

class Reports {

    static function total_entries() {
        return EntriesCore::count();
    }

    static function entries_per_category() {
        $epc = [];

        foreach (array_merge(CategoriesCore::get_categories_by_type(CategoriesCore::type_profession), CategoriesCore::get_categories_by_type(CategoriesCore::type_industry)) as $parent) {
            $pr = new \stdClass();
            $pr->name = $parent->name;
            $pr->subcategories = [];
            foreach (CategoriesCore::get_sub_categories($parent->id) as $subcat) {
                $sc = new \stdClass();
                $sc->name = $subcat->name;
                $sc->count = EntriesCore::count(NULL, [$subcat->id,]);
                if ($sc->count > 0) {
                    $pr->subcategories[] = $sc;
                }
            }
            if (!empty($pr->subcategories)) {
                $epc[] = $pr;
            }
        }
        return $epc;
    }

    static function entries_per_country() {
        return EntriesCore::count_by_country();
    }

    static function on_admin_menu() {
        add_submenu_page('baw_contest', 'View Contest Reports', 'Reports', 'manage_options', 'baw_contest_reports', [__CLASS__, 'view_main',]);
    }

    static function view_main() {
        include BeautyAwards::get_dir('/views/wp-admin/administrator/reports.php');
    }

    static function initialize($pf = NULL) {
        add_action('admin_menu', [__CLASS__, 'on_admin_menu',]);
    }

}
