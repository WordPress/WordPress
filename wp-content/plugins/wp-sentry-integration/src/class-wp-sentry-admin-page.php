<?php

/**
 * Sentry for WordPress Admin Page.
 *
 * @internal This class is not part of the public API and may be removed or changed at any time.
 */
final class WP_Sentry_Admin_Page {
	/**
	 * The admin page slug.
	 */
	private const ADMIN_PAGE_SLUG = 'wp-sentry';

	/**
	 * Holds the class instance.
	 *
	 * @var WP_Sentry_Admin_Page
	 */
	private static $instance;

	/**
	 * Get the Sentry admin page instance.
	 *
	 * @return WP_Sentry_Admin_Page
	 */
	public static function get_instance(): WP_Sentry_Admin_Page {
		return self::$instance ?: self::$instance = new self;
	}

	/**
	 * Check if we are on the admin page currently.
	 *
	 * @return bool
	 */
	public function is_on_admin_page(): bool {
		return is_admin() && isset( $_GET['page'] ) && $_GET['page'] === self::ADMIN_PAGE_SLUG;
	}

	/**
	 * WP_Sentry_Admin_Page constructor.
	 */
	protected function __construct() {
		add_action( 'init', function () {
			if ( ! is_admin() ) {
				return;
			}

			add_action( 'admin_menu', [ $this, 'admin_menu' ] );
			add_action( 'network_admin_menu', [ $this, 'network_admin_menu' ] );
		} );
	}

	/**
	 * Setup the admin menu page.
	 */
	public function admin_menu(): void {
		if ( is_plugin_active_for_network( plugin_basename( WP_SENTRY_PLUGIN_FILE ) ) ) {
			return;
		}

		add_management_page(
			'Sentry',
			'Sentry',
			'activate_plugins',
			self::ADMIN_PAGE_SLUG,
			[ $this, 'render_admin_page' ]
		);
	}

	/**
	 * Setup the network admin menu page.
	 */
	public function network_admin_menu(): void {
		if ( ! is_plugin_active_for_network( plugin_basename( WP_SENTRY_PLUGIN_FILE ) ) ) {
			return;
		}

		global $submenu;

		// Network admin has no tools section so we add it ourselfs
		add_menu_page(
			'',
			'Tools',
			'activate_plugins',
			'wp-sentry-tools-menu',
			'',
			'dashicons-admin-tools',
			22
		);

		add_submenu_page(
			'wp-sentry-tools-menu',
			'Sentry',
			'Sentry',
			'activate_plugins',
			self::ADMIN_PAGE_SLUG,
			[ $this, 'render_admin_page' ]
		);

		// Remove the submenu item crate by `add_menu_page` that links to `wp-sentry-tools-menu` which does not exist
		if ( ! empty( $submenu['wp-sentry-tools-menu'][0] ) && $submenu['wp-sentry-tools-menu'][0][2] === 'wp-sentry-tools-menu' ) {
			unset( $submenu['wp-sentry-tools-menu'][0] );
		}
	}

	/**
	 * Try to send a test even to Sentry.
	 *
	 * @return string|null
	 */
	private function send_test_event(): ?string {
		$tracker = WP_Sentry_Php_Tracker::get_instance();

		if ( ! empty( $tracker->get_dsn() ) || WP_Sentry_Php_Tracker::get_spotlight_enabled() ) {
			return $tracker->get_client()->captureMessage( 'This is a test message sent from the Sentry WP PHP integration.' );
		}

		return null;
	}

	/**
	 * Try to send a test even to Sentry.
	 *
	 * @return string|null
	 */
	private function send_test_exception(): ?string {
		$exception = $this->generateTestException( 'wp sentry test', [ 'foo' => 'bar' ] );

		$tracker = WP_Sentry_Php_Tracker::get_instance();

		if ( ! empty( $tracker->get_dsn() ) || WP_Sentry_Php_Tracker::get_spotlight_enabled() ) {
			return $tracker->get_client()->captureException( $exception );
		}

		return null;
	}

