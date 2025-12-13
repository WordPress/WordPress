<?php

declare(strict_types=1);

namespace Jean85;

class Version
{
    private const SHORT_COMMIT_LENGTH = 7;

    private string $packageName;

    private string $prettyVersion;

    private string $reference;

    private bool $versionIsTagged;

    public const NO_VERSION_TEXT = '{no version}';
    public const NO_REFERENCE_TEXT = '{no reference}';

    public function __construct(string $packageName, ?string $prettyVersion = null, ?string $reference = null)
    {
        $this->packageName = $packageName;
        $this->prettyVersion = $prettyVersion ?? self::NO_VERSION_TEXT;
        $this->reference = $reference ?? self::NO_REFERENCE_TEXT;
        $this->versionIsTagged = preg_match('/^v?(\d+\.)+\d+(-(beta|RC|alpha).?\d+)?/i', $this->getShortVersion()) === 1;
    }

    public function getPrettyVersion(): string
    {
        if ($this->versionIsTagged) {
            return $this->prettyVersion;
        }

        return $this->getVersionWithShortReference();
    }

    public function getFullVersion(): string
    {
        return $this->prettyVersion . '@' . $this->getReference();
    }

    /**
     * @deprecated
     */
    public function getVersionWithShortCommit(): string
    {
        return $this->getVersionWithShortReference();
    }

    public function getVersionWithShortReference(): string
    {
        return $this->prettyVersion . '@' . $this->getShortReference();
    }

    public function getPackageName(): string
    {
        return $this->packageName;
    }

    public function getShortVersion(): string
    {
        return $this->prettyVersion;
    }

    /**
     * @deprecated
     */
    public function getCommitHash(): string
    {
        return $this->getReference();
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @deprecated
     */
    public function getShortCommitHash(): string
    {
        return $this->getShortReference();
    }

    public function getShortReference(): string
    {
        if ($this->reference === self::NO_REFERENCE_TEXT) {
            return self::NO_REFERENCE_TEXT;
        }

        return substr($this->reference, 0, self::SHORT_COMMIT_LENGTH);
    }

    public function __toString(): string
    {
        return $this->getPrettyVersion();
    }
}
