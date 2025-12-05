<?php

namespace Yoast\WP\SEO\Presentations;

use Yoast\WP\SEO\Context\Meta_Tags_Context;
use Yoast\WP\SEO\Generators\Breadcrumbs_Generator;
use Yoast\WP\SEO\Generators\Open_Graph_Image_Generator;
use Yoast\WP\SEO\Generators\Open_Graph_Locale_Generator;
use Yoast\WP\SEO\Generators\Schema_Generator;
use Yoast\WP\SEO\Generators\Twitter_Image_Generator;
use Yoast\WP\SEO\Helpers\Current_Page_Helper;
use Yoast\WP\SEO\Helpers\Image_Helper;
use Yoast\WP\SEO\Helpers\Indexable_Helper;
use Yoast\WP\SEO\Helpers\Open_Graph\Values_Helper;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\Permalink_Helper;
use Yoast\WP\SEO\Helpers\Url_Helper;
use Yoast\WP\SEO\Helpers\User_Helper;
use Yoast\WP\SEO\Models\Indexable;

/**
 * Class Indexable_Presentation.
 *
 * Presentation object for indexables.
 *
 * @property string       $title
 * @property string       $meta_description
 * @property array        $robots
 * @property string       $canonical
 * @property string       $rel_next
 * @property string       $rel_prev
 * @property string       $open_graph_type
 * @property string       $open_graph_title
 * @property string       $open_graph_description
 * @property array        $open_graph_images
 * @property int          $open_graph_image_id
 * @property string       $open_graph_image
 * @property string       $open_graph_url
 * @property string       $open_graph_site_name
 * @property string       $open_graph_article_publisher
 * @property string       $open_graph_article_author
 * @property string       $open_graph_article_published_time
 * @property string       $open_graph_article_modified_time
 * @property string       $open_graph_locale
 * @property string       $open_graph_fb_app_id
 * @property string       $permalink
 * @property array        $schema
 * @property string       $twitter_card
 * @property string       $twitter_title
 * @property string       $twitter_description
 * @property string       $twitter_image
 * @property string       $twitter_creator
 * @property string       $twitter_site
 * @property object|array $source
 * @property array        $breadcrumbs
 * @property int          $estimated_reading_time_minutes
 * @property array        $googlebot
 * @property array        $bingbot
 */
class Indexable_Presentation extends Abstract_Presentation {

	/**
	 * The indexable.
	 *
	 * @var Indexable
	 */
	public $model;

	/**
	 * The meta tags context.
	 *
	 * @var Meta_Tags_Context
	 */
	public $context;

	/**
	 * The Schema generator.
	 *
	 * @var Schema_Generator
	 */
	protected $schema_generator;

	/**
	 * The Open Graph image generator.
	 *
	 * @var Open_Graph_Image_Generator
	 */
	protected $open_graph_image_generator;

	/**
	 * The Twitter image generator.
	 *
	 * @var Twitter_Image_Generator
	 */
	protected $twitter_image_generator;

	/**
	 * The Open Graph locale generator.
	 *
	 * @var Open_Graph_Locale_Generator
	 */
	private $open_graph_locale_generator;

	/**
	 * The breadcrumbs generator.
	 *
	 * @var Breadcrumbs_Generator
	 */
	private $breadcrumbs_generator;

	/**
	 * The current page helper.
	 *
	 * @var Current_Page_Helper
	 */
	protected $current_page;

	/**
	 * The image helper.
	 *
	 * @var Image_Helper
	 */
	protected $image;

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	protected $options;

	/**
	 * The URL helper.
	 *
	 * @var Url_Helper
	 */
	protected $url;

	/**
	 * The user helper.
	 *
	 * @var User_Helper
	 */
	protected $user;

	/**
	 * The indexable helper.
	 *
	 * @var Indexable_Helper
	 */
	protected $indexable_helper;

	/**
	 * The permalink helper.
	 *
	 * @var Permalink_Helper
	 */
	protected $permalink_helper;

