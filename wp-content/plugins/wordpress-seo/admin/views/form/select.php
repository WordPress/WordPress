<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 *
 * @uses string $attributes Additional attributes for the select.
 * @uses string $name       Value for the select name attribute.
 * @uses string $id         ID attribute for the select.
 * @uses array  $options    Array with the options to show.
 * @uses string $selected   The current set options.
 */

if ( ! defined( 'WPSEO_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

?>
<?php /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason: $attributes is properly escaped in parse_attribute via get_attributes in class-yoast-input-select.php. */ ?>
<select <?php echo $attributes; ?>name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $id ); ?>">
	<?php foreach ( $options as $option_attribute_value => $option_html_value ) : ?>
	<option value="<?php echo esc_attr( $option_attribute_value ); ?>"<?php echo selected( $selected, $option_attribute_value, false ); ?>><?php echo esc_html( $option_html_value ); ?></option>
	<?php endforeach; ?>
</select>
