<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Editors\Framework\Seo\Posts;

use WP_Post;
use Yoast\WP\SEO\Editors\Domain\Seo\Seo_Plugin_Data_Interface;

/**
 * Abstract class for all post data providers.
 */
abstract class Abstract_Post_Seo_Data_Provider {

	/**
	 * Holds the WordPress Post.
	 *
	 * @var WP_Post
	 */
	protected $post;

	/**
	 * The post.
	 *
	 * @param WP_Post $post The post.
	 *
	 * @return void
	 */
	public function set_post( WP_Post $post ): void {
		$this->post = $post;
	}

	/**
	 * Method to return the compiled SEO data.
	 *
	 * @return Seo_Plugin_Data_Interface The specific seo data.
	 */
	abstract public function get_data(): Seo_Plugin_Data_Interface;
}
