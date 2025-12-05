<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\Diagnostics\Diagnostic;

use Piwik\Translation\Translator;
/**
 * Check the PHP extensions.
 */
class PhpExtensionsCheck implements \Piwik\Plugins\Diagnostics\Diagnostic\Diagnostic
{
    /**
     * @var Translator
     */
    private $translator;
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }
    public function execute()
    {
        $label = $this->translator->translate('Installation_SystemCheckExtensions');
        $result = new \Piwik\Plugins\Diagnostics\Diagnostic\DiagnosticResult($label);
        $longErrorMessage = '';
        $requiredExtensions = $this->getRequiredExtensions();
        foreach ($requiredExtensions as $extension) {
            if (!extension_loaded($extension)) {
                $status = \Piwik\Plugins\Diagnostics\Diagnostic\DiagnosticResult::STATUS_ERROR;
                $comment = $extension . ': ' . $this->translator->translate('Installation_RestartWebServer');
                $longErrorMessage .= '<p>' . $this->getHelpMessage($extension) . '</p>';
            } else {
                $status = \Piwik\Plugins\Diagnostics\Diagnostic\DiagnosticResult::STATUS_OK;
                $comment = $extension;
            }
            $result->addItem(new \Piwik\Plugins\Diagnostics\Diagnostic\DiagnosticResultItem($status, $comment));
        }
        $result->setLongErrorMessage($longErrorMessage);
        return array($result);
    }
    /**
     * @return string[]
     */
    private function getRequiredExtensions()
    {
        $requiredExtensions = array('zlib', 'json', 'filter', 'hash', 'session');
        return $requiredExtensions;
    }
    private function getHelpMessage($missingExtension)
    {
        $messages = array('zlib' => 'Installation_SystemCheckZlibHelp', 'json' => 'Installation_SystemCheckWarnJsonHelp', 'filter' => 'Installation_SystemCheckFilterHelp', 'hash' => 'Installation_SystemCheckHashHelp', 'session' => 'Installation_SystemCheckSessionHelp');
        return $this->translator->translate($messages[$missingExtension]);
    }
}
