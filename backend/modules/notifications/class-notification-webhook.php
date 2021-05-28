<?php
namespace LSDCommerce;

if (!defined('ABSPATH')) {
    exit;
}

class Webhook extends Notification_Template
{
    protected $id = 'lsdcommerce_webhook';
    protected $name = 'Webhook {Soon}';
    protected $type = 'webhook';
    protected $docs = array(
        'global' => '',
        'id' => '',
    );


    /**
     * Constructing Class
     */
    public function __construct()
    {
        $this->default_settings();

        // Setter Options
        $settings = get_option($this->id);


        // Action for Test and Save
        add_action('wp_ajax_lsdc_whatsapp_webhook_test', array($this, 'testing'));
        add_action('wp_ajax_lsdc_whatsapp_webhook_save', array($this, 'save'));

        // Action for Templating Notification
        add_action('lsdconation/notification/processing', [$this, 'templating']);

    }

    /**
     * Setup Default Values
     *
     * @return void
     */
    public function default_settings()
    {

    }

    /**
     * Templating Message From Hook
     * Get Template based on Event
     * Templating Data
     * Send Message
     *
     * @param array $data
     * @return void
     */
    public function templating(array $object)
    {
        if ($this->status()) {

            $object['payment'] = $object['payment_text'];
            unset($object['payment_text']);

            $settings = get_option($this->id);
            $whatsapp_message = isset($settings['messages']) ? $settings['messages'] : array();
            $template = $whatsapp_message[$object['event']];
            $phone = $object['phone'];

            // Check Template
            if (empty($template)) {
                $this->log(empty($phone) ? 'Not Set' : $phone, 'On ' . ucfirst($object['event']), __('Please set template completed first', 'lsdconation'));
                return;
            }

            // Checking Receiver
            if (empty($phone)) {
                $this->log(empty($phone) ? 'Not Set' : $phone, 'On ' . ucfirst($object['event']), __('User not fill the whatsapp number', 'lsdconation'));
                return;
            }

            // Templating
            foreach ($object as $key => $item) {
                $template = str_replace("{{" . $key . "}}", $item, $template);
            }

            // Send Message
            if ($object['payment'] != false) { // Notification Pattern not Palse
                $this->send(array('event' => $object['event'], 'receiver' => $phone, 'message' => $template));
            }

        }
    }

    /**
     * Log Notification
     * Implement contract form abstract
     *
     * @param string $reciever
     * @param string $event
     * @param string $message
     * @return void
     */
    public function log($reciever, $event, $message)
    {
        $db = get_option($this->id); /// Get Log
        $log = isset($db['log']) ? $db['log'] : array(); // Check Log

        // Auto Reset Log
        if (count($log) >= 30) {
            $log = array();
        }

        $log[] = array(lsdc_current_date(), $reciever, $event, $message); // Push New Log
        $db['log'] = $log; // Set Log

        // Saving Log
        update_option($this->id, $db);
    }

    /**
     * Send Message via REST API
     * Support Text
     * TODO :: Support Media
     *
     * @since 4.0.0
     * @param array $data
     * @return void
     */
    public function send(array $obj)
    {

        $body = array(
            'to' => $obj['receiver'],
            'message' => $obj['message'],
            // 'type' =>'image',
            // 'media_url' => $image_url,
        );

        $payload = array(
            'method' => 'POST',
            'timeout' => 30,
            'headers' => array(
                'apikey' => $this->apikey,
                'Content-Type' => 'application/json',
            ),
            'httpversion' => '1.0',
            'body' => json_encode($body),
            'cookies' => array(),
        );

        $response = wp_safe_remote_post("https://webhook.com/route/v1/send/message/", $payload);
        $response_back = json_decode(wp_remote_retrieve_body($response), true);

        if (isset($response_back['status']) && $response_back['status'] != 'FAILED') {
            $this->log($obj['receiver'], 'On ' . ucfirst($obj['event']), $response_back['message']);
            return true;
        } else {
            $this->log($obj['receiver'], 'Failed !', $obj['message']);
            return false;
        }
    }

