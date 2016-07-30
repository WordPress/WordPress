<?php get_header(); ?>

<div id="main-content">
	<div class="container">
		<div id="content-area" class="clearfix">
			<div id="left-area">
				<article id="post-0" <?php post_class( 'et_pb_post not_found' ); ?>>
					<?php get_template_part( 'includes/no-results', '404' ); ?>
				</article> <!-- .et_pb_post -->
			</div> <!-- #left-area -->

			<?php get_sidebar(); ?>
		</div> <!-- #content-area -->
	</div> <!-- .container -->
</div> <!-- #main-content -->

<?php get_footer(); ?>