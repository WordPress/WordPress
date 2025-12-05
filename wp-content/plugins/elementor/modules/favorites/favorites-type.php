<?php
namespace Elementor\Modules\Favorites;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Core\Utils\Collection;
use Elementor\Core\Utils\Static_Collection;

abstract class Favorites_Type extends Static_Collection {

	public function __construct( array $items = [] ) {
		parent::__construct( $items, true );
	}

	/**
	 * Get the name of the type.
	 *
	 * @return mixed
	 */
	abstract public function get_name();

	/**
	 * Prepare favorites before taking any action.
	 *
	 * @param Collection|array|string $favorites
	 *
	 * @return array
	 */
	public function prepare( $favorites ) {
		if ( $favorites instanceof Collection ) {
			$favorites = $favorites->values();
		}

		if ( ! is_array( $favorites ) ) {
			return [ $favorites ];
		}

		return $favorites;
	}
}
