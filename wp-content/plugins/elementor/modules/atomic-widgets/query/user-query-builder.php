<?php

namespace Elementor\Modules\AtomicWidgets\Query;

use Elementor\Modules\WpRest\Base\Query as Query_Base;
use Elementor\Modules\WpRest\Classes\User_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class User_Query_Builder extends Query_Builder_Base {
	public function build(): array {
		$config = $this->config;

		$params = User_Query::build_query_params( [
			User_Query::KEYS_CONVERSION_MAP_KEY => $config[ Query_Base::KEYS_CONVERSION_MAP_KEY ] ?? null,
			User_Query::INCLUDED_TYPE_KEY => $config[ Query_Base::INCLUDED_TYPE_KEY ] ?? null,
			User_Query::EXCLUDED_TYPE_KEY => $config[ Query_Base::EXCLUDED_TYPE_KEY ] ?? null,
			User_Query::META_QUERY_KEY => $config[ Query_Base::META_QUERY_KEY ] ?? null,
			User_Query::ITEMS_COUNT_KEY => $config[ Query_Base::ITEMS_COUNT_KEY ] ?? null,
		] );

		$endpoint = $config['endpoint'] ?? User_Query::ENDPOINT;
		$namespace = $config['namespace'] ?? User_Query::NAMESPACE;
		$url = $namespace . '/' . $endpoint;

		return [
			'params' => $params,
			'url' => $url,
		];
	}
}
