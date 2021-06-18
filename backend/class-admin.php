<?php
namespace LSDCommerce;

if (!defined('ABSPATH')) {
    exit;
}

class Admin
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
     * @param Options $options
     */
    public static function register(array $plugin)
    {
        $admin = new self($plugin['slug'], $plugin['name'], $plugin['version']);

        add_action('admin_menu', [$admin, 'register_admin_menu']);
        add_action('admin_enqueue_scripts', [$admin, 'enqueue_styles']);
        add_action('admin_enqueue_scripts', [$admin, 'enqueue_scripts']);
        add_action('admin_init', [$admin, 'admin_init']);
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

                
        // Load Required File
        require_once LSDC_PATH . 'backend/admin/tabs.php';
        require_once LSDC_PATH . 'backend/class-ajax.php';
        // require_once LSDC_PATH . 'backend/admin/class-autosetup.php';
        // require_once LSDC_PATH . 'backend/admin/class-dashboard.php';
        // require_once LSDC_PATH . 'backend/admin/class-updater.php';
    }

    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone()
    {
        _doing_it_wrong(__FUNCTION__, esc_html(__('Cloning of is forbidden')), LSDC_VERSION );
    } // End __clone ()

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup()
    {
        _doing_it_wrong(__FUNCTION__, esc_html(__('Unserializing instances of is forbidden')), LSDC_VERSION );
    } // End __wakeup ()

    /**
     * Initiatie Admin
     *
     * @return void
     */
    public function admin_init()
    {
        // Redirect to License after activate plugin
        if( get_option('lsdcommerce_activator_redirect') ){
            delete_option('lsdcommerce_activator_redirect');
            exit( wp_redirect( admin_url( 'admin.php?page=lsdcommerce' ) ) );
        }

        // Handle Ignoring Email Failure Notice
        if (isset($_GET['mail-failed-ignored'])) {
            update_option('lsdcommerce_mail_error', false);
            header("Refresh:0; url=" . get_admin_url());
        }
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        // $dev_css = WP_DEBUG == true ? '.css' : '-min.css';
        $dev_css = '.css';

        if (isset($_GET['page'])) {
            if ($_GET['page'] == 'lsdcommerce' || strpos($_GET['page'], 'lsdc-') !== false) {
                // wp_enqueue_style('select2', LSDC_URL . 'assets/lib/select2/select2.min.css', array(), '4.1.0', 'all');

                wp_enqueue_style('spectre-exp', LSDC_URL . 'backend/assets/lib/spectre/spectre-exp.min.css', array(), '0.5.8', 'all');
                wp_enqueue_style('spectre-icons', LSDC_URL . 'backend/assets/lib/spectre/spectre-icons.min.css', array(), '0.5.8', 'all');
                wp_enqueue_style('spectre', LSDC_URL . 'backend/assets/lib/spectre/spectre.min.css', array(), '0.5.8', 'all');

                wp_enqueue_style( $this->slug, LSDC_URL . 'backend/assets/css/admin-settings' . $dev_css, array(), $this->version, 'all');
                wp_enqueue_style('wp-color-picker');
            }
        }

        if( strpos(get_post_type( get_the_ID() ), 'lsdc-') !== false ){
            wp_enqueue_style( $this->slug . '-product', LSDC_URL . 'backend/assets/css/admin-product' . $dev_css, array(), $this->version, 'all');
        }

        // Global Admin Styles
        wp_enqueue_style($this->slug . '-global', LSDC_URL . 'backend/assets/css/admin-global' . $dev_css, array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        // $dev_js = WP_DEBUG == true ? '.js' : '-min.js';
        $dev_js = '.js';

        // Load Lib Admin Restrict only LSDCommerce Page
        if (isset($_GET['page']) && $_GET['page'] == 'lsdcommerce' || strpos(get_post_type( get_the_ID() ), 'lsdc-') !== false || isset($_GET['page']) && strpos($_GET['page'], 'lsdc-') !== false) {
            // Load Admin Js
            wp_enqueue_script($this->slug, LSDC_URL . 'backend/assets/js/admin' . $dev_js, array('jquery', 'wp-color-picker'), $this->version, false);
            wp_localize_script($this->slug, 'lsdc_admin', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'ajax_nonce' => wp_create_nonce('lsdc_admin_nonce'),
                'plugin_url' => LSDC_URL,
                'currency' => lsdc_get_currency(),
                'translation' => $this->js_translation(),
            ));

            // Enquene Media For Administrator Only
            if (current_user_can('manage_options')) {
                wp_enqueue_media();
            }
        }
    }

    /**
     * Javascript Translation Stack
     *
     * @return array
     */
    public function js_translation()
    {
        return array(
            'delete_report' => __('Are you sure you want to delete this item ?', 'lsdcommerce'),
        );
    }

    /**
     * Register Menu in Admin Area
     *
     * LSDCommerce Settings
     * Products
     * Orders
     *
     * @since 1.0.0
     * @return void
     */
    public function register_admin_menu()
    {
        // Menu LSDCommerce in WP-ADMIN
        add_menu_page(
            $this->name,
            $this->name,
            'manage_options',
            $this->slug,
            [$this, 'admin_menu_callback'],
            LSDC_URL . 'backend/assets/images/lsdcommerce.png',
            45
        );

        $awaiting_mod = (get_option('lsdcommerce_order_unread') > 0) ? abs(get_option('lsdcommerce_order_unread')) : 0;

        // Menu Products
        add_menu_page(
            __('Produk', 'lsdcommerce'),
            __('Produk', 'lsdcommerce'),
            'manage_options',
            'edit.php?post_type=product',
            '',
            LSDC_URL . 'backend/assets/svg/product.svg',
            50
        );
  

        // Menu Orders
        add_menu_page(
            __('Pesanan', 'lsdcommerce'),
            $awaiting_mod ? sprintf((__('Pesanan', 'lsdcommerce') . ' <span class="awaiting-mod">%d</span>'), $awaiting_mod) : __('Pesanan', 'lsdcommerce'),
            'manage_options',
            'edit.php?post_type=lsdcommerce_order',
            '',
            LSDC_URL . 'backend/assets/svg/order.svg',
            50
        );

        // Submenu Product -> Categories
        add_submenu_page(
            'edit.php?post_type=product', 
            __('Kategori', 'lsdcommerce') , 
            __('Kategori', 'lsdcommerce') , 
            'manage_options', 
            'edit-tags.php?taxonomy=product-category&post_type=product', 
            ''
        );

        // Add Shortcode List to wp-admin > LSDCommerce > Appearence
        require_once LSDC_PATH . 'backend/admin/class-shortcode-lists.php';
        Admin\Shortcode_Lists::addShortcodeList( $this->slug, $this->name, array(
            ['shortcode' => '[lsdcommerce_products]', 'description' => __("Menampilkan Produk", 'lsdcommerce')],
            ['shortcode' => '[lsdcommerce_checkout]', 'description' => __("Menampilkan Pembayaran", 'lsdcommerce')],
        ));

        // Add Switch Options to wp-admin > LSDCommerce > Appearence
        require_once LSDC_PATH . 'backend/admin/class-switch-options.php';
        Admin\Switch_Options::addOptions( $this->slug, $this->name, array(
            'lsdc_unique_code' => ['name' => __('Kode Unik', 'lsdcommerce'), 'desc' => __('Matikan/Hidupkan Kode Unik', 'lsdcommerce'), 'override' => false],
        ));
        
    }

    /**
     * Including settings LSDCommerce page
     * when clikcing menu LSDDOnation
     *
     * @return void
     */
    public function admin_menu_callback()
    {
        include_once LSDC_PATH . 'backend/admin/tabs/common.php';
    }

    /**
     * Including Orders File
     * When Clicking Menu Orders
     *
     * @return void
     */
    public function admin_menu_orders()
    {
        include_once LSDC_PATH . 'core/admin/orders/orders.php';
    }
}
?>