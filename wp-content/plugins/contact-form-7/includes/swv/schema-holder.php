<?php

trait WPCF7_SWV_SchemaHolder {

	protected $schema;


	/**
	 * Retrieves SWV schema for this holder object (contact form).
	 *
	 * @return WPCF7_SWV_Schema The schema object.
	 */
	public function get_schema() {
		if ( isset( $this->schema ) ) {
			return $this->schema;
		}

		$schema = new WPCF7_SWV_Schema( array(
			'locale' => isset( $this->locale ) ? $this->locale : '',
		) );

		do_action( 'wpcf7_swv_create_schema', $schema, $this );

		return $this->schema = $schema;
	}


	/**
	 * Validates form inputs based on the schema and given context.
	 */
	public function validate_schema( $context, WPCF7_Validation $validity ) {
		$schema = $this->get_schema();

		foreach ( $schema->validate( $context ) as $result ) {
			if ( is_wp_error( $result ) ) {
				$rule = $result->get_error_data();
				$field = $rule->get_property( 'field' );

				if ( isset( $field ) and $validity->is_valid( $field ) ) {
					$validity->invalidate( $field, $result );
				}
			}
		}
	}

}
