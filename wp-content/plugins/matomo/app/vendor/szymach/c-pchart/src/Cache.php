<?php

namespace CpChart;

/**
 * Cache - speed up the rendering by caching up the pictures
 *
 * Version     : 2.1.4
 * Made by     : Jean-Damien POGOLOTTI
 * Last Update : 19/01/2014
 *
 * This file can be distributed under the license you can find at :
 *
 *                http://www.pchart.net/license
 *
 * You can find the whole class documentation on the pChart web site.
 */
class Cache
{
    /**
     * @var string
     */
    public $CacheFolder;
    /**
     * @var string
     */
    public $CacheIndex;
    /**
     * @var string
     */
    public $CacheDB;
    /**
     * @param array $Settings
     */
    public function __construct(array $Settings = [])
    {
        $CacheFolder = isset($Settings["CacheFolder"]) ? $Settings["CacheFolder"] : __DIR__ . "/../cache";
        $CacheIndex = isset($Settings["CacheIndex"]) ? $Settings["CacheIndex"] : "index.db";
        $CacheDB = isset($Settings["CacheDB"]) ? $Settings["CacheDB"] : "cache.db";
        $this->CacheFolder = $CacheFolder;
        $this->CacheIndex = $CacheIndex;
        $this->CacheDB = $CacheDB;
        if (!file_exists($this->CacheFolder . "/" . $this->CacheIndex)) {
            touch($this->CacheFolder . "/" . $this->CacheIndex);
        }
        if (!file_exists($this->CacheFolder . "/" . $this->CacheDB)) {
            touch($this->CacheFolder . "/" . $this->CacheDB);
        }
    }
    /**
     * Flush the cache contents
     */
    public function flush()
    {
        if (file_exists($this->CacheFolder . "/" . $this->CacheIndex)) {
            unlink($this->CacheFolder . "/" . $this->CacheIndex);
            touch($this->CacheFolder . "/" . $this->CacheIndex);
        }
        if (file_exists($this->CacheFolder . "/" . $this->CacheDB)) {
            unlink($this->CacheFolder . "/" . $this->CacheDB);
            touch($this->CacheFolder . "/" . $this->CacheDB);
        }
    }
    /**
     * Return the MD5 of the data array to clearly identify the chart
     *
     * @param Data $Data
     * @param string $Marker
     * @return string
     */
    public function getHash(\CpChart\Data $Data, $Marker = "")
    {
        return md5($Marker . serialize($Data->Data));
    }
    /**
     * Write the generated picture to the cache
     *
     * @param string $ID
     * @param Image $pChartObject
     */
    public function writeToCache($ID, \CpChart\Image $pChartObject)
    {
        /* Compute the paths */
        $TemporaryFile = tempnam($this->CacheFolder, "tmp_");
        $Database = $this->CacheFolder . "/" . $this->CacheDB;
        $Index = $this->CacheFolder . "/" . $this->CacheIndex;
        /* Flush the picture to a temporary file */
        imagepng($pChartObject->Picture, $TemporaryFile);
        /* Retrieve the files size */
        $PictureSize = filesize($TemporaryFile);
        $DBSize = filesize($Database);
        /* Save the index */
        $Handle = fopen($Index, "a");
        fwrite($Handle, $ID . "," . $DBSize . "," . $PictureSize . "," . time() . ",0\r\n");
        fclose($Handle);
        /* Get the picture raw contents */
        $Handle = fopen($TemporaryFile, "r");
        $Raw = fread($Handle, $PictureSize);
        fclose($Handle);
        /* Save the picture in the solid database file */
        $Handle = fopen($Database, "a");
        fwrite($Handle, $Raw);
        fclose($Handle);
        /* Remove temporary file */
        unlink($TemporaryFile);
    }
    /**
     * Remove object older than the specified TS
     * @param int $Expiry
     */
    public function removeOlderThan($Expiry)
    {
        $this->dbRemoval(["Expiry" => $Expiry]);
    }
    /**
     * Remove an object from the cache
     * @param string $ID
     */
    public function remove($ID)
    {
        $this->dbRemoval(["Name" => $ID]);
    }
    /**
     * Remove with specified criterias.
     *
     * @param array $Settings
     * @return int
     */
    public function dbRemoval(array $Settings)
    {
        $ID = isset($Settings["Name"]) ? $Settings["Name"] : null;
        $Expiry = isset($Settings["Expiry"]) ? $Settings["Expiry"] : -(24 * 60 * 60);
        $TS = time() - $Expiry;
        /* Compute the paths */
        $Database = $this->CacheFolder . "/" . $this->CacheDB;
        $Index = $this->CacheFolder . "/" . $this->CacheIndex;
        $DatabaseTemp = $this->CacheFolder . "/" . $this->CacheDB . ".tmp";
        $IndexTemp = $this->CacheFolder . "/" . $this->CacheIndex . ".tmp";
        /* Single file removal */
        if ($ID != null) {
            /* Retrieve object informations */
            $Object = $this->isInCache($ID, \true);
            /* If it's not in the cache DB, go away */
            if (!$Object) {
                return 0;
            }
        }
        /* Create the temporary files */
        if (!file_exists($DatabaseTemp)) {
            touch($DatabaseTemp);
        }
        if (!file_exists($IndexTemp)) {
            touch($IndexTemp);
        }
        /* Open the file handles */
        $IndexHandle = @fopen($Index, "r");
        $IndexTempHandle = @fopen($IndexTemp, "w");
        $DBHandle = @fopen($Database, "r");
        $DBTempHandle = @fopen($DatabaseTemp, "w");
        /* Remove the selected ID from the database */
        while (!feof($IndexHandle)) {
            $Entry = fgets($IndexHandle, 4096);
            $Entry = str_replace("\r", "", $Entry);
            $Entry = str_replace("\n", "", $Entry);
            $Settings = preg_split("/,/", $Entry);
            if ($Entry != "") {
                $PicID = $Settings[0];
                $DBPos = $Settings[1];
                $PicSize = $Settings[2];
                $GeneratedTS = $Settings[3];
                $Hits = $Settings[4];
                if ($Settings[0] != $ID && $GeneratedTS > $TS) {
                    $CurrentPos = ftell($DBTempHandle);
                    fwrite($IndexTempHandle, sprintf("%s,%s,%s,%s,%s\r\n", $PicID, $CurrentPos, $PicSize, $GeneratedTS, $Hits));
                    fseek($DBHandle, $DBPos);
                    $Picture = fread($DBHandle, $PicSize);
                    fwrite($DBTempHandle, $Picture);
                }
            }
        }
        /* Close the handles */
        fclose($IndexHandle);
        fclose($IndexTempHandle);
        fclose($DBHandle);
        fclose($DBTempHandle);
        /* Remove the prod files */
        unlink($Database);
        unlink($Index);
        /* Swap the temp & prod DB */
        rename($DatabaseTemp, $Database);
        rename($IndexTemp, $Index);
    }
    /**
     * Is the file in cache?
     *
     * @param string $ID
     * @param boolean $Verbose
     * @param boolean $UpdateHitsCount
     * @return boolean
     */
    public function isInCache($ID, $Verbose = \false, $UpdateHitsCount = \false)
    {
        /* Compute the paths */
        $Index = $this->CacheFolder . "/" . $this->CacheIndex;
        /* Search the picture in the index file */
        $Handle = @fopen($Index, "r");
        while (!feof($Handle)) {
            $IndexPos = ftell($Handle);
            $Entry = fgets($Handle, 4096);
            if ($Entry != "") {
                $Settings = preg_split("/,/", $Entry);
                $PicID = $Settings[0];
                if ($PicID == $ID) {
                    fclose($Handle);
                    $DBPos = $Settings[1];
                    $PicSize = $Settings[2];
                    $GeneratedTS = $Settings[3];
                    $Hits = intval($Settings[4]);
                    if ($UpdateHitsCount) {
                        $Hits++;
                        if (strlen($Hits) < 7) {
                            $Hits = $Hits . str_repeat(" ", 7 - strlen($Hits));
                        }
                        $Handle = @fopen($Index, "r+");
                        fseek($Handle, $IndexPos);
                        fwrite($Handle, sprintf("%s,%s,%s,%s,%s\r\n", $PicID, $DBPos, $PicSize, $GeneratedTS, $Hits));
                        fclose($Handle);
                    }
                    if ($Verbose) {
                        return ["DBPos" => $DBPos, "PicSize" => $PicSize, "GeneratedTS" => $GeneratedTS, "Hits" => $Hits];
                    } else {
                        return \true;
                    }
                }
            }
        }
        fclose($Handle);
        /* Picture isn't in the cache */
        return \false;
    }
    /**
     * Automatic output method based on the calling interface
     * @param string $ID
     * @param string $Destination
     */
    public function autoOutput($ID, $Destination = "output.png")
    {
        if (php_sapi_name() == "cli") {
            $this->saveFromCache($ID, $Destination);
        } else {
            $this->strokeFromCache($ID);
        }
    }
    /**
     * Show image from cache
     * @param string $ID
     * @return boolean
     */
    public function strokeFromCache($ID)
    {
        /* Get the raw picture from the cache */
        $Picture = $this->getFromCache($ID);
        /* Do we have a hit? */
        if ($Picture == null) {
            return \false;
        }
        header('Content-type: image/png');
        echo $Picture;
        return \true;
    }
    /**
     * Save file from cache.
     * @param string $ID
     * @param string $Destination
     * @return boolean
     */
    public function saveFromCache($ID, $Destination)
    {
        /* Get the raw picture from the cache */
        $Picture = $this->getFromCache($ID);
        /* Do we have a hit? */
        if ($Picture == null) {
            return \false;
        }
        /* Flush the picture to a file */
        $Handle = fopen($Destination, "w");
        fwrite($Handle, $Picture);
        fclose($Handle);
        /* All went fine */
        return \true;
    }
    /**
     * Get file from cache
     * @param string $ID
     * @return string|null
     */
    public function getFromCache($ID)
    {
        /* Compute the path */
        $Database = $this->CacheFolder . "/" . $this->CacheDB;
        /* Lookup for the picture in the cache */
        $CacheInfo = $this->isInCache($ID, \true, \true);
        /* Not in the cache */
        if (!$CacheInfo) {
            return null;
        }
        /* Get the database extended information */
        $DBPos = $CacheInfo["DBPos"];
        $PicSize = $CacheInfo["PicSize"];
        /* Extract the picture from the solid cache file */
        $Handle = @fopen($Database, "r");
        fseek($Handle, $DBPos);
        $Picture = fread($Handle, $PicSize);
        fclose($Handle);
        /* Return back the raw picture data */
        return $Picture;
    }
}
