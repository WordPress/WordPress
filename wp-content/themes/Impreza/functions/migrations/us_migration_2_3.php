<?php

class us_migration_2_3 extends US_Migration_Translator {

	// Content
	public function translate_content( &$content ) {
		return $this->_translate_content( $content );
	}

	public function translate_us_cta( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['message'] ) ) {
			$content = $params['message'] . '[/us_cta]';
			unset( $params['message'] );
			$changed = TRUE;
		}

		return $changed;
	}

	public function translate_vc_icon( &$name, &$params, &$content )
	{
		$changed = FALSE;

		if ( ! us_get_option( 'enable_unsupported_vc_shortcodes' ) ) {
			global $usof_options;
			usof_load_options_once();

			$theme = wp_get_theme();
			if ( is_child_theme() ) {
				$theme = wp_get_theme( $theme->get( 'Template' ) );
			}
			$theme_name = $theme->get( 'Name' );
			$usof_options['enable_unsupported_vc_shortcodes'] = TRUE;
			update_option( 'usof_options_' . $theme_name, $usof_options, TRUE );
		}


		return $changed;
	}
}
