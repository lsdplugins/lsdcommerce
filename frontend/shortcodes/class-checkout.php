<?php
namespace LSDCommerce\Shortcodes;

if (!defined('ABSPATH')) {
    exit;
}

class Checkout
{
    public function __construct()
    {
        add_shortcode('lsdcommerce_checkout', [$this, 'render']);
    }

    public function form(){}

    public function shipping(){}

    public function payment(){}

    public function render($atts)
    {
        // extract(shortcode_atts(array(
        //     'count' => false,
        //     'program_id' => false,
        // ), $atts));

        ob_start();

        do_action( 'lsdcommerce/checkout' );

        // Set Token 10 Minutes for Checkout
        $cart   = isset( $_COOKIE['_lsdcommerce_cart'] ) ? (array) json_decode( stripslashes(  $_COOKIE['_lsdcommerce_cart'] ) ) : null;
        $token  = isset( $_COOKIE['_lsdcommerce_token'] ) ? sanitize_text_field($_COOKIE['_lsdcommerce_token']) : null;
        ?>
        
        <div id="lsdcommerce-checkout" class="lsdc-content lsdc-bg-color max480">
            <main class="page-content">
                <input type="hidden" id="checkout-nonce" value="<?php echo wp_create_nonce( 'checkout-nonce' ); ?>" />
        
                <div id="checkout-alert" class="lsdp-alert lsdc-info  lsdp-hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-info"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                    <p>{{err}}</p>
                </div>
        
                <div class="lsdc-card">
                    <div class="card-body">
        
                    <?php //if( ! empty( $cart ) ) : ?>
        
                        <?php do_action( 'lsdcommerce_checkout_before_tab' ); ?>
        
                        <div class="section-tabs">
        
                            <!-- Navigation Checkout --> 
                            <div class="swiper-container swiper-tabs-nav">
                                <div class="swiper-wrapper">
                                    <div class="swiper-slide">
                                        <?php _e( 'Pembeli', 'lsdcommerce' ); ?>
                                    </div>
                                    <div class="swiper-slide">
                                        <?php _e( 'Pengiriman', 'lsdcommerce' ); ?>
                                    </div>
                                    <div class="swiper-slide">
                                        <?php _e( 'Pembayaran', 'lsdcommerce' ); ?>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Content Checkout -->
                            <div class="swiper-container swiper-tabs-content">
                                <div class="swiper-wrapper">
        
                                    <!-- Tab : Customer -->
                                    <div id="customer" class="swiper-slide">
                                        <form id="lsdcommerce-form" class="full-height">
                                            <div class="top">
                                                <!-- Before Form -->
                                                <?php do_action( 'lsdcommerce_checkout_form_before' ); ?>
        
                                                <!-- Greeting for User -->
                                                <?php if( is_user_logged_in() ) : ?>
                                                    <h6 class="text-primary font-weight-medium lsdp-mb-15"><?php _e( "Selamat Datang kembali", 'lsdcommerce') ?> 😊 <?php echo lsdc_get_user_name(); ?></h6>
                                                <?php endif; ?>
        
                                                <!-- Load Form -->
                                                <div class="checkout-form lsdp-pt-10">
                                                    <?php do_action( 'lsdcommerce_checkout_form' ); ?>
                                                </div>
                                            
                                                <!-- Login Instruction -->
                                                <?php if( ! is_user_logged_in() ) : ?>
        
                                                    <?php if( ! class_exists( 'LSDCommerce_PRO' ) ) : ?>
                                                        <a href="<?php echo wp_login_url(); ?>" class="text-primary swiper-no-swiping"><?php _e( 'Sudah punya akun ? Silahkan Masuk', 'lsdcommerce' ); ?></a>
                                                    <?php else: ?>
                                                        <a toggle="embed-login" toggle-hide="checkout-form" class="text-primary lsdp-toggle swiper-no-swiping"><?php _e( 'Sudah punya akun ? Silahkan Masuk', 'lsdcommerce' ); ?></a>
                                                    <?php endif; ?>
        
                                                <?php endif; ?>
        
                                                <!-- After Form -->
                                                <?php do_action( 'lsdcommerce_checkout_form_after'); ?>
                                            </div>
        
                                            <div class="bottom">
                                                <button class="lsdp-btn lsdc-btn btn-primary btn-block lsdcommerce-customer swiper-no-swiping">
                                                    <div class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"style="margin-top:-4px;" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shield"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg></div>
                                                    <?php _e( 'Lanjut', 'lsdcommerce' ); ?>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
        
                                    <!-- Tab : Shipping -->
                                    <div id="shipping" class="swiper-slide">
                                        <form id="lsdcommerce-shipping-options" class="full-height">
                                            <div class="top"></div>
                                            <?php do_action('lsdcommerce_checkout_shipping') ?>
        
                                            <div class="bottom py-10">
                                                <button class="lsdp-btn lsdc-btn btn-primary btn-block lsdcommerce-shipping swiper-no-swiping">
                                                    <div class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"style="margin-top:-4px;" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shield"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg></div>
                                                    <?php _e( 'Lanjut', 'lsdcommerce' ); ?>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
        
                                    <!-- Tab : Payment -->
                                    <div id="payment" class="swiper-slide">
                                        <form id="lsdcommerce-payment" class="full-height">
                                            <div class="top">
                                                <!-- Summary Section -->
                                                <?php do_action( 'lsdcommerce_checkout_summary_before' ); ?>
        
                                                    <h6 class="text-primary font-weight-medium lsdp-mb-0 lsdc-toggle-collapse swiper-no-swiping noselect" lsdc-toggle="collapse" data-target="#summary" aria-expanded="false">
                                                        <?php _e( "Rangkuman Pesanan", 'lsdcommerce' ); ?>
                                                    </h6>
                                                    
                                                    <div id="summary" class="lsdc-collapse">
                                                        <div id="checkout-products" class="noselect"></div>
                                                    </div>
        
                                                    <table class="table table-borderless lsdp-mb-10 lsdp-mt-0">
                                                        <tbody>
                                                            <tr>
                                                                <td><?php _e( 'Total', 'lsdcommerce' ); ?></td>
                                                                <td class="text-right">
                                                                    <span id="grandtotal" class="lsdc-color-theme font-weight-medium"><?php _e( 'Checkout Error', 'lsdcommerce' ); ?></span>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
        
                                                <?php do_action( 'lsdcommerce_checkout_summary_after' ); ?>
        
                                                <!-- Payment List Section -->
                                                <?php do_action( 'lsdcommerce_checkout_payment_before' ); ?>
        
                                                    <h6 class="text-primary font-weight-medium lsdp-mb-10 swiper-no-swiping"><?php _e( 'Metode Pembayaran', 'lsdcommerce' ); ?></h6>
        
                                                    <?php do_action( 'lsdcommerce_checkout_payment' ); ?>
        
                                                <?php do_action( 'lsdcommerce_checkout_payment_after' ); ?>
        
                                                <!-- Agree Terms -->
                                                <div class="lsdp-mb-15 mt-3">
                                                    <div class="form-group">
                                                        <div class="checkbox">
                                                            <input type="checkbox" id="agreeterms" checked>
                                                            <label for="agreeterms">
                                                                <span><?php _e( 'Saya menyetujui', 'lsdcommerce' ); ?> <a href="<?php echo get_the_permalink( lsdc_get_settings( 'general_settings', 'terms_conditions' )); ?>" target="_blank">
                                                                <?php _e( 'Syarat dan Ketentuan', 'lsdcommerce'); ?></a></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
        
                                            <div class="bottom">
                                                <button class="lsdp-btn lsdc-btn btn-primary btn-block lsdcommerce-create-order swiper-no-swiping">
                                                    <div class="icon">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" style="margin-top:-4px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-credit-card"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                                                    </div>
                                                    <?php _e( 'Selesaikan Pembayaran', 'lsdcommerce' ); ?>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
        
                        <!-- Hookable : Powered Text, Script Template -->
                        <?php do_action( 'lsdcommerce_checkout_after_tab') ?>
        
                    <?php // else : ?>      
                    
                        <!-- <div class="lsdp-alert lsdc-info lsdp-mt-10 lsdp-mb-10 lsdp-mx-10">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-info"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                            <p><?php _e( 'Keranjang anda kosong', 'lsdcommerce' ); ?></p>
                        </div>
         -->
                    <?php // endif; ?>
                        
                    </div>
        
                </div>
        
            </main>
        
            <script id="checkout-summary-template" type="x-template">
                <table class="table table-borderless">
                    <tbody>
                        {{#items}}
                        <tr data-id="{{id}}">
                            <td class="product-thumbnail">
                                <div class="img-product">
                                <img src="{{thumbnail}}" alt="{{title}}">
                                </div>
                            </td>
                            <td class="product-item-detail">{{title}}<small class="d-block">{{qty}} x {{unit_price}}</small></td>
                            <td class="text-right">{{price}}</td>
                        </tr>
                        {{/items}}
                    </tbody>
                </table>
            </script>
        </div>

        <?php 

        $render = ob_get_clean();

        return $render;
    }
}
new Checkout;
