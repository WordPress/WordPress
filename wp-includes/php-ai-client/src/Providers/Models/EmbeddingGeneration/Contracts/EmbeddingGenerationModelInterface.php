<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\Models\EmbeddingGeneration\Contracts;

use WordPress\AiClient\Messages\DTO\MessagePart;
use WordPress\AiClient\Results\DTO\EmbeddingResult;
/**
 * Interface for models that support embedding generation.
 *
 * @since 1.4.0
 */
interface EmbeddingGenerationModelInterface
{
    /**
     * Generates embeddings from one or more inputs.
     *
     * @since 1.4.0
     *
     * @param list<MessagePart> $inputs The inputs to embed, one embedding generated per input.
     * @return EmbeddingResult Result containing one embedding per input, in input order.
     */
    public function generateEmbeddingResult(array $inputs): EmbeddingResult;
}
