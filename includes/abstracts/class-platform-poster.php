<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


abstract Class SKP_Platform_Poster {

	/**
	 * Initialisation method
	 * Should be replaced by the children
	 *
	 */
	public static function init() {}


	/**
	 * Permits last minute filtering on the SKP_Post and its sibling array just before
	 * sharing it on the SKP_Platform_Account
	 *
	 * @param SKP_Platform_Account $platform_account
	 * @param SKP_Post 			 $skp_post
	 * @param array 				 $skp_post_data
	 *
	 *
	 */
	public static function post_to_account( $platform_account, $skp_post, $skp_post_data ) {

		/**
	     * Filter to permit last minute changes on the SKP_Post object before
	     * sharing on a particular account
	     *
	     * @param SKP_Post $skp_post
	     *
	     */
	    $skp_post = apply_filters( 'skp_post_to_account_post', $skp_post, $platform_account );
	    
	    
	    /**
	     * For extra functionality we will also use the array sibling of the
	     * SKP_Post which can be filtered here
	     *
	     * @param array $skp_post_data
	     *
	     */
	    $skp_post_data = apply_filters( 'skp_post_to_account_post_data', $skp_post_data, $platform_account );


	    /**
	     * Share the post on the platform account
	     *
	     */
		static::post( $platform_account, $skp_post, $skp_post_data );

	}
    
    /**
	 * Returns the content for a specific platform
	 *
	 * @param SKP_Platform_Account $platform_account
     * @param SKP_Post $skp_post
     * 
	 */
     
    protected static function get_platform_content( $platform_account, $skp_post ) {
        
        $content = $skp_post->get( 'content' );

        $platform_content = ( ! empty( $content[$platform_account->platform_unique] ) ? $content[$platform_account->platform_unique] : '' );

        return $platform_content;
        
    }
    

	/**
	 * Handles posting to the platform
	 * Should be replaced by the children
	 *
	 */
	protected static function post( $platform_account, $skp_post, $skp_post_data ) {}
     
}