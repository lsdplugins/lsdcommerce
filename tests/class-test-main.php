<?php
/**
 * Class Test_Sample
 *
 * @package My_Plugin
 */

/**
 * Main Test Case
 */
class TestCore extends WP_UnitTestCase {

	/**
	 * A single example test.
	 */
	function test_main() {
		// Replace this with some actual testing code.
		$this->assertTrue( defined('LSDC_VERSION') );
	}

	function test_version()
	{
		$this->assertEquals("0.0.1", LSDC_VERSION ); 
	}

}
