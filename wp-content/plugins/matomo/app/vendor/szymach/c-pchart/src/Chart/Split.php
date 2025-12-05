<?php

namespace CpChart\Chart;

use CpChart\Data;
use CpChart\Image;
/**
 *    Split - class to draw spline splitted charts
 *
 *    Version     : 2.1.4
 *    Made by     : Jean-Damien POGOLOTTI
 *    Last Update : 19/01/2014
 *
 *    This file can be distributed under the license you can find at :
 *
 *                      http://www.pchart.net/license
 *
 *    You can find the whole class documentation on the pChart web site.
 */
class Split
{
    /**
     * @var Image
     */
    public $pChartObject;
    /**
     * Create the encoded string
     * @param Image $Object
     * @param Data $Values
     * @param array $Format
     */
    public function drawSplitPath(Image $Object, Data $Values, array $Format = [])
    {
        $this->pChartObject = $Object;
        $Spacing = isset($Format["Spacing"]) ? $Format["Spacing"] : 20;
        $TextPadding = isset($Format["TextPadding"]) ? $Format["TextPadding"] : 2;
        $TextPos = isset($Format["TextPos"]) ? $Format["TextPos"] : TEXT_POS_TOP;
        $Surrounding = isset($Format["Surrounding"]) ? $Format["Surrounding"] : null;
        $Force = isset($Format["Force"]) ? $Format["Force"] : 70;
        $Segments = isset($Format["Segments"]) ? $Format["Segments"] : 15;
        $X1 = $Object->GraphAreaX1;
        $Y1 = $Object->GraphAreaY1;
        $X2 = $Object->GraphAreaX2;
        $Y2 = $Object->GraphAreaY2;
        /* Data Processing */
        $Data = $Values->getData();
        $Palette = $Values->getPalette();
        $LabelSerie = $Data["Abscissa"];
        $DataSerie = [];
        foreach ($Data["Series"] as $SerieName => $Value) {
            if ($SerieName != $LabelSerie && empty($DataSerie)) {
                $DataSerie = $SerieName;
            }
        }
        $DataSerieSum = array_sum($Data["Series"][$DataSerie]["Data"]);
        $DataSerieCount = count($Data["Series"][$DataSerie]["Data"]);
        /* Scale Processing */
        if ($TextPos == TEXT_POS_RIGHT) {
            $YScale = ($Y2 - $Y1 - ($DataSerieCount + 1) * $Spacing) / $DataSerieSum;
        } else {
            $YScale = ($Y2 - $Y1 - $DataSerieCount * $Spacing) / $DataSerieSum;
        }
        $LeftHeight = $DataSerieSum * $YScale;
        /* Re-compute graph width depending of the text mode choosen */
        if ($TextPos == TEXT_POS_RIGHT) {
            $MaxWidth = 0;
            foreach ($Data["Series"][$LabelSerie]["Data"] as $Key => $Label) {
                $Boundardies = $Object->getTextBox(0, 0, $Object->FontName, $Object->FontSize, 0, $Label);
                if ($Boundardies[1]["X"] > $MaxWidth) {
                    $MaxWidth = $Boundardies[1]["X"] + $TextPadding * 2;
                }
            }
            $X2 = $X2 - $MaxWidth;
        }
        /* Drawing */
        $LeftY = ($Y2 - $Y1) / 2 + $Y1 - $LeftHeight / 2;
        $RightY = $Y1;
        foreach ($Data["Series"][$DataSerie]["Data"] as $Key => $Value) {
            if (isset($Data["Series"][$LabelSerie]["Data"][$Key])) {
                $Label = $Data["Series"][$LabelSerie]["Data"][$Key];
            } else {
                $Label = "-";
            }
            $LeftY1 = $LeftY;
            $LeftY2 = $LeftY + $Value * $YScale;
            $RightY1 = $RightY + $Spacing;
            $RightY2 = $RightY + $Spacing + $Value * $YScale;
            $Settings = ["R" => $Palette[$Key]["R"], "G" => $Palette[$Key]["G"], "B" => $Palette[$Key]["B"], "Alpha" => $Palette[$Key]["Alpha"], "NoDraw" => \true, "Segments" => $Segments, "Surrounding" => $Surrounding];
            $Angle = $Object->getAngle($X2, $RightY1, $X1, $LeftY1);
            $VectorX1 = cos(deg2rad($Angle + 90)) * $Force + ($X2 - $X1) / 2 + $X1;
            $VectorY1 = sin(deg2rad($Angle + 90)) * $Force + ($RightY1 - $LeftY1) / 2 + $LeftY1;
            $VectorX2 = cos(deg2rad($Angle - 90)) * $Force + ($X2 - $X1) / 2 + $X1;
            $VectorY2 = sin(deg2rad($Angle - 90)) * $Force + ($RightY1 - $LeftY1) / 2 + $LeftY1;
            $Points = $Object->drawBezier($X1, $LeftY1, $X2, $RightY1, $VectorX1, $VectorY1, $VectorX2, $VectorY2, $Settings);
            $PolyGon = [];
            foreach ($Points as $Key => $Pos) {
                $PolyGon[] = $Pos["X"];
                $PolyGon[] = $Pos["Y"];
            }
            $Angle = $Object->getAngle($X2, $RightY2, $X1, $LeftY2);
            $VectorX1 = cos(deg2rad($Angle + 90)) * $Force + ($X2 - $X1) / 2 + $X1;
            $VectorY1 = sin(deg2rad($Angle + 90)) * $Force + ($RightY2 - $LeftY2) / 2 + $LeftY2;
            $VectorX2 = cos(deg2rad($Angle - 90)) * $Force + ($X2 - $X1) / 2 + $X1;
            $VectorY2 = sin(deg2rad($Angle - 90)) * $Force + ($RightY2 - $LeftY2) / 2 + $LeftY2;
            $Points = $Object->drawBezier($X1, $LeftY2, $X2, $RightY2, $VectorX1, $VectorY1, $VectorX2, $VectorY2, $Settings);
            $Points = array_reverse($Points);
            foreach ($Points as $Key => $Pos) {
                $PolyGon[] = $Pos["X"];
                $PolyGon[] = $Pos["Y"];
            }
            $Object->drawPolygon($PolyGon, $Settings);
            if ($TextPos == TEXT_POS_RIGHT) {
                $Object->drawText($X2 + $TextPadding, ($RightY2 - $RightY1) / 2 + $RightY1, $Label, ["Align" => TEXT_ALIGN_MIDDLELEFT]);
            } else {
                $Object->drawText($X2, $RightY1 - $TextPadding, $Label, ["Align" => TEXT_ALIGN_BOTTOMRIGHT]);
            }
            $LeftY = $LeftY2;
            $RightY = $RightY2;
        }
    }
}
