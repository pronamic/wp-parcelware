<?php
class Wp_Parcelware_Woocommerce_Order extends Wp_Parcelware_Abstract_Order {
	
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