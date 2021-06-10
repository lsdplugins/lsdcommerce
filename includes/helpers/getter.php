<?php
use LSDCommerce\Common\i18n;

function lsdc_get_country(string $data = 'iso2')
{
    $country = i18n::get_countries();
    return 'id';
}

function lsdc_get_normal_price($product_id = false)
{
    return abs(get_post_meta($product_id, '_price_normal', true));
}

/**
 * get price discount
 *
 * @package Core
 * @subpackage Price
 * @since 1.0.0
 */
function lsdc_get_discount_price($product_id = false)
{
    return abs(get_post_meta($product_id, '_price_discount', true));
}

function lsdc_get_product_price($product_id = false)
{
    if ($product_id == null) {
        $product_id = get_the_ID();
    }

    $normal = lsdc_get_normal_price($product_id);
    $discount = lsdc_get_discount_price($product_id);

    if ($discount) {
        return abs($discount);
    } else {
        if ($normal) {
            return abs($normal);
        } else {
            return 0;
        }
    }
}

function lsdc_get_product_weight($product_id = false)
{
    if ($product_id == null) {
        $product_id = get_the_ID();
    }
    //Fallback Product ID
    return abs(lsdc_currency_cleanup(get_post_meta($product_id, '_physical_weight', true)));
}

function lsdc_get_product_stock($product_id = false)
{
    if ($product_id == null) {
        $product_id = get_the_ID();
    }
    //Fallback Product ID
    $stock = '<p>' . __('Stok', 'lsdcommerce') . '<span>';
    if (get_post_meta($product_id, '_stock', true) > 999):
        $stock .= __('Tersedia', 'lsdcommerce');
    else:
        $stock .= abs(get_post_meta(get_the_ID(), '_stock', true)) . ' ' . esc_attr(get_post_meta(get_the_ID(), '_stock_unit', true));
    endif;
    $stock .= '</span></p>';

    return $stock;
}

// TODO :: Tight Coupled to License Manager
function lsdc_get_product_version($product_id)
{
    /* Pro Code */
    $changelog = get_post_meta($product_id, '_product_update_changelog', true);

    if ($changelog) {
        foreach (array_reverse($changelog) as $key => $version) {
            if (strtotime($version['datetime']) <= strtotime(current_time('Y-m-d H:i:s'))) {
                return esc_attr($version['version']);
            }
        }
        /* Pro Code */
    } else {
        if (get_post_meta($product_id, '_digital_version', true)) {
            return esc_attr(get_post_meta($product_id, '_digital_version', true));
        }
    }
}

function lsdc_get_product_download($product_id)
{
    /* Pro Code */
    $changelog = get_post_meta($product_id, '_product_update_changelog', true);

    if ($changelog) {
        foreach (array_reverse($changelog) as $key => $version) {
            if (strtotime($version['datetime']) <= strtotime(current_time('Y-m-d H:i:s'))) {
                return esc_url($version['file_url']);
            }
        }
    } else {
        /* Pro Code */
        if (get_post_meta($product_id, '_digital_url', true)) {
            return esc_url(get_post_meta($product_id, '_digital_url', true));
        }
    }
}

function lsdc_get_product_type($order_id)
{
    $products = (array) json_decode(get_post_meta($order_id, 'products', true));
    $types = array();

    foreach ($products as $key => $product) {
        $product_id = abs($product->id);
        $type = get_post_meta($product_id, '_shipping_type', true);

        if (!in_array($type, $types)) {
            array_push($types, $type);
        }
    }
    return $types;
}

function lsdc_product_type($product_id)
{
    $type = strtolower(get_post_meta($product_id, '_shipping_type', true));
    return esc_attr($type);
}

function lsdc_product_extract_ID($product_id)
{

    $productID = explode('-', $product_id);
    if (isset($productID[1])) {
        return abs($productID[0]);
    } else {
        return abs($product_id);
    }
}

