<?php
namespace LSDCommerce\Admin;

if (!defined('ABSPATH')) {
    exit;
}

class Switch_Options
{
    private $optionGroup;
    private $optionItems;
    private $default = [];

    /**
     * Add Options Group to Apperance
     *
     * @param string $slug
     * @param string $title
     * @param array $lists
     * @since 3.2.0
     */
    public static function addOptions(string $id, string $title, array $options)
    {
        /**
         * Source is Callback Variable from Filter
         */
        add_filter('lsdcommerce/admin/options', function ($source) use ($id, $title, $options) {
            $source[$id] = array(
                'title' => $title,
                'options' => $options
            );
            return $source;
        });
    }

    /**
     * Add Options Group to Apperance
     *
     * @param string $slug
     * @param string $title
     * @param array $lists
     * @since 3.2.0
     */
    public function addOptionsItem(string $id, array $new_item)
    {
        /**
         * Source is Callback Variable from Filter
         */
        add_filter('lsdcommerce/admin/options', function ($source) use ($id, $new_item) {
            #Checking Shortcode List ID Exist
            if (array_key_exists($id, $source)) {
                $selecting_shortcodes = $source[$id]['options'];
                $after = array_merge($selecting_shortcodes, $new_item);
                // Override Based on Slug ID
                $source[$id]['options'] = $after;
            }
            return $source;
        });
    }

    /**
     * Display Options in Apperance Page
     */
    public function listOptions()
    {
        // Checking License
        $tablist = array();

        if (has_filter('lsdcommerce/admin/options')) {
            $this->optionGroup = apply_filters('lsdcommerce/admin/options', $this->default);
        } else {
            $this->optionGroup = $this->default;
        }

        return $this->optionGroup;
    }

    // /**
    //  * Remove Tabs Admin
    //  *
    //  * @param string $id
    //  * @return array
    //  * @since 3.2.0
    //  */
    // public function remove($id)
    // {
    //     add_filter('lsdcommerce/admin/options', function ($source) use ($id) {
    //         unset($source[$id]);
    //         return $source;
    //     });
    // }

    /**
     * Getting Apperance Options based on ID Option
     * @param string $id
     * @since 3.2.0
     * 
     * Migration to lsdcommerce_appearance_options @3.2.5
     */
    public static function get(string $id)
    {
        $settings = get_option('lsdc_appearance_settings');

        if (!isset($settings['lsdc_' . $id])) return;

        return;
    }

    public function render()
    {
        $settings = get_option('lsdc_appearance_settings');
    ?>

        <!-- Font Settings -->
        <div class="form-group">
            <div class="col-3 col-sm-12">
                <label class="form-label" for="fontlist"><?php _e('Font', 'lsdcommerce'); ?></label>
            </div>
            <div class="col-4 col-sm-12">
                <select class="form-select" id="fontlist" name="lsdc_theme_font">
                    <option>Rubik</option>
                </select>
                <div id="selectedfont" class="hidden">
                    <?php echo !isset($settings['lsdc_theme_font']) ? 'Rubik' : esc_attr($settings['lsdc_theme_font']); ?>
                </div>
            </div>
        </div>

        <!-- Cache Font List -->
        <script>
            if (localStorage.getItem("lsdc_font_cache") == null || localStorage.getItem("lsdc_font_cache") == '') {
                jQuery.getJSON("https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyCoDdOKhPem_sbA-bDgJ_-4cVhJyekWk-U", function(fonts) {
                    var lsdc_font_cache = {};
                    for (var i = 0; i < fonts.items.length; i++) {
                        lsdc_font_cache[fonts.items[i].family] = fonts.items[i].files.regular;
                    }
                    localStorage.setItem("lsdc_font_cache", JSON.stringify(lsdc_font_cache));
                });
            } else {
                var lsdc_font_cache = JSON.parse(localStorage.getItem("lsdc_font_cache"));
                var selectedfont = jQuery('#selectedfont').text().trim();
                jQuery.each(lsdc_font_cache, function(index, value) {
                    jQuery('#fontlist')
                        .remove("option")
                        .append(jQuery((index == selectedfont) ? "<option selected></option>" : "<option></option>")
                            .attr("value", index)
                            .attr("style", "font-family:" + index + "; font-size: 16px")
                            .text(index));
                });
            }
        </script>

        <!-- Theme Color -->
        <div class="form-group">
            <div class="col-3 col-sm-12">
                <label class="form-label" for="theme-color"><?php _e('Warna Tema', 'lsdcommerce'); ?></label>
            </div>
            <div class="col-9 col-sm-12" style="height: 10px !important;">
                <input type="text" name="lsdc_theme_color" value="<?php echo isset($settings['lsdc_theme_color']) ? esc_attr($settings['lsdc_theme_color']) : '#ff0000'; ?>" class="lsdd-color-picker">
                <div class="color-picker" style="display: inline-block;z-index:999;"></div>
            </div>
        </div>

        <!-- DIsplay Switch Options -->
        <?php foreach ( array_reverse($this->listOptions()) as $key => $optionGroup) : ?>
            <div class="divider" data-content="<?php _e('Opsi', 'lsdcommerce'); ?> <?php echo esc_attr($optionGroup['title']); ?> "></div>
            <ul class="general-menu">
                <?php foreach ($optionGroup['options'] as $key => $option) : ?>
                    <?php if (isset($settings[$key])) : #Option Exist
                    ?>
                        <li>
                            <small style="float:right;"><?php echo esc_attr($option['desc']); ?></small>
                            <label class="form-switch">
                                <input name="<?php echo esc_attr($key); ?>" id="<?php echo esc_attr($key); ?>" type="checkbox" <?php echo ($settings[$key] == 'on') ? 'checked="checked"' : ''; ?>>
                                <i class="form-icon"></i><?php echo esc_attr($option['name']); ?>
                            </label>
                        </li>
                    <?php else : #Option not Exist on Settings 
                    ?>
                        <li>
                            <small style="float:right;"><?php echo esc_attr($option['desc']); ?></small>
                            <label class="form-switch">
                                <input name="<?php echo esc_attr($key); ?>" id="<?php echo esc_attr($key); ?>" type="checkbox">
                                <i class="form-icon"></i><?php echo esc_attr($option['name']); ?>
                            </label>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        <?php endforeach; ?>


        <!-- Appearance Extendable -->
        <?php
        /**
         * Hook for Displaying Custom Apperance Options
         * @since 4.0.0
         */
        do_action('lsdcommerce/appearance/options');
    }
}
?>