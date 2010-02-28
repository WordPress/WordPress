<?php get_header(); ?>

		<div id="container">
			<div id="content">

				<h1 class="page-title"><?php 
					printf(__( 'Category Archives: %s', 'twentyten' ), '<span>' . single_cat_title('', false) . '</span>');
				?></h1>
				<?php $categorydesc = category_description(); if ( !empty($categorydesc) ) echo apply_filters( 'archive_meta', '<div class="archive-meta">' . $categorydesc . '</div>' ); ?>

<?php get_generic_template( 'loop', 'category' ); ?>

			</div><!-- #content -->
		</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>