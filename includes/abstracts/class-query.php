<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


abstract Class SKP_Query {

	/**
	 * The prefix of the tables in the DB
	 *
	 * @var string
	 *
	 */
	protected $table_prefix;

	
	/**
	 * Collection of posts retrieved from the DB
	 *
	 * @var array
	 *
	 */
	protected $results;


	/**
	 * The default arguments that should be replaced by the ones provided in the constructor
	 *
	 * @var array
	 *
	 */
	protected $default_args = array();


	/**
	 * The argumets for the query
	 *
	 * @var array
	 *
	 */
	protected $args = array();


	/**
	 * Constructor
	 *
	 */
	public function __construct( $args = array() ) {

		// Merge args
		$this->args = array_merge( $this->default_args, $args );

		global $wpdb;

		$this->table_prefix = $wpdb->prefix . SKP_Database::get_prefix();

		$this->results = $this->query( $this->get_query_string( $this->args ) );

	}


	/**
	 * Returns the query string needed to make the db query
	 * Should be replaced by child classes
	 *
	 * @param array $args
	 *
	 * @return string
	 *
	 */
	abstract protected function get_query_string( $args = array() );


	/**
	 * Returns the results from the database
	 * Should be replaced by child classes
	 *
	 *
	 */
	private function query( $query_string = '' ) {

		if( $query_string == '' )
			return array();

		global $wpdb;

		$results = $wpdb->get_results( $wpdb->prepare( $query_string, 1 ), OBJECT );

		if( empty( $results ) )
			$results = array();

		return $results;

	}


	/**
	 * Apply changes to the DB results before returning
	 * Can be replaced by child classes
	 *
	 * @param array $results
	 *
	 * @return array
	 *
	 */
	protected function filter_results( $results ) {

		return $results;
	
	}
    
    
    /**
	 * Recursively decode all json strings in an object 
	 *
	 * @param object $object
	 *
	 * @return object
	 *
	 */
    public function json_decode_recursive($object){
        foreach($object as $k => $v){
            if(gettype($v) == 'object'){
                $this->json_decode_recursive($v);
            } else {
                if($decode = json_decode($v, false, 512, JSON_BIGINT_AS_STRING))
                $object->$k = $decode;
            }
        }
        return $object;
    }


	/**
	 * Returns the results
	 * 
	 * @return array
	 *
	 */
	public function get_results() {
        
		return $this->json_decode_recursive( $this->filter_results( $this->results ) );

	}

}