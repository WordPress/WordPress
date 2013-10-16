<?php
/**
 * Twenty Fourteen Featured Content
 *
 * This module allows you to define a subset of posts to be
 * displayed in the theme's Featured Content area.
 *
 * For maximum compatibility with different methods of posting
 * users will designate a featured post tag to associate posts
 * with. Since this tag now has special meaning beyond that of a
 * normal tags, users will have the ability to hide it from the
 * front-end of their site.
 */
class Featured_Content {

	/**
	 * The maximum number of posts that a Featured Content
	 * area can contain. We define a default value here but
	 * themes can override this by defining a "max_posts"
	 * entry in the second parameter passed in the call to
	 * add_theme_support( 'featured-content' ).
	 *
	 * @see Featured_Content::init()
	 */
	public static $max_posts = 15;

	/**
	 * Instantiate
	 *
	 * All custom functionality will be hooked into the "init" action.
	 */
	public static function setup() {
		add_action( 'init', array( __CLASS____, 'init' ), 30 );
	}

	/**
	 * Conditionally hook into WordPress
	 *
	 * Theme must declare that they support this module by adding
	 * add_theme_support( 'featured-content' ); during after_setup_theme.
	 *
	 * If no theme support is found there is no need to hook into
	 * WordPress. We'll just return early instead.
	 *
	 * @uses Featured_Content::$max_posts
	 */
	public static function init() {
		$theme_support = get_theme_support( 'featured-content' );

		// Return early if theme does not support Featured Content.
		if ( ! $theme_support )
			return;

		/*
		 * An array of named arguments must be passed as
		 * the second parameter of add_theme_support().
		 */
		if ( ! isset( $theme_support[0] ) )
			return;

		// Return early if "featured_content_filter" has not been defined.
		if ( ! isset( $theme_support[0]['featured_content_filter'] ) )
			return;

		$filter = $theme_support[0]['featured_content_filter'];

		// Theme can override the number of max posts.
		if ( isset( $theme_support[0]['max_posts'] ) )
			self::$max_posts = absint( $theme_support[0]['max_posts'] );

		add_filter( $filter,                 array( __CLASS__, 'get_featured_posts' ) );
		add_action( 'admin_init',            array( __CLASS__, 'register_setting' ) );
		add_action( 'save_post',             array( __CLASS__, 'delete_transient' ) );
		add_action( 'delete_post_tag',       array( __CLASS__, 'delete_post_tag' ) );
		add_action( 'pre_get_posts',         array( __CLASS__, 'pre_get_posts' ) );

		// Hide "featured" tag from the front-end.
		if ( self::get_setting( 'hide-tag' ) ) {
			add_filter( 'get_terms',     array( __CLASS__, 'hide_featured_term' ), 10, 2 );
			add_filter( 'get_the_terms', array( __CLASS__, 'hide_the_featured_term' ), 10, 3 );
		}
	}

	/**
	 * Get featured posts
	 *
	 * @uses Featured_Content::get_featured_post_ids()
	 *
	 * @return array|bool
	 */
	public static function get_featured_posts() {
		$post_ids = self::get_featured_post_ids();

		// User has disabled Featured Content.
		if ( false === $post_ids )
			return false;

		// No need to query if there is are no featured posts.
		if ( empty( $post_ids ) )
			return array();

		$featured_posts = get_posts( array(
			'include'        => $post_ids,
			'posts_per_page' => count( $post_ids )
		) );

		return $featured_posts;
	}

	/**
	 * Get featured post IDs
	 *
	 * This function will return the an array containing the
	 * post IDs of all featured posts.
	 *
	 * Sets the "featured_content_ids" transient.
	 *
	 * @return array|false Array of post IDs. false if user has disabled this feature.
	 */
	public static function get_featured_post_ids() {
		$settings = self::get_setting();

		// Return false if the user has disabled this feature.
		$tag = $settings['tag-id'];
		if ( empty( $tag ) )
			return false;

		// Return array of cached results if they exist.
		$featured_ids = get_transient( 'featured_content_ids' );
		if ( ! empty( $featured_ids ) )
			return array_map( 'absint', (array) $featured_ids );

		// Query for featured posts.
		$featured = get_posts( array(
			'numberposts' => $settings['quantity'],
			'tax_query'   => array(
				array(
					'field'    => 'term_id',
					'taxonomy' => 'post_tag',
					'terms'    => $tag,
				),
			),
		) );

		// Return empty array if no Featured Content exists.
		if ( ! $featured )
			return array();

		// Ensure correct format before save/return.
		$featured_ids = wp_list_pluck( (array) $featured, 'ID' );
		$featured_ids = array_map( 'absint', $featured_ids );

		set_transient( 'featured_content_ids', $featured_ids );

		return $featured_ids;
	}

