<div class="akismet-setup-instructions">
	<p><?php esc_html_e( 'Set up your Akismet account to enable spam filtering on this site.', 'akismet' ); ?></p>
	<?php
	Akismet::view(
		'get',
		array(
			'text'    => __( 'Choose an Akismet plan', 'akismet' ),
			'classes' => array( 'akismet-button', 'akismet-is-primary' ),
		)
	);
	?>
</div>
