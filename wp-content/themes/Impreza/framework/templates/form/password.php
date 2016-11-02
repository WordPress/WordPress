<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output a form's password field
 *
 * @var $name string Field name
 * @var $title string Field title
 * @var $placeholder string Field placeholder
 * @var $description string Field description
 * @var $required bool Is the field required?
 * @var $value string Field value
 * @var $id string Field id
 * @var $classes string Additional field classes
 *
 * @action Before the template: 'us_before_template:templates/form/password'
 * @action After the template: 'us_after_template:templates/form/password'
 * @filter Template variables: 'us_template_vars:templates/form/password'
 */

$name = isset( $name ) ? $name : '';
$title = isset( $title ) ? $title : '';
$placeholder = isset( $placeholder ) ? $placeholder : '';
$required = ( isset( $required ) AND $required );
$value = isset( $value ) ? $value : '';
if ( ! isset( $id ) ) {
	global $us_form_index;
	$id = 'us_form_' . $us_form_index . '_' . $name;
}
$classes = ( isset( $classes ) AND ! empty( $classes ) ) ? ( ' ' . $classes ) : '';

$required_atts = '';
if ( $required ) {
	$classes .= ' required';
	$required_atts = ' data-required="true" aria-required="true"';
	if ( ! empty( $title ) ) {
		$title .= ' <span class="required">*</span>';
	} elseif ( ! empty( $placeholder ) ) {
		$placeholder .= ' *';
	}
}

?>
<div class="w-form-row for_<?php echo $name ?><?php echo $classes ?>">
	<div class="w-form-row-label">
		<label for="<?php echo $id ?>"><?php echo $title ?></label>
	</div>
	<div class="w-form-row-field">
		<?php do_action( 'us_form_field_start', $vars ) ?>
		<input type="password" name="<?php echo esc_attr( $name ) ?>" id="<?php echo $id ?>" value="<?php echo esc_attr( $value ) ?>"
		       placeholder="<?php echo esc_attr( $placeholder ) ?>"<?php echo $required_atts ?>/>
		<span class="w-form-row-field-bar"></span>
		<?php do_action( 'us_form_field_end', $vars ) ?>
	</div>
	<div class="w-form-row-state"></div>
	<?php if ( isset( $description ) AND ! empty( $description ) ): ?>
		<div class="w-form-row-description"><?php echo $description ?></div>
	<?php endif; ?>
</div>
