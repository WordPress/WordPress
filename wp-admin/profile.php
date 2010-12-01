<?php
/**
 * User Profile Administration Panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * This is a profile page.
 *
 * @since 2.5.0
 * @var bool
 */
define('IS_PROFILE_PAGE', true);

/** Load User Editing Page */
require_once('./user-edit.php');
?>
