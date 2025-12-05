<?php

namespace Yoast\WP\SEO\Presenters\Admin;

use Yoast\WP\SEO\Config\Badge_Group_Names;
use Yoast\WP\SEO\Presenters\Abstract_Presenter;

/**
 * Represents the presenter class for "New" badges.
 */
class Badge_Presenter extends Abstract_Presenter {

	/**
	 * Identifier of the badge.
	 *
	 * @var string
	 */
	private $id;

	/**
	 * Optional link of the badge.
	 *
	 * @var string
	 */
	private $link;

	/**
	 * Optional group which the badge belongs to.
	 *
	 * Each group has a fixed period after which the group will no longer be considered new and the badges will disappear.
	 *
	 * @var string
	 */
	private $group;

	/**
	 * Optional object storing the group names and expiration versions.
	 *
	 * The group names set in Yoast SEO are used by default, but they can be overridden to use custom ones for an add-on.
	 *
	 * @var Badge_Group_Names
	 */
	private $badge_group_names;

	/**
	 * Badge_Presenter constructor.
	 *
	 * @param string                 $id                Id of the badge.
	 * @param string                 $link              Optional link of the badge.
	 * @param string                 $group             Optional group which the badge belongs to.
	 * @param Badge_Group_Names|null $badge_group_names Optional object storing the group names.
	 */
	public function __construct( $id, $link = '', $group = '', $badge_group_names = null ) {
		$this->id    = $id;
		$this->link  = $link;
		$this->group = $group;

		if ( ! $badge_group_names instanceof Badge_Group_Names ) {
			$badge_group_names = new Badge_Group_Names();
		}
		$this->badge_group_names = $badge_group_names;
	}

	/**
	 * Presents the New Badge. If a link has been passed, the badge is presented with the link.
	 * Otherwise a static badge is presented.
	 *
	 * @return string The styled New Badge.
	 */
	public function present() {
		if ( ! $this->is_group_still_new() ) {
			return '';
		}

		if ( $this->link !== '' ) {
			return \sprintf(
				'<a class="yoast-badge yoast-badge__is-link yoast-new-badge" id="%1$s-new-badge" href="%2$s">%3$s</a>',
				\esc_attr( $this->id ),
				\esc_url( $this->link ),
				\esc_html__( 'New', 'wordpress-seo' )
			);
		}

		return \sprintf(
			'<span class="yoast-badge yoast-new-badge" id="%1$s-new-badge">%2$s</span>',
			\esc_attr( $this->id ),
			\esc_html__( 'New', 'wordpress-seo' )
		);
	}

	/**
	 * Check whether the new badge should be shown according to the group it is in.
	 *
	 * @return bool True if still new.
	 */
	public function is_group_still_new() {
		// If there's no group configured, the new badge is always active.
		if ( ! $this->group ) {
			return true;
		}

		return $this->badge_group_names->is_still_eligible_for_new_badge( $this->group );
	}
}
