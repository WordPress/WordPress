<?php
$title = 'Categories';
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

case 'addcat':

	$standalone = 1;
	require_once('b2header.php');

	if ($user_level < 3)
		die ('Cheatin&#8217; uh?');
	
	$cat_name=addslashes($HTTP_POST_VARS["cat_name"]);

	$query = "INSERT INTO $tablecategories (cat_ID,cat_name) VALUES ('0', '$cat_name')";
	$result = mysql_query($query) or die("Couldn't add category <b>$cat_name</b>");
	
	header('Location: b2categories.php');

break;

case 'Delete':

	$standalone = 1;
	require_once('b2header.php');

	$cat_ID = intval($HTTP_POST_VARS["cat_ID"]);
	$cat_name = get_catname($cat_ID);
	$cat_name = addslashes($cat_name);

	if (1 == $cat_ID)
		die("Can't delete the <strong>$cat_name</strong> category: this is the default one");

	if ($user_level < 3)
		die ('Cheatin&#8217; uh?');
	
	$query = "DELETE FROM $tablecategories WHERE cat_ID = $cat_ID";
	$result = mysql_query($query) or die("Couldn't delete category <b>$cat_name</b>".mysql_error());
	
	$query = "UPDATE $tableposts SET post_category='1' WHERE post_category='$cat_ID'";
	$result = mysql_query($query) or die("Couldn't reset category on posts where category was <b>$cat_name</b>");

	header('Location: b2categories.php');

break;

case 'Rename':

	require_once ('b2header.php');
	$cat_name = get_catname($HTTP_POST_VARS["cat_ID"]);
	$cat_name = addslashes($cat_name);
	?>

<div class="wrap">
	<p><strong>Old</strong> name: <?php echo $cat_name ?></p>
	<p>
	<form name="renamecat" action="b2categories.php" method="post">
		<strong>New</strong> name:<br />
		<input type="hidden" name="action" value="editedcat" />
		<input type="hidden" name="cat_ID" value="<?php echo $HTTP_POST_VARS["cat_ID"] ?>" />
		<input type="text" name="cat_name" value="<?php echo $cat_name ?>" /><br />
		<input type="submit" name="submit" value="Edit it !" class="search" />
	</form>
</div>

	<?php

break;

case 'editedcat':

	$standalone = 1;
	require_once('b2header.php');

	if ($user_level < 3)
		die ('Cheatin&#8217; uh?');
	
	$cat_name = addslashes($HTTP_POST_VARS["cat_name"]);
	$cat_ID = addslashes($HTTP_POST_VARS["cat_ID"]);

	$query = "UPDATE $tablecategories SET cat_name='$cat_name' WHERE cat_ID = $cat_ID";
	$result = mysql_query($query) or die("Couldn't edit category <b>$cat_name</b>: ".mysql_error());
	
	header('Location: b2categories.php');

break;

default:

	$standalone = 0;
	require_once ('b2header.php');
	if ($user_level < 3) {
		die("You have no right to edit the categories for this blog.<br />Ask for a promotion to your <a href='mailto:$admin_email'>blog admin</a>. :)");
	}
	?>

<div class="wrap">
	<form name="cats" method="post">
	<h3>Edit a category:</h3>
	<p>
	<?php
	$query = "SELECT * FROM $tablecategories ORDER BY cat_ID";
	$result = mysql_query($query);
	echo "<select name='cat_ID'>\n";
	while($row = mysql_fetch_object($result)) {
		echo "\t<option value='$row->cat_ID'";
		if ($row->cat_ID == $cat)
			echo ' selected="selected"';
		echo ">".$row->cat_ID.": ".$row->cat_name."</option>\n";
	}
	echo "</select>\n";
	?><br />
	<input type="submit" name="action" value="Delete" class="search" />
	<input type="submit" name="action" value="Rename" class="search" />
	</form>
	</p>

	<h3>Add a category:</h3>
		<form name="addcat" action="b2categories.php" method="post">
			<input type="hidden" name="action" value="addcat" />
			<input type="text" name="cat_name" /><br />
			<input type="submit" name="submit" value="Add it!" class="search" /></form>
</div>



<div class="wrap"> 
  <p><strong>Note:</strong><br />
    Deleting a category does not delete posts from that category, it will just 
    set them back to the default category <strong><?php echo get_catname(1) ?></strong>. 
  </p>
</div>

	<?php
break;
}

/* </Categories> */
include('b2footer.php');
?>