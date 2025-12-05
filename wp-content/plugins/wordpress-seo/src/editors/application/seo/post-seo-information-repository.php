<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Editors\Application\Seo;

use WP_Post;
use Yoast\WP\SEO\Editors\Framework\Seo\Posts\Abstract_Post_Seo_Data_Provider;

/**
 * The repository to get post related SEO data.
 *
 * @makePublic
 */
class Post_Seo_Information_Repository {

	/**
	 * The post.
	 *
	 * @var WP_Post
	 */
	private $post;

	/**
	 * The data providers.
	 *
	 * @var Abstract_Post_Seo_Data_Provider
	 */
	private $seo_data_providers;

	/**
	 * The constructor.
	 *
	 * @param Abstract_Post_Seo_Data_Provider ...$seo_data_providers The providers.
	 */
	public function __construct( Abstract_Post_Seo_Data_Provider ...$seo_data_providers ) {
		$this->seo_data_providers = $seo_data_providers;
	}

	/**
	 * The post.
	 *
	 * @param WP_Post $post The post.
	 *
	 * @return void
	 */
	public function set_post( WP_Post $post ) {
		$this->post = $post;
	}

	/**
	 * Method to return the compiled SEO data.
	 *
	 * @return array<string> The specific seo data.
	 */
	public function get_seo_data(): array {
		$array = [];
		foreach ( $this->seo_data_providers as $data_provider ) {
			$data_provider->set_post( $this->post );
			$array = \array_merge( $array, $data_provider->get_data()->to_legacy_array() );
		}
		return $array;
	}
}
