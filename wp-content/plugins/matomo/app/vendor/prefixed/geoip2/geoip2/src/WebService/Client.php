<?php

declare (strict_types=1);
namespace Matomo\Dependencies\GeoIp2\WebService;

use Matomo\Dependencies\GeoIp2\Exception\AddressNotFoundException;
use Matomo\Dependencies\GeoIp2\Exception\AuthenticationException;
use Matomo\Dependencies\GeoIp2\Exception\GeoIp2Exception;
use Matomo\Dependencies\GeoIp2\Exception\HttpException;
use Matomo\Dependencies\GeoIp2\Exception\InvalidRequestException;
use Matomo\Dependencies\GeoIp2\Exception\OutOfQueriesException;
use Matomo\Dependencies\GeoIp2\Model\City;
use Matomo\Dependencies\GeoIp2\Model\Country;
use Matomo\Dependencies\GeoIp2\Model\Insights;
use Matomo\Dependencies\GeoIp2\ProviderInterface;
use Matomo\Dependencies\MaxMind\WebService\Client as WsClient;
/**
 * This class provides a client API for all the GeoIP2 web services.
 * The services are Country, City Plus, and Insights. Each service returns
 * a different set of data about an IP address, with Country returning the
 * least data and Insights the most.
 *
 * Each web service is represented by a different model class, and these model
 * classes in turn contain multiple record classes. The record classes have
 * attributes which contain data about the IP address.
 *
 * If the web service does not return a particular piece of data for an IP
 * address, the associated attribute is not populated.
 *
 * The web service may not return any information for an entire record, in
 * which case all of the attributes for that record class will be empty.
 *
 * ## Usage ##
 *
 * The basic API for this class is the same for all of the web service end
 * points. First you create a web service object with your MaxMind `$accountId`
 * and `$licenseKey`, then you call the method corresponding to a specific end
 * point, passing it the IP address you want to look up.
 *
 * If the request succeeds, the method call will return a model class for
 * the service you called. This model in turn contains multiple record
 * classes, each of which represents part of the data returned by the web
 * service.
 *
 * If the request fails, the client class throws an exception.
 */
