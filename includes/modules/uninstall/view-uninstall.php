<?php
    
    // Exit if accessed directly
    if ( ! defined( 'ABSPATH' ) ) exit;

?>
<?php skp_get_partial( 'plugin-header' ); ?>
<div class="wrap">

    <h1><?php _e('Uninstall SkyePress', 'skp-textdomain');?></h1>
    <p><?php _e('Proceeding with this means that all the data stored by our plugin will be <strong>permanently</strong> removed.', 'skp-textdomain');?></p>
    
    <h3><?php _e('Please confirm your action.', 'skp-textdomain');?></h3>
    <p><?php _e('We have to make sure you don\'t accidentally delete all your data, so to confirm your action please type in <strong>REMOVE</strong> in the box below.', 'skp-textdomain');?></p>
    
    <form method="post" action="<?php echo add_query_arg( array( 'page' => 'skp-uninstall') , admin_url('admin.php') ); ?>">
        <?php wp_nonce_field( 'skp_uninstall', 'skp_tkn' ) ?>
        
        <input type="text" name="skp_uninstall_plugin" id="skp-uninstall-confirmation" />
        
        <p class="submit"><input id="skp-uninstall-plugin-submit" disabled="disabled" type="submit" class="button button-primary" value="<?php _e('Uninstall', 'skp-textdomain');?>" /> <a href="<?php echo admin_url('plugins.php');?>" class="button button-secondary"><?php _e('Cancel', 'skp-textdomain');?></a></p>
    </form>
    
</div>