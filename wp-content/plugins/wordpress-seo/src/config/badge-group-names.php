<?php

namespace Yoast\WP\SEO\Config;

/**
 * Class Badge_Group_Names.
 *
 * This class defines groups for "new" badges, with the version in which those groups are no longer considered
 * to be "new".
 */
class Badge_Group_Names {

	public const GROUP_GLOBAL_TEMPLATES = 'global-templates';

	/**
	 * Constant describing when certain groups of new badges will no longer be shown.
	 */
	public const GROUP_NAMES = [
		self::GROUP_GLOBAL_TEMPLATES => '16.7-beta0',
	];

	/**
	 * The current plugin version.
	 *
	 * @var string
	 */
	protected $version;

	/**
	 * Badge_Group_Names constructor.
	 *
	 * @param string|null $version Optional: the current plugin version.
	 */
	public function __construct( $version = null ) {
		if ( ! $version ) {
			$version = \WPSEO_VERSION;
		}
		$this->version = $version;
	}

	/**
	 * Check whether a group of badges is still eligible for a "new" badge.
	 *
	 * @param string      $group           One of the GROUP_* constants.
	 * @param string|null $current_version The current version of the plugin that's being checked.
	 *
	 * @return bool Whether a group of badges is still eligible for a "new" badge.
	 */
	public function is_still_eligible_for_new_badge( $group, $current_version = null ) {
		if ( ! \array_key_exists( $group, $this::GROUP_NAMES ) ) {
			return false;
		}

		$group_version = $this::GROUP_NAMES[ $group ];

		if ( $current_version === null ) {
			$current_version = $this->version;
		}

		return (bool) \version_compare( $group_version, $current_version, '>' );
	}
}
