<?php
/**
 * Marketing Specs Handler
 *
 * Fetches the specifications for the marketing feature from WC.com API.
 */

namespace Automattic\WooCommerce\Internal\Admin\Marketing;

/**
 * Marketing Specifications Class.
 *
 * @internal
 * @since x.x.x
 */
class MarketingSpecs {
	/**
	 * Name of recommended plugins transient.
	 *
	 * @var string
	 */
	const RECOMMENDED_PLUGINS_TRANSIENT = 'wc_marketing_recommended_plugins';

	/**
	 * Name of knowledge base post transient.
	 *
	 * @var string
	 */
	const KNOWLEDGE_BASE_TRANSIENT = 'wc_marketing_knowledge_base';

	/**
	 * Slug of the category specifying marketing extensions on the WooCommerce.com store.
	 *
	 * @var string
	 */
	const MARKETING_EXTENSION_CATEGORY_SLUG = 'marketing';

	/**
	 * Slug of the subcategory specifying marketing channels on the WooCommerce.com store.
	 *
	 * @var string
	 */
	const MARKETING_CHANNEL_SUBCATEGORY_SLUG = 'sales-channels';

	/**
	 * Load recommended plugins from WooCommerce.com
	 *
	 * @return array
	 */
	public function get_recommended_plugins(): array {
		$plugins = get_transient( self::RECOMMENDED_PLUGINS_TRANSIENT );

		if ( false === $plugins ) {
			$request = wp_remote_get(
				'https://woocommerce.com/wp-json/wccom/marketing-tab/1.2/recommendations.json',
				array(
					'user-agent' => 'WooCommerce/' . WC()->version . '; ' . get_bloginfo( 'url' ),
				)
			);
			$plugins = [];

			if ( ! is_wp_error( $request ) && 200 === $request['response']['code'] ) {
				$plugins = json_decode( $request['body'], true );
			}

			set_transient(
				self::RECOMMENDED_PLUGINS_TRANSIENT,
				$plugins,
				// Expire transient in 15 minutes if remote get failed.
				// Cache an empty result to avoid repeated failed requests.
				empty( $plugins ) ? 900 : 3 * DAY_IN_SECONDS
			);
		}

		return array_values( $plugins );
	}

	/**
	 * Return only the recommended marketing channels from WooCommerce.com.
	 *
	 * @return array
	 */
	public function get_recommended_marketing_channels(): array {
		return array_filter( $this->get_recommended_plugins(), [ $this, 'is_marketing_channel_plugin' ] );
	}

	/**
	 * Return all recommended marketing extensions EXCEPT the marketing channels from WooCommerce.com.
	 *
	 * @return array
	 */
	public function get_recommended_marketing_extensions_excluding_channels(): array {
		return array_filter(
			$this->get_recommended_plugins(),
			function ( array $plugin_data ) {
				return $this->is_marketing_plugin( $plugin_data ) && ! $this->is_marketing_channel_plugin( $plugin_data );
			}
		);
	}

	/**
	 * Returns whether a plugin is a marketing extension.
	 *
	 * @param array $plugin_data The plugin properties returned by the API.
	 *
	 * @return bool
	 */
	protected function is_marketing_plugin( array $plugin_data ): bool {
		$categories = $plugin_data['categories'] ?? [];

		return in_array( self::MARKETING_EXTENSION_CATEGORY_SLUG, $categories, true );
	}

	/**
	 * Returns whether a plugin is a marketing channel.
	 *
	 * @param array $plugin_data The plugin properties returned by the API.
	 *
	 * @return bool
	 */
	protected function is_marketing_channel_plugin( array $plugin_data ): bool {
		if ( ! $this->is_marketing_plugin( $plugin_data ) ) {
			return false;
		}

		$subcategories = $plugin_data['subcategories'] ?? [];
		foreach ( $subcategories as $subcategory ) {
			if ( isset( $subcategory['slug'] ) && self::MARKETING_CHANNEL_SUBCATEGORY_SLUG === $subcategory['slug'] ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Load knowledge base posts from WooCommerce.com
	 *
	 * @param string|null $category Category of posts to retrieve.
	 * @return array
	 */
	public function get_knowledge_base_posts( ?string $category ): array {
		$kb_transient = self::KNOWLEDGE_BASE_TRANSIENT;

		$categories = array(
			'marketing' => 1744,
			'coupons'   => 25202,
		);

		// Default to marketing category (if no category set on the kb component).
		if ( ! empty( $category ) && array_key_exists( $category, $categories ) ) {
			$category_id  = $categories[ $category ];
			$kb_transient = $kb_transient . '_' . strtolower( $category );
		} else {
			$category_id = $categories['marketing'];
		}

		$posts = get_transient( $kb_transient );

		if ( false === $posts ) {
			$request_url = add_query_arg(
				array(
					'categories' => $category_id,
					'page'       => 1,
					'per_page'   => 8,
					'_embed'     => 1,
				),
				'https://woocommerce.com/wp-json/wp/v2/posts?utm_medium=product'
			);

			$request = wp_remote_get(
				$request_url,
				array(
					'user-agent' => 'WooCommerce/' . WC()->version . '; ' . get_bloginfo( 'url' ),
				)
			);
			$posts   = [];

			if ( ! is_wp_error( $request ) && 200 === $request['response']['code'] ) {
				$raw_posts = json_decode( $request['body'], true );

				foreach ( $raw_posts as $raw_post ) {
					$post = [
						'title'         => html_entity_decode( $raw_post['title']['rendered'] ),
						'date'          => $raw_post['date_gmt'],
						'link'          => $raw_post['link'],
						'author_name'   => isset( $raw_post['author_name'] ) ? html_entity_decode( $raw_post['author_name'] ) : '',
						'author_avatar' => isset( $raw_post['author_avatar_url'] ) ? $raw_post['author_avatar_url'] : '',
					];

					$featured_media = $raw_post['_embedded']['wp:featuredmedia'] ?? [];
					if ( count( $featured_media ) > 0 ) {
						$image         = current( $featured_media );
						$post['image'] = add_query_arg(
							array(
								'resize' => '650,340',
								'crop'   => 1,
							),
							$image['source_url']
						);
					}

					$posts[] = $post;
				}
			}

			set_transient(
				$kb_transient,
				$posts,
				// Expire transient in 15 minutes if remote get failed.
				empty( $posts ) ? 900 : DAY_IN_SECONDS
			);
		}

		return $posts;
	}
}
