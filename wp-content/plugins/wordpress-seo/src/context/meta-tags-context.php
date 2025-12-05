<?php

namespace Yoast\WP\SEO\Context;

use WP_Block_Parser_Block;
use WP_Post;
use WPSEO_Replace_Vars;
use Yoast\WP\SEO\Config\Schema_IDs;
use Yoast\WP\SEO\Config\Schema_Types;
use Yoast\WP\SEO\Helpers\Image_Helper;
use Yoast\WP\SEO\Helpers\Indexable_Helper;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\Permalink_Helper;
use Yoast\WP\SEO\Helpers\Schema\ID_Helper;
use Yoast\WP\SEO\Helpers\Site_Helper;
use Yoast\WP\SEO\Helpers\Url_Helper;
use Yoast\WP\SEO\Helpers\User_Helper;
use Yoast\WP\SEO\Models\Indexable;
use Yoast\WP\SEO\Presentations\Abstract_Presentation;
use Yoast\WP\SEO\Presentations\Indexable_Presentation;
use Yoast\WP\SEO\Repositories\Indexable_Repository;

/**
 * Class Meta_Tags_Context.
 *
 * Class that contains all relevant data for rendering the meta tags.
 *
 * @property string          $canonical
 * @property string          $permalink
 * @property string          $title
 * @property string          $description
 * @property string          $id
 * @property string          $site_name
 * @property string          $alternate_site_name
 * @property string          $wordpress_site_name
 * @property string          $site_url
 * @property string          $company_name
 * @property string          $company_alternate_name
 * @property int             $company_logo_id
 * @property array           $company_logo_meta
 * @property int             $person_logo_id
 * @property array           $person_logo_meta
 * @property int             $site_user_id
 * @property string          $site_represents
 * @property array|false     $site_represents_reference
 * @property string|string[] $schema_page_type
 * @property string|string[] $schema_article_type      Represents the type of article.
 * @property string          $main_schema_id
 * @property string|array    $main_entity_of_page
 * @property bool            $open_graph_enabled
 * @property string          $open_graph_publisher
 * @property string          $twitter_card
 * @property string          $page_type
 * @property bool            $has_article
 * @property bool            $has_image
 * @property int             $main_image_id
 * @property string          $main_image_url
 */
class Meta_Tags_Context extends Abstract_Presentation {

	/**
	 * The indexable.
	 *
	 * @var Indexable
	 */
	public $indexable;

	/**
	 * The WP Block Parser Block.
	 *
	 * @var WP_Block_Parser_Block[]
	 */
	public $blocks;

	/**
	 * The WP Post.
	 *
	 * @var WP_Post
	 */
	public $post;

	/**
	 * The indexable presentation.
	 *
	 * @var Indexable_Presentation
	 */
	public $presentation;

	/**
	 * Determines whether we have an Article piece. Set to true by the Article piece itself.
	 *
	 * @var bool
	 */
	public $has_article = false;

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	private $options;

	/**
	 * The URL helper.
	 *
	 * @var Url_Helper
	 */
	private $url;

	/**
	 * The image helper.
	 *
	 * @var Image_Helper
	 */
	private $image;

	/**
	 * The ID helper.
	 *
	 * @var ID_Helper
	 */
	private $id_helper;

	/**
	 * The WPSEO Replace Vars object.
	 *
	 * @var WPSEO_Replace_Vars
	 */
	private $replace_vars;

	/**
	 * The site helper.
	 *
	 * @var Site_Helper
	 */
	private $site;

	/**
	 * The user helper.
	 *
	 * @var User_Helper
	 */
	private $user;

	/**
	 * The permalink helper.
	 *
	 * @var Permalink_Helper
	 */
	private $permalink_helper;

	/**
	 * The indexable helper.
	 *
	 * @var Indexable_Helper
	 */
	private $indexable_helper;

	/**
	 * The indexable repository.
	 *
	 * @var Indexable_Repository
	 */
	private $indexable_repository;

