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
	 * @var string
	 */
	public $type = 'theme';

	/**
	 * Theme object.
	 *
	 * @since 4.2.0
	 * @var WP_Theme
	 */
	public $theme;

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 * @since 4.2.0
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
	 */
	public function render_content() {}

	/**
	 * Render a JS template for theme display.
	 *
	 * @since 4.2.0
	 */
	public function content_template() {
		/* translators: %s: Theme name. */
		$details_label = sprintf( __( 'Details for theme: %s' ), '{{ data.theme.name }}' );
		/* translators: %s: Theme name. */
		$customize_label = sprintf( __( 'Customize theme: %s' ), '{{ data.theme.name }}' );
		/* translators: %s: Theme name. */
		$preview_label = sprintf( __( 'Live preview theme: %s' ), '{{ data.theme.name }}' );
		/* translators: %s: Theme name. */
		$install_label = sprintf( __( 'Install and preview theme: %s' ), '{{ data.theme.name }}' );
		?>
		<# if ( data.theme.active ) { #>
			<div class="theme active" tabindex="0" aria-describedby="{{ data.section }}-{{ data.theme.id }}-action">
		<# } else { #>
			<div class="theme" tabindex="0" aria-describedby="{{ data.section }}-{{ data.theme.id }}-action">
		<# } #>

			<# if ( data.theme.screenshot && data.theme.screenshot[0] ) { #>
				<div class="theme-screenshot">
					<img data-src="{{ data.theme.screenshot[0] }}" alt="" />
				</div>
			<# } else { #>
				<div class="theme-screenshot blank"></div>
			<# } #>

			<span class="more-details theme-details" id="{{ data.section }}-{{ data.theme.id }}-action" aria-label="<?php echo esc_attr( $details_label ); ?>"><?php _e( 'Theme Details' ); ?></span>

			<div class="theme-author">
			<?php
				/* translators: Theme author name. */
				printf( _x( 'By %s', 'theme author' ), '{{ data.theme.author }}' );
			?>
			</div>

			<# if ( 'installed' === data.theme.type && data.theme.hasUpdate ) { #>
				<# if ( data.theme.updateResponse.compatibleWP && data.theme.updateResponse.compatiblePHP ) { #>
					<div class="update-message notice inline notice-warning notice-alt" data-slug="{{ data.theme.id }}">
						<p>
							<?php
							if ( is_multisite() ) {
								_e( 'New version available.' );
							} else {
								printf(
									/* translators: %s: "Update now" button. */
									__( 'New version available. %s' ),
									'<button class="button-link update-theme" type="button">' . __( 'Update now' ) . '</button>'
								);
							}
							?>
						</p>
					</div>
				<# } else { #>
					<div class="update-message notice inline notice-error notice-alt" data-slug="{{ data.theme.id }}">
						<p>
							<# if ( ! data.theme.updateResponse.compatibleWP && ! data.theme.updateResponse.compatiblePHP ) { #>
								<?php
								printf(
									/* translators: %s: Theme name. */
									__( 'There is a new version of %s available, but it doesn&#8217;t work with your versions of WordPress and PHP.' ),
									'{{{ data.theme.name }}}'
								);
								if ( current_user_can( 'update_core' ) && current_user_can( 'update_php' ) ) {
									printf(
										/* translators: 1: URL to WordPress Updates screen, 2: URL to Update PHP page. */
										' ' . __( '<a href="%1$s">Please update WordPress</a>, and then <a href="%2$s">learn more about updating PHP</a>.' ),
										self_admin_url( 'update-core.php' ),
										esc_url( wp_get_update_php_url() )
									);
									wp_update_php_annotation( '</p><p><em>', '</em>' );
								} elseif ( current_user_can( 'update_core' ) ) {
									printf(
										/* translators: %s: URL to WordPress Updates screen. */
										' ' . __( '<a href="%s">Please update WordPress</a>.' ),
										self_admin_url( 'update-core.php' )
									);
								} elseif ( current_user_can( 'update_php' ) ) {
									printf(
										/* translators: %s: URL to Update PHP page. */
										' ' . __( '<a href="%s">Learn more about updating PHP</a>.' ),
										esc_url( wp_get_update_php_url() )
									);
									wp_update_php_annotation( '</p><p><em>', '</em>' );
								}
								?>
							<# } else if ( ! data.theme.updateResponse.compatibleWP ) { #>
								<?php
								printf(
									/* translators: %s: Theme name. */
									__( 'There is a new version of %s available, but it doesn&#8217;t work with your version of WordPress.' ),
									'{{{ data.theme.name }}}'
								);
								if ( current_user_can( 'update_core' ) ) {
									printf(
										/* translators: %s: URL to WordPress Updates screen. */
										' ' . __( '<a href="%s">Please update WordPress</a>.' ),
										self_admin_url( 'update-core.php' )
									);
								}
								?>
							<# } else if ( ! data.theme.updateResponse.compatiblePHP ) { #>
								<?php
								printf(
									/* translators: %s: Theme name. */
									__( 'There is a new version of %s available, but it doesn&#8217;t work with your version of PHP.' ),
									'{{{ data.theme.name }}}'
								);
								if ( current_user_can( 'update_php' ) ) {
									printf(
										/* translators: %s: URL to Update PHP page. */
										' ' . __( '<a href="%s">Learn more about updating PHP</a>.' ),
										esc_url( wp_get_update_php_url() )
									);
									wp_update_php_annotation( '</p><p><em>', '</em>' );
								}
								?>
							<# } #>
						</p>
					</div>
				<# } #>
			<# } #>

			<# if ( ! data.theme.compatibleWP || ! data.theme.compatiblePHP ) { #>
				<div class="notice notice-error notice-alt"><p>
					<# if ( ! data.theme.compatibleWP && ! data.theme.compatiblePHP ) { #>
						<?php
						_e( 'This theme doesn&#8217;t work with your versions of WordPress and PHP.' );
						if ( current_user_can( 'update_core' ) && current_user_can( 'update_php' ) ) {
							printf(
								/* translators: 1: URL to WordPress Updates screen, 2: URL to Update PHP page. */
								' ' . __( '<a href="%1$s">Please update WordPress</a>, and then <a href="%2$s">learn more about updating PHP</a>.' ),
								self_admin_url( 'update-core.php' ),
								esc_url( wp_get_update_php_url() )
							);
							wp_update_php_annotation( '</p><p><em>', '</em>' );
						} elseif ( current_user_can( 'update_core' ) ) {
							printf(
								/* translators: %s: URL to WordPress Updates screen. */
								' ' . __( '<a href="%s">Please update WordPress</a>.' ),
								self_admin_url( 'update-core.php' )
							);
						} elseif ( current_user_can( 'update_php' ) ) {
							printf(
								/* translators: %s: URL to Update PHP page. */
								' ' . __( '<a href="%s">Learn more about updating PHP</a>.' ),
								esc_url( wp_get_update_php_url() )
							);
							wp_update_php_annotation( '</p><p><em>', '</em>' );
						}
						?>
					<# } else if ( ! data.theme.compatibleWP ) { #>
						<?php
						_e( 'This theme doesn&#8217;t work with your version of WordPress.' );
						if ( current_user_can( 'update_core' ) ) {
							printf(
								/* translators: %s: URL to WordPress Updates screen. */
								' ' . __( '<a href="%s">Please update WordPress</a>.' ),
								self_admin_url( 'update-core.php' )
							);
						}
						?>
					<# } else if ( ! data.theme.compatiblePHP ) { #>
						<?php
						_e( 'This theme doesn&#8217;t work with your version of PHP.' );
						if ( current_user_can( 'update_php' ) ) {
							printf(
								/* translators: %s: URL to Update PHP page. */
								' ' . __( '<a href="%s">Learn more about updating PHP</a>.' ),
								esc_url( wp_get_update_php_url() )
							);
							wp_update_php_annotation( '</p><p><em>', '</em>' );
						}
						?>
					<# } #>
				</p></div>
			<# } #>

			<# if ( data.theme.active ) { #>
				<div class="theme-id-container">
					<h3 class="theme-name" id="{{ data.section }}-{{ data.theme.id }}-name">
						<span><?php _ex( 'Previewing:', 'theme' ); ?></span> {{ data.theme.name }}
					</h3>
					<div class="theme-actions">
						<button type="button" class="button button-primary customize-theme" aria-label="<?php echo esc_attr( $customize_label ); ?>"><?php _e( 'Customize' ); ?></button>
					</div>
				</div>
				<div class="notice notice-success notice-alt"><p><?php _ex( 'Installed', 'theme' ); ?></p></div>
			<# } else if ( 'installed' === data.theme.type ) { #>
				<# if ( data.theme.blockTheme ) { #>
					<div class="theme-id-container">
						<h3 class="theme-name" id="{{ data.section }}-{{ data.theme.id }}-name">{{ data.theme.name }}</h3>
						<div class="theme-actions">
							<# if ( data.theme.actions.activate ) { #>
								<?php
									/* translators: %s: Theme name. */
									$aria_label = sprintf( _x( 'Activate %s', 'theme' ), '{{ data.name }}' );
								?>
								<a href="{{{ data.theme.actions.activate }}}" class="button button-primary activate" aria-label="<?php echo esc_attr( $aria_label ); ?>"><?php _e( 'Activate' ); ?></a>
							<# } #>
						</div>
					</div>
					<div class="notice notice-error notice-alt"><p>
					<?php
						_e( 'This theme doesn\'t support Customizer.' );
					?>
					<# if ( data.theme.actions.activate ) { #>
						<?php
							echo ' ';
							printf(
								/* translators: %s: URL to the themes page (also it activates the theme). */
								__( 'However, you can still <a href="%s">activate this theme</a>, and use the Site Editor to customize it.' ),
								'{{{ data.theme.actions.activate }}}'
							);
						?>
					<# } #>
					</p></div>
				<# } else { #>
					<div class="theme-id-container">
						<h3 class="theme-name" id="{{ data.section }}-{{ data.theme.id }}-name">{{ data.theme.name }}</h3>
						<div class="theme-actions">
							<# if ( data.theme.compatibleWP && data.theme.compatiblePHP ) { #>
								<button type="button" class="button button-primary preview-theme" aria-label="<?php echo esc_attr( $preview_label ); ?>" data-slug="{{ data.theme.id }}"><?php _e( 'Live Preview' ); ?></button>
							<# } else { #>
								<button type="button" class="button button-primary disabled" aria-label="<?php echo esc_attr( $preview_label ); ?>"><?php _e( 'Live Preview' ); ?></button>
							<# } #>
						</div>
					</div>
					<div class="notice notice-success notice-alt"><p><?php _ex( 'Installed', 'theme' ); ?></p></div>
				<# } #>
			<# } else { #>
				<div class="theme-id-container">
					<h3 class="theme-name" id="{{ data.section }}-{{ data.theme.id }}-name">{{ data.theme.name }}</h3>
					<div class="theme-actions">
						<# if ( data.theme.compatibleWP && data.theme.compatiblePHP ) { #>
							<button type="button" class="button button-primary theme-install preview" aria-label="<?php echo esc_attr( $install_label ); ?>" data-slug="{{ data.theme.id }}" data-name="{{ data.theme.name }}"><?php _e( 'Install &amp; Preview' ); ?></button>
						<# } else { #>
							<button type="button" class="button button-primary disabled" aria-label="<?php echo esc_attr( $install_label ); ?>" disabled><?php _e( 'Install &amp; Preview' ); ?></button>
						<# } #>
					</div>
				</div>
			<# } #>
		</div>
		<?php
	}
}
