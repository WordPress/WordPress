<?php
$title = "Categories";
/* <Categories> */

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

$b2varstoreset = array('action','standalone','cat');
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

switch($action) {

case "addcat":

	$standalone = 1;
	require_once("./b2header.php");

	if ($user_level < 3)
	die ("Cheatin' uh ?");
	
	$cat_name=addslashes($HTTP_POST_VARS["cat_name"]);

	$query="INSERT INTO $tablecategories (cat_ID,cat_name) VALUES ('0', '$cat_name')";
	$result=mysql_query($query) or die("Couldn't add category <b>$cat_name</b>");
	
	header("Location: b2categories.php");

break;

case "Delete":

	$standalone = 1;
	require_once("./b2header.php");

	$cat_ID = $HTTP_POST_VARS["cat_ID"];
	$cat_name=get_catname($cat_ID);
	$cat_name=addslashes($cat_name);

	if ($cat_ID=="1")
		die("Can't delete the <b>$cat_name</b> category: this is the default one");

	if ($user_level < 3)
	die ("Cheatin' uh ?");
	
	$query="DELETE FROM $tablecategories WHERE cat_ID=\"$cat_ID\"";
	$result=mysql_query($query) or die("Couldn't delete category <b>$cat_name</b>".mysql_error());
	
	$query="UPDATE $tableposts SET post_category='1' WHERE post_category='$cat_ID'";
	$result=mysql_query($query) or die("Couldn't reset category on posts where category was <b>$cat_name</b>");

	header("Location: b2categories.php");

break;

case "Rename":

	require_once ("./b2header.php");
	$cat_name=get_catname($HTTP_POST_VARS["cat_ID"]);
	$cat_name=addslashes($cat_name);
	?>
<?php echo $blankline; ?>
<?php echo $tabletop; ?>
	<p><b>Old</b> name: <?php echo $cat_name ?></p>
	<p>
	<form name="renamecat" action="b2categories.php" method="post">
		<b>New</b> name:<br />
		<input type="hidden" name="action" value="editedcat" />
		<input type="hidden" name="cat_ID" value="<?php echo $HTTP_POST_VARS["cat_ID"] ?>" />
		<input type="text" name="cat_name" value="<?php echo $cat_name ?>" /><br />
		<input type="submit" name="submit" value="Edit it !" class="search" />
	</form>
<?php echo $tablebottom; ?>

	<?php

break;

case "editedcat":

	$standalone = 1;
	require_once("./b2header.php");

	if ($user_level < 3)
	die ("Cheatin' uh ?");
	
	$cat_name=addslashes($HTTP_POST_VARS["cat_name"]);
	$cat_ID=addslashes($HTTP_POST_VARS["cat_ID"]);

	$query="UPDATE $tablecategories SET cat_name='$cat_name' WHERE cat_ID=$cat_ID";
	$result=mysql_query($query) or die("Couldn't edit category <b>$cat_name</b>: ".mysql_error());
	
	header("Location: b2categories.php");

break;

default:

	$standalone=0;
	require_once ("./b2header.php");
	if ($user_level < 3) {
		die("You have no right to edit the categories for this blog.<br>Ask for a promotion to your <a href=\"mailto:$admin_email\">blog admin</a> :)");
	}
	?>

<?php echo $blankline ?>
<?php echo $tabletop ?>
	<table width="" cellpadding="5" cellspacing="0">
	<form></form>
	<tr>
	<td>
	<form name="cats" method="post">
	<b>Edit</b> a category:<br />
	<?php
	$query="SELECT * FROM $tablecategories ORDER BY cat_ID";
	$result=mysql_query($query);
	echo "<select name=\"cat_ID\">\n";
	while($row = mysql_fetch_object($result)) {
		echo "\t<option value=\"".$row->cat_ID."\"";
		if ($row->cat_ID == $cat)
			echo " selected";
		echo ">".$row->cat_ID.": ".$row->cat_name."</option>\n";
	}
	echo "</select>\n";
	?><br />
	<input type="submit" name="action" value="Delete" class="search" />
	<input type="submit" name="action" value="Rename" class="search" />
	</form>
	</p>
	<p>
	<b>Add</b> a category:<br />
		<form name="addcat" action="b2categories.php" method="post">
			<input type="hidden" name="action" value="addcat" />
			<input type="text" name="cat_name" /><br />
			<input type="submit" name="submit" value="Add it !" class="search" /></form></td></tr></table>
<?php echo $tablebottom ?>

<br />

<?php echo $tabletop ?>
	<b>Note:</b><br />
	Deleting a category does not delete posts from that category.<br />It will just set them back to the default category <b><?php echo get_catname(1) ?></b>.
<?php echo $tablebottom ?>

	<?php
break;
}

/* </Categories> */
include($b2inc."/b2footer.php"); ?>