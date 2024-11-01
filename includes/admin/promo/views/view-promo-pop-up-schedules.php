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
				<h2><?php echo __( 'Unlimited schedules', 'skp-textdomain' ); ?></h2>

				<img src="<?php echo SKP_PLUGIN_DIR_URL . 'includes/admin/promo/assets/img/promo-schedule-1.png' ?>" />

				<p><?php echo __( 'Add multiple schedules and take advantage of custom posting hours for weekdays, weekends on different accounts.', 'skp-textdomain' ); ?></p>
			</div>

			<div class="skp-col-1-2 skp-col-right skp-no-padding-top skp-no-padding-bottom">
				<h2><?php echo __( 'Custom Post Types and Taxonomies', 'skp-textdomain' ); ?></h2>

				<img src="<?php echo SKP_PLUGIN_DIR_URL . 'includes/admin/promo/assets/img/promo-schedule-2.png' ?>" />

				<p><?php echo __( 'Revive not only posts, but pages and custom post types. Filter schedules by category and custom taxonomies.', 'skp-textdomain' ); ?></p>
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