	/**
	 * Meta_Tags_Context constructor.
	 *
	 * @param Options_Helper       $options              The options helper.
	 * @param Url_Helper           $url                  The url helper.
	 * @param Image_Helper         $image                The image helper.
	 * @param ID_Helper            $id_helper            The schema id helper.
	 * @param WPSEO_Replace_Vars   $replace_vars         The replace vars helper.
	 * @param Site_Helper          $site                 The site helper.
	 * @param User_Helper          $user                 The user helper.
	 * @param Permalink_Helper     $permalink_helper     The permalink helper.
	 * @param Indexable_Helper     $indexable_helper     The indexable helper.
	 * @param Indexable_Repository $indexable_repository The indexable repository.
	 */
	public function __construct(
		Options_Helper $options,
		Url_Helper $url,
		Image_Helper $image,
		ID_Helper $id_helper,
		WPSEO_Replace_Vars $replace_vars,
		Site_Helper $site,
		User_Helper $user,
		Permalink_Helper $permalink_helper,
		Indexable_Helper $indexable_helper,
		Indexable_Repository $indexable_repository
	) {
		$this->options              = $options;
		$this->url                  = $url;
		$this->image                = $image;
		$this->id_helper            = $id_helper;
		$this->replace_vars         = $replace_vars;
		$this->site                 = $site;
		$this->user                 = $user;
		$this->permalink_helper     = $permalink_helper;
		$this->indexable_helper     = $indexable_helper;
		$this->indexable_repository = $indexable_repository;
	}

	/**
	 * Generates the title.
	 *
	 * @return string the title
	 */
	public function generate_title() {
		return $this->replace_vars->replace( $this->presentation->title, $this->presentation->source );
	}

	/**
	 * Generates the description.
	 *
	 * @return string the description
	 */
	public function generate_description() {
		return $this->replace_vars->replace( $this->presentation->meta_description, $this->presentation->source );
	}

	/**
	 * Generates the canonical.
	 *
	 * @return string the canonical
	 */
	public function generate_canonical() {
		return $this->presentation->canonical;
	}

	/**
	 * Generates the permalink.
	 *
	 * @return string
	 */
	public function generate_permalink() {
		if ( ! \is_search() ) {
			return $this->presentation->permalink;
		}

		return \add_query_arg( 's', \rawurlencode( \get_search_query() ), \trailingslashit( $this->site_url ) );
	}

	/**
	 * Generates the id.
	 *
	 * @return string the id
	 */
	public function generate_id() {
		return $this->indexable->object_id;
	}

	/**
	 * Generates the site name.
	 *
	 * @return string The site name.
	 */
	public function generate_site_name() {
		$site_name = $this->options->get( 'website_name', '' );
		if ( $site_name !== '' ) {
			return $site_name;
		}

		return \get_bloginfo( 'name' );
	}

	/**
	 * Generates the alternate site name.
	 *
	 * @return string The alternate site name.
	 */
	public function generate_alternate_site_name() {
		return (string) $this->options->get( 'alternate_website_name', '' );
	}

	/**
	 * Generates the site name from the WordPress options.
	 *
	 * @return string The site name from the WordPress options.
	 */
	public function generate_wordpress_site_name() {
		return $this->site->get_site_name();
	}

	/**
	 * Generates the site url.
	 *
	 * @return string The site url.
	 */
	public function generate_site_url() {
		$home_page_indexable = $this->indexable_repository->find_for_home_page();

		if ( $this->indexable_helper->dynamic_permalinks_enabled() ) {
			return \trailingslashit( $this->permalink_helper->get_permalink_for_indexable( $home_page_indexable ) );
		}

		return \trailingslashit( $home_page_indexable->permalink );
	}

	/**
	 * Generates the company name.
	 *
	 * @return string The company name.
	 */
	public function generate_company_name() {
		/**
		 * Filter: 'wpseo_schema_company_name' - Allows filtering company name
		 *
		 * @param string $company_name.
		 */
		$company_name = \apply_filters( 'wpseo_schema_company_name', $this->options->get( 'company_name' ) );

		if ( empty( $company_name ) ) {
			$company_name = $this->site_name;
		}

		return $company_name;
	}

	/**
	 * Generates the alternate company name.
	 *
	 * @return string
	 */
	public function generate_company_alternate_name() {
		return (string) $this->options->get( 'company_alternate_name' );
	}

