<?php
/**
 * Customize API: WP_Customize_Date_Time_Control class
 *
 * @package WordPress
 * @subpackage Customize
 * @since 4.9.0
 */

/**
 * Customize Date Time Control class.
 *
 * @since 4.9.0
 *
 * @see WP_Customize_Control
 */
class WP_Customize_Date_Time_Control extends WP_Customize_Control {

	/**
	 * Customize control type.
	 *
	 * @since 4.9.0
	 * @var string
	 */
	public $type = 'date_time';

	/**
	 * Minimum Year.
	 *
	 * @since 4.9.0
	 * @var integer
	 */
	public $min_year = 1000;

	/**
	 * Maximum Year.
	 *
	 * @since 4.9.0
	 * @var integer
	 */
	public $max_year = 9999;

	/**
	 * Allow past date, if set to false user can only select future date.
	 *
	 * @since 4.9.0
	 * @var boolean
	 */
	public $allow_past_date = true;

	/**
	 * If set to false the control will appear in 24 hour format,
	 * the value will still be saved in Y-m-d H:i:s format.
	 *
	 * @since 4.9.0
	 * @var boolean
	 */
	public $twelve_hour_format = true;

	/**
	 * Default date/time to be displayed in the control.
	 *
	 * @since 4.9.0
	 * @var string
	 */
	public $default_value;

	/**
	 * Don't render the control's content - it's rendered with a JS template.
	 *
	 * @since 4.9.0
	 */
	public function render_content() {}

	/**
	 * Export data to JS.
	 *
	 * @since 4.9.0
	 * @return array
	 */
	public function json() {
		$data = parent::json();

		$data['maxYear'] = intval( $this->max_year );
		$data['minYear'] = intval( $this->min_year );
		$data['allowPastDate'] = $this->allow_past_date ? true : false;
		$data['twelveHourFormat'] = $this->twelve_hour_format ? true : false;
		$data['defaultValue'] = $this->default_value;

		return $data;
	}

