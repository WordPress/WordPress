<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Llms_Txt\Infrastructure\Content;

use Exception;
use Yoast\WP\SEO\Llms_Txt\Domain\content\Post_Collection_Interface;


/**
 * The factory to determine which post collection class to use.
 */
class Post_Collection_Factory {

	/**
	 * The manual post collection.
	 *
	 * @var Manual_Post_Collection
	 */
	private $manual_post_collection;

	/**
	 * The automatic post collection.
	 *
	 * @var Automatic_Post_Collection
	 */
	private $automatic_post_collection;

	/**
	 * Constructor.
	 *
	 * @param Manual_Post_Collection    $manual_post_collection    The manual post collection.
	 * @param Automatic_Post_Collection $automatic_post_collection The automatic post collection.
	 */
	public function __construct( Manual_Post_Collection $manual_post_collection, Automatic_Post_Collection $automatic_post_collection ) {
		$this->manual_post_collection    = $manual_post_collection;
		$this->automatic_post_collection = $automatic_post_collection;
	}

	/**
	 * Determines which collection class is needed.
	 *
	 * @param string $collection_type The type of collection.
	 *
	 * @throws Exception Throws when an invalid type is given.
	 * @return Post_Collection_Interface
	 */
	public function get_post_collection( string $collection_type ): Post_Collection_Interface {
		switch ( $collection_type ) {
			case 'manual':
				return $this->manual_post_collection;
			case 'auto':
				return $this->automatic_post_collection;
			default:
				throw new Exception( 'Invalid collection type provided' );
		}
	}
}
