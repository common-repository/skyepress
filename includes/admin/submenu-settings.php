<?php 

    // Exit if accessed directly
    if ( ! defined( 'ABSPATH' ) ) exit;


	/**
	 * Function that creates the sub-menu item and page for the content location of the share buttons
	 *
	 *
	 * @return void
	 *
	 */

	function skp_register_settings_subpage() {

		add_submenu_page( 'skp-skyepress', __('Settings', 'skp-textdomain'), __('Settings', 'skp-textdomain'), 'manage_options', 'skp-settings', 'skp_settings_subpage' );

	}
	add_action( 'admin_menu', 'skp_register_settings_subpage', 30 );


	/**
	 * Function that adds content to the content icons subpage
	 *
	 * @return string
	 *
	 */
	function skp_settings_subpage() {
 	  
        $active_tab = ( !empty( $_GET['skp-tab'] ) ? $_GET['skp-tab'] : 'platform-accounts' );
        $subpage    = ( !empty($_GET['skp-subpage']) ) ? $_GET['skp-subpage'] : '';
        $settings   = get_option( 'skp_settings'); 
        
        if( empty( $subpage ) ) {

            include_once 'views/view-submenu-page-settings.php';

        } else {

            /**
             * Hook to add custom content for the settings subpages from anywhere in the plugin
             *
             * @param string $subpage
             * @param string $active_tab
             * @param array  $settings
             *
             */
            do_action( 'skp_settings_subpage', $subpage, $active_tab, $settings );

        }
    
	}


	/**
	 * Register custom Settings
	 *
	 */
	function skp_settings_register_settings() {

		register_setting( 'skp_settings', 'skp_settings', 'skp_settings_sanitize' );

	}
	add_action( 'admin_init', 'skp_settings_register_settings' );


	/**
	 * Filter and sanitize settings
	 *
	 * @param array $new_settings
	 *
	 */
	function skp_settings_sanitize( $new_settings ) {

		return $new_settings;

	}


    /**
     * Displays an admin notice on the settings page
     *
     */
    function skp_settings_admin_notice() {

        if( empty( $_GET['page'] ) || $_GET['page'] != 'skp-settings' )
            return;

        if( !empty( $_GET['settings-updated'] ) ){
            skp_add_admin_notice( 'success', __( 'Settings saved.', 'skp-textdomain' ) );
        }
        
        if( !empty( $_GET['message'] ) ){
            switch ( $_GET['message'] ){
                case 1:
                    skp_add_admin_notice('error', __( 'Oops. Something went wrong, please try again.', 'skp-textdomain' ) );
                    break; 
                case 2:
                    skp_add_admin_notice('success', __( 'Social account successfully added.', 'skp-textdomain' ) );
                    break; 
                case 3:
                    skp_add_admin_notice('success', __( 'Social account successfully removed.', 'skp-textdomain' ) );
                    break; 
                case 4:
                    skp_add_admin_notice('error', __( 'Please enter your Facebook App ID and App Secret.', 'skp-textdomain' ) );
                    break; 
                case 5:
                    skp_add_admin_notice('error', __( 'Please enter your LinkedIn Client ID and Client Secret.', 'skp-textdomain' ) );
                    break; 
                case 6:
                    skp_add_admin_notice('error', __( 'Facebook API has returned an error. Please check if your credentials are correct and try again.', 'skp-textdomain' ) );
                    break; 
                case 7:
                    skp_add_admin_notice('error', __( 'LinkedIn API has returned an error. Please check if your credentials are correct and try again.', 'skp-textdomain' ) );
                    break; 
                case 8:
                    skp_add_admin_notice('error', __( 'Twitter API has returned an error. Please check if your credentials are correct or if you have a valid callback url in your Twitter App and try again.', 'skp-textdomain' ) );
                    break; 
            }
        }
        

    }
    add_action( 'admin_init', 'skp_settings_admin_notice' );