    /**
     * Saving Option :: AJAX
     *
     * @return void
     */
    public function save()
    {
        if (!check_ajax_referer('lsdc_admin_nonce', 'security')) {
            wp_send_json_error('Invalid security token sent.');
        }

        $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
        $type = $_REQUEST['type'] == 'order' ? 'order' : 'completed';
        $content = $_REQUEST['content'];

        // Saving Template
        $option = get_option($this->id);
        $option['messages'][$type] = $content;
        update_option($this->id, $option);

        echo 'action_success';
        wp_die();
    }

    /**
     * Testing Method :: AJAX Processing
     *
     * @return void
     */
    public function testing()
    {
        if (!check_ajax_referer('lsdc_admin_nonce', 'security')) {
            wp_send_json_error('Invalid security token sent.');
        }

        $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
        $phone = esc_attr($_REQUEST['phone']);

        $args = array('event' => 'test', 'receiver' => $phone, 'message' => '*LSDDonation* :: Whatsapp Notification Test using SenderPad');

        if ($this->send($args)) {
            echo 200;
        } else {
            echo 400;
        }

        wp_die();
    }

    public function manage()
    {
        ?>
        <style>
            /* Action Tab */
            #tab-webhook-log:checked~.tab-body-wrapper #tab-body-webhook-log,
            #tab-webhook-order:checked~.tab-body-wrapper #tab-body-webhook-order,
            #tab-webhook-completed:checked~.tab-body-wrapper #tab-body-webhook-completed,
            #tab-webhook-followup:checked~.tab-body-wrapper #tab-body-webhook-followup,
            #tab-webhook-settings:checked~.tab-body-wrapper #tab-body-webhook-settings {
                position: relative;
                top: 0;
                opacity: 1;
            }

            .tab-body-wrapper .table-log th{
                display: inline-block;
            }

            .tab-body-wrapper .table-log tr{
                margin-bottom: 0;
            }

            .tab-body-wrapper .table-log tbody tr td{
                display: inline-block;
                padding: 10px;
            }

            .tab-body-wrapper .table-log.table td, .tab-body-wrapper .table-log.table th{
                border-bottom: 0;
            }
        </style>

