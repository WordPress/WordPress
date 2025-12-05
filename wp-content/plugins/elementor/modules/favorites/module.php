<?php
namespace Elementor\Modules\Favorites;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Core\Base\Module as BaseModule;
use Elementor\Core\Experiments\Manager;
use Elementor\Modules\Favorites\Types\Widgets;
use Elementor\Plugin;
use http\Exception\InvalidArgumentException;
use WP_Error;

class Module extends BaseModule {

	/**
	 * List of registered favorites type.
	 *
	 * @var Favorites_Type[]
	 */
	protected $types = [];

	const OPTION_NAME = 'elementor_editor_user_favorites';

	/**
	 * The name of the merge action.
	 *
	 * @var string
	 */
	const ACTION_MERGE = 'merge';

	/**
	 * The name of the delete action.
	 *
	 * @var string
	 */
	const ACTION_DELETE = 'delete';

	/**
	 * Favorites module constructor.
	 */
	public function __construct() {
		// Register default types
		$this->register( Widgets::class );

		$this->populate();

		Plugin::instance()->data_manager_v2->register_controller( new Controller() );

		add_filter( 'elementor/tracker/send_tracking_data_params', [ $this, 'add_tracking_data' ] );
	}

	/**
	 * Add usage data related to favorites.
	 *
	 * @param $params
	 *
	 * @return array
	 */
	public function add_tracking_data( $params ) {
		$params['usages']['favorites'] = $this->get();

		return $params;
	}

	public function get_name() {
		return 'favorites';
	}

	/**
	 * Get user favorites by type.
	 *
	 * @param string[]|string $type
	 *
	 * @return array
	 */
	public function get( $type = null ) {
		if ( null === $type ) {
			$type = array_keys( $this->types );
		}

		if ( is_array( $type ) ) {
			return array_intersect_key(
				$this->combined(),
				array_flip( (array) $type )
			);
		}

		return $this->type_instance( $type )
			->values();
	}

	/**
	 * Merge new user favorites to a type.
	 *
	 * @param string       $type
	 * @param array|string $favorites
	 * @param bool         $store
	 *
	 * @return array|bool
	 */
	public function merge( $type, $favorites, $store = true ) {
		return $this->update( $type, $favorites, static::ACTION_MERGE, $store );
	}

	/**
	 * Delete existing favorites from a type.
	 *
	 * @param string       $type
	 * @param array|string $favorites
	 * @param bool         $store
	 *
	 * @return array|int
	 */
	public function delete( $type, $favorites, $store = true ) {
		return $this->update( $type, $favorites, static::ACTION_DELETE, $store );
	}

	/**
	 * Update favorites on a type by merging or deleting from it.
	 *
	 * @param      $type
	 * @param      $favorites
	 * @param      $action
	 * @param bool $store
	 *
	 * @return array|boolean
	 */
	public function update( $type, $favorites, $action, $store = true ) {
		$type_instance = $this->type_instance( $type );
		$favorites = $type_instance->prepare( $favorites );

		switch ( $action ) {
			case static::ACTION_MERGE:
				$type_instance->merge( $favorites );
				break;
			case static::ACTION_DELETE:
				$type_instance->filter(
					function( $value ) use ( $favorites ) {
						return ! in_array( $value, $favorites, true );
					}
				);
				break;
			default:
				$this->action_doesnt_exists( $action );
		}

		if ( $store && ! $this->store() ) {
			return false;
		}

		return $type_instance->values();
	}

	/**
	 * Get registered favorites type instance.
	 *
	 * @param string $type
	 *
	 * @return Favorites_Type
	 */
	public function type_instance( $type ) {
		return $this->types[ $type ];
	}

	/**
	 * Register a new type class.
	 *
	 * @param string $class_name
	 */
	public function register( $class_name ) {
		$type_instance = new $class_name();

		$this->types[ $type_instance->get_name() ] = $type_instance;
	}

	/**
	 * Returns all available types keys.
	 *
	 * @return string[]
	 */
	public function available() {
		return array_keys( $this->types );
	}

	/**
	 * Combine favorites from all types into a single array.
	 *
	 * @return array
	 */
	protected function combined() {
		$all = [];

		foreach ( $this->types as $type ) {
			$favorites = $type->values();

			if ( ! empty( $favorites ) ) {
				$all[ $type->get_name() ] = $favorites;
			}
		}

		return $all;
	}

	/**
	 * Populate all type classes with the stored data.
	 */
	protected function populate() {
		$combined = $this->retrieve();

		foreach ( $this->types as $key => $type ) {
			if ( isset( $combined[ $key ] ) ) {
				$type->merge( $combined[ $key ] );
			}
		}
	}

	/**
	 * Retrieve stored user favorites types.
	 *
	 * @return mixed|false
	 */
	protected function retrieve() {
		return get_user_option( static::OPTION_NAME );
	}

	/**
	 * Update all changes to user favorites type.
	 *
	 * @return int|bool
	 */
	protected function store() {
		return update_user_option( get_current_user_id(), static::OPTION_NAME, $this->combined() );
	}

	/**
	 * Throw action doesn't exist exception.
	 *
	 * @param string $action
	 *
	 * @throws \InvalidArgumentException If favorite action fails or validation errors occur.
	 */
	public function action_doesnt_exists( $action ) {
		throw new \InvalidArgumentException( sprintf( "Action '%s' to apply on favorites doesn't exists", esc_html( $action ) ) );
	}
}
