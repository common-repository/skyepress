<?php
    
    // Exit if accessed directly
    if ( ! defined( 'ABSPATH' ) ) exit;

	$account 	  = $this->data['account'];

	$has_name     = isset( $this->data['has-name'] ) ? $this->data['has-name'] : false;
	$has_actions  = isset( $this->data['has-actions'] ) ? $this->data['has-actions'] : false;

    $has_checkbox = isset( $this->data['has-checkbox'] ) ? $this->data['has-checkbox'] : false;
    $has_radio 	  = isset( $this->data['has-radio'] ) ? $this->data['has-radio'] : false;
    $has_input 	  = $has_checkbox ? $has_checkbox : ( $has_radio ? $has_radio : false );
    $input_type   = $has_checkbox ? 'checkbox' : ( $has_radio ? 'radio' : false );

    $form_data 	  = isset( $this->data['form-data'] ) ? $this->data['form-data'] : false;
    	
?>

<div class="skp-platform-account-entry <?php echo $has_actions ? 'skp-has-actions' : ''; ?> <?php echo $has_name ? 'skp-has-name' : ''; ?> <?php echo $has_input ? 'skp-has-input' : ''; ?>">
    
	<?php if( $has_input ): ?>
        <input id="<?php echo $has_input . '-' . $account->platform_unique; ?>" type="<?php echo $input_type ?>" <?php echo (!empty($form_data[$has_input]) && in_array($account->platform_unique, $form_data[$has_input]) ) ? ' checked="checked"' : '';?> name="<?php echo $has_input;?>[]" value="<?php echo $account->platform_unique;?>" />
    <?php endif; ?>

    <!-- Entry Inner -->
    <label class="skp-platform-account-inner" for="<?php echo ( $has_input ? $has_input . '-' . $account->platform_unique : '' ); ?>">
	    
		<!-- Platform Account Avatar -->
		<span class="skp-platform-account-avatar">
			<img src="<?php echo $account->platform_user_details->avatar; ?>" />
			<span class="skp-platform-icon skp-icon-<?php echo $account->platform_slug; ?>"></span>
		</span>
	    
		<?php if( $has_name ): ?>
			<div class="skp-platform-account-more">

				<!-- Platform Account Name -->
				<span class="skp-platform-account-name"><?php echo $account->platform_user_details->name; ?></span><br />

				<!-- Platform Account Actions -->
				<?php if( $has_actions ): ?>
					<span class="skp-platform-account-actions">
						<a class="skp-platform-account-remove" onclick="return confirm('<?php echo __( "Are you sure you want to remove this account? All scheduled posts for this account will not be delivered.", "skp-textdomain" ) ?> ' );" href="<?php echo wp_nonce_url( add_query_arg( array( 'page' => 'skp-settings', 'skp_platform' => $account->platform_slug, 'skp_platform_unique' => $account->platform_unique ) , admin_url('admin.php') ), 'skp_remove_account', 'skp_tkn' ) ?>"><?php echo __( 'Remove', 'skp-textdomain' ); ?></a>
					</span>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</label>

</div>