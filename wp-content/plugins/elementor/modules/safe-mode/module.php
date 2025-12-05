<?php
namespace Elementor\Modules\SafeMode;

use Elementor\Plugin;
use Elementor\Settings;
use Elementor\Tools;
use Elementor\TemplateLibrary\Source_Local;
use Elementor\Core\Common\Modules\Ajax\Module as Ajax;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Module extends \Elementor\Core\Base\Module {

	const OPTION_ENABLED = 'elementor_safe_mode';
	const OPTION_TOKEN = self::OPTION_ENABLED . '_token';
	const MU_PLUGIN_FILE_NAME = 'elementor-safe-mode.php';
	const DOCS_HELPED_URL = 'https://go.elementor.com/safe-mode-helped/';
	const DOCS_DIDNT_HELP_URL = 'https://go.elementor.com/safe-mode-didnt-helped/';
	const DOCS_MU_PLUGINS_URL = 'https://go.elementor.com/safe-mode-mu-plugins/';
	const DOCS_TRY_SAFE_MODE_URL = 'https://go.elementor.com/safe-mode/';

	const EDITOR_NOTICE_TIMEOUT = 30000; /* ms */

	public function get_name() {
		return 'safe-mode';
	}

	public function register_ajax_actions( Ajax $ajax ) {
		$ajax->register_ajax_action( 'enable_safe_mode', [ $this, 'ajax_enable_safe_mode' ] );
		$ajax->register_ajax_action( 'disable_safe_mode', [ $this, 'disable_safe_mode' ] );
	}

	/**
	 * @param Tools $tools_page
	 */
	public function add_admin_button( $tools_page ) {
		$tools_page->add_fields( Settings::TAB_GENERAL, 'tools', [
			'safe_mode' => [
				'label' => esc_html__( 'Safe Mode', 'elementor' ),
				'field_args' => [
					'type' => 'select',
					'std' => $this->is_enabled() ? 'global' : '',
					'options' => [
						'' => esc_html__( 'Disable', 'elementor' ),
						'global' => esc_html__( 'Enable', 'elementor' ),

					],
					'desc' => esc_html__( 'Safe Mode allows you to troubleshoot issues by only loading the editor, without loading the theme or any other plugin.', 'elementor' ),
				],
			],
		] );
	}

	public function on_update_safe_mode( $value ) {
		if ( 'yes' === $value || 'global' === $value ) {
			$this->enable_safe_mode();
		} else {
			$this->disable_safe_mode();
		}

		return $value;
	}

	/**
	 * @throws \Exception If the safe mode cannot be enabled.
	 */
	public function ajax_enable_safe_mode( $data ) {
		if ( ! current_user_can( 'install_plugins' ) ) {
			throw new \Exception( 'Access denied.' );
		}

		// It will run `$this->>update_safe_mode`.
		update_option( 'elementor_safe_mode', 'yes' );

		$document = Plugin::$instance->documents->get( $data['editor_post_id'] );

		if ( $document ) {
			return add_query_arg( 'elementor-mode', 'safe', $document->get_edit_url() );
		}

		return false;
	}

	public function enable_safe_mode() {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		WP_Filesystem();

		$this->update_allowed_plugins();

		if ( ! is_dir( WPMU_PLUGIN_DIR ) ) {
			wp_mkdir_p( WPMU_PLUGIN_DIR );
			add_option( 'elementor_safe_mode_created_mu_dir', true );
		}

		if ( ! is_dir( WPMU_PLUGIN_DIR ) ) {
			wp_die( esc_html__( 'Cannot enable Safe Mode', 'elementor' ) );
		}

		$results = copy_dir( __DIR__ . '/mu-plugin/', WPMU_PLUGIN_DIR );

		if ( is_wp_error( $results ) ) {
			return;
		}

		$token = hash( 'sha256', wp_rand() );

		// Only who own this key can use 'elementor-safe-mode'.
		update_option( self::OPTION_TOKEN, $token );

		// Save for later use.
		setcookie( self::OPTION_TOKEN, $token, time() + HOUR_IN_SECONDS, COOKIEPATH, '', is_ssl(), true );
	}

	public function disable_safe_mode() {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		$file_path = WP_CONTENT_DIR . '/mu-plugins/elementor-safe-mode.php';
		if ( file_exists( $file_path ) ) {
			unlink( $file_path );
		}

		if ( get_option( 'elementor_safe_mode_created_mu_dir' ) ) {
			// It will be removed only if it's empty and don't have other mu-plugins.
			@rmdir( WPMU_PLUGIN_DIR );
		}

		delete_option( 'elementor_safe_mode' );
		delete_option( 'elementor_safe_mode_allowed_plugins' );
		delete_option( 'theme_mods_elementor-safe' );
		delete_option( 'elementor_safe_mode_created_mu_dir' );

		delete_option( self::OPTION_TOKEN );
		setcookie( self::OPTION_TOKEN, '', 1, '', '', is_ssl(), true );
	}

	public function filter_preview_url( $url ) {
		return add_query_arg( 'elementor-mode', 'safe', $url );
	}

	public function filter_template() {
		return ELEMENTOR_PATH . 'modules/page-templates/templates/canvas.php';
	}

	public function print_safe_mode_css() {
		?>
		<style>
			.elementor-safe-mode-toast {
				position: absolute;
				z-index: 10000; /* Over the loading layer */
				inset-block-end: 10px;
				inset-inline-end: 10px;
				width: 400px;
				line-height: 30px;
				display: flex;
				flex-direction: column;
				gap: 20px;
				color: var(--e-a-color-txt);
				background: var(--e-a-bg-default);
				padding: 20px 25px 25px;
				box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
				border-radius: 5px;
				font-family: var(--e-a-font-family);
			}

			#elementor-try-safe-mode {
				display: none;
			}

			.elementor-safe-mode-toast .elementor-toast-content {
				font-size: 13px;
				line-height: 22px;
			}

			.elementor-safe-mode-toast .elementor-toast-content a {
				color: var(--e-a-color-info);
			}

			.elementor-safe-mode-toast .elementor-toast-content hr {
				margin: 15px auto;
				border: 0 none;
				border-block-start: var(--e-a-border);
			}

			.elementor-safe-mode-toast header {
				display: flex;
				align-items: center;
				gap: 10px;
			}

			.elementor-safe-mode-toast header i {
				font-size: 25px;
				color: var(--e-a-color-warning);
			}

			.elementor-safe-mode-toast header h2 {
				flex-grow: 1;
				font-size: 18px;
			}

			.elementor-safe-mode-list {
				display: flex;
				flex-direction: column;
				gap: 10px;
			}

			.elementor-safe-mode-list-item {
				margin-inline-start: 15px;
				list-style: outside;
			}

			.elementor-safe-mode-list-item-content {
				font-style: italic;
				color: var(--e-a-color-txt);
			}

			.elementor-safe-mode-list-item-title {
				font-weight: 500;
			}

			.elementor-safe-mode-mu-plugins {
				background-color: var(--e-a-bg-hover);
				color: var(--e-a-color-txt-hover);
				margin-block-start: 20px;
				padding: 10px 15px;
			}
		</style>
		<?php
	}

	public function print_safe_mode_notice() {
		$this->print_safe_mode_css()
		?>
		<div class="elementor-safe-mode-toast" id="elementor-safe-mode-message">
			<header>
				<i class="eicon-warning" aria-hidden="true"></i>
				<h2><?php echo esc_html__( 'Safe Mode ON', 'elementor' ); ?></h2>
				<a class="elementor-button elementor-safe-mode-button elementor-disable-safe-mode" target="_blank" href="<?php echo esc_url( $this->get_admin_page_url() ); ?>">
					<?php echo esc_html__( 'Disable Safe Mode', 'elementor' ); ?>
				</a>
			</header>

			<div class="elementor-toast-content">
				<ul class="elementor-safe-mode-list">
					<li class="elementor-safe-mode-list-item">
						<div class="elementor-safe-mode-list-item-title"><?php echo esc_html__( 'Editor successfully loaded?', 'elementor' ); ?></div>
						<div class="elementor-safe-mode-list-item-content">
							<?php
								echo esc_html__( 'The issue was probably caused by one of your plugins or theme.', 'elementor' );
								echo ' ';

								printf(
									/* translators: %1$s Link open tag, %2$s: Link close tag. */
									esc_html__( '%1$sClick here%2$s to troubleshoot', 'elementor' ),
									'<a href="' . self::DOCS_HELPED_URL . '" target="_blank">', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									'</a>'
								);
							?>
						</div>
					</li>
					<li class="elementor-safe-mode-list-item">
						<div class="elementor-safe-mode-list-item-title"><?php echo esc_html__( 'Still experiencing issues?', 'elementor' ); ?></div>
						<div class="elementor-safe-mode-list-item-content">
							<?php
								printf(
									/* translators: %1$s Link open tag, %2$s: Link close tag. */
									esc_html__( '%1$sClick here%2$s to troubleshoot', 'elementor' ),
									'<a href="' . self::DOCS_DIDNT_HELP_URL . '" target="_blank">', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									'</a>'
								);
							?>
						</div>
					</li>
				</ul>
				<?php
				$mu_plugins = wp_get_mu_plugins();

				if ( 1 < count( $mu_plugins ) ) : ?>
					<div class="elementor-safe-mode-mu-plugins">
						<?php
						printf(
							/* translators: %1$s Link open tag, %2$s: Link close tag. */
							esc_html__( 'Please note! We couldn\'t deactivate all of your plugins on Safe Mode. Please %1$sread more%2$s about this issue', 'elementor' ),
							'<a href="' . self::DOCS_MU_PLUGINS_URL . '" target="_blank">', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							'</a>'
						);
						?>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<script>
			var ElementorSafeMode = function() {
				var attachEvents = function() {
					jQuery( '.elementor-disable-safe-mode' ).on( 'click', function( e ) {
						if ( ! elementorCommon || ! elementorCommon.ajax ) {
							return;
						}

						e.preventDefault();

						elementorCommon.ajax.addRequest(
							'disable_safe_mode', {
								success: function() {
									if ( -1 === location.href.indexOf( 'elementor-mode=safe' ) ) {
										location.reload();
									} else {
										// Need to remove the URL from browser history.
										location.replace( location.href.replace( '&elementor-mode=safe', '' ) );
									}
								},
								error: function() {
									alert( 'An error occurred.' );
								},
							},
							true
						);
					} );
				};

				var init = function() {
					attachEvents();
				};

				init();
			};

			new ElementorSafeMode();
		</script>
		<?php
	}

	public function print_try_safe_mode() {
		if ( ! $this->is_allowed_post_type() ) {
			return;
		}

		$this->print_safe_mode_css();
		?>
		<div class="elementor-safe-mode-toast" id="elementor-try-safe-mode">
		<?php if ( current_user_can( 'install_plugins' ) ) : ?>
			<header>
				<i class="eicon-warning" aria-hidden="true"></i>
				<h2><?php echo esc_html__( 'Can\'t Edit?', 'elementor' ); ?></h2>
				<a class="elementor-button e-primary elementor-safe-mode-button elementor-enable-safe-mode" target="_blank" href="<?php echo esc_url( $this->get_admin_page_url() ); ?>">
					<?php echo esc_html__( 'Enable Safe Mode', 'elementor' ); ?>
				</a>
			</header>
			<div class="elementor-toast-content">
				<?php echo esc_html__( 'Having problems loading Elementor? Please enable Safe Mode to troubleshoot.', 'elementor' ); ?>
				<a href="<?php Utils::print_unescaped_internal_string( self::DOCS_TRY_SAFE_MODE_URL ); ?>" target="_blank"><?php echo esc_html__( 'Learn More', 'elementor' ); ?></a>
			</div>
		<?php else : ?>
			<header>
				<i class="eicon-warning" aria-hidden="true"></i>
				<h2><?php echo esc_html__( 'Can\'t Edit?', 'elementor' ); ?></h2>
			</header>
			<div class="elementor-toast-content">
				<?php echo esc_html__( 'If you are experiencing a loading issue, contact your site administrator to troubleshoot the problem using Safe Mode.', 'elementor' ); ?>
				<a href="<?php Utils::print_unescaped_internal_string( self::DOCS_TRY_SAFE_MODE_URL ); ?>" target="_blank"><?php echo esc_html__( 'Learn More', 'elementor' ); ?></a>
			</div>
		<?php endif; ?>
		</div>

		<script>
			var ElementorTrySafeMode = function() {
				var attachEvents = function() {
					jQuery( '.elementor-enable-safe-mode' ).on( 'click', function( e ) {
						if ( ! elementorCommon || ! elementorCommon.ajax ) {
							return;
						}

						e.preventDefault();

						elementorCommon.ajax.addRequest(
							'enable_safe_mode', {
								data: {
									editor_post_id: '<?php
										// PHPCS - the method get_post_id is safe.
										echo Plugin::$instance->editor->get_post_id(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									?>',
								},
								success: function( url ) {
									location.assign( url );
								},
								error: function() {
									alert( 'An error occurred.' );
								},
							},
							true
						);
					} );
				};

				var isElementorLoaded = function() {
					if ( 'undefined' === typeof elementor ) {
						return false;
					}

					if ( ! elementor.loaded ) {
						return false;
					}

					if ( jQuery( '#elementor-loading' ).is( ':visible' ) ) {
						return false;
					}

					return true;
				};

				var handleTrySafeModeNotice = function() {
					var $notice = jQuery( '#elementor-try-safe-mode' );

					if ( isElementorLoaded() ) {
						$notice.remove();
						return;
					}

					if ( ! $notice.data( 'visible' ) ) {
						$notice.attr( 'style', 'display: flex;' );
					}

					// Re-check after 500ms.
					setTimeout( handleTrySafeModeNotice, 500 );
				};

				var init = function() {
					setTimeout( handleTrySafeModeNotice, <?php Utils::print_unescaped_internal_string( self::EDITOR_NOTICE_TIMEOUT ); ?> );

					attachEvents();
				};

				init();
			};

			new ElementorTrySafeMode();
		</script>

		<?php
	}

	public function run_safe_mode() {
		remove_action( 'elementor/editor/footer', [ $this, 'print_try_safe_mode' ] );

		// Avoid notices like for comment.php.
		add_filter( 'deprecated_file_trigger_error', '__return_false' );

		add_filter( 'template_include', [ $this, 'filter_template' ], 999 );
		add_filter( 'elementor/document/urls/preview', [ $this, 'filter_preview_url' ] );
		add_action( 'elementor/editor/footer', [ $this, 'print_safe_mode_notice' ] );
		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'register_scripts' ], 11 /* After Common Scripts */ );
	}

	public function register_scripts() {
		wp_add_inline_script( 'elementor-common', 'elementorCommon.ajax.addRequestConstant( "elementor-mode", "safe" );' );
	}

	private function is_enabled() {
		return get_option( self::OPTION_ENABLED, '' );
	}

	private function get_admin_page_url() {
		// A fallback URL if the Js doesn't work.
		return Tools::get_url();
	}

	public function plugin_action_links( $actions ) {
		$actions['disable'] = '<a href="' . self::get_admin_page_url() . '">' . esc_html__( 'Disable Safe Mode', 'elementor' ) . '</a>';

		return $actions;
	}

	public function on_deactivated_plugin( $plugin ) {
		if ( ELEMENTOR_PLUGIN_BASE === $plugin ) {
			$this->disable_safe_mode();
			return;
		}

		$allowed_plugins = get_option( 'elementor_safe_mode_allowed_plugins', [] );
		$plugin_key = array_search( $plugin, $allowed_plugins, true );

		if ( $plugin_key ) {
			unset( $allowed_plugins[ $plugin_key ] );
			update_option( 'elementor_safe_mode_allowed_plugins', $allowed_plugins );
		}
	}

	public function update_allowed_plugins() {
		$allowed_plugins = [
			'elementor' => ELEMENTOR_PLUGIN_BASE,
		];

		if ( defined( 'ELEMENTOR_PRO_PLUGIN_BASE' ) ) {
			$allowed_plugins['elementor_pro'] = ELEMENTOR_PRO_PLUGIN_BASE;
		}

		if ( defined( 'WC_PLUGIN_BASENAME' ) ) {
			$allowed_plugins['woocommerce'] = WC_PLUGIN_BASENAME;
		}

		update_option( 'elementor_safe_mode_allowed_plugins', $allowed_plugins );
	}

	public function __construct() {
		if ( current_user_can( 'install_plugins' ) ) {
			add_action( 'elementor/admin/after_create_settings/elementor-tools', [ $this, 'add_admin_button' ] );
		}

		add_action( 'elementor/ajax/register_actions', [ $this, 'register_ajax_actions' ] );

		$plugin_file = self::MU_PLUGIN_FILE_NAME;
		add_filter( "plugin_action_links_{$plugin_file}", [ $this, 'plugin_action_links' ] );

		// Use pre_update, in order to catch cases that $value === $old_value and it not updated.
		add_filter( 'pre_update_option_elementor_safe_mode', [ $this, 'on_update_safe_mode' ], 10, 2 );

		add_action( 'elementor/safe_mode/init', [ $this, 'run_safe_mode' ] );
		add_action( 'elementor/editor/footer', [ $this, 'print_try_safe_mode' ] );

		if ( $this->is_enabled() ) {
			add_action( 'activated_plugin', [ $this, 'update_allowed_plugins' ] );
			add_action( 'deactivated_plugin', [ $this, 'on_deactivated_plugin' ] );
		}
	}

	private function is_allowed_post_type() {
		$allowed_post_types = [
			'post',
			'page',
			'product',
			Source_Local::CPT,
		];

		$current_post_type = get_post_type( Plugin::$instance->editor->get_post_id() );

		return in_array( $current_post_type, $allowed_post_types );
	}
}
