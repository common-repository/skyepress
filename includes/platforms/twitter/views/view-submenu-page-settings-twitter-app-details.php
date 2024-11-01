<?php
    
    // Exit if accessed directly
    if ( ! defined( 'ABSPATH' ) ) exit;

?>

<?php skp_get_partial( 'plugin-header' ); ?>
<div class="wrap">

    <h1><?php echo __( 'Settings', 'skp-textdomain' ) ?></h1>

    <?php skp_get_partial('settings-tab-navigation', array( 'disable-javascript' => true ) );?>
    
    <form method="post" action="<?php echo add_query_arg( array( 'page' => 'skp-settings', 'skp_platform' => 'twitter' ), admin_url('admin.php') ); ?>">

        <?php wp_nonce_field( 'skp_connect_account', 'skp_tkn' ); ?>

        <div class="skp-postbox-new-account-wrapper">
            
            <!-- Heading -->
            <h2 class="skp-postbox-new-account-heading">
                <span class="skp-icon-twitter"><!-- --></span>
                <?php echo __( 'Add new Twitter account', 'skp-textdomain' ); ?>
            </h2>

            <!-- Steps -->
            <ul class="skp-postbox-new-account-steps skp-postbox-new-account-steps-twitter">
                <li class="skp-current"><?php echo __( 'Twitter App Credentials', 'skp-textdomain' ); ?></li>
            </ul>

            <!-- Postbox -->
            <div class="skp-postbox-new-account postbox">

                <!-- Body -->
                <div class="skp-postbox-new-account-inner">

                    <p><?php echo __( 'Please add your Twitter App credentials in the fields below.', 'skp-textdomain' ) . ' ' . sprintf( __( 'Don\'t have a Twitter App? <a href="%s" target="_blank">Learn how to create one.</a>', 'skp-textdomain' ), 'http://docs.devpups.com/skyepress/how-to-create-a-twitter-app/' ); ?></p>

                    <table class="form-table">
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <label for="twitter_consumer_key">Twitter Consumer Key</label>
                                </th>
                                <td>
                                    <input type="text" id="twitter_consumer_key" name="twitter_consumer_key" value="<?php echo ( !empty($settings['twitter_consumer_key'])) ? esc_attr($settings['twitter_consumer_key']) : ''; ?>" class="skp_input" class="regular-text" />
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="twitter_consumer_secret">Twitter Consumer Secret</label>
                                </th>
                                <td>
                                    <input type="text" id="twitter_consumer_secret" name="twitter_consumer_secret" value="<?php echo ( !empty($settings['twitter_consumer_secret'])) ? esc_attr($settings['twitter_consumer_secret']) : ''; ?>" class="skp_input" class="regular-text" />
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