<?php

namespace Elementor\Modules\GlobalClasses;

use Elementor\Core\Utils\Collection;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Global_Classes_Changes_Resolver {
	private Global_Classes_Repository $repository;

	private Collection $added;

	private Collection $deleted;

	private Collection $modified;

	public function __construct( Global_Classes_Repository $repository, array $changes ) {
		$this->repository = $repository;
		$this->added = Collection::make( $changes['added'] ?? [] );
		$this->deleted = Collection::make( $changes['deleted'] ?? [] );
		$this->modified = Collection::make( $changes['modified'] ?? [] );
	}

	public static function make( Global_Classes_Repository $repository, array $changes ): self {
		return new self( $repository, $changes );
	}

	public function resolve_items( array $payload ) {
		$touched = $this->added->merge( $this->modified )->values();

		$items_to_save = Collection::make( $payload )->only( $touched );

		return $this->repository
			->all()
			->get_items()
			->except( $this->deleted->values() )
			->merge( $items_to_save )
			->all();
	}

	public function resolve_order( array $payload ) {
		$payload = Collection::make( $payload );

		$current_order = $this->repository->all()->get_order();

		$missing_in_current_order = $payload
			->filter( fn( $item ) => ! $this->added->contains( $item ) )
			->diff( $current_order );

		$payload = $payload->filter( fn( $item ) => ! $missing_in_current_order->contains( $item ) );

		$missing_in_payload = $current_order
			->filter( fn( $item ) => ! $this->deleted->contains( $item ) )
			->diff( $payload );

		return $missing_in_payload
			->merge( $payload )
			->all();
	}
}
