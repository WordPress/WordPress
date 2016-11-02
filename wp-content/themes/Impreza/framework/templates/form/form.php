<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output a single form
 *
 * @var $type string Form type: 'contact' / 'search' / 'comment' / 'protectedpost' / ...
 * @var $action string Form action
 * @var $method string Form method: 'post' / 'get'
 * @var $fields array Form fields (see any of the fields template header for details)
 * @var $json_data array Json data to pass to JavaScript
 * @var $classes string Additional classes to append to form
 * @var $start_html string HTML to append to the form's start
 * @var $end_html string HTML to append to the form's end
 *
 * @action Before the template: 'us_before_template:templates/form/form'
 * @action After the template: 'us_after_template:templates/form/form'
 * @filter Template variables: 'us_template_vars:templates/form/form'
 */

// Variables defaults and filtering
$type = isset( $type ) ? $type : '';
$action = isset( $action ) ? $action : site_url( $_SERVER['REQUEST_URI'] );
$method = isset( $method ) ? $method : 'post';
$fields = isset( $fields ) ? (array) $fields : array();
foreach ( $fields as $field_name => $field ) {
	$fields[ $field_name ]['type'] = isset( $field['type'] ) ? $field['type'] : 'textfield';
	$fields[ $field_name ]['name'] = isset( $field['name'] ) ? $field['name'] : $field_name;
}
$classes = ( isset( $classes ) AND ! empty( $classes ) ) ? ( ' ' . $classes ) : '';
$start_html = isset( $start_html ) ? $start_html : '';
$end_html = isset( $end_html ) ? $end_html : '';

if ( ! empty( $type ) ) {
	$classes = ' for_' . $type . $classes;
}

global $us_form_index;
// Form indexes start from 1
$us_form_index = isset( $us_form_index ) ? ( $us_form_index + 1 ) : 1;

?>
<div class="w-form<?php echo $classes ?>" id="us_form_<?php echo $us_form_index ?>">
	<form class="w-form-h" autocomplete="off" action="<?php echo esc_attr( $action ) ?>" method="<?php echo $method ?>">
		<?php echo $start_html ?>
		<?php foreach ( $fields as $field ): ?>
			<?php us_load_template( 'templates/form/' . $field['type'], $field ) ?>
		<?php endforeach; ?>
		<div class="w-form-message"></div>
		<?php echo $end_html ?>
	</form>
	<?php if ( isset( $json_data ) AND is_array( $json_data ) AND ! empty( $json_data ) ): ?>
		<div class="w-form-json hidden"<?php echo us_pass_data_to_js( $json_data ) ?>></div>
	<?php endif; ?>
</div>
