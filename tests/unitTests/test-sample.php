<?php
/**
 * Class SampleTest
 *
 * @package Truucde
 */

/**
 * Sample test case.
 */
class SampleTest extends WP_UnitTestCase {

	/**
	 * A single example test.
	 */
	public function test_sample() {
		// Replace this with some actual testing code.
		$this->assertTrue( true );
	}
	
	/**
	 * A second sample
	 */
	public function test_sample_string() {
		$string = 'Unit tests are sweet';
		
		$this->assertEquals( 'Unit tests are sweet', $string );
	}
}


