<?php

/**
 * Handles the saving, updating and removing of the SKP_Post of type "on_publish" that is 
 * tied to the WP_Post when saving the WP_Post.
 *
 * The SKP_Post with type "on_publish" should be unique for each WP_Post
 * 
 * @param int $post_id
 * @param WP_Post $post
 *
 */
function skp_save_on_publish_post( $post_id, $post ) {

	$settings = get_option( 'skp_settings', array() );

	// Do nothing if the share on publish options is disabled for the post
	$share_on_publish = get_post_meta( $post_id, '_skp_share_on_publish', true );

	if( empty( $share_on_publish ) )
		return;

	// Skip auto-drafts
	if( $post->post_status == 'auto-draft' )
		return;

	// Check to see if we support this custom post type
	if( !in_array( $post->post_type, skp_get_supported_post_types() ) )
		return;


	// Get the SKP_Post for this post
	$args = array(
		'type' 	  => 'on_publish',
		'post_id' => $post->ID
	);
	
	$skp_posts = skp_get_posts( $args );

	if( !empty( $skp_posts ) )
		$skp_post = $skp_posts[0];
	else
		$skp_post = false;


	// If this post is not in pending mode, do nothing with it
	if( $skp_post !== false && $skp_post->status != 'pending' )
		return;


	/**
	 * If the post is a draft and the SKP_Post exists, remove it altogether as we don't want the cron job
	 * to come and post it to the social platforms
	 *
	 */
	if( $post->post_status == 'draft' ) {

	    if( $skp_post !== false ){
	       $removed = skp_remove_post( $skp_post );  
           skp_update_post_count($skp_post->post_id,'minus');
		}

		return;

	}


	// Returns only the ids of the platforms accounts
	$platform_accounts_ids = get_post_meta( $post->ID, '_skp_platform_account', true );

	/**
	 * The $post_data variable is used to store and manipulate the values of the wp_post,
	 * that will be transmited to the SKP_Post and later inserted/updated in the db
	 *
	 */
	$post_data = array(
		'post_id' 			=> $post->ID,
		'type'	  			=> 'on_publish',
		'date'	  			=> $post->post_date,
		'platform_accounts' => $platform_accounts_ids
	);

	// Specific post data for new SKP_Post
	if( $skp_post === false )
		$post_data['status'] = 'pending';

	// Specific data for existing SKP_Post
	if( $skp_post !== false )
		$post_data = array_merge( $skp_post->to_array(), $post_data );


	/**
	 * Apply a filter on the post_data variable, in case we need to add extra functionality
	 * to the SKP_Post that is updated/inserted into the db
	 *
	 * @param array $post_data - the data of the SKP_Post that is being handled
	 * @param WP_Post $post    - the current WP_Post
	 *
	 */
	$post_data = apply_filters( 'skp_on_publish_post_data', $post_data, $post );

	/**
	 * If the SKP_Post doesn't exist, add it to the db
	 *
	 */
	if( $skp_post === false ){
        $skp_post = skp_insert_post( $post_data, true );
        skp_update_post_count($post->ID,'plus');
	}
		
	

	/**
	 * If the SKP_Post exists, update it
	 *
	 */
	if( $skp_post !== false )
		$updated = skp_update_post( new SKP_Post( (object)$post_data ) );


	/**
	 * If the status of the post is set to publish, we want also to post the SKP_Post to
	 * the platform accounts
	 *
	 */
	if( $post->post_status == 'publish' ) {

		do_action( 'skp_post_to_platforms', $skp_post );

	}

}
add_action( 'save_post', 'skp_save_on_publish_post', 10, 2 );


/**
 * Outputs HTML in the Settings page for the Share on Publish module
 *
 * @param array $settings - the settings of the plugin
 *
 */
function skp_on_publish_add_settings( $settings = array() ) {

	echo '<tr>';
	    echo '<th scope="row">';
	        echo '<label for="share_on_publish">' . __( 'Share when publishing a new Post', 'skp-textdomain' ) . '</label>';
	    echo '</th>';
	    echo '<td>';
	        echo '<div class="skp-form-field">';
	            echo '<p class="description">';
	            	echo '<input id="share_on_publish" type="checkbox" name="skp_settings[share_on_publish]" ' . ( !empty( $settings['share_on_publish'] ) ? 'checked="checked"' : '' ) . ' />';
	            	echo __( 'Check if you wish to share on the social platforms when publishing a new post.', 'skp-textdomain' ); 
	            echo '</p>';
	        echo '</div>';
	    echo '</td>';
	echo '</tr>';

}
add_action( 'skp_tab_general_settings_before_fields', 'skp_on_publish_add_settings', 10 );


SKP_Meta_Box_Share_On_Publish::init();