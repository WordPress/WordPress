<?php
/**
 * WP AI Client: WP_AI_Client_Builder class
 *
 * @package WordPress
 * @subpackage AI
 * @since 7.2.0
 */

use WordPress\AiClient\Common\Exception\InvalidArgumentException;
use WordPress\AiClient\Common\Exception\TokenLimitReachedException;
use WordPress\AiClient\Providers\Http\DTO\RequestOptions;
use WordPress\AiClient\Providers\Http\Exception\ClientException;
use WordPress\AiClient\Providers\Http\Exception\NetworkException;
use WordPress\AiClient\Providers\Http\Exception\ServerException;
use WordPress\AiClient\Providers\ProviderRegistry;

/**
 * Base class for fluent AI client builders, returning WP_Error on failure.
 *
 * This class wraps a builder from the PHP AI Client SDK and adds
 * WordPress-specific behavior shared by all builders: WP_Error handling
 * instead of exceptions, and snake_case method naming proxied to the SDK's
 * camelCase methods.
 *
 * Only the generating methods will return a WP_Error, to not break the fluent
 * interface. As soon as any exception is caught in a chain of method calls,
 * the returned instance will be in an error state, and all subsequent method
 * calls will be no-ops that just return the same error state instance. Only
 * when a generating method is called, the WP_Error will be returned. The
 * support check methods are the exception to the no-op behavior: they return
 * false rather than the error state instance.
 *
 * @since 7.2.0
 * @internal Intended only as the shared base for the AI client builders in core.
 *           It may gain abstract methods in the future, so it should not be extended.
 * @access private
 */
abstract class WP_AI_Client_Builder {

	/**
	 * Wrapped builder instance from the PHP AI Client SDK.
	 *
	 * @since 7.0.0
	 * @since 7.2.0 Moved from `WP_AI_Client_Prompt_Builder` to the `WP_AI_Client_Builder` base class.
	 * @var object
	 */
	private object $builder;

	/**
	 * WordPress error instance, if any error occurred during method calls.
	 *
	 * @since 7.0.0
	 * @since 7.2.0 Moved from `WP_AI_Client_Prompt_Builder` to the `WP_AI_Client_Builder` base class.
	 * @var WP_Error|null
	 */
	private ?WP_Error $error = null;

	/**
	 * Constructor.
	 *
	 * @since 7.0.0
	 * @since 7.2.0 Moved from `WP_AI_Client_Prompt_Builder` to the `WP_AI_Client_Builder` base class.
	 *
	 * @param ProviderRegistry $registry The provider registry for finding suitable models.
	 * @param mixed            $input    Optional. Initial input content for the builder.
	 *                                   See the child class for the supported types.
	 *                                   Default null.
	 */
	public function __construct( ProviderRegistry $registry, $input = null ) {
		try {
			$this->builder = $this->create_sdk_builder( $registry, $input );
		} catch ( Throwable $e ) {
			$this->builder = $this->create_sdk_builder( $registry, null );
			$this->error   = $this->throwable_to_wp_error( $e );
		}

		$default_timeout = 30.0;

		/**
		 * Filters the default request timeout in seconds for AI Client HTTP requests.
		 *
		 * @since 7.0.0
		 * @since 7.2.0 Added the `$builder_type` parameter.
		 *
		 * @param float  $default_timeout The default timeout in seconds.
		 * @param string $builder_type    The class name of the builder the timeout applies to,
		 *                                e.g. 'WP_AI_Client_Prompt_Builder' or 'WP_AI_Client_Embedding_Builder'.
		 */
		$filtered_default_timeout = apply_filters( 'wp_ai_client_default_request_timeout', $default_timeout, get_class( $this ) );
		if ( is_numeric( $filtered_default_timeout ) && (float) $filtered_default_timeout >= 0.0 ) {
			$default_timeout = (float) $filtered_default_timeout;
		} else {
			_doing_it_wrong(
				get_class( $this ) . '::__construct',
				sprintf(
					/* translators: %s: wp_ai_client_default_request_timeout */
					__( 'The %s filter must return a non-negative number.' ),
					'<code>wp_ai_client_default_request_timeout</code>'
				),
				'7.0.0'
			);
		}

		$this->builder->usingRequestOptions(
			RequestOptions::fromArray(
				array(
					RequestOptions::KEY_TIMEOUT => $default_timeout,
				)
			)
		);
	}

	/**
	 * Clones the wrapped builder alongside this instance.
	 *
	 * The wrapped builder mutates its own state, so without this a clone would
	 * share that state with the original and any change made to one would be
	 * visible in the other.
	 *
	 * @since 7.2.0
	 */
	public function __clone() {
		$this->builder = clone $this->builder;

		if ( null !== $this->error ) {
			$this->error = clone $this->error;
		}
	}

