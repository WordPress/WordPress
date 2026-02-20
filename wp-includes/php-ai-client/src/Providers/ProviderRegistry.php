<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers;

use WordPress\AiClientDependencies\Http\Discovery\Exception\NotFoundException as DiscoveryNotFoundException;
use WordPress\AiClient\Common\Exception\InvalidArgumentException;
use WordPress\AiClient\Common\Exception\RuntimeException;
use WordPress\AiClient\Providers\Contracts\ProviderInterface;
use WordPress\AiClient\Providers\Contracts\ProviderWithOperationsHandlerInterface;
use WordPress\AiClient\Providers\DTO\ProviderMetadata;
use WordPress\AiClient\Providers\DTO\ProviderModelsMetadata;
use WordPress\AiClient\Providers\Http\Contracts\HttpTransporterInterface;
use WordPress\AiClient\Providers\Http\Contracts\RequestAuthenticationInterface;
use WordPress\AiClient\Providers\Http\Contracts\WithHttpTransporterInterface;
use WordPress\AiClient\Providers\Http\Contracts\WithRequestAuthenticationInterface;
use WordPress\AiClient\Providers\Http\HttpTransporterFactory;
use WordPress\AiClient\Providers\Http\Traits\WithHttpTransporterTrait;
use WordPress\AiClient\Providers\Models\Contracts\ModelInterface;
use WordPress\AiClient\Providers\Models\DTO\ModelConfig;
use WordPress\AiClient\Providers\Models\DTO\ModelMetadata;
use WordPress\AiClient\Providers\Models\DTO\ModelRequirements;
/**
 * Registry for managing AI providers and their models.
 *
 * This class provides a centralized way to register AI providers, discover
 * their capabilities, and find suitable models based on requirements.
 *
 * @since 0.1.0
 */
