<?php

namespace Yoast\WP\SEO\AI_Authorization\Infrastructure;

/**
 * Interface Refresh_Token_User_Meta_Repository_Interface
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
interface Refresh_Token_User_Meta_Repository_Interface extends Token_User_Meta_Repository_Interface {
	public const META_KEY = '_yoast_wpseo_ai_generator_refresh_jwt';
}
