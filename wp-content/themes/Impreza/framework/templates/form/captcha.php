<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output a form's captcha field
 *
 * @var $name string Field name
 * @var $title string Field title
 * @var $placeholder string Field placeholder
 * @var $description string Field description
 * @var $value string Field value
 * @var $id string Field id
 * @var $classes string Additional field classes
 *
 * @action Before the template: 'us_before_template:templates/form/captcha'
 * @action After the template: 'us_after_template:templates/form/captcha'
 * @filter Template variables: 'us_template_vars:templates/form/captcha'
 */

$name = isset( $name ) ? $name : '';
$title = isset( $title ) ? $title : '';
$placeholder = isset( $placeholder ) ? $placeholder : '';
$value = isset( $value ) ? $value : '';
if ( ! isset( $id ) ) {
	global $us_form_index;
	$id = 'us_form_' . $us_form_index . '_' . $name;
}
$classes = ( isset( $classes ) AND ! empty( $classes ) ) ? ( ' ' . $classes ) : '';

$numbers = array( rand( 16, 30 ), rand( 1, 15 ) );
$sign = rand( 0, 1 );
$title .= '<span>' . implode( $sign ? ' + ' : ' - ', $numbers );
$result_hash = md5( ( $numbers[0] + ( $sign ? 1 : - 1 ) * $numbers[1] ) . NONCE_SALT );

// Always required field
$classes .= ' required';
if ( ! empty( $title ) ) {
	$title .= ' = ?</span>';
} elseif ( ! empty( $placeholder ) ) {
	$placeholder .= ' = ?';
}


?>
<div class="w-form-row for_<?php echo $name ?><?php echo $classes ?>">
	<div class="w-form-row-label">
		<label for="<?php echo $id ?>"><?php echo $title ?></label>
	</div>
	<div class="w-form-row-field">
		<?php do_action( 'us_form_captcha_start', $vars ) ?>
		<input type="hidden" name="<?php echo $name ?>_hash" value="<?php echo $result_hash ?>"/>
		<input type="text" name="<?php echo esc_attr( $name ) ?>" id="<?php echo $id ?>" value="<?php echo esc_attr( $value ) ?>"
		       data-required="1" placeholder="<?php echo esc_attr( $placeholder ) ?>" aria-required="true" />
		<span class="w-form-row-field-bar"></span>
		<?php do_action( 'us_form_captcha_end', $vars ) ?>
	</div>
	<div class="w-form-row-state"></div>
	<?php if ( isset( $description ) AND ! empty( $description ) ): ?>
		<div class="w-form-row-description"><?php echo $description ?></div>
	<?php endif; ?>
</div>
