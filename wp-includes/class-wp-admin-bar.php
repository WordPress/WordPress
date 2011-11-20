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
		$this->root = new stdClass;
		$this->root->children  = (object) array(
			'primary'   => array(),
			'secondary' => array(),
		);

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
	 * - id         - string    - The ID of the item.
	 * - title      - string    - The title of the node.
	 * - parent     - string    - The ID of the parent node. Optional.
	 * - href       - string    - The link for the item. Optional.
	 * - secondary  - boolean   - If the item should be part of a secondary menu. Optional. Default false.
	 * - meta       - array     - Meta data including the following keys: html, class, onclick, target, title.
	 */
	public function add_node( $args ) {
		// Shim for old method signature: add_node( $parent_id, $menu_obj, $args )
		if ( func_num_args() >= 3 && is_string( func_get_arg(0) ) )
			$args = array_merge( array( 'parent' => func_get_arg(0) ), func_get_arg(2) );

		// Ensure we have a valid title.
		if ( empty( $args['title'] ) )
			return false;

		if ( empty( $args['id'] ) ) {
			_doing_it_wrong( __METHOD__, __( 'The menu ID should not be empty.' ), '3.3' );
			$args['id'] = esc_attr( sanitize_title( trim( $args['title'] ) ) );
		}

		$defaults = array(
			'id'        => false,
			'title'     => false,
			'parent'    => false,
			'href'      => false,
			'secondary' => false,
			'meta'      => array(),
		);

		// If the node already exists, keep any data that isn't provided.
		if ( isset( $this->nodes[ $args['id'] ] ) )
			$defaults = (array) $this->nodes[ $args['id'] ];

		$args = wp_parse_args( $args, $defaults );
		$args['children'] = (object) array(
			'primary'   => array(),
			'secondary' => array(),
		);

		$this->nodes[ $args['id'] ] = (object) $args;
	}

	public function remove_node( $id ) {
		unset( $this->nodes[ $id ] );
	}

	public function render() {

		$back_compat_parents = array(
			'appearance' => 'site-name',
		);

		// Link nodes to parents.
		foreach ( $this->nodes as $node ) {

			if ( isset( $back_compat_parents[ $node->parent ] ) )
				$node->parent = $back_compat_parents[ $node->parent ];

			// Handle root menu items
			if ( empty( $node->parent ) ) {
				$parent = $this->root;

			// If the parent node isn't registered, ignore the node.
			} elseif ( ! isset( $this->nodes[ $node->parent ] ) ) {
				continue;

			} else {
				$parent = $this->nodes[ $node->parent ];
			}

			if ( $node->secondary )
				$parent->children->secondary[] = $node;
			else
				$parent->children->primary[] = $node;
		}

		?>
		<div id="wpadminbar" class="nojq nojs">
			<div class="quicklinks">
				<ul class="ab-top-menu"><?php

					foreach ( $this->root->children->primary as $node ) {
						$this->recursive_render( $node );
					}

				?></ul>
				<ul class="ab-top-menu ab-top-secondary"><?php

					foreach ( $this->root->children->secondary as $node ) {
						$this->recursive_render( $node );
					}

				?></ul>
			</div>
		</div>

		<?php
	}

	function recursive_render( $node ) {
		if ( ! $node->children->primary && $node->children->secondary ) {
			$node->children->primary = $node->children->secondary;
			$node->children->secondary = array();
		}

		$is_parent = (bool) $node->children->primary;
		$has_link  = (bool) $node->href;

		$menuclass = $is_parent ? 'menupop' : '';
		if ( ! empty( $node->meta['class'] ) )
			$menuclass .= ' ' . $node->meta['class'];

		$tabindex = !empty($node->meta['tabindex']) ? $node->meta['tabindex'] : 10;
		?>

		<li id="<?php echo esc_attr( "wp-admin-bar-{$node->id}" ); ?>" class="<?php echo esc_attr( $menuclass ); ?>"><?php
			if ( $has_link ):
				?><a class="ab-item" tabindex="<?php echo (int) $tabindex; ?>" href="<?php echo esc_url( $node->href ) ?>"<?php
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
			else:
				?><div class="ab-item ab-empty-item" tabindex="<?php echo (int) $tabindex; ?>"><?php
			endif;

			echo $node->title;

			if ( $has_link ):
				?></a><?php
			else:
				?></div><?php
			endif;

			if ( $is_parent ) :
				?><div class="ab-sub-wrapper"><?php

					// Render primary submenu
					?><ul class="ab-submenu"><?php
					foreach ( $node->children->primary as $child_node ) {
						$this->recursive_render( $child_node );
					}
					?></ul><?php

					// Render secondary submenu
					if ( ! empty( $node->children->secondary ) ):
						?><ul class="ab-submenu ab-sub-secondary"><?php
						foreach ( $node->children->secondary as $child_node ) {
							$this->recursive_render( $child_node );
						}
						?></ul><?php
					endif;

				?></div><?php
			endif;

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

		if ( ! is_admin() )
			add_action( 'admin_bar_menu', 'wp_admin_bar_search_menu', 100 );

		do_action( 'add_admin_bar_menus' );
	}
}
?>
