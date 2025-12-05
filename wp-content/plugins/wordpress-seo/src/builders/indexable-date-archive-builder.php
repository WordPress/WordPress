<?php

namespace Yoast\WP\SEO\Builders;

use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Models\Indexable;
use Yoast\WP\SEO\Values\Indexables\Indexable_Builder_Versions;

/**
 * Date Archive Builder for the indexables.
 *
 * Formats the date archive meta to indexable format.
 */
class Indexable_Date_Archive_Builder {

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	private $options;

	/**
	 * The latest version of the Indexable_Date_Archive_Builder.
	 *
	 * @var int
	 */
	protected $version;

	/**
	 * Indexable_Date_Archive_Builder constructor.
	 *
	 * @param Options_Helper             $options  The options helper.
	 * @param Indexable_Builder_Versions $versions The latest version for all indexable builders.
	 */
	public function __construct( Options_Helper $options, Indexable_Builder_Versions $versions ) {
		$this->options = $options;
		$this->version = $versions->get_latest_version_for_type( 'date-archive' );
	}

	/**
	 * Formats the data.
	 *
	 * @param Indexable $indexable The indexable to format.
	 *
	 * @return Indexable The extended indexable.
	 */
	public function build( $indexable ) {
		$indexable->object_type       = 'date-archive';
		$indexable->title             = $this->options->get( 'title-archive-wpseo' );
		$indexable->description       = $this->options->get( 'metadesc-archive-wpseo' );
		$indexable->is_robots_noindex = $this->options->get( 'noindex-archive-wpseo' );
		$indexable->is_public         = ( (int) $indexable->is_robots_noindex !== 1 );
		$indexable->blog_id           = \get_current_blog_id();
		$indexable->permalink         = null;
		$indexable->version           = $this->version;

		return $indexable;
	}
}
