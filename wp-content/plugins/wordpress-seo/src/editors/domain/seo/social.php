<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Editors\Domain\Seo;

/**
 * This class describes the social SEO data.
 */
class Social implements Seo_Plugin_Data_Interface {

	/**
	 * The Social title template.
	 *
	 * @var string
	 */
	private $social_title_template;

	/**
	 * The Social description template.
	 *
	 * @var string
	 */
	private $social_description_template;

	/**
	 * The Social image template.
	 *
	 * @var string
	 */
	private $social_image_template;

	/**
	 * The first content image for the social preview.
	 *
	 * @var string
	 */
	private $social_first_content_image;

	/**
	 * The constructor.
	 *
	 * @param string $social_title_template       The Social title template.
	 * @param string $social_description_template The Social description template.
	 * @param string $social_image_template       The Social image template.
	 * @param string $social_first_content_image  The first content image for the social preview.
	 */
	public function __construct(
		string $social_title_template,
		string $social_description_template,
		string $social_image_template,
		string $social_first_content_image
	) {
		$this->social_title_template       = $social_title_template;
		$this->social_description_template = $social_description_template;
		$this->social_image_template       = $social_image_template;
		$this->social_first_content_image  = $social_first_content_image;
	}

	/**
	 * Returns the data as an array format.
	 *
	 * @return array<string>
	 */
	public function to_array(): array {
		return [
			'social_title_template'              => $this->social_title_template,
			'social_description_template'        => $this->social_description_template,
			'social_image_template'              => $this->social_image_template,
			'first_content_image_social_preview' => $this->social_first_content_image,
		];
	}

	/**
	 * Returns the data as an array format meant for legacy use.
	 *
	 * @return array<string>
	 */
	public function to_legacy_array(): array {
		return [
			'social_title_template'       => $this->social_title_template,
			'social_description_template' => $this->social_description_template,
			'social_image_template'       => $this->social_image_template,
			'first_content_image'         => $this->social_first_content_image,
		];
	}
}
