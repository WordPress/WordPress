<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Internals
 */

/**
 * Class containing an alternative rewrite rules API for handling them dynamically without requiring flushing rules.
 */
class Yoast_Dynamic_Rewrites implements WPSEO_WordPress_Integration {

	/**
	 * Additional rewrite rules with high priority.
	 *
	 * @var array
	 */
	protected $extra_rules_top = [];

	/**
	 * Additional rewrite rules with low priority.
	 *
	 * @var array
	 */
	protected $extra_rules_bottom = [];

	/**
	 * Main instance holder.
	 *
	 * @var self|null
	 */
	protected static $instance = null;

	/**
	 * WP_Rewrite instance to use.
	 *
	 * @var WP_Rewrite
	 */
	public $wp_rewrite;

	/**
	 * Gets the main instance of the class.
	 *
	 * @return self Dynamic rewrites main instance.
	 */
	public static function instance() {
		if ( self::$instance === null ) {
			self::$instance = new self();
			self::$instance->register_hooks();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * Sets the WP_Rewrite instance to use.
	 *
	 * @param WP_Rewrite|null $rewrite Optional. WP_Rewrite instance to use. Default is the $wp_rewrite global.
	 * @throws RuntimeException Throws an exception if the $wp_rewrite global is not set.
	 */
	public function __construct( $rewrite = null ) {
		if ( ! $rewrite ) {
			if ( empty( $GLOBALS['wp_rewrite'] ) ) {
				/* translators: 1: PHP class name, 2: PHP variable name */
				throw new RuntimeException( sprintf( __( 'The %1$s class must not be instantiated before the %2$s global is set.', 'wordpress-seo' ), self::class, '$wp_rewrite' ) );
			}

			$rewrite = $GLOBALS['wp_rewrite'];
		}

		$this->wp_rewrite = $rewrite;
	}

	/**
	 * Registers all necessary hooks with WordPress.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'init', [ $this, 'trigger_dynamic_rewrite_rules_hook' ], 1 );
		add_filter( 'option_rewrite_rules', [ $this, 'filter_rewrite_rules_option' ] );
		add_filter( 'sanitize_option_rewrite_rules', [ $this, 'sanitize_rewrite_rules_option' ] );
	}

	/**
	 * Adds a dynamic rewrite rule that transforms a URL structure to a set of query vars.
	 *
	 * Rules registered with this method are applied dynamically and do not require the rewrite rules
	 * to be flushed in order to become active, which is a benefit over the regular WordPress core API.
	 * Note however that the dynamic application only works for rules that correspond to index.php.
	 * Non-WordPress rewrite rules still require flushing.
	 *
	 * Any value in the $after parameter that isn't 'bottom' will result in the rule
	 * being placed at the top of the rewrite rules.
	 *
	 * @param string       $regex    Regular expression to match request against.
	 * @param string|array $query    The corresponding query vars for this rewrite rule.
	 * @param string       $priority Optional. Priority of the new rule. Accepts 'top'
	 *                               or 'bottom'. Default 'bottom'.
	 *
	 * @return void
	 */
	public function add_rule( $regex, $query, $priority = 'bottom' ) {
		if ( is_array( $query ) ) {
			$query = add_query_arg( $query, 'index.php' );
		}

		$this->wp_rewrite->add_rule( $regex, $query, $priority );

		// Do not further handle external rules.
		if ( substr( $query, 0, strlen( $this->wp_rewrite->index . '?' ) ) !== $this->wp_rewrite->index . '?' ) {
			return;
		}

		if ( $priority === 'bottom' ) {
			$this->extra_rules_bottom[ $regex ] = $query;
			return;
		}

		$this->extra_rules_top[ $regex ] = $query;
	}

	/**
	 * Triggers the hook on which rewrite rules should be added.
	 *
	 * This allows for a more specific point in time from the generic `init` hook where this is
	 * otherwise handled.
	 *
	 * @return void
	 */
	public function trigger_dynamic_rewrite_rules_hook() {

		/**
		 * Fires when the plugin's dynamic rewrite rules should be added.
		 *
		 * @param self $dynamic_rewrites Dynamic rewrites handler instance. Use its `add_rule()` method
		 *                               to add dynamic rewrite rules.
		 */
		do_action( 'yoast_add_dynamic_rewrite_rules', $this );
	}

	/**
	 * Filters the rewrite rules option to dynamically add additional rewrite rules.
	 *
	 * @param array|string $rewrite_rules Array of rewrite rule $regex => $query pairs, or empty string
	 *                                    if currently not set.
	 *
	 * @return array|string Filtered value of $rewrite_rules.
	 */
	public function filter_rewrite_rules_option( $rewrite_rules ) {
		// Do not add extra rewrite rules if the rules need to be flushed.
		if ( empty( $rewrite_rules ) ) {
			return $rewrite_rules;
		}

		return array_merge( $this->extra_rules_top, $rewrite_rules, $this->extra_rules_bottom );
	}

	/**
	 * Sanitizes the rewrite rules option prior to writing it to the database.
	 *
	 * This method ensures that the dynamic rewrite rules do not become part of the actual option.
	 *
	 * @param array|string $rewrite_rules Array pf rewrite rule $regex => $query pairs, or empty string
	 *                                    in order to unset.
	 *
	 * @return array|string Filtered value of $rewrite_rules before writing the option.
	 */
	public function sanitize_rewrite_rules_option( $rewrite_rules ) {
		if ( empty( $rewrite_rules ) ) {
			return $rewrite_rules;
		}

		return array_diff_key( $rewrite_rules, $this->extra_rules_top, $this->extra_rules_bottom );
	}
}
