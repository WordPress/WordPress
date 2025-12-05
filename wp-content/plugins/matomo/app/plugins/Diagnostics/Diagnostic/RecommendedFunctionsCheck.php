<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\Diagnostics\Diagnostic;

use Piwik\Translation\Translator;
use Piwik\Url;
/**
 * Check the PHP functions that are not required but recommended.
 */
class RecommendedFunctionsCheck implements \Piwik\Plugins\Diagnostics\Diagnostic\Diagnostic
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
        $label = $this->translator->translate('Installation_SystemCheckOtherFunctions');
        $result = new \Piwik\Plugins\Diagnostics\Diagnostic\DiagnosticResult($label);
        foreach ($this->getRecommendedFunctions() as $function) {
            if (!\Piwik\Plugins\Diagnostics\Diagnostic\PhpFunctionsCheck::functionExists($function)) {
                $status = \Piwik\Plugins\Diagnostics\Diagnostic\DiagnosticResult::STATUS_WARNING;
                $comment = $function . '<br/>' . $this->getHelpMessage($function);
            } else {
                $status = \Piwik\Plugins\Diagnostics\Diagnostic\DiagnosticResult::STATUS_OK;
                $comment = $function;
            }
            $result->addItem(new \Piwik\Plugins\Diagnostics\Diagnostic\DiagnosticResultItem($status, $comment));
        }
        return array($result);
    }
    /**
     * @return string[]
     */
    private function getRecommendedFunctions()
    {
        return array('shell_exec', 'set_time_limit', 'mail', 'parse_ini_file', 'glob', 'gzopen', 'md5_file', 'hash_file');
    }
    private function getHelpMessage($function)
    {
        $messages = array('shell_exec' => 'Installation_SystemCheckShellExecHelp', 'set_time_limit' => 'Installation_SystemCheckTimeLimitHelp', 'mail' => 'Installation_SystemCheckMailHelp', 'parse_ini_file' => 'Installation_SystemCheckParseIniFileHelp', 'glob' => 'Installation_SystemCheckGlobHelp', 'gzopen' => 'Installation_SystemCheckZlibHelp');
        $translation_params = array('shell_exec' => ["<a href='" . Url::addCampaignParametersToMatomoLink('https://matomo.org/faq/troubleshooting/how-to-make-the-diagnostic-managing-processes-via-cli-to-display-ok/') . "' rel='noopener' target='_blank'>", "</a>"]);
        return $this->translator->translate($messages[$function], $translation_params[$function] ?? []);
    }
}
