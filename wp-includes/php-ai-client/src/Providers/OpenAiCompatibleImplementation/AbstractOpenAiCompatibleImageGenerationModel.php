<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\OpenAiCompatibleImplementation;

use WordPress\AiClient\Common\Exception\InvalidArgumentException;
use WordPress\AiClient\Common\Exception\RuntimeException;
use WordPress\AiClient\Files\DTO\File;
use WordPress\AiClient\Files\Enums\MediaOrientationEnum;
use WordPress\AiClient\Messages\DTO\Message;
use WordPress\AiClient\Messages\DTO\MessagePart;
use WordPress\AiClient\Messages\Enums\MessageRoleEnum;
use WordPress\AiClient\Providers\ApiBasedImplementation\AbstractApiBasedModel;
use WordPress\AiClient\Providers\Http\DTO\Request;
use WordPress\AiClient\Providers\Http\DTO\Response;
use WordPress\AiClient\Providers\Http\Enums\HttpMethodEnum;
use WordPress\AiClient\Providers\Http\Exception\ResponseException;
use WordPress\AiClient\Providers\Http\Util\ResponseUtil;
use WordPress\AiClient\Providers\Models\ImageGeneration\Contracts\ImageGenerationModelInterface;
use WordPress\AiClient\Results\DTO\Candidate;
use WordPress\AiClient\Results\DTO\GenerativeAiResult;
use WordPress\AiClient\Results\DTO\TokenUsage;
use WordPress\AiClient\Results\Enums\FinishReasonEnum;
/**
 * Base class for an image generation model for providers that implement OpenAI's API format.
 *
 * This abstract class is designed to work with any AI provider that offers an OpenAI-compatible
 * API endpoint for image generation, including but not limited to Anthropic, Google, and other
 * providers that have adopted OpenAI's image generation API specification as a standard interface.
 *
 * @since 0.1.0
 *
 * @phpstan-type ImageGenerationParams array{
 *     model: string,
 *     prompt: string,
 *     n?: int,
 *     response_format?: string,
 *     output_format?: string|null,
 *     size?: string,
 *     ...
 * }
 * @phpstan-type ChoiceData array{
 *     url?: string,
 *     b64_json?: string
 * }
 * @phpstan-type UsageData array{
 *     input_tokens?: int,
 *     output_tokens?: int,
 *     total_tokens?: int
 * }
 * @phpstan-type ResponseData array{
 *     id?: string,
 *     data?: list<ChoiceData>,
 *     usage?: UsageData
 * }
 */
