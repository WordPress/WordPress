<?php
/*
 * Copyright (c) 2013, Christoph Mewes, http://www.xrstf.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 *
 * --------------------------------------------------------------------------
 *
 * 99% of this is copied as-is from the original Composer source code and is
 * released under MIT license as well. Copyright goes to:
 *
 * - Fabien Potencier <fabien@symfony.com>
 * - Jordi Boggiano <j.boggiano@seld.be>
 */

class xrstf_Composer52_ClassLoader {
	private $prefixes              = array();
	private $fallbackDirs          = array();
	private $useIncludePath        = false;
	private $classMap              = array();
	private $classMapAuthoratative = false;
	private $allowUnderscore       = false;

	/**
	 * @param boolean $flag  true to allow class names with a leading underscore, false to disable
	 */
	public function setAllowUnderscore($flag) {
		$this->allowUnderscore = (boolean) $flag;
	}

	/**
	 * @return array
	 */
	public function getPrefixes() {
		return $this->prefixes;
	}

	/**
	 * Turns off searching the prefix and fallback directories for classes
	 * that have not been registered with the class map.
	 *
	 * @param bool $classMapAuthoratative
	 */
	public function setClassMapAuthoritative($classMapAuthoratative) {
		$this->classMapAuthoratative = $classMapAuthoratative;
	}

	/**
	 * Should class lookup fail if not found in the current class map?
	 *
	 * @return bool
	 */
	public function getClassMapAuthoratative() {
		return $this->classMapAuthoratative;
	}

	/**
	 * @return array
	 */
	public function getFallbackDirs() {
		return $this->fallbackDirs;
	}

	/**
	 * @return array
	 */
	public function getClassMap() {
		return $this->classMap;
	}

	/**
	 * @param array $classMap  class to filename map
	 */
	public function addClassMap(array $classMap) {
		if ($this->classMap) {
			$this->classMap = array_merge($this->classMap, $classMap);
		}
		else {
			$this->classMap = $classMap;
		}
	}

	/**
	 * Registers a set of classes, merging with any others previously set.
	 *
	 * @param string       $prefix   the classes prefix
	 * @param array|string $paths    the location(s) of the classes
	 * @param bool         $prepend  prepend the location(s)
	 */
	public function add($prefix, $paths, $prepend = false) {
		if (!$prefix) {
			if ($prepend) {
				$this->fallbackDirs = array_merge(
					(array) $paths,
					$this->fallbackDirs
				);
			}
			else {
				$this->fallbackDirs = array_merge(
					$this->fallbackDirs,
					(array) $paths
				);
			}

			return;
		}

		if (!isset($this->prefixes[$prefix])) {
			$this->prefixes[$prefix] = (array) $paths;
			return;
		}

		if ($prepend) {
			$this->prefixes[$prefix] = array_merge(
				(array) $paths,
				$this->prefixes[$prefix]
			);
		}
		else {
			$this->prefixes[$prefix] = array_merge(
				$this->prefixes[$prefix],
				(array) $paths
			);
		}
	}

	/**
	 * Registers a set of classes, replacing any others previously set.
	 *
	 * @param string       $prefix  the classes prefix
	 * @param array|string $paths   the location(s) of the classes
	 */
	public function set($prefix, $paths) {
		if (!$prefix) {
			$this->fallbackDirs = (array) $paths;
			return;
		}

		$this->prefixes[$prefix] = (array) $paths;
	}

	/**
	 * Turns on searching the include path for class files.
	 *
	 * @param bool $useIncludePath
	 */
	public function setUseIncludePath($useIncludePath) {
		$this->useIncludePath = $useIncludePath;
	}

	/**
	 * Can be used to check if the autoloader uses the include path to check
	 * for classes.
	 *
	 * @return bool
	 */
	public function getUseIncludePath() {
		return $this->useIncludePath;
	}

	/**
	 * Registers this instance as an autoloader.
	 */
	public function register() {
		spl_autoload_register(array($this, 'loadClass'), true);
	}

	/**
	 * Unregisters this instance as an autoloader.
	 */
	public function unregister() {
		spl_autoload_unregister(array($this, 'loadClass'));
	}

	/**
	 * Loads the given class or interface.
	 *
	 * @param  string $class  the name of the class
	 * @return bool|null      true, if loaded
	 */
	public function loadClass($class) {
		if ($file = $this->findFile($class)) {
			include $file;
			return true;
		}
	}

	/**
	 * Finds the path to the file where the class is defined.
	 *
	 * @param  string $class  the name of the class
	 * @return string|null    the path, if found
	 */
	public function findFile($class) {
		if ('\\' === $class[0]) {
			$class = substr($class, 1);
		}

		if (isset($this->classMap[$class])) {
			return $this->classMap[$class];
		}
		elseif ($this->classMapAuthoratative) {
			return false;
		}

		$classPath = $this->getClassPath($class);

		foreach ($this->prefixes as $prefix => $dirs) {
			if (0 === strpos($class, $prefix)) {
				foreach ($dirs as $dir) {
					if (file_exists($dir.DIRECTORY_SEPARATOR.$classPath)) {
						return $dir.DIRECTORY_SEPARATOR.$classPath;
					}
				}
			}
		}

		foreach ($this->fallbackDirs as $dir) {
			if (file_exists($dir.DIRECTORY_SEPARATOR.$classPath)) {
				return $dir.DIRECTORY_SEPARATOR.$classPath;
			}
		}

		if ($this->useIncludePath && $file = self::resolveIncludePath($classPath)) {
			return $file;
		}

		return $this->classMap[$class] = false;
	}

	private function getClassPath($class) {
		if (false !== $pos = strrpos($class, '\\')) {
			// namespaced class name
			$classPath = str_replace('\\', DIRECTORY_SEPARATOR, substr($class, 0, $pos)).DIRECTORY_SEPARATOR;
			$className = substr($class, $pos + 1);
		}
		else {
			// PEAR-like class name
			$classPath = null;
			$className = $class;
		}

		$className = str_replace('_', DIRECTORY_SEPARATOR, $className);

		// restore the prefix
		if ($this->allowUnderscore && DIRECTORY_SEPARATOR === $className[0]) {
			$className[0] = '_';
		}

		$classPath .= $className.'.php';

		return $classPath;
	}

	public static function resolveIncludePath($classPath) {
		$paths = explode(PATH_SEPARATOR, get_include_path());

		foreach ($paths as $path) {
			$path = rtrim($path, '/\\');

			if ($file = file_exists($path.DIRECTORY_SEPARATOR.$file)) {
				return $file;
			}
		}

		return false;
	}
}
