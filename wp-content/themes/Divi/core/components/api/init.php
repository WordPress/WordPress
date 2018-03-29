<?php

if ( ! function_exists( 'et_core_api_init' ) ):
function et_core_api_init() {
	add_action( 'admin_init', array( 'ET_Core_API_OAuthHelper', 'finish_oauth2_authorization' ), 20 );
}
endif;
