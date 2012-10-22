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
	public static function bootstrap() {
		add_action( 'admin_init',            array( __CLASS__, 'maybe_export' ) );
		add_action( 'admin_menu',            array( __CLASS__, 'admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue' ) );
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
	static function maybe_export() {
		if ( empty( $_POST ) || !wp_verify_nonce( filter_input( INPUT_POST, 'parcelware_nonce', FILTER_SANITIZE_STRING ), 'parcelware_export') )
			return;
		
		global $post;

		// Add date filter
		add_filter( 'posts_where', array( __CLASS__, 'posts_where_between_date' ) );
		
		$query = new WP_Query( array(
			'post_type'        => 'shop_order',
			'orderby'          => 'post_date',
			'order'            => 'DESC',
			'nopaging'         => true
		) );
		
		// Convert all orders to Parcelware objects and export them as csv.
		$csv = Parcelware_Abstract_Order::get_csv_header() . "\r\n";

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$row = new Parcelware_Woocommerce_Order( $post->ID );
				
				$csv .= $row->to_csv(). "\r\n";
			}
		}

		// Remove date filter
		remove_filter( 'posts_where', array( __CLASS__, 'posts_where_between_date' ) );

		// Set headers for download
		$filename  = __( 'parcelware-orders-export', 'parcelware' );
		$filename .= '-' . date('Y-m-d_H-i') . '.csv';

		header( 'Content-Type: text/plain;' );
		header( 'Content-Disposition: attachment; filename=' . $filename );
		
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
	static function posts_where_between_date( $where ) {
		global $wpdb;
		
		$date_from = filter_input( INPUT_POST, 'date-from', FILTER_SANITIZE_STRING );
		if( !empty( $date_from ) )
			$where .= $wpdb->prepare( " AND post_date >= '%s'", $date_from );

		$date_to = filter_input( INPUT_POST, 'date-to', FILTER_SANITIZE_STRING );
		if( !empty( $date_to ) )
			$where .= $wpdb->prepare( " AND post_date <= '%s'", $date_to );
		
		return $where;
	}
}