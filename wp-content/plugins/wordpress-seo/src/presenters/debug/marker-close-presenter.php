<?php

namespace Yoast\WP\SEO\Presenters\Debug;

use Yoast\WP\SEO\Presenters\Abstract_Indexable_Presenter;

/**
 * Presenter class for the debug close marker.
 */
final class Marker_Close_Presenter extends Abstract_Indexable_Presenter {

	/**
	 * Returns the debug close marker.
	 *
	 * @return string The debug close marker.
	 */
	public function present() {
		/**
		 * Filter: 'wpseo_debug_markers' - Allow disabling the debug markers.
		 *
		 * @param bool $show_markers True when the debug markers should be shown.
		 */
		if ( ! \apply_filters( 'wpseo_debug_markers', true ) ) {
			return '';
		}

		return \sprintf(
			'<!-- / %s. -->',
			\esc_html( $this->helpers->product->get_name() )
		);
	}

	/**
	 * Gets the raw value of a presentation.
	 *
	 * @return string The raw value.
	 */
	public function get() {
		return '';
	}
}
