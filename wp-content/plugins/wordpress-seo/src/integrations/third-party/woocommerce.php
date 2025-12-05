<?php

namespace Yoast\WP\SEO\Integrations\Third_Party;

use WPSEO_Replace_Vars;
use Yoast\WP\SEO\Conditionals\Front_End_Conditional;
use Yoast\WP\SEO\Conditionals\WooCommerce_Conditional;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\Pagination_Helper;
use Yoast\WP\SEO\Helpers\Woocommerce_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Memoizers\Meta_Tags_Context_Memoizer;
use Yoast\WP\SEO\Models\Indexable;
use Yoast\WP\SEO\Presentations\Indexable_Presentation;
use Yoast\WP\SEO\Repositories\Indexable_Repository;

/**
 * WooCommerce integration.
 */
class WooCommerce implements Integration_Interface {

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	private $options;

	/**
	 * The WPSEO Replace Vars object.
	 *
	 * @var WPSEO_Replace_Vars
	 */
	private $replace_vars;

	/**
	 * The memoizer for the meta tags context.
	 *
	 * @var Meta_Tags_Context_Memoizer
	 */
	protected $context_memoizer;

	/**
	 * The indexable repository.
	 *
	 * @var Indexable_Repository
	 */
	private $repository;

	/**
	 * The pagination helper.
	 *
	 * @var Pagination_Helper
	 */
	protected $pagination_helper;

	/**
	 * The WooCommerce helper.
	 *
	 * @var Woocommerce_Helper
	 */
	private $woocommerce_helper;

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @return array
	 */
	public static function get_conditionals() {
		return [ WooCommerce_Conditional::class, Front_End_Conditional::class ];
	}

	/**
	 * WooCommerce constructor.
	 *
	 * @param Options_Helper             $options            The options helper.
	 * @param WPSEO_Replace_Vars         $replace_vars       The replace vars helper.
	 * @param Meta_Tags_Context_Memoizer $context_memoizer   The meta tags context memoizer.
	 * @param Indexable_Repository       $repository         The indexable repository.
	 * @param Pagination_Helper          $pagination_helper  The paginataion helper.
	 * @param Woocommerce_Helper         $woocommerce_helper The WooCommerce helper.
	 */
	public function __construct(
		Options_Helper $options,
		WPSEO_Replace_Vars $replace_vars,
		Meta_Tags_Context_Memoizer $context_memoizer,
		Indexable_Repository $repository,
		Pagination_Helper $pagination_helper,
		Woocommerce_Helper $woocommerce_helper
	) {
		$this->options            = $options;
		$this->replace_vars       = $replace_vars;
		$this->context_memoizer   = $context_memoizer;
		$this->repository         = $repository;
		$this->pagination_helper  = $pagination_helper;
		$this->woocommerce_helper = $woocommerce_helper;
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_filter( 'wpseo_frontend_page_type_simple_page_id', [ $this, 'get_page_id' ] );
		\add_filter( 'wpseo_breadcrumb_indexables', [ $this, 'add_shop_to_breadcrumbs' ] );

		\add_filter( 'wpseo_title', [ $this, 'title' ], 10, 2 );
		\add_filter( 'wpseo_metadesc', [ $this, 'description' ], 10, 2 );
		\add_filter( 'wpseo_canonical', [ $this, 'canonical' ], 10, 2 );
		\add_filter( 'wpseo_adjacent_rel_url', [ $this, 'adjacent_rel_url' ], 10, 3 );
	}

	/**
	 * Returns the correct canonical when WooCommerce is enabled.
	 *
	 * @param string                      $canonical    The current canonical.
	 * @param Indexable_Presentation|null $presentation The indexable presentation.
	 *
	 * @return string The correct canonical.
	 */
	public function canonical( $canonical, $presentation = null ) {
		if ( ! $this->woocommerce_helper->is_shop_page() ) {
			return $canonical;
		}

		$url = $this->get_shop_paginated_link( 'curr', $presentation );

		if ( $url ) {
			return $url;
		}

		return $canonical;
	}

	/**
	 * Returns correct adjacent pages when WooCommerce is enabled.
	 *
	 * @param string                      $link         The current link.
	 * @param string                      $rel          Link relationship, prev or next.
	 * @param Indexable_Presentation|null $presentation The indexable presentation.
	 *
	 * @return string The correct link.
	 */
	public function adjacent_rel_url( $link, $rel, $presentation = null ) {
		if ( ! $this->woocommerce_helper->is_shop_page() ) {
			return $link;
		}

		if ( $rel !== 'next' && $rel !== 'prev' ) {
			return $link;
		}

		$url = $this->get_shop_paginated_link( $rel, $presentation );

		if ( $url ) {
			return $url;
		}

		return $link;
	}

