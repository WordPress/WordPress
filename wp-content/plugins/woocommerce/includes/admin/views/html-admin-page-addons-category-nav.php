<?php
/**
 * Admin View: Page - Addons - category navigation
 *
 * @package WooCommerce\Admin
 * @var array  $sections
 * @var string $current_section
 * @var string $current_section_name
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div id="marketplace-current-section-dropdown" class="current-section-dropdown">
	<h2 class="current-section-dropdown__title"><?php esc_html_e( 'Browse categories', 'woocommerce' ); ?></h2>
	<ul>
		<?php foreach ( $sections as $section ) : ?>
			<?php
			if ( $current_section === $section->slug && '_featured' !== $section->slug ) {
				$current_section_name = $section->label;
			}
			?>
			<?php if ( $current_section === $section->slug ) : ?>
				<li class="current">
			<?php else: ?>
				<li>
			<?php endif; ?>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-addons&section=' . esc_attr( $section->slug ) ) ); ?>">
				<?php echo esc_html( $section->label ); ?>
			</a>
			</li>
		<?php endforeach; ?>
	</ul>
	<div id="marketplace-current-section-name" class="current-section-name"><?php echo esc_html( $current_section_name ); ?></div>
</div>
