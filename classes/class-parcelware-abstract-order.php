<?php
/**
 * Abstract superclass for all orders. This class stores the general values
 * that are used by Parcelware. It is always constructed by it's subclasses
 * and after construct will always call the abstract read_order_settings function
 * to have it's subclasses read the webshop's specific way of storing order values.
 * The variables stored in this class can be exported to a row of CSV.
 * 
 * @abstract
 * @version 23-08-12
 */
abstract class Parcelware_Abstract_Order {
	
	/** Post id */
	private $post_id;
	
	/** CSV separator */
	private static $separator = ';';
	
	/**
	 * Static keys array 
	 */
	static private $variable_keys = array(
		'IMPORT_SHIPMENT_REF' => null,
		'IMPORT_SENDER_REF' => null, // Required
		'IMPORT_CLIENT_REF' => null, // Required
		'IMPORT_CONTRACT_NAME' => null, // Required
		'IMPORT_SHIPMENT_ITEMS' => null, // Required
		'IMPORT_SENDER_IBAN' => null,
		'IMPORT_COSTCENTER_NAME' => null,
		'IMPORT_RECEIVER_REF' => null,
		'IMPORT_RECEIVER_COMPANYNAME' => null, // Required
		'IMPORT_RECEIVER_LASTNAME' => null,
		'IMPORT_RECEIVER_DEPARTMENT' => null,
		'IMPORT_RECEIVER_TITLE' => null,
		'IMPORT_RECEIVER_FIRSTNAME' => null,
		'IMPORT_RECEIVER_STREET' => null, // Required
		'IMPORT_RECEIVER_HOME_NR' => null, // Required
		'IMPORT_RECEIVER_HOME_NREXT' => null,
		'IMPORT_RECEIVER_ZIP' => null, // Required
		'IMPORT_RECEIVER_CITY' => null, // Required
		'IMPORT_RECEIVER_REGION' => null,
		'IMPORT_RECEIVER_AREA' => null,
		'IMPORT_RECEIVER_BUILDING' => null,
		'IMPORT_RECEIVER_FLOOR' => null,
		'IMPORT_RECEIVER_DOORCODE' => null,
		'IMPORT_RECEIVER_EXTRAINFO' => null,
		'IMPORT_RECEIVER_COUNTRY' => null, // Required
		'IMPORT_RECEIVER_TEL' => null,
		'IMPORT_RECEIVER_MOB' => null,
		'IMPORT_RECEIVER_FAX' => null,
		'IMPORT_RECEIVER_EMAIL' => null,
		'IMPORT_RECEIVER_TAXID' => null,
		'IMPORT_RECEIVER_REMARK' => null,
		'IMPORT_SHIPMENTS_COUNT' => null,
		'IMPORT_SHIPMENT_REMARK' => null,
		'IMPORT_SHIPMENT_RETURN_LABEL' => null,
		'IMPORT_SHIPMENT_RETURN_REFERENCE' => null,
		'IMPORT_SHIPMENT_RETURN_BARCODE' => null,
		'IMPORT_SHIPMENT_TIJDVAK_PALLET' => null,
		'XML' => '<items><item id=""311101""/></items>' // Required
	);
	
	/**
	 * Variables array
	 */
	private $variables;
	
	/**
	 * Constructor
	 * 
	 * @param int $post_id
	 */
	public function __construct( $post_id, $xml_items = true ){
		$this->set_post_id( $post_id );
		
		$this->variables = self::$variable_keys;
		
		$this->read_order_settings( $xml_items );
	}
	
	/**
	 * Get post id
	 * 
	 * @return int $post_id
	 */
	function get_post_id(){
		return $this->post_id;
	}
	
	/**
	 * Set post id
	 * 
	 * @param int $post_id
	 */
	function set_post_id( $post_id ){
		$this->post_id = $post_id;
	}
	
	/**
	 * Get variable
	 * 
	 * @param string $variable_name
	 * @return mixed $variable
	 */
	function get_variable( $name ){
		if( ! isset( $this->variables[ $name ] ) )
			return;
		
		return $this->variables[ $name ];
	}
	
	/**
	 * Get all available variable key
	 * 
	 * @return array of strings $variable_keys
	 */
	function get_variable_keys(){
		return array_keys( $this->variables );
	}
	
	/**
	 * Get all variables
	 * 
	 * @return mixed array $variables
	 */
	function get_variables(){
		return $this->variables;
	}
	
	/**
	 * Set variable
	 * 
	 * @param string $variable_name
	 * @param string $value
	 */
	protected function set_variable( $name, $value ){
		if( ! array_key_exists( $name, $this->variables ) || ! isset( $value ) || ( empty( $value ) && ! is_numeric( $value ) ) )
			return;
		
		$this->variables[ $name ] = $value;
	}
	
	/**
	 * Read order variables from the database and store them in
	 * their respective variable slot. This function is called 
	 * on creation of the object.
	 * 
	 * @abstract
	 */
	abstract function read_order_settings();
	
	/**
	 * Get orders
	 * 
	 * @return mixed order
	 */
	static function get_orders( $date_from, $date_to ){
		// Get orders between the two defined dates, this function uses a filter.
		define('PARCELWARE_GET_ORDERS_FILTER_DATE_FROM', $date_from );
		define('PARCELWARE_GET_ORDERS_FILTER_DATE_TO', $date_to );
		add_filter('posts_where', array( __CLASS__, 'order_page_get_orders_where_dates_between') );
		$orders = get_posts( array(
			'numberposts' => -1,
			'offset' => 0,
			'orderby' => 'post_date',
			'order' => 'DESC',
			'post_type' => 'shop_order',
			'suppress_filters' => false
		) );
		remove_filter('posts_where', 'order_page_get_orders_where_dates_between');
		
		return $orders;
	}
	
	/**
	 * Applies a where clause on the get_posts call
	 * 
	 * @param string $where
	 * @return string $where
	 */
	static function order_page_get_orders_where_dates_between( $where ){
		global $wpdb;

		if( ! defined('PARCELWARE_GET_ORDERS_FILTER_DATE_FROM') || ! defined('PARCELWARE_GET_ORDERS_FILTER_DATE_TO') )
			return $where;
		
		$where .= $wpdb->prepare(" AND post_date >= '%s' ", PARCELWARE_GET_ORDERS_FILTER_DATE_FROM);
		$where .= $wpdb->prepare(" AND post_date <= '%s' ", PARCELWARE_GET_ORDERS_FILTER_DATE_TO);
		
		return $where;
	}
	
	/**
	 * Builds the header row for the csv file
	 * 
	 * @return string $csv
	 */
	static function get_csv_header(){
		return implode( self::$separator, array_keys( self::$variable_keys ) );
	}
	
	/**
	 * Converts this object to a comma separated values line
	 * 
	 * @param mixed array $array
	 * @return string $csv_line
	 */
	function to_CSV(){
		if( empty( $this->variables ) )
			return '';
		
		$csv = '';
		foreach( $this->variables as $variable )
			$csv .= $variable . self::$separator;
		
		return implode( self::$separator, $this->variables );
	}
}
