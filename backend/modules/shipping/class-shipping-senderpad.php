<?php
namespace LSDCommerce;

if (!defined('ABSPATH')) {
    exit;
}

class Senderpad_Shipping extends Shipping_Template
{
    protected $id = 'senderpad';
    protected $name = 'Senderpad';
    protected $type = 'WhatsApp';
    protected $docs = array(
        'global' => 'https://learn.lsdplugins.com/en/docs/lsdcommerce/settings/email-notifications/',
        'id' => 'https://learn.lsdplugins.com/docs/lsdcommerce/pengaturan/notifikasi-email/',
    );

    private $log = array();

    private $order_saved;
    private $order_bg;

    private $completed_saved;
    private $completed_bg;

    private $settings = array();
    private $sender;
    private $sender_email;

    const EVENT_ORDER = 'order';
    const EVENT_COMPLETED = 'completed';
    const EVENT_TEST = 'test';

    private $country;

    public function __construct()
    {
        $this->default_settings();

        // Setter
        $this->settings = get_option($this->id); // senderpad

        $order = isset($this->settings['order']) ? $this->settings['order'] : array();
        $this->order_saved = isset($order['saved']) ? $order['saved'] : '';
        $this->order_bg = isset($order['header_bg']) ? $order['header_bg'] : '';
        $this->order_subject = isset($order['subject']) ? $order['subject']: '';

        $completed = isset($this->settings['completed']) ? $this->settings['completed'] : array();
        $this->completed_saved = isset($completed['saved']) ? $completed['saved'] : '';
        $this->completed_bg = isset($completed['header_bg']) ? $completed['header_bg'] : '';
        $this->completed_subject = isset($completed['subject']) ? $completed['subject'] : '';

        $settings = isset($this->settings['settings']) ? $this->settings['settings'] : array();
        $this->sender = isset($settings['sender']) ? $settings['sender'] : '';
        $this->sender_email = isset($settings['sender_email']) ? $settings['sender_email'] : '';

        
        add_action( 'wp_ajax_lsdc_notification_email_test', [ $this, 'testing'] );
        add_filter( 'wp_mail', [$this, 'on_wp_mail'] );
        add_filter( 'wp_mail_failed', [$this, 'failed_wp_mail'] );

        // Shipping Hook, Every Shipping Generate will Call This.
        add_action('lsdcommerce/notification/processing', [$this, 'templating']);
        add_action('init', [$this, 'template_test']);
    }

    public function template_test()
    {
        // $order = array(
        //     'event' => 'order',
        //     'receiver' => 'lasidaziz@gmail.com',
        //     'donors' => 'Lasida',
        //     'nominal' => 'Rp 150.000',
        //     'program' => 'Bantu Sesama',
        //     'country' => 'id',
        //     'payment' => 'Transfer Bank - BCA'
        // );
        // $this->templating( $order );

        // $completed = array(
        //     'event' => 'completed',
        //     'receiver' => 'lasidaziz@gmail.com',
        //     'donors' => 'Lasida',
        //     'program' => 'Bantu Sesama',
        //     'country' => 'id',
        // );
        // $this->templating( $completed );
    }

    public function on_wp_mail( $args ){
        $this->log($args["to"], $args["subject"],__('Email was sending...', 'lsdcommerce'));
        return $args;
    }

    public function failed_wp_mail($error){
        $err = $error->error_data['wp_mail_failed'];
        $errmsg = $error->errors['wp_mail_failed'][0];
        $this->log( $err['to'][0],  $err['subject'], 'Failed : '. $errmsg );
    }

    public function default_settings()
    {
        // Empty Option -> Set Default Value :: New User !!!
        if (empty(get_option($this->id))) {
            $new = array();
            $new['order']['header_bg'] = '#f7f7f7';
            $new['order']['subject'] = __('{{donors}} your donation is still waiting to be completed', 'lsdcommerce');
            $new['order']['saved'] = false;
            $new['completed']['header_bg'] = '#f7f7f7';
            $new['completed']['subject'] = __('Thank you for donating at {{program}}', 'lsdcommerce');
            $new['completed']['saved'] = false;
            $new['settings']['sender'] = 'LSD Plugins';
            $new['settings']['sender_email'] = 'sender@lsdplugins.com';
            update_option($this->id, $new);
        }
    }

