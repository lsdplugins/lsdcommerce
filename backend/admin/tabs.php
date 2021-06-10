<?php
/**
 * Add tabs to LSDCommerce settings.
 *
 * @param string $tab_slug
 * @param string $tab_title
 * @param callable $function
 * @return void
 */
function lsdc_add_tab_settings( string $tab_slug, string $tab_title, $function = ''){
    global $lsdc_admin_tabs;

    $lsdc_admin_tabs = is_array($lsdc_admin_tabs) ? $lsdc_admin_tabs : array();

    // Make sure menu not overridable
    if( !in_array( $tab_slug, $lsdc_admin_tabs ) ){
        add_filter('lsdcommerce/admin/tabs', function( $tab_lists ) use ( $tab_slug, $tab_title, $function ){
            $tab = array();
            $tab[$tab_slug] = $tab_title;
            return array_reverse( array_merge( $tab,  array_reverse( $tab_lists ) ) );
        });
        add_action( "lsdcommerce/admin/tabs/{$tab_slug}", $function );
        array_push( $lsdc_admin_tabs, $tab_slug );
    }
}

/**
 * Remove tabas from LSDCommerce Settings.
 *
 * @param string $tab_slug
 * @return void
 */
function lsdc_remove_tab_settings( string $tab_slug ){
    global $lsdc_admin_tabs;
    
    $lsdc_admin_tabs = is_array($lsdc_admin_tabs) ? $lsdc_admin_tabs : array();

    add_filter('lsdcommerce/admin/tabs', function( $tab_lists ) use ( $tab_slug ){
        unset( $tab_lists[$tab_slug] );
        return $tab_lists;
    });
}

/**
 * Listing tabs
 *
 * @return void
 */
function lsdc_tab_lists(){
    $default = array();
    if( has_filter('lsdcommerce/admin/tabs') ) {
        $lists = apply_filters( 'lsdcommerce/admin/tabs', $default );
    }
    return $lists;
}

/**
 * Default Admin Tabs
 */
lsdc_add_tab_settings( 'store', __('Toko', 'lsdcommerce'), function () {
    require_once 'tabs/store.php';
});

lsdc_add_tab_settings( 'appearance', __('Tampilan', 'lsdcommerce'), function () {
    require_once 'tabs/appearance.php';
});

lsdc_add_tab_settings( 'notifications', __('Notifikasi', 'lsdcommerce'), function () {
    require_once 'tabs/notifications.php';
});

lsdc_add_tab_settings( 'payments', __('Pembayaran', 'lsdcommerce'), function () {
    require_once 'tabs/payments.php';
});

lsdc_add_tab_settings( 'shipping', __('Pengiriman', 'lsdcommerce'), function () {
    require_once 'tabs/shipping.php';
});

lsdc_add_tab_settings( 'settings', __('Pengaturan', 'lsdcommerce'), function () {
    require_once 'tabs/settings.php';
});
?>