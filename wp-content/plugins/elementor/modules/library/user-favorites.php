<?php
namespace Elementor\Modules\Library;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class User_Favorites {
	const USER_META_KEY = 'elementor_library_favorites';

	/**
	 * @var int
	 */
	private $user_id;

	/**
	 * @var array|null
	 */
	private $cache;

	/**
	 * User_Favorites constructor.
	 *
	 * @param $user_id
	 */
	public function __construct( $user_id ) {
		$this->user_id = $user_id;
	}

	/**
	 * @param null  $vendor
	 * @param null  $resource_name
	 * @param false $ignore_cache
	 *
	 * @return array
	 */
	public function get( $vendor = null, $resource_name = null, $ignore_cache = false ) {
		if ( $ignore_cache || empty( $this->cache ) ) {
			$this->cache = get_user_meta( $this->user_id, self::USER_META_KEY, true );
		}

		if ( ! $this->cache || ! is_array( $this->cache ) ) {
			return [];
		}

		if ( $vendor && $resource_name ) {
			$key = $this->get_key( $vendor, $resource_name );

			return isset( $this->cache[ $key ] ) ? $this->cache[ $key ] : [];
		}

		return $this->cache;
	}

	/**
	 * @param $vendor
	 * @param $resource_name
	 * @param $id
	 *
	 * @return bool
	 */
	public function exists( $vendor, $resource_name, $id ) {
		return in_array( $id, $this->get( $vendor, $resource_name ), true );
	}

	/**
	 * @param       $vendor
	 * @param       $resource_name
	 * @param array $value
	 *
	 * @return $this
	 * @throws \Exception If the favorites cannot be saved.
	 */
	public function save( $vendor, $resource_name, $value = [] ) {
		$all_favorites = $this->get();

		$all_favorites[ $this->get_key( $vendor, $resource_name ) ] = $value;

		$result = update_user_meta( $this->user_id, self::USER_META_KEY, $all_favorites );

		if ( false === $result ) {
			throw new \Exception( 'Failed to save user favorites.' );
		}

		$this->cache = $all_favorites;

		return $this;
	}

	/**
	 * @param $vendor
	 * @param $resource_name
	 * @param $id
	 *
	 * @return $this
	 * @throws \Exception If the favorites cannot be added.
	 */
	public function add( $vendor, $resource_name, $id ) {
		$favorites = $this->get( $vendor, $resource_name );

		if ( in_array( $id, $favorites, true ) ) {
			return $this;
		}

		$favorites[] = $id;

		$this->save( $vendor, $resource_name, $favorites );

		return $this;
	}

	/**
	 * @param $vendor
	 * @param $resource_name
	 * @param $id
	 *
	 * @return $this
	 * @throws \Exception If the favorites cannot be removed.
	 */
	public function remove( $vendor, $resource_name, $id ) {
		$favorites = $this->get( $vendor, $resource_name );

		if ( ! in_array( $id, $favorites, true ) ) {
			return $this;
		}

		$favorites = array_filter( $favorites, function ( $item ) use ( $id ) {
			return $item !== $id;
		} );

		$this->save( $vendor, $resource_name, $favorites );

		return $this;
	}

	/**
	 * @param $vendor
	 * @param $resource_name
	 *
	 * @return string
	 */
	private function get_key( $vendor, $resource_name ) {
		return "{$vendor}/{$resource_name}";
	}
}
