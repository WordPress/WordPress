<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Autoloader Generator.
 *
 * @package automattic/jetpack-autoloader
 */

// phpcs:disable PHPCompatibility.Keywords.NewKeywords.t_useFound
// phpcs:disable PHPCompatibility.LanguageConstructs.NewLanguageConstructs.t_ns_separatorFound
// phpcs:disable PHPCompatibility.FunctionDeclarations.NewClosure.Found
// phpcs:disable PHPCompatibility.Keywords.NewKeywords.t_namespaceFound
// phpcs:disable PHPCompatibility.Keywords.NewKeywords.t_dirFound
// phpcs:disable WordPress.Files.FileName.InvalidClassFileName
// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_var_export
// phpcs:disable WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
// phpcs:disable WordPress.NamingConventions.ValidVariableName.InterpolatedVariableNotSnakeCase
// phpcs:disable WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
// phpcs:disable WordPress.NamingConventions.ValidVariableName.PropertyNotSnakeCase

namespace Automattic\Jetpack\Autoloader;

use Composer\Composer;
use Composer\Config;
use Composer\Installer\InstallationManager;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Util\Filesystem;
use Composer\Util\PackageSorter;

/**
 * Class AutoloadGenerator.
 */
class AutoloadGenerator {

	/**
	 * IO object.
	 *
	 * @var IOInterface IO object.
	 */
	private $io;

	/**
	 * The filesystem utility.
	 *
	 * @var Filesystem
	 */
	private $filesystem;

	/**
	 * Instantiate an AutoloadGenerator object.
	 *
	 * @param IOInterface $io IO object.
	 */
	public function __construct( IOInterface $io = null ) {
		$this->io         = $io;
		$this->filesystem = new Filesystem();
	}

	/**
	 * Dump the Jetpack autoloader files.
	 *
	 * @param Composer                     $composer The Composer object.
	 * @param Config                       $config Config object.
	 * @param InstalledRepositoryInterface $localRepo Installed Repository object.
	 * @param PackageInterface             $mainPackage Main Package object.
	 * @param InstallationManager          $installationManager Manager for installing packages.
	 * @param string                       $targetDir Path to the current target directory.
	 * @param bool                         $scanPsrPackages Whether or not PSR packages should be converted to a classmap.
	 * @param string                       $suffix The autoloader suffix.
	 */
	public function dump(
		Composer $composer,
		Config $config,
		InstalledRepositoryInterface $localRepo,
		PackageInterface $mainPackage,
		InstallationManager $installationManager,
		$targetDir,
		$scanPsrPackages = false,
		$suffix = null
	) {
		$this->filesystem->ensureDirectoryExists( $config->get( 'vendor-dir' ) );

		$packageMap = $composer->getAutoloadGenerator()->buildPackageMap( $installationManager, $mainPackage, $localRepo->getCanonicalPackages() );
		$autoloads  = $this->parseAutoloads( $packageMap, $mainPackage );

		// Convert the autoloads into a format that the manifest generator can consume more easily.
		$basePath           = $this->filesystem->normalizePath( realpath( getcwd() ) );
		$vendorPath         = $this->filesystem->normalizePath( realpath( $config->get( 'vendor-dir' ) ) );
		$processedAutoloads = $this->processAutoloads( $autoloads, $scanPsrPackages, $vendorPath, $basePath );
		unset( $packageMap, $autoloads );

		// Make sure none of the legacy files remain that can lead to problems with the autoloader.
		$this->removeLegacyFiles( $vendorPath );

		// Write all of the files now that we're done.
		$this->writeAutoloaderFiles( $vendorPath . '/jetpack-autoloader/', $suffix );
		$this->writeManifests( $vendorPath . '/' . $targetDir, $processedAutoloads );

		if ( ! $scanPsrPackages ) {
			$this->io->writeError( '<warning>You are generating an unoptimized autoloader. If this is a production build, consider using the -o option.</warning>' );
		}
	}

	/**
	 * Compiles an ordered list of namespace => path mappings
	 *
	 * @param  array            $packageMap  Array of array(package, installDir-relative-to-composer.json).
	 * @param  PackageInterface $mainPackage Main package instance.
	 *
	 * @return array The list of path mappings.
	 */
	public function parseAutoloads( array $packageMap, PackageInterface $mainPackage ) {
		$rootPackageMap = array_shift( $packageMap );

		$sortedPackageMap   = $this->sortPackageMap( $packageMap );
		$sortedPackageMap[] = $rootPackageMap;
		array_unshift( $packageMap, $rootPackageMap );

		$psr0     = $this->parseAutoloadsType( $packageMap, 'psr-0', $mainPackage );
		$psr4     = $this->parseAutoloadsType( $packageMap, 'psr-4', $mainPackage );
		$classmap = $this->parseAutoloadsType( array_reverse( $sortedPackageMap ), 'classmap', $mainPackage );
		$files    = $this->parseAutoloadsType( $sortedPackageMap, 'files', $mainPackage );

		krsort( $psr0 );
		krsort( $psr4 );

		return array(
			'psr-0'    => $psr0,
			'psr-4'    => $psr4,
			'classmap' => $classmap,
			'files'    => $files,
		);
	}

