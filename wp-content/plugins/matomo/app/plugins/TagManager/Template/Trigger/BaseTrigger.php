<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Template\Trigger;

use Piwik\Plugins\TagManager\Context\WebContext;
use Piwik\Plugins\TagManager\Template\BaseTemplate;
/**
 * @api
 */
abstract class BaseTrigger extends BaseTemplate
{
    public const CATEGORY_PAGE_VIEW = 'TagManager_CategoryPageview';
    public const CATEGORY_CLICK = 'TagManager_CategoryClick';
    public const CATEGORY_USER_ENGAGEMENT = 'TagManager_CategoryUserEngagement';
    public const CATEGORY_OTHERS = 'General_Others';
    protected $templateType = 'Trigger';
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
