<?php
/**
 * General settings administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('./admin.php');

if ( ! current_user_can('manage_options') )
	wp_die(__('You do not have sufficient permissions to manage options for this blog.'));

$title = __('General Settings');
$parent_file = 'options-general.php';
/* translators: date and time format for exact current time, mainly about timezones, see http://php.net/date */
$timezone_format = _x('Y-m-d G:i:s', 'timezone date format');

/**
 * Display JavaScript on the page.
 *
 * @package WordPress
 * @subpackage General_Settings_Panel
 */
function add_js() {
?>
<script type="text/javascript">
//<![CDATA[
	jQuery(document).ready(function($){
		$("input[name='date_format']").click(function(){
			if ( "date_format_custom_radio" != $(this).attr("id") )
				$("input[name='date_format_custom']").val( $(this).val() );
		});
		$("input[name='date_format_custom']").focus(function(){
			$("#date_format_custom_radio").attr("checked", "checked");
		});

		$("input[name='time_format']").click(function(){
			if ( "time_format_custom_radio" != $(this).attr("id") )
				$("input[name='time_format_custom']").val( $(this).val() );
		});
		$("input[name='time_format_custom']").focus(function(){
			$("#time_format_custom_radio").attr("checked", "checked");
		});
	});
//]]>
</script>
<?php
}
add_filter('admin_head', 'add_js');

add_contextual_help($current_screen, __('<a href="http://codex.wordpress.org/Settings_General_SubPanel" target="_blank">General Settings</a>'));

include('./admin-header.php');
?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title ); ?></h2>

<form method="post" action="options.php">
<?php settings_fields('general'); ?>

<table class="form-table">
<tr valign="top">
<th scope="row"><label for="blogname"><?php _e('Blog Title') ?></label></th>
<td><input name="blogname" type="text" id="blogname" value="<?php form_option('blogname'); ?>" class="regular-text" /></td>
</tr>
<tr valign="top">
<th scope="row"><label for="blogdescription"><?php _e('Tagline') ?></label></th>
<td><input name="blogdescription" type="text" id="blogdescription"  value="<?php form_option('blogdescription'); ?>" class="regular-text" />
<span class="description"><?php _e('In a few words, explain what this blog is about.') ?></span></td>
</tr>
<?php if ( !is_multisite() ) { ?>
<tr valign="top">
<th scope="row"><label for="siteurl"><?php _e('WordPress address (URL)') ?></label></th>
<td><input name="siteurl" type="text" id="siteurl" value="<?php form_option('siteurl'); ?>" class="regular-text code<?php if ( defined( 'WP_SITEURL' ) ) : ?> disabled" disabled="disabled"<?php else: ?>"<?php endif; ?> /></td>
</tr>
<tr valign="top">
<th scope="row"><label for="home"><?php _e('Blog address (URL)') ?></label></th>
<td><input name="home" type="text" id="home" value="<?php form_option('home'); ?>" class="regular-text code<?php if ( defined( 'WP_HOME' ) ) : ?> disabled" disabled="disabled"<?php else: ?>"<?php endif; ?> />
<span class="description"><?php _e('Enter the address here if you want your blog homepage <a href="http://codex.wordpress.org/Giving_WordPress_Its_Own_Directory">to be different from the directory</a> you installed WordPress.'); ?></span></td>
</tr>
<tr valign="top">
<th scope="row"><label for="admin_email"><?php _e('E-mail address') ?> </label></th>
<td><input name="admin_email" type="text" id="admin_email" value="<?php form_option('admin_email'); ?>" class="regular-text" />
<span class="description"><?php _e('This address is used for admin purposes, like new user notification.') ?></span></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Membership') ?></th>
<td> <fieldset><legend class="screen-reader-text"><span><?php _e('Membership') ?></span></legend><label for="users_can_register">
<input name="users_can_register" type="checkbox" id="users_can_register" value="1" <?php checked('1', get_option('users_can_register')); ?> />
<?php _e('Anyone can register') ?></label>
</fieldset></td>
</tr>
<tr valign="top">
<th scope="row"><label for="default_role"><?php _e('New User Default Role') ?></label></th>
<td>
<select name="default_role" id="default_role"><?php wp_dropdown_roles( get_option('default_role') ); ?></select>
</td>
</tr>
<?php } else { ?>
<tr valign="top">
<th scope="row"><label for="new_admin_email"><?php _e('E-mail address') ?> </label></th>
<td><input name="new_admin_email" type="text" id="new_admin_email" value="<?php form_option('admin_email'); ?>" class="regular-text code" />
<span class="setting-description"><?php _e('This address is used for admin purposes.') ?> <?php _e('If you change this we will send you an email at your new address to confirm it. <strong>The new address will not become active until confirmed.</strong>') ?></span></td>
</tr>
<?php } ?>
<tr>
<?php
if ( !wp_timezone_supported() ) : // no magic timezone support here
?>
<th scope="row"><label for="gmt_offset"><?php _e('Timezone') ?> </label></th>
<td>
<select name="gmt_offset" id="gmt_offset">
<?php
$current_offset = get_option('gmt_offset');
$offset_range = array (-12, -11.5, -11, -10.5, -10, -9.5, -9, -8.5, -8, -7.5, -7, -6.5, -6, -5.5, -5, -4.5, -4, -3.5, -3, -2.5, -2, -1.5, -1, -0.5,
	0, 0.5, 1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5, 5.5, 5.75, 6, 6.5, 7, 7.5, 8, 8.5, 8.75, 9, 9.5, 10, 10.5, 11, 11.5, 12, 12.75, 13, 13.75, 14);
