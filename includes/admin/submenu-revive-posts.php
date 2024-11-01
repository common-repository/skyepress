<?php 

    // Exit if accessed directly
    if ( ! defined( 'ABSPATH' ) ) exit;
    

	/**
	 * Function that creates the sub-menu item and page for reviving old posts
	 *
	 *
	 * @return void
	 *
	 */

	function skp_register_revive_posts_subpage() {
		add_submenu_page( 'skp-skyepress', __('Revive Posts', 'skp-textdomain'), __('Revive Posts', 'skp-textdomain'), 'manage_options', 'skp-revive-posts', 'skp_revive_posts_subpage' );
	}
	add_action( 'admin_menu', 'skp_register_revive_posts_subpage', 20 );


	/**
	 * Function that adds content to the content icons subpage
	 *
	 * @return string
	 *
	 */
	function skp_revive_posts_subpage() {
	   	   
        $subpage = ( !empty($_GET['subpage']) ) ? $_GET['subpage'] : '';
        
        //if editing a post and form was not submitted ($_POST is empty), load form data from database
        if(!empty($_GET['skp_schedule_id']) && empty($_POST['skp_schedule_form_action'])) {
            $form_data = skp_get_schedule( (int)$_GET['skp_schedule_id'] ); 
            $form_data = $form_data->to_array('skp_schedule_');
        //if form was submitted, load form data from $_POST
        } elseif(!empty($_POST['skp_schedule_form_action'])){
             $form_data = $_POST;
        } else {
            $form_data = false;
        }
        
        switch($subpage){
            case 'add-schedule':
                $page_title = "Add New Schedule";
                include_once 'views/view-submenu-page-revive-posts-schedule-form.php';
                break;
            case 'edit-schedule':
                $page_title = "Edit Schedule";
                include_once 'views/view-submenu-page-revive-posts-schedule-form.php';
                break;
            default:
                include_once 'views/view-submenu-page-revive-posts.php';
        }
		

	}

