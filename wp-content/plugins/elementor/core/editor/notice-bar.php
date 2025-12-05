<?php
namespace Elementor\Core\Editor;

use Elementor\Core\Base\Base_Object;
use Elementor\Core\Common\Modules\Ajax\Module as Ajax;
use Elementor\Core\Utils\Promotions\Filtered_Promotions_Manager;
use Elementor\Plugin;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Notice_Bar extends Base_Object {

	protected function get_init_settings() {
		if ( Plugin::$instance->get_install_time() > strtotime( '-1 days' ) ) {
			return [];
		}

		$upgrade_url = 'https://go.elementor.com/go-pro-editor-notice-bar/';

		$config = [
			'description' => $this->get_description(),
			'upgrade_text' => $this->get_upgrade_text(),
			'upgrade_url' => $upgrade_url,
		];

		$config = Filtered_Promotions_Manager::get_filtered_promotion_data( $config, 'elementor/notice-bar/custom_promotion', 'upgrade_url' );

		return [
			'muted_period' => 14,
			'option_key' => '_elementor_editor_upgrade_notice_dismissed',
			'message' => $config['description'] ?? $this->get_description(),
			'action_title' => $config['upgrade_text'] ?? $this->get_upgrade_text(),
			'action_url' => $config['upgrade_url'] ?? $upgrade_url,
		];
	}

	public function get_upgrade_text() {
		return esc_html__( 'Upgrade Now', 'elementor' );
	}

	public function get_description() {
		return esc_html__( 'Unleash the full power of Elementor\'s features and web creation tools.', 'elementor' );
	}

	final public function get_notice() {
		if ( ! $this->has_access_to_notice() ) {
			return null;
		}

		$settings = $this->get_settings();

		if ( empty( $settings['option_key'] ) ) {
			return null;
		}

		$dismissed_time = get_option( $settings['option_key'] );

		if ( $dismissed_time ) {
			if ( $dismissed_time > strtotime( '-' . $settings['muted_period'] . ' days' ) ) {
				return null;
			}

			$this->set_notice_dismissed();
		}

		return $this;
	}

	protected function render_action( $type ) {
		$settings = $this->get_settings();

		// TODO: Make the API better. The bad naming is because of BC.
		$prefix_map = [
			'primary' => '',
			'secondary' => 'secondary_',
		];

		$prefix = $prefix_map[ $type ];

		$action_title = "{$prefix}action_title";
		$action_url = "{$prefix}action_url";
		$action_message = "{$prefix}message";
		$action_target = "{$prefix}action_target";

		if ( empty( $settings[ $action_title ] ) || empty( $settings[ $action_url ] ) || empty( $settings[ $action_message ] ) ) {
			return;

		}

		?>
		<div class="e-notice-bar__message <?php echo esc_attr( "e-notice-bar__{$type}_message" ); ?>">
			<?php Utils::print_unescaped_internal_string( sprintf( $settings[ $action_message ], $settings[ $action_url ] ) ); ?>
		</div>

		<div class="e-notice-bar__action <?php echo esc_attr( "e-notice-bar__{$type}_action" ); ?>">
			<a href="<?php Utils::print_unescaped_internal_string( $settings[ $action_url ] ); ?>"
				target="<?php echo empty( $settings[ $action_target ] ) ? '_blank' : esc_attr( $settings[ $action_target ] ); ?>"
			>
				<?php Utils::print_unescaped_internal_string( $settings[ $action_title ] ); ?>
			</a>
		</div>
		<?php
	}

	public function render() {
		$settings = $this->get_settings();

		$icon = empty( $settings['icon'] )
			? 'eicon-elementor-square'
			: esc_attr( $settings['icon'] );

		?>
		<div id="e-notice-bar" class="e-notice-bar">
			<i class="e-notice-bar__icon <?php echo esc_attr( $icon ); ?>"></i>

			<?php
			$this->render_action( 'primary' );
			$this->render_action( 'secondary' );
			?>

			<i id="e-notice-bar__close" class="e-notice-bar__close eicon-close"></i>
		</div>
		<?php
	}

	public function __construct() {
		add_action( 'elementor/ajax/register_actions', [ $this, 'register_ajax_actions' ] );
	}

	public function set_notice_dismissed() {
		if ( ! $this->has_access_to_notice() ) {
			throw new \Exception( 'Access denied' );
		}

		update_option( $this->get_settings( 'option_key' ), time() );
	}

	public function register_ajax_actions( Ajax $ajax ) {
		$ajax->register_ajax_action( 'notice_bar_dismiss', [ $this, 'set_notice_dismissed' ] );
	}

	private function has_access_to_notice() {
		return current_user_can( 'manage_options' );
	}
}
