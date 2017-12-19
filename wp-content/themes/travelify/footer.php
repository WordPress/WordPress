<?php
/**
 * Displays the footer section of the theme.
 *
 */
?>
	   </div><!-- #main -->

	   <?php
	      /**
	       * travelify_after_main hook
	       */
	      do_action( 'travelify_after_main' );
	   ?>

	   <?php
	   	/**
	   	 * travelify_before_footer hook
	   	 */
	   	do_action( 'travelify_before_footer' );
	   ?>

	   <footer id="footerarea" class="clearfix">
			<?php
		      /**
		       * travelify_footer hook
				 *
				 * HOOKED_FUNCTION_NAME PRIORITY
				 *
				 * travelify_footer_widget_area 10
				 * travelify_open_sitegenerator_div 20
				 * travelify_socialnetworks 25
				 * travelify_footer_info 30
				 * travelify_close_sitegenerator_div 35
				 * travelify_backtotop_html 40
		       */
		      do_action( 'travelify_footer' );
		   ?>
		</footer>

		<?php
	   	/**
	   	 * travelify_after_footer hook
	   	 */
	   	do_action( 'travelify_after_footer' );
	   ?>

	</div><!-- .wrapper -->

	<?php
		/**
		 * travelify_after hook
		 */
		do_action( 'travelify_after' );
	?>

<?php wp_footer(); ?>

</body>
</html>