<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

/**
 * Class WPSEO_Taxonomy_Tab.
 *
 * Contains the basics for each class extending this one.
 */
class WPSEO_Taxonomy_Fields {

	/**
	 * Returns the taxonomy fields.
	 *
	 * @param string $field_group The field group.
	 *
	 * @return array
	 */
	public function get( $field_group ) {
		$fields = [];

		switch ( $field_group ) {
			case 'content':
				$fields = $this->get_content_fields();
				break;
			case 'settings':
				$fields = $this->get_settings_fields();
				break;
			case 'social':
				$fields = $this->get_social_fields();
				break;
		}

		return $this->filter_hidden_fields( $fields );
	}

	/**
	 * Returns array with the fields for the general tab.
	 *
	 * @return array
	 */
	protected function get_content_fields() {
		$fields = [
			'title' => [
				'label'       => '',
				'description' => '',
				'type'        => 'hidden',
				'options'     => '',
				'hide'        => false,
			],
			'desc' => [
				'label'       => '',
				'description' => '',
				'type'        => 'hidden',
				'options'     => '',
				'hide'        => false,
			],
			'linkdex' => [
				'label'       => '',
				'description' => '',
				'type'        => 'hidden',
				'options'     => '',
				'hide'        => false,
			],
			'content_score' => [
				'label'       => '',
				'description' => '',
				'type'        => 'hidden',
				'options'     => '',
				'hide'        => false,
			],
			'inclusive_language_score' => [
				'label'       => '',
				'description' => '',
				'type'        => 'hidden',
				'options'     => '',
				'hide'        => false,
			],
			'focuskw' => [
				'label'       => '',
				'description' => '',
				'type'        => 'hidden',
				'options'     => '',
				'hide'        => false,
			],
			'is_cornerstone' => [
				'label'       => '',
				'description' => '',
				'type'        => 'hidden',
				'options'     => '',
				'hide'        => false,
			],
		];

		/**
		 * Filter: 'wpseo_taxonomy_content_fields' - Adds the possibility to register additional content fields.
		 *
		 * @param array $additional_fields The additional fields.
		 */
		$additional_fields = apply_filters( 'wpseo_taxonomy_content_fields', [] );

		return array_merge( $fields, $additional_fields );
	}

	/**
	 * Returns array with the fields for the settings tab.
	 *
	 * @return array
	 */
	protected function get_settings_fields() {
		return [
			'noindex' => [
				'label'       => '',
				'description' => '',
				'type'        => 'hidden',
				'options'     => '',
				'hide'        => false,
			],
			'bctitle' => [
				'label'       => '',
				'description' => '',
				'type'        => 'hidden',
				'options'     => '',
				'hide'        => ( WPSEO_Options::get( 'breadcrumbs-enable' ) !== true ),
			],
			'canonical' => [
				'label'       => '',
				'description' => '',
				'type'        => 'hidden',
				'options'     => '',
				'hide'        => false,
			],
		];
	}

	/**
	 * Returning the fields for the social media tab.
	 *
	 * @return array
	 */
	protected function get_social_fields() {
		$fields = [];

		if ( WPSEO_Options::get( 'opengraph', false ) === true ) {
			$fields = [
				'opengraph-title'       => [
					'label'       => '',
					'description' => '',
					'type'        => 'hidden',
					'options'     => '',
					'hide'        => false,
				],
				'opengraph-description' => [
					'label'       => '',
					'description' => '',
					'type'        => 'hidden',
					'options'     => '',
					'hide'        => false,
				],
				'opengraph-image'       => [
					'label'       => '',
					'description' => '',
					'type'        => 'hidden',
					'options'     => '',
					'hide'        => false,
				],
				'opengraph-image-id'    => [
					'label'       => '',
					'description' => '',
					'type'        => 'hidden',
					'options'     => '',
					'hide'        => false,
				],
			];
		}

		if ( WPSEO_Options::get( 'twitter', false ) === true ) {
			$fields = array_merge(
				$fields,
				[
					'twitter-title'       => [
						'label'       => '',
						'description' => '',
						'type'        => 'hidden',
						'options'     => '',
						'hide'        => false,
					],
					'twitter-description' => [
						'label'       => '',
						'description' => '',
						'type'        => 'hidden',
						'options'     => '',
						'hide'        => false,
					],
					'twitter-image'       => [
						'label'       => '',
						'description' => '',
						'type'        => 'hidden',
						'options'     => '',
						'hide'        => false,
					],
					'twitter-image-id'    => [
						'label'       => '',
						'description' => '',
						'type'        => 'hidden',
						'options'     => '',
						'hide'        => false,
					],
				]
			);
		}

		return $fields;
	}

	/**
	 * Filter the hidden fields.
	 *
	 * @param array $fields Array with the form fields that has will be filtered.
	 *
	 * @return array
	 */
	protected function filter_hidden_fields( array $fields ) {
		foreach ( $fields as $field_name => $field_options ) {
			if ( ! empty( $field_options['hide'] ) ) {
				unset( $fields[ $field_name ] );
			}
		}

		return $fields;
	}
}
