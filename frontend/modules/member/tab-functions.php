<?php
/**
 * Add tabs to LSDCommerce settings.
 *
 * @param string $tab_slug
 * @param string $tab_title
 * @param callable $function
 * @return void
 */
function lsdc_member_add_tab(string $tab_slug, string $tab_title, $function = '')
{
    global $lsdc_member_tabs;

    $lsdc_member_tabs = is_array($lsdc_member_tabs) ? $lsdc_member_tabs : array();

    // Make sure menu not overridable
    if (!in_array($tab_slug, $lsdc_member_tabs)) {
        add_filter('lsdcommerce/member/tabs', function ($tab_lists) use ($tab_slug, $tab_title, $function) {
            $tab = array();
            $tab[$tab_slug] = $tab_title;
            return array_reverse(array_merge($tab, array_reverse($tab_lists)));
        });
        add_action("lsdcommerce/member/tabs/{$tab_slug}", $function);
        array_push($lsdc_member_tabs, $tab_slug);
    }
}

/**
 * Remove tabas from LSDCommerce Settings.
 *
 * @param string $tab_slug
 * @return void
 */
function lsdc_member_remove_tab(string $tab_slug)
{
    global $lsdc_member_tabs;

    $lsdc_member_tabs = is_array($lsdc_member_tabs) ? $lsdc_member_tabs : array();

    add_filter('lsdcommerce/member/tabs', function ($tab_lists) use ($tab_slug) {
        unset($tab_lists[$tab_slug]);
        return $tab_lists;
    });
}

/**
 * Listing tabs
 *
 * @return void
 */
function lsdc_member_tablists()
{
    $default = array();
    if (has_filter('lsdcommerce/member/tabs')) {
        $lists = apply_filters('lsdcommerce/member/tabs', $default);
    }
    return $lists;
}

/**
 * Default Admin Tabs
 */
lsdc_member_add_tab('dashboard', __('Dasbor', 'lsdcommerce'), function () {
    require_once LSDC_PATH . 'frontend/templates/storefront/member/dashboard.php';
});

lsdc_member_add_tab('order', __('Pembelian', 'lsdcommerce'), function () {
    require_once LSDC_PATH . 'frontend/templates/storefront/member/order-history.php';
});

lsdc_member_add_tab('shipping', __('Pengiriman', 'lsdcommerce'), function () {
    require_once LSDC_PATH . 'frontend/templates/storefront/member/shipping-history.php';
});

lsdc_member_add_tab('profile', __('Profil', 'lsdcommerce'), function () {
    require_once LSDC_PATH . 'frontend/templates/storefront/member/edit-profile.php';
});