	/**
	 * Renders a JS template for the content of date time control.
	 *
	 * @since 4.9.0
	 */
	public function content_template() {
		$data = array_merge( $this->json(), $this->get_month_choices() );
		$timezone_info = $this->get_timezone_info();
		?>

		<# _.defaults( data, <?php echo wp_json_encode( $data ); ?> ); #>

		<span class="customize-control-title">
			<label>{{ data.label }}</label>
		</span>
		<div class="customize-control-notifications-container"></div>
		<span class="description customize-control-description">{{ data.description }}</span>
		<div class="date-time-fields">
			<div class="day-row">
				<span class="title-day"><?php esc_html_e( 'Day' ); ?></span>
				<div class="day-fields clear">
					<label class="month-field">
						<span class="screen-reader-text"><?php esc_html_e( 'Month' ); ?></span>
							<select class="date-input month" data-component="month">
								<# _.each( data.month_choices, function( choice ) {
									if ( _.isObject( choice ) && ! _.isUndefined( choice.text ) && ! _.isUndefined( choice.value ) ) {
										text = choice.text;
										value = choice.value;
									}
									#>
									<option value="{{ value }}" >
										{{ text }}
									</option>
								<# } ); #>
							</select>
					</label>
					<label class="day-field">
						<span class="screen-reader-text"><?php esc_html_e( 'Day' ); ?></span>
						<input type="number" size="2" maxlength="2" autocomplete="off" class="date-input day" data-component="day" min="1" max="31"" />
					</label>
					<span class="time-special-char date-time-separator">,</span>
					<label class="year-field">
						<span class="screen-reader-text"><?php esc_html_e( 'Year' ); ?></span>
						<# var maxYearLength = String( data.maxYear ).length; #>
						<input type="number" size="4" maxlength="{{ maxYearLength }}" autocomplete="off" class="date-input year" data-component="year" min="{{ data.minYear }}" max="{{ data.maxYear }}" />
					</label>
				</div>
			</div>
			<div class="time-row clear">
				<span class="title-time"><?php esc_html_e( 'Time' ); ?></span>
				<div class="time-fields clear">
					<label class="hour-field">
						<span class="screen-reader-text"><?php esc_html_e( 'Hour' ); ?></span>
						<# var maxHour = data.twelveHourFormat ? 12 : 24; #>
						<input type="number" size="2" maxlength="2" autocomplete="off" class="date-input hour" data-component="hour" min="1" max="{{ maxHour }}"" />
					</label>
					<span class="time-special-char date-time-separator">:</span>
					<label class="minute-field">
						<span class="screen-reader-text"><?php esc_html_e( 'Minute' ); ?></span>
						<input type="number" size="2" maxlength="2" autocomplete="off" class="date-input minute" data-component="minute" min="0" max="59" />
					</label>
					<# if ( data.twelveHourFormat ) { #>
					<label class="am-pm-field">
						<span class="screen-reader-text"><?php esc_html_e( 'AM / PM' ); ?></span>
						<select class="date-input" data-component="ampm">
							<option value="am"><?php esc_html_e( 'AM' ); ?></option>
							<option value="pm"><?php esc_html_e( 'PM' ); ?></option>
						</select>
					</label>
					<# } #>
					<abbr class="date-timezone" aria-label="<?php esc_attr_e( 'Timezone' ); ?>" title="<?php echo esc_attr( $timezone_info['description'] ); ?>"><?php echo esc_html( $timezone_info['abbr'] ); ?></abbr>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Generate options for the month Select.
	 *
	 * Based on touch_time().
	 *
	 * @since 4.9.0
	 * @see touch_time()
	 *
	 * @return array
	 */
	public function get_month_choices() {
		global $wp_locale;
		$months = array();
		for ( $i = 1; $i < 13; $i++ ) {
			$month_text = $wp_locale->get_month_abbrev( $wp_locale->get_month( $i ) );

			/* translators: 1: month number (01, 02, etc.), 2: month abbreviation */
			$months[ $i ]['text'] = sprintf( __( '%1$s-%2$s' ), $i, $month_text );
			$months[ $i ]['value'] = $i;
		}
		return array(
			'month_choices' => $months,
		);
	}

	/**
	 * Get timezone info.
	 *
	 * @since 4.9.0
	 *
	 * @return array abbr and description.
	 */
	public function get_timezone_info() {
		$tz_string = get_option( 'timezone_string' );
		$timezone_info = array();

		if ( $tz_string ) {
			try {
				$tz = new DateTimezone( $tz_string );
			} catch ( Exception $e ) {
				$tz = '';
			}

			if ( $tz ) {
				$now = new DateTime( 'now', $tz );
				$formatted_gmt_offset = sprintf( 'UTC%s', $this->format_gmt_offset( $tz->getOffset( $now ) / 3600 ) );
				$tz_name = str_replace( '_', ' ', $tz->getName() );
				$timezone_info['abbr'] = $now->format( 'T' );

				/* translators: 1: timezone name, 2: timezone abbreviation, 3: gmt offset  */
				$timezone_info['description'] = sprintf( __( 'Timezone is %1$s (%2$s), currently %3$s.' ), $tz_name, $timezone_info['abbr'], $formatted_gmt_offset );
			} else {
				$timezone_info['description'] = '';
			}
		} else {
			$formatted_gmt_offset = $this->format_gmt_offset( intval( get_option( 'gmt_offset', 0 ) ) );
			$timezone_info['abbr'] = sprintf( 'UTC%s', $formatted_gmt_offset );

			/* translators: %s: UTC offset  */
			$timezone_info['description'] = sprintf( __( 'Timezone is %s.' ), $timezone_info['abbr'] );
		}

		return $timezone_info;
	}

	/**
	 * Format GMT Offset.
	 *
	 * @since 4.9.0
	 * @see wp_timezone_choice()
	 *
	 * @param float $offset Offset in hours.
	 * @return string Formatted offset.
	 */
	public function format_gmt_offset( $offset ) {
		if ( 0 <= $offset ) {
			$formatted_offset = '+' . (string) $offset;
		} else {
			$formatted_offset = (string) $offset;
		}
		$formatted_offset = str_replace(
			array( '.25', '.5', '.75' ),
			array( ':15', ':30', ':45' ),
			$formatted_offset
		);
		return $formatted_offset;
	}
}
