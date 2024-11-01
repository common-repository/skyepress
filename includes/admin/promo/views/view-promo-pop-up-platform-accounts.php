<?php
    
    // Exit if accessed directly
    if ( ! defined( 'ABSPATH' ) ) exit;

?>
<div class="skp-promo-pop-up-wrapper">

	<!-- Pop-Up Box -->
	<div class="skp-promo-pop-up">

		<!-- Close button -->
		<span class="skp-promo-pop-up-close dashicons dashicons-no"><!-- --></span>
		
		<!-- Main -->
		<div class="skp-row skp-xl-padding">

			<div class="skp-col-1-2 skp-col-left skp-no-padding-top skp-no-padding-bottom">
				<h2><?php echo __( 'Connect LinkedIn', 'skp-textdomain' ); ?></h2>

				<img src="<?php echo SKP_PLUGIN_DIR_URL . 'includes/admin/promo/assets/img/promo-accounts-1.png' ?>" />

				<p><?php echo __( 'Connect your LinkedIn profile account or pages and start sharing your posts with your professional peers.', 'skp-textdomain' ); ?></p>
			</div>

			<div class="skp-col-1-2 skp-col-right skp-no-padding-top skp-no-padding-bottom">
				<h2><?php echo __( 'Add Multiple Accounts', 'skp-textdomain' ); ?></h2>

				<img src="<?php echo SKP_PLUGIN_DIR_URL . 'includes/admin/promo/assets/img/promo-accounts-2.png' ?>" />

				<p><?php echo __( 'Whether it is your personal profile or a page that you manage, take advantage of connecting and sharing on multiple accounts.', 'skp-textdomain' ); ?></p>
			</div>
		</div>

		<!-- Call to Action -->
		<div class="skp-call-to-action-wrapper">
			<a class="button button-primary" target="_blank" href="https://devpups.com/skyepress/"><?php echo __( 'Upgrade to Pro', 'skp-textdomain' ); ?></a>
		</div>

	</div>

	<!-- Pop-Up Overlay -->
	<div class="skp-promo-pop-up-overlay"><!-- --></div>

</div>