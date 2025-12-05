<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\Goals;

use Piwik\Common;
use Piwik\Config;
use Piwik\Date;
use Piwik\DbHelper;
use Piwik\Plugins\Live\Model;
use Piwik\Plugins\Live\VisitorDetailsAbstract;
use function Piwik\Plugins\Referrers\getReferrerTypeFromShortName;
class VisitorDetails extends VisitorDetailsAbstract
{
    protected $lastGoalResults = [];
    protected $lastVisitIds = [];
    public function extendVisitorDetails(&$visitor)
    {
        $idVisit = $visitor['idVisit'];
        if (in_array($idVisit, $this->lastVisitIds)) {
            $goalConversionDetails = isset($this->lastGoalResults[$idVisit]) ? $this->lastGoalResults[$idVisit] : [];
        } else {
            $goalConversionDetails = $this->queryGoalConversionsForVisits([$idVisit]);
        }
        $visitor['goalConversions'] = count($goalConversionDetails);
    }
    public function provideActionsForVisitIds(&$actions, $idVisits)
    {
        $this->lastVisitIds = $idVisits;
        $this->lastGoalResults = [];
        $goalConversionDetails = $this->queryGoalConversionsForVisits($idVisits);
        // use while / array_shift combination instead of foreach to save memory
        while (is_array($goalConversionDetails) && count($goalConversionDetails)) {
            $goalConversionDetail = array_shift($goalConversionDetails);
            $idVisit = $goalConversionDetail['idvisit'];
            unset($goalConversionDetail['idvisit']);
            $goalConversionDetail['referrerType'] = $this->getReferrerType($goalConversionDetail['referrerType']);
            $this->lastGoalResults[$idVisit][] = $actions[$idVisit][] = $goalConversionDetail;
        }
    }
    /**
     * @param $idVisit
     * @return array
     * @throws \Exception
     */
    protected function queryGoalConversionsForVisits($idVisits)
    {
        if (empty($idVisits)) {
            return [];
        }
        $sql = "\n\t\t\t\tSELECT\n\t\t\t\t\t\tlog_conversion.idvisit,\n\t\t\t\t\t\t'goal' as type,\n\t\t\t\t\t\tgoal.name as goalName,\n\t\t\t\t\t\tgoal.idgoal as goalId,\n\t\t\t\t\t\tlog_link_visit_action.idpageview,\n\t\t\t\t\t\tlog_conversion.revenue as revenue,\n\t\t\t\t\t\tlog_conversion.idlink_va,\n\t\t\t\t\t\tlog_conversion.idlink_va as goalPageId,\n\t\t\t\t\t\tlog_conversion.server_time as serverTimePretty,\n\t\t\t\t\t\tlog_conversion.url as url,\n\t\t\t\t\t\tlog_conversion.referer_type as referrerType,\n\t\t\t\t\t\tlog_conversion.referer_name as referrerName,\n\t\t\t\t\t\tlog_conversion.referer_keyword as referrerKeyword\n\t\t\t\tFROM " . Common::prefixTable('log_conversion') . " AS log_conversion\n\t\t\t\tLEFT JOIN " . Common::prefixTable('log_link_visit_action') . " AS log_link_visit_action\n\t\t\t\t    ON log_link_visit_action.idlink_va = log_conversion.idlink_va\n\t\t\t\tLEFT JOIN " . Common::prefixTable('goal') . " AS goal\n\t\t\t\t\tON (goal.idsite = log_conversion.idsite\n\t\t\t\t\t\tAND\n\t\t\t\t\t\tgoal.idgoal = log_conversion.idgoal)\n\t\t\t\t\tAND goal.deleted = 0\n\t\t\t\tWHERE log_conversion.idvisit IN ('" . implode("','", $idVisits) . "')\n\t\t\t\t\tAND log_conversion.idgoal > 0\n                ORDER BY log_conversion.idvisit, log_conversion.server_time ASC\n\t\t\t";
        $sql = DbHelper::addMaxExecutionTimeHintToQuery($sql, $this->getLiveQueryMaxExecutionTime());
        try {
            $conversions = $this->getDb()->fetchAll($sql);
        } catch (\Exception $e) {
            $now = Date::now();
            Model::handleMaxExecutionTimeError($this->getDb(), $e, '', $now, $now, null, 0, ['sql' => $sql]);
            throw $e;
        }
        foreach ($conversions as &$conversion) {
            $conversion['goalName'] = Common::unsanitizeInputValue($conversion['goalName']);
        }
        return $conversions;
    }
    public function initProfile($visits, &$profile)
    {
        $profile['totalGoalConversions'] = 0;
        $profile['totalConversionsByGoal'] = [];
    }
    public function handleProfileVisit($visit, &$profile)
    {
        $profile['totalGoalConversions'] += $visit->getColumn('goalConversions');
    }
    public function handleProfileAction($action, &$profile)
    {
        if ($action['type'] != 'goal') {
            return;
        }
        $idGoal = $action['goalId'];
        if (empty($idGoal)) {
            return;
        }
        $idGoalKey = 'idgoal=' . $idGoal;
        if (!isset($profile['totalConversionsByGoal'][$idGoalKey])) {
            $profile['totalConversionsByGoal'][$idGoalKey] = 0;
        }
        ++$profile['totalConversionsByGoal'][$idGoalKey];
        if (!empty($action['revenue'])) {
            if (!isset($profile['totalRevenueByGoal'][$idGoalKey])) {
                $profile['totalRevenueByGoal'][$idGoalKey] = 0;
            }
            $profile['totalRevenueByGoal'][$idGoalKey] += $action['revenue'];
        }
    }
    protected function getReferrerType($referrerTypeId)
    {
        try {
            $referrerType = getReferrerTypeFromShortName($referrerTypeId);
        } catch (\Exception $e) {
            $referrerType = '';
        }
        return $referrerType;
    }
    private function getLiveQueryMaxExecutionTime()
    {
        return Config::getInstance()->General['live_query_max_execution_time'];
    }
}
