<?php
/**
 * Plugin Name:       LSDCommerce
 * Plugin URI:        https://lsdplugins.com/lsdcommerce/
 * Description:       Plugin Toko Online Indonesia.
 * Version:           0.0.1
 * Author:            LSD Plugins
 * Author URI:        https://lsdplugins.com/
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       lsdcommerce
 * Domain Path:       /languages
 * 
 * Build: Pre Development
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 * Define Constant
 */
defined('LSDC_VERSION') or define('LSDC_VERSION', '0.0.1');
defined('LSDC_BASE') or define('LSDC_BASE', plugin_basename(__FILE__));
defined('LSDC_PATH') or define('LSDC_PATH', plugin_dir_path(__FILE__));
defined('LSDC_URL') or define('LSDC_URL', plugin_dir_url(__FILE__));
defined('LSDC_STORAGE') or define('LSDC_STORAGE', wp_upload_dir()['basedir'] . '/lsdcommerce');
defined('LSDC_TRANSLATION') or define('LSDC_TRANSLATION', '0.0.0.1' ); // Show Up Different Translation to User for Make User Sync Trnaslation

/**
 * Requirement System
 * PHP :: 7.3 - 7.4
 * WordPress :: 5.7
 */
if (!version_compare(PHP_VERSION, '7.2', '>=')) {
	add_action('admin_notices', 'lsdc_fail_php_version');
} elseif (!version_compare(PHP_VERSION, '7.5', '<=')) {
	add_action('admin_notices', 'lsdc_fail_php_version');
} elseif (!version_compare(get_bloginfo('version'), '5.7', '>=')) {
	add_action('admin_notices', 'lsdc_fail_wp_version');
} else {
	if (WP_DEBUG == true) {
		add_action('admin_notices', 'lsdc_developer_mode');
	}

	require_once LSDC_PATH . 'middleware/plugin.php';   
	\LSDCommerce\Plugin::load();
}

/**
 * LSDCommerce admin notice for minimum PHP version.
 * Warning when the site doesn't have the minimum required PHP version.
 *
 * @since 1.0.0
 * @return void
 */
function lsdc_fail_php_version()
{
	/* translators: %s: PHP version */
	$message = sprintf( esc_html__('LSDCommerce requires PHP version %s, plugin is currently not running.', 'lsdcommerce'), '7.3 - 7.4' );
	$html_message = sprintf('<div class="error">%s</div>', wpautop($message));
	echo wp_kses_post($html_message);
}

/**
 * LSDCommerce admin notice for minimum WordPress version.
 * Warning when the site doesn't have the minimum required WordPress version.
 *
 * @since 1.0.0
 * @return void
 */
function lsdc_fail_wp_version()
{
	/* translators: %s: WordPress version */
	$message = sprintf(esc_html__('LSDCommerce requires WordPress version %s+. Because you are using an earlier version, 
	the plugin is currently NOT RUNNING.', 'lsdcommerce'), '5.2');
	$html_message = sprintf('<div class="error">%s</div>', wpautop($message));
	echo wp_kses_post($html_message);
}

/**
 * LSDCommerce admin notice for enabling debugging
 * Warning when the WP DEBUG set true
 *
 * @since 1.0.0
 * @return void
 */
function lsdc_developer_mode()
{
	// Showing All Error Occured
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

  // Display Notice
	$message = esc_html__('Developer Mode Active, We dont recommend you to activate WP_DEBUG in live site, set WP_DEBUG false in wp-config.php to turn off this notice', 'lsdcommerce');
	$html_message = sprintf('<div class="error">%s</div>', wpautop($message));
	echo wp_kses_post($html_message);
}


/**
 * LSDCommerce admin notice for wp_mail failure
 * Warning when try sending email but get error.
 * 
 * @since 1.0.0
 * @return void
 */
if( get_option('lsdcommerce_mail_error') ){
	add_action('admin_notices', function(){
		$message = esc_html__('This website cannot send email properly, please use SMTP or check this tutorial', 'lsdcommerce');
		$html_message = sprintf('<div class="notice notice-error is-dismissible">%s <a href="%s" target="_blank">'. __('Read Article', 'lsdcommerce') . '</a> or <a href="?mail-failed-ignored">Ignore</a></div>', wpautop($message), 'https://learn.lsdplugins.com/docs/umum/masalah/notifikasi-email-tidak-terkirim/');
		echo wp_kses_post($html_message);
	});
}
?>