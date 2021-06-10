<?php
namespace LSDCommerce;

if (!defined('ABSPATH')) {
    exit;
}

class Payment
{
    public static $payments = [];

    public static function register(string $id, Payment_Template $method)
    {
        self::$payments[$id] = $method;
    }

    public static function registered()
    {
        return self::$payments;
    }
}

require_once LSDC_PATH . "includes/abstracts/abstract-payment.php";
