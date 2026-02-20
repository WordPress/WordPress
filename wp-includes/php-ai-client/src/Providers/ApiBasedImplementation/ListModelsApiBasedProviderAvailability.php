<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\ApiBasedImplementation;

use Exception;
use WordPress\AiClient\Providers\Contracts\ModelMetadataDirectoryInterface;
use WordPress\AiClient\Providers\Contracts\ProviderAvailabilityInterface;
/**
 * Class to check availability for an API-based provider via a test request to the endpoint to list models.
 *
 * This class should be used for cloud-based providers that offer a model listing endpoint which requires
 * authentication. A request to this endpoint is used to determine if the provider is properly configured
 * with valid credentials.
 *
 * @since 0.1.0
 */
class ListModelsApiBasedProviderAvailability implements ProviderAvailabilityInterface
{
    /**
     * @var ModelMetadataDirectoryInterface The model metadata directory to use for checking availability.
     */
    private ModelMetadataDirectoryInterface $modelMetadataDirectory;
    /**
     * Constructor.
     *
     * @since 0.1.0
     *
     * @param ModelMetadataDirectoryInterface $modelMetadataDirectory The model metadata directory to use for checking
     *                                                                availability.
     */
    public function __construct(ModelMetadataDirectoryInterface $modelMetadataDirectory)
    {
        $this->modelMetadataDirectory = $modelMetadataDirectory;
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    public function isConfigured(): bool
    {
        try {
            // Attempt to list models to check if the provider is available.
            $this->modelMetadataDirectory->listModelMetadata();
            return \true;
        } catch (Exception $e) {
            // If an exception occurs, the provider is not available.
            return \false;
        }
    }
}
