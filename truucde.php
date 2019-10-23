<?php
	/*
	Plugin name: User create domain exception
	Description: This plugin enables super/site admins to create users outside of white/black list restrictions
	Author: Troy Welch twelch@tru.ca
	 */
	
	namespace truucde;
	
	function truucde( $result ){
		// Error code and messages variables
		$target_error_code = 'user_email';
		$black_list_msg = 'You cannot use that email address to signup. We are having problems with them blocking some of our email. Please use another email provider.';
		$white_list_msg = 'Sorry, that email address is not allowed!';
		
		// Get error messages from $result
		$error_messages = $result['errors']->get_error_messages( $target_error_code );
		
		// Black key/White key
		$black_key=array_search( ( $black_list_msg ), $error_messages );
		$white_key=array_search( ( $white_list_msg ), $error_messages );
		
		// Check if user is logged in and Super/Site Admin
		if ( is_user_logged_in() && current_user_can( 'promote-users' ) ) {
			
			// Check if error message array is empty or does not contain black/white list error messages
			if ( empty ( $error_messages )
			     || false == $black_key
			        && false == $white_key
			) {
				return $result;
			}
			// remember any error data
			$data = $result['errors']->get_error_data( $target_error_code );
			// Remove all target errors
			$result['errors']->remove( $target_error_code );
			// Add back errors that we are not interested in
			foreach ( $error_messages as $index => $message ) {
				if ( ( $index !== $black_key ) && ( $index !== $white_key ) ) {
					$result['errors']->add( $target_error_code, $message );
				}
			}
			// restore any target error data that was present
			if ( ! empty( $data ) ) {
				$result['errors']->add_data( $data, $target_error_code );
			}
		}
	
	return $result;
	}
	// add_filter( 'wpmu_validate_user_signup', 'tru_ucde' );
	
?>