<?php

namespace CpChart;

use Exception;
/**
 *  Draw - class extension with drawing methods
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
abstract class Draw extends \CpChart\BaseDraw
{
    /**
     * Draw a polygon
     * @param array $Points
     * @param array $Format
     */
    public function drawPolygon(array $Points, array $Format = [])
    {
        $R = isset($Format["R"]) ? $Format["R"] : 0;
        $G = isset($Format["G"]) ? $Format["G"] : 0;
        $B = isset($Format["B"]) ? $Format["B"] : 0;
        $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;
        $NoFill = isset($Format["NoFill"]) ? $Format["NoFill"] : \false;
        $NoBorder = isset($Format["NoBorder"]) ? $Format["NoBorder"] : \false;
        $BorderR = isset($Format["BorderR"]) ? $Format["BorderR"] : $R;
        $BorderG = isset($Format["BorderG"]) ? $Format["BorderG"] : $G;
        $BorderB = isset($Format["BorderB"]) ? $Format["BorderB"] : $B;
        $BorderAlpha = isset($Format["Alpha"]) ? $Format["Alpha"] : $Alpha / 2;
        $Surrounding = isset($Format["Surrounding"]) ? $Format["Surrounding"] : null;
        $SkipX = isset($Format["SkipX"]) ? $Format["SkipX"] : OUT_OF_SIGHT;
        $SkipY = isset($Format["SkipY"]) ? $Format["SkipY"] : OUT_OF_SIGHT;
        /* Calling the ImageFilledPolygon() public function over the $Points array will round it */
        $Backup = $Points;
        if ($Surrounding != null) {
            $BorderR = $R + $Surrounding;
            $BorderG = $G + $Surrounding;
            $BorderB = $B + $Surrounding;
        }
        if ($SkipX != OUT_OF_SIGHT) {
            $SkipX = floor($SkipX);
        }
        if ($SkipY != OUT_OF_SIGHT) {
            $SkipY = floor($SkipY);
        }
        $RestoreShadow = $this->Shadow;
        if (!$NoFill) {
            if ($this->Shadow && $this->ShadowX != 0 && $this->ShadowY != 0) {
                $this->Shadow = \false;
                for ($i = 0; $i <= count($Points) - 1; $i = $i + 2) {
                    $Shadow[] = $Points[$i] + $this->ShadowX;
                    $Shadow[] = $Points[$i + 1] + $this->ShadowY;
                }
                $this->drawPolygon($Shadow, ["R" => $this->ShadowR, "G" => $this->ShadowG, "B" => $this->ShadowB, "Alpha" => $this->Shadowa, "NoBorder" => \true]);
            }
            $FillColor = $this->allocateColor($this->Picture, $R, $G, $B, $Alpha);
            if (count($Points) >= 6) {
                $this->imageFilledPolygonWrapper($this->Picture, $Points, count($Points) / 2, $FillColor);
            }
        }
        if (!$NoBorder) {
            $Points = $Backup;
            if ($NoFill) {
                $BorderSettings = ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha];
            } else {
                $BorderSettings = ["R" => $BorderR, "G" => $BorderG, "B" => $BorderB, "Alpha" => $BorderAlpha];
            }
            for ($i = 0; $i <= count($Points) - 1; $i = $i + 2) {
                if (isset($Points[$i + 2]) && !($Points[$i] == $Points[$i + 2] && $Points[$i] == $SkipX) && !($Points[$i + 1] == $Points[$i + 3] && $Points[$i + 1] == $SkipY)) {
                    $this->drawLine($Points[$i], $Points[$i + 1], $Points[$i + 2], $Points[$i + 3], $BorderSettings);
                } elseif (!($Points[$i] == $Points[0] && $Points[$i] == $SkipX) && !($Points[$i + 1] == $Points[1] && $Points[$i + 1] == $SkipY)) {
                    $this->drawLine($Points[$i], $Points[$i + 1], $Points[0], $Points[1], $BorderSettings);
                }
            }
        }
        $this->Shadow = $RestoreShadow;
    }
    /**
     * Draw a rectangle with rounded corners
     * @param int $X1
     * @param int $Y1
     * @param int $X2
     * @param int $Y2
     * @param int|float $Radius
     * @param array $Format
     * @return null|integer
     */
    public function drawRoundedRectangle($X1, $Y1, $X2, $Y2, $Radius, array $Format = [])
    {
        $R = isset($Format["R"]) ? $Format["R"] : 0;
        $G = isset($Format["G"]) ? $Format["G"] : 0;
        $B = isset($Format["B"]) ? $Format["B"] : 0;
        $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;
        list($X1, $Y1, $X2, $Y2) = $this->fixBoxCoordinates($X1, $Y1, $X2, $Y2);
        if ($X2 - $X1 < $Radius) {
            $Radius = floor(($X2 - $X1) / 2);
        }
        if ($Y2 - $Y1 < $Radius) {
            $Radius = floor(($Y2 - $Y1) / 2);
        }
        $Color = ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "NoBorder" => \true];
        if ($Radius <= 0) {
            $this->drawRectangle($X1, $Y1, $X2, $Y2, $Color);
            return 0;
        }
        if ($this->Antialias) {
            $this->drawLine($X1 + $Radius, $Y1, $X2 - $Radius, $Y1, $Color);
            $this->drawLine($X2, $Y1 + $Radius, $X2, $Y2 - $Radius, $Color);
            $this->drawLine($X2 - $Radius, $Y2, $X1 + $Radius, $Y2, $Color);
            $this->drawLine($X1, $Y1 + $Radius, $X1, $Y2 - $Radius, $Color);
        } else {
            $Color = $this->allocateColor($this->Picture, $R, $G, $B, $Alpha);
            imageline($this->Picture, $X1 + $Radius, $Y1, $X2 - $Radius, $Y1, $Color);
            imageline($this->Picture, $X2, $Y1 + $Radius, $X2, $Y2 - $Radius, $Color);
            imageline($this->Picture, $X2 - $Radius, $Y2, $X1 + $Radius, $Y2, $Color);
            imageline($this->Picture, $X1, $Y1 + $Radius, $X1, $Y2 - $Radius, $Color);
        }
        $Step = 360 / (2 * PI * $Radius);
        for ($i = 0; $i <= 90; $i = $i + $Step) {
            $X = cos(($i + 180) * PI / 180) * $Radius + $X1 + $Radius;
            $Y = sin(($i + 180) * PI / 180) * $Radius + $Y1 + $Radius;
            $this->drawAntialiasPixel($X, $Y, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha]);
            $X = cos(($i + 90) * PI / 180) * $Radius + $X1 + $Radius;
            $Y = sin(($i + 90) * PI / 180) * $Radius + $Y2 - $Radius;
            $this->drawAntialiasPixel($X, $Y, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha]);
            $X = cos($i * PI / 180) * $Radius + $X2 - $Radius;
            $Y = sin($i * PI / 180) * $Radius + $Y2 - $Radius;
            $this->drawAntialiasPixel($X, $Y, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha]);
            $X = cos(($i + 270) * PI / 180) * $Radius + $X2 - $Radius;
            $Y = sin(($i + 270) * PI / 180) * $Radius + $Y1 + $Radius;
            $this->drawAntialiasPixel($X, $Y, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha]);
        }
    }
    /**
     * Draw a rectangle with rounded corners
     * @param int $X1
     * @param int $Y1
     * @param int $X2
     * @param int $Y2
     * @param int|float $Radius
     * @param array $Format
     * @return null|integer
     */
    public function drawRoundedFilledRectangle($X1, $Y1, $X2, $Y2, $Radius, array $Format = [])
    {
        $R = isset($Format["R"]) ? $Format["R"] : 0;
        $G = isset($Format["G"]) ? $Format["G"] : 0;
        $B = isset($Format["B"]) ? $Format["B"] : 0;
        $BorderR = isset($Format["BorderR"]) ? $Format["BorderR"] : -1;
        $BorderG = isset($Format["BorderG"]) ? $Format["BorderG"] : -1;
        $BorderB = isset($Format["BorderB"]) ? $Format["BorderB"] : -1;
        $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;
        $Surrounding = isset($Format["Surrounding"]) ? $Format["Surrounding"] : null;
        /* Temporary fix for AA issue */
        $Y1 = (int) floor($Y1);
        $Y2 = (int) floor($Y2);
        $X1 = (int) floor($X1);
        $X2 = (int) floor($X2);
        if ($Surrounding != null) {
            $BorderR = $R + $Surrounding;
            $BorderG = $G + $Surrounding;
            $BorderB = $B + $Surrounding;
        }
        if ($BorderR == -1) {
            $BorderR = $R;
            $BorderG = $G;
            $BorderB = $B;
        }
        list($X1, $Y1, $X2, $Y2) = $this->fixBoxCoordinates($X1, $Y1, $X2, $Y2);
        if ($X2 - $X1 < $Radius * 2) {
            $Radius = (int) floor(($X2 - $X1) / 4);
        }
        if ($Y2 - $Y1 < $Radius * 2) {
            $Radius = (int) floor(($Y2 - $Y1) / 4);
        }
        $RestoreShadow = $this->Shadow;
        if ($this->Shadow && $this->ShadowX != 0 && $this->ShadowY != 0) {
            $this->Shadow = \false;
            $this->drawRoundedFilledRectangle($X1 + $this->ShadowX, $Y1 + $this->ShadowY, $X2 + $this->ShadowX, $Y2 + $this->ShadowY, $Radius, ["R" => $this->ShadowR, "G" => $this->ShadowG, "B" => $this->ShadowB, "Alpha" => $this->Shadowa]);
        }
        $Color = ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "NoBorder" => \true];
        if ($Radius <= 0) {
            $this->drawFilledRectangle($X1, $Y1, $X2, $Y2, $Color);
            return 0;
        }
        $YTop = $Y1 + $Radius;
        $YBottom = $Y2 - $Radius;
        $Step = 360 / (2 * PI * $Radius);
        $Positions = [];
        $Radius--;
        $MinY = null;
        $MaxY = null;
        for ($i = 0; $i <= 90; $i = $i + $Step) {
            $Xp1 = cos(($i + 180) * PI / 180) * $Radius + $X1 + $Radius;
            $Xp2 = cos((90 - $i + 270) * PI / 180) * $Radius + $X2 - $Radius;
            $Yp = (int) floor(sin(($i + 180) * PI / 180) * $Radius + $YTop);
            if (null === $MinY || $Yp > $MinY) {
                $MinY = $Yp;
            }
            if ($Xp1 <= floor($X1)) {
                $Xp1++;
            }
            if ($Xp2 >= floor($X2)) {
                $Xp2--;
            }
            $Xp1++;
            if (!isset($Positions[$Yp])) {
                $Positions[$Yp]["X1"] = $Xp1;
                $Positions[$Yp]["X2"] = $Xp2;
            } else {
                $Positions[$Yp]["X1"] = ($Positions[$Yp]["X1"] + $Xp1) / 2;
                $Positions[$Yp]["X2"] = ($Positions[$Yp]["X2"] + $Xp2) / 2;
            }
            $Xp1 = cos(($i + 90) * PI / 180) * $Radius + $X1 + $Radius;
            $Xp2 = cos((90 - $i) * PI / 180) * $Radius + $X2 - $Radius;
            $Yp = (int) floor(sin(($i + 90) * PI / 180) * $Radius + $YBottom);
            if (null === $MaxY || $Yp < $MaxY) {
                $MaxY = $Yp;
            }
            if ($Xp1 <= floor($X1)) {
                $Xp1++;
            }
            if ($Xp2 >= floor($X2)) {
                $Xp2--;
            }
            $Xp1++;
            if (!isset($Positions[$Yp])) {
                $Positions[$Yp]["X1"] = $Xp1;
                $Positions[$Yp]["X2"] = $Xp2;
            } else {
                $Positions[$Yp]["X1"] = ($Positions[$Yp]["X1"] + $Xp1) / 2;
                $Positions[$Yp]["X2"] = ($Positions[$Yp]["X2"] + $Xp2) / 2;
            }
        }
        $ManualColor = $this->allocateColor($this->Picture, $R, $G, $B, $Alpha);
        foreach ($Positions as $Yp => $Bounds) {
            $X1 = (int) $Bounds["X1"];
            $X1Dec = $this->getFirstDecimal($X1);
            if ($X1Dec != 0) {
                $X1 = (int) floor($X1) + 1;
            }
            $X2 = (int) $Bounds["X2"];
            $X2Dec = $this->getFirstDecimal($X2);
            if ($X2Dec != 0) {
                $X2 = (int) floor($X2) - 1;
            }
            imageline($this->Picture, $X1, $Yp, $X2, $Yp, $ManualColor);
        }
        $this->drawFilledRectangle($X1, $MinY + 1, (int) floor($X2), $MaxY - 1, $Color);
        $Radius++;
        $this->drawRoundedRectangle($X1, $Y1, $X2 + 1, $Y2 - 1, $Radius, ["R" => $BorderR, "G" => $BorderG, "B" => $BorderB, "Alpha" => $Alpha]);
        $this->Shadow = $RestoreShadow;
    }
    /**
     * Draw a rectangle
     * @param int $X1
     * @param int $Y1
     * @param int $X2
     * @param int $Y2
     * @param array $Format
     */
    public function drawRectangle($X1, $Y1, $X2, $Y2, array $Format = [])
    {
        $R = isset($Format["R"]) ? $Format["R"] : 0;
        $G = isset($Format["G"]) ? $Format["G"] : 0;
        $B = isset($Format["B"]) ? $Format["B"] : 0;
        $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;
        $Ticks = isset($Format["Ticks"]) ? $Format["Ticks"] : null;
        $NoAngle = isset($Format["NoAngle"]) ? $Format["NoAngle"] : \false;
        if ($X1 > $X2) {
            list($X1, $X2) = [$X2, $X1];
        }
        if ($Y1 > $Y2) {
            list($Y1, $Y2) = [$Y2, $Y1];
        }
        if ($this->Antialias) {
            if ($NoAngle) {
                $this->drawLine($X1 + 1, $Y1, $X2 - 1, $Y1, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "Ticks" => $Ticks]);
                $this->drawLine($X2, $Y1 + 1, $X2, $Y2 - 1, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "Ticks" => $Ticks]);
                $this->drawLine($X2 - 1, $Y2, $X1 + 1, $Y2, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "Ticks" => $Ticks]);
                $this->drawLine($X1, $Y1 + 1, $X1, $Y2 - 1, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "Ticks" => $Ticks]);
            } else {
                $this->drawLine($X1 + 1, $Y1, $X2 - 1, $Y1, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "Ticks" => $Ticks]);
                $this->drawLine($X2, $Y1, $X2, $Y2, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "Ticks" => $Ticks]);
                $this->drawLine($X2 - 1, $Y2, $X1 + 1, $Y2, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "Ticks" => $Ticks]);
                $this->drawLine($X1, $Y1, $X1, $Y2, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "Ticks" => $Ticks]);
            }
        } else {
            $Color = $this->allocateColor($this->Picture, $R, $G, $B, $Alpha);
            imagerectangle($this->Picture, $X1, $Y1, $X2, $Y2, $Color);
        }
    }
    /**
     * Draw a filled rectangle
     * @param int $X1
     * @param int $Y1
     * @param int $X2
     * @param int $Y2
     * @param array $Format
     */
    public function drawFilledRectangle($X1, $Y1, $X2, $Y2, array $Format = [])
    {
        $R = isset($Format["R"]) ? $Format["R"] : 0;
        $G = isset($Format["G"]) ? $Format["G"] : 0;
        $B = isset($Format["B"]) ? $Format["B"] : 0;
        $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;
        $BorderR = isset($Format["BorderR"]) ? $Format["BorderR"] : -1;
        $BorderG = isset($Format["BorderG"]) ? $Format["BorderG"] : -1;
        $BorderB = isset($Format["BorderB"]) ? $Format["BorderB"] : -1;
        $BorderAlpha = isset($Format["BorderAlpha"]) ? $Format["BorderAlpha"] : $Alpha;
        $Surrounding = isset($Format["Surrounding"]) ? $Format["Surrounding"] : null;
        $Ticks = isset($Format["Ticks"]) ? $Format["Ticks"] : null;
        $NoAngle = isset($Format["NoAngle"]) ? $Format["NoAngle"] : null;
        $Dash = isset($Format["Dash"]) ? $Format["Dash"] : \false;
        $DashStep = isset($Format["DashStep"]) ? $Format["DashStep"] : 4;
        $DashR = isset($Format["DashR"]) ? $Format["DashR"] : 0;
        $DashG = isset($Format["DashG"]) ? $Format["DashG"] : 0;
        $DashB = isset($Format["DashB"]) ? $Format["DashB"] : 0;
        $NoBorder = isset($Format["NoBorder"]) ? $Format["NoBorder"] : \false;
        if ($Surrounding != null) {
            $BorderR = $R + $Surrounding;
            $BorderG = $G + $Surrounding;
            $BorderB = $B + $Surrounding;
        }
        if ($X1 > $X2) {
            list($X1, $X2) = [$X2, $X1];
        }
        if ($Y1 > $Y2) {
            list($Y1, $Y2) = [$Y2, $Y1];
        }
        $RestoreShadow = $this->Shadow;
        if ($this->Shadow && $this->ShadowX != 0 && $this->ShadowY != 0) {
            $this->Shadow = \false;
            $this->drawFilledRectangle($X1 + $this->ShadowX, $Y1 + $this->ShadowY, $X2 + $this->ShadowX, $Y2 + $this->ShadowY, ["R" => $this->ShadowR, "G" => $this->ShadowG, "B" => $this->ShadowB, "Alpha" => $this->Shadowa, "Ticks" => $Ticks, "NoAngle" => $NoAngle]);
        }
        $Color = $this->allocateColor($this->Picture, $R, $G, $B, $Alpha);
        if ($NoAngle) {
            imagefilledrectangle($this->Picture, ceil($X1) + 1, ceil($Y1), floor($X2) - 1, floor($Y2), $Color);
            imageline($this->Picture, ceil($X1), ceil($Y1) + 1, ceil($X1), floor($Y2) - 1, $Color);
            imageline($this->Picture, floor($X2), ceil($Y1) + 1, floor($X2), floor($Y2) - 1, $Color);
        } else {
            imagefilledrectangle($this->Picture, ceil($X1), ceil($Y1), floor($X2), floor($Y2), $Color);
        }
        if ($Dash) {
            if ($BorderR != -1) {
                $iX1 = $X1 + 1;
                $iY1 = $Y1 + 1;
                $iX2 = $X2 - 1;
                $iY2 = $Y2 - 1;
            } else {
                $iX1 = $X1;
                $iY1 = $Y1;
                $iX2 = $X2;
                $iY2 = $Y2;
            }
            $Color = $this->allocateColor($this->Picture, $DashR, $DashG, $DashB, $Alpha);
            $Y = $iY1 - $DashStep;
            for ($X = $iX1; $X <= $iX2 + ($iY2 - $iY1); $X = $X + $DashStep) {
                $Y = $Y + $DashStep;
                if ($X > $iX2) {
                    $Xa = $X - ($X - $iX2);
                    $Ya = $iY1 + ($X - $iX2);
                } else {
                    $Xa = $X;
                    $Ya = $iY1;
                }
                if ($Y > $iY2) {
                    $Xb = $iX1 + ($Y - $iY2);
                    $Yb = $Y - ($Y - $iY2);
                } else {
                    $Xb = $iX1;
                    $Yb = $Y;
                }
                imageline($this->Picture, $Xa, $Ya, $Xb, $Yb, $Color);
            }
        }
        if ($this->Antialias && !$NoBorder) {
            if ($X1 < ceil($X1)) {
                $AlphaA = $Alpha * (ceil($X1) - $X1);
                $Color = $this->allocateColor($this->Picture, $R, $G, $B, $AlphaA);
                imageline($this->Picture, ceil($X1) - 1, ceil($Y1), ceil($X1) - 1, floor($Y2), $Color);
            }
            if ($Y1 < ceil($Y1)) {
                $AlphaA = $Alpha * (ceil($Y1) - $Y1);
                $Color = $this->allocateColor($this->Picture, $R, $G, $B, $AlphaA);
                imageline($this->Picture, ceil($X1), ceil($Y1) - 1, floor($X2), ceil($Y1) - 1, $Color);
            }
            if ($X2 > floor($X2)) {
                $AlphaA = $Alpha * (0.5 - ($X2 - floor($X2)));
                $Color = $this->allocateColor($this->Picture, $R, $G, $B, $AlphaA);
                imageline($this->Picture, floor($X2) + 1, ceil($Y1), floor($X2) + 1, floor($Y2), $Color);
            }
            if ($Y2 > floor($Y2)) {
                $AlphaA = $Alpha * (0.5 - ($Y2 - floor($Y2)));
                $Color = $this->allocateColor($this->Picture, $R, $G, $B, $AlphaA);
                imageline($this->Picture, ceil($X1), floor($Y2) + 1, floor($X2), floor($Y2) + 1, $Color);
            }
        }
        if ($BorderR != -1) {
            $this->drawRectangle($X1, $Y1, $X2, $Y2, ["R" => $BorderR, "G" => $BorderG, "B" => $BorderB, "Alpha" => $BorderAlpha, "Ticks" => $Ticks, "NoAngle" => $NoAngle]);
        }
        $this->Shadow = $RestoreShadow;
    }
    /**
     * Draw a rectangular marker of the specified size
     * @param int $X
     * @param int $Y
     * @param array $Format
     */
    public function drawRectangleMarker($X, $Y, array $Format = [])
    {
        $Size = isset($Format["Size"]) ? $Format["Size"] : 4;
        $HalfSize = floor($Size / 2);
        $this->drawFilledRectangle($X - $HalfSize, $Y - $HalfSize, $X + $HalfSize, $Y + $HalfSize, $Format);
    }
    /**
     * Drawn a spline based on the bezier public function
     * @param array $Coordinates
     * @param array $Format
     * @return array
     */
    public function drawSpline(array $Coordinates, array $Format = [])
    {
        $R = isset($Format["R"]) ? $Format["R"] : 0;
        $G = isset($Format["G"]) ? $Format["G"] : 0;
        $B = isset($Format["B"]) ? $Format["B"] : 0;
        $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;
        $Force = isset($Format["Force"]) ? $Format["Force"] : 30;
        $Forces = isset($Format["Forces"]) ? $Format["Forces"] : null;
        $ShowC = isset($Format["ShowControl"]) ? $Format["ShowControl"] : \false;
        $Ticks = isset($Format["Ticks"]) ? $Format["Ticks"] : null;
        $PathOnly = isset($Format["PathOnly"]) ? $Format["PathOnly"] : \false;
        $Weight = isset($Format["Weight"]) ? $Format["Weight"] : null;
        $Cpt = null;
        $Mode = null;
        $Result = [];
        for ($i = 1; $i <= count($Coordinates) - 1; $i++) {
            $X1 = $Coordinates[$i - 1][0];
            $Y1 = $Coordinates[$i - 1][1];
            $X2 = $Coordinates[$i][0];
            $Y2 = $Coordinates[$i][1];
            if ($Forces != null) {
                $Force = $Forces[$i];
            }
            /* First segment */
            if ($i == 1) {
                $Xv1 = $X1;
                $Yv1 = $Y1;
            } else {
                $Angle1 = $this->getAngle($XLast, $YLast, $X1, $Y1);
                $Angle2 = $this->getAngle($X1, $Y1, $X2, $Y2);
                $XOff = cos($Angle2 * PI / 180) * $Force + $X1;
                $YOff = sin($Angle2 * PI / 180) * $Force + $Y1;
                $Xv1 = cos($Angle1 * PI / 180) * $Force + $XOff;
                $Yv1 = sin($Angle1 * PI / 180) * $Force + $YOff;
            }
            /* Last segment */
            if ($i == count($Coordinates) - 1) {
                $Xv2 = $X2;
                $Yv2 = $Y2;
            } else {
                $Angle1 = $this->getAngle($X2, $Y2, $Coordinates[$i + 1][0], $Coordinates[$i + 1][1]);
                $Angle2 = $this->getAngle($X1, $Y1, $X2, $Y2);
                $XOff = cos(($Angle2 + 180) * PI / 180) * $Force + $X2;
                $YOff = sin(($Angle2 + 180) * PI / 180) * $Force + $Y2;
                $Xv2 = cos(($Angle1 + 180) * PI / 180) * $Force + $XOff;
                $Yv2 = sin(($Angle1 + 180) * PI / 180) * $Force + $YOff;
            }
            $Path = $this->drawBezier($X1, $Y1, $X2, $Y2, $Xv1, $Yv1, $Xv2, $Yv2, $Format);
            if ($PathOnly) {
                $Result[] = $Path;
            }
            $XLast = $X1;
            $YLast = $Y1;
        }
        return $Result;
    }
    /**
     * Draw a bezier curve with two controls points
     * @param int $X1
     * @param int $Y1
     * @param int $X2
     * @param int $Y2
     * @param int $Xv1
     * @param int $Yv1
     * @param int $Xv2
     * @param int $Yv2
     * @param array $Format
     * @return array
     */
    public function drawBezier($X1, $Y1, $X2, $Y2, $Xv1, $Yv1, $Xv2, $Yv2, array $Format = [])
    {
        $R = isset($Format["R"]) ? $Format["R"] : 0;
        $G = isset($Format["G"]) ? $Format["G"] : 0;
        $B = isset($Format["B"]) ? $Format["B"] : 0;
        $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;
        $ShowC = isset($Format["ShowControl"]) ? $Format["ShowControl"] : \false;
        $Segments = isset($Format["Segments"]) ? $Format["Segments"] : null;
        $Ticks = isset($Format["Ticks"]) ? $Format["Ticks"] : null;
        $NoDraw = isset($Format["NoDraw"]) ? $Format["NoDraw"] : \false;
        $PathOnly = isset($Format["PathOnly"]) ? $Format["PathOnly"] : \false;
        $Weight = isset($Format["Weight"]) ? $Format["Weight"] : null;
        $DrawArrow = isset($Format["DrawArrow"]) ? $Format["DrawArrow"] : \false;
        $ArrowSize = isset($Format["ArrowSize"]) ? $Format["ArrowSize"] : 10;
        $ArrowRatio = isset($Format["ArrowRatio"]) ? $Format["ArrowRatio"] : 0.5;
        $ArrowTwoHeads = isset($Format["ArrowTwoHeads"]) ? $Format["ArrowTwoHeads"] : \false;
        if ($Segments == null) {
            $Length = $this->getLength($X1, $Y1, $X2, $Y2);
            $Precision = $Length * 125 / 1000;
        } else {
            $Precision = $Segments;
        }
        $P[0]["X"] = $X1;
        $P[0]["Y"] = $Y1;
        $P[1]["X"] = $Xv1;
        $P[1]["Y"] = $Yv1;
        $P[2]["X"] = $Xv2;
        $P[2]["Y"] = $Yv2;
        $P[3]["X"] = $X2;
        $P[3]["Y"] = $Y2;
        /* Compute the bezier points */
        $Q = [];
        $ID = 0;
        for ($i = 0; $i <= $Precision; $i = $i + 1) {
            $u = $i / $Precision;
            $C = [];
            $C[0] = (1 - $u) * (1 - $u) * (1 - $u);
            $C[1] = $u * 3 * (1 - $u) * (1 - $u);
            $C[2] = 3 * $u * $u * (1 - $u);
            $C[3] = $u * $u * $u;
            for ($j = 0; $j <= 3; $j++) {
                if (!isset($Q[$ID])) {
                    $Q[$ID] = [];
                }
                if (!isset($Q[$ID]["X"])) {
                    $Q[$ID]["X"] = 0;
                }
                if (!isset($Q[$ID]["Y"])) {
                    $Q[$ID]["Y"] = 0;
                }
                $Q[$ID]["X"] = $Q[$ID]["X"] + $P[$j]["X"] * $C[$j];
                $Q[$ID]["Y"] = $Q[$ID]["Y"] + $P[$j]["Y"] * $C[$j];
            }
            $ID++;
        }
        $Q[$ID]["X"] = $X2;
        $Q[$ID]["Y"] = $Y2;
        if (!$NoDraw) {
            /* Display the control points */
            if ($ShowC && !$PathOnly) {
                $Xv1 = floor($Xv1);
                $Yv1 = floor($Yv1);
                $Xv2 = floor($Xv2);
                $Yv2 = floor($Yv2);
                $this->drawLine($X1, $Y1, $X2, $Y2, ["R" => 0, "G" => 0, "B" => 0, "Alpha" => 30]);
                $MyMarkerSettings = ["R" => 255, "G" => 0, "B" => 0, "BorderR" => 255, "BorderB" => 255, "BorderG" => 255, "Size" => 4];
                $this->drawRectangleMarker($Xv1, $Yv1, $MyMarkerSettings);
                $this->drawText($Xv1 + 4, $Yv1, "v1");
                $MyMarkerSettings = ["R" => 0, "G" => 0, "B" => 255, "BorderR" => 255, "BorderB" => 255, "BorderG" => 255, "Size" => 4];
                $this->drawRectangleMarker($Xv2, $Yv2, $MyMarkerSettings);
                $this->drawText($Xv2 + 4, $Yv2, "v2");
            }
            /* Draw the bezier */
            $LastX = null;
            $LastY = null;
            $Cpt = null;
            $Mode = null;
            $ArrowS = [];
            foreach ($Q as $Point) {
                $X = $Point["X"];
                $Y = $Point["Y"];
                /* Get the first segment */
                if (!count($ArrowS) && $LastX != null && $LastY != null) {
                    $ArrowS["X2"] = $LastX;
                    $ArrowS["Y2"] = $LastY;
                    $ArrowS["X1"] = $X;
                    $ArrowS["Y1"] = $Y;
                }
                if ($LastX != null && $LastY != null && !$PathOnly) {
                    list($Cpt, $Mode) = $this->drawLine($LastX, $LastY, $X, $Y, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "Ticks" => $Ticks, "Cpt" => $Cpt, "Mode" => $Mode, "Weight" => $Weight]);
                }
                /* Get the last segment */
                $ArrowE["X1"] = $LastX;
                $ArrowE["Y1"] = $LastY;
                $ArrowE["X2"] = $X;
                $ArrowE["Y2"] = $Y;
                $LastX = $X;
                $LastY = $Y;
            }
            if ($DrawArrow && !$PathOnly) {
                $ArrowSettings = ["FillR" => $R, "FillG" => $G, "FillB" => $B, "Alpha" => $Alpha, "Size" => $ArrowSize, "Ratio" => $ArrowRatio];
                if ($ArrowTwoHeads) {
                    $this->drawArrow($ArrowS["X1"], $ArrowS["Y1"], $ArrowS["X2"], $ArrowS["Y2"], $ArrowSettings);
                }
                $this->drawArrow($ArrowE["X1"], $ArrowE["Y1"], $ArrowE["X2"], $ArrowE["Y2"], $ArrowSettings);
            }
        }
        return $Q;
    }
    /**
     * Draw a line between two points
     * @param int|float $X1
     * @param int|float $Y1
     * @param int|float $X2
     * @param int|float $Y2
     * @param array $Format
     * @return array|int
     */
    public function drawLine($X1, $Y1, $X2, $Y2, array $Format = [])
    {
        $R = isset($Format["R"]) ? $Format["R"] : 0;
        $G = isset($Format["G"]) ? $Format["G"] : 0;
        $B = isset($Format["B"]) ? $Format["B"] : 0;
        $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;
        $Ticks = isset($Format["Ticks"]) ? $Format["Ticks"] : null;
        $Cpt = isset($Format["Cpt"]) ? $Format["Cpt"] : 1;
        $Mode = isset($Format["Mode"]) ? $Format["Mode"] : 1;
        $Weight = isset($Format["Weight"]) ? $Format["Weight"] : null;
        $Threshold = isset($Format["Threshold"]) ? $Format["Threshold"] : null;
        if ($this->Antialias == \false && $Ticks == null) {
            if ($this->Shadow && $this->ShadowX != 0 && $this->ShadowY != 0) {
                $ShadowColor = $this->allocateColor($this->Picture, $this->ShadowR, $this->ShadowG, $this->ShadowB, $this->Shadowa);
                imageline($this->Picture, intval($X1 + $this->ShadowX), intval($Y1 + $this->ShadowY), intval($X2 + $this->ShadowX), intval($Y2 + $this->ShadowY), $ShadowColor);
            }
            $Color = $this->allocateColor($this->Picture, $R, $G, $B, $Alpha);
            imageline($this->Picture, (int) $X1, (int) $Y1, (int) $X2, (int) $Y2, $Color);
            return 0;
        }
        $Distance = sqrt(($X2 - $X1) * ($X2 - $X1) + ($Y2 - $Y1) * ($Y2 - $Y1));
        if ($Distance == 0) {
            return -1;
        }
        /* Derivative algorithm for overweighted lines, re-route to polygons primitives */
        if ($Weight != null) {
            $Angle = $this->getAngle($X1, $Y1, $X2, $Y2);
            $PolySettings = ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "BorderAlpha" => $Alpha];
            if ($Ticks == null) {
                $Points = [];
                $Points[] = cos(deg2rad($Angle - 90)) * $Weight + $X1;
                $Points[] = sin(deg2rad($Angle - 90)) * $Weight + $Y1;
                $Points[] = cos(deg2rad($Angle + 90)) * $Weight + $X1;
                $Points[] = sin(deg2rad($Angle + 90)) * $Weight + $Y1;
                $Points[] = cos(deg2rad($Angle + 90)) * $Weight + $X2;
                $Points[] = sin(deg2rad($Angle + 90)) * $Weight + $Y2;
                $Points[] = cos(deg2rad($Angle - 90)) * $Weight + $X2;
                $Points[] = sin(deg2rad($Angle - 90)) * $Weight + $Y2;
                $this->drawPolygon($Points, $PolySettings);
            } else {
                for ($i = 0; $i <= $Distance; $i = $i + $Ticks * 2) {
                    $Xa = ($X2 - $X1) / $Distance * $i + $X1;
                    $Ya = ($Y2 - $Y1) / $Distance * $i + $Y1;
                    $Xb = ($X2 - $X1) / $Distance * ($i + $Ticks) + $X1;
                    $Yb = ($Y2 - $Y1) / $Distance * ($i + $Ticks) + $Y1;
                    $Points = [];
                    $Points[] = cos(deg2rad($Angle - 90)) * $Weight + $Xa;
                    $Points[] = sin(deg2rad($Angle - 90)) * $Weight + $Ya;
                    $Points[] = cos(deg2rad($Angle + 90)) * $Weight + $Xa;
                    $Points[] = sin(deg2rad($Angle + 90)) * $Weight + $Ya;
                    $Points[] = cos(deg2rad($Angle + 90)) * $Weight + $Xb;
                    $Points[] = sin(deg2rad($Angle + 90)) * $Weight + $Yb;
                    $Points[] = cos(deg2rad($Angle - 90)) * $Weight + $Xb;
                    $Points[] = sin(deg2rad($Angle - 90)) * $Weight + $Yb;
                    $this->drawPolygon($Points, $PolySettings);
                }
            }
            return 1;
        }
        $XStep = ($X2 - $X1) / $Distance;
        $YStep = ($Y2 - $Y1) / $Distance;
        for ($i = 0; $i <= $Distance; $i++) {
            $X = $i * $XStep + $X1;
            $Y = $i * $YStep + $Y1;
            $Color = ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha];
            if ($Threshold != null) {
                foreach ($Threshold as $Key => $Parameters) {
                    if ($Y <= $Parameters["MinX"] && $Y >= $Parameters["MaxX"]) {
                        if (isset($Parameters["R"])) {
                            $RT = $Parameters["R"];
                        } else {
                            $RT = 0;
                        }
                        if (isset($Parameters["G"])) {
                            $GT = $Parameters["G"];
                        } else {
                            $GT = 0;
                        }
                        if (isset($Parameters["B"])) {
                            $BT = $Parameters["B"];
                        } else {
                            $BT = 0;
                        }
                        if (isset($Parameters["Alpha"])) {
                            $AlphaT = $Parameters["Alpha"];
                        } else {
                            $AlphaT = 0;
                        }
                        $Color = ["R" => $RT, "G" => $GT, "B" => $BT, "Alpha" => $AlphaT];
                    }
                }
            }
            if ($Ticks != null) {
                if ($Cpt % $Ticks == 0) {
                    $Cpt = 0;
                    if ($Mode == 1) {
                        $Mode = 0;
                    } else {
                        $Mode = 1;
                    }
                }
                if ($Mode == 1) {
                    $this->drawAntialiasPixel($X, $Y, $Color);
                }
                $Cpt++;
            } else {
                $this->drawAntialiasPixel($X, $Y, $Color);
            }
        }
        return [$Cpt, $Mode];
    }
    /**
     * Draw a circle
     * @param int $Xc
     * @param int $Yc
     * @param int|float $Height
     * @param int|float $Width
     * @param array $Format
     */
    public function drawCircle($Xc, $Yc, $Height, $Width, array $Format = [])
    {
        $R = isset($Format["R"]) ? $Format["R"] : 0;
        $G = isset($Format["G"]) ? $Format["G"] : 0;
        $B = isset($Format["B"]) ? $Format["B"] : 0;
        $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;
        $Ticks = isset($Format["Ticks"]) ? $Format["Ticks"] : null;
        $Height = abs($Height);
        $Width = abs($Width);
        if ($Height == 0) {
            $Height = 1;
        }
        if ($Width == 0) {
            $Width = 1;
        }
        $Xc = floor($Xc);
        $Yc = floor($Yc);
        $RestoreShadow = $this->Shadow;
        if ($this->Shadow && $this->ShadowX != 0 && $this->ShadowY != 0) {
            $this->Shadow = \false;
            $this->drawCircle($Xc + $this->ShadowX, $Yc + $this->ShadowY, $Height, $Width, ["R" => $this->ShadowR, "G" => $this->ShadowG, "B" => $this->ShadowB, "Alpha" => $this->Shadowa, "Ticks" => $Ticks]);
        }
        if ($Width == 0) {
            $Width = $Height;
        }
        if ($R < 0) {
            $R = 0;
        }
        if ($R > 255) {
            $R = 255;
        }
        if ($G < 0) {
            $G = 0;
        }
        if ($G > 255) {
            $G = 255;
        }
        if ($B < 0) {
            $B = 0;
        }
        if ($B > 255) {
            $B = 255;
        }
        $Step = 360 / (2 * PI * max($Width, $Height));
        $Mode = 1;
        $Cpt = 1;
        for ($i = 0; $i <= 360; $i = $i + $Step) {
            $X = cos($i * PI / 180) * $Height + $Xc;
            $Y = sin($i * PI / 180) * $Width + $Yc;
            if ($Ticks != null) {
                if ($Cpt % $Ticks == 0) {
                    $Cpt = 0;
                    if ($Mode == 1) {
                        $Mode = 0;
                    } else {
                        $Mode = 1;
                    }
                }
                if ($Mode == 1) {
                    $this->drawAntialiasPixel($X, $Y, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha]);
                }
                $Cpt++;
            } else {
                $this->drawAntialiasPixel($X, $Y, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha]);
            }
        }
        $this->Shadow = $RestoreShadow;
    }
    /**
     * Draw a filled circle
     * @param int $X
     * @param int $Y
     * @param int|float $Radius
     * @param array $Format
     */
    public function drawFilledCircle($X, $Y, $Radius, array $Format = [])
    {
        $R = isset($Format["R"]) ? $Format["R"] : 0;
        $G = isset($Format["G"]) ? $Format["G"] : 0;
        $B = isset($Format["B"]) ? $Format["B"] : 0;
        $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;
        $BorderR = isset($Format["BorderR"]) ? $Format["BorderR"] : -1;
        $BorderG = isset($Format["BorderG"]) ? $Format["BorderG"] : -1;
        $BorderB = isset($Format["BorderB"]) ? $Format["BorderB"] : -1;
        $BorderAlpha = isset($Format["BorderAlpha"]) ? $Format["BorderAlpha"] : $Alpha;
        $Ticks = isset($Format["Ticks"]) ? $Format["Ticks"] : null;
        $Surrounding = isset($Format["Surrounding"]) ? $Format["Surrounding"] : null;
        if ($Radius == 0) {
            $Radius = 1;
        }
        if ($Surrounding != null) {
            $BorderR = $R + $Surrounding;
            $BorderG = $G + $Surrounding;
            $BorderB = $B + $Surrounding;
        }
        $X = (int) floor($X);
        $Y = (int) floor($Y);
        $Radius = abs($Radius);
        $RestoreShadow = $this->Shadow;
        if ($this->Shadow && $this->ShadowX != 0 && $this->ShadowY != 0) {
            $this->Shadow = \false;
            $this->drawFilledCircle($X + $this->ShadowX, $Y + $this->ShadowY, $Radius, ["R" => $this->ShadowR, "G" => $this->ShadowG, "B" => $this->ShadowB, "Alpha" => $this->Shadowa, "Ticks" => $Ticks]);
        }
        $this->Mask = [];
        $Color = $this->allocateColor($this->Picture, $R, $G, $B, $Alpha);
        for ($i = 0; $i <= $Radius * 2; $i++) {
            $Slice = sqrt($Radius * $Radius - ($Radius - $i) * ($Radius - $i));
            $XPos = (int) floor($Slice);
            $YPos = (int) ($Y + $i - $Radius);
            $this->Mask[$X - $XPos][$YPos] = \true;
            $this->Mask[$X + $XPos][$YPos] = \true;
            imageline($this->Picture, $X - $XPos, $YPos, $X + $XPos, $YPos, $Color);
        }
        if ($this->Antialias) {
            $this->drawCircle($X, $Y, $Radius, $Radius, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "Ticks" => $Ticks]);
        }
        $this->Mask = [];
        if ($BorderR != -1) {
            $this->drawCircle($X, $Y, $Radius, $Radius, ["R" => $BorderR, "G" => $BorderG, "B" => $BorderB, "Alpha" => $BorderAlpha, "Ticks" => $Ticks]);
        }
        $this->Shadow = $RestoreShadow;
    }
    /**
     * Write text
     * @param int|float $X
     * @param int|float $Y
     * @param string $Text
     * @param array $Format
     * @return array
     */
    public function drawText($X, $Y, $Text, array $Format = [])
    {
        $R = isset($Format["R"]) ? $Format["R"] : $this->FontColorR;
        $G = isset($Format["G"]) ? $Format["G"] : $this->FontColorG;
        $B = isset($Format["B"]) ? $Format["B"] : $this->FontColorB;
        $Angle = isset($Format["Angle"]) ? $Format["Angle"] : 0;
        $Align = isset($Format["Align"]) ? $Format["Align"] : TEXT_ALIGN_BOTTOMLEFT;
        $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : $this->FontColorA;
        $FontName = isset($Format["FontName"]) ? $this->loadFont($Format["FontName"], 'fonts') : $this->FontName;
        $FontSize = isset($Format["FontSize"]) ? $Format["FontSize"] : $this->FontSize;
        $ShowOrigine = isset($Format["ShowOrigine"]) ? $Format["ShowOrigine"] : \false;
        $TOffset = isset($Format["TOffset"]) ? $Format["TOffset"] : 2;
        $DrawBox = isset($Format["DrawBox"]) ? $Format["DrawBox"] : \false;
        $BorderOffset = isset($Format["BorderOffset"]) ? $Format["BorderOffset"] : 6;
        $BoxRounded = isset($Format["BoxRounded"]) ? $Format["BoxRounded"] : \false;
        $RoundedRadius = isset($Format["RoundedRadius"]) ? $Format["RoundedRadius"] : 6;
        $BoxR = isset($Format["BoxR"]) ? $Format["BoxR"] : 255;
        $BoxG = isset($Format["BoxG"]) ? $Format["BoxG"] : 255;
        $BoxB = isset($Format["BoxB"]) ? $Format["BoxB"] : 255;
        $BoxAlpha = isset($Format["BoxAlpha"]) ? $Format["BoxAlpha"] : 50;
        $BoxSurrounding = isset($Format["BoxSurrounding"]) ? $Format["BoxSurrounding"] : "";
        $BoxBorderR = isset($Format["BoxR"]) ? $Format["BoxR"] : 0;
        $BoxBorderG = isset($Format["BoxG"]) ? $Format["BoxG"] : 0;
        $BoxBorderB = isset($Format["BoxB"]) ? $Format["BoxB"] : 0;
        $BoxBorderAlpha = isset($Format["BoxAlpha"]) ? $Format["BoxAlpha"] : 50;
        $NoShadow = isset($Format["NoShadow"]) ? $Format["NoShadow"] : \false;
        $Shadow = $this->Shadow;
        if ($NoShadow) {
            $this->Shadow = \false;
        }
        if ($BoxSurrounding != "") {
            $BoxBorderR = $BoxR - $BoxSurrounding;
            $BoxBorderG = $BoxG - $BoxSurrounding;
            $BoxBorderB = $BoxB - $BoxSurrounding;
            $BoxBorderAlpha = $BoxAlpha;
        }
        if ($ShowOrigine) {
            $MyMarkerSettings = ["R" => 255, "G" => 0, "B" => 0, "BorderR" => 255, "BorderB" => 255, "BorderG" => 255, "Size" => 4];
            $this->drawRectangleMarker($X, $Y, $MyMarkerSettings);
        }
        $TxtPos = $this->getTextBox($X, $Y, $FontName, $FontSize, $Angle, $Text);
        if ($DrawBox && ($Angle == 0 || $Angle == 90 || $Angle == 180 || $Angle == 270)) {
            $T[0]["X"] = 0;
            $T[0]["Y"] = 0;
            $T[1]["X"] = 0;
            $T[1]["Y"] = 0;
            $T[2]["X"] = 0;
            $T[2]["Y"] = 0;
            $T[3]["X"] = 0;
            $T[3]["Y"] = 0;
            if ($Angle == 0) {
                $T[0]["X"] = -$TOffset;
                $T[0]["Y"] = $TOffset;
                $T[1]["X"] = $TOffset;
                $T[1]["Y"] = $TOffset;
                $T[2]["X"] = $TOffset;
                $T[2]["Y"] = -$TOffset;
                $T[3]["X"] = -$TOffset;
                $T[3]["Y"] = -$TOffset;
            }
            $X1 = min($TxtPos[0]["X"], $TxtPos[1]["X"], $TxtPos[2]["X"], $TxtPos[3]["X"]) - $BorderOffset + 3;
            $Y1 = min($TxtPos[0]["Y"], $TxtPos[1]["Y"], $TxtPos[2]["Y"], $TxtPos[3]["Y"]) - $BorderOffset;
            $X2 = max($TxtPos[0]["X"], $TxtPos[1]["X"], $TxtPos[2]["X"], $TxtPos[3]["X"]) + $BorderOffset + 3;
            $Y2 = max($TxtPos[0]["Y"], $TxtPos[1]["Y"], $TxtPos[2]["Y"], $TxtPos[3]["Y"]) + $BorderOffset - 3;
            $X1 = $X1 - $TxtPos[$Align]["X"] + $X + $T[0]["X"];
            $Y1 = $Y1 - $TxtPos[$Align]["Y"] + $Y + $T[0]["Y"];
            $X2 = $X2 - $TxtPos[$Align]["X"] + $X + $T[0]["X"];
            $Y2 = $Y2 - $TxtPos[$Align]["Y"] + $Y + $T[0]["Y"];
            $Settings = ["R" => $BoxR, "G" => $BoxG, "B" => $BoxB, "Alpha" => $BoxAlpha, "BorderR" => $BoxBorderR, "BorderG" => $BoxBorderG, "BorderB" => $BoxBorderB, "BorderAlpha" => $BoxBorderAlpha];
            if ($BoxRounded) {
                $this->drawRoundedFilledRectangle($X1, $Y1, $X2, $Y2, $RoundedRadius, $Settings);
            } else {
                $this->drawFilledRectangle($X1, $Y1, $X2, $Y2, $Settings);
            }
        }
        $X = (int) ($X - $TxtPos[$Align]["X"] + $X);
        $Y = (int) ($Y - $TxtPos[$Align]["Y"] + $Y);
        if ($this->Shadow && $this->ShadowX != 0 && $this->ShadowY != 0) {
            $C_ShadowColor = $this->allocateColor($this->Picture, $this->ShadowR, $this->ShadowG, $this->ShadowB, $this->Shadowa);
            imagettftext($this->Picture, $FontSize, $Angle, (int) ($X + $this->ShadowX), (int) ($Y + $this->ShadowY), (int) $C_ShadowColor, $FontName, $Text);
        }
        $C_TextColor = $this->AllocateColor($this->Picture, $R, $G, $B, $Alpha);
        imagettftext($this->Picture, $FontSize, $Angle, $X, $Y, $C_TextColor, $FontName, $Text);
        $this->Shadow = $Shadow;
        return $TxtPos;
    }
    /**
     * Draw a gradient within a defined area
     * @param int $X1
     * @param int $Y1
     * @param int $X2
     * @param int $Y2
     * @param int $Direction
     * @param array $Format
     * @return null|integer
     */
    public function drawGradientArea($X1, $Y1, $X2, $Y2, $Direction, array $Format = [])
    {
        $StartR = isset($Format["StartR"]) ? $Format["StartR"] : 90;
        $StartG = isset($Format["StartG"]) ? $Format["StartG"] : 90;
        $StartB = isset($Format["StartB"]) ? $Format["StartB"] : 90;
        $EndR = isset($Format["EndR"]) ? $Format["EndR"] : 0;
        $EndG = isset($Format["EndG"]) ? $Format["EndG"] : 0;
        $EndB = isset($Format["EndB"]) ? $Format["EndB"] : 0;
        $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;
        $Levels = isset($Format["Levels"]) ? $Format["Levels"] : null;
        $Shadow = $this->Shadow;
        $this->Shadow = \false;
        if ($StartR == $EndR && $StartG == $EndG && $StartB == $EndB) {
            $this->drawFilledRectangle($X1, $Y1, $X2, $Y2, ["R" => $StartR, "G" => $StartG, "B" => $StartB, "Alpha" => $Alpha]);
            return 0;
        }
        if ($Levels != null) {
            $EndR = $StartR + $Levels;
            $EndG = $StartG + $Levels;
            $EndB = $StartB + $Levels;
        }
        if ($X1 > $X2) {
            list($X1, $X2) = [$X2, $X1];
        }
        if ($Y1 > $Y2) {
            list($Y1, $Y2) = [$Y2, $Y1];
        }
        if ($Direction == DIRECTION_VERTICAL) {
            $Width = abs($Y2 - $Y1);
        }
        if ($Direction == DIRECTION_HORIZONTAL) {
            $Width = abs($X2 - $X1);
        }
        $Step = max(abs($EndR - $StartR), abs($EndG - $StartG), abs($EndB - $StartB));
        $StepSize = $Width / $Step;
        $RStep = ($EndR - $StartR) / $Step;
        $GStep = ($EndG - $StartG) / $Step;
        $BStep = ($EndB - $StartB) / $Step;
        $R = $StartR;
        $G = $StartG;
        $B = $StartB;
        switch ($Direction) {
            case DIRECTION_VERTICAL:
                $StartY = $Y1;
                $EndY = floor($Y2) + 1;
                $LastY2 = $StartY;
                for ($i = 0; $i <= $Step; $i++) {
                    $Y2 = floor($StartY + $i * $StepSize);
                    if ($Y2 > $EndY) {
                        $Y2 = $EndY;
                    }
                    if ($Y1 != $Y2 && $Y1 < $Y2 || $Y2 == $EndY) {
                        $Color = ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha];
                        $this->drawFilledRectangle($X1, $Y1, $X2, $Y2, $Color);
                        $LastY2 = max($LastY2, $Y2);
                        $Y1 = $Y2 + 1;
                    }
                    $R = $R + $RStep;
                    $G = $G + $GStep;
                    $B = $B + $BStep;
                }
                if ($LastY2 < $EndY && isset($Color)) {
                    for ($i = $LastY2 + 1; $i <= $EndY; $i++) {
                        $this->drawLine($X1, $i, $X2, $i, $Color);
                    }
                }
                break;
            case DIRECTION_HORIZONTAL:
                $StartX = $X1;
                $EndX = $X2;
                for ($i = 0; $i <= $Step; $i++) {
                    $X2 = floor($StartX + $i * $StepSize);
                    if ($X2 > $EndX) {
                        $X2 = $EndX;
                    }
                    if ($X1 != $X2 && $X1 < $X2 || $X2 == $EndX) {
                        $Color = ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha];
                        $this->drawFilledRectangle($X1, $Y1, $X2, $Y2, $Color);
                        $X1 = $X2 + 1;
                    }
                    $R = $R + $RStep;
                    $G = $G + $GStep;
                    $B = $B + $BStep;
                }
                if ($X2 < $EndX && isset($Color)) {
                    $this->drawFilledRectangle($X2, $Y1, $EndX, $Y2, $Color);
                }
                break;
        }
        $this->Shadow = $Shadow;
    }
    /**
     * Draw an aliased pixel
     * @param int $X
     * @param int $Y
     * @param array $Format
     * @return int|null
     */
    public function drawAntialiasPixel($X, $Y, array $Format = [])
    {
        $R = isset($Format["R"]) ? $Format["R"] : 0;
        $G = isset($Format["G"]) ? $Format["G"] : 0;
        $B = isset($Format["B"]) ? $Format["B"] : 0;
        $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;
        if ($X < 0 || $Y < 0 || $X >= $this->XSize || $Y >= $this->YSize) {
            return -1;
        }
        if ($R < 0) {
            $R = 0;
        }
        if ($R > 255) {
            $R = 255;
        }
        if ($G < 0) {
            $G = 0;
        }
        if ($G > 255) {
            $G = 255;
        }
        if ($B < 0) {
            $B = 0;
        }
        if ($B > 255) {
            $B = 255;
        }
        if (!$this->Antialias) {
            if ($this->Shadow && $this->ShadowX != 0 && $this->ShadowY != 0) {
                $ShadowColor = $this->allocateColor($this->Picture, $this->ShadowR, $this->ShadowG, $this->ShadowB, $this->Shadowa);
                imagesetpixel($this->Picture, intval($X + $this->ShadowX), intval($Y + $this->ShadowY), $ShadowColor);
            }
            $PlotColor = $this->allocateColor($this->Picture, $R, $G, $B, $Alpha);
            imagesetpixel($this->Picture, (int) $X, (int) $Y, $PlotColor);
            return 0;
        }
        $Xi = floor($X);
        $Yi = floor($Y);
        if ($Xi == $X && $Yi == $Y) {
            if ($Alpha == 100) {
                $this->drawAlphaPixel($X, $Y, 100, $R, $G, $B);
            } else {
                $this->drawAlphaPixel($X, $Y, $Alpha, $R, $G, $B);
            }
        } else {
            $Alpha1 = (1 - ($X - floor($X))) * (1 - ($Y - floor($Y))) * 100 / 100 * $Alpha;
            if ($Alpha1 > $this->AntialiasQuality) {
                $this->drawAlphaPixel($Xi, $Yi, $Alpha1, $R, $G, $B);
            }
            $Alpha2 = ($X - floor($X)) * (1 - ($Y - floor($Y))) * 100 / 100 * $Alpha;
            if ($Alpha2 > $this->AntialiasQuality) {
                $this->drawAlphaPixel($Xi + 1, $Yi, $Alpha2, $R, $G, $B);
            }
            $Alpha3 = (1 - ($X - floor($X))) * ($Y - floor($Y)) * 100 / 100 * $Alpha;
            if ($Alpha3 > $this->AntialiasQuality) {
                $this->drawAlphaPixel($Xi, $Yi + 1, $Alpha3, $R, $G, $B);
            }
            $Alpha4 = ($X - floor($X)) * ($Y - floor($Y)) * 100 / 100 * $Alpha;
            if ($Alpha4 > $this->AntialiasQuality) {
                $this->drawAlphaPixel($Xi + 1, $Yi + 1, $Alpha4, $R, $G, $B);
            }
        }
    }
    /**
     * Draw a semi-transparent pixel
     * @param int $X
     * @param int $Y
     * @param int $Alpha
     * @param int $R
     * @param int $G
     * @param int $B
     * @return null|integer
     */
    public function drawAlphaPixel($X, $Y, $Alpha, $R, $G, $B)
    {
        if (isset($this->Mask[$X]) && isset($this->Mask[$X][$Y])) {
            return 0;
        }
        if ($X < 0 || $Y < 0 || $X >= $this->XSize || $Y >= $this->YSize) {
            return -1;
        }
        if ($R < 0) {
            $R = 0;
        }
        if ($R > 255) {
            $R = 255;
        }
        if ($G < 0) {
            $G = 0;
        }
        if ($G > 255) {
            $G = 255;
        }
        if ($B < 0) {
            $B = 0;
        }
        if ($B > 255) {
            $B = 255;
        }
        if ($this->Shadow && $this->ShadowX != 0 && $this->ShadowY != 0) {
            $AlphaFactor = floor($Alpha / 100 * $this->Shadowa);
            $ShadowColor = $this->allocateColor($this->Picture, $this->ShadowR, $this->ShadowG, $this->ShadowB, $AlphaFactor);
            imagesetpixel($this->Picture, (int) ($X + $this->ShadowX), (int) ($Y + $this->ShadowY), $ShadowColor);
        }
        $C_Aliased = $this->allocateColor($this->Picture, $R, $G, $B, $Alpha);
        imagesetpixel($this->Picture, (int) $X, (int) $Y, $C_Aliased);
    }
    /**
     * Load a PNG file and draw it over the chart
     * @param int $X
     * @param int $Y
     * @param string $FileName
     */
    public function drawFromPNG($X, $Y, $FileName)
    {
        $this->drawFromPicture(1, $FileName, $X, $Y);
    }
    /**
     * Load a GIF file and draw it over the chart
     * @param int $X
     * @param int $Y
     * @param string $FileName
     */
    public function drawFromGIF($X, $Y, $FileName)
    {
        $this->drawFromPicture(2, $FileName, $X, $Y);
    }
    /**
     * Load a JPEG file and draw it over the chart
     * @param int $X
     * @param int $Y
     * @param string $FileName
     */
    public function drawFromJPG($X, $Y, $FileName)
    {
        $this->drawFromPicture(3, $FileName, $X, $Y);
    }
    /**
     * Generic loader public function for external pictures
     * @param int $PicType
     * @param string $FileName
     * @param int $X
     * @param int $Y
     * @return null|integer
     */
    public function drawFromPicture($PicType, $FileName, $X, $Y)
    {
        $X = (int) $X;
        $Y = (int) $Y;
        if (file_exists($FileName)) {
            list($Width, $Height) = $this->getPicInfo($FileName);
            if ($PicType == 1) {
                $Raster = imagecreatefrompng($FileName);
            } elseif ($PicType == 2) {
                $Raster = imagecreatefromgif($FileName);
            } elseif ($PicType == 3) {
                $Raster = imagecreatefromjpeg($FileName);
            } else {
                return 0;
            }
            $RestoreShadow = $this->Shadow;
            if ($this->Shadow && $this->ShadowX != 0 && $this->ShadowY != 0) {
                $this->Shadow = \false;
                if ($PicType == 3) {
                    $this->drawFilledRectangle($X + $this->ShadowX, $Y + $this->ShadowY, $X + $Width + $this->ShadowX, $Y + $Height + $this->ShadowY, ["R" => $this->ShadowR, "G" => $this->ShadowG, "B" => $this->ShadowB, "Alpha" => $this->Shadowa]);
                } else {
                    $TranparentID = imagecolortransparent($Raster);
                    for ($Xc = 0; $Xc <= $Width - 1; $Xc++) {
                        for ($Yc = 0; $Yc <= $Height - 1; $Yc++) {
                            $RGBa = imagecolorat($Raster, $Xc, $Yc);
                            $Values = imagecolorsforindex($Raster, $RGBa);
                            if ($Values["alpha"] < 120) {
                                $AlphaFactor = floor($this->Shadowa / 100 * (100 / 127 * (127 - $Values["alpha"])));
                                $this->drawAlphaPixel($X + $Xc + $this->ShadowX, $Y + $Yc + $this->ShadowY, $AlphaFactor, $this->ShadowR, $this->ShadowG, $this->ShadowB);
                            }
                        }
                    }
                }
            }
            $this->Shadow = $RestoreShadow;
            imagecopy($this->Picture, $Raster, $X, $Y, 0, 0, $Width, $Height);
            imagedestroy($Raster);
        }
    }
    /**
     * Draw an arrow
     * @param int $X1
     * @param int $Y1
     * @param int $X2
     * @param int $Y2
     * @param array $Format
     */
    public function drawArrow($X1, $Y1, $X2, $Y2, array $Format = [])
    {
        $FillR = isset($Format["FillR"]) ? $Format["FillR"] : 0;
        $FillG = isset($Format["FillG"]) ? $Format["FillG"] : 0;
        $FillB = isset($Format["FillB"]) ? $Format["FillB"] : 0;
        $BorderR = isset($Format["BorderR"]) ? $Format["BorderR"] : $FillR;
        $BorderG = isset($Format["BorderG"]) ? $Format["BorderG"] : $FillG;
        $BorderB = isset($Format["BorderB"]) ? $Format["BorderB"] : $FillB;
        $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;
        $Size = isset($Format["Size"]) ? $Format["Size"] : 10;
        $Ratio = isset($Format["Ratio"]) ? $Format["Ratio"] : 0.5;
        $TwoHeads = isset($Format["TwoHeads"]) ? $Format["TwoHeads"] : \false;
        $Ticks = isset($Format["Ticks"]) ? $Format["Ticks"] : \false;
        /* Calculate the line angle */
        $Angle = $this->getAngle($X1, $Y1, $X2, $Y2);
        /* Override Shadow support, this will be managed internally */
        $RestoreShadow = $this->Shadow;
        if ($this->Shadow && $this->ShadowX != 0 && $this->ShadowY != 0) {
            $this->Shadow = \false;
            $this->drawArrow($X1 + $this->ShadowX, $Y1 + $this->ShadowY, $X2 + $this->ShadowX, $Y2 + $this->ShadowY, ["FillR" => $this->ShadowR, "FillG" => $this->ShadowG, "FillB" => $this->ShadowB, "Alpha" => $this->Shadowa, "Size" => $Size, "Ratio" => $Ratio, "TwoHeads" => $TwoHeads, "Ticks" => $Ticks]);
        }
        /* Draw the 1st Head */
        $TailX = cos(($Angle - 180) * PI / 180) * $Size + $X2;
        $TailY = sin(($Angle - 180) * PI / 180) * $Size + $Y2;
        $Points = [];
        $Points[] = $X2;
        $Points[] = $Y2;
        $Points[] = cos(($Angle - 90) * PI / 180) * $Size * $Ratio + $TailX;
        $Points[] = sin(($Angle - 90) * PI / 180) * $Size * $Ratio + $TailY;
        $Points[] = cos(($Angle - 270) * PI / 180) * $Size * $Ratio + $TailX;
        $Points[] = sin(($Angle - 270) * PI / 180) * $Size * $Ratio + $TailY;
        $Points[] = $X2;
        $Points[] = $Y2;
        /* Visual correction */
        if ($Angle == 180 || $Angle == 360) {
            $Points[4] = $Points[2];
        }
        if ($Angle == 90 || $Angle == 270) {
            $Points[5] = $Points[3];
        }
        $ArrowColor = $this->allocateColor($this->Picture, $FillR, $FillG, $FillB, $Alpha);
        $this->imageFilledPolygonWrapper($this->Picture, $Points, 4, $ArrowColor);
        $this->drawLine($Points[0], $Points[1], $Points[2], $Points[3], ["R" => $BorderR, "G" => $BorderG, "B" => $BorderB, "Alpha" => $Alpha]);
        $this->drawLine($Points[2], $Points[3], $Points[4], $Points[5], ["R" => $BorderR, "G" => $BorderG, "B" => $BorderB, "Alpha" => $Alpha]);
        $this->drawLine($Points[0], $Points[1], $Points[4], $Points[5], ["R" => $BorderR, "G" => $BorderG, "B" => $BorderB, "Alpha" => $Alpha]);
        /* Draw the second head */
        if ($TwoHeads) {
            $Angle = $this->getAngle($X2, $Y2, $X1, $Y1);
            $TailX2 = cos(($Angle - 180) * PI / 180) * $Size + $X1;
            $TailY2 = sin(($Angle - 180) * PI / 180) * $Size + $Y1;
            $Points = [];
            $Points[] = $X1;
            $Points[] = $Y1;
            $Points[] = cos(($Angle - 90) * PI / 180) * $Size * $Ratio + $TailX2;
            $Points[] = sin(($Angle - 90) * PI / 180) * $Size * $Ratio + $TailY2;
            $Points[] = cos(($Angle - 270) * PI / 180) * $Size * $Ratio + $TailX2;
            $Points[] = sin(($Angle - 270) * PI / 180) * $Size * $Ratio + $TailY2;
            $Points[] = $X1;
            $Points[] = $Y1;
            /* Visual correction */
            if ($Angle == 180 || $Angle == 360) {
                $Points[4] = $Points[2];
            }
            if ($Angle == 90 || $Angle == 270) {
                $Points[5] = $Points[3];
            }
            $ArrowColor = $this->allocateColor($this->Picture, $FillR, $FillG, $FillB, $Alpha);
            $this->imageFilledPolygonWrapper($this->Picture, $Points, 4, $ArrowColor);
            $this->drawLine($Points[0], $Points[1], $Points[2], $Points[3], ["R" => $BorderR, "G" => $BorderG, "B" => $BorderB, "Alpha" => $Alpha]);
            $this->drawLine($Points[2], $Points[3], $Points[4], $Points[5], ["R" => $BorderR, "G" => $BorderG, "B" => $BorderB, "Alpha" => $Alpha]);
            $this->drawLine($Points[0], $Points[1], $Points[4], $Points[5], ["R" => $BorderR, "G" => $BorderG, "B" => $BorderB, "Alpha" => $Alpha]);
            $this->drawLine($TailX, $TailY, $TailX2, $TailY2, ["R" => $BorderR, "G" => $BorderG, "B" => $BorderB, "Alpha" => $Alpha, "Ticks" => $Ticks]);
        } else {
            $this->drawLine($X1, $Y1, $TailX, $TailY, ["R" => $BorderR, "G" => $BorderG, "B" => $BorderB, "Alpha" => $Alpha, "Ticks" => $Ticks]);
        }
        /* Re-enable shadows */
        $this->Shadow = $RestoreShadow;
    }
    /**
     * Draw a label with associated arrow
     * @param int $X1
     * @param int $Y1
     * @param string $Text
     * @param array $Format
     */
    public function drawArrowLabel($X1, $Y1, $Text, array $Format = [])
    {
        $FillR = isset($Format["FillR"]) ? $Format["FillR"] : 0;
        $FillG = isset($Format["FillG"]) ? $Format["FillG"] : 0;
        $FillB = isset($Format["FillB"]) ? $Format["FillB"] : 0;
        $BorderR = isset($Format["BorderR"]) ? $Format["BorderR"] : $FillR;
        $BorderG = isset($Format["BorderG"]) ? $Format["BorderG"] : $FillG;
        $BorderB = isset($Format["BorderB"]) ? $Format["BorderB"] : $FillB;
        $FontName = isset($Format["FontName"]) ? $this->loadFont($Format["FontName"], 'fonts') : $this->FontName;
        $FontSize = isset($Format["FontSize"]) ? $Format["FontSize"] : $this->FontSize;
        $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;
        $Length = isset($Format["Length"]) ? $Format["Length"] : 50;
        $Angle = isset($Format["Angle"]) ? (int) $Format["Angle"] : 315;
        $Size = isset($Format["Size"]) ? $Format["Size"] : 10;
        $Position = isset($Format["Position"]) ? $Format["Position"] : POSITION_TOP;
        $RoundPos = isset($Format["RoundPos"]) ? $Format["RoundPos"] : \false;
        $Ticks = isset($Format["Ticks"]) ? $Format["Ticks"] : null;
        $Angle = $Angle % 360;
        $X2 = sin(($Angle + 180) * PI / 180) * $Length + $X1;
        $Y2 = cos(($Angle + 180) * PI / 180) * $Length + $Y1;
        if ($RoundPos && $Angle > 0 && $Angle < 180) {
            $Y2 = ceil($Y2);
        }
        if ($RoundPos && $Angle > 180) {
            $Y2 = floor($Y2);
        }
        $this->drawArrow($X2, $Y2, $X1, $Y1, $Format);
        $Size = imagettfbbox($FontSize, 0, $FontName, $Text);
        $TxtWidth = max(abs($Size[2] - $Size[0]), abs($Size[0] - $Size[6]));
        if ($Angle > 0 && $Angle < 180) {
            $this->drawLine($X2, $Y2, $X2 - $TxtWidth, $Y2, ["R" => $BorderR, "G" => $BorderG, "B" => $BorderB, "Alpha" => $Alpha, "Ticks" => $Ticks]);
            if ($Position == POSITION_TOP) {
                $this->drawText($X2, $Y2 - 2, $Text, ["R" => $BorderR, "G" => $BorderG, "B" => $BorderB, "Alpha" => $Alpha, "Align" => TEXT_ALIGN_BOTTOMRIGHT]);
            } else {
                $this->drawText($X2, $Y2 + 4, $Text, ["R" => $BorderR, "G" => $BorderG, "B" => $BorderB, "Alpha" => $Alpha, "Align" => TEXT_ALIGN_TOPRIGHT]);
            }
        } else {
            $this->drawLine($X2, $Y2, $X2 + $TxtWidth, $Y2, ["R" => $BorderR, "G" => $BorderG, "B" => $BorderB, "Alpha" => $Alpha, "Ticks" => $Ticks]);
            if ($Position == POSITION_TOP) {
                $this->drawText($X2, $Y2 - 2, $Text, ["R" => $BorderR, "G" => $BorderG, "B" => $BorderB, "Alpha" => $Alpha]);
            } else {
                $this->drawText($X2, $Y2 + 4, $Text, ["R" => $BorderR, "G" => $BorderG, "B" => $BorderB, "Alpha" => $Alpha, "Align" => TEXT_ALIGN_TOPLEFT]);
            }
        }
    }
    /**
     * Draw a progress bar filled with specified %
     * @param int $X
     * @param int $Y
     * @param int|float $Percent
     * @param array $Format
     */
    public function drawProgress($X, $Y, $Percent, array $Format = [])
    {
        if ($Percent > 100) {
            $Percent = 100;
        }
        if ($Percent < 0) {
            $Percent = 0;
        }
        $Width = isset($Format["Width"]) ? $Format["Width"] : 200;
        $Height = isset($Format["Height"]) ? $Format["Height"] : 20;
        $Orientation = isset($Format["Orientation"]) ? $Format["Orientation"] : ORIENTATION_HORIZONTAL;
        $ShowLabel = isset($Format["ShowLabel"]) ? $Format["ShowLabel"] : \false;
        $LabelPos = isset($Format["LabelPos"]) ? $Format["LabelPos"] : LABEL_POS_INSIDE;
        $Margin = isset($Format["Margin"]) ? $Format["Margin"] : 10;
        $R = isset($Format["R"]) ? $Format["R"] : 130;
        $G = isset($Format["G"]) ? $Format["G"] : 130;
        $B = isset($Format["B"]) ? $Format["B"] : 130;
        $RFade = isset($Format["RFade"]) ? $Format["RFade"] : -1;
        $GFade = isset($Format["GFade"]) ? $Format["GFade"] : -1;
        $BFade = isset($Format["BFade"]) ? $Format["BFade"] : -1;
        $BorderR = isset($Format["BorderR"]) ? $Format["BorderR"] : $R;
        $BorderG = isset($Format["BorderG"]) ? $Format["BorderG"] : $G;
        $BorderB = isset($Format["BorderB"]) ? $Format["BorderB"] : $B;
        $BoxBorderR = isset($Format["BoxBorderR"]) ? $Format["BoxBorderR"] : 0;
        $BoxBorderG = isset($Format["BoxBorderG"]) ? $Format["BoxBorderG"] : 0;
        $BoxBorderB = isset($Format["BoxBorderB"]) ? $Format["BoxBorderB"] : 0;
        $BoxBackR = isset($Format["BoxBackR"]) ? $Format["BoxBackR"] : 255;
        $BoxBackG = isset($Format["BoxBackG"]) ? $Format["BoxBackG"] : 255;
        $BoxBackB = isset($Format["BoxBackB"]) ? $Format["BoxBackB"] : 255;
        $Surrounding = isset($Format["Surrounding"]) ? $Format["Surrounding"] : null;
        $BoxSurrounding = isset($Format["BoxSurrounding"]) ? $Format["BoxSurrounding"] : null;
        $NoAngle = isset($Format["NoAngle"]) ? $Format["NoAngle"] : \false;
        if ($RFade != -1 && $GFade != -1 && $BFade != -1) {
            $RFade = ($RFade - $R) / 100 * $Percent + $R;
            $GFade = ($GFade - $G) / 100 * $Percent + $G;
            $BFade = ($BFade - $B) / 100 * $Percent + $B;
        }
        if ($Surrounding != null) {
            $BorderR = $R + $Surrounding;
            $BorderG = $G + $Surrounding;
            $BorderB = $B + $Surrounding;
        }
        if ($BoxSurrounding != null) {
            $BoxBorderR = $BoxBackR + $Surrounding;
            $BoxBorderG = $BoxBackG + $Surrounding;
            $BoxBorderB = $BoxBackB + $Surrounding;
        }
        if ($Orientation == ORIENTATION_VERTICAL) {
            $InnerHeight = ($Height - 2) / 100 * $Percent;
            $this->drawFilledRectangle($X, $Y, $X + $Width, $Y - $Height, ["R" => $BoxBackR, "G" => $BoxBackG, "B" => $BoxBackB, "BorderR" => $BoxBorderR, "BorderG" => $BoxBorderG, "BorderB" => $BoxBorderB, "NoAngle" => $NoAngle]);
            $RestoreShadow = $this->Shadow;
            $this->Shadow = \false;
            if ($RFade != -1 && $GFade != -1 && $BFade != -1) {
                $GradientOptions = ["StartR" => $RFade, "StartG" => $GFade, "StartB" => $BFade, "EndR" => $R, "EndG" => $G, "EndB" => $B];
                $this->drawGradientArea($X + 1, $Y - 1, $X + $Width - 1, $Y - $InnerHeight, DIRECTION_VERTICAL, $GradientOptions);
                if ($Surrounding) {
                    $this->drawRectangle($X + 1, $Y - 1, $X + $Width - 1, $Y - $InnerHeight, ["R" => 255, "G" => 255, "B" => 255, "Alpha" => $Surrounding]);
                }
            } else {
                $this->drawFilledRectangle($X + 1, $Y - 1, $X + $Width - 1, $Y - $InnerHeight, ["R" => $R, "G" => $G, "B" => $B, "BorderR" => $BorderR, "BorderG" => $BorderG, "BorderB" => $BorderB]);
            }
            $this->Shadow = $RestoreShadow;
            if ($ShowLabel && $LabelPos == LABEL_POS_BOTTOM) {
                $this->drawText($X + $Width / 2, $Y + $Margin, $Percent . "%", ["Align" => TEXT_ALIGN_TOPMIDDLE]);
            }
            if ($ShowLabel && $LabelPos == LABEL_POS_TOP) {
                $this->drawText($X + $Width / 2, $Y - $Height - $Margin, $Percent . "%", ["Align" => TEXT_ALIGN_BOTTOMMIDDLE]);
            }
            if ($ShowLabel && $LabelPos == LABEL_POS_INSIDE) {
                $this->drawText($X + $Width / 2, $Y - $InnerHeight - $Margin, $Percent . "%", ["Align" => TEXT_ALIGN_MIDDLELEFT, "Angle" => 90]);
            }
            if ($ShowLabel && $LabelPos == LABEL_POS_CENTER) {
                $this->drawText($X + $Width / 2, $Y - $Height / 2, $Percent . "%", ["Align" => TEXT_ALIGN_MIDDLEMIDDLE, "Angle" => 90]);
            }
        } else {
            if ($Percent == 100) {
                $InnerWidth = $Width - 1;
            } else {
                $InnerWidth = ($Width - 2) / 100 * $Percent;
            }
            $this->drawFilledRectangle($X, $Y, $X + $Width, $Y + $Height, ["R" => $BoxBackR, "G" => $BoxBackG, "B" => $BoxBackB, "BorderR" => $BoxBorderR, "BorderG" => $BoxBorderG, "BorderB" => $BoxBorderB, "NoAngle" => $NoAngle]);
            $RestoreShadow = $this->Shadow;
            $this->Shadow = \false;
            if ($RFade != -1 && $GFade != -1 && $BFade != -1) {
                $GradientOptions = ["StartR" => $R, "StartG" => $G, "StartB" => $B, "EndR" => $RFade, "EndG" => $GFade, "EndB" => $BFade];
                $this->drawGradientArea($X + 1, $Y + 1, $X + $InnerWidth, $Y + $Height - 1, DIRECTION_HORIZONTAL, $GradientOptions);
                if ($Surrounding) {
                    $this->drawRectangle($X + 1, $Y + 1, $X + $InnerWidth, $Y + $Height - 1, ["R" => 255, "G" => 255, "B" => 255, "Alpha" => $Surrounding]);
                }
            } else {
                $this->drawFilledRectangle($X + 1, $Y + 1, $X + $InnerWidth, $Y + $Height - 1, ["R" => $R, "G" => $G, "B" => $B, "BorderR" => $BorderR, "BorderG" => $BorderG, "BorderB" => $BorderB]);
            }
            $this->Shadow = $RestoreShadow;
            if ($ShowLabel && $LabelPos == LABEL_POS_LEFT) {
                $this->drawText($X - $Margin, $Y + $Height / 2, $Percent . "%", ["Align" => TEXT_ALIGN_MIDDLERIGHT]);
            }
            if ($ShowLabel && $LabelPos == LABEL_POS_RIGHT) {
                $this->drawText($X + $Width + $Margin, $Y + $Height / 2, $Percent . "%", ["Align" => TEXT_ALIGN_MIDDLELEFT]);
            }
            if ($ShowLabel && $LabelPos == LABEL_POS_CENTER) {
                $this->drawText($X + $Width / 2, $Y + $Height / 2, $Percent . "%", ["Align" => TEXT_ALIGN_MIDDLEMIDDLE]);
            }
            if ($ShowLabel && $LabelPos == LABEL_POS_INSIDE) {
                $this->drawText($X + $InnerWidth + $Margin, $Y + $Height / 2, $Percent . "%", ["Align" => TEXT_ALIGN_MIDDLELEFT]);
            }
        }
    }
    /**
     * Draw the legend of the active series
     * @param int $X
     * @param int $Y
     * @param array $Format
     */
    public function drawLegend($X, $Y, array $Format = [])
    {
        $Family = isset($Format["Family"]) ? $Format["Family"] : LEGEND_FAMILY_BOX;
        $FontName = isset($Format["FontName"]) ? $this->loadFont($Format["FontName"], 'fonts') : $this->FontName;
        $FontSize = isset($Format["FontSize"]) ? $Format["FontSize"] : $this->FontSize;
        $FontR = isset($Format["FontR"]) ? $Format["FontR"] : $this->FontColorR;
        $FontG = isset($Format["FontG"]) ? $Format["FontG"] : $this->FontColorG;
        $FontB = isset($Format["FontB"]) ? $Format["FontB"] : $this->FontColorB;
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
        $Data = $this->DataSet->getData();
        foreach ($Data["Series"] as $SerieName => $Serie) {
            if ($Serie["isDrawable"] == \true && $SerieName != $Data["Abscissa"] && isset($Serie["Picture"])) {
                list($PicWidth, $PicHeight) = $this->getPicInfo($Serie["Picture"]);
                if ($IconAreaWidth < $PicWidth) {
                    $IconAreaWidth = $PicWidth;
                }
                if ($IconAreaHeight < $PicHeight) {
                    $IconAreaHeight = $PicHeight;
                }
            }
        }
        $YStep = max($this->FontSize, $IconAreaHeight) + 5;
        $XStep = $IconAreaWidth + 5;
        $XStep = $XSpacing;
        $Boundaries = [];
        $Boundaries["L"] = $X;
        $Boundaries["T"] = $Y;
        $Boundaries["R"] = 0;
        $Boundaries["B"] = 0;
        $vY = $Y;
        $vX = $X;
        foreach ($Data["Series"] as $SerieName => $Serie) {
            if ($Serie["isDrawable"] == \true && $SerieName != $Data["Abscissa"]) {
                if ($Mode == LEGEND_VERTICAL) {
                    $BoxArray = $this->getTextBox($vX + $IconAreaWidth + 4, $vY + $IconAreaHeight / 2, $FontName, $FontSize, 0, $Serie["Description"]);
                    if ($Boundaries["T"] > $BoxArray[2]["Y"] + $IconAreaHeight / 2) {
                        $Boundaries["T"] = $BoxArray[2]["Y"] + $IconAreaHeight / 2;
                    }
                    if ($Boundaries["R"] < $BoxArray[1]["X"] + 2) {
                        $Boundaries["R"] = $BoxArray[1]["X"] + 2;
                    }
                    if ($Boundaries["B"] < $BoxArray[1]["Y"] + 2 + $IconAreaHeight / 2) {
                        $Boundaries["B"] = $BoxArray[1]["Y"] + 2 + $IconAreaHeight / 2;
                    }
                    $Lines = preg_split("/\n/", $Serie["Description"]);
                    $vY = $vY + max($this->FontSize * count($Lines), $IconAreaHeight) + 5;
                } elseif ($Mode == LEGEND_HORIZONTAL) {
                    $Lines = preg_split("/\n/", $Serie["Description"]);
                    $Width = [];
                    foreach ($Lines as $Key => $Value) {
                        $BoxArray = $this->getTextBox($vX + $IconAreaWidth + 6, $Y + $IconAreaHeight / 2 + ($this->FontSize + 3) * $Key, $FontName, $FontSize, 0, $Value);
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
            $this->drawRoundedFilledRectangle($Boundaries["L"] - $Margin, $Boundaries["T"] - $Margin, $Boundaries["R"] + $Margin, $Boundaries["B"] + $Margin, $Margin, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "BorderR" => $BorderR, "BorderG" => $BorderG, "BorderB" => $BorderB]);
        } elseif ($Style == LEGEND_BOX) {
            $this->drawFilledRectangle($Boundaries["L"] - $Margin, $Boundaries["T"] - $Margin, $Boundaries["R"] + $Margin, $Boundaries["B"] + $Margin, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "BorderR" => $BorderR, "BorderG" => $BorderG, "BorderB" => $BorderB]);
        }
        $RestoreShadow = $this->Shadow;
        $this->Shadow = \false;
        foreach ($Data["Series"] as $SerieName => $Serie) {
            if ($Serie["isDrawable"] == \true && $SerieName != $Data["Abscissa"]) {
                $R = $Serie["Color"]["R"];
                $G = $Serie["Color"]["G"];
                $B = $Serie["Color"]["B"];
                $Ticks = $Serie["Ticks"];
                $Weight = $Serie["Weight"];
                if (isset($Serie["Picture"])) {
                    $Picture = $Serie["Picture"];
                    list($PicWidth, $PicHeight) = $this->getPicInfo($Picture);
                    $PicX = $X + $IconAreaWidth / 2;
                    $PicY = $Y + $IconAreaHeight / 2;
                    $this->drawFromPNG($PicX - $PicWidth / 2, $PicY - $PicHeight / 2, $Picture);
                } else {
                    if ($Family == LEGEND_FAMILY_BOX) {
                        $XOffset = 0;
                        if ($BoxWidth != $IconAreaWidth) {
                            $XOffset = floor(($IconAreaWidth - $BoxWidth) / 2);
                        }
                        $YOffset = 0;
                        if ($BoxHeight != $IconAreaHeight) {
                            $YOffset = floor(($IconAreaHeight - $BoxHeight) / 2);
                        }
                        $this->drawFilledRectangle($X + 1 + $XOffset, $Y + 1 + $YOffset, $X + $BoxWidth + $XOffset + 1, $Y + $BoxHeight + 1 + $YOffset, ["R" => 0, "G" => 0, "B" => 0, "Alpha" => 20]);
                        $this->drawFilledRectangle($X + $XOffset, $Y + $YOffset, $X + $BoxWidth + $XOffset, $Y + $BoxHeight + $YOffset, ["R" => $R, "G" => $G, "B" => $B, "Surrounding" => 20]);
                    } elseif ($Family == LEGEND_FAMILY_CIRCLE) {
                        $this->drawFilledCircle($X + 1 + $IconAreaWidth / 2, $Y + 1 + $IconAreaHeight / 2, min($IconAreaHeight / 2, $IconAreaWidth / 2), ["R" => 0, "G" => 0, "B" => 0, "Alpha" => 20]);
                        $this->drawFilledCircle($X + $IconAreaWidth / 2, $Y + $IconAreaHeight / 2, min($IconAreaHeight / 2, $IconAreaWidth / 2), ["R" => $R, "G" => $G, "B" => $B, "Surrounding" => 20]);
                    } elseif ($Family == LEGEND_FAMILY_LINE) {
                        $this->drawLine($X + 1, $Y + 1 + $IconAreaHeight / 2, $X + 1 + $IconAreaWidth, $Y + 1 + $IconAreaHeight / 2, ["R" => 0, "G" => 0, "B" => 0, "Alpha" => 20, "Ticks" => $Ticks, "Weight" => $Weight]);
                        $this->drawLine($X, $Y + $IconAreaHeight / 2, $X + $IconAreaWidth, $Y + $IconAreaHeight / 2, ["R" => $R, "G" => $G, "B" => $B, "Ticks" => $Ticks, "Weight" => $Weight]);
                    }
                }
                if ($Mode == LEGEND_VERTICAL) {
                    $Lines = preg_split("/\n/", $Serie["Description"]);
                    foreach ($Lines as $Key => $Value) {
                        $this->drawText($X + $IconAreaWidth + 4, $Y + $IconAreaHeight / 2 + ($this->FontSize + 3) * $Key, $Value, ["R" => $FontR, "G" => $FontG, "B" => $FontB, "Align" => TEXT_ALIGN_MIDDLELEFT, "FontSize" => $FontSize, "FontName" => $FontName]);
                    }
                    $Y = $Y + max($this->FontSize * count($Lines), $IconAreaHeight) + 5;
                } elseif ($Mode == LEGEND_HORIZONTAL) {
                    $Lines = preg_split("/\n/", $Serie["Description"]);
                    $Width = [];
                    foreach ($Lines as $Key => $Value) {
                        $BoxArray = $this->drawText($X + $IconAreaWidth + 4, $Y + $IconAreaHeight / 2 + ($this->FontSize + 3) * $Key, $Value, ["R" => $FontR, "G" => $FontG, "B" => $FontB, "Align" => TEXT_ALIGN_MIDDLELEFT, "FontSize" => $FontSize, "FontName" => $FontName]);
                        $Width[] = $BoxArray[1]["X"];
                    }
                    $X = max($Width) + 2 + $XStep;
                }
            }
        }
        $this->Shadow = $RestoreShadow;
    }
    /**
     * @param array $Format
     * @throws Exception
     */
    public function drawScale(array $Format = [])
    {
        $FloatingOffset = 0;
        $Pos = isset($Format["Pos"]) ? $Format["Pos"] : SCALE_POS_LEFTRIGHT;
        $Floating = isset($Format["Floating"]) ? $Format["Floating"] : \false;
        $Mode = isset($Format["Mode"]) ? $Format["Mode"] : SCALE_MODE_FLOATING;
        $RemoveXAxis = isset($Format["RemoveXAxis"]) ? $Format["RemoveXAxis"] : \false;
        $RemoveYAxis = isset($Format["RemoveYAxis"]) ? $Format["RemoveYAxis"] : \false;
        $RemoveYAxiValues = isset($Format["RemoveYAxisValues"]) ? $Format["RemoveYAxisValues"] : \false;
        $MinDivHeight = isset($Format["MinDivHeight"]) ? $Format["MinDivHeight"] : 20;
        $Factors = isset($Format["Factors"]) ? $Format["Factors"] : [1, 2, 5];
        $ManualScale = isset($Format["ManualScale"]) ? $Format["ManualScale"] : ["0" => ["Min" => -100, "Max" => 100]];
        $XMargin = isset($Format["XMargin"]) ? $Format["XMargin"] : AUTO;
        $YMargin = isset($Format["YMargin"]) ? $Format["YMargin"] : 0;
        $ScaleSpacing = isset($Format["ScaleSpacing"]) ? $Format["ScaleSpacing"] : 15;
        $InnerTickWidth = isset($Format["InnerTickWidth"]) ? $Format["InnerTickWidth"] : 2;
        $OuterTickWidth = isset($Format["OuterTickWidth"]) ? $Format["OuterTickWidth"] : 2;
        $DrawXLines = isset($Format["DrawXLines"]) ? $Format["DrawXLines"] : \true;
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
        $AutoAxisLabels = isset($Format["AutoAxisLabels"]) ? $Format["AutoAxisLabels"] : \true;
        $XReleasePercent = isset($Format["XReleasePercent"]) ? $Format["XReleasePercent"] : 1;
        $DrawArrows = isset($Format["DrawArrows"]) ? $Format["DrawArrows"] : \false;
        $ArrowSize = isset($Format["ArrowSize"]) ? $Format["ArrowSize"] : 8;
        $CycleBackground = isset($Format["CycleBackground"]) ? $Format["CycleBackground"] : \false;
        $BackgroundR1 = isset($Format["BackgroundR1"]) ? $Format["BackgroundR1"] : 255;
        $BackgroundG1 = isset($Format["BackgroundG1"]) ? $Format["BackgroundG1"] : 255;
        $BackgroundB1 = isset($Format["BackgroundB1"]) ? $Format["BackgroundB1"] : 255;
        $BackgroundAlpha1 = isset($Format["BackgroundAlpha1"]) ? $Format["BackgroundAlpha1"] : 20;
        $BackgroundR2 = isset($Format["BackgroundR2"]) ? $Format["BackgroundR2"] : 230;
        $BackgroundG2 = isset($Format["BackgroundG2"]) ? $Format["BackgroundG2"] : 230;
        $BackgroundB2 = isset($Format["BackgroundB2"]) ? $Format["BackgroundB2"] : 230;
        $BackgroundAlpha2 = isset($Format["BackgroundAlpha2"]) ? $Format["BackgroundAlpha2"] : 20;
        $LabelingMethod = isset($Format["LabelingMethod"]) ? $Format["LabelingMethod"] : LABELING_ALL;
        $LabelSkip = isset($Format["LabelSkip"]) ? $Format["LabelSkip"] : 0;
        $LabelRotation = isset($Format["LabelRotation"]) ? $Format["LabelRotation"] : 0;
        $RemoveSkippedAxis = isset($Format["RemoveSkippedAxis"]) ? $Format["RemoveSkippedAxis"] : \false;
        $SkippedAxisTicks = isset($Format["SkippedAxisTicks"]) ? $Format["SkippedAxisTicks"] : $GridTicks + 2;
        $SkippedAxisR = isset($Format["SkippedAxisR"]) ? $Format["SkippedAxisR"] : $GridR;
        $SkippedAxisG = isset($Format["SkippedAxisG"]) ? $Format["SkippedAxisG"] : $GridG;
        $SkippedAxisB = isset($Format["SkippedAxisB"]) ? $Format["SkippedAxisB"] : $GridB;
        $SkippedAxisAlpha = isset($Format["SkippedAxisAlpha"]) ? $Format["SkippedAxisAlpha"] : $GridAlpha - 30;
        $SkippedTickR = isset($Format["SkippedTickR"]) ? $Format["SkippedTickR"] : $TickRo;
        $SkippedTickG = isset($Format["SkippedTickG"]) ? $Format["SkippedTickG"] : $TickGo;
        $SkippedTickB = isset($Format["SkippedTicksB"]) ? $Format["SkippedTickB"] : $TickBo;
        $SkippedTickAlpha = isset($Format["SkippedTickAlpha"]) ? $Format["SkippedTickAlpha"] : $TickAlpha - 80;
        $SkippedInnerTickWidth = isset($Format["SkippedInnerTickWidth"]) ? $Format["SkippedInnerTickWidth"] : 0;
        $SkippedOuterTickWidth = isset($Format["SkippedOuterTickWidth"]) ? $Format["SkippedOuterTickWidth"] : 2;
        /* Floating scale require X & Y margins to be set manually */
        if ($Floating && ($XMargin == AUTO || $YMargin == 0)) {
            $Floating = \false;
        }
        /* Skip a NOTICE event in case of an empty array */
        if ($DrawYLines == NONE || $DrawYLines == \false) {
            $DrawYLines = ["zarma" => "31"];
        }
        /* Define the color for the skipped elements */
        $SkippedAxisColor = ["R" => $SkippedAxisR, "G" => $SkippedAxisG, "B" => $SkippedAxisB, "Alpha" => $SkippedAxisAlpha, "Ticks" => $SkippedAxisTicks];
        $SkippedTickColor = ["R" => $SkippedTickR, "G" => $SkippedTickG, "B" => $SkippedTickB, "Alpha" => $SkippedTickAlpha];
        $Data = $this->DataSet->getData();
        $Abscissa = null;
        if (isset($Data["Abscissa"])) {
            $Abscissa = $Data["Abscissa"];
        }
        /* Unset the abscissa axis, needed if we display multiple charts on the same picture */
        if ($Abscissa != null) {
            foreach ($Data["Axis"] as $AxisID => $Parameters) {
                if ($Parameters["Identity"] == AXIS_X) {
                    unset($Data["Axis"][$AxisID]);
                }
            }
        }
        /* Build the scale settings */
        $GotXAxis = \false;
        foreach ($Data["Axis"] as $AxisID => $AxisParameter) {
            if ($AxisParameter["Identity"] == AXIS_X) {
                $GotXAxis = \true;
            }
            if ($Pos == SCALE_POS_LEFTRIGHT && $AxisParameter["Identity"] == AXIS_Y) {
                $Height = $this->GraphAreaY2 - $this->GraphAreaY1 - $YMargin * 2;
            } elseif ($Pos == SCALE_POS_LEFTRIGHT && $AxisParameter["Identity"] == AXIS_X) {
                $Height = $this->GraphAreaX2 - $this->GraphAreaX1;
            } elseif ($Pos == SCALE_POS_TOPBOTTOM && $AxisParameter["Identity"] == AXIS_Y) {
                $Height = $this->GraphAreaX2 - $this->GraphAreaX1 - $YMargin * 2;
            } else {
                $Height = $this->GraphAreaY2 - $this->GraphAreaY1;
            }
            $AxisMin = ABSOLUTE_MAX;
            $AxisMax = OUT_OF_SIGHT;
            if ($Mode == SCALE_MODE_FLOATING || $Mode == SCALE_MODE_START0) {
                foreach ($Data["Series"] as $SerieID => $SerieParameter) {
                    if ($SerieParameter["Axis"] == $AxisID && $Data["Series"][$SerieID]["isDrawable"] && $Data["Abscissa"] != $SerieID) {
                        $AxisMax = max($AxisMax, $Data["Series"][$SerieID]["Max"]);
                        $AxisMin = min($AxisMin, $Data["Series"][$SerieID]["Min"]);
                    }
                }
                $AutoMargin = ($AxisMax - $AxisMin) / 100 * $XReleasePercent;
                $Data["Axis"][$AxisID]["Min"] = $AxisMin - $AutoMargin;
                $Data["Axis"][$AxisID]["Max"] = $AxisMax + $AutoMargin;
                if ($Mode == SCALE_MODE_START0) {
                    $Data["Axis"][$AxisID]["Min"] = 0;
                }
            } elseif ($Mode == SCALE_MODE_MANUAL) {
                if (isset($ManualScale[$AxisID]["Min"]) && isset($ManualScale[$AxisID]["Max"])) {
                    $Data["Axis"][$AxisID]["Min"] = $ManualScale[$AxisID]["Min"];
                    $Data["Axis"][$AxisID]["Max"] = $ManualScale[$AxisID]["Max"];
                } else {
                    throw new Exception("Manual scale boundaries not set.");
                }
            } elseif ($Mode == SCALE_MODE_ADDALL || $Mode == SCALE_MODE_ADDALL_START0) {
                $Series = [];
                foreach ($Data["Series"] as $SerieID => $SerieParameter) {
                    if ($SerieParameter["Axis"] == $AxisID && $SerieParameter["isDrawable"] && $Data["Abscissa"] != $SerieID) {
                        $Series[$SerieID] = count($Data["Series"][$SerieID]["Data"]);
                    }
                }
                for ($ID = 0; $ID <= max($Series) - 1; $ID++) {
                    $PointMin = 0;
                    $PointMax = 0;
                    foreach ($Series as $SerieID => $ValuesCount) {
                        if (isset($Data["Series"][$SerieID]["Data"][$ID]) && $Data["Series"][$SerieID]["Data"][$ID] != null) {
                            $Value = $Data["Series"][$SerieID]["Data"][$ID];
                            if ($Value > 0) {
                                $PointMax = $PointMax + $Value;
                            } else {
                                $PointMin = $PointMin + $Value;
                            }
                        }
                    }
                    $AxisMax = max($AxisMax, $PointMax);
                    $AxisMin = min($AxisMin, $PointMin);
                }
                $AutoMargin = ($AxisMax - $AxisMin) / 100 * $XReleasePercent;
                $Data["Axis"][$AxisID]["Min"] = $AxisMin - $AutoMargin;
                $Data["Axis"][$AxisID]["Max"] = $AxisMax + $AutoMargin;
            }
            $MaxDivs = floor($Height / $MinDivHeight);
            if ($Mode == SCALE_MODE_ADDALL_START0) {
                $Data["Axis"][$AxisID]["Min"] = 0;
            }
            $Scale = $this->computeScale($Data["Axis"][$AxisID]["Min"], $Data["Axis"][$AxisID]["Max"], $MaxDivs, $Factors, $AxisID);
            $Data["Axis"][$AxisID]["Margin"] = $AxisParameter["Identity"] == AXIS_X ? $XMargin : $YMargin;
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
        /* Still no X axis */
        if ($GotXAxis == \false) {
            if ($Abscissa != null) {
                $Points = count($Data["Series"][$Abscissa]["Data"]);
                $AxisName = null;
                if ($AutoAxisLabels) {
                    $AxisName = isset($Data["Series"][$Abscissa]["Description"]) ? $Data["Series"][$Abscissa]["Description"] : null;
                }
            } else {
                $Points = 0;
                $AxisName = isset($Data["XAxisName"]) ? $Data["XAxisName"] : null;
                foreach ($Data["Series"] as $SerieID => $SerieParameter) {
                    if ($SerieParameter["isDrawable"]) {
                        $Points = max($Points, count($SerieParameter["Data"]));
                    }
                }
            }
            $AxisID = count($Data["Axis"]);
            $Data["Axis"][$AxisID]["Identity"] = AXIS_X;
            if ($Pos == SCALE_POS_LEFTRIGHT) {
                $Data["Axis"][$AxisID]["Position"] = AXIS_POSITION_BOTTOM;
            } else {
                $Data["Axis"][$AxisID]["Position"] = AXIS_POSITION_LEFT;
            }
            if (isset($Data["AbscissaName"])) {
                $Data["Axis"][$AxisID]["Name"] = $Data["AbscissaName"];
            }
            if ($XMargin == AUTO) {
                if ($Pos == SCALE_POS_LEFTRIGHT) {
                    $Height = $this->GraphAreaX2 - $this->GraphAreaX1;
                } else {
                    $Height = $this->GraphAreaY2 - $this->GraphAreaY1;
                }
                if ($Points == 0 || $Points == 1) {
                    $Data["Axis"][$AxisID]["Margin"] = $Height / 2;
                } else {
                    $Data["Axis"][$AxisID]["Margin"] = $Height / $Points / 2;
                }
            } else {
                $Data["Axis"][$AxisID]["Margin"] = $XMargin;
            }
            $Data["Axis"][$AxisID]["Rows"] = $Points - 1;
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
        /* Do we need to reverse the abscissa position? */
        if ($Pos != SCALE_POS_LEFTRIGHT) {
            $Data["AbsicssaPosition"] = AXIS_POSITION_RIGHT;
            if ($Data["AbsicssaPosition"] == AXIS_POSITION_BOTTOM) {
                $Data["AbsicssaPosition"] = AXIS_POSITION_LEFT;
            }
        }
        $Data["Axis"][$AxisID]["Position"] = $Data["AbsicssaPosition"];
        $this->DataSet->saveOrientation($Pos);
        $this->DataSet->saveAxisConfig($Data["Axis"]);
        $this->DataSet->saveYMargin($YMargin);
        $FontColorRo = $this->FontColorR;
        $FontColorGo = $this->FontColorG;
        $FontColorBo = $this->FontColorB;
        $AxisPos["L"] = $this->GraphAreaX1;
        $AxisPos["R"] = $this->GraphAreaX2;
        $AxisPos["T"] = $this->GraphAreaY1;
        $AxisPos["B"] = $this->GraphAreaY2;
        foreach ($Data["Axis"] as $AxisID => $Parameters) {
            if (isset($Parameters["Color"])) {
                $AxisR = $Parameters["Color"]["R"];
                $AxisG = $Parameters["Color"]["G"];
                $AxisB = $Parameters["Color"]["B"];
                $TickR = $Parameters["Color"]["R"];
                $TickG = $Parameters["Color"]["G"];
                $TickB = $Parameters["Color"]["B"];
                $this->setFontProperties(["R" => $Parameters["Color"]["R"], "G" => $Parameters["Color"]["G"], "B" => $Parameters["Color"]["B"]]);
            } else {
                $AxisR = $AxisRo;
                $AxisG = $AxisGo;
                $AxisB = $AxisBo;
                $TickR = $TickRo;
                $TickG = $TickGo;
                $TickB = $TickBo;
                $this->setFontProperties(["R" => $FontColorRo, "G" => $FontColorGo, "B" => $FontColorBo]);
            }
            $LastValue = "w00t";
            $ID = 1;
            if ($Parameters["Identity"] == AXIS_X) {
                if ($Pos == SCALE_POS_LEFTRIGHT) {
                    if ($Parameters["Position"] == AXIS_POSITION_BOTTOM) {
                        if ($LabelRotation == 0) {
                            $LabelAlign = TEXT_ALIGN_TOPMIDDLE;
                            $YLabelOffset = 2;
                        }
                        if ($LabelRotation > 0 && $LabelRotation < 190) {
                            $LabelAlign = TEXT_ALIGN_MIDDLERIGHT;
                            $YLabelOffset = 5;
                        }
                        if ($LabelRotation == 180) {
                            $LabelAlign = TEXT_ALIGN_BOTTOMMIDDLE;
                            $YLabelOffset = 5;
                        }
                        if ($LabelRotation > 180 && $LabelRotation < 360) {
                            $LabelAlign = TEXT_ALIGN_MIDDLELEFT;
                            $YLabelOffset = 2;
                        }
                        if (!$RemoveXAxis) {
                            if ($Floating) {
                                $FloatingOffset = $YMargin;
                                $this->drawLine($this->GraphAreaX1 + $Parameters["Margin"], $AxisPos["B"], $this->GraphAreaX2 - $Parameters["Margin"], $AxisPos["B"], ["R" => $AxisR, "G" => $AxisG, "B" => $AxisB, "Alpha" => $AxisAlpha]);
                            } else {
                                $FloatingOffset = 0;
                                $this->drawLine($this->GraphAreaX1, $AxisPos["B"], $this->GraphAreaX2, $AxisPos["B"], ["R" => $AxisR, "G" => $AxisG, "B" => $AxisB, "Alpha" => $AxisAlpha]);
                            }
                            if ($DrawArrows) {
                                $this->drawArrow($this->GraphAreaX2 - $Parameters["Margin"], $AxisPos["B"], $this->GraphAreaX2 + $ArrowSize * 2, $AxisPos["B"], ["FillR" => $AxisR, "FillG" => $AxisG, "FillB" => $AxisB, "Size" => $ArrowSize]);
                            }
                        }
                        $Width = $this->GraphAreaX2 - $this->GraphAreaX1 - $Parameters["Margin"] * 2;
                        if ($Parameters["Rows"] == 0) {
                            $Step = $Width;
                        } else {
                            $Step = $Width / $Parameters["Rows"];
                        }
                        $MaxBottom = $AxisPos["B"];
                        for ($i = 0; $i <= $Parameters["Rows"]; $i++) {
                            $XPos = $this->GraphAreaX1 + $Parameters["Margin"] + $Step * $i;
                            $YPos = $AxisPos["B"];
                            if ($Abscissa != null) {
                                $Value = "";
                                if (isset($Data["Series"][$Abscissa]["Data"][$i])) {
                                    $Value = $this->scaleFormat($Data["Series"][$Abscissa]["Data"][$i], $Data["XAxisDisplay"], $Data["XAxisFormat"], $Data["XAxisUnit"]);
                                }
                            } else {
                                $Value = $i;
                                if (isset($Parameters["ScaleMin"]) && isset($Parameters["RowHeight"])) {
                                    $Value = $this->scaleFormat($Parameters["ScaleMin"] + $Parameters["RowHeight"] * $i, $Data["XAxisDisplay"], $Data["XAxisFormat"], $Data["XAxisUnit"]);
                                }
                            }
                            $ID++;
                            $Skipped = \true;
                            if ($this->isValidLabel($Value, $LastValue, $LabelingMethod, $ID, $LabelSkip) && !$RemoveXAxis) {
                                $Bounds = $this->drawText($XPos, $YPos + $OuterTickWidth + $YLabelOffset, $Value, ["Angle" => $LabelRotation, "Align" => $LabelAlign]);
                                $TxtBottom = $YPos + $OuterTickWidth + 2 + ($Bounds[0]["Y"] - $Bounds[2]["Y"]);
                                $MaxBottom = max($MaxBottom, $TxtBottom);
                                $LastValue = $Value;
                                $Skipped = \false;
                            }
                            if ($RemoveXAxis) {
                                $Skipped = \false;
                            }
                            if ($Skipped) {
                                if ($DrawXLines && !$RemoveSkippedAxis) {
                                    $this->drawLine($XPos, $this->GraphAreaY1 + $FloatingOffset, $XPos, $this->GraphAreaY2 - $FloatingOffset, $SkippedAxisColor);
                                }
                                if (($SkippedInnerTickWidth != 0 || $SkippedOuterTickWidth != 0) && !$RemoveXAxis && !$RemoveSkippedAxis) {
                                    $this->drawLine($XPos, $YPos - $SkippedInnerTickWidth, $XPos, $YPos + $SkippedOuterTickWidth, $SkippedTickColor);
                                }
                            } else {
                                if ($DrawXLines && ($XPos != $this->GraphAreaX1 && $XPos != $this->GraphAreaX2)) {
                                    $this->drawLine($XPos, $this->GraphAreaY1 + $FloatingOffset, $XPos, $this->GraphAreaY2 - $FloatingOffset, ["R" => $GridR, "G" => $GridG, "B" => $GridB, "Alpha" => $GridAlpha, "Ticks" => $GridTicks]);
                                }
                                if (($InnerTickWidth != 0 || $OuterTickWidth != 0) && !$RemoveXAxis) {
                                    $this->drawLine($XPos, $YPos - $InnerTickWidth, $XPos, $YPos + $OuterTickWidth, ["R" => $TickR, "G" => $TickG, "B" => $TickB, "Alpha" => $TickAlpha]);
                                }
                            }
                        }
                        if (isset($Parameters["Name"]) && !$RemoveXAxis) {
                            $YPos = $MaxBottom + 2;
                            $XPos = $this->GraphAreaX1 + ($this->GraphAreaX2 - $this->GraphAreaX1) / 2;
                            $Bounds = $this->drawText($XPos, $YPos, $Parameters["Name"], ["Align" => TEXT_ALIGN_TOPMIDDLE]);
                            $MaxBottom = $Bounds[0]["Y"];
                            $this->DataSet->Data["GraphArea"]["Y2"] = $MaxBottom + $this->FontSize;
                        }
                        $AxisPos["B"] = $MaxBottom + $ScaleSpacing;
                    } elseif ($Parameters["Position"] == AXIS_POSITION_TOP) {
                        if ($LabelRotation == 0) {
                            $LabelAlign = TEXT_ALIGN_BOTTOMMIDDLE;
                            $YLabelOffset = 2;
                        }
                        if ($LabelRotation > 0 && $LabelRotation < 190) {
                            $LabelAlign = TEXT_ALIGN_MIDDLELEFT;
                            $YLabelOffset = 2;
                        }
                        if ($LabelRotation == 180) {
                            $LabelAlign = TEXT_ALIGN_TOPMIDDLE;
                            $YLabelOffset = 5;
                        }
                        if ($LabelRotation > 180 && $LabelRotation < 360) {
                            $LabelAlign = TEXT_ALIGN_MIDDLERIGHT;
                            $YLabelOffset = 5;
                        }
                        if (!$RemoveXAxis) {
                            if ($Floating) {
                                $FloatingOffset = $YMargin;
                                $this->drawLine($this->GraphAreaX1 + $Parameters["Margin"], $AxisPos["T"], $this->GraphAreaX2 - $Parameters["Margin"], $AxisPos["T"], ["R" => $AxisR, "G" => $AxisG, "B" => $AxisB, "Alpha" => $AxisAlpha]);
                            } else {
                                $FloatingOffset = 0;
                                $this->drawLine($this->GraphAreaX1, $AxisPos["T"], $this->GraphAreaX2, $AxisPos["T"], ["R" => $AxisR, "G" => $AxisG, "B" => $AxisB, "Alpha" => $AxisAlpha]);
                            }
                            if ($DrawArrows) {
                                $this->drawArrow($this->GraphAreaX2 - $Parameters["Margin"], $AxisPos["T"], $this->GraphAreaX2 + $ArrowSize * 2, $AxisPos["T"], ["FillR" => $AxisR, "FillG" => $AxisG, "FillB" => $AxisB, "Size" => $ArrowSize]);
                            }
                        }
                        $Width = $this->GraphAreaX2 - $this->GraphAreaX1 - $Parameters["Margin"] * 2;
                        if ($Parameters["Rows"] == 0) {
                            $Step = $Width;
                        } else {
                            $Step = $Width / $Parameters["Rows"];
                        }
                        $MinTop = $AxisPos["T"];
                        for ($i = 0; $i <= $Parameters["Rows"]; $i++) {
                            $XPos = $this->GraphAreaX1 + $Parameters["Margin"] + $Step * $i;
                            $YPos = $AxisPos["T"];
                            if ($Abscissa != null) {
                                $Value = "";
                                if (isset($Data["Series"][$Abscissa]["Data"][$i])) {
                                    $Value = $this->scaleFormat($Data["Series"][$Abscissa]["Data"][$i], $Data["XAxisDisplay"], $Data["XAxisFormat"], $Data["XAxisUnit"]);
                                }
                            } else {
                                $Value = $i;
                                if (isset($Parameters["ScaleMin"]) && isset($Parameters["RowHeight"])) {
                                    $Value = $this->scaleFormat($Parameters["ScaleMin"] + $Parameters["RowHeight"] * $i, $Data["XAxisDisplay"], $Data["XAxisFormat"], $Data["XAxisUnit"]);
                                }
                            }
                            $ID++;
                            $Skipped = \true;
                            if ($this->isValidLabel($Value, $LastValue, $LabelingMethod, $ID, $LabelSkip) && !$RemoveXAxis) {
                                $Bounds = $this->drawText($XPos, $YPos - $OuterTickWidth - $YLabelOffset, $Value, ["Angle" => $LabelRotation, "Align" => $LabelAlign]);
                                $TxtBox = $YPos - $OuterTickWidth - 2 - ($Bounds[0]["Y"] - $Bounds[2]["Y"]);
                                $MinTop = min($MinTop, $TxtBox);
                                $LastValue = $Value;
                                $Skipped = \false;
                            }
                            if ($RemoveXAxis) {
                                $Skipped = \false;
                            }
                            if ($Skipped) {
                                if ($DrawXLines && !$RemoveSkippedAxis) {
                                    $this->drawLine($XPos, $this->GraphAreaY1 + $FloatingOffset, $XPos, $this->GraphAreaY2 - $FloatingOffset, $SkippedAxisColor);
                                }
                                if (($SkippedInnerTickWidth != 0 || $SkippedOuterTickWidth != 0) && !$RemoveXAxis && !$RemoveSkippedAxis) {
                                    $this->drawLine($XPos, $YPos + $SkippedInnerTickWidth, $XPos, $YPos - $SkippedOuterTickWidth, $SkippedTickColor);
                                }
                            } else {
                                if ($DrawXLines) {
                                    $this->drawLine($XPos, $this->GraphAreaY1 + $FloatingOffset, $XPos, $this->GraphAreaY2 - $FloatingOffset, ["R" => $GridR, "G" => $GridG, "B" => $GridB, "Alpha" => $GridAlpha, "Ticks" => $GridTicks]);
                                }
                                if (($InnerTickWidth != 0 || $OuterTickWidth != 0) && !$RemoveXAxis) {
                                    $this->drawLine($XPos, $YPos + $InnerTickWidth, $XPos, $YPos - $OuterTickWidth, ["R" => $TickR, "G" => $TickG, "B" => $TickB, "Alpha" => $TickAlpha]);
                                }
                            }
                        }
                        if (isset($Parameters["Name"]) && !$RemoveXAxis) {
                            $YPos = $MinTop - 2;
                            $XPos = $this->GraphAreaX1 + ($this->GraphAreaX2 - $this->GraphAreaX1) / 2;
                            $Bounds = $this->drawText($XPos, $YPos, $Parameters["Name"], ["Align" => TEXT_ALIGN_BOTTOMMIDDLE]);
                            $MinTop = $Bounds[2]["Y"];
                            $this->DataSet->Data["GraphArea"]["Y1"] = $MinTop;
                        }
                        $AxisPos["T"] = $MinTop - $ScaleSpacing;
                    }
                } elseif ($Pos == SCALE_POS_TOPBOTTOM) {
                    if ($Parameters["Position"] == AXIS_POSITION_LEFT) {
                        if ($LabelRotation == 0) {
                            $LabelAlign = TEXT_ALIGN_MIDDLERIGHT;
                            $XLabelOffset = -2;
                        }
                        if ($LabelRotation > 0 && $LabelRotation < 190) {
                            $LabelAlign = TEXT_ALIGN_MIDDLERIGHT;
                            $XLabelOffset = -6;
                        }
                        if ($LabelRotation == 180) {
                            $LabelAlign = TEXT_ALIGN_MIDDLELEFT;
                            $XLabelOffset = -2;
                        }
                        if ($LabelRotation > 180 && $LabelRotation < 360) {
                            $LabelAlign = TEXT_ALIGN_MIDDLELEFT;
                            $XLabelOffset = -5;
                        }
                        if (!$RemoveXAxis) {
                            if ($Floating) {
                                $FloatingOffset = $YMargin;
                                $this->drawLine($AxisPos["L"], $this->GraphAreaY1 + $Parameters["Margin"], $AxisPos["L"], $this->GraphAreaY2 - $Parameters["Margin"], ["R" => $AxisR, "G" => $AxisG, "B" => $AxisB, "Alpha" => $AxisAlpha]);
                            } else {
                                $FloatingOffset = 0;
                                $this->drawLine($AxisPos["L"], $this->GraphAreaY1, $AxisPos["L"], $this->GraphAreaY2, ["R" => $AxisR, "G" => $AxisG, "B" => $AxisB, "Alpha" => $AxisAlpha]);
                            }
                            if ($DrawArrows) {
                                $this->drawArrow($AxisPos["L"], $this->GraphAreaY2 - $Parameters["Margin"], $AxisPos["L"], $this->GraphAreaY2 + $ArrowSize * 2, ["FillR" => $AxisR, "FillG" => $AxisG, "FillB" => $AxisB, "Size" => $ArrowSize]);
                            }
                        }
                        $Height = $this->GraphAreaY2 - $this->GraphAreaY1 - $Parameters["Margin"] * 2;
                        if ($Parameters["Rows"] == 0) {
                            $Step = $Height;
                        } else {
                            $Step = $Height / $Parameters["Rows"];
                        }
                        $MinLeft = $AxisPos["L"];
                        for ($i = 0; $i <= $Parameters["Rows"]; $i++) {
                            $YPos = $this->GraphAreaY1 + $Parameters["Margin"] + $Step * $i;
                            $XPos = $AxisPos["L"];
                            if ($Abscissa != null) {
                                $Value = "";
                                if (isset($Data["Series"][$Abscissa]["Data"][$i])) {
                                    $Value = $this->scaleFormat($Data["Series"][$Abscissa]["Data"][$i], $Data["XAxisDisplay"], $Data["XAxisFormat"], $Data["XAxisUnit"]);
                                }
                            } else {
                                $Value = $i;
                                if (isset($Parameters["ScaleMin"]) && isset($Parameters["RowHeight"])) {
                                    $Value = $this->scaleFormat($Parameters["ScaleMin"] + $Parameters["RowHeight"] * $i, $Data["XAxisDisplay"], $Data["XAxisFormat"], $Data["XAxisUnit"]);
                                }
                            }
                            $ID++;
                            $Skipped = \true;
                            if ($this->isValidLabel($Value, $LastValue, $LabelingMethod, $ID, $LabelSkip) && !$RemoveXAxis) {
                                $Bounds = $this->drawText($XPos - $OuterTickWidth + $XLabelOffset, $YPos, $Value, ["Angle" => $LabelRotation, "Align" => $LabelAlign]);
                                $TxtBox = $XPos - $OuterTickWidth - 2 - ($Bounds[1]["X"] - $Bounds[0]["X"]);
                                $MinLeft = min($MinLeft, $TxtBox);
                                $LastValue = $Value;
                                $Skipped = \false;
                            }
                            if ($RemoveXAxis) {
                                $Skipped = \false;
                            }
                            if ($Skipped) {
                                if ($DrawXLines && !$RemoveSkippedAxis) {
                                    $this->drawLine($this->GraphAreaX1 + $FloatingOffset, $YPos, $this->GraphAreaX2 - $FloatingOffset, $YPos, $SkippedAxisColor);
                                }
                                if (($SkippedInnerTickWidth != 0 || $SkippedOuterTickWidth != 0) && !$RemoveXAxis && !$RemoveSkippedAxis) {
                                    $this->drawLine($XPos - $SkippedOuterTickWidth, $YPos, $XPos + $SkippedInnerTickWidth, $YPos, $SkippedTickColor);
                                }
                            } else {
                                if ($DrawXLines && ($YPos != $this->GraphAreaY1 && $YPos != $this->GraphAreaY2)) {
                                    $this->drawLine($this->GraphAreaX1 + $FloatingOffset, $YPos, $this->GraphAreaX2 - $FloatingOffset, $YPos, ["R" => $GridR, "G" => $GridG, "B" => $GridB, "Alpha" => $GridAlpha, "Ticks" => $GridTicks]);
                                }
                                if (($InnerTickWidth != 0 || $OuterTickWidth != 0) && !$RemoveXAxis) {
                                    $this->drawLine($XPos - $OuterTickWidth, $YPos, $XPos + $InnerTickWidth, $YPos, ["R" => $TickR, "G" => $TickG, "B" => $TickB, "Alpha" => $TickAlpha]);
                                }
                            }
                        }
                        if (isset($Parameters["Name"]) && !$RemoveXAxis) {
                            $XPos = $MinLeft - 2;
                            $YPos = $this->GraphAreaY1 + ($this->GraphAreaY2 - $this->GraphAreaY1) / 2;
                            $Bounds = $this->drawText($XPos, $YPos, $Parameters["Name"], ["Align" => TEXT_ALIGN_BOTTOMMIDDLE, "Angle" => 90]);
                            $MinLeft = $Bounds[0]["X"];
                            $this->DataSet->Data["GraphArea"]["X1"] = $MinLeft;
                        }
                        $AxisPos["L"] = $MinLeft - $ScaleSpacing;
                    } elseif ($Parameters["Position"] == AXIS_POSITION_RIGHT) {
                        if ($LabelRotation == 0) {
                            $LabelAlign = TEXT_ALIGN_MIDDLELEFT;
                            $XLabelOffset = 2;
                        }
                        if ($LabelRotation > 0 && $LabelRotation < 190) {
                            $LabelAlign = TEXT_ALIGN_MIDDLELEFT;
                            $XLabelOffset = 6;
                        }
                        if ($LabelRotation == 180) {
                            $LabelAlign = TEXT_ALIGN_MIDDLERIGHT;
                            $XLabelOffset = 5;
                        }
                        if ($LabelRotation > 180 && $LabelRotation < 360) {
                            $LabelAlign = TEXT_ALIGN_MIDDLERIGHT;
                            $XLabelOffset = 7;
                        }
                        if (!$RemoveXAxis) {
                            if ($Floating) {
                                $FloatingOffset = $YMargin;
                                $this->drawLine($AxisPos["R"], $this->GraphAreaY1 + $Parameters["Margin"], $AxisPos["R"], $this->GraphAreaY2 - $Parameters["Margin"], ["R" => $AxisR, "G" => $AxisG, "B" => $AxisB, "Alpha" => $AxisAlpha]);
                            } else {
                                $FloatingOffset = 0;
                                $this->drawLine($AxisPos["R"], $this->GraphAreaY1, $AxisPos["R"], $this->GraphAreaY2, ["R" => $AxisR, "G" => $AxisG, "B" => $AxisB, "Alpha" => $AxisAlpha]);
                            }
                            if ($DrawArrows) {
                                $this->drawArrow($AxisPos["R"], $this->GraphAreaY2 - $Parameters["Margin"], $AxisPos["R"], $this->GraphAreaY2 + $ArrowSize * 2, ["FillR" => $AxisR, "FillG" => $AxisG, "FillB" => $AxisB, "Size" => $ArrowSize]);
                            }
                        }
                        $Height = $this->GraphAreaY2 - $this->GraphAreaY1 - $Parameters["Margin"] * 2;
                        if ($Parameters["Rows"] == 0) {
                            $Step = $Height;
                        } else {
                            $Step = $Height / $Parameters["Rows"];
                        }
                        $MaxRight = $AxisPos["R"];
                        for ($i = 0; $i <= $Parameters["Rows"]; $i++) {
                            $YPos = $this->GraphAreaY1 + $Parameters["Margin"] + $Step * $i;
                            $XPos = $AxisPos["R"];
                            if ($Abscissa != null) {
                                $Value = "";
                                if (isset($Data["Series"][$Abscissa]["Data"][$i])) {
                                    $Value = $this->scaleFormat($Data["Series"][$Abscissa]["Data"][$i], $Data["XAxisDisplay"], $Data["XAxisFormat"], $Data["XAxisUnit"]);
                                }
                            } else {
                                $Value = $i;
                                if (isset($Parameters["ScaleMin"]) && isset($Parameters["RowHeight"])) {
                                    $Value = $this->scaleFormat($Parameters["ScaleMin"] + $Parameters["RowHeight"] * $i, $Data["XAxisDisplay"], $Data["XAxisFormat"], $Data["XAxisUnit"]);
                                }
                            }
                            $ID++;
                            $Skipped = \true;
                            if ($this->isValidLabel($Value, $LastValue, $LabelingMethod, $ID, $LabelSkip) && !$RemoveXAxis) {
                                $Bounds = $this->drawText($XPos + $OuterTickWidth + $XLabelOffset, $YPos, $Value, ["Angle" => $LabelRotation, "Align" => $LabelAlign]);
                                $TxtBox = $XPos + $OuterTickWidth + 2 + ($Bounds[1]["X"] - $Bounds[0]["X"]);
                                $MaxRight = max($MaxRight, $TxtBox);
                                $LastValue = $Value;
                                $Skipped = \false;
                            }
                            if ($RemoveXAxis) {
                                $Skipped = \false;
                            }
                            if ($Skipped) {
                                if ($DrawXLines && !$RemoveSkippedAxis) {
                                    $this->drawLine($this->GraphAreaX1 + $FloatingOffset, $YPos, $this->GraphAreaX2 - $FloatingOffset, $YPos, $SkippedAxisColor);
                                }
                                if (($SkippedInnerTickWidth != 0 || $SkippedOuterTickWidth != 0) && !$RemoveXAxis && !$RemoveSkippedAxis) {
                                    $this->drawLine($XPos + $SkippedOuterTickWidth, $YPos, $XPos - $SkippedInnerTickWidth, $YPos, $SkippedTickColor);
                                }
                            } else {
                                if ($DrawXLines) {
                                    $this->drawLine($this->GraphAreaX1 + $FloatingOffset, $YPos, $this->GraphAreaX2 - $FloatingOffset, $YPos, ["R" => $GridR, "G" => $GridG, "B" => $GridB, "Alpha" => $GridAlpha, "Ticks" => $GridTicks]);
                                }
                                if (($InnerTickWidth != 0 || $OuterTickWidth != 0) && !$RemoveXAxis) {
                                    $this->drawLine($XPos + $OuterTickWidth, $YPos, $XPos - $InnerTickWidth, $YPos, ["R" => $TickR, "G" => $TickG, "B" => $TickB, "Alpha" => $TickAlpha]);
                                }
                            }
                        }
                        if (isset($Parameters["Name"]) && !$RemoveXAxis) {
                            $XPos = $MaxRight + 4;
                            $YPos = $this->GraphAreaY1 + ($this->GraphAreaY2 - $this->GraphAreaY1) / 2;
                            $Bounds = $this->drawText($XPos, $YPos, $Parameters["Name"], ["Align" => TEXT_ALIGN_BOTTOMMIDDLE, "Angle" => 270]);
                            $MaxRight = $Bounds[1]["X"];
                            $this->DataSet->Data["GraphArea"]["X2"] = $MaxRight + $this->FontSize;
                        }
                        $AxisPos["R"] = $MaxRight + $ScaleSpacing;
                    }
                }
            }
            if ($Parameters["Identity"] == AXIS_Y && !$RemoveYAxis) {
                if ($Pos == SCALE_POS_LEFTRIGHT) {
                    if ($Parameters["Position"] == AXIS_POSITION_LEFT) {
                        if ($Floating) {
                            $FloatingOffset = $XMargin;
                            $this->drawLine($AxisPos["L"], $this->GraphAreaY1 + $Parameters["Margin"], $AxisPos["L"], $this->GraphAreaY2 - $Parameters["Margin"], ["R" => $AxisR, "G" => $AxisG, "B" => $AxisB, "Alpha" => $AxisAlpha]);
                        } else {
                            $FloatingOffset = 0;
                            $this->drawLine($AxisPos["L"], $this->GraphAreaY1, $AxisPos["L"], $this->GraphAreaY2, ["R" => $AxisR, "G" => $AxisG, "B" => $AxisB, "Alpha" => $AxisAlpha]);
                        }
                        if ($DrawArrows) {
                            $this->drawArrow($AxisPos["L"], $this->GraphAreaY1 + $Parameters["Margin"], $AxisPos["L"], $this->GraphAreaY1 - $ArrowSize * 2, ["FillR" => $AxisR, "FillG" => $AxisG, "FillB" => $AxisB, "Size" => $ArrowSize]);
                        }
                        $Height = $this->GraphAreaY2 - $this->GraphAreaY1 - $Parameters["Margin"] * 2;
                        $Step = $Height / $Parameters["Rows"];
                        $SubTicksSize = $Step / 2;
                        $MinLeft = $AxisPos["L"];
                        $LastY = null;
                        for ($i = 0; $i <= $Parameters["Rows"]; $i++) {
                            $YPos = $this->GraphAreaY2 - $Parameters["Margin"] - $Step * $i;
                            $XPos = $AxisPos["L"];
                            $Value = $this->scaleFormat($Parameters["ScaleMin"] + $Parameters["RowHeight"] * $i, $Parameters["Display"], $Parameters["Format"], $Parameters["Unit"]);
                            if ($i % 2 == 1) {
                                $BGColor = ["R" => $BackgroundR1, "G" => $BackgroundG1, "B" => $BackgroundB1, "Alpha" => $BackgroundAlpha1];
                            } else {
                                $BGColor = ["R" => $BackgroundR2, "G" => $BackgroundG2, "B" => $BackgroundB2, "Alpha" => $BackgroundAlpha2];
                            }
                            if ($LastY != null && $CycleBackground && ($DrawYLines == ALL || in_array($AxisID, $DrawYLines))) {
                                $this->drawFilledRectangle($this->GraphAreaX1 + $FloatingOffset, $LastY, $this->GraphAreaX2 - $FloatingOffset, $YPos, $BGColor);
                            }
                            if ($DrawYLines == ALL || in_array($AxisID, $DrawYLines)) {
                                $this->drawLine($this->GraphAreaX1 + $FloatingOffset, $YPos, $this->GraphAreaX2 - $FloatingOffset, $YPos, ["R" => $GridR, "G" => $GridG, "B" => $GridB, "Alpha" => $GridAlpha, "Ticks" => $GridTicks]);
                            }
                            if ($DrawSubTicks && $i != $Parameters["Rows"]) {
                                $this->drawLine($XPos - $OuterSubTickWidth, $YPos - $SubTicksSize, $XPos + $InnerSubTickWidth, $YPos - $SubTicksSize, ["R" => $SubTickR, "G" => $SubTickG, "B" => $SubTickB, "Alpha" => $SubTickAlpha]);
                            }
                            if (!$RemoveYAxiValues) {
                                $this->drawLine($XPos - $OuterTickWidth, $YPos, $XPos + $InnerTickWidth, $YPos, ["R" => $TickR, "G" => $TickG, "B" => $TickB, "Alpha" => $TickAlpha]);
                                $Bounds = $this->drawText($XPos - $OuterTickWidth - 2, $YPos, $Value, ["Align" => TEXT_ALIGN_MIDDLERIGHT]);
                                $TxtLeft = $XPos - $OuterTickWidth - 2 - ($Bounds[1]["X"] - $Bounds[0]["X"]);
                                $MinLeft = min($MinLeft, $TxtLeft);
                            }
                            $LastY = $YPos;
                        }
                        if (isset($Parameters["Name"])) {
                            $XPos = $MinLeft - 2;
                            $YPos = $this->GraphAreaY1 + ($this->GraphAreaY2 - $this->GraphAreaY1) / 2;
                            $Bounds = $this->drawText($XPos, $YPos, $Parameters["Name"], ["Align" => TEXT_ALIGN_BOTTOMMIDDLE, "Angle" => 90]);
                            $MinLeft = $Bounds[2]["X"];
                            $this->DataSet->Data["GraphArea"]["X1"] = $MinLeft;
                        }
                        $AxisPos["L"] = $MinLeft - $ScaleSpacing;
                    } elseif ($Parameters["Position"] == AXIS_POSITION_RIGHT) {
                        if ($Floating) {
                            $FloatingOffset = $XMargin;
                            $this->drawLine($AxisPos["R"], $this->GraphAreaY1 + $Parameters["Margin"], $AxisPos["R"], $this->GraphAreaY2 - $Parameters["Margin"], ["R" => $AxisR, "G" => $AxisG, "B" => $AxisB, "Alpha" => $AxisAlpha]);
                        } else {
                            $FloatingOffset = 0;
                            $this->drawLine($AxisPos["R"], $this->GraphAreaY1, $AxisPos["R"], $this->GraphAreaY2, ["R" => $AxisR, "G" => $AxisG, "B" => $AxisB, "Alpha" => $AxisAlpha]);
                        }
                        if ($DrawArrows) {
                            $this->drawArrow($AxisPos["R"], $this->GraphAreaY1 + $Parameters["Margin"], $AxisPos["R"], $this->GraphAreaY1 - $ArrowSize * 2, ["FillR" => $AxisR, "FillG" => $AxisG, "FillB" => $AxisB, "Size" => $ArrowSize]);
                        }
                        $Height = $this->GraphAreaY2 - $this->GraphAreaY1 - $Parameters["Margin"] * 2;
                        $Step = $Height / $Parameters["Rows"];
                        $SubTicksSize = $Step / 2;
                        $MaxLeft = $AxisPos["R"];
                        $LastY = null;
                        for ($i = 0; $i <= $Parameters["Rows"]; $i++) {
                            $YPos = $this->GraphAreaY2 - $Parameters["Margin"] - $Step * $i;
                            $XPos = $AxisPos["R"];
                            $Value = $this->scaleFormat($Parameters["ScaleMin"] + $Parameters["RowHeight"] * $i, $Parameters["Display"], $Parameters["Format"], $Parameters["Unit"]);
                            if ($i % 2 == 1) {
                                $BGColor = ["R" => $BackgroundR1, "G" => $BackgroundG1, "B" => $BackgroundB1, "Alpha" => $BackgroundAlpha1];
                            } else {
                                $BGColor = ["R" => $BackgroundR2, "G" => $BackgroundG2, "B" => $BackgroundB2, "Alpha" => $BackgroundAlpha2];
                            }
                            if ($LastY != null && $CycleBackground && ($DrawYLines == ALL || in_array($AxisID, $DrawYLines))) {
                                $this->drawFilledRectangle($this->GraphAreaX1 + $FloatingOffset, $LastY, $this->GraphAreaX2 - $FloatingOffset, $YPos, $BGColor);
                            }
                            if ($DrawYLines == ALL || in_array($AxisID, $DrawYLines)) {
                                $this->drawLine($this->GraphAreaX1 + $FloatingOffset, $YPos, $this->GraphAreaX2 - $FloatingOffset, $YPos, ["R" => $GridR, "G" => $GridG, "B" => $GridB, "Alpha" => $GridAlpha, "Ticks" => $GridTicks]);
                            }
                            if ($DrawSubTicks && $i != $Parameters["Rows"]) {
                                $this->drawLine($XPos - $OuterSubTickWidth, $YPos - $SubTicksSize, $XPos + $InnerSubTickWidth, $YPos - $SubTicksSize, ["R" => $SubTickR, "G" => $SubTickG, "B" => $SubTickB, "Alpha" => $SubTickAlpha]);
                            }
                            $this->drawLine($XPos - $InnerTickWidth, $YPos, $XPos + $OuterTickWidth, $YPos, ["R" => $TickR, "G" => $TickG, "B" => $TickB, "Alpha" => $TickAlpha]);
                            $Bounds = $this->drawText($XPos + $OuterTickWidth + 2, $YPos, $Value, ["Align" => TEXT_ALIGN_MIDDLELEFT]);
                            $TxtLeft = $XPos + $OuterTickWidth + 2 + ($Bounds[1]["X"] - $Bounds[0]["X"]);
                            $MaxLeft = max($MaxLeft, $TxtLeft);
                            $LastY = $YPos;
                        }
                        if (isset($Parameters["Name"])) {
                            $XPos = $MaxLeft + 6;
                            $YPos = $this->GraphAreaY1 + ($this->GraphAreaY2 - $this->GraphAreaY1) / 2;
                            $Bounds = $this->drawText($XPos, $YPos, $Parameters["Name"], ["Align" => TEXT_ALIGN_BOTTOMMIDDLE, "Angle" => 270]);
                            $MaxLeft = $Bounds[2]["X"];
                            $this->DataSet->Data["GraphArea"]["X2"] = $MaxLeft + $this->FontSize;
                        }
                        $AxisPos["R"] = $MaxLeft + $ScaleSpacing;
                    }
                } elseif ($Pos == SCALE_POS_TOPBOTTOM) {
                    if ($Parameters["Position"] == AXIS_POSITION_TOP) {
                        if ($Floating) {
                            $FloatingOffset = $XMargin;
                            $this->drawLine($this->GraphAreaX1 + $Parameters["Margin"], $AxisPos["T"], $this->GraphAreaX2 - $Parameters["Margin"], $AxisPos["T"], ["R" => $AxisR, "G" => $AxisG, "B" => $AxisB, "Alpha" => $AxisAlpha]);
                        } else {
                            $FloatingOffset = 0;
                            $this->drawLine($this->GraphAreaX1, $AxisPos["T"], $this->GraphAreaX2, $AxisPos["T"], ["R" => $AxisR, "G" => $AxisG, "B" => $AxisB, "Alpha" => $AxisAlpha]);
                        }
                        if ($DrawArrows) {
                            $this->drawArrow($this->GraphAreaX2 - $Parameters["Margin"], $AxisPos["T"], $this->GraphAreaX2 + $ArrowSize * 2, $AxisPos["T"], ["FillR" => $AxisR, "FillG" => $AxisG, "FillB" => $AxisB, "Size" => $ArrowSize]);
                        }
                        $Width = $this->GraphAreaX2 - $this->GraphAreaX1 - $Parameters["Margin"] * 2;
                        $Step = $Width / $Parameters["Rows"];
                        $SubTicksSize = $Step / 2;
                        $MinTop = $AxisPos["T"];
                        $LastX = null;
                        for ($i = 0; $i <= $Parameters["Rows"]; $i++) {
                            $XPos = $this->GraphAreaX1 + $Parameters["Margin"] + $Step * $i;
                            $YPos = $AxisPos["T"];
                            $Value = $this->scaleFormat($Parameters["ScaleMin"] + $Parameters["RowHeight"] * $i, $Parameters["Display"], $Parameters["Format"], $Parameters["Unit"]);
                            if ($i % 2 == 1) {
                                $BGColor = ["R" => $BackgroundR1, "G" => $BackgroundG1, "B" => $BackgroundB1, "Alpha" => $BackgroundAlpha1];
                            } else {
                                $BGColor = ["R" => $BackgroundR2, "G" => $BackgroundG2, "B" => $BackgroundB2, "Alpha" => $BackgroundAlpha2];
                            }
                            if ($LastX != null && $CycleBackground && ($DrawYLines == ALL || in_array($AxisID, $DrawYLines))) {
                                $this->drawFilledRectangle($LastX, $this->GraphAreaY1 + $FloatingOffset, $XPos, $this->GraphAreaY2 - $FloatingOffset, $BGColor);
                            }
                            if ($DrawYLines == ALL || in_array($AxisID, $DrawYLines)) {
                                $this->drawLine($XPos, $this->GraphAreaY1 + $FloatingOffset, $XPos, $this->GraphAreaY2 - $FloatingOffset, ["R" => $GridR, "G" => $GridG, "B" => $GridB, "Alpha" => $GridAlpha, "Ticks" => $GridTicks]);
                            }
                            if ($DrawSubTicks && $i != $Parameters["Rows"]) {
                                $this->drawLine($XPos + $SubTicksSize, $YPos - $OuterSubTickWidth, $XPos + $SubTicksSize, $YPos + $InnerSubTickWidth, ["R" => $SubTickR, "G" => $SubTickG, "B" => $SubTickB, "Alpha" => $SubTickAlpha]);
                            }
                            $this->drawLine($XPos, $YPos - $OuterTickWidth, $XPos, $YPos + $InnerTickWidth, ["R" => $TickR, "G" => $TickG, "B" => $TickB, "Alpha" => $TickAlpha]);
                            $Bounds = $this->drawText($XPos, $YPos - $OuterTickWidth - 2, $Value, ["Align" => TEXT_ALIGN_BOTTOMMIDDLE]);
                            $TxtHeight = $YPos - $OuterTickWidth - 2 - ($Bounds[1]["Y"] - $Bounds[2]["Y"]);
                            $MinTop = min($MinTop, $TxtHeight);
                            $LastX = $XPos;
                        }
                        if (isset($Parameters["Name"])) {
                            $YPos = $MinTop - 2;
                            $XPos = $this->GraphAreaX1 + ($this->GraphAreaX2 - $this->GraphAreaX1) / 2;
                            $Bounds = $this->drawText($XPos, $YPos, $Parameters["Name"], ["Align" => TEXT_ALIGN_BOTTOMMIDDLE]);
                            $MinTop = $Bounds[2]["Y"];
                            $this->DataSet->Data["GraphArea"]["Y1"] = $MinTop;
                        }
                        $AxisPos["T"] = $MinTop - $ScaleSpacing;
                    } elseif ($Parameters["Position"] == AXIS_POSITION_BOTTOM) {
                        if ($Floating) {
                            $FloatingOffset = $XMargin;
                            $this->drawLine($this->GraphAreaX1 + $Parameters["Margin"], $AxisPos["B"], $this->GraphAreaX2 - $Parameters["Margin"], $AxisPos["B"], ["R" => $AxisR, "G" => $AxisG, "B" => $AxisB, "Alpha" => $AxisAlpha]);
                        } else {
                            $FloatingOffset = 0;
                            $this->drawLine($this->GraphAreaX1, $AxisPos["B"], $this->GraphAreaX2, $AxisPos["B"], ["R" => $AxisR, "G" => $AxisG, "B" => $AxisB, "Alpha" => $AxisAlpha]);
                        }
                        if ($DrawArrows) {
                            $this->drawArrow($this->GraphAreaX2 - $Parameters["Margin"], $AxisPos["B"], $this->GraphAreaX2 + $ArrowSize * 2, $AxisPos["B"], ["FillR" => $AxisR, "FillG" => $AxisG, "FillB" => $AxisB, "Size" => $ArrowSize]);
                        }
                        $Width = $this->GraphAreaX2 - $this->GraphAreaX1 - $Parameters["Margin"] * 2;
                        $Step = $Width / $Parameters["Rows"];
                        $SubTicksSize = $Step / 2;
                        $MaxBottom = $AxisPos["B"];
                        $LastX = null;
                        for ($i = 0; $i <= $Parameters["Rows"]; $i++) {
                            $XPos = $this->GraphAreaX1 + $Parameters["Margin"] + $Step * $i;
                            $YPos = $AxisPos["B"];
                            $Value = $this->scaleFormat($Parameters["ScaleMin"] + $Parameters["RowHeight"] * $i, $Parameters["Display"], $Parameters["Format"], $Parameters["Unit"]);
                            if ($i % 2 == 1) {
                                $BGColor = ["R" => $BackgroundR1, "G" => $BackgroundG1, "B" => $BackgroundB1, "Alpha" => $BackgroundAlpha1];
                            } else {
                                $BGColor = ["R" => $BackgroundR2, "G" => $BackgroundG2, "B" => $BackgroundB2, "Alpha" => $BackgroundAlpha2];
                            }
                            if ($LastX != null && $CycleBackground && ($DrawYLines == ALL || in_array($AxisID, $DrawYLines))) {
                                $this->drawFilledRectangle($LastX, $this->GraphAreaY1 + $FloatingOffset, $XPos, $this->GraphAreaY2 - $FloatingOffset, $BGColor);
                            }
                            if ($DrawYLines == ALL || in_array($AxisID, $DrawYLines)) {
                                $this->drawLine($XPos, $this->GraphAreaY1 + $FloatingOffset, $XPos, $this->GraphAreaY2 - $FloatingOffset, ["R" => $GridR, "G" => $GridG, "B" => $GridB, "Alpha" => $GridAlpha, "Ticks" => $GridTicks]);
                            }
                            if ($DrawSubTicks && $i != $Parameters["Rows"]) {
                                $this->drawLine($XPos + $SubTicksSize, $YPos - $OuterSubTickWidth, $XPos + $SubTicksSize, $YPos + $InnerSubTickWidth, ["R" => $SubTickR, "G" => $SubTickG, "B" => $SubTickB, "Alpha" => $SubTickAlpha]);
                            }
                            $this->drawLine($XPos, $YPos - $OuterTickWidth, $XPos, $YPos + $InnerTickWidth, ["R" => $TickR, "G" => $TickG, "B" => $TickB, "Alpha" => $TickAlpha]);
                            $Bounds = $this->drawText($XPos, $YPos + $OuterTickWidth + 2, $Value, ["Align" => TEXT_ALIGN_TOPMIDDLE]);
                            $TxtHeight = $YPos + $OuterTickWidth + 2 + ($Bounds[1]["Y"] - $Bounds[2]["Y"]);
                            $MaxBottom = max($MaxBottom, $TxtHeight);
                            $LastX = $XPos;
                        }
                        if (isset($Parameters["Name"])) {
                            $YPos = $MaxBottom + 2;
                            $XPos = $this->GraphAreaX1 + ($this->GraphAreaX2 - $this->GraphAreaX1) / 2;
                            $Bounds = $this->drawText($XPos, $YPos, $Parameters["Name"], ["Align" => TEXT_ALIGN_TOPMIDDLE]);
                            $MaxBottom = $Bounds[0]["Y"];
                            $this->DataSet->Data["GraphArea"]["Y2"] = $MaxBottom + $this->FontSize;
                        }
                        $AxisPos["B"] = $MaxBottom + $ScaleSpacing;
                    }
                }
            }
        }
    }
    /**
     * Draw an X threshold
     * @param mixed $Value
     * @param boolean $Format
     * @return array|null|integer
     */
    public function drawXThreshold($Value, array $Format = [])
    {
        $R = isset($Format["R"]) ? $Format["R"] : 255;
        $G = isset($Format["G"]) ? $Format["G"] : 0;
        $B = isset($Format["B"]) ? $Format["B"] : 0;
        $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 50;
        $Weight = isset($Format["Weight"]) ? $Format["Weight"] : null;
        $Ticks = isset($Format["Ticks"]) ? $Format["Ticks"] : 6;
        $Wide = isset($Format["Wide"]) ? $Format["Wide"] : \false;
        $WideFactor = isset($Format["WideFactor"]) ? $Format["WideFactor"] : 5;
        $WriteCaption = isset($Format["WriteCaption"]) ? $Format["WriteCaption"] : \false;
        $Caption = isset($Format["Caption"]) ? $Format["Caption"] : null;
        $CaptionAlign = isset($Format["CaptionAlign"]) ? $Format["CaptionAlign"] : CAPTION_LEFT_TOP;
        $CaptionOffset = isset($Format["CaptionOffset"]) ? $Format["CaptionOffset"] : 5;
        $CaptionR = isset($Format["CaptionR"]) ? $Format["CaptionR"] : 255;
        $CaptionG = isset($Format["CaptionG"]) ? $Format["CaptionG"] : 255;
        $CaptionB = isset($Format["CaptionB"]) ? $Format["CaptionB"] : 255;
        $CaptionAlpha = isset($Format["CaptionAlpha"]) ? $Format["CaptionAlpha"] : 100;
        $DrawBox = isset($Format["DrawBox"]) ? $Format["DrawBox"] : \true;
        $DrawBoxBorder = isset($Format["DrawBoxBorder"]) ? $Format["DrawBoxBorder"] : \false;
        $BorderOffset = isset($Format["BorderOffset"]) ? $Format["BorderOffset"] : 3;
        $BoxRounded = isset($Format["BoxRounded"]) ? $Format["BoxRounded"] : \true;
        $RoundedRadius = isset($Format["RoundedRadius"]) ? $Format["RoundedRadius"] : 3;
        $BoxR = isset($Format["BoxR"]) ? $Format["BoxR"] : 0;
        $BoxG = isset($Format["BoxG"]) ? $Format["BoxG"] : 0;
        $BoxB = isset($Format["BoxB"]) ? $Format["BoxB"] : 0;
        $BoxAlpha = isset($Format["BoxAlpha"]) ? $Format["BoxAlpha"] : 30;
        $BoxSurrounding = isset($Format["BoxSurrounding"]) ? $Format["BoxSurrounding"] : "";
        $BoxBorderR = isset($Format["BoxBorderR"]) ? $Format["BoxBorderR"] : 255;
        $BoxBorderG = isset($Format["BoxBorderG"]) ? $Format["BoxBorderG"] : 255;
        $BoxBorderB = isset($Format["BoxBorderB"]) ? $Format["BoxBorderB"] : 255;
        $BoxBorderAlpha = isset($Format["BoxBorderAlpha"]) ? $Format["BoxBorderAlpha"] : 100;
        $ValueIsLabel = isset($Format["ValueIsLabel"]) ? $Format["ValueIsLabel"] : \false;
        $Data = $this->DataSet->getData();
        $AbscissaMargin = $this->getAbscissaMargin($Data);
        $XScale = $this->scaleGetXSettings();
        if (is_array($Value)) {
            foreach ($Value as $Key => $ID) {
                $this->drawXThreshold($ID, $Format);
            }
            return 0;
        }
        if ($ValueIsLabel) {
            $Format["ValueIsLabel"] = \false;
            foreach ($Data["Series"][$Data["Abscissa"]]["Data"] as $Key => $SerieValue) {
                if ($SerieValue == $Value) {
                    $this->drawXThreshold($Key, $Format);
                }
            }
            return 0;
        }
        $CaptionSettings = ["DrawBox" => $DrawBox, "DrawBoxBorder" => $DrawBoxBorder, "BorderOffset" => $BorderOffset, "BoxRounded" => $BoxRounded, "RoundedRadius" => $RoundedRadius, "BoxR" => $BoxR, "BoxG" => $BoxG, "BoxB" => $BoxB, "BoxAlpha" => $BoxAlpha, "BoxSurrounding" => $BoxSurrounding, "BoxBorderR" => $BoxBorderR, "BoxBorderG" => $BoxBorderG, "BoxBorderB" => $BoxBorderB, "BoxBorderAlpha" => $BoxBorderAlpha, "R" => $CaptionR, "G" => $CaptionG, "B" => $CaptionB, "Alpha" => $CaptionAlpha];
        if ($Caption == null) {
            $Caption = $Value;
            if (isset($Data["Abscissa"]) && isset($Data["Series"][$Data["Abscissa"]]["Data"][$Value])) {
                $Caption = $Data["Series"][$Data["Abscissa"]]["Data"][$Value];
            }
        }
        if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
            $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XScale[0] * 2) / $XScale[1];
            $XPos = $this->GraphAreaX1 + $XScale[0] + $XStep * $Value;
            $YPos1 = $this->GraphAreaY1 + $Data["YMargin"];
            $YPos2 = $this->GraphAreaY2 - $Data["YMargin"];
            if ($XPos >= $this->GraphAreaX1 + $AbscissaMargin && $XPos <= $this->GraphAreaX2 - $AbscissaMargin) {
                $this->drawLine($XPos, $YPos1, $XPos, $YPos2, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "Ticks" => $Ticks, "Weight" => $Weight]);
                if ($Wide) {
                    $this->drawLine($XPos - 1, $YPos1, $XPos - 1, $YPos2, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha / $WideFactor, "Ticks" => $Ticks]);
                    $this->drawLine($XPos + 1, $YPos1, $XPos + 1, $YPos2, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha / $WideFactor, "Ticks" => $Ticks]);
                }
                if ($WriteCaption) {
                    if ($CaptionAlign == CAPTION_LEFT_TOP) {
                        $Y = $YPos1 + $CaptionOffset;
                        $CaptionSettings["Align"] = TEXT_ALIGN_TOPMIDDLE;
                    } else {
                        $Y = $YPos2 - $CaptionOffset;
                        $CaptionSettings["Align"] = TEXT_ALIGN_BOTTOMMIDDLE;
                    }
                    $this->drawText($XPos, $Y, $Caption, $CaptionSettings);
                }
                return ["X" => $XPos];
            }
        } elseif ($Data["Orientation"] == SCALE_POS_TOPBOTTOM) {
            $XStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XScale[0] * 2) / $XScale[1];
            $XPos = $this->GraphAreaY1 + $XScale[0] + $XStep * $Value;
            $YPos1 = $this->GraphAreaX1 + $Data["YMargin"];
            $YPos2 = $this->GraphAreaX2 - $Data["YMargin"];
            if ($XPos >= $this->GraphAreaY1 + $AbscissaMargin && $XPos <= $this->GraphAreaY2 - $AbscissaMargin) {
                $this->drawLine($YPos1, $XPos, $YPos2, $XPos, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "Ticks" => $Ticks, "Weight" => $Weight]);
                if ($Wide) {
                    $this->drawLine($YPos1, $XPos - 1, $YPos2, $XPos - 1, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha / $WideFactor, "Ticks" => $Ticks]);
                    $this->drawLine($YPos1, $XPos + 1, $YPos2, $XPos + 1, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha / $WideFactor, "Ticks" => $Ticks]);
                }
                if ($WriteCaption) {
                    if ($CaptionAlign == CAPTION_LEFT_TOP) {
                        $Y = $YPos1 + $CaptionOffset;
                        $CaptionSettings["Align"] = TEXT_ALIGN_MIDDLELEFT;
                    } else {
                        $Y = $YPos2 - $CaptionOffset;
                        $CaptionSettings["Align"] = TEXT_ALIGN_MIDDLERIGHT;
                    }
                    $this->drawText($Y, $XPos, $Caption, $CaptionSettings);
                }
                return ["X" => $XPos];
            }
        }
    }
    /**
     * Draw an X threshold area
     * @param mixed $Value1
     * @param mixed $Value2
     * @param array $Format
     * @return array|null
     */
    public function drawXThresholdArea($Value1, $Value2, array $Format = [])
    {
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
        $AreaName = isset($Format["AreaName"]) ? $Format["AreaName"] : null;
        $NameAngle = isset($Format["NameAngle"]) ? $Format["NameAngle"] : ZONE_NAME_ANGLE_AUTO;
        $NameR = isset($Format["NameR"]) ? $Format["NameR"] : 255;
        $NameG = isset($Format["NameG"]) ? $Format["NameG"] : 255;
        $NameB = isset($Format["NameB"]) ? $Format["NameB"] : 255;
        $NameAlpha = isset($Format["NameAlpha"]) ? $Format["NameAlpha"] : 100;
        $DisableShadowOnArea = isset($Format["DisableShadowOnArea"]) ? $Format["DisableShadowOnArea"] : \true;
        $RestoreShadow = $this->Shadow;
        if ($DisableShadowOnArea && $this->Shadow) {
            $this->Shadow = \false;
        }
        if ($BorderAlpha > 100) {
            $BorderAlpha = 100;
        }
        $Data = $this->DataSet->getData();
        $XScale = $this->scaleGetXSettings();
        if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
            $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XScale[0] * 2) / $XScale[1];
            $XPos1 = $this->GraphAreaX1 + $XScale[0] + $XStep * $Value1;
            $XPos2 = $this->GraphAreaX1 + $XScale[0] + $XStep * $Value2;
            $YPos1 = $this->GraphAreaY1 + $Data["YMargin"];
            $YPos2 = $this->GraphAreaY2 - $Data["YMargin"];
            if ($XPos1 < $this->GraphAreaX1 + $XScale[0]) {
                $XPos1 = $this->GraphAreaX1 + $XScale[0];
            }
            if ($XPos1 > $this->GraphAreaX2 - $XScale[0]) {
                $XPos1 = $this->GraphAreaX2 - $XScale[0];
            }
            if ($XPos2 < $this->GraphAreaX1 + $XScale[0]) {
                $XPos2 = $this->GraphAreaX1 + $XScale[0];
            }
            if ($XPos2 > $this->GraphAreaX2 - $XScale[0]) {
                $XPos2 = $this->GraphAreaX2 - $XScale[0];
            }
            $this->drawFilledRectangle($XPos1, $YPos1, $XPos2, $YPos2, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha]);
            if ($Border) {
                $this->drawLine($XPos1, $YPos1, $XPos1, $YPos2, ["R" => $BorderR, "G" => $BorderG, "B" => $BorderB, "Alpha" => $BorderAlpha, "Ticks" => $BorderTicks]);
                $this->drawLine($XPos2, $YPos1, $XPos2, $YPos2, ["R" => $BorderR, "G" => $BorderG, "B" => $BorderB, "Alpha" => $BorderAlpha, "Ticks" => $BorderTicks]);
            }
            if ($AreaName != null) {
                $XPos = ($XPos2 - $XPos1) / 2 + $XPos1;
                $YPos = ($YPos2 - $YPos1) / 2 + $YPos1;
                if ($NameAngle == ZONE_NAME_ANGLE_AUTO) {
                    $TxtPos = $this->getTextBox($XPos, $YPos, $this->FontName, $this->FontSize, 0, $AreaName);
                    $TxtWidth = $TxtPos[1]["X"] - $TxtPos[0]["X"];
                    $NameAngle = 90;
                    if (abs($XPos2 - $XPos1) > $TxtWidth) {
                        $NameAngle = 0;
                    }
                }
                $this->Shadow = $RestoreShadow;
                $this->drawText($XPos, $YPos, $AreaName, ["R" => $NameR, "G" => $NameG, "B" => $NameB, "Alpha" => $NameAlpha, "Angle" => $NameAngle, "Align" => TEXT_ALIGN_MIDDLEMIDDLE]);
                if ($DisableShadowOnArea) {
                    $this->Shadow = \false;
                }
            }
            $this->Shadow = $RestoreShadow;
            return ["X1" => $XPos1, "X2" => $XPos2];
        } elseif ($Data["Orientation"] == SCALE_POS_TOPBOTTOM) {
            $XStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XScale[0] * 2) / $XScale[1];
            $XPos1 = $this->GraphAreaY1 + $XScale[0] + $XStep * $Value1;
            $XPos2 = $this->GraphAreaY1 + $XScale[0] + $XStep * $Value2;
            $YPos1 = $this->GraphAreaX1 + $Data["YMargin"];
            $YPos2 = $this->GraphAreaX2 - $Data["YMargin"];
            if ($XPos1 < $this->GraphAreaY1 + $XScale[0]) {
                $XPos1 = $this->GraphAreaY1 + $XScale[0];
            }
            if ($XPos1 > $this->GraphAreaY2 - $XScale[0]) {
                $XPos1 = $this->GraphAreaY2 - $XScale[0];
            }
            if ($XPos2 < $this->GraphAreaY1 + $XScale[0]) {
                $XPos2 = $this->GraphAreaY1 + $XScale[0];
            }
            if ($XPos2 > $this->GraphAreaY2 - $XScale[0]) {
                $XPos2 = $this->GraphAreaY2 - $XScale[0];
            }
            $this->drawFilledRectangle($YPos1, $XPos1, $YPos2, $XPos2, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha]);
            if ($Border) {
                $this->drawLine($YPos1, $XPos1, $YPos2, $XPos1, ["R" => $BorderR, "G" => $BorderG, "B" => $BorderB, "Alpha" => $BorderAlpha, "Ticks" => $BorderTicks]);
                $this->drawLine($YPos1, $XPos2, $YPos2, $XPos2, ["R" => $BorderR, "G" => $BorderG, "B" => $BorderB, "Alpha" => $BorderAlpha, "Ticks" => $BorderTicks]);
            }
            if ($AreaName != null) {
                $XPos = ($XPos2 - $XPos1) / 2 + $XPos1;
                $YPos = ($YPos2 - $YPos1) / 2 + $YPos1;
                $this->Shadow = $RestoreShadow;
                $this->drawText($YPos, $XPos, $AreaName, ["R" => $NameR, "G" => $NameG, "B" => $NameB, "Alpha" => $NameAlpha, "Angle" => 0, "Align" => TEXT_ALIGN_MIDDLEMIDDLE]);
                if ($DisableShadowOnArea) {
                    $this->Shadow = \false;
                }
            }
            $this->Shadow = $RestoreShadow;
            return ["X1" => $XPos1, "X2" => $XPos2];
        }
    }
    /**
     * Draw an Y threshold with the computed scale
     * @param mixed $Value
     * @param array $Format
     * @return array|int
     */
    public function drawThreshold($Value, array $Format = [])
    {
        $AxisID = isset($Format["AxisID"]) ? $Format["AxisID"] : 0;
        $R = isset($Format["R"]) ? $Format["R"] : 255;
        $G = isset($Format["G"]) ? $Format["G"] : 0;
        $B = isset($Format["B"]) ? $Format["B"] : 0;
        $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 50;
        $Weight = isset($Format["Weight"]) ? $Format["Weight"] : null;
        $Ticks = isset($Format["Ticks"]) ? $Format["Ticks"] : 6;
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
        $NoMargin = isset($Format["NoMargin"]) ? $Format["NoMargin"] : \false;
        if (is_array($Value)) {
            foreach ($Value as $Key => $ID) {
                $this->drawThreshold($ID, $Format);
            }
            return 0;
        }
        $CaptionSettings = ["DrawBox" => $DrawBox, "DrawBoxBorder" => $DrawBoxBorder, "BorderOffset" => $BorderOffset, "BoxRounded" => $BoxRounded, "RoundedRadius" => $RoundedRadius, "BoxR" => $BoxR, "BoxG" => $BoxG, "BoxB" => $BoxB, "BoxAlpha" => $BoxAlpha, "BoxSurrounding" => $BoxSurrounding, "BoxBorderR" => $BoxBorderR, "BoxBorderG" => $BoxBorderG, "BoxBorderB" => $BoxBorderB, "BoxBorderAlpha" => $BoxBorderAlpha, "R" => $CaptionR, "G" => $CaptionG, "B" => $CaptionB, "Alpha" => $CaptionAlpha];
        $Data = $this->DataSet->getData();
        $AbscissaMargin = $this->getAbscissaMargin($Data);
        if ($NoMargin) {
            $AbscissaMargin = 0;
        }
        if (!isset($Data["Axis"][$AxisID])) {
            return -1;
        }
        if ($Caption == null) {
            $Caption = $Value;
        }
        if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
            $YPos = $this->scaleComputeY($Value, ["AxisID" => $AxisID]);
            if ($YPos >= $this->GraphAreaY1 + $Data["Axis"][$AxisID]["Margin"] && $YPos <= $this->GraphAreaY2 - $Data["Axis"][$AxisID]["Margin"]) {
                $X1 = $this->GraphAreaX1 + $AbscissaMargin;
                $X2 = $this->GraphAreaX2 - $AbscissaMargin;
                $this->drawLine($X1, $YPos, $X2, $YPos, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "Ticks" => $Ticks, "Weight" => $Weight]);
                if ($Wide) {
                    $this->drawLine($X1, $YPos - 1, $X2, $YPos - 1, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha / $WideFactor, "Ticks" => $Ticks]);
                    $this->drawLine($X1, $YPos + 1, $X2, $YPos + 1, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha / $WideFactor, "Ticks" => $Ticks]);
                }
                if ($WriteCaption) {
                    if ($CaptionAlign == CAPTION_LEFT_TOP) {
                        $X = $X1 + $CaptionOffset;
                        $CaptionSettings["Align"] = TEXT_ALIGN_MIDDLELEFT;
                    } else {
                        $X = $X2 - $CaptionOffset;
                        $CaptionSettings["Align"] = TEXT_ALIGN_MIDDLERIGHT;
                    }
                    $this->drawText($X, $YPos, $Caption, $CaptionSettings);
                }
            }
            return ["Y" => $YPos];
        }
        if ($Data["Orientation"] == SCALE_POS_TOPBOTTOM) {
            $XPos = $this->scaleComputeY($Value, ["AxisID" => $AxisID]);
            if ($XPos >= $this->GraphAreaX1 + $Data["Axis"][$AxisID]["Margin"] && $XPos <= $this->GraphAreaX2 - $Data["Axis"][$AxisID]["Margin"]) {
                $Y1 = $this->GraphAreaY1 + $AbscissaMargin;
                $Y2 = $this->GraphAreaY2 - $AbscissaMargin;
                $this->drawLine($XPos, $Y1, $XPos, $Y2, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "Ticks" => $Ticks, "Weight" => $Weight]);
                if ($Wide) {
                    $this->drawLine($XPos - 1, $Y1, $XPos - 1, $Y2, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha / $WideFactor, "Ticks" => $Ticks]);
                    $this->drawLine($XPos + 1, $Y1, $XPos + 1, $Y2, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha / $WideFactor, "Ticks" => $Ticks]);
                }
                if ($WriteCaption) {
                    if ($CaptionAlign == CAPTION_LEFT_TOP) {
                        $Y = $Y1 + $CaptionOffset;
                        $CaptionSettings["Align"] = TEXT_ALIGN_TOPMIDDLE;
                    } else {
                        $Y = $Y2 - $CaptionOffset;
                        $CaptionSettings["Align"] = TEXT_ALIGN_BOTTOMMIDDLE;
                    }
                    $CaptionSettings["Align"] = TEXT_ALIGN_TOPMIDDLE;
                    $this->drawText($XPos, $Y, $Caption, $CaptionSettings);
                }
            }
            return ["Y" => $XPos];
        }
    }
    /**
     * Draw a threshold with the computed scale
     * @param mixed $Value1
     * @param mixed $Value2
     * @param array $Format
     * @return array|int|null
     */
    public function drawThresholdArea($Value1, $Value2, array $Format = [])
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
        $AreaName = isset($Format["AreaName"]) ? $Format["AreaName"] : null;
        $NameAngle = isset($Format["NameAngle"]) ? $Format["NameAngle"] : ZONE_NAME_ANGLE_AUTO;
        $NameR = isset($Format["NameR"]) ? $Format["NameR"] : 255;
        $NameG = isset($Format["NameG"]) ? $Format["NameG"] : 255;
        $NameB = isset($Format["NameB"]) ? $Format["NameB"] : 255;
        $NameAlpha = isset($Format["NameAlpha"]) ? $Format["NameAlpha"] : 100;
        $DisableShadowOnArea = isset($Format["DisableShadowOnArea"]) ? $Format["DisableShadowOnArea"] : \true;
        $NoMargin = isset($Format["NoMargin"]) ? $Format["NoMargin"] : \false;
        if ($Value1 > $Value2) {
            list($Value1, $Value2) = [$Value2, $Value1];
        }
        $RestoreShadow = $this->Shadow;
        if ($DisableShadowOnArea && $this->Shadow) {
            $this->Shadow = \false;
        }
        if ($BorderAlpha > 100) {
            $BorderAlpha = 100;
        }
        $Data = $this->DataSet->getData();
        $AbscissaMargin = $this->getAbscissaMargin($Data);
        if ($NoMargin) {
            $AbscissaMargin = 0;
        }
        if (!isset($Data["Axis"][$AxisID])) {
            return -1;
        }
        if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
            $XPos1 = $this->GraphAreaX1 + $AbscissaMargin;
            $XPos2 = $this->GraphAreaX2 - $AbscissaMargin;
            $YPos1 = $this->scaleComputeY($Value1, ["AxisID" => $AxisID]);
            $YPos2 = $this->scaleComputeY($Value2, ["AxisID" => $AxisID]);
            if ($YPos1 < $this->GraphAreaY1 + $Data["Axis"][$AxisID]["Margin"]) {
                $YPos1 = $this->GraphAreaY1 + $Data["Axis"][$AxisID]["Margin"];
            }
            if ($YPos1 > $this->GraphAreaY2 - $Data["Axis"][$AxisID]["Margin"]) {
                $YPos1 = $this->GraphAreaY2 - $Data["Axis"][$AxisID]["Margin"];
            }
            if ($YPos2 < $this->GraphAreaY1 + $Data["Axis"][$AxisID]["Margin"]) {
                $YPos2 = $this->GraphAreaY1 + $Data["Axis"][$AxisID]["Margin"];
            }
            if ($YPos2 > $this->GraphAreaY2 - $Data["Axis"][$AxisID]["Margin"]) {
                $YPos2 = $this->GraphAreaY2 - $Data["Axis"][$AxisID]["Margin"];
            }
            $this->drawFilledRectangle($XPos1, $YPos1, $XPos2, $YPos2, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha]);
            if ($Border) {
                $this->drawLine($XPos1, $YPos1, $XPos2, $YPos1, ["R" => $BorderR, "G" => $BorderG, "B" => $BorderB, "Alpha" => $BorderAlpha, "Ticks" => $BorderTicks]);
                $this->drawLine($XPos1, $YPos2, $XPos2, $YPos2, ["R" => $BorderR, "G" => $BorderG, "B" => $BorderB, "Alpha" => $BorderAlpha, "Ticks" => $BorderTicks]);
            }
            if ($AreaName != null) {
                $XPos = ($XPos2 - $XPos1) / 2 + $XPos1;
                $YPos = ($YPos2 - $YPos1) / 2 + $YPos1;
                $this->Shadow = $RestoreShadow;
                $this->drawText($XPos, $YPos, $AreaName, ["R" => $NameR, "G" => $NameG, "B" => $NameB, "Alpha" => $NameAlpha, "Angle" => 0, "Align" => TEXT_ALIGN_MIDDLEMIDDLE]);
                if ($DisableShadowOnArea) {
                    $this->Shadow = \false;
                }
            }
            $this->Shadow = $RestoreShadow;
            return ["Y1" => $YPos1, "Y2" => $YPos2];
        } elseif ($Data["Orientation"] == SCALE_POS_TOPBOTTOM) {
            $YPos1 = $this->GraphAreaY1 + $AbscissaMargin;
            $YPos2 = $this->GraphAreaY2 - $AbscissaMargin;
            $XPos1 = $this->scaleComputeY($Value1, ["AxisID" => $AxisID]);
            $XPos2 = $this->scaleComputeY($Value2, ["AxisID" => $AxisID]);
            if ($XPos1 < $this->GraphAreaX1 + $Data["Axis"][$AxisID]["Margin"]) {
                $XPos1 = $this->GraphAreaX1 + $Data["Axis"][$AxisID]["Margin"];
            }
            if ($XPos1 > $this->GraphAreaX2 - $Data["Axis"][$AxisID]["Margin"]) {
                $XPos1 = $this->GraphAreaX2 - $Data["Axis"][$AxisID]["Margin"];
            }
            if ($XPos2 < $this->GraphAreaX1 + $Data["Axis"][$AxisID]["Margin"]) {
                $XPos2 = $this->GraphAreaX1 + $Data["Axis"][$AxisID]["Margin"];
            }
            if ($XPos2 > $this->GraphAreaX2 - $Data["Axis"][$AxisID]["Margin"]) {
                $XPos2 = $this->GraphAreaX2 - $Data["Axis"][$AxisID]["Margin"];
            }
            $this->drawFilledRectangle($XPos1, $YPos1, $XPos2, $YPos2, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha]);
            if ($Border) {
                $this->drawLine($XPos1, $YPos1, $XPos1, $YPos2, ["R" => $BorderR, "G" => $BorderG, "B" => $BorderB, "Alpha" => $BorderAlpha, "Ticks" => $BorderTicks]);
                $this->drawLine($XPos2, $YPos1, $XPos2, $YPos2, ["R" => $BorderR, "G" => $BorderG, "B" => $BorderB, "Alpha" => $BorderAlpha, "Ticks" => $BorderTicks]);
            }
            if ($AreaName != null) {
                $XPos = ($YPos2 - $YPos1) / 2 + $YPos1;
                $YPos = ($XPos2 - $XPos1) / 2 + $XPos1;
                if ($NameAngle == ZONE_NAME_ANGLE_AUTO) {
                    $TxtPos = $this->getTextBox($XPos, $YPos, $this->FontName, $this->FontSize, 0, $AreaName);
                    $TxtWidth = $TxtPos[1]["X"] - $TxtPos[0]["X"];
                    $NameAngle = 90;
                    if (abs($XPos2 - $XPos1) > $TxtWidth) {
                        $NameAngle = 0;
                    }
                }
                $this->Shadow = $RestoreShadow;
                $this->drawText($YPos, $XPos, $AreaName, ["R" => $NameR, "G" => $NameG, "B" => $NameB, "Alpha" => $NameAlpha, "Angle" => $NameAngle, "Align" => TEXT_ALIGN_MIDDLEMIDDLE]);
                if ($DisableShadowOnArea) {
                    $this->Shadow = \false;
                }
            }
            $this->Shadow = $RestoreShadow;
            return ["Y1" => $XPos1, "Y2" => $XPos2];
        }
    }
    /**
     * Draw a plot chart
     * @param array $Format
     */
    public function drawPlotChart(array $Format = [])
    {
        $PlotSize = isset($Format["PlotSize"]) ? $Format["PlotSize"] : null;
        $PlotBorder = isset($Format["PlotBorder"]) ? $Format["PlotBorder"] : \false;
        $BorderR = isset($Format["BorderR"]) ? $Format["BorderR"] : 50;
        $BorderG = isset($Format["BorderG"]) ? $Format["BorderG"] : 50;
        $BorderB = isset($Format["BorderB"]) ? $Format["BorderB"] : 50;
        $BorderAlpha = isset($Format["BorderAlpha"]) ? $Format["BorderAlpha"] : 30;
        $BorderSize = isset($Format["BorderSize"]) ? $Format["BorderSize"] : 2;
        $Surrounding = isset($Format["Surrounding"]) ? $Format["Surrounding"] : null;
        $DisplayValues = isset($Format["DisplayValues"]) ? $Format["DisplayValues"] : \false;
        $DisplayOffset = isset($Format["DisplayOffset"]) ? $Format["DisplayOffset"] : 4;
        $DisplayColor = isset($Format["DisplayColor"]) ? $Format["DisplayColor"] : DISPLAY_MANUAL;
        $DisplayR = isset($Format["DisplayR"]) ? $Format["DisplayR"] : 0;
        $DisplayG = isset($Format["DisplayG"]) ? $Format["DisplayG"] : 0;
        $DisplayB = isset($Format["DisplayB"]) ? $Format["DisplayB"] : 0;
        $RecordImageMap = isset($Format["RecordImageMap"]) ? $Format["RecordImageMap"] : \false;
        $this->LastChartLayout = CHART_LAST_LAYOUT_REGULAR;
        $Data = $this->DataSet->getData();
        list($XMargin, $XDivs) = $this->scaleGetXSettings();
        foreach ($Data["Series"] as $SerieName => $Serie) {
            if ($Serie["isDrawable"] == \true && $SerieName != $Data["Abscissa"]) {
                if (isset($Serie["Weight"])) {
                    $SerieWeight = $Serie["Weight"] + 2;
                } else {
                    $SerieWeight = 2;
                }
                if ($PlotSize != null) {
                    $SerieWeight = $PlotSize;
                }
                $R = $Serie["Color"]["R"];
                $G = $Serie["Color"]["G"];
                $B = $Serie["Color"]["B"];
                $Alpha = (int) $Serie["Color"]["Alpha"];
                $Ticks = $Serie["Ticks"];
                if ($Surrounding != null) {
                    $BorderR = $R + $Surrounding;
                    $BorderG = $G + $Surrounding;
                    $BorderB = $B + $Surrounding;
                }
                if (isset($Serie["Picture"])) {
                    $Picture = $Serie["Picture"];
                    list($PicWidth, $PicHeight, $PicType) = $this->getPicInfo($Picture);
                } else {
                    $Picture = null;
                    $PicOffset = 0;
                }
                if ($DisplayColor == DISPLAY_AUTO) {
                    $DisplayR = $R;
                    $DisplayG = $G;
                    $DisplayB = $B;
                }
                $AxisID = $Serie["Axis"];
                $Shape = $Serie["Shape"];
                $Mode = $Data["Axis"][$AxisID]["Display"];
                $Format = $Data["Axis"][$AxisID]["Format"];
                $Unit = $Data["Axis"][$AxisID]["Unit"];
                if (isset($Serie["Description"])) {
                    $SerieDescription = $Serie["Description"];
                } else {
                    $SerieDescription = $SerieName;
                }
                $PosArray = $this->scaleComputeY($Serie["Data"], ["AxisID" => $Serie["Axis"]]);
                $this->DataSet->Data["Series"][$SerieName]["XOffset"] = 0;
                if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
                    if ($XDivs == 0) {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1) / 4;
                    } else {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
                    }
                    if ($Picture != null) {
                        $PicOffset = $PicHeight / 2;
                        $SerieWeight = 0;
                    }
                    $X = $this->GraphAreaX1 + $XMargin;
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    foreach ($PosArray as $Key => $Y) {
                        if ($DisplayValues) {
                            $this->drawText($X, $Y - $DisplayOffset - $SerieWeight - $BorderSize - $PicOffset, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit), ["R" => $DisplayR, "G" => $DisplayG, "B" => $DisplayB, "Align" => TEXT_ALIGN_BOTTOMMIDDLE]);
                        }
                        if ($Y != VOID) {
                            if ($RecordImageMap) {
                                $this->addToImageMap("CIRCLE", floor($X) . "," . floor($Y) . "," . $SerieWeight, $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
                            }
                            if ($Picture != null) {
                                $this->drawFromPicture($PicType, $Picture, $X - $PicWidth / 2, $Y - $PicHeight / 2);
                            } else {
                                $this->drawShape($X, $Y, $Shape, $SerieWeight, $PlotBorder, $BorderSize, $R, $G, $B, $Alpha, $BorderR, $BorderG, $BorderB, $BorderAlpha);
                            }
                        }
                        $X = $X + $XStep;
                    }
                } else {
                    if ($XDivs == 0) {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1) / 4;
                    } else {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
                    }
                    if ($Picture != null) {
                        $PicOffset = $PicWidth / 2;
                        $SerieWeight = 0;
                    }
                    $Y = $this->GraphAreaY1 + $XMargin;
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    foreach ($PosArray as $Key => $X) {
                        if ($DisplayValues) {
                            $this->drawText($X + $DisplayOffset + $SerieWeight + $BorderSize + $PicOffset, $Y, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit), ["Angle" => 270, "R" => $DisplayR, "G" => $DisplayG, "B" => $DisplayB, "Align" => TEXT_ALIGN_BOTTOMMIDDLE]);
                        }
                        if ($X != VOID) {
                            if ($RecordImageMap) {
                                $this->addToImageMap("CIRCLE", floor($X) . "," . floor($Y) . "," . $SerieWeight, $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
                            }
                            if ($Picture != null) {
                                $this->drawFromPicture($PicType, $Picture, $X - $PicWidth / 2, $Y - $PicHeight / 2);
                            } else {
                                $this->drawShape($X, $Y, $Shape, $SerieWeight, $PlotBorder, $BorderSize, $R, $G, $B, $Alpha, $BorderR, $BorderG, $BorderB, $BorderAlpha);
                            }
                        }
                        $Y = $Y + $YStep;
                    }
                }
            }
        }
    }
    /**
     * Draw a spline chart
     * @param array $Format
     */
    public function drawSplineChart(array $Format = [])
    {
        $BreakVoid = isset($Format["BreakVoid"]) ? $Format["BreakVoid"] : \true;
        $VoidTicks = isset($Format["VoidTicks"]) ? $Format["VoidTicks"] : 4;
        $BreakR = isset($Format["BreakR"]) ? $Format["BreakR"] : null;
        // 234
        $BreakG = isset($Format["BreakG"]) ? $Format["BreakG"] : null;
        // 55
        $BreakB = isset($Format["BreakB"]) ? $Format["BreakB"] : null;
        // 26
        $DisplayValues = isset($Format["DisplayValues"]) ? $Format["DisplayValues"] : \false;
        $DisplayOffset = isset($Format["DisplayOffset"]) ? $Format["DisplayOffset"] : 2;
        $DisplayColor = isset($Format["DisplayColor"]) ? $Format["DisplayColor"] : DISPLAY_MANUAL;
        $DisplayR = isset($Format["DisplayR"]) ? $Format["DisplayR"] : 0;
        $DisplayG = isset($Format["DisplayG"]) ? $Format["DisplayG"] : 0;
        $DisplayB = isset($Format["DisplayB"]) ? $Format["DisplayB"] : 0;
        $RecordImageMap = isset($Format["RecordImageMap"]) ? $Format["RecordImageMap"] : \false;
        $ImageMapPlotSize = isset($Format["ImageMapPlotSize"]) ? $Format["ImageMapPlotSize"] : 5;
        $this->LastChartLayout = CHART_LAST_LAYOUT_REGULAR;
        $Data = $this->DataSet->getData();
        list($XMargin, $XDivs) = $this->scaleGetXSettings();
        foreach ($Data["Series"] as $SerieName => $Serie) {
            if ($Serie["isDrawable"] == \true && $SerieName != $Data["Abscissa"]) {
                $R = $Serie["Color"]["R"];
                $G = $Serie["Color"]["G"];
                $B = $Serie["Color"]["B"];
                $Alpha = $Serie["Color"]["Alpha"];
                $Ticks = $Serie["Ticks"];
                $Weight = $Serie["Weight"];
                if ($BreakR == null) {
                    $BreakSettings = ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "Ticks" => $VoidTicks];
                } else {
                    $BreakSettings = ["R" => $BreakR, "G" => $BreakG, "B" => $BreakB, "Alpha" => $Alpha, "Ticks" => $VoidTicks, "Weight" => $Weight];
                }
                if ($DisplayColor == DISPLAY_AUTO) {
                    $DisplayR = $R;
                    $DisplayG = $G;
                    $DisplayB = $B;
                }
                $AxisID = $Serie["Axis"];
                $Mode = $Data["Axis"][$AxisID]["Display"];
                $Format = $Data["Axis"][$AxisID]["Format"];
                $Unit = $Data["Axis"][$AxisID]["Unit"];
                if (isset($Serie["Description"])) {
                    $SerieDescription = $Serie["Description"];
                } else {
                    $SerieDescription = $SerieName;
                }
                $PosArray = $this->scaleComputeY($Serie["Data"], ["AxisID" => $Serie["Axis"]]);
                $this->DataSet->Data["Series"][$SerieName]["XOffset"] = 0;
                if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
                    if ($XDivs == 0) {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1) / 4;
                    } else {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
                    }
                    $X = $this->GraphAreaX1 + $XMargin;
                    $WayPoints = [];
                    $Force = $XStep / 5;
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    $LastGoodY = null;
                    $LastGoodX = null;
                    $LastX = 1;
                    $LastY = 1;
                    foreach ($PosArray as $Key => $Y) {
                        if ($DisplayValues) {
                            $this->drawText($X, $Y - $DisplayOffset, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit), ["R" => $DisplayR, "G" => $DisplayG, "B" => $DisplayB, "Align" => TEXT_ALIGN_BOTTOMMIDDLE]);
                        }
                        if ($RecordImageMap && $Y != VOID) {
                            $this->addToImageMap("CIRCLE", floor($X) . "," . floor($Y) . "," . $ImageMapPlotSize, $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
                        }
                        if ($Y == VOID && $LastY != null) {
                            $this->drawSpline($WayPoints, ["Force" => $Force, "R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "Ticks" => $Ticks, "Weight" => $Weight]);
                            $WayPoints = [];
                        }
                        if ($Y != VOID && $LastY == null && $LastGoodY != null && !$BreakVoid) {
                            $this->drawLine($LastGoodX, $LastGoodY, $X, $Y, $BreakSettings);
                        }
                        if ($Y != VOID) {
                            $WayPoints[] = [$X, $Y];
                        }
                        if ($Y != VOID) {
                            $LastGoodY = $Y;
                            $LastGoodX = $X;
                        }
                        if ($Y == VOID) {
                            $Y = null;
                        }
                        $LastX = $X;
                        $LastY = $Y;
                        $X = $X + $XStep;
                    }
                    $this->drawSpline($WayPoints, ["Force" => $Force, "R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "Ticks" => $Ticks, "Weight" => $Weight]);
                } else {
                    if ($XDivs == 0) {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1) / 4;
                    } else {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
                    }
                    $Y = $this->GraphAreaY1 + $XMargin;
                    $WayPoints = [];
                    $Force = $YStep / 5;
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    $LastGoodY = null;
                    $LastGoodX = null;
                    $LastX = 1;
                    $LastY = 1;
                    foreach ($PosArray as $Key => $X) {
                        if ($DisplayValues) {
                            $this->drawText($X + $DisplayOffset, $Y, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit), ["Angle" => 270, "R" => $DisplayR, "G" => $DisplayG, "B" => $DisplayB, "Align" => TEXT_ALIGN_BOTTOMMIDDLE]);
                        }
                        if ($RecordImageMap && $X != VOID) {
                            $this->addToImageMap("CIRCLE", floor($X) . "," . floor($Y) . "," . $ImageMapPlotSize, $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
                        }
                        if ($X == VOID && $LastX != null) {
                            $this->drawSpline($WayPoints, ["Force" => $Force, "R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "Ticks" => $Ticks, "Weight" => $Weight]);
                            $WayPoints = [];
                        }
                        if ($X != VOID && $LastX == null && $LastGoodX != null && !$BreakVoid) {
                            $this->drawLine($LastGoodX, $LastGoodY, $X, $Y, $BreakSettings);
                        }
                        if ($X != VOID) {
                            $WayPoints[] = [$X, $Y];
                        }
                        if ($X != VOID) {
                            $LastGoodX = $X;
                            $LastGoodY = $Y;
                        }
                        if ($X == VOID) {
                            $X = null;
                        }
                        $LastX = $X;
                        $LastY = $Y;
                        $Y = $Y + $YStep;
                    }
                    $this->drawSpline($WayPoints, ["Force" => $Force, "R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "Ticks" => $Ticks, "Weight" => $Weight]);
                }
            }
        }
    }
    /**
     * Draw a filled spline chart
     * @param array $Format
     */
    public function drawFilledSplineChart(array $Format = [])
    {
        $DisplayValues = isset($Format["DisplayValues"]) ? $Format["DisplayValues"] : \false;
        $DisplayOffset = isset($Format["DisplayOffset"]) ? $Format["DisplayOffset"] : 2;
        $DisplayColor = isset($Format["DisplayColor"]) ? $Format["DisplayColor"] : DISPLAY_MANUAL;
        $DisplayR = isset($Format["DisplayR"]) ? $Format["DisplayR"] : 0;
        $DisplayG = isset($Format["DisplayG"]) ? $Format["DisplayG"] : 0;
        $DisplayB = isset($Format["DisplayB"]) ? $Format["DisplayB"] : 0;
        $AroundZero = isset($Format["AroundZero"]) ? $Format["AroundZero"] : \true;
        $Threshold = isset($Format["Threshold"]) ? $Format["Threshold"] : null;
        $this->LastChartLayout = CHART_LAST_LAYOUT_REGULAR;
        $Data = $this->DataSet->getData();
        list($XMargin, $XDivs) = $this->scaleGetXSettings();
        foreach ($Data["Series"] as $SerieName => $Serie) {
            if ($Serie["isDrawable"] == \true && $SerieName != $Data["Abscissa"]) {
                $R = $Serie["Color"]["R"];
                $G = $Serie["Color"]["G"];
                $B = $Serie["Color"]["B"];
                $Alpha = $Serie["Color"]["Alpha"];
                $Ticks = $Serie["Ticks"];
                if ($DisplayColor == DISPLAY_AUTO) {
                    $DisplayR = $R;
                    $DisplayG = $G;
                    $DisplayB = $B;
                }
                $AxisID = $Serie["Axis"];
                $Mode = $Data["Axis"][$AxisID]["Display"];
                $Format = $Data["Axis"][$AxisID]["Format"];
                $Unit = $Data["Axis"][$AxisID]["Unit"];
                $PosArray = $this->scaleComputeY($Serie["Data"], ["AxisID" => $Serie["Axis"]]);
                if ($AroundZero) {
                    $YZero = $this->scaleComputeY(0, ["AxisID" => $Serie["Axis"]]);
                }
                if ($Threshold != null) {
                    foreach ($Threshold as $Key => $Params) {
                        $Threshold[$Key]["MinX"] = $this->scaleComputeY($Params["Min"], ["AxisID" => $Serie["Axis"]]);
                        $Threshold[$Key]["MaxX"] = $this->scaleComputeY($Params["Max"], ["AxisID" => $Serie["Axis"]]);
                    }
                }
                $this->DataSet->Data["Series"][$SerieName]["XOffset"] = 0;
                if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
                    if ($XDivs == 0) {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1) / 4;
                    } else {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
                    }
                    $X = $this->GraphAreaX1 + $XMargin;
                    $WayPoints = [];
                    $Force = $XStep / 5;
                    if (!$AroundZero) {
                        $YZero = $this->GraphAreaY2 - 1;
                    }
                    if ($YZero > $this->GraphAreaY2 - 1) {
                        $YZero = $this->GraphAreaY2 - 1;
                    }
                    if ($YZero < $this->GraphAreaY1 + 1) {
                        $YZero = $this->GraphAreaY1 + 1;
                    }
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    foreach ($PosArray as $Key => $Y) {
                        if ($DisplayValues) {
                            $this->drawText($X, $Y - $DisplayOffset, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit), ["R" => $DisplayR, "G" => $DisplayG, "B" => $DisplayB, "Align" => TEXT_ALIGN_BOTTOMMIDDLE]);
                        }
                        if ($Y == VOID) {
                            $Area = $this->drawSpline($WayPoints, ["Force" => $Force, "PathOnly" => \true]);
                            if (count($Area)) {
                                foreach ($Area as $key => $Points) {
                                    $Corners = [];
                                    $Corners[] = $Area[$key][0]["X"];
                                    $Corners[] = $YZero;
                                    foreach ($Points as $subKey => $Point) {
                                        if ($subKey == count($Points) - 1) {
                                            $Corners[] = $Point["X"] - 1;
                                        } else {
                                            $Corners[] = $Point["X"];
                                        }
                                        $Corners[] = $Point["Y"] + 1;
                                    }
                                    $Corners[] = $Points[$subKey]["X"] - 1;
                                    $Corners[] = $YZero;
                                    $this->drawPolygonChart($Corners, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha / 2, "NoBorder" => \true, "Threshold" => $Threshold]);
                                }
                                $this->drawSpline($WayPoints, ["Force" => $Force, "R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "Ticks" => $Ticks]);
                            }
                            $WayPoints = [];
                        } else {
                            $WayPoints[] = [$X, $Y - 0.5];
                            /* -.5 for AA visual fix */
                        }
                        $X = $X + $XStep;
                    }
                    $Area = $this->drawSpline($WayPoints, ["Force" => $Force, "PathOnly" => \true]);
                    if (count($Area)) {
                        foreach ($Area as $key => $Points) {
                            $Corners = [];
                            $Corners[] = $Area[$key][0]["X"];
                            $Corners[] = $YZero;
                            foreach ($Points as $subKey => $Point) {
                                if ($subKey == count($Points) - 1) {
                                    $Corners[] = $Point["X"] - 1;
                                } else {
                                    $Corners[] = $Point["X"];
                                }
                                $Corners[] = $Point["Y"] + 1;
                            }
                            $Corners[] = $Points[$subKey]["X"] - 1;
                            $Corners[] = $YZero;
                            $this->drawPolygonChart($Corners, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha / 2, "NoBorder" => \true, "Threshold" => $Threshold]);
                        }
                        $this->drawSpline($WayPoints, ["Force" => $Force, "R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "Ticks" => $Ticks]);
                    }
                } else {
                    if ($XDivs == 0) {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1) / 4;
                    } else {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
                    }
                    $Y = $this->GraphAreaY1 + $XMargin;
                    $WayPoints = [];
                    $Force = $YStep / 5;
                    if (!$AroundZero) {
                        $YZero = $this->GraphAreaX1 + 1;
                    }
                    if ($YZero > $this->GraphAreaX2 - 1) {
                        $YZero = $this->GraphAreaX2 - 1;
                    }
                    if ($YZero < $this->GraphAreaX1 + 1) {
                        $YZero = $this->GraphAreaX1 + 1;
                    }
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    foreach ($PosArray as $Key => $X) {
                        if ($DisplayValues) {
                            $this->drawText($X + $DisplayOffset, $Y, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit), ["Angle" => 270, "R" => $DisplayR, "G" => $DisplayG, "B" => $DisplayB, "Align" => TEXT_ALIGN_BOTTOMMIDDLE]);
                        }
                        if ($X == VOID) {
                            $Area = $this->drawSpline($WayPoints, ["Force" => $Force, "PathOnly" => \true]);
                            if (count($Area)) {
                                foreach ($Area as $key => $Points) {
                                    $Corners = [];
                                    $Corners[] = $YZero;
                                    $Corners[] = $Area[$key][0]["Y"];
                                    foreach ($Points as $subKey => $Point) {
                                        if ($subKey == count($Points) - 1) {
                                            $Corners[] = $Point["X"] - 1;
                                        } else {
                                            $Corners[] = $Point["X"];
                                        }
                                        $Corners[] = $Point["Y"];
                                    }
                                    $Corners[] = $YZero;
                                    $Corners[] = $Points[$subKey]["Y"] - 1;
                                    $this->drawPolygonChart($Corners, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha / 2, "NoBorder" => \true, "Threshold" => $Threshold]);
                                }
                                $this->drawSpline($WayPoints, ["Force" => $Force, "R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "Ticks" => $Ticks]);
                            }
                            $WayPoints = [];
                        } else {
                            $WayPoints[] = [$X, $Y];
                        }
                        $Y = $Y + $YStep;
                    }
                    $Area = $this->drawSpline($WayPoints, ["Force" => $Force, "PathOnly" => \true]);
                    if (count($Area)) {
                        foreach ($Area as $key => $Points) {
                            $Corners = [];
                            $Corners[] = $YZero;
                            $Corners[] = $Area[$key][0]["Y"];
                            foreach ($Points as $subKey => $Point) {
                                if ($subKey == count($Points) - 1) {
                                    $Corners[] = $Point["X"] - 1;
                                } else {
                                    $Corners[] = $Point["X"];
                                }
                                $Corners[] = $Point["Y"];
                            }
                            $Corners[] = $YZero;
                            $Corners[] = $Points[$subKey]["Y"] - 1;
                            $this->drawPolygonChart($Corners, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha / 2, "NoBorder" => \true, "Threshold" => $Threshold]);
                        }
                        $this->drawSpline($WayPoints, ["Force" => $Force, "R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "Ticks" => $Ticks]);
                    }
                }
            }
        }
    }
    /**
     * Draw a line chart
     * @param array $Format
     */
    public function drawLineChart(array $Format = [])
    {
        $BreakVoid = isset($Format["BreakVoid"]) ? $Format["BreakVoid"] : \true;
        $VoidTicks = isset($Format["VoidTicks"]) ? $Format["VoidTicks"] : 4;
        $BreakR = isset($Format["BreakR"]) ? $Format["BreakR"] : null;
        $BreakG = isset($Format["BreakG"]) ? $Format["BreakG"] : null;
        $BreakB = isset($Format["BreakB"]) ? $Format["BreakB"] : null;
        $DisplayValues = isset($Format["DisplayValues"]) ? $Format["DisplayValues"] : \false;
        $DisplayOffset = isset($Format["DisplayOffset"]) ? $Format["DisplayOffset"] : 2;
        $DisplayColor = isset($Format["DisplayColor"]) ? $Format["DisplayColor"] : DISPLAY_MANUAL;
        $DisplayR = isset($Format["DisplayR"]) ? $Format["DisplayR"] : 0;
        $DisplayG = isset($Format["DisplayG"]) ? $Format["DisplayG"] : 0;
        $DisplayB = isset($Format["DisplayB"]) ? $Format["DisplayB"] : 0;
        $RecordImageMap = isset($Format["RecordImageMap"]) ? $Format["RecordImageMap"] : \false;
        $ImageMapPlotSize = isset($Format["ImageMapPlotSize"]) ? $Format["ImageMapPlotSize"] : 5;
        $ForceColor = isset($Format["ForceColor"]) ? $Format["ForceColor"] : \false;
        $ForceR = isset($Format["ForceR"]) ? $Format["ForceR"] : 0;
        $ForceG = isset($Format["ForceG"]) ? $Format["ForceG"] : 0;
        $ForceB = isset($Format["ForceB"]) ? $Format["ForceB"] : 0;
        $ForceAlpha = isset($Format["ForceAlpha"]) ? $Format["ForceAlpha"] : 100;
        $this->LastChartLayout = CHART_LAST_LAYOUT_REGULAR;
        $Data = $this->DataSet->getData();
        list($XMargin, $XDivs) = $this->scaleGetXSettings();
        foreach ($Data["Series"] as $SerieName => $Serie) {
            if ($Serie["isDrawable"] == \true && $SerieName != $Data["Abscissa"]) {
                $R = $Serie["Color"]["R"];
                $G = $Serie["Color"]["G"];
                $B = $Serie["Color"]["B"];
                $Alpha = $Serie["Color"]["Alpha"];
                $Ticks = $Serie["Ticks"];
                $Weight = $Serie["Weight"];
                if ($ForceColor) {
                    $R = $ForceR;
                    $G = $ForceG;
                    $B = $ForceB;
                    $Alpha = $ForceAlpha;
                }
                if ($BreakR == null) {
                    $BreakSettings = ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "Ticks" => $VoidTicks, "Weight" => $Weight];
                } else {
                    $BreakSettings = ["R" => $BreakR, "G" => $BreakG, "B" => $BreakB, "Alpha" => $Alpha, "Ticks" => $VoidTicks, "Weight" => $Weight];
                }
                if ($DisplayColor == DISPLAY_AUTO) {
                    $DisplayR = $R;
                    $DisplayG = $G;
                    $DisplayB = $B;
                }
                $AxisID = $Serie["Axis"];
                $Mode = $Data["Axis"][$AxisID]["Display"];
                $Format = $Data["Axis"][$AxisID]["Format"];
                $Unit = $Data["Axis"][$AxisID]["Unit"];
                if (isset($Serie["Description"])) {
                    $SerieDescription = $Serie["Description"];
                } else {
                    $SerieDescription = $SerieName;
                }
                $PosArray = $this->scaleComputeY($Serie["Data"], ["AxisID" => $Serie["Axis"]]);
                $this->DataSet->Data["Series"][$SerieName]["XOffset"] = 0;
                if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
                    if ($XDivs == 0) {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1) / 4;
                    } else {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
                    }
                    $X = $this->GraphAreaX1 + $XMargin;
                    $LastX = null;
                    $LastY = null;
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    $LastGoodY = null;
                    $LastGoodX = null;
                    foreach ($PosArray as $Key => $Y) {
                        if ($DisplayValues && $Serie["Data"][$Key] != VOID) {
                            if ($Serie["Data"][$Key] > 0) {
                                $Align = TEXT_ALIGN_BOTTOMMIDDLE;
                                $Offset = $DisplayOffset;
                            } else {
                                $Align = TEXT_ALIGN_TOPMIDDLE;
                                $Offset = -$DisplayOffset;
                            }
                            $this->drawText($X, $Y - $Offset - $Weight, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit), ["R" => $DisplayR, "G" => $DisplayG, "B" => $DisplayB, "Align" => $Align]);
                        }
                        if ($RecordImageMap && $Y != VOID) {
                            $this->addToImageMap("CIRCLE", floor($X) . "," . floor($Y) . "," . $ImageMapPlotSize, $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
                        }
                        if ($Y != VOID && $LastX != null && $LastY != null) {
                            $this->drawLine($LastX, $LastY, $X, $Y, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "Ticks" => $Ticks, "Weight" => $Weight]);
                        }
                        if ($Y != VOID && $LastY == null && $LastGoodY != null && !$BreakVoid) {
                            $this->drawLine($LastGoodX, $LastGoodY, $X, $Y, $BreakSettings);
                            $LastGoodY = null;
                        }
                        if ($Y != VOID) {
                            $LastGoodY = $Y;
                            $LastGoodX = $X;
                        }
                        if ($Y == VOID) {
                            $Y = null;
                        }
                        $LastX = $X;
                        $LastY = $Y;
                        $X = $X + $XStep;
                    }
                } else {
                    if ($XDivs == 0) {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1) / 4;
                    } else {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
                    }
                    $Y = $this->GraphAreaY1 + $XMargin;
                    $LastX = null;
                    $LastY = null;
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    $LastGoodY = null;
                    $LastGoodX = null;
                    foreach ($PosArray as $Key => $X) {
                        if ($DisplayValues && $Serie["Data"][$Key] != VOID) {
                            $this->drawText($X + $DisplayOffset + $Weight, $Y, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit), ["Angle" => 270, "R" => $DisplayR, "G" => $DisplayG, "B" => $DisplayB, "Align" => TEXT_ALIGN_BOTTOMMIDDLE]);
                        }
                        if ($RecordImageMap && $X != VOID) {
                            $this->addToImageMap("CIRCLE", floor($X) . "," . floor($Y) . "," . $ImageMapPlotSize, $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
                        }
                        if ($X != VOID && $LastX != null && $LastY != null) {
                            $this->drawLine($LastX, $LastY, $X, $Y, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "Ticks" => $Ticks, "Weight" => $Weight]);
                        }
                        if ($X != VOID && $LastX == null && $LastGoodY != null && !$BreakVoid) {
                            $this->drawLine($LastGoodX, $LastGoodY, $X, $Y, $BreakSettings);
                            $LastGoodY = null;
                        }
                        if ($X != VOID) {
                            $LastGoodY = $Y;
                            $LastGoodX = $X;
                        }
                        if ($X == VOID) {
                            $X = null;
                        }
                        $LastX = $X;
                        $LastY = $Y;
                        $Y = $Y + $YStep;
                    }
                }
            }
        }
    }
    /**
     * Draw a zone chart
     *
     * @param string $SerieA
     * @param string $SerieB
     * @param array $Format
     * @return null|integer
     */
    public function drawZoneChart($SerieA, $SerieB, array $Format = [])
    {
        $AxisID = isset($Format["AxisID"]) ? $Format["AxisID"] : 0;
        $LineR = isset($Format["LineR"]) ? $Format["LineR"] : 150;
        $LineG = isset($Format["LineG"]) ? $Format["LineG"] : 150;
        $LineB = isset($Format["LineB"]) ? $Format["LineB"] : 150;
        $LineAlpha = isset($Format["LineAlpha"]) ? $Format["LineAlpha"] : 50;
        $LineTicks = isset($Format["LineTicks"]) ? $Format["LineTicks"] : 1;
        $AreaR = isset($Format["AreaR"]) ? $Format["AreaR"] : 150;
        $AreaG = isset($Format["AreaG"]) ? $Format["AreaG"] : 150;
        $AreaB = isset($Format["AreaB"]) ? $Format["AreaB"] : 150;
        $AreaAlpha = isset($Format["AreaAlpha"]) ? $Format["AreaAlpha"] : 5;
        $this->LastChartLayout = CHART_LAST_LAYOUT_REGULAR;
        $Data = $this->DataSet->getData();
        if (!isset($Data["Series"][$SerieA]["Data"]) || !isset($Data["Series"][$SerieB]["Data"])) {
            return 0;
        }
        $SerieAData = $Data["Series"][$SerieA]["Data"];
        $SerieBData = $Data["Series"][$SerieB]["Data"];
        list($XMargin, $XDivs) = $this->scaleGetXSettings();
        $Mode = $Data["Axis"][$AxisID]["Display"];
        $Format = $Data["Axis"][$AxisID]["Format"];
        $PosArrayA = $this->scaleComputeY($SerieAData, ["AxisID" => $AxisID]);
        $PosArrayB = $this->scaleComputeY($SerieBData, ["AxisID" => $AxisID]);
        if (count($PosArrayA) != count($PosArrayB)) {
            return 0;
        }
        if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
            if ($XDivs == 0) {
                $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1) / 4;
            } else {
                $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
            }
            $X = $this->GraphAreaX1 + $XMargin;
            $LastX = null;
            $LastY = null;
            $LastY1 = null;
            $LastY2 = null;
            $BoundsA = [];
            $BoundsB = [];
            foreach ($PosArrayA as $Key => $Y1) {
                $Y2 = $PosArrayB[$Key];
                $BoundsA[] = $X;
                $BoundsA[] = $Y1;
                $BoundsB[] = $X;
                $BoundsB[] = $Y2;
                $LastX = $X;
                $LastY1 = $Y1;
                $LastY2 = $Y2;
                $X = $X + $XStep;
            }
            $Bounds = array_merge($BoundsA, $this->reversePlots($BoundsB));
            $this->drawPolygonChart($Bounds, ["R" => $AreaR, "G" => $AreaG, "B" => $AreaB, "Alpha" => $AreaAlpha]);
            for ($i = 0; $i <= count($BoundsA) - 4; $i = $i + 2) {
                $this->drawLine($BoundsA[$i], $BoundsA[$i + 1], $BoundsA[$i + 2], $BoundsA[$i + 3], ["R" => $LineR, "G" => $LineG, "B" => $LineB, "Alpha" => $LineAlpha, "Ticks" => $LineTicks]);
                $this->drawLine($BoundsB[$i], $BoundsB[$i + 1], $BoundsB[$i + 2], $BoundsB[$i + 3], ["R" => $LineR, "G" => $LineG, "B" => $LineB, "Alpha" => $LineAlpha, "Ticks" => $LineTicks]);
            }
        } else {
            if ($XDivs == 0) {
                $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1) / 4;
            } else {
                $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
            }
            $Y = $this->GraphAreaY1 + $XMargin;
            $LastX = null;
            $LastY = null;
            $LastX1 = null;
            $LastX2 = null;
            $BoundsA = [];
            $BoundsB = [];
            foreach ($PosArrayA as $Key => $X1) {
                $X2 = $PosArrayB[$Key];
                $BoundsA[] = $X1;
                $BoundsA[] = $Y;
                $BoundsB[] = $X2;
                $BoundsB[] = $Y;
                $LastY = $Y;
                $LastX1 = $X1;
                $LastX2 = $X2;
                $Y = $Y + $YStep;
            }
            $Bounds = array_merge($BoundsA, $this->reversePlots($BoundsB));
            $this->drawPolygonChart($Bounds, ["R" => $AreaR, "G" => $AreaG, "B" => $AreaB, "Alpha" => $AreaAlpha]);
            for ($i = 0; $i <= count($BoundsA) - 4; $i = $i + 2) {
                $this->drawLine($BoundsA[$i], $BoundsA[$i + 1], $BoundsA[$i + 2], $BoundsA[$i + 3], ["R" => $LineR, "G" => $LineG, "B" => $LineB, "Alpha" => $LineAlpha, "Ticks" => $LineTicks]);
                $this->drawLine($BoundsB[$i], $BoundsB[$i + 1], $BoundsB[$i + 2], $BoundsB[$i + 3], ["R" => $LineR, "G" => $LineG, "B" => $LineB, "Alpha" => $LineAlpha, "Ticks" => $LineTicks]);
            }
        }
    }
    /**
     * Draw a step chart
     * @param array $Format
     */
    public function drawStepChart(array $Format = [])
    {
        $BreakVoid = isset($Format["BreakVoid"]) ? $Format["BreakVoid"] : \false;
        $ReCenter = isset($Format["ReCenter"]) ? $Format["ReCenter"] : \true;
        $VoidTicks = isset($Format["VoidTicks"]) ? $Format["VoidTicks"] : 4;
        $BreakR = isset($Format["BreakR"]) ? $Format["BreakR"] : null;
        $BreakG = isset($Format["BreakG"]) ? $Format["BreakG"] : null;
        $BreakB = isset($Format["BreakB"]) ? $Format["BreakB"] : null;
        $DisplayValues = isset($Format["DisplayValues"]) ? $Format["DisplayValues"] : \false;
        $DisplayOffset = isset($Format["DisplayOffset"]) ? $Format["DisplayOffset"] : 2;
        $DisplayColor = isset($Format["DisplayColor"]) ? $Format["DisplayColor"] : DISPLAY_MANUAL;
        $DisplayR = isset($Format["DisplayR"]) ? $Format["DisplayR"] : 0;
        $DisplayG = isset($Format["DisplayG"]) ? $Format["DisplayG"] : 0;
        $DisplayB = isset($Format["DisplayB"]) ? $Format["DisplayB"] : 0;
        $RecordImageMap = isset($Format["RecordImageMap"]) ? $Format["RecordImageMap"] : \false;
        $ImageMapPlotSize = isset($Format["ImageMapPlotSize"]) ? $Format["ImageMapPlotSize"] : 5;
        $this->LastChartLayout = CHART_LAST_LAYOUT_REGULAR;
        $Data = $this->DataSet->getData();
        list($XMargin, $XDivs) = $this->scaleGetXSettings();
        foreach ($Data["Series"] as $SerieName => $Serie) {
            if ($Serie["isDrawable"] == \true && $SerieName != $Data["Abscissa"]) {
                $R = $Serie["Color"]["R"];
                $G = $Serie["Color"]["G"];
                $B = $Serie["Color"]["B"];
                $Alpha = $Serie["Color"]["Alpha"];
                $Ticks = $Serie["Ticks"];
                $Weight = $Serie["Weight"];
                if (isset($Serie["Description"])) {
                    $SerieDescription = $Serie["Description"];
                } else {
                    $SerieDescription = $SerieName;
                }
                if ($BreakR == null) {
                    $BreakSettings = ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "Ticks" => $VoidTicks, "Weight" => $Weight];
                } else {
                    $BreakSettings = ["R" => $BreakR, "G" => $BreakG, "B" => $BreakB, "Alpha" => $Alpha, "Ticks" => $VoidTicks, "Weight" => $Weight];
                }
                if ($DisplayColor == DISPLAY_AUTO) {
                    $DisplayR = $R;
                    $DisplayG = $G;
                    $DisplayB = $B;
                }
                $AxisID = $Serie["Axis"];
                $Mode = $Data["Axis"][$AxisID]["Display"];
                $Format = $Data["Axis"][$AxisID]["Format"];
                $Unit = $Data["Axis"][$AxisID]["Unit"];
                $Color = ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "Ticks" => $Ticks, "Weight" => $Weight];
                $PosArray = $this->scaleComputeY($Serie["Data"], ["AxisID" => $Serie["Axis"]]);
                $this->DataSet->Data["Series"][$SerieName]["XOffset"] = 0;
                if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
                    if ($XDivs == 0) {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1) / 4;
                    } else {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
                    }
                    $X = $this->GraphAreaX1 + $XMargin;
                    $LastX = null;
                    $LastY = null;
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    $LastGoodY = null;
                    $LastGoodX = null;
                    $Init = \false;
                    foreach ($PosArray as $Key => $Y) {
                        if ($DisplayValues && $Serie["Data"][$Key] != VOID) {
                            if ($Y <= $LastY) {
                                $Align = TEXT_ALIGN_BOTTOMMIDDLE;
                                $Offset = $DisplayOffset;
                            } else {
                                $Align = TEXT_ALIGN_TOPMIDDLE;
                                $Offset = -$DisplayOffset;
                            }
                            $this->drawText($X, $Y - $Offset - $Weight, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit), ["R" => $DisplayR, "G" => $DisplayG, "B" => $DisplayB, "Align" => $Align]);
                        }
                        if ($Y != VOID && $LastX != null && $LastY != null) {
                            $this->drawLine($LastX, $LastY, $X, $LastY, $Color);
                            $this->drawLine($X, $LastY, $X, $Y, $Color);
                            if ($ReCenter && $X + $XStep < $this->GraphAreaX2 - $XMargin) {
                                $this->drawLine($X, $Y, $X + $XStep, $Y, $Color);
                                if ($RecordImageMap) {
                                    $this->addToImageMap("RECT", sprintf('%s,%s,%s,%s', floor($X - $ImageMapPlotSize), floor($Y - $ImageMapPlotSize), floor($X + $XStep + $ImageMapPlotSize), floor($Y + $ImageMapPlotSize)), $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
                                }
                            } else {
                                if ($RecordImageMap) {
                                    $this->addToImageMap("RECT", sprintf('%s,%s,%s,%s', floor($LastX - $ImageMapPlotSize), floor($LastY - $ImageMapPlotSize), floor($X + $ImageMapPlotSize), floor($LastY + $ImageMapPlotSize)), $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
                                }
                            }
                        }
                        if ($Y != VOID && $LastY == null && $LastGoodY != null && !$BreakVoid) {
                            if ($ReCenter) {
                                $this->drawLine($LastGoodX + $XStep, $LastGoodY, $X, $LastGoodY, $BreakSettings);
                                if ($RecordImageMap) {
                                    $this->addToImageMap("RECT", sprintf('%s,%s,%s,%s', floor($LastGoodX + $XStep - $ImageMapPlotSize), floor($LastGoodY - $ImageMapPlotSize), floor($X + $ImageMapPlotSize), floor($LastGoodY + $ImageMapPlotSize)), $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
                                }
                            } else {
                                $this->drawLine($LastGoodX, $LastGoodY, $X, $LastGoodY, $BreakSettings);
                                if ($RecordImageMap) {
                                    $this->addToImageMap("RECT", sprintf('%s,%s,%s,%s', floor($LastGoodX - $ImageMapPlotSize), floor($LastGoodY - $ImageMapPlotSize), floor($X + $ImageMapPlotSize), floor($LastGoodY + $ImageMapPlotSize)), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
                                }
                            }
                            $this->drawLine($X, $LastGoodY, $X, $Y, $BreakSettings);
                            $LastGoodY = null;
                        } elseif (!$BreakVoid && $LastGoodY == null && $Y != VOID) {
                            $this->drawLine($this->GraphAreaX1 + $XMargin, $Y, $X, $Y, $BreakSettings);
                            if ($RecordImageMap) {
                                $this->addToImageMap("RECT", sprintf('%s,%s,%s,%s', floor($this->GraphAreaX1 + $XMargin - $ImageMapPlotSize), floor($Y - $ImageMapPlotSize), floor($X + $ImageMapPlotSize), floor($Y + $ImageMapPlotSize)), $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
                            }
                        }
                        if ($Y != VOID) {
                            $LastGoodY = $Y;
                            $LastGoodX = $X;
                        }
                        if ($Y == VOID) {
                            $Y = null;
                        }
                        if (!$Init && $ReCenter) {
                            $X = $X - $XStep / 2;
                            $Init = \true;
                        }
                        $LastX = $X;
                        $LastY = $Y;
                        if ($LastX < $this->GraphAreaX1 + $XMargin) {
                            $LastX = $this->GraphAreaX1 + $XMargin;
                        }
                        $X = $X + $XStep;
                    }
                    if ($ReCenter) {
                        $this->drawLine($LastX, $LastY, $this->GraphAreaX2 - $XMargin, $LastY, $Color);
                        if ($RecordImageMap) {
                            $this->addToImageMap("RECT", sprintf('%s,%s,%s,%s', floor($LastX - $ImageMapPlotSize), floor($LastY - $ImageMapPlotSize), floor($this->GraphAreaX2 - $XMargin + $ImageMapPlotSize), floor($LastY + $ImageMapPlotSize)), $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
                        }
                    }
                } else {
                    if ($XDivs == 0) {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1) / 4;
                    } else {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
                    }
                    $Y = $this->GraphAreaY1 + $XMargin;
                    $LastX = null;
                    $LastY = null;
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    $LastGoodY = null;
                    $LastGoodX = null;
                    $Init = \false;
                    foreach ($PosArray as $Key => $X) {
                        if ($DisplayValues && $Serie["Data"][$Key] != VOID) {
                            if ($X >= $LastX) {
                                $Align = TEXT_ALIGN_MIDDLELEFT;
                                $Offset = $DisplayOffset;
                            } else {
                                $Align = TEXT_ALIGN_MIDDLERIGHT;
                                $Offset = -$DisplayOffset;
                            }
                            $this->drawText($X + $Offset + $Weight, $Y, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit), ["R" => $DisplayR, "G" => $DisplayG, "B" => $DisplayB, "Align" => $Align]);
                        }
                        if ($X != VOID && $LastX != null && $LastY != null) {
                            $this->drawLine($LastX, $LastY, $LastX, $Y, $Color);
                            $this->drawLine($LastX, $Y, $X, $Y, $Color);
                            if ($RecordImageMap) {
                                $this->addToImageMap("RECT", sprintf('%s,%s,%s,%s', floor($LastX - $ImageMapPlotSize), floor($LastY - $ImageMapPlotSize), floor($LastX + $XStep + $ImageMapPlotSize), floor($Y + $ImageMapPlotSize)), $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
                            }
                        }
                        if ($X != VOID && $LastX == null && $LastGoodY != null && !$BreakVoid) {
                            $this->drawLine($LastGoodX, $LastGoodY, $LastGoodX, $LastGoodY + $YStep, $Color);
                            if ($RecordImageMap) {
                                $this->addToImageMap("RECT", sprintf('%s,%s,%s,%s', floor($LastGoodX - $ImageMapPlotSize), floor($LastGoodY - $ImageMapPlotSize), floor($LastGoodX + $ImageMapPlotSize), floor($LastGoodY + $YStep + $ImageMapPlotSize)), $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
                            }
                            $this->drawLine($LastGoodX, $LastGoodY + $YStep, $LastGoodX, $Y, $BreakSettings);
                            if ($RecordImageMap) {
                                $this->addToImageMap("RECT", sprintf('%s,%s,%s,%s', floor($LastGoodX - $ImageMapPlotSize), floor($LastGoodY + $YStep - $ImageMapPlotSize), floor($LastGoodX + $ImageMapPlotSize), floor($YStep + $ImageMapPlotSize)), $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
                            }
                            $this->drawLine($LastGoodX, $Y, $X, $Y, $BreakSettings);
                            $LastGoodY = null;
                        } elseif ($X != VOID && $LastGoodY == null && !$BreakVoid) {
                            $this->drawLine($X, $this->GraphAreaY1 + $XMargin, $X, $Y, $BreakSettings);
                            if ($RecordImageMap) {
                                $this->addToImageMap("RECT", sprintf('%s,%s,%s,%s', floor($X - $ImageMapPlotSize), floor($this->GraphAreaY1 + $XMargin - $ImageMapPlotSize), floor($X + $ImageMapPlotSize), floor($Y + $ImageMapPlotSize)), $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
                            }
                        }
                        if ($X != VOID) {
                            $LastGoodY = $Y;
                            $LastGoodX = $X;
                        }
                        if ($X == VOID) {
                            $X = null;
                        }
                        if (!$Init && $ReCenter) {
                            $Y = $Y - $YStep / 2;
                            $Init = \true;
                        }
                        $LastX = $X;
                        $LastY = $Y;
                        if ($LastY < $this->GraphAreaY1 + $XMargin) {
                            $LastY = $this->GraphAreaY1 + $XMargin;
                        }
                        $Y = $Y + $YStep;
                    }
                    if ($ReCenter) {
                        $this->drawLine($LastX, $LastY, $LastX, $this->GraphAreaY2 - $XMargin, $Color);
                        if ($RecordImageMap) {
                            $this->addToImageMap("RECT", sprintf('%s,%s,%s,%s', floor($LastX - $ImageMapPlotSize), floor($LastY - $ImageMapPlotSize), floor($LastX + $ImageMapPlotSize), floor($this->GraphAreaY2 - $XMargin + $ImageMapPlotSize)), $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
                        }
                    }
                }
            }
        }
    }
    /**
     * Draw a step chart
     * @param array $Format
     */
    public function drawFilledStepChart(array $Format = [])
    {
        $ReCenter = isset($Format["ReCenter"]) ? $Format["ReCenter"] : \true;
        $DisplayValues = isset($Format["DisplayValues"]) ? $Format["DisplayValues"] : \false;
        $DisplayOffset = isset($Format["DisplayOffset"]) ? $Format["DisplayOffset"] : 2;
        $DisplayColor = isset($Format["DisplayColor"]) ? $Format["DisplayColor"] : DISPLAY_MANUAL;
        $ForceTransparency = isset($Format["ForceTransparency"]) ? $Format["ForceTransparency"] : null;
        $DisplayR = isset($Format["DisplayR"]) ? $Format["DisplayR"] : 0;
        $DisplayG = isset($Format["DisplayG"]) ? $Format["DisplayG"] : 0;
        $DisplayB = isset($Format["DisplayB"]) ? $Format["DisplayB"] : 0;
        $AroundZero = isset($Format["AroundZero"]) ? $Format["AroundZero"] : \true;
        $this->LastChartLayout = CHART_LAST_LAYOUT_REGULAR;
        $Data = $this->DataSet->getData();
        list($XMargin, $XDivs) = $this->scaleGetXSettings();
        foreach ($Data["Series"] as $SerieName => $Serie) {
            if ($Serie["isDrawable"] == \true && $SerieName != $Data["Abscissa"]) {
                $R = $Serie["Color"]["R"];
                $G = $Serie["Color"]["G"];
                $B = $Serie["Color"]["B"];
                $Alpha = $Serie["Color"]["Alpha"];
                if ($DisplayColor == DISPLAY_AUTO) {
                    $DisplayR = $R;
                    $DisplayG = $G;
                    $DisplayB = $B;
                }
                $AxisID = $Serie["Axis"];
                $Format = $Data["Axis"][$AxisID]["Format"];
                $Color = ["R" => $R, "G" => $G, "B" => $B];
                if ($ForceTransparency != null) {
                    $Color["Alpha"] = $ForceTransparency;
                } else {
                    $Color["Alpha"] = $Alpha;
                }
                $PosArray = $this->scaleComputeY($Serie["Data"], ["AxisID" => $Serie["Axis"]]);
                $YZero = $this->scaleComputeY(0, ["AxisID" => $Serie["Axis"]]);
                $this->DataSet->Data["Series"][$SerieName]["XOffset"] = 0;
                if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
                    if ($YZero > $this->GraphAreaY2 - 1) {
                        $YZero = $this->GraphAreaY2 - 1;
                    }
                    if ($YZero < $this->GraphAreaY1 + 1) {
                        $YZero = $this->GraphAreaY1 + 1;
                    }
                    if ($XDivs == 0) {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1) / 4;
                    } else {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
                    }
                    $X = $this->GraphAreaX1 + $XMargin;
                    $LastX = null;
                    $LastY = null;
                    if (!$AroundZero) {
                        $YZero = $this->GraphAreaY2 - 1;
                    }
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    $LastGoodY = null;
                    $LastGoodX = null;
                    $Points = [];
                    $Init = \false;
                    foreach ($PosArray as $Key => $Y) {
                        if ($Y == VOID && $LastX != null && $LastY != null && count($Points)) {
                            $Points[] = $LastX;
                            $Points[] = $LastY;
                            $Points[] = $X;
                            $Points[] = $LastY;
                            $Points[] = $X;
                            $Points[] = $YZero;
                            $this->drawPolygon($Points, $Color);
                            $Points = [];
                        }
                        if ($Y != VOID && $LastX != null && $LastY != null) {
                            if (count($Points)) {
                                $Points[] = $LastX;
                                $Points[] = $YZero;
                            }
                            $Points[] = $LastX;
                            $Points[] = $LastY;
                            $Points[] = $X;
                            $Points[] = $LastY;
                            $Points[] = $X;
                            $Points[] = $Y;
                        }
                        if ($Y != VOID) {
                            $LastGoodY = $Y;
                            $LastGoodX = $X;
                        }
                        if ($Y == VOID) {
                            $Y = null;
                        }
                        if (!$Init && $ReCenter) {
                            $X = $X - $XStep / 2;
                            $Init = \true;
                        }
                        $LastX = $X;
                        $LastY = $Y;
                        if ($LastX < $this->GraphAreaX1 + $XMargin) {
                            $LastX = $this->GraphAreaX1 + $XMargin;
                        }
                        $X = $X + $XStep;
                    }
                    if ($ReCenter) {
                        $Points[] = $LastX + $XStep / 2;
                        $Points[] = $LastY;
                        $Points[] = $LastX + $XStep / 2;
                        $Points[] = $YZero;
                    } else {
                        $Points[] = $LastX;
                        $Points[] = $YZero;
                    }
                    $this->drawPolygon($Points, $Color);
                } else {
                    if ($YZero < $this->GraphAreaX1 + 1) {
                        $YZero = $this->GraphAreaX1 + 1;
                    }
                    if ($YZero > $this->GraphAreaX2 - 1) {
                        $YZero = $this->GraphAreaX2 - 1;
                    }
                    if ($XDivs == 0) {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1) / 4;
                    } else {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
                    }
                    $Y = $this->GraphAreaY1 + $XMargin;
                    $LastX = null;
                    $LastY = null;
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    $LastGoodY = null;
                    $LastGoodX = null;
                    $Points = [];
                    foreach ($PosArray as $Key => $X) {
                        if ($X == VOID && $LastX != null && $LastY != null && count($Points)) {
                            $Points[] = $LastX;
                            $Points[] = $LastY;
                            $Points[] = $LastX;
                            $Points[] = $Y;
                            $Points[] = $YZero;
                            $Points[] = $Y;
                            $this->drawPolygon($Points, $Color);
                            $Points = [];
                        }
                        if ($X != VOID && $LastX != null && $LastY != null) {
                            if (count($Points)) {
                                $Points[] = $YZero;
                                $Points[] = $LastY;
                            }
                            $Points[] = $LastX;
                            $Points[] = $LastY;
                            $Points[] = $LastX;
                            $Points[] = $Y;
                            $Points[] = $X;
                            $Points[] = $Y;
                        }
                        if ($X != VOID) {
                            $LastGoodY = $Y;
                            $LastGoodX = $X;
                        }
                        if ($X == VOID) {
                            $X = null;
                        }
                        if ($LastX == null && $ReCenter) {
                            $Y = $Y - $YStep / 2;
                        }
                        $LastX = $X;
                        $LastY = $Y;
                        if ($LastY < $this->GraphAreaY1 + $XMargin) {
                            $LastY = $this->GraphAreaY1 + $XMargin;
                        }
                        $Y = $Y + $YStep;
                    }
                    if ($ReCenter) {
                        $Points[] = $LastX;
                        $Points[] = $LastY + $YStep / 2;
                        $Points[] = $YZero;
                        $Points[] = $LastY + $YStep / 2;
                    } else {
                        $Points[] = $YZero;
                        $Points[] = $LastY;
                    }
                    $this->drawPolygon($Points, $Color);
                }
            }
        }
    }
    /**
     * Draw an area chart
     * @param array $Format
     */
    public function drawAreaChart(array $Format = [])
    {
        $DisplayValues = isset($Format["DisplayValues"]) ? $Format["DisplayValues"] : \false;
        $DisplayOffset = isset($Format["DisplayOffset"]) ? $Format["DisplayOffset"] : 2;
        $DisplayColor = isset($Format["DisplayColor"]) ? $Format["DisplayColor"] : DISPLAY_MANUAL;
        $DisplayR = isset($Format["DisplayR"]) ? $Format["DisplayR"] : 0;
        $DisplayG = isset($Format["DisplayG"]) ? $Format["DisplayG"] : 0;
        $DisplayB = isset($Format["DisplayB"]) ? $Format["DisplayB"] : 0;
        $ForceTransparency = isset($Format["ForceTransparency"]) ? $Format["ForceTransparency"] : 25;
        $AroundZero = isset($Format["AroundZero"]) ? $Format["AroundZero"] : \true;
        $Threshold = isset($Format["Threshold"]) ? $Format["Threshold"] : null;
        $this->LastChartLayout = CHART_LAST_LAYOUT_REGULAR;
        $Data = $this->DataSet->getData();
        list($XMargin, $XDivs) = $this->scaleGetXSettings();
        foreach ($Data["Series"] as $SerieName => $Serie) {
            if ($Serie["isDrawable"] == \true && $SerieName != $Data["Abscissa"]) {
                $R = $Serie["Color"]["R"];
                $G = $Serie["Color"]["G"];
                $B = $Serie["Color"]["B"];
                $Alpha = $Serie["Color"]["Alpha"];
                $Ticks = $Serie["Ticks"];
                if ($DisplayColor == DISPLAY_AUTO) {
                    $DisplayR = $R;
                    $DisplayG = $G;
                    $DisplayB = $B;
                }
                $AxisID = $Serie["Axis"];
                $Mode = $Data["Axis"][$AxisID]["Display"];
                $Format = $Data["Axis"][$AxisID]["Format"];
                $Unit = $Data["Axis"][$AxisID]["Unit"];
                $PosArray = $this->scaleComputeY($Serie["Data"], ["AxisID" => $Serie["Axis"]]);
                $YZero = $this->scaleComputeY(0, ["AxisID" => $Serie["Axis"]]);
                if ($Threshold != null) {
                    foreach ($Threshold as $Key => $Params) {
                        $Threshold[$Key]["MinX"] = $this->scaleComputeY($Params["Min"], ["AxisID" => $Serie["Axis"]]);
                        $Threshold[$Key]["MaxX"] = $this->scaleComputeY($Params["Max"], ["AxisID" => $Serie["Axis"]]);
                    }
                }
                $this->DataSet->Data["Series"][$SerieName]["XOffset"] = 0;
                if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
                    if ($YZero > $this->GraphAreaY2 - 1) {
                        $YZero = $this->GraphAreaY2 - 1;
                    }
                    $Areas = [];
                    $AreaID = 0;
                    $Areas[$AreaID][] = $this->GraphAreaX1 + $XMargin;
                    if ($AroundZero) {
                        $Areas[$AreaID][] = $YZero;
                    } else {
                        $Areas[$AreaID][] = $this->GraphAreaY2 - 1;
                    }
                    if ($XDivs == 0) {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1) / 4;
                    } else {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
                    }
                    $X = $this->GraphAreaX1 + $XMargin;
                    $LastX = null;
                    $LastY = null;
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    foreach ($PosArray as $Key => $Y) {
                        if ($DisplayValues && $Serie["Data"][$Key] != VOID) {
                            if ($Serie["Data"][$Key] > 0) {
                                $Align = TEXT_ALIGN_BOTTOMMIDDLE;
                                $Offset = $DisplayOffset;
                            } else {
                                $Align = TEXT_ALIGN_TOPMIDDLE;
                                $Offset = -$DisplayOffset;
                            }
                            $this->drawText($X, $Y - $Offset, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit), ["R" => $DisplayR, "G" => $DisplayG, "B" => $DisplayB, "Align" => $Align]);
                        }
                        if ($Y == VOID && isset($Areas[$AreaID])) {
                            if ($LastX == null) {
                                $Areas[$AreaID][] = $X;
                            } else {
                                $Areas[$AreaID][] = $LastX;
                            }
                            if ($AroundZero) {
                                $Areas[$AreaID][] = $YZero;
                            } else {
                                $Areas[$AreaID][] = $this->GraphAreaY2 - 1;
                            }
                            $AreaID++;
                        } elseif ($Y != VOID) {
                            if (!isset($Areas[$AreaID])) {
                                $Areas[$AreaID][] = $X;
                                if ($AroundZero) {
                                    $Areas[$AreaID][] = $YZero;
                                } else {
                                    $Areas[$AreaID][] = $this->GraphAreaY2 - 1;
                                }
                            }
                            $Areas[$AreaID][] = $X;
                            $Areas[$AreaID][] = $Y;
                        }
                        $LastX = $X;
                        $X = $X + $XStep;
                    }
                    $Areas[$AreaID][] = $LastX;
                    if ($AroundZero) {
                        $Areas[$AreaID][] = $YZero;
                    } else {
                        $Areas[$AreaID][] = $this->GraphAreaY2 - 1;
                    }
                    /* Handle shadows in the areas */
                    if ($this->Shadow) {
                        $ShadowArea = [];
                        foreach ($Areas as $Key => $Points) {
                            $ShadowArea[$Key] = [];
                            foreach ($Points as $Key2 => $Value) {
                                if ($Key2 % 2 == 0) {
                                    $ShadowArea[$Key][] = $Value + $this->ShadowX;
                                } else {
                                    $ShadowArea[$Key][] = $Value + $this->ShadowY;
                                }
                            }
                        }
                        foreach ($ShadowArea as $Key => $Points) {
                            $this->drawPolygonChart($Points, ["R" => $this->ShadowR, "G" => $this->ShadowG, "B" => $this->ShadowB, "Alpha" => $this->Shadowa]);
                        }
                    }
                    $Alpha = $ForceTransparency != null ? $ForceTransparency : $Alpha;
                    $Color = ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "Threshold" => $Threshold];
                    foreach ($Areas as $Key => $Points) {
                        $this->drawPolygonChart($Points, $Color);
                    }
                } else {
                    if ($YZero < $this->GraphAreaX1 + 1) {
                        $YZero = $this->GraphAreaX1 + 1;
                    }
                    if ($YZero > $this->GraphAreaX2 - 1) {
                        $YZero = $this->GraphAreaX2 - 1;
                    }
                    $Areas = [];
                    $AreaID = 0;
                    if ($AroundZero) {
                        $Areas[$AreaID][] = $YZero;
                    } else {
                        $Areas[$AreaID][] = $this->GraphAreaX1 + 1;
                    }
                    $Areas[$AreaID][] = $this->GraphAreaY1 + $XMargin;
                    if ($XDivs == 0) {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1) / 4;
                    } else {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
                    }
                    $Y = $this->GraphAreaY1 + $XMargin;
                    $LastX = null;
                    $LastY = null;
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    foreach ($PosArray as $Key => $X) {
                        if ($DisplayValues && $Serie["Data"][$Key] != VOID) {
                            if ($Serie["Data"][$Key] > 0) {
                                $Align = TEXT_ALIGN_BOTTOMMIDDLE;
                                $Offset = $DisplayOffset;
                            } else {
                                $Align = TEXT_ALIGN_TOPMIDDLE;
                                $Offset = -$DisplayOffset;
                            }
                            $this->drawText($X + $Offset, $Y, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit), ["Angle" => 270, "R" => $DisplayR, "G" => $DisplayG, "B" => $DisplayB, "Align" => $Align]);
                        }
                        if ($X == VOID && isset($Areas[$AreaID])) {
                            if ($AroundZero) {
                                $Areas[$AreaID][] = $YZero;
                            } else {
                                $Areas[$AreaID][] = $this->GraphAreaX1 + 1;
                            }
                            if ($LastY == null) {
                                $Areas[$AreaID][] = $Y;
                            } else {
                                $Areas[$AreaID][] = $LastY;
                            }
                            $AreaID++;
                        } elseif ($X != VOID) {
                            if (!isset($Areas[$AreaID])) {
                                if ($AroundZero) {
                                    $Areas[$AreaID][] = $YZero;
                                } else {
                                    $Areas[$AreaID][] = $this->GraphAreaX1 + 1;
                                }
                                $Areas[$AreaID][] = $Y;
                            }
                            $Areas[$AreaID][] = $X;
                            $Areas[$AreaID][] = $Y;
                        }
                        $LastX = $X;
                        $LastY = $Y;
                        $Y = $Y + $YStep;
                    }
                    if ($AroundZero) {
                        $Areas[$AreaID][] = $YZero;
                    } else {
                        $Areas[$AreaID][] = $this->GraphAreaX1 + 1;
                    }
                    $Areas[$AreaID][] = $LastY;
                    /* Handle shadows in the areas */
                    if ($this->Shadow) {
                        $ShadowArea = [];
                        foreach ($Areas as $Key => $Points) {
                            $ShadowArea[$Key] = [];
                            foreach ($Points as $Key2 => $Value) {
                                if ($Key2 % 2 == 0) {
                                    $ShadowArea[$Key][] = $Value + $this->ShadowX;
                                } else {
                                    $ShadowArea[$Key][] = $Value + $this->ShadowY;
                                }
                            }
                        }
                        foreach ($ShadowArea as $Key => $Points) {
                            $this->drawPolygonChart($Points, ["R" => $this->ShadowR, "G" => $this->ShadowG, "B" => $this->ShadowB, "Alpha" => $this->Shadowa]);
                        }
                    }
                    $Alpha = $ForceTransparency != null ? $ForceTransparency : $Alpha;
                    $Color = ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "Threshold" => $Threshold];
                    foreach ($Areas as $Key => $Points) {
                        $this->drawPolygonChart($Points, $Color);
                    }
                }
            }
        }
    }
    /**
     * Draw a bar chart
     * @param array $Format
     */
    public function drawBarChart(array $Format = [])
    {
        $Floating0Serie = isset($Format["Floating0Serie"]) ? $Format["Floating0Serie"] : null;
        $Floating0Value = isset($Format["Floating0Value"]) ? $Format["Floating0Value"] : null;
        $Draw0Line = isset($Format["Draw0Line"]) ? $Format["Draw0Line"] : \false;
        $DisplayValues = isset($Format["DisplayValues"]) ? $Format["DisplayValues"] : \false;
        $DisplayOffset = isset($Format["DisplayOffset"]) ? $Format["DisplayOffset"] : 2;
        $DisplayColor = isset($Format["DisplayColor"]) ? $Format["DisplayColor"] : DISPLAY_MANUAL;
        $DisplayFont = isset($Format["DisplayFont"]) ? $Format["DisplayFont"] : $this->FontName;
        $DisplaySize = isset($Format["DisplaySize"]) ? $Format["DisplaySize"] : $this->FontSize;
        $DisplayPos = isset($Format["DisplayPos"]) ? $Format["DisplayPos"] : LABEL_POS_OUTSIDE;
        $DisplayShadow = isset($Format["DisplayShadow"]) ? $Format["DisplayShadow"] : \true;
        $DisplayR = isset($Format["DisplayR"]) ? $Format["DisplayR"] : 0;
        $DisplayG = isset($Format["DisplayG"]) ? $Format["DisplayG"] : 0;
        $DisplayB = isset($Format["DisplayB"]) ? $Format["DisplayB"] : 0;
        $AroundZero = isset($Format["AroundZero"]) ? $Format["AroundZero"] : \true;
        $Interleave = isset($Format["Interleave"]) ? $Format["Interleave"] : 0.5;
        $Rounded = isset($Format["Rounded"]) ? $Format["Rounded"] : \false;
        $RoundRadius = isset($Format["RoundRadius"]) ? $Format["RoundRadius"] : 4;
        $Surrounding = isset($Format["Surrounding"]) ? $Format["Surrounding"] : null;
        $BorderR = isset($Format["BorderR"]) ? $Format["BorderR"] : -1;
        $BorderG = isset($Format["BorderG"]) ? $Format["BorderG"] : -1;
        $BorderB = isset($Format["BorderB"]) ? $Format["BorderB"] : -1;
        $Gradient = isset($Format["Gradient"]) ? $Format["Gradient"] : \false;
        $GradientMode = isset($Format["GradientMode"]) ? $Format["GradientMode"] : GRADIENT_SIMPLE;
        $GradientAlpha = isset($Format["GradientAlpha"]) ? $Format["GradientAlpha"] : 20;
        $GradientStartR = isset($Format["GradientStartR"]) ? $Format["GradientStartR"] : 255;
        $GradientStartG = isset($Format["GradientStartG"]) ? $Format["GradientStartG"] : 255;
        $GradientStartB = isset($Format["GradientStartB"]) ? $Format["GradientStartB"] : 255;
        $GradientEndR = isset($Format["GradientEndR"]) ? $Format["GradientEndR"] : 0;
        $GradientEndG = isset($Format["GradientEndG"]) ? $Format["GradientEndG"] : 0;
        $GradientEndB = isset($Format["GradientEndB"]) ? $Format["GradientEndB"] : 0;
        $TxtMargin = isset($Format["TxtMargin"]) ? $Format["TxtMargin"] : 6;
        $OverrideColors = isset($Format["OverrideColors"]) ? $Format["OverrideColors"] : null;
        $OverrideSurrounding = isset($Format["OverrideSurrounding"]) ? $Format["OverrideSurrounding"] : 30;
        $InnerSurrounding = isset($Format["InnerSurrounding"]) ? $Format["InnerSurrounding"] : null;
        $InnerBorderR = isset($Format["InnerBorderR"]) ? $Format["InnerBorderR"] : -1;
        $InnerBorderG = isset($Format["InnerBorderG"]) ? $Format["InnerBorderG"] : -1;
        $InnerBorderB = isset($Format["InnerBorderB"]) ? $Format["InnerBorderB"] : -1;
        $RecordImageMap = isset($Format["RecordImageMap"]) ? $Format["RecordImageMap"] : \false;
        $this->LastChartLayout = CHART_LAST_LAYOUT_REGULAR;
        $Data = $this->DataSet->getData();
        list($XMargin, $XDivs) = $this->scaleGetXSettings();
        if ($OverrideColors != null) {
            $OverrideColors = $this->validatePalette($OverrideColors, $OverrideSurrounding);
            $this->DataSet->saveExtendedData("Palette", $OverrideColors);
        }
        $RestoreShadow = $this->Shadow;
        $SeriesCount = $this->countDrawableSeries();
        $CurrentSerie = 0;
        foreach ($Data["Series"] as $SerieName => $Serie) {
            if ($Serie["isDrawable"] == \true && $SerieName != $Data["Abscissa"]) {
                $R = $Serie["Color"]["R"];
                $G = $Serie["Color"]["G"];
                $B = $Serie["Color"]["B"];
                $Alpha = $Serie["Color"]["Alpha"];
                $Ticks = $Serie["Ticks"];
                if ($DisplayColor == DISPLAY_AUTO) {
                    $DisplayR = $R;
                    $DisplayG = $G;
                    $DisplayB = $B;
                }
                if ($Surrounding != null) {
                    $BorderR = $R + $Surrounding;
                    $BorderG = $G + $Surrounding;
                    $BorderB = $B + $Surrounding;
                }
                if ($InnerSurrounding != null) {
                    $InnerBorderR = $R + $InnerSurrounding;
                    $InnerBorderG = $G + $InnerSurrounding;
                    $InnerBorderB = $B + $InnerSurrounding;
                }
                if ($InnerBorderR == -1) {
                    $InnerColor = null;
                } else {
                    $InnerColor = ["R" => $InnerBorderR, "G" => $InnerBorderG, "B" => $InnerBorderB];
                }
                $Color = ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "BorderR" => $BorderR, "BorderG" => $BorderG, "BorderB" => $BorderB];
                $AxisID = $Serie["Axis"];
                $Mode = $Data["Axis"][$AxisID]["Display"];
                $Format = $Data["Axis"][$AxisID]["Format"];
                $Unit = $Data["Axis"][$AxisID]["Unit"];
                if (isset($Serie["Description"])) {
                    $SerieDescription = $Serie["Description"];
                } else {
                    $SerieDescription = $SerieName;
                }
                $PosArray = $this->scaleComputeY($Serie["Data"], ["AxisID" => $Serie["Axis"]]);
                if ($Floating0Value != null) {
                    $YZero = $this->scaleComputeY($Floating0Value, ["AxisID" => $Serie["Axis"]]);
                } else {
                    $YZero = $this->scaleComputeY(0, ["AxisID" => $Serie["Axis"]]);
                }
                if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
                    if ($YZero > $this->GraphAreaY2 - 1) {
                        $YZero = $this->GraphAreaY2 - 1;
                    }
                    if ($YZero < $this->GraphAreaY1 + 1) {
                        $YZero = $this->GraphAreaY1 + 1;
                    }
                    if ($XDivs == 0) {
                        $XStep = 0;
                    } else {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
                    }
                    $X = $this->GraphAreaX1 + $XMargin;
                    if ($AroundZero) {
                        $Y1 = $YZero;
                    } else {
                        $Y1 = $this->GraphAreaY2 - 1;
                    }
                    if ($XDivs == 0) {
                        $XSize = ($this->GraphAreaX2 - $this->GraphAreaX1) / ($SeriesCount + $Interleave);
                    } else {
                        $XSize = $XStep / ($SeriesCount + $Interleave);
                    }
                    $XOffset = -($XSize * $SeriesCount) / 2 + $CurrentSerie * $XSize;
                    if ($X + $XOffset <= $this->GraphAreaX1) {
                        $XOffset = $this->GraphAreaX1 - $X + 1;
                    }
                    $this->DataSet->Data["Series"][$SerieName]["XOffset"] = $XOffset + $XSize / 2;
                    if ($Rounded || $BorderR != -1) {
                        $XSpace = 1;
                    } else {
                        $XSpace = 0;
                    }
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    $ID = 0;
                    foreach ($PosArray as $Key => $Y2) {
                        if ($Floating0Serie != null) {
                            if (isset($Data["Series"][$Floating0Serie]["Data"][$Key])) {
                                $Value = $Data["Series"][$Floating0Serie]["Data"][$Key];
                            } else {
                                $Value = 0;
                            }
                            $YZero = $this->scaleComputeY($Value, ["AxisID" => $Serie["Axis"]]);
                            if ($YZero > $this->GraphAreaY2 - 1) {
                                $YZero = $this->GraphAreaY2 - 1;
                            }
                            if ($YZero < $this->GraphAreaY1 + 1) {
                                $YZero = $this->GraphAreaY1 + 1;
                            }
                            if ($AroundZero) {
                                $Y1 = $YZero;
                            } else {
                                $Y1 = $this->GraphAreaY2 - 1;
                            }
                        }
                        if ($OverrideColors != null) {
                            if (isset($OverrideColors[$ID])) {
                                $Color = ["R" => $OverrideColors[$ID]["R"], "G" => $OverrideColors[$ID]["G"], "B" => $OverrideColors[$ID]["B"], "Alpha" => $OverrideColors[$ID]["Alpha"], "BorderR" => $OverrideColors[$ID]["BorderR"], "BorderG" => $OverrideColors[$ID]["BorderG"], "BorderB" => $OverrideColors[$ID]["BorderB"]];
                            } else {
                                $Color = $this->getRandomColor();
                            }
                        }
                        if ($Y2 != VOID) {
                            $BarHeight = $Y1 - $Y2;
                            if ($Serie["Data"][$Key] == 0) {
                                $this->drawLine($X + $XOffset + $XSpace, $Y1, $X + $XOffset + $XSize - $XSpace, $Y1, $Color);
                                if ($RecordImageMap) {
                                    $this->addToImageMap("RECT", sprintf("%s,%s,%s,%s", floor($X + $XOffset + $XSpace), floor($Y1 - 1), floor($X + $XOffset + $XSize - $XSpace), floor($Y1 + 1)), $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
                                }
                            } else {
                                if ($RecordImageMap) {
                                    $this->addToImageMap("RECT", sprintf("%s,%s,%s,%s", floor($X + $XOffset + $XSpace), floor($Y1), floor($X + $XOffset + $XSize - $XSpace), floor($Y2)), $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
                                }
                                if ($Rounded) {
                                    $this->drawRoundedFilledRectangle($X + $XOffset + $XSpace, $Y1, $X + $XOffset + $XSize - $XSpace, $Y2, $RoundRadius, $Color);
                                } else {
                                    $this->drawFilledRectangle($X + $XOffset + $XSpace, $Y1, $X + $XOffset + $XSize - $XSpace, $Y2, $Color);
                                    if ($InnerColor != null) {
                                        $this->drawRectangle($X + $XOffset + $XSpace + 1, min($Y1, $Y2) + 1, $X + $XOffset + $XSize - $XSpace - 1, max($Y1, $Y2) - 1, $InnerColor);
                                    }
                                    if ($Gradient) {
                                        $this->Shadow = \false;
                                        if ($GradientMode == GRADIENT_SIMPLE) {
                                            if ($Serie["Data"][$Key] >= 0) {
                                                $GradienColor = ["StartR" => $GradientStartR, "StartG" => $GradientStartG, "StartB" => $GradientStartB, "EndR" => $GradientEndR, "EndG" => $GradientEndG, "EndB" => $GradientEndB, "Alpha" => $GradientAlpha];
                                            } else {
                                                $GradienColor = ["StartR" => $GradientEndR, "StartG" => $GradientEndG, "StartB" => $GradientEndB, "EndR" => $GradientStartR, "EndG" => $GradientStartG, "EndB" => $GradientStartB, "Alpha" => $GradientAlpha];
                                            }
                                            $this->drawGradientArea($X + $XOffset + $XSpace, $Y1, $X + $XOffset + $XSize - $XSpace, $Y2, DIRECTION_VERTICAL, $GradienColor);
                                        } elseif ($GradientMode == GRADIENT_EFFECT_CAN) {
                                            $GradienColor1 = ["StartR" => $GradientEndR, "StartG" => $GradientEndG, "StartB" => $GradientEndB, "EndR" => $GradientStartR, "EndG" => $GradientStartG, "EndB" => $GradientStartB, "Alpha" => $GradientAlpha];
                                            $GradienColor2 = ["StartR" => $GradientStartR, "StartG" => $GradientStartG, "StartB" => $GradientStartB, "EndR" => $GradientEndR, "EndG" => $GradientEndG, "EndB" => $GradientEndB, "Alpha" => $GradientAlpha];
                                            $XSpan = floor($XSize / 3);
                                            $this->drawGradientArea($X + $XOffset + $XSpace, $Y1, $X + $XOffset + $XSpan - $XSpace, $Y2, DIRECTION_HORIZONTAL, $GradienColor1);
                                            $this->drawGradientArea($X + $XOffset + $XSpan + $XSpace, $Y1, $X + $XOffset + $XSize - $XSpace, $Y2, DIRECTION_HORIZONTAL, $GradienColor2);
                                        }
                                        $this->Shadow = $RestoreShadow;
                                    }
                                }
                                if ($Draw0Line) {
                                    $Line0Color = ["R" => 0, "G" => 0, "B" => 0, "Alpha" => 20];
                                    if (abs($Y1 - $Y2) > 3) {
                                        $Line0Width = 3;
                                    } else {
                                        $Line0Width = 1;
                                    }
                                    if ($Y1 - $Y2 < 0) {
                                        $Line0Width = -$Line0Width;
                                    }
                                    $this->drawFilledRectangle($X + $XOffset + $XSpace, floor($Y1), $X + $XOffset + $XSize - $XSpace, floor($Y1) - $Line0Width, $Line0Color);
                                    $this->drawLine($X + $XOffset + $XSpace, floor($Y1), $X + $XOffset + $XSize - $XSpace, floor($Y1), $Line0Color);
                                }
                            }
                            if ($DisplayValues && $Serie["Data"][$Key] != VOID) {
                                if ($DisplayShadow) {
                                    $this->Shadow = \true;
                                }
                                $Caption = $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit);
                                $TxtPos = $this->getTextBox(0, 0, $DisplayFont, $DisplaySize, 90, $Caption);
                                $TxtHeight = $TxtPos[0]["Y"] - $TxtPos[1]["Y"] + $TxtMargin;
                                if ($DisplayPos == LABEL_POS_INSIDE && abs($TxtHeight) < abs($BarHeight)) {
                                    $CenterX = ($X + $XOffset + $XSize - $XSpace - ($X + $XOffset + $XSpace)) / 2 + $X + $XOffset + $XSpace;
                                    $CenterY = ($Y2 - $Y1) / 2 + $Y1;
                                    $this->drawText($CenterX, $CenterY, $Caption, ["R" => $DisplayR, "G" => $DisplayG, "B" => $DisplayB, "Align" => TEXT_ALIGN_MIDDLEMIDDLE, "FontSize" => $DisplaySize, "Angle" => 90]);
                                } else {
                                    if ($Serie["Data"][$Key] >= 0) {
                                        $Align = TEXT_ALIGN_BOTTOMMIDDLE;
                                        $Offset = $DisplayOffset;
                                    } else {
                                        $Align = TEXT_ALIGN_TOPMIDDLE;
                                        $Offset = -$DisplayOffset;
                                    }
                                    $this->drawText($X + $XOffset + $XSize / 2, $Y2 - $Offset, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit), ["R" => $DisplayR, "G" => $DisplayG, "B" => $DisplayB, "Align" => $Align, "FontSize" => $DisplaySize]);
                                }
                                $this->Shadow = $RestoreShadow;
                            }
                        }
                        $X = $X + $XStep;
                        $ID++;
                    }
                } else {
                    if ($YZero < $this->GraphAreaX1 + 1) {
                        $YZero = $this->GraphAreaX1 + 1;
                    }
                    if ($YZero > $this->GraphAreaX2 - 1) {
                        $YZero = $this->GraphAreaX2 - 1;
                    }
                    if ($XDivs == 0) {
                        $YStep = 0;
                    } else {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
                    }
                    $Y = $this->GraphAreaY1 + $XMargin;
                    if ($AroundZero) {
                        $X1 = $YZero;
                    } else {
                        $X1 = $this->GraphAreaX1 + 1;
                    }
                    if ($XDivs == 0) {
                        $YSize = ($this->GraphAreaY2 - $this->GraphAreaY1) / ($SeriesCount + $Interleave);
                    } else {
                        $YSize = $YStep / ($SeriesCount + $Interleave);
                    }
                    $YOffset = -($YSize * $SeriesCount) / 2 + $CurrentSerie * $YSize;
                    if ($Y + $YOffset <= $this->GraphAreaY1) {
                        $YOffset = $this->GraphAreaY1 - $Y + 1;
                    }
                    $this->DataSet->Data["Series"][$SerieName]["XOffset"] = $YOffset + $YSize / 2;
                    if ($Rounded || $BorderR != -1) {
                        $YSpace = 1;
                    } else {
                        $YSpace = 0;
                    }
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    $ID = 0;
                    foreach ($PosArray as $Key => $X2) {
                        if ($Floating0Serie != null) {
                            if (isset($Data["Series"][$Floating0Serie]["Data"][$Key])) {
                                $Value = $Data["Series"][$Floating0Serie]["Data"][$Key];
                            } else {
                                $Value = 0;
                            }
                            $YZero = $this->scaleComputeY($Value, ["AxisID" => $Serie["Axis"]]);
                            if ($YZero < $this->GraphAreaX1 + 1) {
                                $YZero = $this->GraphAreaX1 + 1;
                            }
                            if ($YZero > $this->GraphAreaX2 - 1) {
                                $YZero = $this->GraphAreaX2 - 1;
                            }
                            if ($AroundZero) {
                                $X1 = $YZero;
                            } else {
                                $X1 = $this->GraphAreaX1 + 1;
                            }
                        }
                        if ($OverrideColors != null) {
                            if (isset($OverrideColors[$ID])) {
                                $Color = ["R" => $OverrideColors[$ID]["R"], "G" => $OverrideColors[$ID]["G"], "B" => $OverrideColors[$ID]["B"], "Alpha" => $OverrideColors[$ID]["Alpha"], "BorderR" => $OverrideColors[$ID]["BorderR"], "BorderG" => $OverrideColors[$ID]["BorderG"], "BorderB" => $OverrideColors[$ID]["BorderB"]];
                            } else {
                                $Color = $this->getRandomColor();
                            }
                        }
                        if ($X2 != VOID) {
                            $BarWidth = $X2 - $X1;
                            if ($Serie["Data"][$Key] == 0) {
                                $this->drawLine($X1, $Y + $YOffset + $YSpace, $X1, $Y + $YOffset + $YSize - $YSpace, $Color);
                                if ($RecordImageMap) {
                                    $this->addToImageMap("RECT", sprintf("%s,%s,%s,%s", floor($X1 - 1), floor($Y + $YOffset + $YSpace), floor($X1 + 1), floor($Y + $YOffset + $YSize - $YSpace)), $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
                                }
                            } else {
                                if ($RecordImageMap) {
                                    $this->addToImageMap("RECT", sprintf("%s,%s,%s,%s", floor($X1), floor($Y + $YOffset + $YSpace), floor($X2), floor($Y + $YOffset + $YSize - $YSpace)), $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
                                }
                                if ($Rounded) {
                                    $this->drawRoundedFilledRectangle($X1 + 1, $Y + $YOffset + $YSpace, $X2, $Y + $YOffset + $YSize - $YSpace, $RoundRadius, $Color);
                                } else {
                                    $this->drawFilledRectangle($X1, $Y + $YOffset + $YSpace, $X2, $Y + $YOffset + $YSize - $YSpace, $Color);
                                    if ($InnerColor != null) {
                                        $this->drawRectangle(min($X1, $X2) + 1, $Y + $YOffset + $YSpace + 1, max($X1, $X2) - 1, $Y + $YOffset + $YSize - $YSpace - 1, $InnerColor);
                                    }
                                    if ($Gradient) {
                                        $this->Shadow = \false;
                                        if ($GradientMode == GRADIENT_SIMPLE) {
                                            if ($Serie["Data"][$Key] >= 0) {
                                                $GradienColor = ["StartR" => $GradientStartR, "StartG" => $GradientStartG, "StartB" => $GradientStartB, "EndR" => $GradientEndR, "EndG" => $GradientEndG, "EndB" => $GradientEndB, "Alpha" => $GradientAlpha];
                                            } else {
                                                $GradienColor = ["StartR" => $GradientEndR, "StartG" => $GradientEndG, "StartB" => $GradientEndB, "EndR" => $GradientStartR, "EndG" => $GradientStartG, "EndB" => $GradientStartB, "Alpha" => $GradientAlpha];
                                            }
                                            $this->drawGradientArea($X1, $Y + $YOffset + $YSpace, $X2, $Y + $YOffset + $YSize - $YSpace, DIRECTION_HORIZONTAL, $GradienColor);
                                        } elseif ($GradientMode == GRADIENT_EFFECT_CAN) {
                                            $GradienColor1 = ["StartR" => $GradientEndR, "StartG" => $GradientEndG, "StartB" => $GradientEndB, "EndR" => $GradientStartR, "EndG" => $GradientStartG, "EndB" => $GradientStartB, "Alpha" => $GradientAlpha];
                                            $GradienColor2 = ["StartR" => $GradientStartR, "StartG" => $GradientStartG, "StartB" => $GradientStartB, "EndR" => $GradientEndR, "EndG" => $GradientEndG, "EndB" => $GradientEndB, "Alpha" => $GradientAlpha];
                                            $YSpan = floor($YSize / 3);
                                            $this->drawGradientArea($X1, $Y + $YOffset + $YSpace, $X2, $Y + $YOffset + $YSpan - $YSpace, DIRECTION_VERTICAL, $GradienColor1);
                                            $this->drawGradientArea($X1, $Y + $YOffset + $YSpan, $X2, $Y + $YOffset + $YSize - $YSpace, DIRECTION_VERTICAL, $GradienColor2);
                                        }
                                        $this->Shadow = $RestoreShadow;
                                    }
                                }
                                if ($Draw0Line) {
                                    $Line0Color = ["R" => 0, "G" => 0, "B" => 0, "Alpha" => 20];
                                    if (abs($X1 - $X2) > 3) {
                                        $Line0Width = 3;
                                    } else {
                                        $Line0Width = 1;
                                    }
                                    if ($X2 - $X1 < 0) {
                                        $Line0Width = -$Line0Width;
                                    }
                                    $this->drawFilledRectangle(floor($X1), $Y + $YOffset + $YSpace, floor($X1) + $Line0Width, $Y + $YOffset + $YSize - $YSpace, $Line0Color);
                                    $this->drawLine(floor($X1), $Y + $YOffset + $YSpace, floor($X1), $Y + $YOffset + $YSize - $YSpace, $Line0Color);
                                }
                            }
                            if ($DisplayValues && $Serie["Data"][$Key] != VOID) {
                                if ($DisplayShadow) {
                                    $this->Shadow = \true;
                                }
                                $Caption = $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit);
                                $TxtPos = $this->getTextBox(0, 0, $DisplayFont, $DisplaySize, 0, $Caption);
                                $TxtWidth = $TxtPos[1]["X"] - $TxtPos[0]["X"] + $TxtMargin;
                                if ($DisplayPos == LABEL_POS_INSIDE && abs($TxtWidth) < abs($BarWidth)) {
                                    $CenterX = ($X2 - $X1) / 2 + $X1;
                                    $CenterY = ($Y + $YOffset + $YSize - $YSpace - ($Y + $YOffset + $YSpace)) / 2 + ($Y + $YOffset + $YSpace);
                                    $this->drawText($CenterX, $CenterY, $Caption, ["R" => $DisplayR, "G" => $DisplayG, "B" => $DisplayB, "Align" => TEXT_ALIGN_MIDDLEMIDDLE, "FontSize" => $DisplaySize]);
                                } else {
                                    if ($Serie["Data"][$Key] >= 0) {
                                        $Align = TEXT_ALIGN_MIDDLELEFT;
                                        $Offset = $DisplayOffset;
                                    } else {
                                        $Align = TEXT_ALIGN_MIDDLERIGHT;
                                        $Offset = -$DisplayOffset;
                                    }
                                    $this->drawText($X2 + $Offset, $Y + $YOffset + $YSize / 2, $Caption, ["R" => $DisplayR, "G" => $DisplayG, "B" => $DisplayB, "Align" => $Align, "FontSize" => $DisplaySize]);
                                }
                                $this->Shadow = $RestoreShadow;
                            }
                        }
                        $Y = $Y + $YStep;
                        $ID++;
                    }
                }
                $CurrentSerie++;
            }
        }
    }
    /**
     * Draw a bar chart
     * @param array $Format
     */
    public function drawStackedBarChart(array $Format = [])
    {
        $DisplayValues = isset($Format["DisplayValues"]) ? $Format["DisplayValues"] : \false;
        $DisplayOrientation = isset($Format["DisplayOrientation"]) ? $Format["DisplayOrientation"] : ORIENTATION_AUTO;
        $DisplayRound = isset($Format["DisplayRound"]) ? $Format["DisplayRound"] : 0;
        $DisplayColor = isset($Format["DisplayColor"]) ? $Format["DisplayColor"] : DISPLAY_MANUAL;
        $DisplayFont = isset($Format["DisplayFont"]) ? $Format["DisplayFont"] : $this->FontName;
        $DisplaySize = isset($Format["DisplaySize"]) ? $Format["DisplaySize"] : $this->FontSize;
        $DisplayR = isset($Format["DisplayR"]) ? $Format["DisplayR"] : 0;
        $DisplayG = isset($Format["DisplayG"]) ? $Format["DisplayG"] : 0;
        $DisplayB = isset($Format["DisplayB"]) ? $Format["DisplayB"] : 0;
        $Interleave = isset($Format["Interleave"]) ? $Format["Interleave"] : 0.5;
        $Rounded = isset($Format["Rounded"]) ? $Format["Rounded"] : \false;
        $RoundRadius = isset($Format["RoundRadius"]) ? $Format["RoundRadius"] : 4;
        $Surrounding = isset($Format["Surrounding"]) ? $Format["Surrounding"] : null;
        $BorderR = isset($Format["BorderR"]) ? $Format["BorderR"] : -1;
        $BorderG = isset($Format["BorderG"]) ? $Format["BorderG"] : -1;
        $BorderB = isset($Format["BorderB"]) ? $Format["BorderB"] : -1;
        $Gradient = isset($Format["Gradient"]) ? $Format["Gradient"] : \false;
        $GradientMode = isset($Format["GradientMode"]) ? $Format["GradientMode"] : GRADIENT_SIMPLE;
        $GradientAlpha = isset($Format["GradientAlpha"]) ? $Format["GradientAlpha"] : 20;
        $GradientStartR = isset($Format["GradientStartR"]) ? $Format["GradientStartR"] : 255;
        $GradientStartG = isset($Format["GradientStartG"]) ? $Format["GradientStartG"] : 255;
        $GradientStartB = isset($Format["GradientStartB"]) ? $Format["GradientStartB"] : 255;
        $GradientEndR = isset($Format["GradientEndR"]) ? $Format["GradientEndR"] : 0;
        $GradientEndG = isset($Format["GradientEndG"]) ? $Format["GradientEndG"] : 0;
        $GradientEndB = isset($Format["GradientEndB"]) ? $Format["GradientEndB"] : 0;
        $InnerSurrounding = isset($Format["InnerSurrounding"]) ? $Format["InnerSurrounding"] : null;
        $InnerBorderR = isset($Format["InnerBorderR"]) ? $Format["InnerBorderR"] : -1;
        $InnerBorderG = isset($Format["InnerBorderG"]) ? $Format["InnerBorderG"] : -1;
        $InnerBorderB = isset($Format["InnerBorderB"]) ? $Format["InnerBorderB"] : -1;
        $RecordImageMap = isset($Format["RecordImageMap"]) ? $Format["RecordImageMap"] : \false;
        $FontFactor = isset($Format["FontFactor"]) ? $Format["FontFactor"] : 8;
        $this->LastChartLayout = CHART_LAST_LAYOUT_STACKED;
        $Data = $this->DataSet->getData();
        list($XMargin, $XDivs) = $this->scaleGetXSettings();
        $RestoreShadow = $this->Shadow;
        $LastX = [];
        $LastY = [];
        foreach ($Data["Series"] as $SerieName => $Serie) {
            if ($Serie["isDrawable"] == \true && $SerieName != $Data["Abscissa"]) {
                $R = $Serie["Color"]["R"];
                $G = $Serie["Color"]["G"];
                $B = $Serie["Color"]["B"];
                $Alpha = $Serie["Color"]["Alpha"];
                $Ticks = $Serie["Ticks"];
                if ($DisplayColor == DISPLAY_AUTO) {
                    $DisplayR = 255;
                    $DisplayG = 255;
                    $DisplayB = 255;
                }
                if ($Surrounding != null) {
                    $BorderR = $R + $Surrounding;
                    $BorderG = $G + $Surrounding;
                    $BorderB = $B + $Surrounding;
                }
                if ($InnerSurrounding != null) {
                    $InnerBorderR = $R + $InnerSurrounding;
                    $InnerBorderG = $G + $InnerSurrounding;
                    $InnerBorderB = $B + $InnerSurrounding;
                }
                if ($InnerBorderR == -1) {
                    $InnerColor = null;
                } else {
                    $InnerColor = ["R" => $InnerBorderR, "G" => $InnerBorderG, "B" => $InnerBorderB];
                }
                $AxisID = $Serie["Axis"];
                $Mode = $Data["Axis"][$AxisID]["Display"];
                $Format = $Data["Axis"][$AxisID]["Format"];
                $Unit = $Data["Axis"][$AxisID]["Unit"];
                if (isset($Serie["Description"])) {
                    $SerieDescription = $Serie["Description"];
                } else {
                    $SerieDescription = $SerieName;
                }
                $PosArray = $this->scaleComputeY($Serie["Data"], ["AxisID" => $Serie["Axis"]], \true);
                $YZero = $this->scaleComputeY(0, ["AxisID" => $Serie["Axis"]]);
                $this->DataSet->Data["Series"][$SerieName]["XOffset"] = 0;
                $Color = ["TransCorner" => \true, "R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "BorderR" => $BorderR, "BorderG" => $BorderG, "BorderB" => $BorderB];
                if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
                    if ($YZero > $this->GraphAreaY2 - 1) {
                        $YZero = $this->GraphAreaY2 - 1;
                    }
                    if ($YZero > $this->GraphAreaY2 - 1) {
                        $YZero = $this->GraphAreaY2 - 1;
                    }
                    if ($XDivs == 0) {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1) / 4;
                    } else {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
                    }
                    $X = $this->GraphAreaX1 + $XMargin;
                    $XSize = $XStep / (1 + $Interleave);
                    $XOffset = -($XSize / 2);
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    foreach ($PosArray as $Key => $Height) {
                        if ($Height != VOID && $Serie["Data"][$Key] != 0) {
                            if ($Serie["Data"][$Key] > 0) {
                                $Pos = "+";
                            } else {
                                $Pos = "-";
                            }
                            if (!isset($LastY[$Key])) {
                                $LastY[$Key] = [];
                            }
                            if (!isset($LastY[$Key][$Pos])) {
                                $LastY[$Key][$Pos] = $YZero;
                            }
                            $Y1 = $LastY[$Key][$Pos];
                            $Y2 = $Y1 - $Height;
                            if (($Rounded || $BorderR != -1) && ($Pos == "+" && $Y1 != $YZero)) {
                                $YSpaceUp = 1;
                            } else {
                                $YSpaceUp = 0;
                            }
                            if (($Rounded || $BorderR != -1) && ($Pos == "-" && $Y1 != $YZero)) {
                                $YSpaceDown = 1;
                            } else {
                                $YSpaceDown = 0;
                            }
                            if ($RecordImageMap) {
                                $this->addToImageMap("RECT", sprintf("%s,%s,%s,%s", floor($X + $XOffset), floor($Y1 - $YSpaceUp + $YSpaceDown), floor($X + $XOffset + $XSize), floor($Y2)), $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
                            }
                            if ($Rounded) {
                                $this->drawRoundedFilledRectangle($X + $XOffset, $Y1 - $YSpaceUp + $YSpaceDown, $X + $XOffset + $XSize, $Y2, $RoundRadius, $Color);
                            } else {
                                $this->drawFilledRectangle($X + $XOffset, $Y1 - $YSpaceUp + $YSpaceDown, $X + $XOffset + $XSize, $Y2, $Color);
                                if ($InnerColor != null) {
                                    $RestoreShadow = $this->Shadow;
                                    $this->Shadow = \false;
                                    $this->drawRectangle(min($X + $XOffset + 1, $X + $XOffset + $XSize), min($Y1 - $YSpaceUp + $YSpaceDown, $Y2) + 1, max($X + $XOffset + 1, $X + $XOffset + $XSize) - 1, max($Y1 - $YSpaceUp + $YSpaceDown, $Y2) - 1, $InnerColor);
                                    $this->Shadow = $RestoreShadow;
                                }
                                if ($Gradient) {
                                    $this->Shadow = \false;
                                    if ($GradientMode == GRADIENT_SIMPLE) {
                                        $GradientColor = ["StartR" => $GradientStartR, "StartG" => $GradientStartG, "StartB" => $GradientStartB, "EndR" => $GradientEndR, "EndG" => $GradientEndG, "EndB" => $GradientEndB, "Alpha" => $GradientAlpha];
                                        $this->drawGradientArea($X + $XOffset, $Y1 - 1 - $YSpaceUp + $YSpaceDown, $X + $XOffset + $XSize, $Y2 + 1, DIRECTION_VERTICAL, $GradientColor);
                                    } elseif ($GradientMode == GRADIENT_EFFECT_CAN) {
                                        $GradientColor1 = ["StartR" => $GradientEndR, "StartG" => $GradientEndG, "StartB" => $GradientEndB, "EndR" => $GradientStartR, "EndG" => $GradientStartG, "EndB" => $GradientStartB, "Alpha" => $GradientAlpha];
                                        $GradientColor2 = ["StartR" => $GradientStartR, "StartG" => $GradientStartG, "StartB" => $GradientStartB, "EndR" => $GradientEndR, "EndG" => $GradientEndG, "EndB" => $GradientEndB, "Alpha" => $GradientAlpha];
                                        $XSpan = floor($XSize / 3);
                                        $this->drawGradientArea($X + $XOffset - 0.5, $Y1 - 0.5 - $YSpaceUp + $YSpaceDown, $X + $XOffset + $XSpan, $Y2 + 0.5, DIRECTION_HORIZONTAL, $GradientColor1);
                                        $this->drawGradientArea($X + $XSpan + $XOffset - 0.5, $Y1 - 0.5 - $YSpaceUp + $YSpaceDown, $X + $XOffset + $XSize, $Y2 + 0.5, DIRECTION_HORIZONTAL, $GradientColor2);
                                    }
                                    $this->Shadow = $RestoreShadow;
                                }
                            }
                            if ($DisplayValues) {
                                $BarHeight = abs($Y2 - $Y1) - 2;
                                $BarWidth = $XSize + $XOffset / 2 - $FontFactor;
                                $Caption = $this->scaleFormat(round($Serie["Data"][$Key], $DisplayRound), $Mode, $Format, $Unit);
                                $TxtPos = $this->getTextBox(0, 0, $DisplayFont, $DisplaySize, 0, $Caption);
                                $TxtHeight = abs($TxtPos[2]["Y"] - $TxtPos[0]["Y"]);
                                $TxtWidth = abs($TxtPos[1]["X"] - $TxtPos[0]["X"]);
                                $XCenter = ($X + $XOffset + $XSize - ($X + $XOffset)) / 2 + $X + $XOffset;
                                $YCenter = ($Y2 - ($Y1 - $YSpaceUp + $YSpaceDown)) / 2 + $Y1 - $YSpaceUp + $YSpaceDown;
                                $Done = \false;
                                if ($DisplayOrientation == ORIENTATION_HORIZONTAL || $DisplayOrientation == ORIENTATION_AUTO) {
                                    if ($TxtHeight < $BarHeight && $TxtWidth < $BarWidth) {
                                        $this->drawText($XCenter, $YCenter, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit), ["R" => $DisplayR, "G" => $DisplayG, "B" => $DisplayB, "Align" => TEXT_ALIGN_MIDDLEMIDDLE, "FontSize" => $DisplaySize, "FontName" => $DisplayFont]);
                                        $Done = \true;
                                    }
                                }
                                if ($DisplayOrientation == ORIENTATION_VERTICAL || $DisplayOrientation == ORIENTATION_AUTO && !$Done) {
                                    if ($TxtHeight < $BarWidth && $TxtWidth < $BarHeight) {
                                        $this->drawText($XCenter, $YCenter, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit), ["R" => $DisplayR, "G" => $DisplayG, "B" => $DisplayB, "Angle" => 90, "Align" => TEXT_ALIGN_MIDDLEMIDDLE, "FontSize" => $DisplaySize, "FontName" => $DisplayFont]);
                                    }
                                }
                            }
                            $LastY[$Key][$Pos] = $Y2;
                        }
                        $X = $X + $XStep;
                    }
                } else {
                    if ($YZero < $this->GraphAreaX1 + 1) {
                        $YZero = $this->GraphAreaX1 + 1;
                    }
                    if ($YZero > $this->GraphAreaX2 - 1) {
                        $YZero = $this->GraphAreaX2 - 1;
                    }
                    if ($XDivs == 0) {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1) / 4;
                    } else {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
                    }
                    $Y = $this->GraphAreaY1 + $XMargin;
                    $YSize = $YStep / (1 + $Interleave);
                    $YOffset = -($YSize / 2);
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    foreach ($PosArray as $Key => $Width) {
                        if ($Width != VOID && $Serie["Data"][$Key] != 0) {
                            if ($Serie["Data"][$Key] > 0) {
                                $Pos = "+";
                            } else {
                                $Pos = "-";
                            }
                            if (!isset($LastX[$Key])) {
                                $LastX[$Key] = [];
                            }
                            if (!isset($LastX[$Key][$Pos])) {
                                $LastX[$Key][$Pos] = $YZero;
                            }
                            $X1 = $LastX[$Key][$Pos];
                            $X2 = $X1 + $Width;
                            if (($Rounded || $BorderR != -1) && ($Pos == "+" && $X1 != $YZero)) {
                                $XSpaceLeft = 2;
                            } else {
                                $XSpaceLeft = 0;
                            }
                            if (($Rounded || $BorderR != -1) && ($Pos == "-" && $X1 != $YZero)) {
                                $XSpaceRight = 2;
                            } else {
                                $XSpaceRight = 0;
                            }
                            if ($RecordImageMap) {
                                $this->addToImageMap("RECT", sprintf("%s,%s,%s,%s", floor($X1 + $XSpaceLeft), floor($Y + $YOffset), floor($X2 - $XSpaceRight), floor($Y + $YOffset + $YSize)), $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
                            }
                            if ($Rounded) {
                                $this->drawRoundedFilledRectangle($X1 + $XSpaceLeft, $Y + $YOffset, $X2 - $XSpaceRight, $Y + $YOffset + $YSize, $RoundRadius, $Color);
                            } else {
                                $this->drawFilledRectangle($X1 + $XSpaceLeft, $Y + $YOffset, $X2 - $XSpaceRight, $Y + $YOffset + $YSize, $Color);
                                if ($InnerColor != null) {
                                    $RestoreShadow = $this->Shadow;
                                    $this->Shadow = \false;
                                    $this->drawRectangle(min($X1 + $XSpaceLeft, $X2 - $XSpaceRight) + 1, min($Y + $YOffset, $Y + $YOffset + $YSize) + 1, max($X1 + $XSpaceLeft, $X2 - $XSpaceRight) - 1, max($Y + $YOffset, $Y + $YOffset + $YSize) - 1, $InnerColor);
                                    $this->Shadow = $RestoreShadow;
                                }
                                if ($Gradient) {
                                    $this->Shadow = \false;
                                    if ($GradientMode == GRADIENT_SIMPLE) {
                                        $GradientColor = ["StartR" => $GradientStartR, "StartG" => $GradientStartG, "StartB" => $GradientStartB, "EndR" => $GradientEndR, "EndG" => $GradientEndG, "EndB" => $GradientEndB, "Alpha" => $GradientAlpha];
                                        $this->drawGradientArea($X1 + $XSpaceLeft, $Y + $YOffset, $X2 - $XSpaceRight, $Y + $YOffset + $YSize, DIRECTION_HORIZONTAL, $GradientColor);
                                    } elseif ($GradientMode == GRADIENT_EFFECT_CAN) {
                                        $GradientColor1 = ["StartR" => $GradientEndR, "StartG" => $GradientEndG, "StartB" => $GradientEndB, "EndR" => $GradientStartR, "EndG" => $GradientStartG, "EndB" => $GradientStartB, "Alpha" => $GradientAlpha];
                                        $GradientColor2 = ["StartR" => $GradientStartR, "StartG" => $GradientStartG, "StartB" => $GradientStartB, "EndR" => $GradientEndR, "EndG" => $GradientEndG, "EndB" => $GradientEndB, "Alpha" => $GradientAlpha];
                                        $YSpan = floor($YSize / 3);
                                        $this->drawGradientArea($X1 + $XSpaceLeft, $Y + $YOffset, $X2 - $XSpaceRight, $Y + $YOffset + $YSpan, DIRECTION_VERTICAL, $GradientColor1);
                                        $this->drawGradientArea($X1 + $XSpaceLeft, $Y + $YOffset + $YSpan, $X2 - $XSpaceRight, $Y + $YOffset + $YSize, DIRECTION_VERTICAL, $GradientColor2);
                                    }
                                    $this->Shadow = $RestoreShadow;
                                }
                            }
                            if ($DisplayValues) {
                                $BarWidth = abs($X2 - $X1) - $FontFactor;
                                $BarHeight = $YSize + $YOffset / 2 - $FontFactor / 2;
                                $Caption = $this->scaleFormat(round($Serie["Data"][$Key], $DisplayRound), $Mode, $Format, $Unit);
                                $TxtPos = $this->getTextBox(0, 0, $DisplayFont, $DisplaySize, 0, $Caption);
                                $TxtHeight = abs($TxtPos[2]["Y"] - $TxtPos[0]["Y"]);
                                $TxtWidth = abs($TxtPos[1]["X"] - $TxtPos[0]["X"]);
                                $XCenter = ($X2 - $X1) / 2 + $X1;
                                $YCenter = ($Y + $YOffset + $YSize - ($Y + $YOffset)) / 2 + $Y + $YOffset;
                                $Done = \false;
                                if ($DisplayOrientation == ORIENTATION_HORIZONTAL || $DisplayOrientation == ORIENTATION_AUTO) {
                                    if ($TxtHeight < $BarHeight && $TxtWidth < $BarWidth) {
                                        $this->drawText($XCenter, $YCenter, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit), ["R" => $DisplayR, "G" => $DisplayG, "B" => $DisplayB, "Align" => TEXT_ALIGN_MIDDLEMIDDLE, "FontSize" => $DisplaySize, "FontName" => $DisplayFont]);
                                        $Done = \true;
                                    }
                                }
                                if ($DisplayOrientation == ORIENTATION_VERTICAL || $DisplayOrientation == ORIENTATION_AUTO && !$Done) {
                                    if ($TxtHeight < $BarWidth && $TxtWidth < $BarHeight) {
                                        $this->drawText($XCenter, $YCenter, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit), ["R" => $DisplayR, "G" => $DisplayG, "B" => $DisplayB, "Angle" => 90, "Align" => TEXT_ALIGN_MIDDLEMIDDLE, "FontSize" => $DisplaySize, "FontName" => $DisplayFont]);
                                    }
                                }
                            }
                            $LastX[$Key][$Pos] = $X2;
                        }
                        $Y = $Y + $YStep;
                    }
                }
            }
        }
    }
    /**
     * Draw a stacked area chart
     * @param array $Format
     */
    public function drawStackedAreaChart(array $Format = [])
    {
        $DrawLine = isset($Format["DrawLine"]) ? $Format["DrawLine"] : \false;
        $LineSurrounding = isset($Format["LineSurrounding"]) ? $Format["LineSurrounding"] : null;
        $LineR = isset($Format["LineR"]) ? $Format["LineR"] : VOID;
        $LineG = isset($Format["LineG"]) ? $Format["LineG"] : VOID;
        $LineB = isset($Format["LineB"]) ? $Format["LineB"] : VOID;
        $LineAlpha = isset($Format["LineAlpha"]) ? $Format["LineAlpha"] : 100;
        $DrawPlot = isset($Format["DrawPlot"]) ? $Format["DrawPlot"] : \false;
        $PlotRadius = isset($Format["PlotRadius"]) ? $Format["PlotRadius"] : 2;
        $PlotBorder = isset($Format["PlotBorder"]) ? $Format["PlotBorder"] : 1;
        $PlotBorderSurrounding = isset($Format["PlotBorderSurrounding"]) ? $Format["PlotBorderSurrounding"] : null;
        $PlotBorderR = isset($Format["PlotBorderR"]) ? $Format["PlotBorderR"] : 0;
        $PlotBorderG = isset($Format["PlotBorderG"]) ? $Format["PlotBorderG"] : 0;
        $PlotBorderB = isset($Format["PlotBorderB"]) ? $Format["PlotBorderB"] : 0;
        $PlotBorderAlpha = isset($Format["PlotBorderAlpha"]) ? $Format["PlotBorderAlpha"] : 50;
        $ForceTransparency = isset($Format["ForceTransparency"]) ? $Format["ForceTransparency"] : null;
        $this->LastChartLayout = CHART_LAST_LAYOUT_STACKED;
        $Data = $this->DataSet->getData();
        list($XMargin, $XDivs) = $this->scaleGetXSettings();
        $RestoreShadow = $this->Shadow;
        $this->Shadow = \false;
        /* Build the offset data series */
        $OverallOffset = [];
        $SerieOrder = [];
        foreach ($Data["Series"] as $SerieName => $Serie) {
            if ($Serie["isDrawable"] == \true && $SerieName != $Data["Abscissa"]) {
                $SerieOrder[] = $SerieName;
                foreach ($Serie["Data"] as $Key => $Value) {
                    if ($Value == VOID) {
                        $Value = 0;
                    }
                    if ($Value >= 0) {
                        $Sign = "+";
                    } else {
                        $Sign = "-";
                    }
                    if (!isset($OverallOffset[$Key]) || !isset($OverallOffset[$Key][$Sign])) {
                        $OverallOffset[$Key][$Sign] = 0;
                    }
                    if ($Sign == "+") {
                        $Data["Series"][$SerieName]["Data"][$Key] = $Value + $OverallOffset[$Key][$Sign];
                    } else {
                        $Data["Series"][$SerieName]["Data"][$Key] = $Value - $OverallOffset[$Key][$Sign];
                    }
                    $OverallOffset[$Key][$Sign] = $OverallOffset[$Key][$Sign] + abs($Value);
                }
            }
        }
        $SerieOrder = array_reverse($SerieOrder);
        foreach ($SerieOrder as $Key => $SerieName) {
            $Serie = $Data["Series"][$SerieName];
            if ($Serie["isDrawable"] == \true && $SerieName != $Data["Abscissa"]) {
                $R = $Serie["Color"]["R"];
                $G = $Serie["Color"]["G"];
                $B = $Serie["Color"]["B"];
                $Alpha = $Serie["Color"]["Alpha"];
                $Ticks = $Serie["Ticks"];
                if ($ForceTransparency != null) {
                    $Alpha = $ForceTransparency;
                }
                $Color = ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha];
                if ($LineSurrounding != null) {
                    $LineColor = ["R" => $R + $LineSurrounding, "G" => $G + $LineSurrounding, "B" => $B + $LineSurrounding, "Alpha" => $Alpha];
                } elseif ($LineR != VOID) {
                    $LineColor = ["R" => $LineR, "G" => $LineG, "B" => $LineB, "Alpha" => $LineAlpha];
                } else {
                    $LineColor = $Color;
                }
                if ($PlotBorderSurrounding != null) {
                    $PlotBorderColor = ["R" => $R + $PlotBorderSurrounding, "G" => $G + $PlotBorderSurrounding, "B" => $B + $PlotBorderSurrounding, "Alpha" => $PlotBorderAlpha];
                } else {
                    $PlotBorderColor = ["R" => $PlotBorderR, "G" => $PlotBorderG, "B" => $PlotBorderB, "Alpha" => $PlotBorderAlpha];
                }
                $AxisID = $Serie["Axis"];
                $Format = $Data["Axis"][$AxisID]["Format"];
                $PosArray = $this->scaleComputeY($Serie["Data"], ["AxisID" => $Serie["Axis"]], \true);
                $YZero = $this->scaleComputeY(0, ["AxisID" => $Serie["Axis"]]);
                $this->DataSet->Data["Series"][$SerieName]["XOffset"] = 0;
                if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
                    if ($YZero < $this->GraphAreaY1 + 1) {
                        $YZero = $this->GraphAreaY1 + 1;
                    }
                    if ($YZero > $this->GraphAreaY2 - 1) {
                        $YZero = $this->GraphAreaY2 - 1;
                    }
                    if ($XDivs == 0) {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1) / 4;
                    } else {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
                    }
                    $X = $this->GraphAreaX1 + $XMargin;
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    $Plots = [];
                    $Plots[] = $X;
                    $Plots[] = $YZero;
                    foreach ($PosArray as $Key => $Height) {
                        if ($Height != VOID) {
                            $Plots[] = $X;
                            $Plots[] = $YZero - $Height;
                        }
                        $X = $X + $XStep;
                    }
                    $Plots[] = $X - $XStep;
                    $Plots[] = $YZero;
                    $this->drawPolygon($Plots, $Color);
                    $this->Shadow = $RestoreShadow;
                    if ($DrawLine) {
                        for ($i = 2; $i <= count($Plots) - 6; $i = $i + 2) {
                            $this->drawLine($Plots[$i], $Plots[$i + 1], $Plots[$i + 2], $Plots[$i + 3], $LineColor);
                        }
                    }
                    if ($DrawPlot) {
                        for ($i = 2; $i <= count($Plots) - 4; $i = $i + 2) {
                            if ($PlotBorder != 0) {
                                $this->drawFilledCircle($Plots[$i], $Plots[$i + 1], $PlotRadius + $PlotBorder, $PlotBorderColor);
                            }
                            $this->drawFilledCircle($Plots[$i], $Plots[$i + 1], $PlotRadius, $Color);
                        }
                    }
                    $this->Shadow = \false;
                } elseif ($Data["Orientation"] == SCALE_POS_TOPBOTTOM) {
                    if ($YZero < $this->GraphAreaX1 + 1) {
                        $YZero = $this->GraphAreaX1 + 1;
                    }
                    if ($YZero > $this->GraphAreaX2 - 1) {
                        $YZero = $this->GraphAreaX2 - 1;
                    }
                    if ($XDivs == 0) {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1) / 4;
                    } else {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
                    }
                    $Y = $this->GraphAreaY1 + $XMargin;
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    $Plots = [];
                    $Plots[] = $YZero;
                    $Plots[] = $Y;
                    foreach ($PosArray as $Key => $Height) {
                        if ($Height != VOID) {
                            $Plots[] = $YZero + $Height;
                            $Plots[] = $Y;
                        }
                        $Y = $Y + $YStep;
                    }
                    $Plots[] = $YZero;
                    $Plots[] = $Y - $YStep;
                    $this->drawPolygon($Plots, $Color);
                    $this->Shadow = $RestoreShadow;
                    if ($DrawLine) {
                        for ($i = 2; $i <= count($Plots) - 6; $i = $i + 2) {
                            $this->drawLine($Plots[$i], $Plots[$i + 1], $Plots[$i + 2], $Plots[$i + 3], $LineColor);
                        }
                    }
                    if ($DrawPlot) {
                        for ($i = 2; $i <= count($Plots) - 4; $i = $i + 2) {
                            if ($PlotBorder != 0) {
                                $this->drawFilledCircle($Plots[$i], $Plots[$i + 1], $PlotRadius + $PlotBorder, $PlotBorderColor);
                            }
                            $this->drawFilledCircle($Plots[$i], $Plots[$i + 1], $PlotRadius, $Color);
                        }
                    }
                    $this->Shadow = \false;
                }
            }
        }
        $this->Shadow = $RestoreShadow;
    }
    /**
     * Draw the derivative chart associated to the data series
     * @param array $Format
     */
    public function drawDerivative(array $Format = [])
    {
        $Offset = isset($Format["Offset"]) ? $Format["Offset"] : 10;
        $SerieSpacing = isset($Format["SerieSpacing"]) ? $Format["SerieSpacing"] : 3;
        $DerivativeHeight = isset($Format["DerivativeHeight"]) ? $Format["DerivativeHeight"] : 4;
        $ShadedSlopeBox = isset($Format["ShadedSlopeBox"]) ? $Format["ShadedSlopeBox"] : \false;
        $DrawBackground = isset($Format["DrawBackground"]) ? $Format["DrawBackground"] : \true;
        $BackgroundR = isset($Format["BackgroundR"]) ? $Format["BackgroundR"] : 255;
        $BackgroundG = isset($Format["BackgroundG"]) ? $Format["BackgroundG"] : 255;
        $BackgroundB = isset($Format["BackgroundB"]) ? $Format["BackgroundB"] : 255;
        $BackgroundAlpha = isset($Format["BackgroundAlpha"]) ? $Format["BackgroundAlpha"] : 20;
        $DrawBorder = isset($Format["DrawBorder"]) ? $Format["DrawBorder"] : \true;
        $BorderR = isset($Format["BorderR"]) ? $Format["BorderR"] : 0;
        $BorderG = isset($Format["BorderG"]) ? $Format["BorderG"] : 0;
        $BorderB = isset($Format["BorderB"]) ? $Format["BorderB"] : 0;
        $BorderAlpha = isset($Format["BorderAlpha"]) ? $Format["BorderAlpha"] : 100;
        $Caption = isset($Format["Caption"]) ? $Format["Caption"] : \true;
        $CaptionHeight = isset($Format["CaptionHeight"]) ? $Format["CaptionHeight"] : 10;
        $CaptionWidth = isset($Format["CaptionWidth"]) ? $Format["CaptionWidth"] : 20;
        $CaptionMargin = isset($Format["CaptionMargin"]) ? $Format["CaptionMargin"] : 4;
        $CaptionLine = isset($Format["CaptionLine"]) ? $Format["CaptionLine"] : \false;
        $CaptionBox = isset($Format["CaptionBox"]) ? $Format["CaptionBox"] : \false;
        $CaptionBorderR = isset($Format["CaptionBorderR"]) ? $Format["CaptionBorderR"] : 0;
        $CaptionBorderG = isset($Format["CaptionBorderG"]) ? $Format["CaptionBorderG"] : 0;
        $CaptionBorderB = isset($Format["CaptionBorderB"]) ? $Format["CaptionBorderB"] : 0;
        $CaptionFillR = isset($Format["CaptionFillR"]) ? $Format["CaptionFillR"] : 255;
        $CaptionFillG = isset($Format["CaptionFillG"]) ? $Format["CaptionFillG"] : 255;
        $CaptionFillB = isset($Format["CaptionFillB"]) ? $Format["CaptionFillB"] : 255;
        $CaptionFillAlpha = isset($Format["CaptionFillAlpha"]) ? $Format["CaptionFillAlpha"] : 80;
        $PositiveSlopeStartR = isset($Format["PositiveSlopeStartR"]) ? $Format["PositiveSlopeStartR"] : 184;
        $PositiveSlopeStartG = isset($Format["PositiveSlopeStartG"]) ? $Format["PositiveSlopeStartG"] : 234;
        $PositiveSlopeStartB = isset($Format["PositiveSlopeStartB"]) ? $Format["PositiveSlopeStartB"] : 88;
        $PositiveSlopeEndR = isset($Format["PositiveSlopeStartR"]) ? $Format["PositiveSlopeStartR"] : 239;
        $PositiveSlopeEndG = isset($Format["PositiveSlopeStartG"]) ? $Format["PositiveSlopeStartG"] : 31;
        $PositiveSlopeEndB = isset($Format["PositiveSlopeStartB"]) ? $Format["PositiveSlopeStartB"] : 36;
        $NegativeSlopeStartR = isset($Format["NegativeSlopeStartR"]) ? $Format["NegativeSlopeStartR"] : 184;
        $NegativeSlopeStartG = isset($Format["NegativeSlopeStartG"]) ? $Format["NegativeSlopeStartG"] : 234;
        $NegativeSlopeStartB = isset($Format["NegativeSlopeStartB"]) ? $Format["NegativeSlopeStartB"] : 88;
        $NegativeSlopeEndR = isset($Format["NegativeSlopeStartR"]) ? $Format["NegativeSlopeStartR"] : 67;
        $NegativeSlopeEndG = isset($Format["NegativeSlopeStartG"]) ? $Format["NegativeSlopeStartG"] : 124;
        $NegativeSlopeEndB = isset($Format["NegativeSlopeStartB"]) ? $Format["NegativeSlopeStartB"] : 227;
        $Data = $this->DataSet->getData();
        list($XMargin, $XDivs) = $this->scaleGetXSettings();
        if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
            $YPos = $this->DataSet->Data["GraphArea"]["Y2"] + $Offset;
        } else {
            $XPos = $this->DataSet->Data["GraphArea"]["X2"] + $Offset;
        }
        foreach ($Data["Series"] as $SerieName => $Serie) {
            if ($Serie["isDrawable"] == \true && $SerieName != $Data["Abscissa"]) {
                $R = $Serie["Color"]["R"];
                $G = $Serie["Color"]["G"];
                $B = $Serie["Color"]["B"];
                $Alpha = $Serie["Color"]["Alpha"];
                $Ticks = $Serie["Ticks"];
                $Weight = $Serie["Weight"];
                $AxisID = $Serie["Axis"];
                $PosArray = $this->scaleComputeY($Serie["Data"], ["AxisID" => $Serie["Axis"]]);
                if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
                    if ($Caption) {
                        if ($CaptionLine) {
                            $StartX = floor($this->GraphAreaX1 - $CaptionWidth + $XMargin - $CaptionMargin);
                            $EndX = floor($this->GraphAreaX1 - $CaptionMargin + $XMargin);
                            $CaptionSettings = ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "Ticks" => $Ticks, "Weight" => $Weight];
                            if ($CaptionBox) {
                                $this->drawFilledRectangle($StartX, $YPos, $EndX, $YPos + $CaptionHeight, ["R" => $CaptionFillR, "G" => $CaptionFillG, "B" => $CaptionFillB, "BorderR" => $CaptionBorderR, "BorderG" => $CaptionBorderG, "BorderB" => $CaptionBorderB, "Alpha" => $CaptionFillAlpha]);
                            }
                            $this->drawLine($StartX + 2, $YPos + $CaptionHeight / 2, $EndX - 2, $YPos + $CaptionHeight / 2, $CaptionSettings);
                        } else {
                            $this->drawFilledRectangle($this->GraphAreaX1 - $CaptionWidth + $XMargin - $CaptionMargin, $YPos, $this->GraphAreaX1 - $CaptionMargin + $XMargin, $YPos + $CaptionHeight, ["R" => $R, "G" => $G, "B" => $B, "BorderR" => $CaptionBorderR, "BorderG" => $CaptionBorderG, "BorderB" => $CaptionBorderB]);
                        }
                    }
                    if ($XDivs == 0) {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1) / 4;
                    } else {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
                    }
                    $X = $this->GraphAreaX1 + $XMargin;
                    $TopY = $YPos + $CaptionHeight / 2 - $DerivativeHeight / 2;
                    $BottomY = $YPos + $CaptionHeight / 2 + $DerivativeHeight / 2;
                    $StartX = floor($this->GraphAreaX1 + $XMargin);
                    $EndX = floor($this->GraphAreaX2 - $XMargin);
                    if ($DrawBackground) {
                        $this->drawFilledRectangle($StartX - 1, $TopY - 1, $EndX + 1, $BottomY + 1, ["R" => $BackgroundR, "G" => $BackgroundG, "B" => $BackgroundB, "Alpha" => $BackgroundAlpha]);
                    }
                    if ($DrawBorder) {
                        $this->drawRectangle($StartX - 1, $TopY - 1, $EndX + 1, $BottomY + 1, ["R" => $BorderR, "G" => $BorderG, "B" => $BorderB, "Alpha" => $BorderAlpha]);
                    }
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    $RestoreShadow = $this->Shadow;
                    $this->Shadow = \false;
                    /* Determine the Max slope index */
                    $LastX = null;
                    $LastY = null;
                    $MinSlope = 0;
                    $MaxSlope = 1;
                    foreach ($PosArray as $Key => $Y) {
                        if ($Y != VOID && $LastX != null) {
                            $Slope = $LastY - $Y;
                            if ($Slope > $MaxSlope) {
                                $MaxSlope = $Slope;
                            }
                            if ($Slope < $MinSlope) {
                                $MinSlope = $Slope;
                            }
                        }
                        if ($Y == VOID) {
                            $LastX = null;
                            $LastY = null;
                        } else {
                            $LastX = $X;
                            $LastY = $Y;
                        }
                    }
                    $LastX = null;
                    $LastY = null;
                    $LastColor = null;
                    foreach ($PosArray as $Key => $Y) {
                        if ($Y != VOID && $LastY != null) {
                            $Slope = $LastY - $Y;
                            if ($Slope >= 0) {
                                $SlopeIndex = 100 / $MaxSlope * $Slope;
                                $R = ($PositiveSlopeEndR - $PositiveSlopeStartR) / 100 * $SlopeIndex + $PositiveSlopeStartR;
                                $G = ($PositiveSlopeEndG - $PositiveSlopeStartG) / 100 * $SlopeIndex + $PositiveSlopeStartG;
                                $B = ($PositiveSlopeEndB - $PositiveSlopeStartB) / 100 * $SlopeIndex + $PositiveSlopeStartB;
                            } elseif ($Slope < 0) {
                                $SlopeIndex = 100 / abs($MinSlope) * abs($Slope);
                                $R = ($NegativeSlopeEndR - $NegativeSlopeStartR) / 100 * $SlopeIndex + $NegativeSlopeStartR;
                                $G = ($NegativeSlopeEndG - $NegativeSlopeStartG) / 100 * $SlopeIndex + $NegativeSlopeStartG;
                                $B = ($NegativeSlopeEndB - $NegativeSlopeStartB) / 100 * $SlopeIndex + $NegativeSlopeStartB;
                            }
                            $Color = ["R" => $R, "G" => $G, "B" => $B];
                            if ($ShadedSlopeBox && $LastColor != null) {
                                // && $Slope != 0
                                $GradientSettings = ["StartR" => $LastColor["R"], "StartG" => $LastColor["G"], "StartB" => $LastColor["B"], "EndR" => $R, "EndG" => $G, "EndB" => $B];
                                $this->drawGradientArea($LastX, $TopY, $X, $BottomY, DIRECTION_HORIZONTAL, $GradientSettings);
                            } elseif (!$ShadedSlopeBox || $LastColor == null) {
                                // || $Slope == 0
                                $this->drawFilledRectangle(floor($LastX), $TopY, floor($X), $BottomY, $Color);
                            }
                            $LastColor = $Color;
                        }
                        if ($Y == VOID) {
                            $LastY = null;
                        } else {
                            $LastX = $X;
                            $LastY = $Y;
                        }
                        $X = $X + $XStep;
                    }
                    $YPos = $YPos + $CaptionHeight + $SerieSpacing;
                } else {
                    if ($Caption) {
                        $StartY = floor($this->GraphAreaY1 - $CaptionWidth + $XMargin - $CaptionMargin);
                        $EndY = floor($this->GraphAreaY1 - $CaptionMargin + $XMargin);
                        if ($CaptionLine) {
                            $CaptionSettings = ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "Ticks" => $Ticks, "Weight" => $Weight];
                            if ($CaptionBox) {
                                $this->drawFilledRectangle($XPos, $StartY, $XPos + $CaptionHeight, $EndY, ["R" => $CaptionFillR, "G" => $CaptionFillG, "B" => $CaptionFillB, "BorderR" => $CaptionBorderR, "BorderG" => $CaptionBorderG, "BorderB" => $CaptionBorderB, "Alpha" => $CaptionFillAlpha]);
                            }
                            $this->drawLine($XPos + $CaptionHeight / 2, $StartY + 2, $XPos + $CaptionHeight / 2, $EndY - 2, $CaptionSettings);
                        } else {
                            $this->drawFilledRectangle($XPos, $StartY, $XPos + $CaptionHeight, $EndY, ["R" => $R, "G" => $G, "B" => $B, "BorderR" => $CaptionBorderR, "BorderG" => $CaptionBorderG, "BorderB" => $CaptionBorderB]);
                        }
                    }
                    if ($XDivs == 0) {
                        $XStep = ($this->GraphAreaY2 - $this->GraphAreaY1) / 4;
                    } else {
                        $XStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
                    }
                    $Y = $this->GraphAreaY1 + $XMargin;
                    $TopX = $XPos + $CaptionHeight / 2 - $DerivativeHeight / 2;
                    $BottomX = $XPos + $CaptionHeight / 2 + $DerivativeHeight / 2;
                    $StartY = floor($this->GraphAreaY1 + $XMargin);
                    $EndY = floor($this->GraphAreaY2 - $XMargin);
                    if ($DrawBackground) {
                        $this->drawFilledRectangle($TopX - 1, $StartY - 1, $BottomX + 1, $EndY + 1, ["R" => $BackgroundR, "G" => $BackgroundG, "B" => $BackgroundB, "Alpha" => $BackgroundAlpha]);
                    }
                    if ($DrawBorder) {
                        $this->drawRectangle($TopX - 1, $StartY - 1, $BottomX + 1, $EndY + 1, ["R" => $BorderR, "G" => $BorderG, "B" => $BorderB, "Alpha" => $BorderAlpha]);
                    }
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    $RestoreShadow = $this->Shadow;
                    $this->Shadow = \false;
                    /* Determine the Max slope index */
                    $LastX = null;
                    $LastY = null;
                    $MinSlope = 0;
                    $MaxSlope = 1;
                    foreach ($PosArray as $Key => $X) {
                        if ($X != VOID && $LastX != null) {
                            $Slope = $X - $LastX;
                            if ($Slope > $MaxSlope) {
                                $MaxSlope = $Slope;
                            }
                            if ($Slope < $MinSlope) {
                                $MinSlope = $Slope;
                            }
                        }
                        if ($X == VOID) {
                            $LastX = null;
                        } else {
                            $LastX = $X;
                        }
                    }
                    $LastX = null;
                    $LastY = null;
                    $LastColor = null;
                    foreach ($PosArray as $Key => $X) {
                        if ($X != VOID && $LastX != null) {
                            $Slope = $X - $LastX;
                            if ($Slope >= 0) {
                                $SlopeIndex = 100 / $MaxSlope * $Slope;
                                $R = ($PositiveSlopeEndR - $PositiveSlopeStartR) / 100 * $SlopeIndex + $PositiveSlopeStartR;
                                $G = ($PositiveSlopeEndG - $PositiveSlopeStartG) / 100 * $SlopeIndex + $PositiveSlopeStartG;
                                $B = ($PositiveSlopeEndB - $PositiveSlopeStartB) / 100 * $SlopeIndex + $PositiveSlopeStartB;
                            } elseif ($Slope < 0) {
                                $SlopeIndex = 100 / abs($MinSlope) * abs($Slope);
                                $R = ($NegativeSlopeEndR - $NegativeSlopeStartR) / 100 * $SlopeIndex + $NegativeSlopeStartR;
                                $G = ($NegativeSlopeEndG - $NegativeSlopeStartG) / 100 * $SlopeIndex + $NegativeSlopeStartG;
                                $B = ($NegativeSlopeEndB - $NegativeSlopeStartB) / 100 * $SlopeIndex + $NegativeSlopeStartB;
                            }
                            $Color = ["R" => $R, "G" => $G, "B" => $B];
                            if ($ShadedSlopeBox && $LastColor != null) {
                                $GradientSettings = ["StartR" => $LastColor["R"], "StartG" => $LastColor["G"], "StartB" => $LastColor["B"], "EndR" => $R, "EndG" => $G, "EndB" => $B];
                                $this->drawGradientArea($TopX, $LastY, $BottomX, $Y, DIRECTION_VERTICAL, $GradientSettings);
                            } elseif (!$ShadedSlopeBox || $LastColor == null) {
                                $this->drawFilledRectangle($TopX, floor($LastY), $BottomX, floor($Y), $Color);
                            }
                            $LastColor = $Color;
                        }
                        if ($X == VOID) {
                            $LastX = null;
                        } else {
                            $LastX = $X;
                            $LastY = $Y;
                        }
                        $Y = $Y + $XStep;
                    }
                    $XPos = $XPos + $CaptionHeight + $SerieSpacing;
                }
                $this->Shadow = $RestoreShadow;
            }
        }
    }
    /**
     * Draw the line of best fit
     * @param array $Format
     */
    public function drawBestFit(array $Format = [])
    {
        $OverrideTicks = isset($Format["Ticks"]) ? $Format["Ticks"] : null;
        $OverrideR = isset($Format["R"]) ? $Format["R"] : VOID;
        $OverrideG = isset($Format["G"]) ? $Format["G"] : VOID;
        $OverrideB = isset($Format["B"]) ? $Format["B"] : VOID;
        $OverrideAlpha = isset($Format["Alpha"]) ? $Format["Alpha"] : VOID;
        $Data = $this->DataSet->getData();
        list($XMargin, $XDivs) = $this->scaleGetXSettings();
        foreach ($Data["Series"] as $SerieName => $Serie) {
            if ($Serie["isDrawable"] == \true && $SerieName != $Data["Abscissa"]) {
                if ($OverrideR != VOID && $OverrideG != VOID && $OverrideB != VOID) {
                    $R = $OverrideR;
                    $G = $OverrideG;
                    $B = $OverrideB;
                } else {
                    $R = $Serie["Color"]["R"];
                    $G = $Serie["Color"]["G"];
                    $B = $Serie["Color"]["B"];
                }
                if ($OverrideTicks == null) {
                    $Ticks = $Serie["Ticks"];
                } else {
                    $Ticks = $OverrideTicks;
                }
                if ($OverrideAlpha == VOID) {
                    $Alpha = $Serie["Color"]["Alpha"];
                } else {
                    $Alpha = $OverrideAlpha;
                }
                $Color = ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "Ticks" => $Ticks];
                $PosArray = $this->scaleComputeY($Serie["Data"], ["AxisID" => $Serie["Axis"]]);
                if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
                    if ($XDivs == 0) {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1) / 4;
                    } else {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
                    }
                    $X = $this->GraphAreaX1 + $XMargin;
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    $Sxy = 0;
                    $Sx = 0;
                    $Sy = 0;
                    $Sxx = 0;
                    foreach ($PosArray as $Key => $Y) {
                        if ($Y != VOID) {
                            $Sxy = $Sxy + $X * $Y;
                            $Sx = $Sx + $X;
                            $Sy = $Sy + $Y;
                            $Sxx = $Sxx + $X * $X;
                        }
                        $X = $X + $XStep;
                    }
                    $n = count($this->DataSet->stripVOID($PosArray));
                    //$n = count($PosArray);
                    $M = ($n * $Sxy - $Sx * $Sy) / ($n * $Sxx - $Sx * $Sx);
                    $B = ($Sy - $M * $Sx) / $n;
                    $X1 = $this->GraphAreaX1 + $XMargin;
                    $Y1 = $M * $X1 + $B;
                    $X2 = $this->GraphAreaX2 - $XMargin;
                    $Y2 = $M * $X2 + $B;
                    if ($Y1 < $this->GraphAreaY1) {
                        $X1 = $X1 + ($this->GraphAreaY1 - $Y1);
                        $Y1 = $this->GraphAreaY1;
                    }
                    if ($Y1 > $this->GraphAreaY2) {
                        $X1 = $X1 + ($Y1 - $this->GraphAreaY2);
                        $Y1 = $this->GraphAreaY2;
                    }
                    if ($Y2 < $this->GraphAreaY1) {
                        $X2 = $X2 - ($this->GraphAreaY1 - $Y2);
                        $Y2 = $this->GraphAreaY1;
                    }
                    if ($Y2 > $this->GraphAreaY2) {
                        $X2 = $X2 - ($Y2 - $this->GraphAreaY2);
                        $Y2 = $this->GraphAreaY2;
                    }
                    $this->drawLine($X1, $Y1, $X2, $Y2, $Color);
                } else {
                    if ($XDivs == 0) {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1) / 4;
                    } else {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
                    }
                    $Y = $this->GraphAreaY1 + $XMargin;
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    $Sxy = 0;
                    $Sx = 0;
                    $Sy = 0;
                    $Sxx = 0;
                    foreach ($PosArray as $Key => $X) {
                        if ($X != VOID) {
                            $Sxy = $Sxy + $X * $Y;
                            $Sx = $Sx + $Y;
                            $Sy = $Sy + $X;
                            $Sxx = $Sxx + $Y * $Y;
                        }
                        $Y = $Y + $YStep;
                    }
                    $n = count($this->DataSet->stripVOID($PosArray));
                    //$n = count($PosArray);
                    $M = ($n * $Sxy - $Sx * $Sy) / ($n * $Sxx - $Sx * $Sx);
                    $B = ($Sy - $M * $Sx) / $n;
                    $Y1 = $this->GraphAreaY1 + $XMargin;
                    $X1 = $M * $Y1 + $B;
                    $Y2 = $this->GraphAreaY2 - $XMargin;
                    $X2 = $M * $Y2 + $B;
                    if ($X1 < $this->GraphAreaX1) {
                        $Y1 = $Y1 + ($this->GraphAreaX1 - $X1);
                        $X1 = $this->GraphAreaX1;
                    }
                    if ($X1 > $this->GraphAreaX2) {
                        $Y1 = $Y1 + ($X1 - $this->GraphAreaX2);
                        $X1 = $this->GraphAreaX2;
                    }
                    if ($X2 < $this->GraphAreaX1) {
                        $Y2 = $Y2 - ($this->GraphAreaY1 - $X2);
                        $X2 = $this->GraphAreaX1;
                    }
                    if ($X2 > $this->GraphAreaX2) {
                        $Y2 = $Y2 - ($X2 - $this->GraphAreaX2);
                        $X2 = $this->GraphAreaX2;
                    }
                    $this->drawLine($X1, $Y1, $X2, $Y2, $Color);
                }
            }
        }
    }
    /**
     * Draw a label box
     * @param int $X
     * @param int $Y
     * @param string $Title
     * @param array $Captions
     * @param array $Format
     */
    public function drawLabelBox($X, $Y, $Title, array $Captions, array $Format = [])
    {
        $NoTitle = isset($Format["NoTitle"]) ? $Format["NoTitle"] : null;
        $BoxWidth = isset($Format["BoxWidth"]) ? $Format["BoxWidth"] : 50;
        $DrawSerieColor = isset($Format["DrawSerieColor"]) ? $Format["DrawSerieColor"] : \true;
        $SerieBoxSize = isset($Format["SerieBoxSize"]) ? $Format["SerieBoxSize"] : 6;
        $SerieBoxSpacing = isset($Format["SerieBoxSpacing"]) ? $Format["SerieBoxSpacing"] : 4;
        $VerticalMargin = isset($Format["VerticalMargin"]) ? $Format["VerticalMargin"] : 10;
        $HorizontalMargin = isset($Format["HorizontalMargin"]) ? $Format["HorizontalMargin"] : 8;
        $R = isset($Format["R"]) ? $Format["R"] : $this->FontColorR;
        $G = isset($Format["G"]) ? $Format["G"] : $this->FontColorG;
        $B = isset($Format["B"]) ? $Format["B"] : $this->FontColorB;
        $FontName = isset($Format["FontName"]) ? $this->loadFont($Format["FontName"], 'fonts') : $this->FontName;
        $FontSize = isset($Format["FontSize"]) ? $Format["FontSize"] : $this->FontSize;
        $TitleMode = isset($Format["TitleMode"]) ? $Format["TitleMode"] : LABEL_TITLE_NOBACKGROUND;
        $TitleR = isset($Format["TitleR"]) ? $Format["TitleR"] : $R;
        $TitleG = isset($Format["TitleG"]) ? $Format["TitleG"] : $G;
        $TitleB = isset($Format["TitleB"]) ? $Format["TitleB"] : $B;
        $TitleBackgroundR = isset($Format["TitleBackgroundR"]) ? $Format["TitleBackgroundR"] : 0;
        $TitleBackgroundG = isset($Format["TitleBackgroundG"]) ? $Format["TitleBackgroundG"] : 0;
        $TitleBackgroundB = isset($Format["TitleBackgroundB"]) ? $Format["TitleBackgroundB"] : 0;
        $GradientStartR = isset($Format["GradientStartR"]) ? $Format["GradientStartR"] : 255;
        $GradientStartG = isset($Format["GradientStartG"]) ? $Format["GradientStartG"] : 255;
        $GradientStartB = isset($Format["GradientStartB"]) ? $Format["GradientStartB"] : 255;
        $GradientEndR = isset($Format["GradientEndR"]) ? $Format["GradientEndR"] : 220;
        $GradientEndG = isset($Format["GradientEndG"]) ? $Format["GradientEndG"] : 220;
        $GradientEndB = isset($Format["GradientEndB"]) ? $Format["GradientEndB"] : 220;
        $BoxAlpha = isset($Format["BoxAlpha"]) ? $Format["BoxAlpha"] : 100;
        if (!$DrawSerieColor) {
            $SerieBoxSize = 0;
            $SerieBoxSpacing = 0;
        }
        $TxtPos = $this->getTextBox($X, $Y, $FontName, $FontSize, 0, $Title);
        $TitleWidth = $TxtPos[1]["X"] - $TxtPos[0]["X"] + $VerticalMargin * 2;
        $TitleHeight = $TxtPos[0]["Y"] - $TxtPos[2]["Y"];
        if ($NoTitle) {
            $TitleWidth = 0;
            $TitleHeight = 0;
        }
        $CaptionWidth = 0;
        $CaptionHeight = -$HorizontalMargin;
        foreach ($Captions as $Key => $Caption) {
            $TxtPos = $this->getTextBox($X, $Y, $FontName, $FontSize, 0, $Caption["Caption"]);
            $CaptionWidth = max($CaptionWidth, $TxtPos[1]["X"] - $TxtPos[0]["X"] + $VerticalMargin * 2);
            $CaptionHeight = $CaptionHeight + max($TxtPos[0]["Y"] - $TxtPos[2]["Y"], $SerieBoxSize + 2) + $HorizontalMargin;
        }
        if ($CaptionHeight <= 5) {
            $CaptionHeight = $CaptionHeight + $HorizontalMargin / 2;
        }
        if ($DrawSerieColor) {
            $CaptionWidth = $CaptionWidth + $SerieBoxSize + $SerieBoxSpacing;
        }
        $BoxWidth = max($BoxWidth, $TitleWidth, $CaptionWidth);
        $XMin = $X - 5 - floor(($BoxWidth - 10) / 2);
        $XMax = $X + 5 + floor(($BoxWidth - 10) / 2);
        $RestoreShadow = $this->Shadow;
        if ($this->Shadow == \true) {
            $this->Shadow = \false;
            $Poly = [];
            $Poly[] = $X + $this->ShadowX;
            $Poly[] = $Y + $this->ShadowX;
            $Poly[] = $X + 5 + $this->ShadowX;
            $Poly[] = $Y - 5 + $this->ShadowX;
            $Poly[] = $XMax + $this->ShadowX;
            $Poly[] = $Y - 5 + $this->ShadowX;
            if ($NoTitle) {
                $Poly[] = $XMax + $this->ShadowX;
                $Poly[] = $Y - 5 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 2 + $this->ShadowX;
                $Poly[] = $XMin + $this->ShadowX;
                $Poly[] = $Y - 5 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 2 + $this->ShadowX;
            } else {
                $Poly[] = $XMax + $this->ShadowX;
                $Poly[] = $Y - 5 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 3 + $this->ShadowX;
                $Poly[] = $XMin + $this->ShadowX;
                $Poly[] = $Y - 5 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 3 + $this->ShadowX;
            }
            $Poly[] = $XMin + $this->ShadowX;
            $Poly[] = $Y - 5 + $this->ShadowX;
            $Poly[] = $X - 5 + $this->ShadowX;
            $Poly[] = $Y - 5 + $this->ShadowX;
            $this->drawPolygon($Poly, ["R" => $this->ShadowR, "G" => $this->ShadowG, "B" => $this->ShadowB, "Alpha" => $this->Shadowa]);
        }
        /* Draw the background */
        $GradientSettings = ["StartR" => $GradientStartR, "StartG" => $GradientStartG, "StartB" => $GradientStartB, "EndR" => $GradientEndR, "EndG" => $GradientEndG, "EndB" => $GradientEndB, "Alpha" => $BoxAlpha];
        if ($NoTitle) {
            $this->drawGradientArea($XMin, $Y - 5 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 2, $XMax, $Y - 6, DIRECTION_VERTICAL, $GradientSettings);
        } else {
            $this->drawGradientArea($XMin, $Y - 5 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 3, $XMax, $Y - 6, DIRECTION_VERTICAL, $GradientSettings);
        }
        $Poly = [];
        $Poly[] = $X;
        $Poly[] = $Y;
        $Poly[] = $X - 5;
        $Poly[] = $Y - 5;
        $Poly[] = $X + 5;
        $Poly[] = $Y - 5;
        $this->drawPolygon($Poly, ["R" => $GradientEndR, "G" => $GradientEndG, "B" => $GradientEndB, "Alpha" => $BoxAlpha, "NoBorder" => \true]);
        /* Outer border */
        $OuterBorderColor = $this->allocateColor($this->Picture, 100, 100, 100, $BoxAlpha);
        imageline($this->Picture, $XMin, $Y - 5, $X - 5, $Y - 5, $OuterBorderColor);
        imageline($this->Picture, $X, $Y, $X - 5, $Y - 5, $OuterBorderColor);
        imageline($this->Picture, $X, $Y, $X + 5, $Y - 5, $OuterBorderColor);
        imageline($this->Picture, $X + 5, $Y - 5, $XMax, $Y - 5, $OuterBorderColor);
        if ($NoTitle) {
            imageline($this->Picture, $XMin, $Y - 5 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 2, $XMin, $Y - 5, $OuterBorderColor);
            imageline($this->Picture, $XMax, $Y - 5 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 2, $XMax, $Y - 5, $OuterBorderColor);
            imageline($this->Picture, $XMin, $Y - 5 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 2, $XMax, $Y - 5 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 2, $OuterBorderColor);
        } else {
            imageline($this->Picture, $XMin, $Y - 5 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 3, $XMin, $Y - 5, $OuterBorderColor);
            imageline($this->Picture, $XMax, $Y - 5 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 3, $XMax, $Y - 5, $OuterBorderColor);
            imageline($this->Picture, $XMin, $Y - 5 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 3, $XMax, $Y - 5 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 3, $OuterBorderColor);
        }
        /* Inner border */
        $InnerBorderColor = $this->allocateColor($this->Picture, 255, 255, 255, $BoxAlpha);
        imageline($this->Picture, $XMin + 1, $Y - 6, $X - 5, $Y - 6, $InnerBorderColor);
        imageline($this->Picture, $X, $Y - 1, $X - 5, $Y - 6, $InnerBorderColor);
        imageline($this->Picture, $X, $Y - 1, $X + 5, $Y - 6, $InnerBorderColor);
        imageline($this->Picture, $X + 5, $Y - 6, $XMax - 1, $Y - 6, $InnerBorderColor);
        if ($NoTitle) {
            imageline($this->Picture, $XMin + 1, $Y - 4 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 2, $XMin + 1, $Y - 6, $InnerBorderColor);
            imageline($this->Picture, $XMax - 1, $Y - 4 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 2, $XMax - 1, $Y - 6, $InnerBorderColor);
            imageline($this->Picture, $XMin + 1, $Y - 4 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 2, $XMax - 1, $Y - 4 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 2, $InnerBorderColor);
        } else {
            imageline($this->Picture, $XMin + 1, $Y - 4 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 3, $XMin + 1, $Y - 6, $InnerBorderColor);
            imageline($this->Picture, $XMax - 1, $Y - 4 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 3, $XMax - 1, $Y - 6, $InnerBorderColor);
            imageline($this->Picture, $XMin + 1, $Y - 4 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 3, $XMax - 1, $Y - 4 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 3, $InnerBorderColor);
        }
        /* Draw the separator line */
        if ($TitleMode == LABEL_TITLE_NOBACKGROUND && !$NoTitle) {
            $YPos = $Y - 7 - $CaptionHeight - $HorizontalMargin - $HorizontalMargin / 2;
            $XMargin = $VerticalMargin / 2;
            $this->drawLine($XMin + $XMargin, $YPos + 1, $XMax - $XMargin, $YPos + 1, ["R" => $GradientEndR, "G" => $GradientEndG, "B" => $GradientEndB, "Alpha" => $BoxAlpha]);
            $this->drawLine($XMin + $XMargin, $YPos, $XMax - $XMargin, $YPos, ["R" => $GradientStartR, "G" => $GradientStartG, "B" => $GradientStartB, "Alpha" => $BoxAlpha]);
        } elseif ($TitleMode == LABEL_TITLE_BACKGROUND) {
            $this->drawFilledRectangle($XMin, $Y - 5 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 3, $XMax, $Y - 5 - $TitleHeight - $CaptionHeight - $HorizontalMargin + $HorizontalMargin / 2, ["R" => $TitleBackgroundR, "G" => $TitleBackgroundG, "B" => $TitleBackgroundB, "Alpha" => $BoxAlpha]);
            imageline($this->Picture, $XMin + 1, $Y - 5 - $TitleHeight - $CaptionHeight - $HorizontalMargin + $HorizontalMargin / 2 + 1, $XMax - 1, $Y - 5 - $TitleHeight - $CaptionHeight - $HorizontalMargin + $HorizontalMargin / 2 + 1, $InnerBorderColor);
        }
        /* Write the description */
        if (!$NoTitle) {
            $this->drawText($XMin + $VerticalMargin, $Y - 7 - $CaptionHeight - $HorizontalMargin * 2, $Title, ["Align" => TEXT_ALIGN_BOTTOMLEFT, "R" => $TitleR, "G" => $TitleG, "B" => $TitleB]);
        }
        /* Write the value */
        $YPos = $Y - 5 - $HorizontalMargin;
        $XPos = $XMin + $VerticalMargin + $SerieBoxSize + $SerieBoxSpacing;
        foreach ($Captions as $Key => $Caption) {
            $CaptionTxt = $Caption["Caption"];
            $TxtPos = $this->getTextBox($XPos, $YPos, $FontName, $FontSize, 0, $CaptionTxt);
            $CaptionHeight = $TxtPos[0]["Y"] - $TxtPos[2]["Y"];
            /* Write the serie color if needed */
            if ($DrawSerieColor) {
                $BoxSettings = ["R" => $Caption["Format"]["R"], "G" => $Caption["Format"]["G"], "B" => $Caption["Format"]["B"], "Alpha" => $Caption["Format"]["Alpha"], "BorderR" => 0, "BorderG" => 0, "BorderB" => 0];
                $this->drawFilledRectangle($XMin + $VerticalMargin, $YPos - $SerieBoxSize, $XMin + $VerticalMargin + $SerieBoxSize, $YPos, $BoxSettings);
            }
            $this->drawText($XPos, $YPos, $CaptionTxt, ["Align" => TEXT_ALIGN_BOTTOMLEFT]);
            $YPos = $YPos - $CaptionHeight - $HorizontalMargin;
        }
        $this->Shadow = $RestoreShadow;
    }
    /**
     * Draw a basic shape
     * @param int $X
     * @param int $Y
     * @param int $Shape
     * @param int $PlotSize
     * @param int $PlotBorder
     * @param int $BorderSize
     * @param int $R
     * @param int $G
     * @param int $B
     * @param int|float $Alpha
     * @param int $BorderR
     * @param int $BorderG
     * @param int $BorderB
     * @param int|float $BorderAlpha
     */
    public function drawShape($X, $Y, $Shape, $PlotSize, $PlotBorder, $BorderSize, $R, $G, $B, $Alpha, $BorderR, $BorderG, $BorderB, $BorderAlpha)
    {
        if ($Shape == SERIE_SHAPE_FILLEDCIRCLE) {
            if ($PlotBorder) {
                $this->drawFilledCircle($X, $Y, $PlotSize + $BorderSize, ["R" => $BorderR, "G" => $BorderG, "B" => $BorderB, "Alpha" => $BorderAlpha]);
            }
            $this->drawFilledCircle($X, $Y, $PlotSize, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha]);
        } elseif ($Shape == SERIE_SHAPE_FILLEDSQUARE) {
            if ($PlotBorder) {
                $this->drawFilledRectangle($X - $PlotSize - $BorderSize, $Y - $PlotSize - $BorderSize, $X + $PlotSize + $BorderSize, $Y + $PlotSize + $BorderSize, ["R" => $BorderR, "G" => $BorderG, "B" => $BorderB, "Alpha" => $BorderAlpha]);
            }
            $this->drawFilledRectangle($X - $PlotSize, $Y - $PlotSize, $X + $PlotSize, $Y + $PlotSize, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha]);
        } elseif ($Shape == SERIE_SHAPE_FILLEDTRIANGLE) {
            if ($PlotBorder) {
                $Pos = [];
                $Pos[] = $X;
                $Pos[] = $Y - $PlotSize - $BorderSize;
                $Pos[] = $X - $PlotSize - $BorderSize;
                $Pos[] = $Y + $PlotSize + $BorderSize;
                $Pos[] = $X + $PlotSize + $BorderSize;
                $Pos[] = $Y + $PlotSize + $BorderSize;
                $this->drawPolygon($Pos, ["R" => $BorderR, "G" => $BorderG, "B" => $BorderB, "Alpha" => $BorderAlpha]);
            }
            $Pos = [];
            $Pos[] = $X;
            $Pos[] = $Y - $PlotSize;
            $Pos[] = $X - $PlotSize;
            $Pos[] = $Y + $PlotSize;
            $Pos[] = $X + $PlotSize;
            $Pos[] = $Y + $PlotSize;
            $this->drawPolygon($Pos, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha]);
        } elseif ($Shape == SERIE_SHAPE_TRIANGLE) {
            $this->drawLine($X, $Y - $PlotSize, $X - $PlotSize, $Y + $PlotSize, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha]);
            $this->drawLine($X - $PlotSize, $Y + $PlotSize, $X + $PlotSize, $Y + $PlotSize, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha]);
            $this->drawLine($X + $PlotSize, $Y + $PlotSize, $X, $Y - $PlotSize, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha]);
        } elseif ($Shape == SERIE_SHAPE_SQUARE) {
            $this->drawRectangle($X - $PlotSize, $Y - $PlotSize, $X + $PlotSize, $Y + $PlotSize, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha]);
        } elseif ($Shape == SERIE_SHAPE_CIRCLE) {
            $this->drawCircle($X, $Y, $PlotSize, $PlotSize, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha]);
        } elseif ($Shape == SERIE_SHAPE_DIAMOND) {
            $Pos = [];
            $Pos[] = $X - $PlotSize;
            $Pos[] = $Y;
            $Pos[] = $X;
            $Pos[] = $Y - $PlotSize;
            $Pos[] = $X + $PlotSize;
            $Pos[] = $Y;
            $Pos[] = $X;
            $Pos[] = $Y + $PlotSize;
            $this->drawPolygon($Pos, ["NoFill" => \true, "BorderR" => $R, "BorderG" => $G, "BorderB" => $B, "BorderAlpha" => $Alpha]);
        } elseif ($Shape == SERIE_SHAPE_FILLEDDIAMOND) {
            if ($PlotBorder) {
                $Pos = [];
                $Pos[] = $X - $PlotSize - $BorderSize;
                $Pos[] = $Y;
                $Pos[] = $X;
                $Pos[] = $Y - $PlotSize - $BorderSize;
                $Pos[] = $X + $PlotSize + $BorderSize;
                $Pos[] = $Y;
                $Pos[] = $X;
                $Pos[] = $Y + $PlotSize + $BorderSize;
                $this->drawPolygon($Pos, ["R" => $BorderR, "G" => $BorderG, "B" => $BorderB, "Alpha" => $BorderAlpha]);
            }
            $Pos = [];
            $Pos[] = $X - $PlotSize;
            $Pos[] = $Y;
            $Pos[] = $X;
            $Pos[] = $Y - $PlotSize;
            $Pos[] = $X + $PlotSize;
            $Pos[] = $Y;
            $Pos[] = $X;
            $Pos[] = $Y + $PlotSize;
            $this->drawPolygon($Pos, ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha]);
        }
    }
    /**
     *
     * @param array $Points
     * @param array $Format
     * @return null|integer
     */
    public function drawPolygonChart(array $Points, array $Format = [])
    {
        $R = isset($Format["R"]) ? $Format["R"] : 0;
        $G = isset($Format["G"]) ? $Format["G"] : 0;
        $B = isset($Format["B"]) ? $Format["B"] : 0;
        $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;
        $NoFill = isset($Format["NoFill"]) ? $Format["NoFill"] : \false;
        $NoBorder = isset($Format["NoBorder"]) ? $Format["NoBorder"] : \false;
        $BorderR = isset($Format["BorderR"]) ? $Format["BorderR"] : $R;
        $BorderG = isset($Format["BorderG"]) ? $Format["BorderG"] : $G;
        $BorderB = isset($Format["BorderB"]) ? $Format["BorderB"] : $B;
        $BorderAlpha = isset($Format["BorderAlpha"]) ? $Format["BorderAlpha"] : $Alpha / 2;
        $Surrounding = isset($Format["Surrounding"]) ? $Format["Surrounding"] : null;
        $Threshold = isset($Format["Threshold"]) ? $Format["Threshold"] : null;
        if ($Surrounding != null) {
            $BorderR = $R + $Surrounding;
            $BorderG = $G + $Surrounding;
            $BorderB = $B + $Surrounding;
        }
        $RestoreShadow = $this->Shadow;
        $this->Shadow = \false;
        $AllIntegers = \true;
        for ($i = 0; $i <= count($Points) - 2; $i = $i + 2) {
            if ($this->getFirstDecimal($Points[$i + 1]) != 0) {
                $AllIntegers = \false;
            }
        }
        /* Convert polygon to segments */
        $Segments = [];
        for ($i = 2; $i <= count($Points) - 2; $i = $i + 2) {
            $Segments[] = ["X1" => $Points[$i - 2], "Y1" => $Points[$i - 1], "X2" => $Points[$i], "Y2" => $Points[$i + 1]];
        }
        $Segments[] = ["X1" => $Points[$i - 2], "Y1" => $Points[$i - 1], "X2" => $Points[0], "Y2" => $Points[1]];
        /* Simplify straight lines */
        $Result = [];
        $inHorizon = \false;
        $LastX = VOID;
        foreach ($Segments as $Key => $Pos) {
            if ($Pos["Y1"] != $Pos["Y2"]) {
                if ($inHorizon) {
                    $inHorizon = \false;
                    $Result[] = ["X1" => $LastX, "Y1" => $Pos["Y1"], "X2" => $Pos["X1"], "Y2" => $Pos["Y1"]];
                }
                $Result[] = ["X1" => $Pos["X1"], "Y1" => $Pos["Y1"], "X2" => $Pos["X2"], "Y2" => $Pos["Y2"]];
            } else {
                if (!$inHorizon) {
                    $inHorizon = \true;
                    $LastX = $Pos["X1"];
                }
            }
        }
        $Segments = $Result;
        /* Do we have something to draw */
        if (!count($Segments)) {
            return 0;
        }
        /* For segments debugging purpose */
        //foreach($Segments as $Key => $Pos)
        // echo $Pos["X1"].",".$Pos["Y1"].",".$Pos["X2"].",".$Pos["Y2"]."\r\n";
        /* Find out the min & max Y boundaries */
        $MinY = OUT_OF_SIGHT;
        $MaxY = OUT_OF_SIGHT;
        foreach ($Segments as $Key => $Coords) {
            if ($MinY == OUT_OF_SIGHT || $MinY > min($Coords["Y1"], $Coords["Y2"])) {
                $MinY = min($Coords["Y1"], $Coords["Y2"]);
            }
            if ($MaxY == OUT_OF_SIGHT || $MaxY < max($Coords["Y1"], $Coords["Y2"])) {
                $MaxY = max($Coords["Y1"], $Coords["Y2"]);
            }
        }
        if ($AllIntegers) {
            $YStep = 1;
        } else {
            $YStep = 0.5;
        }
        $MinY = floor($MinY);
        $MaxY = floor($MaxY);
        /* Scan each Y lines */
        $DefaultColor = $this->allocateColor($this->Picture, $R, $G, $B, $Alpha);
        $DebugLine = 0;
        $DebugColor = $this->allocateColor($this->Picture, 255, 0, 0, 100);
        $MinY = floor($MinY);
        $MaxY = floor($MaxY);
        $YStep = 1;
        if (!$NoFill) {
            //if ($DebugLine ) { $MinY = $DebugLine; $MaxY = $DebugLine; }
            for ($Y = $MinY; $Y <= $MaxY; $Y = $Y + $YStep) {
                $Intersections = [];
                $LastSlope = null;
                $RestoreLast = "-";
                foreach ($Segments as $Key => $Coords) {
                    $X1 = $Coords["X1"];
                    $X2 = $Coords["X2"];
                    $Y1 = $Coords["Y1"];
                    $Y2 = $Coords["Y2"];
                    if (min($Y1, $Y2) <= $Y && max($Y1, $Y2) >= $Y) {
                        if ($Y1 == $Y2) {
                            $X = $X1;
                        } else {
                            $X = $X1 + (($Y - $Y1) * $X2 - ($Y - $Y1) * $X1) / ($Y2 - $Y1);
                        }
                        $X = floor($X);
                        if ($X2 == $X1) {
                            $Slope = "!";
                        } else {
                            $SlopeC = ($Y2 - $Y1) / ($X2 - $X1);
                            if ($SlopeC == 0) {
                                $Slope = "=";
                            } elseif ($SlopeC > 0) {
                                $Slope = "+";
                            } elseif ($SlopeC < 0) {
                                $Slope = "-";
                            }
                        }
                        if (!is_array($Intersections)) {
                            $Intersections[] = $X;
                        } elseif (!in_array($X, $Intersections)) {
                            $Intersections[] = $X;
                        } elseif (in_array($X, $Intersections)) {
                            if ($Y == $DebugLine) {
                                echo $Slope . "/" . $LastSlope . "(" . $X . ") ";
                            }
                            if ($Slope == "=" && $LastSlope == "-") {
                                $Intersections[] = $X;
                            }
                            if ($Slope != $LastSlope && $LastSlope != "!" && $LastSlope != "=") {
                                $Intersections[] = $X;
                            }
                            if ($Slope != $LastSlope && $LastSlope == "!" && $Slope == "+") {
                                $Intersections[] = $X;
                            }
                        }
                        if (is_array($Intersections) && in_array($X, $Intersections) && $LastSlope == "=" && $Slope == "-") {
                            $Intersections[] = $X;
                        }
                        $LastSlope = $Slope;
                    }
                }
                if ($RestoreLast != "-") {
                    $Intersections[] = $RestoreLast;
                    echo "@" . $Y . "\r\n";
                }
                if (is_array($Intersections)) {
                    sort($Intersections);
                    if ($Y == $DebugLine) {
                        print_r($Intersections);
                    }
                    /* Remove null plots */
                    $Result = [];
                    for ($i = 0; $i <= count($Intersections) - 1; $i = $i + 2) {
                        if (isset($Intersections[$i + 1])) {
                            if ($Intersections[$i] != $Intersections[$i + 1]) {
                                $Result[] = $Intersections[$i];
                                $Result[] = $Intersections[$i + 1];
                            }
                        }
                    }
                    if (is_array($Result)) {
                        $Intersections = $Result;
                        $LastX = OUT_OF_SIGHT;
                        foreach ($Intersections as $Key => $X) {
                            if ($LastX == OUT_OF_SIGHT) {
                                $LastX = $X;
                            } elseif ($LastX != OUT_OF_SIGHT) {
                                if ($this->getFirstDecimal($LastX) > 1) {
                                    $LastX++;
                                }
                                $Color = $DefaultColor;
                                if ($Threshold != null) {
                                    foreach ($Threshold as $Key => $Parameters) {
                                        if ($Y <= $Parameters["MinX"] && $Y >= $Parameters["MaxX"]) {
                                            if (isset($Parameters["R"])) {
                                                $R = $Parameters["R"];
                                            } else {
                                                $R = 0;
                                            }
                                            if (isset($Parameters["G"])) {
                                                $G = $Parameters["G"];
                                            } else {
                                                $G = 0;
                                            }
                                            if (isset($Parameters["B"])) {
                                                $B = $Parameters["B"];
                                            } else {
                                                $B = 0;
                                            }
                                            if (isset($Parameters["Alpha"])) {
                                                $Alpha = $Parameters["Alpha"];
                                            } else {
                                                $Alpha = 100;
                                            }
                                            $Color = $this->allocateColor($this->Picture, $R, $G, $B, $Alpha);
                                        }
                                    }
                                }
                                imageline($this->Picture, $LastX, $Y, $X, $Y, $Color);
                                if ($Y == $DebugLine) {
                                    imageline($this->Picture, $LastX, $Y, $X, $Y, $DebugColor);
                                }
                                $LastX = OUT_OF_SIGHT;
                            }
                        }
                    }
                }
            }
        }
        /* Draw the polygon border, if required */
        if (!$NoBorder) {
            foreach ($Segments as $Key => $Coords) {
                $this->drawLine($Coords["X1"], $Coords["Y1"], $Coords["X2"], $Coords["Y2"], ["R" => $BorderR, "G" => $BorderG, "B" => $BorderB, "Alpha" => $BorderAlpha, "Threshold" => $Threshold]);
            }
        }
        $this->Shadow = $RestoreShadow;
    }
}
