<?php
/**
 * Class TruUcde
 *
 * @package Truucde
 */

	use TruUcde\TruUcde;
	// use \WP_Error; // need to create empty error block below.
	
	
	/**
	 * Sample test case.
	 */
class TruUcdeTest extends \WP_Mock\Tools\TestCase {

	/**
	 * Test setup
	 */
	public function setUp() : void {
		\WP_Mock::setUp();
	}

	/**
	 * Test teardown
	 */
	public function tearDown() : void {
		\WP_Mock::tearDown();
	}

	/**
	 * Initiate an instance of class to be tested
	 *
	 * @return TruUcde
	 */
	private function get_subject() {
		$test_subject = new TruUcde();
		return $test_subject;
	}

	/**
	 * Test that hooks are being initialized.
	 *
	 * @group skipped
	 */
	public function test_it_adds_hooks() {
		// get instance of the subject.
		$test_subject = $this->get_subject();

		// ensure the filter is added.
		\WP_Mock::expectFilterAdded(
			'wpmu_validate_user_signup',
			array( $test_subject, 'on_loaded' )
		);

		// Now test the init hook method of the class to check if the filter is added.
		$test_subject->init();
	}
	
	/**
	 * Tests that user checks are working as they should
	 * TT
	 */
	public function test_user_can_add_TT() {
		// Get instance of the subject.
		$test_subject = $this->get_subject();

		// Mock 'is_user_logged' in and 'current_user_can'.
		\WP_Mock::userFunction(
			'is_user_logged_in',
			array(
				'times'  => 1,
				'return' => true,
			)
		);

		\WP_Mock::userFunction(
			'current_user_can',
			array(
				'times'  => 1,
				'args'   => array( 'promote-users' ),
				'return' => true,
			)
		);

		// Run it through the function.
		$result = $test_subject->user_can_add();

		// And assert
		$this->assertSame( true, $result );
	}
	
	/**
	 * Tests that user checks are working as they should
	 * TF
	 */
	public function test_user_can_add_TF() {
		// Get instance of the subject.
		$test_subject = $this->get_subject();
		
		// Mock 'is_user_logged' in and 'current_user_can'.
		\WP_Mock::userFunction(
			'is_user_logged_in',
			array(
				'times'  => 1,
				'return' => true,
			)
		);
		
		\WP_Mock::userFunction(
			'current_user_can',
			array(
				'times'  => 1,
				'args'   => array( 'promote-users' ),
				'return' => false,
			)
		);
		
		// Run it through the function.
		$result = $test_subject->user_can_add();
		
		// And assert
		$this->assertSame( false, $result );
	}
	
	/**
	 * Tests that user checks are working as they should
	 * FT
	 */
	public function test_user_can_add_FT() {
		// Get instance of the subject.
		$test_subject = $this->get_subject();
		
		// Mock 'is_user_logged' in and 'current_user_can'.
		\WP_Mock::userFunction(
			'is_user_logged_in',
			array(
				'times'  => 1,
				'return' => false,
			)
		);
		
		\WP_Mock::userFunction(
			'current_user_can',
			array(
				'times'  => 0,
				'args'   => array( 'promote-users' ),
				'return' => true,
			)
		);
		
		// Run it through the function.
		$result = $test_subject->user_can_add();
		
		// And assert
		$this->assertSame( false, $result );
	}
	
	/**
	 * Tests that user checks are working as they should
	 * FF
	 */
	public function test_user_can_add_FF() {
		// Get instance of the subject.
		$test_subject = $this->get_subject();
		
		// Mock 'is_user_logged' in and 'current_user_can'.
		\WP_Mock::userFunction(
			'is_user_logged_in',
			array(
				'times'  => 1,
				'return' => false,
			)
		);
		
		\WP_Mock::userFunction(
			'current_user_can',
			array(
				'times'  => 0,
				'args'   => array( 'promote-users' ),
				'return' => false,
			)
		);
		
		// Run it through the function.
		$result = $test_subject->user_can_add();
		
		// And assert
		$this->assertSame( false, $result );
	}
	
	public function test_e_needs_processing() {
		// Not an error object (8/26 cases).
		$test_subject = $this->get_subject();
		$original_error = $this->get_subject();
		
		\WP_Mock::userFunction(
			'is_wp_error',
			array(
				'times'  => 1,
				'return' => false,
			)
		);
		
		$result = $test_subject->e_needs_processing($original_error);
		
		$this->assertFalse($result);
		
		// Is error object -> is empty (4 cases)
		
		// Each of the blacklist messages (4 cases)
	}

}


