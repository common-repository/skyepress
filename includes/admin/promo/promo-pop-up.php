<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Includes the promotional pop-ups depending on the page the admin is on
 *
 */
function skp_promo_pop_up() {

	if( empty( $_GET['page'] ) )
		return;

	if( $_GET['page'] == 'skp-settings' ) {
		include 'views/view-promo-pop-up-platform-accounts.php';
	}

	if( $_GET['page'] == 'skp-revive-posts' ) {
		include 'views/view-promo-pop-up-schedules.php';
	}

}
add_action( 'admin_footer', 'skp_promo_pop_up' );


/**
 * Include assets
 *
 */
function skp_promo_enqueue_scripts() {

	wp_register_script( 'skp-promo-script', SKP_PLUGIN_DIR_URL . 'includes/admin/promo/assets/js/promo.js', array( 'jquery' ), SKP_VERSION );
	wp_enqueue_script( 'skp-promo-script' );
    
	wp_register_style( 'skp-promo-style', SKP_PLUGIN_DIR_URL . 'includes/admin/promo/assets/css/promo.css', array(), SKP_VERSION );
	wp_enqueue_style( 'skp-promo-style' );

}
add_action( 'admin_enqueue_scripts', 'skp_promo_enqueue_scripts' );