<?php
/**
 * Administration API: WP_Admin_Notice_Collector class
 *
 * @package WordPress
 * @subpackage Administration
 * @since x.x.x
 */

/**
 * Capture admin notices generated in PHP during a request.
 *
 * Notices are captured via the {@see 'wp_admin_notice_markup'} filter.
 * a title placeholder is inserted into the admin page's `<title>` tag,
 * the admin page title is modified via the {@see 'admin_title'}
 * filter, the admin page is wrapped in an output buffer, and an
 * `ob_start()` callback replaces the placeholder with the final count once
 * the buffer is flushed.
 *
 * @since 7.2.0
 */
class WP_Admin_Notice_Collector {

	/**
	 * Placeholder inserted into the admin title, replaced with the
	 * actual notice count once it is known.
	 *
	 * @since 7.2.0
	 * @var string
	 */
	const TITLE_PLACEHOLDER = '%%wp_admin_notice_count%%';

	/**
	 * Notices captured during the current request.
	 *
	 * @since 7.2.0
	 * @var array[]
	 */
	protected static $notices = array();

	/**
	 * Whether the collector's hooks have already been registered.
	 *
	 * @since 7.2.0
	 * @var bool
	 */
	protected static $hooked = false;

	/**
	 * Registers the hooks used to collect notices and to annotate the admin title.
	 *
	 * @since 7.2.0
	 */
	public static function init() {
		if ( self::$hooked ) {
			return;
		}

		self::$hooked = true;

		add_filter( 'wp_admin_notice_markup', array( __CLASS__, 'capture_notice' ), 10, 3 );

		// Ajax/REST admin requests don't render a <title>, so skip the buffer there.
		if ( is_admin() && ! wp_doing_ajax() ) {
			add_filter( 'admin_title', array( __CLASS__, 'add_title_placeholder' ), 10, 2 );
			ob_start( array( __CLASS__, 'inject_notice_count' ) );
		}
	}

	/**
	 * Captures a single admin notice without altering its markup.
	 *
	 * Hooked to the {@see 'wp_admin_notice_markup'} filter.
	 *
	 * @since 7.2.0
	 *
	 * @param string $markup  The HTML markup for the admin notice.
	 * @param string $message The message for the admin notice.
	 * @param array  $args    The arguments for the admin notice.
	 * @return string The unmodified markup, so the filter remains transparent.
	 */
	public static function capture_notice( $markup, $message, $args ) {
		if ( '' !== trim( wp_strip_all_tags( $message ) ) ) {
			self::$notices[ $args['notice_id'] ] = array(
				'markup'  => $markup,
				'message' => $message,
				'args'    => $args,
			);
		}

		return $markup;
	}

	/**
	 * Returns all notices captured during the current request.
	 *
	 * @since 7.2.0
	 *
	 * @return array[] Array of captured notices. Each entry contains the
	 *                 notice's 'markup', 'message', and 'args'.
	 */
	public static function get_notices() {
		return self::$notices;
	}

	/**
	 * Returns the number of notices captured during the current request.
	 *
	 * @since 7.2.0
	 *
	 * @return int Notice count.
	 */
	public static function get_notice_count() {
		return count( self::$notices );
	}

	/**
	 * Clears all previously captured notices.
	 *
	 * @since 7.2.0
	 */
	public static function reset() {
		self::$notices = array();
	}

	/**
	 * Prepends a title placeholder via the {@see 'admin_title'} filter.
	 *
	 * The real count isn't known yet at this point, since notices are usually
	 * produced later in the page. inject_notice_count() resolves it.
	 *
	 * @since 7.2.0
	 *
	 * @param string $admin_title The page title, with extra context added.
	 * @param string $title       The original page title.
	 * @return string The admin title with the placeholder prepended.
	 */
	public static function add_title_placeholder( $admin_title, $title ) {
		return self::TITLE_PLACEHOLDER . $admin_title;
	}

	/**
	 * Replaces the title placeholder with the final notice count.
	 *
	 * Used as an `ob_start()` callback so it runs once the full page - including
	 * any notices printed after `<title>` - has been buffered.
	 *
	 * @since 7.2.0
	 *
	 * @param string $buffer The full buffered page output.
	 * @return string The page output with the placeholder resolved.
	 */
	public static function inject_notice_count( $buffer ) {
		$count = self::get_notice_count();

		if ( 0 === $count ) {
			return str_replace( self::TITLE_PLACEHOLDER, '', $buffer );
		}

		/* translators: %d: Number of admin notices on the current page. */
		$prefix = sprintf( _n( '(%d notice) ', '(%d notices) ', $count ), $count );

		return str_replace( self::TITLE_PLACEHOLDER, $prefix, $buffer );
	}
}
