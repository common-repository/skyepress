<?php
    
    // Exit if accessed directly
    if ( ! defined( 'ABSPATH' ) ) exit;

	$platform_accounts = skp_get_platform_accounts();

	$field_name	  = isset( $this->data['field-name'] ) ? $this->data['field-name'] : '';
	$form_data 	  = isset( $this->data['form-data'] ) ? $this->data['form-data'] : false;
	$is_disabled  = isset( $this->data['disabled'] ) && true === $this->data['disabled'] ? true : false ;

?>
                    
<?php if( ! empty( $platform_accounts ) ): ?>

	<!-- Custom Messages Wrapper -->
	<div class="skp-platform-account-messages-wrapper">

		<!-- Tabs for Custom Message -->
	    <div class="skp-platform-account-messages-tabs-wrapper">
	        <?php
	        	$current_tab = 1;
	        	foreach( $platform_accounts as $account ): ?>
	        	<div class="skp-platform-account-messages-nav-tab <?php echo ( $is_disabled ? 'skp-nav-tab-disabled' : '' ); ?> <?php echo ( $current_tab == 1 ? 'skp-nav-tab-active' : '' ); ?>" data-tab="<?php echo esc_attr( $account->platform_unique ); ?>">
		        	<?php echo skp_get_partial( 'platform-account', array( 'account' => $account ) ); ?>
		        </div>
			<?php $current_tab++; endforeach; ?>

		</div>

		<!-- Custom Message per Account -->
	    <?php 
	    	$current_tab = 1;
	    	foreach( $platform_accounts as $account ):?>
	        <div class="skp-platform-account-messages-tab <?php echo ( $current_tab == 1 ? 'skp-tab-active' : '' ); ?>" data-tab="<?php echo esc_attr( $account->platform_unique ); ?>">
	            <div class="skp-platform-account-message-account-name"><?php echo skp_nice_platforms(array($account->platform_unique));?></div>
	            <textarea <?php echo ( $is_disabled ? 'disabled' : '' ); ?> name="<?php echo $field_name . '['. $account->platform_unique . ']';?>"><?php echo ( ! empty( $form_data[$account->platform_unique] ) ? esc_attr( $form_data[$account->platform_unique] ) : '' ); ?></textarea>
	        </div>
	    <?php $current_tab++; endforeach; ?>

	    <p class="description"><?php echo __( 'Set custom messages for each of your accounts. You can use the following tags: {{post_title}}, {{post_excerpt}}', 'skp-textdomain' ); ?></p>
	</div>

<?php else:?>
    <p class="description"><?php echo __( 'You will be able to write custom messages after you add a social account.', 'skp-textdomain' ); ?></p>
<?php endif;?>