	/**
	 * Delete transient
	 *
	 * Hooks in the "save_post" action.
	 * @see Featured_Content::validate_settings().
	 */
	public static function delete_transient() {
		delete_transient( 'featured_content_ids' );
	}

	/**
	 * Exclude featured posts from the home page blog query
	 *
	 * Filter the home page posts, and remove any featured post ID's from it. Hooked
	 * onto the 'pre_get_posts' action, this changes the parameters of the query
	 * before it gets any posts.
	 *
	 * @uses Featured_Content::get_featured_post_ids();
	 * @param WP_Query $query
	 * @return WP_Query Possibly modified WP_query
	 */
	public static function pre_get_posts( $query = false ) {

		// Bail if not home, not a query, not main query.
		if ( ! is_home() || ! is_a( $query, 'WP_Query' ) || ! $query->is_main_query() )
			return;

		$page_on_front = get_option( 'page_on_front' );

		// Bail if the blog page is not the front page.
		if ( ! empty( $page_on_front ) )
			return;

		$featured = self::get_featured_post_ids();

		// Bail if no featured posts.
		if ( ! $featured )
			return;

		// We need to respect post ids already in the blacklist.
		$post__not_in = $query->get( 'post__not_in' );

		if ( ! empty( $post__not_in ) ) {
			$featured = array_merge( (array) $post__not_in, $featured );
			$featured = array_unique( $featured );
		}

		$query->set( 'post__not_in', $featured );
	}

	/**
	 * Reset tag option when the saved tag is deleted
	 *
	 * It's important to mention that the transient needs to be deleted, too.
	 * While it may not be obvious by looking at the function alone, the transient
	 * is deleted by Featured_Content::validate_settings().
	 *
	 * Hooks in the "delete_post_tag" action.
	 * @see Featured_Content::validate_settings().
	 *
	 * @param int $tag_id the term_id of the tag that has been deleted.
	 * @return void
	 */
	public static function delete_post_tag( $tag_id ) {
		$settings = self::get_setting();

		if ( empty( $settings['tag-id'] ) )
			return;

		if ( $tag_id != $settings['tag-id'] )
			return;

		$settings['tag-id'] = 0;
		$settings = self::validate_settings( $settings );
		update_option( 'featured-content', $settings );
	}

	/**
	 * Hide featured tag from displaying when global terms are queried from the front-end
	 *
	 * Hooks into the "get_terms" filter.
	 *
	 * @param array $terms A list of term objects. This is the return value of get_terms().
	 * @param array $taxonomies An array of taxonomy slugs.
	 * @return array $terms
	 *
	 * @uses Featured_Content::get_setting()
	 */
	public static function hide_featured_term( $terms, $taxonomies ) {

		// This filter is only appropriate on the front-end.
		if ( is_admin() )
			return $terms;

		// We only want to hide the featured tag.
		if ( ! in_array( 'post_tag', $taxonomies ) )
			return $terms;

		// Bail if no terms were returned.
		if ( empty( $terms ) )
			return $terms;

		foreach( $terms as $order => $term ) {
			if ( self::get_setting( 'tag-id' ) == $term->term_id && 'post_tag' == $term->taxonomy )
				unset( $terms[$order] );
		}

		return $terms;
	}

	/**
	 * Hide featured tag from display when terms associated with a post object are queried from the front-end
	 *
	 * Hooks into the "get_the_terms" filter.
	 *
	 * @param array $terms A list of term objects. This is the return value of get_the_terms().
	 * @param int $id The ID field for the post object that terms are associated with.
	 * @param array $taxonomy An array of taxonomy slugs.
	 * @return array $terms
	 *
	 * @uses Featured_Content::get_setting()
	 */
	public static function hide_the_featured_term( $terms, $id, $taxonomy ) {

		// This filter is only appropriate on the front-end.
		if ( is_admin() )
			return $terms;

		// Make sure we are in the correct taxonomy.
		if ( ! 'post_tag' == $taxonomy )
			return $terms;

		// No terms? Return early!
		if ( empty( $terms ) )
			return $terms;

		foreach( $terms as $order => $term ) {
			if ( self::get_setting( 'tag-id' ) == $term->term_id )
				unset( $terms[$term->term_id] );
		}

		return $terms;
	}

	/**
	 * Register custom setting on the Settings -> Reading screen
	 *
	 * @uses Featured_Content::render_form()
	 * @uses Featured_Content::validate_settings()
	 *
	 * @return void
	 */
	public static function register_setting() {
		add_settings_field( 'featured-content', __( 'Featured content', 'twentyfourteen' ), array( __CLASS__, 'render_form' ), 'reading' );
		register_setting( 'reading', 'featured-content', array( __CLASS__, 'validate_settings' ) );
	}

