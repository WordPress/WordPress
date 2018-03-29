<?php

class UM_DateTime {

	function __construct() {
	
	}
	
	/***
	***	@Display time in specific format
	***/
	function get_time( $format ) {
		return current_time( $format );
	}
	
	/***
	***	@Show a cool time difference between 2 timestamps
	***/
	function time_diff( $from, $to = '' ) {
		if ( empty( $to ) ) {
			$to = time();
		}
		$diff = (int) abs( $to - $from );
		if ( $diff < 60 ) {
			
			$since = __('just now','ultimate-member');
			
		} elseif ( $diff < HOUR_IN_SECONDS ) {
			
			$mins = round( $diff / MINUTE_IN_SECONDS );
			if ( $mins <= 1 )
				$mins = 1;
			if ( $mins == 1 ) {
				$since = sprintf( __('%s min','ultimate-member'), $mins );
			} else {
				$since = sprintf( __('%s mins','ultimate-member'), $mins );
			}
			
		} elseif ( $diff < DAY_IN_SECONDS && $diff >= HOUR_IN_SECONDS ) {
			
			$hours = round( $diff / HOUR_IN_SECONDS );
			if ( $hours <= 1 )
				$hours = 1;
			if ( $hours == 1 ) {
				$since = sprintf( __('%s hr','ultimate-member'), $hours );
			} else {
				$since = sprintf( __('%s hrs','ultimate-member'), $hours );
			}
			
		} elseif ( $diff < WEEK_IN_SECONDS && $diff >= DAY_IN_SECONDS ) {
			
			$days = round( $diff / DAY_IN_SECONDS );
			if ( $days <= 1 )
				$days = 1;
			if ( $days == 1 ) {
				$since = sprintf( __('Yesterday at %s','ultimate-member'), date('g:ia', $from ) );
			} else {
				$since = sprintf(__('%s at %s','ultimate-member'), date('F d', $from ), date('g:ia', $from ) ); 
			}
			
		} elseif ( $diff < 30 * DAY_IN_SECONDS && $diff >= WEEK_IN_SECONDS ) {
			
			$since = sprintf(__('%s at %s','ultimate-member'), date('F d', $from ), date('g:ia', $from ) ); 
			
		} elseif ( $diff < YEAR_IN_SECONDS && $diff >= 30 * DAY_IN_SECONDS ) {

			$since = sprintf(__('%s at %s','ultimate-member'), date('F d', $from ), date('g:ia', $from ) );
			
		} elseif ( $diff >= YEAR_IN_SECONDS ) {
			
			$since = sprintf(__('%s at %s','ultimate-member'), date( 'F d, Y', $from ), date('g:ia', $from ) );
			
		}

		return apply_filters( 'um_human_time_diff', $since, $diff, $from, $to );
	}
	
	/***
	***	@Get age
	***/
	function get_age($then) {
		if ( !$then ) return '';
		$then_ts = strtotime($then);
		$then_year = date('Y', $then_ts);
		$age = date('Y') - $then_year;
		if( strtotime('+' . $age . ' years', $then_ts) > current_time( 'timestamp' ) ) $age--;
		if ( $age == 1 )
			return sprintf(__('%s year old','ultimate-member'), $age );
		if ( $age > 1 )
			return sprintf(__('%s years old','ultimate-member'), $age );
		if ( $age == 0 )
			return __('Less than 1 year old','ultimate-member');
	}
	
	/***
	***	@Reformat dates
	***/
	function format($old, $new){
		$datetime = new DateTime($old);
		$output = $datetime->format( $new );
		return $output;
	}
	
	/***
	***	@Get last 30 days as array
	***/
	function get_last_days($num = 30, $reverse = true) {
		$d = array();
		for($i = 0; $i < $num; $i++) {
			$d[ date('Y-m-d', strtotime('-'. $i .' days')) ] = date('m/d', strtotime('-'. $i .' days'));
		}
		if ($reverse == true){
			return array_reverse($d);
		} else {
			return $d;
		}
	}

}