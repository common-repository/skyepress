<?php
    
    // Exit if accessed directly
    if ( ! defined( 'ABSPATH' ) ) exit;

?>
<?php skp_get_partial( 'plugin-header' ); ?>
<div class="wrap">

    <h1><?php echo __( 'Settings', 'skp-textdomain' ) ?></h1>

    <?php skp_get_partial('settings-tab-navigation', array( 'disable-javascript' => true ) );?>
    
    <form method="post" action="<?php echo add_query_arg( array( 'page' => 'skp-settings', 'skp_platform' => 'facebook' ), admin_url('admin.php') ); ?>">

        <?php wp_nonce_field( 'skp_connect_account', 'skp_tkn' ); ?>

        <div class="skp-postbox-new-account-wrapper">
            
            <!-- Heading -->
            <h2 class="skp-postbox-new-account-heading">
                <span class="skp-icon-facebook_profile"><!-- --></span>
                <?php echo __( 'Add new Facebook account', 'skp-textdomain' ); ?>
            </h2>

            <!-- Steps -->
            <ul class="skp-postbox-new-account-steps">
                <li class="skp-current"><?php echo __( 'Facebook App Credentials', 'skp-textdomain' ); ?></li>
                <li><?php echo __( 'Select Account', 'skp-textdomain' ); ?></li>
            </ul>

            <!-- Postbox -->
            <div class="skp-postbox-new-account postbox">

                <!-- Body -->
                <div class="skp-postbox-new-account-inner">

                    <p><?php echo __( 'Please add your Facebook App credentials in the fields below.', 'skp-textdomain' ) . ' ' . sprintf( __( 'Don\'t have a Facebook App? <a href="%s" target="_blank">Learn how to create one.</a>', 'skp-textdomain' ), 'http://docs.devpups.com/skyepress/how-to-create-a-facebook-app/' ); ?></p>

                    <table class="form-table">
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <label for="facebook_app_id">Facebook App ID</label>
                                </th>
                                <td>
                                    <input type="text" id="facebook_app_id" name="facebook_app_id" value="<?php echo ( !empty($settings['facebook_app_id'])) ? esc_attr($settings['facebook_app_id']) : ''; ?>" class="skp_input" class="regular-text" />
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="facebook_app_secret">Facebook App Secret</label>
                                </th>
                                <td>
                                    <input type="text" id="facebook_app_secret" name="facebook_app_secret" value="<?php echo ( !empty($settings['facebook_app_secret'])) ? esc_attr($settings['facebook_app_secret']) : ''; ?>" class="skp_input" class="regular-text" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Footer -->
                <div class="skp-postbox-new-account-footer">
                    <a class="button-secondary" href="<?php echo admin_url('admin.php?page=skp-settings'); ?>"><?php echo __( 'Cancel', 'skp-textdomain' ); ?></a>
                    <input type="submit" class="button button-primary" value="<?php echo __( 'Continue', 'skp-textdomain' ); ?>" />
                </div>

            </div>

        </div>
    </form> 
    
</div>