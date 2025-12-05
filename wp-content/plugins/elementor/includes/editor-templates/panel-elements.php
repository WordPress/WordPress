<?php
namespace Elementor;

use Elementor\Utils;
use Elementor\Core\Utils\Promotions\Filtered_Promotions_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<script type="text/template" id="tmpl-elementor-panel-elements">
	<# if ( $e.components.get( 'document/elements' ).utils.allowAddingWidgets()) { #>
	<div id="elementor-panel-elements-navigation" class="elementor-panel-navigation">
		<button class="elementor-component-tab elementor-panel-navigation-tab" data-tab="categories"><?php echo esc_html__( 'Elements', 'elementor' ); ?></button>
		<button class="elementor-component-tab elementor-panel-navigation-tab" data-tab="global"><?php echo esc_html__( 'Globals', 'elementor' ); ?></button>
	</div>
	<# } #>
	<div id="elementor-panel-elements-search-area"></div>
	<div id="elementor-panel-elements-notice-area"></div>
	<div id="elementor-panel-elements-wrapper"></div>
</script>

<script type="text/template" id="tmpl-elementor-panel-categories">
	<div id="elementor-panel-categories"></div>
	<?php
	$get_pro_details = apply_filters( 'elementor/editor/panel/get_pro_details', [
		'link' => 'https://go.elementor.com/pro-widgets/',
		'message' => __( 'Get more with Elementor Pro', 'elementor' ),
		'button_text' => __( 'Upgrade Now', 'elementor' ),
	] );
	$promotion_data_sticky = [
		'url' => 'https://go.elementor.com/go-pro-sticky-widget-panel/',
		'message' => __( 'Access all Pro widgets.', 'elementor' ),
		'button_text' => __( 'Upgrade Now', 'elementor' ),
	];
	$promotion_data_sticky = Filtered_Promotions_Manager::get_filtered_promotion_data( $promotion_data_sticky, 'elementor/editor/panel/get_pro_details-sticky', 'url' );
	$has_pro = Utils::has_pro();
	?>
	<div id="elementor-panel-get-pro-elements" class="elementor-nerd-box">
		<img class="elementor-nerd-box-icon" src="<?php echo ELEMENTOR_ASSETS_URL . 'images/go-pro.svg'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" loading="lazy" alt="<?php echo esc_attr__( 'Upgrade', 'elementor' ); ?>" />
		<div class="elementor-nerd-box-message"><?php echo esc_html( $get_pro_details['message'] ); ?></div>
		<a class="elementor-button go-pro" target="_blank" href="<?php echo esc_url( $get_pro_details['link'] ); ?>"><?php echo esc_html( $get_pro_details['button_text'] ); ?></a>
	</div>
	<?php if ( ! $has_pro ) : ?>
	<div id="elementor-panel-get-pro-elements-sticky">
		<img class="elementor-nerd-box-icon" src="<?php echo ELEMENTOR_ASSETS_URL . 'images/unlock-sticky.svg'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" loading="lazy" alt="<?php echo esc_attr__( 'Upgrade', 'elementor' ); ?>"/>
		<div class="elementor-get-pro-sticky-message">
			<?php echo esc_html( $promotion_data_sticky['message'] ); ?>
			<a target="_blank" href="<?php echo esc_url( $promotion_data_sticky['url'] ); ?>"><?php echo esc_html( $promotion_data_sticky['button_text'] ); ?></a>
		</div>
	</div>
	<?php endif; ?>
</script>

<script type="text/template" id="tmpl-elementor-panel-elements-category">
	<button class="elementor-panel-heading elementor-panel-category-title">
		<span class="elementor-panel-heading-toggle">
			<i class="eicon-caret-right" aria-hidden="true"></i>
		</span>
		<span class="elementor-panel-heading-title">{{{ title }}}</span>
		<?php do_action( 'elementor/editor/templates/panel/category' ); ?>
		<# if ( 'undefined' !== typeof promotion && promotion ) { #>
			<span class="elementor-panel-heading-promotion">
				<a href="{{{ promotion.url }}}" target="_blank">
					<i class="eicon-upgrade-crown"></i><?php echo esc_html__( 'Upgrade', 'elementor' ); ?>
				</a>
			</span>
		<# } #>
	</button>
	<div class="elementor-panel-category-items elementor-responsive-panel"></div>
</script>

<script type="text/template" id="tmpl-elementor-panel-element-search">
	<label for="elementor-panel-elements-search-input" class="screen-reader-text"><?php echo esc_html__( 'Search Widget:', 'elementor' ); ?></label>
	<input type="search" id="elementor-panel-elements-search-input" placeholder="<?php esc_attr_e( 'Search Widget...', 'elementor' ); ?>" autocomplete="off"/>
	<i class="eicon-search-bold" aria-hidden="true"></i>
</script>

<script type="text/template" id="tmpl-elementor-element-library-element">
	<button class="elementor-element">
	<# if ( obj.integration ) { #>
			<i class="eicon-plug"></i>
		<# } else if ( false === obj.editable ) { #>
			<i class="eicon-lock"></i>
		<# } #>
		<# if ( obj.categories.includes( 'v4-elements' ) ) { #>
			<i class="eicon-atomic"></i>
		<# } #>
		<div class="icon">
			<i class="{{ icon }}" aria-hidden="true"></i>
		</div>
		<div class="title-wrapper">
			<div class="title">{{{ title }}}</div>
		</div>
	</button>
</script>

<script type="text/template" id="tmpl-elementor-panel-global">
	<div class="elementor-nerd-box">
		<img class="elementor-nerd-box-icon" src="<?php echo ELEMENTOR_ASSETS_URL . 'images/information.svg'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" loading="lazy" alt="<?php echo esc_attr__( 'Elementor', 'elementor' ); ?>" />
		<div class="elementor-nerd-box-title"><?php echo esc_html__( 'Meet Our Global Widget', 'elementor' ); ?></div>
		<div class="elementor-nerd-box-message"><?php echo esc_html__( 'With this feature, you can save a widget as global, then add it to multiple areas. All areas will be editable from one single place.', 'elementor' ); ?></div>
		<a class="elementor-button go-pro" target="_blank" href="https://go.elementor.com/pro-global/"><?php echo esc_html__( 'Upgrade Now', 'elementor' ); ?></a>
	</div>
</script>
