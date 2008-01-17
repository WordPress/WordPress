<?php
/**
 * $Id: Logger.class.php 10 2007-05-27 10:55:12Z spocke $
 *
 * @package MCFileManager.filesystems
 * @author Moxiecode
 * @copyright Copyright © 2005, Moxiecode Systems AB, All rights reserved.
 */

// File type contstants
define('MC_LOGGER_DEBUG', 0);
define('MC_LOGGER_INFO', 10);
define('MC_LOGGER_WARN', 20);
define('MC_LOGGER_ERROR', 30);
define('MC_LOGGER_FATAL', 40);

/**
 * Logging utility class. This class handles basic logging with levels, log rotation and custom log formats. It's
 * designed to be compact but still powerful and flexible.
 */
class Moxiecode_Logger {
	// Private fields
	var $_path;
	var $_filename;
	var $_maxSize;
	var $_maxFiles;
	var $_maxSizeBytes;
	var $_level;
	var $_format;

	/**
	 * Constructs a new logger instance.
	 */
	function Moxiecode_Logger() {
		$this->_path = "";
		$this->_filename = "{level}.log";
		$this->setMaxSize("100k");
		$this->_maxFiles = 10;
		$this->_level = MC_LOGGER_DEBUG;
		$this->_format = "[{time}] [{level}] {message}";
	}

	/**
	 * Sets the current log level, use the MC_LOGGER constants.
	 *
	 * @param int $level Log level instance for example MC_LOGGER_DEBUG.
	 */
	function setLevel($level) {
		if (is_string($level)) {
			switch (strtolower($level)) {
				case "debug":
					$level = MC_LOGGER_DEBUG;
					break;

				case "info":
					$level = MC_LOGGER_INFO;
					break;

				case "warn":
				case "warning":
					$level = MC_LOGGER_WARN;
					break;

				case "error":
					$level = MC_LOGGER_ERROR;
					break;

				case "fatal":
					$level = MC_LOGGER_FATAL;
					break;

				default:
					$level = MC_LOGGER_FATAL;
			}
		}

		$this->_level = $level;
	}

	/**
	 * Returns the current log level for example MC_LOGGER_DEBUG.
	 *
	 * @return int Current log level for example MC_LOGGER_DEBUG.
	 */
	function getLevel() {
		return $this->_level;
	}

	function setPath($path) {
		$this->_path = $path;
	}

	function getPath() {
		return $this->_path;
	}

	function setFileName($file_name) {
		$this->_filename = $file_name;
	}

	function getFileName() {
		return $this->_filename;
	}

	function setFormat($format) {
		$this->_format = $format;
	}

	function getFormat() {
		return $this->_format;
	}

	function setMaxSize($size) {
		// Fix log max size
		$logMaxSizeBytes = intval(preg_replace("/[^0-9]/", "", $size));

		// Is KB
		if (strpos((strtolower($size)), "k") > 0)
			$logMaxSizeBytes *= 1024;

		// Is MB
		if (strpos((strtolower($size)), "m") > 0)
			$logMaxSizeBytes *= (1024 * 1024);

		$this->_maxSizeBytes = $logMaxSizeBytes;
		$this->_maxSize = $size;
	}

	function getMaxSize() {
		return $this->_maxSize;
	}

	function setMaxFiles($max_files) {
		$this->_maxFiles = $max_files;
	}

	function getMaxFiles() {
		return $this->_maxFiles;
	}

	function debug($msg) {
		$args = func_get_args();
		$this->_logMsg(MC_LOGGER_DEBUG, implode(', ', $args));
	}

	function info($msg) {
		$args = func_get_args();
		$this->_logMsg(MC_LOGGER_INFO, implode(', ', $args));
	}

	function warn($msg) {
		$args = func_get_args();
		$this->_logMsg(MC_LOGGER_WARN, implode(', ', $args));
	}

	function error($msg) {
		$args = func_get_args();
		$this->_logMsg(MC_LOGGER_ERROR, implode(', ', $args));
	}

	function fatal($msg) {
		$args = func_get_args();
		$this->_logMsg(MC_LOGGER_FATAL, implode(', ', $args));
	}

	function isDebugEnabled() {
		return $this->_level >= MC_LOGGER_DEBUG;
	}

	function isInfoEnabled() {
		return $this->_level >= MC_LOGGER_INFO;
	}

	function isWarnEnabled() {
		return $this->_level >= MC_LOGGER_WARN;
	}

	function isErrorEnabled() {
		return $this->_level >= MC_LOGGER_ERROR;
	}

	function isFatalEnabled() {
		return $this->_level >= MC_LOGGER_FATAL;
	}

	function _logMsg($level, $message) {
		$roll = false;

		if ($level < $this->_level)
			return;

		$logFile = $this->toOSPath($this->_path . "/" . $this->_filename);

		switch ($level) {
			case MC_LOGGER_DEBUG:
				$levelName = "DEBUG";
				break;

			case MC_LOGGER_INFO:
				$levelName = "INFO";
				break;

			case MC_LOGGER_WARN:
				$levelName = "WARN";
				break;

			case MC_LOGGER_ERROR:
				$levelName = "ERROR";
				break;

			case MC_LOGGER_FATAL:
				$levelName = "FATAL";
				break;
		}

		$logFile = str_replace('{level}', strtolower($levelName), $logFile);

		$text = $this->_format;
		$text = str_replace('{time}', date("Y-m-d H:i:s"), $text);
		$text = str_replace('{level}', strtolower($levelName), $text);
		$text = str_replace('{message}', $message, $text);
		$message = $text . "\r\n";

		// Check filesize
		if (file_exists($logFile)) {
			$size = @filesize($logFile);

			if ($size + strlen($message) > $this->_maxSizeBytes)
				$roll = true;
		}

		// Roll if the size is right
		if ($roll) {
			for ($i=$this->_maxFiles-1; $i>=1; $i--) {
				$rfile = $this->toOSPath($logFile . "." . $i);
				$nfile = $this->toOSPath($logFile . "." . ($i+1));

				if (@file_exists($rfile))
					@rename($rfile, $nfile);
			}

			@rename($logFile, $this->toOSPath($logFile . ".1"));

			// Delete last logfile
			$delfile = $this->toOSPath($logFile . "." . ($this->_maxFiles + 1));
			if (@file_exists($delfile))
				@unlink($delfile);
		}

		// Append log line
		if (($fp = @fopen($logFile, "a")) != null) {
			@fputs($fp, $message);
			@fflush($fp);
			@fclose($fp);
		}
	}

	/**
	 * Converts a Unix path to OS specific path.
	 *
	 * @param String $path Unix path to convert.
	 */
	function toOSPath($path) {
		return str_replace("/", DIRECTORY_SEPARATOR, $path);
	}
}

?>