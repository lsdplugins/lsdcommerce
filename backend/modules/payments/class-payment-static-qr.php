<?php
namespace LSDCommerce;

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Static_QR')){

    class Static_QR extends Payment_Template
    {
        public $id = 'static_qr';

        public $country = 'id';

        public function __construct()
        {
            $this->default_settings();  

            // $this->reset_settings();

            // Inject Thankyou Instruction based on Payment
            add_action("lsddonation/confirmation/instruction", [$this, 'instruction'], 10, 2);
        }

        /**
         * Set Default QR Static Settings
         *
         * @return void
         */
        public function default_settings()
        {
            $payment_data = get_option('lsdd_payment_settings');
            
            if ( !isset($payment_data[$this->id]) || $payment_data[$this->id] == null ) { // Empty and Not Isset
                $payment_data = is_array($payment_data) ? $payment_data : array();
                
                $payment_data[$this->id] = array(
                    'name'              => 'QRIS',
                    'description'       => 'Semua Pembayaran dengan Kode QR',
                    'logo'              => LSDD_URL . 'assets/images/payment/qris.png',
                    'group'             => 'e-money',
                    'group_name'        => 'E-Money',
                    'docs'              => 'google.com',
                    'template_class'    => 'Static_QR',
                    'qr_image'          => 'https://i.ibb.co/z2031gJ/QRIS-LSDPlugins.png',
                    'qr_merchant'       => 'LSD Plugins',
                    'instruction'       => __('Scan this QR or download the qr code image and open it in your QR payment application', 'lsddonation'),
                    'confirmation'      => self::MANUAL,
                    "excluded_fields"   => ['lsdd_form_email'],
                    "required_fields"   => ['lsdd_form_name', 'lsdd_form_phone']
                );
                update_option('lsdd_payment_settings', $payment_data);
            }
        }

        /**
         * Reset QR Static Settings
         */
        private function reset_settings( $options )
        {
            $options = get_option('lsdd_payment_settings');
            if(isset($options[$this->id])){
                unset( $options[$this->id] );
                update_option('lsdd_payment_settings', $options);
                $this->default_settings();
            }
        }

        /**
         * Manage QR Static Settings
         */
        public function manage( $payment_id )
        {
     
            $payment_data = get_option('lsdd_payment_settings');
            $settings = $payment_data[$this->id];
            ?>
            <div id="<?php echo $this->id; ?>_content" class="payment-editor d-hide">
                <div class="panel-header text-center">
                    <div class="panel-title h5 mt-10 float-left"><?php _e('Edit QR Payment', 'lsddonation');?></div>
                    <div class="panel-close float-right"><i class="icon icon-cross"></i></div>
                </div>

                <div class="panel-body">
                    <form>
                        <div class="form-group">
                            <label class="form-label" for="name"><?php _e('Payment Name', 'lsddonation');?></label>
                            <input class="form-input" type="text" name="name" value="<?php echo esc_attr($settings['name']); ?>" placeholder="<?php echo $this->name; ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="description"><?php _e('Payment Description', 'lsddonation');?></label>
                            <textarea class="form-input" name="description" placeholder="<?php _e('Description of payment', 'lsddonation');?>" lsd-rows="3"><?php esc_attr_e($settings['description']);?></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="logo"><?php _e('Payment Logo', 'lsddonation');?></label>
                            <?php if (current_user_can('upload_files')): ?>
                                <img style="width:150px;margin-bottom:15px;" src="<?php echo ($settings['logo'] == '') ? $this->logo : esc_url($settings['logo']); ?>"/>
                                <input class="form-input" type="text" style="display:none;" name="logo" value="<?php echo ($settings['logo'] == '') ? $this->logo : esc_url($settings['logo']); ?>" >
                                <input type="button" value="<?php _e('Choose Image', 'lsddonation');?>" class="lsdd_admin_upload btn col-12">
                            <?php endif;?>
                        </div>

                        <div class="divider text-center" style="margin-top:25px;" data-content="<?php _e('QR Information', 'lsddonation');?>"></div>

                        <div class="form-group">
                            <label class="form-label" for="qr_image"><?php _e('QR Code Image', 'lsddonation');?></label>
                            <?php if (current_user_can('upload_files')): ?>
                                <img style="width:150px;margin-bottom:15px;" src="<?php echo ($settings['qr_image'] == '') ? $this->qr_image : esc_url($settings['qr_image']); ?>"/>
                                <input class="form-input" type="text" style="display:none;" name="qr_image" value="<?php echo ($settings['qr_image'] == '') ? $this->logo : esc_url($settings['qr_image']); ?>" >
                                <input type="button" value="<?php _e('Choose QR Image', 'lsddonation');?>" class="lsdd_admin_upload btn col-12">
                            <?php endif;?>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="qr_merchant"><?php _e('QR Merchant', 'lsddonation');?></label>
                            <input class="form-input" type="text" name="qr_merchant" value="<?php echo $settings['qr_merchant']; ?>" placeholder="LSD Plugins">
                        </div>

                        <?php
                        /**
                         * Hooking Confirmation Status
                         */
                        do_action('lsddonation/admin/payment/static_qr');
                        ?>

                        <div class="divider text-center" style="margin-top:25px;" data-content="<?php _e('Instruction', 'lsddonation'); ?>"></div>

                        <div class="form-group">
                            <label class="form-label" for="instruction"><?php _e('Payment Instruction', 'lsddonation');?></label>
                            <textarea class="form-input" name="instruction" placeholder="<?php _e('Take a payment app and open scan qr payment', 'lsddonation');?>" lsd-rows="3"><?php esc_attr_e($settings['instruction']);?></textarea>
                        </div>

                        <!-- Form Exclude Options -->

                        <div class="divider text-center" style="margin-top:25px;" data-content="<?php _e('Form', 'lsddonation');?>"></div>

                        <div class="form-group">
                            <label class="form-label" for="instruction">
                                <?php _e('Hidden Fields', 'lsddonation');?>
                            </label>

                            <?php 
                                $forms = apply_filters("lsddonation/form/fields/payment" , array());
                                $excluded_fields = isset($settings['excluded_fields']) ? $settings['excluded_fields'] : array();
                            ?>
                            
                            <select multiple="multiple" name="excluded_fields[]" class="selectlive js-states form-select" 
                                data-placeholder="<?php _e( "Choose field to hidden on payment form", 'lsddonation' ); ?>">
                                <?php foreach ($forms as $key => $item) : ?>
                                    <option value="<?php echo $item['id']; ?>" <?php echo in_array($item['id'], $excluded_fields) ? 'selected' : ''; ?>><?php echo $item['label']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <p class="label-description mb-0"><?php _e('fields will not be displayed on the form page.', 'lsddonation');?></p>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="instruction">
                                <?php _e('Required Fields', 'lsddonation');?>
                            </label>

                            <?php 
                                $forms = apply_filters("lsddonation/form/fields/payment" , array());
                                $required_fields = isset($settings['required_fields']) ? $settings['required_fields'] : array();
                            ?>
                            
                            <select multiple="multiple" name="required_fields[]" class="selectlive js-states form-select" 
                                data-placeholder="<?php _e( "Choose field to Exclude", 'lsddonation' ); ?>">
                                <?php foreach ($forms as $key => $item) : ?>
                                    <option value="<?php echo $item['id']; ?>" <?php echo in_array($item['id'], $required_fields) ? 'selected' : ''; ?>><?php echo $item['label']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <p class="label-description mb-0"><?php _e('fields will set to required.', 'lsddonation');?></p>
                        </div>

                        <!-- End Of Options -->
                    </form>
                </div>

                <div class="panel-footer">
                    <button class="btn btn-primary btn-block lsdd-payment-save" id="<?php echo $this->id; ?>_payment"><?php _e('Save', 'lsddonation');?></button>
                </div>
            </div>
        <?php
        }

        public function instruction( int $report_id, $report )
        {
            $gateway = $report->gateway;

            if( $gateway == $this->id ) :

                $payment_settings = lsdd_payment_active();
                $settings = $payment_settings[$gateway];
                $logo = esc_url($settings['logo']);
                $name = esc_attr($settings['name']);
                $group = esc_attr($settings['group_name']);

                /**
                 * Set Scheduler :: +10 Seconds
                 * Send Notification to Whatsapp
                 * Notification Email was tackle by midtrans
                 */
                // if ( false === as_next_scheduled_action( 'lsdd_notication_services' ) ) {
                //     as_schedule_single_action(strtotime( '+2 minutes' ), "lsdd_notication_services" , array( $report_id . '-order' ) );
                // }
                ?>

                <h6 class="lsdp-mb-10 lsdp-mt-15 font-weight-bold">
                    <?php echo $group . ' - ' . $name; ?>
                </h6>
                <div class="brand-img lsdp-mb-15">
                    <img src="<?php echo esc_url($logo); ?>" alt="<?php echo esc_url($name); ?>" class="h-50">
                </div>

                <div class="lsdp-mb-5">
                    <!-- Account  -->
                    <p class="lsdp-mb-5 lsdp-font-13"><?php _e('QR Code', 'lsddonation');?></p>
                    <div class="lsdp-row no-gutters">
                        <div class="col-auto" style="height:220px;width:auto;">
                             <!-- TODO :: Set LSD Plugins QR -->
                            <img style="width:220px;height:100%;" src="<?php echo isset($settings['qr_image']) ? esc_url($settings['qr_image']) : 'https://i.ibb.co/z2031gJ/QRIS-LSDPlugins.png'; ?>" alt="<?php echo esc_attr($settings['qr_merchant']); ?>">
                        </div>
                    </div>

                    <!-- Merchant -->
                    <div class="lsdp-mb-15 lsdp-mt-10">
                        <p class="mb-0 lsdp-font-13"><?php _e('QR Merchant', 'lsddonation');?></p>
                        <h6 class="font-weight-medium mt-up-5">
                        <?php echo isset($settings['qr_merchant']) ?  esc_attr($settings['qr_merchant']) : null; ?>
                        </h6>
                    </div>
                </div>
                <?php
                echo esc_attr($settings['instruction']);
            endif;
        }

        /**
         * Formatting Notification Text Template
         *
         * @return void
         */
        public function notification_text( object $report_id, string $event, string $gateway )
        {
            $payment_settings = lsdd_payment_active();
            $settings = $payment_settings[$gateway];

            $name = esc_attr($settings['name']);
            $group = esc_attr($settings['group_name']);

            $template =  '*' . $group . ' - ' . $name .'*'. PHP_EOL;
            $template .= __( "QR Code Image", 'lsddonation' ) . ' : ' . esc_url( $settings['qr_image']) . PHP_EOL;
            $template .= __( "QR Merchant", 'lsddonation' ) . ' : ' . esc_attr($settings['qr_merchant'])  . PHP_EOL  . PHP_EOL;
            $template .= __( "Instruction", 'lsddonation' ) . PHP_EOL . esc_attr($settings['instruction'])  . PHP_EOL;
            return trim(preg_replace("/\t/", '', $template));
        }

        /**
         * Formatting Notification HTML Template
         *
         * @return void
         */
        public function notification_html( object $report_id, string $event, string $gateway )
        {

            $template = '<tr>
                <td align="left" style="font-size:0px;padding:0 25px 10px;">
                    <div style="font-family:Helvetica Neue,Arial,sans-serif;font-size:14px;line-height:22px;text-align:left;color:#525252;">
                        <p style="margin:0;padding:0;"><strong>' . $this->group_name . ' - ' . $this->name . '</strong></p>
                        <p style="margin:0;padding:0;">' . __( "QR Image", 'lsddonation' ) . '</p><br>
                        <img style="width:220px" src="'. esc_url( $settings['qr_image'] )  .'"/>
                        <p style="margin:0;padding:0;">' . __( "QR Merchant", 'lsddonation' ) . ' : <strong>' . $settings['qr_merchant'] . '</strong></p>
                        <br>
                        <h6>' . __( "Payment Instruction", 'lsddonation' ) . '</h6>
                        <p style="margin:0;padding:0;"><strong>' . $settings['instruction'] . '</strong></p>
                    </div>
                </td>
            </tr>
            <tr>
                <td align="left" style="font-size:0px;padding:0px 25px;">
                    <div
                        style="font-family:Helvetica Neue,Arial,sans-serif;font-size:14px;line-height:22px;text-align:left;color:#525252;">
                        <h6>Instruction</h6>
                        '. esc_attr($settings['instruction']) .'
                        <p>Please complete this payment according total, for automatic confirmation</p>
                    </div>
                </td>
            </tr>
            ';
            return trim(preg_replace("/\t/", '', $template));
        }

    }
    // Payments\Payment_Registrar::register("static_qr", new Static_QR());

}
?>