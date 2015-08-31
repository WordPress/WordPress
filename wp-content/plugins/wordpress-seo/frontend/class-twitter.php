<?php
/**
 * @package WPSEO\Frontend
 */

/**
 * This class handles the Twitter card functionality.
 *
 * @link https://dev.twitter.com/docs/cards
 */
class WPSEO_Twitter {

	/**
	 * @var    object    Instance of this class
	 */
	public static $instance;

	/**
	 * @var array Images
	 */
	private $images = array();

	/**
	 * @var array Images
	 */
	public $shown_images = array();

	/**
	 * @var array $options Holds the options for the Twitter Card functionality
	 */
	public $options;

	/**
	 * Will hold the Twitter card type being created
	 *
	 * @var string
	 */
	private $type;

	/**
	 * Class constructor
	 */
	public function __construct() {
		$this->options = WPSEO_Options::get_all();
		$this->twitter();
	}

	/**
	 * Outputs the Twitter Card code on singular pages.
	 */
	public function twitter() {
		wp_reset_query();

		$this->type();
		$this->description();
		$this->title();
		$this->site_twitter();
		$this->site_domain();
		$this->image();
		if ( is_singular() ) {
			$this->author();
		}

		/**
		 * Action: 'wpseo_twitter' - Hook to add all Yoast SEO Twitter output to so they're close together.
		 */
		do_action( 'wpseo_twitter' );
	}

	/**
	 * Display the Twitter card type.
	 *
	 * This defaults to summary but can be filtered using the <code>wpseo_twitter_card_type</code> filter.
	 *
	 * @link https://dev.twitter.com/docs/cards
	 */
	protected function type() {
		$this->determine_card_type();
		$this->sanitize_card_type();

		$this->output_metatag( 'card', $this->type );
	}

	/**
	 * Determines the twitter card type for the current page
	 */
	private function determine_card_type() {
		$this->type = $this->options['twitter_card_type'];
		if ( is_singular() ) {
			// If the current post has a gallery, output a gallery card.
			if ( has_shortcode( $GLOBALS['post']->post_content, 'gallery' ) ) {
				$this->images = get_post_gallery_images();
				if ( count( $this->images ) > 3 ) {
					$this->type = 'gallery';
				}
			}
		}

		/**
		 * Filter: 'wpseo_twitter_card_type' - Allow changing the Twitter Card type as output in the Twitter card by Yoast SEO
		 *
		 * @api string $unsigned The type string
		 */
		$this->type = apply_filters( 'wpseo_twitter_card_type', $this->type );
	}

	/**
	 * Determines whether the card type is of a type currently allowed by Twitter
	 *
	 * @link https://dev.twitter.com/cards/types
	 */
	private function sanitize_card_type() {
		if ( ! in_array( $this->type, array(
			'summary',
			'summary_large_image',
			'photo',
			'gallery',
			'app',
			'player',
			'product',
		) )
		) {
			$this->type = 'summary';
		}
	}

	/**
	 * Output the metatag
	 *
	 * @param string $name
	 * @param string $value
	 * @param bool   $escaped
	 */
	private function output_metatag( $name, $value, $escaped = false ) {

		// Escape the value if not escaped.
		if ( false === $escaped ) {
			$value = esc_attr( $value );
		}

		/**
		 * Filter: 'wpseo_twitter_metatag_key' - Make the Twitter metatag key filterable
		 *
		 * @api string $key The Twitter metatag key
		 */
		$metatag_key = apply_filters( 'wpseo_twitter_metatag_key', 'name' );

		// Output meta.
		echo '<meta ', esc_attr( $metatag_key ), '="twitter:', esc_attr( $name ), '" content="', $value, '"/>', "\n";
	}

	/**
	 * Displays the description for Twitter.
	 *
	 * Only used when OpenGraph is inactive.
	 */
	protected function description() {
		if ( is_singular() ) {
			$meta_desc = $this->single_description();
		}
		elseif ( WPSEO_Frontend::get_instance()->is_posts_page() ) {
			$meta_desc = $this->single_description( get_option( 'page_for_posts' ) );
		}
		else {
			$meta_desc = $this->fallback_description();
		}

		/**
		 * Filter: 'wpseo_twitter_description' - Allow changing the Twitter description as output in the Twitter card by Yoast SEO
		 *
		 * @api string $twitter The description string
		 */
		$meta_desc = apply_filters( 'wpseo_twitter_description', $meta_desc );
		if ( is_string( $meta_desc ) && $meta_desc !== '' ) {
			$this->output_metatag( 'description', $meta_desc );
		}
	}

