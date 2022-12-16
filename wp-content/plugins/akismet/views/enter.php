<div class="akismet-enter-api-key-box centered">
	<a href="#"><?php esc_html_e( 'Manually enter an API key', 'akismet' ); ?></a>
	<div class="enter-api-key">
		<form action="<?php echo esc_url( Akismet_Admin::get_page_url() ); ?>" method="post">
			<?php wp_nonce_field( Akismet_Admin::NONCE ) ?>
			<input type="hidden" name="action" value="enter-key">
			<p style="width: 100%; display: flex; flex-wrap: nowrap; box-sizing: border-box;">
				<input id="key" name="key" type="text" size="15" value="" placeholder="<?php esc_attr_e( 'Enter your API key' , 'akismet' ); ?>" class="regular-text code" style="flex-grow: 1; margin-right: 1rem;">
				<input type="submit" name="submit" id="submit" class="akismet-button" value="<?php esc_attr_e( 'Connect with API key', 'akismet' );?>">
			</p>
		</form>
	</div>
</div>