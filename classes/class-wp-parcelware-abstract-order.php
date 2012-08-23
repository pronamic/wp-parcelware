<?php
/**
 * 
 * 
 * @version 23-08-12
 */
abstract class Wp_Parcelware_Abstract_Order {
	
	/**
	 * Class variables array
	 */
	private $variables = array(
		'order_id' => null,
		'first_name' => null,
		'last_name' => null,
		'address' => null,
		'address2' => null,
		'zip' => null,
		'city' => null,
		'country' => null,
		'state' => null,
		'email' => null,
		'phone' => null
	);
	
	/**
	 * Constructor
	 * 
	 * @param int $order_id
	 */
	protected function __construct( $id ){
		set_variable('order_id', $id );
		
		$this->read_settings();
	}
	
	/**
	 * Read order variables from the database and store them in
	 * their respective variable slot
	 */
	abstract function read_settings();
	
	/**
	 * Get variable
	 * 
	 * @param string $variable_name
	 * @return mixed $variable
	 */
	function get_variable( $name ){
		if( ! isset( $variables[ $name ] ) )
			return;
		
		return $variables[ $name ];
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
		if( ! isset( $name ) || ! isset( $variables[ $name ] ) )
			return;
		
		$variables[ $name ] = $value;
	}
	
	/**
	 * Converts this object to a comma separated values line
	 * 
	 * @return string $csv_line
	 */
	function to_CSV(){
		$csv = '';
		foreach($this->variables as $variable)
			$csv = $variable . ',';
		
		return substr($csv, 0 , -1);
	}
}