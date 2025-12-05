<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Llms_Txt\Infrastructure\Markdown_Services;

use Yoast\WP\SEO\Llms_Txt\Domain\Markdown\Sections\Description;
use Yoast\WP\SEO\Surfaces\Meta_Surface;

/**
 * The adapter of the description.
 */
class Description_Adapter {

	/**
	 * Holds the meta helper surface.
	 *
	 * @var Meta_Surface
	 */
	private $meta;

	/**
	 * Class constructor.
	 *
	 * @param Meta_Surface $meta The meta surface.
	 */
	public function __construct(
		Meta_Surface $meta
	) {
		$this->meta = $meta;
	}

	/**
	 * Gets the description.
	 *
	 * @return Description The description.
	 */
	public function get_description(): Description {
		$meta_description = $this->meta->for_home_page()->meta_description;

		// In a lot of cases, the homepage's meta description falls back to the site's tagline.
		// But that is already used for the title section, so let's try to not have duplicate content.
		if ( $meta_description === \get_bloginfo( 'description' ) ) {
			return new Description( '' );
		}

		return new Description( $meta_description );
	}
}
