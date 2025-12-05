<?php

namespace Yoast\WP\SEO\Helpers\Schema;

use Closure;
use WPSEO_Replace_Vars;
use Yoast\WP\SEO\Conditionals\No_Conditionals;
use Yoast\WP\SEO\Config\Schema_IDs;
use Yoast\WP\SEO\Context\Meta_Tags_Context;
use Yoast\WP\SEO\Helpers\Date_Helper;
use Yoast\WP\SEO\Presentations\Indexable_Presentation;

/**
 * Registers the Schema replace variables and exposes a method to replace variables on a Schema graph.
 */
class Replace_Vars_Helper {

	use No_Conditionals;

	/**
	 * The replace vars.
	 *
	 * @var WPSEO_Replace_Vars
	 */
	protected $replace_vars;

	/**
	 * The Schema ID helper.
	 *
	 * @var ID_Helper
	 */
	protected $id_helper;

	/**
	 * The date helper.
	 *
	 * @var Date_Helper
	 */
	protected $date_helper;

	/**
	 * Replace_Vars_Helper constructor.
	 *
	 * @param WPSEO_Replace_Vars $replace_vars The replace vars.
	 * @param ID_Helper          $id_helper    The Schema ID helper.
	 * @param Date_Helper        $date_helper  The date helper.
	 */
	public function __construct(
		WPSEO_Replace_Vars $replace_vars,
		ID_Helper $id_helper,
		Date_Helper $date_helper
	) {
		$this->replace_vars = $replace_vars;
		$this->id_helper    = $id_helper;
		$this->date_helper  = $date_helper;
	}

	/**
	 * Replaces the variables.
	 *
	 * @param array                  $schema_data  The Schema data.
	 * @param Indexable_Presentation $presentation The indexable presentation.
	 *
	 * @return array The array with replaced vars.
	 */
	public function replace( array $schema_data, Indexable_Presentation $presentation ) {
		foreach ( $schema_data as $key => $value ) {
			if ( \is_array( $value ) ) {
				$schema_data[ $key ] = $this->replace( $value, $presentation );

				continue;
			}

			$schema_data[ $key ] = $this->replace_vars->replace( $value, $presentation->source );
		}

		return $schema_data;
	}

	/**
	 * Registers the Schema-related replace vars.
	 *
	 * @param Meta_Tags_Context $context The meta tags context.
	 *
	 * @return void
	 */
	public function register_replace_vars( $context ) {
		$replace_vars = [
			'main_schema_id'   => $context->main_schema_id,
			'author_id'        => $this->id_helper->get_user_schema_id( $context->indexable->author_id, $context ),
			'person_id'        => $context->site_url . Schema_IDs::PERSON_HASH,
			'primary_image_id' => $context->canonical . Schema_IDs::PRIMARY_IMAGE_HASH,
			'webpage_id'       => $context->main_schema_id,
			'website_id'       => $context->site_url . Schema_IDs::WEBSITE_HASH,
			'organization_id'  => $context->site_url . Schema_IDs::ORGANIZATION_HASH,
		];

		if ( $context->post ) {
			// Post does not always exist, e.g. on term pages.
			$replace_vars['post_date'] = $this->date_helper->format( $context->post->post_date, \DATE_ATOM );
		}

		foreach ( $replace_vars as $var => $value ) {
			$this->register_replacement( $var, $value );
		}
	}

	/**
	 * Registers a replace var and its value.
	 *
	 * @param string $variable The replace variable.
	 * @param string $value    The value that the variable should be replaced with.
	 *
	 * @return void
	 */
	protected function register_replacement( $variable, $value ) {
		$this->replace_vars->safe_register_replacement(
			$variable,
			$this->get_identity_function( $value )
		);
	}

	/**
	 * Returns an anonymous function that in turn just returns the given value.
	 *
	 * @param mixed $value The value that the function should return.
	 *
	 * @return Closure A function that returns the given value.
	 */
	protected function get_identity_function( $value ) {
		return static function () use ( $value ) {
			return $value;
		};
	}
}
