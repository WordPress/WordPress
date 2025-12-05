<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\Ecommerce;

use Piwik\Common;
use Piwik\Config;
use Piwik\DataAccess\LogAggregator;
use Piwik\Date;
use Piwik\DbHelper;
use Piwik\Piwik;
use Piwik\Plugins\Ecommerce\Columns\ProductCategory;
use Piwik\Plugins\Live\Model;
use Piwik\Plugins\Live\VisitorDetailsAbstract;
use Piwik\Site;
use Piwik\Tracker\GoalManager;
use Piwik\View;
use function Piwik\Plugins\Referrers\getReferrerTypeFromShortName;
class VisitorDetails extends VisitorDetailsAbstract
{
    public const CATEGORY_COUNT = 5;
    public const DEFAULT_LIFETIME_STAT = array('lifeTimeRevenue' => 0, 'lifeTimeConversions' => 0, 'lifeTimeEcommerceItems' => 0);
    public function extendVisitorDetails(&$visitor)
    {
        if (Site::isEcommerceEnabledFor($visitor['idSite'])) {
            $ecommerceMetrics = $this->queryEcommerceConversionsVisitorLifeTimeMetricsForVisitor($visitor['idSite'], $visitor['visitorId']);
            $visitor['totalEcommerceRevenue'] = $ecommerceMetrics['totalEcommerceRevenue'];
            $visitor['totalEcommerceConversions'] = $ecommerceMetrics['totalEcommerceConversions'];
            $visitor['totalEcommerceItems'] = $ecommerceMetrics['totalEcommerceItems'];
            $visitor['totalAbandonedCartsRevenue'] = $ecommerceMetrics['totalAbandonedCartsRevenue'];
            $visitor['totalAbandonedCarts'] = $ecommerceMetrics['totalAbandonedCarts'];
            $visitor['totalAbandonedCartsItems'] = $ecommerceMetrics['totalAbandonedCartsItems'];
        }
    }
    public function extendActionDetails(&$action, $nextAction, $visitorDetails)
    {
        if (empty($action['productViewName'])) {
            unset($action['productViewName']);
        }
        if (empty($action['productViewSku'])) {
            unset($action['productViewSku']);
        }
        if (empty($action['productViewPrice'])) {
            unset($action['productViewPrice']);
        }
        $categories = [];
        for ($i = 1; $i <= ProductCategory::PRODUCT_CATEGORY_COUNT; $i++) {
            if (!empty($action['productViewCategory' . $i])) {
                $categories[] = $action['productViewCategory' . $i];
            }
            unset($action['productViewCategory' . $i]);
        }
        if (!empty($categories)) {
            $action['productViewCategories'] = $categories;
        }
    }
    public function renderActionTooltip($action, $visitInfo)
    {
        if (!isset($action['productViewName']) && !isset($action['productViewSku']) && !isset($action['productViewPrice']) && !isset($action['productViewCategories'])) {
            return [];
        }
        $view = new View('@Ecommerce/_actionTooltip');
        $view->sendHeadersWhenRendering = \false;
        $view->action = $action;
        $view->visitInfo = $visitInfo;
        return [[15, $view->render()]];
    }
    public function provideActionsForVisitIds(&$actions, $idVisits)
    {
        $ecommerceDetails = $this->queryEcommerceConversionsForVisits($idVisits);
        // use while / array_shift combination instead of foreach to save memory
        while (is_array($ecommerceDetails) && count($ecommerceDetails)) {
            $ecommerceDetail = array_shift($ecommerceDetails);
            $idVisit = $ecommerceDetail['idvisit'];
            unset($ecommerceDetail['idvisit']);
            if ($ecommerceDetail['type'] == Piwik::LABEL_ID_GOAL_IS_ECOMMERCE_CART) {
                unset($ecommerceDetail['orderId']);
                unset($ecommerceDetail['revenueSubTotal']);
                unset($ecommerceDetail['revenueTax']);
                unset($ecommerceDetail['revenueShipping']);
                unset($ecommerceDetail['revenueDiscount']);
            }
            $ecommerceDetail['referrerType'] = $this->getReferrerType($ecommerceDetail['referrerType']);
            // 25.00 => 25
            foreach ($ecommerceDetail as $column => $value) {
                if (strpos($column, 'revenue') !== \false) {
                    if (!is_numeric($value)) {
                        $ecommerceDetail[$column] = 0;
                    } elseif ($value == round($value)) {
                        $ecommerceDetail[$column] = round($value);
                    }
                }
            }
            $idOrder = isset($ecommerceDetail['orderId']) ? $ecommerceDetail['orderId'] : GoalManager::ITEM_IDORDER_ABANDONED_CART;
            $itemsDetails = $this->queryEcommerceItemsForOrder($idVisit, $idOrder);
            foreach ($itemsDetails as &$detail) {
                if ($detail['price'] == round($detail['price'])) {
                    $detail['price'] = round($detail['price']);
                }
            }
            $ecommerceDetail['itemDetails'] = $itemsDetails;
            $actions[$idVisit][] = $ecommerceDetail;
        }
    }
    /**
     * @param $idSite
     * @param $idVisitor
     * @return array
     * @throws \Exception
     */
    protected function queryEcommerceConversionsVisitorLifeTimeMetricsForVisitor($idSite, $idVisitor)
    {
        $sql = $this->getSqlEcommerceConversionsLifeTimeMetricsForIdGoal();
        $lifeTimeStats = $this->getDb()->fetchAll($sql, array($idSite, @Common::hex2bin($idVisitor)));
        $defaultStats = array_fill_keys([GoalManager::IDGOAL_CART, GoalManager::IDGOAL_ORDER], self::DEFAULT_LIFETIME_STAT);
        $lifeTimeStatsByGoal = array_reduce($lifeTimeStats, function ($carry, $statRow) {
            $idgoal = $statRow['idgoal'];
            $carry[$idgoal] = array_merge($carry[$idgoal], $statRow);
            return $carry;
        }, $defaultStats);
        $ecommerceOrders = $lifeTimeStatsByGoal[GoalManager::IDGOAL_ORDER];
        $abandonedCarts = $lifeTimeStatsByGoal[GoalManager::IDGOAL_CART];
        return array('totalEcommerceRevenue' => $ecommerceOrders['lifeTimeRevenue'], 'totalEcommerceConversions' => $ecommerceOrders['lifeTimeConversions'], 'totalEcommerceItems' => $ecommerceOrders['lifeTimeEcommerceItems'], 'totalAbandonedCartsRevenue' => $abandonedCarts['lifeTimeRevenue'], 'totalAbandonedCarts' => $abandonedCarts['lifeTimeConversions'], 'totalAbandonedCartsItems' => $abandonedCarts['lifeTimeEcommerceItems']);
    }
    /**
     * Returns and SQL string that queries for `lifeTimeRevenue`, `lifeTimeConversions`, and `lifeTimeEcommerceItems` grouped by
     * `idgoal` for abandoned carts and orders.
     * @return string
     */
    protected function getSqlEcommerceConversionsLifeTimeMetricsForIdGoal()
    {
        $sql = "SELECT\n                    idgoal,\n                    COALESCE(SUM(" . LogAggregator::getSqlRevenue('revenue') . "), 0) as lifeTimeRevenue,\n                    COUNT(*) as lifeTimeConversions,\n                    COALESCE(SUM(" . LogAggregator::getSqlRevenue('items') . "), 0)  as lifeTimeEcommerceItems\n\t\t\t\t\tFROM  " . Common::prefixTable('log_visit') . " AS log_visit\n\t\t\t\t\t    STRAIGHT_JOIN " . Common::prefixTable('log_conversion') . " AS log_conversion\n\t\t\t\t\t    ON log_visit.idvisit = log_conversion.idvisit\n\t\t\t\t\tWHERE\n\t\t\t\t\t        log_visit.idsite = ?\n\t\t\t\t\t    AND log_visit.idvisitor = ?\n\t\t\t\t\t\tAND log_conversion.idgoal IN ( " . GoalManager::IDGOAL_CART . ", " . GoalManager::IDGOAL_ORDER . " )\n                    GROUP BY idgoal\n        ";
        return $sql;
    }
    /**
     * @param $idVisit
     * @param $limit
     * @return array
     * @throws \Exception
     */
    protected function queryEcommerceConversionsForVisits($idVisits)
    {
        $sql = "SELECT\n\t\t\t\t\t\tlog_conversion.idvisit,\n\t\t\t\t\t\tcase idgoal when " . GoalManager::IDGOAL_CART . " then '" . Piwik::LABEL_ID_GOAL_IS_ECOMMERCE_CART . "' else '" . Piwik::LABEL_ID_GOAL_IS_ECOMMERCE_ORDER . "' end as type,\n\t\t\t\t\t\tidorder as orderId,\n\t\t\t\t\t\t" . LogAggregator::getSqlRevenue('revenue') . " as revenue,\n\t\t\t\t\t\t" . LogAggregator::getSqlRevenue('revenue_subtotal') . " as revenueSubTotal,\n\t\t\t\t\t\t" . LogAggregator::getSqlRevenue('revenue_tax') . " as revenueTax,\n\t\t\t\t\t\t" . LogAggregator::getSqlRevenue('revenue_shipping') . " as revenueShipping,\n\t\t\t\t\t\t" . LogAggregator::getSqlRevenue('revenue_discount') . " as revenueDiscount,\n\t\t\t\t\t\titems as items,\n\t\t\t\t\t\tlog_conversion.server_time as serverTimePretty,\n\t\t\t\t\t\tlog_conversion.idlink_va,\n\t\t\t\t\t\tlog_link_visit_action.idpageview,\n\t\t\t\t\t\tlog_conversion.referer_type as referrerType,\n\t\t\t\t\t\tlog_conversion.referer_name as referrerName,\n\t\t\t\t\t\tlog_conversion.referer_keyword as referrerKeyword\n\t\t\t\t\tFROM " . Common::prefixTable('log_conversion') . " AS log_conversion\n\t\t       LEFT JOIN " . Common::prefixTable('log_link_visit_action') . " AS log_link_visit_action\n\t\t              ON log_link_visit_action.idlink_va = log_conversion.idlink_va\n\t\t\t\t\tWHERE log_conversion.idvisit IN ('" . implode("','", $idVisits) . "')\n\t\t\t\t\t\tAND idgoal <= " . GoalManager::IDGOAL_ORDER . "\n\t\t\t\t\tORDER BY log_conversion.idvisit, log_conversion.server_time ASC";
        $sql = DbHelper::addMaxExecutionTimeHintToQuery($sql, $this->getLiveQueryMaxExecutionTime());
        try {
            $ecommerceDetails = $this->getDb()->fetchAll($sql);
        } catch (\Exception $e) {
            $now = Date::now();
            Model::handleMaxExecutionTimeError($this->getDb(), $e, '', $now, $now, null, 0, ['sql' => $sql]);
            throw $e;
        }
        return $ecommerceDetails;
    }
    /**
     * @param $idVisit
     * @param $idOrder
     * @param $actionsLimit
     * @return array
     * @throws \Exception
     */
    protected function queryEcommerceItemsForOrder($idVisit, $idOrder)
    {
        $categorySelects = [];
        $categoryJoins = [];
        for ($i = 0; $i < self::CATEGORY_COUNT; ++$i) {
            $suffix = $i === 0 ? '' : $i;
            $column = $i === 0 ? 'idaction_category' : 'idaction_category' . ($i + 1);
            $categorySelects[] = 'log_action_category' . $suffix . '.name as itemCategory' . $suffix;
            $categoryJoins[] = 'LEFT JOIN ' . Common::prefixTable('log_action') . " AS log_action_category{$suffix}\n                                       ON {$column} = log_action_category{$suffix}.idaction";
        }
        $categorySelects = implode(',', $categorySelects);
        $categoryJoins = implode("\n", $categoryJoins);
        $sql = "SELECT\n\t\t\t\t\t\t\tlog_action_sku.name as itemSKU,\n\t\t\t\t\t\t\tlog_action_name.name as itemName,\n\t\t\t\t\t\t\t{$categorySelects},\n\t\t\t\t\t\t\t" . LogAggregator::getSqlRevenue('price') . " as price,\n\t\t\t\t\t\t\tquantity as quantity\n\t\t\t\t\t\tFROM " . Common::prefixTable('log_conversion_item') . "\n\t\t\t\t\t\t\tINNER JOIN " . Common::prefixTable('log_action') . " AS log_action_sku\n\t\t\t\t\t\t\tON  idaction_sku = log_action_sku.idaction\n\t\t\t\t\t\t\tLEFT JOIN " . Common::prefixTable('log_action') . " AS log_action_name\n\t\t\t\t\t\t\tON  idaction_name = log_action_name.idaction\n\t\t\t\t\t\t\t{$categoryJoins}\n\t\t\t\t\t\tWHERE idvisit = ?\n\t\t\t\t\t\t\tAND idorder = ?\n\t\t\t\t\t\t\tAND deleted = 0\n\t\t\t\t";
        $bind = array($idVisit, $idOrder);
        $itemsDetails = $this->getDb()->fetchAll($sql, $bind);
        // create categories array for each item
        foreach ($itemsDetails as &$item) {
            $categories = [];
            for ($i = 0; $i < self::CATEGORY_COUNT; ++$i) {
                $suffix = $i === 0 ? '' : $i;
                if (empty($item['itemCategory' . $suffix])) {
                    continue;
                }
                $categories[] = trim($item['itemCategory' . $suffix]);
            }
            $item['categories'] = array_filter($categories);
            // remove itemCategotyN properties, except 'itemCategory' property for BC
            for ($i = 1; $i < self::CATEGORY_COUNT; ++$i) {
                unset($item['itemCategory' . $i]);
            }
        }
        return $itemsDetails;
    }
    public function initProfile($visits, &$profile)
    {
        if (Site::isEcommerceEnabledFor($visits->getFirstRow()->getColumn('idSite'))) {
            $profile['totalEcommerceRevenue'] = 0;
            $profile['totalEcommerceConversions'] = 0;
            $profile['totalEcommerceItems'] = 0;
            $profile['totalAbandonedCarts'] = 0;
            $profile['totalAbandonedCartsRevenue'] = 0;
            $profile['totalAbandonedCartsItems'] = 0;
        }
    }
    public function finalizeProfile($visits, &$profile)
    {
        $lastVisit = $visits->getLastRow();
        if ($lastVisit && Site::isEcommerceEnabledFor($lastVisit->getColumn('idSite'))) {
            $profile['totalEcommerceRevenue'] = $lastVisit->getColumn('totalEcommerceRevenue');
            $profile['totalEcommerceConversions'] = $lastVisit->getColumn('totalEcommerceConversions');
            $profile['totalEcommerceItems'] = $lastVisit->getColumn('totalEcommerceItems');
            $profile['totalAbandonedCartsRevenue'] = $lastVisit->getColumn('totalAbandonedCartsRevenue');
            $profile['totalAbandonedCarts'] = $lastVisit->getColumn('totalAbandonedCarts');
            $profile['totalAbandonedCartsItems'] = $lastVisit->getColumn('totalAbandonedCartsItems');
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
