<?php
    
    // Exit if accessed directly
    if ( ! defined( 'ABSPATH' ) ) exit;

?>
<?php skp_get_partial( 'plugin-header' ); ?>
<div class="wrap">

    <h1><?php echo __( 'Settings', 'skp-textdomain' ) ?></h1>

    <?php skp_get_partial('settings-tab-navigation');?>

    <!-- Form Wrapper -->
    <form method="post" action="options.php">

        <?php 
            settings_fields( 'skp_settings' ); 
            $settings = get_option( 'skp_settings', array() );
        ?>
        
        <!-- Platform Accounts Tab -->
        <div id="skp-tab-platform-accounts" class="skp-tab <?php echo ( $active_tab == 'platform-accounts' ? 'skp-tab-active' : '' ); ?>">

            <p><strong><?php echo __( 'Connect the social accounts you wish to share posts to.', 'skp-textdomain' ); ?></strong></p><br />

            <?php do_action( 'skp_tab_platform_accounts_settings', $settings ); ?>

        </div><!-- End of Platform Accounts tab -->
        
        
        <!-- General Settings Tab -->
        <div id="skp-tab-general-settings" class="skp-tab <?php echo ( $active_tab == 'general-settings' ? 'skp-tab-active' : '' ); ?>">

            <table class="form-table">
                <tbody>

                    <?php do_action( 'skp_tab_general_settings_before_fields', $settings ); ?>

                    <tr>
                        <th scope="row">
                            <label><?php echo __( 'Custom Messages', 'skp-textdomain' ); ?></label>
                        </th>
                        <td>
                            <div class="skp-form-field">
                                <?php skp_get_partial( 'accounts-custom-messages', array( 'field-name' => 'skp_settings[custom_messages]', 'form-data' => ( ! empty( $settings['custom_messages'] ) ? $settings['custom_messages'] : array() ) ) ); ?>
                            </div>
                        </td>
                    </tr>

                    <?php do_action( 'skp_tab_general_settings_after_fields', $settings ); ?>

                </tbody>
            </table>

            <input type="hidden" name="skp_settings[always_update]" value="<?php echo ( isset( $settings['always_update'] ) && $settings['always_update'] == 1 ? 0 : 1 ); ?>" />

            <!-- Save Settings Button -->
            <input type="submit" class="button-primary" value="<?php echo __( 'Save Settings', 'skp-textdomain' ); ?>" />

        </div><!-- End of General Settings Tab -->


        <?php do_action( 'skp_settings_form_bottom', $settings, $active_tab ); ?>

    </form>
    
</div>