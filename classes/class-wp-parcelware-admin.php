<?php
/**
 * 
 * 
 * @version 23-08-2012
 */
class WP_Parcelware_Admin {
	
	/**
	 * Variables
	 */
	private static $submenu_parent_slug = 'tools.php';
	private static $submenu_menu_slug = 'wp-parcelware-order-page';
	
	/**
	 * Initialize admin
	 */
	static function init(){
		// Load admin menu items
		add_action('admin_menu', array( __CLASS__, 'admin_menu') );
		
		add_action('init', array( __CLASS__, 'enqueue') );
	}
	
	/**
	 * Should be called on admin_menu hook. Adds settings pages to the admin menu.
	 */
	static function admin_menu(){
		add_submenu_page(
			self::$submenu_parent_slug,
			__('Parcelware', 'wp-parcelware-plugin'),
			__('Parcelware', 'wp-parcelware-plugin'),
			'manage_options',
			self::$submenu_menu_slug,
			array( __CLASS__, 'order_page' )
		);
	}
	
	/**
	 * Called on init to initialize scripts and styles
	 */
	static function enqueue(){
		wp_enqueue_style(
			'jquery-ui',
			WP_Parcelware::get_plugin_url() . '/style/jquery-ui.css'
		);
		
		wp_enqueue_script(
			'jquery-ui',
			WP_Parcelware::get_plugin_url() . '/js/jquery-ui-min.js',
			array('jquery')
		);
		
		wp_enqueue_script(
			'jquery-datetime-picker',
			WP_Parcelware::get_plugin_url() . '/js/jquery-ui-timepicker-addon.js',
			array('jquery', 'jquery-ui')
		);
	}
	
	/**
	 * Shows the parcelware admin page
	 */
	static function order_page(){
		if( isset( $_POST['submit'] ) ){
			add_filter('posts_where', array( __CLASS__, 'order_page_get_orders_where_dates_between') );
			$posts = get_posts( array(
				'numberposts' => -1,
				'offset' => 0,
				'orderby' => 'post_date',
				'order' => 'DESC',
				'post_type' => 'shop_order',
				'suppress_filters' => false
			) );
			remove_filter('posts_where', 'order_page_get_orders_where_dates_between');
			
			echo WP_Parcelware_Abstract_Order::get_csv_header();
			foreach($posts as $post){
				$class = new WP_Parcelware_Woocommerce_Order( $post->ID );
				echo $csv = $class->to_csv();
				
				//var_dump($post);
				echo '<br /><br />';
			}
		
			//if($_POST['date_from'])
		}
		
		// Form action
		$form_action = self::$submenu_parent_slug . '?page=' . self::$submenu_menu_slug;
		
		// Today
		$datetime_today = date('o-m-d H:i:s');
		
		include_once( WP_Parcelware::get_plugin_path() . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'order-page.php' );
	}
	
	/**
	 * Applies a where clause on the get_posts call
	 * 
	 * @param string $where
	 * @return string $where
	 */
	static function order_page_get_orders_where_dates_between( $where ){
		global $wpdb;
		
		if( isset( $_POST['date-from'] ) )
			$where .= $wpdb->prepare(" AND post_date >= '%s'", $_POST['date-from']);
			
		if( isset( $_POST['date-to'] ) )
			$where .= $wpdb->prepare(" AND post_date <= '%s'", $_POST['date-to']);
		
		return $where;
	}
}