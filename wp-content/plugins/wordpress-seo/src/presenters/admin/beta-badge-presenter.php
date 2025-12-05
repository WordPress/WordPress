<?php

namespace Yoast\WP\SEO\Presenters\Admin;

use Yoast\WP\SEO\Presenters\Abstract_Presenter;

/**
 * Represents the presenter class for "Beta" badges.
 */
class Beta_Badge_Presenter extends Abstract_Presenter {

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
	 * Beta_Badge_Presenter constructor.
	 *
	 * @param string $id   Id of the badge.
	 * @param string $link Optional link of the badge.
	 */
	public function __construct( $id, $link = '' ) {
		$this->id   = $id;
		$this->link = $link;
	}

	/**
	 * Presents the Beta Badge. If a link has been passed, the badge is presented with the link.
	 * Otherwise a static badge is presented.
	 *
	 * @return string The styled Beta Badge.
	 */
	public function present() {
		if ( $this->link !== '' ) {
			return \sprintf(
				'<a class="yoast-badge yoast-badge__is-link yoast-beta-badge" id="%1$s-beta-badge" href="%2$s">%3$s</a>',
				\esc_attr( $this->id ),
				\esc_url( $this->link ),
				'Beta' // We don't want this string to be translatable.
			);
		}

		return \sprintf(
			'<span class="yoast-badge yoast-beta-badge" id="%1$s-beta-badge">%2$s</span>',
			\esc_attr( $this->id ),
			'Beta' // We don't want this string to be translatable.
		);
	}
}
