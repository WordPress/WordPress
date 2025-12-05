<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Context;

use Piwik\Common;
use Piwik\Container\StaticContainer;
use Piwik\Plugins\TagManager\Context\Storage\StorageInterface;
use Piwik\Plugins\TagManager\Dao\TagsDao;
use Piwik\Plugins\TagManager\Exception\EntityRecursionException;
use Piwik\Plugins\TagManager\Model\Container;
use Piwik\Plugins\TagManager\Model\Environment;
use Piwik\Plugins\TagManager\Model\Salt;
use Piwik\Plugins\TagManager\Model\Tag;
use Piwik\Plugins\TagManager\Model\Trigger;
use Piwik\Plugins\TagManager\Model\Variable;
use Piwik\Plugins\TagManager\Template\Variable\VariablesProvider;
use Piwik\Settings\FieldConfig;
abstract class BaseContext
{
    /**
     * @var VariablesProvider
     */
    protected $variablesProvider;
    /**
     * @var Variable
     */
    protected $variableModel;
    /**
     * @var Trigger
     */
    protected $triggerModel;
    /**
     * @var Tag
     */
    protected $tagModel;
    /**
     * @var Container
     */
    protected $containerModel;
    /**
     * @var StorageInterface
     */
    protected $storage;
    /**
     * @var Salt
     */
    protected $salt;
    private $variables = array();
    private $nestedVariableCals = [];
    public function __construct(VariablesProvider $variablesProvider, Variable $variableModel, Trigger $triggerModel, Tag $tagModel, Container $containerModel, StorageInterface $storage, Salt $salt)
    {
        $this->variablesProvider = $variablesProvider;
        $this->variableModel = $variableModel;
        $this->triggerModel = $triggerModel;
        $this->tagModel = $tagModel;
        $this->containerModel = $containerModel;
        $this->storage = $storage;
        $this->salt = $salt;
    }
    public abstract function getId();
    public abstract function getName();
    public abstract function generate($container);
    public abstract function getInstallInstructions($container, $environment);
    public abstract function getInstallInstructionsReact($container, $environment);
    protected function generatePublicContainer($container, $release)
    {
        $this->nestedVariableCals = [];
        $idSite = $container['idsite'];
        $idContainer = $container['idcontainer'];
        $isTagFireLimitAllowedInPreviewMode = 0;
        if (isset($container['isTagFireLimitAllowedInPreviewMode'])) {
            $isTagFireLimitAllowedInPreviewMode = $container['isTagFireLimitAllowedInPreviewMode'] ? 1 : 0;
        }
        $idContainerVersion = $release['idcontainerversion'];
        $container['idcontainerversion'] = $idContainerVersion;
        $environment = $release['environment'];
        $this->variables = [];
        $version = $this->containerModel->getContainerVersion($idSite, $idContainer, $idContainerVersion);
        $containerJs = ['id' => $idContainer, 'isTagFireLimitAllowedInPreviewMode' => $isTagFireLimitAllowedInPreviewMode, 'idsite' => $idSite, 'versionName' => $version['name'], 'revision' => $version['revision'], 'environment' => $environment, 'tags' => [], 'triggers' => [], 'variables' => []];
        foreach ($this->tagModel->getContainerTags($idSite, $idContainerVersion) as $tag) {
            if ($tag['status'] !== TagsDao::STATUS_ACTIVE) {
                continue;
            }
            $containerJs['tags'][] = ['id' => $tag['idtag'], 'type' => $tag['type'], 'name' => $tag['name'], 'parameters' => $this->parametersToVariableJs($container, $tag), 'blockTriggerIds' => $tag['block_trigger_ids'], 'fireTriggerIds' => $tag['fire_trigger_ids'], 'fireLimit' => $tag['fire_limit'], 'fireDelay' => $tag['fire_delay'], 'startDate' => $tag['start_date'], 'endDate' => $tag['end_date']];
        }
        foreach ($this->triggerModel->getContainerTriggers($idSite, $idContainerVersion) as $trigger) {
            // we are ignoring any trigger that is not actually used in any tag for performance reasons
            // (for now, we might change this later so triggers can more easily build on top of each other)
            $conditions = [];
            if (!empty($trigger['conditions'])) {
                foreach ($trigger['conditions'] as $condition) {
                    if (!empty($condition['actual'])) {
                        $actual = $this->variableToArray($container, $condition['actual']);
                        if ($actual) {
                            $conditions[] = ['actual' => $actual, 'comparison' => $condition['comparison'], 'expected' => $condition['expected']];
                        }
                    }
                }
            }
            $trigger = ['id' => $trigger['idtrigger'], 'type' => $trigger['type'], 'name' => $trigger['name'], 'parameters' => $this->parametersToVariableJs($container, $trigger), 'conditions' => $conditions];
            $containerJs['triggers'][] = $trigger;
        }
        foreach ($this->variableModel->getContainerVariables($idSite, $idContainerVersion) as $variable) {
            // we are ignoring any trigger that is not actually used in any tag for performance reasons
            // (for now, we might change this later so triggers can more easily build on top of each other)
            $this->variableToArray($container, $variable);
        }
        $containerJs['variables'] = array_values($this->variables);
        $this->variables = array();
        return $containerJs;
    }
    public function getPreConfiguredVariablesJSCodeResponse($context)
    {
        $response = ['keys' => [], 'values' => []];
        $preConfiguredVariables = $this->variablesProvider->getPreConfiguredVariables();
        foreach ($preConfiguredVariables as $variable) {
            if (method_exists($variable, 'getDataLayerVariableJs')) {
                $response['keys'][] = '{{' . $variable->getId() . '}}';
                $response['values'][] = $variable->getDataLayerVariableJs();
            } elseif (method_exists($variable, 'loadTemplate')) {
                $response['keys'][] = '{{' . $variable->getId() . '}}';
                $response['values'][] = '(function(){' . $variable->loadTemplate($context, $variable, \true) . '})()';
            }
        }
        return $response;
    }
    private function parametersToVariableJs($container, $entity)
    {
        if (!empty($entity['name'])) {
            $this->nestedVariableCals[] = $entity['name'];
        }
        if (count($this->nestedVariableCals) > 500) {
            // eg MatomoConfiguration variable referencing itself in a variable like matomoUrl=https://matomo.org{{MatomoConfiguration}}
            $entries = array_slice($this->nestedVariableCals, -3);
            // show last 3 entities in error message
            $entries = array_unique($entries);
            throw new EntityRecursionException('It seems an entity references itself or a recursion is caused in some other way. It may be related due to these entites: "' . implode(',', $entries) . '". Please check if the entity references itself maybe or if a recursion might happen in another way.');
        }
        $parameters = $entity['parameters'];
        $keyTemplateTypeSeparator = '____';
        $parameterTemplateTypes = array();
        if (!empty($entity['typeMetadata']['parameters'])) {
            foreach ($entity['typeMetadata']['parameters'] as $parameter) {
                // we replace variables only when the field type is a template
                if (Variable::hasFieldConfigVariableParameter($parameter)) {
                    $parameterTemplateTypes[] = $parameter['name'];
                }
                if (!empty($parameter['uiControl']) && $parameter['uiControl'] === FieldConfig::UI_CONTROL_MULTI_TUPLE) {
                    if (!empty($parameter['uiControlAttributes']['field1']['key']) && Variable::hasFieldConfigVariableParameter($parameter['uiControlAttributes']['field1'])) {
                        $parameterTemplateTypes[] = $parameter['name'] . $keyTemplateTypeSeparator . $parameter['uiControlAttributes']['field1']['key'];
                    }
                    if (!empty($parameter['uiControlAttributes']['field2']['key']) && Variable::hasFieldConfigVariableParameter($parameter['uiControlAttributes']['field2'])) {
                        $parameterTemplateTypes[] = $parameter['name'] . $keyTemplateTypeSeparator . $parameter['uiControlAttributes']['field2']['key'];
                    }
                }
            }
        }
        $vars = [];
        foreach ($parameters as $name => $value) {
            if (is_array($value)) {
                if (in_array($name, $parameterTemplateTypes, \true)) {
                    foreach ($value as $key => $subValue) {
                        if (is_array($subValue)) {
                            foreach ($subValue as $subKey => $subSubValue) {
                                if (in_array($name . $keyTemplateTypeSeparator . $subKey, $parameterTemplateTypes, \true)) {
                                    $value[$key][$subKey] = $this->parameterToVariableJs($subSubValue, $container);
                                }
                            }
                        } else {
                            $value[$key] = $this->parameterToVariableJs($subValue, $container);
                        }
                    }
                }
                $vars[$name] = $value;
            } else {
                if (in_array($name, $parameterTemplateTypes, \true)) {
                    $vars[$name] = $this->parameterToVariableJs($value, $container);
                } else {
                    $vars[$name] = $value;
                }
            }
        }
        if (!empty($entity['name'])) {
            array_pop($this->nestedVariableCals);
        }
        return $vars;
    }
    private function mb_strpos($haystack, $needle, $offset)
    {
        if (function_exists('mb_strpos')) {
            return mb_strpos($haystack, $needle, $offset, 'UTF-8');
        }
        return strpos($haystack, $needle, $offset);
    }
    private function mb_strrpos($haystack, $needle, $offset)
    {
        if (function_exists('mb_strpos')) {
            return mb_strrpos($haystack, $needle, $offset, 'UTF-8');
        }
        return strrpos($haystack, $needle, $offset);
    }
    protected function parameterToVariableJs($value, $container)
    {
        if (is_scalar($value) && preg_match_all('/{{.+?}}/', $value, $matches)) {
            $multiVars = [];
            $pos = 0;
            do {
                $start = $this->mb_strpos($value, '{{', $pos);
                $end = \false;
                if ($start !== \false) {
                    // only if string contains a {{ we need to look to see if we find a matching end string
                    $end = $this->mb_strpos($value, '}}', $start);
                }
                if ($end !== \false) {
                    // now this might seem random, but it is basically to detect if there are the brackets two times there
                    // like "foo{{notExisting{{PageUrl}}"  then we still detect "{{PageUrl}}"
                    $start = $this->mb_strrpos(Common::mb_substr($value, 0, $end), '{{', $pos);
                }
                if ($start === \false || $end === \false) {
                    $val = $this->substr($value, $pos);
                    if ($val !== '' && $val !== \false && $val !== null) {
                        $multiVars[] = $val;
                    }
                    break;
                }
                if ($start !== 0) {
                    // only if string does not start with "{{..."
                    $val = str_replace(array('\\{', '\\}'), array('{', '}'), $this->substr($value, $pos, $start - $pos));
                    // regular text
                    if ($val !== '' && $val !== \false && $val !== null) {
                        $multiVars[] = $val;
                    }
                }
                $ignoreLengthOpeningBrackets = 2;
                $variableName = Common::mb_substr($value, $start + $ignoreLengthOpeningBrackets, $end - ($start + $ignoreLengthOpeningBrackets));
                $trimmedVariableName = trim($variableName);
                if ($trimmedVariableName && Common::mb_substr($trimmedVariableName, 0, 1) !== '{') {
                    // case when using {{{foobar}}
                    $var = $this->variableToArray($container, $trimmedVariableName);
                    if ($var) {
                        $multiVars[] = $var;
                    } else {
                        // the variable does not exist, therefore we simply add the text again
                        $multiVars[] = '{{' . $variableName . '}}';
                    }
                } else {
                    // the variable does not exist, therefore we simply add the text again
                    $multiVars[] = '{{' . $variableName . '}}';
                }
                $pos = $end + $ignoreLengthOpeningBrackets;
            } while ($end !== \false);
            $allStrings = \true;
            foreach ($multiVars as $var) {
                if (!is_string($var)) {
                    $allStrings = \false;
                }
            }
            if ($allStrings) {
                // no variables detected... for simplicity we return one single string
                return implode('', $multiVars);
            }
            if (count($multiVars) === 1) {
                return array_shift($multiVars);
            } else {
                return array('joinedVariable' => $multiVars);
            }
        }
        // just a regular text value but does not contain a variable
        return $value;
    }
    private function substr($str, $start, $length = null)
    {
        if (function_exists('mb_substr')) {
            return mb_substr($str, $start, $length, 'UTF-8');
        }
        return substr($str, $start, $length);
    }
    protected function variableToArray($container, $variableNameOrVariable)
    {
        if (is_array($variableNameOrVariable)) {
            $variable = $variableNameOrVariable;
        } elseif (isset($this->variables[$variableNameOrVariable])) {
            return $this->variables[$variableNameOrVariable];
        } else {
            $variable = $this->variableModel->findVariableByName($container['idsite'], $container['idcontainerversion'], $variableNameOrVariable);
        }
        if ($variable) {
            $lookUpTable = [];
            if (!empty($variable['lookup_table']) && is_array($variable['lookup_table'])) {
                foreach ($variable['lookup_table'] as $lookup) {
                    $lookUpTable[] = ['matchValue' => $lookup['match_value'], 'outValue' => $lookup['out_value'], 'comparison' => $lookup['comparison']];
                }
            }
            $var = ['name' => $variable['name'], 'type' => $variable['type'], 'lookUpTable' => $lookUpTable, 'defaultValue' => $variable['default_value'], 'parameters' => $this->parametersToVariableJs($container, $variable)];
            // by setting var name key we make sure to not include same var twice
            $this->variables[$var['name']] = $var;
            return $var;
        } else {
            // try to find pre-configured variable if no user variable found
            $variable = $this->variablesProvider->getPreConfiguredVariable($variableNameOrVariable);
            if ($variable) {
                $defaultParams = [];
                foreach ($variable->getParameters() as $parameter) {
                    $defaultParams[$parameter->getName()] = $parameter->getValue();
                }
                $var = ['name' => ucfirst($variable->getId()), 'type' => $variable->getId(), 'lookUpTable' => [], 'defaultValue' => null, 'parameters' => $this->parametersToVariableJs($container, array('parameters' => $defaultParams, 'typeMetadata' => array()))];
                $this->variables[$var['name']] = $var;
                return $var;
            }
        }
    }
    public function getOrder()
    {
        return 99;
    }
    public function toArray()
    {
        return array('id' => $this->getId(), 'name' => $this->getName());
    }
    public function getJsTargetPath($idSite, $idContainer, $environment, $containerCreatedDate)
    {
        $idSite = (int) $idSite;
        $path = StaticContainer::get('TagManagerContainerStorageDir') . '/' . StaticContainer::get('TagManagerContainerFilesPrefix') . $idContainer;
        if ($environment === Environment::ENVIRONMENT_PREVIEW) {
            // we do not add a hash here with the salt as the preview may be public, and if this was public, they could
            // calculate the salt from the hash which would then allow to calculate other hashes
            $path .= '_' . $environment;
        } elseif ($environment !== Environment::ENVIRONMENT_LIVE) {
            // we need to add a random ID behind it, otherwise people would be able to guess the path to dev or staging
            // environment and see in advance what might be rolled out soon, what is being tested, etc. We make sure to
            // have two/three random factors in here not only the salt to reduce chances of being able to calculate the salt
            $path .= '_' . $environment . '_' . substr(sha1($idContainer . $idSite . $containerCreatedDate . $environment . $this->salt->getSalt()), 0, 24);
        }
        return $path;
    }
    public static function removeAllFilesOfAllContainers()
    {
        $files = self::findFiles(PIWIK_DOCUMENT_ROOT . StaticContainer::get('TagManagerContainerStorageDir'), StaticContainer::get('TagManagerContainerFilesPrefix') . '*.js');
        if (!empty($files)) {
            foreach ($files as $file) {
                self::deleteFile($file);
            }
        }
        return count($files);
    }
    public static function removeAllContainerFiles($idContainer)
    {
        if (empty($idContainer) || strlen($idContainer) <= 5) {
            return;
            // prevent accidental deletion of multiple container files
        }
        $files = self::findFiles(PIWIK_DOCUMENT_ROOT . StaticContainer::get('TagManagerContainerStorageDir'), sprintf('%s%s*.js', StaticContainer::get('TagManagerContainerFilesPrefix'), $idContainer));
        if (!empty($files)) {
            foreach ($files as $file) {
                self::deleteFile($file);
            }
        }
        return count($files);
    }
    private static function deleteFile($file)
    {
        $storage = StaticContainer::get('Piwik\\Plugins\\TagManager\\Context\\Storage\\StorageInterface');
        $storage->delete($file);
    }
    private static function findFiles($sdir, $spattern)
    {
        $storage = StaticContainer::get('Piwik\\Plugins\\TagManager\\Context\\Storage\\StorageInterface');
        return $storage->find($sdir, $spattern);
    }
    public static function removeNoLongerExistingEnvironments($availableEnvironments)
    {
        if (!is_array($availableEnvironments)) {
            return array();
        }
        $availableEnvironments[] = Environment::ENVIRONMENT_LIVE;
        // we make sure they are set as we never want to remove them
        $availableEnvironments[] = Environment::ENVIRONMENT_PREVIEW;
        $basePath = PIWIK_DOCUMENT_ROOT . StaticContainer::get('TagManagerContainerStorageDir');
        $files = self::findFiles($basePath, StaticContainer::get('TagManagerContainerFilesPrefix') . '*.js');
        $environmentsDeleted = array();
        if (!empty($files)) {
            foreach ($files as $file) {
                $filename = str_replace($basePath . '/' . StaticContainer::get('TagManagerContainerFilesPrefix'), '', $file);
                $filename = str_replace('.js', '', $filename);
                $filename = explode('_', $filename);
                if (count($filename) === 3) {
                    // 0 = container
                    // 1 = environment
                    // 2 = hash
                    // we ignore preview environment and live environment already by design but also define it specifically
                    $env = $filename[1];
                    try {
                        Environment::checkEnvironmentNameFormat($env);
                    } catch (\Exception $e) {
                        // for some reason not a valid environment... we make sure to not delete anything weird
                        continue;
                    }
                    if (!in_array($env, $availableEnvironments)) {
                        $environmentsDeleted[] = $env;
                        self::deleteFile($file);
                    }
                }
            }
        }
        return $environmentsDeleted;
    }
}
