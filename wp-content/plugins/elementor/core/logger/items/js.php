<?php
namespace Elementor\Core\Logger\Items;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class JS extends File {

	const FORMAT = 'JS: date [type X times][file:line:column] message [meta]';

	protected $column;

	public function __construct( $args ) {
		parent::__construct( $args );
		$this->column = $args['column'];
		$this->file = $args['url'];
		$this->date = gmdate( 'Y-m-d H:i:s', $args['timestamp'] );
	}

	#[\ReturnTypeWillChange]
	public function jsonSerialize() {
		$json_arr = parent::jsonSerialize();
		$json_arr['column'] = $this->column;
		return $json_arr;
	}

	public function deserialize( $properties ) {
		parent::deserialize( $properties );
		$this->column = ! empty( $properties['column'] ) && is_string( $properties['column'] ) ? $properties['column'] : '';
	}

	public function get_name() {
		return 'JS';
	}
}
