<?php 

    // Exit if accessed directly
    if ( ! defined( 'ABSPATH' ) ) exit;


	/**
	 * Function that creates the sub-menu item and page for the dashboard
	 *
	 *
	 * @return void
	 *
	 */

	function skp_register_dashboard_subpage() {
		add_submenu_page( 'skp-skyepress', __('Dashboard', 'skp-textdomain'), __('Dashboard', 'skp-textdomain'), 'manage_options', 'skp-dashboard', 'skp_dashboard_subpage' );
	}
	add_action( 'admin_menu', 'skp_register_dashboard_subpage', 10 );


	/**
	 * Function that adds content to the submenu page
	 *
	 * @return string
	 *
	 */
	function skp_dashboard_subpage() {
	   
        $settings = get_option( 'skp_settings', array() );
        
        $schedules = skp_get_schedules();
        $schedule_status = ( count($schedules) > 0 ) ? true : false;
        
        $pending_posts = skp_get_posts( array('status' => 'pending', 'orderby' => 'date', 'order' => 'asc', 'number' => 5) );
        
        $posted_posts = skp_get_posts( array('status' => 'posted', 'orderby' => 'date', 'order' => 'desc') );
        $posted_posts_count = count($posted_posts);
        $posted_posts = array_slice($posted_posts, 0, 5);
        
        $errored_posts = skp_get_posts( array('status' => 'error', 'orderby' => 'date', 'order' => 'desc') );
        
        $subpage = ( !empty($_GET['subpage']) ) ? $_GET['subpage'] : '';
        
        switch($subpage){
            case 'activate-cron-jobs':
                SkyePress::activation_hook();
                wp_redirect(add_query_arg( array( 'page' => 'skp-dashboard', 'message' => 1), admin_url('admin.php') ) );
                break;
            default:
                include_once 'views/view-submenu-page-dashboard.php';
        }
        
		

	}
    
    /**
     * Displays an admin notice on the dashboard page
     *
     */
    function skp_dashboard_admin_notice() {

        if( empty( $_GET['page'] ) || $_GET['page'] != 'skp-dashboard' )
            return;
        
        if( !empty( $_GET['message'] ) ){
            switch ( $_GET['message'] ){
                case 1:
                    skp_add_admin_notice('success', __( 'Cron jobs successfully activated.', 'skp-textdomain' ) );
                    break; 
                
            }
        }
        

    }
    add_action( 'admin_init', 'skp_dashboard_admin_notice' );