	/**
	 * Returns the description for a singular page
	 *
	 * @param int $post_id
	 *
	 * @return string
	 */
	private function single_description( $post_id = 0 ) {
		$meta_desc = trim( WPSEO_Meta::get_value( 'twitter-description', $post_id ) );

		if ( is_string( $meta_desc ) && '' !== $meta_desc ) {
			return $meta_desc;
		}

		$meta_desc = $this->fallback_description();
		if ( is_string( $meta_desc ) && '' !== $meta_desc ) {
			return $meta_desc;
		}

		return strip_tags( get_the_excerpt() );
	}

	/**
	 * Returns a fallback description
	 *
	 * @return string
	 */
	private function fallback_description() {
		return trim( WPSEO_Frontend::get_instance()->metadesc( false ) );
	}

	/**
	 * Displays the title for Twitter.
	 *
	 * Only used when OpenGraph is inactive.
	 */
	protected function title() {
		if ( is_singular() ) {
			$title = $this->single_title();
		}
		elseif ( WPSEO_Frontend::get_instance()->is_posts_page() ) {
			$title = $this->single_title( get_option( 'page_for_posts' ) );
		}
		else {
			$title = $this->fallback_title();
		}

		/**
		 * Filter: 'wpseo_twitter_title' - Allow changing the Twitter title as output in the Twitter card by Yoast SEO
		 *
		 * @api string $twitter The title string
		 */
		$title = apply_filters( 'wpseo_twitter_title', $title );
		if ( is_string( $title ) && $title !== '' ) {
			$this->output_metatag( 'title', $title );
		}
	}

	/**
	 * Returns the Twitter title for a single post
	 *
	 * @param int $post_id
	 *
	 * @return string
	 */
	private function single_title( $post_id = 0 ) {
		$title = WPSEO_Meta::get_value( 'twitter-title', $post_id );
		if ( ! is_string( $title ) || '' === $title ) {
			return $this->fallback_title();
		}
		else {
			return $title;
		}
	}

	/**
	 * Returns the Twitter title for any page
	 *
	 * @return string
	 */
	private function fallback_title() {
		return WPSEO_Frontend::get_instance()->title( '' );
	}

	/**
	 * Displays the Twitter account for the site.
	 */
	protected function site_twitter() {
		/**
		 * Filter: 'wpseo_twitter_site' - Allow changing the Twitter site account as output in the Twitter card by Yoast SEO
		 *
		 * @api string $unsigned Twitter site account string
		 */
		$site = apply_filters( 'wpseo_twitter_site', $this->options['twitter_site'] );
		$site = $this->get_twitter_id( $site );

		if ( is_string( $site ) && $site !== '' ) {
			$this->output_metatag( 'site', '@' . $site );
		}
	}

	/**
	 * Checks if the given id is actually an id or a url and if url, distills the id from it.
	 *
	 * Solves issues with filters returning urls and theme's/other plugins also adding a user meta
	 * twitter field which expects url rather than an id (which is what we expect).
	 *
	 * @param  string $id Twitter ID or url.
	 *
	 * @return string|bool Twitter ID or false if it failed to get a valid Twitter ID.
	 */
	private function get_twitter_id( $id ) {
		if ( preg_match( '`([A-Za-z0-9_]{1,25})$`', $id, $match ) ) {
			return $match[1];
		}
		else {
			return false;
		}
	}

	/**
	 * Displays the domain tag for the site.
	 */
	protected function site_domain() {
		/**
		 * Filter: 'wpseo_twitter_domain' - Allow changing the Twitter domain as output in the Twitter card by Yoast SEO
		 *
		 * @api string $unsigned Name string
		 */
		$domain = apply_filters( 'wpseo_twitter_domain', get_bloginfo( 'name' ) );
		if ( is_string( $domain ) && $domain !== '' ) {
			$this->output_metatag( 'domain', $domain );
		}
	}

	/**
	 * Displays the image for Twitter
	 *
	 * Only used when OpenGraph is inactive or Summary Large Image card is chosen.
	 */
	protected function image() {
		if ( 'gallery' === $this->type ) {
			$this->gallery_images_output();
		}
		else {
			$this->single_image_output();
		}

		if ( count( $this->shown_images ) == 0 && $this->options['og_default_image'] !== '' ) {
			$this->image_output( $this->options['og_default_image'] );
		}
	}

	/**
	 * Outputs the first 4 images of a gallery as the posts gallery images
	 */
	private function gallery_images_output() {
		$image_counter = 0;
		foreach ( $this->images as $image ) {
			if ( $image_counter > 3 ) {
				return;
			}
			$this->image_output( $image, 'image' . $image_counter );
			$image_counter ++;
		}
	}

