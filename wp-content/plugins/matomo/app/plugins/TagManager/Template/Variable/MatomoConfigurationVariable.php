<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Template\Variable;

use Piwik\Common;
use Piwik\Piwik;
use Piwik\Settings\FieldConfig;
use Piwik\SettingsPiwik;
use Piwik\Site;
use Piwik\Tracker\TrackerCodeGenerator;
use Piwik\Url;
use Piwik\Validators\CharacterLength;
use Piwik\Validators\NotEmpty;
use Piwik\Validators\NumberRange;
use Piwik\Plugins\TagManager\Validators\CustomRequestProcessing;
class MatomoConfigurationVariable extends \Piwik\Plugins\TagManager\Template\Variable\BaseVariable
{
    public const ID = 'MatomoConfiguration';
    public function getId()
    {
        return self::ID;
    }
    public function getCategory()
    {
        return self::CATEGORY_ANALYTICS;
    }
    public function getIcon()
    {
        return 'plugins/TagManager/images/MatomoIcon.png';
    }
    public function hasAdvancedSettings()
    {
        return \true;
    }
    public function getParameters()
    {
        $idSite = Common::getRequestVar('idSite', 0, 'int');
        $idContainer = Common::getRequestVar('idContainer', '', 'string');
        $url = SettingsPiwik::getPiwikUrl();
        if (SettingsPiwik::isHttpsForced()) {
            $url = str_replace('http://', 'https://', $url);
        } else {
            $url = str_replace(array('http://', 'https://'), '//', $url);
        }
        $matomoUrl = $this->makeSetting('matomoUrl', $url, FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoUrlTitle');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoUrlDescription');
            $field->validators[] = new NotEmpty();
            $field->transform = function ($value) {
                return trim($value);
            };
        });
        $trackerCodeGenerator = new TrackerCodeGenerator();
        $jsEndpoint = $trackerCodeGenerator->getJsTrackerEndpoint();
        $phpEndpoint = $trackerCodeGenerator->getPhpTrackerEndpoint();
        $parameters = array($matomoUrl, $this->makeSetting('idSite', $idSite, FieldConfig::TYPE_STRING, function (FieldConfig $field) use($matomoUrl, $url) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoIDSiteTitle');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoIDSiteDescription');
            $field->validators[] = new NotEmpty();
            $field->validators[] = new CharacterLength(0, 500);
            $field->validate = function ($value) use($matomoUrl, $url) {
                $value = trim($value);
                if (is_numeric($value)) {
                    if ($matomoUrl->getValue() === $url) {
                        new Site($value);
                        // we validate idSite when it points to this url
                    }
                    return;
                    // valid... we do not validate idSite as it might point to different matomo...
                }
                $posBracket = strpos($value, '{{');
                if ($posBracket === \false || strpos($value, '}}', $posBracket) === \false) {
                    throw new \Exception(Piwik::translate('TagManager_MatomoConfigurationMatomoIDSiteException'));
                }
            };
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('enableLinkTracking', \true, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoEnableLinkTrackingTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoEnableLinkTrackingDescription');
        }), $this->makeSetting('enableFileTracking', \false, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoEnableFileTrackingTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoEnableFileTrackingDescription');
        }), $this->makeSetting('enableCrossDomainLinking', \false, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoEnableCrossDomainLinkingTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoEnableCrossDomainLinkingDescription');
        }), $this->makeSetting('crossDomainLinkingTimeout', '180', FieldConfig::TYPE_INT, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoCrossDomainLinkingTimeoutTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoCrossDomainLinkingTimeoutDescription');
            $field->uiControlAttributes = ['placeholder' => Piwik::translate('TagManager_MatomoConfigurationMatomoCrossDomainLinkingTimeoutPlaceholder')];
            $field->condition = 'enableCrossDomainLinking';
            $field->validators[] = new NumberRange($min = 1);
        }), $this->makeSetting('enableDoNotTrack', \false, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoEnableDoNotTrackTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoEnableDoNotTrackDescription');
            $field->inlineHelp = Piwik::translate('TagManager_MatomoConfigurationMatomoEnableDoNotTrackInlineHelp', array('<strong>', '</strong>'));
        }), $this->makeSetting('disablePerformanceTracking', \false, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoDisablePerformanceTrackingTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoDisablePerformanceTrackingDescription');
        }), $this->makeSetting('enableJSErrorTracking', \false, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoEnableJSErrorTrackingTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoEnableJSErrorTrackingDescription');
        }), $this->makeSetting('enableHeartBeatTimer', \false, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoEnableHeartBeatTimerTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoEnableHeartBeatTimerDescription');
        }), $this->makeSetting('heartBeatTime', '15', FieldConfig::TYPE_INT, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoHeartBeatTimeTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoHeartBeatTimeDescription');
            $field->uiControlAttributes = ['placeholder' => Piwik::translate('TagManager_MatomoConfigurationMatomoHeartBeatTimePlaceholder')];
            $field->condition = 'enableHeartBeatTimer';
            $field->validators[] = new NumberRange($min = 5);
        }), $this->makeSetting('trackAllContentImpressions', \false, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoTrackAllContentImpressionsTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoTrackAllContentImpressionsDescription');
        }), $this->makeSetting('trackVisibleContentImpressions', \false, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoTrackVisibleContentImpressionsTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoTrackVisibleContentImpressionsDescription');
        }), $this->makeSetting('trackBots', \false, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoTrackBotsTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoTrackBotsDescription');
        }), $this->makeSetting('disableCookies', \false, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoDisableCookiesTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoDisableCookiesDescription');
            $field->condition = '!requireConsent && !requireCookieConsent';
        }), $this->makeSetting('requireConsent', \false, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoRequireConsentTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoRequireConsentDescription');
            $field->condition = '!requireCookieConsent && !disableCookies';
        }), $this->makeSetting('requireCookieConsent', \false, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoRequireCookieConsentTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoRequireCookieConsentDescription');
            $field->condition = '!requireConsent && !disableCookies';
        }), $this->makeSetting('customCookieTimeOutEnable', \false, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoCustomCookieTimeOutsEnableTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoCustomCookieTimeOutsEnableDescription');
            $field->condition = '!disableCookies';
        }), $this->makeSetting('customCookieTimeOut', '393', FieldConfig::TYPE_INT, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoVisitorCookieTimeOutTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoVisitorCookieTimeOutDescription');
            $field->uiControlAttributes = ['placeholder' => Piwik::translate('TagManager_MatomoConfigurationMatomoVisitorCookieTimeOutPlaceholder')];
            $field->condition = 'customCookieTimeOutEnable && !disableCookies';
            $field->validators[] = new NumberRange($min = 1);
        }), $this->makeSetting('referralCookieTimeOut', '182', FieldConfig::TYPE_INT, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoReferralCookieTimeOutTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoReferralCookieTimeOutDescription');
            $field->uiControlAttributes = ['placeholder' => Piwik::translate('TagManager_MatomoConfigurationMatomoReferralCookieTimeOutPlaceholder')];
            $field->condition = 'customCookieTimeOutEnable && !disableCookies';
            $field->validators[] = new NumberRange($min = 1);
        }), $this->makeSetting('sessionCookieTimeOut', '30', FieldConfig::TYPE_INT, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoSessionCookieTimeOutTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoSessionCookieTimeOutDescription');
            $field->uiControlAttributes = ['placeholder' => Piwik::translate('TagManager_MatomoConfigurationMatomoSessionCookieTimeOutPlaceholder')];
            $field->condition = 'customCookieTimeOutEnable && !disableCookies';
            $field->validators[] = new NumberRange($min = 1);
        }), $this->makeSetting('setSecureCookie', \false, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoSetSecureCookieTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoSetSecureCookieDescription');
        }), $this->makeSetting('cookieDomain', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoCookieDomainTitle');
            $field->inlineHelp = Piwik::translate('TagManager_MatomoConfigurationMatomoCookieDomainInlineHelp', array('<br><strong>', '</strong>'));
            $field->validators[] = new CharacterLength(0, 500);
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('cookieNamePrefix', '_pk_', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoCookieNamePrefixTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoCookieNamePrefixDescription');
            $field->uiControlAttributes = ['placeholder' => Piwik::translate('TagManager_MatomoConfigurationMatomoCookieNamePrefixPlaceholder')];
            $field->validators[] = new CharacterLength(1, 20);
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('cookiePath', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoCookiePathTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoCookiePathDescription');
            $field->uiControlAttributes = ['placeholder' => Piwik::translate('TagManager_MatomoConfigurationMatomoCookiePathPlaceholder')];
            $field->validators[] = new CharacterLength(0, 500);
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('cookieSameSite', 'Lax', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoCookieSameSiteTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoCookieSameSiteDescription');
            $field->uiControl = FieldConfig::UI_CONTROL_SINGLE_SELECT;
            $field->availableValues = array('Lax' => 'Lax', 'None' => 'None', 'Strict' => 'Strict');
        }), $this->makeSetting('disableBrowserFeatureDetection', \false, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoDisableBrowserFeatureDetectionTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoDisableBrowserFeatureDetectionDescription');
            $field->inlineHelp = Piwik::translate('TagManager_MatomoConfigurationMatomoDisableBrowserFeatureDetectionInLineHelp', ['<br><strong>', '<a href="' . Url::addCampaignParametersToMatomoLink('https://matomo.org/faq/how-to/how-do-i-disable-browser-feature-detection-completely/', null, null, 'App.TagManager.getParameters') . '" target="_blank" rel="noreferrer noopener">', '</a>', '</strong>']);
        }), $this->makeSetting('disableCampaignParameters', \false, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoDisableCampaignParametersTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoDisableCampaignParametersDescription');
        }), $this->makeSetting('domains', array(), FieldConfig::TYPE_ARRAY, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoDomainsTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoDomainsDescription');
            $field->validate = function ($value) {
                if (empty($value)) {
                    return;
                }
                if (!is_array($value)) {
                    throw new \Exception(Piwik::translate('TagManager_MatomoConfigurationMatomoDomainsException'));
                }
            };
            $field->transform = function ($value) {
                if (empty($value) || !is_array($value)) {
                    return array();
                }
                $withValues = array();
                foreach ($value as $domain) {
                    if (!empty($domain['domain'])) {
                        $withValues[] = $domain;
                    }
                }
                return $withValues;
            };
            $field->uiControl = FieldConfig::UI_CONTROL_MULTI_TUPLE;
            $field1 = new FieldConfig\MultiPair('Domain', 'domain', FieldConfig::UI_CONTROL_TEXT);
            $field1->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->uiControlAttributes['field1'] = $field1->toArray();
        }), $this->makeSetting('alwaysUseSendBeacon', \false, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoAlwaysUseSendBeaconTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoAlwaysUseSendBeaconDescription');
            $field->condition = '!disableAlwaysUseSendBeacon';
        }), $this->makeSetting('disableAlwaysUseSendBeacon', \false, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoDisableAlwaysUseSendBeaconTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoDisableAlwaysUseSendBeaconDescription');
            $field->condition = '!alwaysUseSendBeacon';
        }), $this->makeSetting('userId', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoUserIdTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoUserIdDescription');
            $field->validators[] = new CharacterLength(0, 500);
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
        }), $this->makeSetting('customDimensions', array(), FieldConfig::TYPE_ARRAY, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoCustomDimensionsTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoCustomDimensionsDescription');
            $field->validate = function ($value) {
                if (empty($value)) {
                    return;
                }
                if (!is_array($value)) {
                    throw new \Exception(Piwik::translate('TagManager_MatomoConfigurationMatomoCustomDimensionsException'));
                }
            };
            $field->transform = function ($value) {
                if (empty($value) || !is_array($value)) {
                    return array();
                }
                $withValues = array();
                foreach ($value as $dim) {
                    if (!empty($dim['index']) && !empty($dim['value'])) {
                        $withValues[] = $dim;
                    }
                }
                return $withValues;
            };
            $field->uiControl = FieldConfig::UI_CONTROL_MULTI_TUPLE;
            $field1 = new FieldConfig\MultiPair('Index', 'index', FieldConfig::UI_CONTROL_TEXT);
            $field1->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field2 = new FieldConfig\MultiPair('Value', 'value', FieldConfig::UI_CONTROL_TEXT);
            $field2->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->uiControlAttributes['field1'] = $field1->toArray();
            $field->uiControlAttributes['field2'] = $field2->toArray();
        }), $this->makeSetting('registerAsDefaultTracker', \true, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoRegisterAsDefaultTrackerTitle');
            $field->uiControl = FieldConfig::UI_CONTROL_CHECKBOX;
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoRegisterAsDefaultTrackerDescription');
        }), $this->makeSetting('bundleTracker', \true, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoBundleTrackerTitle');
            $field->uiControl = FieldConfig::UI_CONTROL_CHECKBOX;
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoBundleTrackerDescription');
        }), $this->makeSetting('jsEndpoint', $jsEndpoint, FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoJsEndpointTitle');
            $field->uiControl = FieldConfig::UI_CONTROL_SINGLE_SELECT;
            $field->availableValues = array('matomo.js' => 'matomo.js', 'piwik.js' => 'piwik.js', 'js/' => 'js/', 'js/tracker.php' => 'js/tracker.php', 'custom' => Piwik::translate('TagManager_MatomoConfigurationMatomoJsEndpointCustom'));
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoJsEndpointDescription');
            $field->condition = '!bundleTracker';
        }), $matomoUrl = $this->makeSetting('jsEndpointCustom', 'custom.js', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoJsEndpointCustomTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoJsEndpointCustomDescription');
            $field->uiControlAttributes = ['placeholder' => Piwik::translate('TagManager_MatomoConfigurationMatomoJsEndpointCustomPlaceholder')];
            $field->condition = '!bundleTracker && jsEndpoint == "custom"';
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('trackingEndpoint', $phpEndpoint, FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoTrackingEndpointTitle');
            $field->uiControl = FieldConfig::UI_CONTROL_SINGLE_SELECT;
            $field->availableValues = array('matomo.php' => 'matomo.php', 'piwik.php' => 'piwik.php', 'js/' => 'js/', 'js/tracker.php' => 'js/tracker.php', 'custom' => Piwik::translate('TagManager_MatomoConfigurationMatomoTrackingEndpointCustom'));
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoTrackingEndpointDescription');
        }), $matomoUrl = $this->makeSetting('trackingEndpointCustom', 'custom.php', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoTrackingEndpointCustomTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoTrackingEndpointCustomDescription');
            $field->uiControlAttributes = ['placeholder' => Piwik::translate('TagManager_MatomoConfigurationMatomoTrackingEndpointCustomPlaceholder')];
            $field->condition = 'trackingEndpoint == "custom"';
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('appendToTrackingUrl', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoAppendToTrackingUrlTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoAppendToTrackingUrlDescription');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
        }), $this->makeSetting('forceRequestMethod', \false, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoForceRequestMethodTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoForceRequestMethodDescription');
        }), $this->makeSetting('requestMethod', 'GET', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoRequestMethodTitle');
            $field->uiControl = FieldConfig::UI_CONTROL_SINGLE_SELECT;
            $field->availableValues = array('GET' => 'GET', 'POST' => 'POST');
            $field->condition = 'forceRequestMethod';
            $field->inlineHelp = Piwik::translate('TagManager_MatomoConfigurationMatomoRequestMethodInlineHelp', ['<a href="' . Url::addCampaignParametersToMatomoLink('https://matomo.org/faq/how-to/faq_18694/') . '" target="_blank" rel="noreferrer noopener">', '</a>', '<br>']);
        }), $matomoUrl = $this->makeSetting('requestContentType', 'application/x-www-form-urlencoded; charset=UTF-8', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoRequestContentTypeTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoRequestContentTypeDescription');
            $field->uiControlAttributes = ['placeholder' => Piwik::translate('TagManager_MatomoConfigurationMatomoRequestContentTypePlaceholder')];
            $field->condition = 'forceRequestMethod && requestMethod == "POST"';
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('customRequestProcessing', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) use($idSite, $idContainer) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoCustomRequestProcessingTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoCustomRequestProcessingDescription', Piwik::translate('TagManager_CustomRequestProcessingVariableName'));
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->validators[] = new CustomRequestProcessing($idSite, $idContainer);
        }), $this->makeSetting('customData', array(), FieldConfig::TYPE_ARRAY, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoCustomDataTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoCustomDataDescription');
            $field->validate = function ($value) {
                if (empty($value)) {
                    return;
                }
                if (!is_array($value)) {
                    throw new \Exception(Piwik::translate('TagManager_MatomoConfigurationMatomoCustomDimensionsException'));
                }
            };
            $field->uiControl = FieldConfig::UI_CONTROL_MULTI_TUPLE;
            $field->uiControlAttributes['rows'] = 1;
            $field->uiControlAttributes['showAdvancedSettings'] = 1;
            // This is used to hide/show this option under Advanced settings
            $field1 = new FieldConfig\MultiPair('Name', 'name', FieldConfig::UI_CONTROL_TEXT);
            $field1->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field2 = new FieldConfig\MultiPair('Value', 'value', FieldConfig::UI_CONTROL_TEXT);
            $field2->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->uiControlAttributes['field1'] = $field1->toArray();
            $field->uiControlAttributes['field2'] = $field2->toArray();
        }), $this->makeSetting('setDownloadExtensions', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoSetDownloadExtensionsTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoSetDownloadExtensionsDescription');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->uiControlAttributes['showAdvancedSettings'] = 1;
            // This is used to hide/show this option under Advanced settings
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('addDownloadExtensions', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoAddDownloadExtensionsTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoAddDownloadExtensionsDescription');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->uiControlAttributes['showAdvancedSettings'] = 1;
            // This is used to hide/show this option under Advanced settings
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('removeDownloadExtensions', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoRemoveDownloadExtensionsTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoRemoveDownloadExtensionsDescription');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->uiControlAttributes['showAdvancedSettings'] = 1;
            // This is used to hide/show this option under Advanced settings
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('setIgnoreClasses', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoSetIgnoreClassesTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoSetIgnoreClassesDescription');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->uiControlAttributes['showAdvancedSettings'] = 1;
            // This is used to hide/show this option under Advanced settings
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('setReferrerUrl', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoSetReferrerUrlTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoSetReferrerUrlDescription');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->uiControlAttributes['showAdvancedSettings'] = 1;
            // This is used to hide/show this option under Advanced settings
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('setApiUrl', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoSetApiUrlTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoSetApiUrlDescription');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->uiControlAttributes['showAdvancedSettings'] = 1;
            // This is used to hide/show this option under Advanced settings
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('setPageViewId', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoSetPageViewIdTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoSetPageViewIdDescription');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->uiControlAttributes['showAdvancedSettings'] = 1;
            // This is used to hide/show this option under Advanced settings
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('setExcludedReferrers', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoSetExcludedReferrersTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoSetExcludedReferrersDescription');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->uiControlAttributes['showAdvancedSettings'] = 1;
            // This is used to hide/show this option under Advanced settings
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('setDownloadClasses', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoSetDownloadClassesTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoSetDownloadClassesDescription');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->uiControlAttributes['showAdvancedSettings'] = 1;
            // This is used to hide/show this option under Advanced settings
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('setLinkClasses', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoSetLinkClassesTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoSetLinkClassesDescription');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->uiControlAttributes['showAdvancedSettings'] = 1;
            // This is used to hide/show this option under Advanced settings
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('setCampaignNameKey', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoSetCampaignNameKeyTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoSetCampaignNameKeyDescription');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->uiControlAttributes['showAdvancedSettings'] = 1;
            // This is used to hide/show this option under Advanced settings
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('setCampaignKeywordKey', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoSetCampaignKeywordKeyTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoSetCampaignKeywordKeyDescription');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->uiControlAttributes['showAdvancedSettings'] = 1;
            // This is used to hide/show this option under Advanced settings
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('setConsentGiven', \false, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoSetConsentGiveTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoSetConsentGiveDescription');
            $field->uiControlAttributes['showAdvancedSettings'] = 1;
            // This is used to hide/show this option under Advanced settings
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('rememberConsentGiven', \false, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoRememberConsentGivenTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoRememberConsentGivenDescription');
            $field->uiControlAttributes['showAdvancedSettings'] = 1;
            // This is used to hide/show this option under Advanced settings
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('rememberConsentGivenForHours', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoRememberConsentGivenForHoursTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoRememberConsentGivenForHoursDescription');
            $field->uiControlAttributes['showAdvancedSettings'] = 1;
            // This is used to hide/show this option under Advanced settings
            $field->transform = function ($value) {
                return trim($value);
            };
            $field->validate = function ($value) {
                if ($value && !is_numeric($value)) {
                    throw new \Exception(rtrim(Piwik::translate('TagManager_MatomoConfigurationMatomoRememberConsentGivenForHoursTitle'), '.') . ': ' . Piwik::translate('TagManager_MatomoConfigurationNonNumericValueException'));
                }
            };
        }), $this->makeSetting('forgetConsentGiven', \false, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoForgetConsentGivenTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoForgetConsentGivenDescription');
            $field->uiControlAttributes['showAdvancedSettings'] = 1;
            // This is used to hide/show this option under Advanced settings
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('discardHashTag', \false, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoDiscardHashTagTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoDiscardHashTagDescription');
            $field->uiControlAttributes['showAdvancedSettings'] = 1;
            // This is used to hide/show this option under Advanced settings
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('setExcludedQueryParams', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoSetExcludedQueryParamsTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoSetExcludedQueryParamsDescription');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->uiControlAttributes['showAdvancedSettings'] = 1;
            // This is used to hide/show this option under Advanced settings
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('setConversionAttributionFirstReferrer', \false, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoSetConversionAttributionTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoSetConversionAttributionDescription');
            $field->uiControlAttributes['showAdvancedSettings'] = 1;
            // This is used to hide/show this option under Advanced settings
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('setDoNotTrack', \false, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoSetDoNotTrackTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoSetDoNotTrackDescription');
            $field->uiControlAttributes['showAdvancedSettings'] = 1;
            // This is used to hide/show this option under Advanced settings
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('setLinkTrackingTimer', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoSetLinkTrackingTimerTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoSetLinkTrackingTimerDescription');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->uiControlAttributes['showAdvancedSettings'] = 1;
            // This is used to hide/show this option under Advanced settings
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('killFrame', \false, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoKillFrameTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoKillFrameDescription');
            $field->uiControlAttributes['showAdvancedSettings'] = 1;
            // This is used to hide/show this option under Advanced settings
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('setCountPreRendered', \false, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoSetCountPreRenderedTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoSetCountPreRenderedDescription');
            $field->uiControlAttributes['showAdvancedSettings'] = 1;
            // This is used to hide/show this option under Advanced settings
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('setRequestQueueInterval', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoSetRequestQueueIntervalTitle');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoSetRequestQueueIntervalDescription');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->uiControlAttributes['showAdvancedSettings'] = 1;
            // This is used to hide/show this option under Advanced settings
            $field->transform = function ($value) {
                return trim($value);
            };
        }));
        $pluginParameters = [];
        if (\Piwik\Plugin\Manager::getInstance()->isPluginActivated('FormAnalytics')) {
            $pluginParameters[] = $this->makeSetting('enableFormAnalytics', \true, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
                $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoEnableFormAnalyticsTitle');
                $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoEnableFormAnalyticsDescription');
                $field->inlineHelp = Piwik::translate('TagManager_MatomoConfigurationMatomoEnableFormAnalyticsInlineHelp', array('<strong>', '</strong>'));
            });
        }
        if (\Piwik\Plugin\Manager::getInstance()->isPluginActivated('MediaAnalytics')) {
            $pluginParameters[] = $this->makeSetting('enableMediaAnalytics', \true, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
                $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoEnableMediaAnalyticsTitle');
                $field->description = Piwik::translate('TagManager_MatomoConfigurationMatomoEnableMediaAnalyticsDescription');
                $field->inlineHelp = Piwik::translate('TagManager_MatomoConfigurationMatomoEnableMediaAnalyticsInlineHelp', array('<strong>', '</strong>'));
            });
        }
        return $this->insertPluginParameters($pluginParameters, $parameters, $insertAfter = 'enableLinkTracking');
    }
    private function insertPluginParameters($pluginParameters, $parameters, $insertAfter)
    {
        if (empty($pluginParameters)) {
            return $parameters;
        }
        $found = \false;
        foreach ($parameters as $key => $parameter) {
            if ($parameter->getName() == $insertAfter) {
                $found = $key;
            }
        }
        if ($found === \false) {
            return array_merge($parameters, $pluginParameters);
        }
        $firstPart = array_slice($parameters, 0, $found + 1);
        $secondPart = array_slice($parameters, $found + 1);
        return array_merge(array_merge($firstPart, $pluginParameters), $secondPart);
    }
}
