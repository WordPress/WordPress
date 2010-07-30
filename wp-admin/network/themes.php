<?php
/**
 * Multisite themes administration panel.
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.0.0
 */

require_once( './admin.php' );

if ( ! current_user_can( 'manage_network_themes' ) )
	wp_die( __( 'You do not have permission to access this page.' ) );

$title = __( 'Network Themes' );
$parent_file = 'themes.php';

add_contextual_help($current_screen,
	'<p>' . __('This screen enables and disables the inclusion of themes available to choose in the Appearance menu for each site. It does not activate or deactivate which theme a site is currently using.') . '</p>' .
	'<p>' . __('If the network admin disables a theme that is in use, it can still remain selected on that site. If another theme is chosen, the disabled theme will not appear in the site&#8217;s Appearance > Themes screen.') . '</p>' .
	'<p>' . __('Themes can be enabled on a site by site basis by the network admin on the Edit Site screen you go to via the Edit action link on the Sites screen.') . '</p>' .
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Super_Admin_Themes_SubPanel" target="_blank">Documentation on Network Themes</a>') . '</p>' .
	'<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
);

require_once( '../admin-header.php' );

if ( isset( $_GET['updated'] ) ) {
	?>
	<div id="message" class="updated"><p><?php _e( 'Site themes saved.' ) ?></p></div>
	<?php
}

$themes = get_themes();
$allowed_themes = get_site_allowed_themes();
?>
<div class="wrap">
	<form action="<?php echo esc_url( network_admin_url( 'edit.php?action=updatethemes' ) ); ?>" method="post">
		<?php screen_icon(); ?>
		<h2><?php _e( 'Network Themes' ) ?></h2>
		<p><?php _e( 'Themes must be enabled for your network before they will be available to individual sites.' ) ?></p>
		<p class="submit">
			<input type="submit" value="<?php _e( 'Apply Changes' ) ?>" /></p>
		<table class="widefat">
			<thead>
				<tr>
					<th style="width:15%;"><?php _e( 'Enable' ) ?></th>
					<th style="width:25%;"><?php _e( 'Theme' ) ?></th>
					<th style="width:10%;"><?php _e( 'Version' ) ?></th>
					<th style="width:60%;"><?php _e( 'Description' ) ?></th>
				</tr>
			</thead>
			<tbody id="plugins">
			<?php
			$total_theme_count = $activated_themes_count = 0;
			$class = '';
			foreach ( (array) $themes as $key => $theme ) {
				$total_theme_count++;
				$theme_key = esc_html( $theme['Stylesheet'] );
				$class = ( 'alt' == $class ) ? '' : 'alt';
				$class1 = $enabled = $disabled = '';
				$enabled = $disabled = false;

				if ( isset( $allowed_themes[$theme_key] ) == true ) {
					$enabled = true;
					$activated_themes_count++;
					$class1 = 'active';
				} else {
					$disabled = true;
				}
				?>
				<tr valign="top" class="<?php echo $class . ' ' . $class1; ?>">
					<td>
						<label><input name="theme[<?php echo $theme_key ?>]" type="radio" id="enabled_<?php echo $theme_key ?>" value="enabled" <?php checked( $enabled ) ?> /> <?php _e( 'Yes' ) ?></label>
						&nbsp;&nbsp;&nbsp;
						<label><input name="theme[<?php echo $theme_key ?>]" type="radio" id="disabled_<?php echo $theme_key ?>" value="disabled" <?php checked( $disabled ) ?> /> <?php _e( 'No' ) ?></label>
					</td>
					<th scope="row" style="text-align:left;"><?php echo $key ?></th>
					<td><?php echo $theme['Version'] ?></td>
					<td><?php echo $theme['Description'] ?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>

		<p class="submit">
			<input type="submit" value="<?php _e( 'Apply Changes' ) ?>" /></p>
	</form>

	<h3><?php _e( 'Total' )?></h3>
	<p>
		<?php printf( __( 'Themes Installed: %d' ), $total_theme_count); ?>
		<br />
		<?php printf( __( 'Themes Enabled: %d' ), $activated_themes_count); ?>
	</p>
</div>

<?php include( '../admin-footer.php' ); ?>
