<?php

/**
 * Smarty Internal Plugin Smarty Template Compiler Base
 *
 * This file contains the basic classes and methodes for compiling Smarty templates with lexer/parser
 *
 * @package Smarty
 * @subpackage Compiler
 * @author Uwe Tews
 */

/**
 * Main abstract compiler class
 *
 * @package Smarty
 * @subpackage Compiler
 */
abstract class Smarty_Internal_TemplateCompilerBase
{
    /**
     * hash for nocache sections
     *
     * @var mixed
     */
    private $nocache_hash = null;

    /**
     * suppress generation of nocache code
     *
     * @var bool
     */
    public $suppressNocacheProcessing = false;

    /**
     * suppress generation of merged template code
     *
     * @var bool
     */
    public $suppressMergedTemplates = false;

    /**
     * compile tag objects
     *
     * @var array
     */
    public static $_tag_objects = array();

    /**
     * tag stack
     *
     * @var array
     */
    public $_tag_stack = array();

    /**
     * current template
     *
     * @var Smarty_Internal_Template
     */
    public $template = null;

    /**
     * merged templates
     *
     * @var array
     */
    public $merged_templates = array();

    /**
     * sources which must be compiled
     *
     * @var array
     */
    public $sources = array();

    /**
     * flag that we are inside {block}
     *
     * @var bool
     */
    public $inheritance = false;

    /**
     * flag when compiling inheritance child template
     *
     * @var bool
     */
    public $inheritance_child = false;

    /**
     * uid of templates called by {extends} for recursion check
     *
     * @var array
     */
    public $extends_uid = array();

    /**
     * source line offset for error messages
     *
     * @var int
     */
    public $trace_line_offset = 0;

    /**
     * trace uid
     *
     * @var string
     */
    public $trace_uid = '';

    /**
     * trace file path
     *
     * @var string
     */
    public $trace_filepath = '';
    /**
     * stack for tracing file and line of nested {block} tags
     *
     * @var array
     */
    public $trace_stack = array();

    /**
     * plugins loaded by default plugin handler
     *
     * @var array
     */
    public $default_handler_plugins = array();

    /**
     * saved preprocessed modifier list
     *
     * @var mixed
     */
    public $default_modifier_list = null;

    /**
     * force compilation of complete template as nocache
     * @var boolean
     */
    public $forceNocache = false;

    /**
     * suppress Smarty header code in compiled template
     * @var bool
     */
    public $suppressHeader = false;

    /**
     * suppress template property header code in compiled template
     * @var bool
     */
    public $suppressTemplatePropertyHeader = false;

    /**
     * suppress pre and post filter
     * @var bool
     */
    public $suppressFilter = false;

    /**
     * flag if compiled template file shall we written
     * @var bool
     */
    public $write_compiled_code = true;

    /**
     * flag if currently a template function is compiled
     * @var bool
     */
    public $compiles_template_function = false;

    /**
     * called subfuntions from template function
     * @var array
     */
    public $called_functions = array();

    /**
     * flags for used modifier plugins
     * @var array
     */
    public $modifier_plugins = array();

    /**
     * type of already compiled modifier
     * @var array
     */
    public $known_modifier_type = array();

    /**
     * Methode to compile a Smarty template
     *
     * @param  mixed $_content template source
     * @return bool  true if compiling succeeded, false if it failed
     */
    abstract protected function doCompile($_content);

    /**
     * Initialize compiler
     */
    public function __construct()
    {
        $this->nocache_hash = str_replace(array('.',','), '-', uniqid(rand(), true));
    }