	/**
	 * The values helper.
	 *
	 * @var Values_Helper
	 */
	protected $values_helper;

	/**
	 * Sets the generator dependencies.
	 *
	 * @required
	 *
	 * @param Schema_Generator            $schema_generator            The schema generator.
	 * @param Open_Graph_Locale_Generator $open_graph_locale_generator The Open Graph locale generator.
	 * @param Open_Graph_Image_Generator  $open_graph_image_generator  The Open Graph image generator.
	 * @param Twitter_Image_Generator     $twitter_image_generator     The Twitter image generator.
	 * @param Breadcrumbs_Generator       $breadcrumbs_generator       The breadcrumbs generator.
	 *
	 * @return void
	 */
	public function set_generators(
		Schema_Generator $schema_generator,
		Open_Graph_Locale_Generator $open_graph_locale_generator,
		Open_Graph_Image_Generator $open_graph_image_generator,
		Twitter_Image_Generator $twitter_image_generator,
		Breadcrumbs_Generator $breadcrumbs_generator
	) {
		$this->schema_generator            = $schema_generator;
		$this->open_graph_locale_generator = $open_graph_locale_generator;
		$this->open_graph_image_generator  = $open_graph_image_generator;
		$this->twitter_image_generator     = $twitter_image_generator;
		$this->breadcrumbs_generator       = $breadcrumbs_generator;
	}

	/**
	 * Used by dependency injection container to inject the helpers.
	 *
	 * @required
	 *
	 * @param Image_Helper        $image        The image helper.
	 * @param Options_Helper      $options      The options helper.
	 * @param Current_Page_Helper $current_page The current page helper.
	 * @param Url_Helper          $url          The URL helper.
	 * @param User_Helper         $user         The user helper.
	 * @param Indexable_Helper    $indexable    The indexable helper.
	 * @param Permalink_Helper    $permalink    The permalink helper.
	 * @param Values_Helper       $values       The values helper.
	 *
	 * @return void
	 */
	public function set_helpers(
		Image_Helper $image,
		Options_Helper $options,
		Current_Page_Helper $current_page,
		Url_Helper $url,
		User_Helper $user,
		Indexable_Helper $indexable,
		Permalink_Helper $permalink,
		Values_Helper $values
	) {
		$this->image            = $image;
		$this->options          = $options;
		$this->current_page     = $current_page;
		$this->url              = $url;
		$this->user             = $user;
		$this->indexable_helper = $indexable;
		$this->permalink_helper = $permalink;
		$this->values_helper    = $values;
	}

	/**
	 * Gets the permalink from the indexable or generates it if dynamic permalinks are enabled.
	 *
	 * @return string The permalink.
	 */
	public function generate_permalink() {
		if ( $this->indexable_helper->dynamic_permalinks_enabled() ) {
			return $this->permalink_helper->get_permalink_for_indexable( $this->model );
		}

		if ( \is_date() ) {
			return $this->current_page->get_date_archive_permalink();
		}

		if ( \is_attachment() ) {
			global $wp;
			return \trailingslashit( \home_url( $wp->request ) );
		}

		return $this->model->permalink;
	}

	/**
	 * Generates the title.
	 *
	 * @return string The title.
	 */
	public function generate_title() {
		if ( $this->model->title ) {
			return $this->model->title;
		}

		return '';
	}

	/**
	 * Generates the meta description.
	 *
	 * @return string The meta description.
	 */
	public function generate_meta_description() {
		if ( $this->model->description ) {
			return $this->model->description;
		}

		return '';
	}

	/**
	 * Generates the robots value.
	 *
	 * @return array The robots value.
	 */
	public function generate_robots() {
		$robots = $this->get_base_robots();

		return $this->filter_robots( $robots );
	}

	/**
	 * Gets the base robots value.
	 *
	 * @return array The base robots value.
	 */
	protected function get_base_robots() {
		return [
			'index'             => ( $this->model->is_robots_noindex === true ) ? 'noindex' : 'index',
			'follow'            => ( $this->model->is_robots_nofollow === true ) ? 'nofollow' : 'follow',
			'max-snippet'       => 'max-snippet:-1',
			'max-image-preview' => 'max-image-preview:large',
			'max-video-preview' => 'max-video-preview:-1',
		];
	}

