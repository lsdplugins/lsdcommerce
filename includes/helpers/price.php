<?php

/**
 * Get Price based on ID
 *
 * @param integer $product_id
 * @return int
 */
function lsdc_get_price( int $product_id = null )
{   
    if(!$product_id){
      $product_id = get_the_ID();
    }
    return abs(get_post_meta($product_id, '_price_normal', true));
}


/**
 * Get Discount Price based on Product ID
 *
 * @param integer $product_id
 * @return int
 */
function lsdc_get_price_discount( int $product_id = null )
{
    if(!$product_id){
      $product_id = get_the_ID();
    }
    return abs(get_post_meta($product_id, '_price_discount', true));
}


/**
 * Get Price output HTML
 *
 * @param integer $product_id
 * @return html
 */
function lsdc_get_price_html( int $product_id = null )
{
    if(!$product_id){
        $product_id = get_the_ID();
    }

    //Fallback Product ID
    $normal = lsdc_get_price($product_id);
    $discount = lsdc_get_price_discount($product_id);

    if ($discount): ?>
        <span class="product-item-price-discount">
            <?php echo lsdc_currency_format(true, lsdc_get_price() ); ?>
        </span>
        <span class="product-price product-item-price-normal discounted">
            <?php echo lsdc_currency_format(true, lsdc_get_price_discount() ); ?>
        </span>
    <?php else: ?>
        <?php if ($normal): ?>
          <span class="product-price product-item-price-normal">
              <?php echo lsdc_currency_format(true, lsdc_get_price() ); ?>
          </span>
        <?php else: ?>
          <span class="product-item-price-normal">
              <?php _e("Gratis", 'lsdcommerce');?>
          </span>
        <?php endif;?>
    <?php endif;
}
?>
