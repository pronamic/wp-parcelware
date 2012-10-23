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
		
		// Separate street name from street number by looping backwards, first occurance of a character means the end of the street name
		$street = '';
		$home_nr = '';
		$address_1 = $meta[ '_shipping_address_1' ][ 0 ];
		for( $i = strlen( $address_1 ) - 1; $i >= 0; $i-- ){
			if( ! is_numeric( $address_1[ $i ] ) ){
				$street = trim(substr( $address_1, 0, $i ));
				$home_nr = trim(substr( $address_1, $i ));
				break;
			}
		}
		
		// If no street and home number could be determined, fall back on user input
		if( empty( $street ) )
			$street = $meta[ '_shipping_address_1' ][ 0 ];
		if( empty( $home_nr ) || !is_numeric( $home_nr ) )
			$home_nr = $meta[ '_shipping_address_2' ][ 0 ];
		
		// Country
		$country = $meta[ '_shipping_country' ][ 0 ];
		$file = dirname( Parcelware::get_plugin_path() ) . DIRECTORY_SEPARATOR . 'woocommerce' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'class-wc-countries.php';
		if( file_exists( $file ) ){
			include_once( $file );
			$countries = new WC_Countries();
			$country = $countries->countries[ $meta[ '_shipping_country' ][ 0 ] ];
		}
		
		// Items
		$items = array();
		$meta_items = maybe_unserialize( $meta[ '_order_items' ][ 0 ] );
		foreach( $meta_items as $meta_item )
			if( isset( $meta_item[ 'id' ] ) && ! empty( $meta_item[ 'id' ] ) )
				$items[] = $meta_item[ 'id' ];
		
		$order_settings = array(
			'IMPORT_SHIPMENT_REF' => $this->get_post_id(),
			'IMPORT_SENDER_REF' => 1,
			'IMPORT_CLIENT_REF' => 1,
			'IMPORT_CONTRACT_NAME' => 'TNT post pakketservice9707',
			'IMPORT_SHIPMENT_ITEMS' => implode( ',', $items ),
			'IMPORT_RECEIVER_COMPANYNAME' => $meta[ '_shipping_last_name' ][ 0 ],
			'IMPORT_RECEIVER_LASTNAME' => $meta[ '_shipping_last_name' ][ 0 ],
			'IMPORT_RECEIVER_FIRSTNAME' => $meta[ '_shipping_first_name' ][ 0 ],
			'IMPORT_RECEIVER_STREET' => $street,
			'IMPORT_RECEIVER_HOME_NR' => $home_nr,
			'IMPORT_RECEIVER_HOME_NREXT' => $meta[ '_shipping_address_2' ][ 0 ],
			'IMPORT_RECEIVER_ZIP' => $meta[ '_shipping_postcode' ][ 0 ],
			'IMPORT_RECEIVER_CITY' => $meta[ '_shipping_city' ][ 0 ],
			'IMPORT_RECEIVER_REGION' => $meta[ '_shipping_state' ][ 0 ],
			'IMPORT_RECEIVER_COUNTRY' => $country,
			'IMPORT_RECEIVER_TEL' => $meta[ '_billing_phone' ][ 0 ],
			'IMPORT_RECEIVER_EMAIL' => $meta[ '_billing_email' ][ 0 ]
		);
		
		foreach($order_settings as $key => $order_setting)
			$this->set_variable($key, $order_setting);
	}
}