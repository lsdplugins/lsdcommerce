<?php
namespace LSDCommerce\Common;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class i18n
{
    public static $countries = array(
        array(
            'iso2' => "ID",
            'iso3' => "IDN",
            'phone' => "+62",
            'name' => "Indonesia",
            'currency' => "IDR",
            'currency_format' => "IDR - Rupiah ( Rp 100.000 )",
        ),
        // array(
        //     'iso2' => "MY",
        //     'iso3' => "MYS",
        //     'phone' => "+60",
        //     'name' => "Malaysia",
        //     'currency' => "MYR",
        //     'currency_format' => "MYR - Ringgit ( 100 RM )",
        // ),
        // array(
        //     'iso2' => "SG",
        //     'iso3' => "SGP",
        //     'phone' => "+65",
        //     'name' => "Singapore",
        //     'currency' => "SGD",
        //     'currency_format' => "SGD - Singapore Dollar ( S$ 10 )",
        // ),
        // array(
        //     'iso2' => "US",
        //     'iso3' => "USA",
        //     'phone' => "+1",
        //     'name' => "United States",
        //     'currency' => "USD",
        //     'currency_format' => "USD - Dollar ( $15 )",
        // ),
    );

    public static function get_countries()
    {
        return self::$countries;
    }
}
?>