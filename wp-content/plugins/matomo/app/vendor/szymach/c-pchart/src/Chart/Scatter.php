<?php

namespace CpChart\Chart;

use CpChart\Data;
use CpChart\Image;
use Exception;
/**
 *  Scatter - class to draw scatter charts
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
class Scatter
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
     * Prepare the scale
     * @param array $Format
     * @return null|int
     * @throws Exception
     */
    public function drawScatterScale(array $Format = [])
    {
        $Mode = isset($Format["Mode"]) ? $Format["Mode"] : SCALE_MODE_FLOATING;
        $Floating = isset($Format["Floating"]) ? $Format["Floating"] : \false;
        $XLabelsRotation = isset($Format["XLabelsRotation"]) ? $Format["XLabelsRotation"] : 90;
        $MinDivHeight = isset($Format["MinDivHeight"]) ? $Format["MinDivHeight"] : 20;
        $Factors = isset($Format["Factors"]) ? $Format["Factors"] : [1, 2, 5];
        $ManualScale = isset($Format["ManualScale"]) ? $Format["ManualScale"] : ["0" => ["Min" => -100, "Max" => 100]];
        $XMargin = isset($Format["XMargin"]) ? $Format["XMargin"] : 0;
        $YMargin = isset($Format["YMargin"]) ? $Format["YMargin"] : 0;
        $ScaleSpacing = isset($Format["ScaleSpacing"]) ? $Format["ScaleSpacing"] : 15;
        $InnerTickWidth = isset($Format["InnerTickWidth"]) ? $Format["InnerTickWidth"] : 2;
        $OuterTickWidth = isset($Format["OuterTickWidth"]) ? $Format["OuterTickWidth"] : 2;
        $DrawXLines = isset($Format["DrawXLines"]) ? $Format["DrawXLines"] : ALL;
        $DrawYLines = isset($Format["DrawYLines"]) ? $Format["DrawYLines"] : ALL;
        $GridTicks = isset($Format["GridTicks"]) ? $Format["GridTicks"] : 4;
        $GridR = isset($Format["GridR"]) ? $Format["GridR"] : 255;
        $GridG = isset($Format["GridG"]) ? $Format["GridG"] : 255;
        $GridB = isset($Format["GridB"]) ? $Format["GridB"] : 255;
        $GridAlpha = isset($Format["GridAlpha"]) ? $Format["GridAlpha"] : 40;
        $AxisRo = isset($Format["AxisR"]) ? $Format["AxisR"] : 0;
        $AxisGo = isset($Format["AxisG"]) ? $Format["AxisG"] : 0;
        $AxisBo = isset($Format["AxisB"]) ? $Format["AxisB"] : 0;
        $AxisAlpha = isset($Format["AxisAlpha"]) ? $Format["AxisAlpha"] : 100;
        $TickRo = isset($Format["TickR"]) ? $Format["TickR"] : 0;
        $TickGo = isset($Format["TickG"]) ? $Format["TickG"] : 0;
        $TickBo = isset($Format["TickB"]) ? $Format["TickB"] : 0;
        $TickAlpha = isset($Format["TickAlpha"]) ? $Format["TickAlpha"] : 100;
        $DrawSubTicks = isset($Format["DrawSubTicks"]) ? $Format["DrawSubTicks"] : \false;
        $InnerSubTickWidth = isset($Format["InnerSubTickWidth"]) ? $Format["InnerSubTickWidth"] : 0;
        $OuterSubTickWidth = isset($Format["OuterSubTickWidth"]) ? $Format["OuterSubTickWidth"] : 2;
        $SubTickR = isset($Format["SubTickR"]) ? $Format["SubTickR"] : 255;
        $SubTickG = isset($Format["SubTickG"]) ? $Format["SubTickG"] : 0;
        $SubTickB = isset($Format["SubTickB"]) ? $Format["SubTickB"] : 0;
        $SubTickAlpha = isset($Format["SubTickAlpha"]) ? $Format["SubTickAlpha"] : 100;
        $XReleasePercent = isset($Format["XReleasePercent"]) ? $Format["XReleasePercent"] : 1;
        $DrawArrows = isset($Format["DrawArrows"]) ? $Format["DrawArrows"] : \false;
        $ArrowSize = isset($Format["ArrowSize"]) ? $Format["ArrowSize"] : 8;
        $CycleBackground = isset($Format["CycleBackground"]) ? $Format["CycleBackground"] : \false;
        $BackgroundR1 = isset($Format["BackgroundR1"]) ? $Format["BackgroundR1"] : 255;
        $BackgroundG1 = isset($Format["BackgroundG1"]) ? $Format["BackgroundG1"] : 255;
        $BackgroundB1 = isset($Format["BackgroundB1"]) ? $Format["BackgroundB1"] : 255;
        $BackgroundAlpha1 = isset($Format["BackgroundAlpha1"]) ? $Format["BackgroundAlpha1"] : 10;
        $BackgroundR2 = isset($Format["BackgroundR2"]) ? $Format["BackgroundR2"] : 230;
        $BackgroundG2 = isset($Format["BackgroundG2"]) ? $Format["BackgroundG2"] : 230;
        $BackgroundB2 = isset($Format["BackgroundB2"]) ? $Format["BackgroundB2"] : 230;
        $BackgroundAlpha2 = isset($Format["BackgroundAlpha2"]) ? $Format["BackgroundAlpha2"] : 10;
        /* Check if we have at least both one X and Y axis */
        $GotXAxis = \false;
        $GotYAxis = \false;
        foreach ($this->pDataObject->Data["Axis"] as $AxisID => $AxisSettings) {
            if ($AxisSettings["Identity"] == AXIS_X) {
                $GotXAxis = \true;
            }
            if ($AxisSettings["Identity"] == AXIS_Y) {
                $GotYAxis = \true;
            }
        }
        if (!$GotXAxis) {
            return SCATTER_MISSING_X_SERIE;
        }
        if (!$GotYAxis) {
            return SCATTER_MISSING_Y_SERIE;
        }
        /* Skip a NOTICE event in case of an empty array */
        if ($DrawYLines == NONE) {
            $DrawYLines = ["zarma" => "31"];
        }
        $Data = $this->pDataObject->getData();
        foreach ($Data["Axis"] as $AxisID => $AxisSettings) {
            if ($AxisSettings["Identity"] == AXIS_X) {
                $Width = $this->pChartObject->GraphAreaX2 - $this->pChartObject->GraphAreaX1 - $XMargin * 2;
            } else {
                $Width = $this->pChartObject->GraphAreaY2 - $this->pChartObject->GraphAreaY1 - $YMargin * 2;
            }
            $AxisMin = ABSOLUTE_MAX;
            $AxisMax = OUT_OF_SIGHT;
            if ($Mode == SCALE_MODE_FLOATING) {
                foreach ($Data["Series"] as $SerieID => $SerieParameter) {
                    if ($SerieParameter["Axis"] == $AxisID && $Data["Series"][$SerieID]["isDrawable"]) {
                        $AxisMax = max($AxisMax, $Data["Series"][$SerieID]["Max"]);
                        $AxisMin = min($AxisMin, $Data["Series"][$SerieID]["Min"]);
                    }
                }
                $AutoMargin = ($AxisMax - $AxisMin) / 100 * $XReleasePercent;
                $Data["Axis"][$AxisID]["Min"] = $AxisMin - $AutoMargin;
                $Data["Axis"][$AxisID]["Max"] = $AxisMax + $AutoMargin;
            } elseif ($Mode == SCALE_MODE_MANUAL) {
                if (isset($ManualScale[$AxisID]["Min"]) && isset($ManualScale[$AxisID]["Max"])) {
                    $Data["Axis"][$AxisID]["Min"] = $ManualScale[$AxisID]["Min"];
                    $Data["Axis"][$AxisID]["Max"] = $ManualScale[$AxisID]["Max"];
                } else {
                    throw new Exception("Manual scale boundaries not set.");
                }
            }
            /* Full manual scale */
            if (isset($ManualScale[$AxisID]["Rows"]) && isset($ManualScale[$AxisID]["RowHeight"])) {
                $Scale = ["Rows" => $ManualScale[$AxisID]["Rows"], "RowHeight" => $ManualScale[$AxisID]["RowHeight"], "XMin" => $ManualScale[$AxisID]["Min"], "XMax" => $ManualScale[$AxisID]["Max"]];
            } else {
                $MaxDivs = floor($Width / $MinDivHeight);
                $Scale = $this->pChartObject->computeScale($Data["Axis"][$AxisID]["Min"], $Data["Axis"][$AxisID]["Max"], $MaxDivs, $Factors, $AxisID);
            }
            $Data["Axis"][$AxisID]["Margin"] = $AxisSettings["Identity"] == AXIS_X ? $XMargin : $YMargin;
            $Data["Axis"][$AxisID]["ScaleMin"] = $Scale["XMin"];
            $Data["Axis"][$AxisID]["ScaleMax"] = $Scale["XMax"];
            $Data["Axis"][$AxisID]["Rows"] = $Scale["Rows"];
            $Data["Axis"][$AxisID]["RowHeight"] = $Scale["RowHeight"];
            if (isset($Scale["Format"])) {
                $Data["Axis"][$AxisID]["Format"] = $Scale["Format"];
            }
            if (!isset($Data["Axis"][$AxisID]["Display"])) {
                $Data["Axis"][$AxisID]["Display"] = null;
            }
            if (!isset($Data["Axis"][$AxisID]["Format"])) {
                $Data["Axis"][$AxisID]["Format"] = null;
            }
            if (!isset($Data["Axis"][$AxisID]["Unit"])) {
                $Data["Axis"][$AxisID]["Unit"] = null;
            }
        }
        /* Get the default font color */
        $FontColorRo = $this->pChartObject->FontColorR;
        $FontColorGo = $this->pChartObject->FontColorG;
        $FontColorBo = $this->pChartObject->FontColorB;
        /* Set the original boundaries */
        $AxisPos["L"] = $this->pChartObject->GraphAreaX1;
        $AxisPos["R"] = $this->pChartObject->GraphAreaX2;
        $AxisPos["T"] = $this->pChartObject->GraphAreaY1;
        $AxisPos["B"] = $this->pChartObject->GraphAreaY2;
        foreach ($Data["Axis"] as $AxisID => $AxisSettings) {
            if (isset($AxisSettings["Color"])) {
                $AxisR = $AxisSettings["Color"]["R"];
                $AxisG = $AxisSettings["Color"]["G"];
                $AxisB = $AxisSettings["Color"]["B"];
                $TickR = $AxisSettings["Color"]["R"];
                $TickG = $AxisSettings["Color"]["G"];
                $TickB = $AxisSettings["Color"]["B"];
                $this->pChartObject->setFontProperties(["R" => $AxisSettings["Color"]["R"], "G" => $AxisSettings["Color"]["G"], "B" => $AxisSettings["Color"]["B"]]);
            } else {
                $AxisR = $AxisRo;
                $AxisG = $AxisGo;
                $AxisB = $AxisBo;
                $TickR = $TickRo;
                $TickG = $TickGo;
                $TickB = $TickBo;
                $this->pChartObject->setFontProperties(["R" => $FontColorRo, "G" => $FontColorGo, "B" => $FontColorBo]);
            }
            if ($AxisSettings["Identity"] == AXIS_X) {
                if ($AxisSettings["Position"] == AXIS_POSITION_BOTTOM) {
                    if ($XLabelsRotation == 0) {
                        $LabelAlign = TEXT_ALIGN_TOPMIDDLE;
                        $LabelOffset = 2;
                    }
                    if ($XLabelsRotation > 0 && $XLabelsRotation < 190) {
                        $LabelAlign = TEXT_ALIGN_MIDDLERIGHT;
                        $LabelOffset = 5;
                    }
                    if ($XLabelsRotation == 180) {
                        $LabelAlign = TEXT_ALIGN_BOTTOMMIDDLE;
                        $LabelOffset = 5;
                    }
                    if ($XLabelsRotation > 180 && $XLabelsRotation < 360) {
                        $LabelAlign = TEXT_ALIGN_MIDDLELEFT;
                        $LabelOffset = 2;
                    }
                    if ($Floating) {
                        $FloatingOffset = $YMargin;
                        $this->pChartObject->drawLine($this->pChartObject->GraphAreaX1 + $AxisSettings["Margin"], $AxisPos["B"], $this->pChartObject->GraphAreaX2 - $AxisSettings["Margin"], $AxisPos["B"], ["R" => $AxisR, "G" => $AxisG, "B" => $AxisB, "Alpha" => $AxisAlpha]);
                    } else {
                        $FloatingOffset = 0;
                        $this->pChartObject->drawLine($this->pChartObject->GraphAreaX1, $AxisPos["B"], $this->pChartObject->GraphAreaX2, $AxisPos["B"], ["R" => $AxisR, "G" => $AxisG, "B" => $AxisB, "Alpha" => $AxisAlpha]);
                    }
                    if ($DrawArrows) {
                        $this->pChartObject->drawArrow($this->pChartObject->GraphAreaX2 - $AxisSettings["Margin"], $AxisPos["B"], $this->pChartObject->GraphAreaX2 + $ArrowSize * 2, $AxisPos["B"], ["FillR" => $AxisR, "FillG" => $AxisG, "FillB" => $AxisB, "Size" => $ArrowSize]);
                    }
                    $Width = $this->pChartObject->GraphAreaX2 - $this->pChartObject->GraphAreaX1 - $AxisSettings["Margin"] * 2;
                    $Step = $Width / $AxisSettings["Rows"];
                    $SubTicksSize = $Step / 2;
                    $MaxBottom = $AxisPos["B"];
                    $LastX = null;
                    for ($i = 0; $i <= $AxisSettings["Rows"]; $i++) {
                        $XPos = $this->pChartObject->GraphAreaX1 + $AxisSettings["Margin"] + $Step * $i;
                        $YPos = $AxisPos["B"];
                        $Value = $this->pChartObject->scaleFormat($AxisSettings["ScaleMin"] + $AxisSettings["RowHeight"] * $i, $AxisSettings["Display"], $AxisSettings["Format"], $AxisSettings["Unit"]);
                        if ($i % 2 == 1) {
                            $BGColor = ["R" => $BackgroundR1, "G" => $BackgroundG1, "B" => $BackgroundB1, "Alpha" => $BackgroundAlpha1];
                        } else {
                            $BGColor = ["R" => $BackgroundR2, "G" => $BackgroundG2, "B" => $BackgroundB2, "Alpha" => $BackgroundAlpha2];
                        }
                        if ($LastX != null && $CycleBackground && ($DrawXLines == ALL || in_array($AxisID, $DrawXLines))) {
                            $this->pChartObject->drawFilledRectangle($LastX, $this->pChartObject->GraphAreaY1 + $FloatingOffset, $XPos, $this->pChartObject->GraphAreaY2 - $FloatingOffset, $BGColor);
                        }
                        if ($DrawXLines == ALL || in_array($AxisID, $DrawXLines)) {
                            $this->pChartObject->drawLine($XPos, $this->pChartObject->GraphAreaY1 + $FloatingOffset, $XPos, $this->pChartObject->GraphAreaY2 - $FloatingOffset, ["R" => $GridR, "G" => $GridG, "B" => $GridB, "Alpha" => $GridAlpha, "Ticks" => $GridTicks]);
                        }
                        if ($DrawSubTicks && $i != $AxisSettings["Rows"]) {
                            $this->pChartObject->drawLine($XPos + $SubTicksSize, $YPos - $InnerSubTickWidth, $XPos + $SubTicksSize, $YPos + $OuterSubTickWidth, ["R" => $SubTickR, "G" => $SubTickG, "B" => $SubTickB, "Alpha" => $SubTickAlpha]);
                        }
                        $this->pChartObject->drawLine($XPos, $YPos - $InnerTickWidth, $XPos, $YPos + $OuterTickWidth, ["R" => $TickR, "G" => $TickG, "B" => $TickB, "Alpha" => $TickAlpha]);
                        $Bounds = $this->pChartObject->drawText($XPos, $YPos + $OuterTickWidth + $LabelOffset, $Value, ["Angle" => $XLabelsRotation, "Align" => $LabelAlign]);
                        $TxtBottom = $YPos + 2 + $OuterTickWidth + 2 + ($Bounds[0]["Y"] - $Bounds[2]["Y"]);
                        $MaxBottom = max($MaxBottom, $TxtBottom);
                        $LastX = $XPos;
                    }
                    if (isset($AxisSettings["Name"])) {
                        $YPos = $MaxBottom + 2;
                        $XPos = $this->pChartObject->GraphAreaX1 + ($this->pChartObject->GraphAreaX2 - $this->pChartObject->GraphAreaX1) / 2;
                        $Bounds = $this->pChartObject->drawText($XPos, $YPos, $AxisSettings["Name"], ["Align" => TEXT_ALIGN_TOPMIDDLE]);
                        $MaxBottom = $Bounds[0]["Y"];
                        $this->pDataObject->Data["GraphArea"]["Y2"] = $MaxBottom + $this->pChartObject->FontSize;
                    }
                    $AxisPos["B"] = $MaxBottom + $ScaleSpacing;
                } elseif ($AxisSettings["Position"] == AXIS_POSITION_TOP) {
                    if ($XLabelsRotation == 0) {
                        $LabelAlign = TEXT_ALIGN_BOTTOMMIDDLE;
                        $LabelOffset = 2;
                    }
                    if ($XLabelsRotation > 0 && $XLabelsRotation < 190) {
                        $LabelAlign = TEXT_ALIGN_MIDDLELEFT;
                        $LabelOffset = 2;
                    }
                    if ($XLabelsRotation == 180) {
                        $LabelAlign = TEXT_ALIGN_TOPMIDDLE;
                        $LabelOffset = 5;
                    }
                    if ($XLabelsRotation > 180 && $XLabelsRotation < 360) {
                        $LabelAlign = TEXT_ALIGN_MIDDLERIGHT;
                        $LabelOffset = 5;
                    }
                    if ($Floating) {
                        $FloatingOffset = $YMargin;
                        $this->pChartObject->drawLine($this->pChartObject->GraphAreaX1 + $AxisSettings["Margin"], $AxisPos["T"], $this->pChartObject->GraphAreaX2 - $AxisSettings["Margin"], $AxisPos["T"], ["R" => $AxisR, "G" => $AxisG, "B" => $AxisB, "Alpha" => $AxisAlpha]);
                    } else {
                        $FloatingOffset = 0;
                        $this->pChartObject->drawLine($this->pChartObject->GraphAreaX1, $AxisPos["T"], $this->pChartObject->GraphAreaX2, $AxisPos["T"], ["R" => $AxisR, "G" => $AxisG, "B" => $AxisB, "Alpha" => $AxisAlpha]);
                    }
                    if ($DrawArrows) {
                        $this->pChartObject->drawArrow($this->pChartObject->GraphAreaX2 - $AxisSettings["Margin"], $AxisPos["T"], $this->pChartObject->GraphAreaX2 + $ArrowSize * 2, $AxisPos["T"], ["FillR" => $AxisR, "FillG" => $AxisG, "FillB" => $AxisB, "Size" => $ArrowSize]);
                    }
                    $Width = $this->pChartObject->GraphAreaX2 - $this->pChartObject->GraphAreaX1 - $AxisSettings["Margin"] * 2;
                    $Step = $Width / $AxisSettings["Rows"];
                    $SubTicksSize = $Step / 2;
                    $MinTop = $AxisPos["T"];
                    $LastX = null;
                    for ($i = 0; $i <= $AxisSettings["Rows"]; $i++) {
                        $XPos = $this->pChartObject->GraphAreaX1 + $AxisSettings["Margin"] + $Step * $i;
                        $YPos = $AxisPos["T"];
                        $Value = $this->pChartObject->scaleFormat($AxisSettings["ScaleMin"] + $AxisSettings["RowHeight"] * $i, $AxisSettings["Display"], $AxisSettings["Format"], $AxisSettings["Unit"]);
                        if ($i % 2 == 1) {
                            $BGColor = ["R" => $BackgroundR1, "G" => $BackgroundG1, "B" => $BackgroundB1, "Alpha" => $BackgroundAlpha1];
                        } else {
                            $BGColor = ["R" => $BackgroundR2, "G" => $BackgroundG2, "B" => $BackgroundB2, "Alpha" => $BackgroundAlpha2];
                        }
                        if ($LastX != null && $CycleBackground && ($DrawXLines == ALL || in_array($AxisID, $DrawXLines))) {
                            $this->pChartObject->drawFilledRectangle($LastX, $this->pChartObject->GraphAreaY1 + $FloatingOffset, $XPos, $this->pChartObject->GraphAreaY2 - $FloatingOffset, $BGColor);
                        }
                        if ($DrawXLines == ALL || in_array($AxisID, $DrawXLines)) {
                            $this->pChartObject->drawLine($XPos, $this->pChartObject->GraphAreaY1 + $FloatingOffset, $XPos, $this->pChartObject->GraphAreaY2 - $FloatingOffset, ["R" => $GridR, "G" => $GridG, "B" => $GridB, "Alpha" => $GridAlpha, "Ticks" => $GridTicks]);
                        }
                        if ($DrawSubTicks && $i != $AxisSettings["Rows"]) {
                            $this->pChartObject->drawLine($XPos + $SubTicksSize, $YPos - $OuterSubTickWidth, $XPos + $SubTicksSize, $YPos + $InnerSubTickWidth, ["R" => $SubTickR, "G" => $SubTickG, "B" => $SubTickB, "Alpha" => $SubTickAlpha]);
                        }
                        $this->pChartObject->drawLine($XPos, $YPos - $OuterTickWidth, $XPos, $YPos + $InnerTickWidth, ["R" => $TickR, "G" => $TickG, "B" => $TickB, "Alpha" => $TickAlpha]);
                        $Bounds = $this->pChartObject->drawText($XPos, $YPos - $OuterTickWidth - $LabelOffset, $Value, ["Angle" => $XLabelsRotation, "Align" => $LabelAlign]);
                        $TxtBox = $YPos - $OuterTickWidth - 4 - ($Bounds[0]["Y"] - $Bounds[2]["Y"]);
                        $MinTop = min($MinTop, $TxtBox);
                        $LastX = $XPos;
                    }
                    if (isset($AxisSettings["Name"])) {
                        $YPos = $MinTop - 2;
                        $XPos = $this->pChartObject->GraphAreaX1 + ($this->pChartObject->GraphAreaX2 - $this->pChartObject->GraphAreaX1) / 2;
                        $Bounds = $this->pChartObject->drawText($XPos, $YPos, $AxisSettings["Name"], ["Align" => TEXT_ALIGN_BOTTOMMIDDLE]);
                        $MinTop = $Bounds[2]["Y"];
                        $this->pDataObject->Data["GraphArea"]["Y1"] = $MinTop;
                    }
                    $AxisPos["T"] = $MinTop - $ScaleSpacing;
                }
            } elseif ($AxisSettings["Identity"] == AXIS_Y) {
                if ($AxisSettings["Position"] == AXIS_POSITION_LEFT) {
                    if ($Floating) {
                        $FloatingOffset = $XMargin;
                        $this->pChartObject->drawLine($AxisPos["L"], $this->pChartObject->GraphAreaY1 + $AxisSettings["Margin"], $AxisPos["L"], $this->pChartObject->GraphAreaY2 - $AxisSettings["Margin"], ["R" => $AxisR, "G" => $AxisG, "B" => $AxisB, "Alpha" => $AxisAlpha]);
                    } else {
                        $FloatingOffset = 0;
                        $this->pChartObject->drawLine($AxisPos["L"], $this->pChartObject->GraphAreaY1, $AxisPos["L"], $this->pChartObject->GraphAreaY2, ["R" => $AxisR, "G" => $AxisG, "B" => $AxisB, "Alpha" => $AxisAlpha]);
                    }
                    if ($DrawArrows) {
                        $this->pChartObject->drawArrow($AxisPos["L"], $this->pChartObject->GraphAreaY1 + $AxisSettings["Margin"], $AxisPos["L"], $this->pChartObject->GraphAreaY1 - $ArrowSize * 2, ["FillR" => $AxisR, "FillG" => $AxisG, "FillB" => $AxisB, "Size" => $ArrowSize]);
                    }
                    $Height = $this->pChartObject->GraphAreaY2 - $this->pChartObject->GraphAreaY1 - $AxisSettings["Margin"] * 2;
                    $Step = $Height / $AxisSettings["Rows"];
                    $SubTicksSize = $Step / 2;
                    $MinLeft = $AxisPos["L"];
                    $LastY = null;
                    for ($i = 0; $i <= $AxisSettings["Rows"]; $i++) {
                        $YPos = $this->pChartObject->GraphAreaY2 - $AxisSettings["Margin"] - $Step * $i;
                        $XPos = $AxisPos["L"];
                        $Value = $this->pChartObject->scaleFormat($AxisSettings["ScaleMin"] + $AxisSettings["RowHeight"] * $i, $AxisSettings["Display"], $AxisSettings["Format"], $AxisSettings["Unit"]);
                        if ($i % 2 == 1) {
                            $BGColor = ["R" => $BackgroundR1, "G" => $BackgroundG1, "B" => $BackgroundB1, "Alpha" => $BackgroundAlpha1];
                        } else {
                            $BGColor = ["R" => $BackgroundR2, "G" => $BackgroundG2, "B" => $BackgroundB2, "Alpha" => $BackgroundAlpha2];
                        }
                        if ($LastY != null && $CycleBackground && ($DrawYLines == ALL || in_array($AxisID, $DrawYLines))) {
                            $this->pChartObject->drawFilledRectangle($this->pChartObject->GraphAreaX1 + $FloatingOffset, $LastY, $this->pChartObject->GraphAreaX2 - $FloatingOffset, $YPos, $BGColor);
                        }
                        if ($YPos != $this->pChartObject->GraphAreaY1 && $YPos != $this->pChartObject->GraphAreaY2 && ($DrawYLines == ALL || in_array($AxisID, $DrawYLines))) {
                            $this->pChartObject->drawLine($this->pChartObject->GraphAreaX1 + $FloatingOffset, $YPos, $this->pChartObject->GraphAreaX2 - $FloatingOffset, $YPos, ["R" => $GridR, "G" => $GridG, "B" => $GridB, "Alpha" => $GridAlpha, "Ticks" => $GridTicks]);
                        }
                        if ($DrawSubTicks && $i != $AxisSettings["Rows"]) {
                            $this->pChartObject->drawLine($XPos - $OuterSubTickWidth, $YPos - $SubTicksSize, $XPos + $InnerSubTickWidth, $YPos - $SubTicksSize, ["R" => $SubTickR, "G" => $SubTickG, "B" => $SubTickB, "Alpha" => $SubTickAlpha]);
                        }
                        $this->pChartObject->drawLine($XPos - $OuterTickWidth, $YPos, $XPos + $InnerTickWidth, $YPos, ["R" => $TickR, "G" => $TickG, "B" => $TickB, "Alpha" => $TickAlpha]);
                        $Bounds = $this->pChartObject->drawText($XPos - $OuterTickWidth - 2, $YPos, $Value, ["Align" => TEXT_ALIGN_MIDDLERIGHT]);
                        $TxtLeft = $XPos - $OuterTickWidth - 2 - ($Bounds[1]["X"] - $Bounds[0]["X"]);
                        $MinLeft = min($MinLeft, $TxtLeft);
                        $LastY = $YPos;
                    }
                    if (isset($AxisSettings["Name"])) {
                        $XPos = $MinLeft - 2;
                        $YPos = $this->pChartObject->GraphAreaY1 + ($this->pChartObject->GraphAreaY2 - $this->pChartObject->GraphAreaY1) / 2;
                        $Bounds = $this->pChartObject->drawText($XPos, $YPos, $AxisSettings["Name"], ["Align" => TEXT_ALIGN_BOTTOMMIDDLE, "Angle" => 90]);
                        $MinLeft = $Bounds[2]["X"];
                        $this->pDataObject->Data["GraphArea"]["X1"] = $MinLeft;
                    }
                    $AxisPos["L"] = $MinLeft - $ScaleSpacing;
                } elseif ($AxisSettings["Position"] == AXIS_POSITION_RIGHT) {
                    if ($Floating) {
                        $FloatingOffset = $XMargin;
                        $this->pChartObject->drawLine($AxisPos["R"], $this->pChartObject->GraphAreaY1 + $AxisSettings["Margin"], $AxisPos["R"], $this->pChartObject->GraphAreaY2 - $AxisSettings["Margin"], ["R" => $AxisR, "G" => $AxisG, "B" => $AxisB, "Alpha" => $AxisAlpha]);
                    } else {
                        $FloatingOffset = 0;
                        $this->pChartObject->drawLine($AxisPos["R"], $this->pChartObject->GraphAreaY1, $AxisPos["R"], $this->pChartObject->GraphAreaY2, ["R" => $AxisR, "G" => $AxisG, "B" => $AxisB, "Alpha" => $AxisAlpha]);
                    }
                    if ($DrawArrows) {
                        $this->pChartObject->drawArrow($AxisPos["R"], $this->pChartObject->GraphAreaY1 + $AxisSettings["Margin"], $AxisPos["R"], $this->pChartObject->GraphAreaY1 - $ArrowSize * 2, ["FillR" => $AxisR, "FillG" => $AxisG, "FillB" => $AxisB, "Size" => $ArrowSize]);
                    }
                    $Height = $this->pChartObject->GraphAreaY2 - $this->pChartObject->GraphAreaY1 - $AxisSettings["Margin"] * 2;
                    $Step = $Height / $AxisSettings["Rows"];
                    $SubTicksSize = $Step / 2;
                    $MaxLeft = $AxisPos["R"];
                    $LastY = null;
                    for ($i = 0; $i <= $AxisSettings["Rows"]; $i++) {
                        $YPos = $this->pChartObject->GraphAreaY2 - $AxisSettings["Margin"] - $Step * $i;
                        $XPos = $AxisPos["R"];
                        $Value = $this->pChartObject->scaleFormat($AxisSettings["ScaleMin"] + $AxisSettings["RowHeight"] * $i, $AxisSettings["Display"], $AxisSettings["Format"], $AxisSettings["Unit"]);
                        if ($i % 2 == 1) {
                            $BGColor = ["R" => $BackgroundR1, "G" => $BackgroundG1, "B" => $BackgroundB1, "Alpha" => $BackgroundAlpha1];
                        } else {
                            $BGColor = ["R" => $BackgroundR2, "G" => $BackgroundG2, "B" => $BackgroundB2, "Alpha" => $BackgroundAlpha2];
                        }
                        if ($LastY != null && $CycleBackground && ($DrawYLines == ALL || in_array($AxisID, $DrawYLines))) {
                            $this->pChartObject->drawFilledRectangle($this->pChartObject->GraphAreaX1 + $FloatingOffset, $LastY, $this->pChartObject->GraphAreaX2 - $FloatingOffset, $YPos, $BGColor);
                        }
                        if ($YPos != $this->pChartObject->GraphAreaY1 && $YPos != $this->pChartObject->GraphAreaY2 && ($DrawYLines == ALL || in_array($AxisID, $DrawYLines))) {
                            $this->pChartObject->drawLine($this->pChartObject->GraphAreaX1 + $FloatingOffset, $YPos, $this->pChartObject->GraphAreaX2 - $FloatingOffset, $YPos, ["R" => $GridR, "G" => $GridG, "B" => $GridB, "Alpha" => $GridAlpha, "Ticks" => $GridTicks]);
                        }
                        if ($DrawSubTicks && $i != $AxisSettings["Rows"]) {
                            $this->pChartObject->drawLine($XPos - $InnerSubTickWidth, $YPos - $SubTicksSize, $XPos + $OuterSubTickWidth, $YPos - $SubTicksSize, ["R" => $SubTickR, "G" => $SubTickG, "B" => $SubTickB, "Alpha" => $SubTickAlpha]);
                        }
                        $this->pChartObject->drawLine($XPos - $InnerTickWidth, $YPos, $XPos + $OuterTickWidth, $YPos, ["R" => $TickR, "G" => $TickG, "B" => $TickB, "Alpha" => $TickAlpha]);
                        $Bounds = $this->pChartObject->drawText($XPos + $OuterTickWidth + 2, $YPos, $Value, ["Align" => TEXT_ALIGN_MIDDLELEFT]);
                        $TxtLeft = $XPos + $OuterTickWidth + 2 + ($Bounds[1]["X"] - $Bounds[0]["X"]);
                        $MaxLeft = max($MaxLeft, $TxtLeft);
                        $LastY = $YPos;
                    }
                    if (isset($AxisSettings["Name"])) {
                        $XPos = $MaxLeft + 6;
                        $YPos = $this->pChartObject->GraphAreaY1 + ($this->pChartObject->GraphAreaY2 - $this->pChartObject->GraphAreaY1) / 2;
                        $Bounds = $this->pChartObject->drawText($XPos, $YPos, $AxisSettings["Name"], ["Align" => TEXT_ALIGN_BOTTOMMIDDLE, "Angle" => 270]);
                        $MaxLeft = $Bounds[2]["X"];
                        $this->pDataObject->Data["GraphArea"]["X2"] = $MaxLeft + $this->pChartObject->FontSize;
                    }
                    $AxisPos["R"] = $MaxLeft + $ScaleSpacing;
                }
            }
        }
        $this->pDataObject->saveAxisConfig($Data["Axis"]);
    }
    /**
     * Draw a scatter plot chart
     * @param array $Format
     */
    public function drawScatterPlotChart($Format = null)
    {
        $PlotSize = isset($Format["PlotSize"]) ? $Format["PlotSize"] : 3;
        $PlotBorder = isset($Format["PlotBorder"]) ? $Format["PlotBorder"] : \false;
        $BorderR = isset($Format["BorderR"]) ? $Format["BorderR"] : 250;
        $BorderG = isset($Format["BorderG"]) ? $Format["BorderG"] : 250;
        $BorderB = isset($Format["BorderB"]) ? $Format["BorderB"] : 250;
        $BorderAlpha = isset($Format["BorderAlpha"]) ? $Format["BorderAlpha"] : 30;
        $BorderSize = isset($Format["BorderSize"]) ? $Format["BorderSize"] : 1;
        $RecordImageMap = isset($Format["RecordImageMap"]) ? $Format["RecordImageMap"] : \false;
        $ImageMapTitle = isset($Format["ImageMapTitle"]) ? $Format["ImageMapTitle"] : null;
        $Data = $this->pDataObject->getData();
        $BorderColor = ["R" => $BorderR, "G" => $BorderG, "B" => $BorderB, "Alpha" => $BorderAlpha];
        foreach ($Data["ScatterSeries"] as $Key => $Series) {
            if ($Series["isDrawable"] == \true) {
                $SerieX = $Series["X"];
                $SerieValuesX = $Data["Series"][$SerieX]["Data"];
                $SerieXAxis = $Data["Series"][$SerieX]["Axis"];
                $SerieY = $Series["Y"];
                $SerieValuesY = $Data["Series"][$SerieY]["Data"];
                $SerieYAxis = $Data["Series"][$SerieY]["Axis"];
                if ($ImageMapTitle == null) {
                    $Description = sprintf("%s / %s", $Data["Series"][$Series["X"]]["Description"], $Data["Series"][$Series["Y"]]["Description"]);
                } else {
                    $Description = $ImageMapTitle;
                }
                if (isset($Series["Picture"]) && $Series["Picture"] != "") {
                    $Picture = $Series["Picture"];
                    list($PicWidth, $PicHeight, $PicType) = $this->pChartObject->getPicInfo($Picture);
                } else {
                    $Picture = null;
                }
                $PosArrayX = $this->getPosArray($SerieValuesX, $SerieXAxis);
                if (!is_array($PosArrayX)) {
                    $Value = $PosArrayX;
                    $PosArrayX = [];
                    $PosArrayX[0] = $Value;
                }
                $PosArrayY = $this->getPosArray($SerieValuesY, $SerieYAxis);
                if (!is_array($PosArrayY)) {
                    $Value = $PosArrayY;
                    $PosArrayY = [];
                    $PosArrayY[0] = $Value;
                }
                $Color = ["R" => $Series["Color"]["R"], "G" => $Series["Color"]["G"], "B" => $Series["Color"]["B"], "Alpha" => $Series["Color"]["Alpha"]];
                foreach ($PosArrayX as $Key => $Value) {
                    $X = $Value;
                    $Y = $PosArrayY[$Key];
                    if ($X != VOID && $Y != VOID) {
                        $RealValue = sprintf("%s / %s", round($Data["Series"][$Series["X"]]["Data"][$Key], 2), round($Data["Series"][$Series["Y"]]["Data"][$Key], 2));
                        if ($RecordImageMap) {
                            $this->pChartObject->addToImageMap("CIRCLE", sprintf("%s,%s,%s", floor($X), floor($Y), floor($PlotSize + $BorderSize)), $this->pChartObject->toHTMLColor($Series["Color"]["R"], $Series["Color"]["G"], $Series["Color"]["B"]), $Description, $RealValue);
                        }
                        if (isset($Series["Shape"])) {
                            $this->pChartObject->drawShape($X, $Y, $Series["Shape"], $PlotSize, $PlotBorder, $BorderSize, $Series["Color"]["R"], $Series["Color"]["G"], $Series["Color"]["B"], $Series["Color"]["Alpha"], $BorderR, $BorderG, $BorderB, $BorderAlpha);
                        } elseif ($Picture == null) {
                            if ($PlotBorder) {
                                $this->pChartObject->drawFilledCircle($X, $Y, $PlotSize + $BorderSize, $BorderColor);
                            }
                            $this->pChartObject->drawFilledCircle($X, $Y, $PlotSize, $Color);
                        } else {
                            $this->pChartObject->drawFromPicture($PicType, $Picture, $X - $PicWidth / 2, $Y - $PicHeight / 2);
                        }
                    }
                }
            }
        }
    }
    /**
     * Draw a scatter line chart
     * @param array $Format
     */
    public function drawScatterLineChart($Format = null)
    {
        $Data = $this->pDataObject->getData();
        $RecordImageMap = isset($Format["RecordImageMap"]) ? $Format["RecordImageMap"] : \false;
        $ImageMapTitle = isset($Format["ImageMapTitle"]) ? $Format["ImageMapTitle"] : null;
        $ImageMapPlotSize = isset($Format["ImageMapPlotSize"]) ? $Format["ImageMapPlotSize"] : 10;
        /* Parse all the series to draw */
        foreach ($Data["ScatterSeries"] as $Key => $Series) {
            if ($Series["isDrawable"] == \true) {
                $SerieX = $Series["X"];
                $SerieValuesX = $Data["Series"][$SerieX]["Data"];
                $SerieXAxis = $Data["Series"][$SerieX]["Axis"];
                $SerieY = $Series["Y"];
                $SerieValuesY = $Data["Series"][$SerieY]["Data"];
                $SerieYAxis = $Data["Series"][$SerieY]["Axis"];
                $Ticks = $Series["Ticks"];
                $Weight = $Series["Weight"];
                if ($ImageMapTitle == null) {
                    $Description = sprintf("%s / %s", $Data["Series"][$Series["X"]]["Description"], $Data["Series"][$Series["Y"]]["Description"]);
                } else {
                    $Description = $ImageMapTitle;
                }
                $PosArrayX = $this->getPosArray($SerieValuesX, $SerieXAxis);
                if (!is_array($PosArrayX)) {
                    $Value = $PosArrayX;
                    $PosArrayX = [];
                    $PosArrayX[0] = $Value;
                }
                $PosArrayY = $this->getPosArray($SerieValuesY, $SerieYAxis);
                if (!is_array($PosArrayY)) {
                    $Value = $PosArrayY;
                    $PosArrayY = [];
                    $PosArrayY[0] = $Value;
                }
                $Color = ["R" => $Series["Color"]["R"], "G" => $Series["Color"]["G"], "B" => $Series["Color"]["B"], "Alpha" => $Series["Color"]["Alpha"]];
                if ($Ticks != 0) {
                    $Color["Ticks"] = $Ticks;
                }
                if ($Weight != 0) {
                    $Color["Weight"] = $Weight;
                }
                $LastX = VOID;
                $LastY = VOID;
                foreach ($PosArrayX as $Key => $Value) {
                    $X = $Value;
                    $Y = $PosArrayY[$Key];
                    if ($X != VOID && $Y != VOID) {
                        $RealValue = sprintf("%s / %s", round($Data["Series"][$Series["X"]]["Data"][$Key], 2), round($Data["Series"][$Series["Y"]]["Data"][$Key], 2));
                        if ($RecordImageMap) {
                            $this->pChartObject->addToImageMap("CIRCLE", sprintf("%s,%s,%s", floor($X), floor($Y), $ImageMapPlotSize), $this->pChartObject->toHTMLColor($Series["Color"]["R"], $Series["Color"]["G"], $Series["Color"]["B"]), $Description, $RealValue);
                        }
                    }
                    if ($X != VOID && $Y != VOID && $LastX != VOID && $LastY != VOID) {
                        $this->pChartObject->drawLine($LastX, $LastY, $X, $Y, $Color);
                    }
                    $LastX = $X;
                    $LastY = $Y;
                }
            }
        }
    }
    /**
     * Draw a scatter spline chart
     * @param array $Format
     */
    public function drawScatterSplineChart(array $Format = [])
    {
        $Data = $this->pDataObject->getData();
        $RecordImageMap = isset($Format["RecordImageMap"]) ? $Format["RecordImageMap"] : \false;
        $ImageMapTitle = isset($Format["ImageMapTitle"]) ? $Format["ImageMapTitle"] : null;
        $ImageMapPlotSize = isset($Format["ImageMapPlotSize"]) ? $Format["ImageMapPlotSize"] : 10;
        foreach ($Data["ScatterSeries"] as $Key => $Series) {
            if ($Series["isDrawable"] == \true) {
                $SerieX = $Series["X"];
                $SerieValuesX = $Data["Series"][$SerieX]["Data"];
                $SerieXAxis = $Data["Series"][$SerieX]["Axis"];
                $SerieY = $Series["Y"];
                $SerieValuesY = $Data["Series"][$SerieY]["Data"];
                $SerieYAxis = $Data["Series"][$SerieY]["Axis"];
                $Ticks = $Series["Ticks"];
                $Weight = $Series["Weight"];
                if ($ImageMapTitle == null) {
                    $Description = sprintf("%s / %s", $Data["Series"][$Series["X"]]["Description"], $Data["Series"][$Series["Y"]]["Description"]);
                } else {
                    $Description = $ImageMapTitle;
                }
                $PosArrayX = $this->getPosArray($SerieValuesX, $SerieXAxis);
                if (!is_array($PosArrayX)) {
                    $Value = $PosArrayX;
                    $PosArrayX = [];
                    $PosArrayX[0] = $Value;
                }
                $PosArrayY = $this->getPosArray($SerieValuesY, $SerieYAxis);
                if (!is_array($PosArrayY)) {
                    $Value = $PosArrayY;
                    $PosArrayY = [];
                    $PosArrayY[0] = $Value;
                }
                $SplineSettings = ["R" => $Series["Color"]["R"], "G" => $Series["Color"]["G"], "B" => $Series["Color"]["B"], "Alpha" => $Series["Color"]["Alpha"]];
                if ($Ticks != 0) {
                    $SplineSettings["Ticks"] = $Ticks;
                }
                if ($Weight != 0) {
                    $SplineSettings["Weight"] = $Weight;
                }
                $LastX = VOID;
                $LastY = VOID;
                $WayPoints = [];
                $Forces = [];
                foreach ($PosArrayX as $Key => $Value) {
                    $X = $Value;
                    $Y = $PosArrayY[$Key];
                    $Force = $this->pChartObject->getLength($LastX, $LastY, $X, $Y) / 5;
                    if ($X != VOID && $Y != VOID) {
                        $RealValue = sprintf("%s / %s", round($Data["Series"][$Series["X"]]["Data"][$Key], 2), round($Data["Series"][$Series["Y"]]["Data"][$Key], 2));
                        if ($RecordImageMap) {
                            $this->pChartObject->addToImageMap("CIRCLE", sprintf("%s,%s,%s", floor($X), floor($Y), $ImageMapPlotSize), $this->pChartObject->toHTMLColor($Series["Color"]["R"], $Series["Color"]["G"], $Series["Color"]["B"]), $Description, $RealValue);
                        }
                    }
                    if ($X != VOID && $Y != VOID) {
                        $WayPoints[] = [$X, $Y];
                        $Forces[] = $Force;
                    }
                    if ($Y == VOID || $X == VOID) {
                        $SplineSettings["Forces"] = $Forces;
                        $this->pChartObject->drawSpline($WayPoints, $SplineSettings);
                        $WayPoints = [];
                        $Forces = [];
                    }
                    $LastX = $X;
                    $LastY = $Y;
                }
                $SplineSettings["Forces"] = $Forces;
                $this->pChartObject->drawSpline($WayPoints, $SplineSettings);
            }
        }
    }
    /**
     * Return the scaled plot position
     * @param mixed $Values
     * @param string $AxisID
     * @return mixed
     */
    public function getPosArray($Values, $AxisID)
    {
        $Data = $this->pDataObject->getData();
        if (!is_array($Values)) {
            $Values = [$Values];
        }
        if ($Data["Axis"][$AxisID]["Identity"] == AXIS_X) {
            $Height = $this->pChartObject->GraphAreaX2 - $this->pChartObject->GraphAreaX1 - $Data["Axis"][$AxisID]["Margin"] * 2;
            $ScaleHeight = $Data["Axis"][$AxisID]["ScaleMax"] - $Data["Axis"][$AxisID]["ScaleMin"];
            $Step = $Height / $ScaleHeight;
            $Result = [];
            foreach ($Values as $Key => $Value) {
                if ($Value == VOID) {
                    $Result[] = VOID;
                } else {
                    $Result[] = $this->pChartObject->GraphAreaX1 + $Data["Axis"][$AxisID]["Margin"] + $Step * ($Value - $Data["Axis"][$AxisID]["ScaleMin"]);
                }
            }
        } else {
            $Height = $this->pChartObject->GraphAreaY2 - $this->pChartObject->GraphAreaY1 - $Data["Axis"][$AxisID]["Margin"] * 2;
            $ScaleHeight = $Data["Axis"][$AxisID]["ScaleMax"] - $Data["Axis"][$AxisID]["ScaleMin"];
            $Step = $Height / $ScaleHeight;
            $Result = [];
            foreach ($Values as $Key => $Value) {
                if ($Value == VOID) {
                    $Result[] = VOID;
                } else {
                    $Result[] = $this->pChartObject->GraphAreaY2 - $Data["Axis"][$AxisID]["Margin"] - $Step * ($Value - $Data["Axis"][$AxisID]["ScaleMin"]);
                }
            }
        }
        return count($Result) == 1 ? reset($Result) : $Result;
    }
    /**
     * Draw the legend of the active series
     * @param int $X
     * @param int $Y
     * @param array $Format
     */
    public function drawScatterLegend($X, $Y, array $Format = [])
    {
        $Family = isset($Format["Family"]) ? $Format["Family"] : LEGEND_FAMILY_BOX;
        $FontName = isset($Format["FontName"]) ? $Format["FontName"] : $this->pChartObject->FontName;
        $FontSize = isset($Format["FontSize"]) ? $Format["FontSize"] : $this->pChartObject->FontSize;
        $FontR = isset($Format["FontR"]) ? $Format["FontR"] : $this->pChartObject->FontColorR;
        $FontG = isset($Format["FontG"]) ? $Format["FontG"] : $this->pChartObject->FontColorG;
        $FontB = isset($Format["FontB"]) ? $Format["FontB"] : $this->pChartObject->FontColorB;
        $BoxWidth = isset($Format["BoxWidth"]) ? $Format["BoxWidth"] : 5;
        $BoxHeight = isset($Format["BoxHeight"]) ? $Format["BoxHeight"] : 5;
        $IconAreaWidth = isset($Format["IconAreaWidth"]) ? $Format["IconAreaWidth"] : $BoxWidth;
        $IconAreaHeight = isset($Format["IconAreaHeight"]) ? $Format["IconAreaHeight"] : $BoxHeight;
        $XSpacing = isset($Format["XSpacing"]) ? $Format["XSpacing"] : 5;
        $Margin = isset($Format["Margin"]) ? $Format["Margin"] : 5;
        $R = isset($Format["R"]) ? $Format["R"] : 200;
        $G = isset($Format["G"]) ? $Format["G"] : 200;
        $B = isset($Format["B"]) ? $Format["B"] : 200;
        $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;
        $BorderR = isset($Format["BorderR"]) ? $Format["BorderR"] : 255;
        $BorderG = isset($Format["BorderG"]) ? $Format["BorderG"] : 255;
        $BorderB = isset($Format["BorderB"]) ? $Format["BorderB"] : 255;
        $Surrounding = isset($Format["Surrounding"]) ? $Format["Surrounding"] : null;
        $Style = isset($Format["Style"]) ? $Format["Style"] : LEGEND_ROUND;
        $Mode = isset($Format["Mode"]) ? $Format["Mode"] : LEGEND_VERTICAL;
        if ($Surrounding != null) {
            $BorderR = $R + $Surrounding;
            $BorderG = $G + $Surrounding;
            $BorderB = $B + $Surrounding;
        }
        $Data = $this->pDataObject->getData();
        foreach ($Data["ScatterSeries"] as $Key => $Series) {
            if ($Series["isDrawable"] == \true && isset($Series["Picture"])) {
                list($PicWidth, $PicHeight) = $this->pChartObject->getPicInfo($Series["Picture"]);
                if ($IconAreaWidth < $PicWidth) {
                    $IconAreaWidth = $PicWidth;
                }
                if ($IconAreaHeight < $PicHeight) {
                    $IconAreaHeight = $PicHeight;
                }
            }
        }
        $YStep = max($this->pChartObject->FontSize, $IconAreaHeight) + 5;
        $XStep = $XSpacing;
        $Boundaries = [];
        $Boundaries["L"] = $X;
        $Boundaries["T"] = $Y;
        $Boundaries["R"] = 0;
        $Boundaries["B"] = 0;
        $vY = $Y;
        $vX = $X;
        foreach ($Data["ScatterSeries"] as $Key => $Series) {
            if ($Series["isDrawable"] == \true) {
                if ($Mode == LEGEND_VERTICAL) {
                    $BoxArray = $this->pChartObject->getTextBox($vX + $IconAreaWidth + 4, $vY + $IconAreaHeight / 2, $FontName, $FontSize, 0, $Series["Description"]);
                    if ($Boundaries["T"] > $BoxArray[2]["Y"] + $IconAreaHeight / 2) {
                        $Boundaries["T"] = $BoxArray[2]["Y"] + $IconAreaHeight / 2;
                    }
                    if ($Boundaries["R"] < $BoxArray[1]["X"] + 2) {
                        $Boundaries["R"] = $BoxArray[1]["X"] + 2;
                    }
                    if ($Boundaries["B"] < $BoxArray[1]["Y"] + 2 + $IconAreaHeight / 2) {
                        $Boundaries["B"] = $BoxArray[1]["Y"] + 2 + $IconAreaHeight / 2;
                    }
                    $Lines = preg_split("/\n/", $Series["Description"]);
                    $vY = $vY + max($this->pChartObject->FontSize * count($Lines), $IconAreaHeight) + 5;
                } elseif ($Mode == LEGEND_HORIZONTAL) {
                    $Lines = preg_split("/\n/", $Series["Description"]);
                    $Width = [];
                    foreach ($Lines as $Key => $Value) {
                        $BoxArray = $this->pChartObject->getTextBox($vX + $IconAreaWidth + 6, $Y + $IconAreaHeight / 2 + ($this->pChartObject->FontSize + 3) * $Key, $FontName, $FontSize, 0, $Value);
                        if ($Boundaries["T"] > $BoxArray[2]["Y"] + $IconAreaHeight / 2) {
                            $Boundaries["T"] = $BoxArray[2]["Y"] + $IconAreaHeight / 2;
                        }
                        if ($Boundaries["R"] < $BoxArray[1]["X"] + 2) {
                            $Boundaries["R"] = $BoxArray[1]["X"] + 2;
                        }
                        if ($Boundaries["B"] < $BoxArray[1]["Y"] + 2 + $IconAreaHeight / 2) {
                            $Boundaries["B"] = $BoxArray[1]["Y"] + 2 + $IconAreaHeight / 2;
                        }
                        $Width[] = $BoxArray[1]["X"];
                    }
                    $vX = max($Width) + $XStep;
                }
            }
        }
        $vY = $vY - $YStep;
        $vX = $vX - $XStep;
        $TopOffset = $Y - $Boundaries["T"];
        if ($Boundaries["B"] - ($vY + $IconAreaHeight) < $TopOffset) {
            $Boundaries["B"] = $vY + $IconAreaHeight + $TopOffset;
        }
        if ($Style == LEGEND_ROUND) {
            $this->pChartObject->drawRoundedFilledRectangle($Boundaries["L"] - $Margin, $Boundaries["T"] - $Margin, $Boundaries["R"] + $Margin, $Boundaries["B"] + $Margin, $Margin, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "BorderR" => $BorderR, "BorderG" => $BorderG, "BorderB" => $BorderB]);
        } elseif ($Style == LEGEND_BOX) {
            $this->pChartObject->drawFilledRectangle($Boundaries["L"] - $Margin, $Boundaries["T"] - $Margin, $Boundaries["R"] + $Margin, $Boundaries["B"] + $Margin, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "BorderR" => $BorderR, "BorderG" => $BorderG, "BorderB" => $BorderB]);
        }
        $RestoreShadow = $this->pChartObject->Shadow;
        $this->pChartObject->Shadow = \false;
        foreach ($Data["ScatterSeries"] as $Key => $Series) {
            if ($Series["isDrawable"] == \true) {
                $R = $Series["Color"]["R"];
                $G = $Series["Color"]["G"];
                $B = $Series["Color"]["B"];
                $Ticks = $Series["Ticks"];
                $Weight = $Series["Weight"];
                if (isset($Series["Picture"])) {
                    $Picture = $Series["Picture"];
                    list($PicWidth, $PicHeight) = $this->pChartObject->getPicInfo($Picture);
                    $PicX = $X + $IconAreaWidth / 2;
                    $PicY = $Y + $IconAreaHeight / 2;
                    $this->pChartObject->drawFromPNG($PicX - $PicWidth / 2, $PicY - $PicHeight / 2, $Picture);
                } else {
                    if ($Family == LEGEND_FAMILY_BOX) {
                        if ($BoxWidth != $IconAreaWidth) {
                            $XOffset = floor(($IconAreaWidth - $BoxWidth) / 2);
                        } else {
                            $XOffset = 0;
                        }
                        if ($BoxHeight != $IconAreaHeight) {
                            $YOffset = floor(($IconAreaHeight - $BoxHeight) / 2);
                        } else {
                            $YOffset = 0;
                        }
                        $this->pChartObject->drawFilledRectangle($X + 1 + $XOffset, $Y + 1 + $YOffset, $X + $BoxWidth + $XOffset + 1, $Y + $BoxHeight + 1 + $YOffset, ["R" => 0, "G" => 0, "B" => 0, "Alpha" => 20]);
                        $this->pChartObject->drawFilledRectangle($X + $XOffset, $Y + $YOffset, $X + $BoxWidth + $XOffset, $Y + $BoxHeight + $YOffset, ["R" => $R, "G" => $G, "B" => $B, "Surrounding" => 20]);
                    } elseif ($Family == LEGEND_FAMILY_CIRCLE) {
                        $this->pChartObject->drawFilledCircle($X + 1 + $IconAreaWidth / 2, $Y + 1 + $IconAreaHeight / 2, min($IconAreaHeight / 2, $IconAreaWidth / 2), ["R" => 0, "G" => 0, "B" => 0, "Alpha" => 20]);
                        $this->pChartObject->drawFilledCircle($X + $IconAreaWidth / 2, $Y + $IconAreaHeight / 2, min($IconAreaHeight / 2, $IconAreaWidth / 2), ["R" => $R, "G" => $G, "B" => $B, "Surrounding" => 20]);
                    } elseif ($Family == LEGEND_FAMILY_LINE) {
                        $this->pChartObject->drawLine($X + 1, $Y + 1 + $IconAreaHeight / 2, $X + 1 + $IconAreaWidth, $Y + 1 + $IconAreaHeight / 2, ["R" => 0, "G" => 0, "B" => 0, "Alpha" => 20, "Ticks" => $Ticks, "Weight" => $Weight]);
                        $this->pChartObject->drawLine($X, $Y + $IconAreaHeight / 2, $X + $IconAreaWidth, $Y + $IconAreaHeight / 2, ["R" => $R, "G" => $G, "B" => $B, "Ticks" => $Ticks, "Weight" => $Weight]);
                    }
                }
                if ($Mode == LEGEND_VERTICAL) {
                    $Lines = preg_split("/\n/", $Series["Description"]);
                    foreach ($Lines as $Key => $Value) {
                        $this->pChartObject->drawText($X + $IconAreaWidth + 4, $Y + $IconAreaHeight / 2 + ($this->pChartObject->FontSize + 3) * $Key, $Value, ["R" => $FontR, "G" => $FontG, "B" => $FontB, "Align" => TEXT_ALIGN_MIDDLELEFT]);
                    }
                    $Y = $Y + max($this->pChartObject->FontSize * count($Lines), $IconAreaHeight) + 5;
                } elseif ($Mode == LEGEND_HORIZONTAL) {
                    $Lines = preg_split("/\n/", $Series["Description"]);
                    $Width = [];
                    foreach ($Lines as $Key => $Value) {
                        $BoxArray = $this->pChartObject->drawText($X + $IconAreaWidth + 4, $Y + $IconAreaHeight / 2 + ($this->pChartObject->FontSize + 3) * $Key, $Value, ["R" => $FontR, "G" => $FontG, "B" => $FontB, "Align" => TEXT_ALIGN_MIDDLELEFT]);
                        $Width[] = $BoxArray[1]["X"];
                    }
                    $X = max($Width) + 2 + $XStep;
                }
            }
        }
        $this->pChartObject->Shadow = $RestoreShadow;
    }
    /**
     * Get the legend box size
     * @param array $Format
     * @return array
     */
    public function getScatterLegendSize(array $Format = [])
    {
        $FontName = isset($Format["FontName"]) ? $Format["FontName"] : $this->pChartObject->FontName;
        $FontSize = isset($Format["FontSize"]) ? $Format["FontSize"] : $this->pChartObject->FontSize;
        $BoxSize = isset($Format["BoxSize"]) ? $Format["BoxSize"] : 5;
        $Margin = isset($Format["Margin"]) ? $Format["Margin"] : 5;
        $Mode = isset($Format["Mode"]) ? $Format["Mode"] : LEGEND_VERTICAL;
        $XSpacing = isset($Format["XSpacing"]) ? $Format["XSpacing"] : 5;
        $YStep = max($this->pChartObject->FontSize, $BoxSize) + 5;
        $XStep = $BoxSize + 5;
        $X = 100;
        $Y = 100;
        $Data = $this->pDataObject->getData();
        foreach ($Data["ScatterSeries"] as $Key => $Series) {
            if ($Series["isDrawable"] == \true && isset($Series["Picture"])) {
                list($PicWidth, $PicHeight) = $this->pChartObject->getPicInfo($Series["Picture"]);
                if ($IconAreaWidth < $PicWidth) {
                    $IconAreaWidth = $PicWidth;
                }
                if ($IconAreaHeight < $PicHeight) {
                    $IconAreaHeight = $PicHeight;
                }
            }
        }
        $YStep = max($this->pChartObject->FontSize, $IconAreaHeight) + 5;
        $XStep = $XSpacing;
        $Boundaries = [];
        $Boundaries["L"] = $X;
        $Boundaries["T"] = $Y;
        $Boundaries["R"] = 0;
        $Boundaries["B"] = 0;
        $vY = $Y;
        $vX = $X;
        foreach ($Data["ScatterSeries"] as $Key => $Series) {
            if ($Series["isDrawable"] == \true) {
                if ($Mode == LEGEND_VERTICAL) {
                    $BoxArray = $this->pChartObject->getTextBox($vX + $IconAreaWidth + 4, $vY + $IconAreaHeight / 2, $FontName, $FontSize, 0, $Series["Description"]);
                    if ($Boundaries["T"] > $BoxArray[2]["Y"] + $IconAreaHeight / 2) {
                        $Boundaries["T"] = $BoxArray[2]["Y"] + $IconAreaHeight / 2;
                    }
                    if ($Boundaries["R"] < $BoxArray[1]["X"] + 2) {
                        $Boundaries["R"] = $BoxArray[1]["X"] + 2;
                    }
                    if ($Boundaries["B"] < $BoxArray[1]["Y"] + 2 + $IconAreaHeight / 2) {
                        $Boundaries["B"] = $BoxArray[1]["Y"] + 2 + $IconAreaHeight / 2;
                    }
                    $Lines = preg_split("/\n/", $Series["Description"]);
                    $vY = $vY + max($this->pChartObject->FontSize * count($Lines), $IconAreaHeight) + 5;
                } elseif ($Mode == LEGEND_HORIZONTAL) {
                    $Lines = preg_split("/\n/", $Series["Description"]);
                    $Width = [];
                    foreach ($Lines as $Key => $Value) {
                        $BoxArray = $this->pChartObject->getTextBox($vX + $IconAreaWidth + 6, $Y + $IconAreaHeight / 2 + ($this->pChartObject->FontSize + 3) * $Key, $FontName, $FontSize, 0, $Value);
                        if ($Boundaries["T"] > $BoxArray[2]["Y"] + $IconAreaHeight / 2) {
                            $Boundaries["T"] = $BoxArray[2]["Y"] + $IconAreaHeight / 2;
                        }
                        if ($Boundaries["R"] < $BoxArray[1]["X"] + 2) {
                            $Boundaries["R"] = $BoxArray[1]["X"] + 2;
                        }
                        if ($Boundaries["B"] < $BoxArray[1]["Y"] + 2 + $IconAreaHeight / 2) {
                            $Boundaries["B"] = $BoxArray[1]["Y"] + 2 + $IconAreaHeight / 2;
                        }
                        $Width[] = $BoxArray[1]["X"];
                    }
                    $vX = max($Width) + $XStep;
                }
            }
        }
        $vY = $vY - $YStep;
        $vX = $vX - $XStep;
        $TopOffset = $Y - $Boundaries["T"];
        if ($Boundaries["B"] - ($vY + $BoxSize) < $TopOffset) {
            $Boundaries["B"] = $vY + $BoxSize + $TopOffset;
        }
        $Width = $Boundaries["R"] + $Margin - ($Boundaries["L"] - $Margin);
        $Height = $Boundaries["B"] + $Margin - ($Boundaries["T"] - $Margin);
        return ["Width" => $Width, "Height" => $Height];
    }
    /**
     * Draw the line of best fit
     * @param array $Format
     */
    public function drawScatterBestFit(array $Format = [])
    {
        $Ticks = isset($Format["Ticks"]) ? $Format["Ticks"] : 0;
        $Data = $this->pDataObject->getData();
        foreach ($Data["ScatterSeries"] as $Key => $Series) {
            if ($Series["isDrawable"] == \true) {
                $SerieX = $Series["X"];
                $SerieXAxis = $Data["Series"][$SerieX]["Axis"];
                $SerieY = $Series["Y"];
                $SerieYAxis = $Data["Series"][$SerieY]["Axis"];
                $Color = ["R" => $Series["Color"]["R"], "G" => $Series["Color"]["G"], "B" => $Series["Color"]["B"], "Alpha" => $Series["Color"]["Alpha"]];
                $Color["Ticks"] = $Ticks;
                $PosArrayX = $Data["Series"][$Series["X"]]["Data"];
                $PosArrayY = $Data["Series"][$Series["Y"]]["Data"];
                $Sxy = 0;
                $Sx = 0;
                $Sy = 0;
                $Sxx = 0;
                foreach ($PosArrayX as $Key => $Value) {
                    $X = $Value;
                    $Y = $PosArrayY[$Key];
                    $Sxy = $Sxy + $X * $Y;
                    $Sx = $Sx + $X;
                    $Sy = $Sy + $Y;
                    $Sxx = $Sxx + $X * $X;
                }
                $n = count($PosArrayX);
                if ($n * $Sxx == $Sx * $Sx) {
                    $X1 = $this->getPosArray($Data["Axis"][$SerieXAxis]["ScaleMin"], $SerieXAxis);
                    $X2 = $X1;
                    $Y1 = $this->pChartObject->GraphAreaY1;
                    $Y2 = $this->pChartObject->GraphAreaY2;
                } else {
                    $M = ($n * $Sxy - $Sx * $Sy) / ($n * $Sxx - $Sx * $Sx);
                    $B = ($Sy - $M * $Sx) / $n;
                    $X1 = $this->getPosArray($Data["Axis"][$SerieXAxis]["ScaleMin"], $SerieXAxis);
                    $Y1 = $this->getPosArray($M * $Data["Axis"][$SerieXAxis]["ScaleMin"] + $B, $SerieYAxis);
                    $X2 = $this->getPosArray($Data["Axis"][$SerieXAxis]["ScaleMax"], $SerieXAxis);
                    $Y2 = $this->getPosArray($M * $Data["Axis"][$SerieXAxis]["ScaleMax"] + $B, $SerieYAxis);
                    $RealM = -($Y2 - $Y1) / ($X2 - $X1);
                    if ($Y1 < $this->pChartObject->GraphAreaY1) {
                        $X1 = $X1 + ($this->pChartObject->GraphAreaY1 - $Y1 / $RealM);
                        $Y1 = $this->pChartObject->GraphAreaY1;
                    }
                    if ($Y1 > $this->pChartObject->GraphAreaY2) {
                        $X1 = $X1 + ($Y1 - $this->pChartObject->GraphAreaY2) / $RealM;
                        $Y1 = $this->pChartObject->GraphAreaY2;
                    }
                    if ($Y2 < $this->pChartObject->GraphAreaY1) {
                        $X2 = $X2 - ($this->pChartObject->GraphAreaY1 - $Y2) / $RealM;
                        $Y2 = $this->pChartObject->GraphAreaY1;
                    }
                    if ($Y2 > $this->pChartObject->GraphAreaY2) {
                        $X2 = $X2 - ($Y2 - $this->pChartObject->GraphAreaY2) / $RealM;
                        $Y2 = $this->pChartObject->GraphAreaY2;
                    }
                }
                $this->pChartObject->drawLine($X1, $Y1, $X2, $Y2, $Color);
            }
        }
    }
    /**
     *
     * @param string $ScatterSerieID
     * @param mixed $Points
     * @param array $Format
     * @return null|int
     */
    public function writeScatterLabel($ScatterSerieID, $Points, array $Format = [])
    {
        $DrawPoint = isset($Format["DrawPoint"]) ? $Format["DrawPoint"] : LABEL_POINT_BOX;
        $Decimals = isset($Format["Decimals"]) ? $Format["Decimals"] : null;
        $Data = $this->pDataObject->getData();
        if (!is_array($Points)) {
            $Points = [$Points];
        }
        if (!isset($Data["ScatterSeries"][$ScatterSerieID])) {
            return 0;
        }
        $Series = $Data["ScatterSeries"][$ScatterSerieID];
        $SerieX = $Series["X"];
        $SerieValuesX = $Data["Series"][$SerieX]["Data"];
        $SerieXAxis = $Data["Series"][$SerieX]["Axis"];
        $SerieY = $Series["Y"];
        $SerieValuesY = $Data["Series"][$SerieY]["Data"];
        $SerieYAxis = $Data["Series"][$SerieY]["Axis"];
        $PosArrayX = $this->getPosArray($SerieValuesX, $SerieXAxis);
        if (!is_array($PosArrayX)) {
            $Value = $PosArrayX;
            $PosArrayX = [];
            $PosArrayX[0] = $Value;
        }
        $PosArrayY = $this->getPosArray($SerieValuesY, $SerieYAxis);
        if (!is_array($PosArrayY)) {
            $Value = $PosArrayY;
            $PosArrayY = [];
            $PosArrayY[0] = $Value;
        }
        foreach ($Points as $Key => $Point) {
            if (isset($PosArrayX[$Point]) && isset($PosArrayY[$Point])) {
                $X = floor($PosArrayX[$Point]);
                $Y = floor($PosArrayY[$Point]);
                if ($DrawPoint == LABEL_POINT_CIRCLE) {
                    $this->pChartObject->drawFilledCircle($X, $Y, 3, ["R" => 255, "G" => 255, "B" => 255, "BorderR" => 0, "BorderG" => 0, "BorderB" => 0]);
                } elseif ($DrawPoint == LABEL_POINT_BOX) {
                    $this->pChartObject->drawFilledRectangle($X - 2, $Y - 2, $X + 2, $Y + 2, ["R" => 255, "G" => 255, "B" => 255, "BorderR" => 0, "BorderG" => 0, "BorderB" => 0]);
                }
                $Serie = [];
                $Serie["R"] = $Series["Color"]["R"];
                $Serie["G"] = $Series["Color"]["G"];
                $Serie["B"] = $Series["Color"]["B"];
                $Serie["Alpha"] = $Series["Color"]["Alpha"];
                $XAxisMode = $Data["Axis"][$SerieXAxis]["Display"];
                $XAxisFormat = $Data["Axis"][$SerieXAxis]["Format"];
                $XAxisUnit = $Data["Axis"][$SerieXAxis]["Unit"];
                if ($Decimals == null) {
                    $XValue = $SerieValuesX[$Point];
                } else {
                    $XValue = round($SerieValuesX[$Point], $Decimals);
                }
                $XValue = $this->pChartObject->scaleFormat($XValue, $XAxisMode, $XAxisFormat, $XAxisUnit);
                $YAxisMode = $Data["Axis"][$SerieYAxis]["Display"];
                $YAxisFormat = $Data["Axis"][$SerieYAxis]["Format"];
                $YAxisUnit = $Data["Axis"][$SerieYAxis]["Unit"];
                if ($Decimals == null) {
                    $YValue = $SerieValuesY[$Point];
                } else {
                    $YValue = round($SerieValuesY[$Point], $Decimals);
                }
                $YValue = $this->pChartObject->scaleFormat($YValue, $YAxisMode, $YAxisFormat, $YAxisUnit);
                $Caption = sprintf("%s / %s", $XValue, $YValue);
                if (isset($Series["Description"])) {
                    $Description = $Series["Description"];
                } else {
                    $Description = "No description";
                }
                $Series = [["Format" => $Serie, "Caption" => $Caption]];
                $this->pChartObject->drawLabelBox($X, $Y - 3, $Description, $Series, $Format);
            }
        }
    }
    /**
     * Draw a Scatter threshold
     * @param mixed $Value
     * @param array $Format
     * @return array
     */
    public function drawScatterThreshold($Value, array $Format = [])
    {
        $AxisID = isset($Format["AxisID"]) ? $Format["AxisID"] : 0;
        $R = isset($Format["R"]) ? $Format["R"] : 255;
        $G = isset($Format["G"]) ? $Format["G"] : 0;
        $B = isset($Format["B"]) ? $Format["B"] : 0;
        $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 50;
        $Weight = isset($Format["Weight"]) ? $Format["Weight"] : null;
        $Ticks = isset($Format["Ticks"]) ? $Format["Ticks"] : 3;
        $Wide = isset($Format["Wide"]) ? $Format["Wide"] : \false;
        $WideFactor = isset($Format["WideFactor"]) ? $Format["WideFactor"] : 5;
        $WriteCaption = isset($Format["WriteCaption"]) ? $Format["WriteCaption"] : \false;
        $Caption = isset($Format["Caption"]) ? $Format["Caption"] : null;
        $CaptionAlign = isset($Format["CaptionAlign"]) ? $Format["CaptionAlign"] : CAPTION_LEFT_TOP;
        $CaptionOffset = isset($Format["CaptionOffset"]) ? $Format["CaptionOffset"] : 10;
        $CaptionR = isset($Format["CaptionR"]) ? $Format["CaptionR"] : 255;
        $CaptionG = isset($Format["CaptionG"]) ? $Format["CaptionG"] : 255;
        $CaptionB = isset($Format["CaptionB"]) ? $Format["CaptionB"] : 255;
        $CaptionAlpha = isset($Format["CaptionAlpha"]) ? $Format["CaptionAlpha"] : 100;
        $DrawBox = isset($Format["DrawBox"]) ? $Format["DrawBox"] : \true;
        $DrawBoxBorder = isset($Format["DrawBoxBorder"]) ? $Format["DrawBoxBorder"] : \false;
        $BorderOffset = isset($Format["BorderOffset"]) ? $Format["BorderOffset"] : 5;
        $BoxRounded = isset($Format["BoxRounded"]) ? $Format["BoxRounded"] : \true;
        $RoundedRadius = isset($Format["RoundedRadius"]) ? $Format["RoundedRadius"] : 3;
        $BoxR = isset($Format["BoxR"]) ? $Format["BoxR"] : 0;
        $BoxG = isset($Format["BoxG"]) ? $Format["BoxG"] : 0;
        $BoxB = isset($Format["BoxB"]) ? $Format["BoxB"] : 0;
        $BoxAlpha = isset($Format["BoxAlpha"]) ? $Format["BoxAlpha"] : 20;
        $BoxSurrounding = isset($Format["BoxSurrounding"]) ? $Format["BoxSurrounding"] : "";
        $BoxBorderR = isset($Format["BoxBorderR"]) ? $Format["BoxBorderR"] : 255;
        $BoxBorderG = isset($Format["BoxBorderG"]) ? $Format["BoxBorderG"] : 255;
        $BoxBorderB = isset($Format["BoxBorderB"]) ? $Format["BoxBorderB"] : 255;
        $BoxBorderAlpha = isset($Format["BoxBorderAlpha"]) ? $Format["BoxBorderAlpha"] : 100;
        $CaptionSettings = ["DrawBox" => $DrawBox, "DrawBoxBorder" => $DrawBoxBorder, "BorderOffset" => $BorderOffset, "BoxRounded" => $BoxRounded, "RoundedRadius" => $RoundedRadius, "BoxR" => $BoxR, "BoxG" => $BoxG, "BoxB" => $BoxB, "BoxAlpha" => $BoxAlpha, "BoxSurrounding" => $BoxSurrounding, "BoxBorderR" => $BoxBorderR, "BoxBorderG" => $BoxBorderG, "BoxBorderB" => $BoxBorderB, "BoxBorderAlpha" => $BoxBorderAlpha, "R" => $CaptionR, "G" => $CaptionG, "B" => $CaptionB, "Alpha" => $CaptionAlpha];
        if ($Caption == null) {
            $Caption = $Value;
        }
        $Data = $this->pDataObject->getData();
        if (!isset($Data["Axis"][$AxisID])) {
            return -1;
        }
        if ($Data["Axis"][$AxisID]["Identity"] == AXIS_Y) {
            $X1 = $this->pChartObject->GraphAreaX1 + $Data["Axis"][$AxisID]["Margin"];
            $X2 = $this->pChartObject->GraphAreaX2 - $Data["Axis"][$AxisID]["Margin"];
            $Y = $this->getPosArray($Value, $AxisID);
            $this->pChartObject->drawLine($X1, $Y, $X2, $Y, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "Ticks" => $Ticks, "Weight" => $Weight]);
            if ($Wide) {
                $this->pChartObject->drawLine($X1, $Y - 1, $X2, $Y - 1, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha / $WideFactor, "Ticks" => $Ticks]);
                $this->pChartObject->drawLine($X1, $Y + 1, $X2, $Y + 1, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha / $WideFactor, "Ticks" => $Ticks]);
            }
            if ($WriteCaption) {
                if ($CaptionAlign == CAPTION_LEFT_TOP) {
                    $X = $this->pChartObject->GraphAreaX1 + $Data["Axis"][$AxisID]["Margin"] + $CaptionOffset;
                    $CaptionSettings["Align"] = TEXT_ALIGN_MIDDLELEFT;
                } else {
                    $X = $this->pChartObject->GraphAreaX2 - $Data["Axis"][$AxisID]["Margin"] - $CaptionOffset;
                    $CaptionSettings["Align"] = TEXT_ALIGN_MIDDLERIGHT;
                }
                $this->pChartObject->drawText($X, $Y, $Caption, $CaptionSettings);
            }
            return ["Y" => $Y];
        } elseif ($Data["Axis"][$AxisID]["Identity"] == AXIS_X) {
            $X = $this->getPosArray($Value, $AxisID);
            $Y1 = $this->pChartObject->GraphAreaY1 + $Data["Axis"][$AxisID]["Margin"];
            $Y2 = $this->pChartObject->GraphAreaY2 - $Data["Axis"][$AxisID]["Margin"];
            $this->pChartObject->drawLine($X, $Y1, $X, $Y2, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "Ticks" => $Ticks, "Weight" => $Weight]);
            if ($Wide) {
                $this->pChartObject->drawLine($X - 1, $Y1, $X - 1, $Y2, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha / $WideFactor, "Ticks" => $Ticks]);
                $this->pChartObject->drawLine($X + 1, $Y1, $X + 1, $Y2, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha / $WideFactor, "Ticks" => $Ticks]);
            }
            if ($WriteCaption) {
                if ($CaptionAlign == CAPTION_LEFT_TOP) {
                    $Y = $this->pChartObject->GraphAreaY1 + $Data["Axis"][$AxisID]["Margin"] + $CaptionOffset;
                    $CaptionSettings["Align"] = TEXT_ALIGN_TOPMIDDLE;
                } else {
                    $Y = $this->pChartObject->GraphAreaY2 - $Data["Axis"][$AxisID]["Margin"] - $CaptionOffset;
                    $CaptionSettings["Align"] = TEXT_ALIGN_BOTTOMMIDDLE;
                }
                $CaptionSettings["Align"] = TEXT_ALIGN_TOPMIDDLE;
                $this->pChartObject->drawText($X, $Y, $Caption, $CaptionSettings);
            }
            return ["X" => $X];
        }
    }
    /**
     * Draw a Scatter threshold area
     * @param int|float $Value1
     * @param int|float $Value2
     * @param array $Format
     * @return type
     */
    public function drawScatterThresholdArea($Value1, $Value2, array $Format = [])
    {
        $AxisID = isset($Format["AxisID"]) ? $Format["AxisID"] : 0;
        $R = isset($Format["R"]) ? $Format["R"] : 255;
        $G = isset($Format["G"]) ? $Format["G"] : 0;
        $B = isset($Format["B"]) ? $Format["B"] : 0;
        $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 20;
        $Border = isset($Format["Border"]) ? $Format["Border"] : \true;
        $BorderR = isset($Format["BorderR"]) ? $Format["BorderR"] : $R;
        $BorderG = isset($Format["BorderG"]) ? $Format["BorderG"] : $G;
        $BorderB = isset($Format["BorderB"]) ? $Format["BorderB"] : $B;
        $BorderAlpha = isset($Format["BorderAlpha"]) ? $Format["BorderAlpha"] : $Alpha + 20;
        $BorderTicks = isset($Format["BorderTicks"]) ? $Format["BorderTicks"] : 2;
        $AreaName = isset($Format["AreaName"]) ? $Format["AreaName"] : "La ouate de phoque";
        //null;
        $NameAngle = isset($Format["NameAngle"]) ? $Format["NameAngle"] : ZONE_NAME_ANGLE_AUTO;
        $NameR = isset($Format["NameR"]) ? $Format["NameR"] : 255;
        $NameG = isset($Format["NameG"]) ? $Format["NameG"] : 255;
        $NameB = isset($Format["NameB"]) ? $Format["NameB"] : 255;
        $NameAlpha = isset($Format["NameAlpha"]) ? $Format["NameAlpha"] : 100;
        $DisableShadowOnArea = isset($Format["DisableShadowOnArea"]) ? $Format["DisableShadowOnArea"] : \true;
        if ($Value1 > $Value2) {
            list($Value1, $Value2) = [$Value2, $Value1];
        }
        $RestoreShadow = $this->pChartObject->Shadow;
        if ($DisableShadowOnArea && $this->pChartObject->Shadow) {
            $this->pChartObject->Shadow = \false;
        }
        if ($BorderAlpha > 100) {
            $BorderAlpha = 100;
        }
        $Data = $this->pDataObject->getData();
        if (!isset($Data["Axis"][$AxisID])) {
            return -1;
        }
        if ($Data["Axis"][$AxisID]["Identity"] == AXIS_X) {
            $Y1 = $this->pChartObject->GraphAreaY1 + $Data["Axis"][$AxisID]["Margin"];
            $Y2 = $this->pChartObject->GraphAreaY2 - $Data["Axis"][$AxisID]["Margin"];
            $X1 = $this->getPosArray($Value1, $AxisID);
            $X2 = $this->getPosArray($Value2, $AxisID);
            if ($X1 <= $this->pChartObject->GraphAreaX1) {
                $X1 = $this->pChartObject->GraphAreaX1 + $Data["Axis"][$AxisID]["Margin"];
            }
            if ($X2 >= $this->pChartObject->GraphAreaX2) {
                $X2 = $this->pChartObject->GraphAreaX2 - $Data["Axis"][$AxisID]["Margin"];
            }
            $this->pChartObject->drawFilledRectangle($X1, $Y1, $X2, $Y2, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha]);
            if ($Border) {
                $this->pChartObject->drawLine($X1, $Y1, $X1, $Y2, ["R" => $BorderR, "G" => $BorderG, "B" => $BorderB, "Alpha" => $BorderAlpha, "Ticks" => $BorderTicks]);
                $this->pChartObject->drawLine($X2, $Y1, $X2, $Y2, ["R" => $BorderR, "G" => $BorderG, "B" => $BorderB, "Alpha" => $BorderAlpha, "Ticks" => $BorderTicks]);
            }
            if ($AreaName != null) {
                $XPos = ($X2 - $X1) / 2 + $X1;
                $YPos = ($Y2 - $Y1) / 2 + $Y1;
                if ($NameAngle == ZONE_NAME_ANGLE_AUTO) {
                    $TxtPos = $this->pChartObject->getTextBox($XPos, $YPos, $this->pChartObject->FontName, $this->pChartObject->FontSize, 0, $AreaName);
                    $TxtWidth = $TxtPos[1]["X"] - $TxtPos[0]["X"];
                    if (abs($X2 - $X1) > $TxtWidth) {
                        $NameAngle = 0;
                    } else {
                        $NameAngle = 90;
                    }
                }
                $this->pChartObject->Shadow = $RestoreShadow;
                $this->pChartObject->drawText($XPos, $YPos, $AreaName, ["R" => $NameR, "G" => $NameG, "B" => $NameB, "Alpha" => $NameAlpha, "Angle" => $NameAngle, "Align" => TEXT_ALIGN_MIDDLEMIDDLE]);
                if ($DisableShadowOnArea) {
                    $this->pChartObject->Shadow = \false;
                }
            }
            $this->pChartObject->Shadow = $RestoreShadow;
            return ["X1" => $X1, "X2" => $X2];
        } elseif ($Data["Axis"][$AxisID]["Identity"] == AXIS_Y) {
            $X1 = $this->pChartObject->GraphAreaX1 + $Data["Axis"][$AxisID]["Margin"];
            $X2 = $this->pChartObject->GraphAreaX2 - $Data["Axis"][$AxisID]["Margin"];
            $Y1 = $this->getPosArray($Value1, $AxisID);
            $Y2 = $this->getPosArray($Value2, $AxisID);
            if ($Y1 >= $this->pChartObject->GraphAreaY2) {
                $Y1 = $this->pChartObject->GraphAreaY2 - $Data["Axis"][$AxisID]["Margin"];
            }
            if ($Y2 <= $this->pChartObject->GraphAreaY1) {
                $Y2 = $this->pChartObject->GraphAreaY1 + $Data["Axis"][$AxisID]["Margin"];
            }
            $this->pChartObject->drawFilledRectangle($X1, $Y1, $X2, $Y2, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha]);
            if ($Border) {
                $this->pChartObject->drawLine($X1, $Y1, $X2, $Y1, ["R" => $BorderR, "G" => $BorderG, "B" => $BorderB, "Alpha" => $BorderAlpha, "Ticks" => $BorderTicks]);
                $this->pChartObject->drawLine($X1, $Y2, $X2, $Y2, ["R" => $BorderR, "G" => $BorderG, "B" => $BorderB, "Alpha" => $BorderAlpha, "Ticks" => $BorderTicks]);
            }
            if ($AreaName != null) {
                $XPos = ($X2 - $X1) / 2 + $X1;
                $YPos = ($Y2 - $Y1) / 2 + $Y1;
                $this->pChartObject->Shadow = $RestoreShadow;
                $this->pChartObject->drawText($YPos, $XPos, $AreaName, ["R" => $NameR, "G" => $NameG, "B" => $NameB, "Alpha" => $NameAlpha, "Angle" => 0, "Align" => TEXT_ALIGN_MIDDLEMIDDLE]);
                if ($DisableShadowOnArea) {
                    ${$this}->pChartObject->Shadow = \false;
                }
            }
            $this->pChartObject->Shadow = $RestoreShadow;
            return ["Y1" => $Y1, "Y2" => $Y2];
        }
    }
}
