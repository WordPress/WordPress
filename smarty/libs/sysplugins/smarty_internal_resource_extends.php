<?php
/**
 * Smarty Internal Plugin Resource Extends
 *
 * @package Smarty
 * @subpackage TemplateResources
 * @author Uwe Tews
 * @author Rodney Rehm
 */

/**
 * Smarty Internal Plugin Resource Extends
 *
 * Implements the file system as resource for Smarty which {extend}s a chain of template files templates
 *
 * @package Smarty
 * @subpackage TemplateResources
 */
class Smarty_Internal_Resource_Extends extends Smarty_Resource
{
    /**
     * mbstring.overload flag
     *
     * @var int
     */
    public $mbstring_overload = 0;

    /**
     * populate Source Object with meta data from Resource
     *
     * @param Smarty_Template_Source $source    source object
     * @param Smarty_Internal_Template $_template template object
     */
    public function populate(Smarty_Template_Source $source, Smarty_Internal_Template $_template = null)
    {
        $uid = '';
        $sources = array();
        $components = explode('|', $source->name);
        $exists = true;
        foreach ($components as $component) {
            $s = Smarty_Resource::source(null, $source->smarty, $component);
            if ($s->type == 'php') {
                throw new SmartyException("Resource type {$s->type} cannot be used with the extends resource type");
            }
            $sources[$s->uid] = $s;
            $uid .= $s->filepath;
            if ($_template && $_template->smarty->compile_check) {
                $exists = $exists && $s->exists;
            }
        }
        $source->components = $sources;
        $source->filepath = $s->filepath;
        $source->uid = sha1($uid);
        if ($_template && $_template->smarty->compile_check) {
            $source->timestamp = $s->timestamp;
            $source->exists = $exists;
        }
        // need the template at getContent()
        $source->template = $_template;
    }

    /**
     * populate Source Object with timestamp and exists from Resource
     *
     * @param Smarty_Template_Source $source source object
     */
    public function populateTimestamp(Smarty_Template_Source $source)
    {
        $source->exists = true;
        foreach ($source->components as $s) {
            $source->exists = $source->exists && $s->exists;
        }
        $source->timestamp = $s->timestamp;
    }

    /**
     * Load template's source from files into current template object
     *
     * @param Smarty_Template_Source $source source object
     * @return string template source
     * @throws SmartyException if source cannot be loaded
     */
    public function getContent(Smarty_Template_Source $source)
    {
        if (!$source->exists) {
            throw new SmartyException("Unable to read template {$source->type} '{$source->name}'");
        }

        $_components = array_reverse($source->components);

        $_content = '';
        foreach ($_components as $_component) {
            // read content
            $_content .= $_component->content;
        }
        return $_content;
    }

    /**
     * Determine basename for compiled filename
     *
     * @param Smarty_Template_Source $source source object
     * @return string resource's basename
     */
    public function getBasename(Smarty_Template_Source $source)
    {
        return str_replace(':', '.', basename($source->filepath));
    }

}
