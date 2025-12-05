<?php

namespace Yoast\WP\SEO\Presenters\Debug;

use Yoast\WP\SEO\Presenters\Abstract_Indexable_Presenter;

/**
 * Presenter class for the debug open marker.
 */
final class Marker_Open_Presenter extends Abstract_Indexable_Presenter {

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
		$version_info = 'v' . \WPSEO_VERSION;

		if ( $this->helpers->product->is_premium() ) {
			$version_info = $this->construct_version_info();
		}

		return \sprintf(
			'<!-- This site is optimized with the %1$s %2$s - https://yoast.com/wordpress/plugins/seo/ -->',
			\esc_html( $this->helpers->product->get_name() ),
			$version_info
		);
	}

	/**
	 * Gets the plugin version information, including the free version if Premium is used.
	 *
	 * @return string The constructed version information.
	 */
	private function construct_version_info() {
		/**
		 * Filter: 'wpseo_hide_version' - can be used to hide the Yoast SEO version in the debug marker (only available in Yoast SEO Premium).
		 *
		 * @param bool $hide_version
		 */
		if ( \apply_filters( 'wpseo_hide_version', false ) ) {
			return '';
		}

		return 'v' . \WPSEO_PREMIUM_VERSION . ' (Yoast SEO v' . \WPSEO_VERSION . ')';
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
