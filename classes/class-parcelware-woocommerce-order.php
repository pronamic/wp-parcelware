<?php
/**
 * The WooCommerce specific implementation op the Parcelware_Abstract_Order class
 * 
 * @version 24-08-12
 */
class Parcelware_Woocommerce_Order extends Parcelware_Abstract_Order {
	
	
	/**
	 * @see Parcelware_Abstract_Order::read_order_settings()
	 */
	function read_order_settings( $xml_items = true ){
		$meta = get_post_custom( $this->get_post_id() );
		
		// Separate street name from street number by looping backwards, first occurance of a character means the end of the street name
		$street = '';
		$home_nr = '';
		$address_1 = $meta[ '_shipping_address_1' ][ 0 ];
		for ( $i = 0 ; $i < strlen( $address_1 ) ; $i++ ) {
			if (  is_numeric( $address_1[ $i ] ) ) {
				$street = trim( substr( $address_1, 0, $i ) );
				$home_nr = trim( substr( $address_1, $i ) );
				break;
			}
		}
		
		// If no street and home number could be determined, fall back on user input
		if( empty( $street ) )
			$street = $meta[ '_shipping_address_1' ][ 0 ];
		if( empty( $home_nr ) )
			$home_nr = $meta[ '_shipping_address_2' ][ 0 ];
		
		// Country
		$country = $meta[ '_shipping_country' ][ 0 ];
		$countries = new WC_Countries();
		$country = $countries->countries[ $meta[ '_shipping_country' ][ 0 ] ];
		
		// Items
		$order = new WC_Order( $this->get_post_id() );
		
		$items = ( true === $xml_items ) ? $this->items_as_xml( $order->get_items() ) : $this->items_as_comma( $order->get_items() );
		
		$order_settings = array(
			'IMPORT_SHIPMENT_REF' => $this->get_post_id(),
			'IMPORT_SENDER_REF' => 1,
			'IMPORT_CLIENT_REF' => 1,
			'IMPORT_CONTRACT_NAME' => 'TNT post pakketservice9707',
			'IMPORT_SHIPMENT_ITEMS' => $items,
			'IMPORT_RECEIVER_COMPANYNAME' => ( ! empty( $meta[ '_shipping_company' ][ 0 ] ) ? $meta['_shipping_company'][0] : ' ' ),
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
	
	/**
	 * Splits the items of an order into an XML string.
	 * 
	 * @access public
	 */
	public function items_as_xml( $items ) {
		$xml_items = new DOMDocument();
		$items_element = $xml_items->createElement( 'items' );
		$xml_items->appendChild( $items_element );
		
		foreach ( $items as $item ) {
			if ( ! empty( $item['product_id'] ) ) {
				// Create the item element
				$item_element = $xml_items->createElement( 'item' );
				
				// Create ID attribute
				$item_id_attribute = $xml_items->createAttribute( 'id' );
				$item_id_attribute->value = $item['product_id'];
				$item_element->appendChild( $item_id_attribute );
				
				// Add to parent document
				$items_element->appendChild( $item_element );
			}
		}
		
		return (string) $xml_items->saveXML( $xml_items->documentElement );
	}
	
	/**
	 * Splits the items of an order into a comma delimited string
	 * 
	 * @access public
	 */
	public function items_as_comma( $items ) {
		$xml_items = array();
		foreach ( $items as $item ) {
			if ( ! empty( $item['product_id'] ) )
				$xml_items[] = $item['product_id'];
		}
		
		return implode( ',', $xml_items );
	}
}