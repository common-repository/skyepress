<?php
    
    // Exit if accessed directly
    if ( ! defined( 'ABSPATH' ) ) exit;

?>
<?php skp_get_partial( 'plugin-header' ); ?>
<div class="wrap skp-wrap-dashboard">
    <h1><?php echo __( 'Dashboard', 'skp-textdomain' ) ?></h1>
    
    <!-- Row -->
    <div class="skp-row">

        <!-- 5 Upcoming Shares -->
        <div class="skp-col-1-2">
            <div class="skp-dashboard-widget postbox">
                <div class="inner skp-no-padding-bottom">
                    <h2><?php echo __( '5 Upcoming Shares', 'skp-textdomain' ); ?></h2>
                    <?php if($pending_posts): foreach($pending_posts as $post):?>
                        <div class="skp-dashboard-list-item skp-post">
                            <a target="_blank" href="<?php echo get_permalink($post->post_id);?>">
                                <?php echo get_the_title($post->post_id);?>
                            </a> will be shared on <?php echo skp_nice_platforms($post->platform_accounts);?>

                            <strong class="skp-post-datetime"><?php echo date( get_option('date_format'), strtotime($post->date));?> at <?php echo date( get_option('time_format'), strtotime($post->date));?></strong>
                        </div>
                    <?php endforeach; else:?>
                        <p class="skp-dashboard-list-item-empty"><strong><?php echo __( 'Nothing here yet.', 'skp-textdomain' ); ?></strong></p>
                    <?php endif;?>
                </div>
            </div>
        </div>
        
        <!-- 5 Lates Shared -->
        <div class="skp-col-1-2">
            <div class="skp-dashboard-widget postbox">
                <div class="inner skp-no-padding-bottom">

                    <h2><?php echo __( '5 Latest Shared', 'skp-textdomain' ); ?></h2>
                
                    <?php if($posted_posts): foreach($posted_posts as $post):?>
                        <div class="skp-dashboard-list-item skp-post">
                            <a target="_blank" href="<?php echo get_permalink($post->post_id);?>">
                                <?php echo get_the_title($post->post_id);?>
                            </a> was shared on <?php echo skp_nice_platforms($post->platform_accounts);?> 

                            <strong class="skp-post-datetime"><?php echo date( get_option('date_format'), strtotime($post->date));?> at <?php echo date( get_option('time_format'), strtotime($post->date));?></strong>
                        </div>
                    <?php endforeach; else:?>
                        <p class="skp-dashboard-list-item-empty"><strong><?php echo __( 'Nothing here yet.', 'skp-textdomain' ); ?></strong></p>
                    <?php endif;?>
                </div>
            </div>
        </div>
    </div>


    <!-- Row -->
    <div class="skp-row">

        <!-- Plugin Status -->
        <div class="skp-col-1-2">
            <div class="skp-dashboard-widget postbox">
                <div class="inner skp-no-padding-bottom">
                    <h2><?php echo __( 'SkyePress Status', 'skp-textdomain' ); ?></h2>
                    
                    <div class="skp-dashboard-list-item">
                        <strong>Share on Publish:</strong>
                        <?php echo (isset($settings['share_on_publish']) && $settings['share_on_publish'] == 'on') ? "On. All good." : "Off. <a href='" . add_query_arg( array( 'page' => 'skp-settings', 'skp-tab' => 'general-settings' ), admin_url('admin.php') ) . "'>Activate the Share on Publish feature here</a>."; ?>
                    </div>
                    
                    <div class="skp-dashboard-list-item">
                        <strong>Revive Posts:</strong>
                        <?php echo ($schedule_status == true) ? "Great, you have " . count($schedules) . " running schedules." : "Looks like you haven't created any schedules yet. <a href='" . add_query_arg( array( 'page' => 'skp-revive-posts' ), admin_url('admin.php') ) . "'>You can create one here</a>.";?>
                    </div>
                    
                    <div class="skp-dashboard-list-item">
                        <strong>Posts shared so far:</strong>
                        <?php echo $posted_posts_count;?>
                    </div>
                    
                    <?php if( !empty($pending_posts[0]) ):?>
                    <div class="skp-dashboard-list-item">
                        <strong>Next post will be shared on:</strong>
                        <?php echo date( get_option('date_format'), strtotime($pending_posts[0]->date));?> at <?php echo date( get_option('time_format'), strtotime($pending_posts[0]->date));?>
                    </div>
                    <?php endif;?>
                    
                    <?php if( !empty($posted_posts[0]) ):?>
                    <div class="skp-dashboard-list-item">
                        <strong>Last post was shared on:</strong>
                        <?php echo date( get_option('date_format'), strtotime($posted_posts[0]->date));?> at <?php echo date( get_option('time_format'), strtotime($posted_posts[0]->date));?>
                    </div>
                    <?php endif;?>
                </div>
            </div>
        </div>
    
        <!-- Posting Errors -->
        <div class="skp-col-1-2">
            <div class="skp-dashboard-widget postbox">
                <div class="inner skp-no-padding-bottom">
                    <h2><?php echo __( 'Posting Errors', 'skp-textdomain' ); ?></h2>
                    
                    <?php if($errored_posts): foreach($errored_posts as $post):?>
                    <?php
                        foreach( $post->response as $platform_id => $response ):?>
                            <?php if(isset($response->formatted->error) && $response->formatted->error == 1):?>
                                <div class="skp-dashboard-list-item skp-post">
                                    Uh-oh, we couldn't post <a target="_blank" href="<?php echo get_permalink($post->post_id);?>"><?php echo get_the_title($post->post_id);?></a> to <?php echo skp_nice_platforms( array( 0 => $platform_id) );?>. Error returned: <?php echo $response->formatted->message;?>
        
                                    <strong class="skp-post-datetime"><?php echo date( get_option('date_format'), strtotime($post->date));?> at <?php echo date( get_option('time_format'), strtotime($post->date));?></strong>
                                </div>
                            <?php endif;?>
                        <?php endforeach; ?>
                    
                    <?php endforeach; else:?>
                        <p class="skp-dashboard-list-item-empty"><strong><?php echo __( 'Nothing here. Let\'s hope this stays empty.', 'skp-textdomain' ); ?></strong></p>
                    <?php endif;?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- System Status -->
    <div class="skp-row">
        <div class="skp-col-1-2">
            <div class="skp-dashboard-widget skp-dashboard-widget-system-status postbox">
                <div class="inner skp-no-padding-bottom">
                    <h2><?php echo __( 'System Status', 'skp-textdomain' ); ?></h2>
                    
                    <div class="skp-dashboard-list-item">
                        <label><strong>PHP Version:</strong></label>
                        
                        <?php if (version_compare(phpversion(), '5.4.0', '<')):?>
                            <span class="dashicons dashicons-no"></span>
                            <span class="error">You are running PHP version <?php echo phpversion();?>. You need at least PHP 5.4.0 for the plugin to run properly. Please contact your hosting provider for help.</span>
                        <?php else:?>
                            <span class="dashicons dashicons-yes"></span>
                            <span class="success">You are running PHP version <?php echo phpversion();?>. All good.</span>
                        <?php endif;?>
                    </div>
                    
                    <div class="skp-dashboard-list-item">
                        <label><strong>WP Version:</strong></label>
                        
                        <?php global $wp_version; if (version_compare($wp_version, '4', '<')):?>
                            <span class="dashicons dashicons-no"></span>
                            <span class="error">You are running WordPress version <?php echo $wp_version;?>. You need at least WordPress 4.0 for the plugin to run properly. Please update your WP install.</span>
                        <?php else:?>
                            <span class="dashicons dashicons-yes"></span>
                            <span class="success">You are running WordPress version <?php echo $wp_version;?>. All good.</span>
                        <?php endif;?>
                    </div>
                    
                    <div class="skp-dashboard-list-item">
                        <label><strong>PHP cURL:</strong></label>
                        <?php if (!function_exists('curl_version')):?>
                            <span class="dashicons dashicons-no"></span>
                            <span class="error">cURL is disabled on your server. Please contact your hosting provider for help.</span>
                        <?php else:?>
                            <span class="dashicons dashicons-yes"></span>
                            <span class="success">cURL is enabled.</span>
                        <?php endif;?>
                    </div>
                    
                    <div class="skp-dashboard-list-item">
                        <label><strong>Cron jobs:</strong></label>
                        <?php if( 
                            wp_next_scheduled ( 'skp_cron_post_to_platforms' ) && 
                            wp_next_scheduled ( 'skp_cron_schedule_update_posts' ) && 
                            wp_next_scheduled ( 'skp_cron_update_platform_user_details' ) 
                        ):?> 
                            <span class="dashicons dashicons-yes"></span>
                            <span class="success">All the cron jobs are active.</span>
                        <?php else:?>
                            <span class="dashicons dashicons-no"></span>
                            <span class="success">One or more of our cron jobs are inactive. <a href="<?php echo add_query_arg( array( 'page' => 'skp-dashboard', 'subpage' => 'activate-cron-jobs', 'noheader' => true), admin_url('admin.php') );?>">Click here to activate cron jobs</a>.</span>
                        <?php endif;?>
                
                    </div>
                </div>
            </div>
        </div><!-- End Col -->
    </div><!-- End Row -->

</div>