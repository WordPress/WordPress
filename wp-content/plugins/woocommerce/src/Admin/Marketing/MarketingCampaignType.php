<?php
/**
 * Represents a marketing campaign type supported by a marketing channel.
 *
 * Marketing channels (implementing MarketingChannelInterface) can use this class to define what kind of campaigns they support.
 */

namespace Automattic\WooCommerce\Admin\Marketing;

/**
 * MarketingCampaignType class
 *
 * @since x.x.x
 */
class MarketingCampaignType {
	/**
	 * The unique identifier.
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * The marketing channel that this campaign type belongs to.
	 *
	 * @var MarketingChannelInterface
	 */
	protected $channel;

	/**
	 * Name of the marketing campaign type.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Description of the marketing campaign type.
	 *
	 * @var string
	 */
	protected $description;

	/**
	 * The URL to the create campaign page.
	 *
	 * @var string
	 */
	protected $create_url;

	/**
	 * The URL to an image/icon for the campaign type.
	 *
	 * @var string
	 */
	protected $icon_url;

	/**
	 * MarketingCampaignType constructor.
	 *
	 * @param string                    $id          A unique identifier for the campaign type.
	 * @param MarketingChannelInterface $channel     The marketing channel that this campaign type belongs to.
	 * @param string                    $name        Name of the marketing campaign type.
	 * @param string                    $description Description of the marketing campaign type.
	 * @param string                    $create_url  The URL to the create campaign page.
	 * @param string                    $icon_url    The URL to an image/icon for the campaign type.
	 */
	public function __construct( string $id, MarketingChannelInterface $channel, string $name, string $description, string $create_url, string $icon_url ) {
		$this->id          = $id;
		$this->channel     = $channel;
		$this->name        = $name;
		$this->description = $description;
		$this->create_url  = $create_url;
		$this->icon_url    = $icon_url;
	}

	/**
	 * Returns the marketing campaign's unique identifier.
	 *
	 * @return string
	 */
	public function get_id(): string {
		return $this->id;
	}

	/**
	 * Returns the marketing channel that this campaign type belongs to.
	 *
	 * @return MarketingChannelInterface
	 */
	public function get_channel(): MarketingChannelInterface {
		return $this->channel;
	}

	/**
	 * Returns the name of the marketing campaign type.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * Returns the description of the marketing campaign type.
	 *
	 * @return string
	 */
	public function get_description(): string {
		return $this->description;
	}

	/**
	 * Returns the URL to the create campaign page.
	 *
	 * @return string
	 */
	public function get_create_url(): string {
		return $this->create_url;
	}

	/**
	 * Returns the URL to an image/icon for the campaign type.
	 *
	 * @return string
	 */
	public function get_icon_url(): string {
		return $this->icon_url;
	}
}
