<?php
/*********************************************/
/* Displaying Extensions Menu
/* wp-admin -> LSDCommerce -> Extension
/********************************************/
?>

<div class="columns">
    <div class="column col-12">
        <div class="filter">
            <input class="filter-tag d-hide" id="tag-0" type="radio" name="filter-radio" hidden="" checked="">
            <input class="filter-tag d-hide" id="tag-1" type="radio" name="filter-radio" hidden="">
            <input class="filter-tag d-hide" id="tag-2" type="radio" name="filter-radio" hidden="">
            <input class="filter-tag d-hide" id="tag-3" type="radio" name="filter-radio" hidden="">
            <input class="filter-tag d-hide" id="tag-4" type="radio" name="filter-radio" hidden="">
            <div class="filter-nav">
                <label class="chip" for="tag-0"><?php _e('All', 'lsdcommerce');?></label>
                <label class="chip" for="tag-1"><?php _e('Payment Gateway', 'lsdcommerce');?></label>
                <!-- <label class="chip" for="tag-2"><?php _e('Funding', 'lsdcommerce');?></label> -->
                <label class="chip" for="tag-3"><?php _e('Confirmation', 'lsdcommerce');?></label>
            </div>
            <div class="filter-body columns">
                <div class="column col-xs-12 filter-item" data-tag="tag-2">
                    <div class="card">
                        <span class="label label-success"><a href="https://lsdplugins.com/lsdconasi-campaign/" target="_blank" class="text-light"><?php _e( 'Learn', 'lsdcommerce') ?>
                        </a></span>
                        <div class="card-header">
                        <div class="card-title h5">Pro</div>
                        <label for="">Advanced and Automated</label>
                        </div>

                    </div>
                </div>

                <div class="column col-xs-12 filter-item" data-tag="tag-2">
                    <div class="card">
                        <span class="label label-success"><a href="https://lsdplugins.com/lsdconasi-zakat/" target="_blank" class="text-light"><?php _e( 'Learn', 'lsdcommerce') ?>
                        </a></span>
                        <div class="card-header">
                        <div class="card-title h5">Affiliate</div>
                            <label for="">Affiliasi Program</label>
                        </div>
                    </div>
                </div>

                <div class="column col-xs-12 filter-item" data-tag="tag-2">
                    <div class="card">
                        <span class="label label-success"><a href="https://lsdplugins.com/lsdconasi-membership/" target="_blank" class="text-light"><?php _e( 'Learn', 'lsdcommerce') ?>
                        </a></span>
                        <div class="card-header">
                        <div class="card-title h5">BCA Konfirmasi</div>
                            <label for="">Konfirmasi Bank BCA Otomatis</label>
                        </div>
                    </div>
                </div>

                <div class="column col-xs-12 filter-item" data-tag="tag-1">
                    <div class="card">
                        <span class="label label-success"><a href="https://lsdplugins.com/lsdconasi-midtrans/" target="_blank" class="text-light"><?php _e( 'Learn', 'lsdcommerce') ?>
                        </a></span>
                        <div class="card-header">
                        <div class="card-title h5">Midtrans</div>
                        <label for="">Indonesia Payment Gateway</label>
                        </div>
                    </div>
                </div>

                <div class="column col-xs-12 filter-item" data-tag="tag-1">
                    <div class="card">
                        <span class="label label-success"><a href="https://lsdplugins.com/lsdconasi-ipaymu/" target="_blank" class="text-light"><?php _e( 'Learn', 'lsdcommerce') ?>
                        </a></span>
                        <div class="card-header">
                        <div class="card-title h5">iPaymu</div>
                        <label for="">Indonesia Payment Gateway</label>
                        </div>
                    </div>
                </div>

                <div class="column col-xs-12 filter-item" data-tag="tag-1">
                    <div class="card">
                        <span class="label label-success"><a href="https://lsdplugins.com/lsdconasi-faspay/" target="_blank" class="text-light"><?php _e( 'Learn', 'lsdcommerce') ?>
                        </a></span>
                        <div class="card-header">
                        <div class="card-title h5">Faspay</div>
                        <label for="">Indonesia Payment Gateway</label>
                        </div>
                    </div>
                </div>

                <div class="column col-xs-12 filter-item" data-tag="tag-3">
                    <div class="card">
                        <span class="label label-success"><a href="https://lsdplugins.com/lsdconasi-moota/" target="_blank" class="text-light"><?php _e( 'Learn', 'lsdcommerce') ?>
                        </a></span>
                        <div class="card-header">
                        <div class="card-title h5">Moota</div>
                        <label for="">Payment Confirmation</label>
                        </div>
                    </div>
                </div>

                <div class="column col-xs-12 filter-item" data-tag="tag-1">
                  <div class="card">
                      <span class="label label-success"><a href="https://lsdplugins.com/lsdconasi-paypal/" target="_blank" class="text-light"><?php _e( 'Learn', 'lsdcommerce') ?>
                      </a></span>
                      <div class="card-header">
                      <div class="card-title h5">Paypal</div>
                      <label for="">Payment Gateway</label>
                      </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    .card{
        padding: 0;
    }
</style>