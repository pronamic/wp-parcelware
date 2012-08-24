<?php
class WP_Parcelware_Woocommerce_Order extends WP_Parcelware_Abstract_Order {
	
	/**
	 * Constructor
	 * 
	 * @param int $order_id
	 */
	function __construct( $order_id ){
		parent::__construct();
	}
	
	function read_settings(){
		
	}
}