<?php

class UM_Cron {

	public function __construct() {
		add_filter( 'cron_schedules', array( $this, 'add_schedules'   ) );
		add_action( 'wp',             array( $this, 'schedule_Events' ) );
	}

	public function add_schedules( $schedules = array() ) {

		// Adds once weekly to the existing schedules.
		$schedules['weekly'] = array(
			'interval' => 604800,
			'display'  => __( 'Once Weekly', 'ultimate-member')
		);

		return $schedules;
	}

	public function schedule_Events() {
		$this->weekly_events();
		$this->daily_events();
		$this->twicedaily_events();
		$this->hourly_events();
	}

	private function weekly_events() {
		if ( ! wp_next_scheduled( 'um_weekly_scheduled_events' ) ) {
			wp_schedule_event( current_time( 'timestamp' ), 'weekly', 'um_weekly_scheduled_events' );
		}
	}

	private function daily_events() {
		if ( ! wp_next_scheduled( 'um_daily_scheduled_events' ) ) {
			wp_schedule_event( current_time( 'timestamp' ), 'daily', 'um_daily_scheduled_events' );
		}
	}
	
	private function twicedaily_events() {
		if ( ! wp_next_scheduled( 'um_twicedaily_scheduled_events' ) ) {
			wp_schedule_event( current_time( 'timestamp' ), 'twicedaily', 'um_twicedaily_scheduled_events' );
		}
	}
	
	private function hourly_events() {
		if ( ! wp_next_scheduled( 'um_hourly_scheduled_events' ) ) {
			wp_schedule_event( current_time( 'timestamp' ), 'hourly', 'um_hourly_scheduled_events' );
		}
	}

}