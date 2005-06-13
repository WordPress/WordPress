<?php 
require_once('admin.php');

$title = 'Profile';
$parent_file = 'profile.php';

$wpvarstoreset = array('action', 'profile', 'user');
for ($i=0; $i<count($wpvarstoreset); $i += 1) {
	$wpvar = $wpvarstoreset[$i];
	if (!isset($$wpvar)) {
		if (empty($_POST["$wpvar"])) {
			if (empty($_GET["$wpvar"])) {
				$$wpvar = '';
			} else {
				$$wpvar = $_GET["$wpvar"];
			}
		} else {
			$$wpvar = $_POST["$wpvar"];
		}
	}
}

require_once('../wp-config.php');
auth_redirect();
switch($action) {

case 'update':

	get_currentuserinfo();

	/* checking the nickname has been typed */
	if (empty($_POST["newuser_nickname"])) {
		die (__("<strong>ERROR</strong>: please enter your nickname (can be the same as your username)"));
		return false;
	}

	/* if the ICQ UIN has been entered, check to see if it has only numbers */
	if (!empty($_POST["newuser_icq"])) {
		if ((ereg("^[0-9]+$",$_POST["newuser_icq"]))==false) {
			die (__("<strong>ERROR</strong>: your ICQ UIN can only be a number, no letters allowed"));
			return false;
		}
	}

	/* checking e-mail address */
	if (empty($_POST["newuser_email"])) {
		die (__("<strong>ERROR</strong>: please type your e-mail address"));
		return false;
	} else if (!is_email($_POST["newuser_email"])) {
		die (__("<strong>ERROR</strong>: the e-mail address isn't correct"));
		return false;
	}

	$pass1 = $_POST["pass1"];
	$pass2 = $_POST["pass2"];
	do_action('check_passwords', array($user_login, &$pass1, &$pass2));

	if ( '' == $pass1 ) {
		if ( '' != $pass2 )
			die (__("<strong>ERROR</strong>: you typed your new password only once. Go back to type it twice."));
		$updatepassword = "";
	} else {
		if ('' == $pass2)
			die (__("<strong>ERROR</strong>: you typed your new password only once. Go back to type it twice."));
		if ( $pass1 != $pass2 )
			die (__("<strong>ERROR</strong>: you typed two different passwords. Go back to correct that."));
		$newuser_pass = $pass1;
		$updatepassword = "user_pass=MD5('$newuser_pass'), ";
		wp_clearcookie();
		wp_setcookie($user_login, $newuser_pass);
	}

	$newuser_firstname = wp_specialchars($_POST['newuser_firstname']);
	$newuser_lastname = wp_specialchars($_POST['newuser_lastname']);
	$new_display_name = wp_specialchars($_POST['display_name']);
	$newuser_nickname = $_POST['newuser_nickname'];
	$newuser_nicename = sanitize_title($newuser_nickname);
	$newuser_icq = wp_specialchars($_POST['newuser_icq']);
	$newuser_aim = wp_specialchars($_POST['newuser_aim']);
	$newuser_msn = wp_specialchars($_POST['newuser_msn']);
	$newuser_yim = wp_specialchars($_POST['newuser_yim']);
	$newuser_email = wp_specialchars($_POST['newuser_email']);
	$newuser_url = wp_specialchars($_POST['newuser_url']);
	$newuser_url = preg_match('/^(https?|ftps?|mailto|news|gopher):/is', $newuser_url) ? $newuser_url : 'http://' . $newuser_url; 
	$user_description = $_POST['user_description'];

	$result = $wpdb->query("UPDATE $wpdb->users SET $updatepassword user_email='$newuser_email', user_url='$newuser_url', user_nicename = '$newuser_nicename', display_name = '$new_display_name' WHERE ID = $user_ID");

	update_usermeta( $user_ID, 'first_name', $newuser_firstname );
	update_usermeta( $user_ID, 'last_name', $newuser_lastname );
	update_usermeta( $user_ID, 'nickname', $newuser_nickname );
	update_usermeta( $user_ID, 'description', $user_description );
	update_usermeta( $user_ID, 'icq', $newuser_icq );
	update_usermeta( $user_ID, 'aim', $newuser_aim );
	update_usermeta( $user_ID, 'msn', $newuser_msn );
	update_usermeta( $user_ID, 'yim', $newuser_yim );

	do_action('profile_update', $user_ID);

	wp_redirect('profile.php?updated=true');
break;

case 'IErightclick':

	$bookmarklet_height= 550;

	?>

	<div class="menutop">&nbsp;IE one-click bookmarklet</div>

	<table width="100%" cellpadding="20">
	<tr><td>

	<p>To have a one-click bookmarklet, just copy and paste this<br />into a new text file:</p>
	<?php
	$regedit = "REGEDIT4\r\n[HKEY_CURRENT_USER\Software\Microsoft\Internet Explorer\MenuExt\Post To &WP : ". get_settings('blogname') ."]\r\n@=\"javascript:doc=external.menuArguments.document;Q=doc.selection.createRange().text;void(btw=window.open('". get_settings('siteurl') ."/wp-admin/bookmarklet.php?text='+escape(Q)+'".$bookmarklet_tbpb."&popupurl='+escape(doc.location.href)+'&popuptitle='+escape(doc.title),'bookmarklet','scrollbars=no,width=480,height=".$bookmarklet_height.",left=100,top=150,status=yes'));btw.focus();\"\r\n\"contexts\"=hex:31\"";
	?>
	<pre style="margin: 20px; background-color: #cccccc; border: 1px dashed #333333; padding: 5px; font-size: 12px;"><?php echo $regedit; ?></pre>
	<p>Save it as wordpress.reg, and double-click on this file in an Explorer<br />
	window. Answer Yes to the question, and restart Internet Explorer.<br /><br />
	That's it, you can now right-click in an IE window and select <br />
	'Post to WP' to make the bookmarklet appear. :)</p>

	<p align="center">
	  <form>
		<input class="search" type="button" value="1" name="Close this window" />
	  </form>
	</p>
	</td></tr>
	</table>
	<?php

