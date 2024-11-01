<?php
/*
Plugin Name: WP Facebook Messenger Chat
Plugin URI:  https://niu.com.mt/wp-facebook-messenger-chat/
Description: Allows users to chat with the website's business in real-time by using Facebook Messenger. Users can start a conversation on the website and continue it on other devices.
Version:     1.0.1
Author:      NIU Ltd
Author URI:  https://niu.com.mt
*/

if ( !defined( 'ABSPATH' ) ) {
	die( 'Access denied.' );
}

define( 'WPFM_NAME', 'WP Chat' );
define( 'WPFM_REQUIRED_PHP_VERSION', '5.6' );                          // because of get_called_class()
define( 'WPFM_REQUIRED_WP_VERSION', '4.0' );                          // because of esc_textarea()

/**
 * Checks if the system requirements are met
 *
 * @return bool True if system requirements are met, false if not
 */
function wpfm_requirements_met()
{
	global $wp_version;
	
	if ( version_compare( PHP_VERSION, WPFM_REQUIRED_PHP_VERSION, '<' ) ) {
		return false;
	}
	
	if ( version_compare( $wp_version, WPFM_REQUIRED_WP_VERSION, '<' ) ) {
		return false;
	}
	
	return true;
}

/**
 * Prints an error that the system requirements weren't met.
 */
function wpfm_requirements_error()
{
	global $wp_version;
	
	require_once( dirname( __FILE__ ) . '/views/requirements-error.php' );
}

/*
 * Check requirements and load main class
 * The main program needs to be in a separate file that only gets loaded if the plugin requirements are met. Otherwise older PHP installations could crash when trying to parse it.
 */
if ( wpfm_requirements_met() ) {
	require_once( __DIR__ . '/classes/wpfm-module.php' );
	require_once( __DIR__ . '/classes/wp-facebook-messenger-chat.php' );
	require_once( __DIR__ . '/classes/wpfm-settings.php' );
	require_once( __DIR__ . '/classes/wpfm-instance-class.php' );
	require_once( __DIR__ . '/classes/wpfm-handler.php' );
	
	if ( class_exists( 'WP_Facebook_Messenger_Chat' ) ) {
		$GLOBALS['wpfm'] = WP_Facebook_Messenger_Chat::get_instance();
		register_activation_hook( __FILE__, [$GLOBALS['wpfm'], 'activate'] );
		register_deactivation_hook( __FILE__, [$GLOBALS['wpfm'], 'deactivate'] );
	}
}else {
	add_action( 'admin_notices', 'wpfm_requirements_error' );
}
