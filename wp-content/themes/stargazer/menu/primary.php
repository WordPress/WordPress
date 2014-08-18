<?php if ( has_nav_menu( 'primary' ) ) : // Check if there's a menu assigned to the 'primary' location. ?>

	<nav <?php hybrid_attr( 'menu', 'primary' ); ?>>

		<h3 id="menu-primary-title" class="menu-toggle">
			<button class="screen-reader-text"><?php
				/* Translators: %s is the nav menu name. This is the nav menu title shown to screen readers. */
				printf( _x( '%s Menu', 'nav menu title', 'stargazer' ), hybrid_get_menu_location_name( 'primary' ) ); 
			?></button>
		</h3><!-- .menu-toggle -->

		<?php wp_nav_menu(
			array(
				'theme_location'  => 'primary',
				'container'       => '',
				'menu_id'         => 'menu-primary-items',
				'menu_class'      => 'menu-items',
				'fallback_cb'     => '',
				'items_wrap'      => '<div class="wrap"><ul id="%s" class="%s">%s</ul>' . get_search_form( false ) . '</div>'
			)
		); ?>

	</nav><!-- #menu-primary -->

<?php endif; // End check for menu. ?>