<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\TagManager;

use Piwik\Plugins\TagManager\Template\Variable\MatomoConfigurationVariable;
use Piwik\Plugins\TagManager\UpdateHelper\NewVariableParameterMigrator;
use Piwik\Updater;
use Piwik\Updates as PiwikUpdates;
/**
 * Update for version 4.12.4-b1.
 */
class Updates_4_12_4_b1 extends PiwikUpdates
{
    public function doUpdate(Updater $updater)
    {
        // Migrate the Matomo type tags to all include the newly configured field.
        $migrator = new NewVariableParameterMigrator(MatomoConfigurationVariable::ID, 'customCookieTimeOutEnable', 0);
        $migrator->addField('customCookieTimeOut', 393);
        $migrator->migrate();
        // This kicks off the processing of the tag migration.
    }
}
