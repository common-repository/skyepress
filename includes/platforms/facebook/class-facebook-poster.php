<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


Class SKP_Facebook_Poster extends SKP_Platform_Poster {

	/**
	 * Initialise component
	 *
	 */
	public static function init() {

		add_action( 'skp_post_to_account_facebook_profile', array( get_called_class(), 'post_to_account' ), 10, 3 );
        
        add_action( 'skp_post_to_account_facebook_page', array( get_called_class(), 'post_to_account' ), 10, 3 );

	}

	/**
	 * Posts to Facebook Profile
	 *
	 */
	public static function post( $platform_account, $skp_post, $skp_post_data ) {

		// Initialise Facebook API
		$facebook = new SKP_Facebook($platform_account->platform_app_credentials->app_id,$platform_account->platform_app_credentials->app_secret,$platform_account->platform_app_credentials->access_token);
        
        //Prepare array to be passed to Facebook 
        $data['message'] = self::get_platform_content( $platform_account, $skp_post );
        $data['link'] = get_permalink($skp_post_data['post_id']);
        
        //check if posting featured image is enabled
        if( isset( $skp_post_data['attachment_url'] ) ){
            $data['picture'] = $skp_post_data['attachment_url'];
        }
        
		// Post to Facebook
        if($platform_account->platform_slug == 'facebook_profile'){
            
            try {
                $platform_response = $facebook->post_to_profile( $data ); 
            } catch( Exception $e ) {
                $platform_response = $e->getResponseData();
            }
             
        } elseif($platform_account->platform_slug == 'facebook_page'){
            
            try {
                $platform_response = $facebook->post_to_page($platform_account->platform_user_details->id, $data ); 
            } catch( Exception $e ) {
                $platform_response = $e->getResponseData();
            }
            
        }
        
        //store original response
        $response['original'] = $platform_response;
        
        //format response
        if( isset($platform_response['error']) ){
            $status = 'error';
            $response['formatted'] = array('error' => true, 'message' => $platform_response['error']['type'] . " " . $platform_response['error']['code'] . ': ' . $platform_response['error']['message'] );
        } else {
            $status = 'posted';
            
            $id_parts = explode('_',$platform_response['id']);
            
            $response['formatted'] = array( 
                'id' => $platform_response['id'],
                'permalink' => 'https://www.facebook.com/' . $id_parts[0] . '/posts/' . $id_parts[1]
            );
        }  
        
        

        //Store response in the database
        skp_save_response( $response, $status, $skp_post, $platform_account );
        
	}

}