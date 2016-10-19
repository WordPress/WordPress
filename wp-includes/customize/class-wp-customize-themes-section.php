<?php
/**
 * Customize API: WP_Customize_Themes_Section class
 *
 * @package WordPress
 * @subpackage Customize
 * @since 4.4.0
 */

/**
 * Customize Themes Section class.
 *
 * A UI container for theme controls, which are displayed in tabbed sections.
 *
 * @since 4.2.0
 *
 * @see WP_Customize_Section
 */
class WP_Customize_Themes_Section extends WP_Customize_Section {

	/**
	 * Customize section type.
	 *
	 * @since 4.2.0
	 * @access public
	 * @var string
	 */
	public $type = 'themes';

	/**
	 * Theme section action.
	 *
	 * Defines the type of themes to load (installed, featured, latest, etc.).
	 *
	 * @since 4.7.0
	 * @access public
	 * @var string
	 */
	public $action = '';

	/**
	 * Text before theme section heading.
	 *
	 * @since 4.7.0
	 * @access public
	 * @var string
	 */
	public $text_before = '';

	/**
	 * Get section parameters for JS.
	 *
	 * @since 4.7.0
	 * @access public
	 * @return array Exported parameters.
	 */
	public function json() {
		$exported = parent::json();
		$exported['action'] = $this->action;
		$exported['text_before'] = $this->text_before;

		return $exported;
	}

	/**
	 * Render a themes section as a JS template.
	 *
	 * The template is only rendered by PHP once, so all actions are prepared at once on the server side.
	 *
	 * @since 4.7.0
	 * @access protected
	 */
	protected function render_template() {
		?>
		<li id="accordion-section-{{ data.id }}" class="theme-section">
			<# if ( '' !== data.text_before ) { #>
				<p class="customize-themes-text-before">{{ data.text_before }}</p>
			<# } #>
			<# if ( 'search' === data.action ) { #>
				<div class="search-form customize-themes-section-title themes-section-search_themes">
					<label class="screen-reader-text" for="wp-filter-search-input">{{ data.title }}</label>
					<input placeholder="{{ data.title }}" type="text" aria-describedby="live-search-desc" id="wp-filter-search-input" class="wp-filter-search">
					<span id="live-search-desc" class="screen-reader-text"><?php _e( 'The search results will be updated as you type.' ); ?></span>
				</div>
			<# } else { #>
				<# if ( 'favorites' === data.action || 'feature_filter' === data.action ) {
					var attr = ' aria-expanded="false"';
				} else {
					var attr = '';
				} #>
				<button type="button" class="customize-themes-section-title themes-section-{{ data.id }}"{{{ attr }}}>{{ data.title }}</button>
			<# } #>
			<?php if ( ! current_user_can( 'install_themes' ) || is_multisite() ) : ?>
				<# if ( 'installed' === data.action ) { #>
					<p class="themes-filter-container">
						<label for="themes-filter">
							<span class="screen-reader-text"><?php _e( 'Search installed themes&hellip;' ); ?></span>
							<input type="text" id="themes-filter" placeholder="<?php esc_attr_e( 'Search installed themes&hellip;' ); ?>" />
						</label>
					</p>
				<# } #>
			<?php endif; ?>
			<# if ( 'favorites' === data.action ) { #>
				<div class="favorites-form filter-details">
					<p class="install-help"><?php _e( 'If you have marked themes as favorites on WordPress.org, you can browse them here.' ); ?></p>
					<p>
						<label for="wporg-username-input"><?php _e( 'Your WordPress.org username:' ); ?></label>
						<input type="search" id="wporg-username-input" value="">
						<button type="button" class="button button-secondary favorites-form-submit"><?php _e( 'Get Favorites' ); ?></button>
					</p>
				</div>
			<# } else if ( 'feature_filter' === data.action ) { #>
				<div class="filter-drawer filter-details">
					<?php
					$feature_list = get_theme_feature_list();
					foreach ( $feature_list as $feature_name => $features ) {
						echo '<fieldset class="filter-group">';
						$feature_name = esc_html( $feature_name );
						echo '<legend><button type="button" class="button-link" aria-expanded="false">' . $feature_name . '</button></legend>';
						echo '<div class="filter-group-feature">';
						foreach ( $features as $feature => $feature_name ) {
							$feature = esc_attr( $feature );
							echo '<input type="checkbox" id="filter-id-' . $feature . '" value="' . $feature . '" /> ';
							echo '<label for="filter-id-' . $feature . '">' . $feature_name . '</label><br>';
						}
						echo '</div>';
						echo '</fieldset>';
					}
					?>
				</div>
			<# } #>
			<div class="customize-themes-section themes-section-{{ data.id }} control-section-content themes-php">
				<div class="theme-overlay" tabindex="0" role="dialog" aria-label="<?php esc_attr_e( 'Theme Details' ); ?>"></div>
				<div class="theme-browser rendered">
					<div class="error unexpected-error" style="display: none; "><p><?php _e( 'An unexpected error occurred. Something may be wrong with WordPress.org or this server&#8217;s configuration. If you continue to have problems, please try the <a href="https://wordpress.org/support/">support forums</a>.' ); ?></p></div>
					<ul class="themes">
					</ul>
					<p class="no-themes"><?php _e( 'No themes found. Try a different search.' ); ?></p>
					<p class="spinner"></p>
				</div>
			</div>
		</li>
<?php }
}
