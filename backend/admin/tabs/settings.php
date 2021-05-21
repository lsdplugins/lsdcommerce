<?php
use LSDCommerce\Common\i18n;

/*********************************************/
/* Displaying Settings Menu
/* wp-admin -> LSDCommerce -> Settings
/********************************************/

if (!defined('ABSPATH')) {
    exit;
}
?>

<section id="settings" class="form-horizontal">
    <form>

        <?php
            $countries = i18n::get_countries();
            $settings = get_option('lsdcommerce_general_settings');
            $page_query = new WP_Query(array('posts_per_page' => -1, 'post_type' => 'page', 'post_status' => 'publish'));

            $payment_page = empty($settings['payment_page']) ? '' : abs($settings['payment_page']);
            // $currency_selected = isset($settings['currency']) ? esc_attr($settings['currency']) : 'IDR';
            $report_permission = isset($settings['report_permission']) ? (array) $settings['report_permission'] : array();

            $payment_instruction = isset($settings['payment_instruction']) ? esc_attr($settings['payment_instruction']) : '';
            $payment_confirmation = isset($settings['payment_confirmation']) ? esc_url($settings['payment_confirmation']) : '';

            $currency = strtolower(lsdc_get_currency());
        ?>

        <!-- Payment Url -->
        <div class="form-group">
            <div class="col-3 col-sm-12">
                <label class="form-label" for="payment_page">
                    <?php _e('Payment Page', 'lsdcommerce');?>
                </label>
            </div>
            <div class="col-5 col-sm-12">
                <select class="form-select" name="payment_page">
                    <option value=""><?php _e('Please Choose your Payment Page', 'lsdcommerce');?></option>
                    <?php if ($page_query->have_posts()): ?>
                        <?php while ($page_query->have_posts()): $page_query->the_post();?>
                                <option value="<?php the_ID();?>" <?php echo $payment_page == get_the_ID() ? 'selected' : ''; ?>><?php the_title();?></option>
                        <?php endwhile; wp_reset_postdata();?>
                    <?php endif;?>
                </select>
            </div>
        </div>

        <!-- Currency -->
        <div class="form-group">
            <div class="col-3 col-sm-12">
                <label class="form-label" for="currency"><?php _e('Currency', 'lsdcommerce');?></label>
            </div>
            <div class="col-5 col-sm-12">
                <select class="form-select" name="currency">
                    <?php foreach ($countries as $key => $country): ?>
                        <option value="<?php echo $country['currency']; ?>" <?php echo ($country['currency'] == $currency_selected) ? 'selected' : ''; ?>><?php echo $country['currency_format']; ?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>

        <!-- Payment Instruction -->
        <!-- <div class="form-group">
            <div class="col-3 col-sm-12">
                <label class="form-label" for="lsdd_tac"><?php _e('Payment Instruction', 'lsdcommerce');?></label>
            </div>
            <div class="col-5 col-sm-12">
                <textarea class="form-input" name="payment_instruction" placeholder='<?php echo __('In payment transactions using the transfer method, the addition of a unique code will be made as a Donation.', 'lsdcommerce'); ?>' rows="2" ><?php echo $payment_instruction; ?></textarea>
            </div>
        </div> -->

        <div class="divider" data-content="<?php _e('Nominal and Confirmation', 'lsdcommerce');?>"></div>

        <div class="form-group">
            <div class="col-3 col-sm-12">
                <label class="form-label" for="lsdd_confirmation"><?php _e('Manual Confirmation Link', 'lsdcommerce');?></label>
            </div>
            <div class="col-5 col-sm-12">
                <input type="text" class="form-input" name="payment_confirmation" placeholder="https://wa.me/62821321414121" value="<?php echo $payment_confirmation;?>"/>
            </div>
        </div>

        <?php do_action('lsdcommerce/admin/settings');?>

        <br>
        <button class="btn btn-primary" id="lsdd_admin_settings_save" style="width:120px"><?php _e('Simpan', 'lsdcommerce');?></button>
    </form>
</section>