<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


Class SKP_Twitter_Poster extends SKP_Platform_Poster {

	/**
	 * Initialise component
	 *
	 */
	public static function init() {

		add_action( 'skp_post_to_account_twitter', array( get_called_class(), 'post_to_account' ), 10, 3 );

	}
        

	/**
	 * Posts to Twitter
	 *
	 */
	public static function post( $platform_account, $skp_post, $skp_post_data ) {

		// Initialise Twitter API
        $twitter = new SKP_Twitter( $platform_account->platform_app_credentials->consumer_key, $platform_account->platform_app_credentials->consumer_secret, $platform_account->platform_app_credentials->oauth_token, $platform_account->platform_app_credentials->oauth_token_secret );
        
        // Prepare data
        $data['status'] = self::get_platform_content( $platform_account, $skp_post ) . " " . get_permalink($skp_post_data['post_id']);

        // Add Image
        if( isset( $skp_post_data['attachment_url'] ) ){
            $media = $twitter->upload_media( $skp_post_data['attachment_url'] );
            $data['media_ids'] = $media->media_id;
        }
            
		// Post to Twitter
		$platform_response = $twitter->post( $data );
        
        //store original response
        $response['original'] = $platform_response;
        
        //format response
        if( isset($platform_response->errors) ){
            $status = 'error';
            $error = $platform_response->errors;
            $response['formatted'] = array('error' => true, 'message' => $error[0]->message . ' (error code ' . $error[0]->code . ')' );
        } else {
            $status = 'posted';
            $response['formatted'] = array( 
                'id' => $platform_response->id,
                'permalink' => 'https://twitter.com/statuses/' . $platform_response->id
            );
        }   
        
        //Store response in the database
        skp_save_response( $response, $status, $skp_post, $platform_account );

	}

}