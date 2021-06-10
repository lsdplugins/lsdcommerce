<?php
namespace LSDCommerce;

// use LSDCommerce\Payments;

if (!defined('ABSPATH')) {
    exit;
}

class Frontend
{
    /**
     * The current version of the plugin
     *
     * @since 1.0.0
     * @access protected
     * @var string $version the current version of the plugin.
     */
    protected $version;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $slug    The string used to uniquely identify this plugin.
     */
    protected $slug;

    /**
     * The Name of Plugin
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $name    The string used to uniquely identify this plugin.
     */
    protected $name;

    /**
     * Register the admin page class with all the appropriate WordPress hooks.
     *
     * @param array $plugin
     */
    public static function register( array $plugin )
    {
        $frontend = new self($plugin['slug'], $plugin['name'], $plugin['version']);

        add_action('wp_enqueue_scripts', [$frontend, 'enqueue_styles']);
        add_action('wp_enqueue_scripts', [$frontend, 'enqueue_scripts']);
        add_action('wp_head', [$frontend, 'header']);


        // add_filter('lsdcommerce/payment/extras', [$frontend,'set_unique_code']);

    }

    /**
     * Constructor function.
     *
     * @param object $parent Parent object.
     */
    public function __construct( $slug, $name, $version )
    {
        $this->slug = $slug;
        $this->name = $name;
        $this->version = $version;
        $this->register_ajax();
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        // Loading Base CSS
        wp_enqueue_style('lsdplugins-core', plugins_url('/frontend/assets/lib/lsdplugins/lsdplugins.css', LSDC_BASE), array(), '1.0.1', 'all');

        // Load Theme CSS
        wp_register_style('lsdc-single', plugins_url('/frontend/assets/css/single.css', LSDC_BASE), array(), $this->version, 'all');
        wp_register_style('lsdc-theme', plugins_url('/frontend/assets/css/theme.css', LSDC_BASE), array(), $this->version, 'all');
        wp_register_style('lsdc-member', plugins_url('/frontend/assets/css/member.css', LSDC_BASE), array(), $this->version, 'all');
        wp_register_style('lsdc-responsive', plugins_url('/frontend/assets/css/responsive.css', LSDC_BASE), array(), $this->version, 'all');

        // wp_register_style( 'lsdc-tab-swiper', plugins_url( '/assets/frontend/css/tab-swiper.css', LSDC_BASE ), array(), $this->version, 'all' );
        // wp_register_style( 'swiper', LSDC_URL . 'assets/lib/swiper/swiper.css', array(), '5.3.6', 'all' );

        // $apperance = get_option('lsdc_appearance_settings');
        // wp_enqueue_style('lsdc-google-fonts', '//fonts.googleapis.com/css?family=' . esc_attr((empty($apperance['lsdc_fontlist'])) ? 'Poppins' : $apperance['lsdc_fontlist']), array(), $this->version);
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        // $url_parts = parse_url( get_site_url() );

        // if ( $url_parts && isset( $url_parts['host'] ) ) {
        //     $domain =  $url_parts['host'];
        // }

        // wp_enqueue_script('lsdc-helper', plugins_url('/assets/frontend/js/helper.js', LSDC_BASE), array('jquery'), $this->version, false);
        // wp_register_script('lsdc-payment', plugins_url('/assets/frontend/js/payment.js', LSDC_BASE), array('jquery'), $this->version, false);
        // wp_register_script('lsdc-navigo', plugins_url('/assets/lib/navigo/navigo.min.js', LSDC_BASE), array(), '8.11.0', false);

        // wp_enqueue_script($this->slug, plugins_url('/assets/frontend/js/public.js', LSDC_BASE), array('jquery'), $this->version, false);
        // wp_localize_script($this->slug, 'lsdc_public', array(
        //     'plugin_url' => LSDC_URL,
        //     'ajax_wp' => admin_url('admin-ajax.php'),
        //     'ajax_url' => LSDC_URL . 'core/utils/lsdc-ajax.php',
        //     'ajax_nonce' => wp_create_nonce('lsdc-ajax-nonce'),
        //     'rest_url' => get_rest_url(),
        //     'domain_url' => isset($domain) ? $domain : get_site_url(),
        //     'payment_url' => get_permalink(lsdc_get_settings( 'general_settings', 'payment_page' ) ),
        //     'payment_default' => lsdc_payment_default(),
        //     'options' => array(
        //         'popup' => lsdc_get_switch_option('popup_notification')
        //     ),
        //     'translation' => array(
        //         'cart_empty' => __('Cart Empty', 'lsdcommerce'),
        //         'select_method' => __('Please select a payment method', 'lsdcommerce'),
        //         'agree_terms' => __('You must agree Terms and Conditions', 'lsdcommerce'),
        //         'form_error' => __('Please fill a form correctly', 'lsdcommerce'),
        //         'minimum_error' => __('Minimum Donation', 'lsdcommerce'),
        //         'pay_error' => __("Failed to Processing Payment", 'lsdcommerce'),
        //         'popup_was_donate' => __("has contributed an amount", 'lsdcommerce'),
        //         'on' => __("on", 'lsdcommerce'),
        //         'program' => __("Program", 'lsdcommerce'),
        //     ),
        //     'currency' => array(
        //         'symbol' => lsdc_currency_display(),
        //         'format' => lsdc_currency_display('format'),
        //         'currency' => lsdc_get_currency(),
        //     ),
        // ));
        
        // // Popup Notification
        // if (lsdc_get_switch_option('popup_notification')) {
        //     wp_enqueue_script('lsdc-popup', plugins_url('/assets/frontend/js/popup.js', LSDC_BASE), array('jquery'), $this->version, false);
        // }

        // wp_register_script( 'swiper', plugins_url( '/assets/lib/swiper/swiper.js', LSDC_BASE ), array( 'jquery' ), '5.3.6', false );
        // wp_register_script( 'lsdc-swiper', plugins_url( '/assets/frontend/js/main.js', LSDC_BASE ), array( 'jquery' ), $this->version, false );
    }

