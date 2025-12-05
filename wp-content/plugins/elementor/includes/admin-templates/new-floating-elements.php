<?php
namespace Elementor;

use Elementor\Modules\FloatingButtons\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<script type="text/template" id="tmpl-elementor-new-floating-elements">
	<div id="elementor-new-floating-elements__description">
		<div id="elementor-new-floating-elements__description__title"><?php
			printf(
				/* translators: %1$s Span open tag, %2$s: Span close tag. */
				esc_html__( 'Floating Elements Help You %1$sWork Efficiently%2$s', 'elementor' ),
				'<span>',
				'</span>'
			);
			?></div>
		<div id="elementor-new-floating-elements__description__content"><?php echo esc_html__( 'Use floating elements to engage your visitors and increase conversions.', 'elementor' ); ?></div>
	</div>
	<form id="elementor-new-floating-elements__form" action="<?php esc_url( admin_url( '/edit.php' ) ); ?>">
		<input type="hidden" name="post_type" value="<?php echo esc_attr( Module::CPT_FLOATING_BUTTONS ); ?>">
		<input type="hidden" name="template_type" value="<?php echo esc_attr( Module::FLOATING_BUTTONS_DOCUMENT_TYPE ); ?>">
		<input type="hidden" name="action" value="elementor_new_post">
		<?php // PHPCS - a nonce doesn't have to be escaped. ?>
		<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'elementor_action_new_post' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
		<div id="elementor-new-template__form___elementor_source__wrapper" class="elementor-form-field">
			<label for="elementor-new-template__form___elementor_source" class="elementor-form-field__label">
				<?php echo esc_html__( 'Choose Floating Element', 'elementor' ); ?>
			</label>
			<div class="elementor-form-field__select__wrapper">
				<select id="elementor-new-template__form___elementor_source" class="elementor-form-field__select" name="meta[<?php echo esc_attr( Module::FLOATING_ELEMENTS_TYPE_META_KEY ); ?>]">
					<?php foreach ( Module::get_floating_elements_types() as $key => $value ) : ?>
						<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<div id="elementor-new-floating-elements__form__post-title__wrapper" class="elementor-form-field">
			<label for="elementor-new-floating-elements__form__post-title" class="elementor-form-field__label">
				<?php echo esc_html__( 'Name your template', 'elementor' ); ?>
			</label>
			<div class="elementor-form-field__text__wrapper">
				<input type="text" placeholder="<?php echo esc_attr__( 'Enter template name (optional)', 'elementor' ); ?>" id="elementor-new-floating-elements__form__post-title" class="elementor-form-field__text" name="post_data[post_title]">
			</div>
		</div>
		<button id="elementor-new-floating-elements__form__submit" class="elementor-button e-primary"><?php echo esc_html__( 'Create Floating Element', 'elementor' ); ?></button>
	</form>
</script>
