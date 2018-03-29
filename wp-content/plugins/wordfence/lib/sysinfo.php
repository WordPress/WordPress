<?php if (!defined('WORDFENCE_VERSION')) { exit; } ?>
<?php if(! wfUtils::isAdmin()){ exit(); } ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  dir="ltr" lang="en-US">
<head>
<title>Wordfence System Info</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel='stylesheet' id='wordfence-main-style-css'  href='<?php echo wfUtils::getBaseURL() . wfUtils::versionedAsset('css/phpinfo.css'); ?>?ver=<?php echo WORDFENCE_VERSION; ?>' type='text/css' media='all' />
<body>
<?php 
ob_start();
phpinfo(INFO_ALL); 
$out = ob_get_clean();
$out = str_replace('width="600"','width="900"', $out);
// $out = preg_replace('/<hr.*?PHP Credits.*?<\/h1>/s', '', $out);
$out = preg_replace('/<a [^>]+>/', '', $out);
$out = preg_replace('/<\/a>/', '', $out);
$out = preg_replace('/<title>[^<]*<\/title>/','', $out);
echo $out;
?>
<div class="diffFooter">&copy;&nbsp;2011 to <?php echo date('Y'); ?> Wordfence &mdash; Visit <a href="http://wordfence.com/">Wordfence.com</a> for help, security updates and more.</div>
</body>
</html>
