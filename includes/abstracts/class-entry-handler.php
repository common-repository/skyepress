<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


abstract Class SKP_Entry_Handler {

	/**
	 * Cached instance of $wpdb
	 *
	 * @var object 
	 *
	 */
	protected $wpdb;


	/**
	 * The prefix of the tables in the DB
	 *
	 * @var string
	 *
	 */
	protected $table_prefix;


	/**
	 * The last inserted/updated id returned from the db
	 *
	 *
	 */
	protected $last_handled_id = 0;


	/**
	 * Constructor
	 *
	 */
	public function __construct() {

		global $wpdb;
		$this->wpdb = $wpdb;

		$this->table_prefix = $wpdb->prefix . SKP_Database::get_prefix();

	}


	/**
	 * Insert the entry into the table
	 * Must be implemented by each sub-class
	 * 
	 * @param array $data - the data that will be inserted into the db
	 *
	 */
	abstract public function insert( $data = array() );


	/**
	 * Updates the entry in the table
	 * Must be implemented by each sub-class
	 *
	 * @param object $object
	 *
	 */
	abstract public function update( $object );


	/**
	 * Returns the last id of the column
	 *
	 *
	 */
	public function get_last_handled_id() {

		return $this->last_handled_id;

	}

}