	/**
	 * Sorts packages by dependency weight
	 *
	 * Packages of equal weight retain the original order
	 *
	 * @param  array $packageMap The package map.
	 *
	 * @return array
	 */
	protected function sortPackageMap( array $packageMap ) {
		$packages = array();
		$paths    = array();

		foreach ( $packageMap as $item ) {
			list( $package, $path ) = $item;
			$name                   = $package->getName();
			$packages[ $name ]      = $package;
			$paths[ $name ]         = $path;
		}

		$sortedPackages = PackageSorter::sortPackages( $packages );

		$sortedPackageMap = array();

		foreach ( $sortedPackages as $package ) {
			$name               = $package->getName();
			$sortedPackageMap[] = array( $packages[ $name ], $paths[ $name ] );
		}

		return $sortedPackageMap;
	}

	/**
	 * Returns the file identifier.
	 *
	 * @param PackageInterface $package The package instance.
	 * @param string           $path The path.
	 */
	protected function getFileIdentifier( PackageInterface $package, $path ) {
		return md5( $package->getName() . ':' . $path );
	}

	/**
	 * Returns the path code for the given path.
	 *
	 * @param Filesystem $filesystem The filesystem instance.
	 * @param string     $basePath The base path.
	 * @param string     $vendorPath The vendor path.
	 * @param string     $path The path.
	 *
	 * @return string The path code.
	 */
	protected function getPathCode( Filesystem $filesystem, $basePath, $vendorPath, $path ) {
		if ( ! $filesystem->isAbsolutePath( $path ) ) {
			$path = $basePath . '/' . $path;
		}
		$path = $filesystem->normalizePath( $path );

		$baseDir = '';
		if ( 0 === strpos( $path . '/', $vendorPath . '/' ) ) {
			$path    = substr( $path, strlen( $vendorPath ) );
			$baseDir = '$vendorDir';

			if ( false !== $path ) {
				$baseDir .= ' . ';
			}
		} else {
			$path = $filesystem->normalizePath( $filesystem->findShortestPath( $basePath, $path, true ) );
			if ( ! $filesystem->isAbsolutePath( $path ) ) {
				$baseDir = '$baseDir . ';
				$path    = '/' . $path;
			}
		}

		if ( strpos( $path, '.phar' ) !== false ) {
			$baseDir = "'phar://' . " . $baseDir;
		}

		return $baseDir . ( ( false !== $path ) ? var_export( $path, true ) : '' );
	}

	/**
	 * This function differs from the composer parseAutoloadsType in that beside returning the path.
	 * It also return the path and the version of a package.
	 *
	 * Supports PSR-4, PSR-0, and classmap parsing.
	 *
	 * @param array            $packageMap Map of all the packages.
	 * @param string           $type Type of autoloader to use.
	 * @param PackageInterface $mainPackage Instance of the Package Object.
	 *
	 * @return array
	 */
	protected function parseAutoloadsType( array $packageMap, $type, PackageInterface $mainPackage ) {
		$autoloads = array();

		foreach ( $packageMap as $item ) {
			list($package, $installPath) = $item;
			$autoload                    = $package->getAutoload();

			if ( $package === $mainPackage ) {
				$autoload = array_merge_recursive( $autoload, $package->getDevAutoload() );
			}

			if ( null !== $package->getTargetDir() && $package !== $mainPackage ) {
				$installPath = substr( $installPath, 0, -strlen( '/' . $package->getTargetDir() ) );
			}

			if ( in_array( $type, array( 'psr-4', 'psr-0' ), true ) && isset( $autoload[ $type ] ) && is_array( $autoload[ $type ] ) ) {
				foreach ( $autoload[ $type ] as $namespace => $paths ) {
					$paths = is_array( $paths ) ? $paths : array( $paths );
					foreach ( $paths as $path ) {
						$relativePath              = empty( $installPath ) ? ( empty( $path ) ? '.' : $path ) : $installPath . '/' . $path;
						$autoloads[ $namespace ][] = array(
							'path'    => $relativePath,
							'version' => $package->getVersion(), // Version of the class comes from the package - should we try to parse it?
						);
					}
				}
			}

			if ( 'classmap' === $type && isset( $autoload['classmap'] ) && is_array( $autoload['classmap'] ) ) {
				foreach ( $autoload['classmap'] as $paths ) {
					$paths = is_array( $paths ) ? $paths : array( $paths );
					foreach ( $paths as $path ) {
						$relativePath = empty( $installPath ) ? ( empty( $path ) ? '.' : $path ) : $installPath . '/' . $path;
						$autoloads[]  = array(
							'path'    => $relativePath,
							'version' => $package->getVersion(), // Version of the class comes from the package - should we try to parse it?
						);
					}
				}
			}
			if ( 'files' === $type && isset( $autoload['files'] ) && is_array( $autoload['files'] ) ) {
				foreach ( $autoload['files'] as $paths ) {
					$paths = is_array( $paths ) ? $paths : array( $paths );
					foreach ( $paths as $path ) {
						$relativePath = empty( $installPath ) ? ( empty( $path ) ? '.' : $path ) : $installPath . '/' . $path;
						$autoloads[ $this->getFileIdentifier( $package, $path ) ] = array(
							'path'    => $relativePath,
							'version' => $package->getVersion(), // Version of the file comes from the package - should we try to parse it?
						);
					}
				}
			}
		}

		return $autoloads;
	}

