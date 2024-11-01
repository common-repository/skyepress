<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * 
 * Output deactivaton modal window code on Plugins page.
 * 
 */
function skp_deactivation_modal() {

    if( !is_admin() && $pagenow !== 'plugins.php' ) {
        return;
    }

    $current_user = wp_get_current_user();
    if( !($current_user instanceof WP_User) ) {
        $email = '';
    } else {
        $email = trim( $current_user->user_email );
    }

    include_once 'view-deactivate.php';
}
add_filter( 'admin_footer', 'skp_deactivation_modal' );


/**
 * 
 * Ajax callback for sending deactivation email.
 * 
 */
function skp_send_deactivation_feedback() {

    if( isset( $_POST['data'] ) ) {
        parse_str( $_POST['data'], $form );
    }

    $message = '';
    
    $headers = array();

    $from = isset( $form['skp_disable_from'] ) ? sanitize_text_field( $form['skp_disable_from'] ) : '';

    if( $from ) {
        $headers[] = "From: " . $from;
        $headers[] = "Reply-To: " . $from;
    }
    
    $subject = "SkyePress Deactivation Notification";
    
    $message .= isset( $form['skp_disable_reason'] ) ? 'Deactivation reason: ' . sanitize_text_field( $form['skp_disable_reason'] ) : '(no reason given)';
    
    if( isset( $form['skp_disable_text'] ) ) {
        $message .= "\n\r";
        $message .= 'Message: ' . sanitize_text_field( implode('', $form['skp_disable_text']) );
    }
    
    $success = wp_mail( array('support@devpups.com', 'murgroland@gmail.com'), $subject, $message, $headers );

    die();
}
add_action( 'wp_ajax_skp_send_deactivation_feedback', 'skp_send_deactivation_feedback' );
