<?php
/**
 * LOG Class can instance in any conditions
 */
namespace LSDCommerce;

final class LOG
{
    private static $instance;

    private static function get_instance()
    {
        if (!self::$instance) {
            self::$instance = new LOG();
        }
        return self::$instance;
    }

    private function write_to_file($message, $slug)
    {
        file_put_contents(LSDC_STORAGE . '/' . $slug . '.log', "$message\n", FILE_APPEND);
    }

    public static function INFO($message, $slug = 'lsdcommerce')
    {
        if (is_array($message)) {
            $message = json_encode($message);
        }

        $date = lsdc_current_date();
        $severity = "[INFO]";
        $message = "$date $severity :: $message";
        self::get_instance()->write_to_file($message, $slug);
    }

    public static function WARNING($message, $slug = 'lsdcommerce')
    {
        if (is_array($message)) {
            $message = json_encode($message);
        }

        $date = lsdc_current_date();
        $severity = "[WARNING]";
        $message = "$date $severity :: $message";
        self::get_instance()->write_to_file($message, $slug);
    }

    public static function ERROR($message, $slug = 'lsdcommerce')
    {
        if (is_array($message)) {
            $message = json_encode($message);
        }

        $date = lsdc_current_date();
        $severity = "[ERROR]";
        $message = "$date $severity :: $message";
        self::get_instance()->write_to_file($message, $slug);
    }
}