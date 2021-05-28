<?php 
namespace LSDCommerce\Admin;

if ( ! defined( 'ABSPATH' ) ) exit;
/***
 * Class Shortcode Lists
 * Task : Displaying Shortcode Lists Registered
 * Handler for Extension Plugin ( To Display Shortcode List )
 * Grouping Based on Extension Plugin
 * Can Modify
 */
class Shortcode_Lists
{
    private static $shortcodeLists;
    private static $default = [];

    /**
     * Add New Shortcode List form Extension
     * 
     * @param string $slug
     * @param string $title
     * @param array $lists
     * @since 3.2.0
     */
    public static function addShortcodeList( string $slug, string $title, array $lists )
    {
        /**
         * Source is Callback Variable from Filter
         */
        add_filter('lsdcommerce/admin/shortcodes/list', function( $source ) use ( $slug, $title, $lists ){
            $source[$slug] = array(
                'plugin_title' => $title,
                'plugin_shortcodes' => $lists
            );
            return $source;
        });
    }

    /**
     * Populating Data ShortcodeList
     * 
     * @since 3.2.0
     */
    public static function ShortcodeList()
    {
        // Checking License		
        if( has_filter('lsdcommerce/admin/shortcodes/list') ) 
        {
            self::$shortcodeLists = apply_filters( 'lsdcommerce/admin/shortcodes/list', self::$default );
        }else{
            self::$shortcodeLists = self::$default;
        }
        
        return self::$shortcodeLists;
    }

    /**
     * Add New ShortCodeList Item
     */
    public static function addShortcodeListItem( string $slug, array $new_item )
    {

        /**
         * Source is Callback Variable from Filter
         */
        add_filter('lsdcommerce/admin/shortcodes/list', function( $source ) use ( $slug, $new_item ){

            #Checking Shortcode List ID Exist
            if (array_key_exists( $slug, $source) ){
                $selecting_shortcodes = $source[$slug]['plugin_shortcodes'];
                $after = array_merge( $selecting_shortcodes, $new_item );
                // Override Based on Slug ID
                $source[$slug]['plugin_shortcodes'] = $after;
            }

            return $source;
        });
    }

    /**
     * Deleting Shortcode Item form Lists
     * @since 3.2.5
     */
    // public static function deleteShortcodeListItem( $id )
    // {
    //     add_filter('lsdcommerce/admin/shortcodes/list/item', function( $source ) use ( $id ){
    //         unset( $source[$id] );
    //         return $source;
    //     });
    // }

    public static function render()
    {
        foreach (array_reverse(self::ShortcodeList()) as $slug => $shortcode) :
           $shortcode = (object) $shortcode;
        ?>
        	<style>
                .load-more-container #load-shortcode-<?php esc_attr_e( $slug ); ?> {
                    display: none;
                }
                .load-more-container #load-shortcode-<?php esc_attr_e( $slug ); ?>:checked ~ ul li:nth-child(1n + 5) {
                    max-height: 999px;
                    opacity: 1;
                    transition: 0.2s ease-in;
                }
                .load-more-container #load-shortcode-<?php esc_attr_e( $slug ); ?>:checked ~ .load-more-btn .loaded {
                    display: block;
                }
                .load-more-container #load-shortcode-<?php esc_attr_e( $slug ); ?>:checked ~ .load-more-btn .unloaded {
                    display: none;
                }
                li.hr{
                    border-bottom: 1px solid #ddd;
                    padding-bottom: 5px;
                }

                /* Load Container CSS */
                .load-more-container {
                background: #fff;
                margin: 20px auto;
                position: relative;
                }

                .load-more-container ul {
                list-style-type: none;
                padding: 0;
                margin: 10px 0 0;
                margin-left: 0;
                }

                .load-more-container ul:after {
                content: "";
                display: table;
                clear: both;
                }

                .load-more-container ul li {
                width: 100%;
                margin: 0;
                float: left;
                border-radius: 2px;
                }

                .load-more-container ul li:nth-child(1n + 5) {
                max-height: 0;
                opacity: 0;
                transition: 0.1s ease-in;
                }

                .load-more-container .load-more-btn .loaded {
                display: none;
                }

                /* Apperance Load More */
                .load-more-container #load-shortcode-lsdd {
                display: none;
                }

                .load-more-container #load-shortcode-lsdd:checked~ul li:nth-child(1n + 5) {
                max-height: 999px;
                opacity: 1;
                transition: 0.2s ease-in;
                }

                .load-more-container #load-shortcode-lsdd:checked~.load-more-btn .loaded {
                display: block;
                }

                .load-more-container #load-shortcode-lsdd:checked~.load-more-btn .unloaded {
                display: none;
                }
            </style>

            <div class="load-more-container">
                <input type="checkbox" id="load-shortcode-<?php esc_attr_e( $slug ); ?>"/>
                <h6 class="float-left"><?php _e( $shortcode->plugin_title ); ?> Shortcodes</h6>

                <label class="load-more-btn float-right" title="<?php _e( "Show All Shortcodes", 'lsdcommerce' ); ?>" for="load-shortcode-<?php esc_attr_e( $slug ); ?>">
                    <span class="unloaded"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg></span>
                    <span class="loaded"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-minus"><line x1="5" y1="12" x2="19" y2="12"></line></svg></span>
                </label>  
    
                <ul>
                    <?php foreach ($shortcode->plugin_shortcodes as $key => $item ) : ?>
                        <li><p class="m-zero"><?php echo isset($item['description'])  ? esc_attr($item['description']) : ''; ?></p><code><?php echo esc_attr( $item['shortcode'] ); ?></code></li>
                    <?php endforeach; ?>
                </ul>
            </div>
      <?php
      endforeach;
    }
     
}
?>