	/**
	 * Creates the wrapped builder instance from the PHP AI Client SDK.
	 *
	 * @since 7.2.0
	 *
	 * @param ProviderRegistry $registry The provider registry for finding suitable models.
	 * @param mixed            $input    Initial input content for the builder, or null.
	 * @return object The SDK builder instance.
	 */
	abstract protected function create_sdk_builder( ProviderRegistry $registry, $input ): object;

	/**
	 * Retrieves the prefix used for WP_Error codes created by this builder.
	 *
	 * @since 7.2.0
	 *
	 * @return string The error code prefix, e.g. 'prompt' or 'embedding'.
	 */
	abstract protected function get_error_code_prefix(): string;

	/**
	 * Retrieves the error message to use when execution is prevented by a filter.
	 *
	 * @since 7.2.0
	 *
	 * @return string The translated error message.
	 */
	abstract protected function get_prevented_error_message(): string;

	/**
	 * Checks whether execution is prevented by the builder's prevent filter.
	 *
	 * Child classes apply their specific filter, passing a clone of the builder
	 * instance for read-only inspection.
	 *
	 * @since 7.2.0
	 *
	 * @return bool Whether execution is prevented.
	 */
	abstract protected function is_prevented_by_filter(): bool;

	/**
	 * Retrieves the methods that generate a result from the builder.
	 *
	 * Structured as a map of method name to true for faster lookups.
	 *
	 * @since 7.2.0
	 *
	 * @return array<string, bool> The generating methods map.
	 */
	abstract protected function get_generating_methods(): array;

	/**
	 * Retrieves the methods that check whether the builder input is supported.
	 *
	 * Structured as a map of method name to true for faster lookups.
	 *
	 * @since 7.2.0
	 *
	 * @return array<string, bool> The support check methods map.
	 */
	abstract protected function get_support_check_methods(): array;

	/**
	 * Magic method to proxy snake_case method calls to their PHP AI Client camelCase counterparts.
	 *
	 * This allows WordPress developers to use snake_case naming conventions. It catches
	 * any exceptions thrown, stores them, and returns a WP_Error when a terminate method
	 * is called, or false when a support check method is called.
	 *
	 * @since 7.0.0
	 * @since 7.2.0 Moved from `WP_AI_Client_Prompt_Builder` to the `WP_AI_Client_Builder` base class.
	 *
	 * @param string            $name      The method name in snake_case.
	 * @param array<int, mixed> $arguments The method arguments.
	 * @return mixed The result of the method call.
	 */
	public function __call( string $name, array $arguments ) {
		/*
		 * If an error occurred in a previous method call, either return the error for terminate methods,
		 * or return the same instance for other methods to maintain the fluent interface.
		 */
		if ( null !== $this->error ) {
			if ( $this->is_generating_method( $name ) ) {
				return $this->error;
			}
			if ( $this->is_support_check_method( $name ) ) {
				return false;
			}
			return $this;
		}

		// Check if execution should be prevented for support check and generating methods.
		if ( $this->is_support_check_method( $name ) || $this->is_generating_method( $name ) ) {
			// If AI is not supported, then there's no need to apply the filter as execution will be prevented anyway.
			$is_ai_disabled = ! wp_supports_ai();
			$prevent        = $is_ai_disabled;
			if ( ! $prevent ) {
				$prevent = $this->is_prevented_by_filter();
			}

			if ( $prevent ) {
				// For support check methods, return false.
				if ( $this->is_support_check_method( $name ) ) {
					return false;
				}

				$error_message = $is_ai_disabled
					? __( 'AI features are not supported in this environment.' )
					: $this->get_prevented_error_message();

				// For generating methods, create a WP_Error.
				$this->error = new WP_Error(
					$this->get_error_code_prefix() . '_prevented',
					$error_message,
					array(
						'status' => 503,
					)
				);

				if ( $this->is_generating_method( $name ) ) {
					return $this->error;
				}
				return $this;
			}
		}

		try {
			$callable = $this->get_builder_callable( $name );
			$result   = $callable( ...$arguments );

			// If the result is the wrapped SDK builder, return the current instance to allow method chaining.
			if ( $result instanceof $this->builder ) {
				return $this;
			}

			return $result;
		} catch ( Throwable $e ) {
			$this->error = $this->throwable_to_wp_error( $e );

			if ( $this->is_generating_method( $name ) ) {
				return $this->error;
			}
			if ( $this->is_support_check_method( $name ) ) {
				return false;
			}
			return $this;
		}
	}

