<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output a form's info field
 *
 * @var $name string Field name
 * @var $title string Field title
 * @var $classes string Additional field classes
 *
 * @action Before the template: 'us_before_template:templates/form/info'
 * @action After the template: 'us_after_template:templates/form/info'
 * @filter Template variables: 'us_template_vars:templates/form/info'
 */

$name = isset( $name ) ? $name : '';
$title = isset( $title ) ? $title : '';
$classes = ( isset( $classes ) AND ! empty( $classes ) ) ? ( ' ' . $classes ) : '';

?>
<div class="w-form-row for_<?php echo $name ?><?php echo $classes ?>">
	<p><?php echo $title ?></p>
</div>
