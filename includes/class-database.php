<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


Class SKP_Database {

	/**
	 * Plugin's table prefix
	 *
	 */
	private static $prefix = 'skp_';


	/**
	 * Updates the plugin's database tables 
	 *
	 * @return void
	 *
	 */
	public static function update_tables() {

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( self::get_schema() );

	}


	/**
	 * Returns the custom database tables schema needed for the plugin
	 *
	 * @return string
	 *
	 */
	private static function get_schema() {

		global $wpdb;

		$charset = $wpdb->get_charset_collate();
		$prefix  = self::get_prefix();

		$schema = "CREATE TABLE {$wpdb->prefix}{$prefix}platform_accounts (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) NOT NULL,
            platform_unique text NOT NULL,
			platform_slug text NOT NULL,
			platform_app_credentials text NOT NULL,
            platform_user_details longtext NOT NULL,
			UNIQUE KEY id (id)
		) {$charset};
		CREATE TABLE {$wpdb->prefix}{$prefix}posts (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			post_id bigint(20) NOT NULL,
			type text NOT NULL,
			date datetime NOT NULL,
			platform_accounts longtext NOT NULL,
			content longtext,
			attachment bigint(20),
			response longtext,
            status text,
            schedule_id bigint(20),
			UNIQUE KEY id (id)
		) {$charset};
		CREATE TABLE {$wpdb->prefix}{$prefix}schedules (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			name text NOT NULL,
			post_type text NOT NULL,
			taxonomy longtext NOT NULL,
			older_than int(9) NOT NULL,
			platform_accounts longtext NOT NULL,
            content longtext,
			day longtext NOT NULL,
			hour longtext NOT NULL,
			UNIQUE KEY id (id)
		) {$charset};";

		return $schema;

	}


	/**
	 * Returns the prefix of the database tables the plugin uses
	 *
	 * @return string
	 *
	 */
	public static function get_prefix() {

		return self::$prefix;

	}

}