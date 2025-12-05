<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Template\Tag;

use Piwik\Plugins\TagManager\Context\WebContext;
use Piwik\Plugins\TagManager\Template\BaseTemplate;
/**
 * @api
 */
abstract class BaseTag extends BaseTemplate
{
    public const CATEGORY_ANALYTICS = 'TagManager_CategoryAnalytics';
    public const CATEGORY_CUSTOM = 'TagManager_CategoryCustom';
    public const CATEGORY_DEVELOPERS = 'TagManager_CategoryDevelopers';
    public const CATEGORY_ADS = 'TagManager_CategoryAds';
    public const CATEGORY_EMAIL = 'TagManager_CategoryEmail';
    public const CATEGORY_AFFILIATES = 'TagManager_CategoryAffiliates';
    public const CATEGORY_REMARKETING = 'TagManager_CategoryRemarketing';
    public const CATEGORY_SOCIAL = 'TagManager_CategorySocial';
    public const CATEGORY_OTHERS = 'General_Others';
    protected $templateType = 'Tag';
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
}
