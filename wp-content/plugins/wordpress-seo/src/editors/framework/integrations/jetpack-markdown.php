<?php

// @phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- This namespace should reflect the namespace of the original class.
namespace Yoast\WP\SEO\Editors\Framework\Integrations;

use Jetpack;
use Yoast\WP\SEO\Editors\Domain\Integrations\Integration_Data_Provider_Interface;

/**
 * Describes if the Jetpack markdown integration is enabled.
 */
class Jetpack_Markdown implements Integration_Data_Provider_Interface {

	/**
	 * If the integration is activated.
	 *
	 * @return bool If the integration is activated.
	 */
	public function is_enabled(): bool {
		return $this->is_markdown_enabled();
	}

	/**
	 * Return this object represented by a key value array.
	 *
	 * @return array<string, bool> Returns the name and if the feature is enabled.
	 */
	public function to_array(): array {
		return [
			'markdownEnabled' => $this->is_enabled(),
		];
	}

	/**
	 * Returns this object represented by a key value structure that is compliant with the script data array.
	 *
	 * @return array<string, bool> Returns the legacy key and if the feature is enabled.
	 */
	public function to_legacy_array(): array {
		return [
			'markdownEnabled' => $this->is_enabled(),
		];
	}

	/**
	 * Checks if Jetpack's markdown module is enabled.
	 * Can be extended to work with other plugins that parse markdown in the content.
	 *
	 * @return bool
	 */
	private function is_markdown_enabled() {
		$is_markdown = false;

		if ( \class_exists( 'Jetpack' ) && \method_exists( 'Jetpack', 'get_active_modules' ) ) {
			$active_modules = Jetpack::get_active_modules();

			// First at all, check if Jetpack's markdown module is active.
			$is_markdown = \in_array( 'markdown', $active_modules, true );
		}

		/**
		 * Filters whether markdown support is active in the readability- and seo-analysis.
		 *
		 * @since 11.3
		 *
		 * @param array $is_markdown Is markdown support for Yoast SEO active.
		 */
		return \apply_filters( 'wpseo_is_markdown_enabled', $is_markdown );
	}
}
