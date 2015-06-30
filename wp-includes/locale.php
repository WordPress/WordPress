<?php
/**
 * Date and Time Locale object
 *
 * @package WordPress
 * @subpackage i18n
 */

/**
 * Class that loads the calendar locale.
 *
 * @since 2.1.0
 */
class WP_Locale {
	/**
	 * Stores the translated strings for the full weekday names.
	 *
	 * @since 2.1.0
	 * @var array
	 */
	public $weekday;

	/**
	 * Stores the translated strings for the one character weekday names.
	 *
	 * There is a hack to make sure that Tuesday and Thursday, as well
	 * as Sunday and Saturday, don't conflict. See init() method for more.
	 *
	 * @see WP_Locale::init() for how to handle the hack.
	 *
	 * @since 2.1.0
	 * @var array
	 */
	public $weekday_initial;

	/**
	 * Stores the translated strings for the abbreviated weekday names.
	 *
	 * @since 2.1.0
	 * @var array
	 */
	public $weekday_abbrev;

	/**
	 * Stores the translated strings for the full month names.
	 *
	 * @since 2.1.0
	 * @var array
	 */
	public $month;

	/**
	 * Stores the translated strings for the abbreviated month names.
	 *
	 * @since 2.1.0
	 * @var array
	 */
	public $month_abbrev;

	/**
	 * Stores the translated strings for 'am' and 'pm'.
	 *
	 * Also the capitalized versions.
	 *
	 * @since 2.1.0
	 * @var array
	 */
	public $meridiem;

	/**
	 * The text direction of the locale language.
	 *
	 * Default is left to right 'ltr'.
	 *
	 * @since 2.1.0
	 * @var string
	 */
	public $text_direction = 'ltr';

	/**
	 * @var array
	 */
	public $number_format;

	/**
	 * Sets up the translated strings and object properties.
	 *
	 * The method creates the translatable strings for various
	 * calendar elements. Which allows for specifying locale
	 * specific calendar names and text direction.
	 *
	 * @since 2.1.0
	 * @access private
	 *
	 * @global string $text_direction
	 * @global string $wp_version
	 */
	public function init() {
		// The Weekdays
		$this->weekday[0] = /* translators: weekday */ __('Sunday');
		$this->weekday[1] = /* translators: weekday */ __('Monday');
		$this->weekday[2] = /* translators: weekday */ __('Tuesday');
		$this->weekday[3] = /* translators: weekday */ __('Wednesday');
		$this->weekday[4] = /* translators: weekday */ __('Thursday');
		$this->weekday[5] = /* translators: weekday */ __('Friday');
		$this->weekday[6] = /* translators: weekday */ __('Saturday');

		// The first letter of each day. The _%day%_initial suffix is a hack to make
		// sure the day initials are unique.
		$this->weekday_initial[__('Sunday')]    = /* translators: one-letter abbreviation of the weekday */ __('S_Sunday_initial');
		$this->weekday_initial[__('Monday')]    = /* translators: one-letter abbreviation of the weekday */ __('M_Monday_initial');
		$this->weekday_initial[__('Tuesday')]   = /* translators: one-letter abbreviation of the weekday */ __('T_Tuesday_initial');
		$this->weekday_initial[__('Wednesday')] = /* translators: one-letter abbreviation of the weekday */ __('W_Wednesday_initial');
		$this->weekday_initial[__('Thursday')]  = /* translators: one-letter abbreviation of the weekday */ __('T_Thursday_initial');
		$this->weekday_initial[__('Friday')]    = /* translators: one-letter abbreviation of the weekday */ __('F_Friday_initial');
		$this->weekday_initial[__('Saturday')]  = /* translators: one-letter abbreviation of the weekday */ __('S_Saturday_initial');

		foreach ($this->weekday_initial as $weekday_ => $weekday_initial_) {
			$this->weekday_initial[$weekday_] = preg_replace('/_.+_initial$/', '', $weekday_initial_);
		}

		// Abbreviations for each day.
		$this->weekday_abbrev[__('Sunday')]    = /* translators: three-letter abbreviation of the weekday */ __('Sun');
		$this->weekday_abbrev[__('Monday')]    = /* translators: three-letter abbreviation of the weekday */ __('Mon');
		$this->weekday_abbrev[__('Tuesday')]   = /* translators: three-letter abbreviation of the weekday */ __('Tue');
		$this->weekday_abbrev[__('Wednesday')] = /* translators: three-letter abbreviation of the weekday */ __('Wed');
		$this->weekday_abbrev[__('Thursday')]  = /* translators: three-letter abbreviation of the weekday */ __('Thu');
		$this->weekday_abbrev[__('Friday')]    = /* translators: three-letter abbreviation of the weekday */ __('Fri');
		$this->weekday_abbrev[__('Saturday')]  = /* translators: three-letter abbreviation of the weekday */ __('Sat');

		// The Months
		$this->month['01'] = /* translators: month name */ __('January');
		$this->month['02'] = /* translators: month name */ __('February');
		$this->month['03'] = /* translators: month name */ __('March');
		$this->month['04'] = /* translators: month name */ __('April');
		$this->month['05'] = /* translators: month name */ __('May');
		$this->month['06'] = /* translators: month name */ __('June');
		$this->month['07'] = /* translators: month name */ __('July');
		$this->month['08'] = /* translators: month name */ __('August');
		$this->month['09'] = /* translators: month name */ __('September');
		$this->month['10'] = /* translators: month name */ __('October');
		$this->month['11'] = /* translators: month name */ __('November');
		$this->month['12'] = /* translators: month name */ __('December');

		// Abbreviations for each month. Uses the same hack as above to get around the
		// 'May' duplication.
		$this->month_abbrev[__('January')] = /* translators: three-letter abbreviation of the month */ __('Jan_January_abbreviation');
		$this->month_abbrev[__('February')] = /* translators: three-letter abbreviation of the month */ __('Feb_February_abbreviation');
		$this->month_abbrev[__('March')] = /* translators: three-letter abbreviation of the month */ __('Mar_March_abbreviation');
		$this->month_abbrev[__('April')] = /* translators: three-letter abbreviation of the month */ __('Apr_April_abbreviation');
		$this->month_abbrev[__('May')] = /* translators: three-letter abbreviation of the month */ __('May_May_abbreviation');
		$this->month_abbrev[__('June')] = /* translators: three-letter abbreviation of the month */ __('Jun_June_abbreviation');
		$this->month_abbrev[__('July')] = /* translators: three-letter abbreviation of the month */ __('Jul_July_abbreviation');
		$this->month_abbrev[__('August')] = /* translators: three-letter abbreviation of the month */ __('Aug_August_abbreviation');
		$this->month_abbrev[__('September')] = /* translators: three-letter abbreviation of the month */ __('Sep_September_abbreviation');
		$this->month_abbrev[__('October')] = /* translators: three-letter abbreviation of the month */ __('Oct_October_abbreviation');
		$this->month_abbrev[__('November')] = /* translators: three-letter abbreviation of the month */ __('Nov_November_abbreviation');
		$this->month_abbrev[__('December')] = /* translators: three-letter abbreviation of the month */ __('Dec_December_abbreviation');

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

		/* translators: $thousands_sep argument for http://php.net/number_format, default is , */
		$trans = __('number_format_thousands_sep');
		$this->number_format['thousands_sep'] = ('number_format_thousands_sep' == $trans) ? ',' : $trans;

		/* translators: $dec_point argument for http://php.net/number_format, default is . */
		$trans = __('number_format_decimal_point');
		$this->number_format['decimal_point'] = ('number_format_decimal_point' == $trans) ? '.' : $trans;

		// Set text direction.
		if ( isset( $GLOBALS['text_direction'] ) )
			$this->text_direction = $GLOBALS['text_direction'];
		/* translators: 'rtl' or 'ltr'. This sets the text direction for WordPress. */
		elseif ( 'rtl' == _x( 'ltr', 'text direction' ) )
			$this->text_direction = 'rtl';

		if ( 'rtl' === $this->text_direction && strpos( $GLOBALS['wp_version'], '-src' ) ) {
			$this->text_direction = 'ltr';
			add_action( 'all_admin_notices', array( $this, 'rtl_src_admin_notice' ) );
		}
	}