break;


default:
	$parent_file = 'profile.php';
	include_once('admin-header.php');
	$profiledata = get_userdata($user_ID);

	$bookmarklet_height= 440;

var_dump( $profiledata );
if (isset($updated)) { ?>
<div class="updated">
<p><strong><?php _e('Profile updated.') ?></strong></p>
</div>
<?php } ?>
<div class="wrap">
<h2><?php _e('Profile'); ?></h2>
<form name="profile" id="profile" action="profile.php" method="post">
	<p>
    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="checkuser_id" value="<?php echo $user_ID ?>" />
  </p>

  <table width="99%"  border="0" cellspacing="2" cellpadding="3" class="editform">
    <tr>
      <th width="33%" scope="row"><?php _e('Username:') ?></th>
      <td width="67%"><?php echo $profiledata->user_login; ?></td>
    </tr>
    <tr>
      <th scope="row"><?php _e('Level:') ?></th>
      <td><?php echo $profiledata->user_level; ?></td>
    </tr>
    <tr>
      <th scope="row"><?php _e('Posts:') ?></th>
      <td>    <?php
	$posts = get_usernumposts($user_ID);
	echo $posts;
	?></td>
    </tr>
    <tr>
      <th scope="row"><?php _e('First name:') ?></th>
      <td><input type="text" name="newuser_firstname" id="newuser_firstname" value="<?php echo $profiledata->first_name ?>" /></td>
    </tr>
    <tr>
      <th scope="row"><?php _e('Last name:') ?></th>
      <td><input type="text" name="newuser_lastname" id="newuser_lastname2" value="<?php echo $profiledata->last_name ?>" /></td>
    </tr>
    <tr>
      <th scope="row"><?php _e('Nickname:') ?></th>
      <td><input type="text" name="newuser_nickname" id="newuser_nickname2" value="<?php echo $profiledata->nickname ?>" /></td>
    </tr>
    <tr>
      <th scope="row"><?php _e('How to display name:') ?> </th>
      <td>
	<select name="display_name">
		<option value="<?php echo $profiledata->display_name; ?>"><?php echo $profiledata->display_name; ?></option>
        <option value="<?php echo $profiledata->nickname ?>"><?php echo $profiledata->nickname ?></option>
        <option value="<?php echo $profiledata->user_login ?>"><?php echo $profiledata->user_login ?></option>
	<?php if ( !empty( $profiledata->first_name ) ) : ?>
        <option value="<?php echo $profiledata->first_name ?>"><?php echo $profiledata->first_name ?></option>
	<?php endif; ?>
	<?php if ( !empty( $profiledata->last_name ) ) : ?>
        <option value="<?php echo $profiledata->last_name ?>"><?php echo $profiledata->last_name ?></option>
	<?php endif; ?>
	<?php if ( !empty( $profiledata->first_name ) && !empty( $profiledata->last_name ) ) : ?>
        <option value="<?php echo $profiledata->first_name." ".$profiledata->last_name ?>"><?php echo $profiledata->first_name." ".$profiledata->last_name ?></option>
        <option value="<?php echo $profiledata->last_name." ".$profiledata->first_name ?>"><?php echo $profiledata->last_name." ".$profiledata->first_name ?></option>
	<?php endif; ?>
      </select>        </td>
    </tr>
    <tr>
      <th scope="row"><?php _e('E-mail:') ?></th>
      <td><input type="text" name="newuser_email" id="newuser_email2" value="<?php echo $profiledata->user_email ?>" /></td>
    </tr>
    <tr>
      <th scope="row"><?php _e('Website:') ?></th>
      <td><input type="text" name="newuser_url" id="newuser_url2" value="<?php echo $profiledata->user_url ?>" /></td>
    </tr>
    <tr>
      <th scope="row"><?php _e('ICQ:') ?></th>
      <td><input type="text" name="newuser_icq" id="newuser_icq2" value="<?php if ($profiledata->icq > 0) { echo $profiledata->icq; } ?>" /></td>
    </tr>
    <tr>
      <th scope="row"><?php _e('AIM:') ?></th>
      <td><input type="text" name="newuser_aim" id="newuser_aim2" value="<?php echo $profiledata->aim ?>" /></td>
    </tr>
    <tr>
      <th scope="row"><?php _e('MSN IM:') ?> </th>
      <td><input type="text" name="newuser_msn" id="newuser_msn2" value="<?php echo $profiledata->msn ?>" /></td>
    </tr>
    <tr>
      <th scope="row"><?php _e('Yahoo IM:') ?> </th>
      <td>        <input type="text" name="newuser_yim" id="newuser_yim2" value="<?php echo $profiledata->yim ?>" />      </td>
    </tr>
    <tr>
      <th scope="row"><?php _e('Profile:') ?></th>
      <td><textarea name="user_description" rows="5" id="textarea2" style="width: 99%; "><?php echo $profiledata->user_description ?></textarea></td>
    </tr>
