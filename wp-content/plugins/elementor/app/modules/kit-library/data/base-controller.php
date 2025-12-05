<?php
namespace Elementor\App\Modules\KitLibrary\Data;

use Elementor\Plugin;
use Elementor\Data\V2\Base\Controller;
use Elementor\Core\Utils\Collection;
use Elementor\Modules\Library\User_Favorites;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

abstract class Base_Controller extends Controller {
	/**
	 * @var Repository
	 */
	private $repository;

	/**
	 * @return Repository
	 */
	public function get_repository() {
		if ( ! $this->repository ) {
			/** @var \Elementor\Core\Common\Modules\Connect\Module $connect */
			$connect = Plugin::$instance->common->get_component( 'connect' );

			$subscription_plans = ( new Collection( $connect->get_subscription_plans() ) )
				->map( function ( $value ) {
					return $value['label'];
				} );

			$this->repository = new Repository(
				$connect->get_app( 'kit-library' ),
				new User_Favorites( get_current_user_id() ),
				$subscription_plans
			);
		}

		return $this->repository;
	}
}
