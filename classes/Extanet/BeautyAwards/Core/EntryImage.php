<?php

namespace Extanet\BeautyAwards\Core;

use Alekhin\WebsiteHelpers\ReturnObject;

class EntryImage {

    const post_meta_key_lead_id = __CLASS__ . '\lead_id';
    const allowed_mime_types = ['image/jpeg', 'image/png', 'image/gif', 'image/bmp'];
    const max_file_size = 12582912; // 12Mib
    const min_image_width = '800';
    const min_image_height = '600';

    static function max_file_size_string() {
        $mb_size = self::max_file_size / 1048576;
        return number_format($mb_size, intval($mb_size * 10) == $mb_size * 10 ? 1 : 2);
    }

    static function clear_lead_image($lead_id) {
        $image_id = 0;
        $posts = get_posts([
            'post_type' => 'attachment',
            'meta_key' => self::post_meta_key_lead_id,
            'meta_value' => $lead_id,
        ]);
        if (!empty($posts) && is_array($posts)) {
            $image_id = $posts[0]->ID;
        }

        if ($image_id > 0) {
            self::delete_image($image_id);
        }
    }

    static function delete_image($image_id) {
        if ($image_id > 0) {
            wp_delete_post($image_id, TRUE);
        }
    }

    static function process_image($field_name, $lead_id = 0) {
        if ($lead_id > 0) {
            self::clear_lead_image($lead_id);
        }

        $r = new ReturnObject();
        $r->data->image_id = 0;

        $uf = $_FILES[$field_name];

        if ($uf['size'] > self::max_file_size) {
            $r->message = 'Max image file size is ' . self::max_file_size_string() . ' MB!';
            return $r;
        }

        if (!in_array($uf['type'], self::allowed_mime_types)) {
            $r->message = 'The file you uploaded does not seem to be an image!';
            return $r;
        }
        if (getimagesize($uf['tmp_name']) === FALSE) {
            $r->message = 'The file you uploaded does not seem to be an image!';
            return $r;
        }

        if (!is_admin()) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
        }

        if (is_wp_error($result = media_handle_upload($field_name, 0))) {
            $r->message = 'There was an error saving the file you uploaded!';
            return $r;
        }

        $r->data->image_id = $result;
        update_post_meta($r->data->image_id, self::post_meta_key_lead_id, $lead_id);

        $r->success = TRUE;
        $r->message = 'Image uploaded successfully!';
        return $r;
    }

    static function get_source($image_id) {
        if (FALSE === ($src = wp_get_attachment_image_src($image_id, 'large'))) {
            return '';
        }

        return $src[0];
    }

}
