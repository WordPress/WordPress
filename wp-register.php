<?php

# This file is deprecated, but you shouldn't have been linking to it directly anyway :P
# Use wp_register() to create a registration link instead, it's much better ;)

require('./wp-config.php');
wp_redirect('wp-login.php?action=register');
exit();

?>