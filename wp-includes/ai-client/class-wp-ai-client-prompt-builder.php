<?php
/**
 * WP AI Client: WP_AI_Client_Prompt_Builder class
 *
 * @package WordPress
 * @subpackage AI
 * @since 7.0.0
 */

use WordPress\AiClient\AiClient;
use WordPress\AiClient\Builders\PromptBuilder;
use WordPress\AiClient\Files\DTO\File;
use WordPress\AiClient\Files\Enums\FileTypeEnum;
use WordPress\AiClient\Files\Enums\MediaOrientationEnum;
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
 * when a generating method is called, the WP_Error will be returned. The
 * support check methods are the exception to the no-op behavior: they return
 * false rather than the error state instance.
 *
 * @since 7.0.0
 * @since 7.2.0 Now extends the `WP_AI_Client_Builder` base class.
 *
 * @see WP_AI_Client_Builder
 *
 * @phpstan-import-type Prompt from PromptBuilder
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
 * @method self as_output_media_orientation(MediaOrientationEnum $orientation) Sets the output media orientation.
 * @method self as_output_media_aspect_ratio(string $aspectRatio) Sets the output media aspect ratio.
 * @method self as_output_speech_voice(string $voice) Sets the output speech voice.
 * @method self as_json_response(?array<string, mixed> $schema = null) Configures the prompt for JSON response output.
 * @method bool is_supported(?CapabilityEnum $capability = null) Checks if the prompt is supported for the given capability.
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
 * @method GenerativeAiResult|WP_Error generate_video_result() Generates a video result from the prompt.
 * @method string|WP_Error generate_text() Generates text from the prompt.
 * @method list<string>|WP_Error generate_texts(?int $candidateCount = null) Generates multiple text candidates from the prompt.
 * @method File|WP_Error generate_image() Generates an image from the prompt.
 * @method list<File>|WP_Error generate_images(?int $candidateCount = null) Generates multiple images from the prompt.
 * @method File|WP_Error convert_text_to_speech() Converts text to speech.
 * @method list<File>|WP_Error convert_text_to_speeches(?int $candidateCount = null) Converts text to multiple speech outputs.
 * @method File|WP_Error generate_speech() Generates speech from the prompt.
 * @method list<File>|WP_Error generate_speeches(?int $candidateCount = null) Generates multiple speech outputs from the prompt.
 * @method File|WP_Error generate_video() Generates a video from the prompt.
 * @method list<File>|WP_Error generate_videos(?int $candidateCount = null) Generates multiple videos from the prompt.
 */
class WP_AI_Client_Prompt_Builder extends WP_AI_Client_Builder {

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
		'generate_video_result'         => true,
		'generate_text'                 => true,
		'generate_texts'                => true,
		'generate_image'                => true,
		'generate_images'               => true,
		'convert_text_to_speech'        => true,
		'convert_text_to_speeches'      => true,
		'generate_speech'               => true,
		'generate_speeches'             => true,
		'generate_video'                => true,
		'generate_videos'               => true,
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
			$input_schema  = wp_prepare_json_schema_for_client( $ability->get_input_schema() );

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
	 * Creates the wrapped prompt builder instance from the PHP AI Client SDK.
	 *
	 * @since 7.2.0
	 *
	 * @param ProviderRegistry $registry The provider registry for finding suitable models.
	 * @param Prompt           $prompt   Initial prompt content, or null.
	 *                                   A string for simple text prompts,
	 *                                   a MessagePart or Message object for
	 *                                   structured content, an array for a
	 *                                   message array shape, or a list of
	 *                                   parts or messages for multi-turn
	 *                                   conversations.
	 * @return PromptBuilder The SDK prompt builder instance.
	 */
	protected function create_sdk_builder( ProviderRegistry $registry, $prompt ): object {
		return new PromptBuilder( $registry, $prompt, AiClient::getEventDispatcher() );
	}

	/**
	 * Retrieves the prefix used for WP_Error codes created by this builder.
	 *
	 * @since 7.2.0
	 *
	 * @return string The error code prefix.
	 */
	protected function get_error_code_prefix(): string {
		return 'prompt';
	}

	/**
	 * Retrieves the error message to use when the prompt is prevented by a filter.
	 *
	 * @since 7.2.0
	 *
	 * @return string The translated error message.
	 */
	protected function get_prevented_error_message(): string {
		return __( 'Prompt execution was prevented by a filter.' );
	}

	/**
	 * Checks whether the prompt is prevented by the `wp_ai_client_prevent_prompt` filter.
	 *
	 * @since 7.2.0
	 *
	 * @return bool Whether the prompt is prevented.
	 */
	protected function is_prevented_by_filter(): bool {
		/**
		 * Filters whether to prevent the prompt from being executed.
		 *
		 * @since 7.0.0
		 *
		 * @param bool                        $prevent Whether to prevent the prompt. Default false.
		 * @param WP_AI_Client_Prompt_Builder $builder A clone of the prompt builder instance (read-only).
		 */
		return (bool) apply_filters( 'wp_ai_client_prevent_prompt', false, clone $this );
	}

	/**
	 * Retrieves the methods that generate a result from the prompt.
	 *
	 * @since 7.2.0
	 *
	 * @return array<string, bool> The generating methods map.
	 */
	protected function get_generating_methods(): array {
		return self::$generating_methods;
	}

	/**
	 * Retrieves the methods that check whether the prompt is supported.
	 *
	 * @since 7.2.0
	 *
	 * @return array<string, bool> The support check methods map.
	 */
	protected function get_support_check_methods(): array {
		return self::$support_check_methods;
	}
}
