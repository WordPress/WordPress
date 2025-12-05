<?php

namespace {
    /**
     * This file provides the part of lessphp API (https://github.com/leafo/lessphp)
     * to be a drop-in replacement for following products:
     *  - Drupal 7, by the less module v3.0+ (https://drupal.org/project/less)
     *  - Symfony 2
     */
    // Register autoloader for non-composer installations
    if (!\class_exists('Less_Parser')) {
        require_once __DIR__ . '/lib/Less/Autoloader.php';
        \Less_Autoloader::register();
    }
    class lessc
    {
        public static $VERSION = \Less_Version::less_version;
        public $importDir = '';
        protected $allParsedFiles = [];
        protected $libFunctions = [];
        protected $registeredVars = [];
        private $formatterName;
        private $options = [];
        public function __construct($lessc = null, $sourceName = null)
        {
        }
        public function setImportDir($dirs)
        {
            $this->importDir = (array) $dirs;
        }
        public function addImportDir($dir)
        {
            $this->importDir = (array) $this->importDir;
            $this->importDir[] = $dir;
        }
        public function setFormatter($name)
        {
            $this->formatterName = $name;
        }
        public function setPreserveComments($preserve)
        {
        }
        public function registerFunction($name, $func)
        {
            $this->libFunctions[$name] = $func;
        }
        public function unregisterFunction($name)
        {
            unset($this->libFunctions[$name]);
        }
        public function setVariables($variables)
        {
            foreach ($variables as $name => $value) {
                $this->setVariable($name, $value);
            }
        }
        public function setVariable($name, $value)
        {
            $this->registeredVars[$name] = $value;
        }
        public function unsetVariable($name)
        {
            unset($this->registeredVars[$name]);
        }
        public function setOptions($options)
        {
            foreach ($options as $name => $value) {
                $this->setOption($name, $value);
            }
        }
        public function setOption($name, $value)
        {
            $this->options[$name] = $value;
        }
        public function parse($buffer, $presets = [])
        {
            $this->setVariables($presets);
            $parser = new \Less_Parser($this->getOptions());
            $parser->setImportDirs($this->getImportDirs());
            foreach ($this->libFunctions as $name => $func) {
                $parser->registerFunction($name, $func);
            }
            $parser->parse($buffer);
            if (\count($this->registeredVars)) {
                $parser->ModifyVars($this->registeredVars);
            }
            return $parser->getCss();
        }
        protected function getOptions()
        {
            $options = ['relativeUrls' => \false];
            switch ($this->formatterName) {
                case 'compressed':
                    $options['compress'] = \true;
                    break;
            }
            if (\is_array($this->options)) {
                $options = \array_merge($options, $this->options);
            }
            return $options;
        }
        protected function getImportDirs()
        {
            $dirs_ = (array) $this->importDir;
            $dirs = [];
            foreach ($dirs_ as $dir) {
                $dirs[$dir] = '';
            }
            return $dirs;
        }
        public function compile($string, $name = null)
        {
            $oldImport = $this->importDir;
            $this->importDir = (array) $this->importDir;
            $this->allParsedFiles = [];
            $parser = new \Less_Parser($this->getOptions());
            $parser->SetImportDirs($this->getImportDirs());
            if (\count($this->registeredVars)) {
                $parser->ModifyVars($this->registeredVars);
            }
            foreach ($this->libFunctions as $name => $func) {
                $parser->registerFunction($name, $func);
            }
            $parser->parse($string);
            $out = $parser->getCss();
            $parsed = \Less_Parser::AllParsedFiles();
            foreach ($parsed as $file) {
                $this->addParsedFile($file);
            }
            $this->importDir = $oldImport;
            return $out;
        }
        public function compileFile($fname, $outFname = null)
        {
            if (!\is_readable($fname)) {
                throw new \Exception('load error: failed to find ' . $fname);
            }
            $pi = \pathinfo($fname);
            $oldImport = $this->importDir;
            $this->importDir = (array) $this->importDir;
            $this->importDir[] = \Less_Parser::AbsPath($pi['dirname']) . '/';
            $this->allParsedFiles = [];
            $this->addParsedFile($fname);
            $parser = new \Less_Parser($this->getOptions());
            $parser->SetImportDirs($this->getImportDirs());
            if (\count($this->registeredVars)) {
                $parser->ModifyVars($this->registeredVars);
            }
            foreach ($this->libFunctions as $name => $func) {
                $parser->registerFunction($name, $func);
            }
            $parser->parseFile($fname);
            $out = $parser->getCss();
            $parsed = \Less_Parser::AllParsedFiles();
            foreach ($parsed as $file) {
                $this->addParsedFile($file);
            }
            $this->importDir = $oldImport;
            if ($outFname !== null) {
                return \file_put_contents($outFname, $out);
            }
            return $out;
        }
        public function checkedCompile($in, $out)
        {
            if (!\is_file($out) || \filemtime($in) > \filemtime($out)) {
                $this->compileFile($in, $out);
                return \true;
            }
            return \false;
        }
        /**
         * Execute lessphp on a .less file or a lessphp cache structure
         *
         * The lessphp cache structure contains information about a specific
         * less file having been parsed. It can be used as a hint for future
         * calls to determine whether or not a rebuild is required.
         *
         * The cache structure contains two important keys that may be used
         * externally:
         *
         * compiled: The final compiled CSS
         * updated: The time (in seconds) the CSS was last compiled
         *
         * The cache structure is a plain-ol' PHP associative array and can
         * be serialized and unserialized without a hitch.
         *
         * @param mixed $in Input
         * @param bool $force Force rebuild?
         * @return array lessphp cache structure
         */
        public function cachedCompile($in, $force = \false)
        {
            // assume no root
            $root = null;
            if (\is_string($in)) {
                $root = $in;
            } elseif (\is_array($in) and isset($in['root'])) {
                if ($force or !isset($in['files'])) {
                    // If we are forcing a recompile or if for some reason the
                    // structure does not contain any file information we should
                    // specify the root to trigger a rebuild.
                    $root = $in['root'];
                } elseif (isset($in['files']) and \is_array($in['files'])) {
                    foreach ($in['files'] as $fname => $ftime) {
                        if (!\file_exists($fname) or \filemtime($fname) > $ftime) {
                            // One of the files we knew about previously has changed
                            // so we should look at our incoming root again.
                            $root = $in['root'];
                            break;
                        }
                    }
                }
            } else {
                // TODO: Throw an exception? We got neither a string nor something
                // that looks like a compatible lessphp cache structure.
                return null;
            }
            if ($root !== null) {
                // If we have a root value which means we should rebuild.
                $out = [];
                $out['root'] = $root;
                $out['compiled'] = $this->compileFile($root);
                $out['files'] = $this->allParsedFiles();
                $out['updated'] = \time();
                return $out;
            } else {
                // No changes, pass back the structure
                // we were given initially.
                return $in;
            }
        }
        public function ccompile($in, $out, $less = null)
        {
            if ($less === null) {
                $less = new self();
            }
            return $less->checkedCompile($in, $out);
        }
        public static function cexecute($in, $force = \false, $less = null)
        {
            if ($less === null) {
                $less = new self();
            }
            return $less->cachedCompile($in, $force);
        }
        public function allParsedFiles()
        {
            return $this->allParsedFiles;
        }
        protected function addParsedFile($file)
        {
            $this->allParsedFiles[\Less_Parser::AbsPath($file)] = \filemtime($file);
        }
    }
}
