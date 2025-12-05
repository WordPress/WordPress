<?php

namespace Yoast\WP\SEO\Surfaces\Values;

use WPSEO_Replace_Vars;
use Yoast\WP\SEO\Context\Meta_Tags_Context;
use Yoast\WP\SEO\Exceptions\Forbidden_Property_Mutation_Exception;
use Yoast\WP\SEO\Integrations\Front_End_Integration;
use Yoast\WP\SEO\Models\Indexable;
use Yoast\WP\SEO\Presenters\Abstract_Indexable_Presenter;
use Yoast\WP\SEO\Presenters\Rel_Next_Presenter;
use Yoast\WP\SEO\Presenters\Rel_Prev_Presenter;
use Yoast\WP\SEO\Surfaces\Helpers_Surface;
use YoastSEO_Vendor\Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Meta value object.
 *
 * @property array       $breadcrumbs                       The breadcrumbs array for the current page.
 * @property string      $canonical                         The canonical URL for the current page.
 * @property string      $company_name                      The company name from the Knowledge graph settings.
 * @property int         $company_logo_id                   The attachment ID for the company logo.
 * @property string      $description                       The meta description for the current page, if set.
 * @property int         $estimated_reading_time_minutes    The estimated reading time in minutes for posts.
 * @property Indexable   $indexable                         The indexable object.
 * @property string      $main_schema_id                    Schema ID that points to the main Schema thing on the page, usually the webpage or article Schema piece.
 * @property string      $meta_description                  The meta description for the current page, if set.
 * @property string      $open_graph_article_author         The article:author value.
 * @property string      $open_graph_article_modified_time  The article:modified_time value.
 * @property string      $open_graph_article_published_time The article:published_time value.
 * @property string      $open_graph_article_publisher      The article:publisher value.
 * @property string      $open_graph_description            The og:description.
 * @property bool        $open_graph_enabled                Whether OpenGraph is enabled on this site.
 * @property string      $open_graph_fb_app_id              The Facebook App ID.
 * @property array       $open_graph_images                 The array of images we have for this page.
 * @property string      $open_graph_locale                 The og:locale for the current page.
 * @property string      $open_graph_publisher              The OpenGraph publisher reference.
 * @property string      $open_graph_site_name              The og:site_name.
 * @property string      $open_graph_title                  The og:title.
 * @property string      $open_graph_type                   The og:type.
 * @property string      $open_graph_url                    The og:url.
 * @property string      $page_type                         The Schema page type.
 * @property array       $robots                            An array of the robots values set for the current page.
 * @property string      $rel_next                          The next page in the series, if any.
 * @property string      $rel_prev                          The previous page in the series, if any.
 * @property array       $schema                            The entire Schema array for the current page.
 * @property string      $schema_page_type                  The Schema page type.
 * @property string      $site_name                         The site name from the Yoast SEO settings.
 * @property string      $site_represents                   Whether the site represents a 'person' or a 'company'.
 * @property array|false $site_represents_reference         The schema reference ID for what this site represents.
 * @property string      $site_url                          The site's main URL.
 * @property int         $site_user_id                      If the site represents a 'person', this is the ID of the accompanying user profile.
 * @property string      $title                             The SEO title for the current page.
 * @property string      $twitter_card                      The Twitter card type for the current page.
 * @property string      $twitter_creator                   The Twitter card author for the current page.
 * @property string      $twitter_description               The Twitter card description for the current page.
 * @property string      $twitter_image                     The Twitter card image for the current page.
 * @property string      $twitter_site                      The Twitter card site reference for the current page.
 * @property string      $twitter_title                     The Twitter card title for the current page.
 * @property string      $wordpress_site_name               The site name from the WordPress settings.
 */
class Meta {

	/**
	 * The container.
	 *
	 * @var ContainerInterface
	 */
	protected $container;

	/**
	 * The meta tags context.
	 *
	 * @var Meta_Tags_Context
	 */
	protected $context;

	/**
	 * The front end integration.
	 *
	 * @var Front_End_Integration
	 */
	protected $front_end;

	/**
	 * The helpers surface.
	 *
	 * @var Helpers_Surface
	 */
	protected $helpers;

	/**
	 * The replace vars helper
	 *
	 * @var WPSEO_Replace_Vars
	 */
	protected $replace_vars;

	/**
	 * Collection of properties dynamically set via the magic __get() method.
	 *
	 * @var array<string, mixed> Key is the property name.
	 */
	private $properties_bin = [];

	/**
	 * Create a meta value object.
	 *
	 * @param Meta_Tags_Context  $context   The indexable presentation.
	 * @param ContainerInterface $container The DI container.
	 */
	public function __construct( Meta_Tags_Context $context, ContainerInterface $container ) {
		$this->container = $container;
		$this->context   = $context;

		$this->helpers      = $this->container->get( Helpers_Surface::class );
		$this->replace_vars = $this->container->get( WPSEO_Replace_Vars::class );
		$this->front_end    = $this->container->get( Front_End_Integration::class );
	}

	/**
	 * Returns the output as would be presented in the head.
	 *
	 * @return object The HTML and JSON presentation of the head metadata.
	 */
	public function get_head() {
		$presenters = $this->get_presenters();

		/** This filter is documented in src/integrations/front-end-integration.php */
		$presentation = \apply_filters( 'wpseo_frontend_presentation', $this->context->presentation, $this->context );

		$html_output      = '';
		$json_head_fields = [];

		foreach ( $presenters as $presenter ) {
			$presenter->presentation = $presentation;
			$presenter->replace_vars = $this->replace_vars;
			$presenter->helpers      = $this->helpers;

			$html_output .= $this->create_html_presentation( $presenter );
			$json_field   = $this->create_json_field( $presenter );

			// Only use the output of presenters that could successfully present their data.
			if ( $json_field !== null && ! empty( $json_field->key ) ) {
				$json_head_fields[ $json_field->key ] = $json_field->value;
			}
		}
		$html_output = \trim( $html_output );

		return (object) [
			'html' => $html_output,
			'json' => $json_head_fields,
		];
	}

