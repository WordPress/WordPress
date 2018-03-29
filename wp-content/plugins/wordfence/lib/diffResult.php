<?php if (!defined('WORDFENCE_VERSION')) { exit; } ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  dir="ltr" lang="en-US">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel='stylesheet' id='wordfence-main-style-css'  href='<?php echo wfUtils::getBaseURL() . wfUtils::versionedAsset('css/diff.css'); ?>?ver=<?php echo WORDFENCE_VERSION; ?>' type='text/css' media='all' />
<body>
<h1>Wordfence: Viewing File Differences</h1>
<p style="width: 800px; font-size: 16px; font-family: Verdana;">
	The two panels below show a before and after view of a file on your system that has been modified.
	The left panel shows the original file before modification. The right panel shows your version
	of the file that has been modified.
	Use this view to determine if a file has been modified by an attacker or if this is a change
	that you or another trusted person made. 
	If you are happy with the modifications you see here, then you should choose to
	ignore this file the next time Wordfence scans your system.
</p>
<table border="0" style="margin: 0 0 20px 0;" class="summary">
<tr><td>Filename:</td><td><?php echo wp_kses($_GET['file'], array()); ?></td></tr>
<tr><td>File type:</td><td><?php 
	$cType = $_GET['cType'];
	if($cType == 'core'){
		echo "WordPress Core File</td></tr>";
	} else if($cType == 'theme'){
		echo "Theme File</td></tr><tr><td>Theme Name:</td><td>" . wp_kses($_GET['cName'], array()) . "</td></tr><tr><td>Theme Version:</td><td>" . wp_kses($_GET['cVersion'], array()) . "</td></tr>";
	} else if($cType == 'plugin'){
		echo "Plugin File</td></tr><tr><td>Plugin Name:</td><td>" . wp_kses($_GET['cName'], array()) . "</td></tr><tr><td>Plugin Version:</td><td>" . wp_kses($_GET['cVersion'], array()) . "</td></tr>";
	} else {
		echo "Unknown Type</td></tr>";
	}
	?>
</table>

<?php 
	if($diffResult){
		echo $diffResult; 
	} else {
		echo "<br />There are no differences between the original file and the file in the repository.";
	}

?>


<div class="diffFooter">&copy;&nbsp;2011 to <?php echo date('Y'); ?> Wordfence &mdash; Visit <a href="http://wordfence.com/">Wordfence.com</a> for help, security updates and more.</div>
</body>
</html>
