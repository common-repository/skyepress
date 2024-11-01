<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


Class SKP_Schedule_Listener {

	/**
	 * Initialise connector
	 *
	 */
	public static function init() {

		// We need to isolate somehow who can access the connector
		if( !current_user_can( 'manage_options' ) )
			return;
    
        skp_check_revive_posts();
            
        add_action( 'admin_init', array( get_called_class(), 'display_admin_messages' ) );
        
		add_action( 'admin_init', array( get_called_class(), 'save_schedule' ) );
        
        add_action( 'admin_init', array( get_called_class(), 'remove_schedule' ) );
        
        
	}
    
    /**
	 * Display admin messages
	 *
	 */
    public static function display_admin_messages() {
        if( !isset($_GET['page']) || $_GET['page'] != 'skp-revive-posts' || !isset($_GET['message']) )
			return;
        
        switch ($_GET['message']){
            case 1:
                skp_add_admin_notice('success','Schedule successfully added.');
                break; 
            case 2:
                skp_add_admin_notice('success','Schedule successfully removed.');
                break; 
            case 3:
                skp_add_admin_notice('success','Schedule successfully updated.');
                break; 
        }
    }


	/**
	 * Add or Update a schedule
	 *
	 */
	public static function save_schedule() {

		if( !isset( $_POST['skp_tkn'] ) || !wp_verify_nonce( $_POST['skp_tkn'], 'skp_save_schedule' ) )
			return;

        $updating_schedule = ( isset ($_POST['skp_schedule_id'] ) && (int)$_POST['skp_schedule_id'] > 0 ) ? true : false;
        
        $error = false;

        //do some validation
        if( empty( $_POST['skp_schedule_name'] ) ){
            skp_add_admin_notice('error','Please enter a name for the schedule.'); 
            $error = true; 
        }
        
        if( empty( $_POST['skp_schedule_post_type'] ) ){
            skp_add_admin_notice('error','Please select a post type.');  
            $error = true;
        }
        
        if( !isset( $_POST['skp_schedule_older_than'] ) ){
            skp_add_admin_notice('error','Please enter a value for how old the scheduled posts should be.');  
            $error = true;
        } elseif ((int)$_POST['skp_schedule_older_than'] < 0){
            skp_add_admin_notice('error','The number of days should be greater than or equal to 0.');  
            $error = true;
        }
        
        if( empty( $_POST['skp_schedule_day'] ) ){
            skp_add_admin_notice('error','Please select at least one day of the week.');  
            $error = true;
        }
        
        if( empty( $_POST['skp_schedule_hour'] ) ){
            skp_add_admin_notice('error','Please add at least one posting time.');  
            $error = true;
        }
        
        
        if( empty( $_POST['skp_schedule_platform_accounts'] ) ){
            skp_add_admin_notice('error','Please select at least one social platform.');  
            $error = true;
        }
        

        if($error == false){
            //all good.
            
            $schedule_name       = sanitize_text_field( $_POST['skp_schedule_name'] );
            $schedule_post_type  = sanitize_text_field( $_POST['skp_schedule_post_type'] );
            $schedule_taxonomy   = ( ! empty( $_POST['skp_schedule_taxonomy'] ) ) ? $_POST['skp_schedule_taxonomy'] : false;
            $schedule_days       = ( is_array( $_POST['skp_schedule_day'] ) ? array_map( 'intval', $_POST['skp_schedule_day'] ) : array() );
            $schedule_platforms  = ( is_array( $_POST['skp_schedule_platform_accounts'] ) ? $_POST['skp_schedule_platform_accounts'] : array() );
            $schedule_older_than = (int)sanitize_text_field( $_POST['skp_schedule_older_than'] );

            $schedule_hours = ( is_array( $_POST['skp_schedule_hour'] ) ? $_POST['skp_schedule_hour'] : array() );
            //remove duplicates
            if( ! empty( $schedule_hours ) ) {
                $schedule_hours = array_map("unserialize", array_unique(array_map("serialize", $schedule_hours)));
                $schedule_hours = array_values( $schedule_hours );
            }

            $schedule_content = ( ! empty( $_POST['skp_schedule_content'] ) && is_array( $_POST['skp_schedule_content'] ) ? json_encode( $_POST['skp_schedule_content'] ) : '' );

            //insert or update the schedule
            if( $updating_schedule ){
                //update
                $schedule_id = (int)$_POST['skp_schedule_id'];
                $schedule    = skp_get_schedule( $schedule_id );
                
                $schedule->name                 = $schedule_name;
                $schedule->post_type            = $schedule_post_type;
                $schedule->taxonomy             = json_encode( $schedule_taxonomy );
                $schedule->older_than           = $schedule_older_than;
                $schedule->platform_accounts    = json_encode( $schedule_platforms );
                $schedule->content              = $schedule_content;
                $schedule->day                  = json_encode( $schedule_days );
                $schedule->hour                 = json_encode( $schedule_hours );
                
                skp_update_schedule( $schedule );
                
                //remove all old posts
                $posts = skp_get_posts(array( 'schedule_id' => $schedule->id, 'status' => 'pending' )); 

                foreach( $posts as $post ){
                    skp_remove_post( $post );
                    skp_update_post_count( $post->post_id, 'minus' );
                }
                
            } else {
                //insert
                $schedule_id = skp_insert_schedule ( array(
                    'name'              => $schedule_name,
                    'post_type'         => $schedule_post_type,
                    'taxonomy'          => json_encode( $schedule_taxonomy ),
                    'older_than'        => $schedule_older_than,
                    'platform_accounts' => json_encode( $schedule_platforms ),
                    'content'           => $schedule_content,
                    'day'               => json_encode( $schedule_days ),
                    'hour'              => json_encode( $schedule_hours )    
                ) );
            }
            

            if($schedule_id > 0){
                
                $publish_dates = skp_schedule_get_publish_dates( $schedule_days, $schedule_hours );                
                
                $wp_posts = skp_schedule_get_matching_posts( $schedule_post_type, $schedule_taxonomy, $schedule_older_than, count( $publish_dates ) );
                
                $wp_posts = skp_shuffle_assoc( $wp_posts );
                
                //loop through all the posts
                $i=0; 
                foreach( $wp_posts as $wp_post ){
                    skp_insert_post ( array(
                        'post_id'           => $wp_post->ID, 
                        'type'              => 'schedule',
                        'date'              => $publish_dates[$i++],
                        'platform_accounts' => json_encode( $schedule_platforms ),
                        'content'           => ( ! empty( $_POST['skp_schedule_content'] ) ? json_encode( $_POST['skp_schedule_content'] ) : '' ),
                        'schedule_id'       => $schedule_id,
                        'status'            => 'pending'
                    ) );

                    skp_update_post_count($wp_post->ID,'plus');
                }
            }

            //go back to the main page
            wp_redirect(add_query_arg( array( 'page' => 'skp-revive-posts', 'message' => ($updating_schedule) ? 3 : 1), admin_url('admin.php') ) );
        } 

	}
    
    /**
	 * Remove a schedule
	 *
	 */
	public static function remove_schedule() {

		if( !isset( $_GET['skp_tkn'] ) || !wp_verify_nonce( $_GET['skp_tkn'], 'skp_remove_schedule' ) )
			return;
            
        if( !isset( $_GET['skp_schedule_id'] ) )
            return;
        
        //get the schedule
        $schedule = skp_get_schedule( (int)$_GET['skp_schedule_id'] );
        
        //remove all posts linked to the schedule
        $posts = skp_get_posts(array( 'schedule_id' => $schedule->id, 'status' => 'pending' )); foreach($posts as $post){
            skp_remove_post($post);
            skp_update_post_count($post->post_id,'minus');
        }
        
        //remove the schedule too.
        skp_remove_schedule($schedule);
       
        wp_redirect(add_query_arg( array( 'page' => 'skp-revive-posts', 'message' => 2), admin_url('admin.php') ) );
        
    }
}