<?php
/************************************/
/* Cart Helper
/***********************************/

/**
 * Getting Cart Value based on Key
 *
 * @param string $key
 * @return void
 */
function lsdc_payment_cart(string $key)
{
    if (isset($_COOKIE['_lsdc_cart'])) {
        $carts = (array) json_decode(stripslashes($_COOKIE['_lsdc_cart']));
        if ($carts) {
            switch ($key) {
                case 'program_id':
                    return array_keys($carts)[0];
                    break;
                case 'nominal':
                    return $carts[$program_id]->nominal;
                    break;
            }
        }
    } else {
        return false;
    }
}

/**
 * Return Payment LInk
 *
 * @return void
 */
function lsdc_payment_url()
{
    return get_the_permalink(lsdc_get_settings('general_settings', 'payment_page'));
}

function lsdc_payment_key($report_id)
{
    return 'instruction/' . bin2hex("lsdc#" . $report_id);
}


/**
 * Get Payment Sorted Data
 *
 * @return void
 */
function lsdcommerce_payment_sorted()
{
    $payment_sorted = get_option('lsdcommerce_payment_sorted') != null ? get_option('lsdcommerce_payment_sorted') : array(); 

    $sorted = array_values($payment_sorted);
    $payment_settings = lsdc_payment_settings();

    if( empty($payment_sorted) ){
        return isset($payment_settings) ? array_keys($payment_settings) : array();
    }else{
        $settings = array_keys($payment_settings);
        $rest = array_merge(array_diff($sorted, $settings), array_diff($settings, $sorted));
        $payment_sorted = array_merge( $payment_sorted ,$rest );
    
        return $payment_sorted;
    }
}

/**
 * Get Payment Settings Data
 *
 * @return void
 */
function lsdc_payment_settings()
{
    return get_option('lsdcommerce_payment_settings') != null ? get_option('lsdcommerce_payment_settings') : array(); 
}

/**
 * Get Payment on Top Sort and Status On
 * Convert to Hex value
 *
 * @return void
 */
function lsdc_payment_default()
{
    $sorted = lsdc_payment_active();
    reset($sorted);
    return bin2hex(key($sorted));
}

/**
 * Display Active Payment Method
 * Sorted and Status On
 *
 * @return void
 */
function lsdc_payment_active()
{
    $payment_sorted = lsdcommerce_payment_sorted();
    $payment_status = get_option('lsdcommerce_payment_status') != null ? get_option('lsdcommerce_payment_status') : $payment_sorted;
    $payment_settings = lsdc_payment_settings();

    $sorted_active = array();

    if( $payment_sorted && $payment_status ){
        foreach ($payment_sorted as $payment_id) { // Sorted Payment { 0 : bank_transfer, 1 : qris_static }
            // Checking Payment Available 
            if(!isset($payment_status[$payment_id])){
                continue;
            }
            
            // Checking Class Payment Exist
            if(!class_exists($payment_settings[$payment_id]['template_class'])){
                continue;
            }

            // Checking Payment Status On
            if( $payment_status[$payment_id] == 'on' ){
                $sorted_active[$payment_id] = $payment_settings[$payment_id];
            }
        }
    }else{
        $sorted_active = null;
    }

    return $sorted_active;
}

function lsdc_payment_cache_object( $payment_settings, $payment_id, $cache = array() )
{
    // Empty Key -> Skipped
    if(isset($payment_settings[$payment_id])){
        $item = $payment_settings[$payment_id];
    }else{
        $obj = 'continue';
    }

    // Caching Mechanism
    if( isset($item['template_class'] ) ){
        $template = "LSDCommerce\\" . $item['template_class'];

        if( isset($cache[$template]) ){ 
            $obj = $cache[$template];
        }else{

            if( class_exists($template)){
                $obj = new $template;
            }else{
                $obj = 'continue';
            }       
            $cache[$template] = $obj;
        }
    }

    return $obj;
}