	/**
	 * Adds a breadcrumb for the shop page.
	 *
	 * @param Indexable[] $indexables The array with indexables.
	 *
	 * @return Indexable[] The indexables to be shown in the breadcrumbs, with the shop page added.
	 */
	public function add_shop_to_breadcrumbs( $indexables ) {
		$shop_page_id = $this->woocommerce_helper->get_shop_page_id();

		if ( ! \is_int( $shop_page_id ) || $shop_page_id < 1 ) {
			return $indexables;
		}

		foreach ( $indexables as $index => $indexable ) {
			if ( $indexable->object_type === 'post-type-archive' && $indexable->object_sub_type === 'product' ) {
				$shop_page_indexable = $this->repository->find_by_id_and_type( $shop_page_id, 'post' );
				if ( \is_a( $shop_page_indexable, Indexable::class ) ) {
					$indexables[ $index ] = $shop_page_indexable;
				}
			}
		}

		return $indexables;
	}

	/**
	 * Returns the ID of the WooCommerce shop page when the currently opened page is the shop page.
	 *
	 * @param int $page_id The page id.
	 *
	 * @return int The Page ID of the shop.
	 */
	public function get_page_id( $page_id ) {
		if ( ! $this->woocommerce_helper->is_shop_page() ) {
			return $page_id;
		}

		return $this->woocommerce_helper->get_shop_page_id();
	}

	/**
	 * Handles the title.
	 *
	 * @param string                      $title        The title.
	 * @param Indexable_Presentation|null $presentation The indexable presentation.
	 *
	 * @return string The title to use.
	 */
	public function title( $title, $presentation = null ) {
		$presentation = $this->ensure_presentation( $presentation );

		if ( $presentation->model->title ) {
			return $title;
		}

		if ( ! $this->woocommerce_helper->is_shop_page() ) {
			return $title;
		}

		if ( ! \is_archive() ) {
			return $title;
		}

		$shop_page_id = $this->woocommerce_helper->get_shop_page_id();
		if ( $shop_page_id < 1 ) {
			return $title;
		}

		$product_template_title = $this->get_product_template( 'title-product', $shop_page_id );
		if ( $product_template_title ) {
			return $product_template_title;
		}

		return $title;
	}

	/**
	 * Handles the meta description.
	 *
	 * @param string                      $description  The title.
	 * @param Indexable_Presentation|null $presentation The indexable presentation.
	 *
	 * @return string The description to use.
	 */
	public function description( $description, $presentation = null ) {
		$presentation = $this->ensure_presentation( $presentation );

		if ( $presentation->model->description ) {
			return $description;
		}

		if ( ! $this->woocommerce_helper->is_shop_page() ) {
			return $description;
		}

		if ( ! \is_archive() ) {
			return $description;
		}

		$shop_page_id = $this->woocommerce_helper->get_shop_page_id();
		if ( $shop_page_id < 1 ) {
			return $description;
		}

		$product_template_description = $this->get_product_template( 'metadesc-product', $shop_page_id );
		if ( $product_template_description ) {
			return $product_template_description;
		}

		return $description;
	}

	/**
	 * Uses template for the given option name and replace the replacement variables on it.
	 *
	 * @param string $option_name  The option name to get the template for.
	 * @param string $shop_page_id The page id to retrieve template for.
	 *
	 * @return string The rendered value.
	 */
	protected function get_product_template( $option_name, $shop_page_id ) {
		$template = $this->options->get( $option_name );
		$page     = \get_post( $shop_page_id );

		return $this->replace_vars->replace( $template, $page );
	}

	/**
	 * Get paginated link for shop page.
	 *
	 * @param string                      $rel          Link relationship, prev or next or curr.
	 * @param Indexable_Presentation|null $presentation The indexable presentation.
	 *
	 * @return string|null The link.
	 */
	protected function get_shop_paginated_link( $rel, $presentation = null ) {
		$presentation = $this->ensure_presentation( $presentation );

		$permalink = $presentation->permalink;
		if ( ! $permalink ) {
			return null;
		}

		$current_page = \max( 1, $this->pagination_helper->get_current_archive_page_number() );

		if ( $rel === 'curr' && $current_page === 1 ) {
			return $permalink;
		}

		if ( $rel === 'curr' && $current_page > 1 ) {
			return $this->pagination_helper->get_paginated_url( $permalink, $current_page );
		}

		if ( $rel === 'prev' && $current_page === 2 ) {
			return $permalink;
		}

		if ( $rel === 'prev' && $current_page > 2 ) {
			return $this->pagination_helper->get_paginated_url( $permalink, ( $current_page - 1 ) );
		}

		if ( $rel === 'next' && $current_page < $this->pagination_helper->get_number_of_archive_pages() ) {
			return $this->pagination_helper->get_paginated_url( $permalink, ( $current_page + 1 ) );
		}

		return null;
	}

	/**
	 * Ensures a presentation is available.
	 *
	 * @param Indexable_Presentation $presentation The indexable presentation.
	 *
	 * @return Indexable_Presentation The presentation, taken from the current page if the input was invalid.
	 */
	protected function ensure_presentation( $presentation ) {
		if ( \is_a( $presentation, Indexable_Presentation::class ) ) {
			return $presentation;
		}

		$context = $this->context_memoizer->for_current_page();

		return $context->presentation;
	}
}
