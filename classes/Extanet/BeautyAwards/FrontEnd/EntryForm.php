<?php

namespace Extanet\BeautyAwards\FrontEnd;

use Extanet\BeautyAwards\Core\BeautyAwards;
use Extanet\BeautyAwards\Core\Contest;
use Extanet\BeautyAwards\Core\Settings;

class EntryForm {

    const step_profession = 1;
    const step_user_info = 2;
    const step_industry = 3;
    const step_entry_details = 4;
    const step_categories = 5;
    const step_entries = 6;
    const step_address = 7;
    const step_payment = 8;
    const step_done = 9;
    const session_life = 10800; // seconds
    const session_key_posted = __CLASS__ . '\posted';
    const session_key_step = __CLASS__ . '\step';
    const session_key_last_update = __CLASS__ . '\last_update';

    static $p = NULL;
    static $entry_form_shown = FALSE;

    static function session_reset() {
        $sks = [];
        $sks[] = self::session_key_step;
        foreach ($sks as $sk) {
            if (isset($_SESSION[$sk])) {
                unset($_SESSION[$sk]);
            }
        }

        Profession::session_reset();
        UserInfo::session_reset();
        Industry::session_reset();
        Categories::session_reset();
        Entries::session_reset();
        Address::session_reset();
    }

    static function photo_tips_title() {
        $pid = Settings::photo_tips_page_id();
        if ($pid > 0) {
            return get_the_title($pid);
        }
        return 'Photo Tips';
    }

    static function photo_tips_content() {
        $pid = Settings::photo_tips_page_id();
        if ($pid > 0) {
            $the_page = get_post($pid);
            return str_replace(']]>', ']]&gt;', apply_filters('the_content', $the_page->post_content));
        }
        return '';
    }

    static function create_editing_entry() {
        $entries = Entries::get_entries();
        if (empty($entries)) {
            Entries::add_entry();
        } else {
            $current_entry = $entries[Entries::editing_index()];
            if (!empty($current_entry->categories)) {
                Entries::add_entry();
            }
        }
    }

    static function session_step($step = NULL) {
        $valid_views = [self::step_profession, self::step_user_info, self::step_industry, self::step_entry_details, self::step_categories, self::step_entries, self::step_address, self::step_payment, self::step_done,];

        if (!is_null($step) && in_array($step, $valid_views)) {
            $_SESSION[self::session_key_step] = $step;
        }

        if (!isset($_SESSION[self::session_key_step])) {
            return self::step_profession;
        }

        if (!in_array($sv = $_SESSION[self::session_key_step], $valid_views)) {
            return self::step_profession;
        }

        return $sv;
    }

    static function do_step($result, $success_step) {
        self::$p = $result;
        if (self::$p->success) {
            self::session_step($success_step);
        } else {
            $_SESSION[self::session_key_posted] = self::$p;
        }
        wp_redirect(self::$p->redirect);
        exit;
    }

    static function skip_step($result, $skip_to_step) {
        self::$p = $result;
        if (self::$p->success) {
            self::session_step($skip_to_step);
        }
        wp_redirect(self::$p->redirect);
        exit;
    }

    static function on_init() {
        $right_now = time();
        if (isset($_SESSION[self::session_key_last_update])) {
            if ($_SESSION[self::session_key_last_update] < $right_now - self::session_life) {
                self::session_reset();
            }
        }
        $_SESSION[self::session_key_last_update] = $right_now;

        if (isset($_SESSION[self::session_key_posted])) {
            self::$p = $_SESSION[self::session_key_posted];
            unset($_SESSION[self::session_key_posted]);
        }
    }

    static function on_wp_enqueue_scripts() {
        wp_enqueue_style('baw_main', BeautyAwards::get_url('styles/main.css'), ['dashicons'], BeautyAwards::get_ss_version());

        wp_dequeue_script('jquery');
        wp_enqueue_script('jquery', BeautyAwards::get_url('scripts/jquery-3.2.1.min.js'), [], FALSE, TRUE);
        wp_enqueue_script('baw_plugins', BeautyAwards::get_url('scripts/plugins.js'), ['jquery',], BeautyAwards::get_ss_version(), TRUE);
        wp_enqueue_script('baw_main', BeautyAwards::get_url('scripts/main.js'), ['jquery', 'baw_plugins', 'stripe-js',], BeautyAwards::get_ss_version(), TRUE);
    }

