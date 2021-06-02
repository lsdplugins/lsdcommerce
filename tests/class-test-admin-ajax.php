<?php
/**
 * Class Test_Sample
 *
 * @package My_Plugin
 */

/**
 * Main Test Case
 */
class AdminAJAX extends WP_UnitTestCase
{

    public function test_store_settings()
    {}

    public function test_appearance_options()
    {}

    public function test_appearance_shortcodeinfo()
    {}

    public function test_notification_email()
    {}

    public function test_notification_email_log()
    {}

    public function test_notification_email_order()
    {}

    public function test_notification_email_completed()
    {}

    public function test_notification_email_settings()
    {}

    public function test_notification_senderpad()
    {}

    public function test_notification_webhook()
    {}

    /**
     * A single example test.
     */
    public function test_payment_status()
    {
        // Replace this with some actual testing code.
        // Fulfill requirements of the callback here...
        $this->_setRole('administrator');

        $_POST['_wpnonce'] = wp_create_nonce('my_nonce');
        $_POST['option_value'] = 'yes';

        try {
            $this->_handleAjax('my_ajax_action');
        } catch (WPAjaxDieStopException $e) {
            // We expected this, do nothing.
        }

        // Check that the exception was thrown.
        $this->assertTrue(isset($e));

        // The output should be a 1 for success.
        $this->assertEquals('1', $e->getMessage());

        $this->assertEquals('yes', get_option('some_option'));
    }

    public function test_payment_sorting()
    {}

    public function test_payment_manage()
    {}

    public function test_shipping_email()
    {}

    public function test_shipping_rajaongkir()
    {}

    public function test_general_settings()
    {}

}
