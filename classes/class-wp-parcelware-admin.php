<?php
/**
 * 
 * 
 * @version 23-08-2012
 */
class Wp_Parcelware_Admin {
	
	/**
	 * Initialize admin
	 */
	static function init(){
		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );
	}
	
	/**
	 * Should be called on admin_menu hook. Adds settings pages to the admin menu.
	 */
	static function admin_menu(){
		add_submenu_page(
			'tools.php',
			__('Parcelware', 'wp-parcelware-plugin'),
			__('Parcelware', 'wp-parcelware-plugin'),
			'manage_options',
			'wp-parcelware-order-page',
			array( __CLASS__, 'order_page' )
		);
	}
	
	/**
	 * Shows the parcelware admin page
	 */
	static function order_page(){
		// Include settings page
		include_once( WP_Parcelware::get_plugin_path() . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'order-page.php' );
	}
}