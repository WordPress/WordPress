<?php

/**
 * admin side controller
 * accessible in $polylang global object
 *
 * properties:
 * options          => inherited, reference to Polylang options array
 * model            => inherited, reference to PLL_Model object
 * links_model      => inherited, reference to PLL_Links_Model object
 * links            => inherited, reference to PLL_Admin_Links object
 * static_pages     => inherited, reference to PLL_Admin_Static_Pages object
 * filters_links    => inherited, reference to PLL_Filters_Links object
 * curlang          => inherited, optional, current language used to filter the content (language of the post or term being edited, equal to filter_lang otherwise)
 * filter_lang      => inherited, optional, current status of the admin languages filter (in the admin bar)
 * pref_lang        => inherited, preferred language used as default when saving posts or terms
 * filters          => reference to PLL_Filters object
 * filters_columns  => reference to PLL_Admin_Filters_Columns object
 * filters_post     => reference to PLL_Admin_Filters_Post object
 * filters_term     => reference to PLL_Admin_filters_Term object
 * nav_menu         => reference to PLL_Admin_Nav_Menu object
 * filters_media    => optional, reference to PLL_Admin_Filters_Media object
 *
 * @since 1.2
 */
class PLL_Admin extends PLL_Admin_Base {
	public $filters, $filters_columns, $filters_post, $filters_term, $nav_menu, $sync, $filters_media;

	/**
	 * loads the polylang text domain
	 * setups filters and action needed on all admin pages and on plugins page
	 *
	 * @since 1.2
	 *
	 * @param object $links_model
	 */
	public function __construct( &$links_model ) {
		parent::__construct( $links_model );

		// adds a 'settings' link in the plugins table
		add_filter( 'plugin_action_links_' . POLYLANG_BASENAME, array( $this, 'plugin_action_links' ) );
		add_action( 'in_plugin_update_message-' . POLYLANG_BASENAME, array( $this, 'plugin_update_message' ), 10, 2 );
	}

	/**
	 * setups filters and action needed on all admin pages and on plugins page
	 * loads the settings pages or the filters base on the request
	 *
	 * @since 1.2
	 */
	public function init() {
		parent::init();

		// setup filters for admin pages
		// priority 5 to make sure filters are there before customize_register is fired
		if ( $this->model->get_languages_list() ) {
			add_action( 'wp_loaded', array( $this, 'add_filters' ), 5 );
		}
	}

	/**
	 * adds a 'settings' link in the plugins table
	 *
	 * @since 0.1
	 *
	 * @param array $links list of links associated to the plugin
	 * @return array modified list of links
	 */
	public function plugin_action_links( $links ) {
		array_unshift( $links, '<a href="admin.php?page=mlang">' . __( 'Settings', 'polylang' ) . '</a>' );
		return $links;
	}

	/**
	 * adds the upgrade notice in plugins table
	 *
	 * @since 1.1.6
	 *
	 * @param array  $plugin_data Not used
	 * @param object $r           Plugin update data
	 */
	function plugin_update_message( $plugin_data, $r ) {
		if ( isset( $r->upgrade_notice ) ) {
			printf( '<p style="margin: 3px 0 0 0; border-top: 1px solid #ddd; padding-top: 3px">%s</p>', esc_html( $r->upgrade_notice ) );
		}
	}

	/**
	 * setup filters for admin pages
	 *
	 * @since 1.2
	 */
	public function add_filters() {
		// all these are separated just for convenience and maintainability
		$classes = array( 'Filters', 'Filters_Columns', 'Filters_Post', 'Filters_Term', 'Nav_Menu', 'Sync' );

		// don't load media filters if option is disabled or if user has no right
		if ( $this->options['media_support'] && ( $obj = get_post_type_object( 'attachment' ) ) && ( current_user_can( $obj->cap->edit_posts ) || current_user_can( $obj->cap->create_posts ) ) ) {
			$classes[] = 'Filters_Media';
		}

		foreach ( $classes as $class ) {
			$obj = strtolower( $class );

			/**
			 * Filter the class to instantiate when loading admin filters
			 *
			 * @since 1.5
			 *
			 * @param string $class class name
			 */
			$class = apply_filters( 'pll_' . $obj, 'PLL_Admin_' . $class );
			$this->$obj = new $class( $this );
		}
	}
}
