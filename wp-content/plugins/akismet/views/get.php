<?php

//phpcs:disable VariableAnalysis
// There are "undefined" variables here because they're defined in the code that includes this file as a template.

$submit_classes_attr = 'akismet-button';

if ( isset( $classes ) && ( is_countable( $classes ) ? count( $classes ) : 0 ) > 0 ) {
	$submit_classes_attr = implode( ' ', $classes );
}
?>

<form name="akismet_activate" action="https://akismet.com/get/" method="POST" target="_blank">
	<input type="hidden" name="passback_url" value="<?php echo esc_url( Akismet_Admin::get_page_url() ); ?>"/>
	<input type="hidden" name="blog" value="<?php echo esc_url( get_option( 'home' ) ); ?>"/>
	<input type="hidden" name="redirect" value="<?php echo isset( $redirect ) ? esc_attr( $redirect ) : 'plugin-signup'; ?>"/>
	<button type="submit" class="<?php echo esc_attr( $submit_classes_attr ); ?>" value="<?php echo esc_attr( $text ); ?>"><?php echo esc_attr( $text ) . '<span class="screen-reader-text">' . esc_html__( '(opens in a new tab)', 'akismet' ) . '</span>'; ?></button>
</form>
