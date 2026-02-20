<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\OpenAiCompatibleImplementation;

use WordPress\AiClient\Common\Exception\InvalidArgumentException;
use WordPress\AiClient\Common\Exception\RuntimeException;
use WordPress\AiClient\Messages\DTO\Message;
use WordPress\AiClient\Messages\DTO\MessagePart;
use WordPress\AiClient\Messages\Enums\MessagePartChannelEnum;
use WordPress\AiClient\Messages\Enums\MessageRoleEnum;
use WordPress\AiClient\Messages\Enums\ModalityEnum;
use WordPress\AiClient\Providers\ApiBasedImplementation\AbstractApiBasedModel;
use WordPress\AiClient\Providers\Http\DTO\Request;
use WordPress\AiClient\Providers\Http\DTO\Response;
use WordPress\AiClient\Providers\Http\Enums\HttpMethodEnum;
use WordPress\AiClient\Providers\Http\Exception\ResponseException;
use WordPress\AiClient\Providers\Http\Util\ResponseUtil;
use WordPress\AiClient\Providers\Models\TextGeneration\Contracts\TextGenerationModelInterface;
use WordPress\AiClient\Results\DTO\Candidate;
use WordPress\AiClient\Results\DTO\GenerativeAiResult;
use WordPress\AiClient\Results\DTO\TokenUsage;
use WordPress\AiClient\Results\Enums\FinishReasonEnum;
use WordPress\AiClient\Tools\DTO\FunctionCall;
use WordPress\AiClient\Tools\DTO\FunctionDeclaration;
/**
 * Base class for a text generation model for providers that implement OpenAI's API format.
 *
 * This abstract class is designed to work with any AI provider that offers an OpenAI-compatible
 * API endpoint, including but not limited to Anthropic, Google, and other providers
 * that have adopted OpenAI's API specification as a standard interface.
 *
 * @since 0.1.0
 *
 * @phpstan-type ToolCallData array{
 *     type?: string,
 *     id?: string,
 *     function?: array{
 *         name?: string,
 *         arguments: string|array<string, mixed>
 *     }
 * }
 * @phpstan-type MessageData array{
 *     role?: string,
 *     reasoning_content?: string,
 *     content?: string,
 *     tool_calls?: list<ToolCallData>
 * }
 * @phpstan-type ChoiceData array{
 *     message?: MessageData,
 *     finish_reason?: string
 * }
 * @phpstan-type UsageData array{
 *     prompt_tokens?: int,
 *     completion_tokens?: int,
 *     total_tokens?: int
 * }
 * @phpstan-type ResponseData array{
 *     id?: string,
 *     choices?: list<ChoiceData>,
 *     usage?: UsageData
 * }
 */
