<?php

/*
Plugin name: User create domain exception
Description: This plugin enables super/site admins to create users outside of white/black list restrictions
Author: Troy Welch twelch@tru.ca
*/

namespace truucde;

function tru_ucde( $result ) {
	// Get error object from $result
	$original_error = $result['errors'];
	$new_error      = new \WP_Error();

	// Error code and messages
	$target_error_code = 'user_email';
	$black_list_msg    = 'You cannot use that email address to signup. We are having problems with them blocking some of our email. Please use another email provider.';
	$white_list_msg    = 'Sorry, that email address is not allowed!';

	// Check if user is logged in and Super/Site Admin
	if ( is_user_logged_in() && current_user_can( 'promote-users' ) ) {

		// Check if error object is empty
		if ( is_wp_error( $original_error ) // is an error object
		     && ! empty( $original_error->errors ) // and is not empty and contains black/white list error
		     && ( in_array ( $black_list_msg, $original_error->get_error_messages($target_error_code), true )
		       || in_array ( $white_list_msg, $original_error->get_error_messages($target_error_code), true ) )
		) {
			// Run through error codes and messages keep all but white/black list errors
			foreach ( $original_error->get_error_codes() as $code ) {
				foreach ( $original_error->get_error_messages( $code ) as $message ) {
					if ( $code !== $target_error_code ) {
							$new_error->add( $code, $message );
					} else {
						// Don't add the white/black list messages
						if ( $message !== $black_list_msg && $message !== $white_list_msg ) {
								$new_error->add( $code, $message );
						}
					}
				}
				// add data entries for original to new error object
				if ( ! is_null( $original_error->get_error_data( $code ) ) ) {
					$new_error->add_data( $original_error->get_error_data( $code ), $code );
				}
			}
			// Put the new error object back in $result
			$result['errors'] = $new_error;
		}
	}

	return $result;
}

add_filter( 'wpmu_validate_user_signup', 'truucde\tru_ucde' );