	/**
	 * Run the robots output content through the `wpseo_robots` filter.
	 *
	 * @param array $robots The meta robots values to filter.
	 *
	 * @return array The filtered meta robots values.
	 */
	protected function filter_robots( $robots ) {
		// Remove values that are only listened to when indexing.
		if ( $robots['index'] === 'noindex' ) {
			$robots['imageindex']        = null;
			$robots['archive']           = null;
			$robots['snippet']           = null;
			$robots['max-snippet']       = null;
			$robots['max-image-preview'] = null;
			$robots['max-video-preview'] = null;
		}

		$robots_string = \implode( ', ', \array_filter( $robots ) );

		/**
		 * Filter: 'wpseo_robots' - Allows filtering of the meta robots output of Yoast SEO.
		 *
		 * @param string                 $robots       The meta robots directives to be echoed.
		 * @param Indexable_Presentation $presentation The presentation of an indexable.
		 */
		$robots_filtered = \apply_filters( 'wpseo_robots', $robots_string, $this );

		// Convert the robots string back to an array.
		if ( \is_string( $robots_filtered ) ) {
			$robots_values = \explode( ', ', $robots_filtered );
			$robots_new    = [];

			foreach ( $robots_values as $value ) {
				$key = $value;

				// Change `noindex` to `index.
				if ( \strpos( $key, 'no' ) === 0 ) {
					$key = \substr( $value, 2 );
				}
				// Change `max-snippet:-1` to `max-snippet`.
				$colon_position = \strpos( $key, ':' );
				if ( $colon_position !== false ) {
					$key = \substr( $value, 0, $colon_position );
				}

				$robots_new[ $key ] = $value;
			}

			$robots = $robots_new;
		}

		if ( \is_bool( $robots_filtered ) && ( $robots_filtered === false ) ) {
			return [
				'index'  => 'noindex',
				'follow' => 'nofollow',
			];
		}

		if ( ! $robots_filtered ) {
			return [];
		}

		/**
		 * Filter: 'wpseo_robots_array' - Allows filtering of the meta robots output array of Yoast SEO.
		 *
		 * @param array                  $robots       The meta robots directives to be used.
		 * @param Indexable_Presentation $presentation The presentation of an indexable.
		 */
		return \apply_filters( 'wpseo_robots_array', \array_filter( $robots ), $this );
	}

	/**
	 * Generates the canonical.
	 *
	 * @return string The canonical.
	 */
	public function generate_canonical() {
		if ( $this->model->canonical ) {
			return $this->model->canonical;
		}

		if ( $this->permalink ) {
			return $this->permalink;
		}

		return '';
	}

	/**
	 * Generates the rel prev.
	 *
	 * @return string The rel prev value.
	 */
	public function generate_rel_prev() {
		return '';
	}

	/**
	 * Generates the rel next.
	 *
	 * @return string The rel prev next.
	 */
	public function generate_rel_next() {
		return '';
	}

	/**
	 * Generates the Open Graph type.
	 *
	 * @return string The Open Graph type.
	 */
	public function generate_open_graph_type() {
		return 'website';
	}

	/**
	 * Generates the open graph title.
	 *
	 * @return string The open graph title.
	 */
	public function generate_open_graph_title() {
		if ( $this->model->open_graph_title ) {
			$open_graph_title = $this->model->open_graph_title;
		}

		if ( empty( $open_graph_title ) ) {
			// The helper applies a filter, but we don't have a default value at this stage so we pass an empty string.
			$open_graph_title = $this->values_helper->get_open_graph_title( '', $this->model->object_type, $this->model->object_sub_type );
		}

		if ( empty( $open_graph_title ) ) {
			$open_graph_title = $this->title;
		}

		return $open_graph_title;
	}

