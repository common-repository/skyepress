<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


Class SKP_Platform_Account {

	/**
	 * The id of the platform account
	 *
	 * @var int 
	 *
	 */
	public $id;


	/**
	 * The slug of the social platform
	 *
	 * @var string
	 *
	 */
	public $platform_slug;


	/**
	 * The ids / tokens returned from the social platform needed to post on the platform
	 *
	 * @var string
	 *
	 */
	public $platform_app_credentials;
    
    /**
	 * An unique identifier or user id returned from the platform
	 *
	 * @var string
	 *
	 */
	public $platform_unique;
    
    /**
	 * Details about the platform user, such as name, avatar, etc.
	 *
	 * @var string
	 *
	 */
	public $platform_user_details;


	/**
	 * The WordPress user_id associated with the social account
	 * 
	 * @var int
	 *
	 */
	public $user_id;


	/**
	 * Constructor
	 *
	 * @param object $data
	 *
	 */
	public function __construct( $data = null ) {

		if( !is_null($data) && is_object( $data ) ) {

			foreach( get_object_vars( $data ) as $key => $value ) {
				if( property_exists( $this , $key ) )
					$this->$key = $value;
			}
			
		}

	}


}