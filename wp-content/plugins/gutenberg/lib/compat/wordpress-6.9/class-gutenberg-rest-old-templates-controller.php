<?php

class Gutenberg_REST_Old_Templates_Controller extends WP_REST_Templates_Controller {
	public function get_template_fallback( $request ) {
		// Check active_templates experiment status.
		if ( ! gutenberg_is_experiment_enabled( 'active_templates' ) ) {
			return parent::get_template_fallback( $request );
		}
		$hierarchy = get_template_hierarchy( $request['slug'], $request['is_custom'], $request['template_prefix'] );

		do {
			$fallback_template = gutenberg_resolve_block_template( $request['slug'], $hierarchy, '' );
			array_shift( $hierarchy );
		} while ( ! empty( $hierarchy ) && empty( $fallback_template->content ) );

		// To maintain original behavior, return an empty object rather than a 404 error when no template is found.
		$response = $fallback_template ? $this->prepare_item_for_response( $fallback_template, $request ) : new stdClass();

		return rest_ensure_response( $response );
	}
}
