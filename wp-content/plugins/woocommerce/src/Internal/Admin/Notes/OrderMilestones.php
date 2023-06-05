<?php
/**
 * WooCommerce Admin (Dashboard) Order Milestones Note Provider.
 *
 * Adds a note to the merchant's inbox when certain order milestones are reached.
 */

namespace Automattic\WooCommerce\Internal\Admin\Notes;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Admin\Notes\Notes;
/**
 * Order_Milestones
 */
class OrderMilestones {
	/**
	 * Name of the "other milestones" note.
	 */
	const NOTE_NAME = 'wc-admin-orders-milestone';

	/**
	 * Option key name to store last order milestone.
	 */
	const LAST_ORDER_MILESTONE_OPTION_KEY = 'woocommerce_admin_last_orders_milestone';

	/**
	 * Hook to process order milestones.
	 */
	const PROCESS_ORDERS_MILESTONE_HOOK = 'wc_admin_process_orders_milestone';

	/**
	 * Allowed order statuses for calculating milestones.
	 *
	 * @var array
	 */
	protected $allowed_statuses = array(
		'pending',
		'processing',
		'completed',
	);

	/**
	 * Orders count cache.
	 *
	 * @var int
	 */
	protected $orders_count = null;

	/**
	 * Further order milestone thresholds.
	 *
	 * @var array
	 */
	protected $milestones = array(
		1,
		10,
		100,
		250,
		500,
		1000,
		5000,
		10000,
		500000,
		1000000,
	);

	/**
	 * Delay hook attachment until after the WC post types have been registered.
	 *
	 * This is required for retrieving the order count.
	 */
	public function __construct() {
		/**
		 * Filter Order statuses that will count towards milestones.
		 *
		 * @since 3.5.0
		 *
		 * @param array $allowed_statuses Order statuses that will count towards milestones.
		 */
		$this->allowed_statuses = apply_filters( 'woocommerce_admin_order_milestone_statuses', $this->allowed_statuses );

		add_action( 'woocommerce_after_register_post_type', array( $this, 'init' ) );
		register_deactivation_hook( WC_PLUGIN_FILE, array( $this, 'clear_scheduled_event' ) );
	}

	/**
	 * Hook everything up.
	 */
	public function init() {
		if ( ! wp_next_scheduled( self::PROCESS_ORDERS_MILESTONE_HOOK ) ) {
			wp_schedule_event( time(), 'hourly', self::PROCESS_ORDERS_MILESTONE_HOOK );
		}

		add_action( 'wc_admin_installed', array( $this, 'backfill_last_milestone' ) );

		add_action( self::PROCESS_ORDERS_MILESTONE_HOOK, array( $this, 'possibly_add_note' ) );
	}

	/**
	 * Clear out our hourly milestone hook upon plugin deactivation.
	 */
	public function clear_scheduled_event() {
		wp_clear_scheduled_hook( self::PROCESS_ORDERS_MILESTONE_HOOK );
	}

	/**
	 * Get the total count of orders (in the allowed statuses).
	 *
	 * @param bool $no_cache Optional. Skip cache.
	 * @return int Total orders count.
	 */
	public function get_orders_count( $no_cache = false ) {
		if ( $no_cache || is_null( $this->orders_count ) ) {
			$status_counts      = array_map( 'wc_orders_count', $this->allowed_statuses );
			$this->orders_count = array_sum( $status_counts );
		}

		return $this->orders_count;
	}

	/**
	 * Backfill the store's current milestone.
	 *
	 * Used to avoid celebrating milestones that were reached before plugin activation.
	 */
	public function backfill_last_milestone() {
		// If the milestone notes have been disabled via filter, bail.
		if ( ! $this->are_milestones_enabled() ) {
			return;
		}

		$this->set_last_milestone( $this->get_current_milestone() );
	}

	/**
	 * Get the store's last milestone.
	 *
	 * @return int Last milestone reached.
	 */
	public function get_last_milestone() {
		return get_option( self::LAST_ORDER_MILESTONE_OPTION_KEY, 0 );
	}

	/**
	 * Update the last reached milestone.
	 *
	 * @param int $milestone Last milestone reached.
	 */
	public function set_last_milestone( $milestone ) {
		update_option( self::LAST_ORDER_MILESTONE_OPTION_KEY, $milestone );
	}

	/**
	 * Calculate the current orders milestone.
	 *
	 * Based on the threshold values in $this->milestones.
	 *
	 * @return int Current orders milestone.
	 */
	public function get_current_milestone() {
		$milestone_reached = 0;
		$orders_count      = $this->get_orders_count();

		foreach ( $this->milestones as $milestone ) {
			if ( $milestone <= $orders_count ) {
				$milestone_reached = $milestone;
			}
		}

		return $milestone_reached;
	}

