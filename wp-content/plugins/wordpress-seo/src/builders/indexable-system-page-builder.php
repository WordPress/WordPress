<?php

namespace Yoast\WP\SEO\Builders;

use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Models\Indexable;
use Yoast\WP\SEO\Values\Indexables\Indexable_Builder_Versions;

/**
 * System page builder for the indexables.
 *
 * Formats system pages ( search and error ) meta to indexable format.
 */
class Indexable_System_Page_Builder {

	/**
	 * Mapping of object type to title option keys.
	 */
	public const OPTION_MAPPING = [
		'search-result' => [
			'title'            => 'title-search-wpseo',
		],
		'404'           => [
			'title'            => 'title-404-wpseo',
			'breadcrumb_title' => 'breadcrumbs-404crumb',
		],
	];

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	protected $options;

	/**
	 * The latest version of the Indexable_System_Page_Builder.
	 *
	 * @var int
	 */
	protected $version;

	/**
	 * Indexable_System_Page_Builder constructor.
	 *
	 * @param Options_Helper             $options  The options helper.
	 * @param Indexable_Builder_Versions $versions The latest version of each Indexable Builder.
	 */
	public function __construct( Options_Helper $options, Indexable_Builder_Versions $versions ) {
		$this->options = $options;
		$this->version = $versions->get_latest_version_for_type( 'system-page' );
	}

	/**
	 * Formats the data.
	 *
	 * @param string    $object_sub_type The object sub type of the system page.
	 * @param Indexable $indexable       The indexable to format.
	 *
	 * @return Indexable The extended indexable.
	 */
	public function build( $object_sub_type, Indexable $indexable ) {
		$indexable->object_type       = 'system-page';
		$indexable->object_sub_type   = $object_sub_type;
		$indexable->title             = $this->options->get( static::OPTION_MAPPING[ $object_sub_type ]['title'] );
		$indexable->is_robots_noindex = true;
		$indexable->blog_id           = \get_current_blog_id();

		if ( \array_key_exists( 'breadcrumb_title', static::OPTION_MAPPING[ $object_sub_type ] ) ) {
			$indexable->breadcrumb_title = $this->options->get( static::OPTION_MAPPING[ $object_sub_type ]['breadcrumb_title'] );
		}

		$indexable->version = $this->version;

		return $indexable;
	}
}
