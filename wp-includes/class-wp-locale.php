<?php
/**
 * Locale API: WP_Locale class
 *
 * @package WordPress
 * @subpackage i18n
 * @since 4.6.0
 */

/**
 * Core class used to store translated data for a locale.
 *
 * @since 2.1.0
 * @since 4.6.0 Moved to its own file from wp-includes/locale.php.
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
	 * Stores the translated strings for the month names in genitive case, if the locale specifies.
	 *
	 * @since 4.4.0
	 * @var array
	 */
	public $month_genitive;

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
	 * The thousands separator and decimal point values used for localizing numbers.
	 *
	 * @since 2.3.0
	 * @var array
	 */
	public $number_format;

	/**
	 * Constructor which calls helper methods to set up object variables.
	 *
	 * @since 2.1.0
	 */
	public function __construct() {
		$this->init();
		$this->register_globals();
	}

	/**
	 * Sets up the translated strings and object properties.
	 *
	 * The method creates the translatable strings for various
	 * calendar elements. Which allows for specifying locale
	 * specific calendar names and text direction.
	 *
	 * @since 2.1.0
	 *
	 * @global string $text_direction
	 * @global string $wp_version
	 */
	public function init() {
		// The Weekdays
		$this->weekday[0] = /* translators: weekday */ __( 'Sunday' );
		$this->weekday[1] = /* translators: weekday */ __( 'Monday' );
		$this->weekday[2] = /* translators: weekday */ __( 'Tuesday' );
		$this->weekday[3] = /* translators: weekday */ __( 'Wednesday' );
		$this->weekday[4] = /* translators: weekday */ __( 'Thursday' );
		$this->weekday[5] = /* translators: weekday */ __( 'Friday' );
		$this->weekday[6] = /* translators: weekday */ __( 'Saturday' );

		// The first letter of each day.
		$this->weekday_initial[ __( 'Sunday' ) ]    = /* translators: one-letter abbreviation of the weekday */ _x( 'S', 'Sunday initial' );
		$this->weekday_initial[ __( 'Monday' ) ]    = /* translators: one-letter abbreviation of the weekday */ _x( 'M', 'Monday initial' );
		$this->weekday_initial[ __( 'Tuesday' ) ]   = /* translators: one-letter abbreviation of the weekday */ _x( 'T', 'Tuesday initial' );
		$this->weekday_initial[ __( 'Wednesday' ) ] = /* translators: one-letter abbreviation of the weekday */ _x( 'W', 'Wednesday initial' );
		$this->weekday_initial[ __( 'Thursday' ) ]  = /* translators: one-letter abbreviation of the weekday */ _x( 'T', 'Thursday initial' );
		$this->weekday_initial[ __( 'Friday' ) ]    = /* translators: one-letter abbreviation of the weekday */ _x( 'F', 'Friday initial' );
		$this->weekday_initial[ __( 'Saturday' ) ]  = /* translators: one-letter abbreviation of the weekday */ _x( 'S', 'Saturday initial' );

		// Abbreviations for each day.
		$this->weekday_abbrev[ __( 'Sunday' ) ]    = /* translators: three-letter abbreviation of the weekday */ __( 'Sun' );
		$this->weekday_abbrev[ __( 'Monday' ) ]    = /* translators: three-letter abbreviation of the weekday */ __( 'Mon' );
		$this->weekday_abbrev[ __( 'Tuesday' ) ]   = /* translators: three-letter abbreviation of the weekday */ __( 'Tue' );
		$this->weekday_abbrev[ __( 'Wednesday' ) ] = /* translators: three-letter abbreviation of the weekday */ __( 'Wed' );
		$this->weekday_abbrev[ __( 'Thursday' ) ]  = /* translators: three-letter abbreviation of the weekday */ __( 'Thu' );
		$this->weekday_abbrev[ __( 'Friday' ) ]    = /* translators: three-letter abbreviation of the weekday */ __( 'Fri' );
		$this->weekday_abbrev[ __( 'Saturday' ) ]  = /* translators: three-letter abbreviation of the weekday */ __( 'Sat' );

		// The Months
		$this->month['01'] = /* translators: month name */ __( 'January' );
		$this->month['02'] = /* translators: month name */ __( 'February' );
		$this->month['03'] = /* translators: month name */ __( 'March' );
		$this->month['04'] = /* translators: month name */ __( 'April' );
		$this->month['05'] = /* translators: month name */ __( 'May' );
		$this->month['06'] = /* translators: month name */ __( 'June' );
		$this->month['07'] = /* translators: month name */ __( 'July' );
		$this->month['08'] = /* translators: month name */ __( 'August' );
		$this->month['09'] = /* translators: month name */ __( 'September' );
		$this->month['10'] = /* translators: month name */ __( 'October' );
		$this->month['11'] = /* translators: month name */ __( 'November' );
		$this->month['12'] = /* translators: month name */ __( 'December' );

		// The Months, genitive
		$this->month_genitive['01'] = /* translators: month name, genitive */ _x( 'January', 'genitive' );
		$this->month_genitive['02'] = /* translators: month name, genitive */ _x( 'February', 'genitive' );
		$this->month_genitive['03'] = /* translators: month name, genitive */ _x( 'March', 'genitive' );
		$this->month_genitive['04'] = /* translators: month name, genitive */ _x( 'April', 'genitive' );
		$this->month_genitive['05'] = /* translators: month name, genitive */ _x( 'May', 'genitive' );
		$this->month_genitive['06'] = /* translators: month name, genitive */ _x( 'June', 'genitive' );
		$this->month_genitive['07'] = /* translators: month name, genitive */ _x( 'July', 'genitive' );
		$this->month_genitive['08'] = /* translators: month name, genitive */ _x( 'August', 'genitive' );
		$this->month_genitive['09'] = /* translators: month name, genitive */ _x( 'September', 'genitive' );
		$this->month_genitive['10'] = /* translators: month name, genitive */ _x( 'October', 'genitive' );
		$this->month_genitive['11'] = /* translators: month name, genitive */ _x( 'November', 'genitive' );
		$this->month_genitive['12'] = /* translators: month name, genitive */ _x( 'December', 'genitive' );

		// Abbreviations for each month.
		$this->month_abbrev[ __( 'January' ) ]   = /* translators: three-letter abbreviation of the month */ _x( 'Jan', 'January abbreviation' );
		$this->month_abbrev[ __( 'February' ) ]  = /* translators: three-letter abbreviation of the month */ _x( 'Feb', 'February abbreviation' );
		$this->month_abbrev[ __( 'March' ) ]     = /* translators: three-letter abbreviation of the month */ _x( 'Mar', 'March abbreviation' );
		$this->month_abbrev[ __( 'April' ) ]     = /* translators: three-letter abbreviation of the month */ _x( 'Apr', 'April abbreviation' );
		$this->month_abbrev[ __( 'May' ) ]       = /* translators: three-letter abbreviation of the month */ _x( 'May', 'May abbreviation' );
		$this->month_abbrev[ __( 'June' ) ]      = /* translators: three-letter abbreviation of the month */ _x( 'Jun', 'June abbreviation' );
		$this->month_abbrev[ __( 'July' ) ]      = /* translators: three-letter abbreviation of the month */ _x( 'Jul', 'July abbreviation' );
		$this->month_abbrev[ __( 'August' ) ]    = /* translators: three-letter abbreviation of the month */ _x( 'Aug', 'August abbreviation' );
		$this->month_abbrev[ __( 'September' ) ] = /* translators: three-letter abbreviation of the month */ _x( 'Sep', 'September abbreviation' );
		$this->month_abbrev[ __( 'October' ) ]   = /* translators: three-letter abbreviation of the month */ _x( 'Oct', 'October abbreviation' );
		$this->month_abbrev[ __( 'November' ) ]  = /* translators: three-letter abbreviation of the month */ _x( 'Nov', 'November abbreviation' );
		$this->month_abbrev[ __( 'December' ) ]  = /* translators: three-letter abbreviation of the month */ _x( 'Dec', 'December abbreviation' );

		// The Meridiems
		$this->meridiem['am'] = __( 'am' );
		$this->meridiem['pm'] = __( 'pm' );
		$this->meridiem['AM'] = __( 'AM' );
		$this->meridiem['PM'] = __( 'PM' );

		// Numbers formatting
		// See https://secure.php.net/number_format

		/* translators: $thousands_sep argument for https://secure.php.net/number_format, default is , */
		$thousands_sep = __( 'number_format_thousands_sep' );

		if ( version_compare( PHP_VERSION, '5.4', '>=' ) ) {
			// Replace space with a non-breaking space to avoid wrapping.
			$thousands_sep = str_replace( ' ', '&nbsp;', $thousands_sep );
		} else {
			// PHP < 5.4.0 does not support multiple bytes in thousands separator.
			$thousands_sep = str_replace( array( '&nbsp;', '&#160;' ), ' ', $thousands_sep );
		}

		$this->number_format['thousands_sep'] = ( 'number_format_thousands_sep' === $thousands_sep ) ? ',' : $thousands_sep;

		/* translators: $dec_point argument for https://secure.php.net/number_format, default is . */
		$decimal_point = __( 'number_format_decimal_point' );

		$this->number_format['decimal_point'] = ( 'number_format_decimal_point' === $decimal_point ) ? '.' : $decimal_point;

		// Set text direction.
		if ( isset( $GLOBALS['text_direction'] ) ) {
			$this->text_direction = $GLOBALS['text_direction'];

			/* translators: 'rtl' or 'ltr'. This sets the text direction for WordPress. */
		} elseif ( 'rtl' == _x( 'ltr', 'text direction' ) ) {
			$this->text_direction = 'rtl';
		}

		if ( 'rtl' === $this->text_direction && strpos( $GLOBALS['wp_version'], '-src' ) ) {
			$this->text_direction = 'ltr';
			add_action( 'all_admin_notices', array( $this, 'rtl_src_admin_notice' ) );
		}
	}

	/**
	 * Outputs an admin notice if the /build directory must be used for RTL.
	 *
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
	 *
	 * @param int $weekday_number 0 for Sunday through 6 Saturday.
	 * @return string Full translated weekday.
	 */
	public function get_weekday( $weekday_number ) {
		return $this->weekday[ $weekday_number ];
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
	 *
	 * @param string $weekday_name Full translated weekday word.
	 * @return string Translated weekday initial.
	 */
	public function get_weekday_initial( $weekday_name ) {
		return $this->weekday_initial[ $weekday_name ];
	}

	/**
	 * Retrieve the translated weekday abbreviation.
	 *
	 * The weekday abbreviation is retrieved by the translated
	 * full weekday word.
	 *
	 * @since 2.1.0
	 *
	 * @param string $weekday_name Full translated weekday word.
	 * @return string Translated weekday abbreviation.
	 */
	public function get_weekday_abbrev( $weekday_name ) {
		return $this->weekday_abbrev[ $weekday_name ];
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
	 *
	 * @param string|int $month_number '01' through '12'.
	 * @return string Translated full month name.
	 */
	public function get_month( $month_number ) {
		return $this->month[ zeroise( $month_number, 2 ) ];
	}

	/**
	 * Retrieve translated version of month abbreviation string.
	 *
	 * The $month_name parameter is expected to be the translated or
	 * translatable version of the month.
	 *
	 * @since 2.1.0
	 *
	 * @param string $month_name Translated month to get abbreviated version.
	 * @return string Translated abbreviated month.
	 */
	public function get_month_abbrev( $month_name ) {
		return $this->month_abbrev[ $month_name ];
	}

	/**
	 * Retrieve translated version of meridiem string.
	 *
	 * The $meridiem parameter is expected to not be translated.
	 *
	 * @since 2.1.0
	 *
	 * @param string $meridiem Either 'am', 'pm', 'AM', or 'PM'. Not translated version.
	 * @return string Translated version
	 */
	public function get_meridiem( $meridiem ) {
		return $this->meridiem[ $meridiem ];
	}

	/**
	 * Global variables are deprecated.
	 *
	 * For backward compatibility only.
	 *
	 * @deprecated For backward compatibility only.
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
		/* translators: localized date format, see https://secure.php.net/date */
		__( 'F j, Y' );
		/* translators: localized time format, see https://secure.php.net/date */
		__( 'g:i a' );
		/* translators: localized date and time format, see https://secure.php.net/date */
		__( 'F j, Y g:i a' );
	}
}
