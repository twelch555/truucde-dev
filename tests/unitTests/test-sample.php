<?php
/**
 * Class SampleTest
 *
 * @package Truucde
 */

/**
 * Sample test case.
 */
class SampleTest extends \WP_Mock\Tools\TestCase {
	
	/**
	 * Test setup
	 */
	public function setUp() : void {
		\WP_Mock::setUp();
	}
	
	public function tearDown() : void {
		\WP_Mock::tearDown();
	}
	
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


