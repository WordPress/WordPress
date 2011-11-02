<?php
class WP_Admin_Bar {
	private $nodes = array();
	private $root = array();

	public $proto = 'http://';
	public $user;

	function initialize() {
		/* Set the protocol used throughout this code */
		if ( is_ssl() )
			$this->proto = 'https://';

		$this->user = new stdClass;

		if ( is_user_logged_in() ) {
			/* Populate settings we need for the menu based on the current user. */
			$this->user->blogs = get_blogs_of_user( get_current_user_id() );
			if ( is_multisite() ) {
				$this->user->active_blog = get_active_blog_for_user( get_current_user_id() );
				$this->user->domain = empty( $this->user->active_blog ) ? user_admin_url() : trailingslashit( get_home_url( $this->user->active_blog->blog_id ) );
				$this->user->account_domain = $this->user->domain;
			} else {
				$this->user->active_blog = $this->user->blogs[get_current_blog_id()];
				$this->user->domain = trailingslashit( home_url() );
				$this->user->account_domain = $this->user->domain;
			}
		}

		add_action( 'wp_head', 'wp_admin_bar_header' );

		add_action( 'admin_head', 'wp_admin_bar_header' );

		if ( current_theme_supports( 'admin-bar' ) ) {
			$admin_bar_args = get_theme_support( 'admin-bar' ); // add_theme_support( 'admin-bar', array( 'callback' => '__return_false') );
			$header_callback = $admin_bar_args[0]['callback'];
		}

		if ( empty($header_callback) )
			$header_callback = '_admin_bar_bump_cb';

		add_action('wp_head', $header_callback);

		wp_enqueue_script( 'admin-bar' );
		wp_enqueue_style( 'admin-bar' );

		do_action( 'admin_bar_init' );
	}

	public function add_menu( $node ) {
		$this->add_node( $node );
	}

	public function remove_menu( $id ) {
		$this->remove_node( $id );
	}

	/**
	 * Add a node to the menu.
	 *
	 * @param array $args - The arguments for each node.
	 * - id       - string - The ID of the item.
	 * - title    - string - The title of the node.
	 * - parent   - string - The ID of the parent node. Optional.
	 * - href     - string - The link for the item. Optional.
	 * - meta     - array  - Meta data including the following keys: html, class, onclick, target, title.
	 */
	public function add_node( $args ) {
		// Shim for old method signature: add_node( $parent_id, $menu_obj, $args )
		if ( func_num_args() >= 3 && is_string( func_get_arg(0) ) )
			$args = array_merge( array( 'parent' => func_get_arg(0) ), func_get_arg(2) );

		// Ensure we have a valid ID and title.
		if ( empty( $args['title'] ) || empty( $args['id'] ) )
			return false;

		$defaults = array(
			'id'       => false,
			'title'    => false,
			'parent'   => false,
			'href'     => false,
			'meta'     => array(),
		);

		// If the node already exists, keep any data that isn't provided.
		if ( isset( $this->nodes[ $args['id'] ] ) )
			$defaults = (array) $this->nodes[ $args['id'] ];

		$args = wp_parse_args( $args, $defaults );

		$this->nodes[ $args['id'] ] = (object) $args;
	}

	public function remove_node( $id ) {
		unset( $this->nodes[ $id ] );
	}

	public function render() {
		// Link nodes to parents.
		foreach ( $this->nodes as $node ) {

			// Handle root menu items
			if ( empty( $node->parent ) ) {
				$this->root[] = $node;
				continue;
			}

			// If the parent node isn't registered, ignore the node.
			if ( ! isset( $this->nodes[ $node->parent ] ) )
				continue;

			$parent = $this->nodes[ $node->parent ];
			if ( ! isset( $parent->children ) )
				$parent->children = array();

			$parent->children[] = $node;
		}

		?>
		<div id="wpadminbar" class="nojq nojs">
			<div class="quicklinks">
				<ul class="ab-top-menu"><?php

					foreach ( $this->root as $node ) {
						$this->recursive_render( $node );
					}

				?></ul>
			</div>
		</div>

		<?php
	}

	function recursive_render( $node ) {
		$is_parent = ! empty( $node->children );

		$menuclass = $is_parent ? 'menupop' : '';
		if ( ! empty( $node->meta['class'] ) )
			$menuclass .= ' ' . $node->meta['class'];
		?>

		<li id="<?php echo esc_attr( "wp-admin-bar-{$node->id}" ); ?>" class="<?php echo esc_attr( $menuclass ); ?>">
			<a href="<?php echo esc_url( $node->href ) ?>"<?php
				if ( ! empty( $node->meta['onclick'] ) ) :
					?> onclick="<?php echo esc_js( $node->meta['onclick'] ); ?>"<?php
				endif;
			if ( ! empty( $node->meta['target'] ) ) :
				?> target="<?php echo esc_attr( $node->meta['target'] ); ?>"<?php
			endif;
			if ( ! empty( $node->meta['title'] ) ) :
				?> title="<?php echo esc_attr( $node->meta['title'] ); ?>"<?php
			endif;

			?>><?php

			if ( $is_parent ) :
				?><span><?php
			endif;

			echo $node->title;

			if ( $is_parent ) :
				?></span><?php
			endif;

			?></a>

			<?php if ( $is_parent ) : ?>
				<ul><?php

				// Render children.
				foreach ( $node->children as $child_node ) {
					$this->recursive_render( $child_node );
				}

				?></ul>
			<?php endif;

			if ( ! empty( $node->meta['html'] ) )
				echo $node->meta['html'];

			?>
		</li><?php

	}

	function add_menus() {
		// User related, aligned right.
		add_action( 'admin_bar_menu', 'wp_admin_bar_my_account_menu', 10 );

		// Site related.
		add_action( 'admin_bar_menu', 'wp_admin_bar_wp_menu', 10 );
		add_action( 'admin_bar_menu', 'wp_admin_bar_my_sites_menu', 20 );
		add_action( 'admin_bar_menu', 'wp_admin_bar_site_menu', 30 );
		add_action( 'admin_bar_menu', 'wp_admin_bar_updates_menu', 40 );

		// Content related.
		add_action( 'admin_bar_menu', 'wp_admin_bar_comments_menu', 60 );
		add_action( 'admin_bar_menu', 'wp_admin_bar_new_content_menu', 70 );
		add_action( 'admin_bar_menu', 'wp_admin_bar_edit_menu', 80 );
		add_action( 'admin_bar_menu', 'wp_admin_bar_shortlink_menu', 90 );

		if ( ! is_admin() ) {
			add_action( 'admin_bar_menu', 'wp_admin_bar_search_menu', 100 );
		} else {
			add_action( 'admin_bar_menu', 'wp_admin_bar_help_menu', 90 );
		}

		do_action( 'add_admin_bar_menus' );
	}
}
?>