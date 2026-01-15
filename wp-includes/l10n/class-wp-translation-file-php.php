<?php
/**
 * I18N: WP_Translation_File_PHP class.
 *
 * @package WordPress
 * @subpackage I18N
 * @since 6.5.0
 */

/**
 * Class WP_Translation_File_PHP.
 *
 * @since 6.5.0
 */
class WP_Translation_File_PHP extends WP_Translation_File {
	/**
	 * Parses the file.
	 *
	 * @since 6.5.0
	 */
	protected function parse_file() {
		$this->parsed = true;

		$result = include $this->file;
		if ( ! $result || ! is_array( $result ) ) {
			$this->error = 'Invalid data';
			return;
		}

		if ( isset( $result['messages'] ) && is_array( $result['messages'] ) ) {
			foreach ( $result['messages'] as $original => $translation ) {
				$this->entries[ (string) $original ] = $translation;
			}
			unset( $result['messages'] );
		}

		$this->headers = array_change_key_case( $result );
	}

	/**
	 * Exports translation contents as a string.
	 *
	 * @since 6.5.0
	 *
	 * @return string Translation file contents.
	 */
	public function export(): string {
		$data = array_merge( $this->headers, array( 'messages' => $this->entries ) );

		return '<?php' . PHP_EOL . 'return ' . $this->var_export( $data ) . ';' . PHP_EOL;
	}

	/**
	 * Outputs or returns a parsable string representation of a variable.
	 *
	 * Like {@see var_export()} but "minified", using short array syntax
	 * and no newlines.
	 *
	 * @since 6.5.0
	 *
	 * @param mixed $value The variable you want to export.
	 * @return string The variable representation.
	 */
	private function var_export( $value ): string {
		if ( ! is_array( $value ) ) {
			return var_export( $value, true );
		}

		$entries = array();

		$is_list = array_is_list( $value );

		foreach ( $value as $key => $val ) {
			$entries[] = $is_list ? $this->var_export( $val ) : var_export( $key, true ) . '=>' . $this->var_export( $val );
		}

		return '[' . implode( ',', $entries ) . ']';
	}
}