foreach ( $offset_range as $offset ) {
	if ( 0 < $offset )
		$offset_name = '+' . $offset;
	elseif ( 0 == $offset )
		$offset_name = '';
	else
		$offset_name = (string) $offset;

	$offset_name = str_replace(array('.25','.5','.75'), array(':15',':30',':45'), $offset_name);

	$selected = '';
	if ( $current_offset == $offset ) {
		$selected = " selected='selected'";
		$current_offset_name = $offset_name;
	}
	echo "<option value=\"" . esc_attr($offset) . "\"$selected>" . sprintf(__('UTC %s'), $offset_name) . '</option>';
}
?>
</select>
<?php _e('hours'); ?>
<span id="utc-time"><?php printf(__('<abbr title="Coordinated Universal Time">UTC</abbr> time is <code>%s</code>'), date_i18n( $time_format, false, 'gmt')); ?></span>
<?php if ($current_offset) : ?>
	<span id="local-time"><?php printf(__('UTC %1$s is <code>%2$s</code>'), $current_offset_name, date_i18n($time_format)); ?></span>
<?php endif; ?>
<br />
<span class="description"><?php _e('Unfortunately, you have to manually update this for Daylight Savings Time. Lame, we know, but will be fixed in the future.'); ?></span>
</td>
<?php
else: // looks like we can do nice timezone selection!
$current_offset = get_option('gmt_offset');
$tzstring = get_option('timezone_string');

$check_zone_info = true;

// Remove old Etc mappings.  Fallback to gmt_offset.
if ( false !== strpos($tzstring,'Etc/GMT') )
	$tzstring = '';

if (empty($tzstring)) { // Create a UTC+- zone if no timezone string exists
	$check_zone_info = false;
	if ( 0 == $current_offset )
		$tzstring = 'UTC+0';
	elseif ($current_offset < 0)
		$tzstring = 'UTC' . $current_offset;
	else
		$tzstring = 'UTC+' . $current_offset;
}

?>
<th scope="row"><label for="timezone_string"><?php _e('Timezone') ?></label></th>
<td>

<select id="timezone_string" name="timezone_string">
<?php echo wp_timezone_choice($tzstring); ?>
</select>

    <span id="utc-time"><?php printf(__('<abbr title="Coordinated Universal Time">UTC</abbr> time is <code>%s</code>'), date_i18n($timezone_format, false, 'gmt')); ?></span>
<?php if (get_option('timezone_string')) : ?>
	<span id="local-time"><?php printf(__('Local time is <code>%1$s</code>'), date_i18n($timezone_format)); ?></span>
<?php endif; ?>
<br />
<span class="description"><?php _e('Choose a city in the same timezone as you.'); ?></span>
<br />
<span>
<?php if ($check_zone_info && $tzstring) : ?>
	<?php
	$now = localtime(time(),true);
	if ($now['tm_isdst']) _e('This timezone is currently in daylight savings time.');
	else _e('This timezone is currently in standard time.');
	?>
	<br />
	<?php
	if (function_exists('timezone_transitions_get')) {
		$dateTimeZoneSelected = new DateTimeZone($tzstring);
		foreach (timezone_transitions_get($dateTimeZoneSelected) as $tr) {
			if ($tr['ts'] > time()) {
			    $found = true;
				break;
			}
		}

		if ( isset($found) && $found === true ) {
			echo ' ';
			$message = $tr['isdst'] ?
				__('Daylight savings time begins on: <code>%s</code>.') :
				__('Standard time begins  on: <code>%s</code>.');
			printf( $message, date_i18n(get_option('date_format').' '.get_option('time_format'), $tr['ts'] ) );
		} else {
			_e('This timezone does not observe daylight savings time.');
		}
	}
	?>
	</span>
