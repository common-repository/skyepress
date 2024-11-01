<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Add Twitter to the platforms array
 *
 * @param array $platforms
 *
 * @return array
 *
 */
function skp_add_platform_twitter( $platforms = array() ) {

	$platforms['twitter'] = __( 'Twitter', 'skp-textdomain' );

	return $platforms;

}
add_filter( 'skp_get_platforms', 'skp_add_platform_twitter', 10 );


/**
 * Initialise Twitter components
 *
 */
function skp_init_components_twitter() {

	SKP_Twitter_Account_Listener::init();
	SKP_Twitter_Poster::init();

}
add_action( 'init', 'skp_init_components_twitter' );


/**
 * Adds the platform accounts to the "Accounts" tab in the "Settings" page
 *
 * @param array $settings
 *
 */
function skp_add_platform_accounts_to_settings_twitter( $settings ) {

	$platform_accounts = skp_get_platform_accounts( array('platform_slug' => 'twitter') );

?>

    <div class="skp-platform-accounts-column">
        <h4><?php echo __( 'Twitter', 'skp-textdomain' ); ?></h4>
        <?php

        foreach( $platform_accounts as $account ) {
            skp_get_partial( 'platform-account', array( 'account' => $account, 'has-name' => true, 'has-actions' => true ) );
        }

        ?>

        <?php if( empty( $platform_accounts ) || ( !empty( $platform_accounts ) && SKP_VERSION_OPTION != 2 ) ): ?>
            <a class="skp-connect-account-link skp-twitter" href="<?php echo add_query_arg( array( 'page' => 'skp-settings', 'skp-subpage' => 'twitter-app-details' ), admin_url('admin.php') ); ?>"><span class="skp-new"><span class="dashicons dashicons-plus"></span></span><span class="skp-text"><?php echo __( 'Connect Account', 'skp-textdomain' ); ?></span></a>
        <?php endif; ?>
    </div>

<?php
}
add_action( 'skp_tab_platform_accounts_settings', 'skp_add_platform_accounts_to_settings_twitter', 10 );


/**
 * Add custom subpage in the Settings page
 *
 * @param string $subpage
 * @param string $active_tab
 * @param array  $settings
 *
 */
function skp_settings_subpage_twitter( $subpage = '', $active_tab = '', $settings = array() ) {

    if( 'twitter-app-details' != $subpage)
        return;

    /**
     * Display settings page
     *
     */
    $platform_accounts = skp_get_platform_accounts( array('platform_slug' => 'twitter') );

    if( count( $platform_accounts ) >= 1 && SKP_VERSION_OPTION != 3 ) {
        include_once SKP_PLUGIN_DIR . 'includes/admin/views/view-submenu-page-settings.php';
        return;
    }


    /**
     * Display the Twitter App Credentials subpage
     *
     */
    if( 'twitter-app-details' == $subpage )
        include_once 'views/view-submenu-page-settings-twitter-app-details.php';
  
}
add_action( 'skp_settings_subpage', 'skp_settings_subpage_twitter', 10, 2 );


/**
 * Redirect the user to the main Settings page
 *
 */
function skp_settings_subpage_redirect_twitter() {

    if( empty( $_GET['skp_platform'] ) )
        return;

    if( ( $_GET['skp_platform'] != 'twitter' )  )
        return;

    if( !empty( $_GET['skp_platform_unique'] ) )
        return;

    if( SKP_VERSION_OPTION != 1 )
        return;

    $platform_accounts = skp_get_platform_accounts( array('platform_slug' => 'twitter') );

    if( count( $platform_accounts ) >= 1 ) {
        wp_redirect( add_query_arg( array( 'page' => 'skp-settings' ), admin_url( 'admin.php' ) ) );
        exit;
    }

}
add_action( 'init', 'skp_settings_subpage_redirect_twitter' );