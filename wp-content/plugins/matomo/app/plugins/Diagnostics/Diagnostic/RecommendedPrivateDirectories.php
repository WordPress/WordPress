<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\Diagnostics\Diagnostic;

use Piwik\Filesystem;
use Piwik\Translation\Translator;
use Piwik\Url;
class RecommendedPrivateDirectories extends \Piwik\Plugins\Diagnostics\Diagnostic\AbstractPrivateDirectories
{
    protected $privatePaths = ['tmp/', 'tmp/empty', 'lang/en.json'];
    protected $labelKey = 'Diagnostics_RecommendedPrivateDirectories';
    public function __construct(Translator $translator)
    {
        parent::__construct($translator);
        Filesystem::mkdir(PIWIK_INCLUDE_PATH . '/tmp');
        file_put_contents(PIWIK_INCLUDE_PATH . '/tmp/empty', 'test');
    }
    protected function addError(\Piwik\Plugins\Diagnostics\Diagnostic\DiagnosticResult &$result)
    {
        $result->addItem(new \Piwik\Plugins\Diagnostics\Diagnostic\DiagnosticResultItem(\Piwik\Plugins\Diagnostics\Diagnostic\DiagnosticResult::STATUS_INFORMATIONAL, $this->translator->translate('Diagnostics_UrlsAccessibleViaBrowser') . ' ' . $this->translator->translate('General_ReadThisToLearnMore', ['<a target="_blank" rel="noopener noreferrer" href="' . Url::addCampaignParametersToMatomoLink('https://matomo.org/faq/troubleshooting/how-do-i-fix-the-error-private-directories-are-accessible/') . '">', '</a>'])));
    }
}
