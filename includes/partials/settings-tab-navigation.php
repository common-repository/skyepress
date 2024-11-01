<?php
    
    // Exit if accessed directly
    if ( ! defined( 'ABSPATH' ) ) exit;

	$active_tab 		= ( !empty( $_GET['skp-tab'] ) ? $_GET['skp-tab'] : 'platform-accounts' ); 
	$disable_javascript = isset( $this->data['disable-javascript'] ) ? true : false;

	$nav_tabs = apply_filters( 'skp_settings_nav_tabs', 
		array(
			'platform-accounts' => __( 'Accounts', 'skp-textdomain' ),
			'general-settings'	=> __( 'General Settings', 'skp-textdomain' )
		),
		$this->data
	);
?>

<!-- Navigation Tabs -->
<h2 class="nav-tab-wrapper">
	<?php foreach( $nav_tabs as $nav_tab => $nav_tab_name ): ?>
		<a href="<?php echo add_query_arg( array( 'page' => 'skp-settings', 'skp-tab' => $nav_tab ), admin_url('admin.php') );?>" data-tab="<?php echo $nav_tab; ?>" class="nav-tab<?php echo (!$disable_javascript) ? ' skp-nav-tab' : '';?> <?php echo ( $active_tab == $nav_tab ? 'nav-tab-active' : '' ); ?>"><?php echo $nav_tab_name; ?></a>    
	<?php endforeach; ?>
</h2>