<?php
namespace LSDCommerce;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Activator
{
    public static function activate()
    {
        // Set Order Read
        update_option('lsdcommerce_order_unread', 0);

        // // Sending Usage Data
        // Usages::init();

        // Auto Setup

        // Checking Daily Update
        if( ! wp_next_scheduled( 'lsdcommerce_daily_update' ) ) {
            wp_schedule_event( time(), 'daily', 'lsdcommerce_daily_update' );
        }

        // Automatic Flush Permalink
        add_option( 'lsdcommerce_permalink_flush', true );

        // Automatic Redirect Aftet Activate
        add_option( 'lsdcommerce_activator_redirect', true );
    }

    /**
     * [UNUSED][EXPERIMENTAL]
     * Get install time.
     *
     * Retrieve the time when LSDCommerce was installed.
     * @inspire from ELementor
     * 
     * @since 4.0.0
     * @access public
     * @static
     *
     * @return int Unix timestamp when LSDCommerce was installed.
     */
    public function get_install_time()
    {
        $installed_time = get_option('_' . $this->_slug . '_installed_time');

        if (!$installed_time) {
            $installed_time = time();

            update_option('_' . $this->_slug . '_installed_time', $installed_time);
        }

        return $installed_time;
    }
}
?>