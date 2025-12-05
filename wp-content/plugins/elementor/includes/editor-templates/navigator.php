<?php
namespace Elementor;

use Elementor\Utils;
use Elementor\Core\Utils\Promotions\Filtered_Promotions_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$has_pro = Utils::has_pro();
$elements_list_class = '';

if ( ! $has_pro ) {
	$promotion_data = [
		'text' => esc_html__( 'Access all Pro widgets', 'elementor' ),
		'url_label' => esc_html__( 'Upgrade Now', 'elementor' ),
		'url' => 'https://go.elementor.com/go-pro-structure-panel/',
	];

	$promotion_data = Filtered_Promotions_Manager::get_filtered_promotion_data(
		$promotion_data,
		'elementor/navigator/custom_promotion',
		'url'
	);
	$elements_list_class = 'elementor-navigator-list__promotion';
}

?>
<script type="text/template" id="tmpl-elementor-navigator">
	<div id="elementor-navigator__header">
		<button id="elementor-navigator__toggle-all" data-elementor-action="expand" aria-label="<?php echo esc_attr__( 'Expand all elements', 'elementor' ); ?>">
			<i class="eicon-expand" aria-hidden="true"></i>
		</button>
		<h2 id="elementor-navigator__header__title"><?php echo esc_html__( 'Structure', 'elementor' ); ?></h2>
		<button id="elementor-navigator__close" aria-label="<?php echo esc_attr__( 'Close structure', 'elementor' ); ?>">
			<i class="eicon-close" aria-hidden="true"></i>
		</button>
	</div>
	<div id="elementor-navigator__elements"
		<?php if ( ! empty( $elements_list_class ) ) : ?>
			class="<?php echo esc_attr( $elements_list_class ); ?>"
	<?php endif; ?>
	>
	</div>
	<div id="elementor-navigator__footer">
		<?php if ( ! $has_pro && ! empty( $promotion_data ) ) : ?>
			<div id="elementor-navigator__footer__promotion">
				<div class="elementor-navigator__promotion-text">
					<?php echo esc_attr( $promotion_data['text'] ); ?>.
					<a href="<?php echo esc_url( $promotion_data['url'] ); ?>" target="_blank" class="e-link-promotion"><?php echo esc_attr( $promotion_data['url_label'] ); ?></a>
				</div>
			</div>
		<?php endif; ?>

		<div id="elementor-navigator__footer__resize-bar">
			<i class="eicon-ellipsis-h" aria-hidden="true"></i>
			<span class="elementor-screen-only"><?php echo esc_html__( 'Resize structure', 'elementor' ); ?></span>
		</div>
	</div>
</script>

<script type="text/template" id="tmpl-elementor-navigator__elements">
	<# if ( obj.elType ) { #>
		<div class="elementor-navigator__item" data-locked="{{ obj.isLocked ? 'true' : 'false' }}" tabindex="0">
			<div class="elementor-navigator__element__list-toggle">
				<i class="eicon-sort-down" aria-hidden="true"></i>
				<span class="elementor-screen-only"><?php echo esc_html__( 'Show/hide inner elements', 'elementor' ); ?></span>
			</div>
			<#
			if ( icon ) { #>
				<div class="elementor-navigator__element__element-type">
					<i class="{{{ icon }}}" aria-hidden="true"></i>
				</div>
			<# } #>
			<div class="elementor-navigator__element__title">
				<span class="elementor-navigator__element__title__text">{{{ title }}}</span>
			</div>
			<div class="elementor-navigator__element__toggle">
				<i class="eicon-preview-medium" aria-hidden="true"></i>
				<span class="elementor-screen-only"><?php echo esc_html__( 'Show/hide Element', 'elementor' ); ?></span>
			</div>
			<div class="elementor-navigator__element__indicators"></div>
		</div>
	<# } #>
	<div class="elementor-navigator__elements"></div>
</script>

<script type="text/template" id="tmpl-elementor-navigator__elements--empty">
	<div class="elementor-empty-view__title"><?php echo esc_html__( 'Empty', 'elementor' ); ?></div>
</script>

<script type="text/template" id="tmpl-elementor-navigator__root--empty">
	<img class="elementor-nerd-box-icon" src="<?php echo ELEMENTOR_ASSETS_URL . 'images/information.svg'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" loading="lazy" alt="<?php echo esc_attr__( 'Elementor', 'elementor' ); ?>" />
	<div class="elementor-nerd-box-title"><?php echo esc_html__( 'Easy Navigation is Here!', 'elementor' ); ?></div>
	<div class="elementor-nerd-box-message"><?php echo esc_html__( 'Once you fill your page with content, this window will give you an overview display of all the page elements. This way, you can easily move around any section, column, or widget.', 'elementor' ); ?></div>
</script>