	/**
	 * Generates the open graph description.
	 *
	 * @return string The open graph description.
	 */
	public function generate_open_graph_description() {
		if ( $this->model->open_graph_description ) {
			$open_graph_description = $this->model->open_graph_description;
		}

		if ( empty( $open_graph_description ) ) {
			// The helper applies a filter, but we don't have a default value at this stage so we pass an empty string.
			$open_graph_description = $this->values_helper->get_open_graph_description( '', $this->model->object_type, $this->model->object_sub_type );
		}

		if ( empty( $open_graph_description ) ) {
			$open_graph_description = $this->meta_description;
		}

		return $open_graph_description;
	}

	/**
	 * Generates the open graph images.
	 *
	 * @return array The open graph images.
	 */
	public function generate_open_graph_images() {
		if ( $this->context->open_graph_enabled === false ) {
			return [];
		}

		return $this->open_graph_image_generator->generate( $this->context );
	}

	/**
	 * Generates the open graph image ID.
	 *
	 * @return string The open graph image ID.
	 */
	public function generate_open_graph_image_id() {
		if ( $this->model->open_graph_image_id ) {
			return $this->model->open_graph_image_id;
		}

		return $this->values_helper->get_open_graph_image_id( 0, $this->model->object_type, $this->model->object_sub_type );
	}

	/**
	 * Generates the open graph image URL.
	 *
	 * @return string The open graph image URL.
	 */
	public function generate_open_graph_image() {
		if ( $this->model->open_graph_image ) {
			return $this->model->open_graph_image;
		}

		return $this->values_helper->get_open_graph_image( '', $this->model->object_type, $this->model->object_sub_type );
	}

	/**
	 * Generates the open graph url.
	 *
	 * @return string The open graph url.
	 */
	public function generate_open_graph_url() {
		if ( $this->model->canonical ) {
			return $this->model->canonical;
		}

		return $this->permalink;
	}

	/**
	 * Generates the open graph article publisher.
	 *
	 * @return string The open graph article publisher.
	 */
	public function generate_open_graph_article_publisher() {
		return '';
	}

	/**
	 * Generates the open graph article author.
	 *
	 * @return string The open graph article author.
	 */
	public function generate_open_graph_article_author() {
		return '';
	}

	/**
	 * Generates the open graph article published time.
	 *
	 * @return string The open graph article published time.
	 */
	public function generate_open_graph_article_published_time() {
		return '';
	}

	/**
	 * Generates the open graph article modified time.
	 *
	 * @return string The open graph article modified time.
	 */
	public function generate_open_graph_article_modified_time() {
		return '';
	}

	/**
	 * Generates the open graph locale.
	 *
	 * @return string The open graph locale.
	 */
	public function generate_open_graph_locale() {
		return $this->open_graph_locale_generator->generate( $this->context );
	}

	/**
	 * Generates the open graph site name.
	 *
	 * @return string The open graph site name.
	 */
	public function generate_open_graph_site_name() {
		return $this->context->wordpress_site_name;
	}

	/**
	 * Generates the Twitter card type.
	 *
	 * @return string The Twitter card type.
	 */
	public function generate_twitter_card() {
		return $this->context->twitter_card;
	}

	/**
	 * Generates the Twitter title.
	 *
	 * @return string The Twitter title.
	 */
	public function generate_twitter_title() {
		if ( $this->model->twitter_title ) {
			return $this->model->twitter_title;
		}

		if ( $this->context->open_graph_enabled === true ) {
			$social_template_title = $this->values_helper->get_open_graph_title( '', $this->model->object_type, $this->model->object_sub_type );
			$open_graph_title      = $this->open_graph_title;

			// If the helper returns a value and it's different from the OG value in the indexable,
			// output it in a twitter: tag.
			if ( ! empty( $social_template_title ) && $social_template_title !== $open_graph_title ) {
				return $social_template_title;
			}

			// If the OG title is set, let og: tag take care of this.
			if ( ! empty( $open_graph_title ) ) {
				return '';
			}
		}

		if ( $this->title ) {
			return $this->title;
		}

		return '';
	}

