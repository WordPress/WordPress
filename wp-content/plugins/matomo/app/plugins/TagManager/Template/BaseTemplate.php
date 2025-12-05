<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Template;

use JShrink\Minifier;
use Piwik\Common;
use Piwik\Container\StaticContainer;
use Piwik\Development;
use Piwik\Piwik;
use Piwik\Plugins\CorePluginsAdmin\SettingsMetadata;
use Piwik\Plugins\TagManager\Context\WebContext;
use Piwik\Plugins\TagManager\Settings\Storage\Backend\TransientBackend;
use Piwik\Settings\Setting;
use Piwik\Settings\Storage\Storage;
/**
 * @api
 */
abstract class BaseTemplate
{
    private $pluginName = null;
    protected $templateType = '';
    public const FIELD_TEXTAREA_VARIABLE_COMPONENT = ['plugin' => 'TagManager', 'name' => 'FieldTextareaVariable'];
    public const FIELD_VARIABLE_COMPONENT = ['plugin' => 'TagManager', 'name' => 'FieldVariableTemplate'];
    public const FIELD_VARIABLE_TYPE_COMPONENT = ['plugin' => 'TagManager', 'name' => 'FieldVariableTypeTemplate'];
    public static $RESERVED_SETTING_NAMES = ['container', 'tag', 'variable', 'trigger', 'length', 'window', 'document', 'get', 'fire', 'setUp', 'set', 'reset', 'type', 'part', 'default_value', 'lookup_table', 'conditions', 'condition', 'fire_limit', 'fire_delay', 'priority', 'parameters', 'start_date', 'end_date', 'type', 'name', 'status'];
    private $settingsStorage;
    /**
     * Get the ID of this template.
     * The ID is by default automatically generated from the class name, but can be customized by returning a string.
     *
     * @return string
     */
    public function getId()
    {
        return $this->makeIdFromClassname($this->templateType);
    }
    /**
     * Get the list of parameters that can be configured for this template.
     * @return Setting[]
     */
    public abstract function getParameters();
    /**
     * Get the category this template belongs to.
     * @return string
     */
    public abstract function getCategory();
    /**
     * Defines in which contexts this tag should be available, for example "web".
     * @return string[]
     */
    public abstract function getSupportedContexts();
    private function getTranslationKey($part)
    {
        if (empty($this->templateType)) {
            return '';
        }
        if (!isset($this->pluginName)) {
            $classname = get_class($this);
            $parts = explode('\\', $classname);
            if (count($parts) >= 4 && $parts[1] === 'Plugins') {
                $this->pluginName = $parts[2];
            }
        }
        if (isset($this->pluginName)) {
            return $this->pluginName . '_' . $this->getId() . $this->templateType . $part;
        }
        return '';
    }
    /**
     * Get the translated name of this template.
     * @return string
     */
    public function getName()
    {
        $key = $this->getTranslationKey('Name');
        if ($key) {
            $translated = Piwik::translate($key);
            if ($translated === $key) {
                return $this->getId();
            }
            return $translated;
        }
        return $this->getId();
    }
    /**
     * Get the translated description of this template.
     * @return string
     */
    public function getDescription()
    {
        $key = $this->getTranslationKey('Description');
        if ($key) {
            $translated = Piwik::translate($key);
            if ($translated === $key) {
                return '';
            }
            return $translated;
        }
    }
    /**
     * Get the translated help text for this template.
     * @return string
     */
    public function getHelp()
    {
        $key = $this->getTranslationKey('Help');
        if ($key) {
            $translated = Piwik::translate($key);
            if ($translated === $key) {
                return '';
            }
            return $translated;
        }
    }
    /**
     * Get the order for this template. The lower the order is, the higher in the list the template will be shown.
     * @return int
     */
    public function getOrder()
    {
        return 9999;
    }
    /**
     * Get the image icon url. We could also use data:uris to return the amount of requests to load a page like this:
     * return 'data:image/svg+xml;base64,' . base64_encode('<svg...</svg>');
     * However, we prefer the files since we can better define them in the legal notice.
     *
     * @return string
     */
    public function getIcon()
    {
        return 'plugins/TagManager/images/defaultIcon.svg';
    }
    /**
     * Creates a new setting / parameter.
     *
     * Settings will be displayed in the UI depending on the order of `makeSetting` calls. This means you can define
     * the order of the displayed settings by calling makeSetting first for more important settings.
     *
     * @param string $name         The name of the setting that shall be created
     * @param mixed  $defaultValue The default value for this setting. Note the value will not be converted to the
     *                             specified type.
     * @param string $type         The PHP internal type the value of this setting should have.
     *                             Use one of FieldConfig::TYPE_* constancts
     * @param \Closure $fieldConfigCallback   A callback method to configure the field that shall be displayed in the
     *                             UI to define the value for this setting
     * @return Setting   Returns an instance of the created measurable setting.
     */
    protected function makeSetting($name, $defaultValue, $type, $fieldConfigCallback)
    {
        if (in_array(strtolower($name), self::$RESERVED_SETTING_NAMES, \true)) {
            throw new \Exception(sprintf('The setting name "%s" is reserved and cannot be used', $name));
        }
        // we need to make sure to create new instance of storage all the time to prevent "leaking" using values across
        // multiple tags, or triggers, or variables
        $this->settingsStorage = new Storage(new TransientBackend($this->getId()));
        $setting = new Setting($name, $defaultValue, $type, 'TagManager');
        $setting->setStorage($this->settingsStorage);
        $setting->setConfigureCallback($fieldConfigCallback);
        $setting->setIsWritableByCurrentUser(\true);
        // we validate access on API level.
        return $setting;
    }
    /**
     * @ignore
     */
    public function loadTemplate($context, $entity)
    {
        switch ($context) {
            case WebContext::ID:
                $className = get_class($this);
                $autoloader_reflector = new \ReflectionClass($className);
                $fileName = $autoloader_reflector->getFileName();
                $lenPhpExtension = 3;
                $base = substr($fileName, 0, -1 * $lenPhpExtension);
                $file = $base . 'web.js';
                $minFile = $base . 'web.min.js';
                if (!StaticContainer::get('TagManagerJSMinificationEnabled')) {
                    return $this->loadTemplateFile($file);
                    // avoid minification in test mode
                } elseif (Development::isEnabled() && $this->hasTemplateFile($file)) {
                    // during dev mode we prefer the non-minified version for debugging purposes, but we still use
                    // the internal minifier to make sure we debug the same as a user would receive
                    $template = $this->loadTemplateFile($file);
                    $minified = Minifier::minify($template);
                    return $minified;
                } elseif ($this->hasTemplateFile($minFile)) {
                    // recommended when there is a lot of content in the template. For example if the tag contains the
                    // content of a Matomo JS tracker then it will be useful or also in general.
                    return $this->loadTemplateFile($minFile);
                } elseif ($this->hasTemplateFile($file)) {
                    // it does not minify so well as it doesn't rename variables, however, it does make it a bit smaller
                    // gzip should help with filesize re variables like `tagmanager` etc.
                    // the big advantage is really that JS Min files cannot be out of date or forgotton to be updated
                    $template = $this->loadTemplateFile($file);
                    $minified = Minifier::minify($template);
                    return $minified;
                }
        }
    }
    /**
     * @ignore
     */
    protected function makeIdFromClassname($rightTrimWord)
    {
        $className = get_class($this);
        $parts = explode('\\', $className);
        $id = end($parts);
        if ($rightTrimWord && Common::stringEndsWith($id, $rightTrimWord)) {
            $id = substr($id, 0, -strlen($rightTrimWord));
        }
        return $id;
    }
    /**
     * @ignore tests only
     * @param $file
     * @return bool
     */
    protected function hasTemplateFile($file)
    {
        return is_readable($file);
    }
    /**
     * @ignore tests only
     * @param $file
     * @return string|null
     */
    protected function loadTemplateFile($file)
    {
        if ($this->hasTemplateFile($file)) {
            return trim(file_get_contents($file));
        }
    }
    /**
     * Lets you hide the advanced settings tab in the UI.
     * @return bool
     */
    public function hasAdvancedSettings()
    {
        return \true;
    }
    /**
     * If your template allows a user to add js/html code to the site for example, you should be overwriting this
     * method and return `true`.
     * @return bool
     */
    public function isCustomTemplate()
    {
        return \false;
    }
    /**
     * @ignore
     * @return array
     */
    public function toArray()
    {
        $settingsMetadata = new SettingsMetadata();
        $params = array();
        $tagParameters = $this->getParameters();
        if (!empty($tagParameters)) {
            foreach ($tagParameters as $parameter) {
                $param = $settingsMetadata->formatSetting($parameter);
                if (!empty($param)) {
                    // we need to manually set the value as otherwise a value from an actual tag, trigger, variable,...
                    // might be set because the instance of the template is shared and therefore the storage...
                    $param['value'] = $parameter->getDefaultValue();
                    $params[] = $param;
                }
            }
        }
        return array('id' => $this->getId(), 'name' => $this->getName(), 'description' => $this->getDescription(), 'category' => Piwik::translate($this->getCategory()), 'icon' => $this->getIcon(), 'help' => $this->getHelp(), 'order' => $this->getOrder(), 'contexts' => $this->getSupportedContexts(), 'hasAdvancedSettings' => $this->hasAdvancedSettings(), 'isCustomTemplate' => $this->isCustomTemplate(), 'parameters' => $params);
    }
}
