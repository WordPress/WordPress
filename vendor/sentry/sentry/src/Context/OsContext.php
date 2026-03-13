<?php

declare(strict_types=1);

namespace Sentry\Context;

/**
 * This class stores information about the operating system of the server.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */
final class OsContext
{
    /**
     * @var string The name of the operating system
     */
    private $name;

    /**
     * @var string|null The version of the operating system
     */
    private $version;

    /**
     * @var string|null The internal build revision of the operating system
     */
    private $build;

    /**
     * @var string|null An independent kernel version string
     */
    private $kernelVersion;

    /**
     * @var string|null
     */
    private $machineType;

    /**
     * Constructor.
     *
     * @param string      $name          The name of the operating system
     * @param string|null $version       The version of the operating system
     * @param string|null $build         The internal build revision of the operating system
     * @param string|null $kernelVersion An independent kernel version string
     * @param string|null $machineType   The machine type
     */
    public function __construct(
        string $name,
        ?string $version = null,
        ?string $build = null,
        ?string $kernelVersion = null,
        ?string $machineType = null
    ) {
        if (trim($name) === '') {
            throw new \InvalidArgumentException('The $name argument cannot be an empty string.');
        }

        $this->name = $name;
        $this->version = $version;
        $this->build = $build;
        $this->kernelVersion = $kernelVersion;
        $this->machineType = $machineType;
    }

    /**
     * Gets the name of the operating system.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the name of the operating system.
     *
     * @param string $name The name
     */
    public function setName(string $name): void
    {
        if (trim($name) === '') {
            throw new \InvalidArgumentException('The $name argument cannot be an empty string.');
        }

        $this->name = $name;
    }

    /**
     * Gets the version of the operating system.
     */
    public function getVersion(): ?string
    {
        return $this->version;
    }

    /**
     * Sets the version of the operating system.
     *
     * @param string|null $version The version
     */
    public function setVersion(?string $version): void
    {
        $this->version = $version;
    }

    /**
     * Gets the build of the operating system.
     */
    public function getBuild(): ?string
    {
        return $this->build;
    }

    /**
     * Sets the build of the operating system.
     *
     * @param string|null $build The build
     */
    public function setBuild(?string $build): void
    {
        $this->build = $build;
    }

    /**
     * Gets the version of the kernel of the operating system.
     */
    public function getKernelVersion(): ?string
    {
        return $this->kernelVersion;
    }

    /**
     * Sets the version of the kernel of the operating system.
     *
     * @param string|null $kernelVersion The kernel version
     */
    public function setKernelVersion(?string $kernelVersion): void
    {
        $this->kernelVersion = $kernelVersion;
    }

    public function getMachineType(): ?string
    {
        return $this->machineType;
    }

    /**
     * @param string|null $machineType The machine type
     */
    public function setMachineType(?string $machineType): void
    {
        $this->machineType = $machineType;
    }
}
