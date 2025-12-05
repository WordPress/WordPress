<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\ScheduledReports;

use Piwik\Common;
use Piwik\Db;
use Piwik\DbHelper;
class Model
{
    public static $rawPrefix = 'report';
    private $table;
    public function __construct()
    {
        $this->table = Common::prefixTable(self::$rawPrefix);
    }
    public function deleteUserReportForSite($userLogin, $idSite)
    {
        $query = 'DELETE FROM ' . $this->table . ' WHERE login = ? and idsite = ?';
        $bind = array($userLogin, $idSite);
        Db::query($query, $bind);
    }
    public function deleteAllReportForUser($userLogin)
    {
        Db::query('DELETE FROM ' . $this->table . ' WHERE login = ?', $userLogin);
    }
    public function updateReport($idReport, $report)
    {
        $idReport = (int) $idReport;
        $this->getDb()->update($this->table, $report, "idreport = " . $idReport);
    }
    public function createReport($report)
    {
        $nextId = $this->getNextReportId();
        $report['idreport'] = $nextId;
        $this->getDb()->insert($this->table, $report);
        return $nextId;
    }
    private function getNextReportId()
    {
        $db = $this->getDb();
        $idReport = $db->fetchOne("SELECT max(idreport) + 1 FROM " . $this->table);
        if ($idReport == \false) {
            $idReport = 1;
        }
        return $idReport;
    }
    private function getDb()
    {
        return Db::get();
    }
    public static function install()
    {
        $reportTable = "`idreport` INT(11) NOT NULL AUTO_INCREMENT,\n\t\t\t\t\t    `idsite` INTEGER(11) NOT NULL,\n\t\t\t\t\t    `login` VARCHAR(100) NOT NULL,\n\t\t\t\t\t    `description` VARCHAR(255) NOT NULL,\n\t\t\t\t\t    `idsegment` INT(11),\n\t\t\t\t\t    `period` VARCHAR(10) NOT NULL,\n\t\t\t\t\t    `hour` tinyint NOT NULL default 0,\n\t\t\t\t\t    `type` VARCHAR(10) NOT NULL,\n\t\t\t\t\t    `format` VARCHAR(10) NOT NULL,\n\t\t\t\t\t    `reports` TEXT NOT NULL,\n\t\t\t\t\t    `parameters` TEXT NULL,\n\t\t\t\t\t    `ts_created` TIMESTAMP NULL,\n\t\t\t\t\t    `ts_last_sent` TIMESTAMP NULL,\n\t\t\t\t\t    `deleted` tinyint(4) NOT NULL default 0,\n\t\t\t\t\t    `evolution_graph_within_period` TINYINT(4) NOT NULL DEFAULT 0,\n\t\t\t\t\t    `evolution_graph_period_n` INT(11) NOT NULL,\n\t\t\t\t\t    `period_param` VARCHAR(10) NULL,\n\t\t\t\t\t    PRIMARY KEY (`idreport`)";
        DbHelper::createTable(self::$rawPrefix, $reportTable);
    }
}