    public function reset_settings()
    {
        update_option($this->id, null);
    }

    /**
     * Templating Email
     */
    public function templating($object)
    {
        if ( $this->status() ) { // Checking Status Shipping Enable ?

            $object['payment'] = $object['payment_html'];
            unset($object['payment_html']);

            $event = $object['event'];
            $country = $object['country'];
            $email = $object['email'];

            // Default Template
            if( file_exists( LSDC_PATH . 'frontend/templates/emails/' . $event . '-source-' .  $country .'.html') ){
                $template = file_get_contents( LSDC_PATH . 'frontend/templates/emails/' . $event . '-source-' .  $country .'.html');
            }
           
            // Personal Template
            if (isset($this->settings[$event]['saved']) && $this->settings[$event]['saved'] == true ) {
                if( file_exists( LSDC_STORAGE . 'email-' . $event . '-' . $country .'.html' ) ){
                    $template = file_get_contents( LSDC_STORAGE . 'email-' . $event . '-' . $country .'.html' );
                }
            }

            // #Email Property -> Subject Empty
            $subject = null;
            if (isset($this->settings[$event]['subject']) && $this->settings[$event]['subject'] == true) {
                $subject = $this->settings[$event]['subject'];
            }

            // Templating HTML with Data
            foreach ($object as $key => $item) {
                $template = str_replace("{{" . $key . "}}", $item, $template);
                $subject = str_replace("{{" . $key . "}}", $item, $subject);
            }

            // Check Template
            if(empty($template)){
                $this->log( empty($email) ? 'Not Set' : $email, 'On ' . ucfirst($object['event']), __('Please set template completed first', 'lsdcommerce'));
                return;
            }
            
            // Checking Receiver
            if(empty($email)){
                $this->log( empty($email) ? 'Not Set' : $email, 'On ' . ucfirst($object['event']), __('User not fill the email address', 'lsdcommerce'));
                return;
            }

            // Send the Email
            if( $object['payment'] != false ){ // Instruction Html Not False
                $this->send( array('event' => $object['event'], 'subject' => $subject, 'receiver' => $email, 'template' => $template));
            }
           
        }
    }

    /**
     * Email Sendint
     * 
     * @param array $obj
     * @return void;
     */
    public function send($obj)
    {
        $subject = $obj['subject'];
        $reciever = $obj['receiver'];
        $headers[] = 'From: '. $this->sender .' <'. $this->sender_email .'>';
        $headers[] = 'Content-Type: text/html; charset="' . get_option( 'blog_charset' ) . '"';
        $message = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><title>'. $subject .'</title><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><meta http-equiv="X-UA-Compatible" content="IE=edge" /><meta name="viewport" content="width=device-width, initial-scale=1.0 " /><meta name="format-detection" content="telephone=no" />';
        $message .= '<!--[if !mso]>--><meta http-equiv="X-UA-Compatible" content="IE=edge"><!--<![endif]--><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">';
        $message .= $obj['template'];
        $message .= '</html>';

        wp_mail( $reciever , $subject, $message, $headers );
    }

    /**
     * Shipping Log
     *
     * @param [type] $reciever
     * @param [type] $event
     * @param [type] $message
     * @return void
     */
    public function log($reciever, $event, $message)
    {
        // Reading Log
        $logdb = get_option($this->id); /// Get Log
        $log = isset($logdb['log']) ? $logdb['log'] : array(); // Check Log

        // Auto Reset Log on Reach 30 Line
        if (count($log) >= 30) {
            $log = array();
        }

        // Add New Line
        $log[] = array(lsdc_current_date(), $reciever, $event, $message); // Push New Log
        $logdb['log'] = $log; // Set Log

        // Saving Log
        update_option($this->id, $logdb);
    }