<?php endif; ?>
</td>

<?php endif; ?>
</tr>
<tr>
<th scope="row"><?php _e('Date Format') ?></th>
<td>
	<fieldset><legend class="screen-reader-text"><span><?php _e('Date Format') ?></span></legend>
<?php

	$date_formats = apply_filters( 'date_formats', array(
		__('F j, Y'),
		'Y/m/d',
		'm/d/Y',
		'd/m/Y',
	) );

	$custom = TRUE;

	foreach ( $date_formats as $format ) {
		echo "\t<label title='" . esc_attr($format) . "'><input type='radio' name='date_format' value='" . esc_attr($format) . "'";
		if ( get_option('date_format') === $format ) { // checked() uses "==" rather than "==="
			echo " checked='checked'";
			$custom = FALSE;
		}
		echo ' /> ' . date_i18n( $format ) . "</label><br />\n";
	}

	echo '	<label><input type="radio" name="date_format" id="date_format_custom_radio" value="\c\u\s\t\o\m"';
	checked( $custom );
	echo '/> ' . __('Custom:') . ' </label><input type="text" name="date_format_custom" value="' . esc_attr( get_option('date_format') ) . '" class="small-text" /> ' . date_i18n( get_option('date_format') ) . "\n";

	echo "\t<p>" . __('<a href="http://codex.wordpress.org/Formatting_Date_and_Time">Documentation on date formatting</a>. Click &#8220;Save Changes&#8221; to update sample output.') . "</p>\n";
?>
	</fieldset>
</td>
</tr>
<tr>
<th scope="row"><?php _e('Time Format') ?></th>
<td>
	<fieldset><legend class="screen-reader-text"><span><?php _e('Time Format') ?></span></legend>
<?php

	$time_formats = apply_filters( 'time_formats', array(
		__('g:i a'),
		'g:i A',
		'H:i',
	) );

	$custom = TRUE;

	foreach ( $time_formats as $format ) {
		echo "\t<label title='" . esc_attr($format) . "'><input type='radio' name='time_format' value='" . esc_attr($format) . "'";
		if ( get_option('time_format') === $format ) { // checked() uses "==" rather than "==="
			echo " checked='checked'";
			$custom = FALSE;
		}
		echo ' /> ' . date_i18n( $format ) . "</label><br />\n";
	}

	echo '	<label><input type="radio" name="time_format" id="time_format_custom_radio" value="\c\u\s\t\o\m"';
	checked( $custom );
	echo '/> ' . __('Custom:') . ' </label><input type="text" name="time_format_custom" value="' . esc_attr( get_option('time_format') ) . '" class="small-text" /> ' . date_i18n( get_option('time_format') ) . "\n";
?>
	</fieldset>
</td>
</tr>
<tr>
<th scope="row"><label for="start_of_week"><?php _e('Week Starts On') ?></label></th>
<td><select name="start_of_week" id="start_of_week">
<?php
for ($day_index = 0; $day_index <= 6; $day_index++) :
	$selected = (get_option('start_of_week') == $day_index) ? 'selected="selected"' : '';
	echo "\n\t<option value='" . esc_attr($day_index) . "' $selected>" . $wp_locale->get_weekday($day_index) . '</option>';
endfor;
?>
</select></td>
</tr>
<?php do_settings_fields('general', 'default'); ?>
<?php

$lang_files = array();
if ( is_multisite() && is_dir( ABSPATH . LANGDIR ) && $dh = opendir( ABSPATH . LANGDIR ) ) {
	while ( ( $lang_file = readdir( $dh ) ) !== false ) {
		if ( substr( $lang_file, -3 ) == '.mo' )
			$lang_files[] = $lang_file;
	}
}

if ( !empty($lang_files) ) {
?>
	<tr valign="top">
		<th width="33%" scope="row"><?php _e('Blog language:') ?></th>
		<td>
			<select name="WPLANG" id="WPLANG">
				<?php mu_dropdown_languages( $lang_files, get_option('WPLANG') ); ?>
			</select>
		</td>
	</tr>
<?php
} // languages
?>
</table>

<?php do_settings_sections('general'); ?>

<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
</p>
</form>

</div>

<?php include('./admin-footer.php') ?>
