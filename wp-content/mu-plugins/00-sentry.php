<?php
// Path to Composer autoload
$autoload_path = ABSPATH . 'vendor/autoload.php';

if ( file_exists( $autoload_path ) ) {
    require_once $autoload_path;

    if ( getenv('SENTRY_DSN') ) {
        \Sentry\init([
            'dsn' => getenv('SENTRY_DSN'),
            'environment' => getenv('WP_ENV') ?: 'development',
            'traces_sample_rate' => 0.2,
        ]);

        // Test startup message
        \Sentry\captureMessage('Sentry initialized successfully');

        // TEMP: send a test exception only once
        static $sent_test_exception = false;
        if ( ! $sent_test_exception ) {
            $sent_test_exception = true;
            \Sentry\captureException(new Exception("Sentry test - mu-plugin custom exception"));
        }
    }
}
