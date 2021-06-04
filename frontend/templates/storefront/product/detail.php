<?php 
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}
?>

<?php get_header(); ?>

<div id="lsdcommerce-container" class="max480">
    <main class="page-content lsdcommerce">

        <?php do_action( 'lsdcommerce/single/before' ); ?>

        <!-- Product Detail -->
        <div id="product-detail" 
            data-id="<?php the_ID(); ?>"
            data-title="<?php the_title(); ?>"
            data-price="<?php echo lsdc_get_product_price(); ?>" 
            data-weight="<?php echo lsdc_get_product_weight(); ?>" 
            data-thumbnail="<?php the_post_thumbnail_url( get_the_ID(), 'lsdcommerce-thumbnail-mini' ); ?>" 
            data-limit="<?php echo empty( get_post_meta( get_the_ID(), '_limit_order', true ) ) ? 9999 : get_post_meta( get_the_ID(), '_limit_order', true ); ?>"  
            class="card">

            <div class="card-body lsdcommerce-bg-color">
                <section class="product-detail">
                    <figure id="featured-image">
                        <?php the_post_thumbnail('lsdcommerce-thumbnail-single' ); ?>
                    </figure>
                </section>

                <!-- Product Meta -->
                <section class="product-item--detail lsdp-py-10">
    
                    <div class="lsdp-row p-default align-items-end">
                        <div class="col-8 py-10">
                            <!-- Product Name -->
                            <h2 class="product-item-name">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_title(); ?>
                                </a>
                            </h2>

                            <!-- Product Price -->
                            <h6 class="product-item-price">
                                <?php do_action( 'lsdcommerce/single/price'); ?>
                            </h6>
                            
                            <!-- Product Category -->
                            <?php echo get_the_term_list( get_the_ID(), 'product-category', ' <div class="product-item-category">', ', ', '</div>' ); ?>
                        </div>
                        
                        <div class="col-auto ml-auto">
                            <!-- Product Stock -->
                            <div class="product-item-stock text-right">
                                <?php echo lsdc_get_product_stock(); ?>
                            </div>
                        </div>
                    </div>
          
                </section>

                <?php do_action( 'lsdcommerce/single/tab/before' ); ?>

                <!-- Product Description -->
                <section class="product-description">
                    <?php do_action('lsdcommerce/single/tab'); ?>
                </section>

                <?php do_action( 'lsdcommerce/single/tab/after' ); ?>
            </div>

        </div>

        <!-- Cart Manager -->
        <?php do_action( 'lsdcommerce/single/after' ); ?>

    </main> <!-- main -->
</div>

<?php get_footer();  ?>