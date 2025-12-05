<?php

add_action(
	'wpcf7_update_option',
	'wpcf7_config_validator_update_option',
	10, 3
);

/**
 * Runs bulk validation after the reCAPTCHA integration option is updated.
 */
function wpcf7_config_validator_update_option( $name, $value, $old_option ) {
	if ( 'recaptcha' === $name ) {
		$contact_forms = WPCF7_ContactForm::find();

		$options = array(
			'include' => 'unsafe_email_without_protection',
		);

		foreach ( $contact_forms as $contact_form ) {
			$config_validator = new WPCF7_ConfigValidator( $contact_form, $options );
			$config_validator->restore();
			$config_validator->validate();
			$config_validator->save();
		}
	}
}
