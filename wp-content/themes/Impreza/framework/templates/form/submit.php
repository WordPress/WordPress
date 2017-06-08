<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output a form's hidden field
 *
 * @var $name string Field name
 * @var $title string Submit button title
 * @var $classes string Additional field classes
 * @var $btn_wrapper_classes string Additional button wrapper classes
 * @var $btn_classes string Additional button classes
 * @var $btn_inner_css string Button inner css
 *
 * @action Before the template: 'us_before_template:templates/form/submit'
 * @action After the template: 'us_after_template:templates/form/submit'
 * @filter Template variables: 'us_template_vars:templates/form/submit'
 */

$name = isset( $name ) ? $name : '';
$title = isset( $title ) ? $title : __( 'Submit', 'us' );
$classes = ( isset( $classes ) AND ! empty( $classes ) ) ? ( ' ' . $classes ) : '';
$btn_classes = ( isset( $btn_classes ) AND ! empty( $btn_classes ) ) ? ( ' ' . $btn_classes ) : '';
$btn_inner_css = ( isset( $btn_inner_css ) AND ! empty( $btn_inner_css ) ) ? ( '  style="' . $btn_inner_css . '"' ) : '';

?>
<div class="w-form-row for_<?php echo $name ?>">
	<div class="w-form-row-field">
		<button class="w-btn<?php echo $btn_classes ?>"<?php echo $btn_inner_css ?> type="submit"><span class="g-preloader type_1"></span><span class="w-btn-label"><?php echo $title ?></span></button>
	</div>
</div>
