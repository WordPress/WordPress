<?php
/**
 * Pipe-related classes.
 *
 * @link https://contactform7.com/selectable-recipient-with-pipes/
 */


/**
 * Class representing a pair of pipe.
 */
class WPCF7_Pipe {

	public $before = '';
	public $after = '';

	public function __construct( $text ) {
		$text = (string) $text;

		$pipe_pos = strpos( $text, '|' );

		if ( false === $pipe_pos ) {
			$this->before = $this->after = wpcf7_strip_whitespaces( $text );
		} else {
			$this->before = wpcf7_strip_whitespaces( substr( $text, 0, $pipe_pos ) );
			$this->after = wpcf7_strip_whitespaces( substr( $text, $pipe_pos + 1 ) );
		}
	}
}


/**
 * Class representing a list of pipes.
 */
class WPCF7_Pipes {

	private $pipes = array();

	public function __construct( $texts = null ) {
		foreach ( (array) $texts as $text ) {
			$this->add_pipe( $text );
		}
	}

	private function add_pipe( $text ) {
		$pipe = new WPCF7_Pipe( $text );
		$this->pipes[] = $pipe;
	}

	public function merge( self $another ) {
		$this->pipes = array_merge( $this->pipes, $another->pipes );
	}

	public function do_pipe( $input ) {
		$input_canonical = wpcf7_canonicalize( $input, array(
			'strto' => 'as-is',
		) );

		foreach ( $this->pipes as $pipe ) {
			$before_canonical = wpcf7_canonicalize( $pipe->before, array(
				'strto' => 'as-is',
			) );

			if ( $input_canonical === $before_canonical ) {
				return $pipe->after;
			}
		}

		return $input;
	}

	public function collect_befores() {
		$befores = array();

		foreach ( $this->pipes as $pipe ) {
			$befores[] = $pipe->before;
		}

		return $befores;
	}

	public function collect_afters() {
		$afters = array();

		foreach ( $this->pipes as $pipe ) {
			$afters[] = $pipe->after;
		}

		return $afters;
	}

	public function zero() {
		return empty( $this->pipes );
	}

	public function random_pipe() {
		if ( $this->zero() ) {
			return null;
		}

		return $this->pipes[array_rand( $this->pipes )];
	}

	public function to_array() {
		return array_map(
			static function ( WPCF7_Pipe $pipe ) {
				return array(
					$pipe->before,
					$pipe->after,
				);
			},
			$this->pipes
		);
	}
}


/**
 * Trait for classes that hold cross-tag WPCF7_Pipes object.
 */
trait WPCF7_PipesHolder {

	protected $pipes;

	public function get_pipes( $field_name ) {
		if ( isset( $this->pipes[$field_name] ) ) {
			return $this->pipes[$field_name];
		}

		$result = new WPCF7_Pipes();

		$tags = $this->scan_form_tags( array(
			'name' => $field_name,
		) );

		foreach ( $tags as $tag ) {
			if ( $tag->pipes instanceof WPCF7_Pipes ) {
				$result->merge( $tag->pipes );
			}
		}

		return $this->pipes[$field_name] = $result;
	}

	public function scan_form_tags() {
		return array();
	}

}
