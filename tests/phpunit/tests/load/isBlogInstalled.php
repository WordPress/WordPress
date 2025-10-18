<?php
/**
 * Tests for the is_blog_installed() function.
 *
 * @package WordPress\Tests\Load
 */

if ( ! class_exists( 'WPDieException' ) ) {
        class WPDieException extends Exception {}
}

/**
 * @group load
 */
class Tests_Load_IsBlogInstalled extends WP_UnitTestCase {

        /**
         * Original global wpdb instance.
         *
         * @var wpdb|null
         */
        private $original_wpdb = null;

        /**
         * List of temporary tables created during a test.
         *
         * @var string[]
         */
        private $temporary_tables = array();

        public function setUp(): void {
                parent::setUp();

                add_filter( 'wp_die_handler', array( $this, 'filter_wp_die_handler' ) );
        }

        public function tearDown(): void {
                remove_filter( 'wp_die_handler', array( $this, 'filter_wp_die_handler' ) );

                $this->drop_temporary_tables();
                $this->restore_original_wpdb();

                wp_cache_delete( 'is_blog_installed' );
                wp_cache_delete( 'alloptions', 'options' );

                parent::tearDown();
        }

        /**
         * Ensures that a clean schema reports the blog as not installed.
         */
        public function test_returns_false_when_core_tables_absent() {
                $this->switch_to_temporary_wpdb();

                wp_cache_delete( 'is_blog_installed' );

                try {
                        $this->assertFalse( is_blog_installed() );
                } catch ( WPDieException $exception ) {
                        $this->fail( 'is_blog_installed() triggered wp_die() unexpectedly: ' . $exception->getMessage() );
                }
        }

        /**
         * Ensures that partially created schemas trigger a database error.
         */
        public function test_partial_schema_triggers_database_error() {
                $this->switch_to_temporary_wpdb();

                global $wpdb;

                $table_name = $wpdb->options;

                $this->temporary_tables[] = $table_name;

                $wpdb->query(
                        "CREATE TABLE $table_name (id bigint(20) unsigned NOT NULL AUTO_INCREMENT, PRIMARY KEY  (id))"
                );

                wp_cache_delete( 'is_blog_installed' );

                try {
                        is_blog_installed();
                        $this->fail( 'is_blog_installed() did not trigger wp_die() for a partial schema.' );
                } catch ( WPDieException $exception ) {
                        $this->assertNotEmpty( $wpdb->error, 'Expected wpdb error message to be populated.' );
                }
        }

        /**
         * Filters the wp_die handler to throw an exception during the tests.
         *
         * @param callable $handler Original handler.
         * @return callable
         */
        public function filter_wp_die_handler( $handler ) {
                return array( $this, 'throwing_wp_die_handler' );
        }

        /**
         * Custom wp_die handler that converts calls into exceptions.
         *
         * @param mixed  $message Error message.
         * @param string $title   Optional. Error title. Default empty string.
         * @param array  $args    Optional. Arguments passed to wp_die(). Default empty array.
         * @throws WPDieException When invoked.
         */
        public function throwing_wp_die_handler( $message, $title = '', $args = array() ) {
                if ( is_array( $message ) ) {
                        $message = implode( ' ', $message );
                }

                throw new WPDieException( (string) $message );
        }

        /**
         * Switches the global wpdb instance to a temporary prefix with an empty schema.
         */
        private function switch_to_temporary_wpdb() {
                global $wpdb;

                if ( null !== $this->original_wpdb ) {
                        return;
                }

                $prefix = 'wptemp_' . uniqid() . '_';

                $this->original_wpdb = $wpdb;
                $wpdb                = new wpdb( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST );
                $wpdb->set_prefix( $prefix );
                $wpdb->suppress_errors( true );
                $wpdb->show_errors( false );
        }

        /**
         * Drops any temporary tables created during the test run.
         */
        private function drop_temporary_tables() {
                if ( empty( $this->temporary_tables ) ) {
                        return;
                }

                global $wpdb;

                foreach ( $this->temporary_tables as $table_name ) {
                        $wpdb->query( "DROP TABLE IF EXISTS $table_name" );
                }

                $this->temporary_tables = array();
        }

        /**
         * Restores the original global wpdb instance.
         */
        private function restore_original_wpdb() {
                if ( null === $this->original_wpdb ) {
                        return;
                }

                global $wpdb;

                $wpdb = $this->original_wpdb;

                $this->original_wpdb = null;
        }
}