    static function on_template_redirect() {
        if (!is_null(filter_input(INPUT_POST, 'choose_profession'))) {
            self::do_step(Profession::choose_profession(), self::step_user_info);
        }
        if (!is_null(filter_input(INPUT_POST, 'submit_user_info'))) {
            $next = UserInfo::next_screen();
            self::do_step(UserInfo::submit_user_info(), $next);
        }
        if (!is_null(filter_input(INPUT_GET, 'baw_action')) && trim(filter_input(INPUT_GET, 'baw_action')) == 'back_to_profession' && !is_null(filter_input(INPUT_GET, 'session_marker'))) {
            self::skip_step(UserInfo::back_to_profession(), self::step_profession);
        }
        if (!is_null(filter_input(INPUT_POST, 'choose_industry'))) {
            self::create_editing_entry();
            self::do_step(Industry::choose_industry(), self::step_entry_details);
        }
        if (!is_null(filter_input(INPUT_GET, 'baw_action')) && trim(filter_input(INPUT_GET, 'baw_action')) == 'skip_industries' && !is_null(filter_input(INPUT_GET, 'session_marker'))) {
            self::create_editing_entry();
            self::skip_step(Industry::skip_industries(), self::step_entry_details);
        }
        if (!is_null(filter_input(INPUT_GET, 'baw_action')) && trim(filter_input(INPUT_GET, 'baw_action')) == 'back_to_user_info' && !is_null(filter_input(INPUT_GET, 'session_marker'))) {
            self::skip_step(Industry::back_to_user_info(), self::step_user_info);
        }
        if (!is_null(filter_input(INPUT_POST, 'submit_entry_details'))) {
            self::do_step(EntryDetails::submit_entry_details(), self::step_categories);
        }
        if (!is_null(filter_input(INPUT_GET, 'baw_action')) && trim(filter_input(INPUT_GET, 'baw_action')) == 'back_to_industries' && !is_null(filter_input(INPUT_GET, 'session_marker'))) {
            self::skip_step(EntryDetails::back_to_industries(), self::step_industry);
        }
        if (!is_null(filter_input(INPUT_GET, 'baw_action')) && trim(filter_input(INPUT_GET, 'baw_action')) == 'canel_new_entry' && !is_null(filter_input(INPUT_GET, 'session_marker'))) {
            self::skip_step(EntryDetails::cancel_new_entry(), self::step_entries);
        }
        if (!is_null(filter_input(INPUT_POST, 'choose_categories'))) {
            self::do_step(Categories::choose_categories(), self::step_entries);
        }
        if (!is_null(filter_input(INPUT_GET, 'baw_action')) && trim(filter_input(INPUT_GET, 'baw_action')) == 'show_all_categories' && !is_null(filter_input(INPUT_GET, 'session_marker'))) {
            self::skip_step(Categories::show_all_categories(), self::step_categories);
        }
        if (!is_null(filter_input(INPUT_GET, 'baw_action')) && trim(filter_input(INPUT_GET, 'baw_action')) == 'hide_other_categories' && !is_null(filter_input(INPUT_GET, 'session_marker'))) {
            self::skip_step(Categories::hide_other_categories(), self::step_categories);
        }
        if (!is_null(filter_input(INPUT_GET, 'baw_action')) && trim(filter_input(INPUT_GET, 'baw_action')) == 'back_to_entry_details' && !is_null(filter_input(INPUT_GET, 'session_marker'))) {
            self::skip_step(Categories::back_to_entry_details(), self::step_entry_details);
        }
        if (!is_null(filter_input(INPUT_POST, 'add_entry'))) {
            self::skip_step(Entries::add_entry(), self::step_entry_details);
        }
        if (!is_null(filter_input(INPUT_GET, 'baw_action')) && trim(filter_input(INPUT_GET, 'baw_action')) == 'edit_entry' && !is_null(filter_input(INPUT_GET, 'baw_entry_index')) && !is_null(filter_input(INPUT_GET, 'session_marker'))) {
            self::skip_step(Entries::edit_entry(), self::step_entry_details);
        }
        if (!is_null(filter_input(INPUT_GET, 'baw_action')) && trim(filter_input(INPUT_GET, 'baw_action')) == 'delete_entry' && !is_null(filter_input(INPUT_GET, 'baw_entry_index')) && !is_null(filter_input(INPUT_GET, 'session_marker'))) {
            self::skip_step(Entries::remove_entry(intval(trim(filter_input(INPUT_GET, 'baw_entry_index')))), self::step_entries);
        }
        if (!is_null(filter_input(INPUT_POST, 'confirm_entries'))) {
            $next = Entries::next_screen();
            self::do_step(Entries::confirm_entries(), $next);
        }
        if (!is_null(filter_input(INPUT_POST, 'submit_address'))) {
            self::do_step(Address::submit_address(), self::step_payment);
        }
        if (!is_null(filter_input(INPUT_GET, 'baw_action')) && trim(filter_input(INPUT_GET, 'baw_action')) == 'back_to_entries' && !is_null(filter_input(INPUT_GET, 'session_marker'))) {
            self::skip_step(Address::back_to_entries(), self::step_entries);
        }
        if (!is_null(filter_input(INPUT_POST, 'submit_payment'))) {
            self::do_step(Payment::submit_payment(), self::step_done);
        }
        if (!is_null(filter_input(INPUT_GET, 'baw_action')) && trim(filter_input(INPUT_GET, 'baw_action')) == 'back_address' && !is_null(filter_input(INPUT_GET, 'session_marker'))) {
            self::skip_step(Payment::back_to_address(), self::step_address);
        }
        if (!is_null(filter_input(INPUT_GET, 'baw_action')) && trim(filter_input(INPUT_GET, 'baw_action')) == 'change_user_info' && !is_null(filter_input(INPUT_GET, 'session_marker'))) {
            UserInfo::next_screen(self::step_payment);
            self::skip_step(Payment::change_user_info(), self::step_user_info);
        }
        if (!is_null(filter_input(INPUT_GET, 'baw_action')) && trim(filter_input(INPUT_GET, 'baw_action')) == 'change_entries' && !is_null(filter_input(INPUT_GET, 'session_marker'))) {
            Entries::next_screen(self::step_payment);
            self::skip_step(Payment::change_entries(), self::step_entries);
        }
        if (!is_null(filter_input(INPUT_GET, 'baw_action')) && trim(filter_input(INPUT_GET, 'baw_action')) == 'start_over' && !is_null(filter_input(INPUT_GET, 'session_marker'))) {
            self::do_step(Done::start_over(), self::step_profession);
        }
    }

