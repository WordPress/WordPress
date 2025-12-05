<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Model;

use Piwik\Piwik;
use Piwik\Plugins\TagManager\API\TagReference;
use Piwik\Plugins\TagManager\API\TriggerReference;
use Piwik\Plugins\TagManager\API\VariableReference;
use Piwik\Plugins\TagManager\Dao\VariablesDao;
use Piwik\Plugins\TagManager\Input\IdSite;
use Piwik\Plugins\TagManager\Validators\LookupTable;
use Piwik\Plugins\TagManager\Input\Name;
use Piwik\Plugins\TagManager\Template\BaseTemplate;
use Piwik\Plugins\TagManager\Template\Variable\VariablesProvider;
use Piwik\Settings\FieldConfig;
use Piwik\Validators\BaseValidator;
use Piwik\Validators\CharacterLength;
class Variable extends \Piwik\Plugins\TagManager\Model\BaseModel
{
    /**
     * @var VariablesDao
     */
    private $dao;
    /**
     * @var VariablesProvider
     */
    private $variablesProvider;
    /**
     * @var Tag
     */
    private $tag;
    /**
     * @var Trigger
     */
    private $trigger;
    public function __construct(VariablesDao $variablesDao, VariablesProvider $variablesProvider, \Piwik\Plugins\TagManager\Model\Tag $tag, \Piwik\Plugins\TagManager\Model\Trigger $trigger)
    {
        $this->dao = $variablesDao;
        $this->variablesProvider = $variablesProvider;
        $this->tag = $tag;
        $this->trigger = $trigger;
    }
    private function validateValues($idSite, $name, $defaultValue, $lookupTable)
    {
        $site = new IdSite($idSite);
        $site->check();
        $theName = new Name($name);
        $theName->check();
        if ($this->variablesProvider->getPreConfiguredVariable($name)) {
            throw new \Exception(Piwik::translate('TagManager_ErrorVariableNameInUseByPreconfiguredVariable'));
        }
        if (isset($defaultValue) && !is_string($defaultValue) && !is_int($defaultValue) && !is_float($defaultValue) && !is_bool($defaultValue)) {
            throw new \Exception(Piwik::translate('TagManager_ErrorVariableInvalidDefaultValue'));
        }
        BaseValidator::check(Piwik::translate('TagManager_DefaultValue'), $lookupTable, [new CharacterLength(0, 300)]);
        BaseValidator::check(Piwik::translate('TagManager_LookupTable'), $lookupTable, [new LookupTable()]);
    }
    public function addContainerVariable($idSite, $idContainerVersion, $type, $name, $parameters, $defaultValue, $lookupTable, $description = '')
    {
        $this->validateValues($idSite, $name, $defaultValue, $lookupTable);
        $this->variablesProvider->checkIsValidVariable($type);
        $createdDate = $this->getCurrentDateTime();
        $parameters = $this->formatParameters($type, $parameters);
        return $this->dao->createVariable($idSite, $idContainerVersion, $type, $name, $parameters, $defaultValue, $lookupTable, $createdDate, $description);
    }
    public function updateContainerVariable($idSite, $idContainerVersion, $idVariable, $name, $parameters, $defaultValue, $lookupTable, $description = '')
    {
        $this->validateValues($idSite, $name, $defaultValue, $lookupTable);
        $variable = $this->dao->getContainerVariable($idSite, $idContainerVersion, $idVariable);
        if (!empty($variable)) {
            $parameters = $this->formatParameters($variable['type'], $parameters);
            $columns = array('name' => $name, 'description' => $description, 'default_value' => $defaultValue, 'lookup_table' => $lookupTable, 'parameters' => $parameters);
            $this->updateVariableColumns($idSite, $idContainerVersion, $idVariable, $columns);
            if ($variable['name'] !== $name) {
                $this->updateContainerVariableReferences($idSite, $idContainerVersion, $variable['name'], $name);
            }
        }
    }
    private function formatParameters($variableType, $parameters)
    {
        $variableTemplate = $this->variablesProvider->getVariable($variableType);
        if (empty($variableTemplate)) {
            throw new \Exception('Invalid variable type');
        }
        $params = $variableTemplate->getParameters();
        // we make sure to only save parameters that are defined in the tag template
        $newParameters = [];
        foreach ($params as $param) {
            if (isset($parameters[$param->getName()])) {
                $param->setValue($parameters[$param->getName()]);
                $newParameters[$param->getName()] = $param->getValue();
            } else {
                // we need to set a value to make sure that if for example a value is required, we trigger an error
                $param->setValue($param->getDefaultValue());
            }
        }
        return $newParameters;
    }
    public function getContainerVariableReferences($idSite, $idContainerVersion, $idVariable)
    {
        $variable = $this->dao->getContainerVariable($idSite, $idContainerVersion, $idVariable);
        if (empty($variable)) {
            return [];
        }
        $varName = $variable['name'];
        $references = [];
        $tags = $this->tag->getContainerTags($idSite, $idContainerVersion);
        $triggers = $this->trigger->getContainerTriggers($idSite, $idContainerVersion);
        $variables = $this->getContainerVariables($idSite, $idContainerVersion);
        foreach ($tags as $tag) {
            foreach ($tag['typeMetadata']['parameters'] as $parameter) {
                if ($this->isUsingParameterTheVariable($parameter, $varName)) {
                    $tagRef = new TagReference($tag['idtag'], $tag['name']);
                    $references[] = $tagRef->toArray();
                }
            }
        }
        foreach ($triggers as $trigger) {
            foreach ($trigger['typeMetadata']['parameters'] as $parameter) {
                if ($this->isUsingParameterTheVariable($parameter, $varName)) {
                    $triggerRef = new TriggerReference($trigger['idtrigger'], $trigger['name']);
                    $references[] = $triggerRef->toArray();
                    continue 2;
                    // not needed to check for condition reference
                }
            }
            foreach ($trigger['conditions'] as $condition) {
                if ($condition['actual'] === $varName) {
                    $triggerRef = new TriggerReference($trigger['idtrigger'], $trigger['name']);
                    $references[] = $triggerRef->toArray();
                }
            }
        }
        foreach ($variables as $var) {
            $tempReferences = $this->listVariableNamesInParameters($var);
            if (in_array($varName, $tempReferences)) {
                $variableRef = new VariableReference($var['idvariable'], $var['name']);
                $references[] = $variableRef->toArray();
            }
        }
        return $references;
    }
    public static function hasFieldConfigVariableParameter($parameter)
    {
        if (!empty($parameter['component']) && ($parameter['component'] === BaseTemplate::FIELD_TEXTAREA_VARIABLE_COMPONENT || $parameter['component'] === BaseTemplate::FIELD_VARIABLE_COMPONENT || $parameter['component'] === BaseTemplate::FIELD_VARIABLE_TYPE_COMPONENT)) {
            return \true;
        }
        if (!empty($parameter['uiControl']) && $parameter['uiControl'] === FieldConfig::UI_CONTROL_MULTI_TUPLE) {
            if (!empty($parameter['uiControlAttributes']['field1']) && self::hasFieldConfigVariableParameter($parameter['uiControlAttributes']['field1'])) {
                return \true;
            }
            if (!empty($parameter['uiControlAttributes']['field2']) && self::hasFieldConfigVariableParameter($parameter['uiControlAttributes']['field2'])) {
                return \true;
            }
        }
        if (!empty($parameter['uiControlAttributes']['parseVariables'])) {
            // workaround for some variables that don't use above templates but still need to be parsed
            return \true;
        }
        return \false;
    }
    private function isUsingParameterTheVariable($parameter, $varName)
    {
        $varNameTemplate = $this->convertVariableNameToTemplateVar($varName);
        if (!self::hasFieldConfigVariableParameter($parameter)) {
            return \false;
        }
        if (is_string($parameter['value'])) {
            $value = $parameter['value'];
        } elseif (is_array($parameter['value'])) {
            // todo: in theory, when using a MultiTuple field where 2 fields can be configured, we would need to check
            // whether both or only one of the fields are using variables and then iterate over all values to only
            // check the values for that specific object key/field. Eg array(array('index' => '{{foo}}', 'bar' => '{{baz}}'))
            // in theory it is possible that "index" key is a variable, but "bar" key is not and actually the user entered that text
            // simplify when the value has an array instead of iterating over everything...
            $value = json_encode($parameter['value']);
        } else {
            // we do not support objects or resources... and an integer or boolean etc cannot contain a variable
            return \false;
        }
        return strpos($value, $varNameTemplate) !== \false;
    }
    private function updateContainerVariableReferences($idSite, $idContainerVersion, $oldVarName, $newVarName)
    {
        $tags = $this->tag->getContainerTags($idSite, $idContainerVersion);
        $triggers = $this->trigger->getContainerTriggers($idSite, $idContainerVersion);
        $variables = $this->getContainerVariables($idSite, $idContainerVersion);
        foreach ($tags as $tag) {
            $parameters = $this->replaceVariableNameInParameters($tag, $oldVarName, $newVarName);
            if ($parameters) {
                $this->tag->updateParameters($idSite, $idContainerVersion, $tag['idtag'], $parameters);
            }
        }
        foreach ($triggers as $trigger) {
            $parameters = $this->replaceVariableNameInParameters($trigger, $oldVarName, $newVarName);
            $found = \false;
            foreach ($trigger['conditions'] as $index => $condition) {
                if (isset($condition['actual']) && $condition['actual'] === $oldVarName) {
                    $found = \true;
                    $condition['actual'] = $newVarName;
                    $trigger['conditions'][$index] = $condition;
                }
            }
            if ($parameters || $found) {
                $this->trigger->updateContainerTrigger($idSite, $idContainerVersion, $trigger['idtrigger'], $trigger['name'], $parameters, $trigger['conditions']);
            }
        }
        foreach ($variables as $variable) {
            $parameters = $this->replaceVariableNameInParameters($variable, $oldVarName, $newVarName);
            if ($parameters) {
                $this->updateVariableColumns($idSite, $idContainerVersion, $variable['idvariable'], array('parameters' => $parameters));
            }
        }
    }
    private function replaceVariableNameInParameters($entity, $oldVarName, $newVarName)
    {
        $oldVarNameTemplate = $this->convertVariableNameToTemplateVar($oldVarName);
        $newVarNameTemplate = $this->convertVariableNameToTemplateVar($newVarName);
        $found = \false;
        $parameters = $entity['parameters'];
        foreach ($entity['typeMetadata']['parameters'] as $parameter) {
            $paramName = $parameter['name'];
            if ($this->canParameterContainVariables($parameter, $entity['type']) && isset($parameters[$paramName]) && is_string($parameters[$paramName]) && strpos($parameters[$paramName], $oldVarNameTemplate) !== \false) {
                $found = \true;
                $parameters[$paramName] = str_replace($oldVarNameTemplate, $newVarNameTemplate, $parameters[$paramName]);
            }
        }
        if ($found) {
            return $parameters;
        }
    }
    private function canParameterContainVariables(array $parameterMetadata, string $entityType)
    {
        // If the parameter is for a variable component, or it's the jsFunction param of a CustomJsFunction variable
        return isset($parameterMetadata['component']) && in_array($parameterMetadata['component'], [BaseTemplate::FIELD_VARIABLE_COMPONENT, BaseTemplate::FIELD_VARIABLE_TYPE_COMPONENT]) || $entityType === 'CustomJsFunction' && $parameterMetadata['name'] === 'jsFunction' || $entityType === 'CustomHtml' && $parameterMetadata['name'] === 'customHtml';
    }
    /**
     * Check the Tag/Trigger/Variable for references to variables. Return a list of variable names that were found.
     *
     * @param array $entity The array of the Tag/Trigger/Variable built when loading the entry from the database
     * @return array List of names found referenced by the provided entity.
     */
    public function listVariableNamesInParameters(array $entity) : array
    {
        $variables = [];
        $parameters = $entity['parameters'];
        foreach ($entity['typeMetadata']['parameters'] as $parameter) {
            $paramName = $parameter['name'];
            if ($this->canParameterContainVariables($parameter, $entity['type']) && isset($parameters[$paramName]) && is_string($parameters[$paramName]) && strpos($parameters[$paramName], '{{') !== \false) {
                // Use regex to get the list of all the variable names
                $matches = [];
                preg_match_all('/{{.[^}]+}}/', $parameters[$paramName], $matches);
                $matches = array_unique($matches[0]);
                $variables = array_map(function ($value) {
                    return trim(str_replace(['{{', '}}'], '', $value));
                }, $matches);
            }
        }
        return array_unique($variables);
    }
    public function convertVariableNameToTemplateVar($variableName)
    {
        return '{{' . $variableName . '}}';
    }
    public function getContainerVariables($idSite, $idContainerVersion)
    {
        $variables = $this->dao->getContainerVariables($idSite, $idContainerVersion);
        return $this->enrichVariables($variables);
    }
    public function deleteContainerVariable($idSite, $idContainerVersion, $idVariable)
    {
        if ($this->getContainerVariableReferences($idSite, $idContainerVersion, $idVariable)) {
            throw new \Exception(Piwik::translate('TagManager_ErrorDeleteReferencedVariable'));
        }
        $this->dao->deleteContainerVariable($idSite, $idContainerVersion, $idVariable, $this->getCurrentDateTime());
    }
    public function getContainerVariable($idSite, $idContainerVersion, $idVariable)
    {
        $variable = $this->dao->getContainerVariable($idSite, $idContainerVersion, $idVariable);
        return $this->enrichVariable($variable);
    }
    public function findVariableByName($idSite, $idContainerVersion, $variableName)
    {
        $variable = $this->dao->findVariableByName($idSite, $idContainerVersion, $variableName);
        return $this->enrichVariable($variable);
    }
    /**
     * Check the Tag/Trigger/Variable for references to variables. If any are found, update the names in the parameters
     * to reference the copies. For triggers, do the same for the conditions.
     *
     * @param array $entity The array of the Tag/Trigger/Variable built when loading the entry from the database. Copied
     * by reference so that the variable references within the entity can be updated with the new variable names.
     * @param int $idSite ID of the source site from which the variables are being copied
     * @param int $idContainerVersion ID of the source container version from which the variables are being copied
     * @param null|int $idDestinationSite Optional ID of the site to which the variables are being copied. In not
     * specified, the idSite is used
     * @param null|int $idDestinationVersion Optional ID of the container version to which the variables are being
     * copied. If not specified, the idContainerVersion is used
     * @return void
     * @throws \Exception
     */
    public function copyReferencedVariables(array &$entity, int $idSite, int $idContainerVersion, ?int $idDestinationSite = 0, ?int $idDestinationVersion = 0) : void
    {
        $idDestinationSite = $idDestinationSite ?: $idSite;
        $idDestinationVersion = $idDestinationVersion ?: $idContainerVersion;
        $variableNameList = $this->listVariableNamesInParameters($entity);
        $variableNameMap = [];
        foreach ($variableNameList as $variableName) {
            $newVarName = $this->copyVariableByNameIfNoEquivalent($variableName, $idSite, $idContainerVersion, $idDestinationSite, $idDestinationVersion);
            // This might be empty if it's a preconfigured variable and doesn't exist in the DB. So, just skip it
            if (empty($newVarName)) {
                continue;
            }
            // Update the references in parameters with the new variable name
            $entity['parameters'] = $this->replaceVariableNameInParameters($entity, $variableName, $newVarName);
            // Map out the original name to the new one
            $variableNameMap[$variableName] = $newVarName;
        }
        // If the entity is not a trigger, we're done
        if (empty($entity['idtrigger']) || !is_array($entity['conditions'])) {
            return;
        }
        // If the entity is a trigger, copy any variables in its conditions
        foreach ($entity['conditions'] as $index => $condition) {
            if (empty($condition['actual'])) {
                continue;
            }
            // If the variable was already copied above, simply use the name of the new variable copy
            if (in_array($condition['actual'], $variableNameList)) {
                $entity['conditions'][$index]['actual'] = $variableNameMap[$condition['actual']];
                continue;
            }
            $newVarName = $this->copyVariableByNameIfNoEquivalent($condition['actual'], $idSite, $idContainerVersion, $idDestinationSite, $idDestinationVersion);
            // This might be empty if it's a preconfigured variable and doesn't exist in the DB. So, just skip it
            if (empty($newVarName)) {
                continue;
            }
            // Replace the old variable name with the new one
            $entity['conditions'][$index]['actual'] = $newVarName;
        }
    }
    /**
     * Make a copy of the variable and return the ID.
     *
     * @param int $idSite
     * @param int $idContainerVersion
     * @param int $idVariable
     * @param null|int $idDestinationSite Optional ID of the site to which to copy the variable. If empty, isSite is used
     * @param string|null $idDestinationContainer Optional ID of the container to copy the variable to. If not provided
     * the copy goes to the source site and container
     * @return int ID of the newly created variable
     */
    public function copyVariable(int $idSite, int $idContainerVersion, int $idVariable, ?int $idDestinationSite = 0, ?string $idDestinationContainer = null) : int
    {
        $idDestinationSite = $idDestinationSite ?: $idSite;
        $idDestinationVersion = $idContainerVersion;
        if ($idDestinationSite !== null && !empty($idDestinationContainer)) {
            $idDestinationVersion = $this->getDraftContainerVersion($idDestinationSite, $idDestinationContainer);
        }
        $variable = $this->getContainerVariable($idSite, $idContainerVersion, $idVariable);
        $newVarName = $this->dao->makeCopyNameUnique($idDestinationSite, $variable['name'], $idDestinationVersion);
        $this->copyReferencedVariables($variable, $idSite, $idContainerVersion, $idDestinationSite, $idDestinationVersion);
        return $this->addContainerVariable($idDestinationSite, $idDestinationVersion, $variable['type'], $newVarName, $variable['parameters'], $variable['default_value'], $variable['lookup_table'], $variable['description']);
    }
    private function copyVariableByNameIfNoEquivalent(string $variableName, int $idSite, int $idContainerVersion, int $idDestinationSite, int $idDestinationContainerVersion) : string
    {
        $variable = $this->findVariableByName($idSite, $idContainerVersion, $variableName);
        // This might be empty if it's a preconfigured variable and doesn't exist in the DB. So, just skip it
        if (empty($variable)) {
            return '';
        }
        // If the site and container version are the same, we already know that the variable exists, so return its name
        if ($idSite === $idDestinationSite && $idContainerVersion === $idDestinationContainerVersion) {
            return $variableName;
        }
        // If no variable with that name is found, call the method to make a copy
        $existingVariable = $this->findVariableByName($idDestinationSite, $idDestinationContainerVersion, $variableName);
        if (empty($existingVariable)) {
            return $this->copyVariableByName($variable, $idSite, $idContainerVersion, $idDestinationSite, $idDestinationContainerVersion);
        }
        // If a duplicate variable already exists in the destination container, just use that variable
        if ($variable['type'] === $existingVariable['type'] && $variable['parameters'] == $existingVariable['parameters'] && $variable['lookup_table'] == $existingVariable['lookup_table'] && $variable['default_value'] == $existingVariable['default_value']) {
            return $variableName;
        }
        // Since no existing duplicate was found, make a copy of the variable
        return $this->copyVariableByName($variable, $idSite, $idContainerVersion, $idDestinationSite, $idDestinationContainerVersion);
    }
    private function copyVariableByName(array $variable, int $idSite, int $idContainerVersion, int $idDestinationSite, int $idDestinationContainerVersion) : string
    {
        if (empty($variable) || empty($variable['type']) || empty($variable['name']) || empty($variable['parameters']) || !isset($variable['default_value']) || !isset($variable['lookup_table']) || !isset($variable['description'])) {
            throw new \Exception('Variable name cannot be empty');
        }
        $this->copyReferencedVariables($variable, $idSite, $idContainerVersion, $idDestinationSite, $idDestinationContainerVersion);
        // Insert the new variable
        $newVarName = $this->dao->makeCopyNameUnique($idDestinationSite, $variable['name'], $idDestinationContainerVersion);
        $this->addContainerVariable($idDestinationSite, $idDestinationContainerVersion, $variable['type'], $newVarName, $variable['parameters'], $variable['default_value'], $variable['lookup_table'], $variable['description']);
        return $newVarName;
    }
    private function updateVariableColumns($idSite, $idContainerVersion, $idVariable, $columns)
    {
        if (!isset($columns['updated_date'])) {
            $columns['updated_date'] = $this->getCurrentDateTime();
        }
        $this->dao->updateVariableColumns($idSite, $idContainerVersion, $idVariable, $columns);
    }
    private function enrichVariables($variables)
    {
        if (empty($variables)) {
            return array();
        }
        foreach ($variables as $index => $variable) {
            $variables[$index] = $this->enrichVariable($variable);
        }
        return $variables;
    }
    private function enrichVariable($variable)
    {
        if (empty($variable)) {
            return $variable;
        }
        $variable['created_date_pretty'] = $this->formatDate($variable['created_date'], $variable['idsite']);
        $variable['updated_date_pretty'] = $this->formatDate($variable['updated_date'], $variable['idsite']);
        unset($variable['deleted_date']);
        $variable['typeMetadata'] = null;
        if (empty($variable['parameters'])) {
            $variable['parameters'] = array();
        }
        $variableTemplate = $this->variablesProvider->getVariable($variable['type']);
        if (!empty($variableTemplate)) {
            $variable['typeMetadata'] = $variableTemplate->toArray();
            foreach ($variable['typeMetadata']['parameters'] as &$parameter) {
                $paramName = $parameter['name'];
                if (isset($variable['parameters'][$paramName])) {
                    $parameter['value'] = $variable['parameters'][$paramName];
                } else {
                    $variable['parameters'][$paramName] = $parameter['defaultValue'];
                }
            }
        }
        return $variable;
    }
}
