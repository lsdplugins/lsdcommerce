<?php
namespace LSDCommerce\Shortcodes;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Display Store Front
 *
 * Filter
 * Listing
 * Sorting
 */
class StoreFront
{
    public function __construct()
    {
        add_shortcode('lsdcommerce_storefront', [$this, 'render']);
    }

    public function filter()
    {

    }
    
    public function listing()
    {

    }

    public function sorting()
    {

    }

    public function render($atts)
    {
        // extract(shortcode_atts(array(
        //     'count' => false,
        //     'program_id' => false,
        // ), $atts));

        ob_start();

        ?>

<div id="lsdcommerce-container" class="max480 lsdc-bg-color">

    <main class="page-content lsdcommerce">
        <div class="lsdc-card">
            <!-- Header Store -->
            <div class="card-header card-header-white">
                <h6 class="card-title">
                    <?php _e( 'Semua Produk', 'lsdcommerce' ); ?>
                    <small><?php // echo lsdc_count_products( 'lsdc-product', 'Produk', 'Produk' ); ?></small>
                </h6>
            </div>

            <!-- Body Store -->
            <div class="card-body">
                <!-- Product Listing -->
                <section class="products py-3">
                    <div class="container">
                        
                        <?php 
                            $query = new \WP_Query( array( 
                                'post_type' => 'lsdc-product',
                                'post_status' => 'publish',
                            ));
                        ?>
                        <?php if ( $query->have_posts() ) : ?>
                            <div class="lsdp-row lsdp-pt-10 lsdp-pl-10">
                            <?php while ( $query->have_posts() ) : $query->the_post(); ?>

                                <div class="lsdc-product-item col-6">
                                    <figure class="product-item">
                                        <div class="product-item-img">
                                            <a href="<?php the_permalink(); ?>" alt="<?php the_title(); ?>">
                                                <?php the_post_thumbnail( 'lsdcommerce-thumbnail-listing' ); ?>
                                            </a>
                                        </div>
                                        <figcaption>
                                            <div class="lsdp-row no-gutters">
                                                <div class="col-10">
                                                    <!-- Display Category Product -->
                                                    <?php echo get_the_term_list( get_the_ID(), 'lsdc-product-category', ' <div class="product-item-category">', ', ', '</div>' ); ?>
                                                  
                                                    <h3 class="product-item-name">
                                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                                    </h3>
                                                    <h6 class="product-item-price">
                                                        <?php do_action('lsdcommerce_listing_price_hook'); ?>
                                                    </h6>
                                                </div>
                                                <!-- PRO Version <div class="col-2 pl-0 text-right">
                                                    <a href="#" class="product-item-wishlist">
                                                        <ion-icon name="heart-outline"></ion-icon>
                                                    </a>
                                                </div> -->
                                            </div>
                                        </figcaption>
                                    </figure>
                                </div>

                            <?php endwhile; wp_reset_postdata(); ?>
                            </div>
                        <?php else: ?>

                            <div class="lsdp-alert lsdc-info  mb-10 lsdp-mx-10">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-info"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                                <p><?php _e( 'Product is empty', 'lsdcommerce' ); ?></p>
                            </div>

                        <?php endif; ?>
                        
                    </div>
                </section>
            </div>
            <?php do_action( 'lsdcommerce_etalase_after' ); ?>

        </div>
    </main> <!-- main -->

</div>
                        <?php

        $render = ob_get_clean();

        return $render;
    }
}
new StoreFront;
