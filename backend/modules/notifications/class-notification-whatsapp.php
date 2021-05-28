<?php
namespace LSDCommerce;

if (!defined('ABSPATH')) {
    exit;
}

class WhatsappSenderPad extends Notification_Template
{
    protected $id = 'lsdcommerce_whatsapp';
    protected $name = 'SenderPad';
    protected $type = 'whatsapp';

    /**
     * Template when donors create donation
     *
     * @var string
     */
    protected $template_order = 'Kepada YTH Bpk/Ibu *{{donors}}*
Berikut ini Pesanan Anda :
{{program}}

Total Pembayaran :
{{nominal}}

Silahkan Lakukan Pembayaran
{{payment}}

Salam Hangat
*LSDDonasi*';

    /**
     * Template when donors completed the payment
     *
     * @var string
     */
    protected $template_completed = 'Terimakasih *{{donors}}*
atas donasi yang telah Anda berikan
Donasi {{program}} akan kami sampaikan kepada orang-orang yang membutuhkan

*Semoga menjadi amal ibadah anda dan Tuhan memberi keberkahan*

Salam Hangat
*LSDDonasi*';

    /**
     * Template for following up, abandon donor
     *
     * @var string
     */
    protected $template_followup;

    /**
     * Api Key for Credentials to SenderPad
     */
    protected $apikey;

    /**
     * Constructing Class
     */
    public function __construct()
    {
        $this->default_settings();

        // Setter Options
        $settings = get_option($this->id);

        $whatsapp_message = isset($settings['messages']) ? $settings['messages'] : array();
        $this->order_message = isset($whatsapp_message['order']) ? $whatsapp_message['order'] : '';
        $this->completed_message = isset($whatsapp_message['completed']) ? $whatsapp_message['completed'] : '';

        $whatsapp_settings = isset($settings['settings']) ? $settings['settings'] : array();
        $this->apikey = isset($whatsapp_settings['apikey']) ? $whatsapp_settings['apikey'] : '';

        // Action for Test and Simpan
        add_action('wp_ajax_lsdc_whatsapp_senderpad_test', array($this, 'testing'));
        add_action('wp_ajax_lsdc_whatsapp_senderpad_save', array($this, 'save'));

        // Action for Templating Notification
        add_action('lsdcommerce/notification/processing', [$this, 'templating']);

        // $order = array(
        //     'event' => 'order',
        //     'phone' => '08561655028',
        //     'donors' => 'Lasida',
        //     'nominal' => 'Rp 150.000',
        //     'program' => 'Bantu Sesama',
        //     'payment' => 'Transfer Bank - BCA'
        // );
        // $this->templating( $order );

        // $completed = array(
        //     'event' => 'completed',
        //     'phone' => '08561655028',
        //     'donors' => 'Lasida',
        //     'program' => 'Bantu Sesama',
        // );
        // $this->templating( $completed );
    }

