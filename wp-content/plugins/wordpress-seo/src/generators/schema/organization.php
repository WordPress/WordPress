<?php

namespace Yoast\WP\SEO\Generators\Schema;

use Yoast\WP\SEO\Config\Schema_IDs;

/**
 * Returns schema Organization data.
 */
class Organization extends Abstract_Schema_Piece {

	/**
	 * Determines whether an Organization graph piece should be added.
	 *
	 * @return bool
	 */
	public function is_needed() {
		return $this->context->site_represents === 'company';
	}

	/**
	 * Returns the Organization Schema data.
	 *
	 * @return array The Organization schema.
	 */
	public function generate() {
		$logo_schema_id = $this->context->site_url . Schema_IDs::ORGANIZATION_LOGO_HASH;

		if ( $this->context->company_logo_meta ) {
			$logo = $this->helpers->schema->image->generate_from_attachment_meta( $logo_schema_id, $this->context->company_logo_meta, $this->context->company_name );
		}
		else {
			$logo = $this->helpers->schema->image->generate_from_attachment_id( $logo_schema_id, $this->context->company_logo_id, $this->context->company_name );
		}

		$organization = [
			'@type' => 'Organization',
			'@id'   => $this->context->site_url . Schema_IDs::ORGANIZATION_HASH,
			'name'  => $this->helpers->schema->html->smart_strip_tags( $this->context->company_name ),
		];

		if ( ! empty( $this->context->company_alternate_name ) ) {
			$organization['alternateName'] = $this->context->company_alternate_name;
		}

		$organization['url']   = $this->context->site_url;
		$organization['logo']  = $logo;
		$organization['image'] = [ '@id' => $logo['@id'] ];

		$same_as = \array_values( \array_unique( \array_filter( $this->fetch_social_profiles() ) ) );
		if ( ! empty( $same_as ) ) {
			$organization['sameAs'] = $same_as;
		}

		if ( \is_array( $this->context->schema_page_type ) && \in_array( 'ProfilePage', $this->context->schema_page_type, true ) ) {
			$organization['mainEntityOfPage'] = [
				'@id' => $this->context->main_schema_id,
			];
		}

		return $organization;
	}

	/**
	 * Retrieve the social profiles to display in the organization schema.
	 *
	 * @return array An array of social profiles.
	 */
	private function fetch_social_profiles() {
		$profiles = $this->helpers->social_profiles->get_organization_social_profiles();

		if ( isset( $profiles['other_social_urls'] ) ) {
			$other_social_urls = $profiles['other_social_urls'];
			unset( $profiles['other_social_urls'] );
			$profiles = \array_merge( $profiles, $other_social_urls );
		}

		/**
		 * Filter: 'wpseo_schema_organization_social_profiles' - Allows filtering social profiles for the
		 * represented organization.
		 *
		 * @param string[] $profiles
		 */
		$profiles = \apply_filters( 'wpseo_schema_organization_social_profiles', $profiles );

		return $profiles;
	}
}
