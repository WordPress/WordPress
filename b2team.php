<?php
$title = "Team management";
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
	
case "promote":

	$standalone = 1;
	require_once("./b2header.php");

	if (empty($HTTP_GET_VARS["prom"])) {
		header("Location: b2team.php");
	}

	$id = $HTTP_GET_VARS["id"];
	$prom = $HTTP_GET_VARS["prom"];

	$user_data=get_userdata($id);
	$usertopromote_level=$user_data[13];

	if ($user_level <= $usertopromote_level) {
		die("Can't change the level of an user whose level is higher than yours.");
	}

	if ($prom == "up") {
		$sql="UPDATE $tableusers SET user_level=user_level+1 WHERE ID = $id";
	} elseif ($prom == "down") {
		$sql="UPDATE $tableusers SET user_level=user_level-1 WHERE ID = $id";
	}
	$result=mysql_query($sql) or die("Couldn't change $id's level.");

	header("Location: b2team.php");

break;

case "delete":

	$standalone = 1;
	require_once("./b2header.php");

	$id = $HTTP_GET_VARS["id"];

	if (!$id) {
		header("Location: b2team.php");
	}

	$user_data=get_userdata($id);
	$usertodelete_level=$user_data[13];

	if ($user_level <= $usertodelete_level)
	die("Can't delete an user whose level is higher than yours.");

	$sql="DELETE FROM $tableusers WHERE ID = $id";
	$result=mysql_query($sql) or die("Couldn't delete user #$id.");

	$sql="DELETE FROM $tableposts WHERE post_author = $id";
	$result=mysql_query($sql) or die("Couldn't delete user #$id's posts.");

	header("Location: b2team.php");

break;

default:
	
	$standalone=0;
	include ("./b2header.php");
	?>
<?php echo $blankline.$tabletop ?>
	<table cellspacing="0" cellpadding="5" border="0" width="100%">
	<tr>
	<td>Click on an user's login name to see his/her complete Profile.<br />
	To edit your Profile, click on your login name.</td>
	</tr>
</table>
<?php echo $tablebottom ?>
<br />
<?php echo $tabletop ?>
	<p><b>Active users</b>
	<table cellpadding="5" cellspacing="0">
	<tr>
	<td class="tabletoprow">ID</td>
	<td class="tabletoprow">Nickname</td>
	<td class="tabletoprow">Name</td>
	<td class="tabletoprow">E-mail</td>
	<td class="tabletoprow">URL</td>
	<td class="tabletoprow">Level</td>
	<?php if ($user_level > 3) { ?>
	<td class="tabletoprow">Login</td>
	<?php } ?>
	</tr>
	<?php
	$request = " SELECT * FROM $tableusers WHERE user_level>0 ORDER BY ID";
	$result = mysql_query($request);
	while($row = mysql_fetch_object($result)) {
		$user_data = get_userdata2($row->ID);
		echo "<tr>\n<!--".$user_data["user_login"]."-->\n";
		$email = $user_data["user_email"];
		$url = $user_data["user_url"];
		$bg1 = ($user_data["user_login"] == $user_login) ? "style=\"background-image: url('b2-img/b2button.gif');\"" : "bgcolor=\"#dddddd\"";
		$bg2 = ($user_data["user_login"] == $user_login) ? "style=\"background-image: url('b2-img/b2button.gif');\"" : "bgcolor=\"#eeeeee\"";
		echo "<td $bg1>".$user_data["ID"]."</td>\n";
		echo "<td $bg2><b><a href=\"javascript:profile(".$user_data["ID"].")\">".$user_data["user_nickname"]."</a></b></td>\n";
		echo "<td $bg1>".$user_data["user_firstname"]."&nbsp;".$user_data["user_lastname"]."</td>\n";
		echo "<td $bg2>&nbsp;<a href=\"mailto:$email\" title=\"e-mail: $email\"><img src=\"b2-img/email.gif\" border=\"0\" alt=\"e-mail: $email\" /></a>&nbsp;</td>";
		echo "<td $bg1>&nbsp;";
		if (($user_data["user_url"] != "http://") and ($user_data["user_url"] != ""))
			echo "<a href=\"$url\" target=\"_blank\" title=\"website: $url\"><img src=\"b2-img/url.gif\" border=\"0\" alt=\"website: $url\" /></a>&nbsp;";
		echo "</td>\n";
		echo "<td $bg2>".$user_data["user_level"];
		if (($user_level >= 2) and ($user_level > ($user_data["user_level"] + 1)))
			echo " <a href=\"b2team.php?action=promote&id=".$user_data["ID"]."&prom=up\">+</a> ";
		if (($user_level >= 2) and ($user_level > $user_data["user_level"]) and ($user_data["user_level"] > 0))
			echo " <a href=\"b2team.php?action=promote&id=".$user_data["ID"]."&prom=down\">-</a> ";
		echo "</td>\n";
		if ($user_level > 3) {
			echo "<td $bg1>".$user_data["user_login"]."</td>\n";
		}
		echo "</tr>\n";
	}
	
	?>
	
	</table>
	</p>
