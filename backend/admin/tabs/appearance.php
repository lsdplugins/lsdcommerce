<?php
use LSDCommerce\Admin;

/*********************************************/
/* Displaying Appearance Menu
/* wp-admin -> LSDCommerce -> Appearance
/********************************************/

if (!defined('ABSPATH')) {
    exit;
}

?>

<div class="entry columns col-gapless">

  <!-- Appearance Switch Options -->
  <section id="appearance" class="column col-8 form-horizontal">
    <form>
    <?php
    require_once LSDC_PATH . 'backend/admin/class-switch-options.php';
    $options = new Admin\Switch_Options;
    $options->render();
    ?>
    </form>

    <br>

    <button class="btn btn-primary w-120" id="lsdd-admin-apperance-save">
      <?php _e('Simpan', 'lsdcommerce');?>
    </button>
  </section>

  <!-- Sidebar - Shortcodes -->
  <section class="column col-4">
    <a class="btn btn-primary " target="_blank" href="https://learn.lsdplugins.com/docs/umum/wordpress/apa-itu-shortcodes/">
      <?php _e('Learn how to use shortcode', 'lsdcommerce');?>
    </a>

    <?php
    require_once LSDC_PATH . 'backend/admin/class-shortcode-lists.php';
    Admin\Shortcode_Lists::render();
    ?>
  </section>

</div>