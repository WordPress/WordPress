<?php if ( is_taxonomy_hierarchical( get_queried_object()->taxonomy ) ) : // If the taxonomy is hierarchical. ?>

	<?php $terms = wp_list_categories(
		array(
			'taxonomy'         => get_queried_object()->taxonomy, 
			'child_of'         => get_queried_object_id(), 
			'depth'            => 1, 
			'title_li'         => false, 
			'show_option_none' => false, 
			'echo'             => false 
		) 
	); ?>

	<?php if ( !empty( $terms ) ) : // If a list of child categories/terms was found. ?>

		<nav <?php hybrid_attr( 'menu', 'sub-terms' ); ?>>

			<ul id="menu-sub-terms-items" class="menu-items">
				<?php echo $terms; ?>
			</ul><!-- .sub-terms -->

		</nav><!-- .menu -->

	<?php endif; // End check for list. ?>

<?php endif; // End check for hierarchy. ?>