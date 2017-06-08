<?php

class us_migration_2_2 extends US_Migration_Translator {

	// Content
	public function translate_content( &$content ) {
		return $this->_translate_content( $content );
	}

	public function translate_us_gmaps( &$name, &$params, &$content ) {
		$changed = FALSE;
		$markers = array();

		for ( $i = 2; $i <= 5; $i++ ) {
			if ( ( ! empty( $params['marker' . $i . '_address'] ) ) AND ( ! empty( $params['marker' . $i . '_text'] ) ) ) {
				$markers[] = array(
					'marker_address' => $params['marker' . $i . '_address'],
					'marker_text' => rawurldecode( base64_decode( $params['marker' . $i . '_text'] ) ),
				);
				unset( $params['marker' . $i . '_address'] );
				unset( $params['marker' . $i . '_text'] );
				$changed = TRUE;
			}
		}

		if ( $changed ) {
			$params['markers'] = rawurlencode( json_encode( $markers ) );
		}

		return $changed;
	}
}
