<?php 
/**
 * Formatting Currency
 * from float to string with currency symbol //10000-> Rp 10.000
 *
 * @param boolean $symbol
 * @param float $nominal 
 * @param string $currency
 * @param boolean $correction
 * @return string
 */
//TODO :: Unit Testing Currency Format
function lsdc_currency_format( bool $symbol = true, float $nominal, string $currency = "", bool $correction = false)
{
    $currency_selected = empty($currency) ? lsdc_get_currency() : $currency;

    // Currency Memmory [ Digit ]
    $list['IDR'] = array(0, ',', '.', 'Rp ');
    $list['USD'] = array(2, '.', ',', '$');
    $list['MYR'] = array(2, '.', ',', 'RM ');
    $list['SGD'] = array(2, '.', ',', 'S$');

    $twodigit = $list[$currency_selected][0];

    // Default Remove Correction
    if (!$correction) {
        $twodigit = 0;
    }

    if ($symbol == false) {
        return number_format(floatval($nominal), $twodigit, $list[$currency_selected][1], $list[$currency_selected][2]); 
    } else {
        return $list[$currency_selected][3] . number_format(floatval($nominal), $twodigit, $list[$currency_selected][1], $list[$currency_selected][2]);
    }
}

/**
 * Clean Up Currency
 * from string with symbol to int // Rp 10.000 -> 10000
 *
 * @param string $formatted_currency
 * @return void
 */
//TODO :: Unit Testing Currency Cleanup
function lsdc_currency_cleanup( string $formatted_currency )
{
    $formatted_currency = preg_replace('/[^0-9]/', '', $formatted_currency);
    $formatted_currency = preg_replace('/\,/', '', $formatted_currency);
    return abs(preg_replace('/\./', '', $formatted_currency));
}

/**
 * Get Currency based on Settings
 * default IDR 
 *
 * @return void
 */
//TODO :: Unit Testing Get Currency
function lsdc_get_currency()
{
    $settings = get_option('lsdcommerce_general_settings');
    // return strtoupper(isset($settings['currency']) ? esc_attr($settings['currency']) : 'IDR'); // Disabled Option
    return 'IDR';
}

/**
 * Display currency based on options symbol, minimum, or format
 *
 * @param string $type
 * @param integer $nominal
 * @return string
 */
//TODO :: Unit Testing Currency Display
function lsdc_currency_display( string $type = 'symbol', int $nominal = 10000 )
{
    $currency_lists = array(
        'IDR' => array(
            'symbol' => 'Rp ',
            'minimum' => 10000,
            'format' => lsdc_currency_format(false, $nominal),
        ),
        'USD' => array(
            'symbol' => '$',
            'minimum' => 1,
            'format' => lsdc_currency_format(false, $nominal),
        ),
        'MYR' => array(
            'symbol' => 'RM ',
            'minimum' => 1,
            'format' => lsdc_currency_format(false, $nominal),
        ),
        'SGD' => array(
            'symbol' => 'S$ ',
            'minimum' => 1,
            'format' => lsdc_currency_format(false, $nominal),
        ),
    );

    $output = null;
    
    if( isset($currency_lists[lsdc_get_currency()][$type] ) ){
      $output = $currency_lists[lsdc_get_currency()][$type];
    }
    // Else :: Create WP ERROR Handler
    
    return $output;
}
?>
