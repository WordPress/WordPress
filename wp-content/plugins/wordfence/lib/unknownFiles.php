<?php if (!defined('WORDFENCE_VERSION')) { exit; } ?>
<?php if(! wfUtils::isAdmin()){ exit(); } ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  dir="ltr" lang="en-US">
<head>
<title>Files found that don't belong to WordPress Core or known Themes and Plugins</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel='stylesheet' id='wordfence-main-style-css'  href='<?php echo wfUtils::getBaseURL() . wfUtils::versionedAsset('css/diff.css'); ?>?ver=<?php echo WORDFENCE_VERSION; ?>' type='text/css' media='all' />
<body>
<h1>Wordfence: Files found that don't belong to WordPress Core or known Themes and Plugins.</h1>
<?php 
$path = ABSPATH;
$fileList = wfConfig::get('lastUnknownFileList');
if($fileList){
?>
<p style="width: 700px; margin-top: 20px;">
	<b>Please note:</b> To use this utility, you must enable scanning of Core, Theme and Plugin files on the Wordfence options page. 
	<?php if(! wfConfig::get('scansEnabled_themes')){ echo '<span style="color: #F00;">Theme scanning is currently disabled.</span> '; } ?>
	<?php if(! wfConfig::get('scansEnabled_plugins')){ echo '<span style="color: #F00;">Plugin scanning is currently disabled.</span> '; } ?>
	<?php if( (!wfConfig::get('scansEnabled_plugins')) || (!wfConfig::get('scansEnabled_themes')) ){ echo 'You can visit the Wordfence "options" page to enable theme or plugin scanning.'; } ?>

	If you don't have core, theme and plugin scanning enabled, then the list below will not be very useful because Wordfence won't recognize known core, theme and plugin files. 
	If you have the option enabled to "Scan files outside your WordPress installation" enabled, then you may find that this list is very long because it will include files in all your directories.
	<br /><br />
	<b>What is in this list:</b>
	When Wordfence does a scan, it separates files on your system into two lists. The first list is files that belong to WordPress Core or a known theme or plugin. The second list is all other files. 
	<br /><br />
	If a <b>file belongs to WordPress Core or a known theme or plugin</b>, we do an integrity check and let you know if it has been modified.
	The integrity check we do on known Core, theme and plugin files is a very reliable way to detect compromised files. It is impossible as far as we know for a hacker to fool this scan
	because we are comparing your files to known originals on our secure scanning servers. If the file is modified, we let you know with a warning or critical alert in the scan results. 
	<br /><br />
	If the file <b>does not belong to WordPress Core or a known theme or plugin</b>, we scan it for security problems. 
	We have a pretty good detection rate for this second scan, but for very advanced or sneaky attacks our admin's sometimes prefer to examine these files by hand. 
	If you would like to look at these non-integrity checked files, we provide you with the list below. You can click on any file to view the contents and see if it has been hacked.
	<br /><br />
	<b>Files that you will find in this list are:</b>
	<ul>
		<li>Files belonging to commercial themes that are not in the open source WordPress theme repository</li>
		<li>Files belonging to commercial plugins that are not in the open source WordPress repository</li>
		<li>Files created by themes or plugins</li>
		<li>Files created by you on your WordPress installation by uploading them through WordPress or a utility like FTP or SFTP</li>
		<li>Files that a hacker put on your system to create a back-door, distribute spam or for another nefarious purpose.</li>
	</ul>
	<b>How to use this list to clean your system if it is infected:</b>
	<ul>
		<li>First sort by most recently modified files by clicking the "Last Modified" column. You may have to click it twice.</li>
		<li>Examine recently modified files by clicking them to view the file and check if it is infected. This is often the most reliable way to find an infection.</li>
		<li>Then sort by "Full File Path" and look at files that aren't one of your custom themes or plugins.</li>
		<li>Note that custom themes and plugins live in the /wp-content/themes/ and /wp-content/plugins directories.</li>
		<li>Then start going through your themes and plugins to see if they are infected.</li>
	</ul>
</p>
<h2 style="margin-top: 30px;">Files that don't belong to WordPress Core, or to a theme or plugin in the WordPress Repository:</h2>


<?php
	$files = array();
	while(strlen($fileList) > 0){
		$filenameLen = unpack('n', substr($fileList, 0, 2));
		$filenameLen = $filenameLen[1];
		if($filenameLen > 1000 || $filenameLen < 1){
			continue;
		}
		$file = substr($fileList, 2, $filenameLen);
		$fileList = substr($fileList, 2 + $filenameLen);
		$fullFile = $path . $file;
		if(! file_exists($fullFile)){
			continue;
		}
		$fileExt = '';
		if(preg_match('/\.([a-zA-Z\d\-]{1,7})$/', $file, $matches)){
			$fileExt = strtolower($matches[1]);
		}
		$isPHP = false;
		if(preg_match('/^(?:php|phtml|php\d+)$/', $fileExt)){ 
			$isPHP = true;
		}
		//  http://test3.com/?_wfsf=view&nonce=c1ad72bcbd&file=wp-content%2Fplugins%2Fwordfence%2Flib%2Fmenu_options.php
		$viewLink = wfUtils::siteURLRelative() . '?_wfsf=view&nonce=' . wp_create_nonce('wp-ajax') . '&file=' . urlencode($file);
		$stat = stat($fullFile);
		if(function_exists('posix_getpwuid')){
			$owner = posix_getpwuid($stat['uid']);
			$owner = $owner['name'];
		} else {
			$owner = "unknown";
		}
		if(function_exists('posix_getgrgid')){
			$group = posix_getgrgid($stat['gid']);
			$group = $group['name'];
		} else {
			$group = 'unknown';
		}
		$perms = substr(sprintf('%o', fileperms($fullFile)), -4);
		$files[] = array($file, $fullFile, $stat['size'], $stat['mtime'], $viewLink, $owner, $group, $perms);
	}
	function wfUKFcmp($a, $b){
		$idx = $_GET['sort'] ? $_GET['sort'] : 2;
		if($_GET['dir'] == 'rev'){
			$tmp = $a;
			$a = $b;
			$b = $tmp;
		}
		$type = 'num';
		if($idx == 1 || $idx == 5 || $idx == 6 || $idx == 7){
			$type = 'str';
		}

		if($a[$idx] == $b[$idx]){
			return 0;
		}
		if($type == 'num'){
			return ($a[$idx] < $b[$idx])  ? -1 : 1;
		} else {
			return strcmp($a[$idx], $b[$idx]);
		}
	}
	usort($files, 'wfUKFcmp');

	$sortLink = wfUtils::siteURLRelative() . '?_wfsf=unknownFiles&nonce=' . wp_create_nonce('wp-ajax') . '&sort=';
	$sortIDX = $_GET['sort'];
	if(! $sortIDX){
		$sortIDX = 2;
	}
	$sortDir = $_GET['dir'];
	if(! $sortDir){
		$sortDir = 'fwd';
	}
?>
<p>
	All columns are sortable. Click the heading to sort a column. Click again to sort in reverse direction.<br />
	If you are cleaning a hacked site, start by sorting files by most recently modified and view those files first.
</p>
<table border="1" cellpadding="2" cellspacing="0">
<tr>
	<th><a href="<?php echo $sortLink; ?>2&dir=<?php echo ($sortIDX == 2 && $sortDir == 'fwd') ? 'rev' : 'fwd'; ?>">File Size in Bytes</a></th>
	<th><a href="<?php echo $sortLink; ?>3&dir=<?php echo ($sortIDX == 3 && $sortDir == 'fwd') ? 'rev' : 'fwd'; ?>">Last modified</a></th>
	<th><a href="<?php echo $sortLink; ?>5&dir=<?php echo ($sortIDX == 5 && $sortDir == 'fwd') ? 'rev' : 'fwd'; ?>">Owner<a></th>
	<th><a href="<?php echo $sortLink; ?>6&dir=<?php echo ($sortIDX == 6 && $sortDir == 'fwd') ? 'rev' : 'fwd'; ?>">Group</a></th>
	<th><a href="<?php echo $sortLink; ?>7&dir=<?php echo ($sortIDX == 7 && $sortDir == 'fwd') ? 'rev' : 'fwd'; ?>">Permissions</a></th>
	<th><a href="<?php echo $sortLink; ?>1&dir=<?php echo ($sortIDX == 1 && $sortDir == 'fwd') ? 'rev' : 'fwd'; ?>">Full file path</a></th>
</tr>
<?php
	for($i = 0; $i < sizeof($files); $i++){
		echo '<tr><td>' . wfUtils::formatBytes($files[$i][2]) . '</td><td>' . wfUtils::makeTimeAgo(time() - $files[$i][3]) . ' ago.</td><td>' . $files[$i][5] . '</td><td>' . $files[$i][6] . '</td><td>' . $files[$i][7] . '</td><td><a href="' . $files[$i][4] . '" target="_blank" rel="noopener noreferrer">' . $files[$i][1] . '</a></td></tr>';
	}
	echo "</table>";
} else {
?>
<p style="margin: 40px; font-size: 20px;">
	You either have not completed a scan recently, or there were no files found on your system that are not in the WordPress official repository for Core files, themes and plugins.
</p>
<?php
}

?>

<div class="diffFooter">&copy;&nbsp;2011 to <?php echo date('Y'); ?> Wordfence &mdash; Visit <a href="http://wordfence.com/">Wordfence.com</a> for help, security updates and more.</div>
</body>
</html>
