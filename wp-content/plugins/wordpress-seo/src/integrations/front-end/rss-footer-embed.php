<?php

namespace Yoast\WP\SEO\Integrations\Front_End;

use Yoast\WP\SEO\Conditionals\Front_End_Conditional;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Class RSS_Footer_Embed.
 */
class RSS_Footer_Embed implements Integration_Interface {

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	protected $options;

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @return array
	 */
	public static function get_conditionals() {
		return [ Front_End_Conditional::class ];
	}

	/**
	 * Sets the required helpers.
	 *
	 * @codeCoverageIgnore It only handles dependencies.
	 *
	 * @param Options_Helper $options The options helper.
	 */
	public function __construct( Options_Helper $options ) {
		$this->options = $options;
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_filter( 'the_content_feed', [ $this, 'embed_rssfooter' ] );
		\add_filter( 'the_excerpt_rss', [ $this, 'embed_rssfooter_excerpt' ] );
	}

	/**
	 * Adds the RSS footer (or header) to the full RSS feed item.
	 *
	 * @param string $content Feed item content.
	 *
	 * @return string
	 */
	public function embed_rssfooter( $content ) {
		if ( ! $this->include_rss_footer( 'full' ) ) {
			return $content;
		}

		return $this->embed_rss( $content );
	}

	/**
	 * Adds the RSS footer (or header) to the excerpt RSS feed item.
	 *
	 * @param string $content Feed item excerpt.
	 *
	 * @return string
	 */
	public function embed_rssfooter_excerpt( $content ) {
		if ( ! $this->include_rss_footer( 'excerpt' ) ) {
			return $content;
		}

		return $this->embed_rss( \wpautop( $content ) );
	}

	/**
	 * Checks if the RSS footer should included.
	 *
	 * @param string $context The context of the RSS content.
	 *
	 * @return bool Whether or not the RSS footer should included.
	 */
	protected function include_rss_footer( $context ) {
		if ( ! \is_feed() ) {
			return false;
		}

		/**
		 * Filter: 'wpseo_include_rss_footer' - Allow the RSS footer to be dynamically shown/hidden.
		 *
		 * @param bool   $show_embed Indicates if the RSS footer should be shown or not.
		 * @param string $context    The context of the RSS content - 'full' or 'excerpt'.
		 */
		if ( ! \apply_filters( 'wpseo_include_rss_footer', true, $context ) ) {
			return false;
		}

		return $this->is_configured();
	}

	/**
	 * Checks if the RSS feed fields are configured.
	 *
	 * @return bool True when one of the fields has a value.
	 */
	protected function is_configured() {
		return ( $this->options->get( 'rssbefore', '' ) !== '' || $this->options->get( 'rssafter', '' ) );
	}

	/**
	 * Adds the RSS footer and/or header to an RSS feed item.
	 *
	 * @param string $content Feed item content.
	 *
	 * @return string The content to add.
	 */
	protected function embed_rss( $content ) {
		$before  = $this->rss_replace_vars( $this->options->get( 'rssbefore', '' ) );
		$after   = $this->rss_replace_vars( $this->options->get( 'rssafter', '' ) );
		$content = $before . $content . $after;

		return $content;
	}

	/**
	 * Replaces the possible RSS variables with their actual values.
	 *
	 * @param string $content The RSS content that should have the variables replaced.
	 *
	 * @return string
	 */
	protected function rss_replace_vars( $content ) {
		if ( $content === '' ) {
			return $content;
		}

		$replace_vars = $this->get_replace_vars( $this->get_link_template(), \get_post() );

		$content = \stripslashes( \trim( $content ) );
		$content = \str_ireplace( \array_keys( $replace_vars ), \array_values( $replace_vars ), $content );

		return \wpautop( $content );
	}

	/**
	 * Retrieves the replacement variables.
	 *
	 * @codeCoverageIgnore It just contains too much WordPress functions.
	 *
	 * @param string $link_template The link template.
	 * @param mixed  $post          The post to use.
	 *
	 * @return array The replacement variables.
	 */
	protected function get_replace_vars( $link_template, $post ) {
		$author_link = '';
		if ( \is_object( $post ) ) {
			$author_link = \sprintf( $link_template, \esc_url( \get_author_posts_url( $post->post_author ) ), \esc_html( \get_the_author() ) );
		}

		return [
			'%%AUTHORLINK%%'   => $author_link,
			'%%POSTLINK%%'     => \sprintf( $link_template, \esc_url( \get_permalink() ), \esc_html( \get_the_title() ) ),
			'%%BLOGLINK%%'     => \sprintf( $link_template, \esc_url( \get_bloginfo( 'url' ) ), \esc_html( \get_bloginfo( 'name' ) ) ),
			'%%BLOGDESCLINK%%' => \sprintf( $link_template, \esc_url( \get_bloginfo( 'url' ) ), \esc_html( \get_bloginfo( 'name' ) ) . ' - ' . \esc_html( \get_bloginfo( 'description' ) ) ),
		];
	}

	/**
	 * Retrieves the link template.
	 *
	 * @return string The link template.
	 */
	protected function get_link_template() {
		/**
		 * Filter: 'nofollow_rss_links' - Allow the developer to determine whether or not to follow the links in
		 * the bits Yoast SEO adds to the RSS feed, defaults to false.
		 *
		 * @since 1.4.20
		 *
		 * @param bool $unsigned Whether or not to follow the links in RSS feed, defaults to true.
		 */
		if ( \apply_filters( 'nofollow_rss_links', false ) ) {
			return '<a rel="nofollow" href="%1$s">%2$s</a>';
		}

		return '<a href="%1$s">%2$s</a>';
	}
}
