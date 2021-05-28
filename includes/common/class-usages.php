<?php
namespace LSDCommerce\Common;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Usages
{
    private $domain;
    private $settings;

    public static function register()
    {
        $plugin = new self();
        $plugin->domain = str_replace(".", "_", parse_url(get_site_url())['host']);
        $plugin->settings = is_array(get_option(plugin_basename(LSDC_PATH) . '_site_usage')) ? get_option(plugin_basename(LSDC_PATH) . '_site_usage') : $plugin->collecting();
        
        add_action('lsdcommerce/updates', [ $plugin, 'post_active_day']);

        if( empty($plugin->settings) ){
            $plugin->collecting();
            $plugin->remote_post();
        }
    }

    /**
     * Counting Active Day
     * Used every day on scheduler
     *
     * @return void
     */
    private function post_active_day()
    {
        $old = abs($this->settings[$this->domain]['plugin_usage']['active_day']);
        $count = $this->settings[$this->domain]['plugin_usage']['active_day'] = $old + 1; // Updating Data Active

        update_option(plugin_basename(LSDC_PATH) . '_site_usage', $this->settings);
        $this->remote_post();
        return $count;
    }

    /**
     * Collecting Usages Data.
     * Used when data usage empty
     *
     * @return void
     */
    private function collecting()
    {
        global $wpdb;

        $new = array();
        $new[$this->domain] = array(
            'server' => $_SERVER['SERVER_SOFTWARE'],
            'server_php_version' => phpversion(),
            'server_mysql_version' => $wpdb->db_version(),
            'wp_version' => get_bloginfo('version'),
            'wp_memory_limit' => WP_MEMORY_LIMIT,
            'wp_max_upload' => ini_get('upload_max_filesize'),
            'wp_permalink' => get_option('permalink_structure'),
            'wp_multisite' => is_multisite(),
            'wp_language' => get_bloginfo('language'),
            'wp_theme' => wp_get_theme()->get('Name'),
            'wp_plugins' => get_option('active_plugins'),
            'site_url' => get_bloginfo('url'),
            'site_email' => get_bloginfo('admin_email'),
            'plugin_usage' => array(
                'plugin' => plugin_basename(LSDC_PATH),
                'installed' => get_option(plugin_basename(LSDC_PATH) . '_installed'),
                'active' => false,
                'active_day' => 0,
                'updated' => 0,
                'version' => LSDC_VERSION,
                'storage' => is_dir(LSDC_STORAGE),
                'translation' => is_dir(WP_CONTENT_DIR . '/languages/plugins/'),
            ),
        );

        return $new;
    }

    /**
     * Activate This Plugin
     * used when active or deactive plugin
     *
     * @return void
     */
    private function post_plugin_active_status()
    {

        if (isset($this->settings[$this->domain])) {
            $this->settings[$this->domain]['plugin_usage']['active'] = is_plugin_active(plugin_basename(LSDC_PATH) . '/' . plugin_basename(LSDC_PATH) . '.php');
            update_option(plugin_basename(LSDC_PATH) . '_site_usage', $this->settings);
        }
        $this->remote_post();
    }

    /**
     * Counting Updated Times
     * used when have new update version of plugin
     * calling via schduler every day
     *
     * @return void
     */
    private function post_plugin_updated_status()
    {
        $old = $this->settings[$this->domain]['plugin_usage']['updated'];
    
        if (is_array($old)) {
            if (!in_array(LSDC_VERSION, $old)) {
                array_push($old, LSDC_VERSION);
            }
        } else {
            $old = array(LSDC_VERSION);
        }
        $this->settings[$this->domain]['plugin_usage']['updated'] = $old; // Updating Data Active
        update_option(plugin_basename(LSDC_PATH) . '_site_usage', $this->settings);
        $this->remote_post();
    }

    /**
     * Push to Remote
     *
     * @return void
     */
    private function remote_post()
    {
        $headers = array(
            'Content-Type' => 'application/json',
        );

        $payload = array(
            'method' => 'POST',
            'timeout' => 30,
            'headers' => $headers,
            'httpversion' => '1.0',
            'sslverify' => false,
            'body' => json_encode($this->settings),
            'cookies' => array(),
        );

        if ($this->domain != 'localhost') {
            $response = wp_remote_post('https://stats.lsdplugins.com/api/v1/lsdcommerce/', $payload);
            $response = json_decode(wp_remote_retrieve_body($response), true);
        }
        return $response;
    }

}
Usages::register();
?>