<?php

namespace Yoast\WP\SEO\Helpers\Schema;

use Yoast\WP\SEO\Config\Schema_IDs;
use Yoast\WP\SEO\Context\Meta_Tags_Context;

/**
 * Schema utility functions.
 */
class ID_Helper {

	/**
	 * Retrieve a users Schema ID.
	 *
	 * @param int               $user_id The ID of the User you need a Schema ID for.
	 * @param Meta_Tags_Context $context A value object with context variables.
	 *
	 * @return string The user's schema ID.
	 */
	public function get_user_schema_id( $user_id, $context ) {
		$user = \get_userdata( $user_id );
		if ( \is_a( $user, 'WP_User' ) ) {
			return $context->site_url . Schema_IDs::PERSON_HASH . \wp_hash( $user->user_login . $user_id );
		}

		return '';
	}
}
