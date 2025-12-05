<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Template\Variable;

use Piwik\Plugins\TagManager\Context\WebContext;
use Piwik\Plugins\TagManager\Template\BaseTemplate;
/**
 * @api
 */
abstract class BaseVariable extends BaseTemplate
{
    public const CATEGORY_PAGE_VARIABLES = 'TagManager_CategoryPageVariables';
    public const CATEGORY_VISIBILITY = 'TagManager_CategoryVisibility';
    public const CATEGORY_CLICKS = 'TagManager_CategoryClicks';
    public const CATEGORY_CONTAINER_INFO = 'TagManager_CategoryContainerInfo';
    public const CATEGORY_HISTORY = 'TagManager_CategoryHistory';
    public const CATEGORY_ERRORS = 'TagManager_CategoryErrors';
    public const CATEGORY_SCROLLS = 'TagManager_CategoryScrolls';
    public const CATEGORY_FORMS = 'TagManager_CategoryForms';
    public const CATEGORY_DATE = 'TagManager_CategoryDate';
    public const CATEGORY_PERFORMANCE = 'TagManager_CategoryPerformance';
    public const CATEGORY_UTILITIES = 'TagManager_CategoryUtilities';
    public const CATEGORY_DEVICE = 'TagManager_CategoryDevice';
    public const CATEGORY_SEO = 'TagManager_CategorySEO';
    public const CATEGORY_OTHERS = 'General_Others';
    public const CATEGORY_ANALYTICS = 'TagManager_CategoryAnalytics';
    protected $templateType = 'Variable';
    /**
     * @inheritdoc
     */
    public function getCategory()
    {
        return self::CATEGORY_OTHERS;
    }
    /**
     * @inheritdoc
     */
    public function getSupportedContexts()
    {
        return array(WebContext::ID);
    }
    /**
     * Defines whether this variable is a preconfigured variable which cannot be configured and is ready to use.
     * @return bool
     */
    public function isPreConfigured()
    {
        return \false;
    }
}
