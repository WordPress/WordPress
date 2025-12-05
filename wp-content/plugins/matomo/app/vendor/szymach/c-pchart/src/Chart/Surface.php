<?php

namespace CpChart\Chart;

use CpChart\Image;
/**
 *  Surface - class to draw surface charts
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
class Surface
{
    /**
     * @var Image
     */
    public $pChartObject;
    /**
     * @var int
     */
    public $GridSizeX;
    /**
     * @var int
     */
    public $GridSizeY;
    /**
     * @var array
     */
    public $Points = [];
    /**
     * @param Image $pChartObject
     */
    public function __construct(Image $pChartObject)
    {
        $this->pChartObject = $pChartObject;
    }
    /**
     * Define the grid size and initialise the 2D matrix
     * @param int $XSize
     * @param int $YSize
     */
    public function setGrid($XSize = 10, $YSize = 10)
    {
        for ($X = 0; $X <= $XSize; $X++) {
            for ($Y = 0; $Y <= $YSize; $Y++) {
                $this->Points[$X][$Y] = UNKNOWN;
            }
        }
        $this->GridSizeX = $XSize;
        $this->GridSizeY = $YSize;
    }
    /**
     * Add a point on the grid
     * @param int $X
     * @param int $Y
     * @param int|float $Value
     * @param boolean $Force
     * @return null
     */
    public function addPoint($X, $Y, $Value, $Force = \true)
    {
        if ($X < 0 || $X > $this->GridSizeX) {
            return 0;
        }
        if ($Y < 0 || $Y > $this->GridSizeY) {
            return 0;
        }
        if ($this->Points[$X][$Y] == UNKNOWN || $Force) {
            $this->Points[$X][$Y] = $Value;
        } elseif ($this->Points[$X][$Y] == UNKNOWN) {
            $this->Points[$X][$Y] = $Value;
        } else {
            $this->Points[$X][$Y] = ($this->Points[$X][$Y] + $Value) / 2;
        }
    }
    /**
     * Write the X labels
     * @param array $Format
     * @return null|int
     */
    public function writeXLabels(array $Format = [])
    {
        $R = isset($Format["R"]) ? $Format["R"] : $this->pChartObject->FontColorR;
        $G = isset($Format["G"]) ? $Format["G"] : $this->pChartObject->FontColorG;
        $B = isset($Format["B"]) ? $Format["B"] : $this->pChartObject->FontColorB;
        $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : $this->pChartObject->FontColorA;
        $Angle = isset($Format["Angle"]) ? $Format["Angle"] : 0;
        $Padding = isset($Format["Padding"]) ? $Format["Padding"] : 5;
        $Position = isset($Format["Position"]) ? $Format["Position"] : LABEL_POSITION_TOP;
        $Labels = isset($Format["Labels"]) ? $Format["Labels"] : null;
        $CountOffset = isset($Format["CountOffset"]) ? $Format["CountOffset"] : 0;
        if ($Labels != null && !is_array($Labels)) {
            $Label = $Labels;
            $Labels = [$Label];
        }
        $X0 = $this->pChartObject->GraphAreaX1;
        $XSize = ($this->pChartObject->GraphAreaX2 - $this->pChartObject->GraphAreaX1) / ($this->GridSizeX + 1);
        $Settings = ["Angle" => $Angle, "R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha];
        if ($Position == LABEL_POSITION_TOP) {
            $YPos = $this->pChartObject->GraphAreaY1 - $Padding;
            if ($Angle == 0) {
                $Settings["Align"] = TEXT_ALIGN_BOTTOMMIDDLE;
            }
            if ($Angle != 0) {
                $Settings["Align"] = TEXT_ALIGN_MIDDLELEFT;
            }
        } elseif ($Position == LABEL_POSITION_BOTTOM) {
            $YPos = $this->pChartObject->GraphAreaY2 + $Padding;
            if ($Angle == 0) {
                $Settings["Align"] = TEXT_ALIGN_TOPMIDDLE;
            }
            if ($Angle != 0) {
                $Settings["Align"] = TEXT_ALIGN_MIDDLERIGHT;
            }
        } else {
            return -1;
        }
        for ($X = 0; $X <= $this->GridSizeX; $X++) {
            $XPos = floor($X0 + $X * $XSize + $XSize / 2);
            if ($Labels == null || !isset($Labels[$X])) {
                $Value = $X + $CountOffset;
            } else {
                $Value = $Labels[$X];
            }
            $this->pChartObject->drawText($XPos, $YPos, $Value, $Settings);
        }
    }
    /**
     * Write the Y labels
     * @param array $Format
     * @return type
     */
    public function writeYLabels(array $Format = [])
    {
        $R = isset($Format["R"]) ? $Format["R"] : $this->pChartObject->FontColorR;
        $G = isset($Format["G"]) ? $Format["G"] : $this->pChartObject->FontColorG;
        $B = isset($Format["B"]) ? $Format["B"] : $this->pChartObject->FontColorB;
        $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : $this->pChartObject->FontColorA;
        $Angle = isset($Format["Angle"]) ? $Format["Angle"] : 0;
        $Padding = isset($Format["Padding"]) ? $Format["Padding"] : 5;
        $Position = isset($Format["Position"]) ? $Format["Position"] : LABEL_POSITION_LEFT;
        $Labels = isset($Format["Labels"]) ? $Format["Labels"] : null;
        $CountOffset = isset($Format["CountOffset"]) ? $Format["CountOffset"] : 0;
        if ($Labels != null && !is_array($Labels)) {
            $Label = $Labels;
            $Labels = [$Label];
        }
        $Y0 = $this->pChartObject->GraphAreaY1;
        $YSize = ($this->pChartObject->GraphAreaY2 - $this->pChartObject->GraphAreaY1) / ($this->GridSizeY + 1);
        $Settings = ["Angle" => $Angle, "R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha];
        if ($Position == LABEL_POSITION_LEFT) {
            $XPos = $this->pChartObject->GraphAreaX1 - $Padding;
            $Settings["Align"] = TEXT_ALIGN_MIDDLERIGHT;
        } elseif ($Position == LABEL_POSITION_RIGHT) {
            $XPos = $this->pChartObject->GraphAreaX2 + $Padding;
            $Settings["Align"] = TEXT_ALIGN_MIDDLELEFT;
        } else {
            return -1;
        }
        for ($Y = 0; $Y <= $this->GridSizeY; $Y++) {
            $YPos = floor($Y0 + $Y * $YSize + $YSize / 2);
            if ($Labels == null || !isset($Labels[$Y])) {
                $Value = $Y + $CountOffset;
            } else {
                $Value = $Labels[$Y];
            }
            $this->pChartObject->drawText($XPos, $YPos, $Value, $Settings);
        }
    }
    /**
     * Draw the area arround the specified Threshold
     * @param int|float $Threshold
     * @param array $Format
     */
    public function drawContour($Threshold, array $Format = [])
    {
        $R = isset($Format["R"]) ? $Format["R"] : 0;
        $G = isset($Format["G"]) ? $Format["G"] : 0;
        $B = isset($Format["B"]) ? $Format["B"] : 0;
        $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;
        $Ticks = isset($Format["Ticks"]) ? $Format["Ticks"] : 3;
        $Padding = isset($Format["Padding"]) ? $Format["Padding"] : 0;
        $X0 = $this->pChartObject->GraphAreaX1;
        $Y0 = $this->pChartObject->GraphAreaY1;
        $XSize = ($this->pChartObject->GraphAreaX2 - $this->pChartObject->GraphAreaX1) / ($this->GridSizeX + 1);
        $YSize = ($this->pChartObject->GraphAreaY2 - $this->pChartObject->GraphAreaY1) / ($this->GridSizeY + 1);
        $Color = ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "Ticks" => $Ticks];
        for ($X = 0; $X <= $this->GridSizeX; $X++) {
            for ($Y = 0; $Y <= $this->GridSizeY; $Y++) {
                $Value = $this->Points[$X][$Y];
                if ($Value != UNKNOWN && $Value != IGNORED && $Value >= $Threshold) {
                    $X1 = floor($X0 + $X * $XSize) + $Padding;
                    $Y1 = floor($Y0 + $Y * $YSize) + $Padding;
                    $X2 = floor($X0 + $X * $XSize + $XSize);
                    $Y2 = floor($Y0 + $Y * $YSize + $YSize);
                    if ($X > 0 && $this->Points[$X - 1][$Y] != UNKNOWN && $this->Points[$X - 1][$Y] != IGNORED && $this->Points[$X - 1][$Y] < $Threshold) {
                        $this->pChartObject->drawLine($X1, $Y1, $X1, $Y2, $Color);
                    }
                    if ($Y > 0 && $this->Points[$X][$Y - 1] != UNKNOWN && $this->Points[$X][$Y - 1] != IGNORED && $this->Points[$X][$Y - 1] < $Threshold) {
                        $this->pChartObject->drawLine($X1, $Y1, $X2, $Y1, $Color);
                    }
                    if ($X < $this->GridSizeX && $this->Points[$X + 1][$Y] != UNKNOWN && $this->Points[$X + 1][$Y] != IGNORED && $this->Points[$X + 1][$Y] < $Threshold) {
                        $this->pChartObject->drawLine($X2, $Y1, $X2, $Y2, $Color);
                    }
                    if ($Y < $this->GridSizeY && $this->Points[$X][$Y + 1] != UNKNOWN && $this->Points[$X][$Y + 1] != IGNORED && $this->Points[$X][$Y + 1] < $Threshold) {
                        $this->pChartObject->drawLine($X1, $Y2, $X2, $Y2, $Color);
                    }
                }
            }
        }
    }
    /**
     * Draw the surface chart
     * @param array $Format
     */
    public function drawSurface(array $Format = [])
    {
        $Palette = isset($Format["Palette"]) ? $Format["Palette"] : null;
        $ShadeR1 = isset($Format["ShadeR1"]) ? $Format["ShadeR1"] : 77;
        $ShadeG1 = isset($Format["ShadeG1"]) ? $Format["ShadeG1"] : 205;
        $ShadeB1 = isset($Format["ShadeB1"]) ? $Format["ShadeB1"] : 21;
        $ShadeA1 = isset($Format["ShadeA1"]) ? $Format["ShadeA1"] : 40;
        $ShadeR2 = isset($Format["ShadeR2"]) ? $Format["ShadeR2"] : 227;
        $ShadeG2 = isset($Format["ShadeG2"]) ? $Format["ShadeG2"] : 135;
        $ShadeB2 = isset($Format["ShadeB2"]) ? $Format["ShadeB2"] : 61;
        $ShadeA2 = isset($Format["ShadeA2"]) ? $Format["ShadeA2"] : 100;
        $Border = isset($Format["Border"]) ? $Format["Border"] : \false;
        $BorderR = isset($Format["BorderR"]) ? $Format["BorderR"] : 0;
        $BorderG = isset($Format["BorderG"]) ? $Format["BorderG"] : 0;
        $BorderB = isset($Format["BorderB"]) ? $Format["BorderB"] : 0;
        $Surrounding = isset($Format["Surrounding"]) ? $Format["Surrounding"] : -1;
        $Padding = isset($Format["Padding"]) ? $Format["Padding"] : 1;
        $X0 = $this->pChartObject->GraphAreaX1;
        $Y0 = $this->pChartObject->GraphAreaY1;
        $XSize = ($this->pChartObject->GraphAreaX2 - $this->pChartObject->GraphAreaX1) / ($this->GridSizeX + 1);
        $YSize = ($this->pChartObject->GraphAreaY2 - $this->pChartObject->GraphAreaY1) / ($this->GridSizeY + 1);
        for ($X = 0; $X <= $this->GridSizeX; $X++) {
            for ($Y = 0; $Y <= $this->GridSizeY; $Y++) {
                $Value = $this->Points[$X][$Y];
                if ($Value != UNKNOWN && $Value != IGNORED) {
                    $X1 = floor($X0 + $X * $XSize) + $Padding;
                    $Y1 = floor($Y0 + $Y * $YSize) + $Padding;
                    $X2 = floor($X0 + $X * $XSize + $XSize);
                    $Y2 = floor($Y0 + $Y * $YSize + $YSize);
                    if ($Palette != null) {
                        if (isset($Palette[$Value]) && isset($Palette[$Value]["R"])) {
                            $R = $Palette[$Value]["R"];
                        } else {
                            $R = 0;
                        }
                        if (isset($Palette[$Value]) && isset($Palette[$Value]["G"])) {
                            $G = $Palette[$Value]["G"];
                        } else {
                            $G = 0;
                        }
                        if (isset($Palette[$Value]) && isset($Palette[$Value]["B"])) {
                            $B = $Palette[$Value]["B"];
                        } else {
                            $B = 0;
                        }
                        if (isset($Palette[$Value]) && isset($Palette[$Value]["Alpha"])) {
                            $Alpha = $Palette[$Value]["Alpha"];
                        } else {
                            $Alpha = 1000;
                        }
                    } else {
                        $R = ($ShadeR2 - $ShadeR1) / 100 * $Value + $ShadeR1;
                        $G = ($ShadeG2 - $ShadeG1) / 100 * $Value + $ShadeG1;
                        $B = ($ShadeB2 - $ShadeB1) / 100 * $Value + $ShadeB1;
                        $Alpha = ($ShadeA2 - $ShadeA1) / 100 * $Value + $ShadeA1;
                    }
                    $Settings = ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha];
                    if ($Border) {
                        $Settings["BorderR"] = $BorderR;
                        $Settings["BorderG"] = $BorderG;
                        $Settings["BorderB"] = $BorderB;
                    }
                    if ($Surrounding != -1) {
                        $Settings["BorderR"] = $R + $Surrounding;
                        $Settings["BorderG"] = $G + $Surrounding;
                        $Settings["BorderB"] = $B + $Surrounding;
                    }
                    $this->pChartObject->drawFilledRectangle($X1, $Y1, $X2 - 1, $Y2 - 1, $Settings);
                }
            }
        }
    }
    /**
     * Compute the missing points
     */
    public function computeMissing()
    {
        $Missing = [];
        for ($X = 0; $X <= $this->GridSizeX; $X++) {
            for ($Y = 0; $Y <= $this->GridSizeY; $Y++) {
                if ($this->Points[$X][$Y] == UNKNOWN) {
                    $Missing[] = $X . "," . $Y;
                }
            }
        }
        shuffle($Missing);
        foreach ($Missing as $Pos) {
            $Pos = preg_split("/,/", $Pos);
            $X = $Pos[0];
            $Y = $Pos[1];
            if ($this->Points[$X][$Y] == UNKNOWN) {
                $NearestNeighbor = $this->getNearestNeighbor($X, $Y);
                $Value = 0;
                $Points = 0;
                for ($Xi = $X - $NearestNeighbor; $Xi <= $X + $NearestNeighbor; $Xi++) {
                    for ($Yi = $Y - $NearestNeighbor; $Yi <= $Y + $NearestNeighbor; $Yi++) {
                        if ($Xi >= 0 && $Yi >= 0 && $Xi <= $this->GridSizeX && $Yi <= $this->GridSizeY && $this->Points[$Xi][$Yi] != UNKNOWN && $this->Points[$Xi][$Yi] != IGNORED) {
                            $Value = $Value + $this->Points[$Xi][$Yi];
                            $Points++;
                        }
                    }
                }
                if ($Points != 0) {
                    $this->Points[$X][$Y] = $Value / $Points;
                }
            }
        }
    }
    /**
     * Return the nearest Neighbor distance of a point
     * @param int $Xp
     * @param int $Yp
     * @return int
     */
    public function getNearestNeighbor($Xp, $Yp)
    {
        $Nearest = UNKNOWN;
        for ($X = 0; $X <= $this->GridSizeX; $X++) {
            for ($Y = 0; $Y <= $this->GridSizeY; $Y++) {
                if ($this->Points[$X][$Y] != UNKNOWN && $this->Points[$X][$Y] != IGNORED) {
                    $DistanceX = max($Xp, $X) - min($Xp, $X);
                    $DistanceY = max($Yp, $Y) - min($Yp, $Y);
                    $Distance = max($DistanceX, $DistanceY);
                    if ($Distance < $Nearest || $Nearest == UNKNOWN) {
                        $Nearest = $Distance;
                    }
                }
            }
        }
        return $Nearest;
    }
}
