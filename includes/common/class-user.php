<?php
namespace LSDCommerce\Common;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add User Role and User Meta
 */
class User
{
    public function __construct()
    {
        add_role('customer', __('Pembeli'), array(
            'read' => true,
        ));

        add_role('store_manager', __('Manager Toko'), array(
            'read' => true, // Subscription Access
            'edit_posts' => true,
            'delete_posts' => false,
        ));

        add_action('user_new_form', [$this, 'phone_field']);
        add_action('show_user_profile', [$this, 'phone_field']);
        add_action('edit_user_profile', [$this, 'phone_field']);

        add_action('personal_options_update', [ $this,'phone_save']);
        add_action('edit_user_profile_update', [ $this,'phone_save']);
        add_action('user_register', [ $this,'phone_save']);
        add_action('profile_update', [ $this,'phone_save']);
    }

    public function phone_field($user)
    {
        $phone = null;
        if ($user) {
            $phone = get_user_meta($user->ID, 'user_phone', true) != null ? get_user_meta($user->ID, 'user_phone', true) : null;
        }
        ?>
        <table class="form-table">
          <tr>
            <th><label for="user_phone"><?php _e("Phone");?></label></th>
            <td>
              <input type="text" name="user_phone" id="user_phone" class="regular-text" value="<?php echo lsdc_format_phone($phone); ?> "/><br/>
              <span class="description"><?php _e("Silahkan masukan nomor telepon.");?></span>
            </td>
          </tr>
        </table>
      <?php
    }

    public function phone_save($user_id)
    {
        if (!current_user_can('create_users', $user_id)) {
            return false;
        }
        // if (!current_user_can('manage_options')) return false;
        update_usermeta($user_id, 'user_phone', lsdc_format_phone($_POST['user_phone']));
    }
}
new User;
?>