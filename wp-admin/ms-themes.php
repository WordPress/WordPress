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
$parent_file = 'ms-admin.php';
require_once( './admin-header.php' );

if ( isset( $_GET['updated'] ) ) {
	?>
	<div id="message" class="updated"><p><?php _e( 'Site themes saved.' ) ?></p></div>
	<?php
}

$themes = get_themes();
$allowed_themes = get_site_allowed_themes();
?>
<div class="wrap">
	<form action="<?php echo esc_url( admin_url( 'ms-edit.php?action=updatethemes' ) ); ?>" method="post">
		<?php screen_icon(); ?>
		<h2><?php _e( 'Network Themes' ) ?></h2>
		<p><?php _e( 'Disable themes network-wide. You can enable themes on a site-by-site basis.' ) ?></p>
		<table class="widefat">
			<thead>
				<tr>
					<th style="width:15%;text-align:center;"><?php _e( 'Active' ) ?></th>
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
				<tr valign="top" class="<?php echo $class, $class1; ?>">
					<td style="text-align:center;">
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
			<input type="submit" value="<?php _e( 'Update Themes' ) ?>" /></p>
	</form>

	<h3><?php _e( 'Total' )?></h3>
	<p>
		<?php printf( __( 'Themes Installed: %d' ), $total_theme_count); ?>
		<br />
		<?php printf( __( 'Themes Activated: %d' ), $activated_themes_count); ?>
	</p>
</div>

<?php include( './admin-footer.php' ); ?>
