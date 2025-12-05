<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\TagManager;

use Piwik\Updater;
use Piwik\Updater\Migration;
use Piwik\Updater\Migration\Factory as MigrationFactory;
use Piwik\Updates as PiwikUpdates;
/**
 * Update for version 5.2.0-b3.
 */
class Updates_5_2_0_b3 extends PiwikUpdates
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
        return array(
            // Create activelySyncGtmDataLayer with default 0 so that any existing containers are disabled, but then change the column so that new containers default with it enabled
            $this->migration->db->addColumn('tagmanager_container', 'activelySyncGtmDataLayer', 'TINYINT(1) UNSIGNED NOT NULL DEFAULT 0', 'ignoreGtmDataLayer'),
            $this->migration->db->changeColumn('tagmanager_container', 'activelySyncGtmDataLayer', 'activelySyncGtmDataLayer', 'TINYINT(1) UNSIGNED NOT NULL DEFAULT 1'),
            // Running these again in case they didn't run as part of the 5.2.0-b1 migration for some reason
            $this->migration->db->changeColumn('tagmanager_container_version', 'name', 'name', "VARCHAR(255) NOT NULL DEFAULT ''"),
            $this->migration->db->changeColumn('tagmanager_container', 'name', 'name', 'VARCHAR(255) NOT NULL'),
            $this->migration->db->changeColumn('tagmanager_tag', 'name', 'name', 'VARCHAR(255) NOT NULL'),
            $this->migration->db->changeColumn('tagmanager_trigger', 'name', 'name', 'VARCHAR(255) NOT NULL'),
            $this->migration->db->changeColumn('tagmanager_variable', 'name', 'name', 'VARCHAR(255) NOT NULL'),
        );
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
    }
}
