<?php
/**
 * Class for registering & modifying FIDO U2F security keys.
 *
 * @package Two_Factor
 */

/**
 * Class for registering & modifying FIDO U2F security keys.
 *
 * @since 0.1-dev
 *
 * @package Two_Factor
 */
class Two_Factor_FIDO_U2F_Admin {

	/**
	 * The user meta register data.
	 *
	 * @type string
	 */
	const REGISTER_DATA_USER_META_KEY = '_two_factor_fido_u2f_register_request';

	/**
	 * Add various hooks.
	 *
	 * @since 0.1-dev
	 *
	 * @access public
	 * @static
	 */
	public static function add_hooks() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_action( 'show_user_security_settings', array( __CLASS__, 'show_user_profile' ) );
		add_action( 'personal_options_update', array( __CLASS__, 'catch_submission' ), 0 );
		add_action( 'edit_user_profile_update', array( __CLASS__, 'catch_submission' ), 0 );
		add_action( 'load-profile.php', array( __CLASS__, 'catch_delete_security_key' ) );
		add_action( 'load-user-edit.php', array( __CLASS__, 'catch_delete_security_key' ) );
		add_action( 'wp_ajax_inline-save-key', array( __CLASS__, 'wp_ajax_inline_save' ) );
	}

	/**
	 * Enqueue assets.
	 *
	 * @since 0.1-dev
	 *
	 * @access public
	 * @static
	 *
	 * @param string $hook Current page.
	 */
	public static function enqueue_assets( $hook ) {
		if ( ! in_array( $hook, array( 'user-edit.php', 'profile.php' ), true ) ) {
			return;
		}

		$user_id = Two_Factor_Core::current_user_being_edited();
		if ( ! $user_id ) {
			return;
		}

		$security_keys = Two_Factor_FIDO_U2F::get_security_keys( $user_id );

		// @todo Ensure that scripts don't fail because of missing u2fL10n.
		try {
			$data               = Two_Factor_FIDO_U2F::$u2f->getRegisterData( $security_keys );
			list( $req, $sigs ) = $data;

			update_user_meta( $user_id, self::REGISTER_DATA_USER_META_KEY, $req );
		} catch ( Exception $e ) {
			return false;
		}

		wp_enqueue_style(
			'fido-u2f-admin',
			plugins_url( 'css/fido-u2f-admin.css', __FILE__ ),
			null,
			self::asset_version()
		);

		wp_enqueue_script(
			'fido-u2f-admin',
			plugins_url( 'js/fido-u2f-admin.js', __FILE__ ),
			array( 'jquery', 'fido-u2f-api' ),
			self::asset_version(),
			true
		);

		/**
		 * Pass a U2F challenge and user data to our scripts
		 */

		$translation_array = array(
			'user_id'  => $user_id,
			'register' => array(
				'request' => $req,
				'sigs'    => $sigs,
			),
			'text'     => array(
				'insert'            => esc_html__( 'Now insert (and tap) your Security Key.', 'two-factor' ),
				'error'             => esc_html__( 'U2F request failed.', 'two-factor' ),
				'error_codes'       => array(
					// Map u2f.ErrorCodes to error messages.
					0 => esc_html__( 'Request OK.', 'two-factor' ),
					1 => esc_html__( 'Other U2F error.', 'two-factor' ),
					2 => esc_html__( 'Bad U2F request.', 'two-factor' ),
					3 => esc_html__( 'Unsupported U2F configuration.', 'two-factor' ),
					4 => esc_html__( 'U2F device ineligible.', 'two-factor' ),
					5 => esc_html__( 'U2F request timeout reached.', 'two-factor' ),
				),
				'u2f_not_supported' => esc_html__( 'FIDO U2F appears to be not supported by your web browser. Try using Google Chrome or Firefox.', 'two-factor' ),
			),
		);

		wp_localize_script(
			'fido-u2f-admin',
			'u2fL10n',
			$translation_array
		);

		/**
		 * Script for admin UI
		 */

		wp_enqueue_script(
			'inline-edit-key',
			plugins_url( 'js/fido-u2f-admin-inline-edit.js', __FILE__ ),
			array( 'jquery' ),
			self::asset_version(),
			true
		);

		wp_localize_script(
			'inline-edit-key',
			'inlineEditL10n',
			array(
				'error' => esc_html__( 'Error while saving the changes.', 'two-factor' ),
			)
		);
	}

	/**
	 * Return the current asset version number.
	 *
	 * Added as own helper to allow swapping the implementation once we inject
	 * it as a dependency.
	 *
	 * @return string
	 */
	protected static function asset_version() {
		return Two_Factor_FIDO_U2F::asset_version();
	}

	/**
	 * Display the security key section in a users profile.
	 *
	 * This executes during the `show_user_security_settings` action.
	 *
	 * @since 0.1-dev
	 *
	 * @access public
	 * @static
	 *
	 * @param WP_User $user WP_User object of the logged-in user.
	 */
	public static function show_user_profile( $user ) {
		if ( ! Two_Factor_FIDO_U2F::is_supported_for_user( $user ) ) {
			return;
		}

		wp_nonce_field( "user_security_keys-{$user->ID}", '_nonce_user_security_keys' );
		$new_key = false;

		$security_keys = Two_Factor_FIDO_U2F::get_security_keys( $user->ID );
		if ( $security_keys ) {
			foreach ( $security_keys as &$security_key ) {
				if ( property_exists( $security_key, 'new' ) ) {
					$new_key = true;
					unset( $security_key->new );

					// If we've got a new one, update the db record to not save it there any longer.
					Two_Factor_FIDO_U2F::update_security_key( $user->ID, $security_key );
				}
			}
			unset( $security_key );
		}

		?>
		<div class="security-keys" id="security-keys-section">
			<h3><?php esc_html_e( 'Security Keys', 'two-factor' ); ?></h3>

			<?php if ( ! is_ssl() ) : ?>
			<p class="u2f-error-https">
				<em><?php esc_html_e( 'U2F requires an HTTPS connection. You won\'t be able to add new security keys over HTTP.', 'two-factor' ); ?></em>
			</p>
			<?php endif; ?>

			<div class="register-security-key">
				<input type="hidden" name="do_new_security_key" id="do_new_security_key" />
				<input type="hidden" name="u2f_response" id="u2f_response" />
				<button type="button" class="button button-secondary" id="register_security_key"><?php echo esc_html( _x( 'Register New Key', 'security key', 'two-factor' ) ); ?></button>
				<span class="spinner"></span>
				<span class="security-key-status"></span>
			</div>

			<?php if ( $new_key ) : ?>
			<div class="notice notice-success is-dismissible">
				<p class="new-security-key"><?php esc_html_e( 'Your new security key registered.', 'two-factor' ); ?></p>
			</div>
			<?php endif; ?>

			<p><a href="https://support.google.com/accounts/answer/6103523"><?php esc_html_e( 'You can find FIDO U2F Security Key devices for sale from here.', 'two-factor' ); ?></a></p>

			<?php
				require_once TWO_FACTOR_DIR . 'providers/class-two-factor-fido-u2f-admin-list-table.php';
				$u2f_list_table        = new Two_Factor_FIDO_U2F_Admin_List_Table();
				$u2f_list_table->items = $security_keys;
				$u2f_list_table->prepare_items();
				$u2f_list_table->display();
				$u2f_list_table->inline_edit();
			?>
		</div>
		<?php
	}

	/**
	 * Catch the non-ajax submission from the new form.
	 *
	 * This executes during the `personal_options_update` & `edit_user_profile_update` actions.
	 *
	 * @since 0.1-dev
	 *
	 * @access public
	 * @static
	 *
	 * @param int $user_id User ID.
	 * @return void|never
	 */
	public static function catch_submission( $user_id ) {
		if ( ! empty( $_REQUEST['do_new_security_key'] ) ) {
			check_admin_referer( "user_security_keys-{$user_id}", '_nonce_user_security_keys' );

			try {
				$response = json_decode( stripslashes( $_POST['u2f_response'] ) );
				$reg      = Two_Factor_FIDO_U2F::$u2f->doRegister( get_user_meta( $user_id, self::REGISTER_DATA_USER_META_KEY, true ), $response );
				$reg->new = true;

				Two_Factor_FIDO_U2F::add_security_key( $user_id, $reg );
			} catch ( Exception $e ) {
				return;
			}

			delete_user_meta( $user_id, self::REGISTER_DATA_USER_META_KEY );

			wp_safe_redirect(
				add_query_arg(
					array(
						'new_app_pass' => 1,
					),
					wp_get_referer()
				) . '#security-keys-section'
			);
			exit;
		}
	}

	/**
	 * Catch the delete security key request.
	 *
	 * This executes during the `load-profile.php` & `load-user-edit.php` actions.
	 *
	 * @since 0.1-dev
	 *
	 * @access public
	 * @static
	 */
	public static function catch_delete_security_key() {
		$user_id = Two_Factor_Core::current_user_being_edited();

		if ( ! empty( $user_id ) && ! empty( $_REQUEST['delete_security_key'] ) ) {
			$slug = $_REQUEST['delete_security_key'];

			check_admin_referer( "delete_security_key-{$slug}", '_nonce_delete_security_key' );

			Two_Factor_FIDO_U2F::delete_security_key( $user_id, $slug );

			wp_safe_redirect( remove_query_arg( 'new_app_pass', wp_get_referer() ) . '#security-keys-section' );
			exit;
		}
	}

	/**
	 * Generate a link to rename a specified security key.
	 *
	 * @since 0.1-dev
	 *
	 * @access public
	 * @static
	 *
	 * @param array $item The current item.
	 * @return string
	 */
	public static function rename_link( $item ) {
		return sprintf( '<a href="#" class="editinline">%s</a>', esc_html__( 'Rename', 'two-factor' ) );
	}

	/**
	 * Generate a link to delete a specified security key.
	 *
	 * @since 0.1-dev
	 *
	 * @access public
	 * @static
	 *
	 * @param array $item The current item.
	 * @return string
	 */
	public static function delete_link( $item ) {
		$delete_link = add_query_arg( 'delete_security_key', $item->keyHandle ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$delete_link = wp_nonce_url( $delete_link, "delete_security_key-{$item->keyHandle}", '_nonce_delete_security_key' );
		return sprintf( '<a href="%1$s">%2$s</a>', esc_url( $delete_link ), esc_html__( 'Delete', 'two-factor' ) );
	}

	/**
	 * Ajax handler for quick edit saving for a security key.
	 *
	 * @since 0.1-dev
	 *
	 * @access public
	 * @static
	 */
	public static function wp_ajax_inline_save() {
		check_ajax_referer( 'keyinlineeditnonce', '_inline_edit' );

		require_once TWO_FACTOR_DIR . 'providers/class-two-factor-fido-u2f-admin-list-table.php';
		$wp_list_table = new Two_Factor_FIDO_U2F_Admin_List_Table();

		if ( ! isset( $_POST['keyHandle'] ) ) {
			wp_die();
		}

		$user_id       = Two_Factor_Core::current_user_being_edited();
		$security_keys = Two_Factor_FIDO_U2F::get_security_keys( $user_id );
		if ( ! $security_keys ) {
			wp_die();
		}

		foreach ( $security_keys as &$key ) {
			if ( $key->keyHandle === $_POST['keyHandle'] ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				break;
			}
		}

		$key->name = $_POST['name'];

		$updated = Two_Factor_FIDO_U2F::update_security_key( $user_id, $key );
		if ( ! $updated ) {
			wp_die( esc_html__( 'Item not updated.', 'two-factor' ) );
		}
		$wp_list_table->single_row( $key );
		wp_die();
	}
}