    /**
     * Email Testing
     */
    public function testing()
    {
        if (!check_ajax_referer('lsdc_admin_nonce', 'security')) {
            wp_send_json_error('Invalid security token sent.');
        }

        $email = sanitize_email($_POST['email']);

        if ($email) {
            $test_data =  array('event' => 'test', 'subject' => 'LSDonation Email Testing' , 'receiver' => $email, 'template' => "It's Works");
            $this->send( $test_data );
        } else {
            $this->log($email, 'On Test', __('Email Receiver empty, please fill before test email', 'lsdcommerce'));
        }

        echo 'action_success';
        wp_die();
    }

    /**
     * Shipping Email Manage Settings
     * And Templates.
     *
     * @return void
     */
    public function manage()
    {
        $this->country = lsdc_get_country();
        ?>
        <style>
            /* Action Tab */
            #tab1:checked~.tab-body-wrapper #tab-body-1,
            #tab2:checked~.tab-body-wrapper #tab-body-2,
            #tab3:checked~.tab-body-wrapper #tab-body-3,
            #tab4:checked~.tab-body-wrapper #tab-body-4,
            #tab5:checked~.tab-body-wrapper #tab-body-5 {
                position: relative;
                top: 0;
                opacity: 1;
            }

            .tab-body-wrapper .table-log th {
                display: inline-block;
            }

            .tab-body-wrapper .table-log tr {
                margin-bottom: 0;
            }

            .tab-body-wrapper .table-log tbody tr td {
                display: inline-block;
                padding: 10px;
            }

            .tab-body-wrapper .table-log.table td,
            .tab-body-wrapper .table-log.table th {
                border-bottom: 0;
            }
        </style>

