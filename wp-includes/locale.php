<?php

// Date and Time

class WP_Locale {
	var $weekday;
	var $weekday_initial;
	var $weekday_abbrev;

	var $month;
	var $month_abbrev;

	var $meridiem;

	var $text_direction = 'ltr';
	var $locale_vars = array('text_direction');

	function init() {
		// The Weekdays
		$this->weekday[0] = __('Sunday');
		$this->weekday[1] = __('Monday');
		$this->weekday[2] = __('Tuesday');
		$this->weekday[3] = __('Wednesday');
		$this->weekday[4] = __('Thursday');
		$this->weekday[5] = __('Friday');
		$this->weekday[6] = __('Saturday');

		// The first letter of each day.  The _%day%_initial suffix is a hack to make
		// sure the day initials are unique.
		$this->weekday_initial[__('Sunday')]    = __('S_Sunday_initial');
		$this->weekday_initial[__('Monday')]    = __('M_Monday_initial');
		$this->weekday_initial[__('Tuesday')]   = __('T_Tuesday_initial');
		$this->weekday_initial[__('Wednesday')] = __('W_Wednesday_initial');
		$this->weekday_initial[__('Thursday')]  = __('T_Thursday_initial');
		$this->weekday_initial[__('Friday')]    = __('F_Friday_initial');
		$this->weekday_initial[__('Saturday')]  = __('S_Saturday_initial');

		foreach ($this->weekday_initial as $weekday_ => $weekday_initial_) {
			$this->weekday_initial[$weekday_] = preg_replace('/_.+_initial$/', '', $weekday_initial_);
		}

		// Abbreviations for each day.
		$this->weekday_abbrev[__('Sunday')]    = __('Sun');
		$this->weekday_abbrev[__('Monday')]    = __('Mon');
		$this->weekday_abbrev[__('Tuesday')]   = __('Tue');
		$this->weekday_abbrev[__('Wednesday')] = __('Wed');
		$this->weekday_abbrev[__('Thursday')]  = __('Thu');
		$this->weekday_abbrev[__('Friday')]    = __('Fri');
		$this->weekday_abbrev[__('Saturday')]  = __('Sat');

		// The Months
		$this->month['01'] = __('January');
		$this->month['02'] = __('February');
		$this->month['03'] = __('March');
		$this->month['04'] = __('April');
		$this->month['05'] = __('May');
		$this->month['06'] = __('June');
		$this->month['07'] = __('July');
		$this->month['08'] = __('August');
		$this->month['09'] = __('September');
		$this->month['10'] = __('October');
		$this->month['11'] = __('November');
		$this->month['12'] = __('December');

		// Abbreviations for each month. Uses the same hack as above to get around the
		// 'May' duplication.
		$this->month_abbrev[__('January')] = __('Jan_January_abbreviation');
		$this->month_abbrev[__('February')] = __('Feb_February_abbreviation');
		$this->month_abbrev[__('March')] = __('Mar_March_abbreviation');
		$this->month_abbrev[__('April')] = __('Apr_April_abbreviation');
		$this->month_abbrev[__('May')] = __('May_May_abbreviation');
		$this->month_abbrev[__('June')] = __('Jun_June_abbreviation');
		$this->month_abbrev[__('July')] = __('Jul_July_abbreviation');
		$this->month_abbrev[__('August')] = __('Aug_August_abbreviation');
		$this->month_abbrev[__('September')] = __('Sep_September_abbreviation');
		$this->month_abbrev[__('October')] = __('Oct_October_abbreviation');
		$this->month_abbrev[__('November')] = __('Nov_November_abbreviation');
		$this->month_abbrev[__('December')] = __('Dec_December_abbreviation');

		foreach ($this->month_abbrev as $month_ => $month_abbrev_) {
			$this->month_abbrev[$month_] = preg_replace('/_.+_abbreviation$/', '', $month_abbrev_);
		}

		// The Meridiems
		$this->meridiem['am'] = __('am');
		$this->meridiem['pm'] = __('pm');
		$this->meridiem['AM'] = __('AM');
		$this->meridiem['PM'] = __('PM');

		// Import global locale vars set during inclusion of $locale.php.
		foreach ( $this->locale_vars as $var ) {
			if ( isset($GLOBALS[$var]) )
				$this->$var = $GLOBALS[$var];
		}

	}

	function get_weekday($weekday_number) {
		return $this->weekday[$weekday_number];
	}

	function get_weekday_initial($weekday_name) {
		return $this->weekday_initial[$weekday_name];
	}

	function get_weekday_abbrev($weekday_name) {
		return $this->weekday_abbrev[$weekday_name];
	}

	function get_month($month_number) {
		return $this->month[zeroise($month_number, 2)];
	}

	function get_month_initial($month_name) {
		return $this->month_initial[$month_name];
	}

	function get_month_abbrev($month_name) {
		return $this->month_abbrev[$month_name];
	}

	function get_meridiem($meridiem) {
		return $this->meridiem[$meridiem];
	}

	// Global variables are deprecated. For backwards compatibility only.
	function register_globals() {
		$GLOBALS['weekday']         = $this->weekday;
		$GLOBALS['weekday_initial'] = $this->weekday_initial;
		$GLOBALS['weekday_abbrev']  = $this->weekday_abbrev;
		$GLOBALS['month']           = $this->month;
		$GLOBALS['month_abbrev']    = $this->month_abbrev;
	}

	function WP_Locale() {
		$this->init();
		$this->register_globals();
	}
}

?>