<?php
/**
 * Checkout functions for installation and upgrade routines.
 *
 * Why in a theme? Because we had to change the name from Startup to Checkout
 * due to a trademark conflict.
 *
 * This upgrade routine concept inspired by and borrowed from Easy Digital Downloads.
 *
 * @package Checkout
 * @since 1.1
 */

/**
 * Displays upgrade notices
 *
 * @since 1.1
 */
function checkout_display_upgrade_notices() {

	// Don't show upgrade notices on the upgrade page
	if( isset( $_GET['page'] ) && 'checkout-upgrades' == $_GET['page'] ) {
		return;
	}

	// Don't show upgrade notices on installs that haven't had Startup
	if( ! get_option( 'theme_mods_startup') ) {
		return;
	}

	$checkout_version = get_option( 'checkout_version' );

	if( ! $checkout_version ) {
		// Added in version 1.1
		$checkout_version = '1.1';
	}

	if( version_compare( $checkout_version, '1.1', '<=' ) && ! get_option( 'checkout_startup_conversion' ) ) {
		printf(
			'<div class="error"><p>' . esc_html__( 'Checkout needs to migrate your data from Startup. Click %shere%s to start the migration.', 'checkout' ) . '</p></div>',
			'<a href="' . esc_url( admin_url( 'themes.php?page=checkout-upgrades&checkout-upgrade=startup_migration' ) ) . '">',
			'</a>'
		);
	}
}
add_action( 'admin_notices', 'checkout_display_upgrade_notices' );


/**
 * Adds the upgrades page
 *
 * @since 1.1
 */
function checkout_add_upgrades_page() {
	add_submenu_page( NULL, __( 'Checkout Upgrades Page', 'checkout' ), __( 'Checkout Upgrades Page', 'checkout' ), 'manage_options', 'checkout-upgrades', 'checkout_render_upgrades_page' );
}
add_action( 'admin_menu', 'checkout_add_upgrades_page' );


/**
 * Renders the upgrades page
 *
 * @since 1.1
 */
function checkout_render_upgrades_page() {
?>

	<div class="wrap">
		<h2><?php _e( 'Checkout - Upgrades Page', 'checkout' );?></h2>
		<div class="checkout-upgrade-status">
			<p>
				<?php _e( 'The migration process has started. You will be automatically redirected when the migration is finished.', 'checkout' ); ?>
			</p>
		</div>
		<script type="text/javascript">
			setTimeout(function() { document.location.href = "themes.php?page=checkout-upgrades&checkout-upgrade=startup_migration"; }, 4000);
		</script>

		<script type="text/javascript">
			jQuery( document ).ready( function() {
				// Trigger upgrades on page load
				var data = { action: 'checkout_trigger_upgrades' };
				jQuery.post( ajaxurl, data, function (response) {
					if( response == 'complete' ) {
						document.location.href = 'themes.php?page=checkout-license'; // Redirect to Getting Started page
					}
				});
			});
		</script>
	</div>
<?php
}


/**
 * Triggers upgrade functions
 * @since 1.1
 */
