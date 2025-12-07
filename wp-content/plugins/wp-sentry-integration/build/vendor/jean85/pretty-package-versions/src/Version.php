<?php

namespace WPSentry\ScopedVendor\Jean85;

class Version
{
    const SHORT_COMMIT_LENGTH = \WPSentry\ScopedVendor\Jean85\PrettyVersions::SHORT_COMMIT_LENGTH;
    /** @var string */
    private $packageName;
    /** @var string */
    private $shortVersion;
    /** @var string */
    private $commitHash;
    /** @var bool */
    private $versionIsTagged;
    public function __construct(string $packageName, string $version)
    {
        $this->packageName = $packageName;
        $splittedVersion = \explode('@', $version);
        $this->shortVersion = $splittedVersion[0];
        $this->commitHash = $splittedVersion[1];
        $this->versionIsTagged = \preg_match('/[^v\\d\\.]/', $this->getShortVersion()) === 0;
    }
    public function getPrettyVersion() : string
    {
        if ($this->versionIsTagged) {
            return $this->getShortVersion();
        }
        return $this->getVersionWithShortCommit();
    }
    public function getFullVersion() : string
    {
        return $this->getShortVersion() . '@' . $this->getCommitHash();
    }
    public function getVersionWithShortReference() : string
    {
        return $this->getShortVersion() . '@' . $this->getShortCommitHash();
    }
    /**
     * @deprecated since 1.6, use getVersionWithShortReference instead
     */
    public function getVersionWithShortCommit() : string
    {
        return $this->getVersionWithShortReference();
    }
    public function getPackageName() : string
    {
        return $this->packageName;
    }
    public function getShortVersion() : string
    {
        return $this->shortVersion;
    }
    public function getReference() : string
    {
        return $this->commitHash;
    }
    /**
     * @deprecated since 1.6, use getReference instead
     */
    public function getCommitHash() : string
    {
        return $this->getReference();
    }
    public function getShortReference() : string
    {
        return \substr($this->commitHash, 0, self::SHORT_COMMIT_LENGTH);
    }
    /**
     * @deprecated since 1.6, use getShortReference instead
     */
    public function getShortCommitHash() : string
    {
        return $this->getShortReference();
    }
    public function __toString() : string
    {
        return $this->getPrettyVersion();
    }
}
