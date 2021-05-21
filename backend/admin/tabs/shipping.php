<?php
use LSDDonation\Common\i18n;

/*********************************************/
/* Displaying Institution Menu
/* wp-admin -> LSDDonation -> Institution
/********************************************/

if (!defined('ABSPATH')) {
    exit;
}
?>

<section id="settings" class="form-horizontal">
    <form>
        <?php
        $countries = i18n::get_countries();

        $settings = get_option('lsdd_institution_settings');
        $institution_name = isset($settings['institution_name']) ? esc_attr($settings['institution_name']) : '';
        $institution_logo = isset($settings['institution_logo']) ? esc_url($settings['institution_logo']) : 'https://plugin.lasida.work/demo/wp-content/uploads/2020/04/LSD-DONASI-LOGO.png';

        $institution_country = isset($settings['institution_country']) ? esc_attr($settings['institution_country']) : '';
        $institution_state = isset($settings['institution_state']) ? esc_attr($settings['institution_state']) : '';
        $institution_district = isset($settings['institution_district']) ? esc_attr($settings['institution_district']) : '';
        $institution_address = isset($settings['institution_address']) ? esc_attr($settings['institution_address']) : '';

        $id_states = json_decode(file_get_contents(LSDD_PATH . 'assets/cache/ID-states.json'));
        $id_cities = json_decode(file_get_contents(LSDD_PATH . 'assets/cache/ID-cities.json'));
        ?>

        <!-- Name -->
        <div class="form-group">
            <div class="col-3 col-sm-12">
                <label class="form-label" for="institution_name"><?php _e('Name', 'lsddonation');?></label>
            </div>
            <div class="col-5 col-sm-12">
                <input type="text" class="form-input" name="institution_name" placeholder="Yayasan Indonesia" value="<?php echo $institution_name; ?>"/>
            </div>
        </div>

        <!-- Logo -->
        <div class="form-group">
            <div class="col-3 col-sm-12">
                <label class="form-label" for="institution_logo"><?php _e('Logo', 'lsddonation');?></label>
            </div>
            <div class="col-5 col-sm-12">
                <img style="width:75px;" src="<?php echo $institution_logo; ?>"/>
                <input class="form-input" type="text" style="display:none;" name="institution_logo">
                <input type="button" value="<?php _e('Choose Image', 'lsddonation');?>" class="lsdd_admin_upload btn col-12">
            </div>
        </div>

        <!-- Country -->
        <div class="form-group hidden">
            <div class="col-3 col-sm-12">
                <label class="form-label" for="institution_country"><?php _e('Country', 'lsddonation');?></label>
            </div>

            <div class="col-5 col-sm-12">
                <select class="form-select" name="institution_country" id="form-country">
                    <!-- lsddonation-admin.js : onChange trigger result States -->
                    <option disabled selected><?php _e('Select a country', 'lsddonation')?></option>
                    <?php foreach ($countries as $key => $country): ?>
                        <option value="<?php echo $country['iso2']; ?>" <?php echo ($institution_country == $country['iso2']) ? 'selected' : ''; ?>><?php echo $country['name']; ?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>

        <!-- State -->
        <?php $states = array();?>
        <div class="form-group hidden">
            <div class="col-3 col-sm-12">
                <label class="form-label" for="institution_state"><?php _e('State', 'lsddonation');?></label>
            </div>
            <div class="col-5 col-sm-12">
                <select class="form-select" name="institution_state" id="form-state">
                    <option><?php _e('Select a state', 'lsddonation');?></option>
                    <?php foreach ($id_states as $key => $state): ?>
                        <?php if (!in_array($state->province_id, $states)): ?>
                            <option value="<?php echo $state->province_id; ?>" <?php echo ($institution_state == $state->province_id) ? 'selected' : ''; ?>><?php echo $state->province; ?></option>
                            <?php array_push($states, $state->province_id);?>
                        <?php endif;?>
                    <?php endforeach;?>
                </select>
            </div>
        </div>

        <!-- District -->
        <div class="form-group hidden">
            <div class="col-3 col-sm-12">
                <label class="form-label" for="institution_district"><?php _e('District', 'lsddonation');?></label>
            </div>

            <div class="col-5 col-sm-12">
                <select class="form-select" name="institution_district" id="form-distric">
                    <option><?php _e('Select district', 'lsddonation');?></option>
                    <?php foreach ($id_cities as $key => $city): ?>
                        <?php if ($city->city_id == $institution_district): ?>
                            <option value="<?php echo esc_attr($institution_district); ?>" selected><?php echo esc_attr($city->type . ' ' . $city->city_name); ?></option>
                            <?php break;?>
                        <?php endif;?>
                    <?php endforeach;?>
                </select>
            </div>
        </div>

        <!-- Address -->
        <div class="form-group">
            <div class="col-3 col-sm-12">
                <label class="form-label" for="institution_address"><?php _e('Address', 'lsddonation');?></label>
            </div>
            <div class="col-5 col-sm-12">
                <textarea class="form-input" name="institution_address" placeholder='<?php echo __('Jl.Jendral Sudirman no 40. 15560', 'lsddonation'); ?>' rows="3"><?php echo $institution_address; ?></textarea>
            </div>
        </div>


        <!-- form checkbox control -->
        <!-- <div class="form-group">
          <label class="form-checkbox">
            <input type="checkbox">
            <i class="form-icon"></i> <?php _e('I agree to contribute instituion data for better experience and mapping institution in Indonesia.')?> what we collect.
          </label>
        </div> -->

        <br>
        <button class="btn btn-primary" id="lsdd_institution_settings_save" style="width:120px"><?php _e('Simpan', 'lsddonation');?></button>
    </form>
</section>

<script>
    var id_cities = <?=json_encode($id_cities)?>;
    var us_cities = <?=file_get_contents(LSDD_PATH . 'assets/cache/US-cities.json')?>;
    var us_states = <?=file_get_contents(LSDD_PATH . 'assets/cache/US-states.json')?>;

    (function($) {
        $('#form-state').on('change', function() {
            var value = $(this).val();
            var intital_countries = $('#form-country').find(":selected").val();
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