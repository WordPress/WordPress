<?php
$title = "Template(s) &amp; file editing";
/* <Template> */

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

$b2varstoreset = array('action','standalone','redirect','profile','error','warning','a','file');
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

case "update":

	$standalone=1;
	require_once("./b2header.php");

	if ($user_level < 3) {
		die("You have no right to edit the template for this blog.<br>Ask for a promotion to your <a href=\"mailto:$admin_email\">blog admin</a> :)");
	}

	$newcontent = stripslashes($HTTP_POST_VARS["newcontent"]);
	$file = $HTTP_POST_VARS["file"];
	$f = fopen($file,"w+");
	fwrite($f,$newcontent);
	fclose($f);

	header("Location: b2template.php?file=$file&a=te");
	exit();

break;

default:

	include("./b2header.php");

	if ($user_level <= 3) {
		die("You have no right to edit the template for this blog.<br>Ask for a promotion to your <a href=\"mailto:$admin_email\">blog admin</a> :)");
	}

	if ($file=="") {
		if ($blogfilename != "") {
			$file = $blogfilename;
		} else {
			$file = "b2.php";
		}
	}
	
	if (substr($file,0,2) == "..")
		die ("Sorry, can't edit files that are up one directory or more.");
	
	if (substr($file,1,1) == ":")
		die ("Sorry, can't call files with their real path.");

	if (substr($file,0,1) == "/")
		$file = ".".$file;
	
	if (!is_file($file))
		$error = 1;

	$file = stripslashes($file);

	if ((substr($file,0,2) == "b2") and (substr($file,-4,4) == ".php") and ($file != "b2.php"))
		$warning = " - this is a b2 file, be careful when editing it !";

	if (!$error) {
		$f = fopen($file,"r");
		$content = fread($f,filesize($file));
//		$content = template_simplify($content);
		$content = htmlspecialchars($content);
//		$content = str_replace("</textarea","&lt;/textarea",$content);
	}

	echo $blankline;
	echo $tabletop;
	?>
	<table width="100%" cellpadding="5" cellspacing="0">
	<tr>
	<td>
	<?php
	echo "Listing <b>$file</b>".$warning;
	if ($a == "te")
		echo "<i> [ file edited ! ]</i>";
	
	if (!$error) {
	?>
		<form name="template" action="b2template.php" method="post">
		<textarea cols="80" rows="20" style="width:100%" name="newcontent" tabindex="1"><?php echo $content ?></textarea>
		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="file" value="<?php echo $file ?>" />
		<br />
		<?php
		if (is_writeable($file)) {
			echo "<input type=\"submit\" name=\"submit\" class=\"search\" value=\"update template !\" tabindex=\"2\" />";
		} else {
			echo "<input type=\"button\" name=\"oops\" class=\"search\" value=\"(you cannot update that file/template: must make it writable, e.g. CHMOD 766)\" tabindex=\"2\" />";
		}
		?>
		</form>
	<?php
	} else {
		echo "<p>oops, no such file !</p>";
	}
	echo $tablebottom;
	?>
	</td>
	</table>
	<br />
	<?php	echo $tabletop; ?>
	You can also edit the <a href="b2template.php?file=b2comments.php">comments' template</a> or the <a href="b2template.php?file=b2commentspopup.php">popup comments' template</a>, or edit any other file (provided it's writable by the server, e.g. CHMOD 766).<br />
	<br />
	To edit a file, type its name here:
	<form name="file" action="b2template.php" method="get">
	<input type="text" name="file" />
	<input type="submit" name="submit"  class="search" value="go" />
	</form>
	<br />
	Note: of course, you can also edit the files/templates in your text editor and upload them. This online editor is only meant to be used when you don't have access to a text editor...
	
<?php	echo $tablebottom; ?>
	

	<?php

break;
}

/* </Template> */
include("b2footer.php") ?>