<?php
use LSDCommerce\Licenses;

/*********************************************/
/* Displaying Licenses Menu
/* wp-admin -> LSDCommerce -> Licenses
/********************************************/

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="columns" style="margin: 0 auto;">
    <?php if (Licenses::get('key', 'lsdcommerce')): ?>
        <div class="column col-4 col-xs-12">
            <div class="card">
                <span class="label label-success">
                    <a href="#" class="text-light"><?php echo ucfirst(Licenses::get('status', 'lsdcommerce')); ?></a>
                    <small class="float-right"><?php echo Licenses::get('expired', 'lsdcommerce'); ?></small>
                </span>
                <div class="card-header">
                    <div class="card-title h5"><?php _e('LSDCommerce', 'lsdcommerce');?></div>
                    <label for=""><?php _e('registered', 'lsdcommerce');?> : <?php echo Licenses::get('registered', 'lsdcommerce'); ?></label><br>
                    <button class="btn btn-block my-2 bg-error lsdc-license-register" style="border:none;" data-id="lsdcommerce" data-type="unregister">
                        <?php _e('Release', 'lsdcommerce');?>
                    </button>
                </div>
            </div>
        </div>
        <?php do_action('lsdcommerce/admin/licenses');?>
    <?php else: ?>  
        <div class="column col-4 col-xs-12">
            <div class="card">
                <span class="label label-secondary"><?php _e('Not Registered', 'lsdcommerce');?></span>
                <div class="card-header">
                    <div class="card-title h5"><?php _e('LSDCommerce', 'lsdcommerce');?></div>
                    <small><?php _e('Input your license key', 'lsdcommerce');?> </small>
                    <input autocomplete="off" style="margin-top:5px;" class="form-input lsdc-license-key" type="password" placeholder="License Key">
                    <button class="btn btn-block my-2 bg-success lsdc-license-register" style="border:none;" data-id="lsdcommerce" data-type="register"><?php _e('Register', 'lsdcommerce');?></button>
                    <small id="msg" style="text-transform: capitalize;"><?php _e('Take your license key in the ', 'lsdcommerce');?> <a target="_blank" href="https://lsdplugins.com/member/"><?php _e('Member Area', 'lsdcommerce');?></a></small>
                </div>
            </div>
        </div>
    <?php endif;?>
</div>
<style>.card{padding: 0;}</style>