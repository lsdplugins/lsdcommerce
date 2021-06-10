<?php
namespace LSDCommerce\Common;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Order
{
    public function __construct()
    {
        add_action('init', [$this, 'register']);
     
        // add_filter( 'add_meta_boxes', [ $this, 'metabox_register' ] );
        // add_action( 'save_post', [ $this, 'metabox_save' ] );
        // add_action( 'new_to_publish', [ $this, 'metabox_save' ] );

        // add_filter('manage_lsdcommerce_order_posts_columns', [$this, 'column_header']);
        // add_action('manage_lsdcommerce_order_posts_custom_column', [$this, 'columen_content'], 10, 2);
    }

    /**
     * Registering Posttype Order
     *
     * @return void
     */
    protected function register()
    {
        $supports = array(
            'title',
            'excerpt',
            'thumbnail',
        );

        $labels = array(
            'name' => _x('Orders', 'plural', 'lsdcommerce'),
            'singular_name' => _x('Order', 'singular', 'lsdcommerce'),
            'add_new' => _x('New Order', 'Add Order', 'lsdcommerce'),
            'add_new_item' => __('Add Order', 'lsdcommerce'),
            'new_item' => __('New Order', 'lsdcommerce'),
            'edit_item' => __('Edit Order', 'lsdcommerce'),
            'view_item' => __('View Order', 'lsdcommerce'),
            'all_items' => __('All Order', 'lsdcommerce'),
            'search_items' => __('Find Order', 'lsdcommerce'),
            'not_found' => __('Order Not Found.', 'lsdcommerce'),
        );

        $args = array(
            'supports' => $supports,
            'labels' => $labels,
            'public' => false,
            'publicly_queryable' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'show_in_admin_bar' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'order'),
            'has_archive' => true,
            'hierarchical' => false,
            'menu_icon' => LSDC_URL . 'backend/assets/svg/order.svg',
            'comments' => true,
            'exclude_from_search' => true,
        );

        register_post_type('lsdcommerce_order', $args);
        $this->flush();
    }

    protected function flush()
    {
        if (get_option('lsdcommerce_permalink_flush')) {
            // Force and Flush
            global $wp_rewrite;
            $wp_rewrite->set_permalink_structure('/%postname%/');
            update_option("rewrite_rules", false);
            $wp_rewrite->flush_rules(true);

            delete_option('lsdcommerce_permalink_flush');
        }
    }

    protected function js_inject()
    {
        ?>
        <script>
           jQuery( document ).ready(function() {
               jQuery('body.post-type-lsdcommerce_order #postimagediv .inside').append('<p class="recommended" id="donation-recommended">Recommended image size 392 x 210px</p>');
            });

           jQuery(document).on('change', 'input[name="order_type"]',  function() {
               jQuery('body.post-type-lsdcommerce_order #postimagediv .recommended').hide()
                if(jQuery('input[name="order_type"]:checked').val() ){
                   jQuery('body.post-type-lsdcommerce_order #postimagediv #' +jQuery('input[name="order_type"]:checked').val().trim() + '-recommended').show();
                }
            });
        </script>
        <?php
    }

    public function metabox_register()
    {

    }

    public function metabox_render()
    {

    }

    public function metabox_save($postID)
    {
        if (!isset($_POST['lsdc_admin_nonce']) || !wp_verify_nonce($_POST['lsdc_admin_nonce'], basename(__FILE__))) {
            return 'Nonce not Verified';
        }

        if (wp_is_post_autosave($postID)) // Check AutoSave
        {
            return 'autosave';
        }

        if (wp_is_post_revision($postID)) // Check Revision
        {
            return 'revision';
        }

        if ('order' == $_POST['post_type']) // Checking Posttype
        {
            if (!current_user_can('edit_page', $postID)) {
                return 'cannot edit page';
            }
        } else if (!current_user_can('edit_post', $postID)) {
            return 'cannot edit post';
        }

        // update_post_meta($postID, '_order_type', sanitize_text_field($_POST['order_type']));
    }

    public function column_header($columns)
    {
        $columns = array(
            'cb' => $columns['cb'],
            'image' => __('Image'),
            'title' => __('Title'),
            // 'type' => __( 'Type' ),
            'id' => __('ID'),
            'date' => $columns['date'],
        );

        return $columns;
    }

    public function columen_content($column, $postID)
    {
        ?>
        <style>
            .column-image,
            .column-id{
                width: 7%;
            }
        </style>

        <?php
        if ('image' === $column) {
            echo get_the_post_thumbnail($postID, array(39, 39));
        }

        // Type ID
        if ('id' === $column) {
            echo '<input style="width:50px;text-align:center;" value="' . get_the_ID() . '"/>';
        }

        // Type Column
        if ('type' === $column) {
            echo esc_attr(ucfirst(get_post_meta($postID, '_order_type', true)));
        }
    }

}
new Order;
?>