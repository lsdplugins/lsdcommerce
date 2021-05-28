<?php
namespace LSDCommerce\Admin;

use LSDCommerce\DB\Reports_Repository;
use LSDCommerce\Utils\Log;
use LSDCommerce\Payments;

if (!defined('ABSPATH')) {
    exit;
}

class AJAX
{
    public function __construct()
    {
        add_action('wp_ajax_lsdcommerce_store_save', [$this, 'store_settings']);

        add_action('wp_ajax_lsdc_admin_appearance_save', [$this, 'admin_appearance_save']);

        // add_action('wp_ajax_lsdc_admin_notification_status', [$this, 'admin_notification_status']);

        // add_action('wp_ajax_lsdc_admin_payment_sorting', [$this, 'admin_payment_sorting']);
        // add_action('wp_ajax_lsdc_admin_payment_status', [$this, 'admin_payment_status']);
        // add_action('wp_ajax_lsdc_admin_payment_manage', [$this, 'admin_payment_manage']);
        // add_action('wp_ajax_lsdc_payment_delete', [$this, 'admin_payment_delete']);

        // add_action('wp_ajax_lsdc_payment_settings', [$this, 'admin_payment_settings']);

        // add_action('wp_ajax_lsdc_admin_settings_save', [$this, 'admin_settings_save']);

        // add_action('wp_ajax_lsdc_admin_option_save', [$this, 'admin_option_save']);

        // add_action('wp_ajax_lsdc_get_settings_invoice', [$this, 'admin_get_invoice']);
        // add_action('wp_ajax_lsdc_report_export_action', [$this, 'admin_report_export_action']);
        // add_action('wp_ajax_lsdc_report_import_action', [$this, 'admin_report_import_action']);
        // add_action('wp_ajax_lsdc_report_action', [$this, 'admin_report_action']);
        // add_action('wp_ajax_lsdc_report_bulk_action', [$this, 'admin_report_bulk_action']);
        // add_action('wp_ajax_lsdc_report_chart', [$this, 'statistics_chart_data']);

        // add_action('wp_ajax_lsdc_notification_email_template', [$this, 'notification_email_template']);
        // add_action('wp_ajax_lsdc_notification_email_reset', [$this, 'notification_email_reset']);
    }
    
    /**
     * Saving payment list based on user sort
     * save into option 'lsdc_payment_sorted'
     *
     * @return void
     */
    public function admin_payment_sorting()
    {
        if (!check_ajax_referer('lsdc_admin_nonce', 'security')) {
            wp_send_json_error('Invalid security token sent.');
        }

        $payments = isset($_POST['payments']) ? (array) $_POST['payments'] : array();

        // sanitize array: Any of the WordPress data sanitization functions can be used here
        $payments = array_map('esc_attr', $payments);
        $success = update_option('lsdc_payment_sorted', $payments);
        wp_send_json_success($payments);

        wp_die();
    }

    /**
     * Saving Appearance Options in Admin
     * save into option "lsdc_appearance_settings'
     *
     * @return void
     */
    public function admin_appearance_save()
    {
        if (!check_ajax_referer('lsdc_admin_nonce', 'security')) {
            wp_send_json_error('Invalid security token sent.');
        }

        $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
        $data = $_REQUEST['appearance'];
        $out = array();
        parse_str(html_entity_decode($data), $out);

        update_option('lsdc_appearance_settings', $out);
        echo 'action_success';

        wp_die();
    }

    public function admin_notification_status()
    {
        if (!check_ajax_referer('lsdc_admin_nonce', 'security')) {
            wp_send_json_error('Invalid security token sent.');
        }

        // Toggle Notification Method
        $id = str_replace("_status", "", esc_attr($_REQUEST['id']));
        $state = esc_attr($_REQUEST['state']);
        $lsdc_payment_status = get_option('lsdc_notification_status');

        if ($lsdc_payment_status == '') {
            $lsdc_payment_status = array();
        }

        $lsdc_payment_status[$id] = $state;

        update_option('lsdc_notification_status', $lsdc_payment_status);
        echo 'action_success';

        wp_die();
    }

