<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


Class SKP_Post {

	/**
	 * The unique id of the post 
	 *
	 * @var int
	 *
	 */
	public $id;

	/**
	 * The id of the wp_post this post is tied to. Can also be 0,
	 * thus not being tied to any wp_post
	 *
	 * @var int
	 *
	 */
	public $post_id = 0;


	/**
	 * The type of the post
	 *
	 * @var string
	 *
	 */
	public $type;


	/**
	 * The date when the post is scheduled to be posted to the social platforms
	 * 
	 * @var string
	 *
	 */
	public $date;


	/**
	 * The social platform accounts where the post is to be published
	 *
	 * @var array
	 *
	 */
	public $platform_accounts;


	/**
	 * Custom content for the post, used mainly when post is not attached to a wp_post
	 * 
	 * @var string
	 *
	 */
	public $content;


	/**
	 * The attachment id
	 *
	 * @var int
	 *
	 */
	public $attachment;


	/**
	 * Social network accounts responses after attemting to publish the post on them
	 * 
	 * @var array
	 *
	 */
	public $response;
    
    /**
	 * The id of the schedule the post is linked to. Optional.
	 * 
	 * @var int
	 *
	 */
	public $schedule_id;

	/**
	 * The status of the post
	 *
	 * @var string - pending - when a post is scheduled to be posted
	 * 			   - error   - when a post attempted to be posted to the social platforms, but errors were retuned by the platform
	 * 			   - posted  - when the post was posed successfully to all social platforms
	 *
	 */
	public $status;


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
	 * Property setter
	 *
	 * @param string $property - the property we want to add the value to
	 * @param mixed $value 	   - the value to be added
	 *
	 */
	public function set( $property = '', $value = '' ) {

		if( property_exists( $this, $property ) )
			$this->$property = $value;

	}


	/**
	 * Property getter
	 *
	 * @param $property - the name of the property we want the value for
	 *
	 */
	public function get( $property = '' ) {

		if( property_exists( $this , $property ) )
			return $this->$property;

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