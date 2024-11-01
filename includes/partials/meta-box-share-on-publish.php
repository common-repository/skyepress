<?php
    
    // Exit if accessed directly
    if ( ! defined( 'ABSPATH' ) ) exit;

	/**
	 * Action hook to allow extra fields to be added
	 *
	 * @param array $this->data
	 *
	 */
	do_action( 'skp_meta_box_partial_share_on_publish_top', $this->data );

?>

<!-- On Post Publish -->
<div class="skp-form-field skp-form-field-share-on-publish">
	<div class="skp-switch">
		<input  id="_skp_share_on_publish" type="checkbox" name="_skp_share_on_publish" value="1" class="skp-toggle skp-toggle-round" <?php echo ( !empty( $this->data['form-data']['_skp_share_on_publish'] ) && $this->data['form-data']['_skp_share_on_publish'] == true ? 'checked="checked"' : '' ); ?> />
		<label for="_skp_share_on_publish"></label>
	</div>

	<label for="_skp_share_on_publish"><strong><?php echo __( 'Share on Post Publish', 'skp-textdomain' ) ?></strong></label>
</div>

<!-- Custom Content for on Publish -->
<div class="skp-share-on-publish-custom-content">

	<!-- Platform Accounts -->
	<h4><?php echo __( 'Accounts', 'skp-textdomain' ); ?></h4>
	<?php $platform_accounts = skp_get_platform_accounts();?>
    
    <?php if( $platform_accounts ):?>        
    	<?php foreach( $platform_accounts as $platform_account )
    			skp_get_partial( 'platform-account', array( 'account' => $platform_account, 'has-checkbox' => '_skp_platform_account', 'form-data' => $this->data['form-data'] ) );
    	?>

    	<p class="description"><?php echo __( 'Select on which accounts you wish to share this post when publishing it.', 'skp-textdomain' ); ?></p>
     <?php else:?>
        <p class="skp-error-no-platform-accounts"><?php echo __( 'To share this post on publish, you must add at least one social platform.', 'skp-textdomain' ); ?> <a href="<?php echo add_query_arg( array( 'page' => 'skp-settings' ), admin_url('admin.php') ); ?>"><?php echo __( 'You can add one here.', 'skp-textdomain' ); ?></a></p>
    <?php endif;?>

	<!-- Custom Content -->
	<h4><?php echo __( 'Custom Message', 'skp-textdomain' ); ?></h4>
	<div class="skp-form-field skp-in-meta-box">
        
        <?php skp_get_partial( 'accounts-custom-messages', array( 'field-name' => '_skp_post_custom_content', 'form-data' => $this->data['form-data']['_skp_post_custom_content'], 'disabled' => true ) ); ?>

	</div>

</div>

<?php

	/**
	 * Action hook to allow extra fields to be added
	 *
	 * @param array $this->data
	 *
	 */
	do_action( 'skp_meta_box_partial_share_on_publish_bottom', $this->data );

?>

<!-- Nonce field for safety -->
<?php wp_nonce_field( 'skp_meta_box_share_on_publish', 'skp_tkn', false ); ?>