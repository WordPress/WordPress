<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Template\Variable;

use Piwik\Piwik;
use Piwik\Settings\FieldConfig;
use Piwik\Validators\NotEmpty;
class MetaContentVariable extends \Piwik\Plugins\TagManager\Template\Variable\BaseVariable
{
    public function getCategory()
    {
        return self::CATEGORY_SEO;
    }
    public function getParameters()
    {
        return array($this->makeSetting('metaName', 'keywords', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MetaContentVariableNameTitle');
            $field->uiControl = FieldConfig::UI_CONTROL_SINGLE_SELECT;
            $field->validators[] = new NotEmpty();
            $field->availableValues = array('keywords' => 'Keywords', 'description' => 'Description', 'author' => 'Author', 'viewport' => 'Viewport', 'generator' => 'Generator', 'subject' => 'Subject', 'language' => 'Language', 'robots' => 'Robots', 'copyright' => 'Copyright', 'application-name' => 'Application Name', 'content-type' => 'Content Type', 'og:site_name' => 'Open Graph Site Name', 'og:title' => 'Open Graph Title', 'og:description' => 'Open Graph Description', 'og:type' => 'Open Graph Type', 'og:url' => 'Open Graph URL', 'og:image' => 'Open Graph Image', 'og:locale' => 'Open Graph Locale');
        }));
    }
}
