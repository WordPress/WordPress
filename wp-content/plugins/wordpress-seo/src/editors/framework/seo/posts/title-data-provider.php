<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Editors\Framework\Seo\Posts;

use Yoast\WP\SEO\Editors\Domain\Seo\Seo_Plugin_Data_Interface;
use Yoast\WP\SEO\Editors\Domain\Seo\Title;
use Yoast\WP\SEO\Editors\Framework\Seo\Title_Data_Provider_Interface;
use Yoast\WP\SEO\Helpers\Options_Helper;

/**
 * Describes if the title SEO data.
 */
class Title_Data_Provider extends Abstract_Post_Seo_Data_Provider implements Title_Data_Provider_Interface {

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
	 */
	public function __construct( Options_Helper $options_helper ) {
		$this->options_helper = $options_helper;
	}

	/**
	 * Retrieves the title template.
	 *
	 * @param bool $fallback Whether to return the hardcoded fallback if the template value is empty.
	 *
	 * @return string The title template.
	 */
	public function get_title_template( bool $fallback = true ): string {
		$title = $this->get_template( 'title' );

		if ( $title === '' && $fallback === true ) {
			return '%%title%% %%page%% %%sep%% %%sitename%%';
		}

		return $title;
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
	 * Method to return the Title domain object with SEO data.
	 *
	 * @return Seo_Plugin_Data_Interface The specific seo data.
	 */
	public function get_data(): Seo_Plugin_Data_Interface {
		return new Title( $this->get_title_template(), $this->get_title_template( false ) );
	}
}
