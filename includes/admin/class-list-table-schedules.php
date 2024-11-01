<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

Class SKP_List_Table_Schedules extends WP_List_Table {

	/**
	 * The data of the table
	 *
	 * @var array
	 *
	 */
	public $data = array();


	/**
	 * Constructor
	 *
	 */
	public function __construct() {

		parent::__construct( array(
			'plural' 	=> 'skp_schedules',
			'singular' 	=> 'skp_schedule',
			'ajax' 		=> false
		));

		// Get and set table data
		$this->set_table_data();
		
		// Add column headers and table items
		$this->_column_headers = array( $this->get_columns(), array(), array() );
		$this->items 		   = $this->data;

	}


	/**
	 * Returns all the columns for the table
	 *
	 */
	public function get_columns() {

		$columns = array(
			'name' 		=> __( 'Name', 'skp-textdomain' ),
			'schedule'	=> __( 'Schedule', 'skp-textdomain' ),
			'accounts'	=> __( 'Accounts', 'skp-textdomain' )
		);

		return $columns;

	}


	/**
	 * Gets the schedules data and sets it
	 *
	 */
	private function set_table_data() {

		$schedules = skp_get_schedules();
		
		if( !empty( $schedules ) ) {

			foreach( $schedules as $schedule ) {

				$this->data[] = array(
					'id'		=> $schedule->id,
					'name' 		=> $schedule->name,
					'schedule'	=> array(
						'nice_weekdays' => skp_nice_weekdays( $schedule->day ),
						'nice_hours'	=> skp_nice_hours($schedule->hour)
					),
					'platform_accounts' => $schedule->platform_accounts
				);

			}

		}
		
	}


	/**
	 * Returns the actions a user can make on a row
	 *
	 */
	private function get_row_actions( $item ) {

		$actions = array();

		// Edit schedule action
		$actions['edit'] = '<a href="' . add_query_arg( array( 'page' => 'skp-revive-posts', 'subpage' => 'edit-schedule', 'skp_schedule_id' => $item['id'] ) , admin_url('admin.php') ) . '">' . __( 'Edit Schedule', 'skp-textdomain' ) . '</a>';

		// Delete schedule action
		$actions['delete'] = '<a onclick="return confirm(\'Are you sure you want to remove this schedule?\' );" href="' . wp_nonce_url( add_query_arg( array( 'page' => 'skp-revive-posts', 'skp_schedule_id' => $item['id'] ) , admin_url('admin.php') ), 'skp_remove_schedule', 'skp_tkn' ) . '">' . __( 'Delete', 'skp-textdomain' ) . '</a>';

		return $actions;

	}


	/**
	 * Returns the HTML that will be displayed in each columns
	 *
	 * @param array $item 			- data for the current row
	 * @param string $column_name 	- name of the current column
	 *
	 * @return string
	 *
	 */
	public function column_default( $item, $column_name ) {

		return !empty( $item[ $column_name ] ) ? $item[ $column_name ] : '-';

	}


	/**
	 * Returns the HTML that will be displayed in the "name" column
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_name( $item ) {

		$output = '<strong>' . ( !empty( $item['name'] ) ? $item['name'] : '' ) . '</strong>';

		return $output . $this->row_actions( $this->get_row_actions( $item ) );

	}


	/**
	 * Returns the HTML that will be displayed in the "schedule" column
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_schedule( $item ) {

		if( empty( $item['schedule']['nice_weekdays'] ) || empty( $item['schedule']['nice_hours'] ) )
			return '-';

		return sprintf( __( '<p>Runs every <strong>%1$s</strong> at <strong>%2$s</strong></p>', 'skp-textdomain' ), $item['schedule']['nice_weekdays'], $item['schedule']['nice_hours'] );

	}


	/**
	 * Returns the HTML that will be displayed in the "accounts" column
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_accounts( $item ) {

		if( empty( $item['platform_accounts'] ) )
			return '-';

		$output = '';

		foreach( $item['platform_accounts'] as $platform_unique ) {

			$account = skp_get_platform_account_by_platform_unique( $platform_unique );
			$output .= skp_get_partial( 'platform-account', array( 'account' => $account, 'has-name' => false, 'has-actions' => false ), false );

		}

		return $output;

	}


	/**
	 * HTML display when there are no items in the table
	 *
	 */
	public function no_items() {

		echo '<div class="skp-list-table-no-items">';
			echo '<p>' . __( 'Oops... it seems there are no schedules yet', 'skp-textdomain' ) . '</p>';
			echo '<a class="button-primary" href="' . add_query_arg( array( 'page' => 'skp-revive-posts', 'subpage' => 'add-schedule' ), admin_url('admin.php') ) . '">' . __( 'Set up Your First Schedule', 'skp-textdomain' ) . '</a>';
		echo '</div>';

	}

}