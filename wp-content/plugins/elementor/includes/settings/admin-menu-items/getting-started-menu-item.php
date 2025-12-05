<?php
namespace Elementor\Includes\Settings\AdminMenuItems;

use Elementor\Core\Admin\Menu\Interfaces\Admin_Menu_Item_With_Page;
use Elementor\Plugin;
use Elementor\Settings;
use Elementor\User;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Getting_Started_Menu_Item implements Admin_Menu_Item_With_Page {
	public function is_visible() {
		return ! Plugin::instance()->experiments->is_feature_active( 'home_screen' );
	}

	public function get_parent_slug() {
		return Settings::PAGE_ID;
	}

	public function get_label() {
		return esc_html__( 'Getting Started', 'elementor' );
	}

	public function get_page_title() {
		return esc_html__( 'Getting Started', 'elementor' );
	}

	public function get_capability() {
		return 'manage_options';
	}

	public function render() {
		if ( User::is_current_user_can_edit_post_type( 'page' ) ) {
			$create_new_label = esc_html__( 'Create Your First Page', 'elementor' );
			$create_new_cpt = 'page';
		} elseif ( User::is_current_user_can_edit_post_type( 'post' ) ) {
			$create_new_label = esc_html__( 'Create Your First Post', 'elementor' );
			$create_new_cpt = 'post';
		}

		?>
		<div class="wrap">
			<div class="e-getting-started">
				<div class="e-getting-started__box postbox">
					<div class="e-getting-started__header">
						<div class="e-getting-started__title">
							<div class="e-logo-wrapper">
								<i class="eicon-elementor"></i>
							</div>
							<?php echo esc_html__( 'Getting Started', 'elementor' ); ?>
						</div>
						<a class="e-getting-started__skip" href="<?php echo esc_url( admin_url() ); ?>">
							<i class="eicon-close" aria-hidden="true"></i>
							<span class="elementor-screen-only"><?php echo esc_html__( 'Skip', 'elementor' ); ?></span>
						</a>
					</div>
					<div class="e-getting-started__content">
						<div class="e-getting-started__content--narrow">
							<h2><?php echo esc_html__( 'Welcome to Elementor', 'elementor' ); ?></h2>
							<p><?php echo esc_html__( 'Get introduced to Elementor by watching our "Getting Started" video series. It will guide you through the steps needed to create your website. Then click to create your first page.', 'elementor' ); ?></p>
						</div>

						<div class="e-getting-started__video">
							<iframe width="620" height="350" src="https://www.youtube-nocookie.com/embed/videoseries?si=XX1RveLtiTcLKmvC&amp;list=PLZyp9H25CboFLsiad-zQOs-o-pGiv0a54;index=1" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
						</div>

						<div class="e-getting-started__actions e-getting-started__content--narrow">
							<?php if ( ! empty( $create_new_cpt ) ) : ?>
								<a href="<?php echo esc_url( Plugin::$instance->documents->get_create_new_post_url( $create_new_cpt ) ); ?>" class="button button-primary button-hero"><?php echo esc_html( $create_new_label ); ?></a>
							<?php endif; ?>

							<a href="https://go.elementor.com/wp-dash-getting-started-container/" target="_blank" class="button button-secondary button-hero"><?php echo esc_html__( 'Watch the Full Guide', 'elementor' ); ?></a>
						</div>
					</div>
				</div>
			</div>
		</div><!-- /.wrap -->
		<?php
	}
}
