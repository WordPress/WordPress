<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

/**
 * Represents a WPSEO asset
 */
class WPSEO_Admin_Asset {

	/**
	 * Constant used to identify file type as a JS file.
	 *
	 * @var string
	 */
	public const TYPE_JS = 'js';

	/**
	 * Constant used to identify file type as a CSS file.
	 *
	 * @var string
	 */
	public const TYPE_CSS = 'css';

	/**
	 * The name option identifier.
	 *
	 * @var string
	 */
	public const NAME = 'name';

	/**
	 * The source option identifier.
	 *
	 * @var string
	 */
	public const SRC = 'src';

	/**
	 * The dependencies option identifier.
	 *
	 * @var string
	 */
	public const DEPS = 'deps';

	/**
	 * The version option identifier.
	 *
	 * @var string
	 */
	public const VERSION = 'version';

	/* Style specific. */

	/**
	 * The media option identifier.
	 *
	 * @var string
	 */
	public const MEDIA = 'media';

	/**
	 * The rtl option identifier.
	 *
	 * @var string
	 */
	public const RTL = 'rtl';

	/* Script specific. */

	/**
	 * The "in footer" option identifier.
	 *
	 * @var string
	 */
	public const IN_FOOTER = 'in_footer';

	/**
	 * Asset identifier.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Path to the asset.
	 *
	 * @var string
	 */
	protected $src;

	/**
	 * Asset dependencies.
	 *
	 * @var string|array
	 */
	protected $deps;

	/**
	 * Asset version.
	 *
	 * @var string
	 */
	protected $version;

	/**
	 * For CSS Assets. The type of media for which this stylesheet has been defined.
	 *
	 * See https://www.w3.org/TR/CSS2/media.html#media-types.
	 *
	 * @var string
	 */
	protected $media;

	/**
	 * For JS Assets. Whether or not the script should be loaded in the footer.
	 *
	 * @var bool
	 */
	protected $in_footer;

	/**
	 * For JS Assets. The script's async/defer strategy.
	 *
	 * @var string
	 */
	protected $strategy;

	/**
	 * For CSS Assets. Whether this stylesheet is a right-to-left stylesheet.
	 *
	 * @var bool
	 */
	protected $rtl;

	/**
	 * File suffix.
	 *
	 * @var string
	 */
	protected $suffix;

	/**
	 * Default asset arguments.
	 *
	 * @var array
	 */
	private $defaults = [
		'deps'      => [],
		'in_footer' => true,
		'rtl'       => true,
		'media'     => 'all',
		'version'   => '',
		'suffix'    => '',
		'strategy'  => '',
	];

	/**
	 * Constructs an instance of the WPSEO_Admin_Asset class.
	 *
	 * @param array $args The arguments for this asset.
	 *
	 * @throws InvalidArgumentException Throws when no name or src has been provided.
	 */
	public function __construct( array $args ) {
		if ( ! isset( $args['name'] ) ) {
			throw new InvalidArgumentException( 'name is a required argument' );
		}

		if ( ! isset( $args['src'] ) ) {
			throw new InvalidArgumentException( 'src is a required argument' );
		}

		$args = array_merge( $this->defaults, $args );

		$this->name      = $args['name'];
		$this->src       = $args['src'];
		$this->deps      = $args['deps'];
		$this->version   = $args['version'];
		$this->media     = $args['media'];
		$this->in_footer = $args['in_footer'];
		$this->strategy  = $args['strategy'];
		$this->rtl       = $args['rtl'];
		$this->suffix    = $args['suffix'];
	}

	/**
	 * Returns the asset identifier.
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Returns the path to the asset.
	 *
	 * @return string
	 */
	public function get_src() {
		return $this->src;
	}

	/**
	 * Returns the asset dependencies.
	 *
	 * @return array|string
	 */
	public function get_deps() {
		return $this->deps;
	}

	/**
	 * Returns the asset version.
	 *
	 * @return string|null
	 */
	public function get_version() {
		if ( ! empty( $this->version ) ) {
			return $this->version;
		}

		return null;
	}

	/**
	 * Returns the media type for CSS assets.
	 *
	 * @return string
	 */
	public function get_media() {
		return $this->media;
	}

	/**
	 * Returns whether a script asset should be loaded in the footer of the page.
	 *
	 * @return bool
	 */
	public function is_in_footer() {
		return $this->in_footer;
	}

	/**
	 * Returns the script asset's async/defer loading strategy.
	 *
	 * @return string
	 */
	public function get_strategy() {
		return $this->strategy;
	}

	/**
	 * Returns whether this CSS has a RTL counterpart.
	 *
	 * @return bool
	 */
	public function has_rtl() {
		return $this->rtl;
	}

	/**
	 * Returns the file suffix.
	 *
	 * @return string
	 */
	public function get_suffix() {
		return $this->suffix;
	}
}
