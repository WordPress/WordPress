<?php 
require_once('../wp-includes/wp-l10n.php');

$title = "Profile";
/* <Profile | My Profile> */

function add_magic_quotes($array) {
	foreach ($array as $k => $v) {
		if (is_array($v)) {
			$array[$k] = add_magic_quotes($v);
		} else {
			$array[$k] = addslashes($v);
		}
	}
	return $array;
} 

if (!get_magic_quotes_gpc()) {
	$_GET    = add_magic_quotes($_GET);
	$_POST   = add_magic_quotes($_POST);
	$_COOKIE = add_magic_quotes($_COOKIE);
}

$wpvarstoreset = array('action','standalone','redirect','profile','user');
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
require_once('auth.php');
switch($action) {

case 'update':

	get_currentuserinfo();

	/* checking the nickname has been typed */
	if (empty($_POST["newuser_nickname"])) {
		die (__("<strong>ERROR</strong>: please enter your nickname (can be the same as your login)"));
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

	if ($_POST["pass1"] == "") {
		if ($_POST["pass2"] != "")
			die (__("<strong>ERROR</strong>: you typed your new password only once. Go back to type it twice."));
		$updatepassword = "";
	} else {
		if ($_POST["pass2"] == "")
			die (__("<strong>ERROR</strong>: you typed your new password only once. Go back to type it twice."));
		if ($_POST["pass1"] != $_POST["pass2"])
			die (__("<strong>ERROR</strong>: you typed two different passwords. Go back to correct that."));
		$newuser_pass = $_POST["pass1"];
		$updatepassword = "user_pass=MD5('$newuser_pass'), ";
		setcookie('wordpresspass_'.$cookiehash, " ", time() - 31536000, COOKIEPATH);
		setcookie('wordpresspass_'.$cookiehash, md5(md5($newuser_pass)), time() + 31536000, COOKIEPATH);
	}

	$newuser_firstname=$_POST['newuser_firstname'];
	$newuser_lastname=$_POST['newuser_lastname'];
	$newuser_nickname=$_POST['newuser_nickname'];
    $newuser_nicename=sanitize_title($newuser_nickname);
	$newuser_icq=$_POST['newuser_icq'];
	$newuser_aim=$_POST['newuser_aim'];
	$newuser_msn=$_POST['newuser_msn'];
	$newuser_yim=$_POST['newuser_yim'];
	$newuser_email=$_POST['newuser_email'];
	$newuser_url=$_POST['newuser_url'];
	$newuser_url = preg_match('/^(https?|ftps?|mailto|news|gopher):/is', $newuser_url) ? $newuser_url : 'http://' . $newuser_url; 
	$newuser_idmode=$_POST['newuser_idmode'];
	$user_description = $_POST['user_description'];

	$query = "UPDATE $wpdb->users SET user_firstname='$newuser_firstname', $updatepassword user_lastname='$newuser_lastname', user_nickname='$newuser_nickname', user_icq='$newuser_icq', user_email='$newuser_email', user_url='$newuser_url', user_aim='$newuser_aim', user_msn='$newuser_msn', user_yim='$newuser_yim', user_idmode='$newuser_idmode', user_description = '$user_description', user_nicename = '$newuser_nicename' WHERE ID = $user_ID";
	$result = $wpdb->query($query);
	if (!$result) {
		die (__("<strong>ERROR</strong>: couldn't update your profile..."));
	}
	header('Location: profile.php?updated=true');
break;

case 'viewprofile':


	$profiledata = get_userdata($user);
	if ($_COOKIE['wordpressuser_'.$cookiehash] == $profiledata->user_login)
		header ('Location: profile.php');
	
	include_once('admin-header.php');
	?>

<h2><?php _e('View Profile') ?> &#8220;
  <?php
	switch($profiledata->user_idmode) {
		case 'nickname':
			$r = $profiledata->user_nickname;
			break;
		case 'login':
			$r = $profiledata->user_login;
			break;
		case 'firstname':
			$r = $profiledata->user_firstname;
			break;
		case 'lastname':
			$r = $profiledata->user_lastname;
			break;
		case 'namefl':
			$r = $profiledata->user_firstname.' '.$profiledata->user_lastname;
			break;
 		case 'namelf':
			$r = $profiledata->user_lastname.' '.$profiledata->user_firstname;
			break;
	}
	echo $r;
	?>
  &#8221;</h2>
	  
  <div id="profile">
<p> 
  <strong><?php _e('Login') ?></strong> <?php echo $profiledata->user_login ?>
  | <strong><?php _e('User #') ?></strong> <?php echo $profiledata->ID ?> | <strong><?php _e('Level') ?></strong> 
  <?php echo $profiledata->user_level ?> | <strong><?php _e('Posts') ?></strong> 
  <?php
	$posts = get_usernumposts($user);
	echo $posts;
	?>
</p>

<p> <strong><?php _e('First name:') ?></strong> <?php echo $profiledata->user_firstname ?> </p>
  
<p> <strong><?php _e('Last name:') ?></strong> <?php echo $profiledata->user_lastname ?> </p>
  
<p> <strong><?php _e('Nickname:') ?></strong> <?php echo $profiledata->user_nickname ?> </p>
  
<p> <strong><?php _e('E-mail:') ?></strong> <?php echo make_clickable($profiledata->user_email) ?> 
</p>
  
<p> <strong><?php _e('Website:') ?></strong> <?php echo $profiledata->user_url ?> </p>
  
<p> <strong><?php _e('ICQ:') ?></strong> 
  <?php if ($profiledata->user_icq > 0) { echo make_clickable("icq:".$profiledata->user_icq); } ?>
</p>
  
<p> <strong><?php _e('AIM:') ?></strong> <?php echo "<a href='aim:goim?screenname=". str_replace(' ', '+', $profiledata->user_aim) ."&message=Howdy'>$profiledata->user_aim</a>"; ?> 
</p>
  
<p> <strong><?php _e('MSN IM:') ?></strong> <?php echo $profiledata->user_msn ?> </p>
  
<p> <strong><?php _e('Yahoo IM:') ?></strong> <?php echo $profiledata->user_yim ?> </p>
  
</div>

	<?php

break;


case 'IErightclick':


	$bookmarklet_tbpb  = (get_settings('use_trackback')) ? '&trackback=1' : '';
	$bookmarklet_tbpb .= (get_settings('use_pingback'))  ? '&pingback=1'  : '';
	$bookmarklet_height= (get_settings('use_trackback')) ? 590 : 550;

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
	$parent_file = 'users.php';
	include_once('admin-header.php');
	$profiledata=get_userdata($user_ID);

	$bookmarklet_tbpb  = (get_settings('use_trackback')) ? '&trackback=1' : '';
	$bookmarklet_tbpb .= (get_settings('use_pingback'))  ? '&pingback=1'  : '';
	$bookmarklet_height= (get_settings('use_trackback')) ? 480 : 440;

	?>
<ul id="adminmenu2">
	<li><a href="users.php"><?php _e('Authors &amp; Users') ?></a></li>
    <li><a class="current"><?php _e('Your Profile') ?></a></li>
</ul>
<?php if (isset($updated)) { ?>
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

	<style type="text/css" media="screen">
	th { text-align: right; }
	</style>
  <table width="99%"  border="0" cellspacing="2" cellpadding="3">
    <tr>
      <th width="33%" scope="row"><?php _e('Login:') ?></th>
      <td width="73%"><?php echo $profiledata->user_login; ?></td>
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
      <td><input type="text" name="newuser_firstname" id="newuser_firstname" value="<?php echo $profiledata->user_firstname ?>" /></td>
    </tr>
    <tr>
      <th scope="row"><?php _e('Last name:') ?></th>
      <td><input type="text" name="newuser_lastname" id="newuser_lastname2" value="<?php echo $profiledata->user_lastname ?>" /></td>
    </tr>
    <tr>
      <th scope="row"><?php _e('Profile:') ?></th>
      <td><textarea name="user_description" rows="5" id="textarea2" style="width: 99%; "><?php echo $profiledata->user_description ?></textarea></td>
    </tr>
    <tr>
      <th scope="row"><?php _e('Nickname:') ?></th>
      <td><input type="text" name="newuser_nickname" id="newuser_nickname2" value="<?php echo $profiledata->user_nickname ?>" /></td>
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
      <td><input type="text" name="newuser_icq" id="newuser_icq2" value="<?php if ($profiledata->user_icq > 0) { echo $profiledata->user_icq; } ?>" /></td>
    </tr>
    <tr>
      <th scope="row"><?php _e('AIM:') ?></th>
      <td><input type="text" name="newuser_aim" id="newuser_aim2" value="<?php echo $profiledata->user_aim ?>" /></td>
    </tr>
    <tr>
      <th scope="row"><?php _e('MSN IM:') ?> </th>
      <td><input type="text" name="newuser_msn" id="newuser_msn2" value="<?php echo $profiledata->user_msn ?>" /></td>
    </tr>
    <tr>
      <th scope="row"><?php _e('Yahoo IM:') ?> </th>
      <td>        <input type="text" name="newuser_yim" id="newuser_yim2" value="<?php echo $profiledata->user_yim ?>" />      </td>
    </tr>
    <tr>
      <th scope="row"><?php _e('Identity on blog:') ?> </th>
      <td><select name="newuser_idmode">
        <option value="nickname"<?php
	if ($profiledata->user_idmode == 'nickname')
	echo ' selected="selected"'; ?>><?php echo $profiledata->user_nickname ?></option>
        <option value="login"<?php
	if ($profiledata->user_idmode=="login")
	echo ' selected="selected"'; ?>><?php echo $profiledata->user_login ?></option>
        <option value="firstname"<?php
	if ($profiledata->user_idmode=="firstname")
	echo ' selected="selected"'; ?>><?php echo $profiledata->user_firstname ?></option>
        <option value="lastname"<?php
	if ($profiledata->user_idmode=="lastname")
	echo ' selected="selected"'; ?>><?php echo $profiledata->user_lastname ?></option>
        <option value="namefl"<?php
	if ($profiledata->user_idmode=="namefl")
	echo ' selected="selected"'; ?>><?php echo $profiledata->user_firstname." ".$profiledata->user_lastname ?></option>
        <option value="namelf"<?php
	if ($profiledata->user_idmode=="namelf")
	echo ' selected="selected"'; ?>><?php echo $profiledata->user_lastname." ".$profiledata->user_firstname ?></option>
      </select>        </td>
    </tr>
    <tr>
      <th scope="row"><?php _e('New <strong>Password</strong> (Leave blank to stay the same.)') ?></th>
      <td><input type="password" name="pass1" size="16" value="" />
      	<br />
        <input type="password" name="pass2" size="16" value="" /></td>
    </tr>
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
    <?php _e('Add the <a href="#" onClick="addPanel()">WordPress Sidebar</a>!') ?> 
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
