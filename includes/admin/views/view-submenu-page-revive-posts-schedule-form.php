<?php
    
    // Exit if accessed directly
    if ( ! defined( 'ABSPATH' ) ) exit;

?>
<?php skp_get_partial( 'plugin-header' ); ?>

<?php $platform_accounts = skp_get_platform_accounts(); ?>

<div class="wrap">

    <h1><?php echo $page_title;?></h1>
    
    <?php
    $query_args = array( 'page' => 'skp-revive-posts', 'subpage' => 'add-schedule');
    if( !empty( $_GET['skp_schedule_id'] ) ) $query_args['skp_schedule_id'] = $_GET['skp_schedule_id'];
    ?>
    
    <form method="post" id="skp-form-schedule" action="<?php echo add_query_arg( $query_args, admin_url('admin.php') ); ?>">
        <?php 

            /**
             * Save schedule nonce field
             */
            wp_nonce_field( 'skp_save_schedule', 'skp_tkn' ); 

            /**
             * Get supported post types
             */
            $post_types = skp_get_supported_post_types();

            if( !empty( $post_types ) ) {
                foreach( $post_types as $key => $post_type_slug ) {
                    $post_types[$key] = get_post_type_object( $post_type_slug );
                }
            }

        ?>
        <input type="hidden" name="skp_schedule_form_action" value="1" />
        <?php if(!empty($_GET['skp_schedule_id'])):?><input type="hidden" name="skp_schedule_id" value="<?php echo $_GET['skp_schedule_id'];?>" /><?php endif;?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="skp_schedule_name"><?php echo __( 'Schedule Name', 'skp-textdomain' ); ?></label>
                    </th>
                    <td class="skp-form-field">
                        <input type="text" id="skp_schedule_name" class="widefat" name="skp_schedule_name" value="<?php echo (!empty($form_data['skp_schedule_name'])) ? esc_attr($form_data['skp_schedule_name']) : ''; ?>" class="skp_input skp_schedule_name" class="regular-text" />
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="skp_schedule_post_type"><?php echo __( 'Post Type', 'skp-textdomain' ); ?></label>
                    </th>
                    <td class="skp-form-field-select">

                        <?php if( count( $post_types ) > 1 ): ?>
                            <select id="skp_schedule_post_type" class="skp_select skp_post_type" name="skp_schedule_post_type">
                                <option value=""><?php echo __( 'Select post type...', 'skp-textdomain' ); ?></option>
                                <?php foreach( $post_types as $post_type ):?>
                                    <option<?php echo (!empty($form_data['skp_schedule_post_type']) && $form_data['skp_schedule_post_type'] == $post_type->name ) ? ' selected="selected"' : '';?> value="<?php echo $post_type->name;?>"><?php echo $post_type->label;?></option>
                                <?php endforeach;?>
                            </select>
                            <span class="spinner"></span>
                        <?php else: ?>
                            <input type="hidden" name="skp_schedule_post_type" value="<?php echo $post_types[0]->name; ?>" />
                            <p><?php echo $post_types[0]->label; ?></p>
                        <?php endif; ?>
                    </td>
                </tr>

                <?php if( count( $post_types ) > 1 ): ?>
                <tr>
                    <th scope="row">
                        <label><?php echo __( 'Taxonomies', 'skp-textdomain' ); ?></label>
                    </th>
                    <td>
                        <div id="skp_schedule_taxonomies_ajax">
                            <?php if(!empty($form_data['skp_schedule_post_type'])): $taxonomy_data = (!empty($form_data['skp_schedule_taxonomy'])) ? $form_data['skp_schedule_taxonomy'] : false;?>
                                <?php skp_schedule_load_taxonomies_checkboxes($form_data['skp_schedule_post_type'], $taxonomy_data);?>
                            <?php else:?>
                                <?php echo __( 'Select a post type first.', 'skp-textdomain' ); ?>
                            <?php endif;?>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
                
                <tr>
                    <th scope="row">
                        <label for="skp_schedule_older_than"><?php echo __( 'Schedule posts older than', 'skp-textdomain' ); ?></label>
                    </th>
                    <td class="skp-form-field">
                        <input type="number" id="skp_schedule_older_than" value="<?php echo (!empty($form_data['skp_schedule_older_than'])) ? esc_attr($form_data['skp_schedule_older_than']) : '0'; ?>" name="skp_schedule_older_than" class="skp_input skp_schedule_older_than" class="regular-text" min="0" /> days
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label><?php echo __( 'Schedule to run every', 'skp-textdomain' ); ?></label>
                    </th>
                    <td>
                        <fieldset>
                            <?php  foreach( skp_weekdays() as $day_number => $day_name): ?>
                                <input class="skp-weekday-checkbox" <?php echo (!empty($form_data['skp_schedule_day']) && in_array($day_number, $form_data['skp_schedule_day']) ) ? ' checked="checked"' : '';?> type="checkbox" id="skp_schedule_days_<?php echo strtolower($day_name);?>"    name="skp_schedule_day[]" value="<?php echo $day_number;?>" />
                                <label class="skp-weekday-label" for="skp_schedule_days_<?php echo strtolower($day_name);?>"><?php echo $day_name;?></label>                                
                            <?php endforeach;?>
                        </fieldset>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label><?php echo __( 'At exactly', 'skp-textdomain' ); ?></label>
                    </th>
                    <td class="skp-form-field-select skp-small">

                        <?php 
                            if( empty( $form_data['skp_schedule_hour'] ) )
                                $form_data['skp_schedule_hour'] = array();

                            // This is added to generate a hidden template used to
                            // add more posting times through javascript
                            array_unshift( $form_data['skp_schedule_hour'] , array( 'hour' => '#', 'minute' => '#' ) );

                        ?>

                        <?php foreach( $form_data['skp_schedule_hour'] as $key => $time_value ): ?>

                            <?php 
                                $key--;
                            ?>
                            
                            <div class="skp-posting-time <?php echo ( $key == -1 ? 'skp-posting-time-template' : '' ) ?>" data-key="<?php echo $key; ?>">
                                <select name="skp_schedule_hour[<?php echo $key; ?>][hour]">
                                    <?php for( $i=0; $i<24; $i++ ): ?>
                                        <option <?php echo ( $i == $time_value['hour'] ) ? ' selected="selected"' : '';?> value="<?php echo $i;?>"><?php echo date( str_replace( ':i', '', get_option('time_format') ), mktime( $i, 0, 0 ) );?></option>
                                    <?php endfor;?>
                                </select>

                                <select name="skp_schedule_hour[<?php echo $key; ?>][minute]">
                                    <?php for( $i=0; $i<60; $i+=15 ): ?>
                                        <option <?php echo ( $i == $time_value['minute'] ) ? ' selected="selected"' : '';?> value="<?php echo $i;?>"><?php echo $i; ?></option>
                                    <?php endfor;?>
                                </select>

                                <a href="#" class="skp-posting-time-remove dashicons dashicons-no"></a>
                            </div>

                        <?php endforeach; ?>

                        <div>
                            <a href="#" id="skp-add-posting-time-btn" class="button-secondary"><?php echo __( 'Add Posting Time', 'skp-textdomain' ); ?></a>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label><?php echo __( 'On these platforms', 'skp-textdomain' ); ?></label>
                    </th>
                    <td>
                        
                        
                        <?php if($platform_accounts): ?>
                            
                            <?php foreach( $platform_accounts as $account ) { 
                                skp_get_partial( 'platform-account', array( 'account' => $account, 'has-name' => false, 'has-actions' => false, 'has-checkbox' => 'skp_schedule_platform_accounts', 'form-data' => $form_data ) );
                            } ?>
                        
                        <?php else:?>
                            <p class="skp-error-no-platform-accounts"><?php echo __( 'To create a schedule, you must add at least one social platform.', 'skp-textdomain' ); ?> <a href="<?php echo add_query_arg( array( 'page' => 'skp-settings' ), admin_url('admin.php') ); ?>"><?php echo __( 'You can add one here.', 'skp-textdomain' ); ?></a></p>
                        <?php endif;?>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label><?php echo __( 'Custom Message', 'skp-textdomain' ); ?></label>
                    </th>
                    <td class="skp-form-field">

                        <?php skp_get_partial( 'accounts-custom-messages', array( 'field-name' => 'skp_schedule_content', 'form-data' => ( ! empty( $form_data['skp_schedule_content'] ) ? $form_data['skp_schedule_content'] : array() ), 'disabled' => true ) ); ?>
                        
                    </td>
                </tr>

            </tbody>
        </table>

        <p class="submit"><input type="submit" class="button button-primary" value="<?php echo __( 'Save Changes', 'skp-textdomain' ); ?>" /></p>
    </form>

</div>