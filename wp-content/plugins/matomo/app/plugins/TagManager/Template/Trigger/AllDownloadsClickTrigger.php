<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Template\Trigger;

use Piwik\Piwik;
use Piwik\Settings\FieldConfig;
use Piwik\Validators\CharacterLength;
use Piwik\Validators\NotEmpty;
class AllDownloadsClickTrigger extends \Piwik\Plugins\TagManager\Template\Trigger\BaseTrigger
{
    public function getCategory()
    {
        return self::CATEGORY_CLICK;
    }
    public function getIcon()
    {
        return 'plugins/TagManager/images/icons/pointer.svg';
    }
    public function getParameters()
    {
        $downloadFileExtensions = array('7z', 'aac', 'apk', 'arc', 'arj', 'asf', 'asx', 'avi', 'azw3', 'bin', 'csv', 'deb', 'dmg', 'doc', 'docx', 'epub', 'exe', 'flv', 'gif', 'gz', 'gzip', 'hqx', 'ibooks', 'jar', 'jpg', 'jpeg', 'js', 'mobi', 'mp2', 'mp3', 'mp4', 'mpg', 'mpeg', 'mov', 'movie', 'msi', 'msp', 'odb', 'odf', 'odg', 'ods', 'odt', 'ogg', 'ogv', 'pdf', 'phps', 'png', 'ppt', 'pptx', 'qt', 'qtm', 'ra', 'ram', 'rar', 'rpm', 'sea', 'sit', 'tar', 'tbz', 'tbz2', 'bz', 'bz2', 'tgz', 'torrent', 'txt', 'wav', 'wma', 'wmv', 'wpd', 'xls', 'xlsx', 'xml', 'z', 'zip');
        $downloadFileExtensions = implode(',', $downloadFileExtensions);
        return array($this->makeSetting('downloadExtensions', $downloadFileExtensions, FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_AllDownloadsClickTriggerDownloadExtensionsTitle');
            $field->description = Piwik::translate('TagManager_AllDownloadsClickTriggerDownloadExtensionsDescription');
            $field->uiControlAttributes = ['placeholder' => Piwik::translate('TagManager_AllDownloadsClickTriggerDownloadExtensionsPlaceholder')];
            $field->validators[] = new NotEmpty();
            $field->validators[] = new CharacterLength($min = 1, $max = 700);
            $field->transform = function ($value) {
                $value = explode(',', $value);
                foreach ($value as $i => $val) {
                    $value[$i] = trim($val);
                }
                return implode(',', $value);
            };
        }));
    }
}
