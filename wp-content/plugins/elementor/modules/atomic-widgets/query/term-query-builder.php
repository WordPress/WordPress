<?php

namespace Elementor\Modules\AtomicWidgets\Query;

use Elementor\Modules\WpRest\Base\Query as Query_Base;
use Elementor\Modules\WpRest\Classes\Term_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Term_Query_Builder extends Query_Builder_Base {
	public function build(): array {
		$config = $this->config;

		$params = Term_Query::build_query_params( [
			Term_Query::KEYS_CONVERSION_MAP_KEY => $config[ Query_Base::KEYS_CONVERSION_MAP_KEY ] ?? null,
			Term_Query::INCLUDED_TYPE_KEY => $config[ Query_Base::INCLUDED_TYPE_KEY ] ?? null,
			Term_Query::EXCLUDED_TYPE_KEY => $config[ Query_Base::EXCLUDED_TYPE_KEY ] ?? null,
			Term_Query::META_QUERY_KEY => $config[ Query_Base::META_QUERY_KEY ] ?? null,
			Term_Query::IS_PUBLIC_KEY => $config[ Query_Base::IS_PUBLIC_KEY ] ?? null,
			Term_Query::HIDE_EMPTY_KEY => $config[ Query_Base::HIDE_EMPTY_KEY ] ?? null,
			Term_Query::ITEMS_COUNT_KEY => $config[ Query_Base::ITEMS_COUNT_KEY ] ?? null,
		] );

		$endpoint = $config['endpoint'] ?? Term_Query::ENDPOINT;
		$namespace = $config['namespace'] ?? Term_Query::NAMESPACE;
		$url = $namespace . '/' . $endpoint;

		return [
			'params' => $params,
			'url' => $url,
		];
	}
}