<?php
$show_password_fields = apply_filters('show_password_fields', true);
if ( $show_password_fields ) :
?>
    <tr>
      <th scope="row"><?php _e('New <strong>Password</strong> (Leave blank to stay the same.)') ?></th>
      <td><input type="password" name="pass1" size="16" value="" />
      	<br />
        <input type="password" name="pass2" size="16" value="" /></td>
    </tr>
<?php endif; ?>
  </table>
  <p class="submit">
    <input type="submit" value="<?php _e('Update Profile &raquo;') ?>" name="submit" />
  </p>
</form>
</div>


<?php if ($is_gecko && $profiledata->user_level != 0) { ?>
<div class="wrap">
    <script type="text/javascript">
//<![CDATA[
function addPanel()
        {
          if ((typeof window.sidebar == "object") && (typeof window.sidebar.addPanel == "function"))
            window.sidebar.addPanel("WordPress Post: <?php echo get_settings('blogname'); ?>","<?php echo get_settings('siteurl'); ?>/wp-admin/sidebar.php","");
          else
            alert(<?php __("'No Sidebar found!  You must use Mozilla 0.9.4 or later!'") ?>);
        }
//]]>
</script>
    <strong><?php _e('SideBar') ?></strong><br />
    <?php _e('Add the <a href="#" onclick="addPanel()">WordPress Sidebar</a>!') ?> 
    <?php } elseif (($is_winIE) || ($is_macIE)) { ?>
    <strong><?php _e('SideBar') ?></strong><br />
    <?php __('Add this link to your favorites:') ?><br />
<a href="javascript:Q='';if(top.frames.length==0)Q=document.selection.createRange().text;void(_search=open('<?php echo get_settings('siteurl');
	 ?>/wp-admin/sidebar.php?text='+escape(Q)+'&popupurl='+escape(location.href)+'&popuptitle='+escape(document.title),'_search'))"><?php _e('WordPress Sidebar') ?></a>. 
    
</div>
<?php } ?>
</div>
	<?php

break;
}

/* </Profile | My Profile> */
include('admin-footer.php');
 ?>
