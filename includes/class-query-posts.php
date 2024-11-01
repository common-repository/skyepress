<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


Class SKP_Query_Posts extends SKP_Query {

	/**
	 * The name of the DB table for the posts without prefixes
	 *
	 * @var string
	 *
	 */
	private $table_name = 'posts';


	/**
	 * Default query arguments
	 *
	 */
	protected $default_args = array(
		'order'		  => 'DESC',
		'orderby'     => 'date',
		'number' 	  => 0,
		'post_id'	  => '',
        'date'        => '',
        'date_range'  => array(),
		'type'		  => '',
        'schedule_id' => '',
		'include'	  => array(),
        'status'      => ''
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


		// Select only by given post_id
		if( !empty( $args['post_id'] ) ) {
			$query_string .= "AND post_id = '{$args['post_id']}' ";
		}
        
        // Select only by date
		if( !empty( $args['date'] ) ) {
			$query_string .= "AND date = '{$args['date']}' ";
		}
        
        // Select only by date range
		if( !empty( $args['date_range'] ) ) {
			$query_string .= "AND date >= '{$args['date_range'][0]}' AND date <= '{$args['date_range'][1]}' ";
		}

		// Select only by given type
		if( !empty( $args['type'] ) ) {
			$query_string .= "AND type = '{$args['type']}' ";
		}
        
        // Select only by schedule_id
		if( !empty( $args['schedule_id'] ) ) {
			$query_string .= "AND schedule_id = '{$args['schedule_id']}' ";
		}
		
		// Narrow to only certain id's
		if( !empty( $args['include'] ) ) {
			$include = implode( ', ', $args['include'] );
			$query_string .= "AND id IN ({$include}) ";
		}
        
        // Select only by given type
		if( !empty( $args['status'] ) ) {
			$query_string .= "AND status = '{$args['status']}' ";
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
				$results[$key] = new SKP_Post( $obj );

		} else
			$results = array();


		return $results;

	}

}