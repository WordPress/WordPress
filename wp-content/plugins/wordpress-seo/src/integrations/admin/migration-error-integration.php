<?php

namespace Yoast\WP\SEO\Integrations\Admin;

use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Config\Migration_Status;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Presenters\Admin\Migration_Error_Presenter;

/**
 * Migration_Error_Integration class.
 */
class Migration_Error_Integration implements Integration_Interface {

	/**
	 * The migration status object.
	 *
	 * @var Migration_Status
	 */
	protected $migration_status;

	/**
	 * {@inheritDoc}
	 */
	public static function get_conditionals() {
		return [ Admin_Conditional::class ];
	}

	/**
	 * Migration_Error_Integration constructor.
	 *
	 * @param Migration_Status $migration_status The migration status object.
	 */
	public function __construct( Migration_Status $migration_status ) {
		$this->migration_status = $migration_status;
	}

	/**
	 * {@inheritDoc}
	 */
	public function register_hooks() {
		if ( $this->migration_status->get_error( 'free' ) === false ) {
			return;
		}

		\add_action( 'admin_notices', [ $this, 'render_migration_error' ] );
	}

	/**
	 * Renders the migration error.
	 *
	 * @return void
	 */
	public function render_migration_error() {
		// phpcs:ignore WordPress.Security.EscapeOutput -- The Migration_Error_Presenter already escapes it's output.
		echo new Migration_Error_Presenter( $this->migration_status->get_error( 'free' ) );
	}
}