    /**
     * Setting Unique Code
     *
     * @param array $extras
     * @return void
     */
    function set_unique_code($extras)
    {
        // //Getting ID from Cart
        // if (isset($_COOKIE['_lsdc_cart'])) {
        //     $carts = (array) json_decode(stripslashes($_COOKIE['_lsdc_cart']));
        //     if ($carts) {
        //         $program_id = array_keys($carts)[0];
        //     }
        // }

        // $program_id = isset( $program_id ) ? $program_id : null;

        // $settings = get_option('lsdc_appearance_settings');
        // $option = isset($settings['lsdc_unique_code']) ? esc_attr($settings['lsdc_unique_code']) : 'off';
        // $minus = isset($settings['lsdc_unique_code_minus']) ? esc_attr($settings['lsdc_unique_code_minus']) : 'off';

        // // Zakat Exception
        // if(get_post_type( $program_id ) != 'lsdc-zakat' ){
        //     if ($option != 'off') {
        //         $unique = array(
        //             array(
        //                 'title' => 'Unique Code',
        //                 'price' => lsdc_generate_uniquecode(),
        //                 'operation' => $minus == 'on' ? '-' : '+',
        //             ),
        //         );
    
        //         $extras = is_array($extras) ? $extras : [];
        //         $extras = array_merge($unique, $extras);
        //     }
        // }
      
        // return $extras;
    }



    /**
     * Load ROot CSS for Theming
     *
     * @return void
     */
    public function header()
    {
        // Appearance Settings
        $appearance = get_option('lsdc_appearance_settings', true);

        $font = !isset($appearance['lsdc_theme_font']) || $appearance['lsdc_theme_font'] == null ? 'Poppins' : $appearance['lsdc_theme_font'];
        $background = !isset($appearance['lsdc_theme_bg']) || $appearance['lsdc_theme_bg'] == null ? 'transparent' : $appearance['lsdc_theme_bg'];
        $theme = !isset($appearance['lsdc_theme_color']) || $appearance['lsdc_theme_color'] == null ? '#fe5301' : $appearance['lsdc_theme_color'];

        $lighter = lsdc_adjust_brightness($theme, 50);
        $darker = lsdc_adjust_brightness($theme, -40);

        echo lsdc_minify_css('<style id="lsdcommerce-pre-css" type="text/css">
                :root {
                    --lsdc-color: ' . $theme . ';
                    --lsdc-lighter-color: ' . $lighter . ';
                    --lsdc-darker-color: ' . $darker . ';
                    --lsdc-bg-color: ' . $background . ';
                }

                #lsdcommerce,
                #lsdc-payment{
                    background: ' . $background . ' !important;
                }

                .lsdc-content,
                .lsdc-content h1,
                .lsdc-content h2,
                .lsdc-content h4,
                .lsdc-content h5,
                .lsdc-content h6,
                .lsdc-container h3,
                .lsdc-container h4,
                .lsdc-container h5,
                .lsdc-container h6,
                .lsdc-font,
                .lsdc-btn{
                    font-family: -apple-system, BlinkMacSystemFont, "' . $font . '", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
                }

                .lsdc-theme-color{
                    color: ' . $theme . ' !important;
                }

                .lsdc-outline{
                    border: 1px solid ' . $theme . ' !important;
                    background: transparent  !important;
                    color: ' . $theme . ' !important;
                }

                .lsdc-primary{
                    background: ' . $theme . ' !important;
                }

                .lsdc-bg-color{
                    background: ' . $background . ' !important;
                }
            </style>');
    }

    /**
     * Registering Frontend AJAX
     *
     * @return void
     */
    public function register_ajax()
    {
        require_once 'class-ajax.php';
    }
}
?>