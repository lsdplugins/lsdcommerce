<?php
use LSDDonation\Notifications;

/*********************************************/
/* Displaying Notifications Menu Registered
/* wp-admin -> LSDDonation -> Notifications
/********************************************/

if (!defined('ABSPATH')) {
    exit;
}

class Notification_Admin
{
    public function __construct()
    {
        echo '<div id="notifications" class="verticaltab">';
        foreach (Notifications\NotificationsRegistrar::registered() as $item) : ?>
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
new Notification_Admin();
?>