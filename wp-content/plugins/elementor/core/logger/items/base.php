<?php
namespace Elementor\Core\Logger\Items;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Base implements Log_Item_Interface {

	const FORMAT = 'date [type] message [meta]';
	const TRACE_FORMAT = '#key: file(line): class type function()';
	const TRACE_LIMIT = 5;

	protected $date;
	protected $type;
	protected $message;
	protected $meta = [];

	protected $times = 0;
	protected $times_dates = [];
	protected $args = [];

	public function __construct( $args ) {
		$this->date = current_time( 'mysql' );
		$this->message = ! empty( $args['message'] ) ? esc_html( $args['message'] ) : '';
		$this->type = ! empty( $args['type'] ) ? $args['type'] : 'info';
		$this->meta = ! empty( $args['meta'] ) ? $args['meta'] : [];
		$this->args = $args;

		$this->set_trace();
	}

	public function __get( $name ) {
		if ( property_exists( $this, $name ) ) {
			return $this->{$name};
		}

		return '';
	}

	public function __toString() {
		$vars = get_object_vars( $this );
		return strtr( static::FORMAT, $vars );
	}

	#[\ReturnTypeWillChange]
	public function jsonSerialize() {
		return [
			'class' => get_class( $this ),
			'item' => [
				'date' => $this->date,
				'message' => $this->message,
				'type' => $this->type,
				'meta' => $this->meta,
				'times' => $this->times,
				'times_dates' => $this->times_dates,
				'args' => $this->args,
			],
		];
	}

	public function deserialize( $properties ) {
		$this->date = ! empty( $properties['date'] ) && is_string( $properties['date'] ) ? $properties['date'] : '';
		$this->message = ! empty( $properties['message'] ) && is_string( $properties['message'] ) ? $properties['message'] : '';
		$this->type = ! empty( $properties['type'] ) && is_string( $properties['type'] ) ? $properties['type'] : '';
		$this->meta = ! empty( $properties['meta'] ) && is_array( $properties['meta'] ) ? $properties['meta'] : [];
		$this->times = ! empty( $properties['times'] ) && is_string( $properties['times'] ) ? $properties['times'] : '';
		$this->times_dates = ! empty( $properties['times_dates'] ) && is_array( $properties['times_dates'] ) ? $properties['times_dates'] : [];
		$this->args = ! empty( $properties['args'] ) && is_array( $properties['args'] ) ? $properties['args'] : [];
	}

	/**
	 * @return Log_Item_Interface | null
	 */
	public static function from_json( $str ) {
		$obj = json_decode( $str, true );
		if ( ! array_key_exists( 'class', $obj ) ) {
			return null;
		}
		$class = $obj['class'];
		if ( class_exists( $class ) ) {
			/** @var Base $item */
			$item = new $class( $obj['item']['message'] );
			$item->deserialize( $obj['item'] );
			return $item;
		}

		return null;
	}

	public function to_formatted_string( $output_format = 'html' ) {
		$vars = get_object_vars( $this );
		$format = static::FORMAT;
		if ( 'html' === $output_format ) {
			$format = str_replace( 'message', '<strong>message</strong>', static::FORMAT );
		}
		if ( empty( $vars['meta'] ) ) {
			$format = str_replace( '[meta]', '', $format );
		} else {
			$vars['meta'] = stripslashes( var_export( $vars['meta'], true ) ); // @codingStandardsIgnoreLine
		}
		return strtr( $format, $vars );
	}

	public function get_fingerprint() {
		$unique_key = $this->type . $this->message . var_export( $this->meta, true ); // @codingStandardsIgnoreLine
		// Info messages are not be aggregated:
		if ( 'info' === $this->type ) {
			$unique_key .= $this->date;
		}
		return md5( $unique_key );
	}

	public function increase_times( $item, $truncate = true ) {
		++$this->times;
		$this->times_dates[] = $item->date;

		if ( $truncate && ( self::MAX_LOG_ENTRIES < count( $this->times_dates ) ) ) {
			$this->times_dates = array_slice( $this->times_dates, -self::MAX_LOG_ENTRIES );
		}
	}

	public function format( $format = 'html' ) {
		$trace = $this->format_trace();
		if ( empty( $trace ) ) {
			return $this->to_formatted_string( $format );
		}
		$copy = clone $this;
		$copy->meta['trace'] = $trace;
		return $copy->to_formatted_string( $format );
	}

	public function get_name() {
		return 'Log';
	}

	private function format_trace() {
		$trace = empty( $this->meta['trace'] ) ? '' : $this->meta['trace'];

		if ( is_string( $trace ) ) {
			return $trace;
		}

		$trace_str = '';
		foreach ( $trace as $key => $trace_line ) {
			$format = static::TRACE_FORMAT;
			$trace_line['key'] = $key;
			if ( empty( $trace_line['file'] ) ) {
				$format = str_replace( 'file(line): ', '', $format );
			}

			$trace_str .= PHP_EOL . strtr( $format, $trace_line );
			$trace_str .= empty( $trace_line['args'] ) ? '' : var_export( $trace_line['args'], true ); // @codingStandardsIgnoreLine
		}

		return $trace_str . PHP_EOL;
	}

	private function set_trace() {
		if ( ! empty( $this->args['trace'] ) && true === $this->args['trace'] ) {
			$limit = empty( $this->args['trace_limit'] ) ? static::TRACE_LIMIT : $this->args['trace_limit'];

			$stack = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS ); // @codingStandardsIgnoreLine

			while ( ! empty( $stack ) && ! empty( $stack[0]['file'] ) && ( false !== strpos( $stack[0]['file'], 'core' . DIRECTORY_SEPARATOR . 'logger' ) ) ) {
				array_shift( $stack );
			}

			$this->meta['trace'] = array_slice( $stack, 0, $limit );

			return;
		}

		if ( is_array( $this->args ) ) {
			unset( $this->args['trace'] );
		}
	}
}
