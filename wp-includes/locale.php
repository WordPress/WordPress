<?php
/**
 * Date and Time Locale object
 *
 * @package WordPress
 * @subpackage i18n
 */

/**
 * {@internal Missing Short Description}}
 *
 * {@internal Missing Long Description}}
 *
 * @since 2.1.0
 */
class WP_Locale {
	/**
	 * Stores the translated strings for the full weekday names.
	 *
	 * @since 2.1.0
	 * @var array
	 * @access private
	 */
	var $weekday;

	/**
	 * Stores the translated strings for the one character weekday names.
	 *
	 * There is a hack to make sure that Tuesday and Thursday, as well
	 * as Sunday and Saturday don't conflict. See init() method for more.
	 *
	 * @see WP_Locale::init() for how to handle the hack.
	 *
	 * @since 2.1.0
	 * @var array
	 * @access private
	 */
	var $weekday_initial;

	/**
	 * Stores the translated strings for the abbreviated weekday names.
	 *
	 * @since 2.1.0
	 * @var array
	 * @access private
	 */
	var $weekday_abbrev;

	/**
	 * Stores the translated strings for the full month names.
	 *
	 * @since 2.1.0
	 * @var array
	 * @access private
	 */
	var $month;

	/**
	 * Stores the translated strings for the abbreviated month names.
	 *
	 * @since 2.1.0
	 * @var array
	 * @access private
	 */
	var $month_abbrev;

	/**
	 * Stores the translated strings for 'am' and 'pm'.
	 *
	 * Also the capalized versions.
	 *
	 * @since 2.1.0
	 * @var array
	 * @access private
	 */
	var $meridiem;

	/**
	 * The text direction of the locale language.
	 *
	 * Default is left to right 'ltr'.
	 *
	 * @since 2.1.0
	 * @var string
	 * @access private
	 */
	var $text_direction = 'ltr';

	/**
	 * Imports the global version to the class property.
	 *
	 * @since 2.1.0
	 * @var array
	 * @access private
	 */
	var $locale_vars = array('text_direction');

	/**
	 * Sets up the translated strings and object properties.
	 *
	 * The method creates the translatable strings for various
	 * calendar elements. Which allows for specifying locale
	 * specific calendar names and text direction.
	 *
	 * @since 2.1.0
	 * @access private
	 */
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

		// Numbers formatting
		// See http://php.net/number_format

		$trans = _c('number_format_decimals|$decimals argument for http://php.net/number_format, default is 0');
		$this->number_format['decimals'] = ('number_format_decimals' == $trans) ? 0 : $trans;

		$trans = _c('number_format_decimal_point|$dec_point argument for http://php.net/number_format, default is .');
		$this->number_format['decimal_point'] = ('number_format_decimal_point' == $trans) ? '.' : $trans;

		$trans = _c('number_format_thousands_sep|$thousands_sep argument for http://php.net/number_format, default is ,');
		$this->number_format['thousands_sep'] = ('number_format_thousands_sep' == $trans) ? ',' : $trans;

		// Import global locale vars set during inclusion of $locale.php.
		foreach ( $this->locale_vars as $var ) {
			if ( isset($GLOBALS[$var]) )
				$this->$var = $GLOBALS[$var];
		}

	}

	/**
	 * Retrieve the full translated weekday word.
	 *
	 * Week starts on translated Sunday and can be fetched
	 * by using 0 (zero). So the week starts with 0 (zero)
	 * and ends on Saturday with is fetched by using 6 (six).
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @param int $weekday_number 0 for Sunday through 6 Saturday
	 * @return string Full translated weekday
	 */
	function get_weekday($weekday_number) {
		return $this->weekday[$weekday_number];
	}

	/**
	 * Retrieve the translated weekday initial.
	 *
	 * The weekday initial is retrieved by the translated
	 * full weekday word. When translating the weekday initial
	 * pay attention to make sure that the starting letter does
	 * not conflict.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @param string $weekday_name
	 * @return string
	 */
	function get_weekday_initial($weekday_name) {
		return $this->weekday_initial[$weekday_name];
	}

	/**
	 * Retrieve the translated weekday abbreviation.
	 *
	 * The weekday abbreviation is retrieved by the translated
	 * full weekday word.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @param string $weekday_name Full translated weekday word
	 * @return string Translated weekday abbreviation
	 */
	function get_weekday_abbrev($weekday_name) {
		return $this->weekday_abbrev[$weekday_name];
	}

	/**
	 * Retrieve the full translated month by month number.
	 *
	 * The $month_number parameter has to be a string
	 * because it must have the '0' in front of any number
	 * that is less than 10. Starts from '01' and ends at
	 * '12'.
	 *
	 * You can use an integer instead and it will add the
	 * '0' before the numbers less than 10 for you.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @param string|int $month_number '01' through '12'
	 * @return string Translated full month name
	 */
	function get_month($month_number) {
		return $this->month[zeroise($month_number, 2)];
	}

	/**
	 * Retrieve translated version of month abbreviation string.
	 *
	 * The $month_name parameter is expected to be the translated or
	 * translatable version of the month.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @param string $month_name Translated month to get abbreviated version
	 * @return string Translated abbreviated month
	 */
	function get_month_abbrev($month_name) {
		return $this->month_abbrev[$month_name];
	}

	/**
	 * Retrieve translated version of meridiem string.
	 *
	 * The $meridiem parameter is expected to not be translated.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @param string $meridiem Either 'am', 'pm', 'AM', or 'PM'. Not translated version.
	 * @return string Translated version
	 */
	function get_meridiem($meridiem) {
		return $this->meridiem[$meridiem];
	}

	/**
	 * Global variables are deprecated. For backwards compatibility only.
	 *
	 * @deprecated For backwards compatibility only.
	 * @access private
	 *
	 * @since 2.1.0
	 */
	function register_globals() {
		$GLOBALS['weekday']         = $this->weekday;
		$GLOBALS['weekday_initial'] = $this->weekday_initial;
		$GLOBALS['weekday_abbrev']  = $this->weekday_abbrev;
		$GLOBALS['month']           = $this->month;
		$GLOBALS['month_abbrev']    = $this->month_abbrev;
	}

	/**
	 * PHP4 style constructor which calls helper methods to set up object variables
	 *
	 * @uses WP_Locale::init()
	 * @uses WP_Locale::register_globals()
	 * @since 2.1.0
	 *
	 * @return WP_Locale
	 */
	function WP_Locale() {
		$this->init();
		$this->register_globals();
	}
}

?>
