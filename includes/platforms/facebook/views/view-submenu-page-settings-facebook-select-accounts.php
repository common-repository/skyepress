<?php
    
    // Exit if accessed directly
    if ( ! defined( 'ABSPATH' ) ) exit;

?>
<?php skp_get_partial( 'plugin-header' ); ?>
<div class="wrap">

    <h1><?php echo __( 'Settings', 'skp-textdomain' ) ?></h1>

    <?php skp_get_partial('settings-tab-navigation', array( 'disable-javascript' => true ) );?>
    
    <form method="post" action="<?php echo add_query_arg( array( 'page' => 'skp-settings', 'skp_platform' => 'facebook' ), admin_url('admin.php') ); ?>">
        <?php wp_nonce_field( 'skp_insert_user_details', 'skp_tkn' ); ?>

        <div class="skp-postbox-new-account-wrapper">

            <!-- Heading -->
            <h2 class="skp-postbox-new-account-heading">
                <span class="skp-icon-facebook_profile"><!-- --></span>
                <?php echo __( 'Add new Facebook account', 'skp-textdomain' ); ?>
            </h2>

            <!-- Steps -->
            <ul class="skp-postbox-new-account-steps">
                <li class="skp-done"><?php echo __( 'Facebook App Credentials', 'skp-textdomain' ); ?></li>
                <li class="skp-current"><?php echo __( 'Select Account', 'skp-textdomain' ); ?></li>
            </ul>

            <div class="skp-postbox-new-account postbox">

                <!-- Body -->
                <div class="skp-postbox-new-account-inner">

                    <p><?php echo __( 'Select which account you would like to post updates to:', 'skp-textdomain' ); ?></p>

                    <?php
                    
                    if( !empty( $facebook_profile_accounts ) ) {

                        echo '<p><strong>' . __( 'Facebook Profile', 'skp-textdomain' ) . '</strong></p>';

                        foreach( $facebook_profile_accounts as $account ) {
                            if( isset($facebook_existing_accounts['facebook_accounts']) && in_array( $account->platform_unique, $facebook_existing_accounts['facebook_accounts'] ) )
                                skp_get_partial( 'platform-account', array( 'account' => $account, 'has-name' => true, 'has-actions' => false ) );
                            else
                                skp_get_partial( 'platform-account', array( 'account' => $account, 'has-name' => true, 'has-actions' => false, 'has-radio' => 'facebook_accounts', 'form-data' => (isset($facebook_existing_accounts) ? $facebook_existing_accounts : false) ) );
                        }

                    }
    
                    ?>

                    <div class="skp-clear"><!-- --></div>

                    <?php
                    
                    if( !empty( $facebook_page_accounts ) ) {

                        echo '<p><strong>' . __( 'Facebook Pages', 'skp-textdomain' ) . '</strong></p>';

                        foreach( $facebook_page_accounts as $account ) {
                            if( isset($facebook_existing_accounts['facebook_accounts']) && in_array( $account->platform_unique, $facebook_existing_accounts['facebook_accounts'] ) )
                                skp_get_partial( 'platform-account', array( 'account' => $account, 'has-name' => true, 'has-actions' => false ) );
                            else
                                skp_get_partial( 'platform-account', array( 'account' => $account, 'has-name' => true, 'has-actions' => false, 'has-radio' => 'facebook_accounts', 'form-data' => (isset($facebook_existing_accounts) ? $facebook_existing_accounts : false) ) );
                        }

                    }
    
                    ?>

                </div>
                
                <!-- Footer -->
                <div class="skp-postbox-new-account-footer">
                    <a class="button-secondary" href="<?php echo admin_url('admin.php?page=skp-settings'); ?>"><?php echo __( 'Cancel', 'skp-textdomain' ); ?></a>
                    <input type="submit" class="button button-primary" value="<?php echo __( 'Add Account', 'skp-textdomain' ); ?>" />
                </div>

            </div>
        </div>     
    </form> 
    
</div>