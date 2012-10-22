<?php
/**
 * Class Parcelware_Order maps abstract order functions to
 * the desired parsers
 * 
 * @version 24-08-12
 */
class Parcelware_Order {
	
	/**
	 * Create a new order using the desired parser
	 * 
	 * @param int $post_id
	 * @return Parcelware_Abstract_Order
	 */
	static function new_order( $post_id ){
		return new Parcelware_Woocommerce_Order( $post_id );
	}
	
	/**
	 * Get orders
	 * 
	 * @param string $date_from
	 * @param string $date_to
	 * @return mixed $orders
	 */
	static function get_orders( $date_from, $date_to ){
		return Parcelware_Woocommerce_Order::get_orders( $date_from, $date_to );
	}
}