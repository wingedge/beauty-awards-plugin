<?php

namespace Extanet\BeautyAwards\WPAdmin\Administrator;

use Alekhin\Geo\Countries;
use Alekhin\WebsiteHelpers\Address;
use Alekhin\WebsiteHelpers\ReturnObject;
use Extanet\BeautyAwards\Core\BeautyAwards;
use Extanet\BeautyAwards\Core\Categories as CategoriesCore;
use Extanet\BeautyAwards\Core\Contest as ContestCore;
use Extanet\BeautyAwards\Core\Entries as EntriesCore;
use Extanet\BeautyAwards\WPAdmin\WPAdmin;
use PHPExcel;
use PHPExcel_IOFactory;

class Winners {

    const session_key_posted = __CLASS__ . '\posted';

    static $p = NULL;

    static function link_main() {
        return admin_url('admin.php?page=baw_contest_winners');
    }

    static function link_confirm_final_winners() {
        $a = new Address(self::link_main());
        $a->query['action'] = 'confirm-final';
        return $a->url();
    }

    static function link_export_to_excel() {
        return admin_url('admin-ajax.php?action=baw_export_winners_to_excel');
    }

    static function format_shipping_address($name, $address, $address2, $city, $state, $zip, $country) {
        $as = $name . ' - ';
        $ae = [];
        $ae[] = $address;
        if (!empty($address2)) {
            $ae[] = $address2;
        }
        $ae[] = $city;
        $ae[] = $state;
        $ae[] = $zip;
        $ae[] = Countries::get_countries()[$country];
        $as .= implode(',', $ae);
        return $as;
    }

    static function get_winners_by_category() {
        $r = new ReturnObject();
        $r->data->category_id = intval(trim(filter_input(INPUT_POST, 'category_id')));
        $r->data->html = '';

        if (!current_user_can('manage_options')) {
            $r->message = 'You do not have the permission to view the list of winners!';
            return $r;
        }

        ob_start();
        self::view_winners_list(EntriesCore::get_winners_by_category($r->data->category_id));
        $r->data->html = ob_get_clean();

        $r->success = TRUE;
        $r->message = 'List of winners loaded!';
        return $r;
    }

    static function confirm_final_winners() {
        $r = new ReturnObject();
        $r->data->nonce = filter_input(INPUT_POST, 'session_marker');

        if (!wp_verify_nonce($r->data->nonce, 'confirm_final_winners')) {
            $r->message = 'Invalid session! Please refresh the page!';
            return $r;
        }

        if (!current_user_can('manage_options')) {
            $r->message = 'You do not have the permission to confirm final winners!';
            return $r;
        }

        ContestCore::status(FALSE);

        // TODO: Send email to everyone

        $r->redirect = self::link_main();
        $r->success = TRUE;
        $r->message = 'Final list of winners confirmed!';
        return $r;
    }

    static function on_init() {
        if (isset($_SESSION[self::session_key_posted])) {
            self::$p = $_SESSION[self::session_key_posted];
            unset($_SESSION[self::session_key_posted]);
        }
    }

    static function on_admin_menu() {
        add_submenu_page('baw_contest', 'Manage Winners', 'Winners', 'manage_options', 'baw_contest_winners', [__CLASS__, 'view_main',]);
    }

    static function on_current_screen() {
        if (get_current_screen()->id != 'contest_page_baw_contest_winners') {
            return;
        }

        if (empty($action = trim(filter_input(INPUT_GET, 'action')))) {
            $action = 'list';
        }
        if (!in_array($action, ['confirm-final', 'list',])) {
            wp_redirect(self::link_list());
            exit;
        }

        if ($action == 'confirm-final') {
            if (!is_null(filter_input(INPUT_POST, 'confirm_final_winners'))) {
                self::$p = $_SESSION[self::session_key_posted] = self::confirm_final_winners();
                wp_redirect(self::$p->redirect);
                exit;
            }
        }
    }

    static function on_admin_notices() {
        if (is_null(self::$p)) {
            return;
        }

        WPAdmin::view_notice(self::$p->message, self::$p->success);
    }

    static function ajax_get_winners_by_category() {
        header('Content-Type: application/json');

        echo json_encode(self::get_winners_by_category());

        exit;
    }