    public function admin_option_save()
    {
        if (!check_ajax_referer('lsdc_admin_nonce', 'security')) {
            wp_send_json_error('Invalid security token sent.');
        }

        $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
        $data = esc_attr($_REQUEST['settings']);
        $option = esc_attr($_REQUEST['option']);
        $block = sanitize_text_field($_REQUEST['block']);

        $out = array();
        parse_str(html_entity_decode($data), $out);

        // Sanitizing
        $sanitize = array();
        foreach ($out as $key => $item) {
            if ($key == 'sender_email') {
                $item = sanitize_email($item);
                // TODO :: make it extendable
            } elseif ($key == 'confirmations') {
                if (!is_array($item)) {
                    $item = array();
                }

                $item = array_map('esc_attr', $item);
            } else {
                $item = sanitize_text_field($item);
            }
            $sanitize[$key] = $item; //restructure
        }

        // Saving
        $saved = array();
        $exist = get_option($option);
        if ($exist) { // update
            if ($block) {
                $exist[$block] = $sanitize;
            } else {
                $exist = $sanitize;
            }
            update_option($option, $exist);
        } else { // saved
            if ($block) {
                $saved[$block] = $sanitize;
            } else {
                $saved = $sanitize;
            }
            update_option($option, $saved);
        }

        echo 'action_success';

        wp_die();
    }

    public function notification_email_template()
    {
        if (!check_ajax_referer('lsdc_admin_nonce', 'security')) {
            wp_send_json_error('Invalid security token sent.');
        }

        $country_selected = lsdc_get_country();

        $email_type = esc_attr($_POST['email_type']);
        $template_html = $_POST['data'];

        $settings = get_option('lsdc_notification_email'); // lsdc_notification_email
        $settings[$email_type]['saved'] = 'email-' . $email_type . '-' . $country_selected . '.html'; //Saving
        $settings[$email_type]['header_bg'] = sanitize_hex_color($_POST['header_bg']); // header background
        $settings[$email_type]['subject'] = esc_attr($_POST['subject']); // header background

        update_option('lsdc_notification_email', $settings);

        $clean_template_html = stripslashes($template_html);
        file_put_contents(LSDD_STORAGE . '/email-' . $email_type . '-' . $country_selected . '.html', $clean_template_html); //save to file

        echo 'action_success';
        wp_die();
    }

    public function admin_payment_status()
    {
        if (!check_ajax_referer('lsdc_admin_nonce', 'security')) {
            wp_send_json_error('Invalid security token sent.');
        }

        // Toggle Payment Method
        $id = str_replace("_status", "", esc_attr($_REQUEST['id']));

        $state = esc_attr($_REQUEST['state']);
        $lsdc_payment_status = get_option('lsdc_payment_status') != null ? get_option('lsdc_payment_status') : array();
        if( !is_array($lsdc_payment_status) ){
            $lsdc_payment_status = array();
        }
        $lsdc_payment_status[$id] = $state;

        update_option('lsdc_payment_status', $lsdc_payment_status);
        echo 'action_success';

        wp_die();
    }

    public function admin_payment_manage()
    {
        if (!check_ajax_referer('lsdc_admin_nonce', 'security')) {
            wp_send_json_error('Invalid security token sent.');
        }

        $payment_id = esc_attr($_REQUEST['id']);
        $payment_settings = get_option('lsdc_payment_settings');
        $obj = $payment_settings[$payment_id];

        if( isset($obj['template_class'])) {
            $payment = new $obj['template_class'];
            echo $payment->manage( $payment_id );
        }else{
            echo 'error';
        }

        wp_die();
    }

    public function admin_payment_delete()
    {
        if (!check_ajax_referer('lsdc_admin_nonce', 'security')) {
            wp_send_json_error('Invalid security token sent.');
        }

        // Toggle Payment Method
        $id = lsdc_sanitize_id($_REQUEST['id']);

        $payment_data = get_option('lsdc_payment_settings'); 
        $payment_sorted = get_option('lsdc_payment_sorted'); 

        if( isset($payment_data[$id]) || isset($payment_sorted[$id]) ){

            unset($payment_data[$id]);
            unset($payment_sorted[$id]);

            update_option('lsdc_payment_sorted', $payment_sorted);
            update_option('lsdc_payment_settings', $payment_data);
            echo 'action_success';
        }else{
            echo 'error';
        }

        wp_die();
    }


