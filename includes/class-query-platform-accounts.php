<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


Class SKP_Query_Platform_Accounts extends SKP_Query {


	/**
	 * The name of the DB table for the posts without prefixes
	 *
	 * @var string
	 *
	 */
	private $table_name = 'platform_accounts';


	/**
	 * Default query arguments
	 *
	 */
	protected $default_args = array(
		'order'			  => 'ASC',
		'orderby'   	  => 'id',
		'number' 		  => -1,
		'platform_slug'	  => '',
        'platform_unique' => '',
		'include'	      => array(),
        'include_unique'  => array()
	);


	/**
	 * Constructor
	 *
	 * @param array $args
	 *
	 */
	public function __construct( $args = array() ) {

		parent::__construct( $args );

	}


	/**
	 * Returns the query string to query the DB given needed arguments 
	 *
	 * @param array $args
	 *
	 * @return string
	 *
	 */
	protected function get_query_string( $args = array() ) {
		
		// Start query string
		$query_string  = "SELECT * FROM {$this->table_prefix}{$this->table_name} ";
		$query_string .= "WHERE 1=%d ";


		// Select only by given type
		if( !empty( $args['platform_slug'] ) ) {
			$query_string .= "AND platform_slug LIKE '{$args['platform_slug']}%%' ";
		}
        
        // Select by unique identifier
		if( !empty( $args['platform_unique'] ) ) {
			$query_string .= "AND platform_unique = '{$args['platform_unique']}' ";
		}
		
		// Narrow to only certain id's
		if( !empty( $args['include'] ) ) {
			$include = implode( ', ', $args['include'] );
			$query_string .= "AND id IN ({$include}) ";
		}
        
        // Narrow to only certain id's
		if( !empty( $args['include_unique'] ) ) {
			$include = '"' . implode( '", "', $args['include_unique'] ) . '"';            
			$query_string .= "AND platform_unique IN ({$include}) ";
		}

		// Order by
		$query_string .= "ORDER BY {$args['orderby']} {$args['order']} ";


		// Limit number of posts returned
		if( !empty( $args['number'] ) && $args['number'] >= 0 )
			$query_string .= "LIMIT {$args['number']} ";


		return $query_string;

	}


	/**
	 * Filters the results
	 *
	 * @param array $results
	 *
	 * @return array
	 *
	 */
	protected function filter_results( $results ) {

		if( !empty( $results ) ) {

			foreach( $results as $key => $obj )
				$results[$key] = new SKP_Platform_Account( $obj );

		} else
			$results = array();


		return $results;

	}


}