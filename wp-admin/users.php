<?php
$title = 'Users';
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
	require_once('admin-header.php');

	if (empty($HTTP_GET_VARS['prom'])) {
		header('Location: users.php');
	}

	$id = $HTTP_GET_VARS['id'];
	$prom = $HTTP_GET_VARS['prom'];

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

	header('Location: users.php');

break;

case 'delete':

	$standalone = 1;
	require_once('admin-header.php');

	$id = $HTTP_GET_VARS['id'];

	if (!$id) {
		header('Location: users.php');
	}

	$user_data = get_userdata($id);
	$usertodelete_level = $user_data->user_level;

	if ($user_level <= $usertodelete_level)
		die('Can&#8217;t delete a user whose level is higher than yours.');

	$sql = "DELETE FROM $tableusers WHERE ID = $id";
	$result = $wpdb->query($sql) or die("Couldn&#8217;t delete user #$id.");

	$sql = "DELETE FROM $tableposts WHERE post_author = $id";
	$result = $wpdb->query($sql) or die("Couldn&#8217;t delete user #$id&#8217;s posts.");

	header('Location: users.php');

break;

default:
	
	$standalone = 0;
	include ('admin-header.php');
	?>
<div class="wrap">
  <h2>Authors</h2>
  <table cellpadding="3" cellspacing="3" width="100%">
	<tr>
	<th>ID</th>
	<th>Nickname</th>
	<th>Name</th>
	<th>E-mail</th>
	<th>URL</th>
	<th>Level</th>
	<th>Posts</th>
	</tr>
	<?php
	$users = $wpdb->get_results("SELECT ID FROM $tableusers WHERE user_level > 0 ORDER BY ID");
	foreach ($users as $user) {
		$user_data = get_userdata($user->ID);
		$email = $user_data->user_email;
		$url = $user_data->user_url;
		$short_url = str_replace('http://', '', stripslashes($url));
		$short_url = str_replace('www.', '', $short_url);
		if ('/' == substr($short_url, -1))
			$short_url = substr($short_url, 0, -1);
		if (strlen($short_url) > 35)
		$short_url =  substr($short_url, 0, 32).'...';
		$style = ('class="alternate"' == $style) ? '' : 'class="alternate"';
		$numposts = $wpdb->get_var("SELECT COUNT(*) FROM $tableposts WHERE post_author = $user->ID and post_status = 'publish'");
		if (0 < $numposts) $numposts = "<a href='edit.php?author=$user_data->ID' title='View posts'>$numposts</a>";
		echo "
<tr $style>
	<td align='center'>$user_data->ID</td>
	<td><strong>$user_data->user_nickname</strong></td>
	<td>$user_data->user_firstname $user_data->user_lastname</td>
	<td><a href='mailto:$email' title='e-mail: $email'>$email</a></td>
	<td><a href='$url' title='website: $url'>$short_url</a></td>
	<td align='center'>";
	if (($user_level >= 2) and ($user_level > $user_data->user_level) and ($user_data->user_level > 0))
		echo " <a href=\"users.php?action=promote&id=".$user_data->ID."&prom=down\">-</a> ";
	echo $user_data->user_level;
	if (($user_level >= 2) and ($user_level > ($user_data->user_level + 1)))
		echo " <a href=\"users.php?action=promote&id=".$user_data->ID."&prom=up\">+</a> ";
	echo "<td align='right'>$numposts</td>";
	echo '</tr>';
	}
	
	?>
	
  </table>
</div>

<?php
	$users = $wpdb->get_results("SELECT * FROM $tableusers WHERE user_level = 0 ORDER BY ID");
	if ($users) {
?>
<div class="wrap">
	<h2>Users</h2>
	<table cellpadding="3" cellspacing="3" width="100%">
	<tr>
		<th>ID</th>
		<th>Nickname</th>
		<th>Name</th>
		<th>E-mail</th>
		<th>URL</th>
		<th>Level</th>
	</tr>
	<?php
	foreach ($users as $user) {
		$user_data = get_userdata($user->ID);
		$email = $user_data->user_email;
		$url = $user_data->user_url;
		$short_url = str_replace('http://', '', stripslashes($url));
		$short_url = str_replace('www.', '', $short_url);
		if ('/' == substr($short_url, -1))
			$short_url = substr($short_url, 0, -1);
		if (strlen($short_url) > 35)
		$short_url =  substr($short_url, 0, 32).'...';
		$style = ('class="alternate"' == $style) ? '' : 'class="alternate"';
echo "\n<tr $style>
<td align='center'>$user_data->ID</td>
<td><strong>$user_data->user_nickname</td>
<td>$user_data->user_firstname $user_data->user_lastname</td>
<td><a href='mailto:$email' title='e-mail: $email'>$email</a></td>
<td><a href='$url' title='website: $url'>$short_url</a></td>
<td align='center'>";
		if ($user_level >= 3)
			echo " <a href=\"users.php?action=delete&id=".$user_data->ID."\" style=\"color:red;font-weight:bold;\">X</a> ";
		echo $user_data->user_level;
		if ($user_level >= 2)
			echo " <a href=\"users.php?action=promote&id=".$user_data->ID."&prom=up\">+</a> ";	
		echo "</td>\n</tr>\n";
	}
	?>
	
	</table>
	  <p>To delete a user, bring his level to zero, then click on the red X.<br />
    <strong>Warning:</strong> deleting a user also deletes all posts made by this user. 
  </p>
</div>

	<?php 
	} ?>
<div class="wrap">
<h2>Add User</h2>
<p>Users can <a href="<?php echo $siteurl ?>/wp-register.php">register themselves</a> or you can manually create users here.</p>
</div>
	<?php

break;
}
	
/* </Team> */
include('admin-footer.php');
?>