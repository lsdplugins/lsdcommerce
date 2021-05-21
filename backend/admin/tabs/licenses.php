<?php
use LSDDonation\Licenses;

/*********************************************/
/* Displaying Licenses Menu
/* wp-admin -> LSDDonation -> Licenses
/********************************************/

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="columns" style="margin: 0 auto;">
    <?php if (Licenses::get('key', 'lsddonation')): ?>
        <div class="column col-4 col-xs-12">
            <div class="card">
                <span class="label label-success">
                    <a href="#" class="text-light"><?php echo ucfirst(Licenses::get('status', 'lsddonation')); ?></a>
                    <small class="float-right"><?php echo Licenses::get('expired', 'lsddonation'); ?></small>
                </span>
                <div class="card-header">
                    <div class="card-title h5"><?php _e('LSDDonation', 'lsddonation');?></div>
                    <label for=""><?php _e('registered', 'lsddonation');?> : <?php echo Licenses::get('registered', 'lsddonation'); ?></label><br>
                    <button class="btn btn-block my-2 bg-error lsdd-license-register" style="border:none;" data-id="lsddonation" data-type="unregister">
                        <?php _e('Release', 'lsddonation');?>
                    </button>
                </div>
            </div>
        </div>
        <?php do_action('lsddonation/admin/licenses');?>
    <?php else: ?>  
        <div class="column col-4 col-xs-12">
            <div class="card">
                <span class="label label-secondary"><?php _e('Not Registered', 'lsddonation');?></span>
                <div class="card-header">
                    <div class="card-title h5"><?php _e('LSDDonation', 'lsddonation');?></div>
                    <small><?php _e('Input your license key', 'lsddonation');?> </small>
                    <input autocomplete="off" style="margin-top:5px;" class="form-input lsdd-license-key" type="password" placeholder="License Key">
                    <button class="btn btn-block my-2 bg-success lsdd-license-register" style="border:none;" data-id="lsddonation" data-type="register"><?php _e('Register', 'lsddonation');?></button>
                    <small id="msg" style="text-transform: capitalize;"><?php _e('Take your license key in the ', 'lsddonation');?> <a target="_blank" href="https://lsdplugins.com/member/"><?php _e('Member Area', 'lsddonation');?></a></small>
                </div>
            </div>
        </div>
    <?php endif;?>
</div>
<style>.card{padding: 0;}</style>