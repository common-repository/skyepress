<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

require_once('Facebook/autoload.php');

class SKP_Facebook {
    
    /**
	 * Facebook Graph Version
	 *
	 */
    private $graph_version = 'v2.8';    
    
    /**
	 * Callback url after authorization is performed
	 *
	 */
    private $callback_url;
    
    /**
	 * Facebook Class
	 *
	 */
    private $facebook;
    
    
    /**
	 * Constructor
	 *
	 */
    function __construct($app_id, $app_secret, $access_token = false) {
        
       
        
        
        $this->facebook = new Facebook\Facebook([
            'app_id' => $app_id,
            'app_secret' => $app_secret,
            'default_graph_version' => $this->graph_version
        ]);
        
        if( !empty( $access_token ) )
            $this->set_access_token($access_token);
        
    }
    
    /**
	 * Redirects the user to facebook
	 *
	 */
    public function authorize(){
        
        $helper = $this->facebook->getRedirectLoginHelper();
        $permissions = ['email','public_profile','publish_pages','user_posts','user_about_me','publish_actions','manage_pages']; 
        $loginUrl = $helper->getLoginUrl($this->callback_url, $permissions);
        
        wp_redirect($loginUrl);
        
    }
    
    /**
	 * Set the callback url
     *
     * @param string
	 *
	 */
    public function set_callback_url($url){
        $this->callback_url = $url;
    }
    
    /**
	 * Set the access_token
     *
     * @param string
	 *
	 */
    public function set_access_token($token){
        $this->facebook->setDefaultAccessToken($token);
    }
    
    
    /**
	 * Gets the access token from facebook. This function should be called in the $callback_url page.
	 *
     * @return string
     *
	 */
    public function get_access_token(){
        
        $helper = $this->facebook->getRedirectLoginHelper();
        try {
            $accessToken = $helper->getAccessToken();
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            //log the error. 'Graph returned an error: ' . $e->getMessage()
            wp_redirect( add_query_arg( array( 'page' => 'skp-settings', 'skp-subpage' => 'facebook-app-details', 'message' => 6), admin_url('admin.php') ) );
            exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            wp_redirect( add_query_arg( array( 'page' => 'skp-settings', 'skp-subpage' => 'facebook-app-details', 'message' => 6), admin_url('admin.php') ) );
            exit;
        }
        
        return (string) $accessToken;
    }
    
    /**
	 * Get user info
	 *
     * @return array
     *
	 */
    public function get_user_info(){

        $response = $this->facebook->get('/me?fields=id,name,picture');
        return $response->getDecodedBody();

    }
    
    /**
	 * Get facebook users pages.
	 *
     * @return array
     *
	 */
    public function get_user_pages(){
        
        $response = $this->facebook->get('/me/accounts?fields=id,name,picture,access_token');
        return $response->getDecodedBody();
        
    }
    
    /**
	 * Get facebook users page by id.
	 *
     * @return array
     *
	 */
    public function get_user_page_by_id($page_id){
        
        $response = $this->facebook->get('/' . $page_id . '?fields=id,name,picture,access_token');
        return $response->getDecodedBody();
        
    }
    
    /**
	 * Posts a message to the users feed
     *
     * https://developers.facebook.com/docs/graph-api/reference/v2.8/user/feed
     *
     * @params array $data = array(
     *       message, 
     *       link
     *         picture,
     *         name,
     *         caption,
     *         description                
     *   )
     *      
	 *
     * @return array
     *
	 */
    public function post_to_profile($data){        
        $response = $this->facebook->post('/me/feed', $data);      
        return $response->getDecodedBody();  
    }
    
    /**
	 * Posts a message to the users page
     *
     * https://developers.facebook.com/docs/graph-api/reference/v2.8/page/feed
     *
     * @params int   $page_id
     * @params array $data = array(
     *       message, 
     *       link
     *         picture,
     *         name,
     *         caption,
     *         description                
     *   )
     *      
	 *
     * @return array
     *
	 */
    public function post_to_page($page_id,$data){        
        $response = $this->facebook->post('/' . $page_id . '/feed', $data);      
        return $response->getDecodedBody();  
    }

}