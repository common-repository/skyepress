<?php 

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Add action links on the plugins page
 *
 */
function skp_uninstall_add_action_links( $links ) {

    $new_links = array();

    if( current_user_can( 'manage_options' ) )
        $new_links[] = '<span class="delete"><a href="' . add_query_arg( array( 'page' => 'skp-uninstall' ) , admin_url( 'admin.php' ) ) . '">' . __( 'Uninstall', 'skp-textdomain' ) . '</a></span>';

    return array_merge( $links, $new_links );

}    
add_filter( 'plugin_action_links_' . SKP_PLUGIN_BASENAME, 'skp_uninstall_add_action_links' );    


/**
 * Function that creates the sub-menu page for the uninstall page
 *
 *
 * @return void
 *
 */
function skp_register_uninstall_subpage() {
	add_submenu_page( null , __('Uninstall', 'skp-textdomain'), __('Uninstall', 'skp-textdomain'), 'manage_options', 'skp-uninstall', 'skp_uninstall_subpage' );
}
add_action( 'admin_menu', 'skp_register_uninstall_subpage', 99 );


/**
 * Function that adds content to the subpage
 *
 * @return string
 *
 */
function skp_uninstall_subpage() {
   	include_once 'view-uninstall.php';
}


/**
 * Function that removes all the plugin data and deactivates the plugin
 *
 */
function skp_uninstall_plugin(){
    
    if( !isset( $_POST['skp_tkn'] ) || !wp_verify_nonce( $_POST['skp_tkn'], 'skp_uninstall' ) )
		return;
    
    if( !current_user_can( 'manage_options' ) )
    	return;
        
    if( !isset( $_POST['skp_uninstall_plugin'] ) )
		return;
        
    if( $_POST['skp_uninstall_plugin'] !== 'REMOVE' )
		return;
    
    // Proceed
    
    global $wpdb;
    
    // Remove options  
	$options = $wpdb->get_results( "SELECT * FROM {$wpdb->options} WHERE option_name LIKE 'skp_%'", ARRAY_A );
	if( !empty( $options ) ) {
		foreach( $options as $option ) {
			delete_option( $option['option_name'] );
		}
	}
     
    // Remove database tables 
    $prefix = $wpdb->prefix . SKP_Database::get_prefix();             
    $wpdb->query("DROP TABLE IF EXISTS {$prefix}posts"); 
    $wpdb->query("DROP TABLE IF EXISTS {$prefix}platform_accounts"); 
    $wpdb->query("DROP TABLE IF EXISTS {$prefix}schedules"); 
    
    // Remove any user meta
    $user_metas = $wpdb->get_results( "SELECT * FROM {$wpdb->usermeta} WHERE meta_key LIKE 'skp_%'", ARRAY_A );
    if( !empty( $user_metas ) ) {
        foreach( $user_metas as $user_meta ) {
            delete_user_meta( $user_meta['user_id'], $user_meta['meta_key'] );
        }
    }

    // Deactivate the plugin
    deactivate_plugins( SKP_PLUGIN_BASENAME );
    
    wp_redirect( admin_url('plugins.php') );
    exit;
    
}
add_action( 'admin_init', 'skp_uninstall_plugin' );