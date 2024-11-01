<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Wrapper function to retrieve all schedules
 * given certain arguments
 *
 * @param $args
 *
 */
function skp_get_schedules( $args = array() ) {

	$query = new SKP_Query_Schedule( $args );

	return $query->get_results();

}

/**
 * Retrieves a single schedule given the id
 *
 * @param $id
 *
 */
function skp_get_schedule( $id = 0 ) {

	if( $id == 0 )
		return null;

	$posts = skp_get_schedules( array( 'include' => array( $id ) ) );

	if( !empty( $posts ) )
		return $posts[0];
	else
		return null;

}


/**
 * Inserts a new schedule into the DB
 *
 * @param array $data - the details of the schedule
 * 
 * @return mixed int|null - returns int, the id of the inserted row
 *						  - returns null if the insert failed
 *
 */
function skp_insert_schedule( $data = array() ) {

	if( empty( $data ) )
		return null;

	$handler = new SKP_Entry_Handler_Schedule();
	$handler->insert( $data );

	$last_inserted_id = $handler->get_last_handled_id();

	if( $last_inserted_id == 0 )
		return null;

	return $last_inserted_id;

}


/**
 * Updates a schedule in the DB
 * 
 * @param SKP_Schedule $object
 *
 * @return bool
 *
 */
function skp_update_schedule( SKP_Schedule $object = null ) {

	if( empty( $object->id ) )
		return false;

	$handler = new SKP_Entry_Handler_Schedule();
	$handler->update( $object );

	if( $handler->get_last_handled_id() == 0 )
		return false;
	else
		return true;

}


/**
 * Removes a schedule from the DB
 * 
 * @param SKP_Schedule $object
 *
 * @return bool
 *
 */
function skp_remove_schedule( SKP_Schedule $object = null ) {

	if( empty( $object->id ) )
		return false;

	$handler = new SKP_Entry_Handler_Schedule();
	$handler->remove( $object );

	if( $handler->get_last_handled_id() == 0 )
		return false;
	else
		return true;	

}

/**
 * Function for displaying the taxonomies checkboxes
 *
 */
function skp_schedule_load_taxonomies_checkboxes( $post_type = '', $post_data = false ){

    if( empty( $post_type ) ) {
        echo __( 'Invalid custom post type.', 'skp-textdomain' );
        return;
    }

    $taxonomies = get_object_taxonomies($post_type);
    if(count($taxonomies) > 0){
        foreach($taxonomies as $taxonomy){
            if($terms = get_terms( $taxonomy )){
                ?>
                <div class="skp-schedule-taxonomy-wrapper">
                    <h4><?php echo get_taxonomy( $taxonomy )->labels->name;?></h4>
                    <fieldset>
                        <?php foreach( $terms as $term ):?>
                        <label for="skp_schedule_taxonomy_<?php echo $taxonomy;?>_<?php echo $term->term_id;?>"><input data-taxonomy="<?php echo $taxonomy;?>" <?php echo (!empty($post_data[$taxonomy]) && in_array($term->term_id, $post_data[$taxonomy]) ) ? ' checked="checked"' : '';?> type="checkbox" id="skp_schedule_taxonomy_<?php echo $taxonomy;?>_<?php echo $term->term_id;?>" name="skp_schedule_taxonomy[<?php echo $taxonomy;?>][]" value="<?php echo $term->term_id;?>" /> <?php echo $term->name;?></label><br />
                        <?php endforeach;?>
                    </fieldset>
                </div>
                <?php
            }
        }
    } else {
        echo __( "This post type has no taxonomies.", 'skp-textdomain' );
    }
}

/**
 * Ajax function for loading the taxonomies when creating a schedule
 *
 */
function skp_schedule_load_taxonomies_callback() {
    
    $post_type = ( ! empty( $_POST['post_type'] ) ? sanitize_text_field( $_POST['post_type'] ) : '' );

    if( ! empty( $post_type ) )
        skp_schedule_load_taxonomies_checkboxes( $post_type );

    wp_die(); 
    
}
add_action( 'wp_ajax_skp_schedule_load_taxonomies', 'skp_schedule_load_taxonomies_callback' );

/**
 * Get all posts from a specific post type and taxonomies
 *
 */
function skp_schedule_get_matching_posts($post_type, $taxonomies, $older_than, $count = false){
    
    $numberposts = ($count != false) ? $count : -1;
    
    //create the arguments for the get_posts function
    $args = array( 
        'post_type'   => $post_type,
        'numberposts' => $numberposts,
        'orderby' => 'meta_value_num rand',
        'order' => 'asc',
        'meta_query' => array( 
            'relation' => 'OR',
            array( 
                'key' => '_skp_post_count', 
                'compare' => 'NOT EXISTS' 
            ),
            array( 
                'key' => '_skp_post_count', 
                'compare' => 'EXISTS' 
            )    
        )
    );
    
    //create the taxonomy query    
    if(!empty($taxonomies) && is_array($taxonomies)) {
        $tax_query['relation'] = 'AND';
        foreach($taxonomies as $taxonomy => $terms){
            $tax_query[] = array(
        			'taxonomy' => $taxonomy,
        			'field'    => 'term_id',
        			'terms'    => $terms,
        		);
        } 
        $args['tax_query'] = array($tax_query);
    } 
    
    if($older_than > 0){
        $args['date_query'] = array( 'before' => $older_than . ' days ago');
    }
    
    //get all posts that match the post type and taxonomies
    return get_posts( $args );
}

