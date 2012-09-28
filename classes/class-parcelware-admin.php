<?php

/**
 * The admin class takes care of building the admin pages of the plugin
 * 
 * @version 23-08-2012
 */
class Parcelware_Admin {
	/**
	 * Initialize admin
	 */
	static function init(){
		add_action( 'admin_menu',            array( __CLASS__, 'admin_menu') );		
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue' ) );

		// When a submit comes through this page, make it go there ASAP
		self::admin_submit();
	}
	
	/**
	 * Should be called on admin_menu hook. Adds settings pages to the admin menu.
	 */
	static function admin_menu() {
		add_submenu_page(
			'tools.php', // parent_slug
			__( 'Parcelware', 'parcelware' ), // page_title
			__( 'Parcelware', 'parcelware' ), // menu_title
			'manage_options', // capability
			'parcelware', // menu_slug
			array( __CLASS__, 'order_page' ) // function
		);
	}
	
	/**
	 * Called on init to initialize scripts and styles
	 */
	static function admin_enqueue() {
		wp_enqueue_style( 'jquery-ui', plugins_url( 'style/jquery-ui.css', Parcelware::$file ) );
		
		wp_enqueue_script( 'jquery-ui', plugins_url( 'js/jquery-ui-min.js', Parcelware::$file ),  array( 'jquery' ) );
		
		wp_enqueue_script( 'jquery-datetime-picker', plugins_url( 'js/jquery-ui-timepicker-addon.js', Parcelware::$file ), array( 'jquery', 'jquery-ui') );
	}
	
	/**
	 * Shows the parcelware admin page
	 */
	static function order_page() {	
		include Parcelware::get_plugin_path() . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'parcelware.php';
	}
	
	/**
	 * This function is called when a submit has come through this page
	 * Prepares the csv file and offers it as download to the user.
	 */
	static function admin_submit() {
		if( ! isset( $_POST['submit'] ) )
			return;
		
		// Get orders between the two defined dates, this function needs a filter.
		add_filter('posts_where', array( __CLASS__, 'order_page_get_orders_where_dates_between') );
		$orders = get_posts( array(
			'numberposts'      => -1,
			'offset'           => 0,
			'orderby'          => 'post_date',
			'order'            => 'DESC',
			'post_type'        => 'shop_order',
			'suppress_filters' => false
		) );
		remove_filter('posts_where', 'order_page_get_orders_where_dates_between');
		
		// Convert all orders to Parcelware objects and export them as csv.
		$csv = Parcelware_Abstract_Order::get_csv_header() . "\r\n";
		foreach ( $orders as $order ) {
			// Check if already exported. If 'always-export' is set, export order anyways.
			$already_exported = get_post_meta( $order->ID, 'parcelware-has-already-been-exported', true );
			if( $already_exported && isset( $_POST['skip-already-exported'] ) )
				continue;
			
			// Get order and export to csv
			$class = new Parcelware_Woocommerce_Order( $order->ID );
			$csv .= $class->to_csv(). "\r\n";
			
			// Save as exported
			update_post_meta( $order->ID, 'parcelware-has-already-been-exported', true );
		}
		
		// Set headers for download
		header( 'Content-Type: text/plain;' );
		header( 'Content-Disposition: attachment; filename=Parcelware-Orders-Export-' . date('o-m-d_H-i') . '.csv' );
		
		// Output and die
		echo $csv;
		die;
	}
	
	/**
	 * Applies a where clause on the get_posts call
	 * 
	 * @param string $where
	 * @return string $where
	 */
	static function order_page_get_orders_where_dates_between( $where ) {
		global $wpdb;
		
		if( isset( $_POST['date-from'] ) )
			$where .= $wpdb->prepare( " AND post_date >= '%s'", $_POST['date-from'] );
			
		if( isset( $_POST['date-to'] ) )
			$where .= $wpdb->prepare( " AND post_date <= '%s'", $_POST['date-to'] );
		
		return $where;
	}
}
