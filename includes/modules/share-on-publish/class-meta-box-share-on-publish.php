<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


Class SKP_Meta_Box_Share_On_Publish {
	
	/**
	 * Initialize meta-box
	 *
	 */
	public static function init() {

		// Modify the custom content of the post
		add_filter( 'skp_post_to_platforms_post', array( get_called_class(), 'add_custom_content_to_post' ), 20 );

		// For below hooks the user needs to have permissions
		if( !current_user_can( 'manage_options' ) )
			return;

		// Add the meta-box
		add_action( 'add_meta_boxes', array( get_called_class() , 'add' ) );

		// Save meta values
		add_action( 'save_post', array( get_called_class(), 'save' ) );

	}


	/**
	 * Adds the meta-box to all supported custom post types
	 *
	 */
	public static function add() {

		/**
		 * Return if the module is not active
		 *
		 */


		global $post;

		/**
		 * Returns if post has already been published
		 *
		 */
		if( $post->post_status != 'auto-draft' && $post->post_status != 'draft' && $post->post_status != 'future' )
			return;

		/**
		 * Return if this post has already been shared on the platforms
		 *
		 */
		$args = array(
			'post_id' => $post->ID,
			'type'	  => 'on_publish'
		);

		$skp_posts = skp_get_posts( $args );

		if( !empty( $skp_posts ) )
			$skp_post = $skp_posts[0];
		else
			$skp_post = false;

		if( $skp_post !== false && $skp_post->get( 'status' ) != 'pending' )
			return;


		/**
		 * Add the meta-box if everything checks out
		 *
		 */
		add_meta_box( 'skp_post_custom_share_on_publish', __( 'Share on Post Publish', 'skp-textdomain' ), array( get_called_class(), 'display' ), skp_get_supported_post_types() );

	}


	/**
	 * Outputs the meta-box HTML
	 *
	 */
	public static function display( $post ) {

		// Get plugin settings
		$settings = skp_get_settings();

		// Get meta data
		$custom_content    = get_post_meta( $post->ID, '_skp_post_custom_content', true );
		$platform_accounts = json_decode( get_post_meta( $post->ID, '_skp_platform_account', true ), ARRAY_A );
		$share_on_publish  = get_post_meta( $post->ID, '_skp_share_on_publish', true );


		// If the platform accounts are not set, set the defaults to all platforms
		if( !metadata_exists( 'post', $post->ID, '_skp_platform_account' ) ) {

			// This is a php v.5.3 minimum, so another solution may be welcomed
			$platform_accounts_ids = array_map( function($o) { return $o->id; }, skp_get_platform_accounts() );
			$platform_accounts 	   = $platform_accounts_ids;

		}

		// Share post on publish
		if( !metadata_exists( 'post', $post->ID, '_skp_share_on_publish' ) ) {

			$share_on_publish = ( !empty( $settings['share_on_publish'] ) ? true : false );

		} else {

			if( !empty( $share_on_publish ) )
				$share_on_publish = true;
			else
				$share_on_publish = false;

		}


		// Partial data
		$data = array(
			'post-id' 	=> $post->ID,
			'form-data'	=> array(
				'_skp_platform_account'	 => ( !empty( $platform_accounts ) ? $platform_accounts : array() ),
				'_skp_post_custom_content'  => ( !empty( $custom_content ) ? json_decode( $custom_content, ARRAY_A ) : '' ),
				'_skp_share_on_publish'	 => $share_on_publish
			)
		);

		/**
		 * Change the data passed to the the post custom content meta-box partial
		 *
		 * @param array $data
		 *
		 */
		$data = apply_filters( 'skp_meta_box_partial_data_share_on_publish', $data );

		// Display meta-box partial
		skp_get_partial( 'meta-box-share-on-publish', $data );

	}


	/**
	 * Save the meta data upon post save
	 *
	 * @param int $post_id
	 *
	 */
	public static function save( $post_id ) {

		// Skip autosaves
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

		// Check the nonce
		if( !isset( $_POST['skp_tkn'] ) || !wp_verify_nonce( $_POST['skp_tkn'], 'skp_meta_box_share_on_publish' ) )
			return;


		// Update share on publish bool
		if( !empty( $_POST['_skp_share_on_publish'] ) )
			$share_on_publish = sanitize_text_field( $_POST['_skp_share_on_publish'] );
		else
			$share_on_publish = '';

		update_post_meta( $post_id, '_skp_share_on_publish', $share_on_publish );


		// Update the post's custom content
		if( ! empty( $_POST['_skp_post_custom_content'] ) && is_array( $_POST['_skp_post_custom_content'] ) )
            
			$custom_content = json_encode( $_POST['_skp_post_custom_content'] ); 
		else
			$custom_content = '';
		
		update_post_meta( $post_id, '_skp_post_custom_content', $custom_content );


		// Update the platform accounts
		if( ! empty( $_POST['_skp_platform_account'] ) && is_array( $_POST['_skp_platform_account'] ) )
			$platform_accounts = json_encode( $_POST['_skp_platform_account'] );
		else
			$platform_accounts = '';

		update_post_meta( $post_id, '_skp_platform_account', $platform_accounts );


		/**
		 * Action hook to allow extra value saving for this meta-box
		 *
		 * @param int $post_id 	- the id of the WP_Post
		 *
		 */
		do_action( 'skp_meta_box_save_share_on_publish', $post_id );

	}


	/**
	 * Add the custom values saved in the meta data to the SKP_Post
	 * that will be shared
	 *
	 * @param SKP_Post $skp_post
	 *
	 */
	public static function add_custom_content_to_post( SKP_Post $skp_post ) {

		// Return if this share post doesn't have attached a wp_post
		if( $skp_post->get( 'post_id' ) == 0 )
			return $skp_post;

		if( $skp_post->get( 'type' ) != 'on_publish' )
			return $skp_post;

		/**
		 * Handle the custom content
		 *
		 */
		if( $skp_post->get( 'content' ) == '' ) {

			// Get the custom content
			$custom_content = get_post_meta( $skp_post->get( 'post_id' ), '_skp_post_custom_content', true );

			// Set the custom content
			$skp_post->set( 'content', $custom_content );

		}

		/**
		 * Handle custom platform accounts
		 *
		 */
		$platform_accounts = json_decode( get_post_meta( $skp_post->get( 'post_id' ), '_skp_platform_account', true ), ARRAY_A );

		if( !empty( $platform_accounts ) && is_array( $platform_accounts ) )
			$skp_post->set( 'platform_accounts', $platform_accounts );


		// Return the post
		return $skp_post;

	}

}