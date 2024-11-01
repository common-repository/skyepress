<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


class SKP_Twitter {
    /**
	 * The Twitter apps consumer public key
	 *
	 */
    private $consumerKey;
    
    /**
	 * The Twitter apps consumer secret key
	 *
	 */
    private $consumerSecret;
    
    /**
	 * Callback url after authorization is performed
	 *
	 */
    private $urlCallback;
    
    /**
	 * Twitter oAuth object
	 *
	 */
    private $twitteroauth;
    
    
    /**
	 * Constructor
	 *
	 */
    function __construct($consumerKey, $consumerSecret, $oauthToken = false,$oauthTokenSecret = false) {
        
        $this->consumerKey    = $consumerKey;
        $this->consumerSecret = $consumerSecret;
        
        if($oauthToken && $oauthTokenSecret){
            $this->twitteroauth = new SKP_TwitterOAuth($this->consumerKey, $this->consumerSecret, $oauthToken, $oauthTokenSecret);
        } else {
            $this->twitteroauth = new SKP_TwitterOAuth($this->consumerKey, $this->consumerSecret);
        }

    }
    
    /**
	 * Redirects the user to twitter
	 *
	 */
    public function authorize(){
        
        $request_token = $this->twitteroauth->oauth(
            'oauth/request_token', [
                'oauth_callback' => $this->urlCallback
            ]
        );
         
        if($this->twitteroauth->getLastHttpCode() != 200) {
            wp_redirect( add_query_arg( array( 'page' => 'skp-settings', 'message' => 8), admin_url('admin.php') ) );
        }
         
        $_SESSION['oauth_token'] = $request_token['oauth_token'];
        $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
         
        $url = $this->twitteroauth->url(
            'oauth/authorize', [
                'oauth_token' => $request_token['oauth_token']
            ]
        );
         
        wp_redirect($url);
    }
    
    /**
	 * Set the callback url
     *
     * @param string
	 *
	 */
    public function set_callback_url($url){
        $this->urlCallback = $url;
    }
    
    
    /**
	 * Gets the user tokens from twitter. This function should be called in the urlCallback page.
	 *
     * @return array
     *
	 */
    public function get_user_token(){
        $connection = new SKP_TwitterOAuth(
            $this->consumerKey,
            $this->consumerSecret,
            $_SESSION['oauth_token'],
            $_SESSION['oauth_token_secret']
        );
        
        $oauth_verifier = filter_input(INPUT_GET, 'oauth_verifier');
        
        //if user canceled, redirect and show an error.
        if(empty($oauth_verifier)){
            wp_redirect( add_query_arg( array( 'page' => 'skp-settings', 'message' => 1), admin_url('admin.php') ) );
        }
         
        $token = $connection->oauth(
            'oauth/access_token', [
                'oauth_verifier' => $oauth_verifier
            ]
        );
        
        return $token;
    }
    
    
    /**
	 * Get user details
     * https://dev.twitter.com/rest/reference/get/users/show
     *
     * @param int
     *
     * @return object
	 *
	 */
    public function get_user_details($user_id){
        $user = $this->twitteroauth->get(
            "users/show", [
                "id" => $user_id
            ]
        );
        return $user;
    }
   
    /**
	 * Post a tweet
     * https://dev.twitter.com/rest/reference/post/statuses/update
     *
     * @param string
     *
     * @return object
	 *
	 */
    public function post($tweet){
        $status = $this->twitteroauth->post(
            "statuses/update", $tweet
        );
        return $status;
    }
    
    public function upload_media($image_src){
        $media = $this->twitteroauth->upload(
            "media/upload", [
                "media" => $image_src
            ]
        );
        return $media;
    }
}