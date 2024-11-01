<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


Class SKP_Schedule {

	/**
	 * The unique id of the schedule 
	 *
	 * @var int
	 *
	 */
	public $id;

	/**
	 * The name of the scuedule
	 *
	 * @var string
	 *
	 */
	public $name;
    
    /**
	 * The post type of the posts being scheduled
	 *
	 * @var string
	 *
	 */
	public $post_type;


	/**
	 * The taxonomy of the posts being scheduled
	 *
	 * @var string
	 *
	 */
	public $taxonomy;


	/**
	 * The age (in number of days) of the posts being scheduled
	 * 
	 * @var string
	 *
	 */
	public $older_than;


	/**
	 * The social platform accounts where the post is to be published
	 *
	 * @var array
	 *
	 */
	public $platform_accounts;
    
            
    /**
	 * The content for each platform account
	 *
	 * @var array
	 *
	 */
	public $content;            


	/**
	 * The day(s) when the post will be scheduled
	 * 
	 * @var array
	 *
	 */
	public $day;


	/**
	 * The hour(s) when the post will be schedueld
	 *
	 * @var array
	 *
	 */
	public $hour;


	/**
	 * Constructor
	 * 
	 * @param object $data
	 *
	 */
	public function __construct( $data = null ) {

		if( !is_null( $data ) && is_object( $data ) ) {

			foreach( get_object_vars( $data ) as $key => $value ) {
				if( property_exists( $this , $key ) )
					$this->$key = $value;
			}

		}

	}
    

    /**
     * Recursively returns the object vars in an array form
     *
     * @param object|array $object
     *
     * @return array
     *
     */
    private function get_object_vars_recursive( $object ) {

    	$array = ( is_object( $object ) ? get_object_vars( $object ) : $object );

    	foreach($object as $k => $v){
            if( is_object($v) || is_array($v) ) {
                $array[$k] = $this->get_object_vars_recursive($v);
            }
        }
        return $array;
    }
    

    /** 
     * Returns the object attributes and their values as an array 
     *  
     */ 
    public function to_array($prefix = null) {
        
        $array = $this->get_object_vars_recursive( $this );
        
        if($prefix !== null){
            foreach($array as $k => $v){
                $array[$prefix.$k] = $v;
                unset($array[$k]);
            }
        }
        return $array;
    
    } 


}