<div class="akismet-enter-api-key-box centered">
	<button class="akismet-enter-api-key-box__reveal"><?php esc_html_e( 'Manually enter an API key', 'akismet' ); ?></button>
	<div class="akismet-enter-api-key-box__form-wrapper">
		<form action="<?php echo esc_url( Akismet_Admin::get_page_url() ); ?>" method="post">
			<?php wp_nonce_field( Akismet_Admin::NONCE ); ?>
			<input type="hidden" name="action" value="enter-key">
			<h3 class="akismet-enter-api-key-box__header" id="akismet-enter-api-key-box__header"><?php esc_html_e( 'Enter your API key', 'akismet' ); ?></h3>
			<div class="akismet-enter-api-key-box__input-wrapper">
				<input id="key" name="key" type="text" size="15" value="" placeholder="<?php esc_attr_e( 'API key', 'akismet' ); ?>" class="akismet-enter-api-key-box__key-input regular-text code" aria-labelledby="akismet-enter-api-key-box__header">
				<input type="submit" name="submit" id="submit" class="akismet-button" value="<?php esc_attr_e( 'Connect with API key', 'akismet' ); ?>">
			</div>
		</form>
	</div>
</div>