	/**
	 * @since 3.8.0
	 */
	public function rtl_src_admin_notice() {
		/* translators: %s: Name of the directory (build) */
		echo '<div class="error"><p>' . sprintf( __( 'The %s directory of the develop repository must be used for RTL.' ), '<code>build</code>' ) . '</p></div>';
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
	public function get_weekday($weekday_number) {
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
	public function get_weekday_initial($weekday_name) {
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
	public function get_weekday_abbrev($weekday_name) {
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
	public function get_month($month_number) {
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
	public function get_month_abbrev($month_name) {
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
	public function get_meridiem($meridiem) {
		return $this->meridiem[$meridiem];
	}

	/**
	 * Global variables are deprecated. For backwards compatibility only.
	 *
	 * @deprecated For backwards compatibility only.
	 * @access private
	 *
	 * @global array $weekday
	 * @global array $weekday_initial
	 * @global array $weekday_abbrev
	 * @global array $month
	 * @global array $month_abbrev
	 *
	 * @since 2.1.0
	 */
	public function register_globals() {
		$GLOBALS['weekday']         = $this->weekday;
		$GLOBALS['weekday_initial'] = $this->weekday_initial;
		$GLOBALS['weekday_abbrev']  = $this->weekday_abbrev;
		$GLOBALS['month']           = $this->month;
		$GLOBALS['month_abbrev']    = $this->month_abbrev;
	}

	/**
	 * Constructor which calls helper methods to set up object variables
	 *
	 * @since 2.1.0
	 */
	public function __construct() {
		$this->init();
		$this->register_globals();
	}

	/**
	 * Checks if current locale is RTL.
	 *
	 * @since 3.0.0
	 * @return bool Whether locale is RTL.
	 */
	public function is_rtl() {
		return 'rtl' == $this->text_direction;
	}

	/**
	 * Register date/time format strings for general POT.
	 *
	 * Private, unused method to add some date/time formats translated
	 * on wp-admin/options-general.php to the general POT that would
	 * otherwise be added to the admin POT.
	 *
	 * @since 3.6.0
	 */
	public function _strings_for_pot() {
		/* translators: localized date format, see http://php.net/date */
		__( 'F j, Y' );
		/* translators: localized time format, see http://php.net/date */
		__( 'g:i a' );
		/* translators: localized date and time format, see http://php.net/date */
		__( 'F j, Y g:i a' );
	}
}

/**
 * Checks if current locale is RTL.
 *
 * @since 3.0.0
 *
 * @global WP_Locale $wp_locale
 *
 * @return bool Whether locale is RTL.
 */
function is_rtl() {
	global $wp_locale;
	return $wp_locale->is_rtl();
}
