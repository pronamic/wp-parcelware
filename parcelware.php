<?php
/*
Plugin Name: Parcelware
Plugin URI: http://pronamic.eu/wordpress/parcelware/
Description: Create Parcelware importable CSV files from the orders in your WordPress webshop.

Version: 0.1
Requires at least: 3.0

Author: Pronamic
Author URI: http://pronamic.eu/

Text Domain: parcelware
Domain Path: /languages/

License: GPLv2

GitHub URI: https://github.com/pronamic/wp-parcelware
*/

class Parcelware {
	/**
	 * The plugin file
	 * 
	 * @var string
	 */
	public static $file;

	//////////////////////////////////////////////////

	/**
	 * Bootstrap
	 */
	public static function bootstrap( $file ) {
		self::$file = $file;

		self::autoload();

		add_action( 'init', array( __CLASS__, 'init' ) );
		
		if ( is_admin() ) {
			Parcelware_Admin::bootstrap();
		}
	}

	//////////////////////////////////////////////////

	/**
	 * Initialize
	 */
	public static function init() {
		load_plugin_textdomain( 'parcelware', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	//////////////////////////////////////////////////

	/**
	 * Returns path to the base directory of this plugin
	 *
	 * @return string pluginPath
	 */
	public static function get_plugin_path() {
		return dirname( __FILE__ );
	}

	//////////////////////////////////////////////////

	/**
	 * This function will load classes automatically on-call.
	 */
	public static function autoload() {
		if( ! function_exists('spl_autoload_register' ) )
			return;

		function parcelware_autoload( $name ) {
			$name = strtolower( str_replace( '_', '-', $name ) );
			$file = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'class-' . $name . '.php';

			if( is_file( $file ) )
				require_once $file;
		}

		spl_autoload_register( 'parcelware_autoload' );
	}
}

/**
 * Bootsrap application
 */
Parcelware::bootstrap( __FILE__ );
