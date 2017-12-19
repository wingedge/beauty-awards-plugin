<?php

namespace Extanet\BeautyAwards\WPAdmin\Judge;

use Extanet\BeautyAwards\Core\Judges;

class Judge {

    static function on_admin_menu() {
        add_menu_page('Contest Judging', 'Contest Judging', Judges::role_key_judge, 'baw_judge', [__CLASS__, 'view_main'], 'dashicons-awards', '3.999999999');
    }

    static function view_main() {
        echo ''; // silence yeah
    }

    static function initialize($pf) {
        add_action('admin_menu', [__CLASS__, 'on_admin_menu',]);

        ToJudge::initialize($pf);
        Judged::initialize($pf);
    }

}
