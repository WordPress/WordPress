<?php

namespace Automattic\WooCommerce\Admin\RemoteInboxNotifications;

use InvalidArgumentException;
use stdClass;

/**
 * A simple service class for the Transformer classes.
 *
 * Class TransformerService
 *
 * @package Automattic\WooCommerce\Admin\RemoteInboxNotifications
 */
class TransformerService {
	/**
	 * Create a transformer object by name.
	 *
	 * @param string $name name of the transformer.
	 *
	 * @return TransformerInterface|null
	 */
	public static function create_transformer( $name ) {
		$camel_cased = str_replace( ' ', '', ucwords( str_replace( '_', ' ', $name ) ) );

		$classname = __NAMESPACE__ . '\\Transformers\\' . $camel_cased;
		if ( ! class_exists( $classname ) ) {
			return null;
		}

		return new $classname();
	}

	/**
	 * Apply transformers to the given value.
	 *
	 * @param mixed  $target_value a value to transform.
	 * @param array  $transformer_configs transform configuration.
	 * @param string $default default value.
	 *
	 * @throws InvalidArgumentException Throws when one of the requried arguments is missing.
	 * @return mixed|null
	 */
	public static function apply( $target_value, array $transformer_configs, $default ) {
		foreach ( $transformer_configs as $transformer_config ) {
			if ( ! isset( $transformer_config->use ) ) {
				throw new InvalidArgumentException( 'Missing required config value: use' );
			}

			if ( ! isset( $transformer_config->arguments ) ) {
				$transformer_config->arguments = null;
			}

			$transformer = self::create_transformer( $transformer_config->use );
			if ( null === $transformer ) {
				throw new InvalidArgumentException( "Unable to find a transformer by name: {$transformer_config->use}" );
			}

			$transformed_value = $transformer->transform( $target_value, $transformer_config->arguments, $default );
			// if the transformer returns null, then return the previously transformed value.
			if ( null === $transformed_value ) {
				return $target_value;
			}

			$target_value = $transformed_value;
		}

		return $target_value;
	}
}
