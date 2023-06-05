<?php
/**
 * Represents a marketing channel for the multichannel-marketing feature.
 *
 * This interface will be implemented by third-party extensions to register themselves as marketing channels.
 */

namespace Automattic\WooCommerce\Admin\Marketing;

/**
 * MarketingChannelInterface interface
 *
 * @since x.x.x
 */
interface MarketingChannelInterface {
	public const PRODUCT_LISTINGS_NOT_APPLICABLE   = 'not-applicable';
	public const PRODUCT_LISTINGS_SYNC_IN_PROGRESS = 'sync-in-progress';
	public const PRODUCT_LISTINGS_SYNC_FAILED      = 'sync-failed';
	public const PRODUCT_LISTINGS_SYNCED           = 'synced';

	/**
	 * Returns the unique identifier string for the marketing channel extension, also known as the plugin slug.
	 *
	 * @return string
	 */
	public function get_slug(): string;

	/**
	 * Returns the name of the marketing channel.
	 *
	 * @return string
	 */
	public function get_name(): string;

	/**
	 * Returns the description of the marketing channel.
	 *
	 * @return string
	 */
	public function get_description(): string;

	/**
	 * Returns the path to the channel icon.
	 *
	 * @return string
	 */
	public function get_icon_url(): string;

	/**
	 * Returns the setup status of the marketing channel.
	 *
	 * @return bool
	 */
	public function is_setup_completed(): bool;

	/**
	 * Returns the URL to the settings page, or the link to complete the setup/onboarding if the channel has not been set up yet.
	 *
	 * @return string
	 */
	public function get_setup_url(): string;

	/**
	 * Returns the status of the marketing channel's product listings.
	 *
	 * @return string
	 */
	public function get_product_listings_status(): string;

	/**
	 * Returns the number of channel issues/errors (e.g. account-related errors, product synchronization issues, etc.).
	 *
	 * @return int The number of issues to resolve, or 0 if there are no issues with the channel.
	 */
	public function get_errors_count(): int;

	/**
	 * Returns an array of marketing campaign types that the channel supports.
	 *
	 * @return MarketingCampaignType[] Array of marketing campaign type objects.
	 */
	public function get_supported_campaign_types(): array;

	/**
	 * Returns an array of the channel's marketing campaigns.
	 *
	 * @return MarketingCampaign[]
	 */
	public function get_campaigns(): array;
}
