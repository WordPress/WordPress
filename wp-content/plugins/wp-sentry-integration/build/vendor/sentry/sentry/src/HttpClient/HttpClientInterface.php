<?php

declare (strict_types=1);
namespace Sentry\HttpClient;

use Sentry\Options;
interface HttpClientInterface
{
    public function sendRequest(\Sentry\HttpClient\Request $request, \Sentry\Options $options) : \Sentry\HttpClient\Response;
}