<?php echo $tablebottom ?>
<?php
	$request = " SELECT * FROM $tableusers WHERE user_level=0 ORDER BY ID";
	$result = mysql_query($request);
	if (mysql_num_rows($result)) {
?>
<br />
<?php echo $tabletop ?>
	<p><b>Inactive users (level 0)</b>
	<table cellpadding="5" cellspacing="0">
	<tr>
	<td class="tabletoprow">ID</td>
	<td class="tabletoprow">Nickname</td>
	<td class="tabletoprow">Name</td>
	<td class="tabletoprow">E-mail</td>
	<td class="tabletoprow">URL</td>
	<td class="tabletoprow">Level</td>
	<?php if ($user_level > 3) { ?>
	<td class="tabletoprow">Login</td>
	<?php } ?>
	</tr>
	<?php
	while($row = mysql_fetch_object($result)) {
		$user_data = get_userdata2($row->ID);
		echo "<tr>\n<!--".$user_data["user_login"]."-->\n";
		$email = $user_data["user_email"];
		$url = $user_data["user_url"];
		$bg1 = ($user_data["user_login"] == $user_login) ? "style=\"background-image: url('b2-img/b2button.gif');\"" : "bgcolor=\"#dddddd\"";
		$bg2 = ($user_data["user_login"] == $user_login) ? "style=\"background-image: url('b2-img/b2button.gif');\"" : "bgcolor=\"#eeeeee\"";
		echo "<td $bg1>".$user_data["ID"]."</td>\n";
		echo "<td $bg2><b><a href=\"javascript:profile(".$user_data["ID"].")\">".$user_data["user_nickname"]."</a></b></td>\n";
		echo "<td $bg1>".$user_data["user_firstname"]."&nbsp;".$user_data["user_lastname"]."</td>\n";
		echo "<td $bg1>&nbsp;<a href=\"mailto:".antispambot($email)."\" title=\"e-mail: ".antispambot($email)."\"><img src=\"b2-img/email.gif\" border=\"0\" alt=\"e-mail: ".antispambot($email)."\" /></a>&nbsp;</td>";
		echo "<td $bg2>&nbsp;";
		if (($user_data["user_url"] != "http://") and ($user_data["user_url"] != ""))
			echo "<a href=\"$url\" target=\"_blank\" title=\"website: $url\"><img src=\"b2-img/url.gif\" border=\"0\" alt=\"website: $url\" /></a>&nbsp;";
		echo "</td>\n";
		echo "<td $bg1>".$user_data["user_level"];
		if ($user_level >= 2)
			echo " <a href=\"b2team.php?action=promote&id=".$user_data["ID"]."&prom=up\">+</a> ";
		if ($user_level >= 3)
			echo " <a href=\"b2team.php?action=delete&id=".$user_data["ID"]."\" style=\"color:red;font-weight:bold;\">X</a> ";
		echo "</td>\n";
		if ($user_level > 3) {
			echo "<td $bg2>".$user_data["user_login"]."</td>\n";
		}
		echo "</tr>\n";
	}
	
	?>
	
	</table>
	</p>
<?php echo $tablebottom ?>

	<?php 
	}
	if ($user_level >= 3) { ?>
	<br />
<?php echo $tabletop ?>
	To delete an user, bring his/her level to zero, then click on the red cross.<br />
	<b>Warning:</b> deleting an user also deletes all posts made by this user.
<?php echo $tablebottom ?>
	<?php
}

break;
}
	
/* </Team> */
include($b2inc."/b2footer.php") ?>