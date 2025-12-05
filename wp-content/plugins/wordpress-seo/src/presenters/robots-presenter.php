<?php

namespace Yoast\WP\SEO\Presenters;

/**
 * Presenter class for the robots output.
 */
class Robots_Presenter extends Abstract_Indexable_Presenter {

	/**
	 * The tag key name.
	 *
	 * @var string
	 */
	protected $key = 'robots';

	/**
	 * Returns the robots output.
	 *
	 * @return string The robots output tag.
	 */
	public function present() {
		$robots = \implode( ', ', $this->get() );

		if ( \is_string( $robots ) && $robots !== '' ) {
			return \sprintf( '<meta name="robots" content="%s" />', \esc_attr( $robots ) );
		}

		return '';
	}

	/**
	 * Gets the raw value of a presentation.
	 *
	 * @return array The raw value.
	 */
	public function get() {
		return $this->presentation->robots;
	}
}
