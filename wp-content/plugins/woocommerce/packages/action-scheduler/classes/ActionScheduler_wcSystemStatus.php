<?php

/**
 * Class ActionScheduler_wcSystemStatus
 */
class ActionScheduler_wcSystemStatus {

	/**
	 * The active data stores
	 *
	 * @var ActionScheduler_Store
	 */
	protected $store;

	/**
	 * Constructor method for ActionScheduler_wcSystemStatus.
	 *
	 * @param ActionScheduler_Store $store Active store object.
	 *
	 * @return void
	 */
	public function __construct( $store ) {
		$this->store = $store;
	}

	/**
	 * Display action data, including number of actions grouped by status and the oldest & newest action in each status.
	 *
	 * Helpful to identify issues, like a clogged queue.
	 */
	public function render() {
		$action_counts     = $this->store->action_counts();
		$status_labels     = $this->store->get_status_labels();
		$oldest_and_newest = $this->get_oldest_and_newest( array_keys( $status_labels ) );

		$this->get_template( $status_labels, $action_counts, $oldest_and_newest );
	}

	/**
	 * Get oldest and newest scheduled dates for a given set of statuses.
	 *
	 * @param array $status_keys Set of statuses to find oldest & newest action for.
	 * @return array
	 */
	protected function get_oldest_and_newest( $status_keys ) {

		$oldest_and_newest = array();

		foreach ( $status_keys as $status ) {
			$oldest_and_newest[ $status ] = array(
				'oldest' => '&ndash;',
				'newest' => '&ndash;',
			);

			if ( 'in-progress' === $status ) {
				continue;
			}

			$oldest_and_newest[ $status ]['oldest'] = $this->get_action_status_date( $status, 'oldest' );
			$oldest_and_newest[ $status ]['newest'] = $this->get_action_status_date( $status, 'newest' );
		}

		return $oldest_and_newest;
	}

	/**
	 * Get oldest or newest scheduled date for a given status.
	 *
	 * @param string $status Action status label/name string.
	 * @param string $date_type Oldest or Newest.
	 * @return DateTime
	 */
	protected function get_action_status_date( $status, $date_type = 'oldest' ) {

		$order = 'oldest' === $date_type ? 'ASC' : 'DESC';

		$action = $this->store->query_actions(
			array(
				'claimed'  => false,
				'status'   => $status,
				'per_page' => 1,
				'order'    => $order,
			)
		);

		if ( ! empty( $action ) ) {
			$date_object = $this->store->get_date( $action[0] );
			$action_date = $date_object->format( 'Y-m-d H:i:s O' );
		} else {
			$action_date = '&ndash;';
		}

		return $action_date;
	}

	/**
	 * Get oldest or newest scheduled date for a given status.
	 *
	 * @param array $status_labels Set of statuses to find oldest & newest action for.
	 * @param array $action_counts Number of actions grouped by status.
	 * @param array $oldest_and_newest Date of the oldest and newest action with each status.
	 */
	protected function get_template( $status_labels, $action_counts, $oldest_and_newest ) {
		$as_version   = ActionScheduler_Versions::instance()->latest_version();
		$as_datastore = get_class( ActionScheduler_Store::instance() );
		?>

		<table class="wc_status_table widefat" cellspacing="0">
			<thead>
				<tr>
					<th colspan="5" data-export-label="Action Scheduler"><h2><?php esc_html_e( 'Action Scheduler', 'woocommerce' ); ?><?php echo wc_help_tip( esc_html__( 'This section shows details of Action Scheduler.', 'woocommerce' ) ); ?></h2></th>
				</tr>
				<tr>
					<td colspan="2" data-export-label="Version"><?php esc_html_e( 'Version:', 'woocommerce' ); ?></td>
					<td colspan="3"><?php echo esc_html( $as_version ); ?></td>
				</tr>
				<tr>
					<td colspan="2" data-export-label="Data store"><?php esc_html_e( 'Data store:', 'woocommerce' ); ?></td>
					<td colspan="3"><?php echo esc_html( $as_datastore ); ?></td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e( 'Action Status', 'woocommerce' ); ?></strong></td>
					<td class="help">&nbsp;</td>
					<td><strong><?php esc_html_e( 'Count', 'woocommerce' ); ?></strong></td>
					<td><strong><?php esc_html_e( 'Oldest Scheduled Date', 'woocommerce' ); ?></strong></td>
					<td><strong><?php esc_html_e( 'Newest Scheduled Date', 'woocommerce' ); ?></strong></td>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ( $action_counts as $status => $count ) {
					// WC uses the 3rd column for export, so we need to display more data in that (hidden when viewed as part of the table) and add an empty 2nd column.
					printf(
						'<tr><td>%1$s</td><td>&nbsp;</td><td>%2$s<span style="display: none;">, Oldest: %3$s, Newest: %4$s</span></td><td>%3$s</td><td>%4$s</td></tr>',
						esc_html( $status_labels[ $status ] ),
						esc_html( number_format_i18n( $count ) ),
						esc_html( $oldest_and_newest[ $status ]['oldest'] ),
						esc_html( $oldest_and_newest[ $status ]['newest'] )
					);
				}
				?>
			</tbody>
		</table>

		<?php
	}

	/**
	 * Is triggered when invoking inaccessible methods in an object context.
	 *
	 * @param string $name Name of method called.
	 * @param array  $arguments Parameters to invoke the method with.
	 *
	 * @return mixed
	 * @link https://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.methods
	 */
	public function __call( $name, $arguments ) {
		switch ( $name ) {
			case 'print':
				_deprecated_function( __CLASS__ . '::print()', '2.2.4', __CLASS__ . '::render()' );
				return call_user_func_array( array( $this, 'render' ), $arguments );
		}

		return null;
	}
}
