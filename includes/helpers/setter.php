<?php
/**
 * Set Description Tabs
 */
function lsdc_product_description_header()
{
    $product_tab = array(
        'description' => __('Deskripsi', 'lsdcommerce'),
    );

    $product_tab = array_reverse($product_tab);

    if (has_filter('lsdcommerce/product/tab/header')) {
        $product_tab = apply_filters('lsdcommerce/product/tab/header', $product_tab);
    }

    return array_reverse($product_tab);
}

function lsdc_body_class($class)
{
    $class[] = 'lsdcommerce';
    return $class;
}
add_filter('body_class', 'lsdc_body_class');

function lsdc_product_description()
{
    ?>
        <div class="lsdc-nav-tab">
            <?php $count = 0;foreach (lsdc_product_description_header() as $key => $item): ?>
              <a data-target="<?php echo $key; ?>" data-toggle="tab" class="nav-link <?php echo ($count == 0) ? 'active' : ''; ?>"><?php echo $item; ?></a>
            <?php $count++;endforeach;?>
        </div>

        <div class="lsdc-tab-content py-10 px-10">
            <div class="tab-pane show" data-tab="description">
                <?php the_content();?>
            </div>
            <?php do_action('lsdcommerce/single/tab/content')?>
        </div>
    <?php
}
add_action('lsdcommerce/single/tab', 'lsdc_product_description'); //Single Tabs

/**
 * Cart Manager in Single Product
 */
function lsdc_cart_manager()
{
?>
    <!-- Quantity Button -->
    <!-- <div class="cart-qty-float fixed" product-id="<?php //the_ID(); ?>">
        <div class="lsdc-qty" id="single-qty">
            <button type="button" class="minus button-qty" data-qty-action="minus">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-minus"><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            </button>
            <input min="0" type="number" value="0" name="qty" disabled>
            <button type="button" class="plus button-qty" data-qty-action="plus">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            </button>
        </div>
    </div> -->

    <!-- Cart Management Template : Passed 1.0.0 -->
    <script id="item-template" type="x-template">
        <div class="cart-basket">
            {{#items}}
            <div class="item" id="{{id}}">
                <div class="lsdp-row no-gutters">
                    <div class="col-auto item-name">
                        <div class="img">
                            <img src="{{thumbnail}}" alt="{{title}}"></div>
                        <h6>
                            <span class="name">{{title}}</span>
                            <span class="price">{{price}}</span>
                        </h6>
                    </div>
                    <div class="col-auto item-qty qty ml-auto">
                        <div class="lsdc-qty" >
                            <button type="button" class="minus button-qty" data-qty-action="minus">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-minus"><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            </button>
                            <input min="0" type="number" value="{{qty}}" name="qty" disabled>
                            <button type="button" class="plus button-qty" data-qty-action="plus">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            {{/items}}
        </div>
    </script>

    <div id="cart-popup" class="cart-popup">
        <div class="overlay"></div>
        <div class="cart-container">
            <div class="cart-body hidden">
                <div class="lsdp-row no-gutters mb-3">
                    <div class="col-auto text-left">
                        <p><strong><?php _e('Item', 'lsdcommerce'); ?></strong></p>
                    </div>
                    <div class="col-4 text-right ml-auto">
                        <p><strong><?php _e('Quantity', 'lsdcommerce'); ?></strong></p>
                    </div>
                </div>
                <div class="cart-items p-0" id="cart-items">
                </div>
            </div>
            <div class="cart-footer">
                <div class="container">
                    <div class="lsdp-row no-gutters">
                        <div class="col-auto">
                            <div class="lsdp-row no-gutters align-items-center">
                                <div class="col-auto pr-0">
                                    <a href="javascript:void(0);" class="cart-manager">
                                        <span class="counter">0</span>
                                        <img src="<?php echo LSDC_URL; ?>frontend/assets/svg/cart.svg" alt="" class="icon-20">
                                    </a>
                                </div>
                                <div class="col-auto">
                                    <div class="cart-footer-info">
                                        <h6><?php _e("Keranjang", 'lsdcommerce'); ?></h6>
                                        <h4><?php _e("Kosong", 'lsdcommerce'); ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto ml-auto inline-flex">
                            <button class="lsdp-btn lsdc-btn btn-primary px-5 lsdc-addto-cart"><?php _e('Tambah', 'lsdcommerce'); ?></button>
                            <a class="lsdp-btn lsdc-btn btn-primary btn-dark px-4" href="<?php echo get_the_permalink(lsdc_get_settings('general_settings', 'checkout_page')); ?>"><?php _e("Checkout", 'lsdcommerce'); ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}
add_action('lsdcommerce/single/after', 'lsdc_cart_manager');


/**
 * get price frontend based on prioritize price discount
 *
 * @package Core
 * @subpackage Price
 * @since 1.0.0
 */
function lsdc_price_frontend($product_id = false)
{
    if ($product_id == null) $product_id = get_the_ID(); //Fallback Product ID
    $normal = lsdc_get_normal_price($product_id);
    $discount = lsdc_get_discount_price($product_id);

    if ($discount): ?>
        <span class="product-item-price-discount">
            <?php echo lsdc_currency_format(true, $normal); ?>
        </span> 
        <span class="product-price product-item-price-normal discounted">
            <?php echo lsdc_currency_format(true, $discount); ?>
        </span>
    <?php
    else: ?>
        <?php if ($normal): ?>
        <span class="product-price product-item-price-normal">
            <?php echo lsdc_currency_format(true, $normal); ?>
        </span>
        <?php
        else: ?>
            <span class="product-item-price-normal">
                <?php _e("Gratis", 'lsdcommerce'); ?>
            </span>
        <?php
        endif; ?>
    <?php
    endif;
}
add_action('lsdcommerce/single/price', 'lsdc_price_frontend');

// Apply style Based on Settings
function lsdc_apperance(){
  $fontFamily         = lsdc_get_settings('appearance_settings', 'font_family' ) == null ? 'Poppins' : lsdc_get_settings('appearance_settings', 'font_family' );
  $backgroundTheme    = empty( lsdc_get_settings('appearance_settings', 'background_theme_color' )) ? 'transparent' : lsdc_get_settings('appearance_settings', 'background_theme_color' );
  $colorTheme         = lsdc_get_settings('appearance_settings', 'theme_color' );
  $lighter            = lsdc_adjust_brightness( $colorTheme, 50 );
  $darker             = lsdc_adjust_brightness( $colorTheme, -40 );
  $darker = '#000000';
  echo '<style>
          :root {
              --lsdc-color: '. $colorTheme .';
              --lsdc-lighter-color: '. $lighter .';
              --lsdc-darker-color: '. $darker .';
              --lsdc-background-color: '. $backgroundTheme .';
          }
          
          .lsdc-bg-color{
              background: '. $backgroundTheme .';
          }

          .lsdc-theme-color{
              color: '. $colorTheme .';
          }

          .lsdc-content{
              font-family: -apple-system, BlinkMacSystemFont, "'. $fontFamily . '", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
          }
      </style>';
}
add_action( 'wp_head', 'lsdc_apperance');
?>