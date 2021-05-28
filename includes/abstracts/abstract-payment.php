<?php
namespace LSDCommerce;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Abstraction for Payment Method
 * Design Pattern : Template Method CMMIW
 */
abstract class Payment_Template
{
    /**
     * Payment ID
     *
     * @var string
     */
    protected $id = null;

    /**
     * Payment Name
     *
     * @var string
     */
    protected $name = null;

    /**
     * Payment Description
     *
     * @var string
     */
    protected $description = null;

    /**
     * Payment Log
     *
     * @var url
     */
    protected $logo = null;

    /**
     * Payment Group
     *
     * @var string
     */
    protected $group = null;

    /**
     * Payment Group
     *
     * @var string
     */
    protected $group_name = null;

    /**
     * Payment Documtenation
     *
     * @var array
     */
    protected $docs = null;

    /**
     * Payment Template
     *
     * @var string
     */
    protected $template = null;

    /**
     * Payment Country
     *
     * @var string
     */
    protected $country = 'global';    

    /**
     * Manual Tag
     */
    const MANUAL = 'manual';

    /**
     * Automatic Tag
     */
    const AUTOMATIC = 'automatic';

    /**
     * Get Description Settings
     * 
     * @return void
     */
    public function get_description( string $payment_id = null )
    {
        return esc_attr($this->settings[$id]['description']);
    }

    /**
     * Get Payment Status
     * 
     * @return void
     */
    public function get_status( string $payment_id = null )
    {
        $status = get_option('lsdd_payment_status') != null ? get_option('lsdd_payment_status') : array();
        $id = $payment_id ? $payment_id : $this->id;
        $status = isset($status[$id]) ? $status[$id] == 'on' ? 'on' : 'off' : 'off';
        return $status;
    }

    /**
     * Get Confirmation Type
     * Manual or Automatic
     *
     * @return void
     */
    public function get_confirmation( string $payment_id = null )
    {
        $id = $payment_id ? $payment_id : $this->id;
        return isset($this->settings[$id]['confirmation']) ? $this->settings[$id]['confirmation'] : self::MANUAL;
    }

    /**
     * Payment Instruction with Output HTML
     * will be send in notification email
     *
     * @param integer $report_id
     * @param string $event
     * @return html
     */
    abstract public function notification_html( object $report_id, string $event, string $gateway );

    /**
     * Payment Instruction with Output Text
     * will be send in notification whatsapp | sms
     *
     * @param integer $report_id
     * @param string $event
     * @return text
     */
    abstract public function notification_text( object $report_id, string $event, string $gateway );

    // /**
    //  * Payment Instruction with Output JSON
    //  * will be send in notification for integration like integromat | zapier
    //  *
    //  * @param integer $report_id
    //  * @param string $event
    //  * @return json
    //  */
    // abstract public function notification_json( int $report_id, string $event );

    /**
     * Payment on Confirmation Page
     * will be output in thankyou page
     *
     * @param integer $report_id
     * @return html
     */
    abstract public function instruction( int $report_id, $report );

    /**
     * Manage Payment Settings
     * used for settings payment methods
     *
     * @return void
     */
    abstract public function manage( string $payment_id );
}
?>