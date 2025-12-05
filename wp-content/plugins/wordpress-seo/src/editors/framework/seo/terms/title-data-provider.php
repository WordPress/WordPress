<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Editors\Framework\Seo\Terms;

use Yoast\WP\SEO\Editors\Domain\Seo\Seo_Plugin_Data_Interface;
use Yoast\WP\SEO\Editors\Domain\Seo\Title;
use Yoast\WP\SEO\Editors\Framework\Seo\Title_Data_Provider_Interface;

/**
 * Describes if the title SEO data.
 */
class Title_Data_Provider extends Abstract_Term_Seo_Data_Provider implements Title_Data_Provider_Interface {

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
			/* translators: %s expands to the variable used for term title. */
			$archives = \sprintf( \__( '%s Archives', 'wordpress-seo' ), '%%term_title%%' );
			return $archives . ' %%page%% %%sep%% %%sitename%%';
		}

		return $title;
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
