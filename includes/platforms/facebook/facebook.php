<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Add Facebook to the platforms array
 *
 * @param array $platforms
 *
 * @return array
 *
 */
function skp_add_platform_facebook( $platforms = array() ) {

	$platforms['facebook_profile'] = __( 'Facebook Profile', 'skp-textdomain' );
    $platforms['facebook_page']    = __( 'Facebook Page', 'skp-textdomain' );

	return $platforms;

}
add_filter( 'skp_get_platforms', 'skp_add_platform_facebook', 11 );


/**
 * Initialise Facebook components
 *
 */
function skp_init_components_facebook() {

	SKP_Facebook_Account_Listener::init();
	SKP_Facebook_Poster::init();

}
add_action( 'init', 'skp_init_components_facebook' );


/**
 * Adds the platform accounts to the "Accounts" tab in the "Settings" page
 *
 * @param array $settings
 *
 */
function skp_add_platform_accounts_to_settings_facebook( $settings ) {

	$platform_accounts = skp_get_platform_accounts( array('platform_slug' => 'facebook') );

?>
    <input type="hidden" name="skp_settings[facebook_app_id]" value="<?php echo ( !empty($settings['facebook_app_id'])) ? esc_attr($settings['facebook_app_id']) : ''; ?>" />
    <input type="hidden" name="skp_settings[facebook_app_secret]" value="<?php echo ( !empty($settings['facebook_app_secret'])) ? esc_attr($settings['facebook_app_secret']) : ''; ?>" />
    <div class="skp-platform-accounts-column">
        <h4><?php echo __( 'Facebook', 'skp-textdomain' ); ?></h4>
        <?php
        
        foreach( $platform_accounts as $account ) {
            skp_get_partial( 'platform-account', array( 'account' => $account, 'has-name' => true, 'has-actions' => true ) );
        }

        ?>

        <?php if( empty( $platform_accounts ) || ( !empty( $platform_accounts ) && SKP_VERSION_OPTION != 2 ) ): ?>
            <a class="skp-connect-account-link skp-facebook" href="<?php echo add_query_arg( array( 'page' => 'skp-settings', 'skp-subpage' => 'facebook-app-details' ), admin_url('admin.php') ); ?>"><span class="skp-new"><span class="dashicons dashicons-plus"></span></span><span class="skp-text"><?php echo __( 'Connect Account', 'skp-textdomain' ); ?></span></a>
        <?php endif; ?>
    </div>

<?php

}
add_action( 'skp_tab_platform_accounts_settings', 'skp_add_platform_accounts_to_settings_facebook', 11 );


/**
 * Add custom subpage in the Settings page
 *
 * @param string $subpage
 * @param string $active_tab
 * @param array  $settings
 *
 */
function skp_settings_subpage_facebook( $subpage = '', $active_tab = '', $settings = array() ) {

    if( 'facebook-app-details' != $subpage && 'facebook-select-accounts' != $subpage )
        return;

    /**
     * Display settings page
     *
     */
    $platform_accounts = skp_get_platform_accounts( array('platform_slug' => 'facebook') );

    if( count( $platform_accounts ) >= 1 && SKP_VERSION_OPTION != 3 ) {
        include_once SKP_PLUGIN_DIR . 'includes/admin/views/view-submenu-page-settings.php';
        return;
    }


    /**
     * Display the Facebook App Credentials subpage
     *
     */
    if( 'facebook-app-details' == $subpage )
        include_once 'views/view-submenu-page-settings-facebook-app-details.php';
                

    /**
     * Display the platform account selection subpage
     *
     */
    if( 'facebook-select-accounts' == $subpage ) {

        if( !isset( $_GET['skp_tkn'] ) || !wp_verify_nonce( $_GET['skp_tkn'], 'skp_facebook_select_accounts' ) ) {
            include_once SKP_PLUGIN_DIR . 'includes/admin/views/view-submenu-page-settings.php';
            return;
        }
        
        $facebook_accounts = json_decode($_SESSION['facebook_accounts']);
        $facebook_existing_accounts['facebook_accounts'] = array();
        
        foreach( $platform_accounts as $account ) {
            $facebook_existing_accounts['facebook_accounts'][] = $account->platform_unique;
        }

        // Separate into profiles and pages for display purposes
        $facebook_profile_accounts = array();
        $facebook_page_accounts    = array();

        foreach( $facebook_accounts as $account ) {
            if( $account->platform_slug == 'facebook_profile' )
                $facebook_profile_accounts[] = $account;

            if( $account->platform_slug == 'facebook_page' )
                $facebook_page_accounts[] = $account;
        }

        // Include the select accounts view
        include_once 'views/view-submenu-page-settings-facebook-select-accounts.php';

    }

}
add_action( 'skp_settings_subpage', 'skp_settings_subpage_facebook', 10, 2 );


/**
 * Redirect the user to the main Settings page
 *
 */
function skp_settings_subpage_redirect_facebook() {

    if( empty( $_GET['skp-subpage'] ) )
        return;

    if( ( $_GET['skp-subpage'] != 'facebook-app-details' && $_GET['skp-subpage'] != 'facebook-select-accounts' ) )
        return;

    if( SKP_VERSION_OPTION != 1 )
        return;

    $platform_accounts = skp_get_platform_accounts( array('platform_slug' => 'facebook') );

    if( count( $platform_accounts ) >= 1 ) {
        wp_redirect( add_query_arg( array( 'page' => 'skp-settings' ), admin_url( 'admin.php' ) ) );
        exit;
    }

}
add_action( 'init', 'skp_settings_subpage_redirect_facebook' );