	/**
	 * Generates the person logo id.
	 *
	 * @return int|bool The company logo id.
	 */
	public function generate_person_logo_id() {
		$person_logo_id = $this->image->get_attachment_id_from_settings( 'person_logo' );

		if ( empty( $person_logo_id ) ) {
			$person_logo_id = $this->fallback_to_site_logo();
		}

		/**
		 * Filter: 'wpseo_schema_person_logo_id' - Allows filtering person logo id.
		 *
		 * @param int $person_logo_id.
		 */
		return \apply_filters( 'wpseo_schema_person_logo_id', $person_logo_id );
	}

	/**
	 * Retrieve the person logo meta.
	 *
	 * @return array<string|array<int>>|bool
	 */
	public function generate_person_logo_meta() {
		$person_logo_meta = $this->image->get_attachment_meta_from_settings( 'person_logo' );

		if ( empty( $person_logo_meta ) ) {
			$person_logo_id   = $this->fallback_to_site_logo();
			$person_logo_meta = $this->image->get_best_attachment_variation( $person_logo_id );
		}

		/**
		 * Filter: 'wpseo_schema_person_logo_meta' - Allows filtering person logo meta.
		 *
		 * @param string $person_logo_meta.
		 */
		return \apply_filters( 'wpseo_schema_person_logo_meta', $person_logo_meta );
	}

	/**
	 * Generates the company logo id.
	 *
	 * @return int|bool The company logo id.
	 */
	public function generate_company_logo_id() {
		$company_logo_id = $this->image->get_attachment_id_from_settings( 'company_logo' );

		if ( empty( $company_logo_id ) ) {
			$company_logo_id = $this->fallback_to_site_logo();
		}

		/**
		 * Filter: 'wpseo_schema_company_logo_id' - Allows filtering company logo id.
		 *
		 * @param int $company_logo_id.
		 */
		return \apply_filters( 'wpseo_schema_company_logo_id', $company_logo_id );
	}

	/**
	 * Retrieve the company logo meta.
	 *
	 * @return array<string|array<int>>|bool
	 */
	public function generate_company_logo_meta() {
		$company_logo_meta = $this->image->get_attachment_meta_from_settings( 'company_logo' );

		/**
		 * Filter: 'wpseo_schema_company_logo_meta' - Allows filtering company logo meta.
		 *
		 * @param string $company_logo_meta.
		 */
		return \apply_filters( 'wpseo_schema_company_logo_meta', $company_logo_meta );
	}

	/**
	 * Generates the site user id.
	 *
	 * @return int The site user id.
	 */
	public function generate_site_user_id() {
		return (int) $this->options->get( 'company_or_person_user_id', false );
	}

	/**
	 * Determines what our site represents, and grabs their values.
	 *
	 * @return string|false Person or company. False if invalid value.
	 */
	public function generate_site_represents() {
		switch ( $this->options->get( 'company_or_person', false ) ) {
			case 'company':
				// Do not use a non-named company.
				if ( empty( $this->company_name ) ) {
					return false;
				}

				/*
				 * Do not use a company without a logo.
				 * The logic check is on `< 1` instead of `false` due to how `get_attachment_id_from_settings` works.
				 */
				if ( $this->company_logo_id < 1 ) {
					return false;
				}

				return 'company';
			case 'person':
				// Do not use a non-existing user.
				if ( $this->site_user_id !== false && \get_user_by( 'id', $this->site_user_id ) === false ) {
					return false;
				}

				return 'person';
		}

		return false;
	}

	/**
	 * Returns the site represents reference.
	 *
	 * @return array<string>|bool The site represents reference. False if none.
	 */
	public function generate_site_represents_reference() {
		if ( $this->site_represents === 'person' ) {
			return [ '@id' => $this->id_helper->get_user_schema_id( $this->site_user_id, $this ) ];
		}
		if ( $this->site_represents === 'company' ) {
			return [ '@id' => $this->site_url . Schema_IDs::ORGANIZATION_HASH ];
		}

		return false;
	}

	/**
	 * Returns whether or not open graph is enabled.
	 *
	 * @return bool Whether or not open graph is enabled.
	 */
	public function generate_open_graph_enabled() {
		return $this->options->get( 'opengraph' ) === true;
	}

