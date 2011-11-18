<?php
/**
 * Upload new media Administration Screen.
 *
 * @package WordPress
 * @subpackage Administration
 */

global $is_iphone;

if ( $is_iphone ) // cannot upload files from iPhone/iPad
	return;

$_GET['inline'] = 'true';
/** Administration bootstrap */
require_once('./admin.php');
require_once('./media-upload.php');

?>