	/**
	 * Get the appropriate note title for a given milestone.
	 *
	 * @param int $milestone Order milestone.
	 * @return string Note title for the milestone.
	 */
	public static function get_note_title_for_milestone( $milestone ) {
		switch ( $milestone ) {
			case 1:
				return __( 'First order received', 'woocommerce' );
			case 10:
			case 100:
			case 250:
			case 500:
			case 1000:
			case 5000:
			case 10000:
			case 500000:
			case 1000000:
				return sprintf(
					/* translators: Number of orders processed. */
					__( 'Congratulations on processing %s orders!', 'woocommerce' ),
					wc_format_decimal( $milestone )
				);
			default:
				return '';
		}
	}

	/**
	 * Get the appropriate note content for a given milestone.
	 *
	 * @param int $milestone Order milestone.
	 * @return string Note content for the milestone.
	 */
	public static function get_note_content_for_milestone( $milestone ) {
		switch ( $milestone ) {
			case 1:
				return __( 'Congratulations on getting your first order! Now is a great time to learn how to manage your orders.', 'woocommerce' );
			case 10:
				return __( "You've hit the 10 orders milestone! Look at you go. Browse some WooCommerce success stories for inspiration.", 'woocommerce' );
			case 100:
			case 250:
			case 500:
			case 1000:
			case 5000:
			case 10000:
			case 500000:
			case 1000000:
				return __( 'Another order milestone! Take a look at your Orders Report to review your orders to date.', 'woocommerce' );
			default:
				return '';
		}
	}

	/**
	 * Get the appropriate note action for a given milestone.
	 *
	 * @param int $milestone Order milestone.
	 * @return array Note actoion (name, label, query) for the milestone.
	 */
	public static function get_note_action_for_milestone( $milestone ) {
		switch ( $milestone ) {
			case 1:
				return array(
					'name'  => 'learn-more',
					'label' => __( 'Learn more', 'woocommerce' ),
					'query' => 'https://woocommerce.com/document/managing-orders/?utm_source=inbox&utm_medium=product',
				);
			case 10:
				return array(
					'name'  => 'browse',
					'label' => __( 'Browse', 'woocommerce' ),
					'query' => 'https://woocommerce.com/success-stories/?utm_source=inbox&utm_medium=product',
				);
			case 100:
			case 250:
			case 500:
			case 1000:
			case 5000:
			case 10000:
			case 500000:
			case 1000000:
				return array(
					'name'  => 'review-orders',
					'label' => __( 'Review your orders', 'woocommerce' ),
					'query' => '?page=wc-admin&path=/analytics/orders',
				);
			default:
				return array(
					'name'  => '',
					'label' => '',
					'query' => '',
				);
		}
	}

	/**
	 * Convenience method to see if the milestone notes are enabled.
	 *
	 * @return boolean True if milestone notifications are enabled.
	 */
	public function are_milestones_enabled() {
		/**
		 * Filter to allow for disabling order milestones.
		 *
		 * @since 3.7.0
		 *
		 * @param boolean default true
		 */
		$milestone_notes_enabled = apply_filters( 'woocommerce_admin_order_milestones_enabled', true );

		return $milestone_notes_enabled;
	}

	/**
	 * Get the note. This is used for localizing the note.
	 *
	 * @return Note
	 */
	public static function get_note() {
		$note = Notes::get_note_by_name( self::NOTE_NAME );
		if ( ! $note ) {
			return false;
		}
		$content_data = $note->get_content_data();
		if ( ! isset( $content_data->current_milestone ) ) {
			return false;
		}
		return self::get_note_by_milestone(
			$content_data->current_milestone
		);
	}

	/**
	 * Get the note by milestones.
	 *
	 * @param int $current_milestone Current milestone.
	 *
	 * @return Note
	 */
	public static function get_note_by_milestone( $current_milestone ) {
		$content_data = (object) array(
			'current_milestone' => $current_milestone,
		);

		$note = new Note();
		$note->set_title( self::get_note_title_for_milestone( $current_milestone ) );
		$note->set_content( self::get_note_content_for_milestone( $current_milestone ) );
		$note->set_content_data( $content_data );
		$note->set_type( Note::E_WC_ADMIN_NOTE_INFORMATIONAL );
		$note->set_name( self::NOTE_NAME );
		$note->set_source( 'woocommerce-admin' );
		$note_action = self::get_note_action_for_milestone( $current_milestone );
		$note->add_action( $note_action['name'], $note_action['label'], $note_action['query'] );
		return $note;
	}

	/**
	 * Checks if a note can and should be added.
	 *
	 * @return bool
	 */
	public function can_be_added() {
		// If the milestone notes have been disabled via filter, bail.
		if ( ! $this->are_milestones_enabled() ) {
			return false;
		}

		$last_milestone    = $this->get_last_milestone();
		$current_milestone = $this->get_current_milestone();

		if ( $current_milestone <= $last_milestone ) {
			return false;
		}

		return true;
	}

	/**
	 * Add milestone notes for other significant thresholds.
	 */
	public function possibly_add_note() {
		if ( ! self::can_be_added() ) {
			return;
		}
		$current_milestone = $this->get_current_milestone();
		$this->set_last_milestone( $current_milestone );

		// We only want one milestone note at any time.
		Notes::delete_notes_with_name( self::NOTE_NAME );
		$note = $this->get_note_by_milestone( $current_milestone );
		$note->save();
	}
}
