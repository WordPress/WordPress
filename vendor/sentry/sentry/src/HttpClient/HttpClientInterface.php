<?php

declare(strict_types=1);

namespace Sentry\HttpClient;

use Sentry\Options;

interface HttpClientInterface
{
    public function sendRequest(Request $request, Options $options): Response;
}
