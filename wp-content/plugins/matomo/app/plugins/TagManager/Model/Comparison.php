<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Model;

use Exception;
use Piwik\Piwik;
class Comparison
{
    public const ID_EQUALS = 'equals';
    public const ID_EQUALS_EXACTLY = 'equals_exactly';
    public const ID_CONTAINS = 'contains';
    public const ID_STARTS_WITH = 'starts_with';
    public const ID_ENDS_WITH = 'ends_with';
    public function checkIsValidComparison($comparisonId)
    {
        $comparisons = $this->getSupportedComparisons();
        foreach ($comparisons as $comparison) {
            if ($comparison['id'] === $comparisonId) {
                return;
            }
        }
        throw new Exception(Piwik::translate('TagManager_ErrorComparisonNotSupported', $comparisonId));
    }
    /**
     * @return array
     */
    public function getSupportedComparisons()
    {
        $comparisons = array(['id' => self::ID_EQUALS, 'name' => Piwik::translate('TagManager_ComparisonEquals')], ['id' => self::ID_EQUALS_EXACTLY, 'name' => Piwik::translate('TagManager_ComparisonEqualsExactly')], ['id' => self::ID_CONTAINS, 'name' => Piwik::translate('TagManager_ComparisonContains')], ['id' => self::ID_STARTS_WITH, 'name' => Piwik::translate('TagManager_ComparisonStartsWith')], ['id' => self::ID_ENDS_WITH, 'name' => Piwik::translate('TagManager_ComparisonEndsWith')], ['id' => 'lower_than', 'name' => Piwik::translate('TagManager_ComparisonLowerThan')], ['id' => 'lower_than_or_equals', 'name' => Piwik::translate('TagManager_ComparisonLowerThanOrEqual')], ['id' => 'greater_than', 'name' => Piwik::translate('TagManager_ComparisonGreaterThan')], ['id' => 'greater_than_or_equals', 'name' => Piwik::translate('TagManager_ComparisonGreaterThanOrEqual')], ['id' => 'regexp', 'name' => Piwik::translate('TagManager_ComparisonMatchesRegexp')], ['id' => 'regexp_ignore_case', 'name' => Piwik::translate('TagManager_ComparisonMatchesRegexp') . ' (' . Piwik::translate('TagManager_ComparisonIgnoreCase') . ')'], ['id' => 'match_css_selector', 'name' => Piwik::translate('TagManager_ComparisonMatchesCssSelector')]);
        $allComparisons = [];
        foreach ($comparisons as $comparison) {
            $allComparisons[] = $comparison;
            $allComparisons[] = array('id' => 'not_' . $comparison['id'], 'name' => Piwik::translate('TagManager_ComparisonNotX', $comparison['name']));
        }
        return $allComparisons;
    }
}
