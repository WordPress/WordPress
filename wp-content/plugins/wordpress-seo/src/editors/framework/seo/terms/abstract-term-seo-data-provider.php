<?php
//phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Editors\Framework\Seo\Terms;

use WP_Term;
use WPSEO_Options;
use Yoast\WP\SEO\Editors\Domain\Seo\Seo_Plugin_Data_Interface;

/**
 * Abstract class for all term data providers.
 */
abstract class Abstract_Term_Seo_Data_Provider {

	/**
	 * The term the metabox formatter is for.
	 *
	 * @var WP_Term
	 */
	protected $term;

	/**
	 * The term.
	 *
	 * @param WP_Term $term The term.
	 *
	 * @return void
	 */
	public function set_term( WP_Term $term ): void {
		$this->term = $term;
	}

	/**
	 * Retrieves a template.
	 *
	 * @param string $template_option_name The name of the option in which the template you want to get is saved.
	 *
	 * @return string
	 */
	protected function get_template( string $template_option_name ): string {
		$needed_option = $template_option_name . '-tax-' . $this->term->taxonomy;
		return WPSEO_Options::get( $needed_option, '' );
	}

	/**
	 * Method to return the compiled SEO data.
	 *
	 * @return Seo_Plugin_Data_Interface The specific seo data.
	 */
	abstract public function get_data(): Seo_Plugin_Data_Interface;
}
