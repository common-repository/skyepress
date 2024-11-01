<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Wrapper function to retrieve plugin scheduled social posts
 * given certain arguments
 *
 * @param $args
 *
 * @return array
 *
 */
function skp_get_posts( $args = array() ) {

	$query = new SKP_Query_Posts( $args );
	
	return $query->get_results();

}


/**
 * Retrieves a single scheduled social post given the id
 *
 * @param $id
 *
 */
function skp_get_post( $id = 0 ) {

	if( $id == 0 )
		return null;

	$posts = skp_get_posts( array( 'include' => array( $id ) ) );

	if( !empty( $posts ) )
		return $posts[0];
	else
		return null;

}


/**
 * Inserts a new social post into the DB
 *
 * @param array $data - the details of the post
 * 
 * @return mixed int|null|SKP_Post - returns int, the id of the inserted row
 *									 - returns null if the insert failed
 * 									 - returns the post if $return_post is passed as true
 *
 */
function skp_insert_post( $data = array(), $return_post = false ) {

	if( empty( $data ) )
		return null;

	$handler = new SKP_Entry_Handler_Post();
	$handler->insert( $data );

	$last_inserted_id = $handler->get_last_handled_id();


	if( $last_inserted_id == 0 )
		return null;


	if( !$return_post )

		// Return the last inserted id from the db
		return $last_inserted_id;
	else

		// Return an instance of the post
		return skp_get_post( $last_inserted_id );

}


/**
 * Updates a social post's data in the DB
 * 
 * @param SKP_Post $object
 *
 * @return bool
 *
 */
function skp_update_post( SKP_Post $object = null ) {

	if( empty( $object->id ) )
		return false;

	$handler = new SKP_Entry_Handler_Post();
	$handler->update( $object );

	if( $handler->get_last_handled_id() == 0 )
		return false;
	else
		return true;

}

/**
 * Removes a schedule from the DB
 * 
 * @param SKP_Post $object
 *
 * @return bool
 *
 */
function skp_remove_post( SKP_Post $object = null ) {

	if( empty( $object->id ) )
		return false;

	$handler = new SKP_Entry_Handler_Post();
	$handler->remove( $object );

	if( $handler->get_last_handled_id() == 0 )
		return false;
	else
		return true;	

}


/**
 * Add default content just before posting if none has been set so far
 *
 * @param SKP_Post $skp_post
 *
 * @return SKP_Post
 *
 */
function skp_post_to_platforms_post_default_content( SKP_Post $skp_post ) {

	// Return if this share post doesn't have attached a wp_post
	if( $skp_post->get( 'post_id' ) == 0 )
		return $skp_post;
    
    $settings = get_option( 'skp_settings', array() );
    
    $content = json_decode( $skp_post->get( 'content' ), true );
    
    // Loop through platform messages
    if( ! empty( $content ) ) {

    	foreach( $content as $platform_unique => $message){
	        
	        // If content was entered, continue
	        if(!empty($message))
	            continue;
	        
	        // If not, use the default content from the settings page, if any
	        if(!empty($settings['custom_messages'][$platform_unique]))
	            $content[$platform_unique] = $settings['custom_messages'][$platform_unique];
	        
	        // If not, just use the post title;
	        else
	            $content[$platform_unique] =  get_the_title( $skp_post->get( 'post_id' ) );
	        
	    }

	    $skp_post->set( 'content', $content );

    }
    
	return $skp_post;

}
add_filter( 'skp_post_to_platforms_post', 'skp_post_to_platforms_post_default_content', 30 );


/**
 * Convert all tags found in the $pbcz_post just before sharing to the social platforms
 *
 * @param SKP_Post $skp_post
 *
 * @return SKP_Post
 *
 */
function skp_post_to_platforms_post_process_tags( SKP_Post $skp_post ) {

	// Return if this share post doesn't have attached a wp_post
	if( $skp_post->get( 'post_id' ) == 0 )
		return $skp_post;

	// Set up Tag Converter extra data
	$data = array(
		'post_id' => $skp_post->get( 'post_id' )
	);

	// Initialise Tag Converter
	$tag_converter = new SKP_Tag_Converter( $skp_post->get( 'content' ), $data );

	// Set the content content text
	$skp_post->set( 'content', $tag_converter->get_text_converted() );


	return $skp_post;

}
add_filter( 'skp_post_to_platforms_post', 'skp_post_to_platforms_post_process_tags', 50 );