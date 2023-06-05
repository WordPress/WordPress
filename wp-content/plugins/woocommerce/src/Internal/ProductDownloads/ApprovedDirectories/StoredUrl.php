<?php

namespace Automattic\WooCommerce\Internal\ProductDownloads\ApprovedDirectories;

/**
 * Representation of an approved directory URL, bundling the ID and URL in a single entity.
 */
class StoredUrl {
	/**
	 * The approved directory ID.
	 *
	 * @var int
	 */
	private $id;

	/**
	 * The approved directory URL.
	 *
	 * @var string
	 */
	private $url;

	/**
	 * If the individual rule is enabled or disabled.
	 *
	 * @var bool
	 */
	private $enabled;

	/**
	 * Sets up the approved directory rule.
	 *
	 * @param int    $id      The approved directory ID.
	 * @param string $url     The approved directory URL.
	 * @param bool   $enabled Indicates if the approved directory rule is enabled.
	 */
	public function __construct( int $id, string $url, bool $enabled ) {
		$this->id      = $id;
		$this->url     = $url;
		$this->enabled = $enabled;
	}

	/**
	 * Supplies the ID of the approved directory.
	 *
	 * @return int
	 */
	public function get_id(): int {
		return $this->id;
	}

	/**
	 * Supplies the approved directory URL.
	 *
	 * @return string
	 */
	public function get_url(): string {
		return $this->url;
	}

	/**
	 * Indicates if this rule is enabled or not (rules can be temporarily disabled).
	 *
	 * @return bool
	 */
	public function is_enabled(): bool {
		return $this->enabled;
	}
}