class ProviderRegistry implements WithHttpTransporterInterface
{
    use WithHttpTransporterTrait {
        setHttpTransporter as setHttpTransporterOriginal;
    }
    /**
     * @var array<string, class-string<ProviderInterface>> Mapping of provider IDs to class names.
     */
    private array $registeredIdsToClassNames = [];
    /**
     * @var array<class-string<ProviderInterface>, string> Mapping of provider class names to IDs.
     */
    private array $registeredClassNamesToIds = [];
    /**
     * @var array<class-string<ProviderInterface>, RequestAuthenticationInterface> Mapping of provider class names to
     *                                                                             authentication instances.
     */
    private array $providerAuthenticationInstances = [];
    /**
     * Registers a provider class with the registry.
     *
     * @since 0.1.0
     *
     * @param class-string<ProviderInterface> $className The fully qualified provider class name implementing the
     * ProviderInterface
     * @throws InvalidArgumentException If the class doesn't exist or implement the required interface.
     */
    public function registerProvider(string $className): void
    {
        if (!class_exists($className)) {
            throw new InvalidArgumentException(sprintf('Provider class does not exist: %s', $className));
        }
        // Validate that class implements ProviderInterface
        if (!is_subclass_of($className, ProviderInterface::class)) {
            throw new InvalidArgumentException(sprintf('Provider class must implement %s: %s', ProviderInterface::class, $className));
        }
        $metadata = $className::metadata();
        if (!$metadata instanceof ProviderMetadata) {
            throw new InvalidArgumentException(sprintf('Provider must return ProviderMetadata from metadata() method: %s', $className));
        }
        // If there is already a HTTP transporter instance set, hook it up to the provider as needed.
        try {
            $httpTransporter = $this->getHttpTransporter();
        } catch (RuntimeException $e) {
            /*
             * If this fails, it's okay. There is no defined sequence between setting the HTTP transporter in the
             * registry and registering providers in it, so it might be that the transporter is set later. It will be
             * hooked up then.
             * But for now we can ignore this exception and attempt to set the default HTTP transporter, if possible.
             */
            try {
                $this->setHttpTransporter(HttpTransporterFactory::createTransporter());
                $httpTransporter = $this->getHttpTransporter();
            } catch (DiscoveryNotFoundException $e) {
                /*
                 * If no HTTP client implementation can be discovered yet, we can ignore this for now.
                 * It might be set later, so it's not a hard error at this point.
                 * We'll try again the next time a provider is registered, or maybe by that time an explicit
                 * HTTP transporter will have been set.
                 */
            }
        }
        if (isset($httpTransporter)) {
            $this->setHttpTransporterForProvider($className, $httpTransporter);
        }
        // Hook up the request authentication instance, using a default if not set.
        if (!isset($this->providerAuthenticationInstances[$className])) {
            $defaultProviderAuthentication = $this->createDefaultProviderRequestAuthentication($className);
            if ($defaultProviderAuthentication !== null) {
                $this->providerAuthenticationInstances[$className] = $defaultProviderAuthentication;
            }
        }
        if (isset($this->providerAuthenticationInstances[$className])) {
            $this->setRequestAuthenticationForProvider($className, $this->providerAuthenticationInstances[$className]);
        }
        $this->registeredIdsToClassNames[$metadata->getId()] = $className;
        $this->registeredClassNamesToIds[$className] = $metadata->getId();
    }
    /**
     * Gets a list of all registered provider IDs.
     *
     * @since 0.1.0
     *
     * @return list<string> List of registered provider IDs.
     */
    public function getRegisteredProviderIds(): array
    {
        return array_keys($this->registeredIdsToClassNames);
    }
    /**
     * Checks if a provider is registered.
     *
     * @since 0.1.0
     *
     * @param string|class-string<ProviderInterface> $idOrClassName The provider ID or class name to check.
     * @return bool True if the provider is registered.
     */
    public function hasProvider(string $idOrClassName): bool
    {
        return $this->isRegisteredId($idOrClassName) || $this->isRegisteredClassName($idOrClassName);
    }
    /**
     * Gets the class name for a registered provider.
     *
     * @since 0.1.0
     *
     * @param string|class-string<ProviderInterface> $idOrClassName The provider ID or class name.
     * @return class-string<ProviderInterface> The provider class name.
     * @throws InvalidArgumentException If the provider is not registered.
     */
    public function getProviderClassName(string $idOrClassName): string
    {
        // If it's already a class name, return it
        if ($this->isRegisteredClassName($idOrClassName)) {
            return $idOrClassName;
        }
        // If it's a registered ID, return its class name
        if ($this->isRegisteredId($idOrClassName)) {
            return $this->registeredIdsToClassNames[$idOrClassName];
        }
        // Not found
        throw new InvalidArgumentException(sprintf('Provider not registered: %s', $idOrClassName));
    }
    /**
     * Gets the provider ID for a registered provider.
     *
     * @since 0.2.0
     *
     * @param string|class-string<ProviderInterface> $idOrClassName The provider ID or class name.
     * @return string The provider ID.
     * @throws InvalidArgumentException If the provider is not registered.
     */
    public function getProviderId(string $idOrClassName): string
    {
        // If it's already an ID, return it
        if ($this->isRegisteredId($idOrClassName)) {
            return $idOrClassName;
        }
        // If it's a registered class name, return its ID
        if ($this->isRegisteredClassName($idOrClassName)) {
            return $this->registeredClassNamesToIds[$idOrClassName];
        }
        // Not found
        throw new InvalidArgumentException(sprintf('Provider not registered: %s', $idOrClassName));
    }
    /**
     * Checks if a provider is properly configured.
     *
     * @since 0.1.0
     *
     * @param string|class-string<ProviderInterface> $idOrClassName The provider ID or class name.
     * @return bool True if the provider is configured and ready to use.
     */
    public function isProviderConfigured(string $idOrClassName): bool
    {
        try {
            $className = $this->resolveProviderClassName($idOrClassName);
            // Use static method from ProviderInterface
            /** @var class-string<ProviderInterface> $className */
            $availability = $className::availability();
            return $availability->isConfigured();
        } catch (InvalidArgumentException $e) {
            return \false;
        }
    }
    /**
     * Finds models across all available providers that support the given requirements.
     *
     * @since 0.1.0
     *
     * @param ModelRequirements $modelRequirements The requirements to match against.
     * @return list<ProviderModelsMetadata> List of provider models metadata that match requirements.
     */
    public function findModelsMetadataForSupport(ModelRequirements $modelRequirements): array
    {
        $results = [];
        foreach ($this->registeredIdsToClassNames as $providerId => $className) {
            $providerResults = $this->findProviderModelsMetadataForSupport($providerId, $modelRequirements);
            if (!empty($providerResults)) {
                // Use static method from ProviderInterface
                /** @var class-string<ProviderInterface> $className */
                $providerMetadata = $className::metadata();
                $results[] = new ProviderModelsMetadata($providerMetadata, $providerResults);
            }
        }
        return $results;
    }
    /**
     * Finds models within a specific available provider that support the given requirements.
     *
     * @since 0.1.0
     *
     * @param string $idOrClassName The provider ID or class name.
     * @param ModelRequirements $modelRequirements The requirements to match against.
     * @return list<ModelMetadata> List of model metadata that match requirements.
     */
    public function findProviderModelsMetadataForSupport(string $idOrClassName, ModelRequirements $modelRequirements): array
    {
        $className = $this->resolveProviderClassName($idOrClassName);
        // If the provider is not configured, there is no way to use it, so it is considered unavailable.
        if (!$this->isProviderConfigured($className)) {
            return [];
        }
        $modelMetadataDirectory = $className::modelMetadataDirectory();
        // Filter models that meet requirements
        $matchingModels = [];
        foreach ($modelMetadataDirectory->listModelMetadata() as $modelMetadata) {
            if ($modelRequirements->areMetBy($modelMetadata)) {
                $matchingModels[] = $modelMetadata;
            }
        }
        return $matchingModels;
    }
    /**
     * Gets a configured model instance from a provider.
     *
     * @since 0.1.0
     *
     * @param string|class-string<ProviderInterface> $idOrClassName The provider ID or class name.
     * @param string $modelId The model identifier.
     * @param ModelConfig|null $modelConfig The model configuration.
     * @return ModelInterface The configured model instance.
     * @throws InvalidArgumentException If provider or model is not found.
     */
    public function getProviderModel(string $idOrClassName, string $modelId, ?ModelConfig $modelConfig = null): ModelInterface
    {
        $className = $this->resolveProviderClassName($idOrClassName);
        $modelInstance = $className::model($modelId, $modelConfig);
        $this->bindModelDependencies($modelInstance);
        return $modelInstance;
    }
    /**
     * Binds dependencies to a model instance.
     *
     * This method injects required dependencies such as HTTP transporter
     * and authentication into model instances that need them.
     *
     * @since 0.1.0
     *
     * @param ModelInterface $modelInstance The model instance to bind dependencies to.
     * @return void
     */
    public function bindModelDependencies(ModelInterface $modelInstance): void
    {
        $className = $this->resolveProviderClassName($modelInstance->providerMetadata()->getId());
        if ($modelInstance instanceof WithHttpTransporterInterface) {
            $modelInstance->setHttpTransporter($this->getHttpTransporter());
        }
        if ($modelInstance instanceof WithRequestAuthenticationInterface) {
            $requestAuthentication = $this->getProviderRequestAuthentication($className);
            if ($requestAuthentication !== null) {
                $modelInstance->setRequestAuthentication($requestAuthentication);
            }
        }
    }
    /**
     * Gets the class name for a registered provider (handles both ID and class name input).
     *
     * @param string|class-string<ProviderInterface> $idOrClassName The provider ID or class name.
     * @return class-string<ProviderInterface> The provider class name.
     * @throws InvalidArgumentException If provider is not registered.
     */
    private function resolveProviderClassName(string $idOrClassName): string
    {
        // If it's already a class name, return it
        if ($this->isRegisteredClassName($idOrClassName)) {
            return $idOrClassName;
        }
        // If it's a registered ID, return its class name
        if ($this->isRegisteredId($idOrClassName)) {
            return $this->registeredIdsToClassNames[$idOrClassName];
        }
        // Not found
        throw new InvalidArgumentException(sprintf('Provider not registered: %s', $idOrClassName));
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    public function setHttpTransporter(HttpTransporterInterface $httpTransporter): void
    {
        $this->setHttpTransporterOriginal($httpTransporter);
        // Make sure all registered providers have the HTTP transporter hooked up as needed.
        foreach ($this->registeredIdsToClassNames as $className) {
            $this->setHttpTransporterForProvider($className, $httpTransporter);
        }
    }
    /**
     * Sets the request authentication instance for the given provider.
     *
     * @since 0.1.0
     *
     * @param string|class-string<ProviderInterface> $idOrClassName The provider ID or class name.
     * @param RequestAuthenticationInterface $requestAuthentication The request authentication instance.
     */
    public function setProviderRequestAuthentication(string $idOrClassName, RequestAuthenticationInterface $requestAuthentication): void
    {
        $className = $this->resolveProviderClassName($idOrClassName);
        $this->providerAuthenticationInstances[$className] = $requestAuthentication;
        $this->setRequestAuthenticationForProvider($className, $requestAuthentication);
    }
    /**
     * Gets the request authentication instance for the given provider, if set.
     *
     * @since 0.1.0
     *
     * @param string|class-string<ProviderInterface> $idOrClassName The provider ID or class name.
     * @return ?RequestAuthenticationInterface The request authentication instance, or null if not set.
     */
    public function getProviderRequestAuthentication(string $idOrClassName): ?RequestAuthenticationInterface
    {
        $className = $this->resolveProviderClassName($idOrClassName);
        if (!isset($this->providerAuthenticationInstances[$className])) {
            return null;
        }
        return $this->providerAuthenticationInstances[$className];
    }
    /**
     * Sets the HTTP transporter for a specific provider, hooking up its class instances.
     *
     * @since 0.1.0
     *
     * @param class-string<ProviderInterface> $className The provider class name.
     * @param HttpTransporterInterface $httpTransporter The HTTP transporter instance.
     */
    private function setHttpTransporterForProvider(string $className, HttpTransporterInterface $httpTransporter): void
    {
        $availability = $className::availability();
        if ($availability instanceof WithHttpTransporterInterface) {
            $availability->setHttpTransporter($httpTransporter);
        }
        $modelMetadataDirectory = $className::modelMetadataDirectory();
        if ($modelMetadataDirectory instanceof WithHttpTransporterInterface) {
            $modelMetadataDirectory->setHttpTransporter($httpTransporter);
        }
        if (is_subclass_of($className, ProviderWithOperationsHandlerInterface::class)) {
            $operationsHandler = $className::operationsHandler();
            if ($operationsHandler instanceof WithHttpTransporterInterface) {
                $operationsHandler->setHttpTransporter($httpTransporter);
            }
        }
    }
    /**
     * Sets the request authentication for a specific provider, hooking up its class instances.
     *
     * @since 0.1.0
     *
     * @param class-string<ProviderInterface> $className The provider class name.
     * @param RequestAuthenticationInterface $requestAuthentication The authentication instance.
     *
     * @throws InvalidArgumentException If the authentication instance is not of the expected type.
     */
    private function setRequestAuthenticationForProvider(string $className, RequestAuthenticationInterface $requestAuthentication): void
    {
        $authenticationMethod = $className::metadata()->getAuthenticationMethod();
        if ($authenticationMethod === null) {
            throw new InvalidArgumentException(sprintf('Provider %s does not expect any authentication, but got %s.', $className, get_class($requestAuthentication)));
        }
        $expectedClass = $authenticationMethod->getImplementationClass();
        if (!$requestAuthentication instanceof $expectedClass) {
            throw new InvalidArgumentException(sprintf('Provider %s expects authentication of type %s, but got %s.', $className, $expectedClass, get_class($requestAuthentication)));
        }
        $availability = $className::availability();
        if ($availability instanceof WithRequestAuthenticationInterface) {
            $availability->setRequestAuthentication($requestAuthentication);
        }
        $modelMetadataDirectory = $className::modelMetadataDirectory();
        if ($modelMetadataDirectory instanceof WithRequestAuthenticationInterface) {
            $modelMetadataDirectory->setRequestAuthentication($requestAuthentication);
        }
        if (is_subclass_of($className, ProviderWithOperationsHandlerInterface::class)) {
            $operationsHandler = $className::operationsHandler();
            if ($operationsHandler instanceof WithRequestAuthenticationInterface) {
                $operationsHandler->setRequestAuthentication($requestAuthentication);
            }
        }
    }
    /**
     * Creates a default request authentication instance for a provider.
     *
     * @since 0.1.0
     *
     * @param class-string<ProviderInterface> $className The provider class name.
     * @return ?RequestAuthenticationInterface The default request authentication instance, or null if not required or
     *                                         if no credential data can be found.
     */
    private function createDefaultProviderRequestAuthentication(string $className): ?RequestAuthenticationInterface
    {
        $providerMetadata = $className::metadata();
        $providerId = $providerMetadata->getId();
        $authenticationMethod = $providerMetadata->getAuthenticationMethod();
        if ($authenticationMethod === null) {
            return null;
        }
        $authenticationClass = $authenticationMethod->getImplementationClass();
        if ($authenticationClass === null) {
            return null;
        }
        $authenticationSchema = $authenticationClass::getJsonSchema();
        // Iterate over all JSON schema object properties to try to determine the necessary authentication data.
        $authenticationData = [];
        if (isset($authenticationSchema['properties']) && is_array($authenticationSchema['properties'])) {
            /** @var array<string, mixed> $details */
            foreach ($authenticationSchema['properties'] as $property => $details) {
                $envVarName = $this->getEnvVarName($providerId, $property);
                // Try to get the value from environment variable or constant.
                $envValue = getenv($envVarName);
                if ($envValue === \false) {
                    if (!defined($envVarName)) {
                        continue;
                        // Skip if neither environment variable nor constant is defined.
                    }
                    $envValue = constant($envVarName);
                    if (!is_scalar($envValue)) {
                        continue;
                    }
                }
                if (isset($details['type'])) {
                    switch ($details['type']) {
                        case 'boolean':
                            $authenticationData[$property] = filter_var($envValue, \FILTER_VALIDATE_BOOLEAN);
                            break;
                        case 'number':
                            $authenticationData[$property] = (int) $envValue;
                            break;
                        case 'string':
                        default:
                            $authenticationData[$property] = (string) $envValue;
                    }
                } else {
                    // Default to string if no type is specified.
                    $authenticationData[$property] = (string) $envValue;
                }
            }
            // If any required fields are missing, return null to avoid immediate errors.
            if (isset($authenticationSchema['required']) && is_array($authenticationSchema['required'])) {
                /** @var list<string> $requiredProperties */
                $requiredProperties = $authenticationSchema['required'];
                if (array_diff_key(array_flip($requiredProperties), $authenticationData)) {
                    return null;
                }
            }
        }
        /** @var RequestAuthenticationInterface */
        /** @var array<string, mixed> $authenticationData */
        return $authenticationClass::fromArray($authenticationData);
    }
    /**
     * Checks if the given value is a registered provider class name.
     *
     * @since 0.4.0
     *
     * @param string $idOrClassName The value to check.
     * @return bool True if it's a registered class name.
     * @phpstan-assert-if-true class-string<ProviderInterface> $idOrClassName
     */
    private function isRegisteredClassName(string $idOrClassName): bool
    {
        return isset($this->registeredClassNamesToIds[$idOrClassName]);
    }
    /**
     * Checks if the given value is a registered provider ID.
     *
     * @since 0.4.0
     *
     * @param string $idOrClassName The value to check.
     * @return bool True if it's a registered provider ID.
     */
    private function isRegisteredId(string $idOrClassName): bool
    {
        return isset($this->registeredIdsToClassNames[$idOrClassName]);
    }
    /**
     * Converts a provider ID and field name to a constant case environment variable name.
     *
     * @since 0.1.0
     *
     * @param string $providerId The provider ID.
     * @param string $field The field name.
     * @return string The environment variable name in CONSTANT_CASE.
     */
    private function getEnvVarName(string $providerId, string $field): string
    {
        // Convert camelCase or kebab-case or snake_case to CONSTANT_CASE.
        $constantCaseProviderId = strtoupper((string) preg_replace('/([a-z])([A-Z])/', '$1_$2', str_replace('-', '_', $providerId)));
        $constantCaseField = strtoupper((string) preg_replace('/([a-z])([A-Z])/', '$1_$2', str_replace('-', '_', $field)));
        return "{$constantCaseProviderId}_{$constantCaseField}";
    }
}
