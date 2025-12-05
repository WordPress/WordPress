<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Editors\Framework\Seo\Posts;

use WPSEO_Meta;
use Yoast\WP\SEO\Editors\Domain\Seo\Keyphrase;
use Yoast\WP\SEO\Editors\Domain\Seo\Seo_Plugin_Data_Interface;
use Yoast\WP\SEO\Editors\Framework\Seo\Keyphrase_Interface;
use Yoast\WP\SEO\Helpers\Meta_Helper;

/**
 * Describes if the keyphrase SEO data.
 */
class Keyphrase_Data_Provider extends Abstract_Post_Seo_Data_Provider implements Keyphrase_Interface {

	/**
	 *  The meta helper.
	 *
	 * @var Meta_Helper
	 */
	private $meta_helper;

	/**
	 * The constructor.
	 *
	 * @param Meta_Helper $meta_helper The meta helper.
	 */
	public function __construct( Meta_Helper $meta_helper ) {
		$this->meta_helper = $meta_helper;
	}

	/**
	 * Counts the number of given Keyphrase used for other posts other than the given post_id.
	 *
	 * @return array<string> The keyphrase and the associated posts that use it.
	 */
	public function get_focus_keyphrase_usage(): array {
		$keyphrase = $this->meta_helper->get_value( 'focuskw', $this->post->ID );
		$usage     = [ $keyphrase => $this->get_keyphrase_usage_for_current_post( $keyphrase ) ];

		/**
		 * Allows enhancing the array of posts' that share their focus Keyphrase with the post's related Keyphrase.
		 *
		 * @param array<string> $usage   The array of posts' ids that share their focus Keyphrase with the post.
		 * @param int           $post_id The id of the post we're finding the usage of related Keyphrase for.
		 */
		return \apply_filters( 'wpseo_posts_for_related_keywords', $usage, $this->post->ID );
	}

	/**
	 * Retrieves the post types for the given post IDs.
	 *
	 * @param array<string|array<string>> $post_ids_per_keyphrase An associative array with keyphrase as keys and an array of post ids where those keyphrases are used.
	 *
	 * @return array<string|array<string>> The post types for the given post IDs.
	 */
	public function get_post_types_for_all_ids( array $post_ids_per_keyphrase ): array {
		$post_type_per_keyphrase_result = [];
		foreach ( $post_ids_per_keyphrase as $keyphrase => $post_ids ) {
			$post_type_per_keyphrase_result[ $keyphrase ] = WPSEO_Meta::post_types_for_ids( $post_ids );
		}

		return $post_type_per_keyphrase_result;
	}

	/**
	 * Gets the keyphrase usage for the current post and the specified keyphrase.
	 *
	 * @param string $keyphrase The keyphrase to check the usage of.
	 *
	 * @return array<string> The post IDs which use the passed keyphrase.
	 */
	private function get_keyphrase_usage_for_current_post( string $keyphrase ): array {
		return WPSEO_Meta::keyword_usage( $keyphrase, $this->post->ID );
	}

	/**
	 * Method to return the keyphrase domain object with SEO data.
	 *
	 * @return Seo_Plugin_Data_Interface The specific seo data.
	 */
	public function get_data(): Seo_Plugin_Data_Interface {
		$keyphrase_usage = $this->get_focus_keyphrase_usage();

		return new Keyphrase( $keyphrase_usage, $this->get_post_types_for_all_ids( $keyphrase_usage ) );
	}
}
