<?php
/**
 * 
 * 
 * @version 24-08-12
 */
class WP_Parcelware_Order {
	
	/**
	 * Constructor
	 * 
	 * @param int $order_id
	 */
	function __construct( $order_id ){
		return new WP_Parcelware_Woocommerce_Order( $order_id );
	}
	
	//static function 
}