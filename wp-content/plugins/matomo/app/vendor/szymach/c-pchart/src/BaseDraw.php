<?php

namespace CpChart;

use Exception;
/**
 * This class exists only to try and reduce the number of methods and properties
 * in the Draw class. Basically all methods not named 'drawX' were moved in here,
 * as well as all the class fields.
 */
abstract class BaseDraw
{
    /**
     * Width of the picture
     * @var int
     */
    public $XSize;
    /**
     * Height of the picture
     * @var int
     */
    public $YSize;
    /**
     * GD picture object
     * @var resource
     */
    public $Picture;
    /**
     * Turn antialias on or off
     * @var boolean
     */
    public $Antialias = \true;
    /**
     * Quality of the antialiasing implementation (0-1)
     * @var int
     */
    public $AntialiasQuality = 0;
    /**
     * Already drawn pixels mask (Filled circle implementation)
     * @var array
     */
    public $Mask = [];
    /**
     * Just to know if we need to flush the alpha channels when rendering
     * @var boolean
     */
    public $TransparentBackground = \false;
    /**
     * Graph area X origin
     * @var int
     */
    public $GraphAreaX1;
    /**
     * Graph area Y origin
     * @var int
     */
    public $GraphAreaY1;
    /**
     * Graph area bottom right X position
     * @var int
     */
    public $GraphAreaX2;
    /**
     * Graph area bottom right Y position
     * @var int
     */
    public $GraphAreaY2;
    /**
     * Minimum height for scale divs
     * @var int
     */
    public $ScaleMinDivHeight = 20;
    /**
     * @var string
     */
    public $FontName = "GeosansLight.ttf";
    /**
     * @var int
     */
    public $FontSize = 12;
    /**
     * Return the bounding box of the last written string
     * @var array
     */
    public $FontBox;
    /**
     * @var int
     */
    public $FontColorR = 0;
    /**
     * @var int
     */
    public $FontColorG = 0;
    /**
     * @var int
     */
    public $FontColorB = 0;
    /**
     * @var int
     */
    public $FontColorA = 100;
    /**
     * Turn shadows on or off
     * @var boolean
     */
    public $Shadow = \false;
    /**
     * X Offset of the shadow
     * @var int
     */
    public $ShadowX;
    /**
     * Y Offset of the shadow
     * @var int
     */
    public $ShadowY;
    /**
     * R component of the shadow
     * @var int
     */
    public $ShadowR;
    /**
     * G component of the shadow
     * @var int
     */
    public $ShadowG;
    /**
     * B component of the shadow
     * @var int
     */
    public $ShadowB;
    /**
     * Alpha level of the shadow
     * @var int
     */
    public $Shadowa;
    /**
     * Array containing the image map
     * @var array
     */
    public $ImageMap = [];
    /**
     * Name of the session array
     * @var int
     */
    public $ImageMapIndex = "pChart";
    /**
     * Save the current imagemap storage mode
     * @var int
     */
    public $ImageMapStorageMode;
    /**
     * Automatic deletion of the image map temp files
     * @var boolean
     */
    public $ImageMapAutoDelete = \true;
    /**
     * Attached dataset
     * @var Data
     */
    public $DataSet;
    /**
     * Last generated chart info
     * Last layout : regular or stacked
     * @var int
     */
    public $LastChartLayout = CHART_LAST_LAYOUT_REGULAR;
    /**
     * @var string
     */
    private $resourcePath;
    public function __construct()
    {
        $this->resourcePath = sprintf('%s/../resources', __DIR__);
        $this->FontName = $this->loadFont($this->FontName, 'fonts');
    }
    /**
     * Set the path to the folder containing library resources (fonts, data, palettes).
     *
     * @param string $path
     * @throws Exception
     */
    public function setResourcePath($path)
    {
        $escapedPath = rtrim($path, '/');
        if (!file_exists($escapedPath)) {
            throw new Exception(sprintf("The path '%s' to resources' folder does not exist!", $escapedPath));
        }
        $this->resourcePath = $escapedPath;
    }
    /**
     * Check if requested resource exists and return the path to it if yes.
     * @param string $name
     * @param string $type
     * @return string
     * @throws Exception
     */
    protected function loadFont($name, $type)
    {
        if (file_exists($name)) {
            return $name;
        }
        $path = sprintf('%s/%s/%s', $this->resourcePath, $type, $name);
        if (file_exists($path)) {
            return $path;
        }
        throw new Exception(sprintf('The requested resource %s (%s) has not been found!', $name, $type));
    }
    /**
     * Allocate a color with transparency
     * @param resource $Picture
     * @param int $R
     * @param int $G
     * @param int $B
     * @param int $Alpha
     * @return int
     */
    public function allocateColor($Picture, $R, $G, $B, $Alpha = 100)
    {
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
        if ($Alpha < 0) {
            $Alpha = 0;
        }
        if ($Alpha > 100) {
            $Alpha = 100;
        }
        $Alpha = $this->convertAlpha($Alpha);
        return imagecolorallocatealpha($Picture, (int) $R, (int) $G, (int) $B, (int) $Alpha);
    }
    /**
     * Convert apha to base 10
     * @param int|float $AlphaValue
     * @return integer
     */
    public function convertAlpha($AlphaValue)
    {
        return floor(127 / 100 * (100 - $AlphaValue));
    }
    /**
     * @param string $FileName
     * @return array
     */
    public function getPicInfo($FileName)
    {
        $Infos = getimagesize($FileName);
        $Width = $Infos[0];
        $Height = $Infos[1];
        $Type = $Infos["mime"];
        if ($Type == "image/png") {
            $Type = 1;
        }
        if ($Type == "image/gif") {
            $Type = 2;
        }
        if ($Type == "image/jpeg ") {
            $Type = 3;
        }
        return [$Width, $Height, $Type];
    }
    /**
     * Compute the scale, check for the best visual factors
     * @param int $XMin
     * @param int $XMax
     * @param int $MaxDivs
     * @param array $Factors
     * @param int $AxisID
     * @return mixed
     */
    public function computeScale($XMin, $XMax, $MaxDivs, array $Factors, $AxisID = 0)
    {
        /* Compute each factors */
        $Results = [];
        foreach ($Factors as $Key => $Factor) {
            $Results[$Factor] = $this->processScale($XMin, $XMax, $MaxDivs, [$Factor], $AxisID);
        }
        /* Remove scales that are creating to much decimals */
        $GoodScaleFactors = [];
        foreach ($Results as $Key => $Result) {
            $Decimals = preg_split("/\\./", $Result["RowHeight"]);
            if (!isset($Decimals[1]) || strlen($Decimals[1]) < 6) {
                $GoodScaleFactors[] = $Key;
            }
        }
        /* Found no correct scale, shame,... returns the 1st one as default */
        if (!count($GoodScaleFactors)) {
            return $Results[$Factors[0]];
        }
        /* Find the factor that cause the maximum number of Rows */
        $MaxRows = 0;
        $BestFactor = 0;
        foreach ($GoodScaleFactors as $Key => $Factor) {
            if ($Results[$Factor]["Rows"] > $MaxRows) {
                $MaxRows = $Results[$Factor]["Rows"];
                $BestFactor = $Factor;
            }
        }
        /* Return the best visual scale */
        return $Results[$BestFactor];
    }
    /**
     * Compute the best matching scale based on size & factors
     * @param int $XMin
     * @param int $XMax
     * @param int $MaxDivs
     * @param array $Factors
     * @param int $AxisID
     * @return array
     */
    public function processScale($XMin, $XMax, $MaxDivs, array $Factors, $AxisID)
    {
        $ScaleHeight = abs(ceil($XMax) - floor($XMin));
        $Format = null;
        if (isset($this->DataSet->Data["Axis"][$AxisID]["Format"])) {
            $Format = $this->DataSet->Data["Axis"][$AxisID]["Format"];
        }
        $Mode = AXIS_FORMAT_DEFAULT;
        if (isset($this->DataSet->Data["Axis"][$AxisID]["Display"])) {
            $Mode = $this->DataSet->Data["Axis"][$AxisID]["Display"];
        }
        $Scale = [];
        if ($XMin != $XMax) {
            $Found = \false;
            $Rescaled = \false;
            $Scaled10Factor = 0.0001;
            $Result = 0;
            while (!$Found) {
                foreach ($Factors as $Key => $Factor) {
                    if (!$Found) {
                        $XMinRescaled = $XMin;
                        if (!($this->modulo($XMin, $Factor * $Scaled10Factor) == 0) || $XMin != floor($XMin)) {
                            $XMinRescaled = floor($XMin / ($Factor * $Scaled10Factor)) * $Factor * $Scaled10Factor;
                        }
                        $XMaxRescaled = $XMax;
                        if (!($this->modulo($XMax, $Factor * $Scaled10Factor) == 0) || $XMax != floor($XMax)) {
                            $XMaxRescaled = floor($XMax / ($Factor * $Scaled10Factor)) * $Factor * $Scaled10Factor + $Factor * $Scaled10Factor;
                        }
                        $ScaleHeightRescaled = abs($XMaxRescaled - $XMinRescaled);
                        if (!$Found && floor($ScaleHeightRescaled / ($Factor * $Scaled10Factor)) <= $MaxDivs) {
                            $Found = \true;
                            $Rescaled = \true;
                            $Result = $Factor * $Scaled10Factor;
                        }
                    }
                }
                $Scaled10Factor = $Scaled10Factor * 10;
            }
            /* ReCall Min / Max / Height */
            if ($Rescaled) {
                $XMin = $XMinRescaled;
                $XMax = $XMaxRescaled;
                $ScaleHeight = $ScaleHeightRescaled;
            }
            /* Compute rows size */
            $Rows = floor($ScaleHeight / $Result);
            if ($Rows == 0) {
                $Rows = 1;
            }
            $RowHeight = $ScaleHeight / $Rows;
            /* Return the results */
            $Scale["Rows"] = $Rows;
            $Scale["RowHeight"] = $RowHeight;
            $Scale["XMin"] = $XMin;
            $Scale["XMax"] = $XMax;
            /* Compute the needed decimals for the metric view to avoid repetition of the same X Axis labels */
            if ($Mode == AXIS_FORMAT_METRIC && $Format == null) {
                $Done = \false;
                $GoodDecimals = 0;
                for ($Decimals = 0; $Decimals <= 10; $Decimals++) {
                    if (!$Done) {
                        $LastLabel = "zob";
                        $ScaleOK = \true;
                        for ($i = 0; $i <= $Rows; $i++) {
                            $Value = $XMin + $i * $RowHeight;
                            $Label = $this->scaleFormat($Value, AXIS_FORMAT_METRIC, $Decimals);
                            if ($LastLabel == $Label) {
                                $ScaleOK = \false;
                            }
                            $LastLabel = $Label;
                        }
                        if ($ScaleOK) {
                            $Done = \true;
                            $GoodDecimals = $Decimals;
                        }
                    }
                }
                $Scale["Format"] = $GoodDecimals;
            }
        } else {
            /* If all values are the same we keep a +1/-1 scale */
            $Rows = 2;
            $XMin = $XMax - 1;
            $XMax = $XMax + 1;
            $RowHeight = 1;
            /* Return the results */
            $Scale["Rows"] = $Rows;
            $Scale["RowHeight"] = $RowHeight;
            $Scale["XMin"] = $XMin;
            $Scale["XMax"] = $XMax;
        }
        return $Scale;
    }
    /**
     *
     * @param int|float $Value1
     * @param int|float $Value2
     * @return double
     */
    public function modulo($Value1, $Value2)
    {
        if (floor($Value2) == 0) {
            return 0;
        }
        if (floor($Value2) != 0) {
            return (int) $Value1 % (int) $Value2;
        }
        $MinValue = min($Value1, $Value2);
        $Factor = 10;
        while (floor($MinValue * $Factor) == 0) {
            $Factor = $Factor * 10;
        }
        return floor($Value1 * $Factor) % floor($Value2 * $Factor);
    }
    /**
     * @param mixed $Value
     * @param mixed $LastValue
     * @param integer $LabelingMethod
     * @param integer $ID
     * @param boolean $LabelSkip
     * @return boolean
     */
    public function isValidLabel($Value, $LastValue, $LabelingMethod, $ID, $LabelSkip)
    {
        if ($LabelingMethod == LABELING_DIFFERENT && $Value != $LastValue) {
            return \true;
        }
        if ($LabelingMethod == LABELING_DIFFERENT && $Value == $LastValue) {
            return \false;
        }
        if ($LabelingMethod == LABELING_ALL && $LabelSkip == 0) {
            return \true;
        }
        if ($LabelingMethod == LABELING_ALL && ($ID + $LabelSkip) % ($LabelSkip + 1) != 1) {
            return \false;
        }
        return \true;
    }
    /**
     * Returns the number of drawable series
     * @return int
     */
    public function countDrawableSeries()
    {
        $count = 0;
        $Data = $this->DataSet->getData();
        foreach ($Data["Series"] as $SerieName => $Serie) {
            if ($Serie["isDrawable"] == \true && $SerieName != $Data["Abscissa"]) {
                $count++;
            }
        }
        return $count;
    }
    /**
     * Fix box coordinates
     * @param int $Xa
     * @param int $Ya
     * @param int $Xb
     * @param int $Yb
     * @return integer[]
     */
    public function fixBoxCoordinates($Xa, $Ya, $Xb, $Yb)
    {
        return [(int) min($Xa, $Xb), (int) min($Ya, $Yb), (int) max($Xa, $Xb), (int) max($Ya, $Yb)];
    }
    /**
     * Apply AALias correction to the rounded box boundaries
     * @param int|float $Value
     * @param int $Mode
     * @return int|float
     */
    public function offsetCorrection($Value, $Mode)
    {
        $Value = round($Value, 1);
        if ($Value == 0 && $Mode != 1) {
            return 0;
        }
        if ($Mode == 1) {
            if ($Value == 0.5) {
                return 0.5;
            }
            if ($Value == 0.8) {
                return 0.6;
            }
            if (in_array($Value, [0.4, 0.7])) {
                return 0.7;
            }
            if (in_array($Value, [0.2, 0.3, 0.6])) {
                return 0.8;
            }
            if (in_array($Value, [0, 1, 0.1, 0.9])) {
                return 0.9;
            }
        }
        if ($Mode == 2) {
            if ($Value == 0.1) {
                return 0.1;
            }
            if ($Value == 0.2) {
                return 0.2;
            }
            if ($Value == 0.3) {
                return 0.3;
            }
            if ($Value == 0.4) {
                return 0.4;
            }
            if ($Value == 0.5) {
                return 0.5;
            }
            if ($Value == 0.7) {
                return 0.7;
            }
            if (in_array($Value, [0.6, 0.8])) {
                return 0.8;
            }
            if (in_array($Value, [1, 0.9])) {
                return 0.9;
            }
        }
        if ($Mode == 3) {
            if (in_array($Value, [1, 0.1])) {
                return 0.1;
            }
            if ($Value == 0.2) {
                return 0.2;
            }
            if ($Value == 0.3) {
                return 0.3;
            }
            if (in_array($Value, [0.4, 0.8])) {
                return 0.4;
            }
            if ($Value == 0.5) {
                return 0.9;
            }
            if ($Value == 0.6) {
                return 0.6;
            }
            if ($Value == 0.7) {
                return 0.7;
            }
            if ($Value == 0.9) {
                return 0.5;
            }
        }
        if ($Mode == 4) {
            if ($Value == 1) {
                return -1;
            }
            if (in_array($Value, [0.1, 0.4, 0.7, 0.8, 0.9])) {
                return 0.1;
            }
            if ($Value == 0.2) {
                return 0.2;
            }
            if ($Value == 0.3) {
                return 0.3;
            }
            if ($Value == 0.5) {
                return -0.1;
            }
            if ($Value == 0.6) {
                return 0.8;
            }
        }
    }
    /**
     * Get the legend box size
     * @param array $Format
     * @return array
     */
    public function getLegendSize(array $Format = [])
    {
        $FontName = isset($Format["FontName"]) ? $this->loadFont($Format["FontName"], 'fonts') : $this->FontName;
        $FontSize = isset($Format["FontSize"]) ? $Format["FontSize"] : $this->FontSize;
        $Margin = isset($Format["Margin"]) ? $Format["Margin"] : 5;
        $Mode = isset($Format["Mode"]) ? $Format["Mode"] : LEGEND_VERTICAL;
        $BoxWidth = isset($Format["BoxWidth"]) ? $Format["BoxWidth"] : 5;
        $BoxHeight = isset($Format["BoxHeight"]) ? $Format["BoxHeight"] : 5;
        $IconAreaWidth = isset($Format["IconAreaWidth"]) ? $Format["IconAreaWidth"] : $BoxWidth;
        $IconAreaHeight = isset($Format["IconAreaHeight"]) ? $Format["IconAreaHeight"] : $BoxHeight;
        $XSpacing = isset($Format["XSpacing"]) ? $Format["XSpacing"] : 5;
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
        $X = 100;
        $Y = 100;
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
        $Width = $Boundaries["R"] + $Margin - ($Boundaries["L"] - $Margin);
        $Height = $Boundaries["B"] + $Margin - ($Boundaries["T"] - $Margin);
        return ["Width" => $Width, "Height" => $Height];
    }
    /**
     * Return the abscissa margin
     * @param array $Data
     * @return int
     */
    public function getAbscissaMargin(array $Data)
    {
        foreach ($Data["Axis"] as $Values) {
            if ($Values["Identity"] == AXIS_X) {
                return $Values["Margin"];
            }
        }
        return 0;
    }
    /**
     * Returns a random color
     * @param int $Alpha
     * @return array
     */
    public function getRandomColor($Alpha = 100)
    {
        return ["R" => rand(0, 255), "G" => rand(0, 255), "B" => rand(0, 255), "Alpha" => $Alpha];
    }
    /**
     * Validate a palette
     * @param mixed $Colors
     * @param int|float $Surrounding
     * @return array
     */
    public function validatePalette($Colors, $Surrounding = null)
    {
        $Result = [];
        if (!is_array($Colors)) {
            return $this->getRandomColor();
        }
        foreach ($Colors as $Key => $Values) {
            if (isset($Values["R"])) {
                $Result[$Key]["R"] = $Values["R"];
            } else {
                $Result[$Key]["R"] = rand(0, 255);
            }
            if (isset($Values["G"])) {
                $Result[$Key]["G"] = $Values["G"];
            } else {
                $Result[$Key]["G"] = rand(0, 255);
            }
            if (isset($Values["B"])) {
                $Result[$Key]["B"] = $Values["B"];
            } else {
                $Result[$Key]["B"] = rand(0, 255);
            }
            if (isset($Values["Alpha"])) {
                $Result[$Key]["Alpha"] = $Values["Alpha"];
            } else {
                $Result[$Key]["Alpha"] = 100;
            }
            if (null !== $Surrounding) {
                $Result[$Key]["BorderR"] = $Result[$Key]["R"] + $Surrounding;
                $Result[$Key]["BorderG"] = $Result[$Key]["G"] + $Surrounding;
                $Result[$Key]["BorderB"] = $Result[$Key]["B"] + $Surrounding;
            } else {
                if (isset($Values["BorderR"])) {
                    $Result[$Key]["BorderR"] = $Values["BorderR"];
                } else {
                    $Result[$Key]["BorderR"] = $Result[$Key]["R"];
                }
                if (isset($Values["BorderG"])) {
                    $Result[$Key]["BorderG"] = $Values["BorderG"];
                } else {
                    $Result[$Key]["BorderG"] = $Result[$Key]["G"];
                }
                if (isset($Values["BorderB"])) {
                    $Result[$Key]["BorderB"] = $Values["BorderB"];
                } else {
                    $Result[$Key]["BorderB"] = $Result[$Key]["B"];
                }
                if (isset($Values["BorderAlpha"])) {
                    $Result[$Key]["BorderAlpha"] = $Values["BorderAlpha"];
                } else {
                    $Result[$Key]["BorderAlpha"] = $Result[$Key]["Alpha"];
                }
            }
        }
        return $Result;
    }
    /**
     * @param mixed $Values
     * @param array $Option
     * @param boolean $ReturnOnly0Height
     * @return int|float|array
     */
    public function scaleComputeY($Values, array $Option = [], $ReturnOnly0Height = \false)
    {
        $AxisID = isset($Option["AxisID"]) ? $Option["AxisID"] : 0;
        $SerieName = isset($Option["SerieName"]) ? $Option["SerieName"] : null;
        $Data = $this->DataSet->getData();
        if (!isset($Data["Axis"][$AxisID])) {
            return -1;
        }
        if ($SerieName != null) {
            $AxisID = $Data["Series"][$SerieName]["Axis"];
        }
        if (!is_array($Values)) {
            $tmp = $Values;
            $Values = [];
            $Values[0] = $tmp;
        }
        $Result = [];
        if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
            $Height = $this->GraphAreaY2 - $this->GraphAreaY1 - $Data["Axis"][$AxisID]["Margin"] * 2;
            $ScaleHeight = $Data["Axis"][$AxisID]["ScaleMax"] - $Data["Axis"][$AxisID]["ScaleMin"];
            $Step = $Height / $ScaleHeight;
            if ($ReturnOnly0Height) {
                foreach ($Values as $Key => $Value) {
                    if ($Value == VOID) {
                        $Result[] = VOID;
                    } else {
                        $Result[] = $Step * $Value;
                    }
                }
            } else {
                foreach ($Values as $Key => $Value) {
                    if ($Value == VOID) {
                        $Result[] = VOID;
                    } else {
                        $Result[] = $this->GraphAreaY2 - $Data["Axis"][$AxisID]["Margin"] - $Step * ($Value - $Data["Axis"][$AxisID]["ScaleMin"]);
                    }
                }
            }
        } else {
            $Width = $this->GraphAreaX2 - $this->GraphAreaX1 - $Data["Axis"][$AxisID]["Margin"] * 2;
            $ScaleWidth = $Data["Axis"][$AxisID]["ScaleMax"] - $Data["Axis"][$AxisID]["ScaleMin"];
            $Step = $Width / $ScaleWidth;
            if ($ReturnOnly0Height) {
                foreach ($Values as $Key => $Value) {
                    if ($Value == VOID) {
                        $Result[] = VOID;
                    } else {
                        $Result[] = $Step * $Value;
                    }
                }
            } else {
                foreach ($Values as $Key => $Value) {
                    if ($Value == VOID) {
                        $Result[] = VOID;
                    } else {
                        $Result[] = $this->GraphAreaX1 + $Data["Axis"][$AxisID]["Margin"] + $Step * ($Value - $Data["Axis"][$AxisID]["ScaleMin"]);
                    }
                }
            }
        }
        return count($Result) == 1 ? reset($Result) : $Result;
    }
    /**
     * Format the axis values
     * @param mixed $Value
     * @param int $Mode
     * @param array $Format
     * @param string $Unit
     * @return string
     */
    public function scaleFormat($Value, $Mode = null, $Format = null, $Unit = null)
    {
        if ($Value == VOID) {
            return "";
        }
        if ($Mode == AXIS_FORMAT_TRAFFIC) {
            if ($Value == 0) {
                return "0B";
            }
            $Units = ["B", "KB", "MB", "GB", "TB", "PB"];
            $Sign = "";
            if ($Value < 0) {
                $Value = abs($Value);
                $Sign = "-";
            }
            $Value = number_format($Value / pow(1024, $Scale = floor(log($Value, 1024))), 2, ",", ".");
            return $Sign . $Value . " " . $Units[$Scale];
        }
        if ($Mode == AXIS_FORMAT_CUSTOM) {
            if (is_callable($Format)) {
                return call_user_func($Format, $Value);
            }
        }
        if ($Mode == AXIS_FORMAT_DATE) {
            $Pattern = "d/m/Y";
            if ($Format !== null) {
                $Pattern = $Format;
            }
            return gmdate($Pattern, $Value);
        }
        if ($Mode == AXIS_FORMAT_TIME) {
            $Pattern = "H:i:s";
            if ($Format !== null) {
                $Pattern = $Format;
            }
            return gmdate($Pattern, $Value);
        }
        if ($Mode == AXIS_FORMAT_CURRENCY) {
            return $Format . number_format($Value, 2);
        }
        if ($Mode == AXIS_FORMAT_METRIC) {
            if (abs($Value) > 1000000000) {
                return round($Value / 1000000000, $Format) . "g" . $Unit;
            }
            if (abs($Value) > 1000000) {
                return round($Value / 1000000, $Format) . "m" . $Unit;
            } elseif (abs($Value) >= 1000) {
                return round($Value / 1000, $Format) . "k" . $Unit;
            }
        }
        return $Value . $Unit;
    }
    /**
     * @return array|null
     */
    public function scaleGetXSettings()
    {
        $Data = $this->DataSet->getData();
        foreach ($Data["Axis"] as $Settings) {
            if ($Settings["Identity"] == AXIS_X) {
                return [$Settings["Margin"], $Settings["Rows"]];
            }
        }
    }
    /**
     * Write Max value on a chart
     * @param int $Type
     * @param array $Format
     */
    public function writeBounds($Type = BOUND_BOTH, $Format = null)
    {
        $MaxLabelTxt = isset($Format["MaxLabelTxt"]) ? $Format["MaxLabelTxt"] : "max=";
        $MinLabelTxt = isset($Format["MinLabelTxt"]) ? $Format["MinLabelTxt"] : "min=";
        $Decimals = isset($Format["Decimals"]) ? $Format["Decimals"] : 1;
        $ExcludedSeries = isset($Format["ExcludedSeries"]) ? $Format["ExcludedSeries"] : "";
        $DisplayOffset = isset($Format["DisplayOffset"]) ? $Format["DisplayOffset"] : 4;
        $DisplayColor = isset($Format["DisplayColor"]) ? $Format["DisplayColor"] : DISPLAY_MANUAL;
        $MaxDisplayR = isset($Format["MaxDisplayR"]) ? $Format["MaxDisplayR"] : 0;
        $MaxDisplayG = isset($Format["MaxDisplayG"]) ? $Format["MaxDisplayG"] : 0;
        $MaxDisplayB = isset($Format["MaxDisplayB"]) ? $Format["MaxDisplayB"] : 0;
        $MinDisplayR = isset($Format["MinDisplayR"]) ? $Format["MinDisplayR"] : 255;
        $MinDisplayG = isset($Format["MinDisplayG"]) ? $Format["MinDisplayG"] : 255;
        $MinDisplayB = isset($Format["MinDisplayB"]) ? $Format["MinDisplayB"] : 255;
        $MinLabelPos = isset($Format["MinLabelPos"]) ? $Format["MinLabelPos"] : BOUND_LABEL_POS_AUTO;
        $MaxLabelPos = isset($Format["MaxLabelPos"]) ? $Format["MaxLabelPos"] : BOUND_LABEL_POS_AUTO;
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
        $CaptionSettings = ["DrawBox" => $DrawBox, "DrawBoxBorder" => $DrawBoxBorder, "BorderOffset" => $BorderOffset, "BoxRounded" => $BoxRounded, "RoundedRadius" => $RoundedRadius, "BoxR" => $BoxR, "BoxG" => $BoxG, "BoxB" => $BoxB, "BoxAlpha" => $BoxAlpha, "BoxSurrounding" => $BoxSurrounding, "BoxBorderR" => $BoxBorderR, "BoxBorderG" => $BoxBorderG, "BoxBorderB" => $BoxBorderB, "BoxBorderAlpha" => $BoxBorderAlpha];
        list($XMargin, $XDivs) = $this->scaleGetXSettings();
        $Data = $this->DataSet->getData();
        foreach ($Data["Series"] as $SerieName => $Serie) {
            if ($Serie["isDrawable"] == \true && $SerieName != $Data["Abscissa"] && !isset($ExcludedSeries[$SerieName])) {
                $R = $Serie["Color"]["R"];
                $G = $Serie["Color"]["G"];
                $B = $Serie["Color"]["B"];
                $MinValue = $this->DataSet->getMin($SerieName);
                $MaxValue = $this->DataSet->getMax($SerieName);
                $MinPos = VOID;
                $MaxPos = VOID;
                foreach ($Serie["Data"] as $Key => $Value) {
                    if ($Value == $MinValue && $MinPos == VOID) {
                        $MinPos = $Key;
                    }
                    if ($Value == $MaxValue) {
                        $MaxPos = $Key;
                    }
                }
                $AxisID = $Serie["Axis"];
                $Mode = $Data["Axis"][$AxisID]["Display"];
                $Format = $Data["Axis"][$AxisID]["Format"];
                $Unit = $Data["Axis"][$AxisID]["Unit"];
                $PosArray = $this->scaleComputeY($Serie["Data"], ["AxisID" => $Serie["Axis"]]);
                if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
                    $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
                    $X = $this->GraphAreaX1 + $XMargin;
                    $SerieOffset = isset($Serie["XOffset"]) ? $Serie["XOffset"] : 0;
                    if ($Type == BOUND_MAX || $Type == BOUND_BOTH) {
                        if ($MaxLabelPos == BOUND_LABEL_POS_TOP || $MaxLabelPos == BOUND_LABEL_POS_AUTO && $MaxValue >= 0) {
                            $YPos = $PosArray[$MaxPos] - $DisplayOffset + 2;
                            $Align = TEXT_ALIGN_BOTTOMMIDDLE;
                        }
                        if ($MaxLabelPos == BOUND_LABEL_POS_BOTTOM || $MaxLabelPos == BOUND_LABEL_POS_AUTO && $MaxValue < 0) {
                            $YPos = $PosArray[$MaxPos] + $DisplayOffset + 2;
                            $Align = TEXT_ALIGN_TOPMIDDLE;
                        }
                        $XPos = $X + $MaxPos * $XStep + $SerieOffset;
                        $Label = sprintf('%s%s', $MaxLabelTxt, $this->scaleFormat(round($MaxValue, $Decimals), $Mode, $Format, $Unit));
                        $TxtPos = $this->getTextBox($XPos, $YPos, $this->FontName, $this->FontSize, 0, $Label);
                        $XOffset = 0;
                        $YOffset = 0;
                        if ($TxtPos[0]["X"] < $this->GraphAreaX1) {
                            $XOffset = ($this->GraphAreaX1 - $TxtPos[0]["X"]) / 2;
                        }
                        if ($TxtPos[1]["X"] > $this->GraphAreaX2) {
                            $XOffset = -(($TxtPos[1]["X"] - $this->GraphAreaX2) / 2);
                        }
                        if ($TxtPos[2]["Y"] < $this->GraphAreaY1) {
                            $YOffset = $this->GraphAreaY1 - $TxtPos[2]["Y"];
                        }
                        if ($TxtPos[0]["Y"] > $this->GraphAreaY2) {
                            $YOffset = -($TxtPos[0]["Y"] - $this->GraphAreaY2);
                        }
                        $CaptionSettings["R"] = $MaxDisplayR;
                        $CaptionSettings["G"] = $MaxDisplayG;
                        $CaptionSettings["B"] = $MaxDisplayB;
                        $CaptionSettings["Align"] = $Align;
                        $this->drawText($XPos + $XOffset, $YPos + $YOffset, $Label, $CaptionSettings);
                    }
                    if ($Type == BOUND_MIN || $Type == BOUND_BOTH) {
                        if ($MinLabelPos == BOUND_LABEL_POS_TOP || $MinLabelPos == BOUND_LABEL_POS_AUTO && $MinValue >= 0) {
                            $YPos = $PosArray[$MinPos] - $DisplayOffset + 2;
                            $Align = TEXT_ALIGN_BOTTOMMIDDLE;
                        }
                        if ($MinLabelPos == BOUND_LABEL_POS_BOTTOM || $MinLabelPos == BOUND_LABEL_POS_AUTO && $MinValue < 0) {
                            $YPos = $PosArray[$MinPos] + $DisplayOffset + 2;
                            $Align = TEXT_ALIGN_TOPMIDDLE;
                        }
                        $XPos = $X + $MinPos * $XStep + $SerieOffset;
                        $Label = sprintf('%s%s', $MinLabelTxt, $this->scaleFormat(round($MinValue, $Decimals), $Mode, $Format, $Unit));
                        $TxtPos = $this->getTextBox($XPos, $YPos, $this->FontName, $this->FontSize, 0, $Label);
                        $XOffset = 0;
                        $YOffset = 0;
                        if ($TxtPos[0]["X"] < $this->GraphAreaX1) {
                            $XOffset = ($this->GraphAreaX1 - $TxtPos[0]["X"]) / 2;
                        }
                        if ($TxtPos[1]["X"] > $this->GraphAreaX2) {
                            $XOffset = -(($TxtPos[1]["X"] - $this->GraphAreaX2) / 2);
                        }
                        if ($TxtPos[2]["Y"] < $this->GraphAreaY1) {
                            $YOffset = $this->GraphAreaY1 - $TxtPos[2]["Y"];
                        }
                        if ($TxtPos[0]["Y"] > $this->GraphAreaY2) {
                            $YOffset = -($TxtPos[0]["Y"] - $this->GraphAreaY2);
                        }
                        $CaptionSettings["R"] = $MinDisplayR;
                        $CaptionSettings["G"] = $MinDisplayG;
                        $CaptionSettings["B"] = $MinDisplayB;
                        $CaptionSettings["Align"] = $Align;
                        $this->drawText($XPos + $XOffset, $YPos - $DisplayOffset + $YOffset, $Label, $CaptionSettings);
                    }
                } else {
                    $XStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
                    $X = $this->GraphAreaY1 + $XMargin;
                    $SerieOffset = isset($Serie["XOffset"]) ? $Serie["XOffset"] : 0;
                    if ($Type == BOUND_MAX || $Type == BOUND_BOTH) {
                        if ($MaxLabelPos == BOUND_LABEL_POS_TOP || $MaxLabelPos == BOUND_LABEL_POS_AUTO && $MaxValue >= 0) {
                            $YPos = $PosArray[$MaxPos] + $DisplayOffset + 2;
                            $Align = TEXT_ALIGN_MIDDLELEFT;
                        }
                        if ($MaxLabelPos == BOUND_LABEL_POS_BOTTOM || $MaxLabelPos == BOUND_LABEL_POS_AUTO && $MaxValue < 0) {
                            $YPos = $PosArray[$MaxPos] - $DisplayOffset + 2;
                            $Align = TEXT_ALIGN_MIDDLERIGHT;
                        }
                        $XPos = $X + $MaxPos * $XStep + $SerieOffset;
                        $Label = $MaxLabelTxt . $this->scaleFormat($MaxValue, $Mode, $Format, $Unit);
                        $TxtPos = $this->getTextBox($YPos, $XPos, $this->FontName, $this->FontSize, 0, $Label);
                        $XOffset = 0;
                        $YOffset = 0;
                        if ($TxtPos[0]["X"] < $this->GraphAreaX1) {
                            $XOffset = $this->GraphAreaX1 - $TxtPos[0]["X"];
                        }
                        if ($TxtPos[1]["X"] > $this->GraphAreaX2) {
                            $XOffset = -($TxtPos[1]["X"] - $this->GraphAreaX2);
                        }
                        if ($TxtPos[2]["Y"] < $this->GraphAreaY1) {
                            $YOffset = ($this->GraphAreaY1 - $TxtPos[2]["Y"]) / 2;
                        }
                        if ($TxtPos[0]["Y"] > $this->GraphAreaY2) {
                            $YOffset = -(($TxtPos[0]["Y"] - $this->GraphAreaY2) / 2);
                        }
                        $CaptionSettings["R"] = $MaxDisplayR;
                        $CaptionSettings["G"] = $MaxDisplayG;
                        $CaptionSettings["B"] = $MaxDisplayB;
                        $CaptionSettings["Align"] = $Align;
                        $this->drawText($YPos + $XOffset, $XPos + $YOffset, $Label, $CaptionSettings);
                    }
                    if ($Type == BOUND_MIN || $Type == BOUND_BOTH) {
                        if ($MinLabelPos == BOUND_LABEL_POS_TOP || $MinLabelPos == BOUND_LABEL_POS_AUTO && $MinValue >= 0) {
                            $YPos = $PosArray[$MinPos] + $DisplayOffset + 2;
                            $Align = TEXT_ALIGN_MIDDLELEFT;
                        }
                        if ($MinLabelPos == BOUND_LABEL_POS_BOTTOM || $MinLabelPos == BOUND_LABEL_POS_AUTO && $MinValue < 0) {
                            $YPos = $PosArray[$MinPos] - $DisplayOffset + 2;
                            $Align = TEXT_ALIGN_MIDDLERIGHT;
                        }
                        $XPos = $X + $MinPos * $XStep + $SerieOffset;
                        $Label = $MinLabelTxt . $this->scaleFormat($MinValue, $Mode, $Format, $Unit);
                        $TxtPos = $this->getTextBox($YPos, $XPos, $this->FontName, $this->FontSize, 0, $Label);
                        $XOffset = 0;
                        $YOffset = 0;
                        if ($TxtPos[0]["X"] < $this->GraphAreaX1) {
                            $XOffset = $this->GraphAreaX1 - $TxtPos[0]["X"];
                        }
                        if ($TxtPos[1]["X"] > $this->GraphAreaX2) {
                            $XOffset = -($TxtPos[1]["X"] - $this->GraphAreaX2);
                        }
                        if ($TxtPos[2]["Y"] < $this->GraphAreaY1) {
                            $YOffset = ($this->GraphAreaY1 - $TxtPos[2]["Y"]) / 2;
                        }
                        if ($TxtPos[0]["Y"] > $this->GraphAreaY2) {
                            $YOffset = -(($TxtPos[0]["Y"] - $this->GraphAreaY2) / 2);
                        }
                        $CaptionSettings["R"] = $MinDisplayR;
                        $CaptionSettings["G"] = $MinDisplayG;
                        $CaptionSettings["B"] = $MinDisplayB;
                        $CaptionSettings["Align"] = $Align;
                        $this->drawText($YPos + $XOffset, $XPos + $YOffset, $Label, $CaptionSettings);
                    }
                }
            }
        }
    }
    /**
     * Write labels
     * @param string $SeriesName
     * @param array $Indexes
     * @param array $Format
     */
    public function writeLabel($SeriesName, $Indexes, array $Format = [])
    {
        $OverrideTitle = isset($Format["OverrideTitle"]) ? $Format["OverrideTitle"] : null;
        $ForceLabels = isset($Format["ForceLabels"]) ? $Format["ForceLabels"] : null;
        $DrawPoint = isset($Format["DrawPoint"]) ? $Format["DrawPoint"] : LABEL_POINT_BOX;
        $DrawVerticalLine = isset($Format["DrawVerticalLine"]) ? $Format["DrawVerticalLine"] : \false;
        $VerticalLineR = isset($Format["VerticalLineR"]) ? $Format["VerticalLineR"] : 0;
        $VerticalLineG = isset($Format["VerticalLineG"]) ? $Format["VerticalLineG"] : 0;
        $VerticalLineB = isset($Format["VerticalLineB"]) ? $Format["VerticalLineB"] : 0;
        $VerticalLineAlpha = isset($Format["VerticalLineAlpha"]) ? $Format["VerticalLineAlpha"] : 40;
        $VerticalLineTicks = isset($Format["VerticalLineTicks"]) ? $Format["VerticalLineTicks"] : 2;
        $Data = $this->DataSet->getData();
        list($XMargin, $XDivs) = $this->scaleGetXSettings();
        if (!is_array($Indexes)) {
            $Index = $Indexes;
            $Indexes = [];
            $Indexes[] = $Index;
        }
        if (!is_array($SeriesName)) {
            $SerieName = $SeriesName;
            $SeriesName = [];
            $SeriesName[] = $SerieName;
        }
        if ($ForceLabels != null && !is_array($ForceLabels)) {
            $ForceLabel = $ForceLabels;
            $ForceLabels = [];
            $ForceLabels[] = $ForceLabel;
        }
        foreach ($Indexes as $Key => $Index) {
            $Series = [];
            if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
                if ($XDivs == 0) {
                    $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1) / 4;
                } else {
                    $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
                }
                $X = $this->GraphAreaX1 + $XMargin + $Index * $XStep;
                if ($DrawVerticalLine) {
                    $this->drawLine($X, $this->GraphAreaY1 + $Data["YMargin"], $X, $this->GraphAreaY2 - $Data["YMargin"], ["R" => $VerticalLineR, "G" => $VerticalLineG, "B" => $VerticalLineB, "Alpha" => $VerticalLineAlpha, "Ticks" => $VerticalLineTicks]);
                }
                $MinY = $this->GraphAreaY2;
                foreach ($SeriesName as $SerieName) {
                    if (isset($Data["Series"][$SerieName]["Data"][$Index])) {
                        $AxisID = $Data["Series"][$SerieName]["Axis"];
                        $XAxisMode = $Data["XAxisDisplay"];
                        $XAxisFormat = $Data["XAxisFormat"];
                        $XAxisUnit = $Data["XAxisUnit"];
                        $AxisMode = $Data["Axis"][$AxisID]["Display"];
                        $AxisFormat = $Data["Axis"][$AxisID]["Format"];
                        $AxisUnit = $Data["Axis"][$AxisID]["Unit"];
                        $XLabel = "";
                        if (isset($Data["Abscissa"]) && isset($Data["Series"][$Data["Abscissa"]]["Data"][$Index])) {
                            $XLabel = $this->scaleFormat($Data["Series"][$Data["Abscissa"]]["Data"][$Index], $XAxisMode, $XAxisFormat, $XAxisUnit);
                        }
                        if ($OverrideTitle != null) {
                            $Description = $OverrideTitle;
                        } elseif (count($SeriesName) == 1) {
                            $Description = $Data["Series"][$SerieName]["Description"] . " - " . $XLabel;
                        } elseif (isset($Data["Abscissa"]) && isset($Data["Series"][$Data["Abscissa"]]["Data"][$Index])) {
                            $Description = $XLabel;
                        }
                        $Serie = ["R" => $Data["Series"][$SerieName]["Color"]["R"], "G" => $Data["Series"][$SerieName]["Color"]["G"], "B" => $Data["Series"][$SerieName]["Color"]["B"], "Alpha" => $Data["Series"][$SerieName]["Color"]["Alpha"]];
                        if (count($SeriesName) == 1 && isset($Data["Series"][$SerieName]["XOffset"])) {
                            $SerieOffset = $Data["Series"][$SerieName]["XOffset"];
                        } else {
                            $SerieOffset = 0;
                        }
                        $Value = $Data["Series"][$SerieName]["Data"][$Index];
                        if ($Value == VOID) {
                            $Value = "NaN";
                        }
                        if ($ForceLabels != null) {
                            $Caption = isset($ForceLabels[$Key]) ? $ForceLabels[$Key] : "Not set";
                        } else {
                            $Caption = $this->scaleFormat($Value, $AxisMode, $AxisFormat, $AxisUnit);
                        }
                        if ($this->LastChartLayout == CHART_LAST_LAYOUT_STACKED) {
                            if ($Value >= 0) {
                                $LookFor = "+";
                            } else {
                                $LookFor = "-";
                            }
                            $Value = 0;
                            $Done = \false;
                            foreach ($Data["Series"] as $Name => $SerieLookup) {
                                if ($SerieLookup["isDrawable"] == \true && $Name != $Data["Abscissa"] && !$Done) {
                                    if (isset($Data["Series"][$Name]["Data"][$Index]) && $Data["Series"][$Name]["Data"][$Index] != VOID) {
                                        if ($Data["Series"][$Name]["Data"][$Index] >= 0 && $LookFor == "+") {
                                            $Value = $Value + $Data["Series"][$Name]["Data"][$Index];
                                        }
                                        if ($Data["Series"][$Name]["Data"][$Index] < 0 && $LookFor == "-") {
                                            $Value = $Value - $Data["Series"][$Name]["Data"][$Index];
                                        }
                                        if ($Name == $SerieName) {
                                            $Done = \true;
                                        }
                                    }
                                }
                            }
                        }
                        $X = floor($this->GraphAreaX1 + $XMargin + $Index * $XStep + $SerieOffset);
                        $Y = floor($this->scaleComputeY($Value, ["AxisID" => $AxisID]));
                        if ($Y < $MinY) {
                            $MinY = $Y;
                        }
                        if ($DrawPoint == LABEL_POINT_CIRCLE) {
                            $this->drawFilledCircle($X, $Y, 3, ["R" => 255, "G" => 255, "B" => 255, "BorderR" => 0, "BorderG" => 0, "BorderB" => 0]);
                        } elseif ($DrawPoint == LABEL_POINT_BOX) {
                            $this->drawFilledRectangle($X - 2, $Y - 2, $X + 2, $Y + 2, ["R" => 255, "G" => 255, "B" => 255, "BorderR" => 0, "BorderG" => 0, "BorderB" => 0]);
                        }
                        $Series[] = ["Format" => $Serie, "Caption" => $Caption];
                    }
                }
                $this->drawLabelBox($X, $MinY - 3, $Description, $Series, $Format);
            } else {
                if ($XDivs == 0) {
                    $XStep = ($this->GraphAreaY2 - $this->GraphAreaY1) / 4;
                } else {
                    $XStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
                }
                $Y = $this->GraphAreaY1 + $XMargin + $Index * $XStep;
                if ($DrawVerticalLine) {
                    $this->drawLine($this->GraphAreaX1 + $Data["YMargin"], $Y, $this->GraphAreaX2 - $Data["YMargin"], $Y, ["R" => $VerticalLineR, "G" => $VerticalLineG, "B" => $VerticalLineB, "Alpha" => $VerticalLineAlpha, "Ticks" => $VerticalLineTicks]);
                }
                $MinX = $this->GraphAreaX2;
                foreach ($SeriesName as $Key => $SerieName) {
                    if (isset($Data["Series"][$SerieName]["Data"][$Index])) {
                        $AxisID = $Data["Series"][$SerieName]["Axis"];
                        $XAxisMode = $Data["XAxisDisplay"];
                        $XAxisFormat = $Data["XAxisFormat"];
                        $XAxisUnit = $Data["XAxisUnit"];
                        $AxisMode = $Data["Axis"][$AxisID]["Display"];
                        $AxisFormat = $Data["Axis"][$AxisID]["Format"];
                        $AxisUnit = $Data["Axis"][$AxisID]["Unit"];
                        $XLabel = "";
                        if (isset($Data["Abscissa"]) && isset($Data["Series"][$Data["Abscissa"]]["Data"][$Index])) {
                            $XLabel = $this->scaleFormat($Data["Series"][$Data["Abscissa"]]["Data"][$Index], $XAxisMode, $XAxisFormat, $XAxisUnit);
                        }
                        if ($OverrideTitle != null) {
                            $Description = $OverrideTitle;
                        } elseif (count($SeriesName) == 1) {
                            if (isset($Data["Abscissa"]) && isset($Data["Series"][$Data["Abscissa"]]["Data"][$Index])) {
                                $Description = $Data["Series"][$SerieName]["Description"] . " - " . $XLabel;
                            }
                        } elseif (isset($Data["Abscissa"]) && isset($Data["Series"][$Data["Abscissa"]]["Data"][$Index])) {
                            $Description = $XLabel;
                        }
                        $Serie = [];
                        if (isset($Data["Extended"]["Palette"][$Index])) {
                            $Serie["R"] = $Data["Extended"]["Palette"][$Index]["R"];
                            $Serie["G"] = $Data["Extended"]["Palette"][$Index]["G"];
                            $Serie["B"] = $Data["Extended"]["Palette"][$Index]["B"];
                            $Serie["Alpha"] = $Data["Extended"]["Palette"][$Index]["Alpha"];
                        } else {
                            $Serie["R"] = $Data["Series"][$SerieName]["Color"]["R"];
                            $Serie["G"] = $Data["Series"][$SerieName]["Color"]["G"];
                            $Serie["B"] = $Data["Series"][$SerieName]["Color"]["B"];
                            $Serie["Alpha"] = $Data["Series"][$SerieName]["Color"]["Alpha"];
                        }
                        if (count($SeriesName) == 1 && isset($Data["Series"][$SerieName]["XOffset"])) {
                            $SerieOffset = $Data["Series"][$SerieName]["XOffset"];
                        } else {
                            $SerieOffset = 0;
                        }
                        $Value = $Data["Series"][$SerieName]["Data"][$Index];
                        if ($ForceLabels != null) {
                            $Caption = isset($ForceLabels[$Key]) ? $ForceLabels[$Key] : "Not set";
                        } else {
                            $Caption = $this->scaleFormat($Value, $AxisMode, $AxisFormat, $AxisUnit);
                        }
                        if ($Value == VOID) {
                            $Value = "NaN";
                        }
                        if ($this->LastChartLayout == CHART_LAST_LAYOUT_STACKED) {
                            if ($Value >= 0) {
                                $LookFor = "+";
                            } else {
                                $LookFor = "-";
                            }
                            $Value = 0;
                            $Done = \false;
                            foreach ($Data["Series"] as $Name => $SerieLookup) {
                                if ($SerieLookup["isDrawable"] == \true && $Name != $Data["Abscissa"] && !$Done) {
                                    if (isset($Data["Series"][$Name]["Data"][$Index]) && $Data["Series"][$Name]["Data"][$Index] != VOID) {
                                        if ($Data["Series"][$Name]["Data"][$Index] >= 0 && $LookFor == "+") {
                                            $Value = $Value + $Data["Series"][$Name]["Data"][$Index];
                                        }
                                        if ($Data["Series"][$Name]["Data"][$Index] < 0 && $LookFor == "-") {
                                            $Value = $Value - $Data["Series"][$Name]["Data"][$Index];
                                        }
                                        if ($Name == $SerieName) {
                                            $Done = \true;
                                        }
                                    }
                                }
                            }
                        }
                        $X = floor($this->scaleComputeY($Value, ["AxisID" => $AxisID]));
                        $Y = floor($this->GraphAreaY1 + $XMargin + $Index * $XStep + $SerieOffset);
                        if ($X < $MinX) {
                            $MinX = $X;
                        }
                        if ($DrawPoint == LABEL_POINT_CIRCLE) {
                            $this->drawFilledCircle($X, $Y, 3, ["R" => 255, "G" => 255, "B" => 255, "BorderR" => 0, "BorderG" => 0, "BorderB" => 0]);
                        } elseif ($DrawPoint == LABEL_POINT_BOX) {
                            $this->drawFilledRectangle($X - 2, $Y - 2, $X + 2, $Y + 2, ["R" => 255, "G" => 255, "B" => 255, "BorderR" => 0, "BorderG" => 0, "BorderB" => 0]);
                        }
                        $Series[] = ["Format" => $Serie, "Caption" => $Caption];
                    }
                }
                $this->drawLabelBox($MinX, $Y - 3, $Description, $Series, $Format);
            }
        }
    }
    /**
     * @param GdImage|resource $image
     * @param array $points
     * @param int $numPoints
     * @param int $color
     * @return void
     */
    protected function imageFilledPolygonWrapper($image, array $points, $numPoints, $color)
    {
        if (version_compare(\PHP_VERSION, '8.1.0') === -1) {
            imagefilledpolygon($image, $points, $numPoints, $color);
        } else {
            imagefilledpolygon($image, $points, $color);
        }
    }
}
