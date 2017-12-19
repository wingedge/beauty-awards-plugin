<?php

namespace Extanet\BeautyAwards\WPAdmin;

use Extanet\BeautyAwards\Core\BeautyAwards;

class WPAdmin {

    static function on_admin_print_scripts() {
        wp_enqueue_style('baw-admin', BeautyAwards::get_url('styles/admin.css'), [], BeautyAwards::get_ss_version());

        wp_enqueue_script('baw-admin', BeautyAwards::get_url('scripts/admin.js'), ['jquery', 'jquery-ui-sortable',], BeautyAwards::get_ss_version(), TRUE);
    }

    static function view_notice($message, $success, $dismissable = TRUE) {
        $classes = [];
        $classes[] = 'notice';
        if ($success) {
            $classes[] = 'notice-success';
        } else {
            $classes[] = 'notice-error';
        }
        if ($dismissable) {
            $classes[] = 'is-dismissible';
        }
        $classes = implode(' ', $classes);
        include BeautyAwards::get_dir('views/wp-admin/notice.php');
    }

    static function initialize($pf) {
        add_action('admin_print_scripts', [__CLASS__, 'on_admin_print_scripts',]);

        Administrator\Administrator::initialize($pf);
        Judge\Judge::initialize($pf);
    }

}
