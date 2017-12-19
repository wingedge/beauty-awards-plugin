<?php

namespace Extanet\BeautyAwards\Core;

class BeautyAwards {

    static $ssv = '';
    static $dir = '';
    static $url = '';

    static function get_ss_version() {
        return self::$ssv;
    }

    static function get_dir($to_what = '') {
        return self::$dir . $to_what;
    }

    static function get_url($to_what = '') {
        return self::$url . $to_what;
    }

    static function on_after_setup_theme() {
        date_default_timezone_set('America/New_York');

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        add_theme_support('title-tag');
    }

    static function initialize($pf) {
        self::$ssv = '20171102' . (WP_DEBUG ? '.' . time() : '');
        self::$dir = plugin_dir_path($pf);
        self::$url = plugin_dir_url($pf);

        add_action('after_setup_theme', [__CLASS__, 'on_after_setup_theme',]);
    }

}
