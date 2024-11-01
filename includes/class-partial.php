<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


Class SKP_Partial {

	/**
	 * The name of the file without its extension
	 *
	 * @var string
	 *
	 */
	private $file_name;


	/**
	 * Custom data provided to the partial
	 *
	 * @var array
	 *
	 */
	private $data;


	/**
	 * Constructor
	 *
	 * @param string $file_name - the name of the file without its extension
	 * @param array $data   	- custom data provided to the partial
	 *
	 */
	public function __construct( $file_name = '', $data = array() ) {

		$this->data 	 = $data;
		$this->file_name = $file_name;

	}


	/**
	 * Renders the HTML of the partial directly in the page
	 *
	 */
	public function render() {

		if( file_exists( SKP_PLUGIN_PARTIALS_DIR . '/' . $this->file_name . '.php' ) )
			include SKP_PLUGIN_PARTIALS_DIR . '/' . $this->file_name . '.php';

	}


	/**
	 * Returns the HTML of the partials into a string
	 *
	 * @return string
	 *
	 */
	public function get_output() {

		ob_start();

		$this->render();
		$output = ob_get_contents();

		ob_end_clean();

		return $output;

	}

}