	/**
	 * Generate a test exception to send to Sentry.
	 *
	 * @param string $command
	 * @param array  $arg
	 *
	 * @return \Exception
	 */
	private function generateTestException( string $command, array $arg ): ?Exception {
		try {
			throw new Exception( 'This is a test exception sent from the Sentry WP PHP integration.' );
		} catch ( Exception $ex ) {
			return $ex;
		}
	}

	/**
	 * Render the admin page.
	 */
	public function render_admin_page(): void {
		$test_event_sent = false;
		$test_event_id   = null;

		if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			if ( isset( $_POST['wp-sentry-send-test-event-php'] ) ) {
				$test_event_sent = true;
				$test_event_id   = $this->send_test_event();
			} elseif ( isset( $_POST['wp-sentry-send-test-exception-php'] ) ) {
				$test_event_sent = true;
				$test_event_id   = $this->send_test_exception();
			}
		}

		$js_tracker = WP_Sentry_Js_Tracker::get_instance();

		$enabled_for_js      = $js_tracker->enabled();
		$js_tracing_enabled  = $enabled_for_js && $js_tracker->tracing_enabled();
		$js_replays_enabled  = $enabled_for_js && $js_tracker->replays_enabled();
		$js_feedback_enabled = $enabled_for_js && $js_tracker->feedback_enabled();
		$js_enabled_on_admin = $js_tracker->enabled_on_admin_pages();
		$js_enabled_on_login = $js_tracker->enabled_on_login_page();
		$js_enabled_on_front = $js_tracker->enabled_on_frontend_pages();

		$php_tracker = WP_Sentry_Php_Tracker::get_instance();

		$enabled_for_php = $php_tracker->enabled() || WP_Sentry_Php_Tracker::get_spotlight_enabled();

		$php_tracing_enabled   = $enabled_for_php && WP_Sentry_Php_Tracing::get_instance()->is_tracing_enabled();
		$php_profiling_enabled = $enabled_for_php && WP_Sentry_Php_Tracing::get_instance()->is_profiling_enabled();
		$php_profiling_excimer = extension_loaded( 'excimer' );

		$options = $php_tracker->get_default_options();

		$sends_default_pii = defined( 'WP_SENTRY_SEND_DEFAULT_PII' ) && WP_SENTRY_SEND_DEFAULT_PII;

		$uses_scoped_autoloader = defined( 'WP_SENTRY_SCOPED_AUTOLOADER' ) && WP_SENTRY_SCOPED_AUTOLOADER;

		?>
		<div class="wrap">
			<h1>Sentry</h1>

			<p>You are using version <b><?php echo WP_Sentry_Version::SDK_VERSION ?></b> of the Sentry for WordPress plugin.</p>

			<h2>Common</h2>

