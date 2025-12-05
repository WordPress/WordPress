<?php

trait WPCF7_ConfigValidator_AdditionalSettings {

	/**
	 * Runs error detection for the additional settings section.
	 */
	public function validate_additional_settings() {
		$section = 'additional_settings.body';

		if ( $this->supports( 'deprecated_settings' ) ) {
			$deprecated_settings_used =
				$this->contact_form->additional_setting( 'on_sent_ok' ) ||
				$this->contact_form->additional_setting( 'on_submit' );

			if ( $deprecated_settings_used ) {
				$this->add_error( $section, 'deprecated_settings',
					array(
						'message' => __( 'Deprecated settings are used.', 'contact-form-7' ),
					)
				);
			} else {
				$this->remove_error( $section, 'deprecated_settings' );
			}
		}
	}

}
