<?php

namespace Elementor\Core\Utils\Api;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Parse_Result {
	private Parse_Errors $errors;

	private $value;

	public static function make() {
		return new static();
	}

	public function __construct() {
		$this->errors = Parse_Errors::make();
	}

	public function wrap( $value ): self {
		$this->value = $value;

		return $this;
	}

	public function unwrap() {
		return $this->value;
	}

	public function is_valid(): bool {
		return $this->errors->is_empty();
	}

	public function errors(): Parse_Errors {
		return $this->errors;
	}
}
