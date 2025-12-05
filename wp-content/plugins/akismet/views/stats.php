<div id="akismet-plugin-container">
	<div class="akismet-masthead">
		<div class="akismet-masthead__inside-container">
			<?php Akismet::view( 'logo', array( 'include_logo_link' => true ) ); ?>
			<div class="akismet-masthead__back-link-container">
				<a class="akismet-masthead__back-link" href="<?php echo esc_url( Akismet_Admin::get_page_url() ); ?>"><?php esc_html_e( 'Back to settings', 'akismet' ); ?></a>
			</div>
		</div>
	</div>
	<?php /* name attribute on iframe is used as a cache-buster here to force Firefox to load the new style charts: https://bugzilla.mozilla.org/show_bug.cgi?id=356558 */ ?>
	<iframe id="stats-iframe" src="<?php echo esc_url( sprintf( 'https://tools.akismet.com/1.0/user-stats.php?blog=%s&token=%s&locale=%s&is_redecorated=1', urlencode( get_option( 'home' ) ), urlencode( Akismet::get_access_token() ), esc_attr( get_user_locale() ) ) ); ?>" name="<?php echo esc_attr( 'user-stats- ' . filemtime( __FILE__ ) ); ?>" width="100%" height="2500px" frameborder="0" title="<?php echo esc_attr__( 'Akismet detailed stats', 'akismet' ); ?>"></iframe>
</div>