	/**
	 * Generates the Twitter description.
	 *
	 * @return string The Twitter description.
	 */
	public function generate_twitter_description() {
		if ( $this->model->twitter_description ) {
			return $this->model->twitter_description;
		}

		if ( $this->context->open_graph_enabled === true ) {
			$social_template_description = $this->values_helper->get_open_graph_description( '', $this->model->object_type, $this->model->object_sub_type );
			$open_graph_description      = $this->open_graph_description;

			// If the helper returns a value and it's different from the OG value in the indexable,
			// output it in a twitter: tag.
			if ( ! empty( $social_template_description ) && $social_template_description !== $open_graph_description ) {
				return $social_template_description;
			}

			// If the OG description is set, let og: tag take care of this.
			if ( ! empty( $open_graph_description ) ) {
				return '';
			}
		}

		if ( $this->meta_description ) {
			return $this->meta_description;
		}

		return '';
	}

	/**
	 * Generates the Twitter image.
	 *
	 * @return string The Twitter image.
	 */
	public function generate_twitter_image() {
		$images = $this->twitter_image_generator->generate( $this->context );
		$image  = \reset( $images );

		// Use a user-defined Twitter image, if present.
		if ( $image && $this->context->indexable->twitter_image_source === 'set-by-user' ) {
			return $image['url'];
		}

		// Let the Open Graph tags, if enabled, handle the rest of the fallback hierarchy.
		if ( $this->context->open_graph_enabled === true && $this->open_graph_images ) {
			return '';
		}

		// Set a Twitter tag with the featured image, or a prominent image from the content, if present.
		if ( $image ) {
			return $image['url'];
		}

		return '';
	}

	/**
	 * Generates the Twitter creator.
	 *
	 * @return string The Twitter creator.
	 */
	public function generate_twitter_creator() {
		return '';
	}

	/**
	 * Generates the Twitter site.
	 *
	 * @return string The Twitter site.
	 */
	public function generate_twitter_site() {
		switch ( $this->context->site_represents ) {
			case 'person':
				$twitter = $this->user->get_the_author_meta( 'twitter', (int) $this->context->site_user_id );
				if ( empty( $twitter ) ) {
					$twitter = $this->options->get( 'twitter_site' );
				}
				break;
			case 'company':
			default:
				$twitter = $this->options->get( 'twitter_site' );
				break;
		}

		return $twitter;
	}

	/**
	 * Generates the source.
	 *
	 * @return array The source.
	 */
	public function generate_source() {
		return [];
	}

	/**
	 * Generates the schema for the page.
	 *
	 * @codeCoverageIgnore Wrapper method.
	 *
	 * @return array The Schema object.
	 */
	public function generate_schema() {
		return $this->schema_generator->generate( $this->context );
	}

	/**
	 * Generates the breadcrumbs for the page.
	 *
	 * @codeCoverageIgnore Wrapper method.
	 *
	 * @return array The breadcrumbs.
	 */
	public function generate_breadcrumbs() {
		return $this->breadcrumbs_generator->generate( $this->context );
	}

	/**
	 * Generates the estimated reading time.
	 *
	 * @codeCoverageIgnore Wrapper method.
	 *
	 * @return int|null The estimated reading time.
	 */
	public function generate_estimated_reading_time_minutes() {
		if ( $this->model->estimated_reading_time_minutes !== null ) {
			return $this->model->estimated_reading_time_minutes;
		}

		if ( $this->context->post === null ) {
			return null;
		}

		// 200 is the approximate estimated words per minute across languages.
		$words_per_minute = 200;
		$words            = \str_word_count( \wp_strip_all_tags( $this->context->post->post_content ) );
		return (int) \round( $words / $words_per_minute );
	}

	/**
	 * Strips all nested dependencies from the debug info.
	 *
	 * @return array
	 */
	public function __debugInfo() {
		return [
			'model'   => $this->model,
			'context' => $this->context,
		];
	}
}
