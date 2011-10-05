<?php
class WP_Admin_Bar {
	var $menu;
	var $proto = 'http://';
	var $user;

	function initialize() {
		/* Set the protocol used throughout this code */
		if ( is_ssl() )
			$this->proto = 'https://';

		$this->user = new stdClass;
		$this->menu = new stdClass;

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

	function add_menu( $args = array() ) {
		$defaults = array(
			'title' => false,
			'href' => false,
			'parent' => false, // false for a root menu, pass the ID value for a submenu of that menu.
			'id' => false, // defaults to a sanitized title value.
			'meta' => false // array of any of the following options: array( 'html' => '', 'class' => '', 'onclick' => '', target => '', title => '' );
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );

		if ( empty( $title ) )
			return false;

		/* Make sure we have a valid ID */
		if ( empty( $id ) )
			$id = esc_attr( sanitize_title( trim( $title ) ) );

		if ( ! empty( $parent ) ) {
			/* Add the menu to the parent item */
			$child = array( 'id' => $id, 'title' => $title, 'href' => $href );

			if ( ! empty( $meta ) )
				$child['meta'] = $meta;

			$this->add_node( $parent, $this->menu, $child );
		} else {
			/* Add the menu item */
			$this->menu->{$id} = array( 'title' => $title, 'href' => $href );

			if ( ! empty( $meta ) )
				$this->menu->{$id}['meta'] = $meta;
		}
	}

	function remove_menu( $id ) {
		return $this->remove_node( $id, $this->menu );
	}

	function render() {
		?>
		<div id="wpadminbar" class="nojq nojs">
			<div class="quicklinks">
				<ul class="ab-top-menu">
					<?php foreach ( (array) $this->menu as $id => $menu_item ) : ?>
						<?php $this->recursive_render( $id, $menu_item ) ?>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>

		<?php
		/* Wipe the menu, might reduce memory usage, but probably not. */
		$this->menu = null;
	}

	/* Helpers */
	function recursive_render( $id, &$menu_item ) { ?>
		<?php
		$is_parent =  ! empty( $menu_item['children'] );

		$menuclass = $is_parent ? 'menupop' : '';
		if ( ! empty( $menu_item['meta']['class'] ) )
			$menuclass .= ' ' . $menu_item['meta']['class'];
		?>

		<li id="<?php echo esc_attr( "wp-admin-bar-$id" ); ?>" class="<?php echo esc_attr( $menuclass ); ?>">
			<a href="<?php echo esc_url( $menu_item['href'] ) ?>"<?php
				if ( ! empty( $menu_item['meta']['onclick'] ) ) :
					?> onclick="<?php echo esc_js( $menu_item['meta']['onclick'] ); ?>"<?php
				endif;
			if ( ! empty( $menu_item['meta']['target'] ) ) :
				?> target="<?php echo esc_attr( $menu_item['meta']['target'] ); ?>"<?php
			endif;
			if ( ! empty( $menu_item['meta']['title'] ) ) :
				?> title="<?php echo esc_attr( $menu_item['meta']['title'] ); ?>"<?php
			endif;

			?>><?php

			if ( $is_parent ) :
				?><span><?php
			endif;

			echo $menu_item['title'];

			if ( $is_parent ) :
				?></span><?php
			endif;

			?></a>

			<?php if ( $is_parent ) : ?>
			<ul>
				<?php foreach ( $menu_item['children'] as $child_id => $child_menu_item ) : ?>
					<?php $this->recursive_render( $child_id, $child_menu_item ); ?>
				<?php endforeach; ?>
			</ul>
			<?php endif; ?>

			<?php if ( ! empty( $menu_item['meta']['html'] ) ) : ?>
				<?php echo $menu_item['meta']['html']; ?>
			<?php endif; ?>
		</li><?php
	}

	function add_node( $parent_id, &$menu, $child ) {
		foreach( $menu as $id => $menu_item ) {
			if ( $parent_id == $id ) {
				if ( ! isset( $menu->{$parent_id}['children'] ) )
					$menu->{$parent_id}['children'] = new stdClass;
				$menu->{$parent_id}['children']->{$child['id']} = $child;
				$child = null;
				return true;
			}

			if ( ! empty( $menu->{$id}['children'] ) )
				$this->add_node( $parent_id, $menu->{$id}['children'], $child );
		}

		$child = null;

		return false;
	}

	function add_menus() {
		add_action( 'admin_bar_menu', 'wp_admin_bar_wp_menu', 10 );
		add_action( 'admin_bar_menu', 'wp_admin_bar_my_account_menu', 10 );
		add_action( 'admin_bar_menu', 'wp_admin_bar_my_sites_menu', 20 );
		add_action( 'admin_bar_menu', 'wp_admin_bar_edit_menu', 30 );
		add_action( 'admin_bar_menu', 'wp_admin_bar_new_content_menu', 40 );
		add_action( 'admin_bar_menu', 'wp_admin_bar_comments_menu', 50 );
		add_action( 'admin_bar_menu', 'wp_admin_bar_updates_menu', 70 );
		add_action( 'admin_bar_menu', 'wp_admin_bar_shortlink_menu', 80 );

		if ( ! is_admin() ) {
			add_action( 'admin_bar_menu', 'wp_admin_bar_blog_front_menu', 25 );
			add_action( 'admin_bar_menu', 'wp_admin_bar_search_menu', 100 );
		} else {
			add_action( 'admin_bar_menu', 'wp_admin_bar_blog_admin_menu', 25 );
			add_action( 'admin_bar_menu', 'wp_admin_bar_help_menu', 90 );
		}

		do_action( 'add_admin_bar_menus' );
	}

	function remove_node( $id, &$menu ) {
		if ( isset( $menu->$id ) ) {
			unset( $menu->$id );
			return true;
		}

		foreach( $menu as $menu_item_id => $menu_item ) {
			if ( ! empty( $menu->{$menu_item_id}['children'] ) )
				$this->remove_node( $id, $menu->{$menu_item_id}['children'] );
		}

		return false;
	}
}
?>