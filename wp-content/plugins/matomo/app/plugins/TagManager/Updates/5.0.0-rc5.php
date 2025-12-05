<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\TagManager;

use Piwik\Plugins\TagManager\Template\Tag\MatomoTag;
use Piwik\Plugins\TagManager\UpdateHelper\NewTagParameterMigrator;
use Piwik\Updater;
use Piwik\Updater\Migration;
use Piwik\Updater\Migration\Factory as MigrationFactory;
use Piwik\Updates as PiwikUpdates;
/**
 * Update for version 5.0.0-rc5.
 */
class Updates_5_0_0_rc5 extends PiwikUpdates
{
    /**
     * @var MigrationFactory
     */
    private $migration;
    public function __construct(MigrationFactory $factory)
    {
        $this->migration = $factory;
    }
    /**
     * Return database migrations to be executed in this update.
     *
     * Database migrations should be defined here, instead of in `doUpdate()`, since this method is used
     * in the `core:update` command when displaying the queries an update will run. If you execute
     * migrations directly in `doUpdate()`, they won't be displayed to the user. Migrations will be executed in the
     * order as positioned in the returned array.
     *
     * @param Updater $updater
     * @return Migration\Db[]
     */
    public function getMigrations(Updater $updater)
    {
        return array();
    }
    /**
     * Perform the incremental version update.
     *
     * This method should perform all updating logic. If you define queries in the `getMigrations()` method,
     * you must call {@link Updater::executeMigrations()} here.
     *
     * @param Updater $updater
     */
    public function doUpdate(Updater $updater)
    {
        $updater->executeMigrations(__FILE__, $this->getMigrations($updater));
        // Migrate the MatomoConfiguration type variables to all include the newly configured fields.
        $migrator = new NewTagParameterMigrator(MatomoTag::ID, 'isEcommerceView', \false);
        $migrator->addField('productSKU', '');
        $migrator->addField('productName', '');
        $migrator->addField('categoryName', '');
        $migrator->addField('price', '');
        $migrator->migrate();
    }
}
