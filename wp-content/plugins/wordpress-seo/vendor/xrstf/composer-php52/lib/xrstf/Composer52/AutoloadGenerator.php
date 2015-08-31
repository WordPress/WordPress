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
 * - Igor Wiedler <igor@wiedler.ch>
 * - Jordi Boggiano <j.boggiano@seld.be>
 */

namespace xrstf\Composer52;

use Composer\Autoload\AutoloadGenerator as BaseGenerator;
use Composer\Autoload\ClassMapGenerator;
use Composer\Config;
use Composer\Installer\InstallationManager;
use Composer\Package\AliasPackage;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Util\Filesystem;

class AutoloadGenerator extends BaseGenerator {
	public function __construct() {
		// do nothing (but keep this constructor so we can build an instance without the need for an event dispatcher)
	}

	public function dump(Config $config, InstalledRepositoryInterface $localRepo, PackageInterface $mainPackage, InstallationManager $installationManager, $targetDir, $scanPsr0Packages = false, $suffix = '') {
		$filesystem = new Filesystem();
		$filesystem->ensureDirectoryExists($config->get('vendor-dir'));

		$cwd        = getcwd();
		$basePath   = $filesystem->normalizePath($cwd);
		$vendorPath = $filesystem->normalizePath(realpath($config->get('vendor-dir')));
		$targetDir  = $vendorPath.'/'.$targetDir;
		$filesystem->ensureDirectoryExists($targetDir);

		$useGlobalIncludePath  = (bool) $config->get('use-include-path');
		$prependAutoloader     = $config->get('prepend-autoloader') === false ? 'false' : 'true';
		$classMapAuthoritative = $config->get('classmap-authoritative');

		$vendorPathCode            = $filesystem->findShortestPathCode(realpath($targetDir), $vendorPath, true);
		$vendorPathToTargetDirCode = $filesystem->findShortestPathCode($vendorPath, realpath($targetDir), true);

		$appBaseDirCode = $filesystem->findShortestPathCode($vendorPath, $basePath, true);
		$appBaseDirCode = str_replace('__DIR__', '$vendorDir', $appBaseDirCode);

		// add 5.2 compat
		$vendorPathCode            = str_replace('__DIR__', 'dirname(__FILE__)', $vendorPathCode);
		$vendorPathToTargetDirCode = str_replace('__DIR__', 'dirname(__FILE__)', $vendorPathToTargetDirCode);

		$packageMap = $this->buildPackageMap($installationManager, $mainPackage, $localRepo->getCanonicalPackages());
		$autoloads = $this->parseAutoloads($packageMap, $mainPackage);

		// add custom psr-0 autoloading if the root package has a target dir
		$targetDirLoader = null;
		$mainAutoload = $mainPackage->getAutoload();
		if ($mainPackage->getTargetDir() && !empty($mainAutoload['psr-0'])) {
			$levels   = count(explode('/', $filesystem->normalizePath($mainPackage->getTargetDir())));
			$prefixes = implode(', ', array_map(function ($prefix) {
				return var_export($prefix, true);
			}, array_keys($mainAutoload['psr-0'])));

			$baseDirFromTargetDirCode = $filesystem->findShortestPathCode($targetDir, $basePath, true);

			$targetDirLoader = <<<EOF

	public static function autoload(\$class) {
		\$dir      = $baseDirFromTargetDirCode.'/';
		\$prefixes = array($prefixes);

		foreach (\$prefixes as \$prefix) {
			if (0 !== strpos(\$class, \$prefix)) {
				continue;
			}

			\$path = explode(DIRECTORY_SEPARATOR, self::getClassPath(\$class));
			\$path = \$dir.implode('/', array_slice(\$path, $levels));

			if (!\$path = self::resolveIncludePath(\$path)) {
				return false;
			}

			require \$path;
			return true;
		}
	}

EOF;
		}

		$filesCode = "";
		$autoloads['files'] = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($autoloads['files']));
		foreach ($autoloads['files'] as $functionFile) {
			// don't include file if it is using PHP 5.3+ syntax
			// https://bitbucket.org/xrstf/composer-php52/issue/4
			if ($this->isPHP53($functionFile)) {
				$filesCode .= '//		require '.$this->getPathCode($filesystem, $basePath, $vendorPath, $functionFile)."; // disabled because of PHP 5.3 syntax\n";
			}
			else {
				$filesCode .= '		require '.$this->getPathCode($filesystem, $basePath, $vendorPath, $functionFile).";\n";
			}
		}

		if (!$suffix) {
			$suffix = md5(uniqid('', true));
		}

		$includePathFile = $this->getIncludePathsFile($packageMap, $filesystem, $basePath, $vendorPath, $vendorPathCode, $appBaseDirCode);

		file_put_contents($vendorPath.'/autoload_52.php', $this->getAutoloadFile($vendorPathToTargetDirCode, $suffix));
		file_put_contents($targetDir.'/autoload_real_52.php', $this->getAutoloadRealFile(true, (bool) $includePathFile, $targetDirLoader, $filesCode, $vendorPathCode, $appBaseDirCode, $suffix, $useGlobalIncludePath, $prependAutoloader, $classMapAuthoritative));

