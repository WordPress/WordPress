<?php

//phpcs:disable VariableAnalysis
// There are "undefined" variables here because they're defined in the code that includes this file as a template.

?>
<div class="akismet-box">
	<?php Akismet::view( 'title' ); ?>
	<div class="akismet-jp-connect">
		<h3><?php esc_html_e( 'Connect with Jetpack', 'akismet' ); ?></h3>
		<?php if ( in_array( $akismet_user->status, array( 'no-sub', 'missing' ) ) ) { ?>
			<p><?php esc_html_e( 'Use your Jetpack connection to set up Akismet.', 'akismet' ); ?></p>
			<form name="akismet_activate" id="akismet_activate" action="https://akismet.com/get/" method="post" class="akismet-right" target="_blank">
				<input type="hidden" name="passback_url" value="<?php echo esc_url( Akismet_Admin::get_page_url() ); ?>"/>
				<input type="hidden" name="blog" value="<?php echo esc_url( get_option( 'home' ) ); ?>"/>
				<input type="hidden" name="auto-connect" value="<?php echo esc_attr( $akismet_user->ID ); ?>"/>
				<input type="hidden" name="redirect" value="plugin-signup"/>
				<input type="submit" class="akismet-button akismet-is-primary" value="<?php esc_attr_e( 'Connect with Jetpack', 'akismet' ); ?>"/>
			</form>
			<?php echo get_avatar( $akismet_user->user_email, null, null, null, array( 'class' => 'akismet-jetpack-gravatar' ) ); ?>
			<p>
				<?php

				/* translators: %s is the WordPress.com username */
				printf( esc_html( __( 'You are connected as %s.', 'akismet' ) ), '<b>' . esc_html( $akismet_user->user_login ) . '</b>' );

				?>
				<br />
				<span class="akismet-jetpack-email"><?php echo esc_html( $akismet_user->user_email ); ?></span>
			</p>
		<?php } elseif ( $akismet_user->status == 'cancelled' ) { ?>
			<p><?php esc_html_e( 'Use your Jetpack connection to set up Akismet.', 'akismet' ); ?></p>
			<form name="akismet_activate" id="akismet_activate" action="https://akismet.com/get/" method="post" class="akismet-right" target="_blank">
				<input type="hidden" name="passback_url" value="<?php echo esc_url( Akismet_Admin::get_page_url() ); ?>"/>
				<input type="hidden" name="blog" value="<?php echo esc_url( get_option( 'home' ) ); ?>"/>
				<input type="hidden" name="user_id" value="<?php echo esc_attr( $akismet_user->ID ); ?>"/>
				<input type="hidden" name="redirect" value="upgrade"/>
				<input type="submit" class="akismet-button akismet-is-primary" value="<?php esc_attr_e( 'Connect with Jetpack', 'akismet' ); ?>"/>
			</form>
			<?php echo get_avatar( $akismet_user->user_email, null, null, null, array( 'class' => 'akismet-jetpack-gravatar' ) ); ?>
			<p>
				<?php

				/* translators: %s is the WordPress.com email address */
				echo esc_html( sprintf( __( 'Your subscription for %s is cancelled.', 'akismet' ), $akismet_user->user_email ) );

				?>
				<br />
				<span class="akismet-jetpack-email"><?php echo esc_html( $akismet_user->user_email ); ?></span>
			</p>
		<?php } elseif ( $akismet_user->status == 'suspended' ) { ?>
			<div class="akismet-right">
				<p><a href="https://akismet.com/contact" class="akismet-button akismet-is-primary"><?php esc_html_e( 'Contact Akismet support', 'akismet' ); ?></a></p>
			</div>
			<p>
				<span class="akismet-alert-text">
					<?php

					/* translators: %s is the WordPress.com email address */
					echo esc_html( sprintf( __( 'Your subscription for %s is suspended.', 'akismet' ), $akismet_user->user_email ) );

					?>
				</span>
				<?php esc_html_e( 'No worries! Get in touch and we&#8217;ll sort this out.', 'akismet' ); ?>
			</p>
		<?php } else { // ask do they want to use akismet account found using jetpack wpcom connection ?>
			<p><?php esc_html_e( 'Use your Jetpack connection to set up Akismet.', 'akismet' ); ?></p>
			<form name="akismet_use_wpcom_key" action="<?php echo esc_url( Akismet_Admin::get_page_url() ); ?>" method="post" id="akismet-activate" class="akismet-right">
				<input type="hidden" name="key" value="<?php echo esc_attr( $akismet_user->api_key ); ?>"/>
				<input type="hidden" name="action" value="enter-key">
				<?php wp_nonce_field( Akismet_Admin::NONCE ); ?>
				<input type="submit" class="akismet-button akismet-is-primary" value="<?php esc_attr_e( 'Connect with Jetpack', 'akismet' ); ?>"/>
			</form>
			<?php echo get_avatar( $akismet_user->user_email, null, null, null, array( 'class' => 'akismet-jetpack-gravatar' ) ); ?>
			<p>
				<?php

				/* translators: %s is the WordPress.com username */
				printf( esc_html( __( 'You are connected as %s.', 'akismet' ) ), '<b>' . esc_html( $akismet_user->user_login ) . '</b>' );

				?>
				<br />
				<span class="akismet-jetpack-email"><?php echo esc_html( $akismet_user->user_email ); ?></span>
			</p>
		<?php } ?>
	</div>
	<div class="akismet-ak-connect">
		<?php Akismet::view( 'setup' ); ?>
	</div>
	<div class="centered akismet-toggles">
		<a href="#" class="toggle-jp-connect"><?php esc_html_e( 'Connect with Jetpack', 'akismet' ); ?></a>
		<a href="#" class="toggle-ak-connect"><?php esc_html_e( 'Set up a different account', 'akismet' ); ?></a>
	</div>
</div>
<br/>
<div class="akismet-box">
	<?php Akismet::view( 'enter' ); ?>
</div>
