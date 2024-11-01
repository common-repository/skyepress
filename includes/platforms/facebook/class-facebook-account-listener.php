<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


Class SKP_Facebook_Account_Listener {

	/**
	 * Initialise connector
	 *
	 */
	public static function init() {

		// Check for access level
		if( !current_user_can( 'manage_options' ) )
			return;

		if( empty( $_GET['skp_platform'] ) || ($_GET['skp_platform'] != 'facebook' && $_GET['skp_platform'] != 'facebook_page' && $_GET['skp_platform'] != 'facebook_profile') )
			return;
  
		add_action( 'admin_init', array( get_called_class(), 'connect_account' ) );

		add_action( 'admin_init', array( get_called_class(), 'insert_user_details' ) );
        
        add_action( 'admin_init', array( get_called_class(), 'get_platform_response' ) );

		add_action( 'admin_init', array( get_called_class(), 'remove_account' ) );

	}


	/**
	 * Connect account listener
	 *
	 */
	public static function connect_account() {
        
		if( !isset( $_POST['skp_tkn'] ) || !wp_verify_nonce( $_POST['skp_tkn'], 'skp_connect_account' ) )
			return;
            
        $settings = get_option( 'skp_settings'); 
        
        if(!empty($_POST['facebook_app_id'])) $settings['facebook_app_id'] = $_POST['facebook_app_id'];
        if(!empty($_POST['facebook_app_secret'])) $settings['facebook_app_secret'] = $_POST['facebook_app_secret'];
        
        update_option( 'skp_settings' , $settings);
        
        if(empty($_POST['facebook_app_id']) || empty($_POST['facebook_app_secret'])){
            wp_redirect( add_query_arg( array( 'page' => 'skp-settings', 'skp-subpage' => 'facebook-app-details', 'message' => 4), admin_url('admin.php') ) );
        }
      
		$facebook = new SKP_Facebook( $_POST['facebook_app_id'], $_POST['facebook_app_secret'] );
        $facebook->set_callback_url( add_query_arg( array( 'page' => $_GET['page'], 'skp_platform' => 'facebook', 'noheader' => 'true', 'facebook_app_id' => $_POST['facebook_app_id'], 'facebook_app_secret' => $_POST['facebook_app_secret'], 'skp_tkn' => wp_create_nonce('skp_get_plaform_response') ), admin_url('admin.php') ) );
        $facebook->authorize();

	}
    
    /**
	 * Get the response after authorization
	 *
	 */
    public static function get_platform_response(){
        if( !isset( $_GET['skp_tkn'] ) || !wp_verify_nonce( $_GET['skp_tkn'], 'skp_get_plaform_response' ) )
			return;

        $facebook = new SKP_Facebook( $_GET['facebook_app_id'], $_GET['facebook_app_secret'] );
        $access_token = $facebook->get_access_token();    
        
        if(empty($access_token)){
            wp_redirect( add_query_arg( array( 'page' => 'skp-settings', 'message' => 1), admin_url('admin.php') ) );
        }
                
        $facebook->set_access_token($access_token);                
        $user_info = $facebook->get_user_info();
        
        $facebook_accounts[$user_info['id']] =  array(
            'platform_unique'  => 'facebook_' . $user_info['id'],
            'platform_slug'    => 'facebook_profile',
            'platform_app_credentials' =>  array(
                'app_id' 	   => $_GET['facebook_app_id'],
                'app_secret'   => $_GET['facebook_app_secret'],
                'access_token' => $access_token
            ),
            'platform_user_details' =>  array(
                'id' 		   => $user_info['id'],
                'name' 		   => $user_info['name'],
                'avatar' 	   => $user_info['picture']['data']['url']
            )
        );
        
        $pages = $facebook->get_user_pages();

        foreach($pages['data'] as $page){
	        $facebook_accounts[$page['id']] =  array(
	            'platform_unique'  => 'facebook_' . $page['id'],
	            'platform_slug'    => 'facebook_page',
                'platform_app_credentials' =>  array(
                    'app_id' 	   => $_GET['facebook_app_id'],
                    'app_secret'   => $_GET['facebook_app_secret'],
                    'access_token' => $page['access_token']
                ),
	            'platform_user_details' =>  array(
	                'id' 		   => $page['id'],
	                'name' 		   => $page['name'],
	                'avatar' 	   => $page['picture']['data']['url']
	            )
	        );    
	    }
        
        $_SESSION['facebook_accounts'] = json_encode($facebook_accounts);
        
        wp_redirect( add_query_arg( array( 'page' => $_GET['page'], 'skp_platform' => 'facebook', 'page' => 'skp-settings', 'skp-subpage' => 'facebook-select-accounts', 'skp_tkn' => wp_create_nonce('skp_facebook_select_accounts') ), admin_url('admin.php') ) );
    }
    

	/**
	 * Insert account user details listener
	 *
	 */
	public static function insert_user_details() {

		if( !isset( $_POST['skp_tkn'] ) || !wp_verify_nonce( $_POST['skp_tkn'], 'skp_insert_user_details' ) )
			return;
        
        
        $facebook_accounts = json_decode($_SESSION['facebook_accounts'], true);
        
        if( !isset( $facebook_accounts ) || empty( $_POST['facebook_accounts'] ) ) {
            wp_redirect( add_query_arg( array( 'page' => $_GET['page'] ), admin_url('admin.php') ) );
            exit;
        }

        // Get existing accounts
        $platform_accounts = skp_get_platform_accounts( array( 'platform_slug' => 'facebook' ) ); 

        foreach( $facebook_accounts as $facebook_account ){

            if( in_array( $facebook_account['platform_unique'], $_POST['facebook_accounts']) ){

                // Delete the account if it exists
                foreach( $platform_accounts as $account ){
                    if( $account->platform_unique == $facebook_account['platform_unique'] )
                        skp_remove_platform_account( $account );
                }

                // Add the account to the db
                skp_insert_platform_account( array(
    	            'user_id' 		   => get_current_user_id(),
    	            'platform_unique'  => $facebook_account['platform_unique'],
    	            'platform_slug'    => $facebook_account['platform_slug'], 
    	        	'platform_app_credentials' => json_encode(array(
    	                'app_id' 	   => $facebook_account['platform_app_credentials']['app_id'], 
    	                'app_secret'   => $facebook_account['platform_app_credentials']['app_secret'],
                        'access_token' => $facebook_account['platform_app_credentials']['access_token']
    	            )),
    	            'platform_user_details' => json_encode(array(
    	                'id' 		   => $facebook_account['platform_user_details']['id'],
    	                'name' 		   => $facebook_account['platform_user_details']['name'],
    	                'avatar' 	   => $facebook_account['platform_user_details']['avatar']
    	            ))
    	        ) );    
            }
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

		$platform_accounts = skp_get_platform_accounts( array( 'platform_slug' => 'facebook', 'platform_unique' => $_GET['skp_platform_unique'] ) );

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

		$platform_accounts = skp_get_platform_accounts( array( 'platform_slug' => 'facebook' ) ); foreach( $platform_accounts as $platform_account ){
            
            $facebook = new SKP_Facebook( $platform_account->platform_app_credentials->app_id, $platform_account->platform_app_credentials->app_secret, $platform_account->platform_app_credentials->access_token );
            
            if($platform_account->platform_slug == 'facebook_profile'){
                $user_details = $facebook->get_user_info();
            } elseif($platform_account->platform_slug == 'facebook_page') {
                $user_details = $facebook->get_user_page_by_id($platform_account->platform_user_details->id);
            }
                     
            $platform_account->platform_user_details = json_encode(array(
                'id' 		  => $user_details['id'],
                'name' 		  => $user_details['name'],
                'avatar' 	  => $user_details['picture']['data']['url']
            ));

            skp_update_platform_account ( $platform_account );
		} 


	}
    
}
