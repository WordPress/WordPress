<?php

namespace Yoast\WP\SEO\Integrations\Front_End;

use Yoast\WP\SEO\Conditionals\Front_End_Conditional;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Wrappers\WP_Query_Wrapper;

/**
 * Class Force_Rewrite_Title.
 */
class Force_Rewrite_Title implements Integration_Interface {

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	private $options;

	/**
	 * Toggle indicating whether output buffering has been started.
	 *
	 * @var bool
	 */
	private $ob_started = false;

	/**
	 * The WP Query wrapper.
	 *
	 * @var WP_Query_Wrapper
	 */
	private $wp_query;

	/**
	 * Sets the helpers.
	 *
	 * @codeCoverageIgnore It just handles dependencies.
	 *
	 * @param Options_Helper   $options  Options helper.
	 * @param WP_Query_Wrapper $wp_query WP query wrapper.
	 */
	public function __construct( Options_Helper $options, WP_Query_Wrapper $wp_query ) {
		$this->options  = $options;
		$this->wp_query = $wp_query;
	}

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @return array
	 */
	public static function get_conditionals() {
		return [ Front_End_Conditional::class ];
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return void
	 */
	public function register_hooks() {
		// When the option is disabled.
		if ( ! $this->options->get( 'forcerewritetitle', false ) ) {
			return;
		}

		// For WordPress versions below 4.4.
		if ( \current_theme_supports( 'title-tag' ) ) {
			return;
		}

		\add_action( 'template_redirect', [ $this, 'force_rewrite_output_buffer' ], 99999 );
		\add_action( 'wp_footer', [ $this, 'flush_cache' ], -1 );
	}

	/**
	 * Used in the force rewrite functionality this retrieves the output, replaces the title with the proper SEO
	 * title and then flushes the output.
	 *
	 * @return bool
	 */
	public function flush_cache() {
		if ( $this->ob_started !== true ) {
			return false;
		}

		$content = $this->get_buffered_output();

		$old_wp_query = $this->wp_query->get_query();

		\wp_reset_query();

		// When the file has the debug mark.
		if ( \preg_match( '/(?\'before\'.*)<!-- This site is optimized with the Yoast SEO.*<!-- \/ Yoast SEO( Premium)? plugin. -->(?\'after\'.*)/is', $content, $matches ) ) {
			$content = $this->replace_titles_from_content( $content, $matches );

			unset( $matches );
		}

		// phpcs:ignore WordPress.WP.GlobalVariablesOverride -- The query gets reset here with the original query.
		$GLOBALS['wp_query'] = $old_wp_query;

		// phpcs:ignore WordPress.Security.EscapeOutput -- The output should already have been escaped, we are only filtering it.
		echo $content;

		return true;
	}

	/**
	 * Starts the output buffer so it can later be fixed by flush_cache().
	 *
	 * @return void
	 */
	public function force_rewrite_output_buffer() {
		$this->ob_started = true;
		$this->start_output_buffering();
	}

	/**
	 * Replaces the titles from the parts that contains a title.
	 *
	 * @param string $content          The content to remove the titles from.
	 * @param array  $parts_with_title The parts containing a title.
	 *
	 * @return string The modified content.
	 */
	protected function replace_titles_from_content( $content, $parts_with_title ) {
		if ( isset( $parts_with_title['before'] ) && \is_string( $parts_with_title['before'] ) ) {
			$content = $this->replace_title( $parts_with_title['before'], $content );
		}

		if ( isset( $parts_with_title['after'] ) ) {
			$content = $this->replace_title( $parts_with_title['after'], $content );
		}

		return $content;
	}

	/**
	 * Removes the title from the part that contains the title and put this modified part back
	 * into the content.
	 *
	 * @param string $part_with_title The part with the title that needs to be replaced.
	 * @param string $content         The entire content.
	 *
	 * @return string The altered content.
	 */
	protected function replace_title( $part_with_title, $content ) {
		$part_without_title = \preg_replace( '/<title.*?\/title>/i', '', $part_with_title );

		return \str_replace( $part_with_title, $part_without_title, $content );
	}

	/**
	 * Starts the output buffering.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return void
	 */
	protected function start_output_buffering() {
		\ob_start();
	}

	/**
	 * Retrieves the buffered output.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return string|false The buffered output.
	 */
	protected function get_buffered_output() {
		return \ob_get_clean();
	}
}