			<p>Information listed below is used for both the PHP and Browser integration.</p>

			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th>
							<label for="wp-sentry-release"><?php esc_html_e( 'Release (version)', 'wp-sentry' ); ?></label>
						</th>
						<td>
							<input id="wp-sentry-release" type="text" class="regular-text code" readonly name="wp-sentry-release" value="<?php echo esc_html( $options['release'] ?? '' ); ?>" placeholder="[no value set]"/>
							<p class="description">
								<?php echo translate( 'Change this value by defining <code>WP_SENTRY_VERSION</code>, when possible this value defaults version of the active theme.', 'wp-sentry' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th>
							<label for="wp-sentry-environment"><?php esc_html_e( 'Environment', 'wp-sentry' ); ?></label>
						</th>
						<td>
							<input id="wp-sentry-environment" type="text" class="regular-text code" readonly name="wp-sentry-environment" value="<?php echo esc_html( $options['environment'] ?? '' ); ?>" placeholder="[no value set]"/>
							<p class="description">
								<?php echo translate( 'Change this value by defining <code>WP_SENTRY_ENV</code> or <code>WP_ENVIRONMENT_TYPE</code> (WordPress 5.5+).', 'wp-sentry' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Identify Users', 'wp-sentry' ); ?></th>
						<td>
							<fieldset>
								<label>
									<input name="wp-sentry-identify-users-enabled" type="checkbox" id="wp-sentry-identify-users-enabled" value="0" <?php echo $sends_default_pii ? 'checked="checked"' : '' ?> readonly disabled>
									<?php esc_html_e( 'Enabled', 'wp-sentry' ); ?>
									(<a href="https://github.com/stayallive/wp-sentry/tree/v<?php echo WP_Sentry_Version::SDK_VERSION; ?>#privacy" target="_blank" rel="noopener">documentation</a>)
								</label>
							</fieldset>
							<?php if ( ! $sends_default_pii ): ?>
								<p class="description">
									<?php echo translate( 'To enable make sure <code>WP_SENTRY_SEND_DEFAULT_PII</code> is set to true.', 'wp-sentry' ); ?>
								</p>
								<br>
							<?php endif; ?>
						</td>
					</tr>
				</tbody>
			</table>

			<hr>

			<h2>PHP integration</h2>

			<p>Information listed below is only applicable for the PHP integration.</p>
			<p>The PHP integration uses the official <a href="https://github.com/getsentry/sentry-php" target="_blank" rel="noopener">Sentry SDK for PHP</a> version <b><?php echo WP_Sentry_Php_Tracker::get_instance()->get_sdk_version(); ?></b>.</p>

			<?php if ( $test_event_sent ): ?>
				<?php if ( $test_event_id !== null ): ?>
					<div class="notice notice-success is-dismissible">
						<p><?php echo translate( "PHP test sent successfully, event ID: <code>{$test_event_id}</code>!", 'wp-sentry' ); ?></p>
					</div>
				<?php else: ?>
					<div class="notice notice-error is-dismissible">
						<p><?php esc_html_e( 'PHP failed to send test. Check your configuration to make sure your DSN is set correctly.', 'wp-sentry' ); ?></p>
					</div>
				<?php endif; ?>
			<?php endif; ?>

			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th><?php esc_html_e( 'Integration', 'wp-sentry' ); ?></th>
						<td>
							<fieldset>
								<label title="<?php echo $uses_scoped_autoloader ? 'Using scoped vendor (plugin build)' : 'Using regular vendor (composer)'; ?>">
									<input name="wp-sentry-php-enabled" type="checkbox" id="wp-sentry-php-enabled" value="0" <?php echo $enabled_for_php ? 'checked="checked"' : '' ?> readonly disabled>
									<?php esc_html_e( 'Enabled', 'wp-sentry' ); ?>
									(<a href="https://github.com/stayallive/wp-sentry/tree/v<?php echo WP_Sentry_Version::SDK_VERSION; ?>#wp_sentry_php_dsn-php" target="_blank" rel="noopener">documentation</a>)
								</label>
							</fieldset>
							<?php if ( ! $enabled_for_php ): ?>
								<p class="description">
									<?php echo translate( 'To enable make sure <code>WP_SENTRY_PHP_DSN</code> contains a valid DSN.', 'wp-sentry' ); ?>
								</p>
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Tracing', 'wp-sentry' ); ?></th>
						<td>
							<fieldset>
								<label>
									<input name="wp-sentry-php-tracing-enabled" type="checkbox" id="wp-sentry-php-tracing-enabled" value="0" <?php echo $php_tracing_enabled ? 'checked="checked"' : '' ?> readonly disabled>
									<?php esc_html_e( 'Enabled', 'wp-sentry' ); ?>
									(<a href="https://github.com/stayallive/wp-sentry/tree/v<?php echo WP_Sentry_Version::SDK_VERSION; ?>#set-up-tracing" target="_blank" rel="noopener">documentation</a>)
								</label>
							</fieldset>
							<?php if ( ! $php_tracing_enabled ): ?>
								<p class="description">
									<?php echo translate( 'To enable make sure <code>WP_SENTRY_TRACES_SAMPLE_RATE</code> is set.', 'wp-sentry' ); ?>
								</p>
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Profiling', 'wp-sentry' ); ?></th>
						<td>
							<fieldset>
								<label>
									<input name="wp-sentry-php-profiling-enabled" type="checkbox" id="wp-sentry-php-profiling-enabled" value="0" <?php echo $php_profiling_enabled ? 'checked="checked"' : '' ?> readonly disabled>
									<?php esc_html_e( 'Enabled', 'wp-sentry' ); ?>
									(<a href="https://github.com/stayallive/wp-sentry/tree/v<?php echo WP_Sentry_Version::SDK_VERSION; ?>#set-up-profiling" target="_blank" rel="noopener">documentation</a>)
								</label>
							</fieldset>
							<?php if ( ! $php_profiling_excimer ): ?>
								<p class="description">
									<?php echo translate( 'This functionality needs the <code>excimer</code> PHP extension to work, please make sure you have <code>excimer</code> installed and enabled.', 'wp-sentry' ); ?>
								</p>
							<?php elseif ( ! $php_profiling_enabled ): ?>
								<p class="description">
									<?php echo translate( 'To enable make sure tracing is enabled and <code>WP_SENTRY_PROFILES_SAMPLE_RATE</code> is set.', 'wp-sentry' ); ?>
								</p>
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<th>
							<label><?php esc_html_e( 'Test PHP integration', 'wp-sentry' ); ?></label>
						</th>
						<td>
							<form method="post">
								<input type="submit" name="wp-sentry-send-test-event-php" class="button" value="<?php esc_html_e( 'Send PHP test event', 'wp-sentry' ) ?>" <?php echo $enabled_for_php ? '' : 'disabled'; ?>>
								<input type="submit" name="wp-sentry-send-test-exception-php" class="button" value="<?php esc_html_e( 'Send PHP test exception', 'wp-sentry' ) ?>" <?php echo $enabled_for_php ? '' : 'disabled'; ?>>
							</form>
							<?php if ( ! $enabled_for_php ): ?>
								<p class="description">
									<?php echo translate( 'The PHP integration must be enabled to send a test event.', 'wp-sentry' ); ?>
								</p>
							<?php endif; ?>
						</td>
					</tr>
				</tbody>
			</table>

			<hr>

			<h2>Browser integration (JavaScript)</h2>

			<p>Information listed below is only applicable for the Browser integration.</p>
			<p>The Browser integration uses the official <a href="https://github.com/getsentry/sentry-javascript/" target="_blank" rel="noopener">Sentry SDK for JavaScript</a> version <b><?php echo WP_Sentry_Js_Tracker::get_instance()->get_sdk_version(); ?></b>.</p>

			<div class="notice notice-success is-dismissible hidden" id="sentry-test-event-js-success">
				<p><?php echo translate( 'Browser test sent successfully, event ID: <code id="sentry-test-event-js-id"></code>!', 'wp-sentry' ); ?></p>
			</div>

			<div class="notice notice-error is-dismissible hidden" id="sentry-test-event-js-error">
				<p><?php esc_html_e( 'Browser failed to send test. Check your configuration to make sure your DSN is set correctly.', 'wp-sentry' ); ?></p>
			</div>

			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th><?php esc_html_e( 'Integration', 'wp-sentry' ); ?></th>
						<td>
							<fieldset>
								<label>
									<input name="wp-sentry-js-enabled" type="checkbox" id="wp-sentry-js-enabled" value="0" <?php echo $enabled_for_js ? 'checked="checked"' : '' ?> readonly disabled>
									<?php esc_html_e( 'Enabled', 'wp-sentry' ); ?>
									(<a href="https://github.com/stayallive/wp-sentry/tree/v<?php echo WP_Sentry_Version::SDK_VERSION; ?>#wp_sentry_browser_dsn-browser" target="_blank" rel="noopener">documentation</a>)
								</label>
							</fieldset>
							<p class="description">
								<?php if ( ! $enabled_for_js ): ?>
									<?php echo translate( 'To enable make sure <code>WP_SENTRY_BROWSER_DSN</code> contains a valid DSN.', 'wp-sentry' ); ?>
								<?php else: ?>
									<label>
										<input name="wp-sentry-js-tracing-enabled-on-front" type="checkbox" id="wp-sentry-js-tracing-enabled-on-front" value="0" <?php echo $js_enabled_on_front ? 'checked="checked"' : '' ?> readonly disabled> Enabled on front end
									</label>
									<br>
									<label>
										<input name="wp-sentry-js-tracing-enabled-on-admin" type="checkbox" id="wp-sentry-js-tracing-enabled-on-admin" value="0" <?php echo $js_enabled_on_admin ? 'checked="checked"' : '' ?> readonly disabled> Enabled in <code>wp-admin</code>
									</label>
									<br>
									<label>
										<input name="wp-sentry-js-tracing-enabled-on-login" type="checkbox" id="wp-sentry-js-tracing-enabled-on-login" value="0" <?php echo $js_enabled_on_login ? 'checked="checked"' : '' ?> readonly disabled> Enabled on login page
									</label>
								<?php endif; ?>
							</p>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Tracing', 'wp-sentry' ); ?></th>
						<td>
							<fieldset>
								<label>
									<input name="wp-sentry-js-tracing-enabled" type="checkbox" id="wp-sentry-js-tracing-enabled" value="0" <?php echo $js_tracing_enabled ? 'checked="checked"' : '' ?> readonly disabled>
									<?php esc_html_e( 'Enabled', 'wp-sentry' ); ?>
									(<a href="https://github.com/stayallive/wp-sentry/tree/v<?php echo WP_Sentry_Version::SDK_VERSION; ?>#set-up-tracing" target="_blank" rel="noopener">documentation</a>)
								</label>
							</fieldset>
							<?php if ( ! $js_tracing_enabled ): ?>
								<p class="description">
									<?php echo translate( 'To enable make sure <code>WP_SENTRY_BROWSER_TRACES_SAMPLE_RATE</code> is set.', 'wp-sentry' ); ?>
								</p>
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Session Replay', 'wp-sentry' ); ?></th>
						<td>
							<fieldset>
								<label>
									<input name="wp-sentry-js-session-replays-enabled" type="checkbox" id="wp-sentry-js-session-replays-enabled" value="0" <?php echo $js_replays_enabled ? 'checked="checked"' : '' ?> readonly disabled>
									<?php esc_html_e( 'Enabled', 'wp-sentry' ); ?>
									(<a href="https://github.com/stayallive/wp-sentry/tree/v<?php echo WP_Sentry_Version::SDK_VERSION; ?>#wp_sentry_browser_replays_session_sample_rate-browser" target="_blank" rel="noopener">documentation</a>)
								</label>
							</fieldset>
							<?php if ( ! $js_replays_enabled ): ?>
								<p class="description">
									<?php echo translate( 'To enable make sure <code>WP_SENTRY_BROWSER_REPLAYS_SESSION_SAMPLE_RATE</code> or <code>WP_SENTRY_BROWSER_REPLAYS_ON_ERROR_SAMPLE_RATE</code> is set.', 'wp-sentry' ); ?>
								</p>
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'User Feedback Widget', 'wp-sentry' ); ?></th>
						<td>
							<fieldset>
								<label>
									<input name="wp-sentry-js-user-feedback-widget-enabled" type="checkbox" id="wp-sentry-js-user-feedback-widget-enabled" value="0" <?php echo $js_feedback_enabled ? 'checked="checked"' : '' ?> readonly disabled>
									<?php esc_html_e( 'Enabled', 'wp-sentry' ); ?>
									(<a href="https://github.com/stayallive/wp-sentry/tree/v<?php echo WP_Sentry_Version::SDK_VERSION; ?>#set-up-user-feedback" target="_blank" rel="noopener">documentation</a>)
								</label>
							</fieldset>
							<?php if ( ! $js_feedback_enabled ): ?>
								<p class="description">
									<?php echo translate( 'To enable make sure <code>WP_SENTRY_BROWSER_FEEDBACK_OPTIONS</code> is set.', 'wp-sentry' ); ?>
								</p>
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<th>
							<label><?php esc_html_e( 'Test Browser integration', 'wp-sentry' ); ?></label>
						</th>
						<td>
							<form method="post">
								<input type="button" id="wp-sentry-send-test-event-js" class="button" value="<?php esc_html_e( 'Send Browser test event', 'wp-sentry' ) ?>" <?php echo $enabled_for_js ? '' : 'disabled'; ?>>
								<input type="button" id="wp-sentry-send-test-error-js" class="button" value="<?php esc_html_e( 'Send Browser test error', 'wp-sentry' ) ?>" <?php echo $enabled_for_js ? '' : 'disabled'; ?>>
							</form>
							<?php if ( ! $enabled_for_js ): ?>
								<p class="description">
									<?php echo translate( 'The Browser integration must be enabled to send a test event.', 'wp-sentry' ); ?>
								</p>
							<?php endif; ?>
						</td>
					</tr>
				</tbody>
			</table>

			<hr>

			<h2>Links</h2>

			<ul>
				<li>
					<a href="https://github.com/stayallive/wp-sentry/tree/v<?php echo WP_Sentry_Version::SDK_VERSION ?>#configuration" target="_blank" rel="noopener">Plugin Documentation</a>
				</li>
				<li>
					<a href="https://wordpress.org/plugins/wp-sentry-integration/" target="_blank" rel="noopener">WordPress plugin repository</a>
				</li>
				<li>
					<a href="https://docs.sentry.io/platforms/php/" target="_blank" rel="noopener">Sentry SDK for PHP Documentation</a>
				</li>
				<li>
					<a href="https://docs.sentry.io/platforms/javascript/" target="_blank" rel="noopener">Sentry SDK for Browser JavaScript Documentation</a>
				</li>
			</ul>

			<hr>

			Debug string for the plugin developers: <code>wp-sentry-integration@<?php echo WP_Sentry_Version::SDK_VERSION; ?>-<?php echo $uses_scoped_autoloader ? 'scoped' : 'vendor'; ?>:php@<?php echo PHP_VERSION; ?>:sdk@<?php echo WP_Sentry_Php_Tracker::get_instance()->get_sdk_version(); ?>:js-sdk@<?php echo WP_Sentry_Js_Tracker::get_instance()->get_sdk_version(); ?></code>
		</div>

		<script>
            (function () {
                var testEventButton = document.getElementById('wp-sentry-send-test-event-js');
                var testErrorButton = document.getElementById('wp-sentry-send-test-error-js');

                testEventButton.addEventListener('click', function (e) {
                    e.preventDefault();

                    if (testEventButton.classList.contains('disabled')) {
                        return;
                    }

                    testEventButton.classList.add('disabled');

                    console.log('=> Sending a test message to Sentry...');

                    if (typeof Sentry === 'object' && typeof Sentry.captureMessage === 'function') {
                        var eventId = Sentry.captureMessage('This is a test message sent from the Sentry WP Browser integration.');

                        console.log(' > Sent message with event ID:', eventId);

                        if (typeof eventId === 'string' && eventId.length > 1) {
                            document.getElementById('sentry-test-event-js-id').textContent = eventId;
                            document.getElementById('sentry-test-event-js-success').classList.remove('hidden');

                            return;
                        }
                    }

                    console.error('!> Failed to sent a test message to Sentry');

                    document.getElementById('sentry-test-event-js-error').classList.remove('hidden');
                });

                testErrorButton.addEventListener('click', function (e) {
                    e.preventDefault();

                    if (testErrorButton.classList.contains('disabled')) {
                        return;
                    }

                    testErrorButton.classList.add('disabled');

                    console.log('=> Sending a test error to Sentry...');

                    if (typeof Sentry === 'object' && typeof Sentry.captureException === 'function') {
                        try {
                            wpSentryIntegrationTestError();
                        } catch (e) {
                            var eventId = Sentry.captureException(e);

                            console.log(' > Sent error with event ID:', eventId);

                            if (typeof eventId === 'string' && eventId.length > 1) {
                                document.getElementById('sentry-test-event-js-id').textContent = eventId;
                                document.getElementById('sentry-test-event-js-success').classList.remove('hidden');

                                return;
                            }
                        }
                    }

                    console.error('!> Failed to sent a test message to Sentry');

                    document.getElementById('sentry-test-event-js-error').classList.remove('hidden');
                });
            })();
		</script>
	<?php }
}
