<?php
/**
 * Used to be the page which displayed the registration form.
 *
 * This file is no longer used in WordPress and is
 * deprecated.
 *
 * @package WordPress
 * @deprecated Use wp_register() to create a registration link instead
 */

require('./wp-load.php');
wp_redirect( site_url('wp-login.php?action=register') );
exit;