    /**
     * Method to compile a Smarty template
     *
     * @param  Smarty_Internal_Template $template template object to compile
     * @param  bool $nocache    true is shall be compiled in nocache mode
     * @return bool             true if compiling succeeded, false if it failed
     */
    public function compileTemplate(Smarty_Internal_Template $template, $nocache = false)
    {
        if (empty($template->properties['nocache_hash'])) {
            $template->properties['nocache_hash'] = $this->nocache_hash;
        } else {
            $this->nocache_hash = $template->properties['nocache_hash'];
        }
        // flag for nochache sections
        $this->nocache = $nocache;
        $this->tag_nocache = false;
        // save template object in compiler class
        $this->template = $template;
        // reset has nocache code flag
        $this->template->has_nocache_code = false;
        $save_source = $this->template->source;
        // template header code
        $template_header = '';
        if (!$this->suppressHeader) {
            $template_header .= "<?php /* Smarty version " . Smarty::SMARTY_VERSION . ", created on " . strftime("%Y-%m-%d %H:%M:%S") . "\n";
            $template_header .= "         compiled from \"" . $this->template->source->filepath . "\" */ ?>\n";
        }

        if (empty($this->template->source->components)) {
            $this->sources = array($template->source);
        } else {
            // we have array of inheritance templates by extends: resource
            $this->sources = array_reverse($template->source->components);
        }
        $loop = 0;
        // the $this->sources array can get additional elements while compiling by the {extends} tag
        while ($this->template->source = array_shift($this->sources)) {
            $this->smarty->_current_file = $this->template->source->filepath;
            if ($this->smarty->debugging) {
                Smarty_Internal_Debug::start_compile($this->template);
            }
            $no_sources = count($this->sources);
            if ($loop || $no_sources) {
                $this->template->properties['file_dependency'][$this->template->source->uid] = array($this->template->source->filepath, $this->template->source->timestamp, $this->template->source->type);
            }
            $loop++;
            if ($no_sources) {
                $this->inheritance_child = true;
            } else {
                $this->inheritance_child = false;
            }
            do {
                $_compiled_code = '';
                // flag for aborting current and start recompile
                $this->abort_and_recompile = false;
                // get template source
                $_content = $this->template->source->content;
                if ($_content != '') {
                    // run prefilter if required
                    if ((isset($this->smarty->autoload_filters['pre']) || isset($this->smarty->registered_filters['pre'])) && !$this->suppressFilter) {
                        $_content = Smarty_Internal_Filter_Handler::runFilter('pre', $_content, $template);
                    }
                    // call compiler
                    $_compiled_code = $this->doCompile($_content);
                }
            } while ($this->abort_and_recompile);
            if ($this->smarty->debugging) {
                Smarty_Internal_Debug::end_compile($this->template);
            }
        }
        // restore source
        $this->template->source = $save_source;
        unset($save_source);
        $this->smarty->_current_file = $this->template->source->filepath;
        // free memory
        unset($this->parser->root_buffer, $this->parser->current_buffer, $this->parser, $this->lex, $this->template);
        self::$_tag_objects = array();
        // return compiled code to template object
        $merged_code = '';
        if (!$this->suppressMergedTemplates && !empty($this->merged_templates)) {
            foreach ($this->merged_templates as $code) {
                $merged_code .= $code;
            }
        }
        // run postfilter if required on compiled template code
        if ((isset($this->smarty->autoload_filters['post']) || isset($this->smarty->registered_filters['post'])) && !$this->suppressFilter && $_compiled_code != '') {
            $_compiled_code = Smarty_Internal_Filter_Handler::runFilter('post', $_compiled_code, $template);
        }
        if ($this->suppressTemplatePropertyHeader) {
            $code = $_compiled_code . $merged_code;
        } else {
            $code = $template_header . $template->createTemplateCodeFrame($_compiled_code) . $merged_code;
        }
        // unset content because template inheritance could have replace source with parent code
        unset ($template->source->content);

        return $code;
    }