    public function admin_payment_settings()
    {
        if (!check_ajax_referer('lsdc_admin_nonce', 'security')) {
            wp_send_json_error('Invalid security token sent.');
        }

        // Toggle Payment Method
        $origin_id = lsdc_sanitize_id($_REQUEST['id']);
        $method = 'lsdc_payment_settings';

        $data = $_REQUEST['serialize'];
        $out = array();
        parse_str($data, $out);

        $clean = array();
        foreach ($out as $key => $item) {
            if ($key == 'logo') {
                $clean[$key] = esc_url($item);
            } elseif ($key == 'excluded_fields') {
                if (!is_array($item)) {
                    $item = array();
                }
                $clean[$key] = array_map('esc_attr', $item);
            } elseif ($key == 'required_fields') {
                if (!is_array($item)) {
                    $item = array();
                }
                $clean[$key] = array_map('esc_attr', $item);
            } else {
                $clean[$key] = sanitize_text_field(stripslashes_deep($item));
            }

            if ($key == 'pointer') {
                $pointer = lsdc_sanitize_id($item);
            }
        }

        // Check Key Not Existed :: Fix Empty Fields not empty
        if( !isset($clean['excluded_fields']) ){
            $clean['excluded_fields'] = [];
        }

        // Check Key Not Existed :: Fix Empty Fields not empty
        if( !isset($clean['required_fields']) ){
            $clean['required_fields'] = [];
        }

        // Storing to Settings
        $payment_data = get_option($method); // get method

        if (isset($payment_data[$origin_id])) {
            $merge = array_merge($payment_data[$origin_id], $clean); // merge array
        } else if (isset($payment_data[$pointer])) {
            $merge = array_merge($payment_data[$pointer], $clean); // merge array @override
        } else {
            $merge = $clean; // create new array
        }

        if (!empty($pointer)) { // generate new payment method
            $merge['pointer'] = $pointer;
            $payment_data[$origin_id]['pointer'] = $origin_id; // set custom by pointer || pointing to pointer data
            $payment_data[$pointer] = $merge; // set alias data independent
        } else {
            $payment_data[$origin_id] = $merge;
        }


        // CleanUp Data
        unset($payment_data[""]);

        update_option($method, $payment_data);
        echo 'action_success';

        wp_die();
    }

    public function admin_settings_save()
    {
        if (!check_ajax_referer('lsdc_admin_nonce', 'security')) {
            wp_send_json_error('Invalid security token sent.');
        }

        $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
        $data = $_REQUEST['settings'];
        $out = array();
        parse_str(html_entity_decode($data), $out);

        $allowed_html = wp_kses_allowed_html('post');
        $sanitize = array();
        foreach ($out as $key => $item) {
            if ($key == 'lsdc_tac') {
                $item = wp_kses($item, $allowed_html);
            } elseif ($key == 'report_permission') {
                if (!is_array($item)) {
                    $item = array();
                }

                $item = array_map('esc_attr', $item);
            } else {
                $item = sanitize_text_field($item);
            }
            $sanitize[$key] = $item; //restructure
        }

        $settings = get_option('lsdc_general_settings');
        if (empty($settings)) {
            $merge = $sanitize;
        } else {
            $merge = array_merge($settings, $sanitize);
        }

        update_option('lsdc_general_settings', $merge);

        if ($settings['lsdc_currency'] != $out['lsdc_currency']) {
            echo 'action_reload';
        } else {
            echo 'action_success';
        }

        wp_die();
    }

    public function store_settings()
    {
        if (!check_ajax_referer('lsdc_admin_nonce', 'security')) {
            wp_send_json_error('Invalid security token sent.');
        }

        // Stripslash Data
        $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
        $data = $_REQUEST['settings'];

        // Parset Data to Array
        $out = array();
        parse_str(html_entity_decode($data), $out);

        // Loop Sanitize
        $allowed_html = wp_kses_allowed_html('post');
        $sanitize = array();
        foreach ($out as $key => $item) {
            if ($key == 'address') {
                // Sanitize Textarea
                $item = wp_kses($item, $allowed_html);
            } else {
                // Sanitize Textfield
                $item = sanitize_text_field($item);
            }
            $sanitize[$key] = $item; //restructure
        }

        // Merge Exist Settings
        $settings = get_option('lsdcommerce_store');
        if (empty($settings)) {
            $merge = $sanitize;
        } else {
            $merge = array_merge($settings, $sanitize);
        }

        // Update New Settings
        update_option('lsdcommerce_store', $merge);
        echo 'action_success';

        wp_die();
    }

