<?php
namespace LSDCommerce;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class for Registrating Notification
 */
class Notification
{
    public static $notifications = [];

    public static function register(string $id, Notification_Template $channel)
    {
        self::$notifications[$id] = $channel;
    }

    public static function registered()
    {
        return array_reverse(self::$notifications);
    }
}

require_once LSDC_PATH . "includes/abstracts/abstract-notification.php";
