<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Autoload Processor.
 *
 * @package automattic/jetpack-autoloader
 */

// phpcs:disable WordPress.Files.FileName.InvalidClassFileName
// phpcs:disable WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
// phpcs:disable WordPress.NamingConventions.ValidVariableName.InterpolatedVariableNotSnakeCase
// phpcs:disable WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
// phpcs:disable WordPress.NamingConventions.ValidVariableName.PropertyNotSnakeCase

namespace Automattic\Jetpack\Autoloader;

/**
 * Class AutoloadProcessor.
 */
class AutoloadProcessor {

	/**
	 * A callable for scanning a directory for all of its classes.
	 *
	 * @var callable
	 */
	private $classmapScanner;

	/**
	 * A callable for transforming a path into one to be used in code.
	 *
	 * @var callable
	 */
	private $pathCodeTransformer;

	/**
	 * The constructor.
	 *
	 * @param callable $classmapScanner A callable for scanning a directory for all of its classes.
	 * @param callable $pathCodeTransformer A callable for transforming a path into one to be used in code.
	 */
	public function __construct( $classmapScanner, $pathCodeTransformer ) {
		$this->classmapScanner     = $classmapScanner;
		$this->pathCodeTransformer = $pathCodeTransformer;
	}

	/**
	 * Processes the classmap autoloads into a relative path format including the version for each file.
	 *
	 * @param array $autoloads The autoloads we are processing.
	 * @param bool  $scanPsrPackages Whether or not PSR packages should be converted to a classmap.
	 *
	 * @return array $processed
	 */
	public function processClassmap( $autoloads, $scanPsrPackages ) {
		// We can't scan PSR packages if we don't actually have any.
		if ( empty( $autoloads['psr-4'] ) ) {
			$scanPsrPackages = false;
		}

		if ( empty( $autoloads['classmap'] ) && ! $scanPsrPackages ) {
			return null;
		}

		$excludedClasses = null;
		if ( ! empty( $autoloads['exclude-from-classmap'] ) ) {
			$excludedClasses = '{(' . implode( '|', $autoloads['exclude-from-classmap'] ) . ')}';
		}

		$processed = array();

		if ( $scanPsrPackages ) {
			foreach ( $autoloads['psr-4'] as $namespace => $sources ) {
				$namespace = empty( $namespace ) ? null : $namespace;

				foreach ( $sources as $source ) {
					$classmap = call_user_func( $this->classmapScanner, $source['path'], $excludedClasses, $namespace );

					foreach ( $classmap as $class => $path ) {
						$processed[ $class ] = array(
							'version' => $source['version'],
							'path'    => call_user_func( $this->pathCodeTransformer, $path ),
						);
					}
				}
			}
		}

		/*
		 * PSR-0 namespaces are converted to classmaps for both optimized and unoptimized autoloaders because any new
		 * development should use classmap or PSR-4 autoloading.
		 */
		if ( ! empty( $autoloads['psr-0'] ) ) {
			foreach ( $autoloads['psr-0'] as $namespace => $sources ) {
				$namespace = empty( $namespace ) ? null : $namespace;

				foreach ( $sources as $source ) {
					$classmap = call_user_func( $this->classmapScanner, $source['path'], $excludedClasses, $namespace );
					foreach ( $classmap as $class => $path ) {
						$processed[ $class ] = array(
							'version' => $source['version'],
							'path'    => call_user_func( $this->pathCodeTransformer, $path ),
						);
					}
				}
			}
		}

		if ( ! empty( $autoloads['classmap'] ) ) {
			foreach ( $autoloads['classmap'] as $package ) {
				$classmap = call_user_func( $this->classmapScanner, $package['path'], $excludedClasses, null );

				foreach ( $classmap as $class => $path ) {
					$processed[ $class ] = array(
						'version' => $package['version'],
						'path'    => call_user_func( $this->pathCodeTransformer, $path ),
					);
				}
			}
		}

		return $processed;
	}

	/**
	 * Processes the PSR-4 autoloads into a relative path format including the version for each file.
	 *
	 * @param array $autoloads The autoloads we are processing.
	 * @param bool  $scanPsrPackages Whether or not PSR packages should be converted to a classmap.
	 *
	 * @return array $processed
	 */
	public function processPsr4Packages( $autoloads, $scanPsrPackages ) {
		if ( $scanPsrPackages || empty( $autoloads['psr-4'] ) ) {
			return null;
		}

		$processed = array();

		foreach ( $autoloads['psr-4'] as $namespace => $packages ) {
			$namespace = empty( $namespace ) ? null : $namespace;
			$paths     = array();

			foreach ( $packages as $package ) {
				$paths[] = call_user_func( $this->pathCodeTransformer, $package['path'] );
			}

			$processed[ $namespace ] = array(
				'version' => $package['version'],
				'path'    => $paths,
			);
		}

		return $processed;
	}

	/**
	 * Processes the file autoloads into a relative format including the version for each file.
	 *
	 * @param array $autoloads The autoloads we are processing.
	 *
	 * @return array|null $processed
	 */
	public function processFiles( $autoloads ) {
		if ( empty( $autoloads['files'] ) ) {
			return null;
		}

		$processed = array();

		foreach ( $autoloads['files'] as $file_id => $package ) {
			$processed[ $file_id ] = array(
				'version' => $package['version'],
				'path'    => call_user_func( $this->pathCodeTransformer, $package['path'] ),
			);
		}

		return $processed;
	}
}