    /**
     * Compile Tag
     *
     * This is a call back from the lexer/parser
     * It executes the required compile plugin for the Smarty tag
     *
     * @param  string $tag       tag name
     * @param  array $args      array with tag attributes
     * @param  array $parameter array with compilation parameter
     * @return string compiled code
     */
    public function compileTag($tag, $args, $parameter = array())
    {
        // $args contains the attributes parsed and compiled by the lexer/parser
        // assume that tag does compile into code, but creates no HTML output
        $this->has_code = true;
        $this->has_output = false;
        // log tag/attributes
        if (isset($this->smarty->get_used_tags) && $this->smarty->get_used_tags) {
            $this->template->used_tags[] = array($tag, $args);
        }
        // check nocache option flag
        if (in_array("'nocache'", $args) || in_array(array('nocache' => 'true'), $args)
            || in_array(array('nocache' => '"true"'), $args) || in_array(array('nocache' => "'true'"), $args)
        ) {
            $this->tag_nocache = true;
        }
        // compile the smarty tag (required compile classes to compile the tag are autoloaded)
        if (($_output = $this->callTagCompiler($tag, $args, $parameter)) === false) {
            if (isset($this->smarty->template_functions[$tag])) {
                // template defined by {template} tag
                $args['_attr']['name'] = "'" . $tag . "'";
                $_output = $this->callTagCompiler('call', $args, $parameter);
            }
        }
        if ($_output !== false) {
            if ($_output !== true) {
                // did we get compiled code
                if ($this->has_code) {
                    // Does it create output?
                    if ($this->has_output) {
                        $_output .= "\n";
                    }
                    // return compiled code
                    return $_output;
                }
            }
            // tag did not produce compiled code
            return null;
        } else {
            // map_named attributes
            if (isset($args['_attr'])) {
                foreach ($args['_attr'] as $key => $attribute) {
                    if (is_array($attribute)) {
                        $args = array_merge($args, $attribute);
                    }
                }
            }
            // not an internal compiler tag
            if (strlen($tag) < 6 || substr($tag, -5) != 'close') {
                // check if tag is a registered object
                if (isset($this->smarty->registered_objects[$tag]) && isset($parameter['object_methode'])) {
                    $methode = $parameter['object_methode'];
                    if (!in_array($methode, $this->smarty->registered_objects[$tag][3]) &&
                        (empty($this->smarty->registered_objects[$tag][1]) || in_array($methode, $this->smarty->registered_objects[$tag][1]))
                    ) {
                        return $this->callTagCompiler('private_object_function', $args, $parameter, $tag, $methode);
                    } elseif (in_array($methode, $this->smarty->registered_objects[$tag][3])) {
                        return $this->callTagCompiler('private_object_block_function', $args, $parameter, $tag, $methode);
                    } else {
                        return $this->trigger_template_error('unallowed methode "' . $methode . '" in registered object "' . $tag . '"', $this->lex->taglineno);
                    }
                }
                // check if tag is registered
                foreach (array(Smarty::PLUGIN_COMPILER, Smarty::PLUGIN_FUNCTION, Smarty::PLUGIN_BLOCK) as $plugin_type) {
                    if (isset($this->smarty->registered_plugins[$plugin_type][$tag])) {
                        // if compiler function plugin call it now
                        if ($plugin_type == Smarty::PLUGIN_COMPILER) {
                            $new_args = array();
                            foreach ($args as $key => $mixed) {
                                if (is_array($mixed)) {
                                    $new_args = array_merge($new_args, $mixed);
                                } else {
                                    $new_args[$key] = $mixed;
                                }
                            }
                            if (!$this->smarty->registered_plugins[$plugin_type][$tag][1]) {
                                $this->tag_nocache = true;
                            }
                            $function = $this->smarty->registered_plugins[$plugin_type][$tag][0];
                            if (!is_array($function)) {
                                return $function($new_args, $this);
                            } elseif (is_object($function[0])) {
                                return $this->smarty->registered_plugins[$plugin_type][$tag][0][0]->$function[1]($new_args, $this);
                            } else {
                                return call_user_func_array($function, array($new_args, $this));
                            }
                        }
                        // compile registered function or block function
                        if ($plugin_type == Smarty::PLUGIN_FUNCTION || $plugin_type == Smarty::PLUGIN_BLOCK) {
                            return $this->callTagCompiler('private_registered_' . $plugin_type, $args, $parameter, $tag);
                        }
                    }
                }
                // check plugins from plugins folder
                foreach ($this->smarty->plugin_search_order as $plugin_type) {
                    if ($plugin_type == Smarty::PLUGIN_COMPILER && $this->smarty->loadPlugin('smarty_compiler_' . $tag) && (!isset($this->smarty->security_policy) || $this->smarty->security_policy->isTrustedTag($tag, $this))) {
                        $plugin = 'smarty_compiler_' . $tag;
                        if (is_callable($plugin)) {
                            // convert arguments format for old compiler plugins
                            $new_args = array();
                            foreach ($args as $key => $mixed) {
                                if (is_array($mixed)) {
                                    $new_args = array_merge($new_args, $mixed);
                                } else {
                                    $new_args[$key] = $mixed;
                                }
                            }

                            return $plugin($new_args, $this->smarty);
                        }
                        if (class_exists($plugin, false)) {
                            $plugin_object = new $plugin;
                            if (method_exists($plugin_object, 'compile')) {
                                return $plugin_object->compile($args, $this);
                            }
                        }
                        throw new SmartyException("Plugin \"{$tag}\" not callable");
                    } else {
                        if ($function = $this->getPlugin($tag, $plugin_type)) {
                            if (!isset($this->smarty->security_policy) || $this->smarty->security_policy->isTrustedTag($tag, $this)) {
                                return $this->callTagCompiler('private_' . $plugin_type . '_plugin', $args, $parameter, $tag, $function);
                            }
                        }
                    }
                }
                if (is_callable($this->smarty->default_plugin_handler_func)) {
                    $found = false;
                    // look for already resolved tags
                    foreach ($this->smarty->plugin_search_order as $plugin_type) {
                        if (isset($this->default_handler_plugins[$plugin_type][$tag])) {
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        // call default handler
                        foreach ($this->smarty->plugin_search_order as $plugin_type) {
                            if ($this->getPluginFromDefaultHandler($tag, $plugin_type)) {
                                $found = true;
                                break;
                            }
                        }
                    }
                    if ($found) {
                        // if compiler function plugin call it now
                        if ($plugin_type == Smarty::PLUGIN_COMPILER) {
                            $new_args = array();
                            foreach ($args as $mixed) {
                                $new_args = array_merge($new_args, $mixed);
                            }
                            $function = $this->default_handler_plugins[$plugin_type][$tag][0];
                            if (!is_array($function)) {
                                return $function($new_args, $this);
                            } elseif (is_object($function[0])) {
                                return $this->default_handler_plugins[$plugin_type][$tag][0][0]->$function[1]($new_args, $this);
                            } else {
                                return call_user_func_array($function, array($new_args, $this));
                            }
                        } else {
                            return $this->callTagCompiler('private_registered_' . $plugin_type, $args, $parameter, $tag);
                        }
                    }
                }
            } else {
                // compile closing tag of block function
                $base_tag = substr($tag, 0, -5);
                // check if closing tag is a registered object
                if (isset($this->smarty->registered_objects[$base_tag]) && isset($parameter['object_methode'])) {
                    $methode = $parameter['object_methode'];
                    if (in_array($methode, $this->smarty->registered_objects[$base_tag][3])) {
                        return $this->callTagCompiler('private_object_block_function', $args, $parameter, $tag, $methode);
                    } else {
                        return $this->trigger_template_error('unallowed closing tag methode "' . $methode . '" in registered object "' . $base_tag . '"', $this->lex->taglineno);
                    }
                }
                // registered block tag ?
                if (isset($this->smarty->registered_plugins[Smarty::PLUGIN_BLOCK][$base_tag]) || isset($this->default_handler_plugins[Smarty::PLUGIN_BLOCK][$base_tag])) {
                    return $this->callTagCompiler('private_registered_block', $args, $parameter, $tag);
                }
                // block plugin?
                if ($function = $this->getPlugin($base_tag, Smarty::PLUGIN_BLOCK)) {
                    return $this->callTagCompiler('private_block_plugin', $args, $parameter, $tag, $function);
                }
                // registered compiler plugin ?
                if (isset($this->smarty->registered_plugins[Smarty::PLUGIN_COMPILER][$tag])) {
                    // if compiler function plugin call it now
                    $args = array();
                    if (!$this->smarty->registered_plugins[Smarty::PLUGIN_COMPILER][$tag][1]) {
                        $this->tag_nocache = true;
                    }
                    $function = $this->smarty->registered_plugins[Smarty::PLUGIN_COMPILER][$tag][0];
                    if (!is_array($function)) {
                        return $function($args, $this);
                    } elseif (is_object($function[0])) {
                        return $this->smarty->registered_plugins[Smarty::PLUGIN_COMPILER][$tag][0][0]->$function[1]($args, $this);
                    } else {
                        return call_user_func_array($function, array($args, $this));
                    }
                }
                if ($this->smarty->loadPlugin('smarty_compiler_' . $tag)) {
                    $plugin = 'smarty_compiler_' . $tag;
                    if (is_callable($plugin)) {
                        return $plugin($args, $this->smarty);
                    }
                    if (class_exists($plugin, false)) {
                        $plugin_object = new $plugin;
                        if (method_exists($plugin_object, 'compile')) {
                            return $plugin_object->compile($args, $this);
                        }
                    }
                    throw new SmartyException("Plugin \"{$tag}\" not callable");
                }
            }
            $this->trigger_template_error("unknown tag \"" . $tag . "\"", $this->lex->taglineno);
        }
    }

    /**
     * lazy loads internal compile plugin for tag and calls the compile methode
     *
     * compile objects cached for reuse.
     * class name format:  Smarty_Internal_Compile_TagName
     * plugin filename format: Smarty_Internal_Tagname.php
     *
     * @param  string $tag    tag name
     * @param  array $args   list of tag attributes
     * @param  mixed $param1 optional parameter
     * @param  mixed $param2 optional parameter
     * @param  mixed $param3 optional parameter
     * @return string compiled code
     */
    public function callTagCompiler($tag, $args, $param1 = null, $param2 = null, $param3 = null)
    {
        // re-use object if already exists
        if (isset(self::$_tag_objects[$tag])) {
            // compile this tag
            return self::$_tag_objects[$tag]->compile($args, $this, $param1, $param2, $param3);
        }
        // lazy load internal compiler plugin
        $class_name = 'Smarty_Internal_Compile_' . $tag;
        if ($this->smarty->loadPlugin($class_name)) {
            // check if tag allowed by security
            if (!isset($this->smarty->security_policy) || $this->smarty->security_policy->isTrustedTag($tag, $this)) {
                // use plugin if found
                self::$_tag_objects[$tag] = new $class_name;
                // compile this tag
                return self::$_tag_objects[$tag]->compile($args, $this, $param1, $param2, $param3);
            }
        }
        // no internal compile plugin for this tag
        return false;
    }

    /**
     * Check for plugins and return function name
     *
     * @param  string $pugin_name  name of plugin or function
     * @param  string $plugin_type type of plugin
     * @return string call name of function
     */
    public function getPlugin($plugin_name, $plugin_type)
    {
        $function = null;
        if ($this->template->caching && ($this->nocache || $this->tag_nocache)) {
            if (isset($this->template->required_plugins['nocache'][$plugin_name][$plugin_type])) {
                $function = $this->template->required_plugins['nocache'][$plugin_name][$plugin_type]['function'];
            } elseif (isset($this->template->required_plugins['compiled'][$plugin_name][$plugin_type])) {
                $this->template->required_plugins['nocache'][$plugin_name][$plugin_type] = $this->template->required_plugins['compiled'][$plugin_name][$plugin_type];
                $function = $this->template->required_plugins['nocache'][$plugin_name][$plugin_type]['function'];
            }
        } else {
            if (isset($this->template->required_plugins['compiled'][$plugin_name][$plugin_type])) {
                $function = $this->template->required_plugins['compiled'][$plugin_name][$plugin_type]['function'];
            } elseif (isset($this->template->required_plugins['nocache'][$plugin_name][$plugin_type])) {
                $this->template->required_plugins['compiled'][$plugin_name][$plugin_type] = $this->template->required_plugins['nocache'][$plugin_name][$plugin_type];
                $function = $this->template->required_plugins['compiled'][$plugin_name][$plugin_type]['function'];
            }
        }
        if (isset($function)) {
            if ($plugin_type == 'modifier') {
                $this->modifier_plugins[$plugin_name] = true;
            }

            return $function;
        }
        // loop through plugin dirs and find the plugin
        $function = 'smarty_' . $plugin_type . '_' . $plugin_name;
        $file = $this->smarty->loadPlugin($function, false);

        if (is_string($file)) {
            if ($this->template->caching && ($this->nocache || $this->tag_nocache)) {
                $this->template->required_plugins['nocache'][$plugin_name][$plugin_type]['file'] = $file;
                $this->template->required_plugins['nocache'][$plugin_name][$plugin_type]['function'] = $function;
            } else {
                $this->template->required_plugins['compiled'][$plugin_name][$plugin_type]['file'] = $file;
                $this->template->required_plugins['compiled'][$plugin_name][$plugin_type]['function'] = $function;
            }
            if ($plugin_type == 'modifier') {
                $this->modifier_plugins[$plugin_name] = true;
            }

            return $function;
        }
        if (is_callable($function)) {
            // plugin function is defined in the script
            return $function;
        }

        return false;
    }

    /**
     * Check for plugins by default plugin handler
     *
     * @param  string $tag         name of tag
     * @param  string $plugin_type type of plugin
     * @return boolean true if found
     */
    public function getPluginFromDefaultHandler($tag, $plugin_type)
    {
        $callback = null;
        $script = null;
        $cacheable = true;
        $result = call_user_func_array(
            $this->smarty->default_plugin_handler_func, array($tag, $plugin_type, $this->template, &$callback, &$script, &$cacheable)
        );
        if ($result) {
            $this->tag_nocache = $this->tag_nocache || !$cacheable;
            if ($script !== null) {
                if (is_file($script)) {
                    if ($this->template->caching && ($this->nocache || $this->tag_nocache)) {
                        $this->template->required_plugins['nocache'][$tag][$plugin_type]['file'] = $script;
                        $this->template->required_plugins['nocache'][$tag][$plugin_type]['function'] = $callback;
                    } else {
                        $this->template->required_plugins['compiled'][$tag][$plugin_type]['file'] = $script;
                        $this->template->required_plugins['compiled'][$tag][$plugin_type]['function'] = $callback;
                    }
                    include_once $script;
                } else {
                    $this->trigger_template_error("Default plugin handler: Returned script file \"{$script}\" for \"{$tag}\" not found");
                }
            }
            if (!is_string($callback) && !(is_array($callback) && is_string($callback[0]) && is_string($callback[1]))) {
                $this->trigger_template_error("Default plugin handler: Returned callback for \"{$tag}\" must be a static function name or array of class and function name");
            }
            if (is_callable($callback)) {
                $this->default_handler_plugins[$plugin_type][$tag] = array($callback, true, array());

                return true;
            } else {
                $this->trigger_template_error("Default plugin handler: Returned callback for \"{$tag}\" not callable");
            }
        }

        return false;
    }

    /**
     * Inject inline code for nocache template sections
     *
     * This method gets the content of each template element from the parser.
     * If the content is compiled code and it should be not cached the code is injected
     * into the rendered output.
     *
     * @param  string $content content of template element
     * @param  boolean $is_code true if content is compiled code
     * @return string  content
     */
    public function processNocacheCode($content, $is_code)
    {
        // If the template is not evaluated and we have a nocache section and or a nocache tag
        if ($is_code && !empty($content)) {
            // generate replacement code
            if ((!($this->template->source->recompiled) || $this->forceNocache) && $this->template->caching && !$this->suppressNocacheProcessing &&
                ($this->nocache || $this->tag_nocache)
            ) {
                $this->template->has_nocache_code = true;
                $_output = addcslashes($content, '\'\\');
                $_output = str_replace("^#^", "'", $_output);
                $_output = "<?php echo '/*%%SmartyNocache:{$this->nocache_hash}%%*/" . $_output . "/*/%%SmartyNocache:{$this->nocache_hash}%%*/';?>\n";
                // make sure we include modifier plugins for nocache code
                foreach ($this->modifier_plugins as $plugin_name => $dummy) {
                    if (isset($this->template->required_plugins['compiled'][$plugin_name]['modifier'])) {
                        $this->template->required_plugins['nocache'][$plugin_name]['modifier'] = $this->template->required_plugins['compiled'][$plugin_name]['modifier'];
                    }
                }
            } else {
                $_output = $content;
            }
        } else {
            $_output = $content;
        }
        $this->modifier_plugins = array();
        $this->suppressNocacheProcessing = false;
        $this->tag_nocache = false;

        return $_output;
    }

    /**
     *  push current file and line offset on stack for tracing {block} source lines
     *
     * @param string $file new filename
     * @param string $uid uid of file
     * @param string $debug false debug end_compile shall not be called
     * @param int $line line offset to source
     */
    public function pushTrace($file, $uid, $line, $debug = true)
    {
        if ($this->smarty->debugging && $debug) {
            Smarty_Internal_Debug::end_compile($this->template);
        }
        array_push($this->trace_stack, array($this->smarty->_current_file, $this->trace_filepath, $this->trace_uid, $this->trace_line_offset));
        $this->trace_filepath = $this->smarty->_current_file = $file;
        $this->trace_uid = $uid;
        $this->trace_line_offset = $line ;
        if ($this->smarty->debugging) {
            Smarty_Internal_Debug::start_compile($this->template);
        }
    }

    /**
     *  restore file and line offset
     *
     */
    public function popTrace()
    {
        if ($this->smarty->debugging) {
            Smarty_Internal_Debug::end_compile($this->template);
        }
        $r = array_pop($this->trace_stack);
        $this->smarty->_current_file = $r[0];
        $this->trace_filepath = $r[1];
        $this->trace_uid = $r[2];
        $this->trace_line_offset = $r[3];
        if ($this->smarty->debugging) {
            Smarty_Internal_Debug::start_compile($this->template);
        }
    }

    /**
     * display compiler error messages without dying
     *
     * If parameter $args is empty it is a parser detected syntax error.
     * In this case the parser is called to obtain information about expected tokens.
     *
     * If parameter $args contains a string this is used as error message
     *
     * @param  string $args individual error message or null
     * @param  string $line line-number
     * @throws SmartyCompilerException when an unexpected token is found
     */
    public function trigger_template_error($args = null, $line = null)
    {
        // get template source line which has error
        if (!isset($line)) {
            $line = $this->lex->line;
        }
//        $line += $this->trace_line_offset;
        $match = preg_split("/\n/", $this->lex->data);
        $error_text = 'Syntax error in template "' . (empty($this->trace_filepath) ? $this->template->source->filepath : $this->trace_filepath) . '"  on line ' . ($line + $this->trace_line_offset)  . ' "' . trim(preg_replace('![\t\r\n]+!', ' ', $match[$line - 1])) . '" ';
        if (isset($args)) {
            // individual error message
            $error_text .= $args;
        } else {
            // expected token from parser
            $error_text .= ' - Unexpected "' . $this->lex->value . '"';
            if (count($this->parser->yy_get_expected_tokens($this->parser->yymajor)) <= 4) {
                foreach ($this->parser->yy_get_expected_tokens($this->parser->yymajor) as $token) {
                    $exp_token = $this->parser->yyTokenName[$token];
                    if (isset($this->lex->smarty_token_names[$exp_token])) {
                        // token type from lexer
                        $expect[] = '"' . $this->lex->smarty_token_names[$exp_token] . '"';
                    } else {
                        // otherwise internal token name
                        $expect[] = $this->parser->yyTokenName[$token];
                    }
                }
                $error_text .= ', expected one of: ' . implode(' , ', $expect);
            }
        }
        $e = new SmartyCompilerException($error_text);
        $e->line = $line;
        $e->source = trim(preg_replace('![\t\r\n]+!', ' ', $match[$line - 1]));
        $e->desc = $args;
        $e->template = $this->template->source->filepath;
        throw $e;
    }

}
