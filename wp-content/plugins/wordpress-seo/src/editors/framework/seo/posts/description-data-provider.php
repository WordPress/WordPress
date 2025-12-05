<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Editors\Framework\Seo\Posts;

use Yoast\WP\SEO\Editors\Domain\Seo\Description;
use Yoast\WP\SEO\Editors\Domain\Seo\Seo_Plugin_Data_Interface;
use Yoast\WP\SEO\Editors\Framework\Seo\Description_Data_Provider_Interface;
use Yoast\WP\SEO\Helpers\Date_Helper;
use Yoast\WP\SEO\Helpers\Options_Helper;

/**
 * Describes if the description SEO data.
 */
class Description_Data_Provider extends Abstract_Post_Seo_Data_Provider implements Description_Data_Provider_Interface {

	/**
	 * The date helper.
	 *
	 * @var Date_Helper
	 */
	private $date_helper;

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * The constructor.
	 *
	 * @param Date_Helper    $date_helper    The date helper.
	 * @param Options_Helper $options_helper The options helper.
	 */
	public function __construct( Date_Helper $date_helper, Options_Helper $options_helper ) {
		$this->date_helper    = $date_helper;
		$this->options_helper = $options_helper;
	}

	/**
	 * Retrieves the description template.
	 *
	 * @return string The description template.
	 */
	public function get_description_template(): string {
		return $this->get_template( 'metadesc' );
	}

	/**
	 * Determines the date to be displayed in the snippet preview.
	 *
	 * @return string
	 */
	public function get_description_date(): string {
		return $this->date_helper->format_translated( $this->post->post_date, 'M j, Y' );
	}

	/**
	 * Retrieves a template.
	 *
	 * @param string $template_option_name The name of the option in which the template you want to get is saved.
	 *
	 * @return string
	 */
	private function get_template( string $template_option_name ): string {
		$needed_option = $template_option_name . '-' . $this->post->post_type;

		if ( $this->options_helper->get( $needed_option, '' ) !== '' ) {
			return $this->options_helper->get( $needed_option );
		}

		return '';
	}

	/**
	 * Method to return the Description domain object with SEO data.
	 *
	 * @return Seo_Plugin_Data_Interface The specific seo data.
	 */
	public function get_data(): Seo_Plugin_Data_Interface {
		return new Description( $this->get_description_date(), $this->get_description_template() );
	}
}
