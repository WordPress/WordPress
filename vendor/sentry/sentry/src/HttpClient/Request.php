<?php

declare(strict_types=1);

namespace Sentry\HttpClient;

final class Request
{
    /**
     * @var string
     */
    private $stringBody;

    public function hasStringBody(): bool
    {
        return $this->stringBody !== null;
    }

    public function getStringBody(): ?string
    {
        return $this->stringBody;
    }

    public function setStringBody(string $stringBody): void
    {
        $this->stringBody = $stringBody;
    }
}