function lsdc_product_variation_exist($id, $variation)
{
    $variations = json_decode(get_post_meta($id, '_variations', true));
    $multi_variations = explode('-', $variation); // [ 8, hitam, xl ]
    unset($multi_variations[0]); // remove id product [ hitam, xl ]
    $multi_variations = array_map('strtolower', $multi_variations);

    $temp = array();
    if (!empty($variations)) { // Check Variation
        foreach ($variations as $key => $variant) { // Multi Variations
            foreach ($variant->items as $key => $item) { // Inside Variation
                if (in_array(strtolower($item->name), $multi_variations)) { // name exist
                    $temp[] = strtolower($item->name);
                }
            }
        }

    }

    if (!empty($temp)) {
        return true;
    } else {
        return false;
    }
}

function lsdc_product_title_summary($order_id)
{
    $products = (array) json_decode(get_post_meta($order_id, 'products', true));
    $names = array();

    foreach ($products as $key => $product) {
        $product_id = lsdc_product_extract_ID($product->id);
        array_push($names, get_the_title($product_id));
    }

    if (isset($names[1])) {
        $string = $names[0] . ', ' . $names[1] . '...';
    } else {
        $string = $names[0];
    }
    return $string;
}

function lsdc_product_variation_price($id, $variation)
{
    $variations = (array) json_decode(get_post_meta($id, '_variations', true)); // Get Variations Data from Product
    $multi_variations = explode('-', $variation); // [ 8, hitam, xl ]
    unset($multi_variations[0]); // remove id product [ hitam, xl ]
    $multi_variations = array_map('strtolower', $multi_variations);

    $variation_price = null;

    if (!empty($variations)) { // Check Variation
        foreach ($variations as $key => $variant) { // Multi Variations
            foreach ($variant->items as $key => $item) { // Inside Variation
                if (in_array(strtolower($item->name), $multi_variations)) {
                    $variation_price = lsdc_currency_clean($item->price);
                }
            }
        }
    }

    $normal = lsdc_get_normal_price($id);
    $discount = lsdc_get_discount_price($id);

    // Add Variation Price to Base Price
    if ($discount) {
        return abs($discount) + abs($variation_price);
    } else {
        if ($normal) {
            return abs($normal) + abs($variation_price);
        } else {
            return 0;
        }
    }
}

function lsdc_product_variation_label($id, $variation)
{
    $variations = (array) json_decode(get_post_meta($id, '_variations', true)); // Get Variations Data from Product
    $multi_variations = explode('-', $variation); // [ 8, hitam, xl ]
    unset($multi_variations[0]); // remove id product [ hitam, xl ]
    $multi_variations = array_map('strtolower', $multi_variations);

    $variation_price = null;

    if (!empty($variations)) { // Check Variation
        foreach ($variations as $key => $variant) { // Multi Variations
            foreach ($variant->items as $key => $item) { // Inside Variation
                if (in_array(strtolower($item->name), $multi_variations)) {
                    return esc_attr($item->name);
                }
            }
        }
    }
}


function lsdc_get_settings($option, $item)
{
    $settings = get_option('lsdc_' . $option);
    return empty($settings[$item]) ? null : esc_attr($settings[$item]);
}

/**
 * Get User Name by User ID
 * Block : User
 * @param int $user_id
 */
function lsdc_get_user_name( $user_id = false ){
    $user_id = empty( $user_id ) ?  get_current_user_id() : $user_id;
    return ucfirst( esc_attr( get_user_meta( $user_id, 'first_name', true ) ) ) . ' ' . esc_attr( get_user_meta( $user_id, 'last_name', true ) );
}

/**
 * Get User Phone by User ID
 * Block : User
 * @param int $user_id
 */
function lsdc_get_user_phone( $user_id = false ){
    $user_id = empty( $user_id ) ?  get_current_user_id() : $user_id;
    return esc_attr( get_user_meta( $user_id, 'user_phone', true ) );
}

/**
 * Get User Email by User ID
 * Block : User
 * @param int $user_id
 */
function lsdc_get_user_email( $user_id = false ){
    $user_id = empty( $user_id ) ?  get_current_user_id() : $user_id;
    $user = get_user_by( 'id', $user_id );
    if(  $user  ){
        return sanitize_email( $user->user_email );
    }else{
        return false;
    }
    
}