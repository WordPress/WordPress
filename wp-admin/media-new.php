<?php
/**
 * Upload new media Administration Screen.
 *
 * @package WordPress
 * @subpackage Administration
 */

if ( wp_is_mobile() ) // cannot upload files from mobile devices
	return;

$_GET['inline'] = 'true';
/** Administration bootstrap */
require_once('./admin.php');
require_once('./media-upload.php');
