<?php
$title = 'Team management';
/* <Team> */
	
$b2varstoreset = array('action','standalone','redirect','profile');
for ($i=0; $i<count($b2varstoreset); $i += 1) {
	$b2var = $b2varstoreset[$i];
	if (!isset($$b2var)) {
		if (empty($HTTP_POST_VARS["$b2var"])) {
			if (empty($HTTP_GET_VARS["$b2var"])) {
				$$b2var = '';
			} else {
				$$b2var = $HTTP_GET_VARS["$b2var"];
			}
		} else {
			$$b2var = $HTTP_POST_VARS["$b2var"];
		}
	}
}

switch ($action) {
	
case 'promote':

	$standalone = 1;
	require_once('b2header.php');

	if (empty($HTTP_GET_VARS["prom"])) {
		header('Location: b2team.php');
	}

	$id = $HTTP_GET_VARS["id"];
	$prom = $HTTP_GET_VARS["prom"];

	$user_data = get_userdata($id);
	$usertopromote_level = $user_data->user_level;

	if ($user_level <= $usertopromote_level) {
		die('Can&#8217;t change the level of a user whose level is higher than yours.');
	}

	if ('up' == $prom) {
		$sql="UPDATE $tableusers SET user_level=user_level+1 WHERE ID = $id";
	} elseif ('down' == $prom) {
		$sql="UPDATE $tableusers SET user_level=user_level-1 WHERE ID = $id";
	}
	$result = $wpdb->query($sql);

	header('Location: b2team.php');

break;

case 'delete':

	$standalone = 1;
	require_once('b2header.php');

	$id = $HTTP_GET_VARS["id"];

	if (!$id) {
		header('Location: b2team.php');
	}

	$user_data = get_userdata($id);
	$usertodelete_level = $user_data->user_level;

	if ($user_level <= $usertodelete_level)
		die('Can&#8217;t delete a user whose level is higher than yours.');

	$sql = "DELETE FROM $tableusers WHERE ID = $id";
	$result = $wpdb->query($sql) or die("Couldn&#8217;t delete user #$id.");

	$sql = "DELETE FROM $tableposts WHERE post_author = $id";
	$result = $wpdb->query($sql) or die("Couldn&#8217;t delete user #$id&#8217;s posts.");

	header('Location: b2team.php');

break;

default:
	
	$standalone = 0;
	include ('b2header.php');
	?>

<div class="wrap"><p>Click on a user&#8217;s login name to see his complete profile.<br />
	To edit your profile, click on your login name.</p>
</div>

<div class="wrap">
	<h3>Active users</h3>
	<table cellpadding="5" cellspacing="0">
	<tr>
	<th>ID</th>
	<th>Nickname</th>
	<th>Name</th>
	<th>E-mail</th>
	<th>URL</th>
	<th>Level</th>
	<?php if ($user_level > 3) { ?>
	<th>Login</th>
	<?php } ?>
	</tr>
	<?php
	$users = $wpdb->get_results("SELECT ID FROM $tableusers WHERE user_level>0 ORDER BY ID");
	foreach ($users as $user) {
		$user_data = get_userdata($user->ID);
		echo "<tr>\n<!--".$user_data->user_login."-->\n";
		$email = $user_data->user_email;
		$url = $user_data->user_url;
		$bg1 = ($user_data->user_login == $user_login) ? "bgcolor=\"#ffffff\"" : "bgcolor=\"#dddddd\"";
		$bg2 = ($user_data->user_login == $user_login) ? "bgcolor=\"#ffffff\"" : "bgcolor=\"#eeeeee\"";
		echo "<td $bg1>".$user_data->ID."</td>\n";
		echo "<td $bg2><b><a href=\"javascript:profile(".$user_data->ID.")\">".$user_data->user_nickname."</a></b></td>\n";
		echo "<td $bg1>".$user_data->user_firstname."&nbsp;".$user_data->user_lastname."</td>\n";
		echo "<td $bg2>&nbsp;<a href=\"mailto:$email\" title=\"e-mail: $email\"><img src='../b2-img/email.gif' border=\"0\" alt=\"e-mail: $email\" /></a>&nbsp;</td>";
		echo "<td $bg1>&nbsp;";
		if (($user_data->user_url != "http://") and ($user_data->user_url != ""))
			echo "<a href=\"$url\" target=\"_blank\" title=\"website: $url\"><img src=\"../b2-img/url.gif\" border=\"0\" alt=\"website: $url\" /></a>&nbsp;";
		echo "</td>\n";
		echo "<td $bg2>".$user_data->user_level;
		if (($user_level >= 2) and ($user_level > ($user_data->user_level + 1)))
			echo " <a href=\"b2team.php?action=promote&id=".$user_data->ID."&prom=up\">+</a> ";
		if (($user_level >= 2) and ($user_level > $user_data->user_level) and ($user_data->user_level > 0))
			echo " <a href=\"b2team.php?action=promote&id=".$user_data->ID."&prom=down\">-</a> ";
		echo "</td>\n";
		if ($user_level > 3) {
			echo "<td $bg1>".$user_data->user_login."</td>\n";
		}
		echo "</tr>\n";
	}
	
	?>
	
	</table>

</div>

<?php
	$users = $wpdb->get_results("SELECT * FROM $tableusers WHERE user_level=0 ORDER BY ID");
	if ($users) {
?>
<div class="wrap">
	<h3>Inactive users (level 0)</h3>
	<table cellpadding="5" cellspacing="0">
	<tr>
	<td>ID</td>
	<td>Nickname</td>
	<td>Name</td>
	<td>E-mail</td>
	<td>URL</td>
	<td>Level</td>
	<?php if ($user_level > 3) { ?>
	<td>Login</td>
	<?php } ?>
	</tr>
	<?php
	foreach ($users as $user) {
		$user_data = get_userdata($user->ID);
		echo "<tr>\n<!--".$user_data->user_login."-->\n";
		$email = $user_data->user_email;
		$url = $user_data->user_url;
		$bg1 = ($user_data->user_login == $user_login) ? "style=\"background-image: url('../b2-img/b2button.gif');\"" : "bgcolor=\"#dddddd\"";
		$bg2 = ($user_data->user_login == $user_login) ? "style=\"background-image: url('../b2-img/b2button.gif');\"" : "bgcolor=\"#eeeeee\"";
		echo "<td $bg1>".$user_data->ID."</td>\n";
		echo "<td $bg2><b><a href=\"javascript:profile(".$user_data->ID.")\">".$user_data->user_nickname."</a></b></td>\n";
		echo "<td $bg1>".$user_data->user_firstname."&nbsp;".$user_data->user_lastname."</td>\n";
		echo "<td $bg1>&nbsp;<a href=\"mailto:".antispambot($email)."\" title=\"e-mail: ".antispambot($email)."\"><img src=\"../b2-img/email.gif\" border=\"0\" alt=\"e-mail: ".antispambot($email)."\" /></a>&nbsp;</td>";
		echo "<td $bg2>&nbsp;";
		if (($user_data->user_url != "http://") and ($user_data->user_url != ""))
			echo "<a href=\"$url\" target=\"_blank\" title=\"website: $url\"><img src=\"../b2-img/url.gif\" border=\"0\" alt=\"website: $url\" /></a>&nbsp;";
		echo "</td>\n";
		echo "<td $bg1>".$user_data->user_level;
		if ($user_level >= 2)
			echo " <a href=\"b2team.php?action=promote&id=".$user_data->ID."&prom=up\">+</a> ";
		if ($user_level >= 3)
			echo " <a href=\"b2team.php?action=delete&id=".$user_data->ID."\" style=\"color:red;font-weight:bold;\">X</a> ";
		echo "</td>\n";
		if ($user_level > 3) {
			echo "<td $bg2>".$user_data->user_login."</td>\n";
		}
		echo "</tr>\n";
	}
	
	?>
	
	</table>
</div>

	<?php 
	}
	if ($user_level >= 3) { ?>
<div class="wrap"> 
  <p>To delete a user, bring his level to zero, then click on the red X.<br />
    <strong>Warning:</strong> deleting a user also deletes all posts made by this user. 
  </p>
</div>
	<?php
}

break;
}
	
/* </Team> */
include('b2footer.php');
?>