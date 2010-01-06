<?php
/**
 * Draft Post Administration Panel.
 *
 * This was created for the administration navigation instead of using a query
 * for the drafts.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** Locks the post status to draft to prevent premature posting. */
$locked_post_status = 'draft';
$_GET['post_status'] = 'draft';
require_once('admin.php');
$title = __('View Drafts');
require_once('edit.php');

?>