	/**
	 * Returns the open graph publisher.
	 *
	 * @return string The open graph publisher.
	 */
	public function generate_open_graph_publisher() {
		if ( $this->site_represents === 'company' ) {
			return $this->options->get( 'facebook_site', '' );
		}
		if ( $this->site_represents === 'person' ) {
			return $this->user->get_the_author_meta( 'facebook', $this->site_user_id );
		}

		return $this->options->get( 'facebook_site', '' );
	}

	/**
	 * Returns the twitter card type.
	 *
	 * @return string The twitter card type.
	 */
	public function generate_twitter_card() {
		return 'summary_large_image';
	}

	/**
	 * Returns the schema page type.
	 *
	 * @return string|array<string> The schema page type.
	 */
	public function generate_schema_page_type() {
		switch ( $this->indexable->object_type ) {
			case 'system-page':
				switch ( $this->indexable->object_sub_type ) {
					case 'search-result':
						$type = [ 'CollectionPage', 'SearchResultsPage' ];
						break;
					default:
						$type = 'WebPage';
				}
				break;
			case 'user':
				$type = 'ProfilePage';
				break;
			case 'home-page':
			case 'date-archive':
			case 'term':
			case 'post-type-archive':
				$type = 'CollectionPage';
				break;
			default:
				$additional_type = $this->indexable->schema_page_type;
				if ( $additional_type === null ) {
					$additional_type = $this->options->get( 'schema-page-type-' . $this->indexable->object_sub_type );
				}

				$type = [ 'WebPage', $additional_type ];

				// Is this indexable set as a page for posts, e.g. in the WordPress reading settings as a static homepage?
				if ( (int) \get_option( 'page_for_posts' ) === $this->indexable->object_id ) {
					$type[] = 'CollectionPage';
				}

				// Ensure we get only unique values, and remove any null values and the index.
				$type = \array_filter( \array_values( \array_unique( $type ) ) );
		}

		/**
		 * Filter: 'wpseo_schema_webpage_type' - Allow changing the WebPage type.
		 *
		 * @param string|array $type The WebPage type.
		 */
		return \apply_filters( 'wpseo_schema_webpage_type', $type );
	}

	/**
	 * Returns the schema article type.
	 *
	 * @return string|array<string> The schema article type.
	 */
	public function generate_schema_article_type() {
		$additional_type = $this->indexable->schema_article_type;
		if ( $additional_type === null ) {
			$additional_type = $this->options->get( 'schema-article-type-' . $this->indexable->object_sub_type );
		}

		/** This filter is documented in inc/options/class-wpseo-option-titles.php */
		$allowed_article_types = \apply_filters( 'wpseo_schema_article_types', Schema_Types::ARTICLE_TYPES );

		if ( ! \array_key_exists( $additional_type, $allowed_article_types ) ) {
			$additional_type = $this->options->get_title_default( 'schema-article-type-' . $this->indexable->object_sub_type );
		}

		// If the additional type is a subtype of Article, we're fine, and we can bail here.
		if ( \stripos( $additional_type, 'Article' ) !== false ) {
			/**
			 * Filter: 'wpseo_schema_article_type' - Allow changing the Article type.
			 *
			 * @param string|string[] $type      The Article type.
			 * @param Indexable       $indexable The indexable.
			 */
			return \apply_filters( 'wpseo_schema_article_type', $additional_type, $this->indexable );
		}

		$type = 'Article';

		/*
		 * If `None` is set (either on the indexable or as a default), set type to 'None'.
		 * This simplifies is_needed checks downstream.
		 */
		if ( $additional_type === 'None' ) {
			$type = $additional_type;
		}

		if ( $additional_type !== $type ) {
			$type = [ $type, $additional_type ];
		}

		// Filter documented on line 499 above.
		return \apply_filters( 'wpseo_schema_article_type', $type, $this->indexable );
	}

	/**
	 * Returns the main schema id.
	 *
	 * The main schema id.
	 *
	 * @return string
	 */
	public function generate_main_schema_id() {
		return $this->permalink;
	}

