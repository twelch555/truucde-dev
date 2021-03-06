<?php
namespace TruUcde;

use TwTestUtil;
use WP_Error;
use WP_UnitTestCase;
use function add_user_to_blog;
use function update_site_option;

class MsTruUcdeTest extends WP_UnitTestCase {
	
	/*
	 * Define all the things
	 */
	public $admin_user;
	public $editor_user;
	public $blog_2;
	public $blog_3;

	public $Target_code  = 'user_email';
	public $Black_msg    = 'You cannot use that email address to signup. We are having problems with them blocking some of our email. Please use another email provider.';
	public $White_msg    = 'Sorry, that email address is not allowed!';
	public $Target_data  = 'User email things.';
	public $Another_code = 'user_foolishness';
	public $Another_msg  = 'The eagle lands at midnight';
	public $Another_data = 'Spy games';
	
	
	/*
	 * Test setup
	 */
	public function setUp() {
		parent::setUp();
		// add additional users: user 1 - superadmin already created
		$this->admin_user  = $this->factory->user->create( array( 'role' => 'administrator' ) );
		$this->editor_user = $this->factory->user->create( array( 'role' => 'editor' ) );
		
		// add additional subsites
		$this->blog_2 = $this->factory->blog->create();
		$this->blog_3 = $this->factory->blog->create();
	}

	/*
	 * Test Teardown
	 */
	public function tearDown() {
		parent::tearDown();
	}

	/**
	 * Setup test: basic sanity check
	 */
	public function testBasic() {
		$this->assertTrue( true );
	}

	/**
	 * Check for a plugin constant to ensure connection with plugin
	 */
	public function test_plugin_constant() {
		$this->assertSame( 'user_email', TARGET_ERROR_CODE );
	}

	/*
	 * Make sure filter is registered
	 */
	public function test_for_filter() {
		$this->assertEquals(
			10,
			has_filter( 'wpmu_validate_user_signup', 'TruUcde\on_loaded' )
		);

	}

	/*
	 * Check responses from user_can_add() for:
	 * No user logged in, superadmin, siteadmin and editor.
	 */
	public function test_user_can_add() {
		global $current_user;
		
		// No user logged in
		$this->assertFalse( user_can_add() );
		
		// Super admin
		wp_set_current_user( 1 );
		$this->assertTrue( user_can_add() );
		
		// Admin
		switch_to_blog( $this->blog_2 );
		wp_set_current_user( $this->admin_user );
		$current_user->add_cap( 'promote-users' );
		$this->assertTrue( user_can_add() );
		
		// Editor
		wp_set_current_user( $this->editor_user );
		$this->assertFalse( user_can_add() );
	}

	/*
	 * Check if error object needs processing
	 */
	
	// Empty Error object
	public function test_e_needs_processing_empty() {
		$test_error_obj = new WP_Error();
		$result         = e_needs_processing( $test_error_obj );
		$this->assertFalse( $result );

	}

	// Non target error/message
	public function test_e_needs_proc_non_trgt_msg() {
		$test_error_obj = new WP_Error();
		$test_error_obj->add( $this->Another_code, $this->Another_msg, $this->Another_data );
		$result = e_needs_processing( $test_error_obj );
		$this->assertFalse( $result );

	}

	// Black list message
	public function test_e_needs_proc_blk_msg() {
		$test_error_obj = new WP_Error();
		$test_error_obj->add( $this->Another_code, $this->Another_msg, $this->Another_data );
		$test_error_obj->add( $this->Target_code, $this->Black_msg, $this->Target_data );
		$result = e_needs_processing( $test_error_obj );
		$this->assertTrue( $result );

	}

	// White list message
	public function test_e_needs_proc_white_msg() {
		$test_error_obj = new WP_Error();
		$test_error_obj->add( $this->Another_code, $this->Another_msg, $this->Another_data );
		$test_error_obj->add( $this->Target_code, $this->White_msg, $this->Target_data );
		$result = e_needs_processing( $test_error_obj );
		$this->assertTrue( $result );
	}

	// Both messages
	public function test_e_needs_proc_both_msg() {
		$test_error_obj = new WP_Error();
		$test_error_obj->add( $this->Another_code, $this->Another_msg, $this->Another_data );
		$test_error_obj->add( $this->Target_code, $this->Black_msg, $this->Target_data );
		$test_error_obj->add( $this->Target_code, $this->White_msg, $this->Target_data );
		$result = e_needs_processing( $test_error_obj );
		$this->assertTrue( $result );
	}

	/*
	 * Test onload function for each user type
	 */
	public function test_on_load_super_admin() {
		// Setup user
		global $current_user;
		wp_set_current_user( 1 );
		$current_user->add_cap( 'promote-users' );

		// Set site options: limited_email_domains & banned_email_domains
		update_site_option( 'limited_email_domains', 'tru.ca' );
		update_site_option( 'banned_email_domains', 'gmail.com' );

		// Run tests
		$result = wpmu_validate_user_signup( 'test1', 'twelch@tru.ca' );
		$this->assertEmpty( $result['errors']->errors );

		$result = wpmu_validate_user_signup( 'test2', 'troy@twelch.ca' );
		$this->assertEmpty( $result['errors']->errors );

		$result = wpmu_validate_user_signup( 'test3', 'twelch555@gmail.com' );
		$this->assertEmpty( $result['errors']->errors );

	}

	public function test_on_load_admin() {
		// Setup user
		global $current_user;
		switch_to_blog( $this->blog_2 );
		wp_set_current_user( $this->admin_user );
		add_user_to_blog( 2, 2, 'administrator' );
		TwTestUtil::set_admin_role( true );
		$current_user->add_cap( 'promote-users' );

		// Set site options: limited_email_domains & banned_email_domains
		update_site_option( 'limited_email_domains', 'tru.ca' );
		update_site_option( 'banned_email_domains', 'gmail.com' );

		// Run tests
		$result = wpmu_validate_user_signup( 'test1', 'twelch@tru.ca' );
		$this->assertEmpty( $result['errors']->errors );

		$result = wpmu_validate_user_signup( 'test2', 'troy@twelch.ca' );
		$this->assertEmpty( $result['errors']->errors );

		$result = wpmu_validate_user_signup( 'test3', 'twelch555@gmail.com' );
		$this->assertEmpty( $result['errors']->errors );

	}
	
	public function test_on_load_editor() {
		// Setup user
		global $current_user;
		switch_to_blog( $this->blog_2 );
		wp_set_current_user( $this->editor_user );
		add_user_to_blog( 2, 3, 'editor' );
		
		// Set site options: limited_email_domains & banned_email_domains
		update_site_option( 'limited_email_domains', 'tru.ca' );
		update_site_option( 'banned_email_domains', 'gmail.com' );
		
		// Run tests
		$result = wpmu_validate_user_signup( 'test1', 'twelch@tru.ca' );
		print_r($result);
		$this->assertEmpty( $result['errors']->errors );
		
		$result = wpmu_validate_user_signup( 'test2', 'troy@twelch.ca' );
		$this->assertContains( $this->Target_code, $result['errors']->get_error_codes() );
		$this->assertContains( $this->White_msg, $result['errors']->get_error_messages( $this->Target_code ) );
		
		
		$result = wpmu_validate_user_signup( 'test3', 'twelch555@gmail.com' );
		print_r($result);
		$this->assertContains( $this->Target_code, $result['errors']->get_error_codes() );
		$this->assertContains( $this->White_msg, $result['errors']->get_error_messages( $this->Target_code ) );
		$this->assertContains( $this->Black_msg, $result['errors']->get_error_messages( $this->Target_code ) );
		
	}
}
