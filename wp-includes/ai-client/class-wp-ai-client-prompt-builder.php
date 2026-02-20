<?php
/**
 * WP AI Client: WP_AI_Client_Prompt_Builder class
 *
 * @package WordPress
 * @subpackage AI
 * @since 7.0.0
 */

use WordPress\AiClient\Builders\PromptBuilder;
use WordPress\AiClient\Files\DTO\File;
use WordPress\AiClient\Files\Enums\FileTypeEnum;
use WordPress\AiClient\Messages\DTO\Message;
use WordPress\AiClient\Messages\DTO\MessagePart;
use WordPress\AiClient\Messages\Enums\ModalityEnum;
use WordPress\AiClient\Providers\Http\DTO\RequestOptions;
use WordPress\AiClient\Providers\Models\Contracts\ModelInterface;
use WordPress\AiClient\Providers\Models\DTO\ModelConfig;
use WordPress\AiClient\Providers\Models\Enums\CapabilityEnum;
use WordPress\AiClient\Providers\ProviderRegistry;
use WordPress\AiClient\Results\DTO\GenerativeAiResult;
use WordPress\AiClient\Tools\DTO\FunctionDeclaration;
use WordPress\AiClient\Tools\DTO\FunctionResponse;
use WordPress\AiClient\Tools\DTO\WebSearch;

/**
 * Fluent builder for constructing AI prompts, returning WP_Error on failure.
 *
 * This class provides a fluent interface for building prompts with various
 * content types and model configurations. It wraps the PHP AI Client SDK's
 * PromptBuilder and adds WordPress-specific behavior including WP_Error
 * handling instead of exceptions, snake_case method naming, and integration
 * with the Abilities API.
 *
 * Only the generating methods will return a WP_Error, to not break the fluent
 * interface. As soon as any exception is caught in a chain of method calls,
 * the returned instance will be in an error state, and all subsequent method
 * calls will be no-ops that just return the same error state instance. Only
 * when a generating method is called, the WP_Error will be returned.
 *
 * @since 7.0.0
 *
 * @method self with_text(string $text) Adds text to the current message.
 * @method self with_file($file, ?string $mimeType = null) Adds a file to the current message.
 * @method self with_function_response(FunctionResponse $functionResponse) Adds a function response to the current message.
 * @method self with_message_parts(MessagePart ...$parts) Adds message parts to the current message.
 * @method self with_history(Message ...$messages) Adds conversation history messages.
 * @method self using_model(ModelInterface $model) Sets the model to use for generation.
 * @method self using_model_preference(...$preferredModels) Sets preferred models to evaluate in order.
 * @method self using_model_config(ModelConfig $config) Sets the model configuration.
 * @method self using_provider(string $providerIdOrClassName) Sets the provider to use for generation.
 * @method self using_system_instruction(string $systemInstruction) Sets the system instruction.
 * @method self using_max_tokens(int $maxTokens) Sets the maximum number of tokens to generate.
 * @method self using_temperature(float $temperature) Sets the temperature for generation.
 * @method self using_top_p(float $topP) Sets the top-p value for generation.
 * @method self using_top_k(int $topK) Sets the top-k value for generation.
 * @method self using_stop_sequences(string ...$stopSequences) Sets stop sequences for generation.
 * @method self using_candidate_count(int $candidateCount) Sets the number of candidates to generate.
 * @method self using_function_declarations(FunctionDeclaration ...$functionDeclarations) Sets the function declarations available to the model.
 * @method self using_presence_penalty(float $presencePenalty) Sets the presence penalty for generation.
 * @method self using_frequency_penalty(float $frequencyPenalty) Sets the frequency penalty for generation.
 * @method self using_web_search(WebSearch $webSearch) Sets the web search configuration.
 * @method self using_request_options(RequestOptions $options) Sets the request options for HTTP transport.
 * @method self using_top_logprobs(?int $topLogprobs = null) Sets the top log probabilities configuration.
 * @method self as_output_mime_type(string $mimeType) Sets the output MIME type.
 * @method self as_output_schema(array<string, mixed> $schema) Sets the output schema.
 * @method self as_output_modalities(ModalityEnum ...$modalities) Sets the output modalities.
 * @method self as_output_file_type(FileTypeEnum $fileType) Sets the output file type.
 * @method self as_json_response(?array<string, mixed> $schema = null) Configures the prompt for JSON response output.
 * @method bool|WP_Error is_supported(?CapabilityEnum $capability = null) Checks if the prompt is supported for the given capability.
 * @method bool is_supported_for_text_generation() Checks if the prompt is supported for text generation.
 * @method bool is_supported_for_image_generation() Checks if the prompt is supported for image generation.
 * @method bool is_supported_for_text_to_speech_conversion() Checks if the prompt is supported for text to speech conversion.
 * @method bool is_supported_for_video_generation() Checks if the prompt is supported for video generation.
 * @method bool is_supported_for_speech_generation() Checks if the prompt is supported for speech generation.
 * @method bool is_supported_for_music_generation() Checks if the prompt is supported for music generation.
 * @method bool is_supported_for_embedding_generation() Checks if the prompt is supported for embedding generation.
 * @method GenerativeAiResult|WP_Error generate_result(?CapabilityEnum $capability = null) Generates a result from the prompt.
 * @method GenerativeAiResult|WP_Error generate_text_result() Generates a text result from the prompt.
 * @method GenerativeAiResult|WP_Error generate_image_result() Generates an image result from the prompt.
 * @method GenerativeAiResult|WP_Error generate_speech_result() Generates a speech result from the prompt.
 * @method GenerativeAiResult|WP_Error convert_text_to_speech_result() Converts text to speech and returns the result.
 * @method string|WP_Error generate_text() Generates text from the prompt.
 * @method list<string>|WP_Error generate_texts(?int $candidateCount = null) Generates multiple text candidates from the prompt.
 * @method File|WP_Error generate_image() Generates an image from the prompt.
 * @method list<File>|WP_Error generate_images(?int $candidateCount = null) Generates multiple images from the prompt.
 * @method File|WP_Error convert_text_to_speech() Converts text to speech.
 * @method list<File>|WP_Error convert_text_to_speeches(?int $candidateCount = null) Converts text to multiple speech outputs.
 * @method File|WP_Error generate_speech() Generates speech from the prompt.
 * @method list<File>|WP_Error generate_speeches(?int $candidateCount = null) Generates multiple speech outputs from the prompt.
 */
