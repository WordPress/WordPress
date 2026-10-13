<?php

declare (strict_types=1);
namespace WordPress\AiClient\Messages\Util;

use WordPress\AiClient\Messages\Enums\ModalityEnum;
/**
 * Utility class for building modality combinations.
 *
 * @since 1.4.0
 */
class ModalityCombinationsUtil
{
    /**
     * Builds all modality combinations that always include the required modalities
     * with every possible subset of the optional modalities.
     *
     * Uses binary representation to enumerate all 2^n subsets of the optional
     * modalities. Each subset is merged with the required modalities to form one
     * complete combination. Modality lists are unordered, so only unique
     * combinations (not permutations) are produced.
     *
     * @since 1.4.0
     *
     * @param list<ModalityEnum> $required Required modalities included in every combination.
     * @param list<ModalityEnum> $optional Optional modalities to generate subsets from.
     * @return list<list<ModalityEnum>> List of modality combinations, each containing
     *                                  all required modalities plus a unique subset of
     *                                  the optional modalities.
     */
    public static function buildCombinations(array $required, array $optional): array
    {
        $combinations = [];
        $count = count($optional);
        $subsetCount = 1 << $count;
        // 2^count.
        for ($i = 0; $i < $subsetCount; $i++) {
            $combo = $required;
            for ($j = 0; $j < $count; $j++) {
                if ($i & 1 << $j) {
                    $combo[] = $optional[$j];
                }
            }
            $combinations[] = $combo;
        }
        return $combinations;
    }
}