abstract class AbstractOpenAiCompatibleTextGenerationModel extends AbstractApiBasedModel implements TextGenerationModelInterface
{
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    final public function generateTextResult(array $prompt): GenerativeAiResult
    {
        $httpTransporter = $this->getHttpTransporter();
        $params = $this->prepareGenerateTextParams($prompt);
        $request = $this->createRequest(HttpMethodEnum::POST(), 'chat/completions', ['Content-Type' => 'application/json'], $params);
        // Add authentication credentials to the request.
        $request = $this->getRequestAuthentication()->authenticateRequest($request);
        // Send and process the request.
        $response = $httpTransporter->send($request);
        $this->throwIfNotSuccessful($response);
        return $this->parseResponseToGenerativeAiResult($response);
    }
    /**
     * Prepares the given prompt and the model configuration into parameters for the API request.
     *
     * @since 0.1.0
     *
     * @param list<Message> $prompt The prompt to generate text for. Either a single message or a list of messages
     *                              from a chat.
     * @return array<string, mixed> The parameters for the API request.
     */
    protected function prepareGenerateTextParams(array $prompt): array
    {
        $config = $this->getConfig();
        $params = ['model' => $this->metadata()->getId(), 'messages' => $this->prepareMessagesParam($prompt, $config->getSystemInstruction())];
        $outputModalities = $config->getOutputModalities();
        if (is_array($outputModalities)) {
            $this->validateOutputModalities($outputModalities);
            if (count($outputModalities) > 1) {
                $params['modalities'] = $this->prepareOutputModalitiesParam($outputModalities);
            }
        }
        $candidateCount = $config->getCandidateCount();
        if ($candidateCount !== null) {
            $params['n'] = $candidateCount;
        }
        $maxTokens = $config->getMaxTokens();
        if ($maxTokens !== null) {
            $params['max_tokens'] = $maxTokens;
        }
        $temperature = $config->getTemperature();
        if ($temperature !== null) {
            $params['temperature'] = $temperature;
        }
        $topP = $config->getTopP();
        if ($topP !== null) {
            $params['top_p'] = $topP;
        }
        $stopSequences = $config->getStopSequences();
        if (is_array($stopSequences)) {
            $params['stop'] = $stopSequences;
        }
        $presencePenalty = $config->getPresencePenalty();
        if ($presencePenalty !== null) {
            $params['presence_penalty'] = $presencePenalty;
        }
        $frequencyPenalty = $config->getFrequencyPenalty();
        if ($frequencyPenalty !== null) {
            $params['frequency_penalty'] = $frequencyPenalty;
        }
        $logprobs = $config->getLogprobs();
        if ($logprobs !== null) {
            $params['logprobs'] = $logprobs;
        }
        $topLogprobs = $config->getTopLogprobs();
        if ($topLogprobs !== null) {
            $params['top_logprobs'] = $topLogprobs;
        }
        $functionDeclarations = $config->getFunctionDeclarations();
        if (is_array($functionDeclarations)) {
            $params['tools'] = $this->prepareToolsParam($functionDeclarations);
        }
        $outputMimeType = $config->getOutputMimeType();
        if ('application/json' === $outputMimeType) {
            $outputSchema = $config->getOutputSchema();
            $params['response_format'] = $this->prepareResponseFormatParam($outputSchema);
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
        return $params;
    }
    /**
     * Prepares the messages parameter for the API request.
     *
     * @since 0.1.0
     *
     * @param list<Message> $messages The messages to prepare.
     * @param string|null $systemInstruction An optional system instruction to prepend to the messages.
     * @return list<array<string, mixed>> The prepared messages parameter.
     */
    protected function prepareMessagesParam(array $messages, ?string $systemInstruction = null): array
    {
        $messagesParam = array_map(function (Message $message): array {
            // Special case: Function response.
            $messageParts = $message->getParts();
            if (count($messageParts) === 1 && $messageParts[0]->getType()->isFunctionResponse()) {
                $functionResponse = $messageParts[0]->getFunctionResponse();
                if (!$functionResponse) {
                    // This should be impossible due to class internals, but still needs to be checked.
                    throw new RuntimeException('The function response typed message part must contain a function response.');
                }
                return ['role' => 'tool', 'content' => json_encode($functionResponse->getResponse()), 'tool_call_id' => $functionResponse->getId()];
            }
            $messageData = ['role' => $this->getMessageRoleString($message->getRole()), 'content' => array_values(array_filter(array_map([$this, 'getMessagePartContentData'], $messageParts)))];
            // Only include tool_calls if there are any (OpenAI rejects empty arrays).
            $toolCalls = array_values(array_filter(array_map([$this, 'getMessagePartToolCallData'], $messageParts)));
            if (!empty($toolCalls)) {
                $messageData['tool_calls'] = $toolCalls;
            }
            return $messageData;
        }, $messages);
        if ($systemInstruction) {
            array_unshift($messagesParam, [
                /*
                 * TODO: Replace this with 'developer' in the future.
                 * See https://platform.openai.com/docs/api-reference/chat/create#chat_create-messages
                 */
                'role' => 'system',
                'content' => [['type' => 'text', 'text' => $systemInstruction]],
            ]);
        }
        return $messagesParam;
    }
    /**
     * Returns the OpenAI API specific role string for the given message role.
     *
     * @since 0.1.0
     *
     * @param MessageRoleEnum $role The message role.
     * @return string The role for the API request.
     */
    protected function getMessageRoleString(MessageRoleEnum $role): string
    {
        if ($role === MessageRoleEnum::model()) {
            return 'assistant';
        }
        return 'user';
    }
    /**
     * Returns the OpenAI API specific content data for a message part.
     *
     * @since 0.1.0
     *
     * @param MessagePart $part The message part to get the data for.
     * @return ?array<string, mixed> The data for the message content part, or null if not applicable.
     * @throws InvalidArgumentException If the message part type or data is unsupported.
     */
    protected function getMessagePartContentData(MessagePart $part): ?array
    {
        $type = $part->getType();
        if ($type->isText()) {
            /*
             * The OpenAI Chat Completions API spec does not support annotating thought parts as input,
             * so we instead skip them.
             */
            if ($part->getChannel()->isThought()) {
                return null;
            }
            return ['type' => 'text', 'text' => $part->getText()];
        }
        if ($type->isFile()) {
            $file = $part->getFile();
            if (!$file) {
                // This should be impossible due to class internals, but still needs to be checked.
                throw new RuntimeException('The file typed message part must contain a file.');
            }
            if ($file->isRemote()) {
                if ($file->isImage()) {
                    return ['type' => 'image_url', 'image_url' => ['url' => $file->getUrl()]];
                }
                throw new InvalidArgumentException(sprintf('Unsupported MIME type "%s" for remote file message part.', $file->getMimeType()));
            }
            // Else, it is an inline file.
            if ($file->isImage()) {
                return ['type' => 'image_url', 'image_url' => ['url' => $file->getDataUri()]];
            }
            if ($file->isAudio()) {
                return ['type' => 'input_audio', 'input_audio' => ['data' => $file->getBase64Data(), 'format' => $file->getMimeTypeObject()->toExtension()]];
            }
            throw new InvalidArgumentException(sprintf('Unsupported MIME type "%s" for inline file message part.', $file->getMimeType()));
        }
        if ($type->isFunctionCall()) {
            // Skip, as this is separately included. See `getMessagePartToolCallData()`.
            return null;
        }
        if ($type->isFunctionResponse()) {
            // Special case: Function response.
            throw new InvalidArgumentException('The API only allows a single function response, as the only content of the message.');
        }
        throw new InvalidArgumentException(sprintf('Unsupported message part type "%s".', $type));
    }
    /**
     * Returns the OpenAI API specific tool calls data for a message part.
     *
     * @since 0.1.0
     *
     * @param MessagePart $part The message part to get the data for.
     * @return ?array<string, mixed> The data for the message tool call part, or null if not applicable.
     * @throws InvalidArgumentException If the message part type or data is unsupported.
     */
    protected function getMessagePartToolCallData(MessagePart $part): ?array
    {
        $type = $part->getType();
        if ($type->isFunctionCall()) {
            $functionCall = $part->getFunctionCall();
            if (!$functionCall) {
                // This should be impossible due to class internals, but still needs to be checked.
                throw new RuntimeException('The function call typed message part must contain a function call.');
            }
            $args = $functionCall->getArgs();
            /*
             * Ensure null or empty arrays become empty objects for JSON encoding.
             * While in theory the JSON schema could also dictate a type of
             * 'array', in practice function arguments are typically of type
             * 'object'. More importantly, the OpenAI API specification seems
             * to expect that, and does not support passing arrays as the root
             * value. The null check handles the case where FunctionCall normalizes
             * empty arrays to null.
             */
            if ($args === null || is_array($args) && count($args) === 0) {
                $args = new \stdClass();
            }
            return ['type' => 'function', 'id' => $functionCall->getId(), 'function' => ['name' => $functionCall->getName(), 'arguments' => json_encode($args)]];
        }
        // All other types are handled in `getMessagePartContentData()`.
        return null;
    }
    /**
     * Validates that the given output modalities to ensure that at least one output modality is text.
     *
     * @since 0.1.0
     *
     * @param array<ModalityEnum> $outputModalities The output modalities to validate.
     * @throws InvalidArgumentException If no text output modality is present.
     */
    protected function validateOutputModalities(array $outputModalities): void
    {
        // If no output modalities are set, it's fine, as we can assume text.
        if (count($outputModalities) === 0) {
            return;
        }
        foreach ($outputModalities as $modality) {
            if ($modality->isText()) {
                return;
            }
        }
        throw new InvalidArgumentException('A text output modality must be present when generating text.');
    }
    /**
     * Prepares the output modalities parameter for the API request.
     *
     * @since 0.1.0
     *
     * @param array<ModalityEnum> $modalities The modalities to prepare.
     * @return list<string> The prepared modalities parameter.
     */
    protected function prepareOutputModalitiesParam(array $modalities): array
    {
        $prepared = [];
        foreach ($modalities as $modality) {
            if ($modality->isText()) {
                $prepared[] = 'text';
            } elseif ($modality->isImage()) {
                $prepared[] = 'image';
            } elseif ($modality->isAudio()) {
                $prepared[] = 'audio';
            } else {
                throw new InvalidArgumentException(sprintf('Unsupported output modality "%s".', $modality));
            }
        }
        return $prepared;
    }
    /**
     * Prepares the tools parameter for the API request.
     *
     * @since 0.1.0
     *
     * @param list<FunctionDeclaration> $functionDeclarations The function declarations.
     * @return list<array<string, mixed>> The prepared tools parameter.
     */
    protected function prepareToolsParam(array $functionDeclarations): array
    {
        $tools = [];
        foreach ($functionDeclarations as $functionDeclaration) {
            $tools[] = ['type' => 'function', 'function' => $functionDeclaration->toArray()];
        }
        return $tools;
    }
    /**
     * Prepares the response format parameter for the API request.
     *
     * This is only called if the output MIME type is `application/json`.
     *
     * @since 0.1.0
     *
     * @param array<string, mixed>|null $outputSchema The output schema.
     * @return array<string, mixed> The prepared response format parameter.
     */
    protected function prepareResponseFormatParam(?array $outputSchema): array
    {
        if (is_array($outputSchema)) {
            return ['type' => 'json_schema', 'json_schema' => $outputSchema];
        }
        return ['type' => 'json_object'];
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
     * @return GenerativeAiResult The parsed generative AI result.
     */
    protected function parseResponseToGenerativeAiResult(Response $response): GenerativeAiResult
    {
        /** @var ResponseData $responseData */
        $responseData = $response->getData();
        if (!isset($responseData['choices']) || !$responseData['choices']) {
            throw ResponseException::fromMissingData($this->providerMetadata()->getName(), 'choices');
        }
        if (!is_array($responseData['choices'])) {
            throw ResponseException::fromInvalidData($this->providerMetadata()->getName(), 'choices', 'The value must be an array.');
        }
        $candidates = [];
        foreach ($responseData['choices'] as $index => $choiceData) {
            if (!is_array($choiceData) || array_is_list($choiceData)) {
                throw ResponseException::fromInvalidData($this->providerMetadata()->getName(), "choices[{$index}]", 'The value must be an associative array.');
            }
            $candidates[] = $this->parseResponseChoiceToCandidate($choiceData, $index);
        }
        $id = isset($responseData['id']) && is_string($responseData['id']) ? $responseData['id'] : '';
        if (isset($responseData['usage']) && is_array($responseData['usage'])) {
            $usage = $responseData['usage'];
            $tokenUsage = new TokenUsage($usage['prompt_tokens'] ?? 0, $usage['completion_tokens'] ?? 0, $usage['total_tokens'] ?? 0);
        } else {
            $tokenUsage = new TokenUsage(0, 0, 0);
        }
        // Use any other data from the response as provider-specific response metadata.
        $additionalData = $responseData;
        unset($additionalData['id'], $additionalData['choices'], $additionalData['usage']);
        return new GenerativeAiResult($id, $candidates, $tokenUsage, $this->providerMetadata(), $this->metadata(), $additionalData);
    }
    /**
     * Parses a single choice from the API response into a Candidate object.
     *
     * @since 0.1.0
     *
     * @param ChoiceData $choiceData The choice data from the API response.
     * @param int $index The index of the choice in the choices array.
     * @return Candidate The parsed candidate.
     * @throws RuntimeException If the choice data is invalid.
     */
    protected function parseResponseChoiceToCandidate(array $choiceData, int $index): Candidate
    {
        if (!isset($choiceData['message']) || !is_array($choiceData['message']) || array_is_list($choiceData['message'])) {
            throw ResponseException::fromMissingData($this->providerMetadata()->getName(), "choices[{$index}].message");
        }
        if (!isset($choiceData['finish_reason']) || !is_string($choiceData['finish_reason'])) {
            throw ResponseException::fromMissingData($this->providerMetadata()->getName(), "choices[{$index}].finish_reason");
        }
        $messageData = $choiceData['message'];
        $message = $this->parseResponseChoiceMessage($messageData, $index);
        switch ($choiceData['finish_reason']) {
            case 'stop':
                $finishReason = FinishReasonEnum::stop();
                break;
            case 'length':
                $finishReason = FinishReasonEnum::length();
                break;
            case 'content_filter':
                $finishReason = FinishReasonEnum::contentFilter();
                break;
            case 'tool_calls':
                $finishReason = FinishReasonEnum::toolCalls();
                break;
            default:
                throw ResponseException::fromInvalidData($this->providerMetadata()->getName(), "choices[{$index}].finish_reason", sprintf('Invalid finish reason "%s".', $choiceData['finish_reason']));
        }
        return new Candidate($message, $finishReason);
    }
    /**
     * Parses the message from a choice in the API response.
     *
     * @since 0.1.0
     *
     * @param MessageData $messageData The message data from the API response.
     * @param int $index The index of the choice in the choices array.
     * @return Message The parsed message.
     */
    protected function parseResponseChoiceMessage(array $messageData, int $index): Message
    {
        $role = isset($messageData['role']) && 'user' === $messageData['role'] ? MessageRoleEnum::user() : MessageRoleEnum::model();
        $parts = $this->parseResponseChoiceMessageParts($messageData, $index);
        return new Message($role, $parts);
    }
    /**
     * Parses the message parts from a choice in the API response.
     *
     * @since 0.1.0
     *
     * @param MessageData $messageData The message data from the API response.
     * @param int $index The index of the choice in the choices array.
     * @return MessagePart[] The parsed message parts.
     */
    protected function parseResponseChoiceMessageParts(array $messageData, int $index): array
    {
        $parts = [];
        if (isset($messageData['reasoning_content']) && is_string($messageData['reasoning_content'])) {
            $parts[] = new MessagePart($messageData['reasoning_content'], MessagePartChannelEnum::thought());
        }
        if (isset($messageData['content']) && is_string($messageData['content'])) {
            $parts[] = new MessagePart($messageData['content']);
        }
        if (isset($messageData['tool_calls']) && is_array($messageData['tool_calls'])) {
            foreach ($messageData['tool_calls'] as $toolCallIndex => $toolCallData) {
                $toolCallPart = $this->parseResponseChoiceMessageToolCallPart($toolCallData);
                if (!$toolCallPart) {
                    throw ResponseException::fromInvalidData($this->providerMetadata()->getName(), "choices[{$index}].message.tool_calls[{$toolCallIndex}]", 'The response includes a tool call of an unexpected type.');
                }
                $parts[] = $toolCallPart;
            }
        }
        return $parts;
    }
    /**
     * Parses a tool call part from the API response.
     *
     * @since 0.1.0
     *
     * @param ToolCallData $toolCallData The tool call data from the API response.
     * @return MessagePart|null The parsed message part for the tool call, or null if not applicable.
     */
    protected function parseResponseChoiceMessageToolCallPart(array $toolCallData): ?MessagePart
    {
        /*
         * For now, only function calls are supported.
         *
         * Not all OpenAI compatible APIs include a 'type' key, so we only check its value if it is set.
         */
        if (isset($toolCallData['type']) && 'function' !== $toolCallData['type'] || !isset($toolCallData['function']) || !is_array($toolCallData['function'])) {
            return null;
        }
        $functionArguments = is_string($toolCallData['function']['arguments']) ? json_decode($toolCallData['function']['arguments'], \true) : $toolCallData['function']['arguments'];
        $functionCall = new FunctionCall(isset($toolCallData['id']) && is_string($toolCallData['id']) ? $toolCallData['id'] : null, isset($toolCallData['function']['name']) && is_string($toolCallData['function']['name']) ? $toolCallData['function']['name'] : null, $functionArguments);
        return new MessagePart($functionCall);
    }
}
