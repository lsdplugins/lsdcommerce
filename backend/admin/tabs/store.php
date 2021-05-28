<?php
use LSDCommerce\Common\i18n;

/*********************************************/
/* Displaying Store Settings
/* wp-admin -> LSDCommerce -> Institution
/********************************************/

if (!defined('ABSPATH')) {
    exit;
}
?>

<section id="store" class="form-horizontal">
    <form>
        <?php
        $countries = i18n::get_countries();

        $store = get_option('lsdcommerce_store');

        $name = isset($store['name']) ? esc_attr($store['name']) : '';
        // $logo = isset($store['logo']) ? esc_url($store['logo']) : 'https://plugin.lasida.work/demo/wp-content/uploads/2020/04/LSD-DONASI-LOGO.png';

        $country = 'ID';
        $state = isset($store['state']) ? abs($store['state']) : '';
        $district = isset($store['district']) ? esc_attr($store['district']) : '';
        $address = isset($store['address']) ? esc_attr($store['address']) : '';

        $id_states = json_decode(file_get_contents(LSDC_PATH . 'includes/cache/ID-states.json'));
        $id_cities = json_decode(file_get_contents(LSDC_PATH . 'includes/cache/ID-cities.json'));
        ?>

        <!-- Store Name -->
        <div class="form-group">
            <div class="col-3 col-sm-12">
                <label class="form-label" for="name"><?php _e('Store Name', 'lsdcommerce');?></label>
            </div>
            <div class="col-5 col-sm-12">
                <input type="text" class="form-input" name="name" placeholder="TokoAlus" value="<?php echo $name; ?>"/>
            </div>
        </div>

        <!-- Store Logo -->
        <!-- <div class="form-group">
            <div class="col-3 col-sm-12">
                <label class="form-label" for="logo"><?php _e('Store Logo', 'lsdcommerce');?></label>
            </div>
            <div class="col-5 col-sm-12">
                <img style="width:75px;" src="<?php echo $logo; ?>"/>
                <input class="form-input" type="text" style="display:none;" name="logo">
                <input type="button" value="<?php _e('Choose Image', 'lsdcommerce');?>" class="lsdc_admin_upload btn col-12">
            </div>
        </div> -->

        <!-- Provinsi -->
        <?php $states = array();?>
        <div class="form-group hidden">
            <div class="col-3 col-sm-12">
                <label class="form-label" for="state"><?php _e('Provinsi', 'lsdcommerce');?></label>
            </div>
            <div class="col-5 col-sm-12">
                <select class="form-select" name="state" id="form-state">
                    <option><?php _e('Pilih Provinsi', 'lsdcommerce');?></option>
                    <?php foreach ($id_states as $key => $item): ?>
                        <?php if (!in_array($item->province_id, $states)): ?>                            
                            <option value="<?php echo $item->province_id; ?>" <?php echo $state == $item->province_id ? 'selected' : ''; ?>><?php echo $item->province; ?></option>
                            <?php array_push($states, $item->province_id);?>
                        <?php endif;?>
                    <?php endforeach;?>
                </select>
            </div>
        </div>

        <!-- District -->
        <div class="form-group hidden">
            <div class="col-3 col-sm-12">
                <label class="form-label" for="district"><?php _e('Kabupaten', 'lsdcommerce');?></label>
            </div>

            <div class="col-5 col-sm-12">
                <select class="form-select" name="district" id="form-distric">
                    <option><?php _e('Pilih Kabupaten/Kota', 'lsdcommerce');?></option>
                    <?php foreach ($id_cities as $key => $city): ?>
                        <?php if ($city->city_id == $district): ?>
                            <option value="<?php echo esc_attr($district); ?>" selected><?php echo esc_attr($city->type . ' ' . $city->city_name); ?></option>
                            <?php break;?>
                        <?php endif;?>
                    <?php endforeach;?>
                </select>
            </div>
        </div>

        <!-- Address -->
        <div class="form-group">
            <div class="col-3 col-sm-12">
                <label class="form-label" for="address"><?php _e('Address', 'lsdcommerce');?></label>
            </div>
            <div class="col-5 col-sm-12">
                <textarea class="form-input" name="address" placeholder='<?php echo __('Jl.Jendral Sudirman no 40. 15560', 'lsdcommerce'); ?>' rows="3"><?php echo $address; ?></textarea>
            </div>
        </div>


        <!-- form checkbox control -->
        <!-- <div class="form-group">
          <label class="form-checkbox">
            <input type="checkbox">
            <i class="form-icon"></i> <?php _e('Saya setuju untuk membagikan data toko untuk keperluan pemetaan.')?> what we collect.
          </label>
        </div> -->

        <br>
        <button class="btn btn-primary" id="lsdcommerce_store_save" style="width:120px"><?php _e('Simpan', 'lsdcommerce');?></button>
    </form>
</section>

<script>
    var id_cities = <?=json_encode($id_cities)?>;
    var us_cities = <?=file_get_contents(LSDC_PATH . 'includes/cache/US-cities.json')?>;
    var us_states = <?=file_get_contents(LSDC_PATH . 'includes/cache/US-states.json')?>;

    (function($) {
        $('#form-state').on('change', function() {
            var value = $(this).val();
            // var intital_countries = $('#form-country').find(":selected").val();
            intital_countries = 'ID';
            switch (intital_countries) {
                case 'ID':
                    $('#form-distric').empty();
                    id_cities.forEach((e) => {
                        if (e.province_id === value) {
                            var html = `<option value="${e.city_id}">${e.type} ${e.city_name}</option>`;
                            $('#form-distric').append(html);
                        }
                    })
                    break;
                case 'US':
                    $('#form-distric').empty();
                    us_cities.forEach((e) => {
                        if (e.province_id === value) {
                            var html = `<option value="${e.city_id}">${e.type} ${e.city_name}</option>`;
                            $('#form-distric').append(html);
                        }
                    })
                    break;
            }
        })


    })(jQuery)
</script>