	/**
	 * Render the form fields for Settings -> Reading screen
	 *
	 * @return void
	 */
	public static function render_form() {
		$settings = self::get_setting();

		$tag_name = '';
		if ( ! empty( $settings['tag-id'] ) ) {
			$tag = get_term( $settings['tag-id'], 'post_tag' );
			if ( ! is_wp_error( $tag ) && isset( $tag->name ) )
				$tag_name = $tag->name;
		}

		wp_enqueue_script( 'twentyfourteen-admin', get_template_directory_uri() . '/js/featured-content-admin.js', array( 'jquery', 'suggest' ), '20131016', true );
		?>
		<div id="featured-content-ui">
			<p>
				<label for="featured-content-tag-name"><?php echo _e( 'Tag name:', 'twentyfourteen' ); ?></label>
				<input type="text" id="featured-content-tag-name" name="featured-content[tag-name]" value="<?php echo esc_attr( $tag_name ); ?>">
				<input type="hidden" id="featured-content-tag-id" name="featured-content[tag-id]" value="<?php echo esc_attr( $settings['tag-id'] ); ?>">
			</p>
			<p>
				<label for="featured-content-quantity"><?php _e( 'Number of posts:', 'twentyfourteen' ); ?></label>
				<input class="small-text" type="number" step="1" min="1" max="<?php echo esc_attr( self::$max_posts ); ?>" id="featured-content-quantity" name="featured-content[quantity]" value="<?php echo esc_attr( $settings['quantity'] ); ?>">
			</p>
			<p>
				<input type="checkbox" id="featured-content-hide-tag" name="featured-content[hide-tag]" <?php checked( $settings['hide-tag'], 1 ); ?>">
				<label for="featured-content-hide-tag"><?php _e( 'Hide tag from displaying in post meta and tag clouds.', 'twentyfourteen' ); ?></label>
			</p>
		</div>
		<?php
	}

	/**
	 * Get settings
	 *
	 * Get all settings recognized by this module. This function will return
	 * all settings whether or not they have been stored in the database yet.
	 * This ensures that all keys are available at all times.
	 *
	 * In the event that you only require one setting, you may pass its name
	 * as the first parameter to the function and only that value will be returned.
	 *
	 * @uses Featured_Content::self::sanitize_quantity()
	 *
	 * @param string $key The key of a recognized setting.
	 * @return mixed Array of all settings by default. A single value if passed as first parameter.
	 */
	public static function get_setting( $key = 'all' ) {
		$saved = (array) get_option( 'featured-content' );

		$defaults = array(
			'hide-tag' => 1,
			'quantity' => 6,
			'tag-id'   => 0,
		);

		$options = wp_parse_args( $saved, $defaults );
		$options = array_intersect_key( $options, $defaults );
		$options['quantity'] = self::sanitize_quantity( $options['quantity'] );

		if ( 'all' != $key ) {
			if ( isset( $options[$key] ) )
				return $options[$key];
			else
				return false;
		}

		return $options;
	}

	/**
	 * Validate settings
	 *
	 * Make sure that all user supplied content is in an
	 * expected format before saving to the database. This
	 * function will also delete the transient set in
	 * Featured_Content::get_featured_content().
	 *
	 * @uses Featured_Content::self::sanitize_quantity()
	 * @uses Featured_Content::self::delete_transient()
	 *
	 * @param array $input
	 * @return array $output
	 */
	public static function validate_settings( $input ) {
		$output = array();

		if ( isset( $input['tag-id'] ) )
			$output['tag-id'] = absint( $input['tag-id'] );

		if ( isset( $input['tag-name'] ) && ! empty( $input['tag-name'] ) ) {
			$new_tag = wp_create_tag( $input['tag-name'] );
			if ( ! is_wp_error( $new_tag ) && isset( $new_tag['term_id'] ) )
				$tag = get_term( $new_tag['term_id'], 'post_tag' );
			if ( isset( $tag->term_id ) )
				$output['tag-id'] = $tag->term_id;
		} else {
			unset( $output['tag-id'] );
		}

		if ( isset( $input['quantity'] ) )
			$output['quantity'] = self::sanitize_quantity( $input['quantity'] );

		$output['hide-tag'] = ( isset( $input['hide-tag'] ) ) ? 1 : 0;

		self::delete_transient();

		return $output;
	}

	/**
	 * Sanitize quantity
	 *
	 * @param int $input The value to sanitize.
	 * @return int A number between 1 and FeaturedContent::$max_posts.
	 *
	 * @uses Featured_Content::$max_posts
	 */
	public static function sanitize_quantity( $input ) {
		$quantity = absint( $input );

		if ( $quantity > self::$max_posts )
			$quantity = self::$max_posts;
		else if ( 1 > $quantity )
			$quantity = 1;

		return $quantity;
	}
}

Featured_Content::setup();