class WP_AI_Client_Prompt_Builder {

	/**
	 * Wrapped prompt builder instance from the PHP AI Client SDK.
	 *
	 * @since 7.0.0
	 * @var PromptBuilder
	 */
	private PromptBuilder $builder;

	/**
	 * WordPress error instance, if any error occurred during method calls.
	 *
	 * @since 7.0.0
	 * @var WP_Error|null
	 */
	private ?WP_Error $error = null;

	/**
	 * List of methods that generate a result from the prompt.
	 *
	 * Structured as a map for faster lookups.
	 *
	 * @since 7.0.0
	 * @var array<string, bool>
	 */
	private static array $generating_methods = array(
		'generate_result'               => true,
		'generate_text_result'          => true,
		'generate_image_result'         => true,
		'generate_speech_result'        => true,
		'convert_text_to_speech_result' => true,
		'generate_text'                 => true,
		'generate_texts'                => true,
		'generate_image'                => true,
		'generate_images'               => true,
		'convert_text_to_speech'        => true,
		'convert_text_to_speeches'      => true,
		'generate_speech'               => true,
		'generate_speeches'             => true,
	);

	/**
	 * List of methods that check whether the prompt is supported.
	 *
	 * Structured as a map for faster lookups.
	 *
	 * @since 7.0.0
	 * @var array<string, bool>
	 */
	private static array $support_check_methods = array(
		'is_supported'                               => true,
		'is_supported_for_text_generation'           => true,
		'is_supported_for_image_generation'          => true,
		'is_supported_for_text_to_speech_conversion' => true,
		'is_supported_for_video_generation'          => true,
		'is_supported_for_speech_generation'         => true,
		'is_supported_for_music_generation'          => true,
		'is_supported_for_embedding_generation'      => true,
	);

	/**
	 * Constructor.
	 *
	 * @since 7.0.0
	 *
	 * @param ProviderRegistry                                                                 $registry The provider registry for finding suitable models.
	 * @param string|MessagePart|Message|array|list<string|MessagePart|array>|list<Message>|null $prompt   Optional. Initial prompt content.
	 *                                                                                                    A string for simple text prompts,
	 *                                                                                                    a MessagePart or Message object for
	 *                                                                                                    structured content, an array for a
	 *                                                                                                    message array shape, or a list of
	 *                                                                                                    parts or messages for multi-turn
	 *                                                                                                    conversations. Default null.
	 */
	public function __construct( ProviderRegistry $registry, $prompt = null ) {
		$this->builder = new PromptBuilder( $registry, $prompt );

		/**
		 * Filters the default request timeout in seconds for AI Client HTTP requests.
		 *
		 * @since 7.0.0
		 *
		 * @param int $default_timeout The default timeout in seconds.
		 */
		$default_timeout = (int) apply_filters( 'wp_ai_client_default_request_timeout', 30 );

		$this->builder->usingRequestOptions(
			RequestOptions::fromArray(
				array(
					RequestOptions::KEY_TIMEOUT => $default_timeout,
				)
			)
		);
	}

