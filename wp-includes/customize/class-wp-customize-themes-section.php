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
	 * Theme section filter type.
	 *
	 * Determines whether filters are applied to loaded (local) themes or by initiating a new remote query (remote).
	 * When filtering is local, the initial themes query is not paginated by default.
	 *
	 * @since 4.9.0
	 * @var string
	 */
	public $filter_type = 'local';

	/**
	 * Gets section parameters for JS.
	 *
	 * @since 4.9.0
	 * @return array Exported parameters.
	 */
	public function json() {
		$exported                = parent::json();
		$exported['action']      = $this->action;
		$exported['filter_type'] = $this->filter_type;

		return $exported;
	}

	/**
	 * Renders a themes section as a JS template.
	 *
	 * The template is only rendered by PHP once, so all actions are prepared at once on the server side.
	 *
	 * @since 4.9.0
	 */
	protected function render_template() {
		?>
		<li id="accordion-section-{{ data.id }}" class="theme-section">
			<button type="button" class="customize-themes-section-title themes-section-{{ data.id }}">{{ data.title }}</button>
			<?php if ( current_user_can( 'install_themes' ) || is_multisite() ) : // @todo Upload support. ?>
			<?php endif; ?>
			<div class="customize-themes-section themes-section-{{ data.id }} control-section-content themes-php">
				<div class="theme-overlay" tabindex="0" role="dialog" aria-label="<?php esc_attr_e( 'Theme Details' ); ?>"></div>
				<div class="theme-browser rendered">
					<div class="customize-preview-header themes-filter-bar">
						<?php $this->filter_bar_content_template(); ?>
					</div>
					<?php $this->filter_drawer_content_template(); ?>
					<div class="error unexpected-error" style="display: none; ">
						<p>
							<?php
							printf(
								/* translators: %s: Support forums URL. */
								__( 'An unexpected error occurred. Something may be wrong with WordPress.org or this server&#8217;s configuration. If you continue to have problems, please try the <a href="%s">support forums</a>.' ),
								__( 'https://wordpress.org/support/forums/' )
							);
							?>
						</p>
					</div>
					<ul class="themes">
					</ul>
					<p class="no-themes"><?php _e( 'No themes found. Try a different search.' ); ?></p>
					<p class="no-themes-local">
						<?php
						printf(
							/* translators: %s: "Search WordPress.org themes" button text. */
							__( 'No themes found. Try a different search, or %s.' ),
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
	 * Renders the filter bar portion of a themes section as a JS template.
	 *
	 * The template is only rendered by PHP once, so all actions are prepared at once on the server side.
	 * The filter bar container is rendered by {@see render_template()}.
	 *
	 * @since 4.9.0
	 */
	protected function filter_bar_content_template() {
		?>
		<button type="button" class="button button-primary customize-section-back customize-themes-mobile-back"><?php _e( 'Go to theme sources' ); ?></button>
		<# if ( 'wporg' === data.action ) { #>
			<div class="search-form">
				<label for="wp-filter-search-input-{{ data.id }}"><?php _e( 'Search themes' ); ?></label>
				<div class="search-form-input">
					<input type="search" id="wp-filter-search-input-{{ data.id }}" aria-describedby="{{ data.id }}-live-search-desc" class="wp-filter-search">
					<div class="search-icon" aria-hidden="true"></div>
					<span id="{{ data.id }}-live-search-desc" class="screen-reader-text">
						<?php
						/* translators: Hidden accessibility text. */
						_e( 'The search results will be updated as you type.' );
						?>
					</span>
				</div>
			</div>
		<# } else { #>
			<div class="themes-filter-container">
				<label for="{{ data.id }}-themes-filter"><?php _e( 'Search themes' ); ?></label>
				<div class="search-form-input">
					<input type="search" id="{{ data.id }}-themes-filter" aria-describedby="{{ data.id }}-live-search-desc" class="wp-filter-search wp-filter-search-themes" />
					<div class="search-icon" aria-hidden="true"></div>
					<span id="{{ data.id }}-live-search-desc" class="screen-reader-text">
						<?php
						/* translators: Hidden accessibility text. */
						_e( 'The search results will be updated as you type.' );
						?>
					</span>
				</div>
			</div>
		<# } #>
		<div class="filter-themes-wrapper">
			<# if ( 'wporg' === data.action ) { #>
			<button type="button" class="button feature-filter-toggle">
				<span class="filter-count-0"><?php _e( 'Filter themes' ); ?></span><span class="filter-count-filters">
					<?php
					/* translators: %s: Number of filters selected. */
					printf( __( 'Filter themes (%s)' ), '<span class="theme-filter-count">0</span>' );
					?>
				</span>
			</button>
			<# } #>
			<div class="filter-themes-count">
				<span class="themes-displayed">
					<?php
					/* translators: %s: Number of themes displayed. */
					printf( __( '%s themes' ), '<span class="theme-count">0</span>' );
					?>
				</span>
			</div>
		</div>
		<?php
	}

	/**
	 * Renders the filter drawer portion of a themes section as a JS template.
	 *
	 * The filter bar container is rendered by {@see render_template()}.
	 *
	 * @since 4.9.0
	 */
	protected function filter_drawer_content_template() {
		/*
		 * @todo Use the .org API instead of the local core feature list.
		 * The .org API is currently outdated and will be reconciled when the .org themes directory is next redesigned.
		 */
		$feature_list = get_theme_feature_list( false );
		?>
		<# if ( 'wporg' === data.action ) { #>
			<div class="filter-drawer filter-details">
				<?php foreach ( $feature_list as $feature_name => $features ) : ?>
					<fieldset class="filter-group">
						<legend><?php echo esc_html( $feature_name ); ?></legend>
						<div class="filter-group-feature">
							<?php foreach ( $features as $feature => $feature_name ) : ?>
								<input type="checkbox" id="filter-id-<?php echo esc_attr( $feature ); ?>" value="<?php echo esc_attr( $feature ); ?>" />
								<label for="filter-id-<?php echo esc_attr( $feature ); ?>"><?php echo esc_html( $feature_name ); ?></label>
							<?php endforeach; ?>
						</div>
					</fieldset>
				<?php endforeach; ?>
			</div>
		<# } #>
		<?php
	}
}