	/**
	 * Magic getter for presenting values through the appropriate presenter, if it exists.
	 *
	 * @param string $name The property to get.
	 *
	 * @return mixed The value, as presented by the appropriate presenter.
	 */
	public function __get( $name ) {
		if ( \array_key_exists( $name, $this->properties_bin ) ) {
			return $this->properties_bin[ $name ];
		}

		/** This filter is documented in src/integrations/front-end-integration.php */
		$presentation = \apply_filters( 'wpseo_frontend_presentation', $this->context->presentation, $this->context );

		if ( ! isset( $presentation->{$name} ) ) {
			if ( isset( $this->context->{$name} ) ) {
				$this->properties_bin[ $name ] = $this->context->{$name};
				return $this->properties_bin[ $name ];
			}
			return null;
		}

		$presenter_namespace = 'Yoast\WP\SEO\Presenters\\';
		$parts               = \explode( '_', $name );
		if ( $parts[0] === 'twitter' ) {
			$presenter_namespace .= 'Twitter\\';
			$parts                = \array_slice( $parts, 1 );
		}
		elseif ( $parts[0] === 'open' && $parts[1] === 'graph' ) {
			$presenter_namespace .= 'Open_Graph\\';
			$parts                = \array_slice( $parts, 2 );
		}

		$presenter_class = $presenter_namespace . \implode( '_', \array_map( 'ucfirst', $parts ) ) . '_Presenter';

		if ( \class_exists( $presenter_class ) ) {
			/**
			 * The indexable presenter.
			 *
			 * @var Abstract_Indexable_Presenter $presenter
			 */
			$presenter               = new $presenter_class();
			$presenter->presentation = $presentation;
			$presenter->helpers      = $this->helpers;
			$presenter->replace_vars = $this->replace_vars;
			$value                   = $presenter->get();
		}
		else {
			$value = $presentation->{$name};
		}

		$this->properties_bin[ $name ] = $value;
		return $this->properties_bin[ $name ];
	}

	/**
	 * Magic isset for ensuring properties on the presentation are recognised.
	 *
	 * @param string $name The property to get.
	 *
	 * @return bool Whether or not the requested property exists.
	 */
	public function __isset( $name ) {
		if ( \array_key_exists( $name, $this->properties_bin ) ) {
			return true;
		}

		return isset( $this->context->presentation->{$name} );
	}

	/**
	 * Prevents setting dynamic properties and overwriting the value of declared properties
	 * from an inaccessible context.
	 *
	 * @param string $name  The property name.
	 * @param mixed  $value The property value.
	 *
	 * @return void
	 *
	 * @throws Forbidden_Property_Mutation_Exception Set is never meant to be called.
	 */
	public function __set( $name, $value ) { // @phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed -- __set must have a name and value - PHPCS #3715.
		throw Forbidden_Property_Mutation_Exception::cannot_set_because_property_is_immutable( $name );
	}

	/**
	 * Prevents unsetting dynamic properties and unsetting declared properties
	 * from an inaccessible context.
	 *
	 * @param string $name The property name.
	 *
	 * @return void
	 *
	 * @throws Forbidden_Property_Mutation_Exception Unset is never meant to be called.
	 */
	public function __unset( $name ) {
		throw Forbidden_Property_Mutation_Exception::cannot_unset_because_property_is_immutable( $name );
	}

	/**
	 * Strips all nested dependencies from the debug info.
	 *
	 * @return array
	 */
	public function __debugInfo() {
		return [ 'context' => $this->context ];
	}

	/**
	 * Returns all presenters.
	 *
	 * @return Abstract_Indexable_Presenter[]
	 */
	protected function get_presenters() {
		$presenters = $this->front_end->get_presenters( $this->context->page_type, $this->context );

		if ( $this->context->page_type === 'Date_Archive' ) {
			/**
			 * Define a filter that removes objects of type Rel_Next_Presenter or Rel_Prev_Presenter from a list.
			 *
			 * @param object $presenter The presenter to verify.
			 *
			 * @return bool True if the presenter is not a Rel_Next or Rel_Prev presenter.
			 */
			$callback   = static function ( $presenter ) {
				return ! \is_a( $presenter, Rel_Next_Presenter::class )
					&& ! \is_a( $presenter, Rel_Prev_Presenter::class );
			};
			$presenters = \array_filter( $presenters, $callback );
		}

		return $presenters;
	}

	/**
	 * Uses the presenter to create a line of HTML.
	 *
	 * @param Abstract_Indexable_Presenter $presenter The presenter.
	 *
	 * @return string
	 */
	protected function create_html_presentation( $presenter ) {
		$presenter_output = $presenter->present();
		if ( ! empty( $presenter_output ) ) {
			return $presenter_output . \PHP_EOL;
		}
		return '';
	}

	/**
	 * Converts a presenter's key and value to JSON.
	 *
	 * @param Abstract_Indexable_Presenter $presenter The presenter whose key and value are to be converted to JSON.
	 *
	 * @return object|null
	 */
	protected function create_json_field( $presenter ) {
		if ( $presenter->get_key() === 'NO KEY PROVIDED' ) {
			return null;
		}

		$value = $presenter->get();
		if ( empty( $value ) ) {
			return null;
		}

		return (object) [
			'key'   => $presenter->escape_key(),
			'value' => $value,
		];
	}
}
