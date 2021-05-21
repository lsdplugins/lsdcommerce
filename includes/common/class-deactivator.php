<?php
namespace LSDCommerce;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Deactivator
{
    public static function deactivate()
    {
        wp_clear_scheduled_hook('lsdcommerce_daily_update');
        
        delete_option( 'lsdcommerce_permalink_flush' );
    }

}
?>