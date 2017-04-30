<?php
/**
 * Metabox inside posts/pages where user can define custom sidebars for an
 * individual post.
 */

global $wp_registered_sidebars;

$available = $wp_registered_sidebars;
$sidebars = CustomSidebars::get_options( 'modifiable' );
?>

<p>
	<?php _e(
		'Here you can replace the default sidebars. Simply select what ' .
		'sidebar you want to show for this post!', CSB_LANG
	); ?>
</p>

<?php if ( ! empty( $sidebars ) ) { ?>
	<?php foreach ( $sidebars as $s ) { ?>
		<?php $sb_name = $available[ $s ]['name']; ?>
		<p>
			<b><?php echo esc_html( $sb_name ); ?></b>:
			<select name="cs_replacement_<?php echo esc_attr( $s ); ?>">
				<option value=""></option>
				<?php foreach ( $available as $a ) : ?>
				<option value="<?php echo esc_attr( $a['id'] ); ?>" <?php selected( $selected[ $s ], $a['id'] ); ?>>
					<?php echo esc_html( $a['name'] ); ?>
				</option>
				<?php endforeach; ?>
			</select>
		</p>
	<?php
	}
} else {
	?>
	<p id="message" class="updated">
		<?php _e(
			'All sidebars have been locked, you cannot replace them. ' .
			'Go to <a href="widgets.php">the widgets page</a> to unlock a ' .
			'sidebar', CSB_LANG
		); ?>
	</p>
	<?php
}