abstract class AbstractOpenAiCompatibleImageGenerationModel extends AbstractApiBasedModel implements ImageGenerationModelInterface
{
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    public function generateImageResult(array $prompt): GenerativeAiResult
    {
        $httpTransporter = $this->getHttpTransporter();
        $params = $this->prepareGenerateImageParams($prompt);
        $request = $this->createRequest(HttpMethodEnum::POST(), 'images/generations', ['Content-Type' => 'application/json'], $params);
        // Add authentication credentials to the request.
        $request = $this->getRequestAuthentication()->authenticateRequest($request);
        // Send and process the request.
        $response = $httpTransporter->send($request);
        $this->throwIfNotSuccessful($response);
        return $this->parseResponseToGenerativeAiResult($response, isset($params['output_format']) && is_string($params['output_format']) ? "image/{$params['output_format']}" : 'image/png');
    }
    /**
     * Prepares the given prompt and the model configuration into parameters for the API request.
     *
     * @since 0.1.0
     *
     * @param list<Message> $prompt The prompt to generate an image for. Either a single message or a list of messages
     *                              from a chat. However as of today, OpenAI compatible image generation endpoints only
     *                              support a single user message.
     * @return ImageGenerationParams The parameters for the API request.
     */
    protected function prepareGenerateImageParams(array $prompt): array
    {
        $config = $this->getConfig();
        $params = ['model' => $this->metadata()->getId(), 'prompt' => $this->preparePromptParam($prompt)];
        $candidateCount = $config->getCandidateCount();
        if ($candidateCount !== null) {
            $params['n'] = $candidateCount;
        }
        $outputFileType = $config->getOutputFileType();
        if ($outputFileType !== null) {
            $params['response_format'] = $outputFileType->isRemote() ? 'url' : 'b64_json';
        } else {
            // The 'response_format' parameter is required, so we default to 'b64_json' if not set.
            $params['response_format'] = 'b64_json';
        }
        $outputMimeType = $config->getOutputMimeType();
        if ($outputMimeType !== null) {
            $params['output_format'] = preg_replace('/^image\//', '', $outputMimeType);
        }
        $outputMediaOrientation = $config->getOutputMediaOrientation();
        $outputMediaAspectRatio = $config->getOutputMediaAspectRatio();
        if ($outputMediaOrientation !== null || $outputMediaAspectRatio !== null) {
            $params['size'] = $this->prepareSizeParam($outputMediaOrientation, $outputMediaAspectRatio);
        }
        /*
         * Any custom options are added to the parameters as well.
         * This allows developers to pass other options that may be more niche or not yet supported by the SDK.
         */
        $customOptions = $config->getCustomOptions();
        foreach ($customOptions as $key => $value) {
            if (isset($params[$key])) {
                throw new InvalidArgumentException(sprintf('The custom option "%s" conflicts with an existing parameter.', $key));
            }
            $params[$key] = $value;
        }
        /** @var ImageGenerationParams $params */
        return $params;
    }
    /**
     * Prepares the prompt parameter for the API request.
     *
     * @since 0.1.0
     *
     * @param list<Message> $messages The messages to prepare. However as of today, OpenAI compatible image generation
     *                                endpoints only support a single user message.
     * @return string The prepared prompt parameter.
     */
    protected function preparePromptParam(array $messages): string
    {
        if (count($messages) !== 1) {
            throw new InvalidArgumentException('The API requires a single user message as prompt.');
        }
        $message = $messages[0];
        if (!$message->getRole()->isUser()) {
            throw new InvalidArgumentException('The API requires a user message as prompt.');
        }
        $text = null;
        foreach ($message->getParts() as $part) {
            $text = $part->getText();
            if ($text !== null) {
                break;
            }
        }
        if ($text === null) {
            throw new InvalidArgumentException('The API requires a single text message part as prompt.');
        }
        return $text;
    }
    /**
     * Prepares the size parameter for the API request.
     *
     * @since 0.1.0
     *
     * @param MediaOrientationEnum|null $orientation The desired media orientation.
     * @param string|null $aspectRatio The desired media aspect ratio.
     * @return string The prepared size parameter.
     */
    protected function prepareSizeParam(?MediaOrientationEnum $orientation, ?string $aspectRatio): string
    {
        // Use aspect ratio if set, as it is more specific.
        if ($aspectRatio !== null) {
            switch ($aspectRatio) {
                case '1:1':
                    return '1024x1024';
                case '3:2':
                    return '1536x1024';
                case '7:4':
                    return '1792x1024';
                case '2:3':
                    return '1024x1536';
                case '4:7':
                    return '1024x1792';
                default:
                    throw new InvalidArgumentException('The aspect ratio "' . $aspectRatio . '" is not supported.');
            }
        }
        // This should always have a value, as the method is only called if at least one or the other is set.
        if ($orientation !== null) {
            if ($orientation->isLandscape()) {
                return '1536x1024';
            }
            if ($orientation->isPortrait()) {
                return '1024x1536';
            }
        }
        return '1024x1024';
    }
    /**
     * Creates a request object for the provider's API.
     *
     * Implementations should use $this->getRequestOptions() to attach any
     * configured request options to the Request.
     *
     * @since 0.1.0
     *
     * @param HttpMethodEnum $method The HTTP method.
     * @param string $path The API endpoint path, relative to the base URI.
     * @param array<string, string|list<string>> $headers The request headers.
     * @param string|array<string, mixed>|null $data The request data.
     * @return Request The request object.
     */
    abstract protected function createRequest(HttpMethodEnum $method, string $path, array $headers = [], $data = null): Request;
    /**
     * Throws an exception if the response is not successful.
     *
     * @since 0.1.0
     *
     * @param Response $response The HTTP response to check.
     * @throws ResponseException If the response is not successful.
     */
    protected function throwIfNotSuccessful(Response $response): void
    {
        /*
         * While this method only calls the utility method, it's important to have it here as a protected method so
         * that child classes can override it if needed.
         */
        ResponseUtil::throwIfNotSuccessful($response);
    }
    /**
     * Parses the response from the API endpoint to a generative AI result.
     *
     * @since 0.1.0
     *
     * @param Response $response The response from the API endpoint.
     * @param string   $expectedMimeType The expected MIME type the response is in.
     * @return GenerativeAiResult The parsed generative AI result.
     */
    protected function parseResponseToGenerativeAiResult(Response $response, string $expectedMimeType = 'image/png'): GenerativeAiResult
    {
        /** @var ResponseData $responseData */
        $responseData = $response->getData();
        if (!isset($responseData['data']) || !$responseData['data']) {
            throw ResponseException::fromMissingData($this->providerMetadata()->getName(), 'data');
        }
        if (!is_array($responseData['data'])) {
            throw ResponseException::fromInvalidData($this->providerMetadata()->getName(), 'data', 'The value must be an array.');
        }
        $candidates = [];
        foreach ($responseData['data'] as $index => $choiceData) {
            if (!is_array($choiceData) || array_is_list($choiceData)) {
                throw ResponseException::fromInvalidData($this->providerMetadata()->getName(), "data[{$index}]", 'The value must be an associative array.');
            }
            $candidates[] = $this->parseResponseChoiceToCandidate($choiceData, $index, $expectedMimeType);
        }
        $id = $this->getResultId($responseData);
        if (isset($responseData['usage']) && is_array($responseData['usage'])) {
            $usage = $responseData['usage'];
            $tokenUsage = new TokenUsage($usage['input_tokens'] ?? 0, $usage['output_tokens'] ?? 0, $usage['total_tokens'] ?? 0);
        } else {
            $tokenUsage = new TokenUsage(0, 0, 0);
        }
        // Use any other data from the response as provider-specific response metadata.
        $providerMetadata = $responseData;
        unset($providerMetadata['id'], $providerMetadata['data'], $providerMetadata['usage']);
        return new GenerativeAiResult($id, $candidates, $tokenUsage, $this->providerMetadata(), $this->metadata(), $providerMetadata);
    }
    /**
     * Parses a single choice from the API response into a Candidate object.
     *
     * @since 0.1.0
     *
     * @param ChoiceData $choiceData The choice data from the API response.
     * @param int $index The index of the choice in the choices array.
     * @param string   $expectedMimeType The expected MIME type the response is in.
     * @return Candidate The parsed candidate.
     * @throws RuntimeException If the choice data is invalid.
     */
    protected function parseResponseChoiceToCandidate(array $choiceData, int $index, string $expectedMimeType = 'image/png'): Candidate
    {
        if (isset($choiceData['url']) && is_string($choiceData['url'])) {
            $imageFile = new File($choiceData['url'], $expectedMimeType);
        } elseif (isset($choiceData['b64_json']) && is_string($choiceData['b64_json'])) {
            $imageFile = new File($choiceData['b64_json'], $expectedMimeType);
        } else {
            throw ResponseException::fromInvalidData($this->providerMetadata()->getName(), "choices[{$index}]", 'The value must contain either a url or b64_json key with a string value.');
        }
        $parts = [new MessagePart($imageFile)];
        $message = new Message(MessageRoleEnum::model(), $parts);
        return new Candidate($message, FinishReasonEnum::stop());
    }
    /**
     * Extracts the result ID from the API response data.
     *
     * @since 0.4.0
     *
     * @param array<string, mixed> $responseData The response data from the API.
     * @return string The result ID.
     */
    protected function getResultId(array $responseData): string
    {
        return isset($responseData['id']) && is_string($responseData['id']) ? $responseData['id'] : '';
    }
}