        <style>
            #lsdc-editor{
                height:100%;
                margin-top: 20px;
            }

            .tab-body-wrapper label.fix{
                margin-top: 3px;font-weight: 600;float: left;padding: 5px 0 !important;font-size: 14px;
            }
        </style>

        <div class="tabs-wrapper">
            <input type="radio" name="webhook" id="tab-webhook-log" checked="checked"/>
            <label class="tab" for="tab-webhook-log"><?php _e('Log', 'lsdconation');?></label>

            <input type="radio" name="webhook" id="tab-webhook-order"/>
            <label class="tab" for="tab-webhook-order"><?php _e('Order Masuk', 'lsdconation');?></label>

            <input type="radio" name="webhook" id="tab-webhook-completed"/>
            <label class="tab" for="tab-webhook-completed"><?php _e('Order Selesai', 'lsdconation');?></label>
            <!--
            <input type="radio" name="webhook" id="tab-webhook-followup"/>
            <label class="tab" for="tab4"><?php //_e('On FollowUp', 'lsdconation');?></label> -->

            <input type="radio" name="webhook" id="tab-webhook-settings"/>
            <label class="tab" for="tab-webhook-settings"><?php _e('Pengaturan', 'lsdconation');?></label>

            <div class="tab-body-wrapper">

                <!------------ Tab : Test and Log ------------>
                <div id="tab-body-webhook-log" class="tab-body">

                    <p>Integrasi Notifikasi dengan <strong>Integromat</strong> atau <strong>Zapier</strong> akan segera hadir</p>

                    <!-- <div class="divider" data-content="Test Webhook"></div>
                    <div class="input-group" style="width:50%;">
                        <input id="lsdc_webhook_test" style="margin-top:3px;" class="form-input input-md" type="text" placeholder="0812387621812">
                        <button id="lsdc_webhook_test" style="margin-top:3px;" class="btn btn-primary input-group-btn"><?php _e('Test Webhook', "lsdc-webhook");?></button>
                    </div> -->

                    <br>

                    <div class="divider" data-content="Log Notifikasi Webhook"></div>
                    <table class="table-log table table-striped table-hover">
                        <tbody>
                        <?php $db = get_option($this->id);?>
                        <?php $log = isset($db['log']) ? $db['log'] : array();?>

                        <?php if ($log): ?>
                            <?php foreach (array_reverse($log) as $key => $value): ?>
                                <tr>
                                    <td><?php echo lsdc_date_format($value[0], 'j M Y, H:i:s'); ?></td>
                                    <td><?php echo json_encode($value[1]); ?></td>
                                    <td><?php echo $value[2]; ?></td>
                                    <td><?php echo $value[3]; ?></td>
                                </tr>
                            <?php endforeach;?>
                        <?php else: ?>
                            <tr><td><?php _e('Log Kosong', 'lsdconation');?></td></tr>
                        <?php endif;?>
                        </tbody>
                    </table>

                </div>

                <!------------ Tab : Order Masuk ------------>
                <div id="tab-body-webhook-order" class="tab-body">
                    
                    <h6><?php _e('Webhook URL', 'lsdconation');?> : </h6>
                    <textarea name="" id="" cols="100" rows="3"><?php echo get_rest_url(); ?>lsdcommerce/notification/webhook/order</textarea>
                   
                </div>

                <!------------ Tab : Order Selesai ------------>
                <div id="tab-body-webhook-completed" class="tab-body">
     
                    <h6><?php _e('Webhook URL', 'lsdconation');?> : </h6>
                    <textarea name="" id="" cols="100" rows="3"><?php echo get_rest_url(); ?>lsdcommerce/notification/webhook/completed</textarea>
                   
                </div>


                <!------------ Tab : FollowUp ------------>
                <div id="tab-body-webhook-followup" class="tab-body">
                    <!-- TODO : Follow Up Notification -->
                </div>

                <!------------ Tab : Pengaturan ------------>
                <div id="tab-body-webhook-settings" class="tab-body">

                </div>
            </div>

        </div>

        <script>
            // Save Template
            jQuery(document).on("click",".lsdc_webhook_templates_save",function( e ) {
                jQuery(this).addClass('loading');
                let type    = jQuery(this).attr('data-type');
                let content = jQuery('.lsdc_webhook_message_templates[data-event="'+ type +'"]').val();
                let that    = this;

                jQuery.post( lsdc_admin.ajax_url, {
                    action  : 'lsdc_whatsapp_webhook_save',
                    type    : type,
                    content : content,
                    security : lsdc_admin.ajax_nonce,
                    }, function( response ){
                        if( response.trim() == 'action_success' ){
                            jQuery(that).removeClass('loading');
                        }
                    }).fail(function(){
                        alert('Failed, please check your internet');
                        location.reload();
                    }
                );
            });

            // On User Sending Test Email
            jQuery(document).on("click","#lsdc_webhook_test",function( e ) {
                var webhook_number = jQuery('#lsdc_webhook_test').val();
                var that = this;

                if( webhook_number != '' ){
                    jQuery(this).addClass('loading');
                    jQuery('#lsdc_webhook_test').css('border', 'none');

                    jQuery.post( lsdc_admin.ajax_url, {
                        action : 'lsdc_whatsapp_webhook_test',
                        phone  : webhook_number,
                        security : lsdc_admin.ajax_nonce,
                        }, function( response ){
                            if( response.trim() == 200 ){
                                jQuery(that).removeClass('loading');
                                jQuery(that).text("Success");
                            }else{
                                jQuery(that).removeClass('loading');
                                jQuery(that).text("Failed");
                            }
                        }).fail(function(){
                            alert('Failed, please check your internet');
                        }
                    );

                }else{
                    jQuery('#lsdc_webhook_test').css('border', '1px solid red');
                }
            });

        </script>
    <?php
}
}
Notification::register("webhook", new Webhook());
?>