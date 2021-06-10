<?php
namespace LSDCommerce;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

final class Plugin
{
    /**
     * The name of this plugin
     *
     * @var string
     */
    protected static $name;

    /**
     * The unique identifier of this plugin
     *
     * @var string
     */
    protected static $slug;

    /**
     * The currenct version of this plugin
     *
     * @var string
     */
    protected static $version;
    /**
     * Loads the plugin into WordPress.
     */

    /**
     * Singleton Load
     *
     * @return void
     */
    public static function load()
    {
        $lsdcommerce = new self();
        $plugin = array('slug' => 'lsdcommerce', 'name' => 'LSDCommerce', 'version' => LSDC_VERSION);

        // Activation and Deactivation
        register_activation_hook(LSDC_BASE, [$lsdcommerce, 'activation']);
        register_deactivation_hook(LSDC_BASE, [$lsdcommerce, 'uninstall']);

        // Bind to Init
        add_action('plugins_loaded', [$lsdcommerce, 'loaded']);

        /*************** GLOBAL /****************/
        // TODO :: Cleanup Helper Function
        // require_once LSDC_PATH . 'core/modules/utils/class-logger.php';
        require_once LSDC_PATH . 'includes/common/class-i18n.php';

        // TODO :: Server Timing for Performance
        require_once LSDC_PATH . 'includes/helpers/currency.php';
        require_once LSDC_PATH . 'includes/helpers/price.php';
        require_once LSDC_PATH . 'includes/helpers/payment.php';
        require_once LSDC_PATH . 'includes/helpers/getter.php';
        require_once LSDC_PATH . 'includes/helpers/setter.php';
        require_once LSDC_PATH . 'includes/helpers/helper.php';

        // Register Post Type
        require_once LSDC_PATH . 'includes/common/class-posttype-product.php';
        // require_once LSDC_PATH . 'includes/common/class-posttype-order.php';
        // require_once LSDC_PATH . 'includes/common/class-usages.php';

        // Load FrontEnd Class [Only for FrontEnd Needs]
        if (is_admin()) {
            require_once LSDC_PATH . 'backend/admin/class-admin.php';
            Admin::register($plugin);
        }

        // Registrar Module
        require_once LSDC_PATH . 'includes/registrar/class-registrar-notification.php';
        require_once LSDC_PATH . 'includes/registrar/class-registrar-payment.php';
        require_once LSDC_PATH . 'includes/registrar/class-registrar-shipping.php';

        // Module Payments
        require_once LSDC_PATH . 'backend/modules/payments/class-payment-static-qr.php';
        require_once LSDC_PATH . 'backend/modules/payments/class-payment-transfer-bank.php';
        // require_once LSDC_PATH . 'backend/modules/payments/class-payment-shopee-md.php';

        // Module Notification
        require_once LSDC_PATH . 'backend/modules/notifications/class-notification-webhook.php';
        require_once LSDC_PATH . 'backend/modules/notifications/class-notification-whatsapp.php';
        require_once LSDC_PATH . 'backend/modules/notifications/class-notification-email.php';

        // Module Shipping
        require_once LSDC_PATH . 'backend/modules/shipping/class-shipping-email.php';
        require_once LSDC_PATH . 'backend/modules/shipping/class-shipping-rajaongkir-starter.php';
        // require_once LSDC_PATH . 'backend/modules/shipping/class-shipping-cod.php'; // bayar dirumah
        // require_once LSDC_PATH . 'backend/modules/shipping/class-shipping-pickup.php'; // ambil ketempat

        // Load Notification Services
        // require_once LSDC_PATH . '/core/services/scheduler/scheduler.php';

        // Load Global Class
        // require_once LSDC_PATH . '/includes/wp.php';
        // Wordpress::register();

        if (!is_admin()) {
            // Load FrontEnd Class [Only for FrontEnd Needs]
            require_once LSDC_PATH . 'frontend/class-frontend.php';
            Frontend::register($plugin);

            require_once LSDC_PATH . 'frontend/modules/member/tab-functions.php';

            // Shortcodes
            require_once LSDC_PATH . 'frontend/shortcodes/class-checkout.php';
            require_once LSDC_PATH . 'frontend/shortcodes/class-confirmation.php';
            require_once LSDC_PATH . 'frontend/shortcodes/class-listing.php';
            require_once LSDC_PATH . 'frontend/shortcodes/class-member.php';
            require_once LSDC_PATH . 'frontend/shortcodes/class-storefront.php';
        }

    }

    public function loaded()
    {
        load_plugin_textdomain('lsdcommerce', false, LSDC_PATH . '/languages/');
    }

    /**
     * Load Class Activator on Plugin Active
     *
     * @return void
     * @since 1.0.3
     */
    public function activation()
    {
        require_once LSDC_PATH . 'includes/common/class-activator.php';
        Activator::activate();
    }

    /**
     * Load Class Deactivator on Plugin Deactivate
     *
     * @return void
     * @since 1.0.3
     */
    public function uninstall()
    {
        require_once LSDC_PATH . 'includes/common/class-deactivator.php';
        Deactivator::deactivate();
    }

    /**
     * Clone.
     *
     * Disable class cloning and throw an error on object clone.
     *
     * The whole idea of the singleton design pattern is that there is a single
     * object. Therefore, we don't want the object to be cloned.
     *
     * @access public
     * @since 1.0.0
     */
    public function __clone()
    {
        // Cloning instances of the class is forbidden.
        _doing_it_wrong(__FUNCTION__, esc_html__('Something went wrong.', 'lsdcommerce'), LSDC_VERSION);
    }

    /**
     * Wakeup.
     *
     * Disable unserializing of the class.
     *
     * @access public
     * @since 1.0.0
     */
    public function __wakeup()
    {
        // Unserializing instances of the class is forbidden.
        _doing_it_wrong(__FUNCTION__, esc_html__('Something went wrong.', 'lsdcommerce'), LSDC_VERSION);
    }
}

Plugin::load();