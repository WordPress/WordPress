<?php

namespace Elementor\Modules\Variables\Storage\Exceptions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class BatchOperationFailed extends \Exception {
	private array $error_details;

	public function __construct( string $message, array $error_details = [] ) {
		parent::__construct( $message );
		$this->error_details = $error_details;
	}

	public function getErrorDetails(): array {
		return $this->error_details;
	}
}
