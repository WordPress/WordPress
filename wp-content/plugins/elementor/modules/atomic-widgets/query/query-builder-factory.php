<?php

namespace Elementor\Modules\AtomicWidgets\Query;

use Elementor\Modules\WpRest\Classes\Post_Query;
use Elementor\Modules\WpRest\Classes\Term_Query;
use Elementor\Modules\WpRest\Classes\User_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Query_Builder_Factory {
	const ENDPOINT_KEY = 'endpoint';

	private const BUILDERS = [
		Post_Query::ENDPOINT => Post_Query_Builder::class,
		Term_Query::ENDPOINT => Term_Query_Builder::class,
		User_Query::ENDPOINT => User_Query_Builder::class,
	];

	public static function create( ?array $config = [] ): Query_Builder_Base {
		$endpoint = $config[ self::ENDPOINT_KEY ] ?? Post_Query::ENDPOINT;

		$class = self::BUILDERS[ $endpoint ] ?? null;

		if ( ! $class ) {
			throw new \Exception( 'Unsupported query type' );
		}

		return new $class( $config ?? [] );
	}
}
