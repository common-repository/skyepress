<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


Class SKP_Tag_Converter {

	/**
	 * The initial text passed through the tag converter
	 *
	 * @var string
	 *
	 */
	private $text_initial = '';

	/**
	 * The text after all tags have been converted
	 *
	 * @var string
	 *
	 */
	private $text_converted = '';

	/**
	 * Custom data passed 
	 *
	 * @var array
	 *
	 */
	private $data;


	/**
	 * Constructor
	 *
	 */
	public function __construct( $text = '', $data = array() ) {

		/**
		 * Set up default data
		 *
		 */
		$this->text_initial   = $text;
		$this->text_converted = $text;
		$this->data 		  = $data;

		/**
		 * Get all tags and hook convertion methods and filters
		 *
		 */
		$tags = $this->get_tags();

		if( !empty( $tags ) ) {

			foreach( $tags as $tag_name ) {

				/**
				 * Hook in class methods to process convertion
				 *
				 */
				if( method_exists( $this, 'process_tag_' . $tag_name ) )
					$this->text_converted = str_replace( '{{' . $tag_name . '}}', call_user_func( array( $this, 'process_tag_' . $tag_name ) ), $this->text_converted );

				/**
				 * Apply filters to handle tag conversion from the outside
				 *
				 */
				$this->text_converted = str_replace( '{{' . $tag_name . '}}', apply_filters( 'skp_tag_' . $tag_name, '', $this->data ), $this->text_converted );

			}

		}

	}


	/**
	 * Returns all supported tags
	 *
	 * @return array
	 *
	 */
	private function get_tags() {

		return apply_filters( 'skp_tag_converter_tags', array( 'post_title', 'post_excerpt' ) );

	}


	/**
	 * Returns the converted text
	 *
	 * @return string
	 *
	 */
	public function get_text_converted() {

		return $this->text_converted;

	}


	/** 
	 * Replaces the "post_title" tag with the needed content
	 *
	 * @return string
	 *
	 */
	private function process_tag_post_title() {

		if( empty( $this->data['post_id'] ) )
			return '';

		return get_the_title( (int)$this->data['post_id'] );

	}


	/** 
	 * Replaces the "post_excerpt" tag with the needed content
	 *
	 * @return string
	 *
	 */
	private function process_tag_post_excerpt() {

		if( empty( $this->data['post_id'] ) )
			return '';

		// Get wp_post by id
		$post = get_post( (int)$this->data['post_id'] );

		// Check to see if the post has an excerpt
		if( !empty( $post->post_excerpt ) )
			$excerpt = $post->post_excerpt;

		// If not, strip the content
		elseif( !empty( $post->post_content ) ) {

			$excerpt = strip_shortcodes( $post->post_content );
			$excerpt = wp_trim_words( $excerpt, apply_filters( 'skp_tag_post_excerpt_length', 25 ), '' );

		} else 
			$excerpt = '';


		return $excerpt;

	}

}