    static function ajax_export_winners_to_excel() {
        require_once BeautyAwards::get_dir('/classes/OldIncludes/PHPExcel.php');
        require_once BeautyAwards::get_dir('/classes/OldIncludes/PHPExcel/IOFactory.php');

        $current_sheet_id = 0;
        $ex = new PHPExcel();

        foreach (array_merge(CategoriesCore::get_categories_by_type(CategoriesCore::type_profession), CategoriesCore::get_categories_by_type(CategoriesCore::type_industry)) as $parent_category) {
            $ex->setActiveSheetIndex($current_sheet_id);
            $write_row = 1;

            foreach (CategoriesCore::get_sub_categories($parent_category->id) as $sub_category) {
                $winners = EntriesCore::get_winners_by_category($sub_category->id);
                if (!empty($winners)) {
                    $ex->getActiveSheet()->setCellValue("A{$write_row}", $sub_category->name);
                    $write_row++;
                    $ex->getActiveSheet()->setCellValue("A{$write_row}", 'Name');
                    $ex->getActiveSheet()->setCellValue("B{$write_row}", 'Entry Title');
                    $ex->getActiveSheet()->setCellValue("C{$write_row}", 'Description');
                    $ex->getActiveSheet()->setCellValue("D{$write_row}", 'Score');
                    $ex->getActiveSheet()->setCellValue("E{$write_row}", 'Category');
                    $ex->getActiveSheet()->setCellValue("F{$write_row}", 'Sub-Category');
                    $ex->getActiveSheet()->setCellValue("G{$write_row}", 'Shipping Address');

                    foreach ($winners as $winner) {
                        $write_row++;
                        $ex->getActiveSheet()->setCellValue("A{$write_row}", $winner->name);
                        $ex->getActiveSheet()->setCellValue("B{$write_row}", $winner->title);
                        $ex->getActiveSheet()->setCellValue("C{$write_row}", $winner->description);
                        $ex->getActiveSheet()->setCellValue("D{$write_row}", $winner->score);
                        $ex->getActiveSheet()->setCellValue("E{$write_row}", $parent_category->name);
                        $ex->getActiveSheet()->setCellValue("F{$write_row}", $sub_category->name);
                        $ex->getActiveSheet()->setCellValue("G{$write_row}", self::format_shipping_address($winner->shipping_name, $winner->shipping_address1, $winner->shipping_address2, $winner->shipping_city, $winner->shipping_state, $winner->shipping_postal_code, $winner->shipping_country));
                    }

                    $write_row += 2;
                }
            }

            $clean_title = $parent_category->name;
            $clean_title = str_replace('*', '', $clean_title);
            $clean_title = str_replace(':', '', $clean_title);
            $clean_title = str_replace('/', '', $clean_title);
            $clean_title = str_replace('\\', '', $clean_title);
            $clean_title = str_replace('?', '', $clean_title);
            $clean_title = str_replace('[', '', $clean_title);
            $clean_title = str_replace(']', '', $clean_title);
            $clean_title = substr($clean_title, 0, 31);
            $ex->getActiveSheet()->setTitle($clean_title);

            $ex->createSheet();
            $current_sheet_id++;
        }

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="winners.xls"');
        header('Cache-Control: max-age=0');
        $ow = PHPExcel_IOFactory::createWriter($ex, 'Excel5');
        $ow->save('php://output');
        exit;
    }

    static function view_main() {
        if (!in_array($action = trim(filter_input(INPUT_GET, 'action')), ['confirm-final',])) {
            $action = 'list';
        }
        if ($action == 'confirm-final') {
            include BeautyAwards::get_dir('/views/wp-admin/administrator/winners/confirm.php');
        } else {
            include BeautyAwards::get_dir('/views/wp-admin/administrator/winners/main.php');
        }
    }

    static function view_winners_list($winners) {
        include BeautyAwards::get_dir('/views/wp-admin/administrator/winners/list.php');
    }

    static function initialize($pf) {
        add_action('init', [__CLASS__, 'on_init',]);
        add_action('admin_menu', [__CLASS__, 'on_admin_menu',]);
        add_action('current_screen', [__CLASS__, 'on_current_screen',]);
        add_action('admin_notices', [__CLASS__, 'on_admin_notices',]);

        add_action('wp_ajax_baw_get_winners_by_category', [__CLASS__, 'ajax_get_winners_by_category',]);
        add_action('wp_ajax_baw_export_winners_to_excel', [__CLASS__, 'ajax_export_winners_to_excel',]);
    }

}
