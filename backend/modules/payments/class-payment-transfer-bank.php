<?php
namespace LSDCommerce;

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Bank_Transfer')){

    class Bank_Transfer extends Payment_Template
    {
        public $id = 'bank_transfer';

        public $country = 'global';

        private $pointer;

        public function __construct()
        {
            $this->default_settings();

            // Set Setting Property Based on ID
            $settings = !empty(get_option('lsdd_payment_settings')) ? get_option('lsdd_payment_settings') : null; 
            $this->pointer = isset($settings['pointer'][$this->id]) ? $settings['pointer'][$this->id] : $this->id;

            // $this->reset_settings();

            // Inject Thankyou Instruction based on Payment
            add_action("lsddonation/confirmation/instruction", [$this, 'instruction'], 10, 2);
        }

        /**
         * Set Default Setting for New User
         *
         * @return void
         */
        private function default_settings()
        {
            $payment_data = get_option('lsdd_payment_settings');

            if ( !isset($payment_data[$this->id]) || $payment_data[$this->id] == null ) { // Empty and Not Isset
                $payment_data = is_array($payment_data) ? $payment_data : array();

                $payment_data[$this->id] = array(
                    'master'            => $this->id,
                    'pointer'           => $this->id,
                    'name'              => __('Bank Custom One', 'lsddonation'),
                    'description'       => 'Semua Pembayaran dengan Kode QR',
                    'logo'              => LSDD_URL . 'assets/images/payment/custom.png',
                    'group'             => 'bank_transfer',
                    'group_name'        => __('Bank Transfer', 'lsddonation'),
                    'docs'              => 'google.com',
                    'template_class'    => 'Bank_Transfer',
                    'bank_code'         => '014',
                    'swift_code'        => '',
                    'account_number'    => '652424242',
                    'account_owner'     => 'LSD Plugins',
                    'instruction'       => __('Please transfer to this bank account according to the total', 'lsddonation'),
                    'confirmation'      => self::MANUAL,
                    "excluded_fields"   => ['lsdd_form_email'],
                    "required_fields"   => ['lsdd_form_name', 'lsdd_form_phone']
                );

                update_option('lsdd_payment_settings', $payment_data);
            }
        }

        /**
         * Reset Settings
         *
         * @param array $options
         * @return void
         */
        private function reset_settings()
        {
            if(isset(get_option('lsdd_payment_settings')[$this->id])){
                $options = get_option('lsdd_payment_settings');
                unset( $options[$this->id] );
                update_option('lsdd_payment_settings', $options);
                $this->default_settings();
            }
        }

        /**
         * Manage Payment
         *
         * @return void
         */
        public function manage( $payment_id )
        {

            $payment_data = get_option('lsdd_payment_settings');
            $settings = $payment_data[$payment_id];
            ?>

            <div id="<?php echo $payment_id; ?>_content" class="payment-editor d-hide">
                <div class="panel-header text-center">
                    <div class="panel-title h5 mt-10 float-left"><?php _e('Edit Custom Bank', 'lsddonation');?></div>
                    <div class="panel-close float-right"><i class="icon icon-cross"></i></div>
                </div>

                <div class="panel-body">
                    <form>

                        <div class="form-group">
                            <label class="form-label" for="pointer">
                                <?php _e('Custom ID (Required)', 'lsddonation');?>
                            </label>
                            <input class="form-input" type="text" name="pointer" value="<?php echo esc_attr($payment_id); ?>" old-val="<?php echo esc_attr($payment_id); ?>" placeholder="bank_syariah_indonesia" required>
                            <p class="mb-0"><?php _e(' example', 'lsddonation' ); ?> : <strong>bank_</strong>syariah_indonesia</p>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="name">
                                <?php _e('Bank Name (Required)', 'lsddonation');?>
                            </label>
                            <input class="form-input" type="text" name="name" value="<?php echo esc_attr($settings['name']); ?>" placeholder="<?php echo $this->name; ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="logo">
                                <?php _e('Bank Logo (Required)', 'lsddonation');?>
                            </label>
                            <?php if (current_user_can('upload_files')): ?>
                                <img style="width:150px;margin-bottom:15px;" src="<?php echo ($settings['logo'] == '') ? $this->logo : esc_url($settings['logo']); ?>"/>
                                <input class="form-input" type="text" style="display:none;" name="logo" value="<?php echo ($settings['logo'] == '') ? $this->logo : esc_url($settings['logo']); ?>" >
                                <input type="button" value="<?php _e('Choose Image', 'lsddonation');?>" class="lsdd_admin_upload btn col-12">
                            <?php endif;?>
                        </div>

                        <div class="divider text-center" style="margin-top:25px;" data-content="<?php _e('Bank Account', 'lsddonation');?>"></div>

                        <div class="form-group">
                            <label class="form-label" for="bank_code">
                                <?php _e('Bank Code (Optional)', 'lsddonation');?>
                            </label>
                            <input class="form-input" type="text" name="bank_code" value="<?php echo abs($settings['bank_code']); ?>" placeholder="014">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="swift_code">
                                <?php _e('SWIFT Code (Optional)', 'lsddonation');?>
                            </label>
                            <input class="form-input" type="text" name="swift_code" value="<?php echo esc_attr($settings['swift_code']); ?>" placeholder="CENAIDJA">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="account_number">
                                <?php _e('Account Number (Required)', 'lsddonation');?>
                            </label>
                            <input class="form-input" type="number" name="account_number" value="<?php echo abs($settings['account_number']); ?>" placeholder="6545464646">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="account_owner">
                                <?php _e('Account Owner (Required)', 'lsddonation');?>
                            </label>
                            <input class="form-input" type="text" name="account_owner" value="<?php echo esc_attr($settings['account_owner']); ?>" placeholder="LSD Plugins">
                        </div>

                        <?php 
                            /**
                             * Hooking Options based on pointer
                             * Make it Relative
                             */
                            do_action("lsddonation/admin/payment/custombank/{$this->pointer}"); 
                        ?>

                        <div class="divider text-center" style="margin-top:25px;" data-content="<?php _e('Instruction', 'lsddonation');?>"></div>

                        <div class="form-group">
                            <label class="form-label" for="instruction">
                                <?php _e('Payment Instruction', 'lsddonation');?>
                            </label>
                            <textarea class="form-input" name="instruction" placeholder="<?php _e('Please make payments to this account according to the total', 'lsddonation');?>" lsd-rows="3"><?php esc_attr_e($settings['instruction']);?></textarea>
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

                        <!-- TODO : 4.1.1 -->
                        <?php if( $payment_id != $this->id ) : ?>
                        <button class="btn btn-light btn-block lsdd-payment-delete" style="background:#e85600;color:#fff;border:none;" id="<?php echo $payment_id; ?>_delete">
                            <?php _e('Delete', 'lsddonation');?>
                        </button>
                        <?php endif; ?>
                        
                    </form>
                </div>

                <div class="panel-footer">
                    <button class="btn btn-primary btn-block lsdd-payment-save" id="<?php echo $payment_id; ?>_payment">
                        <?php _e('Save', 'lsddonation');?>
                    </button>
                </div>
            </div>
        <?php
        }

        /**
         * Display Instruction on Thankyou page
         *
         * @param string $gateway
         * @return void
         */
         public function instruction( int $report_id, $report )
         {
            $gateway = $report->gateway;
            $payment_settings = lsdd_payment_active();
            $settings = $payment_settings[$gateway];
            $logo = esc_url($settings['logo']);
            $name = esc_attr($settings['name']);
            $group = esc_attr($settings['group_name']);

            if( strpos( $gateway, 'bank_') !== false ) :
                ?>
                <h6 class="lsdp-mb-10 lsdp-mt-15 font-weight-bold">
                    <?php echo esc_attr($group) . ' - ' . esc_attr($name); ?>
                </h6>
                <div class="brand-img lsdp-mb-15">
                    <img src="<?php echo esc_url($logo); ?>" alt="<?php echo esc_url($name); ?>" class="h-50">
                </div>
                
                <div class="lsdp-mb-5">
                    <!-- Swift -->
                    <?php if( ! empty( $settings['swift_code']) ) : ?>
                        <p class="mb-0 lsdp-font-13 pb-5"><?php _e('SWIFT Code', 'lsddonation');?></p>
                        <h6 class="title font-weight-medium lsdp-mb-10"><?php echo esc_attr($settings['swift_code']); ?></h6>
                    <?php endif; ?>

                    <!-- Account  -->
                    <p class="mb-0 lsdp-font-13 pb-0"><?php _e('Account Number ', 'lsddonation');?></p>
                    <div class="lsdp-row no-gutters">
                        <div class="col-auto">
                            <h6 class="title font-weight-medium">
                                <?php echo empty($settings['bank_code']) ? '' : '(' . intval($settings['bank_code']) . ')'; ?> 
                                <span id="account" class=""><?php echo esc_attr($settings['account_number']); ?></span>
                            </h6>
                        </div>
                        <div class="col-auto ml-auto">
                            <a onclick="lsddCopy('#account', this)" class="form-title text-dark-grey c-pointer">
                                <?php _e('Copy', 'lsddonation');?>
                                <svg xmlns="http://www.w3.org/2000/svg"  style="float:right;margin-left:10px;" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-copy">
                                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Owner -->
                    <div class="lsdp-mb-15 lsdp-mt-10 pb-0">
                        <p class="mb-0 lsdp-font-13"><?php _e('Account Owner ', 'lsddonation');?></p>
                        <h6 class="font-weight-medium mt-up-5">
                            <?php echo esc_attr($settings['account_owner']); ?>
                        </h6>
                    </div>
                </div>
                <?php

                // Instruction
                echo esc_attr($settings['instruction']);
            endif;
        }
        
        /**
         * Formatting Notification Text Template
         * for whatsapp 
         *
         * @return void
         */
        public function notification_text( object $report_id, string $event, string $gateway )
        {
            $payment_settings = lsdd_payment_active();
            $settings = $payment_settings[$gateway];
            $name = esc_attr($settings['name']);
            $group = esc_attr($settings['group_name']);

            // Fixing Notification Payment Whatsapp Empty @4.0.0
            $template =  '*' . $group . ' - ' . $name .'*'. PHP_EOL;
            if( isset($settings['swift_code']) && $settings['swift_code'] != ''){
                $template .= __( "SWIFT Code", 'lsddonation' ) . ' : ' . esc_attr($settings['swift_code']) . PHP_EOL;
            }
            $template .= __( "Account Number", 'lsddonation' ) . ' : ' . '(' . intval($settings['bank_code']) . ') ' . abs($settings['account_number']) . PHP_EOL;
            $template .= __( "Account Owner", 'lsddonation' ) . ' : ' . esc_attr($settings['account_owner']) . PHP_EOL . PHP_EOL;
            $template .= __( "Instruction", 'lsddonation' ) . PHP_EOL . esc_attr($settings['instruction'])  . PHP_EOL;
            return trim(preg_replace("/\t/", '', $template));
        }

        /**
         * Formatting Notification HTML Template
         * for email
         *
         * @return void
         */
        public function notification_html( object $report_id, string $event, string $gateway )
        {
            // Relative Setting for Custom Bank
            $swift = isset($settings['swift_code']) && $settings['swift_code'] != '' ? '<p style="margin:0;padding:0;">' . __( "SWIFT Code", 'lsddonation' ) . ' : ' . esc_attr($settings['swift_code']) . '</p>' : '';
         
            $template = '<tr>
                <td align="left" style="font-size:0px;padding:0 25px 10px;">
                    <div style="font-family:Helvetica Neue,Arial,sans-serif;font-size:14px;line-height:22px;text-align:left;color:#525252;">
                        <p style="margin:0;padding:0;"><strong>' . $this->group_name . ' - ' . $this->name . '</strong></p>' . 
                        $swift  .
                        '<p style="margin:0;padding:0;">' . __( "Account Number", 'lsddonation' ) . ' : ' . '(' . abs($settings['bank_code']) . ') <strong>' . abs($settings['account_number']) . '</strong></p>
                        <p style="margin:0;padding:0;">' . __( "Account Owner", 'lsddonation' ) . ' : <strong>' . esc_attr($settings['account_owner']) . '</strong></p>
                    </div>
                </td>
            </tr>
            <tr>
                <td align="left" style="font-size:0px;padding:0px 25px;">
                    <div
                        style="font-family:Helvetica Neue,Arial,sans-serif;font-size:14px;line-height:22px;text-align:left;color:#525252;">
                        <h6>'. __( 'Instruction', 'lsddonation' ) .'</h6>
                        '. esc_attr($settings['instruction']) .'
                    </div>
                </td>
            </tr>
            ';
            return trim(preg_replace("/\t/", '', $template));
        }
    }
    Payment::register("bank_transfer", new Bank_Transfer());

}
?>