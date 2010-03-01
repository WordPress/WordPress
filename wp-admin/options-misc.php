<?php
/**
 * Miscellaneous settings administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('admin.php');

if ( ! current_user_can('manage_options') )
	wp_die(__('You do not have sufficient permissions to manage options for this blog.'));

$title = __('Miscellaneous Settings');
$parent_file = 'options-general.php';

include('admin-header.php');

?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title ); ?></h2>

<form method="post" action="options.php">
<?php settings_fields('misc'); ?>

<table class="form-table">
<tr>
<th scope="row" class="th-full">
<label for="use_linksupdate">
<input name="use_linksupdate" type="checkbox" id="use_linksupdate" value="1"<?php checked('1', get_option('use_linksupdate')); ?> />
<?php _e('Track links&#8217; update times') ?>
</label>
</th>
</tr>
<?php do_settings_fields('misc', 'default'); ?>
</table>

<?php do_settings_sections('misc'); ?>

<p class="submit">
	<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
</p>

</form>
</div>

<?php include('./admin-footer.php'); ?>