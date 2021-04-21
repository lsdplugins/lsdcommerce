<?php
/**
 * Class Test_Sample
 *
 * @package My_Plugin
 */

/**
 * Sample test case.
 */
class TestCore extends WP_UnitTestCase {

	/**
	 * A single example test.
	 */
	function test_main() {
		// Replace this with some actual testing code.
		$this->assertTrue( class_exists( 'LSDCommerce' ) );
	}

	function test_getCurrency()
	{
		$result = lsdc_currency_get();
		$this->assertEquals("IDR", $result); 
	}
}
