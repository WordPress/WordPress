<?php
/**
 * The template for displaying the footer
 * Contains the footer.
 *
 * @package Twenty8teen
 */

?>
	<footer id="footer" <?php twenty8teen_area_classes( 'footer', 'site-footer' ); ?>>
		<?php get_sidebar( 'footer' ); ?>
	</footer><!-- #footer -->
	<a class="skip-link screen-reader-text"
		href="#"><?php esc_html_e( 'Jump to top', 'twenty8teen' ); ?></a>

	<?php wp_footer(); ?>

</body>
</html>
