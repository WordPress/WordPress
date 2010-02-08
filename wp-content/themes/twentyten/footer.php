	</div><!-- #main -->

	<div id="footer">
		<div id="colophon">

<?php get_sidebar('footer'); ?>

			<div id="site-info">
				<a href="<?php bloginfo( 'url' ) ?>/" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ) ?></a>
			</div>

			<div id="site-generator">
				<?php printf( __('Proudly powered by <span id="generator-link">%s</span>.', 'twentyten'), '<a href="http://wordpress.org/" title="' . esc_attr__( 'Semantic Personal Publishing Platform', 'twentyten' ) . '" rel="generator">' . __( 'WordPress', 'twentyten' ) . '</a>' ); ?>
			</div>

		</div><!-- #colophon -->
	</div><!-- #footer -->

</div><!-- #wrapper -->

<?php wp_footer(); ?>

</body>
</html>
