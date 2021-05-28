<?php
namespace LSDCommerce;

/*********************************************/
/* Displaying Notifications Menu Registered
/* wp-admin -> LSDCommerce -> Notifications
/********************************************/

if (!defined('ABSPATH')) {
    exit;
}

class Shipping_Admin
{
    public function __construct()
    {
        echo '<div id="shipping" class="verticaltab">';
        foreach (Shipping::registered() as $item) : ?>
            <section class="tabitem">
                <!-- Tab -->
                <?php echo $item->tab(); ?>

                <article>
                    <!-- Status -->
                    <?php echo $item->header(); ?>

                    <!-- Manage -->
                    <?php echo $item->manage(); ?>
                </article>
            </section>
            <?php
        endforeach;
        echo '</div>';
    }
}
new Shipping_Admin();
?>