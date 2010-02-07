	</div><!-- #main -->
	
	<div id="footer">
		<div id="colophon">
		
<?php get_sidebar('footer'); ?>				
		
			<div id="site-info">
				<a href="<?php bloginfo( 'url' ) ?>/" title="<?php bloginfo( 'name' ) ?>" rel="home"><?php bloginfo( 'name' ) ?></a>
			</div>
			
			<div id="site-generator">
				Proudly powered by <span id="generator-link"><a href="http://wordpress.org/" title="<?php _e( 'Semantic Personal Publishing Platform', 'twentyten' ) ?>" rel="generator"><?php _e( 'WordPress', 'twentyten' ) ?></a>.</span>
			</div>
			
		</div><!-- #colophon -->
	</div><!-- #footer -->
	
</div><!-- #wrapper -->	

<?php wp_footer(); ?>

</body>
</html>
