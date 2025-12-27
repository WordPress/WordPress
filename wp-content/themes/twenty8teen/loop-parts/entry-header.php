<?php
/**
 * Template part for displaying the entry header
 * @package Twenty8teen
 */

$default = twenty8teen_default_identimages();
$attr = array( 'class' => twenty8teen_widget_get_classes( 'entry-header' ) );
$opt = get_theme_mod( 'show_entry_header_identimage', $default['show_entry_header_identimage'] );
if ( $opt != 'none' ) {
	$attr = twenty8teen_add_gradient( get_permalink(), $attr, $opt );
}
?>

	<header <?php twenty8teen_attributes( 'header', $attr ); ?>>
		<?php twenty8teen_entry_title();

		$posted_on = twenty8teen_entry_meta_date();
		$byline = twenty8teen_entry_meta_byline();
		if ( $posted_on || $byline ) :
		?>
		<div class="entry-meta">
			<?php echo "$posted_on $byline"; ?>
		</div><!-- .entry-meta -->
		<?php
		endif; ?>
	</header><!-- .entry-header -->
