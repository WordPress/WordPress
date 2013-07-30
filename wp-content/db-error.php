<?php
// custom WordPress database error page to handle reaping on Pantheon
header('HTTP/1.1 550 Database connection error');
header('Status: 550 Database connection error');
