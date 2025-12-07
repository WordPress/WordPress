<?php
/**
 * Server-Timing API admin integration file.
 *
 * @package performance-lab
 */

// @codeCoverageIgnoreStart
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
// @codeCoverageIgnoreEnd

// Do not add any of the hooks if Server-Timing is disabled.
if ( defined( 'PERFLAB_DISABLE_SERVER_TIMING' ) && PERFLAB_DISABLE_SERVER_TIMING ) {
	return;
}

/**
 * Adds the Server-Timing page to the Tools menu.
 *
 * @since 2.6.0
 */
function perflab_add_server_timing_page(): void {
	$hook_suffix = add_management_page(
		__( 'Server-Timing', 'performance-lab' ),
		__( 'Server-Timing', 'performance-lab' ),
		'manage_options',
		PERFLAB_SERVER_TIMING_SCREEN,
		'perflab_render_server_timing_page'
	);

	// Add the following hooks only if the screen was successfully added.
	if ( false !== $hook_suffix ) {
		add_action( "load-{$hook_suffix}", 'perflab_load_server_timing_page' );
	}
}

add_action( 'admin_menu', 'perflab_add_server_timing_page' );

/**
 * Initializes settings sections and fields for the Server-Timing page.
 *
 * @since 2.6.0
 */
function perflab_load_server_timing_page(): void {
	/*
	 * This settings section technically includes a field, however it is directly rendered as part of the section
	 * callback due to requiring custom markup.
	 */
	add_settings_section(
		'output-buffering',
		__( 'Output Buffering', 'performance-lab' ),
		'perflab_render_server_timing_page_output_buffering_section',
		PERFLAB_SERVER_TIMING_SCREEN
	);

	// Minor style tweaks to improve appearance similar to other core settings screen instances.
	add_action(
		'admin_print_styles',
		static function (): void {
			?>
			<style>
				.wrap p {
					max-width: 800px;
				}
				.wrap .form-table .td-full {
					padding-top: 0;
				}
			</style>
			<?php
		}
	);

	add_settings_section(
		'benchmarking',
		__( 'Benchmarking', 'performance-lab' ),
		static function (): void {
			?>
			<p>
				<?php
				echo wp_kses(
					sprintf(
						/* translators: %s: Server-Timing */
						__( 'In this section, you can provide hook names to include measurements for them in the %s header.', 'performance-lab' ),
						'<code>Server-Timing</code>'
					),
					array( 'code' => array() )
				);
				echo ' ';
				echo wp_kses(
					__( 'For any hook name provided, the <strong>cumulative duration between all callbacks</strong> attached to the hook is measured, in milliseconds.', 'performance-lab' ),
					array( 'strong' => array() )
				);
				?>
			</p>
			<?php if ( ! perflab_server_timing_use_output_buffer() ) : ?>
				<p>
					<?php
					echo wp_kses(
						sprintf(
							/* translators: 1: Server-Timing, 2: template_include, 3: anchor link */
							__( 'Since the %1$s header is sent before the template is loaded, only hooks before the %2$s filter can be measured. Enable <a href="%3$s">Output Buffering</a> to measure hooks during template rendering.', 'performance-lab' ),
							'<code>Server-Timing</code>',
							'<code>template_include</code>',
							esc_url( '#server_timing_output_buffering' )
						),
						array(
							'code' => array(),
							'a'    => array( 'href' => true ),
						)
					);
					?>
				<?php endif; ?>
			</p>
			<?php
		},
		PERFLAB_SERVER_TIMING_SCREEN
	);

	/*
	 * For all settings fields, the field slug, option sub key, and label
	 * suffix have to match for the rendering in the callback to be
	 * semantically correct.
	 */
	add_settings_field(
		'benchmarking_actions',
		__( 'Actions', 'performance-lab' ),
		static function (): void {
			perflab_render_server_timing_page_hooks_field( 'benchmarking_actions' );
		},
		PERFLAB_SERVER_TIMING_SCREEN,
		'benchmarking',
		array( 'label_for' => 'server_timing_benchmarking_actions' )
	);
	add_settings_field(
		'benchmarking_filters',
		__( 'Filters', 'performance-lab' ),
		static function (): void {
			perflab_render_server_timing_page_hooks_field( 'benchmarking_filters' );
		},
		PERFLAB_SERVER_TIMING_SCREEN,
		'benchmarking',
		array( 'label_for' => 'server_timing_benchmarking_filters' )
	);
}

/**
 * Renders the Server-Timing page.
 *
 * @since 2.6.0
 */
