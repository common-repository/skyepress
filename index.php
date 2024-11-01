<?php
/**
 * Plugin Name: SkyePress
 * Plugin URI: http://www.devpups.com/
 * Description: Automatically post to your Facebook and Twitter profiles when publishing your articles, schedule your posts to be shared at a later date and much more...
 * Version: 1.0.1
 * Author: DevPups, iova.mihai, murgroland
 * Author URI: http://www.devpups.com/
 * License: GPL2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


Class SkyePress {

	/**
	 * The constructor
	 *
	 */
	public function __construct() {
        
        session_start();
        
		// Define constants
		define( 'SKP_VERSION', 			 	'1.0.1' );
		define( 'SKP_PLUGIN_FILE', 			__FILE__ );
		define( 'SKP_PLUGIN_DIR', 		  	plugin_dir_path( __FILE__ ) );
		define( 'SKP_PLUGIN_DIR_URL', 	  	plugin_dir_url( __FILE__ ) );
        define( 'SKP_PLUGIN_BASENAME', 	 	plugin_basename(__FILE__) );
		define( 'SKP_PLUGIN_PARTIALS_DIR',  plugin_dir_path( __FILE__ ) . 'includes/partials' );
        define( 'SKP_PLUGIN_MODULES_DIR',   plugin_dir_path( __FILE__ ) . 'includes/modules/' );
        define( 'SKP_PLUGIN_PLATFORMS_DIR', plugin_dir_path( __FILE__ ) . 'includes/platforms/' );
        
        add_action( 'plugins_loaded', array( $this, 'load_modules' ) );
        add_action( 'plugins_loaded', array( $this, 'load_platforms' ) );
        
        add_action( 'admin_menu', array( $this, 'add_main_menu_page' ), 10 );
        add_action( 'admin_menu', array( $this, 'remove_main_menu_page' ), 11 );

		// Check if just updated
		add_action( 'plugins_loaded', array( $this, 'update_check' ) );
        
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Create and update database tables
		add_action( 'skp_update_check', array( $this, 'update_database_tables' ) );

		// Add default settings upon plugin activation
		add_action( 'skp_update_check', array( $this, 'add_default_settings' ) );
        
        spl_autoload_register( array($this, 'register_classes') );
        
        if( $this->set_version_option() )
        	$this->include_files();
        
	}
    

	/**
	 * Autoload required classes
	 *
	 */
    public function register_classes($class){

        $class = 'class' . strtolower(str_replace(array("SKP",'_'), array('','-'),$class)) . '.php';
        foreach(new recursiveIteratorIterator( new recursiveDirectoryIterator(SKP_PLUGIN_DIR . '/includes/')) as $file){
            if(basename($file) == $class){
                require $file; 
                break;
            }
        }          
    }
    
    /**
	 * Load all modules
	 *
	 */
    public function load_modules() {
        $dirs = array_filter(glob(SKP_PLUGIN_MODULES_DIR . '*'), 'is_dir');
        foreach($dirs as $dir){     
            if( file_exists($file =  $dir . '/' . basename($dir) . '.php') ){
                include_once ($file);
            }
        }
        
        SKP_Schedule_Listener::init();
    }


    /**
	 * Load all platforms
	 *
	 */
    public function load_platforms() {
        $dirs = array_filter(glob(SKP_PLUGIN_PLATFORMS_DIR . '*'), 'is_dir');
        foreach($dirs as $dir){     
            if( file_exists($file =  $dir . '/' . basename($dir) . '.php') ){
                include_once ($file);
            }
        }
    }


	/**
	 * Include files
	 *
	 */
	public function include_files() {

		// Functions
		require_once SKP_PLUGIN_DIR . 'includes/functions-platform-account.php';
        require_once SKP_PLUGIN_DIR . 'includes/functions-schedule.php';
		require_once SKP_PLUGIN_DIR . 'includes/functions-post.php';
        require_once SKP_PLUGIN_DIR . 'includes/functions.php';

        // Subpages
    	require_once SKP_PLUGIN_DIR . '/includes/admin/submenu-dashboard.php';     
    	require_once SKP_PLUGIN_DIR . '/includes/admin/submenu-revive-posts.php';
    	require_once SKP_PLUGIN_DIR . '/includes/admin/submenu-settings.php';

    	if( SKP_VERSION_OPTION == 1 && file_exists( SKP_PLUGIN_DIR . '/includes/admin/promo/promo-pop-up.php' ) )
			require_once SKP_PLUGIN_DIR . '/includes/admin/promo/promo-pop-up.php';
       
	}
    
    /**
	 * Enqueue scripts and styles
	 *
	 */
    public function enqueue_admin_scripts( $hook ) {


		if( strpos( $hook, 'skp' ) !== false ) {
            //maybe we don't want something to load everywhere.

			if( ! wp_script_is( 'select2-js', 'registered' ) ) {
				wp_register_script( 'select2-js', SKP_PLUGIN_DIR_URL . 'assets/js/select2.min.js', array( 'jquery' ), SKP_VERSION );
				wp_enqueue_script( 'select2-js' );

				wp_register_style( 'select2-css', SKP_PLUGIN_DIR_URL . 'assets/css/select2.min.css', array(), SKP_VERSION );
				wp_enqueue_style( 'select2-css' );
			}

		}

		wp_register_script( 'skyepress-script', SKP_PLUGIN_DIR_URL . 'assets/js/skyepress.js', array( 'jquery' ), SKP_VERSION );
		wp_enqueue_script( 'skyepress-script' );
        
		wp_register_style( 'skyepress-style', SKP_PLUGIN_DIR_URL . 'assets/css/skyepress.css', array(), SKP_VERSION );
		wp_enqueue_style( 'skyepress-style' );
	}
    
    /**
	 * Add the main menu page
	 *
	 */
	public function add_main_menu_page() {

		add_menu_page( 'SkyePress', 'SkyePress', 'manage_options', 'skp-skyepress', '','dashicons-share' );

	}
    
    /**
	 * Remove the main menu page as we will rely only on submenu pages
	 *
	 */
	public function remove_main_menu_page() {

		remove_submenu_page( 'skp-skyepress', 'skp-skyepress' );

	}

	/**
	 * Checks to see if the current version of the plugin matches the version
	 * saved in the database
	 *
	 * @return void 
	 *
	 */
	public function update_check() {

		$db_version = get_option( 'skp_version', '' );

		if( $db_version != SKP_VERSION ) {

			// Hook for fresh update
			do_action( 'skp_update_check', $db_version );

			// Update the version number in the db
			update_option( 'skp_version', SKP_VERSION );

			// Add first activation time
			if( get_option( 'skp_first_activation', '' ) == '' )
				update_option( 'skp_first_activation', time() );

		}

	}


	/**
	 * Creates and updates the database tables 
	 *
	 * @return void
	 *
	 */
	public function update_database_tables() {

		SKP_Database::update_tables();

	}


	/**
	 * Add default settings upon plugin activation
	 *
	 * @param string $db_version
	 *
	 */
	public function add_default_settings( $db_version = '' ) {

		if( $db_version != '' )
			return;

		$settings = get_option( 'skp_settings', '' );

		if( $settings != '' )
			return;

		// Default settings array
		$default_settings = array(
			'default_content' 			=> '{{post_title}}',
			'attachment_image_size'		=> 'full',
			'schedule_days_in_advance' 	=> 30
		);

		// Set default settings
		update_option( 'skp_settings', $default_settings );

	}


	/**
	 * Determines which version of the plugin is installed and sets a constant with
	 * the value
	 *
	 */
	private function set_version_option() {

		if( defined( 'SKP_VERSION_OPTION' ) )
			return false;

		if( file_exists( SKP_PLUGIN_DIR . '/includes/admin/promo/promo-pop-up.php' ) )
			define( 'SKP_VERSION_OPTION', 1 );

		if( file_exists( SKP_PLUGIN_DIR . '/includes/modules/calendar/calendar.php' ) && !defined( 'SKP_VERSION_OPTION' ) )
			define( 'SKP_VERSION_OPTION', 3 );

		if( file_exists( SKP_PLUGIN_DIR . '/includes/modules/media-attachment/media-attachment.php' ) && !defined( 'SKP_VERSION_OPTION' ) )
			define( 'SKP_VERSION_OPTION', 2 );

		if( !defined( 'SKP_VERSION_OPTION' ) )
			define( 'SKP_VERSION_OPTION', 1 );

		return true;

	}
    
    
    /**
	 * Plugin activation hook
	 *
	 * @return void
	 *
	 */
    public static function activation_hook() {
        
        //add cronjob for posting schedules to social platforms       
        $start_at = ceil(current_time('timestamp') / (15 * 60)) * (15 * 60); //round up the time to the nearest quarter hour
        
		if ( !wp_next_scheduled ( 'skp_cron_post_to_platforms' )){
            wp_schedule_event($start_at, 'skp_quarter_hourly', 'skp_cron_post_to_platforms');
        }
        
        //add cronjob for updating schedule posts
        if ( !wp_next_scheduled ( 'skp_cron_schedule_update_posts' )){
            wp_schedule_event(time(), 'daily', 'skp_cron_schedule_update_posts');
        }
        
        //add cronjob for updating paltform user details
        if ( !wp_next_scheduled ( 'skp_cron_update_platform_user_details' )){
            wp_schedule_event(time(), 'daily', 'skp_cron_update_platform_user_details');
        }
        
        // add cron for the serial key status
       	if( ! wp_next_scheduled( 'skp_cron_update_serial_key_status' ) && skp_module_exists( 'update-checker' ) ) {
       		wp_schedule_event( time(), 'daily', 'skp_cron_update_serial_key_status' );
       	}
        
	}
    
    /**
	 * Plugin deactivation hook
	 *
	 * @return void
	 *
	 */
    public static function deactivation_hook() {
        
        //remove cronjob for posting schedules to social platforms   
        if ( wp_next_scheduled ( 'skp_cron_post_to_platforms' )){
            wp_clear_scheduled_hook('skp_cron_post_to_platforms');
        }
        
        //remove cronjob for updating schedule posts
        if ( wp_next_scheduled ( 'skp_cron_schedule_update_posts' )){
            wp_clear_scheduled_hook('skp_cron_schedule_update_posts');
        }
        
        //remove cronjob for updating paltform user details
        if ( wp_next_scheduled ( 'skp_cron_update_platform_user_details' )){
            wp_clear_scheduled_hook('skp_cron_update_platform_user_details');
        }

        // remove cron for the serial key status
        if ( wp_next_scheduled ( 'skp_cron_update_serial_key_status' )){
            wp_clear_scheduled_hook('skp_cron_update_serial_key_status');
        }

	}

}

// Let's get the party started on a saturday night
new SkyePress;

register_activation_hook( __FILE__, array( 'SkyePress', 'activation_hook' ) );
register_deactivation_hook( __FILE__, array( 'SkyePress', 'deactivation_hook' ) );