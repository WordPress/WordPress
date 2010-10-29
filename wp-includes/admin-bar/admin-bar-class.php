<?php
class WP_Admin_Bar {
	var $changed_locale = false;
	var $menu;
	var $need_to_change_locale = false;
	var $proto = 'http://';
	var $user;

	function initialize() {
		/* Only load super admin menu code if the logged in user is a super admin */
		if ( is_super_admin() ) {
			require( ABSPATH . WPINC . '/admin-bar/admin-bar-superadmin.php' );
		}
		
		/* Set the protocol used throughout this code */
		if ( is_ssl() ) 
			$this->proto = 'https://';

		$this->user = new stdClass;
		$this->menu = new stdClass;

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
		$this->user->locale = get_locale();

		add_action( 'wp_head', 'wp_admin_bar_header' );
		add_action( 'wp_head', 'wp_admin_body_style');

		add_action( 'admin_head', 'wp_admin_bar_header' );
		add_action( 'admin_head', 'wp_admin_body_style');

		wp_enqueue_script( 'admin-bar' );
		wp_enqueue_style( 'admin-bar' );

		if ( is_super_admin() ) {
			wp_enqueue_style( 'super-admin-bar' );
		}
		
		do_action( 'admin_bar_init' );
	}

	function add_menu( $args = array() ) {
		$defaults = array(
			'title' => false,
			'href' => false,
			'parent' => false, // false for a root menu, pass the ID value for a submenu of that menu.
			'id' => false, // defaults to a sanitized title value.
			'meta' => false // array of any of the following options: array( 'html' => '', 'class' => '', 'onclick' => '', target => '' );
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
		<div id="wpadminbar" class="snap_nopreview no-grav">
			<div class="quicklinks">
				<ul>
					<?php foreach ( (array) $this->menu as $id => $menu_item ) : ?>
						<?php $this->recursive_render( $id, $menu_item ) ?>
					<?php endforeach; ?>
				</ul>
			</div>

			<div id="adminbarsearch-wrap">
				<form action="<?php echo home_url(); ?>" method="get" id="adminbarsearch">
					<input class="adminbar-input" name="s" id="adminbar-search" type="text" title="<?php esc_attr_e( 'Search' ); ?>" value="" maxlength="150" /> 
					<button type="submit" class="adminbar-button"><span><?php _e('Search'); ?></span></button>
				</form>
			</div>
		</div>

		<?php
		/* Wipe the menu, might reduce memory usage, but probably not. */
		$this->menu = null;
	}

	/* Helpers */
	function recursive_render( $id, &$menu_item ) { ?>
		<?php $menuclass = ( ! empty( $menu_item['children'] ) ) ? 'menupop ' : ''; ?>

		<li class="<?php echo $menuclass . "ab-$id" ?><?php 
			if ( ! empty( $menu_item['meta']['class'] ) ) : 
				echo ' ' . $menu_item['meta']['class'];
			endif; 
		?>">
			<a href="<?php echo strip_tags( $menu_item['href'] ) ?>"<?php 
				if ( ! empty( $menu_item['meta']['onclick'] ) ) :
					?> onclick="<?php echo $menu_item['meta']['onclick']; ?>"<?php 
				endif;
			if ( ! empty( $menu_item['meta']['target'] ) ) :
				?> target="<?php echo $menu_item['meta']['target']; ?>"<?php 
			endif; 
			
			?>><?php 
			
			if ( ! empty( $menuclass ) ) : 
				?><span><?php 
			endif; 
			
			echo $menu_item['title'];
			
			if ( ! empty( $menuclass ) ) : 
				?></span><?php 
			endif; 
			
			?></a>

			<?php if ( ! empty( $menu_item['children'] ) ) : ?>
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
		foreach( $menu as $id => &$menu_item ) {
			if ( $parent_id == $id ) {
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
		add_action( 'wp_before_admin_bar_render', 'wp_admin_bar_me_separator', 10 );
		add_action( 'wp_before_admin_bar_render', 'wp_admin_bar_my_account_menu', 20 );
		add_action( 'wp_before_admin_bar_render', 'wp_admin_bar_my_blogs_menu', 30 );
		add_action( 'wp_before_admin_bar_render', 'wp_admin_bar_edit_menu', 40 );
		add_action( 'wp_before_admin_bar_render', 'wp_admin_bar_new_content_menu', 50 );
		add_action( 'wp_before_admin_bar_render', 'wp_admin_bar_comments_menu', 60 );
		add_action( 'wp_before_admin_bar_render', 'wp_admin_bar_shortlink_menu', 70 );
		
		if ( is_multisite() && is_super_admin() && function_exists('wp_admin_bar_superadmin_settings_menu') )
			add_action( 'wp_before_admin_bar_render', 'wp_admin_bar_superadmin_settings_menu', 1000 );

		do_action('add_admin_bar_menus');
	}

	function remove_node( $id, &$menu ) {
		foreach( $menu as $menu_item_id => &$menu_item ) {
			if ( $menu_item_id == $id ) {
				$menu_item = null;
				return true;
			}

			if ( ! empty( $menu->{$menu_item_id}['children'] ) )
				$this->remove_node( $id, $menu->{$menu_item_id}['children'] );
		}

		return false;
	}

	// TODO: Convert to a core feature for multisite or remove
	function load_user_locale_translations() {
		$this->need_to_change_locale = ( get_locale() != $this->user->locale );
		if ( ! $this->need_to_change_locale ) 
			return;
		/*
		$this->previous_translations = get_translations_for_domain( 'default' );
		$this->adminbar_locale_filter = lambda( '$_', '$GLOBALS["wp_admin_bar"]->user->locale;' );
		unload_textdomain( 'default' );
		add_filter( 'locale', $this->adminbar_locale_filter );
		load_default_textdomain();
		$this->changed_locale = true;
		*/
	}

	function unload_user_locale_translations() {
		global $l10n;
		if ( ! $this->changed_locale ) 
			return;
		/*
		remove_filter( 'locale', $this->adminbar_locale_filter );
		$l10n['default'] = &$this->previous_translations;
		*/
	}
}
?>
