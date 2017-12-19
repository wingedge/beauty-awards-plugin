<?php

namespace Extanet\BeautyAwards\Core;

class Contest {

    const option_key_startdate = __CLASS__ . '\startdate';
    const option_key_enddate = __CLASS__ . '\enddate';
    const option_key_status = __CLASS__ . '\status';
    const option_key_countdown_opening = __CLASS__ . '\countdown_opening';
    const option_key_countdown_closing = __CLASS__ . '\countdown_closing';
    const option_key_entry_fee = __CLASS__ . '\entry_fee';

    static function start_date($value = NULL) {
        if (!is_null($value)) {
            $value = intval(trim($value));
            update_option(self::option_key_startdate, $value);
        }
        return intval(trim(get_option(self::option_key_startdate, 0)));
    }

    static function end_date($value = NULL) {
        if (!is_null($value)) {
            $value = intval(trim($value));
            update_option(self::option_key_enddate, $value);
        }
        return intval(trim(get_option(self::option_key_enddate, 0)));
    }

    static function status($value = NULL) {
        if (!is_null($value)) {
            $value = intval(trim($value));
            update_option(self::option_key_status, $value ? 1 : 0);
        }
        return intval(trim(get_option(self::option_key_status, 0))) === 1;
    }

    static function countdown_opening($value = NULL) {
        if (!is_null($value)) {
            $value = intval(trim($value));
            update_option(self::option_key_countdown_opening, $value ? 1 : 0);
        }
        return intval(trim(get_option(self::option_key_countdown_opening, 0))) === 1;
    }

    static function countdown_closing($value = NULL) {
        if (!is_null($value)) {
            $value = intval(trim($value));
            update_option(self::option_key_countdown_closing, $value ? 1 : 0);
        }
        return intval(trim(get_option(self::option_key_countdown_closing, 0))) === 1;
    }

    static function entry_fee($value = NULL) {
        if (!is_null($value)) {
            $value = floatval(trim($value));
            update_option(self::option_key_entry_fee, $value);
        }
        return floatval(trim(get_option(self::option_key_entry_fee, 0)));
    }

    /**
     * DEPRECATED. Moved to Settings (Extanet\BeautyAwards\Core)
     * @param string $value The value to write into the settings, provide NULL to read.
     * @return string The value currently saved in the database.
     */
    static function stripe_private_key($value = NULL) {
        return Settings::stripe_private_key($value);
    }

    /**
     * DEPRECATED. Moved to Settings (Extanet\BeautyAwards\Core)
     * @param string $value The value to write into the settings, provide NULL to read.
     * @return string The value currently saved in the database.
     */
    static function stripe_public_key($value = NULL) {
        return Settings::stripe_public_key($value);
    }

}
