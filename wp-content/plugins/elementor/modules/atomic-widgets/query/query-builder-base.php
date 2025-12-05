<?php

namespace Elementor\Modules\AtomicWidgets\Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

abstract class Query_Builder_Base {
	protected array $config;

	public function __construct( array $config ) {
		$this->config = $config;
	}

	abstract public function build(): array;
}
