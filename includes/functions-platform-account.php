<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Wrapper function to retrieve linked platform accounts
 * given certain arguments
 *
 * @param $args
 *
 */
function skp_get_platform_accounts( $args = array() ) {

	$query = new SKP_Query_Platform_Accounts( $args );

	return $query->get_results();

}

/**
 * Retrieves a single platform account given the id
 *
 * @param int $id
 *
 */
function skp_get_platform_account( $id = 0 ) {

	if( $id == 0 )
		return null;

	$platform_accounts = skp_get_platform_accounts( array( 'include' => array( $id ) ) );

	if( !empty( $platform_accounts ) )
		return $platform_accounts[0];
	else
		return null;

}


/**
 * Retrieves a single platform account by the provided platform unique
 * 
 * @param string $platform_unique
 *
 */
function skp_get_platform_account_by_platform_unique( $platform_unique = '' ) {

	if( empty( $platform_unique ) )
		return null;

	$platform_accounts = skp_get_platform_accounts( array( 'platform_unique' => $platform_unique ) );

	if( ! empty( $platform_accounts ) )
		return $platform_accounts[0];
	else
		return null;

}


/**
 * Inserts a new social account post into the DB
 *
 * @param array $data - the details of the post
 * 
 * @return mixed int|null - returns int, the id of the inserted row
 *						  - returns null if the insert failed
 *
 */
function skp_insert_platform_account( $data = array() ) {

	if( empty( $data ) )
		return null;

	$handler = new SKP_Entry_Handler_Platform_Account();
	$handler->insert( $data );

	$last_inserted_id = $handler->get_last_handled_id();

	if( $last_inserted_id == 0 )
		return null;

	return $last_inserted_id;

}


/**
 * Updates a social platform account data in the DB
 * 
 * @param SKP_Platform_Account $object
 *
 * @return bool
 *
 */
function skp_update_platform_account( SKP_Platform_Account $object = null ) {

	if( empty( $object->id ) )
		return false;

	$handler = new SKP_Entry_Handler_Platform_Account();
	$handler->update( $object );

	if( $handler->get_last_handled_id() == 0 )
		return false;
	else
		return true;

}


/**
 * Removes a social platform account from the DB
 * 
 * @param SKP_Platform_Account $object
 *
 * @return bool
 *
 */
function skp_remove_platform_account( SKP_Platform_Account $object = null ) {

	if( empty( $object->id ) )
		return false;

	$handler = new SKP_Entry_Handler_Platform_Account();
	$handler->remove( $object );

	if( $handler->get_last_handled_id() == 0 )
		return false;
	else
		return true;	

}

/**
 * Saves the response in the DB after posting to a social platform
 * 
 * @param $response array
 * @param $skp_post object 
 * @param $platform_account object
 *
 */
function skp_save_response( $response, $status, $skp_post, $platform_account ){
    
    $platform_response = ( isset($skp_post->response) ) ? json_decode($skp_post->response,true) : array();
    
    $platform_response[$platform_account->platform_unique] = $response; 
    
    $skp_post->response = json_encode($platform_response);
    
    if($skp_post->status != 'error'){
        $skp_post->status = $status;
    }
    
    skp_update_post($skp_post);
}

/**
 * Calls the update_user_details methods for all available platforms
 *
 */
function skp_update_platforms_user_details() {
    
    $updates_plaftorms = array();
    
    $platforms = skp_get_platform_accounts();
    
    foreach( $platforms as $platform ) {

        $platform_name_parts = explode( '_', $platform->platform_slug );
        $platform_name 		 = $platform_name_parts[0];

        if(!in_array($platform_name, $updates_plaftorms)) {
            $updates_plaftorms[] = $platform_name;

            $class_name = 'SKP_' . $platform_name . '_Account_Listener';

            if( class_exists( $class_name ) && method_exists( $class_name, 'update_user_details' ) ) {
            	call_user_func( $class_name . '::update_user_details' );
            }
            	
        }
        
    }
    
}
add_action('skp_cron_update_platform_user_details', 'skp_update_platforms_user_details');