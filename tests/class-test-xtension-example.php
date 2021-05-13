<?php
/**
 * Class Test Extension
 * License Manager
 */

/**
 * Main Test Case
 */
class TestExtension_Example extends WP_UnitTestCase {


	function test_version()
	{
		$this->assertEquals("1.0.0", LSDC_EXT_VERSION ); 
		
	}
}
