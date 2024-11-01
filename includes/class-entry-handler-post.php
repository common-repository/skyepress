<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


Class SKP_Entry_Handler_Post extends SKP_Entry_Handler {


	/**
	 * The name of the table to insert the data
	 *
	 * @var string 
	 *
	 */
	private $table_name = 'posts';


	/**
	 * Inserts a new entry into the database
	 * 
	 * @param array $data
	 *
	 * @return void
	 *
	 */
	public function insert( $data = array() ) {

		$result = $this->wpdb->insert( $this->table_prefix . $this->table_name, $data );

		if( $result === 1 )
			$this->last_handled_id = $this->wpdb->insert_id;

	}


	/**
	 * Updates the entry in the db for a given SKP_Post object
	 * 
	 * @param SKP_Post $object
	 *
	 * @return void
	 *
	 */
	public function update( $object = null ) {

		if( empty( $object->id ) )
			return;
            
        $data  = get_object_vars( $object );

        foreach($data as $k => $v) {
            if(is_object($v) || is_array($v)){
                $data[$k] = json_encode($v);
            }
		}

		$result = $this->wpdb->update( $this->table_prefix . $this->table_name, $data, array( 'id' => $data['id'] ) );

		if( $result !== false )
			$this->last_handled_id = $data['id'];

	}
    
    /**
	 * Removes the entry from the db coresponding to the provided object
	 *
	 * @param SKP_Post $object
	 *
	 * @return void
	 *
	 */
	public function remove( $object = null ) {

		if( empty( $object->id ) )
			return;

		$data   = get_object_vars( $object );
        
        // Due to the json encoding in the DB we cannot rely on these two elements
		unset( $data['platform_accounts'] );
		unset( $data['response'] );
        unset( $data['content'] );

		$result = $this->wpdb->delete( $this->table_prefix . $this->table_name, $data );

		if( $result !== false )
			$this->last_handled_id = $data['id'];

	}

}