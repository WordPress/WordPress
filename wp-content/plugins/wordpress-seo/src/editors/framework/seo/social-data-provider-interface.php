<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Editors\Framework\Seo;

interface Social_Data_Provider_Interface {

	/**
	 * Gets the image URL for the post's social preview.
	 *
	 * @return string|null The image URL for the social preview.
	 */
	public function get_image_url(): ?string;

	/**
	 * Retrieves the social title template.
	 *
	 * @return string The social title template.
	 */
	public function get_social_title_template(): string;

	/**
	 * Retrieves the social description template.
	 *
	 * @return string The social description template.
	 */
	public function get_social_description_template(): string;

	/**
	 * Retrieves the social image template.
	 *
	 * @return string The social description template.
	 */
	public function get_social_image_template(): string;
}
