<?php

namespace CpChart\Chart;

use CpChart\Data;
use CpChart\Image;
/**
 *  Stock - class to draw stock charts
 *
 *  Version     : 2.1.4
 *  Made by     : Jean-Damien POGOLOTTI
 *  Last Update : 19/01/2014
 *
 *  This file can be distributed under the license you can find at :
 *
 *  http://www.pchart.net/license
 *
 *  You can find the whole class documentation on the pChart web site.
 */
class Stock
{
    /**
     * @var Image
     */
    public $pChartObject;
    /**
     * @var Data
     */
    public $pDataObject;
    /**
     * @param Image $pChartObject
     * @param Data $pDataObject
     */
    public function __construct(Image $pChartObject, Data $pDataObject)
    {
        $this->pChartObject = $pChartObject;
        $this->pDataObject = $pDataObject;
    }
    /**
     * Draw a stock chart
     * @param array $Format
     * @return integer|null
     */
    public function drawStockChart(array $Format = [])
    {
        $SerieOpen = isset($Format["SerieOpen"]) ? $Format["SerieOpen"] : "Open";
        $SerieClose = isset($Format["SerieClose"]) ? $Format["SerieClose"] : "Close";
        $SerieMin = isset($Format["SerieMin"]) ? $Format["SerieMin"] : "Min";
        $SerieMax = isset($Format["SerieMax"]) ? $Format["SerieMax"] : "Max";
        $SerieMedian = isset($Format["SerieMedian"]) ? $Format["SerieMedian"] : null;
        $LineWidth = isset($Format["LineWidth"]) ? $Format["LineWidth"] : 1;
        $LineR = isset($Format["LineR"]) ? $Format["LineR"] : 0;
        $LineG = isset($Format["LineG"]) ? $Format["LineG"] : 0;
        $LineB = isset($Format["LineB"]) ? $Format["LineB"] : 0;
        $LineAlpha = isset($Format["LineAlpha"]) ? $Format["LineAlpha"] : 100;
        $ExtremityWidth = isset($Format["ExtremityWidth"]) ? $Format["ExtremityWidth"] : 1;
        $ExtremityLength = isset($Format["ExtremityLength"]) ? $Format["ExtremityLength"] : 3;
        $ExtremityR = isset($Format["ExtremityR"]) ? $Format["ExtremityR"] : 0;
        $ExtremityG = isset($Format["ExtremityG"]) ? $Format["ExtremityG"] : 0;
        $ExtremityB = isset($Format["ExtremityB"]) ? $Format["ExtremityB"] : 0;
        $ExtremityAlpha = isset($Format["ExtremityAlpha"]) ? $Format["ExtremityAlpha"] : 100;
        $BoxWidth = isset($Format["BoxWidth"]) ? $Format["BoxWidth"] : 8;
        $BoxUpR = isset($Format["BoxUpR"]) ? $Format["BoxUpR"] : 188;
        $BoxUpG = isset($Format["BoxUpG"]) ? $Format["BoxUpG"] : 224;
        $BoxUpB = isset($Format["BoxUpB"]) ? $Format["BoxUpB"] : 46;
        $BoxUpAlpha = isset($Format["BoxUpAlpha"]) ? $Format["BoxUpAlpha"] : 100;
        $BoxUpSurrounding = isset($Format["BoxUpSurrounding"]) ? $Format["BoxUpSurrounding"] : null;
        $BoxUpBorderR = isset($Format["BoxUpBorderR"]) ? $Format["BoxUpBorderR"] : $BoxUpR - 20;
        $BoxUpBorderG = isset($Format["BoxUpBorderG"]) ? $Format["BoxUpBorderG"] : $BoxUpG - 20;
        $BoxUpBorderB = isset($Format["BoxUpBorderB"]) ? $Format["BoxUpBorderB"] : $BoxUpB - 20;
        $BoxUpBorderAlpha = isset($Format["BoxUpBorderAlpha"]) ? $Format["BoxUpBorderAlpha"] : 100;
        $BoxDownR = isset($Format["BoxDownR"]) ? $Format["BoxDownR"] : 224;
        $BoxDownG = isset($Format["BoxDownG"]) ? $Format["BoxDownG"] : 100;
        $BoxDownB = isset($Format["BoxDownB"]) ? $Format["BoxDownB"] : 46;
        $BoxDownAlpha = isset($Format["BoxDownAlpha"]) ? $Format["BoxDownAlpha"] : 100;
        $BoxDownSurrounding = isset($Format["BoxDownSurrounding"]) ? $Format["BoxDownSurrounding"] : null;
        $BoxDownBorderR = isset($Format["BoxDownBorderR"]) ? $Format["BoxDownBorderR"] : $BoxDownR - 20;
        $BoxDownBorderG = isset($Format["BoxDownBorderG"]) ? $Format["BoxDownBorderG"] : $BoxDownG - 20;
        $BoxDownBorderB = isset($Format["BoxDownBorderB"]) ? $Format["BoxDownBorderB"] : $BoxDownB - 20;
        $BoxDownBorderAlpha = isset($Format["BoxDownBorderAlpha"]) ? $Format["BoxDownBorderAlpha"] : 100;
        $ShadowOnBoxesOnly = isset($Format["ShadowOnBoxesOnly"]) ? $Format["ShadowOnBoxesOnly"] : \true;
        $MedianR = isset($Format["MedianR"]) ? $Format["MedianR"] : 255;
        $MedianG = isset($Format["MedianG"]) ? $Format["MedianG"] : 0;
        $MedianB = isset($Format["MedianB"]) ? $Format["MedianB"] : 0;
        $MedianAlpha = isset($Format["MedianAlpha"]) ? $Format["MedianAlpha"] : 100;
        $RecordImageMap = isset($Format["RecordImageMap"]) ? $Format["RecordImageMap"] : \false;
        $ImageMapTitle = isset($Format["ImageMapTitle"]) ? $Format["ImageMapTitle"] : "Stock Chart";
        /* Data Processing */
        if ($BoxUpSurrounding != null) {
            $BoxUpBorderR = $BoxUpR + $BoxUpSurrounding;
            $BoxUpBorderG = $BoxUpG + $BoxUpSurrounding;
            $BoxUpBorderB = $BoxUpB + $BoxUpSurrounding;
        }
        if ($BoxDownSurrounding != null) {
            $BoxDownBorderR = $BoxDownR + $BoxDownSurrounding;
            $BoxDownBorderG = $BoxDownG + $BoxDownSurrounding;
            $BoxDownBorderB = $BoxDownB + $BoxDownSurrounding;
        }
        if ($LineWidth != 1) {
            $LineOffset = $LineWidth / 2;
        }
        $BoxOffset = $BoxWidth / 2;
        $Data = $this->pChartObject->DataSet->getData();
        list($XMargin, $XDivs) = $this->pChartObject->scaleGetXSettings();
        if (!isset($Data["Series"][$SerieOpen]) || !isset($Data["Series"][$SerieClose]) || !isset($Data["Series"][$SerieMin]) || !isset($Data["Series"][$SerieMax])) {
            return STOCK_MISSING_SERIE;
        }
        $Plots = [];
        foreach ($Data["Series"][$SerieOpen]["Data"] as $Key => $Value) {
            $Point = [];
            if (isset($Data["Series"][$SerieClose]["Data"][$Key]) || isset($Data["Series"][$SerieMin]["Data"][$Key]) || isset($Data["Series"][$SerieMax]["Data"][$Key])) {
                $Point = [$Value, $Data["Series"][$SerieClose]["Data"][$Key], $Data["Series"][$SerieMin]["Data"][$Key], $Data["Series"][$SerieMax]["Data"][$Key]];
            }
            if ($SerieMedian != null && isset($Data["Series"][$SerieMedian]["Data"][$Key])) {
                $Point[] = $Data["Series"][$SerieMedian]["Data"][$Key];
            }
            $Plots[] = $Point;
        }
        $AxisID = $Data["Series"][$SerieOpen]["Axis"];
        $Format = $Data["Axis"][$AxisID]["Format"];
        $YZero = $this->pChartObject->scaleComputeY(0, ["AxisID" => $AxisID]);
        $XStep = ($this->pChartObject->GraphAreaX2 - $this->pChartObject->GraphAreaX1 - $XMargin * 2) / $XDivs;
        $X = $this->pChartObject->GraphAreaX1 + $XMargin;
        $Y = $this->pChartObject->GraphAreaY1 + $XMargin;
        $LineSettings = ["R" => $LineR, "G" => $LineG, "B" => $LineB, "Alpha" => $LineAlpha];
        $ExtremitySettings = ["R" => $ExtremityR, "G" => $ExtremityG, "B" => $ExtremityB, "Alpha" => $ExtremityAlpha];
        $BoxUpSettings = ["R" => $BoxUpR, "G" => $BoxUpG, "B" => $BoxUpB, "Alpha" => $BoxUpAlpha, "BorderR" => $BoxUpBorderR, "BorderG" => $BoxUpBorderG, "BorderB" => $BoxUpBorderB, "BorderAlpha" => $BoxUpBorderAlpha];
        $BoxDownSettings = ["R" => $BoxDownR, "G" => $BoxDownG, "B" => $BoxDownB, "Alpha" => $BoxDownAlpha, "BorderR" => $BoxDownBorderR, "BorderG" => $BoxDownBorderG, "BorderB" => $BoxDownBorderB, "BorderAlpha" => $BoxDownBorderAlpha];
        $MedianSettings = ["R" => $MedianR, "G" => $MedianG, "B" => $MedianB, "Alpha" => $MedianAlpha];
        foreach ($Plots as $Key => $Points) {
            $PosArray = $this->pChartObject->scaleComputeY($Points, ["AxisID" => $AxisID]);
            $Values = "Open :" . $Data["Series"][$SerieOpen]["Data"][$Key] . "<BR>Close : " . $Data["Series"][$SerieClose]["Data"][$Key] . "<BR>Min : " . $Data["Series"][$SerieMin]["Data"][$Key] . "<BR>Max : " . $Data["Series"][$SerieMax]["Data"][$Key] . "<BR>";
            if ($SerieMedian != null) {
                $Values = $Values . "Median : " . $Data["Series"][$SerieMedian]["Data"][$Key] . "<BR>";
            }
            if ($PosArray[0] > $PosArray[1]) {
                $ImageMapColor = $this->pChartObject->toHTMLColor($BoxUpR, $BoxUpG, $BoxUpB);
            } else {
                $ImageMapColor = $this->pChartObject->toHTMLColor($BoxDownR, $BoxDownG, $BoxDownB);
            }
            if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
                if ($YZero > $this->pChartObject->GraphAreaY2 - 1) {
                    $YZero = $this->pChartObject->GraphAreaY2 - 1;
                }
                if ($YZero < $this->pChartObject->GraphAreaY1 + 1) {
                    $YZero = $this->pChartObject->GraphAreaY1 + 1;
                }
                if ($XDivs == 0) {
                    $XStep = 0;
                } else {
                    $XStep = ($this->pChartObject->GraphAreaX2 - $this->pChartObject->GraphAreaX1 - $XMargin * 2) / $XDivs;
                }
                if ($ShadowOnBoxesOnly) {
                    $RestoreShadow = $this->pChartObject->Shadow;
                    $this->pChartObject->Shadow = \false;
                }
                if ($LineWidth == 1) {
                    $this->pChartObject->drawLine($X, $PosArray[2], $X, $PosArray[3], $LineSettings);
                } else {
                    $this->pChartObject->drawFilledRectangle($X - $LineOffset, $PosArray[2], $X + $LineOffset, $PosArray[3], $LineSettings);
                }
                if ($ExtremityWidth == 1) {
                    $this->pChartObject->drawLine($X - $ExtremityLength, $PosArray[2], $X + $ExtremityLength, $PosArray[2], $ExtremitySettings);
                    $this->pChartObject->drawLine($X - $ExtremityLength, $PosArray[3], $X + $ExtremityLength, $PosArray[3], $ExtremitySettings);
                    if ($RecordImageMap) {
                        $this->pChartObject->addToImageMap("RECT", sprintf("%s,%s,%s,%s", floor($X - $ExtremityLength), floor($PosArray[2]), floor($X + $ExtremityLength), floor($PosArray[3])), $ImageMapColor, $ImageMapTitle, $Values);
                    }
                } else {
                    $this->pChartObject->drawFilledRectangle($X - $ExtremityLength, $PosArray[2], $X + $ExtremityLength, $PosArray[2] - $ExtremityWidth, $ExtremitySettings);
                    $this->pChartObject->drawFilledRectangle($X - $ExtremityLength, $PosArray[3], $X + $ExtremityLength, $PosArray[3] + $ExtremityWidth, $ExtremitySettings);
                    if ($RecordImageMap) {
                        $this->pChartObject->addToImageMap("RECT", sprintf("%s,%s,%s,%s", floor($X - $ExtremityLength), floor($PosArray[2] - $ExtremityWidth), floor($X + $ExtremityLength), floor($PosArray[3] + $ExtremityWidth)), $ImageMapColor, $ImageMapTitle, $Values);
                    }
                }
                if ($ShadowOnBoxesOnly) {
                    $this->pChartObject->Shadow = $RestoreShadow;
                }
                if ($PosArray[0] > $PosArray[1]) {
                    $this->pChartObject->drawFilledRectangle($X - $BoxOffset, $PosArray[0], $X + $BoxOffset, $PosArray[1], $BoxUpSettings);
                } else {
                    $this->pChartObject->drawFilledRectangle($X - $BoxOffset, $PosArray[0], $X + $BoxOffset, $PosArray[1], $BoxDownSettings);
                }
                if (isset($PosArray[4])) {
                    $this->pChartObject->drawLine($X - $ExtremityLength, $PosArray[4], $X + $ExtremityLength, $PosArray[4], $MedianSettings);
                }
                $X = $X + $XStep;
            } elseif ($Data["Orientation"] == SCALE_POS_TOPBOTTOM) {
                if ($YZero > $this->pChartObject->GraphAreaX2 - 1) {
                    $YZero = $this->pChartObject->GraphAreaX2 - 1;
                }
                if ($YZero < $this->pChartObject->GraphAreaX1 + 1) {
                    $YZero = $this->pChartObject->GraphAreaX1 + 1;
                }
                if ($XDivs == 0) {
                    $XStep = 0;
                } else {
                    $XStep = ($this->pChartObject->GraphAreaY2 - $this->pChartObject->GraphAreaY1 - $XMargin * 2) / $XDivs;
                }
                if ($LineWidth == 1) {
                    $this->pChartObject->drawLine($PosArray[2], $Y, $PosArray[3], $Y, $LineSettings);
                } else {
                    $this->pChartObject->drawFilledRectangle($PosArray[2], $Y - $LineOffset, $PosArray[3], $Y + $LineOffset, $LineSettings);
                }
                if ($ShadowOnBoxesOnly) {
                    $RestoreShadow = $this->pChartObject->Shadow;
                    $this->pChartObject->Shadow = \false;
                }
                if ($ExtremityWidth == 1) {
                    $this->pChartObject->drawLine($PosArray[2], $Y - $ExtremityLength, $PosArray[2], $Y + $ExtremityLength, $ExtremitySettings);
                    $this->pChartObject->drawLine($PosArray[3], $Y - $ExtremityLength, $PosArray[3], $Y + $ExtremityLength, $ExtremitySettings);
                    if ($RecordImageMap) {
                        $this->pChartObject->addToImageMap("RECT", sprintf("%s,%s,%s,%s", floor($PosArray[2]), floor($Y - $ExtremityLength), floor($PosArray[3]), floor($Y + $ExtremityLength)), $ImageMapColor, $ImageMapTitle, $Values);
                    }
                } else {
                    $this->pChartObject->drawFilledRectangle($PosArray[2], $Y - $ExtremityLength, $PosArray[2] - $ExtremityWidth, $Y + $ExtremityLength, $ExtremitySettings);
                    $this->pChartObject->drawFilledRectangle($PosArray[3], $Y - $ExtremityLength, $PosArray[3] + $ExtremityWidth, $Y + $ExtremityLength, $ExtremitySettings);
                    if ($RecordImageMap) {
                        $this->pChartObject->addToImageMap("RECT", sprintf("%s,%s,%s,%s", floor($PosArray[2] - $ExtremityWidth), floor($Y - $ExtremityLength), floor($PosArray[3] + $ExtremityWidth), floor($Y + $ExtremityLength)), $ImageMapColor, $ImageMapTitle, $Values);
                    }
                }
                if ($ShadowOnBoxesOnly) {
                    $this->pChartObject->Shadow = $RestoreShadow;
                }
                if ($PosArray[0] < $PosArray[1]) {
                    $this->pChartObject->drawFilledRectangle($PosArray[0], $Y - $BoxOffset, $PosArray[1], $Y + $BoxOffset, $BoxUpSettings);
                } else {
                    $this->pChartObject->drawFilledRectangle($PosArray[0], $Y - $BoxOffset, $PosArray[1], $Y + $BoxOffset, $BoxDownSettings);
                }
                if (isset($PosArray[4])) {
                    $this->pChartObject->drawLine($PosArray[4], $Y - $ExtremityLength, $PosArray[4], $Y + $ExtremityLength, $MedianSettings);
                }
                $Y = $Y + $XStep;
            }
        }
    }
}
