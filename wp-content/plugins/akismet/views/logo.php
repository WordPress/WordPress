<?php
//phpcs:disable VariableAnalysis
// There are "undefined" variables here because they're defined in the code that includes this file as a template.
?>
<div class="akismet-masthead__logo-container">
	<?php if ( isset( $include_logo_link ) && $include_logo_link === true ) : ?>
		<a href="<?php echo esc_url( Akismet_Admin::get_page_url() ); ?>" class="akismet-masthead__logo-link">
	<?php endif; ?>
	<img class="akismet-masthead__logo" src="<?php echo esc_url( plugins_url( '../_inc/img/akismet-refresh-logo@2x.png', __FILE__ ) ); ?>" srcset="<?php echo esc_url( plugins_url( '../_inc/img/akismet-refresh-logo.svg', __FILE__ ) ); ?>" alt="Akismet logo" />
	<?php if ( isset( $include_logo_link ) && $include_logo_link === true ) : ?>
		</a>
	<?php endif; ?>
</div>
