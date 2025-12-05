<?php
namespace Elementor\Modules\ImageLoadingOptimization;

use Elementor\Core\Base\Module as BaseModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Module extends BaseModule {
	/**
	 * @var int Minimum square-pixels threshold.
	 */
	private $min_priority_img_pixels = 50000;

	/**
	 * @var int The number of content media elements to not lazy-load.
	 */
	private $omit_threshold = 3;

	/**
	 * @var array Keep a track of images for which loading optimization strategy were computed.
	 */
	private static $image_visited = [];

	/**
	 * Get Module name.
	 */
	public function get_name() {
		return 'image-loading-optimization';
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( ! static::is_optimized_image_loading_enabled() ) {
			return;
		}

		parent::__construct();

		// Stop wp core logic.
		add_action( 'init', [ $this, 'stop_core_fetchpriority_high_logic' ] );
		add_filter( 'wp_lazy_loading_enabled', '__return_false' );

		// Run optimization logic on header.
		add_action( 'get_header', [ $this, 'set_buffer' ] );

		// Ensure buffer is flushed (if any) before the content logic.
		add_filter( 'the_content', [ $this, 'flush_header_buffer' ], 0 );

		// Run optimization logic on content.
		add_filter( 'wp_content_img_tag', [ $this, 'loading_optimization_image' ] );
	}

	/**
	 * Check whether the "Optimized Image Loading" settings is enabled.
	 *
	 * The 'optimized_image_loading' option can be enabled/disabled from the Elementor settings.
	 *
	 * @since 3.21.0
	 * @access private
	 */
	private static function is_optimized_image_loading_enabled(): bool {
		return '1' === get_option( 'elementor_optimized_image_loading', '1' );
	}

	/**
	 * Stop WordPress core fetchpriority logic by setting the wp_high_priority_element_flag flag to false.
	 */
	public function stop_core_fetchpriority_high_logic() {
		wp_high_priority_element_flag( false );
	}

	/**
	 * Set buffer to handle header and footer content.
	 */
	public function set_buffer() {
		ob_start( [ $this, 'handle_buffer_content' ] );
	}

	/**
	 * This function ensure that buffer if any is flushed before the content is called.
	 * This function behaves more like an action than a filter.
	 *
	 * @param string $content the content.
	 * @return string We simply return the content from parameter.
	 */
	public function flush_header_buffer( $content ) {
		$buffer_status = ob_get_status();

		if ( ! empty( $buffer_status ) &&
			1 === $buffer_status['type'] &&
			get_class( $this ) . '::handle_buffer_content' === $buffer_status['name'] ) {
			ob_end_flush();
		}

		return $content;
	}

	/**
	 * Callback to handle image optimization logic on buffered content.
	 *
	 * @param string $buffer Buffered content.
	 * @return string Content with optimized images.
	 */
	public function handle_buffer_content( $buffer ) {
		return $this->filter_images( $buffer );
	}

	/**
	 * Check for image in the content provided and apply optimization logic on them.
	 *
	 * @param string $content Content to be analyzed.
	 * @return string Content with optimized images.
	 */
	private function filter_images( $content ) {
		return preg_replace_callback(
			'/<img\s[^>]+>/',
			function ( $matches ) {
				return $this->loading_optimization_image( $matches[0] );
			},
			$content
		);
	}

	/**
	 * Apply loading optimization logic on the image.
	 *
	 * @param mixed $image Original image tag.
	 * @return string Optimized image.
	 */
	public function loading_optimization_image( $image ) {
		if ( isset( self::$image_visited[ $image ] ) ) {
			return self::$image_visited[ $image ];
		}

		$optimized_image = $this->add_loading_optimization_attrs( $image );
		self::$image_visited[ $image ] = $optimized_image;

		return $optimized_image;
	}

	/**
	 * Adds optimization attributes to an `img` HTML tag.
	 *
	 * @param string $image   The HTML `img` tag where the attribute should be added.
	 * @return string Converted `img` tag with optimization attributes added.
	 */
	private function add_loading_optimization_attrs( $image ) {
		$width             = preg_match( '/ width=["\']([0-9]+)["\']/', $image, $match_width ) ? (int) $match_width[1] : null;
		$height            = preg_match( '/ height=["\']([0-9]+)["\']/', $image, $match_height ) ? (int) $match_height[1] : null;
		$loading_val       = preg_match( '/ loading=["\']([A-Za-z]+)["\']/', $image, $match_loading ) ? $match_loading[1] : null;
		$fetchpriority_val = preg_match( '/ fetchpriority=["\']([A-Za-z]+)["\']/', $image, $match_fetchpriority ) ? $match_fetchpriority[1] : null;

		// Images should have height and dimension width for the loading optimization attributes to be added.
		if ( ! str_contains( $image, ' width="' ) || ! str_contains( $image, ' height="' ) ) {
			return $image;
		}

		$optimization_attrs = $this->get_loading_optimization_attributes(
			[
				'width'         => $width,
				'height'        => $height,
				'loading'       => $loading_val,
				'fetchpriority' => $fetchpriority_val,
			]
		);

		if ( ! empty( $optimization_attrs['fetchpriority'] ) ) {
			$image = str_replace( '<img', '<img fetchpriority="' . esc_attr( $optimization_attrs['fetchpriority'] ) . '"', $image );
		}

		if ( ! empty( $optimization_attrs['loading'] ) ) {
			$image = str_replace( '<img', '<img loading="' . esc_attr( $optimization_attrs['loading'] ) . '"', $image );
		}

		return $image;
	}

	/**
	 * Return loading Loading optimization attributes for a image with give attribute.
	 *
	 * @param array $attr Existing image attributes.
	 * @return array Loading optimization attributes.
	 */
	private function get_loading_optimization_attributes( $attr ) {
		$loading_attrs = [];

		// For any resources, width and height must be provided, to avoid layout shifts.
		if ( ! isset( $attr['width'], $attr['height'] ) ) {
			return $loading_attrs;
		}

		/*
		 * The key function logic starts here.
		 */
		$maybe_in_viewport    = null;
		$increase_count       = false;
		$maybe_increase_count = false;

		/*
		 * Logic to handle a `loading` attribute that is already provided.
		 *
		 * Copied from `wp_get_loading_optimization_attributes()`.
		 */
		if ( isset( $attr['loading'] ) ) {
			/*
			 * Interpret "lazy" as not in viewport. Any other value can be
			 * interpreted as in viewport (realistically only "eager" or `false`
			 * to force-omit the attribute are other potential values).
			 */
			if ( 'lazy' === $attr['loading'] ) {
				$maybe_in_viewport = false;
			} else {
				$maybe_in_viewport = true;
			}
		}

		// Logic to handle a `fetchpriority` attribute that is already provided.
		$has_fetchpriority_high_attr = ( isset( $attr['fetchpriority'] ) && 'high' === $attr['fetchpriority'] );

		/*
		 * Handle cases where a `fetchpriority="high"` has already been set.
		 *
		 * Copied from `wp_get_loading_optimization_attributes()`.
		 */
		if ( $has_fetchpriority_high_attr ) {
			/*
			 * If the image was already determined to not be in the viewport (e.g.
			 * from an already provided `loading` attribute), trigger a warning.
			 * Otherwise, the value can be interpreted as in viewport, since only
			 * the most important in-viewport image should have `fetchpriority` set
			 * to "high".
			 */
			if ( false === $maybe_in_viewport ) {
				_doing_it_wrong(
					__FUNCTION__,
					esc_html__( 'An image should not be lazy-loaded and marked as high priority at the same time.', 'elementor' ),
					''
				);

				/*
				 * Set `fetchpriority` here for backward-compatibility as we should
				 * not override what a developer decided, even though it seems
				 * incorrect.
				 */
				$loading_attrs['fetchpriority'] = 'high';
			} else {
				$maybe_in_viewport = true;
			}
		}

		if ( null === $maybe_in_viewport && ! is_admin() ) {
			$content_media_count = $this->increase_content_media_count( 0 );
			$increase_count      = true;
			if ( $content_media_count < $this->omit_threshold ) {
				$maybe_in_viewport = true;
			} else {
				$maybe_in_viewport = false;
			}
		}

		if ( $maybe_in_viewport ) {
			$loading_attrs = $this->maybe_add_fetchpriority_high_attr( $loading_attrs, $attr );
		} else {
			$loading_attrs['loading'] = 'lazy';
		}

		if ( $increase_count ) {
			$this->increase_content_media_count();
		} elseif ( $maybe_increase_count ) {
			if ( $this->get_min_priority_img_pixels() <= $attr['width'] * $attr['height'] ) {
				$this->increase_content_media_count();
			}
		}

		return $loading_attrs;
	}

	/**
	 * Helper to get the minimum threshold for number of pixels an image needs to have to be considered "priority".
	 *
	 * @return int The minimum number of pixels (width * height). Default is 50000.
	 */
	private function get_min_priority_img_pixels() {
		/**
		 * Filter the minimum pixel threshold used to determine if an image should have fetchpriority="high" applied.
		 *
		 * @see https://developer.wordpress.org/reference/hooks/wp_min_priority_img_pixels/
		 *
		 * @param int $pixels The minimum number of pixels (with * height).
		 * @return int The filtered value.
		 */
		return apply_filters( 'elementor/image-loading-optimization/min_priority_img_pixels', $this->min_priority_img_pixels );
	}

	/**
	 * Keeps a count of media image.
	 *
	 * @param int $amount Amount by which count must be increased.
	 * @return int current image count.
	 */
	private function increase_content_media_count( $amount = 1 ) {
		static $content_media_count = 0;

		$content_media_count += $amount;

		return $content_media_count;
	}

	/**
	 * Determines whether to add `fetchpriority='high'` to loading attributes.
	 *
	 * @param array $loading_attrs Array of the loading optimization attributes for the element.
	 * @param array $attr          Array of the attributes for the element.
	 * @return array Updated loading optimization attributes for the element.
	 */
	private function maybe_add_fetchpriority_high_attr( $loading_attrs, $attr ) {
		if ( isset( $attr['fetchpriority'] ) ) {
			if ( 'high' === $attr['fetchpriority'] ) {
				$loading_attrs['fetchpriority'] = 'high';
				$this->high_priority_element_flag( false );
			}

			return $loading_attrs;
		}

		// Lazy-loading and `fetchpriority="high"` are mutually exclusive.
		if ( isset( $loading_attrs['loading'] ) && 'lazy' === $loading_attrs['loading'] ) {
			return $loading_attrs;
		}

		if ( ! $this->high_priority_element_flag() ) {
			return $loading_attrs;
		}

		if ( $this->get_min_priority_img_pixels() <= $attr['width'] * $attr['height'] ) {
			$loading_attrs['fetchpriority'] = 'high';
			$this->high_priority_element_flag( false );
		}

		return $loading_attrs;
	}

	/**
	 * Accesses a flag that indicates if an element is a possible candidate for `fetchpriority='high'`.
	 *
	 * @param bool $value Optional. Used to change the static variable. Default null.
	 * @return bool Returns true if high-priority element was marked already, otherwise false.
	 */
	private function high_priority_element_flag( $value = null ) {
		static $high_priority_element = true;

		if ( is_bool( $value ) ) {
			$high_priority_element = $value;
		}

		return $high_priority_element;
	}
}