	/**
	 * Registers WordPress abilities as function declarations for the AI model.
	 *
	 * Converts each WP_Ability to a FunctionDeclaration using the wpab__ prefix
	 * naming convention and passes them to the underlying prompt builder.
	 *
	 * @since 7.0.0
	 *
	 * @param WP_Ability|string ...$abilities The abilities to register, either as WP_Ability objects or ability name strings.
	 * @return self The current instance for method chaining.
	 */
	public function using_abilities( ...$abilities ): self {
		$declarations = array();

		foreach ( $abilities as $ability ) {
			if ( is_string( $ability ) ) {
				$ability_name = $ability;
				$ability      = wp_get_ability( $ability );
				if ( ! $ability ) {
					_doing_it_wrong(
						__METHOD__,
						sprintf(
							/* translators: %s: string value of the ability name. */
							__( 'The ability %s was not found.' ),
							'<code>' . esc_html( $ability_name ) . '</code>'
						),
						'7.0.0'
					);
					continue;
				}
			}

			// This is only here as a sanity check, the method signature should ensure this already.
			if ( ! $ability instanceof WP_Ability ) {
				continue;
			}

			$function_name = WP_AI_Client_Ability_Function_Resolver::ability_name_to_function_name( $ability->get_name() );
			$input_schema  = $ability->get_input_schema();

			$declarations[] = new FunctionDeclaration(
				$function_name,
				$ability->get_description(),
				! empty( $input_schema ) ? $input_schema : null
			);
		}

		if ( ! empty( $declarations ) ) {
			return $this->using_function_declarations( ...$declarations );
		}

		return $this;
	}

	/**
	 * Magic method to proxy snake_case method calls to their PHP AI Client camelCase counterparts.
	 *
	 * This allows WordPress developers to use snake_case naming conventions. It catches
	 * any exceptions thrown, stores them, and returns a WP_Error when a terminate method
	 * is called.
	 *
	 * @since 7.0.0
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
			if ( self::is_generating_method( $name ) ) {
				return $this->error;
			}
			if ( self::is_support_check_method( $name ) ) {
				return false;
			}
			return $this;
		}

		// Check if the prompt should be prevented for is_supported* and generate_*/convert_text_to_speech* methods.
		if ( self::is_support_check_method( $name ) || self::is_generating_method( $name ) ) {
			/**
			 * Filters whether to prevent the prompt from being executed.
			 *
			 * @since 7.0.0
			 *
			 * @param bool                        $prevent Whether to prevent the prompt. Default false.
			 * @param WP_AI_Client_Prompt_Builder $builder A clone of the prompt builder instance (read-only).
			 */
			$prevent = (bool) apply_filters( 'wp_ai_client_prevent_prompt', false, clone $this );

			if ( $prevent ) {
				// For is_supported* methods, return false.
				if ( self::is_support_check_method( $name ) ) {
					return false;
				}

				// For generate_* and convert_text_to_speech* methods, create a WP_Error.
				$this->error = new WP_Error(
					'prompt_prevented',
					__( 'Prompt execution was prevented by a filter.' ),
					array(
						'exception_class' => 'WP_AI_Client_Prompt_Prevented',
					)
				);

				if ( self::is_generating_method( $name ) ) {
					return $this->error;
				}
				return $this;
			}
		}

		try {
			$callable = $this->get_builder_callable( $name );
			$result   = $callable( ...$arguments );

			// If the result is a PromptBuilder, return the current instance to allow method chaining.
			if ( $result instanceof PromptBuilder ) {
				return $this;
			}

			return $result;
		} catch ( Exception $e ) {
			$this->error = new WP_Error(
				'prompt_builder_error',
				$e->getMessage(),
				array(
					'exception_class' => get_class( $e ),
				)
			);

			if ( self::is_generating_method( $name ) ) {
				return $this->error;
			}
			return $this;
		}
	}

	/**
	 * Checks if a method name is a support check method (is_supported*).
	 *
	 * @since 7.0.0
	 *
	 * @param string $name The method name.
	 * @return bool True if the method is a support check method, false otherwise.
	 */
	private static function is_support_check_method( string $name ): bool {
		return isset( self::$support_check_methods[ $name ] );
	}

	/**
	 * Checks if a method name is a generating method (generate_*, convert_text_to_speech*).
	 *
	 * @since 7.0.0
	 *
	 * @param string $name The method name.
	 * @return bool True if the method is a generating method, false otherwise.
	 */
	private static function is_generating_method( string $name ): bool {
		return isset( self::$generating_methods[ $name ] );
	}

	/**
	 * Retrieves a callable for a given PHP AI Client SDK prompt builder method name.
	 *
	 * @since 7.0.0
	 *
	 * @param string $name The method name in snake_case.
	 * @return callable The callable for the specified method.
	 *
	 * @throws BadMethodCallException If the method does not exist.
	 */
	protected function get_builder_callable( string $name ): callable {
		$camel_case_name = $this->snake_to_camel_case( $name );

		if ( ! is_callable( array( $this->builder, $camel_case_name ) ) ) {
			throw new BadMethodCallException(
				sprintf(
					/* translators: 1: Method name. 2: Class name. */
					__( 'Method %1$s does not exist on %2$s.' ),
					$name, // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
					get_class( $this->builder ) // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
				)
			);
		}

		return array( $this->builder, $camel_case_name );
	}

	/**
	 * Converts snake_case to camelCase.
	 *
	 * @since 7.0.0
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
