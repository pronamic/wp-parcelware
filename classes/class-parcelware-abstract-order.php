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
	
	/**
	 * Post id
	 */
	private $post_id;
	
	/**
	 * Static keys array 
	 */
	static private $variable_keys = array(
		'order_id'   => null,
		'first_name' => null,
		'last_name'  => null,
		'address'    => null,
		'address2'   => null,
		'zip'        => null,
		'city'       => null,
		'country'    => null,
		'state'      => null,
		'email'      => null,
		'phone'      => null
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
	protected function __construct( $post_id ) {
		$this->set_post_id( $post_id );
		
		$this->variables = self::$variable_keys;
		
		$this->read_order_settings();
	}
	
	/**
	 * Builds the header row for the csv file
	 * 
	 * @return string $csv
	 */
	static function get_csv_header() {
		if( empty( self::$variable_keys ) )
			return '';
		
		$csv = '';
		foreach ( self::$variable_keys as $variable_key => $variable_value )
			$csv .= $variable_key . ',';
		
		return substr( $csv, 0, -1 );
	}
	
	/**
	 * Converts this object to a comma separated values line
	 * 
	 * @param mixed array $array
	 * @return string $csv_line
	 */
	function to_CSV() {
		if( empty( $this->variables ) )
			return '';
		
		$csv = '';
		foreach( $this->variables as $variable )
			$csv .= $variable . ',';
		
		return substr($csv, 0 , -1);
	}
	
	/**
	 * Get post id
	 * 
	 * @return int $post_id
	 */
	function get_post_id() {
		return $this->post_id;
	}
	
	/**
	 * Set post id
	 * 
	 * @param int $post_id
	 */
	function set_post_id( $post_id ) {
		$this->post_id = $post_id;
	}
	
	/**
	 * Get variable
	 * 
	 * @param string $variable_name
	 * @return mixed $variable
	 */
	function get_variable( $name ) {
		if( ! isset( $this->variables[ $name ] ) )
			return;
		
		return $this->variables[ $name ];
	}
	
	/**
	 * Get all available variable key
	 * 
	 * @return array of strings $variable_keys
	 */
	function get_variable_keys() {
		return array_keys( $this->variables );
	}
	
	/**
	 * Get all variables
	 * 
	 * @return mixed array $variables
	 */
	function get_variables() {
		return $this->variables;
	}
	
	/**
	 * Set variable
	 * 
	 * @param string $variable_name
	 * @param string $value
	 */
	protected function set_variable( $name, $value ) {
		if( ! array_key_exists( $name, $this->variables ) || ! isset( $value ) )
			return;
		
		$this->variables[$name] = $value;
	}
	
	/**
	 * Read order variables from the database and store them in
	 * their respective variable slot. This function is called 
	 * on creation of the object.
	 * 
	 * @abstract
	 */
	abstract function read_order_settings();
}
