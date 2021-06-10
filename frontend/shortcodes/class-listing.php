<?php
namespace LSDCommerce\Shortcodes;

if (!defined('ABSPATH')) {
    exit;
}

class Listing
{
    public function __construct()
    {
        add_shortcode('lsdcommerce_product_listing', [$this, 'render']);
    }

    public function render($atts)
    {
        // extract(shortcode_atts(array(
        //     'count' => false,
        //     'program_id' => false,
        // ), $atts));

        ob_start();

        $render = ob_get_clean();

        return $render;
    }
}
new Listing;
