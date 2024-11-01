<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Returns a list of available social platforms
 *
 * @param bool $only_slugs - whether to return only the slugs of the platforms
 *                           or the entire array
 *
 * @return array
 *
 */
function skp_get_platforms( $only_slugs = true ) {

    $platforms = apply_filters( 'skp_get_platforms', array() );

    if( $only_slugs )
        $platforms = array_keys( $platforms );

	return $platforms;

}


/**
 * Returns the settings of the plugin
 *
 */
function skp_get_settings() {

    return get_option( 'skp_settings', array() );

}


/**
 * Returns an array with the post types available for publishing
 *
 * @return array
 *
 */
function skp_get_supported_post_types() {

    return apply_filters( 'skp_supported_post_types', array( 'post' ) );

}


/**
 * Returns / Renders the HTML of a partial
 *
 * @param string $file_name - the name of the partial file without its extension
 * @param array $data 		- custom data that is passed to the partial
 * @param bool $render 		- whether to render the HTML of return it
 *
 * @return mixed void|string
 *
 */
function skp_get_partial( $file_name = '', $data = array(), $render = true ) {

	$partial = new SKP_Partial( $file_name, $data );

	if( $render )
		$partial->render();
	else
		return $partial->get_output();

}


/**
 * Checks to see if a certain module is present withing the plugin
 *
 * @param string $module_slug - the name of the module folder
 *
 * @return bool
 *
 */
function skp_module_exists( $module_slug = '' ) {

    if( empty( $module_slug ) )
        return false;

    if( file_exists( SKP_PLUGIN_MODULES_DIR . '/' . $module_slug . '/' . $module_slug . '.php' ) )
        return true;
    else
        return false;

}


/**
 * Shuffle an associative array
 *
 */
function skp_shuffle_assoc($list) { 
    if (!is_array($list)) return $list; 
    
    $keys = array_keys($list); 
    shuffle($keys); 
    $random = array(); 
    foreach ($keys as $key) { 
        $random[$key] = $list[$key]; 
    }
    return $random; 
} 


/**
 * Function for adding admin notices
 *
 */
function skp_add_admin_notice($type, $message) {
    $f = "echo '<div class=\"notice notice-$type is-dismissible\"><p><strong>$message</strong></p><button type=\"button\" class=\"notice-dismiss\"><span class=\"screen-reader-text\">Dismiss this notice.</span></button></div>';";
    add_action( 'admin_notices', create_function('', $f));
}

/**
 * Function for updating the post meta with the number of times a post has been scheduled
 *
 */
function skp_update_post_count($post_id, $action) {
    $current_count = get_post_meta($post_id,'_skp_post_count',true);
    if(empty($current_count)) $current_count = 0;    
    ($action == 'plus') ? ++$current_count : --$current_count;
    update_post_meta($post_id, '_skp_post_count', $current_count); 
}

/**
 * Convert days of the week from number to day name
 *
 */
function skp_weekdays($w = null){
    $week_days = array(
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
        7 => 'Sunday'
    );
    if($w) return $week_days[$w];
    
    return $week_days;
    
}

/**
 * Outputs the weekdays in a nice format. 
 * For ex. array(1,3,5) would result to Monday, Wednesday and Friday
 *
 */
function skp_nice_weekdays($days){
    $result = "";
    $total_days = count($days);
    for($i=0; $i < $total_days; $i++){
        $result .= skp_weekdays($days[$i]);
        if($i == $total_days -2 ) $result .= " and ";
        elseif($i != $total_days - 1) $result .= ", ";
    }
    return $result;    
}

/**
 * Outputs the hours in a nice format. 
 * For ex. array(12,14,16) would result to 12:00, 14:00 and 16:00, using the time_format set in wordpress
 *
 */
function skp_nice_hours($hours){
    $result = "";
    $total_hours = count($hours);
    for($i=0; $i < $total_hours; $i++){
        $result .= date( get_option('time_format'), mktime( $hours[$i]->hour, $hours[$i]->minute, 0 ) );
        if($i == $total_hours - 2) $result .= " and ";
        elseif($i != $total_hours -1) $result .= ", ";
    }
    return $result;
    
}

/**
 * Outputs the platforms in a nice format. 
 *
 */
function skp_nice_platforms($platforms){
    $result = "";
    $platform_name = array();
    $platform_accounts = skp_get_platform_accounts( array('include_unique' => $platforms) ); 
    $platform_names = skp_get_platforms( false );
    foreach($platform_accounts as $platform_account){
        $platform_name[$platform_account->id] = '<span class="skp-platform-name">' . $platform_names[$platform_account->platform_slug] . '</span>' . ' <em>(' . $platform_account->platform_user_details->name . ')</em>';
    }
    $total_platforms = count($platform_accounts);
    for($i=0; $i < $total_platforms; $i++){
        if(isset($platform_name[$platform_accounts[$i]->id]))
            $result .= $platform_name[$platform_accounts[$i]->id];
        if($i == $total_platforms - 2) $result .= " and ";
        elseif($i != $total_platforms -1) $result .= ", ";
    }
    
    if($result == "")
        return '<em>(' . __('platform account no longer exists', 'skp-textdomain') . ')</em>';
    return $result;
    
}

/**
 * Create a cron job that runs once every 15 minutes
 *
 */
add_filter( 'cron_schedules', 'skp_quarter_hourly_cron_job' );
function skp_quarter_hourly_cron_job( $schedules ) {
    $schedules['skp_quarter_hourly'] = array(
        'interval' => 60*15,
        'display'  => __( 'Quarter Hourly' ),
    );
     
    return $schedules;
}


/**
 * Cron job that posts schedules to social platforms
 *
 */

