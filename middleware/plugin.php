<?php
namespace LSDCommerce;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class Plugin
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
    public static function load()
    {
        $lsdc = new self();

        // Bind to Init
        add_action('plugins_loaded', [$lsdc, 'loaded']);
 
        // Load FrontEnd Class [Only for FrontEnd Needs]
        if (!is_admin()) {

        }

        // Load Admin Class [Only For Admin Needs]
        if (is_admin()) {

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
        // require_once LSDC_PATH . 'core/common/class-activator.php';
        // Activator::activate();
    }

    /**
     * Load Class Deactivator on Plugin Deactivate
     *
     * @return void
     * @since 1.0.3
     */
    public function uninstall()
    {
        // require_once LSDC_PATH . 'core/common/class-deactivator.php';
        // Deactivator::deactivate();
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
        _doing_it_wrong(__FUNCTION__, esc_html__('Something went wrong.', 'lsdcommerce'), LSDC_VERSION );
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
        _doing_it_wrong(__FUNCTION__, esc_html__('Something went wrong.', 'lsdcommerce'), LSDC_VERSION );
    }

    /**
     * Constructing Class
     */
    public function __construct()
    {
        self::$version = LSDC_VERSION ;
        self::$slug = 'lsdcommerce';
        self::$name = 'LSDCommerce';

        // Activation and Deactivation
        register_activation_hook(LSDC_BASE, [$this, 'activation']);
        register_deactivation_hook(LSDC_BASE, [$this, 'uninstall']);
    }
}
?>