    public function admin_get_invoice()
    {
        if (!check_ajax_referer('lsdc_admin_nonce', 'security')) {
            wp_send_json_error('Invalid security token sent.');
        }

        $id = $_REQUEST['id'];

        global $wpdb;
        $results = $wpdb->get_row("SELECT * FROM wp_lsdcommerce_reports WHERE report_id = $id");

        $result = get_the_title($results->program_id);

        echo wp_send_json_success([$results, $result]);
        wp_die();
    }

    public function admin_report_export_action()
    {
        if (!check_ajax_referer('lsdc_admin_nonce', 'security')) {
            wp_send_json_error('Invalid security token sent.');
        }

        $filter = sanitize_text_field($_REQUEST['filter']);
        $exportby = null;

        if ($filter === lsdc_current_date('d M Y') || $filter === lsdc_date_format('-1 day', 'd M Y')) {
            $exportby[] = date('Y', strtotime($filter));
            $exportby[] = date('m', strtotime($filter));
            $exportby[] = date('d', strtotime($filter));
        } else if (strtolower($filter) === 'last 7 day') {   
            $exportby[] = date('Y-m-d');
            $exportby[] = date('Y-m-d', strtotime('-7 day'));
        } else if ($filter === lsdc_current_date('M')) {
            $exportby[] = date('m');
        }

        if ($exportby && $filter === lsdc_current_date('d M Y')) {
            $basename = 'lsdcommerce_today_' . strtolower(date('d', strtotime($filter))) . '_' . strtolower(date('M', strtotime($filter))) . '_' . date('Y', strtotime($filter)); // Set Name
        } else if ($exportby && $filter === lsdc_date_format('-1 day', 'd M Y')) {
            $basename = 'lsdcommerce_yseterday_' . strtolower(date('d', strtotime($filter))) . '_' . strtolower(date('M', strtotime($filter))) . '_' . date('Y', strtotime($filter)); // Set Name
        } else if ($exportby && $filter === 'last 7 day') {
            $basename = 'lsdcommerce_last_7_day'; // Set Name
        } else if ($exportby && $filter === lsdc_current_date('M')) {
            $basename = 'lsdcommerce_this_month';
        } else {
            $basename = 'lsdcommerce_all';
        }

        $reports = new Reports_Repository;

        $source = $reports->export($exportby);
        $source = json_decode($source);

        echo json_encode(array(
            'name' => $basename,
            'header' => $source->header,
            'content' => $source->body,
        ));
        // echo json_encode($exportby);
        wp_die();
    }

    public function admin_report_import_action()
    {
        if (!check_ajax_referer('lsdc_admin_nonce', 'security')) {
            wp_send_json_error('Invalid security token sent.');
        }

        $import = $_REQUEST['data']; // Gathering
        $import = str_replace("\\", "", $import); // Parsing
        $import = json_decode($import); // Decoding

        $reports = new Reports_Repository;

        $report_ids = $reports->ids(); // Getting IDs Available
        $counter = 0;

        if (!array_filter($import) == []) { // check array empty
            foreach ($import as $item) {
                if (count($item) != 0) { // If Valid
                    $id = $item[0];

                    $args = array(
                        'user_id' => absint($item[1]),
                        'program_id' => absint($item[2]),
                        'name' => sanitize_text_field($item[3]),
                        'phone' => sanitize_text_field($item[4]),
                        'email' => sanitize_email($item[5]),
                        'anonim' => sanitize_text_field($item[6]),
                        'messages' => sanitize_text_field($item[7]),
                        'subtotal' => absint(lsdc_currency_cleanup($item[8])),
                        'total' => absint(lsdc_currency_cleanup($item[9])),
                        'currency' => sanitize_text_field($item[10]),
                        'gateway' => sanitize_text_field($item[11]),
                        'ip' => sanitize_text_field($item[12]),
                        'status' => sanitize_text_field($item[13]),
                        'date' => date('Y-m-d H:i:s', strtotime($item[14])),
                        'reference' => sanitize_text_field($item[15]),
                    );

                    if (in_array($id, $report_ids)) {
                        $reports->update($args, $id);
                    } else {
                        $reports->insert($args);
                    }

                    $counter++;
                }
            }
            echo json_encode(array(
                'status' => 'success',
                'message' => sprintf(__('Successfull import %d data', 'lsdc'), abs($counter)),
            ));
            Log::record('Successfull import ' . $counter . ' data', INFO);
        } else {
            echo json_encode(array(
                'status' => 'failed',
                'message' => __("Import Data Failed, Data empty", "lsdc"),
            ));
            Log::record('Import Data Failed, Data empty', WARNING);
        }

        wp_die();
    }

