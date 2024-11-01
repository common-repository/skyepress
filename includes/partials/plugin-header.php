<?php
    
    // Exit if accessed directly
    if ( ! defined( 'ABSPATH' ) ) exit;

	$page = ( isset( $_GET['page'] ) && strpos( $_GET['page'], 'skp' ) !== false ? sanitize_text_field( $_GET['page'] ) : '' );
?>

<div class="skp-page-header">
	<span class="skp-logo">
		SkyePress 
		<span><?php echo ( SKP_VERSION_OPTION == 1 ? 'Free' : ( SKP_VERSION_OPTION == 2 ? 'Basic' : 'Pro' ) ); ?></span>
	</span>
	<small>v.<?php echo SKP_VERSION; ?></small>

	<nav>
        <?php
        $header_buttons = apply_filters( 'skp_header_buttons', 
    		array(
    			'documentation' => '<a href="http://docs.devpups.com/skyepress/what-is-skyepress/" target="_blank"><i class="dashicons dashicons-book"></i>' . __( "Documentation", "skp-textdomain" ) . '</a>',
    			//'feedback'	    => '<a href="https://wordpress.org/plugins/" target="_blank">5<i class="dashicons dashicons-star-filled"></i>' . __( "Leave a Review", "skp-textdomain" ) . '</a>'
    		)
    	);
		?>
        <?php foreach( $header_buttons as $header_button ): ?>
            <?php echo $header_button;?>
        <?php endforeach;?>		
	</nav>

    <?php if( SKP_VERSION_OPTION == 1 ): ?>
        <a id="skp-to-premium" href="http://www.devpups.com/skyepress?utm_source=plugin&utm_medium=header-to-premium&utm_campaign=skyepress" target="_blank"><i class="dashicons dashicons-external"></i><?php echo __( 'Upgrade to Pro', 'skp-textdomain' ); ?></a>
    <?php endif; ?>
</div>