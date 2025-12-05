<?php

/**
 * Server-side user input validation manager.
 */
class WPCF7_Validation implements ArrayAccess {
	private $invalid_fields = array();
	private $container = array();

	public function __construct() {
		$this->container = array(
			'valid' => true,
			'reason' => array(),
			'idref' => array(),
		);
	}


	/**
	 * Marks a form control as an invalid field.
	 *
	 * @param WPCF7_FormTag|array|string $context Context representing the
	 *                                   target field.
	 * @param WP_Error|string $error The error of the field.
	 */
	public function invalidate( $context, $error ) {
		if ( $context instanceof WPCF7_FormTag ) {
			$tag = $context;
		} elseif ( is_array( $context ) ) {
			$tag = new WPCF7_FormTag( $context );
		} elseif ( is_string( $context ) ) {
			$tags = wpcf7_scan_form_tags( array( 'name' => trim( $context ) ) );
			$tag = $tags ? new WPCF7_FormTag( $tags[0] ) : null;
		}

		$name = ! empty( $tag ) ? $tag->name : null;

		if ( empty( $name ) or ! wpcf7_is_name( $name ) ) {
			return;
		}

		if ( is_wp_error( $error ) ) {
			$message = $error->get_error_message();
		} else {
			$message = $error;
		}

		if ( $this->is_valid( $name ) ) {
			$id = (string) $tag->get_option( 'id', 'id', true );

			if ( ! wpcf7_is_name( $id ) or str_starts_with( $id, 'wpcf7' ) ) {
				$id = null;
			}

			$this->invalid_fields[$name] = array(
				'reason' => (string) $message,
				'idref' => $id,
			);
		}
	}


	/**
	 * Returns true if the target field is valid.
	 *
	 * @param string|null $name Optional. If specified, this is the name of
	 *                    the target field. Default null.
	 * @return bool True if the target field has no error. If no target is
	 *              specified, returns true if all fields are valid.
	 *              Otherwise false.
	 */
	public function is_valid( $name = null ) {
		if ( ! empty( $name ) ) {
			return ! isset( $this->invalid_fields[$name] );
		} else {
			return empty( $this->invalid_fields );
		}
	}


	/**
	 * Retrieves an associative array of invalid fields.
	 *
	 * @return array The associative array of invalid fields.
	 */
	public function get_invalid_fields() {
		return $this->invalid_fields;
	}


	/**
	 * Assigns a value to the specified offset.
	 *
	 * @link https://www.php.net/manual/en/arrayaccess.offsetset.php
	 */
	#[ReturnTypeWillChange]
	public function offsetSet( $offset, $value ) {
		if ( isset( $this->container[$offset] ) ) {
			$this->container[$offset] = $value;
		}

		if ( 'reason' === $offset and is_array( $value ) ) {
			foreach ( $value as $k => $v ) {
				$this->invalidate( $k, $v );
			}
		}
	}


	/**
	 * Returns the value at specified offset.
	 *
	 * @link https://www.php.net/manual/en/arrayaccess.offsetget.php
	 */
	#[ReturnTypeWillChange]
	public function offsetGet( $offset ) {
		if ( isset( $this->container[$offset] ) ) {
			return $this->container[$offset];
		}
	}


	/**
	 * Returns true if the specified offset exists.
	 *
	 * @link https://www.php.net/manual/en/arrayaccess.offsetexists.php
	 */
	#[ReturnTypeWillChange]
	public function offsetExists( $offset ) {
		return isset( $this->container[$offset] );
	}


	/**
	 * Unsets an offset.
	 *
	 * @link https://www.php.net/manual/en/arrayaccess.offsetunset.php
	 */
	#[ReturnTypeWillChange]
	public function offsetUnset( $offset ) {
	}

}
