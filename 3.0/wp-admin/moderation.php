<?php
/**
 * Comment Moderation Administration Panel.
 *
 * Redirects to edit-comments.php?comment_status=moderated.
 *
 * @package WordPress
 * @subpackage Administration
 */
require_once('../wp-load.php');
wp_redirect('edit-comments.php?comment_status=moderated');
?>
