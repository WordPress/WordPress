<?php

declare (strict_types=1);
namespace Matomo\Dependencies\MaxMind\WebService\Http;

use Matomo\Dependencies\MaxMind\Exception\HttpException;
/**
 * This class is for internal use only. Semantic versioning does not not apply.
 *
 * @internal
 */
class CurlRequest implements Request
{
    /**
     * @var \CurlHandle
     */
    private $ch;
    /**
     * @var string
     */
    private $url;
    /**
     * @var array
     */
    private $options;
    public function __construct(string $url, array $options)
    {
        $this->url = $url;
        $this->options = $options;
        $this->ch = $options['curlHandle'];
    }
    /**
     * @throws HttpException
     */
    public function post(string $body) : array
    {
        $curl = $this->createCurl();
        curl_setopt($curl, \CURLOPT_POST, \true);
        curl_setopt($curl, \CURLOPT_POSTFIELDS, $body);
        return $this->execute($curl);
    }
    public function get() : array
    {
        $curl = $this->createCurl();
        curl_setopt($curl, \CURLOPT_HTTPGET, \true);
        return $this->execute($curl);
    }
    /**
     * @return \CurlHandle
     */
    private function createCurl()
    {
        curl_reset($this->ch);
        $opts = [];
        $opts[\CURLOPT_URL] = $this->url;
        if (!empty($this->options['caBundle'])) {
            $opts[\CURLOPT_CAINFO] = $this->options['caBundle'];
        }
        $opts[\CURLOPT_ENCODING] = '';
        $opts[\CURLOPT_SSL_VERIFYHOST] = 2;
        $opts[\CURLOPT_FOLLOWLOCATION] = \false;
        $opts[\CURLOPT_SSL_VERIFYPEER] = \true;
        $opts[\CURLOPT_RETURNTRANSFER] = \true;
        $opts[\CURLOPT_HTTPHEADER] = $this->options['headers'];
        $opts[\CURLOPT_USERAGENT] = $this->options['userAgent'];
        $opts[\CURLOPT_PROXY] = $this->options['proxy'];
        // The defined()s are here as the *_MS opts are not available on older
        // cURL versions
        $connectTimeout = $this->options['connectTimeout'];
        if (\defined('CURLOPT_CONNECTTIMEOUT_MS')) {
            $opts[\CURLOPT_CONNECTTIMEOUT_MS] = ceil($connectTimeout * 1000);
        } else {
            $opts[\CURLOPT_CONNECTTIMEOUT] = ceil($connectTimeout);
        }
        $timeout = $this->options['timeout'];
        if (\defined('CURLOPT_TIMEOUT_MS')) {
            $opts[\CURLOPT_TIMEOUT_MS] = ceil($timeout * 1000);
        } else {
            $opts[\CURLOPT_TIMEOUT] = ceil($timeout);
        }
        curl_setopt_array($this->ch, $opts);
        return $this->ch;
    }
    /**
     * @param \CurlHandle $curl
     *
     * @throws HttpException
     */
    private function execute($curl) : array
    {
        $body = curl_exec($curl);
        if ($errno = curl_errno($curl)) {
            $errorMessage = curl_error($curl);
            throw new HttpException("cURL error ({$errno}): {$errorMessage}", 0, $this->url);
        }
        $statusCode = curl_getinfo($curl, \CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($curl, \CURLINFO_CONTENT_TYPE);
        return [
            $statusCode,
            // The PHP docs say "Content-Type: of the requested document. NULL
            // indicates server did not send valid Content-Type: header" for
            // CURLINFO_CONTENT_TYPE. However, it will return FALSE if no header
            // is set. To keep our types simple, we return null in this case.
            $contentType === \false ? null : $contentType,
            $body,
        ];
    }
}