	/**
	 * Retrieves the main image URL. This is the featured image by default.
	 *
	 * @return string|null The main image URL.
	 */
	public function generate_main_image_url() {
		if ( $this->main_image_id !== null ) {
			return $this->image->get_attachment_image_url( $this->main_image_id, 'full' );
		}

		if ( \wp_is_serving_rest_request() ) {
			return $this->get_main_image_url_for_rest_request();
		}

		if ( ! \is_singular() ) {
			return null;
		}

		$url = $this->image->get_post_content_image( $this->id );
		if ( $url === '' ) {
			return null;
		}

		return $url;
	}

	/**
	 * Generates the main image ID.
	 *
	 * @return int|null The main image ID.
	 */
	public function generate_main_image_id() {
		if ( \wp_is_serving_rest_request() ) {
			return $this->get_main_image_id_for_rest_request();
		}

		$image_id = null;

		switch ( true ) {
			case \is_singular():
				$image_id = $this->get_singular_post_image( $this->id );
				break;
			case \is_author():
			case \is_tax():
			case \is_tag():
			case \is_category():
			case \is_search():
			case \is_date():
			case \is_post_type_archive():
				if ( ! empty( $GLOBALS['wp_query']->posts ) ) {
					if ( $GLOBALS['wp_query']->get( 'fields', 'all' ) === 'ids' ) {
						$image_id = $this->get_singular_post_image( $GLOBALS['wp_query']->posts[0] );
						break;
					}

					$image_id = $this->get_singular_post_image( $GLOBALS['wp_query']->posts[0]->ID );
				}
				break;
		}

		/**
		 * Filter: 'wpseo_schema_main_image_id' - Allow changing the main image ID.
		 *
		 * @param int|array $image_id The image ID.
		 */
		return \apply_filters( 'wpseo_schema_main_image_id', $image_id );
	}

	/**
	 * Determines whether the current indexable has an image.
	 *
	 * @return bool Whether the current indexable has an image.
	 */
	public function generate_has_image() {
		return $this->main_image_url !== null;
	}

	/**
	 * Strips all nested dependencies from the debug info.
	 *
	 * @return array<Indexable|Indexable_Presentation>
	 */
	public function __debugInfo() {
		return [
			'indexable'    => $this->indexable,
			'presentation' => $this->presentation,
		];
	}

	/**
	 * Retrieve the site logo ID from WordPress settings.
	 *
	 * @return int|false
	 */
	public function fallback_to_site_logo() {
		$logo_id = \get_option( 'site_logo' );
		if ( ! $logo_id ) {
			$logo_id = \get_theme_mod( 'custom_logo', false );
		}

		return $logo_id;
	}

	/**
	 * Get the ID for a post's featured image.
	 *
	 * @param int $id Post ID.
	 *
	 * @return int|null
	 */
	private function get_singular_post_image( $id ) {
		if ( \has_post_thumbnail( $id ) ) {
			$thumbnail_id = \get_post_thumbnail_id( $id );
			// Prevent returning something else than an int or null.
			if ( \is_int( $thumbnail_id ) && $thumbnail_id > 0 ) {
				return $thumbnail_id;
			}
		}

		if ( \is_singular( 'attachment' ) ) {
			return \get_query_var( 'attachment_id' );
		}

		return null;
	}

	/**
	 * Gets the main image ID for REST requests.
	 *
	 * @return int|null The main image ID.
	 */
	private function get_main_image_id_for_rest_request() {
		switch ( $this->page_type ) {
			case 'Post_Type':
				if ( $this->post instanceof WP_Post ) {
					return $this->get_singular_post_image( $this->post->ID );
				}
				return null;
			default:
				return null;
		}
	}

	/**
	 * Gets the main image URL for REST requests.
	 *
	 * @return string|null The main image URL.
	 */
	private function get_main_image_url_for_rest_request() {
		switch ( $this->page_type ) {
			case 'Post_Type':
				if ( $this->post instanceof WP_Post ) {
					$url = $this->image->get_post_content_image( $this->post->ID );
					if ( $url === '' ) {
						return null;
					}
					return $url;
				}
				return null;
			default:
				return null;
		}
	}
}

\class_alias( Meta_Tags_Context::class, 'WPSEO_Schema_Context' );