    public function admin_report_action()
    {
        if (!check_ajax_referer('lsdc_admin_nonce', 'security')) {
            wp_send_json_error('Invalid security token sent.');
        }

        // TODO : Error Notification 
        $act = esc_attr($_POST['act']);
        $report_id = absint($_POST['id']);
        $data = isset($_POST['update_data']) ? $_POST['update_data'] : '';

        $reports = new Reports_Repository;

        switch ($act) {
            case 'completed':
                $reports->completed($report_id);
                echo 'action_success';
                break;
            case 'delete':
                $reports->delete($report_id);
                echo 'action_success';
                break;
            case 'read':
                echo json_encode($reports->read(array('report_id' => $report_id)));
                break;
            case 'update':

                $args = array(
                    'name' => sanitize_text_field($data['name']),
                    'phone' => sanitize_text_field($data['phone']),
                    'status' => sanitize_text_field($data['status']),
                    'date' => sanitize_text_field($data['date']),
                );
                $reports->update($args, $report_id);
                echo 'action_success';
                break;
        }
        wp_die();
    }

    public function admin_report_bulk_action()
    {
        if (!check_ajax_referer('lsdc_admin_nonce', 'security')) {
            wp_send_json_error('Invalid security token sent.');
        }

        $data = $_POST['data'];
        $data_array = explode(',', $data);
        $act = esc_attr($_POST['act']);

        $reports = new Reports_Repository;

        switch ($act) {
            case 'bulk_pending':
                foreach ($data_array as $id) {
                    $reports->pending($id);
                }
                break;
            case 'bulk_delete':
                foreach ($data_array as $id) {
                    $reports->delete($id);
                }
                break;
            case 'bulk_complete':
                foreach ($data_array as $id) {
                    $reports->completed($id);
                }
                break;
        }
        echo 'action_success';
        wp_die();
    }

    public function statistics_chart_data()
    {
        if (!check_ajax_referer('lsdc_admin_nonce', 'security')) {
            wp_send_json_error('Invalid security token sent.');
        }

        $report = $_POST['report'];
        $reports = new Reports_Repository;
        echo json_encode($reports->chart($report));
        wp_die();
    }

    /**
     * @package LSDCommerce
     * @subpackage Notification
     * Handle Reset Email Template by Type
     *
     * @since    1.0.0
     */
    public function notification_email_reset()
    {
        if (!check_ajax_referer('lsdc_admin_nonce', 'security')) {
            wp_send_json_error('Invalid security token sent.');
        }

        $email_type = esc_attr($_POST['email_type']);

        $settings = get_option('lsdc_notification_email'); // lsdc_notification_email
        $settings[$email_type]['saved'] = ''; //Saving
        $settings[$email_type]['header_bg'] = sanitize_hex_color('#ff0000'); // header background
        update_option('lsdc_notification_email', $settings);

        $source_template = file_get_contents(LSDD_PATH . 'templates/' . $email_type . '-source-' . lsdc_get_country() . '.html'); //getting template email by type
        $clean_template_html = stripslashes($source_template_html);
        file_put_contents(LSDD_STORAGE . '/email-' . $email_type . '-' . lsdc_get_country() . '.html', $clean_template_html); //save to file

        echo 'action_success';
        wp_die();
    }
}
new AJAX;
?>