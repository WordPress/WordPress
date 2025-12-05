<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Editors\Framework\Seo\Posts;

use Yoast\WP\SEO\Editors\Domain\Seo\Seo_Plugin_Data_Interface;
use Yoast\WP\SEO\Editors\Domain\Seo\Social;
use Yoast\WP\SEO\Editors\Framework\Seo\Social_Data_Provider_Interface;
use Yoast\WP\SEO\Helpers\Image_Helper;
use Yoast\WP\SEO\Helpers\Options_Helper;

/**
 * Describes if the social SEO data.
 */
class Social_Data_Provider extends Abstract_Post_Seo_Data_Provider implements Social_Data_Provider_Interface {

	/**
	 * The image helper.
	 *
	 * @var Image_Helper
	 */
	private $image_helper;

	/**
	 * Whether we must return social templates values.
	 *
	 * @var bool
	 */
	private $use_social_templates = false;

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * The constructor.
	 *
	 * @param Options_Helper $options_helper The options helper.
	 * @param Image_Helper   $image_helper   The image helper.
	 */
	public function __construct( Options_Helper $options_helper, Image_Helper $image_helper ) {
		$this->options_helper       = $options_helper;
		$this->use_social_templates = $this->use_social_templates();
		$this->image_helper         = $image_helper;
	}

	/**
	 * Determines whether the social templates should be used.
	 *
	 * @return bool Whether the social templates should be used.
	 */
	private function use_social_templates(): bool {
		return $this->options_helper->get( 'opengraph', false ) === true;
	}

	/**
	 * Gets the image url.
	 *
	 * @return string|null
	 */
	public function get_image_url(): ?string {
		return $this->image_helper->get_post_content_image( $this->post->ID );
	}

	/**
	 * Retrieves the social title template.
	 *
	 * @return string The social title template.
	 */
	public function get_social_title_template(): string {
		if ( $this->use_social_templates ) {
			return $this->get_social_template( 'title' );
		}

		return '';
	}

	/**
	 * Retrieves the social description template.
	 *
	 * @return string The social description template.
	 */
	public function get_social_description_template(): string {
		if ( $this->use_social_templates ) {
			return $this->get_social_template( 'description' );
		}

		return '';
	}

	/**
	 * Retrieves the social image template.
	 *
	 * @return string The social description template.
	 */
	public function get_social_image_template(): string {
		if ( $this->use_social_templates ) {
			return $this->get_social_template( 'image-url' );
		}

		return '';
	}

	/**
	 * Retrieves a social template.
	 *
	 * @param string $template_option_name The name of the option in which the template you want to get is saved.
	 *
	 * @return string
	 */
	private function get_social_template( $template_option_name ) {
		/**
		 * Filters the social template value for a given post type.
		 *
		 * @param string $template             The social template value, defaults to empty string.
		 * @param string $template_option_name The subname of the option in which the template you want to get is saved.
		 * @param string $post_type            The name of the post type.
		 */
		return \apply_filters( 'wpseo_social_template_post_type', '', $template_option_name, $this->post->post_type );
	}

	/**
	 * Method to return the Social domain object with SEO data.
	 *
	 * @return Seo_Plugin_Data_Interface The specific seo data.
	 */
	public function get_data(): Seo_Plugin_Data_Interface {
		return new Social( $this->get_social_title_template(), $this->get_social_description_template(), $this->get_social_image_template(), $this->get_image_url() );
	}
}
