<?php
namespace Elementor\Modules\Apps;

use Elementor\Core\Isolation\Wordpress_Adapter;
use Elementor\Core\Isolation\Plugin_Status_Adapter;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Admin_Apps_Page {

	const APPS_URL = 'https://assets.elementor.com/apps/v1/apps.json';

	private static ?Wordpress_Adapter $wordpress_adapter = null;

	private static ?Plugin_Status_Adapter $plugin_status_adapter = null;

	public static function render() {
		?>
		<div class="wrap e-a-apps">

			<div class="e-a-page-title">
				<h2><?php echo esc_html__( 'Popular Add-ons, New Possibilities.', 'elementor' ); ?></h2>
				<p><?php echo esc_html__( 'Boost your web-creation process with add-ons, plugins, and more tools specially selected to unleash your creativity, increase productivity, and enhance your Elementor-powered website.', 'elementor' ); ?>*<br>
					<a href="https://go.elementor.com/wp-dash-apps-about-apps-page/" target="_blank"><?php echo esc_html__( 'Learn more about this page.', 'elementor' ); ?></a>
				</p>
			</div>

			<div class="e-a-list">
				<?php self::render_plugins_list(); ?>
			</div>
			<div class="e-a-page-footer">
				<p>*<?php echo esc_html__( 'Please note that certain tools and services on this page are developed by third-party companies and are not part of Elementor\'s suite of products or support. Before using them, we recommend independently evaluating them. Additionally, when clicking on their action buttons, you may be redirected to an external website.', 'elementor' ); ?></p>
			</div>
		</div>
		<?php
	}

	private static function render_plugins_list() {
		$plugins = self::get_plugins();

		foreach ( $plugins as $plugin ) {
			self::render_plugin_item( $plugin );
		}
	}

	private static function get_plugins(): array {
		if ( ! self::$wordpress_adapter ) {
			self::$wordpress_adapter = new Wordpress_Adapter();
		}

		if ( ! self::$plugin_status_adapter ) {
			self::$plugin_status_adapter = new Plugin_Status_Adapter( self::$wordpress_adapter );
		}

		$apps = static::get_remote_apps();

		return static::filter_apps( $apps );
	}

	private static function get_remote_apps() {
		$apps = wp_remote_get( static::APPS_URL );

		if ( is_wp_error( $apps ) ) {
			return [];
		}

		$apps = json_decode( wp_remote_retrieve_body( $apps ), true );

		if ( empty( $apps['apps'] ) || ! is_array( $apps['apps'] ) ) {
			return [];
		}

		return $apps['apps'];
	}

	private static function filter_apps( $apps ) {
		$filtered_apps = [];

		foreach ( $apps as $app ) {
			if ( static::is_wporg_app( $app ) ) {
				$app = static::filter_wporg_app( $app );
			}

			if ( static::is_ecom_app( $app ) ) {
				$app = static::filter_ecom_app( $app );
			}

			if ( empty( $app ) ) {
				continue;
			}

			$filtered_apps[] = $app;
		}

		return $filtered_apps;
	}

	private static function is_wporg_app( $app ) {
		return isset( $app['type'] ) && 'wporg' === $app['type'];
	}

	private static function filter_wporg_app( $app ) {
		if ( self::$wordpress_adapter->is_plugin_active( $app['file_path'] ) ) {
			return null;
		}

		if ( self::$plugin_status_adapter->is_plugin_installed( $app['file_path'] ) ) {
			if ( current_user_can( 'activate_plugins' ) ) {
				$app['action_label'] = esc_html__( 'Activate', 'elementor' );
				$app['action_url'] = self::$plugin_status_adapter->get_activate_plugin_url( $app['file_path'] );
			} else {
				$app['action_label'] = esc_html__( 'Cannot Activate', 'elementor' );
				$app['action_url'] = '#';
			}
		} elseif ( current_user_can( 'install_plugins' ) ) {
				$app['action_label'] = esc_html__( 'Install', 'elementor' );
				$app['action_url'] = self::$plugin_status_adapter->get_install_plugin_url( $app['file_path'] );
		} else {
			$app['action_label'] = esc_html__( 'Cannot Install', 'elementor' );
			$app['action_url'] = '#';
		}

		return $app;
	}

	private static function is_ecom_app( $app ) {
		return isset( $app['type'] ) && 'ecom' === $app['type'];
	}

	private static function filter_ecom_app( $app ) {
		if ( self::$wordpress_adapter->is_plugin_active( $app['file_path'] ) ) {
			return null;
		}

		if ( ! self::$plugin_status_adapter->is_plugin_installed( $app['file_path'] ) ) {
			return $app;
		}

		if ( current_user_can( 'activate_plugins' ) ) {
			$app['action_label'] = esc_html__( 'Activate', 'elementor' );
			$app['action_url'] = self::$plugin_status_adapter->get_activate_plugin_url( $app['file_path'] );
		} else {
			$app['action_label'] = esc_html__( 'Cannot Activate', 'elementor' );
			$app['action_url'] = '#';
		}

		$app['target'] = '_self';

		return $app;
	}

	private static function get_images_url() {
		return ELEMENTOR_URL . 'modules/apps/images/';
	}

	private static function is_elementor_pro_installed() {
		return defined( 'ELEMENTOR_PRO_VERSION' );
	}

	private static function render_plugin_item( $plugin ) {
		?>
		<div class="e-a-item"<?php echo ! empty( $plugin['file_path'] ) ? ' data-plugin="' . esc_attr( $plugin['file_path'] ) . '"' : ''; ?>>
			<div class="e-a-heading">
				<img class="e-a-img" src="<?php echo esc_url( $plugin['image'] ); ?>" alt="<?php echo esc_attr( $plugin['name'] ); ?>">
				<?php if ( ! empty( $plugin['badge'] ) ) : ?>
					<span class="e-a-badge"><?php echo esc_html( $plugin['badge'] ); ?></span>
				<?php endif; ?>
			</div>
			<h3 class="e-a-title"><?php echo esc_html( $plugin['name'] ); ?></h3>
			<p class="e-a-author"><?php esc_html_e( 'By', 'elementor' ); ?> <a href="<?php echo esc_url( $plugin['author_url'] ); ?>" target="_blank"><?php echo esc_html( $plugin['author'] ); ?></a></p>
			<div class="e-a-desc">
				<p><?php echo esc_html( $plugin['description'] ); ?></p>
				<?php if ( ! empty( $plugin['offering'] ) ) : ?>
					<p class="e-a-offering"><?php echo esc_html( $plugin['offering'] ); ?></p>
				<?php endif; ?>
			</div>

			<p class="e-a-actions">
				<?php if ( ! empty( $plugin['learn_more_url'] ) ) : ?>
					<a class="e-a-learn-more" href="<?php echo esc_url( $plugin['learn_more_url'] ); ?>" target="_blank"><?php echo esc_html__( 'Learn More', 'elementor' ); ?></a>
				<?php endif; ?>
				<a href="<?php echo esc_url( $plugin['action_url'] ); ?>" class="e-btn e-accent" target="<?php echo isset( $plugin['target'] ) ? esc_attr( $plugin['target'] ) : '_blank'; ?>"><?php echo esc_html( $plugin['action_label'] ); ?></a>
			</p>
		</div>
		<?php
	}
}
