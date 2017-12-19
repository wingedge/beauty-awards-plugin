<?php

namespace Extanet\BeautyAwards\WPAdmin\Administrator;

class Administrator {

    static function on_admin_menu() {
        add_menu_page('Contest Management', 'Contest', 'manage_options', 'baw_contest', [__CLASS__, 'view_main'], 'dashicons-awards', '3.999999999');
    }

    static function view_main() {
        echo ''; // yeah silence
    }

    static function initialize($pf) {
        add_action('admin_menu', [__CLASS__, 'on_admin_menu',]);
        
        Contest::initialize($pf);
        Entries::initialize($pf);
        Judges::initialize($pf);
        Categories::initialize($pf);
        Winners::initialize($pf);
        Reports::initialize($pf);
        Settings::initialize($pf);
    }

}
