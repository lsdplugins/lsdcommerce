<?php
namespace LSDCommerce;

if (!defined('ABSPATH')) {
    exit;
}

class Shipping
{
    public static $shippings = [];

    public static function register(string $id, Shipping_Template $service)
    {
        self::$shippings[$id] = $service;
    }

    public static function registered()
    {
        return self::$shippings;
    }
}


require_once LSDC_PATH . "includes/abstracts/abstract-shipping.php";
