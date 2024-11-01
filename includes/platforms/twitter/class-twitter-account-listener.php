<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


Class SKP_Twitter_Account_Listener {

	/**
	 * Initialise connector
	 *
	 */
	public static function init() {

		// Check for access level
		if( !current_user_can( 'manage_options' ) )
			return;

		if( empty( $_GET['skp_platform'] ) || $_GET['skp_platform'] != 'twitter' )
			return;

		add_action( 'admin_init', array( get_called_class(), 'connect_account' ) );

		add_action( 'admin_init', array( get_called_class(), 'insert_user_details' ) );

		add_action( 'admin_init', array( get_called_class(), 'remove_account' ) );

	}


	/**
	 * Connect account listener
	 *
	 */
	public static function connect_account() {
        
		if( !isset( $_POST['skp_tkn'] ) || !wp_verify_nonce( $_POST['skp_tkn'], 'skp_connect_account' ) )
			return;
        
        $_SESSION['twitter_app_details'] = array(
            'twitter_consumer_key'    => $_POST['twitter_consumer_key'],
            'twitter_consumer_secret' => $_POST['twitter_consumer_secret']
        );
        
		$twitter = new SKP_Twitter($_POST['twitter_consumer_key'], $_POST['twitter_consumer_secret']);
    	$twitter->set_callback_url( add_query_arg( array( 'page' => $_GET['page'], 'skp_platform' => 'twitter', 'noheader' => 'true', 'skp_tkn' => wp_create_nonce('skp_insert_user_details') ), admin_url('admin.php') ) );
    	$twitter->authorize();

	}


	/**
	 * Insert account user details listener
	 *
	 */
	public static function insert_user_details() {

		if( !isset( $_GET['skp_tkn'] ) || !wp_verify_nonce( $_GET['skp_tkn'], 'skp_insert_user_details' ) )
			return;

		$twitter 	  = new SKP_Twitter($_SESSION['twitter_app_details']['twitter_consumer_key'], $_SESSION['twitter_app_details']['twitter_consumer_secret']);
    	$token 		  = $twitter->get_user_token();
    	$user_details = $twitter->get_user_details( $token['user_id'] );

    	if( skp_get_platform_accounts(array('platform_unique' => $token['user_id']) ) == null ){
	        //insert
	        skp_insert_platform_account( array(
	            'user_id' 		  => get_current_user_id(),
	            'platform_unique' => 'twitter_' . $token['user_id'],
	            'platform_slug'   => 'twitter',
	        	'platform_app_credentials' => json_encode(array(
                    'consumer_key'       => $_SESSION['twitter_app_details']['twitter_consumer_key'],
                    'consumer_secret'    => $_SESSION['twitter_app_details']['twitter_consumer_secret'],
                    'oauth_token' 		 => $token['oauth_token'], 
	                'oauth_token_secret' => $token['oauth_token_secret']
	            )), 
	            'platform_user_details' => json_encode(array(
	                'id' 		  => $user_details->id,
	                'name' 		  => $user_details->name, 
	                'screen_name' => $user_details->screen_name,
	                'avatar' 	  => $user_details->profile_image_url
	            ))
	        ) );    
	    }
        
	    wp_redirect( add_query_arg( array( 'page' => $_GET['page'], 'message' => 2 ), admin_url('admin.php') ) );
	    exit;

	}


	/**
	 * Remove account listener
	 *
	 */
	public static function remove_account() {

		if( !isset( $_GET['skp_tkn'] ) || !wp_verify_nonce( $_GET['skp_tkn'], 'skp_remove_account' ) )
			return;

		if( empty( $_GET['skp_platform_unique'] ) )
			return;

		$platform_accounts = skp_get_platform_accounts( array( 'platform_slug' => 'twitter', 'platform_unique' => $_GET['skp_platform_unique'] ) );

		skp_remove_platform_account( $platform_accounts[0] );

		wp_redirect( add_query_arg( array( 'page' => $_GET['page'], 'message' => 3 ), admin_url('admin.php') ) );
	    exit;

	}
    
    /**
	 * Update user details
     * 
     * Method is executed daily by a cron job
	 *
	 */
	public static function update_user_details() {

		$platform_accounts = skp_get_platform_accounts( array( 'platform_slug' => 'twitter' ) ); foreach( $platform_accounts as $platform_account ){
            
            $twitter = new SKP_Twitter( $platform_account->platform_app_credentials->consumer_key, $platform_account->platform_app_credentials->consumer_secret, $platform_account->platform_app_credentials->oauth_token, $platform_account->platform_app_credentials->oauth_token_secret );  
            $user_details = $twitter->get_user_details( $platform_account->platform_user_details->id );
         
            
            $platform_account->platform_user_details = json_encode(array(
                'id' 		  => $user_details->id,
                'name' 		  => $user_details->name, 
                'screen_name' => $user_details->screen_name,
                'avatar' 	  => $user_details->profile_image_url
            ));
            
            skp_update_platform_account ( $platform_account );
		} 

	}

}