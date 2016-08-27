<?php
/*
 * Copyright (c) 2013, Christoph Mewes, http://www.xrstf.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

namespace xrstf\Composer52;

use Composer\Repository\CompositeRepository;
use Composer\Script\Event;

class Generator {
	public static function onPostInstallCmd(Event $event) {
		$composer            = $event->getComposer();
		$installationManager = $composer->getInstallationManager();
		$repoManager         = $composer->getRepositoryManager();
		$localRepo           = $repoManager->getLocalRepository();
		$package             = $composer->getPackage();
		$config              = $composer->getConfig();

		// We can't gain access to the Command's input object, so we have to look
		// for -o / --optimize-autoloader ourselves. Sadly, neither getopt() works
		// (always returns an empty array), nor does Symfony's Console Input, as
		// it expects a full definition of the current command line and we can't
		// provide that.

		$args     = $_SERVER['argv'];
		$optimize = in_array('-o', $args) || in_array('--optimize-autoloader', $args) || in_array('--optimize', $args);

		$suffix   = $config->get('autoloader-suffix');

		$generator = new AutoloadGenerator();
		$generator->dump($config, $localRepo, $package, $installationManager, 'composer', $optimize, $suffix);
	}
}
