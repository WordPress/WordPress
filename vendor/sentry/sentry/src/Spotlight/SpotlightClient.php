<?php

declare(strict_types=1);

namespace Sentry\Spotlight;

use Sentry\HttpClient\Request;
use Sentry\HttpClient\Response;

/**
 * @internal
 */
class SpotlightClient
{
    public static function sendRequest(Request $request, string $url): Response
    {
        if (!\extension_loaded('curl')) {
            throw new \RuntimeException('The cURL PHP extension must be enabled to use the SpotlightClient.');
        }

        $requestData = $request->getStringBody();
        if ($requestData === null) {
            throw new \RuntimeException('The request data is empty.');
        }

        $curlHandle = curl_init();

        curl_setopt($curlHandle, \CURLOPT_URL, $url);
        curl_setopt($curlHandle, \CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-sentry-envelope',
        ]);
        curl_setopt($curlHandle, \CURLOPT_TIMEOUT, 2.0);
        curl_setopt($curlHandle, \CURLOPT_CONNECTTIMEOUT, 1.0);
        curl_setopt($curlHandle, \CURLOPT_ENCODING, '');
        curl_setopt($curlHandle, \CURLOPT_POST, true);
        curl_setopt($curlHandle, \CURLOPT_POSTFIELDS, $requestData);
        curl_setopt($curlHandle, \CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, \CURLOPT_HTTP_VERSION, \CURL_HTTP_VERSION_1_1);

        $body = curl_exec($curlHandle);

        if ($body === false) {
            $errorCode = curl_errno($curlHandle);
            $error = curl_error($curlHandle);
            if (\PHP_MAJOR_VERSION < 8) {
                curl_close($curlHandle);
            }

            $message = 'cURL Error (' . $errorCode . ') ' . $error;

            return new Response(0, [], $message);
        }

        $statusCode = curl_getinfo($curlHandle, \CURLINFO_HTTP_CODE);

        if (\PHP_MAJOR_VERSION < 8) {
            curl_close($curlHandle);
        }

        return new Response($statusCode, [], '');
    }
}