function perflab_render_server_timing_page(): void {
	?>
	<div class="wrap">
		<?php settings_errors(); ?>
		<h1>
			<?php esc_html_e( 'Server-Timing', 'performance-lab' ); ?>
		</h1>

		<form action="options.php" method="post">
			<?php settings_fields( PERFLAB_SERVER_TIMING_SCREEN ); ?>
			<?php do_settings_sections( PERFLAB_SERVER_TIMING_SCREEN ); ?>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

/**
 * Renders a hooks field for the given Server-Timing option.
 *
 * @since 2.6.0
 *
 * @param string $slug Slug of the field and sub-key in the Server-Timing option.
 */
function perflab_render_server_timing_page_hooks_field( string $slug ): void {
	$options = (array) get_option( PERFLAB_SERVER_TIMING_SETTING, array() );

	// Value for the sub-key is an array of hook names.
	$value = '';
	if ( isset( $options[ $slug ] ) ) {
		$value = implode( "\n", $options[ $slug ] );
	}

	$field_id       = "server_timing_{$slug}";
	$field_name     = PERFLAB_SERVER_TIMING_SETTING . '[' . $slug . ']';
	$description_id = "{$field_id}_description";

	?>
	<textarea
		id="<?php echo esc_attr( $field_id ); ?>"
		name="<?php echo esc_attr( $field_name ); ?>"
		aria-describedby="<?php echo esc_attr( $description_id ); ?>"
		class="large-text code"
		rows="8"
	><?php echo esc_textarea( $value ); ?></textarea>
	<p id="<?php echo esc_attr( $description_id ); ?>" class="description">
		<?php esc_html_e( 'Enter a single hook name per line.', 'performance-lab' ); ?>
	</p>
	<?php
}

/**
 * Renders the section for enabling output buffering for Server-Timing.
 *
 * @since 2.6.0
 */
function perflab_render_server_timing_page_output_buffering_section(): void {
	$slug           = 'output_buffering';
	$field_id       = "server_timing_{$slug}";
	$field_name     = PERFLAB_SERVER_TIMING_SETTING . '[' . $slug . ']';
	$description_id = "{$field_id}_description";
	$has_filter     = has_filter( 'perflab_server_timing_use_output_buffer' );
	$is_enabled     = perflab_server_timing_use_output_buffer();

	/*
	 * The hard-coded .form-table output here overall matches the WordPress core markup generated by
	 * `do_settings_sections()` and `do_settings_fields()`, however since it is impossible to modify the CSS classes on
	 * the `<td>` elements, it needs to be hard-coded to achieve the same appearance as e.g. the UI control for the
	 * `uploads_use_yearmonth_folders` option in the _Settings > Media_ screen, which is hard-coded as well.
	 */
	?>
	<table class="form-table" role="presentation">
		<tr>
			<td class="td-full">
				<label for="<?php echo esc_attr( $field_id ); ?>">
					<input
						type="checkbox"
						id="<?php echo esc_attr( $field_id ); ?>"
						name="<?php echo esc_attr( $field_name ); ?>"
						aria-describedby="<?php echo esc_attr( $description_id ); ?>"
						<?php disabled( $has_filter ); ?>
						<?php checked( $is_enabled ); ?>
					>
					<?php esc_html_e( 'Enable output buffering of template rendering', 'performance-lab' ); ?>
				</label>
				<p id="<?php echo esc_attr( $description_id ); ?>" class="description">
					<?php if ( $has_filter ) : ?>
						<?php if ( $is_enabled ) : ?>
							<?php
							echo wp_kses(
								sprintf(
									/* translators: %s: perflab_server_timing_use_output_buffer */
									__( 'Output buffering has been forcibly enabled via the %s filter.', 'performance-lab' ),
									'<code>perflab_server_timing_use_output_buffer</code>'
								),
								array( 'code' => array() )
							);
							?>
						<?php else : ?>
							<?php
							echo wp_kses(
								sprintf(
									/* translators: %s: perflab_server_timing_use_output_buffer */
									__( 'Output buffering has been forcibly disabled via the %s filter.', 'performance-lab' ),
									'<code>perflab_server_timing_use_output_buffer</code>'
								),
								array( 'code' => array() )
							);
							?>
						<?php endif; ?>
					<?php endif; ?>
					<?php esc_html_e( 'Output buffering is needed to capture metrics after headers have been sent and while the template is being rendered. Note that output buffering may possibly cause an increase in TTFB if the response would be flushed multiple times.', 'performance-lab' ); ?>
				</p>
			</td>
		</tr>
	</table>
	<?php
}
