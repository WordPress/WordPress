<?php
/*
  This is just a very simple script to set a cookie with a posted password and redirect back from whence the browser came.
  It doesn't need to connect to the DB, or do anything fancy at all. Yum.
  -- Matt
*/
require(dirname(__FILE__) . '/wp-config.php');
setcookie('wp-postpass_'.$cookiehash, stripslashes($_POST['post_password']), time()+60*60*24*30);
header('Location: ' . $_SERVER['HTTP_REFERER']);

?>