    /**
     * Setup Default Values
     *
     * @return void
     */
    public function default_settings()
    {
        /* Empty Settings -> Set Default Data */
        $settings = get_option($this->id);
        if (empty($settings)) {
            $new = array();
            $new['messages']['order'] = $this->template_order;
            $new['messages']['completed'] = $this->template_completed;
            $new['settings']['apikey'] = '';
            update_option($this->id, $new);
        }

        if (empty($settings['messages']['completed'])) {
            $new = array();
            $new['messages']['completed'] = $this->template_completed;
            $new['messages']['order'] = $settings['messages']['order'];
            $new['settings']['apikey'] = $settings['settings']['apikey'];
            update_option($this->id, $new);
        }

        if (empty($settings['messages']['order'])) {
            $new = array();
            $new['messages']['order'] = $this->template_order;
            $new['messages']['completed'] = $settings['messages']['completed'];
            $new['settings']['apikey'] = $settings['settings']['apikey'];
            update_option($this->id, $new);
        }
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
    public function templating( array $object )
    {
        if ($this->status()) {
            
            $object['payment'] = $object['payment_text'];
            unset($object['payment_text']);

            $settings = get_option($this->id);
            $whatsapp_message = isset($settings['messages']) ? $settings['messages'] : array();
            $template = $whatsapp_message[$object['event']];
            $phone = $object['phone'];

            // Check Template
            if(empty($template)){
                $this->log( empty($phone) ? 'Not Set' : $phone, 'On ' . ucfirst($object['event']), __('Please set template completed first', 'lsdcommerce'));
                return;
            }
            
            // Checking Receiver
            if(empty($phone)){
                $this->log( empty($phone) ? 'Not Set' : $phone, 'On ' . ucfirst($object['event']), __('User not fill the whatsapp number', 'lsdcommerce'));
                return;
            }

            // Templating
            foreach ($object as $key => $item) {
                $template = str_replace("{{" . $key . "}}", $item, $template);
            }

            // Send Message
            if( $object['payment'] != false ){ // Notification Pattern not Palse
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
    public function send( array $obj )
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
        
        $response = wp_safe_remote_post("https://senderpad.com/route/v1/send/message/", $payload);
        $response_back = json_decode(wp_remote_retrieve_body($response), true);

        if (isset($response_back['status']) && $response_back['status'] != 'FAILED' ) {
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

        $args = array('event' => 'test', 'receiver' => $phone, 'message' => '*LSDDonation* :: Whatsapp Notification Test using SenderPad' );

        if ($this->send( $args )) {
            echo 200;
        }else{
            echo 400;
        }
        
        wp_die();
    }

    public function manage()
    {
        ?>
        <style>
            /* Action Tab */
            #tab-senderpad-log:checked~.tab-body-wrapper #tab-body-senderpad-log,
            #tab-senderpad-order:checked~.tab-body-wrapper #tab-body-senderpad-order,
            #tab-senderpad-completed:checked~.tab-body-wrapper #tab-body-senderpad-completed,
            #tab-senderpad-followup:checked~.tab-body-wrapper #tab-body-senderpad-followup,
            #tab-senderpad-settings:checked~.tab-body-wrapper #tab-body-senderpad-settings {
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
            <input type="radio" name="senderpad" id="tab-senderpad-log" checked="checked"/>
            <label class="tab" for="tab-senderpad-log"><?php _e('Log', 'lsdcommerce');?></label>

            <input type="radio" name="senderpad" id="tab-senderpad-order"/>
            <label class="tab" for="tab-senderpad-order"><?php _e('Order Masuk', 'lsdcommerce');?></label>

            <input type="radio" name="senderpad" id="tab-senderpad-completed"/>
            <label class="tab" for="tab-senderpad-completed"><?php _e('Order Selesai', 'lsdcommerce');?></label>
            <!--
            <input type="radio" name="senderpad" id="tab-senderpad-followup"/>
            <label class="tab" for="tab4"><?php //_e('On FollowUp', 'lsdcommerce');?></label> -->

            <input type="radio" name="senderpad" id="tab-senderpad-settings"/>
            <label class="tab" for="tab-senderpad-settings"><?php _e('Pengaturan', 'lsdcommerce');?></label>

            <div class="tab-body-wrapper">

                <!------------ Tab : Test and Log ------------>
                <div id="tab-body-senderpad-log" class="tab-body">
                    <p>Anda dapat mengirimkan notifikasi ke pembeli melalui whatsapp, hubungkan perangkat anda ke layanan senderpad<br> dan mulai nikmati pengiriman otomatis untuk setiap orderan, yang masuk dan selesai :) </p>
                    <a href="https://senderpad.com/" target="_blank" style="margin-top:3px;margin-bottom:15px;background:#b21919;border:none;border-radius: 20px;width:180px;" class="btn btn-primary input-group-btn"><?php _e('Daftar SenderPad', "lsdcommerce");?></a>

                    <div class="divider" data-content="Tes Notifikasi"></div>
                    <div class="input-group" style="width:50%;">
                        <input id="lsdc_senderpad_test" style="margin-top:3px;" class="form-input input-md" type="text" placeholder="0812387621812">
                        <button id="lsdc_senderpad_test" style="margin-top:3px;" class="btn btn-primary input-group-btn"><?php _e('Tes', "lsdcommerce");?></button>
                    </div>

                    <br>

                    <div class="divider" data-content="Log Notifikasi"></div>
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
                            <tr><td><?php _e('Log Kosong', 'lsdcommerce');?></td></tr>
                        <?php endif;?>
                        </tbody>
                    </table>

                </div>

                <!------------ Tab : On Order ------------>
                <div id="tab-body-senderpad-order" class="tab-body">

                    <div class="columns col-gapless">
                        <!-- Email Instruction and Editor -->
                        <div class="column col-3" style="padding:0 10px 0 0;">

                            <div class="option-form">
                                <h6><?php _e('Tanda Pengganti', 'lsdcommerce');?> : </h6>
                                <small>
                                    {{nama_pembeli}} <code>John Doe </code><br>
                                    {{nohp_pembeli}} <code><?php echo lsdc_currency_display("symbol") . lsdc_currency_display("format"); ?></code><br>
                                    {{orderan}} <code>Redefined Plastic</code>
                                    {{pembayaran}} <code>Payment</code>
                                </small>
                            </div>
                            <br>
                            <button data-type="order" class="btn btn-primary input-group-btn lsdc_senderpad_templates_save"><?php _e('Simpan', 'lsdcommerce');?></button>
                        </div>


                        <!-- Email Preview -->
                        <div class="column col-8" style="margin-left:35px;">
                        
                            <?php if (!isset($this->order_message)): ?>
                                <div class="toast toast-error" style="width: 100%;margin: 10px auto;">
                                    <button class="btn btn-clear float-right"></button>
                                    <?php _e('Tolong sesuaikan template dengan kebutuhan anda', 'lsdcommerce');?>
                                </div>
                            <?php endif;?>
                            <!-- Migration Alert -->
                            <textarea data-event="order" class="form-input lsdc_senderpad_message_templates" placeholder="Pesan Notifikasi Untuk Donatur ketika Memesan" rows="14"><?php echo esc_attr($this->order_message); ?></textarea>
                        </div>

                    </div>
                </div>

                <!------------ Tab : On Completed ------------>
                <div id="tab-body-senderpad-completed" class="tab-body">

                    <div class="columns col-gapless">
                        <!-- Email Instruction and Editor -->
                        <div class="column col-3" style="padding:0 10px 0 0;">

                            <div class="option-form">
                                <h6><?php _e('Tanda Pengganti', 'lsdcommerce');?> : </h6>
                                <small>
                                    {{nama_pembeli}} <code>John Doe </code><br>
                                    {{orderan}} <code>Redefined Plastic</code>
                                </small>
                            </div>
                            <br>

                            <button data-type="completed" class="btn btn-primary input-group-btn lsdc_senderpad_templates_save"><?php _e('Simpan', 'lsdcommerce');?></button>
                        </div>

                        <div class="column col-8" style="margin-left:35px;">
                            <?php if (!isset($this->completed_message)): ?>
                                <div class="toast toast-error" style="width: 100%;margin: 10px auto;">
                                    <button class="btn btn-clear float-right"></button>
                                    <?php _e('Tolong sesuaikan template dengan kebutuhan anda', 'lsdcommerce');?>
                                </div>
                            <?php endif;?>
                            <!-- Migration Alert -->
                            <textarea data-event="completed" class="form-input lsdc_senderpad_message_templates" placeholder="Pesan Notifikasi Untuk Donatur Ketika Pembayaran Berhasil" rows="14"><?php echo esc_attr($this->completed_message); ?></textarea>
                        </div>
                    </div>
                    <!-- Content Email ketika Lunas -->
                </div>


                <!------------ Tab : FollowUp ------------>
                <div id="tab-body-senderpad-followup" class="tab-body">
                    <!-- TODO : Follow Up Notification -->
                </div>

                <!------------ Tab : Settings ------------>
                <div id="tab-body-senderpad-settings" class="tab-body">
                    <!-- Content Pengaturan -->
                    <form class="form-horizontal" block="settings">

                    <!-- Sender Email -->
                    <div class="form-group">
                        <div class="col-3 col-sm-12">
                        <label class="form-label" for="country"><?php _e('API Key', "lsdcommerce");?></label>
                        </div>
                        <div class="col-9 col-sm-12">
                        <input class="form-input" type="password" autocompleted="off"  name="apikey" placeholder="B8as91na12m1nn1243nS1n24An1n021" style="width:320px" value="<?php esc_attr_e(isset($this->apikey) ? $this->apikey : null);?>">
                        </div>
                    </div>

                    <button class="btn btn-primary lsdc_admin_option_save" option="<?php echo $this->id; ?>" style="width:120px"><?php _e('Simpan', "lsdcommerce");?></button>
                    </form>

                </div>
            </div>

        </div>

        <script>
            // Simpan Template
            jQuery(document).on("click",".lsdc_senderpad_templates_save",function( e ) {
                jQuery(this).addClass('loading');
                let type    = jQuery(this).attr('data-type');
                let content = jQuery('.lsdc_senderpad_message_templates[data-event="'+ type +'"]').val();
                let that    = this;

                jQuery.post( lsdc_admin.ajax_url, {
                    action  : 'lsdc_whatsapp_senderpad_save',
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
            jQuery(document).on("click","#lsdc_senderpad_test",function( e ) {
                var senderpad_number = jQuery('#lsdc_senderpad_test').val();
                var that = this;

                if( senderpad_number != '' ){
                    jQuery(this).addClass('loading');
                    jQuery('#lsdc_senderpad_test').css('border', 'none');

                    jQuery.post( lsdc_admin.ajax_url, {
                        action : 'lsdc_whatsapp_senderpad_test',
                        phone  : senderpad_number,
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
                    jQuery('#lsdc_senderpad_test').css('border', '1px solid red');
                }
            });

        </script>
    <?php
    }
}
Notification::register("whatsapp-senderpad", new WhatsappSenderPad());
?>