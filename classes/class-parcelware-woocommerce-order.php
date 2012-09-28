<?php

/**
 * The WooCommerce specific implementation op the Parcelware_Abstract_Order class
 * 
 * @version 24-08-12
 */
class Parcelware_Woocommerce_Order extends Parcelware_Abstract_Order {
	
	/**
	 * Constructor
	 * 
	 * @param int $post_id
	 */
	function __construct( $post_id ){
		parent::__construct( $post_id );
	}
	
	/**
	 * @see Parcelware_Abstract_Order::read_order_settings()
	 */
	function read_order_settings(){
		$meta = get_post_custom( $this->get_post_id() );
		
		$order_settings = array(
			'order_id'   => $this->get_post_id(),
			'first_name' => $meta['_shipping_first_name'][0],
			'last_name'  => $meta['_shipping_last_name'][0],
			'address'    => $meta['_shipping_address_1'][0],
			'address2'   => $meta['_shipping_address_2'][0],
			'zip'        => $meta['_shipping_postcode'][0],
			'city'       => $meta['_shipping_city'][0],
			'country'    => $meta['_shipping_country'][0],
			'state'      => $meta['_shipping_state'][0],
			'email'      => $meta['_billing_email'][0],
			'phone'      => $meta['_billing_phone'][0],
		);
		
		foreach ( $order_settings as $key => $order_setting ) {
			$this->set_variable( $key, $order_setting );
		}
	}
}