	/**
	 * Takes care of image output when we only need to display a single image.
	 */
	private function single_image_output() {
		if ( $this->homepage_image_output() ) {
			return;
		}
		if ( is_singular() ) {
			if ( $this->image_from_meta_values_output() ) {
				return;
			}
			if ( $this->image_thumbnail_output() ) {
				return;
			}
			if ( $this->image_from_content_output() ) {
				return;
			}
		}
	}

	/**
	 * Show the front page image
	 *
	 * @return bool
	 */
	private function homepage_image_output() {
		if ( is_front_page() ) {
			if ( $this->options['og_frontpage_image'] !== '' ) {
				$this->image_output( $this->options['og_frontpage_image'] );

				return true;
			}
		}

		return false;
	}

	/**
	 * Outputs a Twitter image tag for a given image
	 *
	 * @param string $img The source URL to the image.
	 * @param string $tag The tag to output, defaults to <code>image:src</code> but can be altered for use in galleries.
	 *
	 * @return bool
	 */
	protected function image_output( $img, $tag = 'image:src' ) {
		/**
		 * Filter: 'wpseo_twitter_image' - Allow changing the Twitter Card image
		 *
		 * @api string $img Image URL string
		 */
		$img = apply_filters( 'wpseo_twitter_image', $img );

		$escaped_img = esc_url( $img );

		if ( in_array( $escaped_img, $this->shown_images ) ) {
			return false;
		}

		if ( is_string( $escaped_img ) && $escaped_img !== '' ) {
			$this->output_metatag( $tag, $escaped_img, true );
			array_push( $this->shown_images, $escaped_img );

			return true;
		}

		return false;
	}

	/**
	 * Retrieve images from the post meta values
	 *
	 * @return bool
	 */
	private function image_from_meta_values_output() {
		foreach ( array( 'twitter-image', 'opengraph-image' ) as $tag ) {
			$img = WPSEO_Meta::get_value( $tag );
			if ( $img !== '' ) {
				$this->image_output( $img );

				return true;
			}
		}

		return false;
	}

	/**
	 * Retrieve the featured image
	 *
	 * @return bool
	 */
	private function image_thumbnail_output() {
		if ( function_exists( 'has_post_thumbnail' ) && has_post_thumbnail( get_the_ID() ) ) {
			/**
			 * Filter: 'wpseo_twitter_image_size' - Allow changing the Twitter Card image size
			 *
			 * @api string $featured_img Image size string
			 */
			$featured_img = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), apply_filters( 'wpseo_twitter_image_size', 'full' ) );

			if ( $featured_img ) {
				$this->image_output( $featured_img[0] );

				return true;
			}
		}

		return false;
	}

	/**
	 * Retrieve the image from the content
	 *
	 * @return bool
	 */
	private function image_from_content_output() {
		/**
		 * Filter: 'wpseo_pre_analysis_post_content' - Allow filtering the content before analysis
		 *
		 * @api string $post_content The Post content string
		 *
		 * @param object $post - The post object.
		 */
		global $post;
		$content = apply_filters( 'wpseo_pre_analysis_post_content', $post->post_content, $post );

		if ( preg_match_all( '`<img [^>]+>`', $content, $matches ) ) {
			foreach ( $matches[0] as $img ) {
				if ( preg_match( '`src=(["\'])(.*?)\1`', $img, $match ) ) {
					$this->image_output( $match[2] );

					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Displays the authors Twitter account.
	 */
	protected function author() {
		$twitter = ltrim( trim( get_the_author_meta( 'twitter', get_post()->post_author ) ), '@' );
		/**
		 * Filter: 'wpseo_twitter_creator_account' - Allow changing the Twitter account as output in the Twitter card by Yoast SEO
		 *
		 * @api string $twitter The twitter account name string
		 */
		$twitter = apply_filters( 'wpseo_twitter_creator_account', $twitter );
		$twitter = $this->get_twitter_id( $twitter );

		if ( is_string( $twitter ) && $twitter !== '' ) {
			$this->output_metatag( 'creator', '@' . $twitter );
		}
		elseif ( $this->options['twitter_site'] !== '' ) {
			if ( is_string( $this->options['twitter_site'] ) && $this->options['twitter_site'] !== '' ) {
				$this->output_metatag( 'creator', '@' . $this->options['twitter_site'] );
			}
		}
	}

	/**
	 * Get the singleton instance of this class
	 *
	 * @return object
	 */
	public static function get_instance() {
		if ( ! ( self::$instance instanceof self ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

} /* End of class */
