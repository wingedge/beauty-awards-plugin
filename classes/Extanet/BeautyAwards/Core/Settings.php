<?php

namespace Extanet\BeautyAwards\Core;

class Settings {

    const option_key_stripe_private_key = __CLASS__ . '\private_key';
    const option_key_stripe_public_key = __CLASS__ . '\public_key';
    const option_key_photo_tips_page_id = __CLASS__ . '\photo_tips_page_id';

    static function stripe_private_key($value = NULL) {
        if (!is_null($value)) {
            $value = trim($value);
            update_option(self::option_key_stripe_private_key, $value);
        }
        return trim(get_option(self::option_key_stripe_private_key, ''));
    }

    static function stripe_public_key($value = NULL) {
        if (!is_null($value)) {
            $value = trim($value);
            update_option(self::option_key_stripe_public_key, $value);
        }
        return trim(get_option(self::option_key_stripe_public_key, ''));
    }

    static function photo_tips_page_id($value = NULL) {
        if (!is_null($value)) {
            $value = intval(trim($value));
            update_option(self::option_key_photo_tips_page_id, $value);
        }
        return intval(trim(get_option(self::option_key_photo_tips_page_id, 0)));
    }

}