/**
 * Returns an array with the selected days and hours for the next 7 days
 *
 * @param $days array
 * @param $hours array
 *
 * @returns array
 */
function skp_schedule_get_publish_dates($days, $hours){       
    $settings = get_option( 'skp_settings'); 
    $how_many_days = (isset($settings['schedule_days_in_advance'])) ? $settings['schedule_days_in_advance'] : 30;
    for($i = 0; $i < $how_many_days; $i++){
        //create the timestamp for each day                    
        $day = mktime(0, 0, 0, current_time('n'), current_time('j') + $i ,current_time('Y'));
        
        //check if day is in user selected day;                                        
        if(in_array(date('N',$day), $days) ){
            
            //it is, add all the hours for this day.                                                
            foreach($hours as $hour){
                //set the publish date                          
                $post_date = date("Y-m-d", $day) . ' ' . $hour['hour'] . ':' . $hour['minute'] . ':00';
                $publish_dates[] = $post_date;
            }
        }
    } 
    
    return $publish_dates;   
}

/**
 * Adds new posts for existing schedules if necessary
 *
 */
function skp_schedule_update_posts(){
    $schedules = skp_get_schedules(); 
    foreach( $schedules as $schedule ){
        $missing_dates = array();
        $publish_dates = skp_schedule_get_publish_dates( $schedule->day, $schedule->hour );
        foreach($publish_dates as $publish_date){
            if( ! skp_get_posts( array('schedule_id' => $schedule->id, 'status' => 'pending', 'date' => $publish_date) ) ){
                $missing_dates[] = $publish_date;
            }
        }
        
        if(!empty($missing_dates) && count($missing_dates) > 0){
        
            $wp_posts = skp_schedule_get_matching_posts( $schedule->post_type, $schedule->taxonomy, $schedule->older_than, count($missing_dates) );
                    
            $wp_posts = skp_shuffle_assoc( $wp_posts );
            
            $i=0; foreach($wp_posts as $wp_post){
                skp_insert_post ( array(
                    'post_id'           => $wp_post->ID, 
                    'type'              => 'schedule',
                    'date'              => $missing_dates[$i++],
                    'platform_accounts' => json_encode( $schedule->platform_accounts ),
                    'content'           => 'content',
                    'schedule_id'       => $schedule->id,
                    'status'            => 'pending'
                 ) );    
                 skp_update_post_count($wp_post->ID,'plus');
            }
        }
        

    }
 
}
add_action('skp_cron_schedule_update_posts', 'skp_schedule_update_posts');

/**
 * Outputs HTML in the Settings page for the number of days to schedule posts for.
 *
 * @param array $settings - the settings of the plugin
 *
 */
function skp_schedule_add_settings( $settings = array() ) {
    echo '<tr>';
        echo '<td style="padding-left: 0;">';
            echo '<h3>' . __( 'Revive Posts', 'skp-textdomain' ) . '</h3>';
        echo '</td>';
    echo '</tr>';
	echo '<tr>';
	    echo '<th scope="row">';
	        echo '<label for="schedule_days_in_advance">' . __( 'Days in advance', 'skp-textdomain' ) . '</label>';
	    echo '</th>';
	    echo '<td>';
	        echo '<div class="skp-form-field">';
	            echo '<p class="description">';
	            	echo '<input id="schedule_days_in_advance" type="number" min="1" name="skp_settings[schedule_days_in_advance]" value="' . ( !empty( $settings['schedule_days_in_advance'] ) ? $settings['schedule_days_in_advance'] : '30' ) . '" /><br />';
	            	echo __( 'When creating a schedule, how many days in advance to schedule posts for?', 'skp-textdomain' ); 
	            echo '</p>';
	        echo '</div>';
	    echo '</td>';
	echo '</tr>';

}
add_action( 'skp_tab_general_settings_after_fields', 'skp_schedule_add_settings', 20 );


/**
 * Ajax function for displaying the number of available posts when creating a schedule
 *

    
function skp_get_number_of_matching_posts_callback() {


    $wp_posts = skp_get_matching_posts($_POST['post_type'], json_decode(stripslashes($_POST['taxonomies'])), $_POST['older_than']);
    
    echo count($wp_posts);                  

    wp_die(); 
    
}
add_action( 'wp_ajax_skp_get_number_of_matching_posts', 'skp_get_number_of_matching_posts_callback' );
 */
