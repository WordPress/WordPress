<?php
namespace Elementor\Core\Editor\Loader\V2\Templates;

use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$notice = Plugin::$instance->editor->notice_bar->get_notice();
?>

<div id="elementor-loading">
	<div class="elementor-loader-wrapper">
		<div class="elementor-loader" aria-hidden="true">
			<div class="elementor-loader-boxes">
				<div class="elementor-loader-box"></div>
				<div class="elementor-loader-box"></div>
				<div class="elementor-loader-box"></div>
				<div class="elementor-loader-box"></div>
			</div>
		</div>
		<div class="elementor-loading-title"><?php echo esc_html__( 'Loading', 'elementor' ); ?></div>
	</div>
</div>

<h1 class="elementor-screen-only"><?php printf(
	/* translators: %s: Page title. */
	esc_html__( 'Edit "%s" with Elementor', 'elementor' ),
	esc_html( get_the_title() )
); ?></h1>

<div id="elementor-editor-wrapper-v2"></div>

<div id="elementor-editor-wrapper">
	<aside id="elementor-panel" class="elementor-panel" aria-labelledby="elementor-panel-header-title"></aside>
	<main id="elementor-preview" aria-label="<?php echo esc_attr__( 'Preview', 'elementor' ); ?>">
		<div id="elementor-responsive-bar"></div>
		<div id="elementor-preview-responsive-wrapper" class="elementor-device-desktop elementor-device-rotate-portrait">
			<div id="elementor-preview-loading">
				<i class="eicon-loading eicon-animation-spin" aria-hidden="true"></i>
			</div>
			<?php if ( $notice ) {
				$notice->render();
			} // IFrame will be created here by the Javascript later. ?>
		</div>
	</main>
	<aside id="elementor-navigator" aria-labelledby="elementor-navigator__header__title"></aside>
</div>
