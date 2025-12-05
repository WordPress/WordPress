<?php

namespace Yoast\WP\SEO\Generators\Schema;

use WP_User;
use Yoast\WP\SEO\Config\Schema_IDs;

/**
 * Returns schema Person data.
 */
class Person extends Abstract_Schema_Piece {

	/**
	 * Array of the social profiles we display for a Person.
	 *
	 * @var string[]
	 */
	private $social_profiles = [
		'facebook',
		'instagram',
		'linkedin',
		'pinterest',
		'twitter',
		'myspace',
		'youtube',
		'soundcloud',
		'tumblr',
		'wikipedia',
	];

	/**
	 * The Schema type we use for this class.
	 *
	 * @var string[]
	 */
	protected $type = [ 'Person', 'Organization' ];

	/**
	 * Determine whether we should return Person schema.
	 *
	 * @return bool
	 */
	public function is_needed() {
		// Using an author piece instead.
		if ( $this->site_represents_current_author() ) {
			return false;
		}

		return $this->context->site_represents === 'person';
	}

	/**
	 * Returns Person Schema data.
	 *
	 * @return bool|array<string|string[]> Person data on success, false on failure.
	 */
	public function generate() {
		$user_id = $this->determine_user_id();
		if ( ! $user_id ) {
			return false;
		}

		return $this->build_person_data( $user_id );
	}

	/**
	 * Determines a User ID for the Person data.
	 *
	 * @return bool|int User ID or false upon return.
	 */
	protected function determine_user_id() {
		/**
		 * Filter: 'wpseo_schema_person_user_id' - Allows filtering of user ID used for person output.
		 *
		 * @param int|bool $user_id The user ID currently determined.
		 */
		$user_id = \apply_filters( 'wpseo_schema_person_user_id', $this->context->site_user_id );

		// It should to be an integer higher than 0.
		if ( \is_int( $user_id ) && $user_id > 0 ) {
			return $user_id;
		}

		return false;
	}

	/**
	 * Retrieve a list of social profile URLs for Person.
	 *
	 * @param string[] $same_as_urls Array of SameAs URLs.
	 * @param int      $user_id      User ID.
	 *
	 * @return string[] A list of SameAs URLs.
	 */
	protected function get_social_profiles( $same_as_urls, $user_id ) {
		/**
		 * Filter: 'wpseo_schema_person_social_profiles' - Allows filtering of social profiles per user.
		 *
		 * @param string[] $social_profiles The array of social profiles to retrieve. Each should be a user meta field
		 *                                  key. As they are retrieved using the WordPress function `get_the_author_meta`.
		 * @param int      $user_id         The current user we're grabbing social profiles for.
		 */
		$social_profiles = \apply_filters( 'wpseo_schema_person_social_profiles', $this->social_profiles, $user_id );

		// We can only handle an array.
		if ( ! \is_array( $social_profiles ) ) {
			return $same_as_urls;
		}

		foreach ( $social_profiles as $profile ) {
			// Skip non-string values.
			if ( ! \is_string( $profile ) ) {
				continue;
			}

			$social_url = $this->url_social_site( $profile, $user_id );
			if ( $social_url ) {
				$same_as_urls[] = $social_url;
			}
		}

		return $same_as_urls;
	}

	/**
	 * Builds our array of Schema Person data for a given user ID.
	 *
	 * @param int  $user_id  The user ID to use.
	 * @param bool $add_hash Wether or not the person's image url hash should be added to the image id.
	 *
	 * @return array<string|string[]> An array of Schema Person data.
	 */
	protected function build_person_data( $user_id, $add_hash = false ) {
		$user_data = \get_userdata( $user_id );
		$data      = [
			'@type' => $this->type,
			'@id'   => $this->helpers->schema->id->get_user_schema_id( $user_id, $this->context ),
		];

		// Safety check for the `get_userdata` WP function, which could return false.
		if ( $user_data === false ) {
			return $data;
		}

		$data['name'] = $this->helpers->schema->html->smart_strip_tags( $user_data->display_name );

		$pronouns = $this->helpers->schema->html->smart_strip_tags( \get_the_author_meta( 'wpseo_pronouns', $user_id ) );
		if ( ! empty( $pronouns ) ) {
			$data['pronouns'] = $pronouns;
		}

		$data = $this->add_image( $data, $user_data, $add_hash );

		if ( ! empty( $user_data->description ) ) {
			$data['description'] = $this->helpers->schema->html->smart_strip_tags( $user_data->description );
		}

		if ( \is_array( $this->context->schema_page_type ) && \in_array( 'ProfilePage', $this->context->schema_page_type, true ) ) {
			$data['mainEntityOfPage'] = [
				'@id' => $this->context->main_schema_id,
			];
		}
		$data = $this->add_same_as_urls( $data, $user_data, $user_id );

		/**
		 * Filter: 'wpseo_schema_person_data' - Allows filtering of schema data per user.
		 *
		 * @param array $data    The schema data we have for this person.
		 * @param int   $user_id The current user we're collecting schema data for.
		 */
		$data = \apply_filters( 'wpseo_schema_person_data', $data, $user_id );

		return $data;
	}