function skp_job_post_to_platforms(){
    
    //get posts starting from NOW - 60 seconds
    $start_time = date('Y-m-d H:i:s', current_time('timestamp') - 60);
    
    //until NOW + 14 minutes
    $end_time = date('Y-m-d H:i:s', current_time('timestamp') + (60*14));
    
    $posts = skp_get_posts( array('type' => 'schedule', 'date_range' => array( $start_time, $end_time ) ) );

    foreach( $posts as $skp_post ) {
        do_action( 'skp_post_to_platforms', $skp_post );
    }    
    
}
add_action('skp_cron_post_to_platforms', 'skp_job_post_to_platforms');

/**
 * Hook that prepares the SKP_Post for sharing on the social platform accounts
 *
 * @param SKP_Post $skp_post
 *
 */
function skp_post_to_platforms( SKP_Post $skp_post ) {

    /**
     * Filter to permit last minute changes on the SKP_Post object before
     * sharing on all platforms
     *
     * @param SKP_Post $skp_post
     *
     */
    $skp_post = apply_filters( 'skp_post_to_platforms_post', $skp_post );
    
    
    /**
     * For extra functionality we will also use the array sibling of the
     * SKP_Post which can be filtered here 
     *
     * @param array $post_data 
     *
     */
    $post_data = $skp_post->to_array();
    $post_data = apply_filters( 'skp_post_to_platforms_post_data', $post_data );
    

    /**
     * We need all platform accounts where this post will be published on
     *
     */
    $platform_accounts = skp_get_platform_accounts( array( 'include_unique' => $post_data['platform_accounts'] ) );

    // Return if no accounts are found
    if( empty( $platform_accounts ) )
        return;

    /**
     * Go through each account and create the dynamic hook that each platform
     * should hook into to share the post
     *
     */
    foreach( $platform_accounts as $platform_account ) {
       
        /**
         * Dynamic action hook where each platform should hook into to share the post
         *
         * @param SKP_Platform_Account $platform_account - the account object
         * @param SKP_Post $skp_post                    - the post object
         * @param $post_data                              - the array version of the post object which can be very different
         *                                                  as it can be filtered above with "skp_sharer_post_data"
         *
         */ 
        do_action( 'skp_post_to_account_' . $platform_account->platform_slug, $platform_account, $skp_post, $post_data );

    }

}
add_action( 'skp_post_to_platforms', 'skp_post_to_platforms' );


/**
 * Add admin notice on plugin activation
 *
 */
function skp_admin_notice_first_activation() {

    if( SKP_VERSION_OPTION != 1 )
        return;

    // Get first activation of the plugin
    $first_activation = get_option( 'skp_first_activation', '' );

    if( empty($first_activation) )
        return;

    // Do not display this notice if user cannot activate plugins
    if( !current_user_can( 'activate_plugins' ) )
        return;

    // Do not display this notice if plugin has been activated for more than 1 minute
    if( time() - 3 * MINUTE_IN_SECONDS >= $first_activation )
        return;

    // Do not display this notice for users that have dismissed it
    if( get_user_meta( get_current_user_id(), 'skp_admin_notice_first_activation', true ) != '' )
        return;

    // Echo the admin notice
    echo '<div class="skp-admin-notice skp-admin-notice-activation notice notice-info">';

        echo '<h4>' . __( 'Thank you for installing SkyePress. Let\'s start sharing your awesome posts.', 'skp-textdomain' ) . '</h4>';

        echo '<a class="skp-admin-notice-link" href="' . add_query_arg( array( 'skp_admin_notice_activation' => 1 ), admin_url('admin.php?page=skp-settings') ) . '"><span class="dashicons dashicons-admin-settings"></span>' . __( 'Go to the Plugin', 'skp-textdomain' ) . '</a>';
        echo '<a class="skp-admin-notice-link" href="http://docs.devpups.com/?utm_source=plugin&utm_medium=plugin-activation&utm_campaign=skyepress" target="_blank"><span class="dashicons dashicons-book"></span>' . __( 'View Documentation', 'skp-textdomain' ) . '</a>';
        echo '<a class="skp-admin-notice-link" href="http://www.devpups.com/skyepress/?utm_source=plugin&utm_medium=plugin-activation&utm_campaign=skyepress" target="_blank"><span class="dashicons dashicons-external"></span>' . __( 'Upgrade to Pro', 'skp-textdomain' ) . '</a>';

        echo '<a href="' . add_query_arg( array( 'skp_admin_notice_activation' => 1 ) ) . '" type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></a>';

    echo '</div>';

}
add_action( 'admin_notices', 'skp_admin_notice_first_activation' );


/**
 * Handle admin notices dismissals
 *
 */
function skp_admin_notice_dismiss() {

    if( isset( $_GET['skp_admin_notice_activation'] ) )
        add_user_meta( get_current_user_id(), 'skp_admin_notice_first_activation', 1, true );

}
add_action( 'admin_init', 'skp_admin_notice_dismiss' );


function skp_check_revive_posts() {
    
    if ( SKP_VERSION_OPTION != 1 )
        return;
    
    if( !isset($_GET['page']) || !isset($_GET['subpage']) || $_GET['page'] != 'skp-revive-posts' || $_GET['subpage'] != 'add-schedule' || isset($_GET['skp_schedule_id']) )
		return;
    
    $schedules = skp_get_schedules();
    
    if(count($schedules) >= 1){
        wp_redirect(add_query_arg( array( 'page' => 'skp-revive-posts'), admin_url('admin.php') ) );
    }
}



/**
 * print_r wrapper function
 *
 */

if(!function_exists('pr')){
    function pr($a){
        echo "<pre>";
        print_r($a);
        echo "</pre>";
    }
}