    static function on_wp_footer() {
        if (self::$entry_form_shown && self::session_step() == self::step_entry_details) {
            self::view_photo_tips_modal();
        }
    }

    static function shortcode_entry_form() {
        ob_start();
        if (time() < Contest::start_date()) {
            self::view_soon_to_open();
        } else if (time() > Contest::end_date()) {
            self::view_submission_closed();
        } else {
            self::$entry_form_shown = TRUE;
            self::view_form_header();
            switch (self::session_step()) {
                case self::step_profession:
                    self::view_step_profession();
                    break;
                case self::step_user_info:
                    self::view_step_user_info();
                    break;
                case self::step_industry:
                    self::view_step_industry();
                    break;
                case self::step_entry_details:
                    self::view_step_entry_details();
                    break;
                case self::step_categories:
                    self::view_step_categories();
                    break;
                case self::step_entries:
                    self::view_step_entries();
                    break;
                case self::step_address:
                    self::view_step_address();
                    break;
                case self::step_payment:
                    self::view_step_payment();
                    break;
                case self::step_done:
                    self::view_step_done();
                    break;
            }
            self::view_form_footer();
        }
        return ob_get_clean();
    }

    static function view_soon_to_open() {
        include BeautyAwards::get_dir('views/entry/count-down/opening.php');
    }

    static function view_soon_to_close() {
        include BeautyAwards::get_dir('views/entry/count-down/closing.php');
    }

    static function view_submission_closed() {
        include BeautyAwards::get_dir('views/entry/count-down/closed.php');
    }

    static function view_form_header() {
        include BeautyAwards::get_dir('views/entry/form-header.php');
        if (Contest::countdown_closing()) {
            self::view_soon_to_close();
        }
    }

    static function view_form_footer() {
        include BeautyAwards::get_dir('views/entry/form-footer.php');
        //echo '<h2>Developer Info</h2>';
        //echo '<p>The data below is for development only and will not be visible in production. It\'s there so that the developer can monitor the data as they are being entered into the system.</p>';
        //echo '<pre>' . print_r($_SESSION, TRUE) . '</pre>';
    }

    static function view_post_message() {
        if (is_null(self::$p)) {
            return;
        }

        if (!isset(self::$p->success) || !isset(self::$p->message)) {
            return;
        }
        include BeautyAwards::get_dir('views/entry/post-message.php');
    }

    static function view_step_profession() {
        include BeautyAwards::get_dir('views/entry/views/profession.php');
    }

    static function view_step_user_info() {
        include BeautyAwards::get_dir('views/entry/views/user-info.php');
    }

    static function view_step_industry() {
        include BeautyAwards::get_dir('views/entry/views/industry.php');
    }

    static function view_step_entry_details() {
        include BeautyAwards::get_dir('views/entry/views/entry-details.php');
    }

    static function view_step_categories() {
        include BeautyAwards::get_dir('views/entry/views/categories.php');
    }

    static function view_step_entries() {
        include BeautyAwards::get_dir('views/entry/views/entries.php');
    }

    static function view_step_address() {
        include BeautyAwards::get_dir('views/entry/views/address.php');
    }

    static function view_step_payment() {
        include BeautyAwards::get_dir('views/entry/views/payment.php');
    }

    static function view_step_done() {
        include BeautyAwards::get_dir('views/entry/views/done.php');
    }

    static function view_photo_tips_modal() {
        include BeautyAwards::get_dir('views/entry/photo-tips-modal.php');
    }

    static function initialize($pf) {
        add_action('init', [__CLASS__, 'on_init',]);
        add_action('wp_enqueue_scripts', [__CLASS__, 'on_wp_enqueue_scripts',]);
        add_action('template_redirect', [__CLASS__, 'on_template_redirect',]);
        add_action('wp_footer', [__CLASS__, 'on_wp_footer',]);

        add_shortcode('entry-form', [__CLASS__, 'shortcode_entry_form',]);

        EntryDetails::initialize($pf);
    }

}
