<?php

/**
 * The SMTP class has been moved to the wp-includes/PHPMailer subdirectory and now uses the PHPMailer\PHPMailer namespace.
 */
_deprecated_file(
	basename( __FILE__ ),
	'5.5.0',
	WPINC . '/PHPMailer/SMTP.php',
	__( 'The SMTP class has been moved to the wp-includes/PHPMailer subdirectory and now uses the PHPMailer\PHPMailer namespace.' )
);

require __DIR__ . '/PHPMailer/SMTP.php';

class_alias( PHPMailer\PHPMailer\SMTP::class, 'SMTP' );