function checkout_trigger_upgrades() {

	if( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$checkout_version = get_option( 'checkout_version' );

	if( empty( $checkout_version ) ) {
		// Added in version 1.1
		$checkout_version = '1.1';
		add_option( 'checkout_version', $checkout_version );

		checkout_v11_upgrades();
	}

	$current_version = wp_get_theme( 'checkout' )->get( 'Version' );

	update_option( 'checkout_version', $current_version );

	if ( DOING_AJAX ) {
		die( 'complete' ); // Let AJAX know that the upgrade is complete
	}

}
add_action( 'wp_ajax_checkout_trigger_upgrades', 'checkout_trigger_upgrades' );


/**
 * Upgrades old 'startup' options, theme mods, and post meta to 'checkout'.
 *
 * Why are we doing this stuff, you may ask? Because "Startup" is a registered trademark
 * owned by someone else, and we're being forced to change the theme name.
 *
 * @since 1.1
 */
function checkout_v11_upgrades() {

	// Bail early if we've already done this.
	if( get_option( 'checkout_startup_conversion' ) ) {
		wp_redirect( esc_url( admin_url( 'themes.php?page=checkout-license' ) ) );
		exit;
	}

	/**
	 * Convert the options
	 */
	$options = array(
		'startup_disable_typekit',
		'startup_license_key_status',
		'startup_license_key',
		'startup_footer_cta_title',
		'startup_footer_cta_subtitle',
		'startup_footer_cta_button',
		'startup_footer_cta_button_text',
		'startup_testimonial_title',
		'startup_testimonial_count',
		'startup_homepage_portfolio_count',
		'startup_homepage_download_count',
		'startup_header_title',
		'startup_header_subtitle',
		'startup_header_button_one_text',
		'startup_header_button_two_text',
		'widget_startup-icon-text-widget',
	);

	foreach( $options as $option ) {
		$old_value = get_option( $option );
		if( $old_value ) {
			$new_key = preg_replace( '/startup/', 'checkout', $option, 7 );
			update_option( $new_key, $old_value );
		}
	}

	/**
	 * Convert the theme_mods
	 */
	// Get the old theme mods
	$startup_theme_mods = get_option( 'theme_mods_startup' );

	// Replace 'startup' with 'checkout' in the array values
	$checkout_theme_mods = checkout_recursive_unserialize_replace( 'startup', 'checkout', $startup_theme_mods );

	// Replace 'startup' with 'checkout' in the array keys
	$keys = array_keys( $checkout_theme_mods );
	$keys = str_replace( 'startup', 'checkout', $keys );
	$values = array_values( $checkout_theme_mods );

	// Put the array back together
	$checkout_theme_mods = array_combine( $keys, $values );

	update_option( 'theme_mods_checkout', $checkout_theme_mods );

	/**
	 * Update the widgets
	 */
	$sidebars_widgets = $checkout_theme_mods['sidebars_widgets']['data'];
	wp_set_sidebars_widgets( $sidebars_widgets );

	/**
	 * Convert post meta for the download details feature
	 */
	global $wpdb;
	$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->postmeta SET meta_key = %s WHERE meta_key = %s", 'checkout_download_details', 'startup_download_details' ) );

	add_option( 'checkout_startup_conversion', '1' );

	wp_redirect( esc_url( admin_url( 'themes.php?page=checkout-license' ) ) );
	exit;
}


/**
 * The following search and replace functions were taken from
 * interconnect/it Database Search and Replace
 * @link https://interconnectit.com/products/search-and-replace-for-wordpress-databases/
 */

/**
 * Take a serialised array and unserialise it replacing elements as needed and
 * unserialising any subordinate arrays and performing the replace on those too.
 *
 * @param string $from       String we're looking to replace.
 * @param string $to         What we want it to be replaced with
 * @param array  $data       Used to pass any subordinate arrays back to in.
 * @param bool   $serialised Does the array passed via $data need serialising.
 *
 * @return array	The original array with all elements replaced as needed.
 */
function checkout_recursive_unserialize_replace( $from = '', $to = '', $data = '', $serialised = false ) {

	// some unserialised data cannot be re-serialised eg. SimpleXMLElements
	try {

		if ( is_string( $data ) && ( $unserialized = @unserialize( $data ) ) !== false ) {
			$data = checkout_recursive_unserialize_replace( $from, $to, $unserialized, true );
		}

		elseif ( is_array( $data ) ) {
			$_tmp = array( );
			foreach ( $data as $key => $value ) {
				$_tmp[ $key ] = checkout_recursive_unserialize_replace( $from, $to, $value, false );
			}

			$data = $_tmp;
			unset( $_tmp );
		}

		// Submitted by Tina Matter
		elseif ( is_object( $data ) ) {
			// $data_class = get_class( $data );
			$_tmp = $data; // new $data_class( );
			$props = get_object_vars( $data );
			foreach ( $props as $key => $value ) {
				$_tmp->$key = checkout_recursive_unserialize_replace( $from, $to, $value, false );
			}

			$data = $_tmp;
			unset( $_tmp );
		}

		else {
			if ( is_string( $data ) ) {
				$data = checkout_str_replace( $from, $to, $data );

			}
		}

		if ( $serialised )
			return serialize( $data );

	} catch( Exception $error ) {

		//$this->add_error( $error->getMessage(), 'results' );

	}

	return $data;
}


/**
 * Wrapper for regex/non regex search & replace
 *
 * @param string $search
 * @param string $replace
 * @param string $string
 * @param int $count
 *
 * @return string
 */
function checkout_str_replace( $search, $replace, $string, &$count = 0 ) {
	//if ( $this->get( 'regex' ) ) {
	//	return preg_replace( $search, $replace, $string, -1, $count );
	if( function_exists( 'mb_split' ) ) {
		return checkout_mb_str_replace( $search, $replace, $string, $count );
	} else {
		return str_replace( $search, $replace, $string, $count );
	}
}


/**
 * Replace all occurrences of the search string with the replacement string.
 *
 * @author Sean Murphy <sean@iamseanmurphy.com>
 * @copyright Copyright 2012 Sean Murphy. All rights reserved.
 * @license http://creativecommons.org/publicdomain/zero/1.0/
 * @link http://php.net/manual/function.str-replace.php
 *
 * @param mixed $search
 * @param mixed $replace
 * @param mixed $subject
 * @param int $count
 * @return mixed
 */
function checkout_mb_str_replace( $search, $replace, $subject, &$count = 0 ) {
	if ( ! is_array( $subject ) ) {
		// Normalize $search and $replace so they are both arrays of the same length
		$searches = is_array( $search ) ? array_values( $search ) : array( $search );
		$replacements = is_array( $replace ) ? array_values( $replace ) : array( $replace );
		$replacements = array_pad( $replacements, count( $searches ), '' );

		foreach ( $searches as $key => $search ) {
			$parts = mb_split( preg_quote( $search ), $subject );
			$count += count( $parts ) - 1;
			$subject = implode( $replacements[ $key ], $parts );
		}
	} else {
		// Call mb_str_replace for each subject in array, recursively
		foreach ( $subject as $key => $value ) {
			$subject[ $key ] = checkout_mb_str_replace( $search, $replace, $value, $count );
		}
	}

	return $subject;
}