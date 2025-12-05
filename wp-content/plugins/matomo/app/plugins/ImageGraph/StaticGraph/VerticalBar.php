<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\ImageGraph\StaticGraph;

/**
 *
 */
class VerticalBar extends \Piwik\Plugins\ImageGraph\StaticGraph\GridGraph
{
    public const INTERLEAVE = 0.1;
    public function renderGraph()
    {
        $this->initGridChart($displayVerticalGridLines = \false, $bulletType = LEGEND_FAMILY_BOX, $horizontalGraph = \false, $showTicks = \true, $verticalLegend = \false);
        $this->pImage->drawBarChart(array('Interleave' => self::INTERLEAVE));
    }
}
