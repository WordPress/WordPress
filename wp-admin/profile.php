<?php $title = "Profile";
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
	$HTTP_GET_VARS    = add_magic_quotes($HTTP_GET_VARS);
	$HTTP_POST_VARS   = add_magic_quotes($HTTP_POST_VARS);
	$HTTP_COOKIE_VARS = add_magic_quotes($HTTP_COOKIE_VARS);
}

$wpvarstoreset = array('action','standalone','redirect','profile','user');
for ($i=0; $i<count($wpvarstoreset); $i += 1) {
	$wpvar = $wpvarstoreset[$i];
	if (!isset($$wpvar)) {
		if (empty($HTTP_POST_VARS["$wpvar"])) {
			if (empty($HTTP_GET_VARS["$wpvar"])) {
				$$wpvar = '';
			} else {
				$$wpvar = $HTTP_GET_VARS["$wpvar"];
			}
		} else {
			$$wpvar = $HTTP_POST_VARS["$wpvar"];
		}
	}
}

require_once('../wp-config.php');
require_once('auth.php');
switch($action) {

case 'update':

	get_currentuserinfo();

	/* checking the nickname has been typed */
	if (empty($HTTP_POST_VARS["newuser_nickname"])) {
		die ("<strong>ERROR</strong>: please enter your nickname (can be the same as your login)");
		return false;
	}

	/* if the ICQ UIN has been entered, check to see if it has only numbers */
	if (!empty($HTTP_POST_VARS["newuser_icq"])) {
		if ((ereg("^[0-9]+$",$HTTP_POST_VARS["newuser_icq"]))==false) {
			die ("<strong>ERROR</strong>: your ICQ UIN can only be a number, no letters allowed");
			return false;
		}
	}

	/* checking e-mail address */
	if (empty($HTTP_POST_VARS["newuser_email"])) {
		die ("<strong>ERROR</strong>: please type your e-mail address");
		return false;
	} else if (!is_email($HTTP_POST_VARS["newuser_email"])) {
		die ("<strong>ERROR</strong>: the email address isn't correct");
		return false;
	}

	if ($HTTP_POST_VARS["pass1"] == "") {
		if ($HTTP_POST_VARS["pass2"] != "")
			die ("<strong>ERROR</strong>: you typed your new password only once. Go back to type it twice.");
		$updatepassword = "";
	} else {
		if ($HTTP_POST_VARS["pass2"] == "")
			die ("<strong>ERROR</strong>: you typed your new password only once. Go back to type it twice.");
		if ($HTTP_POST_VARS["pass1"] != $HTTP_POST_VARS["pass2"])
			die ("<strong>ERROR</strong>: you typed two different passwords. Go back to correct that.");
		$newuser_pass = $HTTP_POST_VARS["pass1"];
		$updatepassword = "user_pass='$newuser_pass', ";
		setcookie("wordpresspass_".$cookiehash,md5($newuser_pass),time()+31536000);
	}

	$newuser_firstname=addslashes(stripslashes($HTTP_POST_VARS['newuser_firstname']));
	$newuser_lastname=addslashes(stripslashes($HTTP_POST_VARS['newuser_lastname']));
	$newuser_nickname=addslashes(stripslashes($HTTP_POST_VARS['newuser_nickname']));
	$newuser_icq=addslashes(stripslashes($HTTP_POST_VARS['newuser_icq']));
	$newuser_aim=addslashes(stripslashes($HTTP_POST_VARS['newuser_aim']));
	$newuser_msn=addslashes(stripslashes($HTTP_POST_VARS['newuser_msn']));
	$newuser_yim=addslashes(stripslashes($HTTP_POST_VARS['newuser_yim']));
	$newuser_email=addslashes(stripslashes($HTTP_POST_VARS['newuser_email']));
	$newuser_url=addslashes(stripslashes($HTTP_POST_VARS['newuser_url']));
	$newuser_idmode=addslashes(stripslashes($HTTP_POST_VARS['newuser_idmode']));
	$user_description = addslashes(stripslashes($HTTP_POST_VARS['user_description']));

	$query = "UPDATE $tableusers SET user_firstname='$newuser_firstname', $updatepassword user_lastname='$newuser_lastname', user_nickname='$newuser_nickname', user_icq='$newuser_icq', user_email='$newuser_email', user_url='$newuser_url', user_aim='$newuser_aim', user_msn='$newuser_msn', user_yim='$newuser_yim', user_idmode='$newuser_idmode', user_description = '$user_description' WHERE ID = $user_ID";
	$result = $wpdb->query($query);
	if (!$result) {
		die ("<strong>ERROR</strong>: couldn't update your profile... please contact the <a href=\"mailto:$admin_email\">webmaster</a> !<br /><br />$query<br /><br />");
	}
	header('Location: profile.php?updated=true');
break;

case 'viewprofile':


	$profiledata = get_userdata($user);
	if ($HTTP_COOKIE_VARS['wordpressuser_'.$cookiehash] == $profiledata->user_login)
		header ('Location: profile.php');
	
	include_once('admin-header.php');
	?>

<h2>View Profile &#8220;
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
  <strong>Login</strong> <?php echo $profiledata->user_login ?>
  | <strong>User #</strong> <?php echo $profiledata->ID ?> | <strong>Level</strong> 
  <?php echo $profiledata->user_level ?> | <strong>Posts</strong> 
  <?php
	$posts = get_usernumposts($user);
	echo $posts;
	?>
</p>

<p> <strong>First:</strong> <?php echo $profiledata->user_firstname ?> </p>
  
<p> <strong>Last:</strong> <?php echo $profiledata->user_lastname ?> </p>
  
<p> <strong>Nickname:</strong> <?php echo $profiledata->user_nickname ?> </p>
  
<p> <strong>Email:</strong> <?php echo make_clickable($profiledata->user_email) ?> 
</p>
  
<p> <strong>URL:</strong> <?php echo $profiledata->user_url ?> </p>
  
<p> <strong>ICQ:</strong> 
  <?php if ($profiledata->user_icq > 0) { echo make_clickable("icq:".$profiledata->user_icq); } ?>
</p>
  
<p> <strong>AIM:</strong> <?php echo "<a href='aim:goim?screenname=". str_replace(' ', '+', $profiledata->user_aim) ."&message=Howdy'>$profiledata->user_aim</a>"; ?> 
</p>
  
<p> <strong>MSN IM:</strong> <?php echo $profiledata->user_msn ?> </p>
  
<p> <strong>Yahoo IM:</strong> <?php echo $profiledata->user_yim ?> </p>
  
</div>

	<?php

break;


case 'IErightclick':


	$bookmarklet_tbpb  = ($use_trackback) ? '&trackback=1' : '';
	$bookmarklet_tbpb .= ($use_pingback)  ? '&pingback=1'  : '';
	$bookmarklet_height= ($use_trackback) ? 590 : 550;

	?>

	<div class="menutop">&nbsp;IE one-click bookmarklet</div>

	<table width="100%" cellpadding="20">
	<tr><td>

	<p>To have a one-click bookmarklet, just copy and paste this<br />into a new text file:</p>
	<?php
	$regedit = "REGEDIT4\r\n[HKEY_CURRENT_USER\Software\Microsoft\Internet Explorer\MenuExt\Post To &WP : ".$blogname."]\r\n@=\"javascript:doc=external.menuArguments.document;Q=doc.selection.createRange().text;void(btw=window.open('".$siteurl."/wp-admin/bookmarklet.php?text='+escape(Q)+'".$bookmarklet_tbpb."&popupurl='+escape(doc.location.href)+'&popuptitle='+escape(doc.title),'bookmarklet','scrollbars=no,width=480,height=".$bookmarklet_height.",left=100,top=150,status=yes'));btw.focus();\"\r\n\"contexts\"=hex:31\"";
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

	include_once('admin-header.php');
	$profiledata=get_userdata($user_ID);

	$bookmarklet_tbpb  = ($use_trackback) ? '&trackback=1' : '';
	$bookmarklet_tbpb .= ($use_pingback)  ? '&pingback=1'  : '';
	$bookmarklet_height= ($use_trackback) ? 480 : 440;

	?>
<?php if ($updated) { ?>
<div class="wrap">
<p><strong>Profile updated.</strong></p>
</div>
<?php } ?>
<div class="wrap">
<form name="profile" id="profile" action="profile.php" method="post">
	<h2>Edit Your Profile</h2>
  <p>
    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="checkuser_id" value="<?php echo $user_ID ?>" />
  </p>
  <p><strong>User ID:</strong> <?php echo $profiledata->ID ?> | <strong>Level:</strong> 
    <?php echo $profiledata->user_level ?> | <strong>Posts:</strong> 
    <?php
	$posts = get_usernumposts($user_ID);
	echo $posts;
	?>
    | <strong>Login:</strong> <?php echo $profiledata->user_login ?></p>
	<style type="text/css" media="screen">
	th { text-align: right; }
	</style>
  <table width="99%"  border="0" cellspacing="2" cellpadding="3">
    <tr>
      <th width="15%" scope="row">First:</th>
      <td><input type="text" name="newuser_firstname" id="newuser_firstname" value="<?php echo $profiledata->user_firstname ?>" /></td>
    </tr>
    <tr>
      <th scope="row">Last:</th>
      <td><input type="text" name="newuser_lastname" id="newuser_lastname2" value="<?php echo $profiledata->user_lastname ?>" /></td>
    </tr>
    <tr>
      <th scope="row">Description:</th>
      <td><textarea name="user_description" rows="5" id="textarea2" style="width: 99%; "><?php echo $profiledata->user_description ?></textarea></td>
    </tr>
    <tr>
      <th scope="row">Nickname:</th>
      <td><input type="text" name="newuser_nickname" id="newuser_nickname2" value="<?php echo $profiledata->user_nickname ?>" /></td>
    </tr>
    <tr>
      <th scope="row">Email:</th>
      <td><input type="text" name="newuser_email" id="newuser_email2" value="<?php echo $profiledata->user_email ?>" /></td>
    </tr>
    <tr>
      <th scope="row">URI:</th>
      <td><input type="text" name="newuser_url" id="newuser_url2" value="<?php echo $profiledata->user_url ?>" /></td>
    </tr>
    <tr>
      <th scope="row">ICQ:</th>
      <td><input type="text" name="newuser_icq" id="newuser_icq2" value="<?php if ($profiledata->user_icq > 0) { echo $profiledata->user_icq; } ?>" /></td>
    </tr>
    <tr>
      <th scope="row">AIM:</th>
      <td><input type="text" name="newuser_aim" id="newuser_aim2" value="<?php echo $profiledata->user_aim ?>" /></td>
    </tr>
    <tr>
      <th scope="row">MSN IM: </th>
      <td><input type="text" name="newuser_msn" id="newuser_msn2" value="<?php echo $profiledata->user_msn ?>" /></td>
    </tr>
    <tr>
      <th scope="row">Yahoo IM: </th>
      <td>        <input type="text" name="newuser_yim" id="newuser_yim2" value="<?php echo $profiledata->user_yim ?>" />      </td>
    </tr>
    <tr>
      <th scope="row">Identity on blog: </th>
      <td><select name="newuser_idmode">
        <option value="nickname"<?php
	if ($profiledata->user_idmode == 'nickname')
	echo " selected"; ?>><?php echo $profiledata->user_nickname ?></option>
        <option value="login"<?php
	if ($profiledata->user_idmode=="login")
	echo " selected"; ?>><?php echo $profiledata->user_login ?></option>
        <option value="firstname"<?php
	if ($profiledata->user_idmode=="firstname")
	echo " selected"; ?>><?php echo $profiledata->user_firstname ?></option>
        <option value="lastname"<?php
	if ($profiledata->user_idmode=="lastname")
	echo " selected"; ?>><?php echo $profiledata->user_lastname ?></option>
        <option value="namefl"<?php
	if ($profiledata->user_idmode=="namefl")
	echo " selected"; ?>><?php echo $profiledata->user_firstname." ".$profiledata->user_lastname ?></option>
        <option value="namelf"<?php
	if ($profiledata->user_idmode=="namelf")
	echo " selected"; ?>><?php echo $profiledata->user_lastname." ".$profiledata->user_firstname ?></option>
      </select>        </td>
    </tr>
    <tr>
      <th scope="row">New <strong>Password</strong> (Leave blank to stay the same.)</th>
      <td><input type="password" name="pass1" size="16" value="" />
        <input type="password" name="pass2" size="16" value="" /></td>
    </tr>
  </table>
  <p style=" text-align: center;">
    <input class="search" type="submit" value="Update" name="submit" /></p>
	</div>
  </form>
</div>
<?php if ($is_gecko) { ?>
<div class="wrap">
    <script language="JavaScript" type="text/javascript">
function addPanel()
        {
          if ((typeof window.sidebar == "object") && (typeof window.sidebar.addPanel == "function"))
            window.sidebar.addPanel("WordPress Post: <?php echo $blogname ?>","<?php echo $siteurl ?>/wp-admin/sidebar.php","");
          else
            alert('No Sidebar found!  You must use Mozilla 0.9.4 or later!');
        }
</script>
    <strong>SideBar</strong><br />
    Add the <a href="#" onclick="addPanel()">WordPress Sidebar</a>! 
    <?php } elseif (($is_winIE) || ($is_macIE)) { ?>
    <strong>SideBar</strong><br />
    Add this link to your favorites:<br />
    <a href="javascript:Q='';if(top.frames.length==0)Q=document.selection.createRange().text;void(_search=open('<?php echo $siteurl ?>/wp-admin/sidebar.php?text='+escape(Q)+'&popupurl='+escape(location.href)+'&popuptitle='+escape(document.title),'_search'))">WordPress 
    Sidebar</a>. 
    
</div>
<?php } ?>
	<?php

break;
}

/* </Profile | My Profile> */
include('admin-footer.php') ?>