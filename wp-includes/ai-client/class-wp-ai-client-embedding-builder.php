<?php
/**
 * WP AI Client: WP_AI_Client_Embedding_Builder class
 *
 * @package WordPress
 * @subpackage AI
 * @since 7.2.0
 */

use WordPress\AiClient\AiClient;
use WordPress\AiClient\Builders\EmbeddingBuilder;
use WordPress\AiClient\Providers\Http\DTO\RequestOptions;
use WordPress\AiClient\Providers\Models\Contracts\ModelInterface;
use WordPress\AiClient\Providers\Models\DTO\ModelConfig;
use WordPress\AiClient\Providers\ProviderRegistry;
use WordPress\AiClient\Results\DTO\Embedding;
use WordPress\AiClient\Results\DTO\EmbeddingResult;

/**
 * Fluent builder for generating embeddings, returning WP_Error on failure.
 *
 * This class provides a fluent interface for generating embedding vectors from
 * text or file inputs. It wraps the PHP AI Client SDK's EmbeddingBuilder and
 * adds WordPress-specific behavior including WP_Error handling instead of
 * exceptions and snake_case method naming.
 *
 * A model must be specified via `using_model()` or `using_provider_model()`.
 * Embedding vectors are only comparable to other vectors produced by the same
 * model, so no model is selected automatically.
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
 *
 * @see WP_AI_Client_Builder
 *
 * @phpstan-import-type EmbeddingInput from EmbeddingBuilder
 *
 * @method self with_input(...$input) Adds one or more inputs to embed.
 * @method self using_dimensions(int $dimensions) Sets the embedding dimensions.
 * @method self using_model(ModelInterface $model) Sets the model to use for generation.
 * @method self using_provider_model(string $providerIdOrClassName, string $modelId) Sets the model to use for generation, by provider and model identifier.
 * @method self using_model_config(ModelConfig $config) Sets the model configuration.
 * @method self using_request_options(RequestOptions $options) Sets the request options for HTTP transport.
 * @method bool is_supported() Checks whether the specified model supports the current inputs and configuration.
 * @method EmbeddingResult|WP_Error generate_embedding_result() Generates an embedding result from the configured inputs.
 * @method Embedding|WP_Error generate_embedding() Generates a single embedding from the configured input.
 * @method list<Embedding>|WP_Error generate_embeddings() Generates embeddings from the configured inputs.
 */
class WP_AI_Client_Embedding_Builder extends WP_AI_Client_Builder {

	/**
	 * List of methods that generate embeddings from the inputs.
	 *
	 * Structured as a map for faster lookups.
	 *
	 * @since 7.2.0
	 * @var array<string, bool>
	 */
	private static array $generating_methods = array(
		'generate_embedding_result' => true,
		'generate_embedding'        => true,
		'generate_embeddings'       => true,
	);

	/**
	 * List of methods that check whether the embedding generation is supported.
	 *
	 * Structured as a map for faster lookups.
	 *
	 * @since 7.2.0
	 * @var array<string, bool>
	 */
	private static array $support_check_methods = array(
		'is_supported' => true,
	);

	/**
	 * Creates the wrapped embedding builder instance from the PHP AI Client SDK.
	 *
	 * @since 7.2.0
	 *
	 * @param ProviderRegistry                         $registry The provider registry for finding suitable models.
	 * @param EmbeddingInput|list<EmbeddingInput>|null $input    Initial input(s) to embed, or null.
	 *                                                           A string for simple text inputs,
	 *                                                           a MessagePart or File object for
	 *                                                           structured content, an array for a
	 *                                                           message part array shape, or a list
	 *                                                           of any of these to embed multiple
	 *                                                           inputs.
	 * @return EmbeddingBuilder The SDK embedding builder instance.
	 */
	protected function create_sdk_builder( ProviderRegistry $registry, $input ): object {
		return new EmbeddingBuilder( $registry, $input, AiClient::getEventDispatcher() );
	}

	/**
	 * Retrieves the prefix used for WP_Error codes created by this builder.
	 *
	 * @since 7.2.0
	 *
	 * @return string The error code prefix.
	 */
	protected function get_error_code_prefix(): string {
		return 'embedding';
	}

	/**
	 * Retrieves the error message to use when the embedding generation is prevented by a filter.
	 *
	 * @since 7.2.0
	 *
	 * @return string The translated error message.
	 */
	protected function get_prevented_error_message(): string {
		return __( 'Embedding generation was prevented by a filter.' );
	}

	/**
	 * Checks whether the embedding generation is prevented by the `wp_ai_client_prevent_embedding` filter.
	 *
	 * @since 7.2.0
	 *
	 * @return bool Whether the embedding generation is prevented.
	 */
	protected function is_prevented_by_filter(): bool {
		/**
		 * Filters whether to prevent the embedding generation from being executed.
		 *
		 * @since 7.2.0
		 *
		 * @param bool                           $prevent Whether to prevent the embedding generation. Default false.
		 * @param WP_AI_Client_Embedding_Builder $builder A clone of the embedding builder instance (read-only).
		 */
		return (bool) apply_filters( 'wp_ai_client_prevent_embedding', false, clone $this );
	}

	/**
	 * Retrieves the methods that generate embeddings from the inputs.
	 *
	 * @since 7.2.0
	 *
	 * @return array<string, bool> The generating methods map.
	 */
	protected function get_generating_methods(): array {
		return self::$generating_methods;
	}

	/**
	 * Retrieves the methods that check whether the embedding generation is supported.
	 *
	 * @since 7.2.0
	 *
	 * @return array<string, bool> The support check methods map.
	 */
	protected function get_support_check_methods(): array {
		return self::$support_check_methods;
	}
}
