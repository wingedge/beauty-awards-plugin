<?php

namespace Extanet\BeautyAwards\WPAdmin\Judge;

use Alekhin\WebsiteHelpers\Address;
use Extanet\BeautyAwards\Core\BeautyAwards;
use Extanet\BeautyAwards\Core\Judges;

class Judged {

    static function link_list() {
        $a = new Address(admin_url('admin.php?page=baw_judge_judged'));
        return $a->url();
    }

    static function on_admin_menu() {
        $judged = Judges::count_judged(get_current_user_id());
        add_submenu_page('baw_judge', 'Judged (' . $judged . ')', 'Judged (' . $judged . ')', Judges::role_key_judge, 'baw_judge_judged', [__CLASS__, 'view_main',]);
    }

    static function view_main() {
        include BeautyAwards::get_dir('/views/wp-admin/judge/judged.php');
    }

    static function initialize($pf) {
        add_action('admin_menu', [__CLASS__, 'on_admin_menu',]);
    }

}
