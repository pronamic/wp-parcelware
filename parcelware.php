<?php
/*
Plugin Name: Parcelware
Plugin URI:
Description:
Version: 1.0.0
Requires at least: 3.0
Author: Pronamic, RemcoTolsma, StefanBoonstra
Author URI: http://pronamic.eu/
License: GPLv2
*/

/**
 * Main class that bootstraps the application
 *
 * @version 22-08-12
 */
class Parcelware {

	/**
	 * Bootstraps the plugin
	 */
	static function bootstrap(){
		// Translate
		add_action('init', array( __CLASS__, 'localize' ));
		
		// Auto include classes
		self::auto_include();
		
		if( is_admin() ) // Initialize admin
			Parcelware_Admin::init();
	}

	/**
	 * Translates the plugin
	 */
	static function localize(){
		load_plugin_textdomain(
			'parcelware-plugin',
			false,
			dirname(plugin_basename(__FILE__)) . '/languages/'
		);
	}

	/**
	 * Returns url to the base directory of this plugin.
	 *
	 * @return string pluginUrl
	 */
	static function get_plugin_url(){
		return plugins_url('', __FILE__ );
	}

	/**
	 * Returns path to the base directory of this plugin
	 *
	 * @return string pluginPath
	 */
	static function get_plugin_path(){
		return dirname( __FILE__ );
	}
	
	/**
	 * This function will load classes automatically on-call.
	 */
	static function auto_include(){
		if( ! function_exists('spl_autoload_register') )
			return;

		function parcelware_file_autoloader( $name ) {
			$name = strtolower( str_replace('_', '-', $name ) );
			$file = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'class-' . $name . '.php';

			if( is_file( $file ) )
				require_once $file;
		}

		spl_autoload_register('parcelware_file_autoloader');
	}
}

/*
 * Bootsrap application
 */
Parcelware::bootstrap();