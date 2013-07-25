<?php // custom WordPress database error page to handle reaping on Pantheon
	header('HTTP/1.1 550 Database connection error');
	header('Status: 550 Database connection error');
	header('Retry-After: 3600'); // 1 hour = 3600 seconds
	//mail("email@domain.tld", "Database Error", "Database connection error", "From: Website");