class Client implements ProviderInterface
{
    /**
     * @var array<string>
     */
    private $locales;
    /**
     * @var WsClient
     */
    private $client;
    /**
     * @var string
     */
    private static $basePath = '/geoip/v2.1';
    public const VERSION = 'v2.13.0';
    /**
     * Constructor.
     *
     * @param int    $accountId  your MaxMind account ID
     * @param string $licenseKey your MaxMind license key
     * @param array  $locales    list of locale codes to use in name property
     *                           from most preferred to least preferred
     * @param array  $options    array of options. Valid options include:
     *                           * `host` - The host to use when querying the web
     *                           service. To query the GeoLite2 web service
     *                           instead of the GeoIP2 web service, set the
     *                           host to `geolite.info`.
     *                           * `timeout` - Timeout in seconds.
     *                           * `connectTimeout` - Initial connection timeout in seconds.
     *                           * `proxy` - The HTTP proxy to use. May include a schema, port,
     *                           username, and password, e.g.,
     *                           `http://username:password@127.0.0.1:10`.
     */
    public function __construct(int $accountId, string $licenseKey, array $locales = ['en'], array $options = [])
    {
        $this->locales = $locales;
        // This is for backwards compatibility. Do not remove except for a
        // major version bump.
        // @phpstan-ignore-next-line
        if (\is_string($options)) {
            $options = ['host' => $options];
        }
        if (!isset($options['host'])) {
            $options['host'] = 'geoip.maxmind.com';
        }
        $options['userAgent'] = $this->userAgent();
        $this->client = new WsClient($accountId, $licenseKey, $options);
    }
    private function userAgent() : string
    {
        return 'GeoIP2-API/' . self::VERSION;
    }
    /**
     * This method calls the City Plus service.
     *
     * @param string $ipAddress IPv4 or IPv6 address as a string. If no
     *                          address is provided, the address that the web service is called
     *                          from will be used.
     *
     * @throws \GeoIp2\Exception\AddressNotFoundException if the address you
     *                                                    provided is not in our database (e.g., a private address).
     * @throws \GeoIp2\Exception\AuthenticationException  if there is a problem
     *                                                    with the account ID or license key that you provided
     * @throws \GeoIp2\Exception\OutOfQueriesException    if your account is out
     *                                                    of queries
     * @throws \GeoIp2\Exception\InvalidRequestException} if your request was received by the web service but is
     *                                                    invalid for some other reason.  This may indicate an issue
     *                                                    with this API. Please report the error to MaxMind.
     * @throws \GeoIp2\Exception\HttpException   if an unexpected HTTP error code or message was returned.
     *                                           This could indicate a problem with the connection between
     *                                           your server and the web service or that the web service
     *                                           returned an invalid document or 500 error code
     * @throws \GeoIp2\Exception\GeoIp2Exception This serves as the parent
     *                                           class to the above exceptions. It will be thrown directly
     *                                           if a 200 status code is returned but the body is invalid.
     */
    public function city(string $ipAddress = 'me') : City
    {
        // @phpstan-ignore-next-line
        return $this->responseFor('city', City::class, $ipAddress);
    }
    /**
     * This method calls the Country service.
     *
     * @param string $ipAddress IPv4 or IPv6 address as a string. If no
     *                          address is provided, the address that the web service is called
     *                          from will be used.
     *
     * @throws \GeoIp2\Exception\AddressNotFoundException if the address you provided is not in our database (e.g.,
     *                                                    a private address).
     * @throws \GeoIp2\Exception\AuthenticationException  if there is a problem
     *                                                    with the account ID or license key that you provided
     * @throws \GeoIp2\Exception\OutOfQueriesException    if your account is out of queries
     * @throws \GeoIp2\Exception\InvalidRequestException} if your request was received by the web service but is
     *                                                    invalid for some other reason.  This may indicate an
     *                                                    issue with this API. Please report the error to MaxMind.
     * @throws \GeoIp2\Exception\HttpException   if an unexpected HTTP error
     *                                           code or message was returned. This could indicate a problem
     *                                           with the connection between your server and the web service
     *                                           or that the web service returned an invalid document or 500
     *                                           error code.
     * @throws \GeoIp2\Exception\GeoIp2Exception This serves as the parent class to the above exceptions. It
     *                                           will be thrown directly if a 200 status code is returned but
     *                                           the body is invalid.
     */
    public function country(string $ipAddress = 'me') : Country
    {
        return $this->responseFor('country', Country::class, $ipAddress);
    }
    /**
     * This method calls the Insights service. Insights is only supported by
     * the GeoIP2 web service. The GeoLite2 web service does not support it.
     *
     * @param string $ipAddress IPv4 or IPv6 address as a string. If no
     *                          address is provided, the address that the web service is called
     *                          from will be used.
     *
     * @throws \GeoIp2\Exception\AddressNotFoundException if the address you
     *                                                    provided is not in our database (e.g., a private address).
     * @throws \GeoIp2\Exception\AuthenticationException  if there is a problem
     *                                                    with the account ID or license key that you provided
     * @throws \GeoIp2\Exception\OutOfQueriesException    if your account is out
     *                                                    of queries
     * @throws \GeoIp2\Exception\InvalidRequestException} if your request was received by the web service but is
     *                                                    invalid for some other reason.  This may indicate an
     *                                                    issue with this API. Please report the error to MaxMind.
     * @throws \GeoIp2\Exception\HttpException   if an unexpected HTTP error code or message was returned.
     *                                           This could indicate a problem with the connection between
     *                                           your server and the web service or that the web service
     *                                           returned an invalid document or 500 error code
     * @throws \GeoIp2\Exception\GeoIp2Exception This serves as the parent
     *                                           class to the above exceptions. It will be thrown directly
     *                                           if a 200 status code is returned but the body is invalid.
     */
    public function insights(string $ipAddress = 'me') : Insights
    {
        // @phpstan-ignore-next-line
        return $this->responseFor('insights', Insights::class, $ipAddress);
    }
    private function responseFor(string $endpoint, string $class, string $ipAddress) : Country
    {
        $path = implode('/', [self::$basePath, $endpoint, $ipAddress]);
        try {
            $service = (new \ReflectionClass($class))->getShortName();
            $body = $this->client->get('GeoIP2 ' . $service, $path);
        } catch (\Matomo\Dependencies\MaxMind\Exception\IpAddressNotFoundException $ex) {
            throw new AddressNotFoundException($ex->getMessage(), $ex->getStatusCode(), $ex);
        } catch (\Matomo\Dependencies\MaxMind\Exception\AuthenticationException $ex) {
            throw new AuthenticationException($ex->getMessage(), $ex->getStatusCode(), $ex);
        } catch (\Matomo\Dependencies\MaxMind\Exception\InsufficientFundsException $ex) {
            throw new OutOfQueriesException($ex->getMessage(), $ex->getStatusCode(), $ex);
        } catch (\Matomo\Dependencies\MaxMind\Exception\InvalidRequestException $ex) {
            throw new InvalidRequestException($ex->getMessage(), $ex->getErrorCode(), $ex->getStatusCode(), $ex->getUri(), $ex);
        } catch (\Matomo\Dependencies\MaxMind\Exception\HttpException $ex) {
            throw new HttpException($ex->getMessage(), $ex->getStatusCode(), $ex->getUri(), $ex);
        } catch (\Matomo\Dependencies\MaxMind\Exception\WebServiceException $ex) {
            throw new GeoIp2Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
        return new $class($body, $this->locales);
    }
}
