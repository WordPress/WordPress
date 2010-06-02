<?php
/**
 * General settings administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('./admin.php');

if ( ! current_user_can( 'manage_options' ) )
	wp_die( __( 'You do not have sufficient permissions to manage options for this site.' ) );

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

add_contextual_help($current_screen, 
	'<p>' . __('The fields on this screen determine some of the basics of your site setup.') . '</p>' .
	'<p>' . __('Most themes display the site title at the top of every page, in the title bar of the browser, and as the identifying name for syndicated feeds. The tagline is also displayed by many themes.') . '</p>' .
	'<p>' . __('The WordPress URL and the Site URL can be the same (example.com) or different; for example, having the WordPress core files (example.com/wordpress) in a subdirectory instead of the root directory.') . '</p>' .
	'<p>' . __('If you want site visitors to be able to register themselves, as opposed to being registered by the site administrator, check the membership box. A default user role can be set for all new users, whether self-registered or registered by the site administrator.') . '</p>' .
	'<p>' . __('UTC means Coordinated Universal Time.') . '</p>' .
	'<p>' . __('Remember to click the Save Changes button at the bottom of the screen for new settings to take effect.') . '</p>' .
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Settings_General_SubPanel">Documentation on General Settings</a>') . '</p>' .
	'<p>' . __('<a href="http://wordpress.org/support/">Support Forums</a>') . '</p>'
);

include('./admin-header.php');
?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title ); ?></h2>

<form method="post" action="options.php">
<?php settings_fields('general'); ?>

<table class="form-table">
<tr valign="top">
<th scope="row"><label for="blogname"><?php _e('Site Title') ?></label></th>
<td><input name="blogname" type="text" id="blogname" value="<?php form_option('blogname'); ?>" class="regular-text" /></td>
</tr>
<tr valign="top">
<th scope="row"><label for="blogdescription"><?php _e('Tagline') ?></label></th>
<td><input name="blogdescription" type="text" id="blogdescription"  value="<?php form_option('blogdescription'); ?>" class="regular-text" />
<span class="description"><?php _e('In a few words, explain what this site is about.') ?></span></td>
</tr>
<?php if ( !is_multisite() ) { ?>
<tr valign="top">
<th scope="row"><label for="siteurl"><?php _e('WordPress address (URL)') ?></label></th>
<td><input name="siteurl" type="text" id="siteurl" value="<?php form_option('siteurl'); ?>"<?php disabled( defined( 'WP_SITEURL' ) ); ?> class="regular-text code<?php if ( defined( 'WP_SITEURL' ) ) echo ' disabled' ?>" /></td>
</tr>
<tr valign="top">
<th scope="row"><label for="home"><?php _e('Site address (URL)') ?></label></th>
<td><input name="home" type="text" id="home" value="<?php form_option('home'); ?>"<?php disabled( defined( 'WP_HOME' ) ); ?> class="regular-text code<?php if ( defined( 'WP_HOME' ) ) echo ' disabled' ?>" />
<span class="description"><?php _e('Enter the address here if you want your site homepage <a href="http://codex.wordpress.org/Giving_WordPress_Its_Own_Directory">to be different from the directory</a> you installed WordPress.'); ?></span></td>
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
<span class="setting-description"><?php _e('This address is used for admin purposes. If you change this we will send you an e-mail at your new address to confirm it. <strong>The new address will not become active until confirmed.</strong>') ?></span>
<?php
$new_admin_email = get_option( 'new_admin_email' );
if ( $new_admin_email && $new_admin_email != get_option('admin_email') ) : ?>
<div class="updated inline">
<p><?php printf( __('There is a pending change of the admin e-mail to <code>%1$s</code>. <a href="%2$s">Cancel</a>'), $new_admin_email, esc_url( admin_url( 'options.php?dismiss=new_admin_email' ) ) ); ?></p>
</div>
<?php endif; ?>
</td>
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
<span class="description"><?php _e('Unfortunately, you have to manually update this for daylight saving time. The PHP Date/Time library is not supported by your web host.'); ?></span>
</td>
<?php
else: // looks like we can do nice timezone selection!
$current_offset = get_option('gmt_offset');
$tzstring = get_option('timezone_string');

$check_zone_info = true;

// Remove old Etc mappings.  Fallback to gmt_offset.
if ( false !== strpos($tzstring,'Etc/GMT') )
	$tzstring = '';

if ( empty($tzstring) ) { // Create a UTC+- zone if no timezone string exists
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
<?php if ( get_option('timezone_string') || !empty($current_offset) ) : ?>
	<span id="local-time"><?php printf(__('Local time is <code>%1$s</code>'), date_i18n($timezone_format)); ?></span>
<?php endif; ?>
<br />
<span class="description"><?php _e('Choose a city in the same timezone as you.'); ?></span>
<?php if ($check_zone_info && $tzstring) : ?>
<br />
<span>
	<?php
	// Set TZ so localtime works.
	date_default_timezone_set($tzstring);
	$now = localtime(time(), true);
	if ( $now['tm_isdst'] )
		_e('This timezone is currently in daylight saving time.');
	else
		_e('This timezone is currently in standard time.');
	?>
	<br />
	<?php
	if ( function_exists('timezone_transitions_get') ) {
		$found = false;
		$date_time_zone_selected = new DateTimeZone($tzstring);
		$tz_offset = timezone_offset_get($date_time_zone_selected, date_create());
		$right_now = time();
		foreach ( timezone_transitions_get($date_time_zone_selected) as $tr) {
			if ( $tr['ts'] > $right_now ) {
			    $found = true;
				break;
			}
		}

		if ( $found ) {
			echo ' ';
			$message = $tr['isdst'] ?
				__('Daylight saving time begins on: <code>%s</code>.') :
				__('Standard time begins  on: <code>%s</code>.');
			// Add the difference between the current offset and the new offset to ts to get the correct transition time from date_i18n().
			printf( $message, date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $tr['ts'] + ($tz_offset - $tr['offset']) ) );
		} else {
			_e('This timezone does not observe daylight saving time.');
		}
	}
	// Set back to UTC.
	date_default_timezone_set('UTC');
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

	$custom = true;

	foreach ( $date_formats as $format ) {
		echo "\t<label title='" . esc_attr($format) . "'><input type='radio' name='date_format' value='" . esc_attr($format) . "'";
		if ( get_option('date_format') === $format ) { // checked() uses "==" rather than "==="
			echo " checked='checked'";
			$custom = false;
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

	$custom = true;

	foreach ( $time_formats as $format ) {
		echo "\t<label title='" . esc_attr($format) . "'><input type='radio' name='time_format' value='" . esc_attr($format) . "'";
		if ( get_option('time_format') === $format ) { // checked() uses "==" rather than "==="
			echo " checked='checked'";
			$custom = false;
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
	$languages = get_available_languages();
	if ( is_multisite() && !empty( $languages ) ):
?>
	<tr valign="top">
		<th width="33%" scope="row"><?php _e('Site language:') ?></th>
		<td>
			<select name="WPLANG" id="WPLANG">
				<?php mu_dropdown_languages( $languages, get_option('WPLANG') ); ?>
			</select>
		</td>
	</tr>
<?php
	endif;
?>
</table>

<?php do_settings_sections('general'); ?>

<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
</p>
</form>

</div>

<?php include('./admin-footer.php') ?>
