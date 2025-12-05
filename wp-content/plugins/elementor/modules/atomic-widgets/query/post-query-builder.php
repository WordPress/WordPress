<?php

namespace Elementor\Modules\AtomicWidgets\Query;

use Elementor\Modules\WpRest\Base\Query as Query_Base;
use Elementor\Modules\WpRest\Classes\Post_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Post_Query_Builder extends Query_Builder_Base {
	public function build(): array {
		$config = $this->config;

		$params = Post_Query::build_query_params( [
			Post_Query::KEYS_CONVERSION_MAP_KEY => $config[ Query_Base::KEYS_CONVERSION_MAP_KEY ] ?? null,
			Post_Query::INCLUDED_TYPE_KEY => $config[ Query_Base::INCLUDED_TYPE_KEY ] ?? null,
			Post_Query::EXCLUDED_TYPE_KEY => $config[ Query_Base::EXCLUDED_TYPE_KEY ] ?? null,
			Post_Query::META_QUERY_KEY => $config[ Query_Base::META_QUERY_KEY ] ?? null,
			Post_Query::TAX_QUERY_KEY => $config[ Query_Base::TAX_QUERY_KEY ] ?? null,
			Post_Query::IS_PUBLIC_KEY => $config[ Query_Base::IS_PUBLIC_KEY ] ?? null,
			Post_Query::ITEMS_COUNT_KEY => $config[ Query_Base::ITEMS_COUNT_KEY ] ?? null,
		] );

		$endpoint = $config['endpoint'] ?? Post_Query::ENDPOINT;
		$namespace = $config['namespace'] ?? Post_Query::NAMESPACE;
		$url = $namespace . '/' . $endpoint;

		return [
			'params' => $params,
			'url' => $url,
		];
	}
}
