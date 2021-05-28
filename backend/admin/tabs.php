<?php
/**
 * Add tabs to LSDDonation settings.
 *
 * @param string $tab_slug
 * @param string $tab_title
 * @param callable $function
 * @return void
 */
function lsdd_add_tab_settings( string $tab_slug, string $tab_title, $function = ''){
    global $lsdd_admin_tabs;

    $lsdd_admin_tabs = is_array($lsdd_admin_tabs) ? $lsdd_admin_tabs : array();

    // Make sure menu not overridable
    if( !in_array( $tab_slug, $lsdd_admin_tabs ) ){
        add_filter('lsdcommerce/admin/tabs', function( $tab_lists ) use ( $tab_slug, $tab_title, $function ){
            $tab = array();
            $tab[$tab_slug] = $tab_title;
            return array_reverse( array_merge( $tab,  array_reverse( $tab_lists ) ) );
        });
        add_action( "lsdcommerce/admin/tabs/{$tab_slug}", $function );
        array_push( $lsdd_admin_tabs, $tab_slug );
    }
}

/**
 * Remove tabas from LSDDonation Settings.
 *
 * @param string $tab_slug
 * @return void
 */
function lsdd_remove_tab_settings( string $tab_slug ){
    global $lsdd_admin_tabs;
    
    $lsdd_admin_tabs = is_array($lsdd_admin_tabs) ? $lsdd_admin_tabs : array();

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
function lsdd_tab_lists(){
    $default = array();
    if( has_filter('lsdcommerce/admin/tabs') ) {
        $lists = apply_filters( 'lsdcommerce/admin/tabs', $default );
    }
    return $lists;
}

/**
 * Default Admin Tabs
 */
lsdd_add_tab_settings( 'store', __('Toko', 'lsdcommerce'), function () {
    require_once 'tabs/store.php';
});

lsdd_add_tab_settings( 'appearance', __('Tampilan', 'lsdcommerce'), function () {
    require_once 'tabs/appearance.php';
});

lsdd_add_tab_settings( 'notifications', __('Notifikasi', 'lsdcommerce'), function () {
    require_once 'tabs/notifications.php';
});

lsdd_add_tab_settings( 'payments', __('Pembayaran', 'lsdcommerce'), function () {
    require_once 'tabs/payments.php';
});

lsdd_add_tab_settings( 'shipping', __('Pengiriman', 'lsdcommerce'), function () {
    require_once 'tabs/shipping.php';
});

lsdd_add_tab_settings( 'settings', __('Pengaturan', 'lsdcommerce'), function () {
    require_once 'tabs/settings.php';
});
?>