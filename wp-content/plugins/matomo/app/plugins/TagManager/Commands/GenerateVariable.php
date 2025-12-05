<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Commands;

use Piwik\Plugins\CoreConsole\Commands\GeneratePluginBase;
class GenerateVariable extends GeneratePluginBase
{
    protected function configure()
    {
        $this->setName('generate:tagmanager-variable');
        $this->setDescription('Generate Variable');
        $this->addRequiredValueOption('pluginname', null, 'The name of an existing plugin');
        $this->addRequiredValueOption('variablename', null, 'The name of the variable you want to create');
    }
    /**
     * @return int
     */
    protected function doExecute() : int
    {
        $pluginName = $this->getPluginName();
        $this->checkAndUpdateRequiredPiwikVersion($pluginName);
        $variableName = $this->getVariableName();
        $variableId = str_replace(array('-', ' '), '', $variableName);
        $variableClass = $variableId . 'Variable';
        $exampleFolder = PIWIK_INCLUDE_PATH . '/plugins/TagManager';
        $replace = array('TagManager' => $pluginName, 'Url' => $variableId, 'Piwik\\Plugins\\' . $pluginName . '\\Template\\Variable\\BaseVariable' => 'Piwik\\Plugins\\TagManager\\Template\\Variable\\BaseVariable', 'parameters, ' . $pluginName => 'parameters, TagManager');
        $whitelistFiles = array('/Template', '/Template/Variable', '/Template/Variable/UrlVariable.php', '/Template/Variable/UrlVariable.web.js');
        $this->copyTemplateToPlugin($exampleFolder, $pluginName, $replace, $whitelistFiles);
        $this->makeTranslationIfPossible($pluginName, $variableName, $variableClass . 'Name');
        $this->makeTranslationIfPossible($pluginName, "This is the description for " . $variableName, $variableClass . 'Description');
        $this->makeTranslationIfPossible($pluginName, "", $variableClass . 'Help');
        $this->writeSuccessMessage(array(sprintf('Variable for %s in folder "plugins/%s/Template/Variable" generated.', $pluginName, $pluginName), 'You can now start implementing the variable', 'Enjoy!'));
        return self::SUCCESS;
    }
    /**
     * @return string
     * @throws \RuntimeException
     */
    private function getVariableName()
    {
        $variableName = $this->getInput()->getOption('variablename');
        $validate = function ($testname) {
            if (empty($testname)) {
                throw new \InvalidArgumentException('You have to enter a variable name');
            }
            if (preg_match("/^[0-9]/", $testname)) {
                throw new \InvalidArgumentException('The variable name may not start with a number.');
            }
            if (preg_match("/[^A-Za-z0-9 -]/", $testname)) {
                throw new \InvalidArgumentException('Only alpha numerical characters, whitespaces, and dashes are allowed as a variable name.');
            }
            return $testname;
        };
        if (empty($variableName)) {
            $variableName = $this->askAndValidate('Enter the name of the variable (CamelCase): ', $validate);
        } else {
            $validate($variableName);
        }
        $variableName = ucfirst($variableName);
        return $variableName;
    }
    protected function getPluginName()
    {
        $pluginNames = $this->getPluginNames();
        $invalidName = 'You have to enter the name of an existing plugin';
        return $this->askPluginNameAndValidate($pluginNames, $invalidName);
    }
}