		// use stream_copy_to_stream instead of copy
		// to work around https://bugs.php.net/bug.php?id=64634
		$sourceLoader = fopen(__DIR__.'/ClassLoader.php', 'r');
		$targetLoader = fopen($targetDir.'/ClassLoader52.php', 'w+');
		stream_copy_to_stream($sourceLoader, $targetLoader);
		fclose($sourceLoader);
		fclose($targetLoader);
		unset($sourceLoader, $targetLoader);
	}

	protected function isPHP53($file) {
		$tokens = token_get_all(file_get_contents($file));
		$php53  = array(T_DIR, T_GOTO, T_NAMESPACE, T_NS_C, T_NS_SEPARATOR, T_USE);

		// PHP 5.4+
		if (defined('T_TRAIT')) {
			$php53[] = T_TRAIT;
			$php53[] = T_TRAIT_C;
			$php53[] = T_TRAIT_C;
		}

		// PHP 5.5+
		if (defined('T_FINALLY')) {
			$php53[] = T_FINALLY;
			$php53[] = T_YIELD;
		}

		foreach ($tokens as $token) {
			if (is_array($token) && in_array($token[0], $php53)) {
				return true;
			}
		}

		return false;
	}

	protected function getIncludePathsFile(array $packageMap, Filesystem $filesystem, $basePath, $vendorPath, $vendorPathCode, $appBaseDirCode) {
		$includePaths = array();

		foreach ($packageMap as $item) {
			list($package, $installPath) = $item;

			if (null !== $package->getTargetDir() && strlen($package->getTargetDir()) > 0) {
				$installPath = substr($installPath, 0, -strlen('/'.$package->getTargetDir()));
			}

			foreach ($package->getIncludePaths() as $includePath) {
				$includePath = trim($includePath, '/');
				$includePaths[] = empty($installPath) ? $includePath : $installPath.'/'.$includePath;
			}
		}

		if (!$includePaths) {
			return;
		}

		$includePathsFile = <<<EOF
<?php

// include_paths_52.php generated by xrstf/composer-php52

\$vendorDir = $vendorPathCode;
\$baseDir = $appBaseDirCode;

return array(

EOF;

		foreach ($includePaths as $path) {
			$includePathsFile .= "\t" . $this->getPathCode($filesystem, $basePath, $vendorPath, $path) . ",\n";
		}

		return $includePathsFile . ");\n";
	}

	protected function getAutoloadFile($vendorPathToTargetDirCode, $suffix) {
		return <<<AUTOLOAD
<?php

// autoload_52.php generated by xrstf/composer-php52

require_once $vendorPathToTargetDirCode.'/autoload_real_52.php';

return ComposerAutoloaderInit$suffix::getLoader();

AUTOLOAD;
	}

	protected function getAutoloadRealFile($useClassMap, $useIncludePath, $targetDirLoader, $filesCode, $vendorPathCode, $appBaseDirCode, $suffix, $useGlobalIncludePath, $prependAutoloader, $classMapAuthoritative) {
		// TODO the class ComposerAutoloaderInit should be revert to a closure
		// when APC has been fixed:
		// - https://github.com/composer/composer/issues/959
		// - https://bugs.php.net/bug.php?id=52144
		// - https://bugs.php.net/bug.php?id=61576
		// - https://bugs.php.net/bug.php?id=59298

		if ($filesCode) {
				$filesCode = "\n\n".rtrim($filesCode);
		}

		$file = <<<HEADER
<?php

// autoload_real_52.php generated by xrstf/composer-php52

class ComposerAutoloaderInit$suffix {
	private static \$loader;

	public static function loadClassLoader(\$class) {
		if ('xrstf_Composer52_ClassLoader' === \$class) {
			require dirname(__FILE__).'/ClassLoader52.php';
		}
	}

	/**
	 * @return xrstf_Composer52_ClassLoader
	 */
	public static function getLoader() {
		if (null !== self::\$loader) {
			return self::\$loader;
		}

		spl_autoload_register(array('ComposerAutoloaderInit$suffix', 'loadClassLoader'), true /*, true */);
		self::\$loader = \$loader = new xrstf_Composer52_ClassLoader();
		spl_autoload_unregister(array('ComposerAutoloaderInit$suffix', 'loadClassLoader'));

		\$vendorDir = $vendorPathCode;
		\$baseDir   = $appBaseDirCode;
		\$dir       = dirname(__FILE__);


HEADER;

		if ($useIncludePath) {
			$file .= <<<'INCLUDE_PATH'
		$includePaths = require $dir.'/include_paths.php';
		array_push($includePaths, get_include_path());
		set_include_path(implode(PATH_SEPARATOR, $includePaths));


INCLUDE_PATH;
		}

		$file .= <<<'PSR0'
		$map = require $dir.'/autoload_namespaces.php';
		foreach ($map as $namespace => $path) {
			$loader->add($namespace, $path);
		}


PSR0;

		if ($useClassMap) {
			$file .= <<<'CLASSMAP'
		$classMap = require $dir.'/autoload_classmap.php';
		if ($classMap) {
			$loader->addClassMap($classMap);
		}


CLASSMAP;
		}

		if ($classMapAuthoritative) {
			$file .= <<<'CLASSMAPAUTHORITATIVE'
		$loader->setClassMapAuthoritative(true);

CLASSMAPAUTHORITATIVE;
		}

		if ($useGlobalIncludePath) {
			$file .= <<<'INCLUDEPATH'
		$loader->setUseIncludePath(true);


INCLUDEPATH;
		}

		if ($targetDirLoader) {
			$file .= <<<REGISTER_AUTOLOAD
		spl_autoload_register(array('ComposerAutoloaderInit$suffix', 'autoload'), true);


REGISTER_AUTOLOAD;

		}

		$file .= <<<METHOD_FOOTER
		\$loader->register($prependAutoloader);{$filesCode}

		return \$loader;
	}

METHOD_FOOTER;

		$file .= $targetDirLoader;

		return $file . <<<FOOTER
}

FOOTER;

	}
}
