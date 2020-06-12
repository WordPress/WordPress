<?php

/**
 * The PHPMailer class has been moved to the wp-includes/PHPMailer subdirectory and now uses the PHPMailer\PHPMailer namespace.
 */
if ( function_exists( '_deprecated_file' ) ) {
	_deprecated_file( basename( __FILE__ ), '5.5.0', WPINC . '/PHPMailer/PHPMailer.php', __( 'The PHPMailer class has been moved to wp-includes/PHPMailer subdirectory and now uses the PHPMailer\PHPMailer namespace.' ) );
}
require __DIR__ . '/PHPMailer/PHPMailer.php';

class_alias( PHPMailer\PHPMailer\PHPMailer::class, 'PHPMailer' );
