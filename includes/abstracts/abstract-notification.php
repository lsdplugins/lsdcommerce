<?php
namespace LSDCommerce;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Contract for Notification
 */
abstract class Notification_Template
{
    protected $id;
    protected $name;
    protected $type;
    protected $docs;

    /**
     * Creating Vertical Tab in Notification Manager
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
     * Automated Create Header for Notifications
     * With Documentation URL and Switch Status On/Off
     *
     * @return void
     */
    public function header()
    {
        $lsdc_notification_status = get_option('lsdcommerce_notification_status');

        if (!isset($lsdc_notification_status[$this->id])) {
            $lsdc_notification_status[$this->id] = 'off';
        }
        $status = $lsdc_notification_status[$this->id] == 'on' ? 'on' : 'off';?>

        <div class="lsdc-notification-status">
            <h5>
                <?php echo $this->name; ?>
                <a href="<?php echo $this->docs[lsdc_get_country()]; ?>" target="_blank" class="btn btn-primary" style="margin-left:10px; border-radius: 20px;padding: 5px 25px;">
                    <?php _e('Pelajari', 'lsdcommerce');?>
                </a>
            </h5>
            <div class="form-group">
                <label class="form-switch" style="width:250px">
                <input type="checkbox" id="<?php echo $this->id . '_status'; ?>" <?php echo ($status == 'on') ? 'checked' : ''; ?>>
                    <i class="form-icon"></i><?php _e('Hidupkan', 'lsdcommerce');?>  <?php echo $this->name; ?>
                </label>
            </div>
        </div>
    <?php
    }

    public function status()
    {
        $lsdc_notification_status = get_option('lsdcommerce_notification_status');

        if (!isset($lsdc_notification_status[$this->id])) {
            $lsdc_notification_status[$this->id] = 'off';
        }
        return $lsdc_notification_status[$this->id] == 'on' ? true : false;
    }

    /**
     * Method for Create Setting for Notifications
     *
     * @return void
     */
    public function manage()
    {}


    /**
     * Methode for Logging Status Notifications
     *
     * @return void
     */
    abstract public function log($reciever, $event, $message);

    /**
     * Method for Testing Notification Sending
     *
     * @return void
     */
    public function test()
    {}
}
?>