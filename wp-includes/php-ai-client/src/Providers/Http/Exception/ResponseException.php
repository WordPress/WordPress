<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\Http\Exception;

use WordPress\AiClient\Common\Exception\RuntimeException;
/**
 * Exception class for HTTP response errors.
 *
 * This is used when response data is unexpected or malformed,
 * typically indicating that a provider changed in ways our code
 * is not aware of or when parsing response data fails.
 *
 * @since 0.1.0
 */
class ResponseException extends RuntimeException
{
    /**
     * Creates a ResponseException for missing expected data.
     *
     * @since 0.2.0
     *
     * @param string $apiName The name of the API/provider.
     * @param string $fieldName The field that was expected but missing.
     * @return self
     */
    public static function fromMissingData(string $apiName, string $fieldName): self
    {
        $message = sprintf('Unexpected %s API response: Missing the "%s" key.', $apiName, $fieldName);
        return new self($message);
    }
    /**
     * Creates a ResponseException from invalid data in an API response.
     *
     * @since 0.2.0
     *
     * @param string $apiName The name of the API service (e.g., 'OpenAI', 'Anthropic').
     * @param string $fieldName The field that was invalid.
     * @param string $message The specific error message describing the invalid data.
     * @return self
     */
    public static function fromInvalidData(string $apiName, string $fieldName, string $message): self
    {
        return new self(sprintf('Unexpected %s API response: Invalid "%s" key: %s', $apiName, $fieldName, $message));
    }
}
