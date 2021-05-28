<?php
namespace LSDCommerce;

/*********************************************/
/* Displaying Tabs Menu
/* wp-admin -> LSDCommerce
/********************************************/

if (!defined('ABSPATH')) {
    exit;
}
?>

<?php
/**
 * Display Default Tab based on URL
 * set default to Institution page
 */
if (isset($_GET["tab"])) {
    if (htmlentities($_GET["tab"], ENT_QUOTES) == "store") {
        $active_tab = "store";
    } else {
        $active_tab = htmlentities($_GET["tab"], ENT_QUOTES);
    }
} else {
    $active_tab = "store";
}
?>

<div class="wrap lsdcommerce-admin">

    <?php $tab_lists = lsdd_tab_lists(); ?>
    
    <!-- Display Header Tabs -->
    <div class="column col-12 col-sm-12 px-0">
        <ul class="tab tab-primary">
            <?php if (  $tab_lists ): ?>
                <?php foreach ( $tab_lists as $key => $title): ?>
                    <li class="tab-item <?php if ($active_tab == $key) {echo 'active';}?>">
                        <a href="?page=lsdcommerce&tab=<?php esc_attr_e($key);?>"><?php echo esc_attr($title); ?></a>
                    </li>
                <?php endforeach;?>
            <?php endif;?>
            <li class="tab-item <?php if ($active_tab == 'extensions') {echo 'active';}?>">
                <a class="" data-badge="10" href="?page=lsdcommerce&tab=extensions"><?php _e('Ekstensi', 'lsdcommerce');?></a>
            </li>
        </ul>
    </div>

    <!-- Display Content Tabs -->
    <article class="tab-content">
    <?php
    // $white_list = [‘db.php’, filter.php’, ‘condense.php’]
    // If (in_array($white_list, $file_to_include)) {
    // include($file_to_include);
    // }

    if (isset($_GET["tab"])) {
        $tabs_query = htmlentities($_GET["tab"], ENT_QUOTES);
        if ($tab_lists) {
            foreach ($tab_lists as $key => $item) {
                if ($tabs_query == $key || $active_tab == $key) {
                    do_action("lsdcommerce/admin/tabs/{$key}");
                } else if ($tabs_query == 'extensions') {
                    require_once 'extensions.php';
                }
            }
        } else if ($tabs_query == 'extensions') {
            require_once 'extensions.php';
        }
    } else { //Fallback
        require_once 'store.php';
    }
    ?>
    </article>
</div>