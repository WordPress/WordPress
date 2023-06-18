<?php
/**
 * Generate a snippet for scheduling a cronjob.
 *
 * @package WPCode
 */

/**
 * WPCode_Generator_Cronjob class.
 */
class WPCode_Generator_Cronjob extends WPCode_Generator_Type {

	/**
	 * The generator slug.
	 *
	 * @var string
	 */
	public $name = 'cronjob';

	/**
	 * The categories for this generator.
	 *
	 * @var string[]
	 */
	public $categories = array(
		'core',
	);

	/**
	 * Set the translatable strings.
	 *
	 * @return void
	 */
	protected function set_strings() {
		$this->title       = __( 'Schedule a Cron Job', 'insert-headers-and-footers' );
		$this->description = __( 'Generate a snippet to schedule a recurring event using the WordPress cron.', 'insert-headers-and-footers' );
	}

	/**
	 * Load the generator tabs.
	 *
	 * @return void
	 */
	protected function load_tabs() {
		$this->tabs = array(
			'info'     => array(
				'label'   => __( 'Info', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						// Column 1 fields.
						array(
							'type'    => 'description',
							'label'   => __( 'Overview', 'insert-headers-and-footers' ),
							'content' => sprintf(
							// Translators: Placeholders add links to the wordpress.org references.
								__( 'This generator makes it easy to generate a snippet that will schedule a recurring event using %1$swp_schedule_event%2$s.', 'insert-headers-and-footers' ),
								'<a href="https://developer.wordpress.org/reference/functions/wp_schedule_event/" target="_blank">',
								'</a>'
							),
						),
					),
					// Column 2.
					array(
						// Column 2 fields.
						array(
							'type'    => 'list',
							'label'   => __( 'Usage', 'insert-headers-and-footers' ),
							'content' => array(
								__( 'Fill in the forms using the menu on the left.', 'insert-headers-and-footers' ),
								__( 'Click the "Update Code" button.', 'insert-headers-and-footers' ),
								__( 'Click on "Use Snippet" to create a new snippet with the generated code.', 'insert-headers-and-footers' ),
								__( 'Activate and save the snippet and you\'re ready to go', 'insert-headers-and-footers' ),
							),
						),
					),
					// Column 3.
					array(
						// Column 3 fields.
						array(
							'type'    => 'description',
							'label'   => __( 'Examples', 'insert-headers-and-footers' ),
							'content' => __( 'You may want to run some code once every hour, day or week, for example you could use this to send an email with the number of published posts every week.', 'insert-headers-and-footers' ),
						),
					),
				),
			),
			'general'  => array(
				'label'   => __( 'General', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Function name', 'insert-headers-and-footers' ),
							'description' => __( 'Make this unique to avoid conflicts with other snippets', 'insert-headers-and-footers' ),
							'id'          => 'function_name',
							'placeholder' => 'add_custom_schedule',
							'default'     => 'add_custom_schedule' . time(),
						),
					),
					// Column 2.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Text Domain', 'insert-headers-and-footers' ),
							'description' => __( 'Optional text domain for translations.', 'insert-headers-and-footers' ),
							'id'          => 'text_domain',
							'placeholder' => 'text_domain',
							'default'     => 'text_domain',
						),
					),
				),
			),
			'schedule' => array(
				'label'   => __( 'Schedule', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'        => 'select',
							'label'       => __( 'Recurrence', 'insert-headers-and-footers' ),
							'description' => __( 'Choose how often you want this event to run.', 'insert-headers-and-footers' ),
							'id'          => 'recurrence',
							'default'     => 'hourly',
							'options'     => array(
								'hourly'     => __( 'Hourly', 'insert-headers-and-footers' ),
								'twicedaily' => __( 'Twice Daily', 'insert-headers-and-footers' ),
								'daily'      => __( 'Daily', 'insert-headers-and-footers' ),
								'custom'     => __( 'Custom', 'insert-headers-and-footers' ),
							),
						),
					),
					// Column 2.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Custom Recurrence Name', 'insert-headers-and-footers' ),
							'description' => __( 'This is the recurrence name slug, lowercase and no space.', 'insert-headers-and-footers' ),
							'id'          => 'recurrence_name',
							'default'     => 'biweekly',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Custom Recurrence Label', 'insert-headers-and-footers' ),
							'id'          => 'recurrence_label',
							'default'     => 'Once every 2 weeks',
							'description' => __( 'This label will be used in a list of cron events, for example.', 'insert-headers-and-footers' ),
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Custom Recurrence Interval', 'insert-headers-and-footers' ),
							'id'          => 'recurrence_interval',
							'default'     => 1209600,
							'description' => __( 'The number of seconds of this interval.', 'insert-headers-and-footers' ),
						),
					),
				),
			),
			'hook'     => array(
				'label'   => __( 'Code', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Hook name', 'insert-headers-and-footers' ),
							'description' => __( 'Unique name of your hook used to run when scheduled.', 'insert-headers-and-footers' ),
							'id'          => 'hook_name',
							'default'     => 'do_custom_event_' . time(),
						),
					),
					// Column 2.
					array(
						array(
							'type'        => 'textarea',
							'label'       => __( 'PHP Code', 'insert-headers-and-footers' ),
							'description' => __( 'Custom PHP code that will run when the event is triggered.', 'insert-headers-and-footers' ),
							'id'          => 'code',
							'code'        => true,
						),
					),
				),
			),
		);
	}

	/**
	 * Get the snippet code with dynamic values applied.
	 *
	 * @return string
	 */
	public function get_snippet_code() {

		$function_name       = $this->sanitize_function_name( $this->get_value( 'function_name' ) );
		$hook_name           = $this->sanitize_function_name( $this->get_value( 'hook_name' ) );
		$interval            = $this->get_value( 'recurrence' );
		$recurrence_interval = intval( $this->get_value( 'recurrence_interval' ) );
		$custom_recurrence   = '';

		if ( 'custom' === $interval ) {
			$recurrence_function_name = 'custom_cron_recurrence_' . time();
			$recurrence_name          = $this->sanitize_function_name( $this->get_value( 'recurrence_name' ) );
			$interval                 = $recurrence_name;

			$custom_recurrence = "
function $recurrence_function_name( \$schedules ) {
	\$schedules['$recurrence_name'] = array(
		'display' => __( '{$this->get_value('recurrence_label')}', '{$this->get_value('text_domain')}' ),
		'interval' => $recurrence_interval,
	);
				
	return \$schedules;
}
add_filter( 'cron_schedules', '$recurrence_function_name' );
			";
		}

		return <<<EOD
// Schedule a cron event.
function $hook_name() {
	{$this->get_value( 'code' )}
}
add_action( '$hook_name', '$hook_name' );

$custom_recurrence
 
function $function_name() {
	if ( ! wp_next_scheduled( '$hook_name' ) ) {
		wp_schedule_event( time(), '$interval', '$hook_name' );
	}
}
add_action( 'wp', '$function_name' );
EOD;
	}

}