	/**
	 * Given Composer's autoloads this will convert them to a version that we can use to generate the manifests.
	 *
	 * When the $scanPsrPackages argument is true, PSR-4 namespaces are converted to classmaps. When $scanPsrPackages
	 * is false, PSR-4 namespaces are not converted to classmaps.
	 *
	 * PSR-0 namespaces are always converted to classmaps.
	 *
	 * @param array  $autoloads The autoloads we want to process.
	 * @param bool   $scanPsrPackages Whether or not PSR-4 packages should be converted to a classmap.
	 * @param string $vendorPath The path to the vendor directory.
	 * @param string $basePath The path to the current directory.
	 *
	 * @return array $processedAutoloads
	 */
	private function processAutoloads( $autoloads, $scanPsrPackages, $vendorPath, $basePath ) {
		$processor = new AutoloadProcessor(
			function ( $path, $excludedClasses, $namespace ) use ( $basePath ) {
				$dir = $this->filesystem->normalizePath(
					$this->filesystem->isAbsolutePath( $path ) ? $path : $basePath . '/' . $path
				);

				// Composer 2.4 changed the name of the class.
				if ( class_exists( \Composer\ClassMapGenerator\ClassMapGenerator::class ) ) {
					$generator = new \Composer\ClassMapGenerator\ClassMapGenerator();
					$generator->scanPaths( $dir, $excludedClasses, 'classmap', empty( $namespace ) ? null : $namespace );
					return $generator->getClassMap()->getMap();
				}

				return \Composer\Autoload\ClassMapGenerator::createMap(
					$dir,
					$excludedClasses,
					null, // Don't pass the IOInterface since the normal autoload generation will have reported already.
					empty( $namespace ) ? null : $namespace
				);
			},
			function ( $path ) use ( $basePath, $vendorPath ) {
				return $this->getPathCode( $this->filesystem, $basePath, $vendorPath, $path );
			}
		);

		return array(
			'psr-4'    => $processor->processPsr4Packages( $autoloads, $scanPsrPackages ),
			'classmap' => $processor->processClassmap( $autoloads, $scanPsrPackages ),
			'files'    => $processor->processFiles( $autoloads ),
		);
	}

	/**
	 * Removes all of the legacy autoloader files so they don't cause any problems.
	 *
	 * @param string $outDir The directory legacy files are written to.
	 */
	private function removeLegacyFiles( $outDir ) {
		$files = array(
			'autoload_functions.php',
			'class-autoloader-handler.php',
			'class-classes-handler.php',
			'class-files-handler.php',
			'class-plugins-handler.php',
			'class-version-selector.php',
		);
		foreach ( $files as $file ) {
			$this->filesystem->remove( $outDir . '/' . $file );
		}
	}

	/**
	 * Writes all of the autoloader files to disk.
	 *
	 * @param string $outDir The directory to write to.
	 * @param string $suffix The unique autoloader suffix.
	 */
	private function writeAutoloaderFiles( $outDir, $suffix ) {
		$this->io->writeError( "<info>Generating jetpack autoloader ($outDir)</info>" );

		// We will remove all autoloader files to generate this again.
		$this->filesystem->emptyDirectory( $outDir );

		// Write the autoloader files.
		AutoloadFileWriter::copyAutoloaderFiles( $this->io, $outDir, $suffix );
	}

	/**
	 * Writes all of the manifest files to disk.
	 *
	 * @param string $outDir The directory to write to.
	 * @param array  $processedAutoloads The processed autoloads.
	 */
	private function writeManifests( $outDir, $processedAutoloads ) {
		$this->io->writeError( "<info>Generating jetpack autoloader manifests ($outDir)</info>" );

		$manifestFiles = array(
			'classmap' => 'jetpack_autoload_classmap.php',
			'psr-4'    => 'jetpack_autoload_psr4.php',
			'files'    => 'jetpack_autoload_filemap.php',
		);

		foreach ( $manifestFiles as $key => $file ) {
			// Make sure the file doesn't exist so it isn't there if we don't write it.
			$this->filesystem->remove( $outDir . '/' . $file );
			if ( empty( $processedAutoloads[ $key ] ) ) {
				continue;
			}

			$content = ManifestGenerator::buildManifest( $key, $file, $processedAutoloads[ $key ] );
			if ( empty( $content ) ) {
				continue;
			}

			if ( file_put_contents( $outDir . '/' . $file, $content ) ) {
				$this->io->writeError( "  <info>Generated: $file</info>" );
			} else {
				$this->io->writeError( "  <error>Error: $file</error>" );
			}
		}
	}
}