	/**
	 * Returns an ImageObject for the persons avatar.
	 *
	 * @param array<string|string[]> $data      The Person schema.
	 * @param WP_User                $user_data User data.
	 * @param bool                   $add_hash  Wether or not the person's image url hash should be added to the image id.
	 *
	 * @return array<string|string[]> The Person schema.
	 */
	protected function add_image( $data, $user_data, $add_hash = false ) {
		$schema_id = $this->context->site_url . Schema_IDs::PERSON_LOGO_HASH;

		$data = $this->set_image_from_options( $data, $schema_id, $add_hash, $user_data );
		if ( ! isset( $data['image'] ) ) {
			$data = $this->set_image_from_avatar( $data, $user_data, $schema_id, $add_hash );
		}

		if ( \is_array( $this->type ) && \in_array( 'Organization', $this->type, true ) ) {
			$data_logo    = ( $data['image']['@id'] ?? $schema_id );
			$data['logo'] = [ '@id' => $data_logo ];
		}

		return $data;
	}

	/**
	 * Generate the person image from our settings.
	 *
	 * @param array<string|string[]> $data      The Person schema.
	 * @param string                 $schema_id The string used in the `@id` for the schema.
	 * @param bool                   $add_hash  Whether or not the person's image url hash should be added to the image id.
	 * @param WP_User|null           $user_data User data.
	 *
	 * @return array<string|string[]> The Person schema.
	 */
	protected function set_image_from_options( $data, $schema_id, $add_hash = false, $user_data = null ) {
		if ( $this->context->site_represents !== 'person' ) {
			return $data;
		}
		if ( \is_array( $this->context->person_logo_meta ) ) {
			$data['image'] = $this->helpers->schema->image->generate_from_attachment_meta( $schema_id, $this->context->person_logo_meta, $data['name'], $add_hash );
		}

		return $data;
	}

	/**
	 * Generate the person logo from gravatar.
	 *
	 * @param array<string|string[]> $data      The Person schema.
	 * @param WP_User                $user_data User data.
	 * @param string                 $schema_id The string used in the `@id` for the schema.
	 * @param bool                   $add_hash  Wether or not the person's image url hash should be added to the image id.
	 *
	 * @return array<string|string[]> The Person schema.
	 */
	protected function set_image_from_avatar( $data, $user_data, $schema_id, $add_hash = false ) {
		// If we don't have an image in our settings, fall back to an avatar, if we're allowed to.
		$show_avatars = \get_option( 'show_avatars' );
		if ( ! $show_avatars ) {
			return $data;
		}

		$url = \get_avatar_url( $user_data->user_email );
		if ( empty( $url ) ) {
			return $data;
		}

		$data['image'] = $this->helpers->schema->image->simple_image_object( $schema_id, $url, $user_data->display_name, $add_hash );

		return $data;
	}

	/**
	 * Returns an author's social site URL.
	 *
	 * @param string    $social_site The social site to retrieve the URL for.
	 * @param int|false $user_id     The user ID to use function outside of the loop.
	 *
	 * @return string
	 */
	protected function url_social_site( $social_site, $user_id = false ) {
		$url = \get_the_author_meta( $social_site, $user_id );

		if ( ! empty( $url ) && $social_site === 'twitter' ) {
			$url = 'https://x.com/' . $url;
		}

		return $url;
	}

	/**
	 * Checks the site is represented by the same person as this indexable.
	 *
	 * @param WP_User|null $user_data User data.
	 *
	 * @return bool True when the site is represented by the same person as this indexable.
	 */
	protected function site_represents_current_author( $user_data = null ) {
		// Can only be the case when the site represents a user.
		if ( $this->context->site_represents !== 'person' ) {
			return false;
		}

		// Article post from the same user as the site represents.
		if (
			$this->context->indexable->object_type === 'post'
			&& $this->helpers->schema->article->is_author_supported( $this->context->indexable->object_sub_type )
			&& $this->context->schema_article_type !== 'None'
		) {
			$user_id = ( $user_data instanceof WP_User && isset( $user_data->ID ) ) ? $user_data->ID : $this->context->indexable->author_id;

			return $this->context->site_user_id === $user_id;
		}

		// Author archive from the same user as the site represents.
		return $this->context->indexable->object_type === 'user' && $this->context->site_user_id === $this->context->indexable->object_id;
	}

	/**
	 * Builds our SameAs array.
	 *
	 * @param array<string|string[]> $data      The Person schema data.
	 * @param WP_User                $user_data The user data object.
	 * @param int                    $user_id   The user ID to use.
	 *
	 * @return array<string|string[]> The Person schema data.
	 */
	protected function add_same_as_urls( $data, $user_data, $user_id ) {
		$same_as_urls = [];

		// Add the "Website" field from WordPress' contact info.
		if ( ! empty( $user_data->user_url ) ) {
			$same_as_urls[] = $user_data->user_url;
		}

		// Add the social profiles.
		$same_as_urls = $this->get_social_profiles( $same_as_urls, $user_id );

		if ( ! empty( $same_as_urls ) ) {
			$same_as_urls   = \array_values( \array_unique( $same_as_urls ) );
			$data['sameAs'] = $same_as_urls;
		}

		return $data;
	}
}