        <style>
            #lsdc-editor {
                height: 100%;
                margin-top: 20px;
            }

            #lsdc-editor-order img,
            #lsdc-editor-completed img {
                cursor: pointer;
            }

            .tab-body-wrapper label.fix {
                margin-top: 3px;
                font-weight: 600;
                float: left;
                padding: 5px 0 !important;
                font-size: 14px;
            }
        </style>

        <div class="tabs-wrapper">
            <input type="radio" name="email" id="tab1" checked="checked" />
            <label class="tab" for="tab1"><?php _e('Log', 'lsdcommerce');?></label>

            <input type="radio" name="email" id="tab2" />
            <label class="tab" for="tab2"><?php _e('On Order', 'lsdcommerce');?></label>

            <input type="radio" name="email" id="tab3" />
            <label class="tab" for="tab3"><?php _e('On Completed', 'lsdcommerce');?></label>

            <!-- <input type="radio" name="email" id="tab4"/>
            <label class="tab" for="tab4"><?php //_e('On FollowUp', 'lsdcommerce');
        ?></label> -->

            <input type="radio" name="email" id="tab5" />
            <label class="tab" for="tab5"><?php _e('Settings', 'lsdcommerce');?></label>

            <div class="tab-body-wrapper">
                <!------------ Tab : Log ------------>
                <div id="tab-body-1" class="tab-body">
                    <div class="divider" data-content="Test Email"></div>
                    <div class="input-group" style="width:50%;">
                        <input id="lsdc_email_test" style="margin-top:3px;" class="form-input input-md" type="email" placeholder="email@gmail.com">
                        <button id="lsdc_email_sendtest" style="margin-top:3px;" class="btn btn-primary input-group-btn"><?php _e( 'Test Email', 'lsdcommerce' );
                        ?></button>
                    </div>
                    <br>

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
                                <tr>
                                    <td><?php _e('Empty Log', 'lsdcommerce');?></td>
                                </tr>
                            <?php endif;?>
                        </tbody>
                    </table>
                </div>

                <!------------ Tab : On Order ------------>
                <div id="tab-body-2" class="tab-body">

                    <div class="columns col-gapless">
                        <!-- Email Instruction and Editor -->
                        <div class="column col-3" style="padding:0 10px 0 0;">

                            <div id="instruction">

                                <!-- Subject Email -->
                                <div class="option-form">
                                    <label class="fix"><?php _e('Subject', 'lsdcommerce');?> : </label>
                                    <input type="text" id="lsdc_subject_order" data-type="order" value="<?php echo isset($this->order_subject) ? esc_attr($this->order_subject) : __('{{donors}} your donation is still waiting to be completed', 'lsdcommerce'); ?>">
                                    <small>
                                        {{donors}} <code>John Doe </code><br>
                                        {{nominal}} <code><?php echo lsdc_currency_display("symbol") . lsdc_currency_display("format"); ?></code><br>
                                        {{program}} <code>Redefined Plastic</code>
                                    </small>
                                </div>

                                <!-- Header Background Option -->
                                <div class="option-form float-left">
                                    <label class="fix"><?php _e('Header Background', 'lsdcommerce');?> : </label>
                                    <input type="text" id="lsdc_header_bg_order" data-type="order" value="<?php echo isset($this->order_bg) ? sanitize_hex_color($this->order_bg) : '#FF0000'; ?>" class="lsdc-email-picker">
                                    <div class="color-picker fix"></div>
                                </div>

                                <div style="clear:both"></div>

                                <div class="option-form">
                                    <h6><?php _e('Replace Tag', 'lsdcommerce');?> : </h6>
                                    <small>
                                        {{donors}} <code>John Doe </code><br>
                                        {{nominal}} <code><?php echo lsdc_currency_display("symbol") . lsdc_currency_display("format"); ?></code><br>
                                        {{program}} <code>Redefined Plastic</code>
                                        {{payment}} <code>Payment Information</code><br>
                                    </small>
                                </div>

                                <!-- Marker -->
                                <p id="tag" class="mt-2">

                                </p>
                            </div>

                            <button data-type="order" class="btn btn-primary input-group-btn <?php echo $this->id; ?>_save"><?php _e('Save', 'lsdcommerce');?></button>
                            <button data-type="order" class="btn btn-primary input-group-btn <?php echo $this->id; ?>_reset"><?php _e('Reset Template', 'lsdcommerce');?></button>
                        </div>

                        <!-- Email Preview -->
                        <div class="column col-9">
                            <!-- Migration Alert -->
                            <?php if (!file_exists(LSDC_STORAGE . '/email-order-' . $this->country . '.html')): ?>
                                <div class="toast toast-error" style="width: 90%;margin: 10px auto;">
                                    <button class="btn btn-clear float-right"></button>
                                    <?php _e('Please adjust replace tag in your notification template', 'lsdcommerce');?>
                                </div>
                            <?php endif;?>

                            <div id="lsdc-editor-order" class="penplate">
                                <?php
                                // Load Template
                                if ($this->order_saved) { // Exist Saved || Reset
                                    if (file_exists(LSDC_STORAGE . '/email-order-' . $this->country . '.html')) {
                                        require_once LSDC_STORAGE . '/email-order-' . $this->country . '.html';
                                    } else {
                                        require_once LSDC_PATH . 'frontend/templates/emails/order-source-' . $this->country . '.html';
                                        // Set Log : Cannot Saving Email to WP-Content
                                    }
                                } else {
                                    require_once LSDC_PATH . 'frontend/templates/emails/order-source-' . $this->country . '.html'; // On Not Exist Saved Data
                                }?>
                            </div>
                        </div>

                    </div>

                </div>
                <!------------ Tab : On Completed ------------>
                <div id="tab-body-3" class="tab-body">

                    <div class="columns col-gapless">
                        <!-- Email Instruction and Editor -->
                        <div class="column col-3" style="padding:0 10px 0 0;">

                            <div id="instruction">
                                <!-- Subject Email -->
                                <div class="option-form">
                                    <label style="margin-top: 3px;font-weight: 600;float: left;padding: 5px 0 !important;font-size: 14px;"><?php _e('Subject', 'lsdcommerce');?> : </label>
                                    <input type="text" id="lsdc_subject_completed" data-type="completed" value="<?php echo isset($this->completed_subject) ? esc_attr($this->completed_subject) : __('Donation Received, Thankyou {{donors}}', 'lsdcommerce'); ?>">
                                    <small>
                                        {{donors}} <code>John Doe </code><br>
                                        {{program}} <code>Redefined Plastic</code>
                                    </small>
                                </div>

                                <div class="option-form" style="float:left">
                                    <label style="margin-top: 3px;font-weight: 600;float: left;padding: 5px 0 !important;font-size: 14px;"><?php _e('Header Background', 'lsdcommerce');?> : </label>
                                    <input type="text" id="lsdc_header_bg_completed" data-type="completed" value="<?php echo isset($this->completed_bg) ? $this->completed_bg : '#FF0000'; ?>" class="lsdc-email-picker">
                                    <div class="color-picker" style="display: inline-block;z-index:999;"></div>
                                </div>

                                <div style="clear:both"></div>

                                <div class="option-form">
                                    <h6><?php _e('Replace Tag', 'lsdcommerce');?> : </h6>
                                    <small>
                                        {{donors}} <code>John Doe </code><br>
                                        {{nominal}} <code><?php echo lsdc_currency_display("symbol") . lsdc_currency_display("format"); ?></code><br>
                                        {{program}} <code>Redefined Plastic</code>
                                    </small>

                                </div>


                                <!-- Marker -->
                                <p id="tag" class="mt-2">

                                </p>
                            </div>


                            <button data-type="completed" class="btn btn-primary input-group-btn <?php echo $this->id; ?>_save"><?php _e('Save', 'lsdcommerce');?></button>
                            <button data-type="completed" class="btn btn-primary input-group-btn <?php echo $this->id; ?>_reset"><?php _e('Reset Template', 'lsdcommerce');?></button>
                        </div>

                        <div class="column col-9">
                            <!-- Migration Alert -->
                            <?php if (!file_exists(LSDC_STORAGE . '/email-completed-' . $this->country . '.html')): ?>
                                <div class="toast toast-error" style="width: 90%;margin: 10px auto;">
                                    <button class="btn btn-clear float-right"></button>
                                    <?php _e('Please adjust replace tag in your notification template', 'lsdcommerce');?>
                                </div>
                            <?php endif;?>

                            <div id="lsdc-editor-completed" class="penplate">
                                <?php
                                // Load Template
                                if ($this->completed_saved) { // Exist Saved || Reset
                                    if (file_exists(LSDC_STORAGE . '/email-completed-' . $this->country . '.html')) {
                                        require_once LSDC_STORAGE . '/email-completed-' . $this->country . '.html';
                                    } else {
                                        require_once LSDC_PATH . 'frontend/templates/emails/completed-source-' . $this->country . '.html';
                                        // Set Log : Cannot Saving Email to WP-Content
                                    }
                                } else {
                                    require_once LSDC_PATH . 'frontend/templates/emails/completed-source-' . $this->country . '.html'; // On Not Exist Saved Data
                                }?>
                            </div>

                        </div>
                    </div>
                    <!-- Content Email ketika Lunas -->
                </div>


                <!------------ Tab : FollowUp ------------>
                <div id="tab-body-4" class="tab-body">
                    <!-- Followup Comming Soon -->
                </div>

                <!------------ Tab : Settings ------------>
                <div id="tab-body-5" class="tab-body">
                    <!-- Content Pengaturan -->
                    <form class="form-horizontal" block="settings">

                        <!-- Sender -->
                        <div class="form-group">
                            <div class="col-3 col-sm-12">
                                <label class="form-label" for="country"><?php _e('Sender', 'lsdcommerce');?></label>
                            </div>
                            <div class="col-9 col-sm-12">
                                <input class="form-input" type="text" name="sender" placeholder="LSDPlugins" style="width:320px" value="<?php esc_attr_e(isset($this->sender) ? $this->sender : null);?>">
                            </div>
                        </div>

                        <!-- Sender Email -->
                        <div class="form-group">
                            <div class="col-3 col-sm-12">
                                <label class="form-label" for="country"><?php _e('Sender Email', 'lsdcommerce');?></label>
                            </div>
                            <div class="col-9 col-sm-12">
                                <input class="form-input" type="email" name="sender_email" placeholder="info@lsdplugins.com" style="width:320px" value="<?php esc_attr_e(isset($this->sender_email) ? $this->sender_email : null);?>">
                            </div>
                        </div>

                        <button class="btn btn-primary lsdc_admin_option_save" option="lsdc_notification_email" style="width:120px"><?php _e('Save', 'lsdcommerce');?></button>
                    </form>

            
                </div>
            </div>

        </div>


        <script>
            jQuery(document).ready(function() {
                jQuery('.penplate').penplate({
                    heading_1_tag: 'h1',
                    heading_2_tag: 'h2',
                    heading_3_tag: 'h3',
                    heading_4_tag: 'h4',
                });

                var file_frame;
                var attachment;

                jQuery(document).on("click", "#lsdc-editor-order table img, #lsdc-editor-completed table img", function() {
                    event.preventDefault();
                    var that = this;

                    // if the file_frame has already been created, just reuse it
                    var frame = file_frame;
                    if (frame) {
                        frame.open();
                        return;
                    }

                    frame = wp.media.frames.frame = wp.media({
                        title: 'Image Upload',
                        button: {
                            text: 'Choose Image'
                        },
                        multiple: false // set this to true for multiple file selection
                    });

                    frame.on('select', function() {
                        attachment = frame.state().get('selection').first().toJSON();
                        // $( '#Public-button' ).hide();
                        jQuery(that).attr('data-id', attachment.id);
                        jQuery(that).attr('src', attachment.url);
                    });

                    frame.open();
                });

            });

            // On Email Editor Save
            jQuery(document).on("click", ".lsdc_notification_email_save", function(e) {
                jQuery(this).addClass('loading');
                let lsdc_email_type = jQuery(this).attr('data-type');
                let that = this;

                jQuery.post(lsdc_admin.ajax_url, {
                    action: 'lsdc_notification_email_template',
                    email_type: lsdc_email_type,
                    data: jQuery('#lsdc-editor-' + lsdc_email_type).html(),
                    header_bg: jQuery('#lsdc_header_bg_' + lsdc_email_type).val(),
                    subject: jQuery('#lsdc_subject_' + lsdc_email_type).val(),
                    security: lsdc_admin.ajax_nonce,
                }, function(response) {
                    if (response.trim() == 'action_success') {
                        jQuery(that).removeClass('loading');
                    }
                }).fail(function() {
                    alert('Failed, please check your internet');
                });
            });

            // On Email Editor Reset
            jQuery(document).on("click", ".lsdc_notification_email_reset", function(e) {
                jQuery(this).addClass('loading');
                let lsdc_email_type = jQuery(this).attr('data-type'); // order | completed
                let that = this;

                jQuery.post(lsdc_admin.ajax_url, {
                    action: 'lsdc_notification_email_reset',
                    email_type: lsdc_email_type,
                    security: lsdc_admin.ajax_nonce,
                }, function(response) {
                    if (response.trim() == 'action_success') {
                        location.reload();
                    }
                }).fail(function() {
                    alert('Failed, please check your internet');
                });
            });

            // On User Sending Test Email
            jQuery(document).on("click", "#lsdc_email_sendtest", function(e) {
                var email_fortest = jQuery('#lsdc_email_test').val();

                if (validateEmail(email_fortest) && email_fortest != '') {
                    jQuery(this).addClass('loading');
                    jQuery('#lsdc_email_test').css('border', 'none');

                    jQuery.post(lsdc_admin.ajax_url, {
                        action: 'lsdc_notification_email_test',
                        email: email_fortest,
                        security: lsdc_admin.ajax_nonce,
                    }, function(response) {
                        if (response.trim() == 'action_success') {
                            location.reload();
                        }
                    }).fail(function() {
                        alert('Failed, please check your internet');
                    });

                } else {
                    jQuery('#lsdc_email_test').css('border', '1px solid red');
                }
            });
        </script>
    <?php
    }
}
Shipping::register("senderpad-shipping", new Senderpad_Shipping());
?>