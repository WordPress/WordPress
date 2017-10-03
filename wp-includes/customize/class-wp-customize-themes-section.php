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
 * A UI container for theme controls, which are displayed within sections.
 *
 * @since 4.2.0
 *
 * @see WP_Customize_Section
 */
class WP_Customize_Themes_Section extends WP_Customize_Section {

	/**
	 * Section type.
	 *
	 * @since 4.2.0
	 * @var string
	 */
	public $type = 'themes';

	/**
	 * Theme section action.
	 *
	 * Defines the type of themes to load (installed, wporg, etc.).
	 *
	 * @since 4.9.0
	 * @var string
	 */
	public $action = '';

	/**
	 * Get section parameters for JS.
	 *
	 * @since 4.9.0
	 * @return array Exported parameters.
	 */
	public function json() {
		$exported = parent::json();
		$exported['action'] = $this->action;

		return $exported;
	}

	/**
	 * Render a themes section as a JS template.
	 *
	 * The template is only rendered by PHP once, so all actions are prepared at once on the server side.
	 *
	 * @since 4.9.0
	 */
	protected function render_template() {
		?>
		<li id="accordion-section-{{ data.id }}" class="theme-section">
			<button type="button" class="customize-themes-section-title themes-section-{{ data.id }}">{{ data.title }}</button>
			<?php if ( current_user_can( 'install_themes' ) || is_multisite() ) : // @todo: upload support ?>
			<?php endif; ?>
			<div class="customize-themes-section themes-section-{{ data.id }} control-section-content themes-php">
				<div class="theme-overlay" tabindex="0" role="dialog" aria-label="<?php esc_attr_e( 'Theme Details' ); ?>"></div>
				<div class="theme-browser rendered">
					<div class="customize-preview-header themes-filter-bar">
						<?php $this->filter_bar_content_template(); ?>
					</div>
					<div class="error unexpected-error" style="display: none; "><p><?php _e( 'An unexpected error occurred. Something may be wrong with WordPress.org or this server&#8217;s configuration. If you continue to have problems, please try the <a href="https://wordpress.org/support/">support forums</a>.' ); ?></p></div>
					<ul class="themes">
					</ul>
					<p class="no-themes"><?php _e( 'No themes found. Try a different search.' ); ?></p>
					<p class="no-themes-local">
						<?php
						/* translators: %s is the string, "search WordPress.org themes" */
						printf( __( 'No themes found. Try a different search, or %s.' ),
							sprintf( '<button type="button" class="button-link search-dotorg-themes">%s</button>', __( 'Search WordPress.org themes' ) )
						);
						?>
					</p>
					<p class="spinner"></p>
				</div>
			</div>
		</li>
		<?php
	}

	/**
	 * Render the filter bar portion of a themes section as a JS template.
	 *
	 * The template is only rendered by PHP once, so all actions are prepared at once on the server side.
	 * The filter bar container is rendered by @see `render_template()`.
	 *
	 * @since 4.9.0
	 */
	protected function filter_bar_content_template() {
		?>
		<button type="button" class="button button-primary customize-section-back customize-themes-mobile-back"><?php _e( 'Back to theme sources' ); ?></button>
		<# if ( 'wporg' === data.action ) { #>
			<div class="search-form">
				<label for="wp-filter-search-input-{{ data.id }}" class="screen-reader-text"><?php _e( 'Search themes&hellip;' ); ?></label>
				<input type="search" id="wp-filter-search-input-{{ data.id }}" placeholder="<?php esc_attr_e( 'Search themes&hellip;' ); ?>" aria-describedby="{{ data.id }}-live-search-desc" class="wp-filter-search">
				<span id="{{ data.id }}-live-search-desc" class="screen-reader-text"><?php _e( 'The search results will be updated as you type.' ); ?></span>
			</div>
			<button type="button" class="button feature-filter-toggle">
				<span class="filter-count-0"><?php _e( 'Filter themes' ); ?></span><span class="filter-count-filters">
				<?php
				/* translators: %s: number of filters selected. */
				printf( __( 'Filter themes (%s)' ), '<span class="theme-filter-count">0</span>' );
				?>
				</span>
			</button>
			<div class="filter-drawer filter-details">
				<?php
				$feature_list = get_theme_feature_list( false ); // @todo: Use the .org API instead of the local core feature list. The .org API is currently outdated and will be reconciled when the .org themes directory is next redesigned.
				foreach ( $feature_list as $feature_name => $features ) {
					echo '<fieldset class="filter-group">';
					echo '<legend>' . esc_html( $feature_name ) . '</legend>';
					echo '<div class="filter-group-feature">';
					foreach ( $features as $feature => $feature_name ) {
						echo '<input type="checkbox" id="filter-id-' . esc_attr( $feature ) . '" value="' . esc_attr( $feature ) . '" /> ';
						echo '<label for="filter-id-' . esc_attr( $feature ) . '">' . esc_html( $feature_name ) . '</label><br>';
					}
					echo '</div>';
					echo '</fieldset>';
				}
				?>
			</div>
		<# } else { #>
			<div class="themes-filter-container">
				<label for="{{ data.id }}-themes-filter" class="screen-reader-text"><?php _e( 'Search themes&hellip;' ); ?></label>
				<input type="search" id="{{ data.id }}-themes-filter" placeholder="<?php esc_attr_e( 'Search themes&hellip;' ); ?>" aria-describedby="{{ data.id }}-live-search-desc" class="wp-filter-search wp-filter-search-themes" />
				<span id="{{ data.id }}-live-search-desc" class="screen-reader-text"><?php _e( 'The search results will be updated as you type.' ); ?></span>
			</div>
		<# } #>
		<div class="filter-themes-count">
			<span class="themes-displayed">
				<?php
				/* translators: %s: number of themes displayed. */
				echo sprintf( __( '%s themes' ), '<span class="theme-count">0</span>' );
				?>
			</span>
		</div>
		<?php
	}
}
