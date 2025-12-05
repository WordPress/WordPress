<?php

namespace Yoast\WP\SEO\Config;

/**
 * Class Indexing_Reasons. Contains constants that aren't context specific.
 */
class Indexing_Reasons {

	/**
	 * Represents the reason that the indexing process failed and should be tried again.
	 */
	public const REASON_INDEXING_FAILED = 'indexing_failed';

	/**
	 * Represents the reason that the permalink settings are changed.
	 */
	public const REASON_PERMALINK_SETTINGS = 'permalink_settings_changed';

	/**
	 * Represents the reason that the category base is changed.
	 */
	public const REASON_CATEGORY_BASE_PREFIX = 'category_base_changed';

	/**
	 * Represents the reason that the tag base is changed.
	 */
	public const REASON_TAG_BASE_PREFIX = 'tag_base_changed';

	/**
	 * Represents the reason that the home url option is changed.
	 */
	public const REASON_HOME_URL_OPTION = 'home_url_option_changed';

	/**
	 * Represents the reason that a post type has been made public.
	 */
	public const REASON_POST_TYPE_MADE_PUBLIC = 'post_type_made_public';

	/**
	 * Represents the reason that a post type has been made viewable.
	 */
	public const REASON_TAXONOMY_MADE_PUBLIC = 'taxonomy_made_public';

	/**
	 * Represents the reason that attachments have stopped being redirected.
	 */
	public const REASON_ATTACHMENTS_MADE_ENABLED = 'attachments_made_enabled';
}
