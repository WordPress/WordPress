<?php

declare (strict_types=1);
namespace Matomo\Dependencies\MaxMind\WebService\Http;

/**
 * Class RequestFactory.
 *
 * @internal
 */
class RequestFactory
{
    /**
     * Keep the cURL resource here, so that if there are multiple API requests
     * done the connection is kept alive, SSL resumption can be used
     * etcetera.
     *
     * @var \CurlHandle|null
     */
    private $ch;
    public function __destruct()
    {
        if (!empty($this->ch)) {
            curl_close($this->ch);
        }
    }
    /**
     * @return \CurlHandle
     */
    private function getCurlHandle()
    {
        if (empty($this->ch)) {
            $this->ch = curl_init();
        }
        return $this->ch;
    }
    public function request(string $url, array $options) : Request
    {
        $options['curlHandle'] = $this->getCurlHandle();
        return new CurlRequest($url, $options);
    }
}
