<?php
	list( $instagram_client_id, $instagram_client_secret, $instagram_redirect_uri )
			= $this->get_instagram_settings();

	$logout_requested = false;
	if ( isset( $_POST['instagram-logout'] )
			&& check_admin_referer( 'ei_user_logout_nonce', 'ei_user_logout_nonce' ) ) {
		$this->set_access_token( '' );
		update_option( 'ei_access_token', '' );
		$logout_requested = true;
	}

	$config = $this->get_instagram_config();
	$cache_dir = $this->get_cache_directory();
	$instagram = new MC_Instagram_Connector( $config );
	$access_token = $this->get_access_token();
	$instagram_exception = null;

	if ( ! $logout_requested && empty( $access_token ) ) {
		if ( isset( $_GET['code'] ) ) {
			try {
				$access_token = $instagram->getAccessToken();
				if ( ! empty( $access_token ) ) {
					$this->set_access_token( $access_token );
				}

				$instagram_user = $instagram->getCurrentUser();
				if ( ! empty( $instagram_user ) ) {
					$this->set_instagram_user_data( $instagram_user->username, $instagram_user->id );
				}
			} catch ( Exception $ex ) {
				$instagram_exception = $ex;
			}
		}
	}
?>


<div id="icon-options-general" class="icon32"></div>

	<h2><?php _e( 'Easy Instagram', 'Easy_Instagram' ) ?></h2>
	<h2 class='ei-nav-tab-wrapper'>
	<a href='#' class='ei-nav-tab ei-nav-tab-active' id='ei-select-general-settings'><?php _e( 'Plugin Settings', 'Easy_Instagram' ); ?></a>
	<a href='#' class='ei-nav-tab' id='ei-select-help'><?php _e( 'Help', 'Easy_Instagram' ); ?></a>
	</h2>

<div class="wrapper">
	<div id='ei-general-settings'>
	<form method='POST' action="options.php" class='easy-instagram-settings-form'>

		<table class='easy-instagram-settings'>
		<?php settings_fields( 'easy_instagram_group' ); ?>
		<?php do_settings_sections( 'easy_instagram_general' ); ?>
		<?php submit_button(); ?>
		</table>
	</form>

	<form method='POST' action='' class='easy-instagram-settings-form'>
		<table class='easy-instagram-settings'>
		<?php if ( empty( $access_token ) ) : ?>
			<tr>
				<td colspan='2'><h3><?php _e( 'Instagram Account', 'Easy_Instagram' ); ?></h3></td>
			</tr>

			<tr>
				<td>
				<?php if ( !empty( $instagram_client_id )
						&& !empty( $instagram_client_secret )
						&& ! empty( $instagram_redirect_uri ) ): ?>
						<?php $authorization_url = $instagram->getAuthorizationUrl(); ?>
						<a href="<?php echo $authorization_url;?>"><?php _e( 'Instagram Login', 'Easy_Instagram' );?></a>
					<?php else: ?>
						<?php _e( 'Please configure the General Settings first', 'Easy_Instagram' ); ?>
					<?php endif; ?>
				</td>
				<td>
				</td>
			</tr>
		<?php else: ?>
			<?php list( $username, $user_id ) = $this->get_instagram_user_data(); ?>
				<tr>
					<td colspan='2'><h3><?php _e( 'Instagram Account', 'Easy_Instagram' ); ?></h3></td>
				</tr>
				<tr>
					<td class='labels'>
						<label><?php _e( 'Instagram Username', 'Easy_Instagram' ); ?></label>
					</td>
					<td>
						<?php echo $username; ?>
					</td>
				</tr>

				<tr>
					<td class='labels'>
						<label><?php _e( 'Instagram User ID', 'Easy_Instagram' ); ?></label>
					</td>
					<td>
						<?php echo $user_id; ?>
					</td>
				</tr>

				<tr>
					<td>
						<?php wp_nonce_field( 'ei_user_logout_nonce', 'ei_user_logout_nonce' ); ?>
					</td>
					<td>
						<input type='submit' name='instagram-logout' value="<?php _e( 'Instagram Logout', 'Easy_Instagram' );?>" />
					</td>
				</tr>
		<?php endif; ?>
		<?php if ( ! is_null( $instagram_exception ) ): ?>
			<tr>
				<td colspan='2' class='exception'>
					<?php echo $instagram_exception->getMessage(); ?>
				</td>
			</tr>
			<?php endif; ?>
		</table>
	</form>

	</div> <?php /* ei-general-setings */ ?>

	<div id='ei-help'>
		<?php do_settings_sections( 'easy_instagram_help' ); ?>
	</div>
</div>	