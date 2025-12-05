<?php

namespace CpChart\Chart;

use CpChart\Data;
use CpChart\Image;
/**
 * Radar - class to draw radar charts
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
class Radar
{
    /**
     * @var Image
     */
    public $pChartObject;
    /**
     * Draw a radar chart
     * @param Image $Object
     * @param Data $Values
     * @param array $Format
     */
    public function drawRadar(Image $Object, Data $Values, array $Format = [])
    {
        $this->pChartObject = $Object;
        $FixedMax = isset($Format["FixedMax"]) ? $Format["FixedMax"] : VOID;
        $AxisR = isset($Format["AxisR"]) ? $Format["AxisR"] : 60;
        $AxisG = isset($Format["AxisG"]) ? $Format["AxisG"] : 60;
        $AxisB = isset($Format["AxisB"]) ? $Format["AxisB"] : 60;
        $AxisAlpha = isset($Format["AxisAlpha"]) ? $Format["AxisAlpha"] : 50;
        $AxisRotation = isset($Format["AxisRotation"]) ? $Format["AxisRotation"] : 0;
        $DrawTicks = isset($Format["DrawTicks"]) ? $Format["DrawTicks"] : \true;
        $TicksLength = isset($Format["TicksLength"]) ? $Format["TicksLength"] : 2;
        $DrawAxisValues = isset($Format["DrawAxisValues"]) ? $Format["DrawAxisValues"] : \true;
        $AxisBoxRounded = isset($Format["AxisBoxRounded"]) ? $Format["AxisBoxRounded"] : \true;
        $AxisFontName = isset($Format["AxisFontName"]) ? $Format["AxisFontName"] : $this->pChartObject->FontName;
        $AxisFontSize = isset($Format["AxisFontSize"]) ? $Format["AxisFontSize"] : $this->pChartObject->FontSize;
        $WriteValues = isset($Format["WriteValues"]) ? $Format["WriteValues"] : \false;
        $WriteValuesInBubble = isset($Format["WriteValuesInBubble"]) ? $Format["WriteValuesInBubble"] : \true;
        $ValueFontName = isset($Format["ValueFontName"]) ? $Format["ValueFontName"] : $this->pChartObject->FontName;
        $ValueFontSize = isset($Format["ValueFontSize"]) ? $Format["ValueFontSize"] : $this->pChartObject->FontSize;
        $ValuePadding = isset($Format["ValuePadding"]) ? $Format["ValuePadding"] : 4;
        $OuterBubbleRadius = isset($Format["OuterBubbleRadius"]) ? $Format["OuterBubbleRadius"] : 2;
        $OuterBubbleR = isset($Format["OuterBubbleR"]) ? $Format["OuterBubbleR"] : VOID;
        $OuterBubbleG = isset($Format["OuterBubbleG"]) ? $Format["OuterBubbleG"] : VOID;
        $OuterBubbleB = isset($Format["OuterBubbleB"]) ? $Format["OuterBubbleB"] : VOID;
        $OuterBubbleAlpha = isset($Format["OuterBubbleAlpha"]) ? $Format["OuterBubbleAlpha"] : 100;
        $InnerBubbleR = isset($Format["InnerBubbleR"]) ? $Format["InnerBubbleR"] : 255;
        $InnerBubbleG = isset($Format["InnerBubbleG"]) ? $Format["InnerBubbleG"] : 255;
        $InnerBubbleB = isset($Format["InnerBubbleB"]) ? $Format["InnerBubbleB"] : 255;
        $InnerBubbleAlpha = isset($Format["InnerBubbleAlpha"]) ? $Format["InnerBubbleAlpha"] : 100;
        $DrawBackground = isset($Format["DrawBackground"]) ? $Format["DrawBackground"] : \true;
        $BackgroundR = isset($Format["BackgroundR"]) ? $Format["BackgroundR"] : 255;
        $BackgroundG = isset($Format["BackgroundG"]) ? $Format["BackgroundG"] : 255;
        $BackgroundB = isset($Format["BackgroundB"]) ? $Format["BackgroundB"] : 255;
        $BackgroundAlpha = isset($Format["BackgroundAlpha"]) ? $Format["BackgroundAlpha"] : 50;
        $BackgroundGradient = isset($Format["BackgroundGradient"]) ? $Format["BackgroundGradient"] : null;
        $Layout = isset($Format["Layout"]) ? $Format["Layout"] : RADAR_LAYOUT_STAR;
        $SegmentHeight = isset($Format["SegmentHeight"]) ? $Format["SegmentHeight"] : SEGMENT_HEIGHT_AUTO;
        $Segments = isset($Format["Segments"]) ? $Format["Segments"] : 4;
        $WriteLabels = isset($Format["WriteLabels"]) ? $Format["WriteLabels"] : \true;
        $SkipLabels = isset($Format["SkipLabels"]) ? $Format["SkipLabels"] : 1;
        $LabelMiddle = isset($Format["LabelMiddle"]) ? $Format["LabelMiddle"] : \false;
        $LabelsBackground = isset($Format["LabelsBackground"]) ? $Format["LabelsBackground"] : \true;
        $LabelsBGR = isset($Format["LabelsBGR"]) ? $Format["LabelsBGR"] : 255;
        $LabelsBGG = isset($Format["LabelsBGR"]) ? $Format["LabelsBGG"] : 255;
        $LabelsBGB = isset($Format["LabelsBGR"]) ? $Format["LabelsBGB"] : 255;
        $LabelsBGAlpha = isset($Format["LabelsBGAlpha"]) ? $Format["LabelsBGAlpha"] : 50;
        $LabelPos = isset($Format["LabelPos"]) ? $Format["LabelPos"] : RADAR_LABELS_ROTATED;
        $LabelPadding = isset($Format["LabelPadding"]) ? $Format["LabelPadding"] : 4;
        $DrawPoints = isset($Format["DrawPoints"]) ? $Format["DrawPoints"] : \true;
        $PointRadius = isset($Format["PointRadius"]) ? $Format["PointRadius"] : 4;
        $PointSurrounding = isset($Format["PointRadius"]) ? $Format["PointRadius"] : -30;
        $DrawLines = isset($Format["DrawLines"]) ? $Format["DrawLines"] : \true;
        $LineLoopStart = isset($Format["LineLoopStart"]) ? $Format["LineLoopStart"] : \true;
        $DrawPoly = isset($Format["DrawPoly"]) ? $Format["DrawPoly"] : \false;
        $PolyAlpha = isset($Format["PolyAlpha"]) ? $Format["PolyAlpha"] : 40;
        $FontSize = $Object->FontSize;
        $X1 = $Object->GraphAreaX1;
        $Y1 = $Object->GraphAreaY1;
        $X2 = $Object->GraphAreaX2;
        $Y2 = $Object->GraphAreaY2;
        $RecordImageMap = isset($Format["RecordImageMap"]) ? $Format["RecordImageMap"] : \false;
        /* Cancel default tick length if ticks not enabled */
        if ($DrawTicks == \false) {
            $TicksLength = 0;
        }
        /* Data Processing */
        $Data = $Values->getData();
        $Palette = $Values->getPalette();
        /* Catch the number of required axis */
        $LabelSerie = $Data["Abscissa"];
        if ($LabelSerie) {
            $Points = count($Data["Series"][$LabelSerie]["Data"]);
        } else {
            $Points = 0;
            foreach ($Data["Series"] as $SerieName => $DataArray) {
                if (count($DataArray["Data"]) > $Points) {
                    $Points = count($DataArray["Data"]);
                }
            }
        }
        /* Draw the axis */
        $CenterX = ($X2 - $X1) / 2 + $X1;
        $CenterY = ($Y2 - $Y1) / 2 + $Y1;
        $EdgeHeight = min(($X2 - $X1) / 2, ($Y2 - $Y1) / 2);
        if ($WriteLabels) {
            $EdgeHeight = $EdgeHeight - $FontSize - $LabelPadding - $TicksLength;
        }
        /* Determine the scale if set to automatic */
        if ($SegmentHeight == SEGMENT_HEIGHT_AUTO) {
            if ($FixedMax != VOID) {
                $Max = $FixedMax;
            } else {
                $Max = 0;
                foreach ($Data["Series"] as $SerieName => $DataArray) {
                    if ($SerieName != $LabelSerie) {
                        if (max($DataArray["Data"]) > $Max) {
                            $Max = max($DataArray["Data"]);
                        }
                    }
                }
            }
            $MaxSegments = $EdgeHeight / 20;
            $Scale = $Object->computeScale(0, $Max, $MaxSegments, [1, 2, 5]);
            $Segments = $Scale["Rows"];
            $SegmentHeight = $Scale["RowHeight"];
        }
        if ($LabelMiddle && $SkipLabels == 1) {
            $Axisoffset = 360 / $Points / 2;
        } elseif ($LabelMiddle && $SkipLabels != 1) {
            $Axisoffset = 360 / ($Points / $SkipLabels) / 2;
        } elseif (!$LabelMiddle) {
            $Axisoffset = 0;
        }
        /* Background processing */
        if ($DrawBackground) {
            $RestoreShadow = $Object->Shadow;
            $Object->Shadow = \false;
            if ($BackgroundGradient == null) {
                if ($Layout == RADAR_LAYOUT_STAR) {
                    $Color = ["R" => $BackgroundR, "G" => $BackgroundG, "B" => $BackgroundB, "Alpha" => $BackgroundAlpha];
                    $PointArray = [];
                    for ($i = 0; $i <= 360; $i = $i + 360 / $Points) {
                        $PointArray[] = cos(deg2rad($i + $AxisRotation)) * $EdgeHeight + $CenterX;
                        $PointArray[] = sin(deg2rad($i + $AxisRotation)) * $EdgeHeight + $CenterY;
                    }
                    $Object->drawPolygon($PointArray, $Color);
                } elseif ($Layout == RADAR_LAYOUT_CIRCLE) {
                    $Color = ["R" => $BackgroundR, "G" => $BackgroundG, "B" => $BackgroundB, "Alpha" => $BackgroundAlpha];
                    $Object->drawFilledCircle($CenterX, $CenterY, $EdgeHeight, $Color);
                }
            } else {
                $GradientROffset = ($BackgroundGradient["EndR"] - $BackgroundGradient["StartR"]) / $Segments;
                $GradientGOffset = ($BackgroundGradient["EndG"] - $BackgroundGradient["StartG"]) / $Segments;
                $GradientBOffset = ($BackgroundGradient["EndB"] - $BackgroundGradient["StartB"]) / $Segments;
                $GradientAlphaOffset = ($BackgroundGradient["EndAlpha"] - $BackgroundGradient["StartAlpha"]) / $Segments;
                if ($Layout == RADAR_LAYOUT_STAR) {
                    for ($j = $Segments; $j >= 1; $j--) {
                        $Color = ["R" => $BackgroundGradient["StartR"] + $GradientROffset * $j, "G" => $BackgroundGradient["StartG"] + $GradientGOffset * $j, "B" => $BackgroundGradient["StartB"] + $GradientBOffset * $j, "Alpha" => $BackgroundGradient["StartAlpha"] + $GradientAlphaOffset * $j];
                        $PointArray = [];
                        for ($i = 0; $i <= 360; $i = $i + 360 / $Points) {
                            $PointArray[] = cos(deg2rad($i + $AxisRotation)) * ($EdgeHeight / $Segments) * $j + $CenterX;
                            $PointArray[] = sin(deg2rad($i + $AxisRotation)) * ($EdgeHeight / $Segments) * $j + $CenterY;
                        }
                        $Object->drawPolygon($PointArray, $Color);
                    }
                } elseif ($Layout == RADAR_LAYOUT_CIRCLE) {
                    for ($j = $Segments; $j >= 1; $j--) {
                        $Color = ["R" => $BackgroundGradient["StartR"] + $GradientROffset * $j, "G" => $BackgroundGradient["StartG"] + $GradientGOffset * $j, "B" => $BackgroundGradient["StartB"] + $GradientBOffset * $j, "Alpha" => $BackgroundGradient["StartAlpha"] + $GradientAlphaOffset * $j];
                        $Object->drawFilledCircle($CenterX, $CenterY, $EdgeHeight / $Segments * $j, $Color);
                    }
                }
            }
            $Object->Shadow = $RestoreShadow;
        }
        /* Axis to axis lines */
        $Color = ["R" => $AxisR, "G" => $AxisG, "B" => $AxisB, "Alpha" => $AxisAlpha];
        $ColorDotted = ["R" => $AxisR, "G" => $AxisG, "B" => $AxisB, "Alpha" => $AxisAlpha * 0.8, "Ticks" => 2];
        if ($Layout == RADAR_LAYOUT_STAR) {
            for ($j = 1; $j <= $Segments; $j++) {
                for ($i = 0; $i < 360; $i = $i + 360 / $Points) {
                    $EdgeX1 = cos(deg2rad($i + $AxisRotation)) * ($EdgeHeight / $Segments) * $j + $CenterX;
                    $EdgeY1 = sin(deg2rad($i + $AxisRotation)) * ($EdgeHeight / $Segments) * $j + $CenterY;
                    $EdgeX2 = cos(deg2rad($i + $AxisRotation + 360 / $Points)) * ($EdgeHeight / $Segments) * $j + $CenterX;
                    $EdgeY2 = sin(deg2rad($i + $AxisRotation + 360 / $Points)) * ($EdgeHeight / $Segments) * $j + $CenterY;
                    $Object->drawLine($EdgeX1, $EdgeY1, $EdgeX2, $EdgeY2, $Color);
                }
            }
        } elseif ($Layout == RADAR_LAYOUT_CIRCLE) {
            for ($j = 1; $j <= $Segments; $j++) {
                $Radius = $EdgeHeight / $Segments * $j;
                $Object->drawCircle($CenterX, $CenterY, $Radius, $Radius, $Color);
            }
        }
        if ($DrawAxisValues) {
            if ($LabelsBackground) {
                $Options = ["DrawBox" => \true, "Align" => TEXT_ALIGN_MIDDLEMIDDLE, "BoxR" => $LabelsBGR, "BoxG" => $LabelsBGG, "BoxB" => $LabelsBGB, "BoxAlpha" => $LabelsBGAlpha];
            } else {
                $Options = ["Align" => TEXT_ALIGN_MIDDLEMIDDLE];
            }
            if ($AxisBoxRounded) {
                $Options["BoxRounded"] = \true;
            }
            $Options["FontName"] = $AxisFontName;
            $Options["FontSize"] = $AxisFontSize;
            $Angle = 360 / ($Points * 2);
            for ($j = 1; $j <= $Segments; $j++) {
                $Label = $j * $SegmentHeight;
                if ($Layout == RADAR_LAYOUT_CIRCLE) {
                    $EdgeX1 = cos(deg2rad($Angle + $AxisRotation)) * ($EdgeHeight / $Segments) * $j + $CenterX;
                    $EdgeY1 = sin(deg2rad($Angle + $AxisRotation)) * ($EdgeHeight / $Segments) * $j + $CenterY;
                } elseif ($Layout == RADAR_LAYOUT_STAR) {
                    $EdgeX1 = cos(deg2rad($AxisRotation)) * ($EdgeHeight / $Segments) * $j + $CenterX;
                    $EdgeY1 = sin(deg2rad($AxisRotation)) * ($EdgeHeight / $Segments) * $j + $CenterY;
                    $EdgeX2 = cos(deg2rad(360 / $Points + $AxisRotation)) * ($EdgeHeight / $Segments) * $j + $CenterX;
                    $EdgeY2 = sin(deg2rad(360 / $Points + $AxisRotation)) * ($EdgeHeight / $Segments) * $j + $CenterY;
                    $EdgeX1 = ($EdgeX2 - $EdgeX1) / 2 + $EdgeX1;
                    $EdgeY1 = ($EdgeY2 - $EdgeY1) / 2 + $EdgeY1;
                }
                $Object->drawText($EdgeX1, $EdgeY1, $Label, $Options);
            }
        }
        /* Axis lines */
        $ID = 0;
        for ($i = 0; $i < 360; $i = $i + 360 / $Points) {
            $EdgeX = cos(deg2rad($i + $AxisRotation)) * ($EdgeHeight + $TicksLength) + $CenterX;
            $EdgeY = sin(deg2rad($i + $AxisRotation)) * ($EdgeHeight + $TicksLength) + $CenterY;
            if ($ID % $SkipLabels == 0) {
                $Object->drawLine($CenterX, $CenterY, $EdgeX, $EdgeY, $Color);
            } else {
                $Object->drawLine($CenterX, $CenterY, $EdgeX, $EdgeY, $ColorDotted);
            }
            if ($WriteLabels) {
                $LabelX = cos(deg2rad($i + $AxisRotation + $Axisoffset)) * ($EdgeHeight + $LabelPadding + $TicksLength) + $CenterX;
                $LabelY = sin(deg2rad($i + $AxisRotation + $Axisoffset)) * ($EdgeHeight + $LabelPadding + $TicksLength) + $CenterY;
                if ($LabelSerie) {
                    $Label = isset($Data["Series"][$LabelSerie]["Data"][$ID]) ? $Data["Series"][$LabelSerie]["Data"][$ID] : "";
                } else {
                    $Label = $ID;
                }
                if ($ID % $SkipLabels == 0) {
                    if ($LabelPos == RADAR_LABELS_ROTATED) {
                        $Object->drawText($LabelX, $LabelY, $Label, ["Angle" => 360 - ($i + $AxisRotation + $Axisoffset) - 90, "Align" => TEXT_ALIGN_BOTTOMMIDDLE]);
                    } else {
                        if (floor($LabelX) == floor($CenterX) && floor($LabelY) < floor($CenterY)) {
                            $Object->drawText($LabelX, $LabelY, $Label, ["Align" => TEXT_ALIGN_BOTTOMMIDDLE]);
                        }
                        if (floor($LabelX) > floor($CenterX) && floor($LabelY) < floor($CenterY)) {
                            $Object->drawText($LabelX, $LabelY, $Label, ["Align" => TEXT_ALIGN_BOTTOMLEFT]);
                        }
                        if (floor($LabelX) > floor($CenterX) && floor($LabelY) == floor($CenterY)) {
                            $Object->drawText($LabelX, $LabelY, $Label, ["Align" => TEXT_ALIGN_MIDDLELEFT]);
                        }
                        if (floor($LabelX) > floor($CenterX) && floor($LabelY) > floor($CenterY)) {
                            $Object->drawText($LabelX, $LabelY, $Label, ["Align" => TEXT_ALIGN_TOPLEFT]);
                        }
                        if (floor($LabelX) < floor($CenterX) && floor($LabelY) < floor($CenterY)) {
                            $Object->drawText($LabelX, $LabelY, $Label, ["Align" => TEXT_ALIGN_BOTTOMRIGHT]);
                        }
                        if (floor($LabelX) < floor($CenterX) && floor($LabelY) == floor($CenterY)) {
                            $Object->drawText($LabelX, $LabelY, $Label, ["Align" => TEXT_ALIGN_MIDDLERIGHT]);
                        }
                        if (floor($LabelX) < floor($CenterX) && floor($LabelY) > floor($CenterY)) {
                            $Object->drawText($LabelX, $LabelY, $Label, ["Align" => TEXT_ALIGN_TOPRIGHT]);
                        }
                        if (floor($LabelX) == floor($CenterX) && floor($LabelY) > floor($CenterY)) {
                            $Object->drawText($LabelX, $LabelY, $Label, ["Align" => TEXT_ALIGN_TOPMIDDLE]);
                        }
                    }
                }
            }
            $ID++;
        }
        /* Compute the plots position */
        $ID = 0;
        $Plot = [];
        foreach ($Data["Series"] as $SerieName => $DataS) {
            if ($SerieName != $LabelSerie) {
                $Color = ["R" => $Palette[$ID]["R"], "G" => $Palette[$ID]["G"], "B" => $Palette[$ID]["B"], "Alpha" => $Palette[$ID]["Alpha"], "Surrounding" => $PointSurrounding];
                foreach ($DataS["Data"] as $Key => $Value) {
                    $Angle = 360 / $Points * $Key;
                    $Length = $EdgeHeight / ($Segments * $SegmentHeight) * $Value;
                    $X = cos(deg2rad($Angle + $AxisRotation)) * $Length + $CenterX;
                    $Y = sin(deg2rad($Angle + $AxisRotation)) * $Length + $CenterY;
                    $Plot[$ID][] = [$X, $Y, $Value];
                    if ($RecordImageMap) {
                        $this->pChartObject->addToImageMap("CIRCLE", sprintf("%s,%s,%s", floor($X), floor($Y), floor($PointRadius)), $this->pChartObject->toHTMLColor($Palette[$ID]["R"], $Palette[$ID]["G"], $Palette[$ID]["B"]), $DataS["Description"], $Data["Series"][$LabelSerie]["Data"][$Key] . " = " . $Value);
                    }
                }
                $ID++;
            }
        }
        /* Draw all that stuff! */
        foreach ($Plot as $ID => $Points) {
            $Color = ["R" => $Palette[$ID]["R"], "G" => $Palette[$ID]["G"], "B" => $Palette[$ID]["B"], "Alpha" => $Palette[$ID]["Alpha"], "Surrounding" => $PointSurrounding];
            /* Draw the polygons */
            if ($DrawPoly) {
                if ($PolyAlpha != null) {
                    $Color = ["R" => $Palette[$ID]["R"], "G" => $Palette[$ID]["G"], "B" => $Palette[$ID]["B"], "Alpha" => $PolyAlpha, "Surrounding" => $PointSurrounding];
                }
                $PointsArray = [];
                for ($i = 0; $i < count($Points); $i++) {
                    $PointsArray[] = $Points[$i][0];
                    $PointsArray[] = $Points[$i][1];
                }
                $Object->drawPolygon($PointsArray, $Color);
            }
            $Color = ["R" => $Palette[$ID]["R"], "G" => $Palette[$ID]["G"], "B" => $Palette[$ID]["B"], "Alpha" => $Palette[$ID]["Alpha"], "Surrounding" => $PointSurrounding];
            /* Bubble and labels settings */
            $TextSettings = ["Align" => TEXT_ALIGN_MIDDLEMIDDLE, "FontName" => $ValueFontName, "FontSize" => $ValueFontSize, "R" => $Palette[$ID]["R"], "G" => $Palette[$ID]["G"], "B" => $Palette[$ID]["B"]];
            $InnerColor = ["R" => $InnerBubbleR, "G" => $InnerBubbleG, "B" => $InnerBubbleB, "Alpha" => $InnerBubbleAlpha];
            if ($OuterBubbleR != VOID) {
                $OuterColor = ["R" => $OuterBubbleR, "G" => $OuterBubbleG, "B" => $OuterBubbleB, "Alpha" => $OuterBubbleAlpha];
            } else {
                $OuterColor = ["R" => $Palette[$ID]["R"] + 20, "G" => $Palette[$ID]["G"] + 20, "B" => $Palette[$ID]["B"] + 20, "Alpha" => $Palette[$ID]["Alpha"]];
            }
            /* Loop to the starting points if asked */
            if ($LineLoopStart && $DrawLines) {
                $Object->drawLine($Points[count($Points) - 1][0], $Points[count($Points) - 1][1], $Points[0][0], $Points[0][1], $Color);
            }
            /* Draw the lines & points */
            for ($i = 0; $i < count($Points); $i++) {
                if ($DrawLines && $i < count($Points) - 1) {
                    $Object->drawLine($Points[$i][0], $Points[$i][1], $Points[$i + 1][0], $Points[$i + 1][1], $Color);
                }
                if ($DrawPoints) {
                    $Object->drawFilledCircle($Points[$i][0], $Points[$i][1], $PointRadius, $Color);
                }
                if ($WriteValuesInBubble && $WriteValues) {
                    $TxtPos = $this->pChartObject->getTextBox($Points[$i][0], $Points[$i][1], $ValueFontName, $ValueFontSize, 0, $Points[$i][2]);
                    $Radius = floor(($TxtPos[1]["X"] - $TxtPos[0]["X"] + $ValuePadding * 2) / 2);
                    $this->pChartObject->drawFilledCircle($Points[$i][0], $Points[$i][1], $Radius + $OuterBubbleRadius, $OuterColor);
                    $this->pChartObject->drawFilledCircle($Points[$i][0], $Points[$i][1], $Radius, $InnerColor);
                }
                if ($WriteValues) {
                    $this->pChartObject->drawText($Points[$i][0] - 1, $Points[$i][1] - 1, $Points[$i][2], $TextSettings);
                }
            }
        }
    }
    /**
     * Draw a radar chart
     * @param Image $Object
     * @param Data $Values
     * @param array $Format
     */
    public function drawPolar(Image $Object, Data $Values, array $Format = [])
    {
        $this->pChartObject = $Object;
        $FixedMax = isset($Format["FixedMax"]) ? $Format["FixedMax"] : VOID;
        $AxisR = isset($Format["AxisR"]) ? $Format["AxisR"] : 60;
        $AxisG = isset($Format["AxisG"]) ? $Format["AxisG"] : 60;
        $AxisB = isset($Format["AxisB"]) ? $Format["AxisB"] : 60;
        $AxisAlpha = isset($Format["AxisAlpha"]) ? $Format["AxisAlpha"] : 50;
        $AxisRotation = isset($Format["AxisRotation"]) ? $Format["AxisRotation"] : -90;
        $DrawTicks = isset($Format["DrawTicks"]) ? $Format["DrawTicks"] : \true;
        $TicksLength = isset($Format["TicksLength"]) ? $Format["TicksLength"] : 2;
        $DrawAxisValues = isset($Format["DrawAxisValues"]) ? $Format["DrawAxisValues"] : \true;
        $AxisBoxRounded = isset($Format["AxisBoxRounded"]) ? $Format["AxisBoxRounded"] : \true;
        $AxisFontName = isset($Format["FontName"]) ? $Format["FontName"] : $this->pChartObject->FontName;
        $AxisFontSize = isset($Format["FontSize"]) ? $Format["FontSize"] : $this->pChartObject->FontSize;
        $WriteValues = isset($Format["WriteValues"]) ? $Format["WriteValues"] : \false;
        $WriteValuesInBubble = isset($Format["WriteValuesInBubble"]) ? $Format["WriteValuesInBubble"] : \true;
        $ValueFontName = isset($Format["ValueFontName"]) ? $Format["ValueFontName"] : $this->pChartObject->FontName;
        $ValueFontSize = isset($Format["ValueFontSize"]) ? $Format["ValueFontSize"] : $this->pChartObject->FontSize;
        $ValuePadding = isset($Format["ValuePadding"]) ? $Format["ValuePadding"] : 4;
        $OuterBubbleRadius = isset($Format["OuterBubbleRadius"]) ? $Format["OuterBubbleRadius"] : 2;
        $OuterBubbleR = isset($Format["OuterBubbleR"]) ? $Format["OuterBubbleR"] : VOID;
        $OuterBubbleG = isset($Format["OuterBubbleG"]) ? $Format["OuterBubbleG"] : VOID;
        $OuterBubbleB = isset($Format["OuterBubbleB"]) ? $Format["OuterBubbleB"] : VOID;
        $OuterBubbleAlpha = isset($Format["OuterBubbleAlpha"]) ? $Format["OuterBubbleAlpha"] : 100;
        $InnerBubbleR = isset($Format["InnerBubbleR"]) ? $Format["InnerBubbleR"] : 255;
        $InnerBubbleG = isset($Format["InnerBubbleG"]) ? $Format["InnerBubbleG"] : 255;
        $InnerBubbleB = isset($Format["InnerBubbleB"]) ? $Format["InnerBubbleB"] : 255;
        $InnerBubbleAlpha = isset($Format["InnerBubbleAlpha"]) ? $Format["InnerBubbleAlpha"] : 100;
        $DrawBackground = isset($Format["DrawBackground"]) ? $Format["DrawBackground"] : \true;
        $BackgroundR = isset($Format["BackgroundR"]) ? $Format["BackgroundR"] : 255;
        $BackgroundG = isset($Format["BackgroundG"]) ? $Format["BackgroundG"] : 255;
        $BackgroundB = isset($Format["BackgroundB"]) ? $Format["BackgroundB"] : 255;
        $BackgroundAlpha = isset($Format["BackgroundAlpha"]) ? $Format["BackgroundAlpha"] : 50;
        $BackgroundGradient = isset($Format["BackgroundGradient"]) ? $Format["BackgroundGradient"] : null;
        $AxisSteps = isset($Format["AxisSteps"]) ? $Format["AxisSteps"] : 20;
        $SegmentHeight = isset($Format["SegmentHeight"]) ? $Format["SegmentHeight"] : SEGMENT_HEIGHT_AUTO;
        $Segments = isset($Format["Segments"]) ? $Format["Segments"] : 4;
        $WriteLabels = isset($Format["WriteLabels"]) ? $Format["WriteLabels"] : \true;
        $LabelsBackground = isset($Format["LabelsBackground"]) ? $Format["LabelsBackground"] : \true;
        $LabelsBGR = isset($Format["LabelsBGR"]) ? $Format["LabelsBGR"] : 255;
        $LabelsBGG = isset($Format["LabelsBGR"]) ? $Format["LabelsBGG"] : 255;
        $LabelsBGB = isset($Format["LabelsBGR"]) ? $Format["LabelsBGB"] : 255;
        $LabelsBGAlpha = isset($Format["LabelsBGAlpha"]) ? $Format["LabelsBGAlpha"] : 50;
        $LabelPos = isset($Format["LabelPos"]) ? $Format["LabelPos"] : RADAR_LABELS_ROTATED;
        $LabelPadding = isset($Format["LabelPadding"]) ? $Format["LabelPadding"] : 4;
        $DrawPoints = isset($Format["DrawPoints"]) ? $Format["DrawPoints"] : \true;
        $PointRadius = isset($Format["PointRadius"]) ? $Format["PointRadius"] : 4;
        $PointSurrounding = isset($Format["PointRadius"]) ? $Format["PointRadius"] : -30;
        $DrawLines = isset($Format["DrawLines"]) ? $Format["DrawLines"] : \true;
        $LineLoopStart = isset($Format["LineLoopStart"]) ? $Format["LineLoopStart"] : \false;
        $DrawPoly = isset($Format["DrawPoly"]) ? $Format["DrawPoly"] : \false;
        $PolyAlpha = isset($Format["PolyAlpha"]) ? $Format["PolyAlpha"] : null;
        $FontSize = $Object->FontSize;
        $X1 = $Object->GraphAreaX1;
        $Y1 = $Object->GraphAreaY1;
        $X2 = $Object->GraphAreaX2;
        $Y2 = $Object->GraphAreaY2;
        $RecordImageMap = isset($Format["RecordImageMap"]) ? $Format["RecordImageMap"] : \false;
        if ($AxisBoxRounded) {
            $DrawAxisValues = \true;
        }
        /* Cancel default tick length if ticks not enabled */
        if ($DrawTicks == \false) {
            $TicksLength = 0;
        }
        /* Data Processing */
        $Data = $Values->getData();
        $Palette = $Values->getPalette();
        /* Catch the number of required axis */
        $LabelSerie = $Data["Abscissa"];
        if ($LabelSerie) {
            $Points = count($Data["Series"][$LabelSerie]["Data"]);
        } else {
            $Points = 0;
            foreach ($Data["Series"] as $SerieName => $DataArray) {
                if (count($DataArray["Data"]) > $Points) {
                    $Points = count($DataArray["Data"]);
                }
            }
        }
        /* Draw the axis */
        $CenterX = ($X2 - $X1) / 2 + $X1;
        $CenterY = ($Y2 - $Y1) / 2 + $Y1;
        $EdgeHeight = min(($X2 - $X1) / 2, ($Y2 - $Y1) / 2);
        if ($WriteLabels) {
            $EdgeHeight = $EdgeHeight - $FontSize - $LabelPadding - $TicksLength;
        }
        /* Determine the scale if set to automatic */
        if ($SegmentHeight == SEGMENT_HEIGHT_AUTO) {
            if ($FixedMax != VOID) {
                $Max = $FixedMax;
            } else {
                $Max = 0;
                foreach ($Data["Series"] as $SerieName => $DataArray) {
                    if ($SerieName != $LabelSerie) {
                        if (max($DataArray["Data"]) > $Max) {
                            $Max = max($DataArray["Data"]);
                        }
                    }
                }
            }
            $MaxSegments = $EdgeHeight / 20;
            $Scale = $Object->computeScale(0, $Max, $MaxSegments, [1, 2, 5]);
            $Segments = $Scale["Rows"];
            $SegmentHeight = $Scale["RowHeight"];
        }
        /* Background processing */
        if ($DrawBackground) {
            $RestoreShadow = $Object->Shadow;
            $Object->Shadow = \false;
            if ($BackgroundGradient == null) {
                $Color = ["R" => $BackgroundR, "G" => $BackgroundG, "B" => $BackgroundB, "Alpha" => $BackgroundAlpha];
                $Object->drawFilledCircle($CenterX, $CenterY, $EdgeHeight, $Color);
            } else {
                $GradientROffset = ($BackgroundGradient["EndR"] - $BackgroundGradient["StartR"]) / $Segments;
                $GradientGOffset = ($BackgroundGradient["EndG"] - $BackgroundGradient["StartG"]) / $Segments;
                $GradientBOffset = ($BackgroundGradient["EndB"] - $BackgroundGradient["StartB"]) / $Segments;
                $GradientAlphaOffset = ($BackgroundGradient["EndAlpha"] - $BackgroundGradient["StartAlpha"]) / $Segments;
                for ($j = $Segments; $j >= 1; $j--) {
                    $Color = ["R" => $BackgroundGradient["StartR"] + $GradientROffset * $j, "G" => $BackgroundGradient["StartG"] + $GradientGOffset * $j, "B" => $BackgroundGradient["StartB"] + $GradientBOffset * $j, "Alpha" => $BackgroundGradient["StartAlpha"] + $GradientAlphaOffset * $j];
                    $Object->drawFilledCircle($CenterX, $CenterY, $EdgeHeight / $Segments * $j, $Color);
                }
            }
            $Object->Shadow = $RestoreShadow;
        }
        /* Axis to axis lines */
        $Color = ["R" => $AxisR, "G" => $AxisG, "B" => $AxisB, "Alpha" => $AxisAlpha];
        for ($j = 1; $j <= $Segments; $j++) {
            $Radius = $EdgeHeight / $Segments * $j;
            $Object->drawCircle($CenterX, $CenterY, $Radius, $Radius, $Color);
        }
        if ($DrawAxisValues) {
            if ($LabelsBackground) {
                $Options = ["DrawBox" => \true, "Align" => TEXT_ALIGN_MIDDLEMIDDLE, "BoxR" => $LabelsBGR, "BoxG" => $LabelsBGG, "BoxB" => $LabelsBGB, "BoxAlpha" => $LabelsBGAlpha];
            } else {
                $Options = ["Align" => TEXT_ALIGN_MIDDLEMIDDLE];
            }
            if ($AxisBoxRounded) {
                $Options["BoxRounded"] = \true;
            }
            $Options["FontName"] = $AxisFontName;
            $Options["FontSize"] = $AxisFontSize;
            $Angle = 360 / ($Points * 2);
            for ($j = 1; $j <= $Segments; $j++) {
                $EdgeX1 = cos(deg2rad($Angle + $AxisRotation)) * ($EdgeHeight / $Segments) * $j + $CenterX;
                $EdgeY1 = sin(deg2rad($Angle + $AxisRotation)) * ($EdgeHeight / $Segments) * $j + $CenterY;
                $Label = $j * $SegmentHeight;
                $Object->drawText($EdgeX1, $EdgeY1, $Label, $Options);
            }
        }
        /* Axis lines */
        $ID = 0;
        for ($i = 0; $i <= 359; $i = $i + $AxisSteps) {
            $EdgeX = cos(deg2rad($i + $AxisRotation)) * ($EdgeHeight + $TicksLength) + $CenterX;
            $EdgeY = sin(deg2rad($i + $AxisRotation)) * ($EdgeHeight + $TicksLength) + $CenterY;
            $Object->drawLine($CenterX, $CenterY, $EdgeX, $EdgeY, $Color);
            if ($WriteLabels) {
                $LabelX = cos(deg2rad($i + $AxisRotation)) * ($EdgeHeight + $LabelPadding + $TicksLength) + $CenterX;
                $LabelY = sin(deg2rad($i + $AxisRotation)) * ($EdgeHeight + $LabelPadding + $TicksLength) + $CenterY;
                $Label = $i . "°";
                if ($LabelPos == RADAR_LABELS_ROTATED) {
                    $Object->drawText($LabelX, $LabelY, $Label, ["Angle" => 360 - $i, "Align" => TEXT_ALIGN_BOTTOMMIDDLE]);
                } else {
                    if (floor($LabelX) == floor($CenterX) && floor($LabelY) < floor($CenterY)) {
                        $Object->drawText($LabelX, $LabelY, $Label, ["Align" => TEXT_ALIGN_BOTTOMMIDDLE]);
                    }
                    if (floor($LabelX) > floor($CenterX) && floor($LabelY) < floor($CenterY)) {
                        $Object->drawText($LabelX, $LabelY, $Label, ["Align" => TEXT_ALIGN_BOTTOMLEFT]);
                    }
                    if (floor($LabelX) > floor($CenterX) && floor($LabelY) == floor($CenterY)) {
                        $Object->drawText($LabelX, $LabelY, $Label, ["Align" => TEXT_ALIGN_MIDDLELEFT]);
                    }
                    if (floor($LabelX) > floor($CenterX) && floor($LabelY) > floor($CenterY)) {
                        $Object->drawText($LabelX, $LabelY, $Label, ["Align" => TEXT_ALIGN_TOPLEFT]);
                    }
                    if (floor($LabelX) < floor($CenterX) && floor($LabelY) < floor($CenterY)) {
                        $Object->drawText($LabelX, $LabelY, $Label, ["Align" => TEXT_ALIGN_BOTTOMRIGHT]);
                    }
                    if (floor($LabelX) < floor($CenterX) && floor($LabelY) == floor($CenterY)) {
                        $Object->drawText($LabelX, $LabelY, $Label, ["Align" => TEXT_ALIGN_MIDDLERIGHT]);
                    }
                    if (floor($LabelX) < floor($CenterX) && floor($LabelY) > floor($CenterY)) {
                        $Object->drawText($LabelX, $LabelY, $Label, ["Align" => TEXT_ALIGN_TOPRIGHT]);
                    }
                    if (floor($LabelX) == floor($CenterX) && floor($LabelY) > floor($CenterY)) {
                        $Object->drawText($LabelX, $LabelY, $Label, ["Align" => TEXT_ALIGN_TOPMIDDLE]);
                    }
                }
            }
            $ID++;
        }
        /* Compute the plots position */
        $ID = 0;
        $Plot = [];
        foreach ($Data["Series"] as $SerieName => $DataSet) {
            if ($SerieName != $LabelSerie) {
                $Color = ["R" => $Palette[$ID]["R"], "G" => $Palette[$ID]["G"], "B" => $Palette[$ID]["B"], "Alpha" => $Palette[$ID]["Alpha"], "Surrounding" => $PointSurrounding];
                foreach ($DataSet["Data"] as $Key => $Value) {
                    $Angle = $Data["Series"][$LabelSerie]["Data"][$Key];
                    $Length = $EdgeHeight / ($Segments * $SegmentHeight) * $Value;
                    $X = cos(deg2rad($Angle + $AxisRotation)) * $Length + $CenterX;
                    $Y = sin(deg2rad($Angle + $AxisRotation)) * $Length + $CenterY;
                    if ($RecordImageMap) {
                        $this->pChartObject->addToImageMap("CIRCLE", sprintf("%s,%s,%s", floor($X), floor($Y), floor($PointRadius)), $this->pChartObject->toHTMLColor($Palette[$ID]["R"], $Palette[$ID]["G"], $Palette[$ID]["B"]), $DataSet["Description"], $Data["Series"][$LabelSerie]["Data"][$Key] . "&deg = " . $Value);
                    }
                    $Plot[$ID][] = [$X, $Y, $Value];
                }
                $ID++;
            }
        }
        /* Draw all that stuff! */
        foreach ($Plot as $ID => $Points) {
            $Color = ["R" => $Palette[$ID]["R"], "G" => $Palette[$ID]["G"], "B" => $Palette[$ID]["B"], "Alpha" => $Palette[$ID]["Alpha"], "Surrounding" => $PointSurrounding];
            /* Draw the polygons */
            if ($DrawPoly) {
                if ($PolyAlpha != null) {
                    $Color = ["R" => $Palette[$ID]["R"], "G" => $Palette[$ID]["G"], "B" => $Palette[$ID]["B"], "Alpha" => $PolyAlpha, "Surrounding" => $PointSurrounding];
                }
                $PointsArray = [];
                for ($i = 0; $i < count($Points); $i++) {
                    $PointsArray[] = $Points[$i][0];
                    $PointsArray[] = $Points[$i][1];
                }
                $Object->drawPolygon($PointsArray, $Color);
            }
            $Color = ["R" => $Palette[$ID]["R"], "G" => $Palette[$ID]["G"], "B" => $Palette[$ID]["B"], "Alpha" => $Palette[$ID]["Alpha"], "Surrounding" => $PointSurrounding];
            /* Bubble and labels settings */
            $TextSettings = ["Align" => TEXT_ALIGN_MIDDLEMIDDLE, "FontName" => $ValueFontName, "FontSize" => $ValueFontSize, "R" => $Palette[$ID]["R"], "G" => $Palette[$ID]["G"], "B" => $Palette[$ID]["B"]];
            $InnerColor = ["R" => $InnerBubbleR, "G" => $InnerBubbleG, "B" => $InnerBubbleB, "Alpha" => $InnerBubbleAlpha];
            if ($OuterBubbleR != VOID) {
                $OuterColor = ["R" => $OuterBubbleR, "G" => $OuterBubbleG, "B" => $OuterBubbleB, "Alpha" => $OuterBubbleAlpha];
            } else {
                $OuterColor = ["R" => $Palette[$ID]["R"] + 20, "G" => $Palette[$ID]["G"] + 20, "B" => $Palette[$ID]["B"] + 20, "Alpha" => $Palette[$ID]["Alpha"]];
            }
            /* Loop to the starting points if asked */
            if ($LineLoopStart && $DrawLines) {
                $Object->drawLine($Points[count($Points) - 1][0], $Points[count($Points) - 1][1], $Points[0][0], $Points[0][1], $Color);
            }
            /* Draw the lines & points */
            for ($i = 0; $i < count($Points); $i++) {
                if ($DrawLines && $i < count($Points) - 1) {
                    $Object->drawLine($Points[$i][0], $Points[$i][1], $Points[$i + 1][0], $Points[$i + 1][1], $Color);
                }
                if ($DrawPoints) {
                    $Object->drawFilledCircle($Points[$i][0], $Points[$i][1], $PointRadius, $Color);
                }
                if ($WriteValuesInBubble && $WriteValues) {
                    $TxtPos = $this->pChartObject->getTextBox($Points[$i][0], $Points[$i][1], $ValueFontName, $ValueFontSize, 0, $Points[$i][2]);
                    $Radius = floor(($TxtPos[1]["X"] - $TxtPos[0]["X"] + $ValuePadding * 2) / 2);
                    $this->pChartObject->drawFilledCircle($Points[$i][0], $Points[$i][1], $Radius + $OuterBubbleRadius, $OuterColor);
                    $this->pChartObject->drawFilledCircle($Points[$i][0], $Points[$i][1], $Radius, $InnerColor);
                }
                if ($WriteValues) {
                    $this->pChartObject->drawText($Points[$i][0] - 1, $Points[$i][1] - 1, $Points[$i][2], $TextSettings);
                }
            }
        }
    }
}
