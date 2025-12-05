<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Editors\Application\Site;

use Yoast\WP\SEO\Editors\Framework\Site\Post_Site_Information;
use Yoast\WP\SEO\Editors\Framework\Site\Term_Site_Information;

/**
 * This class manages getting the two site information wrappers.
 *
 * @makePublic
 */
class Website_Information_Repository {

	/**
	 * The post site information wrapper.
	 *
	 * @var Post_Site_Information
	 */
	private $post_site_information;

	/**
	 * The term site information wrapper.
	 *
	 * @var Term_Site_Information
	 */
	private $term_site_information;

	/**
	 * The constructor.
	 *
	 * @param Post_Site_Information $post_site_information The post specific wrapper.
	 * @param Term_Site_Information $term_site_information The term specific wrapper.
	 */
	public function __construct(
		Post_Site_Information $post_site_information,
		Term_Site_Information $term_site_information
	) {
		$this->post_site_information = $post_site_information;
		$this->term_site_information = $term_site_information;
	}

	/**
	 * Returns the Post Site Information container.
	 *
	 * @return Post_Site_Information
	 */
	public function get_post_site_information(): Post_Site_Information {
		return $this->post_site_information;
	}

	/**
	 * Returns the Term Site Information container.
	 *
	 * @return Term_Site_Information
	 */
	public function get_term_site_information(): Term_Site_Information {
		return $this->term_site_information;
	}
}