	/**
	 * Converts a throwable into a WP_Error with a structured error code and message.
	 *
	 * This method maps different throwable types to specific WP_Error codes and HTTP status codes.
	 * The presence of the status codes means these WP_Error objects can be easily used in REST API responses
	 * or other contexts where HTTP semantics are relevant.
	 *
	 * @since 7.0.0
	 * @since 7.2.0 Moved from `WP_AI_Client_Prompt_Builder` to the `WP_AI_Client_Builder` base class.
	 *
	 * @param Throwable $throwable The throwable to convert.
	 * @return WP_Error The resulting WP_Error object.
	 */
	private function throwable_to_wp_error( Throwable $throwable ): WP_Error {
		$prefix = $this->get_error_code_prefix();

		if ( $throwable instanceof NetworkException ) {
			$error_code  = $prefix . '_network_error';
			$status_code = 503;
		} elseif ( $throwable instanceof ClientException ) {
			// `ClientException` uses HTTP status codes as exception codes, so we can rely on them.
			$error_code  = $prefix . '_client_error';
			$status_code = $throwable->getCode() ? $throwable->getCode() : 400;
		} elseif ( $throwable instanceof ServerException ) {
			// `ServerException` uses HTTP status codes as exception codes, so we can rely on them.
			$error_code  = $prefix . '_upstream_server_error';
			$status_code = $throwable->getCode() ? $throwable->getCode() : 500;
		} elseif ( $throwable instanceof TokenLimitReachedException ) {
			$error_code  = $prefix . '_token_limit_reached';
			$status_code = 400;
		} elseif ( $throwable instanceof InvalidArgumentException ) {
			$error_code  = $prefix . '_invalid_argument';
			$status_code = 400;
		} else {
			$error_code  = $prefix . '_builder_error';
			$status_code = 500;
		}

		return new WP_Error(
			$error_code,
			$throwable->getMessage(),
			array(
				'status'          => $status_code,
				'exception_class' => get_class( $throwable ),
			)
		);
	}

	/**
	 * Checks if a method name is a support check method (is_supported*).
	 *
	 * @since 7.0.0
	 * @since 7.2.0 Moved from `WP_AI_Client_Prompt_Builder` to the `WP_AI_Client_Builder` base class,
	 *              and changed from static to instance method.
	 *
	 * @param string $name The method name.
	 * @return bool True if the method is a support check method, false otherwise.
	 */
	private function is_support_check_method( string $name ): bool {
		$methods = $this->get_support_check_methods();
		return isset( $methods[ $name ] );
	}

	/**
	 * Checks if a method name is a generating method (generate_*, convert_text_to_speech*).
	 *
	 * @since 7.0.0
	 * @since 7.2.0 Moved from `WP_AI_Client_Prompt_Builder` to the `WP_AI_Client_Builder` base class,
	 *              and changed from static to instance method.
	 *
	 * @param string $name The method name.
	 * @return bool True if the method is a generating method, false otherwise.
	 */
	private function is_generating_method( string $name ): bool {
		$methods = $this->get_generating_methods();
		return isset( $methods[ $name ] );
	}

	/**
	 * Retrieves a callable for a given PHP AI Client SDK builder method name.
	 *
	 * @since 7.0.0
	 * @since 7.2.0 Moved from `WP_AI_Client_Prompt_Builder` to the `WP_AI_Client_Builder` base class.
	 *
	 * @param string $name The method name in snake_case.
	 * @return callable The callable for the specified method.
	 *
	 * @throws BadMethodCallException If the method does not exist.
	 */
	protected function get_builder_callable( string $name ): callable {
		$camel_case_name = $this->snake_to_camel_case( $name );

		$method = array( $this->builder, $camel_case_name );
		if ( ! is_callable( $method ) ) {
			throw new BadMethodCallException(
				sprintf(
					/* translators: 1: Method name. 2: Class name. */
					__( 'Method %1$s does not exist on %2$s.' ),
					$name, // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
					get_class( $this->builder ) // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
				)
			);
		}

		return $method;
	}

	/**
	 * Converts snake_case to camelCase.
	 *
	 * @since 7.0.0
	 * @since 7.2.0 Moved from `WP_AI_Client_Prompt_Builder` to the `WP_AI_Client_Builder` base class.
	 *
	 * @param string $snake_case The snake_case string.
	 * @return string The camelCase string.
	 */
	private function snake_to_camel_case( string $snake_case ): string {
		$parts = explode( '_', $snake_case );

		$camel_case  = $parts[0];
		$parts_count = count( $parts );
		for ( $i = 1; $i < $parts_count; $i++ ) {
			$camel_case .= ucfirst( $parts[ $i ] );
		}

		return $camel_case;
	}
}
