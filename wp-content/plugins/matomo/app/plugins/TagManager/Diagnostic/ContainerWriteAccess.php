<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Diagnostic;

use Piwik\Filechecks;
use Piwik\Plugins\Diagnostics\Diagnostic\Diagnostic;
use Piwik\Plugins\Diagnostics\Diagnostic\DiagnosticResult;
use Piwik\Plugins\Diagnostics\Diagnostic\DiagnosticResultItem;
use Piwik\Plugins\TagManager\TagManager;
use Piwik\Translation\Translator;
/**
 * Check the permissions for some directories.
 */
class ContainerWriteAccess implements Diagnostic
{
    /**
     * @var Translator
     */
    private $translator;
    /**
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }
    public function execute()
    {
        $label = $this->translator->translate('TagManager_CheckWriteDirs', $this->translator->translate('TagManager_TagManager'));
        $result = new DiagnosticResult($label);
        $pathJsDir = TagManager::getAbsolutePathToContainerDirectory();
        $directories = array($pathJsDir);
        $directories = Filechecks::checkDirectoriesWritable($directories);
        $error = \false;
        foreach ($directories as $directory => $isWritable) {
            if ($isWritable) {
                $status = DiagnosticResult::STATUS_OK;
            } else {
                $status = DiagnosticResult::STATUS_ERROR;
                $error = \true;
            }
            $result->addItem(new DiagnosticResultItem($status, $directory));
        }
        if ($error) {
            $longErrorMessage = $this->translator->translate('Installation_SystemCheckWriteDirsHelp');
            $longErrorMessage .= '<ul>';
            foreach ($directories as $directory => $isWritable) {
                if (!$isWritable) {
                    $longErrorMessage .= sprintf('<li><pre>chmod a+w %s</pre></li>', $directory);
                }
            }
            $longErrorMessage .= '</ul>';
            $result->setLongErrorMessage($longErrorMessage);
        }
        return array($result);
    }
}
