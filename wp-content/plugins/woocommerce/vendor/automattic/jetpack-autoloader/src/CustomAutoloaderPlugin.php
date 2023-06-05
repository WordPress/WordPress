<?php //phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Custom Autoloader Composer Plugin, hooks into composer events to generate the custom autoloader.
 *
 * @package automattic/jetpack-autoloader
 */

// phpcs:disable PHPCompatibility.Keywords.NewKeywords.t_useFound
// phpcs:disable PHPCompatibility.LanguageConstructs.NewLanguageConstructs.t_ns_separatorFound
// phpcs:disable PHPCompatibility.Keywords.NewKeywords.t_namespaceFound
// phpcs:disable WordPress.Files.FileName.NotHyphenatedLowercase
// phpcs:disable WordPress.Files.FileName.InvalidClassFileName
// phpcs:disable WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

namespace Automattic\Jetpack\Autoloader;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;

/**
 * Class CustomAutoloaderPlugin.
 *
 * @package automattic/jetpack-autoloader
 */
class CustomAutoloaderPlugin implements PluginInterface, EventSubscriberInterface {

	/**
	 * IO object.
	 *
	 * @var IOInterface IO object.
	 */
	private $io;

	/**
	 * Composer object.
	 *
	 * @var Composer Composer object.
	 */
	private $composer;

	/**
	 * Do nothing.
	 *
	 * @param Composer    $composer Composer object.
	 * @param IOInterface $io IO object.
	 */
	public function activate( Composer $composer, IOInterface $io ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		$this->composer = $composer;
		$this->io       = $io;
	}

	/**
	 * Do nothing.
	 * phpcs:disable VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	 *
	 * @param Composer    $composer Composer object.
	 * @param IOInterface $io IO object.
	 */
	public function deactivate( Composer $composer, IOInterface $io ) {
		/*
		 * Intentionally left empty. This is a PluginInterface method.
		 * phpcs:enable VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		 */
	}

	/**
	 * Do nothing.
	 * phpcs:disable VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	 *
	 * @param Composer    $composer Composer object.
	 * @param IOInterface $io IO object.
	 */
	public function uninstall( Composer $composer, IOInterface $io ) {
		/*
		 * Intentionally left empty. This is a PluginInterface method.
		 * phpcs:enable VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		 */
	}

	/**
	 * Tell composer to listen for events and do something with them.
	 *
	 * @return array List of subscribed events.
	 */
	public static function getSubscribedEvents() {
		return array(
			ScriptEvents::POST_AUTOLOAD_DUMP => 'postAutoloadDump',
		);
	}

	/**
	 * Generate the custom autolaoder.
	 *
	 * @param Event $event Script event object.
	 */
	public function postAutoloadDump( Event $event ) {
		// When the autoloader is not required by the root package we don't want to execute it.
		// This prevents unwanted transitive execution that generates unused autoloaders or
		// at worst throws fatal executions.
		if ( ! $this->isRequiredByRoot() ) {
			return;
		}

		$config = $this->composer->getConfig();

		if ( 'vendor' !== $config->raw()['config']['vendor-dir'] ) {
			$this->io->writeError( "\n<error>An error occurred while generating the autoloader files:", true );
			$this->io->writeError( 'The project\'s composer.json or composer environment set a non-default vendor directory.', true );
			$this->io->writeError( 'The default composer vendor directory must be used.</error>', true );
			exit();
		}

		$installationManager = $this->composer->getInstallationManager();
		$repoManager         = $this->composer->getRepositoryManager();
		$localRepo           = $repoManager->getLocalRepository();
		$package             = $this->composer->getPackage();
		$optimize            = $event->getFlags()['optimize'];
		$suffix              = $this->determineSuffix();

		$generator = new AutoloadGenerator( $this->io );
		$generator->dump( $this->composer, $config, $localRepo, $package, $installationManager, 'composer', $optimize, $suffix );
		$this->generated = true;
	}

	/**
	 * Determine the suffix for the autoloader class.
	 *
	 * Reuses an existing suffix from vendor/autoload_packages.php or vendor/autoload.php if possible.
	 *
	 * @return string Suffix.
	 */
	private function determineSuffix() {
		$config     = $this->composer->getConfig();
		$vendorPath = $config->get( 'vendor-dir' );

		// Command line.
		$suffix = $config->get( 'autoloader-suffix' );
		if ( $suffix ) {
			return $suffix;
		}

		// Reuse our own suffix, if any.
		if ( is_readable( $vendorPath . '/autoload_packages.php' ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			$content = file_get_contents( $vendorPath . '/autoload_packages.php' );
			if ( preg_match( '/^namespace Automattic\\\\Jetpack\\\\Autoloader\\\\jp([^;\s]+);/m', $content, $match ) ) {
				return $match[1];
			}
		}

		// Reuse Composer's suffix, if any.
		if ( is_readable( $vendorPath . '/autoload.php' ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			$content = file_get_contents( $vendorPath . '/autoload.php' );
			if ( preg_match( '{ComposerAutoloaderInit([^:\s]+)::}', $content, $match ) ) {
				return $match[1];
			}
		}

		// Generate a random suffix.
		return md5( uniqid( '', true ) );
	}

	/**
	 * Checks to see whether or not the root package is the one that required the autoloader.
	 *
	 * @return bool
	 */
	private function isRequiredByRoot() {
		$package  = $this->composer->getPackage();
		$requires = $package->getRequires();
		if ( ! is_array( $requires ) ) {
			$requires = array();
		}
		$devRequires = $package->getDevRequires();
		if ( ! is_array( $devRequires ) ) {
			$devRequires = array();
		}
		$requires = array_merge( $requires, $devRequires );

		if ( empty( $requires ) ) {
			$this->io->writeError( "\n<error>The package is not required and this should never happen?</error>", true );
			exit();
		}

		foreach ( $requires as $require ) {
			if ( 'automattic/jetpack-autoloader' === $require->getTarget() ) {
				return true;
			}
		}

		return false;
	}
}
