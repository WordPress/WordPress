<?php
/**
 * Customize API: WP_Customize_Theme_Control class
 *
 * @package WordPress
 * @subpackage Customize
 * @since 4.4.0
 */

/**
 * Customize Theme Control class.
 *
 * @since 4.2.0
 *
 * @see WP_Customize_Control
 */
class WP_Customize_Theme_Control extends WP_Customize_Control {

	/**
	 * Customize control type.
	 *
	 * @since 4.2.0
	 * @access public
	 * @var string
	 */
	public $type = 'theme';

	/**
	 * Theme object.
	 *
	 * @since 4.2.0
	 * @access public
	 * @var WP_Theme
	 */
	public $theme;

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 * @since 4.2.0
	 * @access public
	 *
	 * @see WP_Customize_Control::to_json()
	 */
	public function to_json() {
		parent::to_json();
		$this->json['theme'] = $this->theme;
	}

	/**
	 * Don't render the control content from PHP, as it's rendered via JS on load.
	 *
	 * @since 4.2.0
	 * @access public
	 */
	public function render_content() {}

	/**
	 * Render a JS template for theme display.
	 *
	 * @since 4.2.0
	 * @access public
	 */
	public function content_template() {
		$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
		$active_url  = esc_url( remove_query_arg( 'theme', $current_url ) );
		$preview_url = esc_url( add_query_arg( 'theme', '__THEME__', $current_url ) ); // Token because esc_url() strips curly braces.
		$preview_url = str_replace( '__THEME__', '{{ data.theme.id }}', $preview_url );
		?>
		<# if ( data.theme.isActiveTheme ) { #>
			<div class="theme active" tabindex="0" data-preview-url="<?php echo esc_attr( $active_url ); ?>" aria-describedby="{{ data.theme.id }}-action {{ data.theme.id }}-name">
		<# } else { #>
			<div class="theme" tabindex="0" data-preview-url="<?php echo esc_attr( $preview_url ); ?>" aria-describedby="{{ data.theme.id }}-action {{ data.theme.id }}-name">
		<# } #>

			<# if ( data.theme.screenshot[0] ) { #>
				<div class="theme-screenshot">
					<img data-src="{{ data.theme.screenshot[0] }}" alt="" />
				</div>
			<# } else { #>
				<div class="theme-screenshot blank"></div>
			<# } #>

			<# if ( data.theme.isActiveTheme ) { #>
				<span class="more-details" id="{{ data.theme.id }}-action"><?php _e( 'Customize' ); ?></span>
			<# } else { #>
				<span class="more-details" id="{{ data.theme.id }}-action"><?php _e( 'Live Preview' ); ?></span>
			<# } #>

			<div class="theme-author"><?php printf( __( 'By %s' ), '{{ data.theme.author }}' ); ?></div>

			<# if ( data.theme.isActiveTheme ) { #>
				<h3 class="theme-name" id="{{ data.theme.id }}-name">
					<?php
					/* translators: %s: theme name */
					printf( __( '<span>Active:</span> %s' ), '{{{ data.theme.name }}}' );
					?>
				</h3>
			<# } else { #>
				<h3 class="theme-name" id="{{ data.theme.id }}-name">{{{ data.theme.name }}}</h3>
				<div class="theme-actions">
					<button type="button" class="button theme-details"><?php _e( 'Theme Details' ); ?></button>
				</div>
			<# } #>
		</div>
	<?php
	}
}
