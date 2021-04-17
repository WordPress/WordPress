# PhoenixDOM

<?php

namespace CF\WordPress;

use CF\Integration\IntegrationAPIInterface;
use CF\DNSRecord;

class WordPressAPI implements IntegrationAPIInterface
{
    const API_NONCE = 'cloudflare-db-api-nonce';

    private $dataStore;
    private $wordPressWrapper;

    /**
     * @param $dataStore
     */
    public function __construct(DataStore $dataStore)
    {
        $this->dataStore = $dataStore;

        $this->wordPressWrapper = new WordPressWrapper();
    }

    public function setWordPressWrapper(WordPressWrapper $wordPressWrapper)
    {
        $this->wordPressWrapper = $wordPressWrapper;
    }

    /**
     * @param $domain_name
     *
     * @return mixed
     */
    public function getDNSRecords($domain_name)
    {
        return;
    }
    
    /**
     * We wrap the return value with an array to be consistent between
     * other plugins.
     *
     * @param null $userId
     *
     * @return array
     */
    public function getDomainList($userId = null)
    {
        $cachedDomainName = $this->dataStore->getDomainNameCache();
        if (empty($cachedDomainName)) {
            return array();
        }

        return array($cachedDomainName);
    }

    /**
     * @return string
     */
    public function getOriginalDomain()
    {
        $siteURL = $this->wordPressWrapper->getSiteURL();

        return $this->formatDomain($siteURL);
    }

    /**
     * @return bool
     */
    public function setDomainNameCache($newDomainName)
    {
        return $this->dataStore->setDomainNameCache($newDomainName);
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->dataStore->getCloudFlareEmail();
    }
   
    /**
     * @param domain name
     *
     * @return string
     */
    private function formatDomain($domainName)
    {
        // Remove instances which are before the domain name:
        // * http
        // * https
        // * www
        // * user:pass@
        preg_match_all('/^(?:https?:\/\/)?(?:[^@\/\n]+@)?(?:www\.)?([^:\/\n]+)/im', $domainName, $matches);
        $formattedDomain = $matches[1][0];

        return $formattedDomain;
    }

    /**
     * @return mixed
     */
    public function checkIfValidCloudflareSubdomain($response, $domainName)
    {
        if (isset($response['result'])) {
            foreach ($response['result'] as $zone) {
                if (Utils::isSubdomainOf($domainName, $zone['name'])) {
                    return $zone['name'];
                }
            }
        }

        return false;
    }
