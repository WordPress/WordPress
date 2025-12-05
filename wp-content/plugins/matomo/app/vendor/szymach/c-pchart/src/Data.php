<?php

namespace CpChart;

use Exception;
use RuntimeException;
/**
 *  Data - class to manipulate data arrays
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
class Data
{
    /**
     * @var array
     */
    public $Data = [];
    /**
     * @var array
     */
    public $Palette = ["0" => ["R" => 188, "G" => 224, "B" => 46, "Alpha" => 100], "1" => ["R" => 224, "G" => 100, "B" => 46, "Alpha" => 100], "2" => ["R" => 224, "G" => 214, "B" => 46, "Alpha" => 100], "3" => ["R" => 46, "G" => 151, "B" => 224, "Alpha" => 100], "4" => ["R" => 176, "G" => 46, "B" => 224, "Alpha" => 100], "5" => ["R" => 224, "G" => 46, "B" => 117, "Alpha" => 100], "6" => ["R" => 92, "G" => 224, "B" => 46, "Alpha" => 100], "7" => ["R" => 224, "G" => 176, "B" => 46, "Alpha" => 100]];
    public function __construct()
    {
        $this->Data["XAxisDisplay"] = AXIS_FORMAT_DEFAULT;
        $this->Data["XAxisFormat"] = null;
        $this->Data["XAxisName"] = null;
        $this->Data["XAxisUnit"] = null;
        $this->Data["Abscissa"] = null;
        $this->Data["AbsicssaPosition"] = AXIS_POSITION_BOTTOM;
        $this->Data["Axis"][0]["Display"] = AXIS_FORMAT_DEFAULT;
        $this->Data["Axis"][0]["Position"] = AXIS_POSITION_LEFT;
        $this->Data["Axis"][0]["Identity"] = AXIS_Y;
    }
    /**
     * Add a single point or an array to the given serie
     * @param mixed $Values
     * @param string $SerieName
     * @return int
     */
    public function addPoints($Values, $SerieName = "Serie1")
    {
        if (!isset($this->Data["Series"][$SerieName])) {
            $this->initialise($SerieName);
        }
        if (is_array($Values)) {
            foreach ($Values as $Value) {
                $this->Data["Series"][$SerieName]["Data"][] = $Value;
            }
        } else {
            $this->Data["Series"][$SerieName]["Data"][] = $Values;
        }
        if ($Values != VOID) {
            $StrippedData = $this->stripVOID($this->Data["Series"][$SerieName]["Data"]);
            if (empty($StrippedData)) {
                $this->Data["Series"][$SerieName]["Max"] = 0;
                $this->Data["Series"][$SerieName]["Min"] = 0;
                return 0;
            }
            $this->Data["Series"][$SerieName]["Max"] = max($StrippedData);
            $this->Data["Series"][$SerieName]["Min"] = min($StrippedData);
        }
    }
    /**
     * Strip VOID values
     * @param mixed $Values
     * @return array
     */
    public function stripVOID($Values)
    {
        if (!is_array($Values)) {
            return [];
        }
        $Result = [];
        foreach ($Values as $Value) {
            if ($Value != VOID) {
                $Result[] = $Value;
            }
        }
        return $Result;
    }
    /**
     * Return the number of values contained in a given serie
     * @param string $Serie
     * @return int
     */
    public function getSerieCount($Serie)
    {
        if (isset($this->Data["Series"][$Serie]["Data"])) {
            return sizeof($this->Data["Series"][$Serie]["Data"]);
        }
        return 0;
    }
    /**
     * Remove a serie from the pData object
     * @param mixed $Series
     */
    public function removeSerie($Series)
    {
        if (!is_array($Series)) {
            $Series = $this->convertToArray($Series);
        }
        foreach ($Series as $Serie) {
            if (isset($this->Data["Series"][$Serie])) {
                unset($this->Data["Series"][$Serie]);
            }
        }
    }
    /**
     * Return a value from given serie & index
     * @param string $Serie
     * @param int $Index
     * @return mixed
     */
    public function getValueAt($Serie, $Index = 0)
    {
        if (isset($this->Data["Series"][$Serie]["Data"][$Index])) {
            return $this->Data["Series"][$Serie]["Data"][$Index];
        }
        return null;
    }
    /**
     * Return the values array
     * @param string $Serie
     * @return mixed
     */
    public function getValues($Serie)
    {
        if (isset($this->Data["Series"][$Serie]["Data"])) {
            return $this->Data["Series"][$Serie]["Data"];
        }
        return null;
    }
    /**
     * Reverse the values in the given serie
     * @param mixed $Series
     */
    public function reverseSerie($Series)
    {
        if (!is_array($Series)) {
            $Series = $this->convertToArray($Series);
        }
        foreach ($Series as $Serie) {
            if (isset($this->Data["Series"][$Serie]["Data"])) {
                $this->Data["Series"][$Serie]["Data"] = array_reverse($this->Data["Series"][$Serie]["Data"]);
            }
        }
    }
    /**
     * Return the sum of the serie values
     * @param string $Serie
     * @return int|null
     */
    public function getSum($Serie)
    {
        if (isset($this->Data["Series"][$Serie])) {
            return array_sum($this->Data["Series"][$Serie]["Data"]);
        }
        return null;
    }
    /**
     * Return the max value of a given serie
     * @param string $Serie
     * @return mixed
     */
    public function getMax($Serie)
    {
        if (isset($this->Data["Series"][$Serie]["Max"])) {
            return $this->Data["Series"][$Serie]["Max"];
        }
        return null;
    }
    /**
     * @param string $Serie
     * @return mixed
     */
    public function getMin($Serie)
    {
        if (isset($this->Data["Series"][$Serie]["Min"])) {
            return $this->Data["Series"][$Serie]["Min"];
        }
        return null;
    }
    /**
     * Set the description of a given serie
     * @param mixed $Series
     * @param string $Shape
     */
    public function setSerieShape($Series, $Shape = SERIE_SHAPE_FILLEDCIRCLE)
    {
        if (!is_array($Series)) {
            $Series = $this->convertToArray($Series);
        }
        foreach ($Series as $Serie) {
            if (isset($this->Data["Series"][$Serie])) {
                $this->Data["Series"][$Serie]["Shape"] = $Shape;
            }
        }
    }
    /**
     * Set the description of a given serie
     * @param string|array $Series
     * @param string $Description
     */
    public function setSerieDescription($Series, $Description = "My serie")
    {
        if (!is_array($Series)) {
            $Series = $this->convertToArray($Series);
        }
        foreach ($Series as $Serie) {
            if (isset($this->Data["Series"][$Serie])) {
                $this->Data["Series"][$Serie]["Description"] = $Description;
            }
        }
    }
    /**
     * Set a serie as "drawable" while calling a rendering public function
     * @param string|array $Series
     * @param boolean $Drawable
     */
    public function setSerieDrawable($Series, $Drawable = \true)
    {
        if (!is_array($Series)) {
            $Series = $this->convertToArray($Series);
        }
        foreach ($Series as $Serie) {
            if (isset($this->Data["Series"][$Serie])) {
                $this->Data["Series"][$Serie]["isDrawable"] = $Drawable;
            }
        }
    }
    /**
     * Set the icon associated to a given serie
     * @param mixed $Series
     * @param mixed $Picture
     */
    public function setSeriePicture($Series, $Picture = null)
    {
        if (!is_array($Series)) {
            $Series = $this->convertToArray($Series);
        }
        foreach ($Series as $Serie) {
            if (isset($this->Data["Series"][$Serie])) {
                $this->Data["Series"][$Serie]["Picture"] = $Picture;
            }
        }
    }
    /**
     * Set the name of the X Axis
     * @param string $Name
     */
    public function setXAxisName($Name)
    {
        $this->Data["XAxisName"] = $Name;
    }
    /**
     * Set the display mode of the  X Axis
     * @param int $Mode
     * @param array $Format
     */
    public function setXAxisDisplay($Mode, $Format = null)
    {
        $this->Data["XAxisDisplay"] = $Mode;
        $this->Data["XAxisFormat"] = $Format;
    }
    /**
     * Set the unit that will be displayed on the X axis
     * @param string $Unit
     */
    public function setXAxisUnit($Unit)
    {
        $this->Data["XAxisUnit"] = $Unit;
    }
    /**
     * Set the serie that will be used as abscissa
     * @param string $Serie
     */
    public function setAbscissa($Serie)
    {
        if (isset($this->Data["Series"][$Serie])) {
            $this->Data["Abscissa"] = $Serie;
        }
    }
    /**
     * Set the position of the abscissa axis
     * @param int $Position
     */
    public function setAbsicssaPosition($Position = AXIS_POSITION_BOTTOM)
    {
        $this->Data["AbsicssaPosition"] = $Position;
    }
    /**
     * Set the name of the abscissa axis
     * @param string $Name
     */
    public function setAbscissaName($Name)
    {
        $this->Data["AbscissaName"] = $Name;
    }
    /**
     * Create a scatter group specified in X and Y data series
     * @param string $SerieX
     * @param string $SerieY
     * @param int $ID
     */
    public function setScatterSerie($SerieX, $SerieY, $ID = 0)
    {
        if (isset($this->Data["Series"][$SerieX]) && isset($this->Data["Series"][$SerieY])) {
            $this->initScatterSerie($ID);
            $this->Data["ScatterSeries"][$ID]["X"] = $SerieX;
            $this->Data["ScatterSeries"][$ID]["Y"] = $SerieY;
        }
    }
    /**
     *  Set the shape of a given sctatter serie
     * @param int $ID
     * @param int $Shape
     */
    public function setScatterSerieShape($ID, $Shape = SERIE_SHAPE_FILLEDCIRCLE)
    {
        if (isset($this->Data["ScatterSeries"][$ID])) {
            $this->Data["ScatterSeries"][$ID]["Shape"] = $Shape;
        }
    }
    /**
     * Set the description of a given scatter serie
     * @param int $ID
     * @param string $Description
     */
    public function setScatterSerieDescription($ID, $Description = "My serie")
    {
        if (isset($this->Data["ScatterSeries"][$ID])) {
            $this->Data["ScatterSeries"][$ID]["Description"] = $Description;
        }
    }
    /**
     * Set the icon associated to a given scatter serie
     * @param int $ID
     * @param mixed $Picture
     */
    public function setScatterSeriePicture($ID, $Picture = null)
    {
        if (isset($this->Data["ScatterSeries"][$ID])) {
            $this->Data["ScatterSeries"][$ID]["Picture"] = $Picture;
        }
    }
    /**
     * Set a scatter serie as "drawable" while calling a rendering public function
     * @param int $ID
     * @param boolean $Drawable
     */
    public function setScatterSerieDrawable($ID, $Drawable = \true)
    {
        if (isset($this->Data["ScatterSeries"][$ID])) {
            $this->Data["ScatterSeries"][$ID]["isDrawable"] = $Drawable;
        }
    }
    /**
     * Define if a scatter serie should be draw with ticks
     * @param int $ID
     * @param int $Width
     */
    public function setScatterSerieTicks($ID, $Width = 0)
    {
        if (isset($this->Data["ScatterSeries"][$ID])) {
            $this->Data["ScatterSeries"][$ID]["Ticks"] = $Width;
        }
    }
    /**
     * Define if a scatter serie should be draw with a special weight
     * @param int $ID
     * @param int $Weight
     */
    public function setScatterSerieWeight($ID, $Weight = 0)
    {
        if (isset($this->Data["ScatterSeries"][$ID])) {
            $this->Data["ScatterSeries"][$ID]["Weight"] = $Weight;
        }
    }
    /**
     * Associate a color to a scatter serie
     * @param int $ID
     * @param array $Format
     */
    public function setScatterSerieColor($ID, array $Format)
    {
        $R = isset($Format["R"]) ? $Format["R"] : 0;
        $G = isset($Format["G"]) ? $Format["G"] : 0;
        $B = isset($Format["B"]) ? $Format["B"] : 0;
        $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;
        if (isset($this->Data["ScatterSeries"][$ID])) {
            $this->Data["ScatterSeries"][$ID]["Color"]["R"] = $R;
            $this->Data["ScatterSeries"][$ID]["Color"]["G"] = $G;
            $this->Data["ScatterSeries"][$ID]["Color"]["B"] = $B;
            $this->Data["ScatterSeries"][$ID]["Color"]["Alpha"] = $Alpha;
        }
    }
    /**
     * Compute the series limits for an individual and global point of view
     * @return array
     */
    public function limits()
    {
        $GlobalMin = ABSOLUTE_MAX;
        $GlobalMax = ABSOLUTE_MIN;
        foreach (array_keys($this->Data["Series"]) as $Key) {
            if ($this->Data["Abscissa"] != $Key && $this->Data["Series"][$Key]["isDrawable"] == \true) {
                if ($GlobalMin > $this->Data["Series"][$Key]["Min"]) {
                    $GlobalMin = $this->Data["Series"][$Key]["Min"];
                }
                if ($GlobalMax < $this->Data["Series"][$Key]["Max"]) {
                    $GlobalMax = $this->Data["Series"][$Key]["Max"];
                }
            }
        }
        $this->Data["Min"] = $GlobalMin;
        $this->Data["Max"] = $GlobalMax;
        return [$GlobalMin, $GlobalMax];
    }
    /**
     * Mark all series as drawable
     */
    public function drawAll()
    {
        foreach (array_keys($this->Data["Series"]) as $Key) {
            if ($this->Data["Abscissa"] != $Key) {
                $this->Data["Series"][$Key]["isDrawable"] = \true;
            }
        }
    }
    /**
     * Return the average value of the given serie
     * @param string $Serie
     * @return int|null
     */
    public function getSerieAverage($Serie)
    {
        if (isset($this->Data["Series"][$Serie])) {
            $SerieData = $this->stripVOID($this->Data["Series"][$Serie]["Data"]);
            return array_sum($SerieData) / sizeof($SerieData);
        }
        return null;
    }
    /**
     * Return the geometric mean of the given serie
     * @param string $Serie
     * @return int|null
     */
    public function getGeometricMean($Serie)
    {
        if (isset($this->Data["Series"][$Serie])) {
            $SerieData = $this->stripVOID($this->Data["Series"][$Serie]["Data"]);
            $Seriesum = 1;
            foreach ($SerieData as $Value) {
                $Seriesum = $Seriesum * $Value;
            }
            return pow($Seriesum, 1 / sizeof($SerieData));
        }
        return null;
    }
    /**
     * Return the harmonic mean of the given serie
     * @param string $Serie
     * @return int|null
     */
    public function getHarmonicMean($Serie)
    {
        if (isset($this->Data["Series"][$Serie])) {
            $SerieData = $this->stripVOID($this->Data["Series"][$Serie]["Data"]);
            $Seriesum = 0;
            foreach ($SerieData as $Value) {
                $Seriesum = $Seriesum + 1 / $Value;
            }
            return sizeof($SerieData) / $Seriesum;
        }
        return null;
    }
    /**
     * Return the standard deviation of the given serie
     * @param string $Serie
     * @return double|null
     */
    public function getStandardDeviation($Serie)
    {
        if (isset($this->Data["Series"][$Serie])) {
            $Average = $this->getSerieAverage($Serie);
            $SerieData = $this->stripVOID($this->Data["Series"][$Serie]["Data"]);
            $DeviationSum = 0;
            foreach ($SerieData as $Key => $Value) {
                $DeviationSum = $DeviationSum + ($Value - $Average) * ($Value - $Average);
            }
            return sqrt($DeviationSum / count($SerieData));
        }
        return null;
    }
    /**
     * Return the Coefficient of variation of the given serie
     * @param string $Serie
     * @return float|null
     */
    public function getCoefficientOfVariation($Serie)
    {
        if (isset($this->Data["Series"][$Serie])) {
            $Average = $this->getSerieAverage($Serie);
            $StandardDeviation = $this->getStandardDeviation($Serie);
            if ($StandardDeviation != 0) {
                return $StandardDeviation / $Average;
            }
        }
        return null;
    }
    /**
     * Return the median value of the given serie
     * @param string $Serie
     * @return int|float
     */
    public function getSerieMedian($Serie)
    {
        if (isset($this->Data["Series"][$Serie])) {
            $SerieData = $this->stripVOID($this->Data["Series"][$Serie]["Data"]);
            sort($SerieData);
            $SerieCenter = floor(sizeof($SerieData) / 2);
            if (isset($SerieData[$SerieCenter])) {
                return $SerieData[$SerieCenter];
            }
        }
        return null;
    }
    /**
     * Return the x th percentil of the given serie
     * @param string $Serie
     * @param int $Percentil
     * @return int|float| null
     */
    public function getSeriePercentile($Serie = "Serie1", $Percentil = 95)
    {
        if (!isset($this->Data["Series"][$Serie]["Data"])) {
            return null;
        }
        $Values = count($this->Data["Series"][$Serie]["Data"]) - 1;
        if ($Values < 0) {
            $Values = 0;
        }
        $PercentilID = floor($Values / 100 * $Percentil + 0.5);
        $SortedValues = $this->Data["Series"][$Serie]["Data"];
        sort($SortedValues);
        if (is_numeric($SortedValues[$PercentilID])) {
            return $SortedValues[$PercentilID];
        }
        return null;
    }
    /**
     * Add random values to a given serie
     * @param string $SerieName
     * @param array $Options
     */
    public function addRandomValues($SerieName = "Serie1", array $Options = [])
    {
        $Values = isset($Options["Values"]) ? $Options["Values"] : 20;
        $Min = isset($Options["Min"]) ? $Options["Min"] : 0;
        $Max = isset($Options["Max"]) ? $Options["Max"] : 100;
        $withFloat = isset($Options["withFloat"]) ? $Options["withFloat"] : \false;
        for ($i = 0; $i <= $Values; $i++) {
            $Value = $withFloat ? rand($Min * 100, $Max * 100) / 100 : rand($Min, $Max);
            $this->addPoints($Value, $SerieName);
        }
    }
    /**
     * Test if we have valid data
     * @return boolean|null
     */
    public function containsData()
    {
        if (!isset($this->Data["Series"])) {
            return \false;
        }
        foreach (array_keys($this->Data["Series"]) as $Key) {
            if ($this->Data["Abscissa"] != $Key && $this->Data["Series"][$Key]["isDrawable"] == \true) {
                return \true;
            }
        }
        return null;
    }
    /**
     * Set the display mode of an Axis
     * @param int $AxisID
     * @param int $Mode
     * @param array $Format
     */
    public function setAxisDisplay($AxisID, $Mode = AXIS_FORMAT_DEFAULT, $Format = null)
    {
        if (isset($this->Data["Axis"][$AxisID])) {
            $this->Data["Axis"][$AxisID]["Display"] = $Mode;
            if ($Format != null) {
                $this->Data["Axis"][$AxisID]["Format"] = $Format;
            }
        }
    }
    /**
     * Set the position of an Axis
     * @param int $AxisID
     * @param int $Position
     */
    public function setAxisPosition($AxisID, $Position = AXIS_POSITION_LEFT)
    {
        if (isset($this->Data["Axis"][$AxisID])) {
            $this->Data["Axis"][$AxisID]["Position"] = $Position;
        }
    }
    /**
     * Associate an unit to an axis
     * @param int $AxisID
     * @param string $Unit
     */
    public function setAxisUnit($AxisID, $Unit)
    {
        if (isset($this->Data["Axis"][$AxisID])) {
            $this->Data["Axis"][$AxisID]["Unit"] = $Unit;
        }
    }
    /**
     * Associate a name to an axis
     * @param int $AxisID
     * @param string $Name
     */
    public function setAxisName($AxisID, $Name)
    {
        if (isset($this->Data["Axis"][$AxisID])) {
            $this->Data["Axis"][$AxisID]["Name"] = $Name;
        }
    }
    /**
     * Associate a color to an axis
     * @param int $AxisID
     * @param array $Format
     */
    public function setAxisColor($AxisID, array $Format)
    {
        $R = isset($Format["R"]) ? $Format["R"] : 0;
        $G = isset($Format["G"]) ? $Format["G"] : 0;
        $B = isset($Format["B"]) ? $Format["B"] : 0;
        $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;
        if (isset($this->Data["Axis"][$AxisID])) {
            $this->Data["Axis"][$AxisID]["Color"]["R"] = $R;
            $this->Data["Axis"][$AxisID]["Color"]["G"] = $G;
            $this->Data["Axis"][$AxisID]["Color"]["B"] = $B;
            $this->Data["Axis"][$AxisID]["Color"]["Alpha"] = $Alpha;
        }
    }
    /**
     * Design an axis as X or Y member
     * @param int $AxisID
     * @param int $Identity
     */
    public function setAxisXY($AxisID, $Identity = AXIS_Y)
    {
        if (isset($this->Data["Axis"][$AxisID])) {
            $this->Data["Axis"][$AxisID]["Identity"] = $Identity;
        }
    }
    /**
     * Associate one data serie with one axis
     * @param mixed $Series
     * @param int $AxisID
     */
    public function setSerieOnAxis($Series, $AxisID)
    {
        if (!is_array($Series)) {
            $Series = $this->convertToArray($Series);
        }
        foreach ($Series as $Serie) {
            $PreviousAxis = $this->Data["Series"][$Serie]["Axis"];
            /* Create missing axis */
            if (!isset($this->Data["Axis"][$AxisID])) {
                $this->Data["Axis"][$AxisID]["Position"] = AXIS_POSITION_LEFT;
                $this->Data["Axis"][$AxisID]["Identity"] = AXIS_Y;
            }
            $this->Data["Series"][$Serie]["Axis"] = $AxisID;
            /* Cleanup unused axis */
            $Found = \false;
            foreach ($this->Data["Series"] as $Values) {
                if ($Values["Axis"] == $PreviousAxis) {
                    $Found = \true;
                }
            }
            if (!$Found) {
                unset($this->Data["Axis"][$PreviousAxis]);
            }
        }
    }
    /**
     * Define if a serie should be draw with ticks
     * @param mixed $Series
     * @param int $Width
     */
    public function setSerieTicks($Series, $Width = 0)
    {
        if (!is_array($Series)) {
            $Series = $this->convertToArray($Series);
        }
        foreach ($Series as $Serie) {
            if (isset($this->Data["Series"][$Serie])) {
                $this->Data["Series"][$Serie]["Ticks"] = $Width;
            }
        }
    }
    /**
     * Define if a serie should be draw with a special weight
     * @param mixed $Series
     * @param int $Weight
     */
    public function setSerieWeight($Series, $Weight = 0)
    {
        if (!is_array($Series)) {
            $Series = $this->convertToArray($Series);
        }
        foreach ($Series as $Serie) {
            if (isset($this->Data["Series"][$Serie])) {
                $this->Data["Series"][$Serie]["Weight"] = $Weight;
            }
        }
    }
    /**
     * Returns the palette of the given serie
     * @param type $Serie
     * @return null
     */
    public function getSeriePalette($Serie)
    {
        if (!isset($this->Data["Series"][$Serie])) {
            return null;
        }
        $Result = [];
        $Result["R"] = $this->Data["Series"][$Serie]["Color"]["R"];
        $Result["G"] = $this->Data["Series"][$Serie]["Color"]["G"];
        $Result["B"] = $this->Data["Series"][$Serie]["Color"]["B"];
        $Result["Alpha"] = $this->Data["Series"][$Serie]["Color"]["Alpha"];
        return $Result;
    }
    /**
     * Set the color of one serie
     * @param mixed $Series
     * @param array $Format
     */
    public function setPalette($Series, array $Format = [])
    {
        if (!is_array($Series)) {
            $Series = $this->convertToArray($Series);
        }
        foreach ($Series as $Key => $Serie) {
            $R = isset($Format["R"]) ? $Format["R"] : 0;
            $G = isset($Format["G"]) ? $Format["G"] : 0;
            $B = isset($Format["B"]) ? $Format["B"] : 0;
            $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;
            if (isset($this->Data["Series"][$Serie])) {
                $OldR = $this->Data["Series"][$Serie]["Color"]["R"];
                $OldG = $this->Data["Series"][$Serie]["Color"]["G"];
                $OldB = $this->Data["Series"][$Serie]["Color"]["B"];
                $this->Data["Series"][$Serie]["Color"]["R"] = $R;
                $this->Data["Series"][$Serie]["Color"]["G"] = $G;
                $this->Data["Series"][$Serie]["Color"]["B"] = $B;
                $this->Data["Series"][$Serie]["Color"]["Alpha"] = $Alpha;
                /* Do reverse processing on the internal palette array */
                foreach ($this->Palette as $Key => $Value) {
                    if ($Value["R"] == $OldR && $Value["G"] == $OldG && $Value["B"] == $OldB) {
                        $this->Palette[$Key]["R"] = $R;
                        $this->Palette[$Key]["G"] = $G;
                        $this->Palette[$Key]["B"] = $B;
                        $this->Palette[$Key]["Alpha"] = $Alpha;
                    }
                }
            }
        }
    }
    /**
     * Load a palette file
     * @param string $FileName
     * @param boolean $Overwrite
     * @throws Exception
     */
    public function loadPalette($FileName, $Overwrite = \false)
    {
        $path = file_exists($FileName) ? $FileName : sprintf('%s/../resources/palettes/%s', __DIR__, ltrim($FileName, '/'));
        $fileHandle = @fopen($path, "r");
        if (!$fileHandle) {
            throw new Exception(sprintf('The requested palette "%s" was not found at path "%s"!', $FileName, $path));
        }
        if ($Overwrite) {
            $this->Palette = [];
        }
        while (!feof($fileHandle)) {
            $line = fgets($fileHandle, 4096);
            if (\false === $line) {
                continue;
            }
            $row = explode(',', $line);
            if (empty($row)) {
                continue;
            }
            if (count($row) !== 4) {
                throw new RuntimeException(sprintf('A palette row must supply R, G, B and Alpha components, %s given!', var_export($row, \true)));
            }
            list($R, $G, $B, $Alpha) = $row;
            $ID = count($this->Palette);
            $this->Palette[$ID] = ["R" => trim($R), "G" => trim($G), "B" => trim($B), "Alpha" => trim($Alpha)];
        }
        fclose($fileHandle);
        /* Apply changes to current series */
        $ID = 0;
        if (isset($this->Data["Series"])) {
            foreach ($this->Data["Series"] as $Key => $Value) {
                if (!isset($this->Palette[$ID])) {
                    $this->Data["Series"][$Key]["Color"] = ["R" => 0, "G" => 0, "B" => 0, "Alpha" => 0];
                } else {
                    $this->Data["Series"][$Key]["Color"] = $this->Palette[$ID];
                }
                $ID++;
            }
        }
    }
    /**
     * Initialise a given scatter serie
     * @param int $ID
     * @return null
     */
    public function initScatterSerie($ID)
    {
        if (isset($this->Data["ScatterSeries"][$ID])) {
            return null;
        }
        $this->Data["ScatterSeries"][$ID]["Description"] = "Scatter " . $ID;
        $this->Data["ScatterSeries"][$ID]["isDrawable"] = \true;
        $this->Data["ScatterSeries"][$ID]["Picture"] = null;
        $this->Data["ScatterSeries"][$ID]["Ticks"] = 0;
        $this->Data["ScatterSeries"][$ID]["Weight"] = 0;
        if (isset($this->Palette[$ID])) {
            $this->Data["ScatterSeries"][$ID]["Color"] = $this->Palette[$ID];
        } else {
            $this->Data["ScatterSeries"][$ID]["Color"]["R"] = rand(0, 255);
            $this->Data["ScatterSeries"][$ID]["Color"]["G"] = rand(0, 255);
            $this->Data["ScatterSeries"][$ID]["Color"]["B"] = rand(0, 255);
            $this->Data["ScatterSeries"][$ID]["Color"]["Alpha"] = 100;
        }
    }
    /**
     * Initialise a given serie
     * @param string $Serie
     */
    public function initialise($Serie)
    {
        $ID = 0;
        if (isset($this->Data["Series"])) {
            $ID = count($this->Data["Series"]);
        }
        $this->Data["Series"][$Serie]["Description"] = $Serie;
        $this->Data["Series"][$Serie]["isDrawable"] = \true;
        $this->Data["Series"][$Serie]["Picture"] = null;
        $this->Data["Series"][$Serie]["Max"] = null;
        $this->Data["Series"][$Serie]["Min"] = null;
        $this->Data["Series"][$Serie]["Axis"] = 0;
        $this->Data["Series"][$Serie]["Ticks"] = 0;
        $this->Data["Series"][$Serie]["Weight"] = 0;
        $this->Data["Series"][$Serie]["Shape"] = SERIE_SHAPE_FILLEDCIRCLE;
        if (isset($this->Palette[$ID])) {
            $this->Data["Series"][$Serie]["Color"] = $this->Palette[$ID];
        } else {
            $this->Data["Series"][$Serie]["Color"]["R"] = rand(0, 255);
            $this->Data["Series"][$Serie]["Color"]["G"] = rand(0, 255);
            $this->Data["Series"][$Serie]["Color"]["B"] = rand(0, 255);
            $this->Data["Series"][$Serie]["Color"]["Alpha"] = 100;
        }
        $this->Data["Series"][$Serie]["Data"] = [];
    }
    /**
     * @param int $NormalizationFactor
     * @param mixed $UnitChange
     * @param int $Round
     */
    public function normalize($NormalizationFactor = 100, $UnitChange = null, $Round = 1)
    {
        $Abscissa = $this->Data["Abscissa"];
        $SelectedSeries = [];
        $MaxVal = 0;
        foreach (array_keys($this->Data["Axis"]) as $AxisID) {
            if ($UnitChange != null) {
                $this->Data["Axis"][$AxisID]["Unit"] = $UnitChange;
            }
            foreach ($this->Data["Series"] as $SerieName => $Serie) {
                if ($Serie["Axis"] == $AxisID && $Serie["isDrawable"] == \true && $SerieName != $Abscissa) {
                    $SelectedSeries[$SerieName] = $SerieName;
                    if (count($Serie["Data"]) > $MaxVal) {
                        $MaxVal = count($Serie["Data"]);
                    }
                }
            }
        }
        for ($i = 0; $i <= $MaxVal - 1; $i++) {
            $Factor = 0;
            foreach ($SelectedSeries as $Key => $SerieName) {
                $Value = $this->Data["Series"][$SerieName]["Data"][$i];
                if ($Value != VOID) {
                    $Factor = $Factor + abs($Value);
                }
            }
            if ($Factor != 0) {
                $Factor = $NormalizationFactor / $Factor;
                foreach ($SelectedSeries as $Key => $SerieName) {
                    $Value = $this->Data["Series"][$SerieName]["Data"][$i];
                    if ($Value != VOID && $Factor != $NormalizationFactor) {
                        $this->Data["Series"][$SerieName]["Data"][$i] = round(abs($Value) * $Factor, $Round);
                    } elseif ($Value == VOID || $Value == 0) {
                        $this->Data["Series"][$SerieName]["Data"][$i] = VOID;
                    } elseif ($Factor == $NormalizationFactor) {
                        $this->Data["Series"][$SerieName]["Data"][$i] = $NormalizationFactor;
                    }
                }
            }
        }
        foreach ($SelectedSeries as $Key => $SerieName) {
            $this->Data["Series"][$SerieName]["Max"] = max($this->stripVOID($this->Data["Series"][$SerieName]["Data"]));
            $this->Data["Series"][$SerieName]["Min"] = min($this->stripVOID($this->Data["Series"][$SerieName]["Data"]));
        }
    }
    /**
     * Load data from a CSV (or similar) data source
     * @param string $FileName
     * @param array $Options
     */
    public function importFromCSV($FileName, array $Options = [])
    {
        $Delimiter = isset($Options["Delimiter"]) ? $Options["Delimiter"] : ",";
        $GotHeader = isset($Options["GotHeader"]) ? $Options["GotHeader"] : \false;
        $SkipColumns = isset($Options["SkipColumns"]) ? $Options["SkipColumns"] : [-1];
        $DefaultSerieName = isset($Options["DefaultSerieName"]) ? $Options["DefaultSerieName"] : "Serie";
        $Handle = @fopen($FileName, "r");
        if ($Handle) {
            $HeaderParsed = \false;
            $SerieNames = [];
            while (!feof($Handle)) {
                $Buffer = fgets($Handle, 4096);
                $Buffer = str_replace(chr(10), "", $Buffer);
                $Buffer = str_replace(chr(13), "", $Buffer);
                $Values = preg_split("/" . $Delimiter . "/", $Buffer);
                if ($Buffer != "") {
                    if ($GotHeader && !$HeaderParsed) {
                        foreach ($Values as $Key => $Name) {
                            if (!in_array($Key, $SkipColumns)) {
                                $SerieNames[$Key] = $Name;
                            }
                        }
                        $HeaderParsed = \true;
                    } else {
                        if (!count($SerieNames)) {
                            foreach ($Values as $Key => $Name) {
                                if (!in_array($Key, $SkipColumns)) {
                                    $SerieNames[$Key] = $DefaultSerieName . $Key;
                                }
                            }
                        }
                        foreach ($Values as $Key => $Value) {
                            if (!in_array($Key, $SkipColumns)) {
                                $this->addPoints($Value, $SerieNames[$Key]);
                            }
                        }
                    }
                }
            }
            fclose($Handle);
        }
    }
    /**
     * Create a dataset based on a formula
     *
     * @param string $SerieName
     * @param string $Formula
     * @param array $Options
     * @return null
     */
    public function createFunctionSerie($SerieName, $Formula = "", array $Options = [])
    {
        $MinX = isset($Options["MinX"]) ? $Options["MinX"] : -10;
        $MaxX = isset($Options["MaxX"]) ? $Options["MaxX"] : 10;
        $XStep = isset($Options["XStep"]) ? $Options["XStep"] : 1;
        $AutoDescription = isset($Options["AutoDescription"]) ? $Options["AutoDescription"] : \false;
        $RecordAbscissa = isset($Options["RecordAbscissa"]) ? $Options["RecordAbscissa"] : \false;
        $AbscissaSerie = isset($Options["AbscissaSerie"]) ? $Options["AbscissaSerie"] : "Abscissa";
        if ($Formula == "") {
            return null;
        }
        $Result = [];
        $Abscissa = [];
        for ($i = $MinX; $i <= $MaxX; $i = $i + $XStep) {
            $Expression = "\$return = '!'.(" . str_replace("z", $i, $Formula) . ");";
            if (@eval($Expression) === \false) {
                $return = VOID;
            }
            if ($return == "!") {
                $return = VOID;
            } else {
                $return = $this->right($return, strlen($return) - 1);
            }
            if ($return == "NAN") {
                $return = VOID;
            }
            if ($return == "INF") {
                $return = VOID;
            }
            if ($return == "-INF") {
                $return = VOID;
            }
            $Abscissa[] = $i;
            $Result[] = $return;
        }
        $this->addPoints($Result, $SerieName);
        if ($AutoDescription) {
            $this->setSerieDescription($SerieName, $Formula);
        }
        if ($RecordAbscissa) {
            $this->addPoints($Abscissa, $AbscissaSerie);
        }
    }
    /**
     * @param mixed $Series
     */
    public function negateValues($Series)
    {
        if (!is_array($Series)) {
            $Series = $this->convertToArray($Series);
        }
        foreach ($Series as $Key => $SerieName) {
            if (isset($this->Data["Series"][$SerieName])) {
                $Data = [];
                foreach ($this->Data["Series"][$SerieName]["Data"] as $Key => $Value) {
                    if ($Value == VOID) {
                        $Data[] = VOID;
                    } else {
                        $Data[] = -$Value;
                    }
                }
                $this->Data["Series"][$SerieName]["Data"] = $Data;
                $this->Data["Series"][$SerieName]["Max"] = max($this->stripVOID($this->Data["Series"][$SerieName]["Data"]));
                $this->Data["Series"][$SerieName]["Min"] = min($this->stripVOID($this->Data["Series"][$SerieName]["Data"]));
            }
        }
    }
    /**
     * Return the data & configuration of the series
     * @return array
     */
    public function getData()
    {
        return $this->Data;
    }
    /**
     * Save a palette element
     *
     * @param integer $ID
     * @param string $Color
     */
    public function savePalette($ID, $Color)
    {
        $this->Palette[$ID] = $Color;
    }
    /**
     * Return the palette of the series
     * @return array
     */
    public function getPalette()
    {
        return $this->Palette;
    }
    /**
     * Called by the scaling algorithm to save the config
     * @param mixed $Axis
     */
    public function saveAxisConfig($Axis)
    {
        $this->Data["Axis"] = $Axis;
    }
    /**
     * Save the Y Margin if set
     * @param mixed $Value
     */
    public function saveYMargin($Value)
    {
        $this->Data["YMargin"] = $Value;
    }
    /**
     * Save extended configuration to the pData object
     * @param string $Tag
     * @param mixed $Values
     */
    public function saveExtendedData($Tag, $Values)
    {
        $this->Data["Extended"][$Tag] = $Values;
    }
    /**
     * Called by the scaling algorithm to save the orientation of the scale
     * @param mixed $Orientation
     */
    public function saveOrientation($Orientation)
    {
        $this->Data["Orientation"] = $Orientation;
    }
    /**
     * Convert a string to a single elements array
     * @param mixed $Value
     * @return array
     */
    public function convertToArray($Value)
    {
        return [$Value];
    }
    /**
     * Class string wrapper
     * @return string
     */
    public function __toString()
    {
        return "pData object.";
    }
    /**
     * @param string $value
     * @param int $NbChar
     * @return string
     */
    public function left($value, $NbChar)
    {
        return substr($value, 0, $NbChar);
    }
    /**
     * @param string $value
     * @param int $NbChar
     * @return string
     */
    public function right($value, $NbChar)
    {
        return substr($value, strlen($value) - $NbChar, $NbChar);
    }
    /**
     * @param string $value
     * @param int $Depart
     * @param int $NbChar
     * @return string
     */
    public function mid($value, $Depart, $NbChar)
    {
        return substr($value, $Depart - 1, $NbChar);
    }
}
