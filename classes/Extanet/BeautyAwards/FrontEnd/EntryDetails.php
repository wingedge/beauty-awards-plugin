<?phpnamespace Extanet\BeautyAwards\FrontEnd;use Alekhin\WebsiteHelpers\Address;use Alekhin\WebsiteHelpers\ReturnObject;use Extanet\BeautyAwards\Core\EntryImage;use Extanet\BeautyAwards\Core\Leads;use Extanet\BeautyAwards\FrontEnd\Entries;use Extanet\BeautyAwards\FrontEnd\UserInfo;class EntryDetails {    static function link_image_upload() {        return admin_url('admin-ajax.php?action=baw_image_upload');    }    static function link_back_to_industries() {        $a = new Address($_SERVER['REQUEST_URI']);        $a->query['baw_action'] = 'back_to_industries';        return wp_nonce_url($a->url(), 'back_to_industries', 'session_marker');    }    static function link_cancel_new_entry() {        $a = new Address($_SERVER['REQUEST_URI']);        $a->query['baw_action'] = 'canel_new_entry';        return wp_nonce_url($a->url(), 'cancel_new_entry', 'session_marker');    }    static function submit_entry_details() {        $r = new ReturnObject();        $r->data->nonce = filter_input(INPUT_POST, 'session_marker');        $r->data->title = trim(filter_input(INPUT_POST, 'title'));        $r->data->image_id = intval(trim(filter_input(INPUT_POST, 'image_id')));        $r->data->description = trim(filter_input(INPUT_POST, 'description'));        if (!wp_verify_nonce($r->data->nonce, 'submit_entry_details')) {            $r->message = 'Invalid session! Please refresh this page.';            return $r;        }        if ($r->data->image_id == 0 && isset($_FILES['image'])) {            $lead_id = Leads::get_lead_id(UserInfo::email(), session_id());            $iur = EntryImage::process_image('image', $lead_id);            if (!$iur->success) {                $r->message = $iur->message;                return $r;            }            $r->data->image_id = $iur->data->image_id;        }        if (empty($r->data->title)) {            $r->message = 'Please enter the title for this entry!';            return $r;        }        if ($r->data->image_id == 0) {            $r->message = 'Please upload an image for this entry!';            return $r;        }        /* not required          if (empty($r->data->description)) {          $r->message = 'Please enter the description for this entry!';          return $r;          } */        $entry_index = Entries::editing_index();        Entries::title($entry_index, $r->data->title);        Entries::image_id($entry_index, $r->data->image_id);        Entries::description($entry_index, $r->data->description);        $r->success = TRUE;        $r->message = 'Entry details submitted';        return $r;    }    static function back_to_industries() {        $r = new ReturnObject();        $r->data->nonce = filter_input(INPUT_GET, 'session_marker');        $r->data->index = intval(trim(filter_input(INPUT_GET, 'baw_entry_index')));        $a = new Address($_SERVER['REQUEST_URI']);        if (isset($a->query['baw_action'])) {            unset($a->query['baw_action']);        }        if (isset($a->query['session_marker'])) {            unset($a->query['session_marker']);        }        $r->redirect = $a->url();        if (!wp_verify_nonce($r->data->nonce, 'back_to_industries')) {            $r->message = 'Invalid session! Please refresh this page.';            return $r;        }        $r->success = TRUE;        $r->message = 'Back!';        return $r;    }    static function cancel_new_entry() {        $r = new ReturnObject();        $r->data->nonce = filter_input(INPUT_GET, 'session_marker');        $r->data->index = intval(trim(filter_input(INPUT_GET, 'baw_entry_index')));        if (!wp_verify_nonce($r->data->nonce, 'cancel_new_entry')) {            $r->message = 'Invalid session! Please refresh this page.';            return $r;        }        if (Entries::count_entries() < 1) {            $r->message = 'This is not a new entry!';            return $r;        }        Entries::remove_entry(Entries::editing_index(), TRUE);        Entries::editing_index(max(0, min(Entries::count_entries() - 1, Entries::editing_index() - 1)));        $a = new Address($_SERVER['REQUEST_URI']);        if (isset($a->query['baw_action'])) {            unset($a->query['baw_action']);        }        if (isset($a->query['session_marker'])) {            unset($a->query['session_marker']);        }        $r->redirect = $a->url();        $r->success = TRUE;        $r->message = 'Cancelled new entry!';        return $r;    }    static function ajax_upload_image_entry() {        header('Content-Type: application/json');        $lead_id = Leads::get_lead_id(UserInfo::email(), session_id());        echo json_encode(EntryImage::process_image('image', $lead_id));        exit;    }    static function view_nonce() {        wp_nonce_field('submit_entry_details', 'session_marker');    }    static function initialize($pf) {        add_action('wp_ajax_baw_upload_image_entry', [__CLASS__, 'ajax_upload_image_entry',]);        add_action('wp_ajax_nopriv_baw_upload_image_entry', [__CLASS__, 'ajax_upload_image_entry',]);    }}