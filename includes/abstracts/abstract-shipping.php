<?php
namespace LSDCommerce;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Contract for Shipping
 */
abstract class Shipping_Template
{
    protected $id;
    protected $name;
    protected $type;
    protected $docs;

    /**
     * Creating Vertical Tab in Shipping Manager
     *
     * @return void
     */
    public function tab()
    {
        ?>
        <input type="radio" name="sections" id="<?php echo $this->id; ?>">
        <label data-linking="<?php echo esc_attr($this->id); ?>" class="tablabel form-switch" for="<?php echo $this->id; ?>">
            <span><?php echo ucfirst($this->type); ?></span>
            <p><?php echo esc_attr($this->name); ?></p>
        </label>
        <?php
}

    /**
     * Automated Create Header for Shippings
     * With Documentation URL and Switch Status On/Off
     *
     * @return void
     */
    public function header()
    {
        $lsdcommerce_shipping_settings = get_option('lsdcommerce_shipping_settings');

        if (!isset($lsdcommerce_shipping_settings[$this->id])) {
            $lsdcommerce_shipping_settings[$this->id] = 'off';
        }
        $status = $lsdcommerce_shipping_settings[$this->id] == 'on' ? 'on' : 'off';?>

        <div class="lsdd-notification-status">
            <h5>
                <?php echo $this->name; ?>
                <a href="<?php echo $this->docs[lsdd_get_country()]; ?>" target="_blank" class="btn btn-primary" style="margin-left:10px; border-radius: 20px;padding: 5px 25px;">
                    <?php _e('Learn', 'lsddonation');?>
                </a>
            </h5>
            <div class="form-group">
                <label class="form-switch" style="width:250px">
                <input type="checkbox" id="<?php echo $this->id . '_status'; ?>" <?php echo ($status == 'on') ? 'checked' : ''; ?>>
                    <i class="form-icon"></i><?php _e('Enable', 'lsddonation');?>  <?php echo $this->name; ?>
                </label>
            </div>
        </div>
    <?php
    }

    public function status()
    {
        $lsdcommerce_shipping_settings = get_option('lsdcommerce_shipping_settings');

        if (!isset($lsdcommerce_shipping_settings[$this->id])) {
            $lsdcommerce_shipping_settings[$this->id] = 'off';
        }
        return $lsdcommerce_shipping_settings[$this->id] == 'on' ? true : false;
    }

    /**
     * Method for Create Setting for Shippings
     *
     * @return void
     */
    public function manage()
    {}


    /**
     * Methode for Logging Status Shippings
     *
     * @return void
     */
    abstract public function log($reciever, $event, $message);

    /**
     * Method for Testing Shipping Sending
     *
     * @return void
     */
    public function test()
    {}
}
?>